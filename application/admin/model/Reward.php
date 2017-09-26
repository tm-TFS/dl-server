<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Reward extends Model
{
    public function add(){
        $data = array();
        $data['userId'] = input('userId');
        $data['rewardType'] = input('rewardType');
        $data['amount'] = input('amount');
        $data['createDate'] = date('Y-m-d',time());

        foreach ($data as $v){
            if($v == null || $v ==''){
                return WSTReturn("信息不完整!");
            }
        }

        $res = $this->save($data);
        if(!empty($res)){
            return WSTReturn("操作成功", 1);
        }
        return WSTReturn("操作失败");
    }

    public function edit(){
        $data = array();
        $where = array();
        $where['userId'] = input('userId');
        $data['amount'] = input('amount');

        foreach ($data as $v){
            if($v == null || $v ==''){
                return WSTReturn("信息不完整!");
            }
        }

        $res = $this->save($data, $where);
        if(!empty($res)){
            return WSTReturn("操作成功", 1);
        }
        return WSTReturn("操作失败");
    }

}