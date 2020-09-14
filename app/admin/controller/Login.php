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
    /**
     * Notes:后台登录模板
     * User: Administrator
     * Date: 2020/9/14
     * Time: 11:39
     * @return string
     * @author: 雷佳
     */
    public function index()
    {
        return View::fetch('login');
    }
}