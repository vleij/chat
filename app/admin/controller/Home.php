<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 14:46
 */

namespace app\admin\controller;


use app\BaseController;
use think\facade\View;
use app\common\business\home as HomeBusiness;
class Home extends BaseController
{
    /**
     * Notes:后台框架模板
     * User: Administrator
     * Date: 2020/9/14
     * Time: 11:37
     * @return string
     * @author: 雷佳
     */
    public function index(){
        $menu = new HomeBusiness();
        $menu_list = $menu->getAllMenusData();
        View::assign([
            'menu'  => $menu_list,
        ]);
        return View::fetch();
    }

    /**
     * Notes:框架主体模板
     * User: Administrator
     * Date: 2020/9/14
     * Time: 11:38
     * @return string
     * @author: 雷佳
     */
    public function main(){
        return View::fetch();
    }
}