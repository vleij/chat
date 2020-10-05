<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/1 0001
 * Time: 21:27
 */

namespace app\service\controller;


use think\facade\View;

class ServiceChat
{
    public function index()
    {
        View::assign('status',1);
        View::assign('word',1);
        return View::fetch();
    }
}