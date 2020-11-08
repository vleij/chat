<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/3 0003
 * Time: 13:07
 */

namespace app\common\model\mysql;


use think\model\Pivot;

class Message extends Pivot
{
    public function getUserSMessage($uid, $sid)
    {
        $subQuery = $this->where(['u_id'=>$uid,'s_id'=>$sid])->field('content,create_time,msg_type,send_name,send_id,receive_id,s_id,u_id,avatar')->order('create_time desc')->limit(10)->buildSql();
        return $this->table($subQuery . 'record')->order('create_time asc')->select();
    }
}