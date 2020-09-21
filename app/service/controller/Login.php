<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/20 0020
 * Time: 21:38
 */

namespace app\service\controller;


use app\BaseController;
use think\facade\View;
use think\facade\Request;
class Login extends Base
{
    protected $middleware = [
        \app\service\middleware\ServiceCheck::class  => ['except' => ['login']]
    ];
    public function login(){
        if($this->request->isPost()){
            $post = input('post.');
            dump($post);die;
        }else{
            return View::fetch();
        }
    }
}