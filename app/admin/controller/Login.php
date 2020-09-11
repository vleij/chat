<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 12:17
 */
namespace app\admin\controller;
use app\BaseController;
use think\facade\View;

class Login extends BaseController
{
    public function index()
    {
        return View::fetch('login');
    }
}