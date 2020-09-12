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
        $menu = new MenuModel();
        $list = $menu->select();
    }
}