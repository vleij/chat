<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/20 0020
 * Time: 22:06
 */

namespace app\service\middleware;
use think\Facade\Session;

class ServiceCheck
{
    public function handle($request, \Closure $next)
    {
        //前置中间件
        if (!Session::has(config('service.session_service')) && !preg_match('/login/',$request->pathinfo())) {
            return redirect((string)url('/login/login'));
        }
        return $next($request);
        // 后置中间件
    }
}