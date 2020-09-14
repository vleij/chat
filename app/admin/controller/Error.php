<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/14
 * Time: 11:28
 */

namespace app\admin\controller;


class Error
{
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return replace(config('status.action_not_font'), "找不到该{$name}控制器", [], 404);
    }
}