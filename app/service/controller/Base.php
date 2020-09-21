<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/20 0020
 * Time: 22:29
 */

namespace app\service\controller;


use app\BaseController;

class Base extends BaseController
{
    protected $middleware = [\app\service\middleware\ServiceCheck::class];
}