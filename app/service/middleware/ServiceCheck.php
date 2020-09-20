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
        if (!Session::has('service')) {
            return redirect('http://www.chat.com/service.php/login/login');
        }

        return $next($request);
    }
}