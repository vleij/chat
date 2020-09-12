<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 15:49
 */
namespace app\admin\model;
use think\facade\Session;
use think\Model;

class Menu extends Model
{
    protected $name = 'admin_menu';

//    public function menuList($menu,$id=0,$level=0){
//
//        static $menus = array();
//        foreach ($menu as $value) {
//            if ($value['pid']==$id) {
//                $value['level'] = $level+1;
//                if($level == 0)
//                {
//                    $value['str'] = str_repeat('',$value['level']);
//                }
//                elseif($level == 2)
//                {
//                    $value['str'] = '&emsp;&emsp;&emsp;&emsp;'.'└ ';
//                }
//                elseif($level == 3)
//                {
//                    $value['str'] = '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;'.'└ ';
//                }
//                else
//                {
//                    $value['str'] = '&emsp;&emsp;'.'└ ';
//                }
//                $menus[] = $value;
//                $this->menulist($menu,$value['id'],$value['level']);
//            }
//        }
//        return $menus;
//    }

    /*菜单列表*/
    public function  menu_list(){
        /*菜单开始*/
        $menu = $this->where(['is_display'=>1])->order('orders desc')->select();
        //删除不在当前角色权限里的菜单，实现隐藏
        $admin_cate = 25;//Session::get('admin_cate_id');
        $permissions = Cate::where(['id'=>$admin_cate])->value('permissions');

        $permissions = explode(',',$permissions);

        foreach ($menu as $k => $val) {
            if($val['type'] == 1 and $val['is_display'] == 1 and !in_array($val['id'],$permissions)) {
                unset($menu[$k]);
            }
        }

        foreach ($menu as $key => $value) {
            if(empty($value['parameter'])) {
                $url = url($value['module'].'/'.$value['controller'].'/'.$value['function']);
            } else {
                $url = url($value['module'].'/'.$value['controller'].'/'.$value['function'],$value['parameter']);
            }
            $menu[$key]['url'] = $url;
        }
        return $menu;
    }

    public function menusList($menu){
        $menus = array();
        //先找出顶级菜单
        foreach ($menu as $k => $val) {
            if($val['pid'] == 0) {
                $menus[$k] = $val;
            }
        }

        //通过顶级菜单找到下属的子菜单
        foreach ($menus as $k => $val) {
            foreach ($menu as $key => $value) {
                if($value['pid'] == $val['id']) {
                    $menus[$k]['list'][] = $value;
                }
            }
        }

        //三级菜单
        foreach ($menus as $k => $val) {
            if(isset($val['list'])) {
                foreach ($val['list'] as $ks => $vals) {
                    foreach ($menu as $key => $value) {
                        if($value['pid'] == $vals['id']) {
                            $menus[$k]['list'][$ks]['list'][] = $value;
                            $menus[$k]['list'][$ks]['lever'] = 3;
                        }
                    }
                }
            }
        }

        return $menus;
    }

    public function menuList(object $menu, int $id=0){

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
}