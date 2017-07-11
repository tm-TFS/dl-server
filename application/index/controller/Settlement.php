<?php
namespace app\index\controller;

use think\Validate;
class Settlement extends Base{

    //充值
    public function recharge(){
        //token 验证
        //$this->token_check(input('customerId'), input('token'));

        $customerId = input('customerId');
        $walletId = input('walletId') ? input('walletId') : 1;

        $res = model('wallet')->recharge();
        echo json_encode($res);

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