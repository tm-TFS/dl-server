<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use think\Cache;
use think\Request;

class User extends Model
{

    /**
     * 分页
     */
    public function pageQuery($where = [], $oeder = 'createTime desc'){
        /******************** 查询 ************************/
        $pageId = input('pageId') ? input('pageId') : 1;
        $pageSize = input('pageSize') ? input('pageSize') : 10;

        /********************* 取数据 *************************/
        $rs = $this->where($where)
            ->field('loginPwd, payPwd', true)
            ->order($oeder)
            ->paginate($pageSize, false, ['page'=>$pageId]);
        return WSTReturn('', 1, $rs);
    }

    public function register () {
        $data = array ();
        $registerFees = config('registerFee');

        $bankList = ['支付宝账户','中国农业银行','中国建设银行','中国工商银行','中国交通银行','中信银行','中国人民银行','中国邮政储蓄银行','兴业银行','农商银行'];

        $data['loginName'] = input("loginName");    //登录账户，若没有则用id
        $data['loginPwd'] = md5(input('loginPwd'));
        $data['cloginPwd'] = md5(input('cloginPwd'));
        $data['payPwd'] = md5(input('payPwd'));
        $data['cpayPwd'] = md5(input('cpayPwd'));
        $data['userType'] = (int)input("userType");  //1-业务员 2-主任 3-经理 4-总监
        $data['userSex'] = (int)input('userSex');    //0-女 1-男
        $data['trueName'] = input('trueName');
        $data['userPhone'] = input('userPhone');
        $data['bankName'] = $bankList[input('bankName/d')];
        $data['bankAccount'] = input('bankAccount');
        $data['accountName'] = input('accountName');
        $data['recommender'] = input('recommender');
        $data['agentCenter'] = input('agentCenter');    //代理中心
        $data['leaderNo'] = input('leaderNo');  //接点人编号
        $data['direction'] = (int)input('direction');  //所在位置 1-左 2-右

        //以上部分需要验证是否为空

        foreach ($data as $v){
            if($v == null || $v ==''){
                return WSTReturn("注册信息不完整!");
            }
        }

        $data['address'] = input('address');

        //银行分理处
        if(!empty($data['bankNameDetail'])){
            $data['bankName'] = $data['bankName'] . $data['bankNameDetail'];
        }

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
        if($data['direction'] != 1 && $data['direction'] != 2){
            return WSTReturn("所在位置不正确!");
        }

        //判断代理中心是否正确 userType == 4
        $agent = $this->getById($data['agentCenter']);
        if(empty($agent)){
            return WSTReturn("无效的代理中心!");
        } else {
            if($agent['userType'] != 4){
                return WSTReturn("该代理用户级别太低!");
            }
        }

        //查询该接点人位置是否被占用
        $leader = $this->where(array('leaderNo'=> $data['leaderNo'], 'direction' => $data['direction']))->field('userId')->find();
        if(!empty($leader)){
            return WSTReturn("所在位置已被占用!");
        }

        switch ($data['userType']) {
            case 1:
                $data['registerFee'] = $registerFees[1];
                break;
            case 2:
                $data['registerFee'] = $registerFees[2];
                break;
            case 3:
                $data['registerFee'] = $registerFees[3];
                break;
            case 4:
                $data['registerFee'] = $registerFees[4];
                break;
            default:
                $data['registerFee'] = 0;
                break;

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

    public function login() {
        $request = request();
        $data['loginName'] = input("loginName");    //登录账户，若没有则用id
        $data['loginPwd'] = md5(input('loginPwd'));
        $data['code'] = md5(input('code'));

        if(empty($data['loginName'])){
            return WSTReturn("请输入登录账号");
        }
        if(empty($data['loginPwd'])){
            return WSTReturn("请输入登录密码");
        }

        $time = date('Y-m-d h:i:s',time());

        $user = Db::name('user')->where(array('loginName' => $data['loginName'], 'loginPwd' => $data['loginPwd']))->find();
        if(empty($user)){
            return WSTReturn("账号或密码错误");
        }
        $this->save(array('lastTime' => $time, 'lastIP' => $request->ip()), ['userId' => $user['userId']]);
/*
        $token = md5($data['loginName'].time());

        //写入缓存
        Cache::tag('token')->set($user['userId'],$token);

        $this->response['status'] = 1;
        $this->response['content'] = $res;
        $this->response['token'] = $token;*/

        return WSTReturn('', 1, $user);

    }

    public function checkPayPwd() {
        $data = input();
        if(empty($data['payPwd'])){
            return WSTReturn('密码不能为空');
        }
        $res = $this->find(['userId' => $data['userId'], 'payPwd' => md5($data['payPwd'])]);
        if(empty($res)){
            return WSTReturn('二级密码错误');
        }
        return WSTReturn("成功", 1);
    }

    public function getMaxId() {
        $userId = $this->max('userId');
        return WSTReturn("", 1, $userId);
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
        $u = $this->where('userId',$id)->field('userId')->find();
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

        $data['loginPwd'] = md5($data['loginPwd']);
        $data['payPwd'] = md5($data['payPwd']);
        $result = $this->allowField(true)->save($data,['userId'=>$id]);

        if(false !== $result){
            return WSTReturn("编辑成功", 1);
        } else {
            return WSTReturn("编辑失败", -1);
        }
    }

    public function getSort() {
        $userId = input('userId');

        $f_u = Db::name('user')->where('userId',$userId)->field('userId, loginName, trueName, userType, createTime')->select();

        $s_u = Db::name('user')->where(array('leaderNo' => $userId))->field('userId, loginName, trueName, userType, createTime')->select();

        if(!empty($s_u)){
            //$f_u['sub'] = $s_u;
            foreach($f_u as &$f_value){
                $f_value['sub'] = $s_u;
                foreach ($f_value['sub'] as $key => &$value){
                    $t_u = Db::name('user')->where(array('leaderNo' => $value['userId']))->field('userId, loginName, trueName, userType, createTime')->select();
                    if(!empty($t_u)){
                        $value['sub'] = $t_u;
                        foreach ($value['sub'] as $key2 => &$value2){
                            $fourth_u = Db::name('user')->where(array('leaderNo' => $value2['userId']))->field('userId, loginName, trueName, userType, createTime')->select();
                            if(!empty($fourth_u)){
                                $value2['sub'] = $fourth_u;
                            }
                        }
                    }
                }
            }
        }

        return WSTReturn("", 1, $f_u);
    }

    public function getInfo() {
        $id = input('userId/d');
        $u = $this->where('userId',$id)->field('loginPwd, payPwd', true)->find();

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
        $u = $this->order('leaderNo asc')->column('userId, loginName as title, trueName, userType, leaderNo, createTime');

        //该树为 一维数组 靠count 来判断层级
        /*$c_tree = self::tree($u);
        dump($c_tree);*/
        $tree = genTree($u, 'userId', 'leaderNo');
        return WSTReturn("", 1, $tree);
    }

    public function getByIds(){
        $userIds = input('userIds');

        if(empty($userIds)){
            return WSTReturn("用户编号不能为空", -1);
        }

        $u = $this->all($userIds);

        if(empty($u)){
            return WSTReturn("无效用户", -1);
        }

        return WSTReturn("", 1, $u);
    }

    /**
     * 编辑
     */
    public function editAll(){
        $Id = (int)input('post.userId');
        $data = input();



        $u = $this->where('userId',$Id)->field('loginSecret')->find();
        if(empty($u))return WSTReturn('无效的用户');
        //判断是否需要修改密码
        if(empty($data['loginPwd'])){
            unset($data['loginPwd']);
        }else{
            $data['loginPwd'] = md5($data['loginPwd'].$u['loginSecret']);
        }
        Db::startTrans();
        try{
            if(isset($data['userPhoto'])){
                WSTUseImages(1, $Id, $data['userPhoto'], 'users', 'userPhoto');
            }

            WSTUnset($data,'createTime,userId');
            $result = $this->allowField(true)->save($data,['userId'=>$Id]);
            if(false !== $result){
                Db::commit();
                return WSTReturn("编辑成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
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