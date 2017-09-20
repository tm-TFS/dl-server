<?php

namespace app\admin\model;

use think\Model;

class User extends Model
{


    /**
     * 分页
     */
    public function pageQuery($where = []){
        /******************** 查询 ************************/
        $pageId = input('pageId') ? input('pageId') : 1;
        $pageSize = input('pageSize') ? input('pageSize') : 10;

        /********************* 取数据 *************************/
        $rs = $this->where($where)
            ->order('createTime desc')
            ->paginate($pageSize, false, ['page'=>$pageId]);
        return WSTReturn('', 1, $rs);
    }

    public function register () {
        $data = array ();
        $data['loginName'] = input("loginName");    //登录账户，若没有则用id
        $data['loginPwd'] = md5(input('loginPwd'));
        $data['cloginPwd'] = md5(input('cloginPwd'));
        $data['payPwd'] = md5(input('payPwd'));
        $data['cpayPwd'] = md5(input('cpayPwd'));
        $data['userType'] = (int)input("userType");  //1-业务员 2-主任 3-经理 4-总监
        $data['userSex'] = (int)input('userSex');    //0-女 1-男
        $data['trueName'] = input('trueName');
        $data['userPhone'] = input('userPhone');
        $data['address'] = input('address');
        $data['bankName'] = input('bankName');
        $data['bankAccount'] = input('bankAccount');
        $data['accountName'] = input('accountName');
        $data['recommender'] = input('recommender');
        $data['leaderNo'] = input('leaderNo');  //接点人编号
        $data['direction'] = (int)input('direction');  //所在位置 1-左 2-右

        //以上部分需要验证是否为空

        $loginName = $data['loginName'];

        //检测账号是否存在
        $crs = WSTCheckLoginKey($loginName);

        if($crs['status'] != 1){
            return $crs;
        }

        if($data['loginPwd']!=$data['cloginPwd']){
            return WSTReturn("两次输入一级密码不一致!");
        }
        if($data['payPwd']!=$data['cpayPwd']){
            return WSTReturn("两次输入二级密码不一致!");
        }
        foreach ($data as $v){
            if($v == null || $v ==''){
                return WSTReturn("注册信息不完整!");
            }
        }
        if($data['direction'] != 1 && $data['direction'] != 2){
            return WSTReturn("所在位置不正确!");
        }

        //查询该接点人位置是否被占用
        $leader = $this->where(array('leaderNo'=> $data['leaderNo'], 'direction' => $data['direction']))->field('userId')->find();
        if(!empty($leader)){
            return WSTReturn("所在位置已被占用!");
        }

        $data['wechatNo'] = input('wechatNo');
        $data['address'] = input('address');
        $data['createTime'] = date('Y-m-d h:i:s',time());

        unset($data['cloginPwd']);
        unset($data['cpayPwd']);

        //$ip = request()->ip();
        $userId = $this->data($data)->save();
        return WSTReturn('注册成功',1);
    }

    public function changeInfo () {
        $data = array ();
        $id = (int)input('userId');
        $data['userSex'] = (int)input('userSex');    //0-女 1-男
        $data['userPhone'] = input('userPhone');
        $data['address'] = input('address');
        $data['bankName'] = input('bankName');
        $data['bankAccount'] = input('bankAccount');
        $data['accountName'] = input('accountName');

        //以上部分需要验证是否为空

        foreach ($data as $v){
            if($v == null || $v ==''){
                return WSTReturn("修改信息不完整!");
            }
        }

        $data['wechatNo'] = input('wechatNo');
        $data['address'] = input('address');

        //$ip = request()->ip();
        $result = $this->save($data, ['userId' => $id]);

        if(false !== $result){
            return WSTReturn("编辑成功", 1);
        }

        return WSTReturn('编辑失败',-1);
    }

    public function changePwd () {

        $id = (int)input('userId');
        $data = input();
        $u = $this->where('userId',$id)->field('id')->find();
        if(empty($u))return WSTReturn('无效的用户');


        if(empty($data['loginPwd'])){
            return WSTReturn('请输入一级密码',-1);
        }else if (empty($data['cloginPwd']) || $data['loginPwd'] != $data['cloginPwd']){
            return WSTReturn('两次输入一级密码不一致',-1);
        }

        if(empty($data['payPwd'])){
            return WSTReturn('请输入二级密码',-1);
        }else if (empty($data['cpayPwd']) ||$data['payPwd'] != $data['cpayPwd']){
            return WSTReturn('两次输入二级密码不一致',-1);
        }

        $result = $this->allowField(true)->save($data,['userId'=>$id]);

        if(false !== $result){
            return WSTReturn("编辑成功", 1);
        } else {
            return WSTReturn("编辑失败", -1);
        }
    }

    public function getSort() {
        $userId = input('userId');

        $f_u = $this->where('userId',$userId)->column('userId, loginName, trueName, userType');

        $s_u = $this->where(array('leaderNo' => $userId))->column('userId, loginName, trueName, userType');

        if(!empty($s_u)){
            $f_u['sub'] = $s_u;
            foreach ($f_u['sub'] as $key => &$value){
                $t_u = $this->where(array('leaderNo' => $value['userId']))->column('userId, loginName, trueName, userType');
                if(!empty($t_u)){
                    $value['sub'] = $t_u;
                    foreach ($value['sub'] as $key2 => &$value2){
                        $fourth_u = $this->where(array('leaderNo' => $value2['userId']))->column('userId, loginName, trueName, userType');
                        if(!empty($fourth_u)){
                            $value2['sub'] = $fourth_u;
                        }
                    }
                }
            }
        }

        return WSTReturn("", 1, $f_u);
    }

    public function getInfo() {
        $id = (int)input('userId');
        $u = $this->where('userId',$id)->field('loginPwd', true)->find();

        if(empty($u)){
            return WSTReturn("无效的用户", -1);
        } else {
            return WSTReturn("", 1, $u);
        }
    }

    public function getById($id){
        return $this->get(['userId'=>$id]);
    }

    public function getTree(){
        $userId = input('userId');
        $u = $this->order('leaderNo asc')->column('userId, loginName as title, trueName, userType, leaderNo');

        //该树为 一维数组 靠count 来判断层级
        /*$c_tree = self::tree($u);
        dump($c_tree);*/
        $tree = genTree($u, 'userId', 'leaderNo');
        return WSTReturn("", 1, $tree);
    }

    static public $treeList = array(); //存放无限分类结果如果一页面有多个无限分类可以使用 Tool::$treeList = array(); 清空
    /**
     * 无限级分类
     * @access public
     * @param Array $data     //数据库里获取的结果集
     * @param Int $pid
     * @param Int $count       //第几级分类
     * @return Array $treeList
     */
    static public function tree(&$data,$pid = 0,$count = 0) {
        foreach ($data as $key => $value){
            if($value['leaderNo']==$pid){

                $value['count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['userId'],$count+1);
            }
        }
        return self::$treeList ;
    }


}