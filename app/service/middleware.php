<?php
// 全局中间件定义文件
return [
    \think\middleware\SessionInit::class,
    \app\service\middleware\ServiceCheck::class,
];
