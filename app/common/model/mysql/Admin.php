<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/14
 * Time: 18:16
 */

namespace app\common\model\mysql;

use think\Model;
class Admin extends Model
{

    protected $name = 'admin';

    public function admin()
    {
        return $this->belongsToMany(AuthRule::class, AuthGroupAccess::class,'','uid');
    }
}