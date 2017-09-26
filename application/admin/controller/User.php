<?php
namespace app\admin\controller;

use app\admin\model\User as MUser;
use app\admin\model\Test as MTest;
use app\admin\model\Settlement as Mse;
use app\admin\model\Reward as Mre;
use think\Db;
use \think\Request;
class User extends Base{

    public function test() {
        $m = new MTest();
        $res = $m -> execute("update f_test set money = money+:amount where id=:id", ['id' => 1, 'amount' => 100]);
        dump($res);
    }

    public function register (){
        $m = new MUser();
        $res = $m->register();

        if($res['status'] == 1){
            $this->successReturn('注册成功');
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function login() {
        $m = new MUser();
        $res = $m->login();

        if($res['status'] == 1){
            $this->successReturn($res);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function changeInfo () {
        $m = new MUser();
        $res = $m->changeInfo();

        if($res['status'] == 1){
            $this->successReturn('修改成功');
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function changePwd () {
        $m = new MUser();
        $res = $m->changePwd();

        if($res['status'] == 1){
            $this->successReturn('修改成功');
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function getInfo () {
        $m = new MUser();
        $res = $m->getInfo();

        if($res['status'] == 1){
            $this->successReturn($res);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function getSort () {
        $m = new MUser();
        $res = $m->getSort();

        if($res['status'] == 1){
            $this->successReturn($res);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function getTree () {
        $m = new MUser();
        $res = $m->getTree();

        if($res['status'] == 1){
            $this->successReturn($res);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function getRecommendList() {
        $m = new MUser();
        $key = input('key');
        $where = array(
            'recommender' => input('recommender'),

        );
        if(!empty($key)){
            $where['loginName|trueName'] = ['like', "%" . "$key" . "%"];
        }
        $order = 'userStatus asc, createTime desc';
        $res = $m->pageQuery($where, $order);

        if($res['status'] == 1){
            $this->successReturn($res);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function updateUser () {
        $m = new MUser();
        $s = new Mse();
        $request = Request::instance();
        $userId = input('userId');
        $u_userId = input('updateUserId');
        $userType = input('userType');
        $registerFees = config('registerFee');

        //查询升级所需金额
        $user = $m->getById($userId);
        $u_user = $m->getById($u_userId);
        $amount = $registerFees[$userType] - $registerFees[$u_user['userType']];

        if($amount <= 0){
            $this->errorReturn('请选择正确的会员等级');
            return;
        }

        $fictitiousMoney = $user['fictitiousMoney'] - $amount;
        if($fictitiousMoney < 0){
            $this->errorReturn('您的电子币余额不足，请先充值或兑换');
            return;
        }

        $request->post(['tradeType' => 8]);
        $request->post(['amount' => $amount]);

        $settlement = $s->deal();

        if($settlement['status'] == 1){
            $res = $m->save(['fictitiousMoney' => $fictitiousMoney], ['userId' => $userId]);
            $res = $m->save(['userType' => $userType], ['userId' => $u_userId]);
            if($res){
                $this->successReturn('');
            } else {
                $this->errorReturn('操作失败');
            }
        } else {
            $this->errorReturn($settlement['msg']);
        }
    }

    public function adClick(){
        $m = new MUser();
        $adId = input('adId');
        $userId = input('userId');
        $ad = db('ad_click')->where(['userId' => $userId])->find();
        $data  = array();
        $res = 0;
        $user = $m->getById($userId);
        $ad_money = config('ad'); //广告点击奖数组
        $r = new Mre();
        $request = Request::instance();


        if(empty($user)){
            $this->errorReturn('无效的用户');
        }
        $click_money = $ad_money[$user['userType']];  //点击一次的奖励金

        //rewardType  1-广告点击奖 2-组织奖 3-报单奖 4-开拓奖
        $request->post(['rewardType' => 1]);


        if(empty($ad)){
            $data = array('detail' => $adId, 'total' => 1, 'userId' => $userId);

            Db::startTrans();
            try{
                $res = db('ad_click')->insert($data);
                $request->post(['amount' => $click_money]);
                $r->add();
            }catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('操作失败',-1);
            }
        } else {
            $id = $ad['id'];
            if($ad['total'] == 10){
                $this->errorReturn('今日广告点击量已达到10次，无需再点击');
            }
            $detail = explode(',',$ad['detail']);
            if(in_array($adId, $detail)){
                $this->errorReturn('您今天已点击过该广告');
            }
            $detail[] = $adId;
            $data['detail'] = implode(',', $detail);
            $data['userId'] = $userId;
            $data['total'] = ++$ad['total'] ;

            Db::startTrans();
            try{
                $res = db('ad_click')->where(['id' => $id])->update($data);
                $request->post(['amount' => $click_money * $ad['total']]);
                $_res = $r->edit();
                if($_res['status'] != 1){
                    Db::rollback();
                }
            }catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('操作失败',-1);
            }


        }

        if($res){

            $this->successReturn('操作成功');
        } else {
            $this->errorReturn('操作失败');
        }
    }


}