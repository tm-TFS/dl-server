<?php
namespace app\index\controller;

use think\Validate;
class Order extends Base{
    public function getRateList (){

        //token 验证
        $this->token_check(input('customerId'), input('token'));

        $customerId = input('customerId');
        $serverId = input('serverId');
        $pageId = input('pageId');
        $pageSize = input('pageSize') ? input('pageSize') : 10;

        $data = [
            'customerId'  => $customerId,
            'serverId' => $serverId,
            'pageId' => $pageId
        ];
        $this->validateCheck($data);

        $condition = [];
        if($customerId){
            $condition['customerId'] = $customerId;
        }
        if($serverId){
            $condition['serverId'] = $serverId;
        }

        $list = model('rate')
            ->where($condition)
            ->paginate($pageSize, false, ['page'=>$pageId]);
        //dump($list);exit;
        //$count = $list->render();
        //$list['totalPage'] = ceil($list['total']/$list['per_page']);
        $this->response['status'] = 1;
        $this->response['content'] = $list;
        $this->ajaxReturn();
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