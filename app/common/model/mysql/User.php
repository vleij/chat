<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/3 0003
 * Time: 13:03
 */

namespace app\common\model\mysql;


use think\Model;

class User extends Model
{
    protected $name = 'user';
    public function roles()
    {
        return $this->belongsToMany(Service::class, SuMain::class,'','user_id');
    }

}