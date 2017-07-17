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
        $isReceive = input('receive_model'); //是否接手订单 1-未接手 2-已接手
        $rateStatus  =input('rateStatus'); //1-正在代练 2-等待验收 3-订单异常 4-锁定订单 5-协商撤销中 6-客服接入中 7-已处理 10-已结算 11-已撤下
        $sortKey = input('sortKey');  //排序字段
        $order = input('order');  //asc  desc


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
        if ($isReceive) {
            $condition['isReceive'] = $isReceive;
        }
        if ($serverId) {
            $condition['serverId'] = $serverId;
        }
        if ($rateStatus) {
            $condition['rateStatus'] = $rateStatus;
        }
        if ($publishId) {
            $condition['publishType'] = $publishId;
        }
        if ($title) {
            $condition['title'] = $title;
        }
        if ($name) {
            $condition['publishName'] = $name;
        }

        $list = db('rate_list')
            ->where($condition)
            ->order($sortKey, $order)
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
        $res = db('rate_detail')->where($condition)->find();
        if(!$res){
            $this->errorReturn('找不到该订单');
            exit;
        }
        $this->successReturn($res);
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