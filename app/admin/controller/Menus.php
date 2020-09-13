<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/12
 * Time: 19:02
 */

namespace app\admin\controller;


use app\BaseController;
use think\facade\View;
use app\admin\model\Menu as MenuModel;
class Menus extends BaseController
{
    public function index()
    {

        return View::fetch();
    }

    public function menu_list()
    {
        $get = input('get.');
        $menu = new MenuModel();
        $list = $menu->page($get['offset'],$get['limit'])->select();
        $total = $menu->count();
        return json(['rows'=>$list, 'total'=>$total]);
    }
}