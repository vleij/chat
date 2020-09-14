<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 15:49
 */
namespace app\common\model\mysql;
use think\facade\Session;
use think\Model;

class Menu extends Model
{
    protected $name = 'admin_menu';

    /**
     * Notes:
     * Date: 2020/9/14
     * Time: 12:44
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: 雷佳
     */
    public function getShowMenuData()
    {
        $data = $this->where(['is_display'=>1])->order('orders desc')->select();
        return $data;
    }


    /**
     * Notes:
     * User: ${FILE_NAME}
     * User: Administrator
     * Date: 2020/9/14
     * Time: 10:46
     * @author: 雷佳
     */
    public function pagingMenu($offset, $limit)
    {
        $list = $this->page($offset,$limit)->select();
        $total = $this->count();
        return ['data'=>$list,'total'=>$total];
    }
}