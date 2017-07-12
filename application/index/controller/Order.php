<?php

namespace app\index\controller;

use think\Validate;

class Order extends Base
{
    public function __construct() {
        //parent::__construct();

        //token 验证
        $this->token_check(input('customerId'), input('token'));
    }

    public function getRateList()
    {

        $customerId = input('customerId');
        $serverId = input('serverId');
        $pageId = input('pageId');
        $pageSize = input('pageSize') ? input('pageSize') : 10;
        $publishId = input('publishId');
        $title = input('title');
        $name = input('name');

        $data = [
            'customerId' => $customerId,
            'serverId' => $serverId,
            'pageId' => $pageId
        ];
        $this->validateCheck($data);

        $condition = array();

        if ($customerId) {
            $condition['customerId'] = $customerId;
        }
        if ($serverId) {
            $condition['serverId'] = $serverId;
        }
        if ($publishId) {
            $condition['publishType'] = $publishId;
        }
        if ($title) {
            $condition['title'] = $title;
        }
        if ($name) {
            $condition['customerName'] = $name;
        }

        $list = model('rate')
            ->where($condition)
            ->paginate($pageSize, false, ['page' => $pageId]);
        //dump($list);exit;
        //$count = $list->render();
        //$list['totalPage'] = ceil($list['total']/$list['per_page']);
        $this->response['status'] = 1;
        $this->response['content'] = $list;
        $this->ajaxReturn();
    }

    public function getRateDetail () {
        $id = input('orderId');
        $condition = [];
        if($id){
            $condition = array('id'=>$id);
        }
        $res = db('rate')->where($condition)->find();
        if(!$res){
            $this->errorReturn('找不到该订单');
            exit;
        }
        $this->successReturn($res);
    }

    public function getOrderList () {

        $publishCName = input('publishCName');
        $rateTitle = input('rateTitle');
        $pageId = input('pageId');
        $pageSize = input('pageSize') ? input('pageSize') : 10;
        $publishId = input('publishId');
        $title = input('title');
        $name = input('name');

    }

    //后台发布订单
    public function publishRate()
    {

    }


    protected function validateCheck($data)
    {

        $validate = validate(CONTROLLER_NAME);

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