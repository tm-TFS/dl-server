<?php

namespace app\index\model;

use think\Model;

class Wallet extends Model
{
    public function recharge(){
        $wallet = new Wallet;

        $res = $wallet->select();
        return $res;
    }

}