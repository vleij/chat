<?php
// 应用公共文件

/**
 * Notes:通用化Api数据处理
 * User: Administrator
 * Date: 2020/9/14
 * Time: 11:17
 * @param $status
 * @param string $message
 * @param array $data
 * @param string $httpStatus
 * @return \think\response\Json
 * @author: 雷佳
 */
function replace(int $status, $message = "error", $data = [], $httpStatus = '200')
{
    $result = [
        "status" => $status,
        "message" => $message,
        "result" => $data
    ];
    return json($result, $httpStatus);
}