<?php

namespace app\index\validate;

use think\Validate;

class Index extends Validate
{
    protected $rule = [
        'account|账号' => 'require|max:25',
        'password|密码' => 'require|min:6|max: 9',
    ];

}