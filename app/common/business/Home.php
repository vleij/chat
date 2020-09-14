<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/14
 * Time: 10:12
 */
namespace app\common\business;
use app\common\model\mysql\Admin;
use app\common\model\mysql\AuthGroup;
use app\common\model\mysql\Menu as MenuModel;
use app\common\model\mysql\Cate as CateModel;
use app\common\model\mysql\AuthRule as AuthRuleModel;
use app\common\model\mysql\Admin as AdminModel;
use app\common\model\mysql\AuthGroupAccess as AuthGroupModel;
class Home
{
    /**
     * Notes: 获取
     * User: Administrator
     * Date: 2020/9/14
     * Time: 10:57
     * @return array
     * @author: 雷佳
     */
    public function getAllMenusData()
    {
        $list = $this->menu_list();
        $menu_list = $this->menuList($list);
        return $menu_list;
    }

    /**
     * Notes:递归获取所有菜单数据
     * User: 雷佳
     * Date: 2020/9/14
     * Time: 10:48
     * @param object $menu
     * @param int $id
     * @return array
     * @author: 雷佳
     */
    public function menuList($menu, int $id=0){

        $data = array();
        foreach ($menu as $k=>$v){        //PID符合条件的
            if($v['pid'] == $id){            //寻找子集

                $child = $this->menuList($menu,$v['id']);            //加入数组

                $v['child'] = $child?:array();
                $data[] = $v;//加入数组中
            }
        }
        return $data;

    }

    /**
     * Notes:菜单列表
     * User: Administrator
     * Date: 2020/9/14
     * Time: 11:46
     * @return mixed
     * @author: 雷佳
     */
//    public function  menu_list(){
//        /*菜单开始*/
//        $menus = new MenuModel();
//        $cate = new CateModel();
//        $menu = $menus->getShowMenuData();
//        //删除不在当前角色权限里的菜单，实现隐藏
//        $admin_cate = 25;//Session::get('admin_cate_id');
//        $permissions = $cate->getPermissions($admin_cate);
//
//        $permissions = explode(',',$permissions);
//
//        foreach ($menu as $k => $val) {
//            if($val['type'] == 1 and $val['is_display'] == 1 and !in_array($val['id'],$permissions)) {
//                unset($menu[$k]);
//            }
//        }
//
//        foreach ($menu as $key => $value) {
//            if(empty($value['parameter'])) {
//                $url = url($value['module'].'/'.$value['controller'].'/'.$value['function']);
//            } else {
//                $url = url($value['module'].'/'.$value['controller'].'/'.$value['function'],$value['parameter']);
//            }
//            $menu[$key]['url'] = $url;
//        }
//        return $menu;
//    }

    public function menu_list()
    {
        $admin = AuthGroup::find(1);
        $admin2 = Admin::find(1);
    }
}