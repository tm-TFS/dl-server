<?php
namespace app\index\controller;

use think\Validate;
class Order extends Base{
    public function getRateList (){

        //token 验证
        $this->token_check(input('uid'), input('token'));

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

        $list = model('rate')->where($condition)->paginate($pageSize); //获取除password之外的字段
        //dump(model('rate')->getLastSql());exit;
        $count = $list->render();

        $this->response['status'] = 1;
        $this->response['content'] = array('list'=>$list, 'count'=>$count);
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