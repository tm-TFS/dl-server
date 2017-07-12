<?php
namespace app\index\controller;

use think\Validate;
use think\Cache;
class Index extends Base
{
    public function index()
    {
        vendor('wechat.wechat','.class.php');
        $options = array(
            'token'=>'tokenaccesskey', //填写你设定的key
            //'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
            'appid'=>'wxaef5c3a153c200db', //填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret'=>'0e56da74c60df8b75f0d4981a9a9aee8' //填写高级调用功能的密钥
        );
        $weObj = new \Wechat($options);
        $weObj->checkAuth();
        dump($weObj);exit;
        switch($type) {
            case \Wechat::MSGTYPE_TEXT:
                $weObj->text("hello, I'm wechat")->reply();
                exit;
                break;
            case \Wechat::MSGTYPE_EVENT:
                break;
            case \Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $weObj->text("help info")->reply();
        }
    }
    public function test () {

        /*dump(CONTROLLER_NAME);
        exit;*/

        $account = input('account');
        $password = input('password');

        //$this->token_check(input('uid'), input('token'));

        $validate = validate('Index');

        $data = [
            'account'  => $account,
            'password' => $password
        ];
        if (!$validate->check($data)) {

            $err_msg = $validate->getError();

            $this->response['msg'] = $err_msg;

            $this->ajaxReturn();

            exit;
        }

        //写入缓存
        Cache::tag('token')->set("1",md5($account.time()));
        dump(Cache::tag('token')->get(1));
        dump($this->getToken(1));exit;


        $this->response['status'] = 1;
        $this->response['content'] = array('uid'=>1,'nickName'=>'小沉','mobile'=>$account, 'token'=>session("token.$password"));
        $this->ajaxReturn();

    }


}
