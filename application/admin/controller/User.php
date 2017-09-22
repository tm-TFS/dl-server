<?php
namespace app\admin\controller;

use app\admin\model\User as MUser;
use app\admin\model\Settlement as Mse;
use think\Validate;
class User extends Base{
    public function register (){
        $m = new MUser();
        $res = $m->register();

        if($res['status'] == 1){
            $this->successReturn('注册成功');
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
        $where = array(
            'recommender' => input('recommender')
        );
        $res = $m->pageQuery($where);

        if($res['status'] == 1){
            $this->successReturn($res);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function passRegister() {
        $m = new MUser();
        $s = new Mse();

        $users = array();
        $u = $m->getByIds();

        if($u['status'] == 1){
            $users = $u['data'];
        } else {
            $this->errorReturn($u['msg']);
        }

        $registerFee = 0;
        foreach ($users as $v){
            $registerFee += $v['registerFee'];
        }

        $res = $s->deal($registerFee);

        if($res['status'] == 1){
            $this->successReturn($res);
        } else {
            $this->errorReturn($res['msg']);
        }

    }

}