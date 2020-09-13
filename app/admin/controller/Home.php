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
use think\facade\Request;
use app\admin\model\Menu as MenuModel;
class Home extends BaseController
{
    public function index(){
        $menu = new MenuModel();
        $list = $menu->menu_list();
        $menu_list = $menu->menuList($list);

        View::assign([
            'menu'  => $menu_list,
        ]);
        return View::fetch();
    }

    public function main(){
        return View::fetch();
    }

    public function add_menu()
    {
        $menu = new MenuModel();
        // 过滤post数组中的非数据表字段数据
        $data = Request::only(['name','email']);
        $menu->save($data);
    }
}