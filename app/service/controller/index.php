<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/15
 * Time: 17:05
 */
namespace app\service\controller;
use think\facade\View;
class index
{
    public function index()
    {

        View::assign('status',1);
        View::assign('word',1);
        return View::fetch();
    }
}