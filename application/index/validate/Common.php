<?php

namespace app\index\validate;

use think\Validate;

class Common extends Validate
{
    protected $rule = [
        'account|账号' => 'require|max:20',
        'password|密码' => 'require',
    ];

}