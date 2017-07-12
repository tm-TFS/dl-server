<?php
namespace app\index\controller;

use think\Validate;
class Customer extends Base{
    public function getCustomerList (){

        $customerId = input('customerId');
        $customerName = input('customerName');
        $customerLevel = input('customerLevel'); // 客户等级
        $isPlayer = input('isPlayer');  //是否代练
        $pageId = input('pageId');
        $pageSize = input('pageSize') ? input('pageSize') : 10;

        $condition = array();
        $condition['isPlayer'] = $isPlayer;
        if ($customerId) {
            $condition['customerId'] = $customerId;
        }
        if ($customerName) {
            $condition['customerName'] = $customerName;
        }
        if ($customerLevel) {
            $condition['customerLevel'] = $customerLevel;
        }
        $list = model('customer')
            ->where($condition)
            ->paginate($pageSize, false, ['page' => $pageId]);
        //dump($list);exit;
        //$count = $list->render();
        //$list['totalPage'] = ceil($list['total']/$list['per_page']);
        $this->response['status'] = 1;
        $this->response['content'] = $list;
        $this->ajaxReturn();
    }
}