<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/25 0025
 * Time: 22:16
 */

namespace app\common\model\mysql;


use think\Model;

class Service extends Model
{

    public function roles()
    {
        return $this->belongsToMany(User::class, SuMain::class,'','service_id');
    }

    public function rolesUserMessage(int $id)
    {
        $res = $this::where('serial_number', $id)->field('serial_number as id')->find();
        return $res->roles()->join('(select * from c_message where id in (SELECT SUBSTRING_INDEX(GROUP_CONCAT(id order by create_time desc),",",1) as max_id FROM c_message where s_id = '.$id.' GROUP BY u_id)) m','pivot.user_id = m.u_id','left')->where('c_user.service_id', $id)->field('user_name,c_user.id,online,user_avatar,m.content,m.create_time as last_msg_time')->select();
    }

    /**
     * 根据用户名获取客服端数据
     * @param $username
     * @return array|bool|null|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceUsername($username)
    {
        if(empty($username)){
            return false;
        }
        $where = ['user_name'=> trim($username)];
        $result = $this->where($where)->find();
        return $result;
    }

    public function updateById($id, $data)
    {
        $id = intval($id);
        if(empty($id) || empty($data) || !is_array($data)){
            return false;
        }
        $where = ['id'=>$id];
        return $this->where($where)->save($data);
    }

    public function getServiceId($uid)
    {
        if(empty($uid)){
            return false;
        }
        $where = ['serial_number'=> trim($uid)];
        $result = $this->where($where)->find();
        return $result;
    }
}