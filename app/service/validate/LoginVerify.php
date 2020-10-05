<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/24 0024
 * Time: 22:05
 */

namespace app\service\validate;

use think\Validate;
class LoginVerify extends Validate
{
    protected $rule = [
        'username'  => 'require|max:25',
        'password'   => 'require|max:25',
    ];

    protected $message = [
        'username.require' => '请填写用户名',
        'username.max'     => '用户名最多不能超过25个字符',
        'password.require'   => '请填写密码',
        'password.max'   => '请填写密码',
    ];
}