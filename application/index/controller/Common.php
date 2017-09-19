<?php
namespace app\index\controller;

use think\Validate;
use think\Cache;
use think\Db;
define('TOKEN','weixin');
class Common extends Base{
    public function login (){
        //$this->redirect('http://www.baidu.com');
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

    //获取服务器列表 现默认 游戏 王者荣耀
    public function getServerList(){
        $servers1 = db('servers')->field('id as value, name as label')->where('pid','=',0)->select();
        $servers2 = db('servers')->field('id as value, pid, name as label')->where('pid','<>',0)->select();

        $list = array('servers1'=>$servers1, 'servers2' => $servers2);

        $this->successReturn($list);

    }

    public function getSpecials(){
        $specialname = input('specialName');
        $res = [];
        $res = Db::table('ls_special')
            ->where('specialname','like','%'.$specialname.'%')
            ->limit(10)
            ->select();
        $this->successReturn($res);
    }

    public function getSpecialDetail(){
        $id = input('id');
        $res = [];
        $res = Db::table('ls_special')
            ->where(array('id'=>$id))
            ->limit(10)
            ->find();
        $this->successReturn($res);
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

    public function wechatInit(){
        Vendor('wechat.wechat','.class.php');
        $options = array(
            'token'=>'canting', //填写你设定的key
            'encodingaeskey'=>'nY5ZzssGDM3F52g4xPBjOcIW7IkGKMvCkFeYiPyle7J', //填写加密用的EncodingAESKey
            'appid'=>'wx60ec6bfdd9947a4a', //填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret'=>'f8041917e383c5b22fe64684980abb23' //填写高级调用功能的密钥
        );
        $weObj = new \Wechat($options);
        //$weObj->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $accessToken = $weObj->checkAuth($options['appid'],$options['appsecret']);
        dump($weObj);
        /*$type = $weObj->getRev()->getRevType();
        dump($weObj);
        echo $type;
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
        }*/
    }

}