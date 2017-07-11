<?php
namespace app\index\controller;

use think\Validate;
use think\Db;
class Settlement extends Base{

    //充值
    public function recharge(){
        //token 验证
        //$this->token_check(input('customerId'), input('token'));

        $customerId = input('customerId');
        $walletId = input('walletId') ? input('walletId') : 1;
        $amount = input('amount') ? input('amount') : 100;
        $tradeType = 1; // 0支付1充值2提现3转账4退款
        $tradeDescription = '描述';
        $paymentType = 0; //0线下1支付宝2微信3余额支付
        $tradeOrderId = input('tradeOrderId') ? input('tradeOrderId') : 1231;
        $tradeNo = input('tradeNo') ? input('tradeNo') : 1231;

        $detail_add = array(
            'walletToId' => $walletId,
            'amount' => $amount,
            'tradeType' => $tradeType,
            'tradeDescription' => $tradeDescription,
            'paymentType' => $paymentType,
            'tradeOrderId' => $tradeOrderId,
            'tradeNo' => $tradeNo,
            'createUser' => 'admin'
        );

        Db::startTrans();
        try{
            db('wallet_bill_detail')->insert($detail_add);
            db('wallet')
                ->where('id', $walletId)
                ->update([
                    'balanceAmount'  => ['exp',"balanceAmount + $amount"],
                    'enableAmount' => ['exp',"enableAmount + $amount"],
                ]);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->errorReturn();
            exit;
        }

        $this->successReturn();

    }

    //获取钱包明细
    public function getSettlementList () {
        $walletId = input('walletId') ? input('walletId') : 1;
        $pageId = input('pageId') ? input('pageId') : 1;
        $pageSize = input('pageSize') ? input('pageSize') : 10;
        $condition = array(
            'walletToId'=>$walletId,
            'walletFromId'=>$walletId,
        );

        $list = db('wallet_bill_detail')->whereOr($condition)->paginate($pageSize, false, ['page'=>$pageId]);

        $this->successReturn($list);
    }



    protected function validateCheck($data) {

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