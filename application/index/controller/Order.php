<?php

namespace app\index\controller;

use think\Validate;

class Order extends Base
{
    /*public function __construct() {
        //token 验证
        $this->token_check(input('uid'), input('token'));
    }*/

    public function getRateList()
    {

        $customerId = input('customerId');
        $receiveCustomerId = input('receiveCustomerId');
        $serverId = input('serverId');
        $pageId = input('pageId');
        $pageSize = input('pageSize') ? input('pageSize') : 20;
        $publishId = input('publishId');
        $title = input('title');
        $name = input('name');
        $isReceive = input('receive_model'); //是否接手订单 1-未接手 2-已接手
        $rateStatus  =input('rateStatus'); //1-正在代练 2-等待验收 3-订单异常 4-锁定订单 5-协商撤销中 6-客服接入中 7-已处理 10-已结算 11-已撤下
        $sortKey = input('sortKey') ? input('sortKey') : 'id';  //排序字段
        $order = input('order') ? input('order'): 'desc';  //asc  desc

        $condition = array();

        if ($customerId) {
            $condition['customerId'] = $customerId;
        }
        if ($receiveCustomerId) {
            $condition['receiveCustomerId'] = $receiveCustomerId;
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
        //dump($condition);exit;
        //$count = $list->render();
        //$list['totalPage'] = ceil($list['total']/$list['per_page']);
        $this->response['status'] = 1;
        $this->response['content'] = $list;
        $this->ajaxReturn();
    }

    public function addOrder() {
        $customerId = input('customerId') ? input('customerId'): 1;
        $serverId = input('serverId');
        $serverName = input('serverName');
        $title = input('title');
        $price = input('price');
        $timeLimit = input('timeLimit');
        $saveDeposit = input('saveDeposit');
        $efficiencyDeposit = input('efficiencyDeposit');
        $account = input('account');
        $password = input('password');
        $nickname = input('nickname');
        $contactMobile = input('contactMobile');
        $qqNum = input('qqNum');
        $publishType = input('publishType');
        $isReceive = 1;//是否接手订单 1-未接手 2-已接手
        $rateStatus  = 0; //1-正在代练 2-等待验收 3-订单异常 4-锁定订单 5-协商撤销中 6-客服接入中 7-已处理 10-已结算 11-已撤下
        $gameId = 1; //默认王者荣耀
        $data = [
            'customerId'  => $customerId,
            'serverId' => $serverId,
            'serverName' => $serverName,
            'title'  => $title,
            'price' => $price,
            'timeLimit'  => $timeLimit,
            'saveDeposit' => $saveDeposit,
            'efficiencyDeposit'  => $efficiencyDeposit,
            'account' => $account,
            'password'  => $password,
            'nickname' => $nickname,
            'contactMobile'  => $contactMobile,
            'qqNum' => $qqNum,
            'publishType' => $publishType,
            'isReceive' => $isReceive,
            'rateStatus' => $rateStatus,
            'gameId' => $gameId,
            'createTime' => date('Y-m-d h:i:s',time())
        ];

        $this->validateCheck($data);

        $res = db('rate')->insert($data);

        if($res){
            $this->successReturn('提交成功');
        } else {
            $this->errorReturn('订单提交失败');
        }
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