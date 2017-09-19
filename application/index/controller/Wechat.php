<?php
namespace app\index\controller;

use think\Db;
use think\Controller;
define('TOKEN','weixin');
Vendor('wechat.wechat','.class.php');

class Wechat extends Base{

    protected $options = array(
        'token'=>'canting', //填写你设定的key
        'encodingaeskey'=>'nY5ZzssGDM3F52g4xPBjOcIW7IkGKMvCkFeYiPyle7J', //填写加密用的EncodingAESKey
        'appid'=>'wx60ec6bfdd9947a4a', //填写高级调用功能的app id, 请在微信开发模式后台查询
        'appsecret'=>'f8041917e383c5b22fe64684980abb23' //填写高级调用功能的密钥
    );

    public function oauth2 (){
        $weObj = new \Wechat($this->options);
        if (isset($_GET['code'])){
            //echo $_GET['code'];
            dump($weObj->getOauthAccessToken());
        }else{
            echo "NO CODE";
        }
    }

    public function wechatInit(){
        $weObj = new \Wechat($this->options);
        $callback = "http://1720e2d535.51mypc.cn/public/index/Wechat/oauth2";
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='. $this->options['appid'] .'&redirect_uri='. $callback .'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
        //echo $url;
        $this -> redirect($url);
        //$weObj->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        //$accessToken = $weObj->checkAuth($options['appid'],$options['appsecret']);
    }

}