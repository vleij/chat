<?php
/**
 * run with command
 * php kernel.php kernel
 */

ini_set('display_errors', 'on');
use Workerman\Worker;

if(strpos(strtolower(PHP_OS), 'win') === 0)
{
    exit("kernel.php not support windows, please use start_for_win.bat\n");
}

// 检查扩展
if(!extension_loaded('pcntl'))
{
    exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

if(!extension_loaded('posix'))
{
    exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

// 标记是全局启动
define('GLOBAL_START', 1);

require_once __DIR__ . '/vendor/autoload.php';


// 加载所有Applications/*/kernel.php，以便启动所有服务
foreach(glob(__DIR__ . '../kernel/start*.php') as $start_file)
{
    require_once $start_file;
}
// 全局共享组件
$worker = new GlobalData\Server('127.0.0.1', 2207);
// 运行所有服务
Worker::runAll();
