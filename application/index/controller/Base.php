<?php
namespace app\index\controller;
use think\Cache;
use think\Validate;
use think\Controller;
use \think\Request;
//允许跨域
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

define('CONTROLLER_NAME',Request::instance()->controller());
define('MODULE_NAME',Request::instance()->module());
define('ACTION_NAME',Request::instance()->action());

class Base extends Controller{

    public $response = array('status' => 0,'content'=> [], 'msg' => '');

    public function __initialize()
    {

    }

    protected function token_check ($name, $value) {
        $token = $this->getToken($name);
        if($token != $value || !$token){
            header("HTTP/1.0 401 Unauthorized");
            echo "401";
            exit;
        }
    }

    protected function getToken ($name) {
        return Cache::tag('token')->get($name);
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data = '', $type = '', $json_option = 0) {
        if(empty($data))
            $data = $this->response;
        if (empty($type))
            $type = config('default_ajax_return');;
        switch (strtoupper($type)) {
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data, $json_option));
            case 'XML' :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler = isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler . '(' . json_encode($data, $json_option) . ');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
            default :
                // 用于扩展其他返回格式数据
        }
    }

    protected function successReturn($content="操作成功") {
        $this->response['status'] = 1;
        $this->response['content'] = $content;
        $this->ajaxReturn();
    }
    protected function errorReturn($msg="操作失败") {
        $this->response['status'] = 0;
        $this->response['msg'] = $msg;
        $this->ajaxReturn();
    }

}