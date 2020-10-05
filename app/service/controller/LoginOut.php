<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/1 0001
 * Time: 10:46
 */

namespace app\service\controller;


class LoginOut
{
    public function loginOut()
    {
        session(config('service.session_service'), null);
        return redirect(url('/'));
    }
}