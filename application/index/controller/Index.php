<?php
namespace app\index\controller;

use think\Validate;
use think\Cache;
class Index extends Base
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
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
