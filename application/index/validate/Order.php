<?php

namespace app\index\validate;

use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'customerId|用户编码' => 'number',
        'serverId|服务器编码' => 'number',
        'pageId|页码' => 'number',
    ];

}