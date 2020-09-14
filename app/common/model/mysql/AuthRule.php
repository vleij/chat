<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/14
 * Time: 12:59
 */

namespace app\common\model\mysql;
use think\Model;
use think\facade\Request;
use think\facade\Db;
class AuthRule extends Model
{
    protected $name = 'auth_rule';

    public function addRule()
    {
        // 启动事务
        Db::startTrans();
        try {
            $data = Request::only(['name','title']);
            $res = $this->save($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            return 10501;
        }
        return $res;
    }

    public function all()
    {
        $data = $this->select();
        return $data;
    }
}