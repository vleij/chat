<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/14
 * Time: 18:07
 */

namespace app\common\model\mysql;

use think\model\Pivot;
class AuthGroupAccess extends Pivot
{

    protected $name = 'auth_group_access';

}