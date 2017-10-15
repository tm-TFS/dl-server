<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class AdList extends Model{
    public function add(){
        $ad_list = input('adList/a');
        foreach ($ad_list as &$key){
            $key = json_decode(html_entity_decode($key));
        }

        $data = [];

        foreach ($ad_list as $value){
            $_arr = [];
            $_arr['imgUrl'] = $value->imgUrl;
            $_arr['adName'] = $value->adName;
            $_arr['adUrl'] = $value->adUrl;
            if(strpos($_arr['adUrl'], 'http://') === false && strpos($_arr['adUrl'], 'https://') === false){
                $_arr['adUrl'] = 'http://' . $_arr['adUrl'];
            }
            $data[] = $_arr;
        }
        /*if(count($ad_list) != 10){
            return WSTReturn("广告数量不正确，应必须为10个");
        }*/

        $this->where('id', '>', 0)->delete();
        $res = $this->saveAll($data);

        if(count($res) != 10){
            return WSTReturn("修改失败");
        }

        return WSTReturn("", 1, '修改成功');
    }

    public function getList(){
        $res = $this->field('id, adName as name, imgUrl, adUrl as url')->select();
        return WSTReturn("", 1, $res);
    }
}