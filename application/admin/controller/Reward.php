<?php
namespace app\admin\controller;

use app\admin\model\Reward as Mre;
use think\Db;
class Reward extends Base{
    public function getList() {
        $m = new Mre();
        $res = $m->getList();
        if($res['status'] == 1){
            $this->successReturn($res['data']);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

    public function getTotalAmount(){

        $m = new Mre();
        $res = $m->getTotalAmount();

        if($res['status'] == 1){
            $this->successReturn($res['data']);
        } else {
            $this->errorReturn($res['msg']);
        }
    }

}