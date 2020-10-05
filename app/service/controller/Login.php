<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/20 0020
 * Time: 21:38
 */

namespace app\service\controller;


use think\App;
use think\facade\View;
use app\service\validate\LoginVerify;
use app\service\business\ServiceUser;
class Login extends Base
{
    public function initialize()
    {
        //判断是否登录
        if($this->isLogin()){
            return $this->redirect(url('/index'));
        }
    }

    public function login(){
        if($this->request->isPost()){
            $username = $this->request->param('username', '', 'trim');
            $password = $this->request->param('password', '', 'trim');
            $data = ['username'=>$username,'password'=>$password];
            $LoginVerify = new LoginVerify();
            $result = $LoginVerify->check($data);
            if(!$result){
                return replace(config('status.error'), $LoginVerify->getError(), [], 200);
            }
            try{
                $result = ServiceUser::login($data);
            }catch (\Exception $e){
                return replace(config('status.error'), $e->getMessage(), [], 200);
            }
            if($result){
                return replace(config('status.succeed'), '登入成功', [], 200);
            }else{
                return replace(config('status.error'), '登入失败', [], 200);
            }
        }else{
            return View::fetch();
        }

    }


}