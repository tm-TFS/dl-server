<?php

namespace app\admin\model;

use think\Model;
use app\admin\model\User as MUser;

class Settlement extends Model
{
    /**
     * params: {
     * tradeType: 1-支付 2-充值 3-提现 4-转账 5-退款 6-开通会员 7-奖金转电子币,
     * amount: 交易金额,
     * userFromId (userId): 来源编码,
     * userToId (userId): 目标编码,
     * rUserToId : 重复目标编码,
     * tradeDescription: 留言
     *}
     */

    public function deal ()
    {
        $data = input();

        if (empty($data['tradeType'])) {
            return WSTReturn("错误的交易类型。");
        }

        $user = new MUser();

        if(empty($data['userId'])){
            return WSTReturn("错误的充值对象");
        }

        $uRes = $user->getById($data['userId']);
        $time = date('Y-m-d h:i:s', time());

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
        //转账
        if ($data['tradeType'] == 4) {

            $userToId = $data['userToId'];
            $toUser = array();

            if(empty($userToId)){
                return WSTReturn("转账会员编号不能为空！");
            }else if($userToId != $data['rUserToId']) {
                return WSTReturn("两次输入会员编号不一致！");
            } else {
                $toUser = $user->where(array('userId' => $userToId))->field('userId, fictitiousMoney')->find();
            }

            if (empty($toUser)) {
                return WSTReturn("错误的转账对象");
            }

            if (empty($data['amount'])) {
                return WSTReturn("充值金额不能为空");
            }

            $data['toAmount'] = $uRes['fictitiousMoney'];
            $data['userToId'] = $userToId;
            $data['userFromId'] = $data['userId'];
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
     * 分页
     */
    public function pageQuery(){
        /******************** 查询 ************************/
        $where = [];
        $userId = input('userId');
        $tradeType = input('tradeType');
        $pageId = input('pageId') ? input('pageId') : 1;
        $pageSize = input('pageSize') ? input('pageSize') : 10;

        if(empty($userId)){
            return WSTReturn('会员编码错误');
        } else {
            $where['userFromId|userToId'] = $userId;
        }

        if(!empty($tradeType)){
            $where['tradeType'] = $tradeType;
        }

        /********************* 取数据 *************************/
        $rs = $this->where($where)
            ->order('createTime desc')
            ->paginate($pageSize, false, ['page'=>$pageId]);
        return WSTReturn('', 1, $rs);
    }

    public function getById($id){
        return $this->get(['userId'=>$id]);
    }

}