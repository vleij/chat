<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/20 0020
 * Time: 21:38
 */

namespace app\service\controller;


use app\BaseController;
use think\facade\View;
class Login extends Base
{
    protected $middleware = [
        \app\service\middleware\ServiceCheck::class  => ['except' => ['login']]
    ];
    public function login(){
        return View::fetch();
    }
}