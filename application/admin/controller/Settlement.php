<?php
namespace app\admin\controller;

use app\admin\model\Settlement as M;

class Settlement extends Base{
    /*public function __construct() {
        //token 验证
        $this->token_check(input('uid'), input('token'));
    }*/

    //后台充值
    public function deal(){

        $m = new M();

        $res = $m->deal();


        if($res['status'] == 1){
            $this->successReturn($res['msg']);
        } else {
            $this->errorReturn($res['msg']);
        }

    }

    public function getList() {

        $m = new M();

        $res = $m->pageQuery();


        if($res['status'] == 1){
            $this->successReturn($res['data']);
        } else {
            $this->errorReturn($res['msg']);
        }

    }

    //获取钱包明细
    public function getSettlementList () {
        $walletId = input('walletId') ? input('walletId') : 1;
        $pageId = input('pageId') ? input('pageId') : 1;
        $pageSize = input('pageSize') ? input('pageSize') : 10;
        $condition = array(
            'w.walletToId'=>$walletId,
            'w.walletFromId'=>$walletId,
        );

        $list = db('wallet_bill_detail')
            ->alias('w')
            ->field('w.*,c1.customerName as fromName,c2.customerName toName')
            ->join('dl_customer c1', 'w.walletFromId = c1.walletId', 'left')
            ->join('dl_customer c2', 'w.walletToId = c2.walletId', 'left')
            ->whereOr($condition)
            ->paginate($pageSize, false, ['page'=>$pageId]);

        $this->successReturn($list);
    }

    //处理 提现 充值申请
    public function checkSettlement() {
        $m = new M();

        $res = $m->checkSettlement();

        if($res['status'] == 1){
            $this->successReturn($res['data']);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

}