<?php
namespace app\admin\controller;

use think\Validate;
use think\Cache;
use think\Db;
class Api extends Base{
    public function checkReward(){
        $date = date("Y-m-d",time());
        $u_data = [];
        $reward = Db::name('reward')->where(['createDate' => $date])->select();
        if(count($reward)){
            foreach ($reward as $value){
                Db::name('user')->execute("update f_user set userMoney = userMoney + :amount where userId = :userId", ['userId'=> $value['userId'], 'amount'=> $value['amount']]);
                //Db::name('user')->where(['userId' => $value['userId']])->update(['userMoney' => ['exp', "userMoney + $value('amount')"]]);
            }
            $this->successReturn('成功');
        }
        $this->errorReturn('失败');
    }

}