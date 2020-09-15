<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/14
 * Time: 19:32
 */

namespace app\common\model\mysql;

use think\Model;
class AuthGroup extends Model
{
    protected $name = 'auth_group';
    public function authGroup()
    {
        return $this->belongsToMany(Admin::class, AuthGroupAccess::class,'','group_id');
    }

    public function getRules($admin_cate)
    {
        $data = $this->find($admin_cate)->value('rules');
        return $data;
    }
}