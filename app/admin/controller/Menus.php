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
use app\common\business\Menus as MenusBusiness;
use app\common\business\Home as HomeBusiness;
use app\common\model\mysql\AuthRule as AuthRuleModel;

class Menus extends BaseController
{
    /**
     * Notes:菜单管理模板
     * User: Administrator
     * Date: 2020/9/14
     * Time: 11:37
     * @return string
     * @author: 雷佳
     */
    public function index()
    {
        $menusB = new MenusBusiness();
        $homeB= new HomeBusiness();
        $menu = $menusB->menuList($homeB->menu_assemble());
        View::assign('menu',$menu);
        return View::fetch();
    }

    /**
     * Notes:菜单管理列表数据
     * User: Administrator
     * Date: 2020/9/14
     * Time: 11:03
     * @return \think\response\Json
     * @author: 雷佳
     */
    public function menu_list()
    {
        $get = input('get.');
        $authRule = new AuthRuleModel();
        $data= $authRule->pagingMenu($get['offset'],$get['limit']);
        return json(['rows'=>$data['data'], 'total'=>$data['total']]);
    }

    public function add_menus()
    {
        $authrule = new AuthRuleModel();
        $res = $authrule->addRule();
        if($res === 10501){
            return replace(config('status.error'), '菜单名重复', [],200);
        }else{
            return replace(config('status.succeed'), '提交成功', [], 200);
        }
    }
}