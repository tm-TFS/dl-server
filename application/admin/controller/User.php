<?php
namespace app\admin\controller;

use app\admin\model\User as MUser;
use app\admin\model\Test as MTest;
use app\admin\model\Settlement as Mse;
use think\Db;
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


}