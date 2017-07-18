<?php
namespace app\admin\controller;

use think\Validate;
class Customer extends Base{
    public function __construct() {
        //token 验证
        $this->token_check(input('uid'), input('token'));
    }

    public function getCustomerList (){

        $openId = input('openId');
        $customerName = input('customerName');
        $customerLevel = input('customerLevel'); // 客户等级
        $customerType = input('customerType');  //1-普通会员 2-代练 3-优质代练
        $pageId = input('pageId');
        $mobile = input('mobile');
        $pageSize = input('pageSize') ? input('pageSize') : 10;

        $condition = array();
        if($customerType){
            $condition['type'] = $customerType;
        }
        if ($openId) {
            $condition['c.openid'] = ['like','%'.$openId.'%'];
        }
        if ($customerName) {
            $condition['c.customerName'] = ['like','%'.$customerName.'%'];
        }
        if ($mobile) {
            $condition['c.mobile'] = ['like','%'.$mobile.'%'];
        }
        $list = db('customer')
            ->alias('c')
            ->where($condition)
            ->field('c.*,w.balanceAmount, w.frozenAmount, w.enableAmount')
            ->join('dl_wallet w', 'w.id = c.walletId', 'left')
            ->paginate($pageSize, false, ['page' => $pageId]);

        //dump(db('customer')->getLastSql());exit;
        //$count = $list->render();
        //$list['totalPage'] = ceil($list['total']/$list['per_page']);
        $this->response['status'] = 1;
        $this->response['content'] = $list;
        $this->ajaxReturn();
    }

    public function getCustomerDetail () {
        $id = input('customerId');
        $condition = [];
        if($id){
            $condition = array('c.id'=>$id);
        }
        $res = db('customer')
            ->alias('c')
            ->where($condition)
            ->field('c.*,w.balanceAmount, w.frozenAmount, w.enableAmount')
            ->join('dl_wallet w', 'w.id = c.walletId', 'left')
            ->find();
        if(!$res){
            $this->errorReturn('找不到该用户');
            exit;
        }

        $this->successReturn($res);
    }

    public function disableCustomer () {
        $id = input('customerId');
        $condition = [];
        if($id){
            $condition = array('id'=>$id);
        } else {
            $this->errorReturn('用户编码错误');
            exit;
        }
        $res = db('customer')->where($condition)->update(array('status'=>0));

        $this->successReturn('操作成功');
    }

    public function enableCustomer () {
        $id = input('customerId');
        $condition = [];
        if($id){
            $condition = array('id'=>$id);
        } else {
            $this->errorReturn('用户编码错误');
            exit;
        }
        $res = db('customer')->where($condition)->update(array('status'=>1));
        $this->successReturn('操作成功');
    }

}