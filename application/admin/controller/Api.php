<?php

namespace app\admin\controller;

use think\Validate;
use think\Cache;
use think\Db;

class Api extends Base
{
    public function checkReward()
    {
        $date = input('date');
        if (!$date) {
            $date = date("Y-m-d", time() - 3600 * 24);
        }
        $reward = Db::name('reward')->where(['createDate' => $date, "rewardType" => 1, 'isChecked' => 0])->select();
        $total_amount = 0;
        if (count($reward)) {
            foreach ($reward as $value) {
                $total_amount += $value['amount'];
                Db::startTrans();
                try {
                    Db::name('reward')->where(array('id' => $value['id']))->update(array('isChecked' => 1));
                    Db::name('user')->execute("update f_user set userMoney = userMoney + :amount where userId = :userId", ['userId' => $value['userId'], 'amount' => $value['amount']]);
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

    public function cleanUser()
    {
        $users = Db::name('user')->where(array('moneyFrozen' => 0))->select();
        $pass_time = time() - 3600 * 48;
        $frozen_time_1 = time() - 3600 * 24 * 30 * 2;
        $frozen_time_2 = time() - 3600 * 24 * 30 * 4;
        $frozen_users = [];
        $delete_users_count = 0;

        foreach ($users as $user) {
            //清除48小时未激活的用户
            if ($user['userStatus'] != 1 && $pass_time > strtotime($user['lastTime'])) {
                Db::name('user')->where(array('userId' => $user['userId']))->delete();
                $delete_users_count ++;
                continue;
            }

            //冻结注册时间超过二个月，且没有发展会员的用户 moneyFrozen
            if ($frozen_time_1 > strtotime($user['createTime'])) {
                $one_sub_count = Db::name('user')->where(array('leaderNo' => $user['userId']))->count();
                if ($one_sub_count < 1) {
                    Db::name('user')->where(array('userId' => $user['userId']))->update(array('moneyFrozen' => 1));
                    $frozen_users[] = $user['userId'];
                    continue;
                }

                //冻结注册时间超过四个月，且没有发展2个会员的用户 moneyFrozen
                if ($frozen_time_2 > strtotime($user['createTime']) && $one_sub_count < 2){
                    Db::name('user')->where(array('userId' => $user['userId']))->update(array('moneyFrozen' => 1));
                    $frozen_users[] = $user['userId'];
                    continue;
                }
            }
        }

        //输出冻结用户编号，删除用户数量
        $frozen_user_str = implode('、', $frozen_users);
        $str = "冻结用户编号： " . $frozen_user_str . " \n删除用户共 " . $delete_users_count ."个";
        echo $str;
    }

}