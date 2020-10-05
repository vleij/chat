<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/1 0001
 * Time: 14:44
 */

namespace app\service\business;
use app\common\model\mysql\Service;
use Exception;
class ServiceUser
{
    public static function login($data)
    {
        try {
            $serviceUserObj = new Service();
            $serviceUser = self::getServiceUser($data['username']);
            if(empty($serviceUser)){
                throw new Exception('不存在该客户');
            }
            if ($serviceUser['user_pwd'] != self::md5($data['password'])) {
                throw new Exception('密码错误');
            }
            $updateDate = [
                'last_login_time' => time(),
                'last_login_ip' => request()->ip(),
                'update_time' => time(),
            ];
            $res = $serviceUserObj->updateById($serviceUser['id'], $updateDate);
            if (empty($res)) {
                throw new Exception('登录失败');
            }
        } catch (\Exception $e){
            throw new Exception($e->getMessage());
        }
        //记录session
        session(config('service.session_service'), $serviceUser);
        session(config('service.session_service_id'), $serviceUser['id']);
        return true;
    }

    public static function md5($password)
    {
        $psw = md5((string)$password.config('app.salt'));
        $salt = substr($psw,-5,3);
        $psw = crypt($psw, $salt);
        return $psw;
    }

    public static function getServiceUser($username)
    {
        $serviceUserObj = new Service();
        $serviceUser = $serviceUserObj->getServiceUsername($username);
        if (empty($serviceUser) || $serviceUser->status != config('status.mysql.table_normal')) {
            return false;
        }
        $serviceUser = $serviceUser->toArray();
        return $serviceUser;
    }
}