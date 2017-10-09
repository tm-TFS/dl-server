<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use app\admin\model\User as MUser;
use app\admin\model\Reward as Mre;

class Settlement extends Model
{
    /**
     * params: {
     * tradeType: 1-支付 2-充值 3-提现 4-转账 5-退款 6-开通会员 7-奖金转电子币 8-会员升级,
     * amount: 交易金额,
     * userFromId (userId): 来源编码,
     * userToId (userId): 目标编码,
     * rUserToId : 重复目标编码,
     * tradeDescription: 留言
     * userIds: 批量操作的用户ID
     *}
     */

    public function deal ()
    {
        $data = input();
        if (empty($data['tradeType'])) {
            return WSTReturn("错误的交易类型。");
        }

        $u = new MUser();

        if(empty($data['userId'])){
            return WSTReturn("错误的操作对象");
        }

        $uRes = $u->getById($data['userId']);
        $time = date("Y-m-d H:i:s",time());
        $date = date("Y-m-d",time());

        //充值
        if ($data['tradeType'] == 2) {

            if (empty($uRes)) {
                return WSTReturn("错误的充值对象");
            }

            if (empty($data['amount'])) {
                return WSTReturn("充值金额不能为空");
            }

            $data['toAmount'] = $uRes['fictitiousMoney'];
            $data['userToId'] = $data['userId'];
            $data['tradeDescription'] = '充值';
            $data['createUser'] = $data['userId'];
            $data['createTime'] = $time;

        }
        //提现
        if ($data['tradeType'] == 3) {

            $serverPercent = 0.06;  //手续费
            if (empty($uRes)) {
                return WSTReturn("错误的提现对象");
            }

            if ($data['amount'] < 100) {
                return WSTReturn("提现金额必须大于100元");
            }
            if ($data['amount'] > $uRes['userMoney']) {
                return WSTReturn("提现金额不能超出现有奖金");
            }

            //提现时，先减去提现金额
            $data['fromAmount'] = $uRes['userMoney'] - $data['amount'];
            $data['userFromId'] = $data['userId'];
            $data['tradeDescription'] = '提现';
            $data['createUser'] = $data['userId'];
            $data['createTime'] = $time;

            //提现专用部分
            $data['bankName'] = $uRes['bankName'];
            $data['bankAccount'] = $uRes['bankAccount'];
            $data['accountName'] = $uRes['accountName'];
            $data['serverCharge'] = $data['amount'] * $serverPercent;

            Db::startTrans();
            try{
                $res = $this->allowField(true)->save($data);
                $u_data = [
                    'userMoney' => $data['fromAmount']
                ];
                $u->save($u_data, ['userId' => $data['userId']]);
                Db::commit();
            }catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('操作失败',-1);
            }
            return WSTReturn("申请成功", 1);

        }
        //转账
        if ($data['tradeType'] == 4) {

            $userToId = $data['userToId'];
            $toUser = array();

            if(empty($userToId)){
                return WSTReturn("转账会员编号不能为空！");
            } else {
                $toUser = $u->where(array('userId' => $userToId))->field('userId, fictitiousMoney')->find();
            }

            if (empty($toUser)) {
                return WSTReturn("错误的转账对象");
            }

            if (empty($data['amount'])) {
                return WSTReturn("转账电子币不能为空");
            }


            $data['fromAmount'] = $uRes['fictitiousMoney'] - $data['amount'];
            $data['toAmount'] = $toUser['fictitiousMoney'] + $data['amount'];
            $data['userToId'] = $userToId;
            $data['userFromId'] = $data['userId'];
            $data['createUser'] = $data['userId'];
            $data['createTime'] = $time;
            $data['status'] = 10;

            if(!empty($data['remark'])){
                $data['tradeDescription'] = $data['remark'];
            }

            Db::startTrans();
            try{
                $res = $this->allowField(true)->save($data);
                $u_data = [
                    'fictitiousMoney' => $data['fromAmount'],
                ];
                $u->save($u_data, ['userId' => $data['userId']]);
                $to_u_data = ['fictitiousMoney' => $data['toAmount']];
                $u->save($to_u_data, ['userId' => $data['userToId']]);
                Db::commit();
            }catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('操作失败',-1);
            }
            return WSTReturn("申请成功", 1);

        }

        //开通会员 （特殊，不需审核，直接操作user表）
        if ($data['tradeType'] == 6) {

            if (empty($uRes)) {
                return WSTReturn("主键错误");
            }

            //获取用户数组信息
            $users = array();
            $_u = $u->getByIds();
            if($_u['status'] == 1){
                $users = $_u['data'];
            } else {
                return WSTReturn($_u['msg']);
            }

            // rewardType 1-广告点击奖 2-组织奖 3-报单奖 4-开拓奖
            $report = config('report');
            $develop = config('develop');
            $registerFee = 0;
            $w_data = array();
            foreach ($users as $k => $v){

                if($v['userStatus'] == 1){
                    continue;
                }

                $registerFee += $v['registerFee'];
                $recommend = $u->getById($v['recommender']);

                //奖励日志
                $w_data[$k] = array(
                    ['rewardType'=>3, 'userId'=>$v['agentCenter'], 'createDate'=>$date, 'amount' => $report * $v['registerFee']],
                    ['rewardType'=>4, 'userId'=>$v['recommender'], 'createDate'=>$date, 'amount' => $develop[$recommend['userType']] * $v['registerFee']],
                );
                /*$w_data[$k][0]['amount'] = $report * $v['registerFee'];
                $w_data[$k][1]['amount'] = $develop[$recommend['userType']] * $v['registerFee'];*/

            }

            //$registerFee 还是等于0 说明所选用户均为正式用户
            if($registerFee == 0){
                return WSTReturn("所选用户均不是待审核状态");
            }

            $data['fromAmount'] = $uRes['fictitiousMoney'] - $registerFee;
            $data['amount'] = $registerFee;

            if($data['fromAmount'] < 0){
                return WSTReturn("电子币余额不足");
            }

            //资金日志
            $data['userFromId'] = $data['userId'];
            $data['tradeDescription'] = '开通会员';
            $data['paymentType'] = 6;
            $data['createUser'] = $data['userId'];
            $data['createTime'] = $time;
            $data['status'] = 10;

            Db::startTrans();
            try{
                //写入资金日志
                $this->allowField(true)->save($data);

                //记录会员余额，审核状态
                $u_data = [
                    'fictitiousMoney' => $data['fromAmount']
                ];
                $result = Db::name('user')->where(['userId'=>$uRes['userId']])->update($u_data);
                //$_sql = $u->getLastSql();dump($_sql);exit;

                //写入奖励日志 用户奖励金累加
                $r = new Mre();
                foreach ($w_data as $v){
                    $res = $r->saveAll($v);
                    foreach ($v as $value){
                        $_res = Db::name('user')->execute("update f_user set userMoney = userMoney + :amount where userId = :userId", ['userId'=> $value['userId'], 'amount'=> $value['amount']]);
                        if(empty($_res)){
                            Db::rollback();
                            return WSTReturn("写入失败", -1);
                        }
                    }
                }

                //更新注册用户激活状态 userStatus
                $userIdArr = explode(',',input('userIds'));
                $_u_data = array();
                foreach ($userIdArr as $v){
                    $_u_data[] = ['userId'=>$v, 'userStatus' => 1];
                }
                $u->saveAll($_u_data);

                if(false !== $result){
                    Db::commit();
                    return WSTReturn("操作成功", 1);
                }
            }catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('操作失败',-1);
            }

        }

        //奖金转币
        if ($data['tradeType'] == 7) {

            if (empty($uRes)) {
                return WSTReturn("错误的操作对象");
            }

            if (empty($data['amount'])) {
                return WSTReturn("转币金额不能为空");
            }

            if ($data['amount'] > $uRes['userMoney']) {
                return WSTReturn("转币金额不能大于奖金余额");
            }

            $data['fromAmount'] = $uRes['userMoney'] - $data['amount'];
            $data['userToId'] = $data['userId'];
            $data['userFromId'] = $data['userId'];
            $data['createUser'] = $data['userId'];
            $data['createTime'] = $time;
            $data['status'] = 10;

            if(!empty($data['remark'])){
                $data['tradeDescription'] = $data['remark'];
            }

            Db::startTrans();
            try{
                $res = $this->allowField(true)->save($data);
                $u_data = [
                    'userMoney' => $data['fromAmount'],
                    'fictitiousMoney' => $uRes['fictitiousMoney'] + $data['amount']
                ];
                $u->save($u_data, ['userId' => $data['userId']]);
                Db::commit();
            }catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('操作失败',-1);
            }
            return WSTReturn("申请成功", 1);

        }
        //会员升级
        if ($data['tradeType'] == 8) {

            $data['fromAmount'] = $uRes['fictitiousMoney'] - $data['amount'];
            $data['userToId'] = $data['userId'];
            $data['tradeDescription'] = '会员升级';
            $data['createUser'] = $data['userId'];
            $data['createTime'] = $time;

        }
        $res = $this->allowField(true)->save($data);
        if(!empty($res)){
            return WSTReturn("申请成功", 1);
        }
        return WSTReturn("提交失败");
    }


    /**
     *  审核申请
     *  tradeType：2-充值 3-提现
     *  operation: 1-同意 2-不同意
     */
    public function checkSettlement(){
        $u = new MUser();
        $id = input('id');
        $operation = input('operation');
        if(empty($id)){
            return WSTReturn('申请编码错误');
        }
        $res = $this->get(['id'=>$id]);
        $type = $res['tradeType'];
        if(empty($res)){
            return WSTReturn('申请编码错误');
        } else if(!($type == 3 || $type == 2)){
            return WSTReturn('申请类型错误');
        }
        if($type == 3){
            $user = $u->getById($res['userFromId']);
        } else if($type == 2){
            $user = $u->getById($res['userToId']);
        }

        if($res['status'] != 1){
            return WSTReturn('该申请已被处理，不需要继续操作');
        }

        if(empty($user)){
            return WSTReturn('会员编码错误');
        }

        //提现
        if($type == 3){
            if($operation == 1){
                $this->save(['status' => 10], ['id' => $id]);
            } else {
                Db::startTrans();
                try{
                    $this->allowField(true)->save(['status' => 11], ['id' => $id]);
                    $u_data = [
                        'userMoney' => $user['userMoney'] + $res['amount'],
                    ];
                    $u->save($u_data, ['userId' => $user['userId']]);
                    Db::commit();
                }catch (\Exception $e) {
                    Db::rollback();
                    return WSTReturn('操作失败',-1);
                }
            }
        }
        //充值
        if($type == 2){
            if($operation == 1){
                Db::startTrans();
                try{
                    $this->save(['status' => 10], ['id' => $id]);
                    $u_data = [
                        'fictitiousMoney' => $user['fictitiousMoney'] + $res['amount'],
                    ];
                    $u->save($u_data, ['userId' => $user['userId']]);
                    Db::commit();
                }catch (\Exception $e) {
                    Db::rollback();
                    return WSTReturn('操作失败',-1);
                }

            } else {
                $this->save(['status' => 11], ['id' => $id]);
            }
        }
        return WSTReturn('',1, "操作成功");
    }

    /**
     * 分页
     */
    public function pageQuery(){
        /******************** 查询 ************************/
        $where = [];
        $userId = input('userId');
        $tradeType = input('tradeType');
        $pageId = input('pageId') ? input('pageId') : 1;
        $pageSize = input('pageSize') ? input('pageSize') : 10;
        $f_date = input('f_date');
        $e_date = input('e_date');

        if(empty($userId)){
            return WSTReturn('会员编码错误');
        } else {
            $where['userFromId|userToId'] = $userId;
        }

        if(!empty($tradeType)){
            $where['tradeType'] = $tradeType;
        }

        $where2 = [];

        if($f_date){
            $where['createTime'] = ['>=', $f_date];
        }

        if($e_date) {
            $where2['createTime'] = ['<=', $e_date];
        }

        /********************* 取数据 *************************/
        $rs = $this->where($where)
            ->where($where2)
            ->order('createTime desc')
            ->paginate($pageSize, false, ['page'=>$pageId]);
        return WSTReturn('', 1, $rs);
    }

    public function getById($id){
        return $this->get(['userId'=>$id]);
    }

}