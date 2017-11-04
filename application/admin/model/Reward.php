<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Reward extends Model
{
    public function getInfo(){
        $id = input('userId/d');
        $date = date('Y-m-d');
        $u = $this->query("select a.*,r.amount from f_ad_click a LEFT JOIN f_reward r on r.userId = a.userId and r.createDate = a.createDate where a.userId = :userId and a.createDate = :date limit 1",
            ['userId' => $id, 'date' => $date]);
        //$money = Db::name('reward')->where(array('userId' => $id, 'createDate' => $date))->find();
        if(count($u)){
            return WSTReturn("", 1, $u[0]);
        } else {
            $res = [
                'total' => 0,
                'amount' => 0
            ];
            return WSTReturn("", 1, $res);
        }
    }

    public function getList() {
        $userId = input('userId/d');
        $f_date = input('f_date');
        $e_date = input('e_date');
        $pageSize = 10;
        $pageId = input('pageId/d');

        if(empty($userId)){
            return WSTReturn("缺少会员编码", -1);
        }

        $where = ['userId' => $userId ];
        $where2 = [];

        if($f_date){
            $where['createDate'] = ['>=', $f_date];
        }

        if($e_date) {
            $where2['createDate'] = ['<=', $e_date];
        }

        $list = $this->where($where)
            ->where($where2)
            ->order('createDate desc')
            ->paginate($pageSize, false, ['page'=>$pageId]);

        if(empty($list)){
            return WSTReturn("查询失败", -1);
        }
        return WSTReturn("", 1, $list);
    }

    public function getTotalAmount(){
        $userId = input('userId/d');

        if(empty($userId)){
            return WSTReturn("缺少会员编码", -1);
        }
        $total = $this->where(['userId' => $userId])->sum('amount');

        if(empty($total)){
            return WSTReturn("", 1, 0);
        }
        return WSTReturn("", 1, $total);

    }

}