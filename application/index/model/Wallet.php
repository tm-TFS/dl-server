<?php

namespace app\index\model;

use think\Model;
use think\Db;

class Wallet extends Model
{
    public function recharge(){
        Db::transaction(function(){

            $wallet = new Wallet;
            $res = $wallet->select();
            return $res;
        });

    }

}