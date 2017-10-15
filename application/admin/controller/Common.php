<?php
namespace app\admin\controller;

use think\Validate;
use think\Cache;
use think\Db;
class Common extends Base{
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $imgUrl = request()->root(true) . '/uploads/' . $info->getSaveName();
                $imgUrl = str_replace("\\","/",$imgUrl);
                $this->successReturn($imgUrl);
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                $err = $file->getError();
                $this->errorReturn($err);
            }
        }
    }

    public function getArticle() {
        $type = input('type');
        $article = Db::name('article')->where(['type' => $type])->find();
        $this->successReturn($article['content']);
    }

    public function editArticle(){
        $type = input('type');
        $content = input('content');
        $res = DB::name('article')->where(['type' => $type])->update(['content' => $content]);
        if($res){
            $this->successReturn('success');
        } else {
            $this->errorReturn('失败');
        }
    }

}