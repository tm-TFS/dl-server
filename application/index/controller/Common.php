<?php
namespace app\index\controller;

use think\Validate;
use think\Cache;
class Common extends Base{
    public function login (){

        $account = input('account');
        $password = input('password');

        $data = [
            'account'  => $account,
            'password' => $password
        ];
        $this->validateCheck($data);

        $res = model('user')->where(array('userName' => $account, 'password' => $password))->field('password', true)->find(); //获取除password之外的字段
        //dump($res['id']);exit;
        if(!$res){
            $this->errorReturn('账户或密码错误');
            exit;
        }

        $token = md5($account.time());

        //写入缓存
        Cache::tag('token')->set($res['id'],$token);

        $this->response['status'] = 1;
        $this->response['content'] = $res;
        $this->response['token'] = $token;
        $this->ajaxReturn();
    }


    protected function validateCheck($data, $vali_name = CONTROLLER_NAME) {

        $validate = validate($vali_name);

        if (!$validate->check($data)) {

            $err_msg = $validate->getError();

            $this->response['status'] = 0;
            $this->response['msg'] = $err_msg;

            $this->ajaxReturn();

            exit;
        }

        return;
    }
}