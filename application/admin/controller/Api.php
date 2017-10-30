<?php
namespace app\admin\controller;

use think\Validate;
use think\Cache;
use think\Db;
class Api extends Base{
    public function checkReward(){
        $date = input('date');
        if(!$date){
            $date = date("Y-m-d",time());
        }
        $reward = Db::name('reward')->where(['createDate' => $date, "rewardType" => 1, 'isChecked' => 0])->select();
        $total_amount = 0;
        if(count($reward)){
            foreach ($reward as $value){
                $total_amount += $value['amount'];
                Db::startTrans();
                try{
                    Db::name('reward')->where(array('id'=> $value['id']))->update(array('isChecked' => 1));
                    Db::name('user')->execute("update f_user set userMoney = userMoney + :amount where userId = :userId", ['userId'=> $value['userId'], 'amount'=> $value['amount']]);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $error_count = Db::name('reward')->where(['createDate' => $date, "rewardType" => 1, 'isChecked' => 0])->count();
                    $msg = "执行错误，还有" . $error_count . "条记录未执行，请联系管理员";
                    sendMsg($msg);
                    echo $msg;
                    exit;
                }

                //Db::name('user')->where(['userId' => $value['userId']])->update(['userMoney' => ['exp', "userMoney + $value('amount')"]]);
            }
        }
        $msg = $date . "奖金维护成功，共执行" . count($reward) . "条记录，输出奖金共" . $total_amount . "元";
        sendMsg($msg);
        echo $msg;
    }

}