<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 9:24
 */
namespace app\index\controller;
use think\facade\View;
class Index
{
    public function index()
    {
        return View::fetch();
    }
}