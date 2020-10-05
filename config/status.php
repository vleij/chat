<?php
/**
 * 状态码配置
 */
return [
    "action_not_font" => -1,
    "succeed" => 1,
    "error" => 0,
    // mysql 模型相关状态配置
    "mysql" => [
        'table_normal' => 1, //正常
        'table_pedding' => 0, //待审核
        'table_delete' => -1 //删除
    ]
];