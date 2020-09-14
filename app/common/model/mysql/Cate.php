<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 16:42
 */

namespace app\common\model\mysql;
use think\Model;

class Cate extends Model
{
    protected $name = 'admin_cate';

    /**
     * Notes:获取权限数据
     * Date: 2020/9/14
     * Time: 11:55
     * @param $admin_cate
     * @return mixed
     * @author: 雷佳
     */
    public function getPermissions($admin_cate)
    {
        $permissions = $this->where(['id'=>$admin_cate])->value('permissions');
        return $permissions;
    }
}