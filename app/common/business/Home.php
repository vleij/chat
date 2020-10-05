<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/14
 * Time: 10:12
 */
namespace app\common\business;
use app\common\model\mysql\AuthGroup as AuthGroupModel;
use app\common\model\mysql\AuthRule as AuthRuleModel;
use think\facade\Request;
class Home
{
    /**
     * Notes: 前端获取菜单数据
     * User: Administrator
     * Date: 2020/9/14
     * Time: 10:57
     * @return array
     * @author: 雷佳
     */
    public function getAllMenusData()
    {
        $list = $this->menu_assemble();
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
    public function menu_assemble()
    {
        $admin_cate = 1;//Session::get('admin_cate_id');
        $authRule = new AuthRuleModel();
        $authGroup = new AuthGroupModel();
        $menu = $authRule->allRule()->toArray();
        $permissions = $authGroup->getRules($admin_cate);
        $permissions = explode(',',$permissions);
        $possess_rule = [];

        foreach ($menu as $k => $val) {
            if($val['type'] == 1 and $val['is_display'] == 1 and in_array($val['id'],$permissions) and \think\facade\App::instance()->http->getName() == $val['module']) {
                if(empty($value['parameter'])) {
                    $url = (string)url('/'.$val['title']);
                } else {
                    $url = (string)url('/'.$val['title'],$val['parameter']);
                }
                $val['url'] = $url;
                $possess_rule[$k] = $val;
            }
            continue;
        }
        return $possess_rule;
    }
}