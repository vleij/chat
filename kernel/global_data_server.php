<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/18
 * Time: 15:23
 */

use \Workerman\Worker;
require_once __DIR__ . '../../vendor/autoload.php';

// 全局共享组件
$worker = new GlobalData\Server('127.0.0.1', 2207);

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
