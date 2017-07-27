<?php

namespace app\index\validate;

use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'customerId|用户编码' => 'number',
        'serverId|区服' => 'require',
        'title|订单任务' => 'require',
        'price|订单价格' => 'require',
        'timeLimit|订单时限' => 'number',
        'saveDeposit|安全保证金' => 'number',
        'efficiencyDeposit|效率保证金' => 'number',
        'account|账号' => 'require',
        'password|游戏密码' => 'require',
        'nickname|游戏角色名' => 'require',
        'contactMobile|游戏角色名' => 'require',
    ];

}