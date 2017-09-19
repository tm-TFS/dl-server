<?php
namespace app\admin\controller;

use app\admin\model\User as MUser;
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
}