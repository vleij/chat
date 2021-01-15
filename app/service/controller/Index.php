<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/15
 * Time: 17:05
 */
namespace app\service\controller;
use think\facade\View;
use app\common\business\home as HomeBusiness;
use app\common\model\mysql\Service as ServiceModel;
use app\common\model\mysql\Message as MessageModel;
class Index extends Base
{

    public function index(){
        $menu = new HomeBusiness();
        $serviceObj = new ServiceModel();
        $id = session(config('service.session_service_id'));

        $serviceUser = $serviceObj->getServiceId($id);
        $user_list = $serviceObj->rolesUserMessage($id);

        $menu_list = $menu->getAllMenusData();
        $messageObj = new MessageModel();
        $message = [];

        if(!empty($user_list[0])){
            $message = $messageObj->getUserSMessage($user_list[0]['id'], $id);
        }

        View::assign([
            'menu'  => $menu_list,
            'serviceUser' => $serviceUser,
            'user_list' => $user_list,
            'message' => $message
        ]);
        return View::fetch();
    }

    public function getUserData()
    {
        $uid = $this->request->param('uid', '', 'trim');
        $sid = $this->request->param('sid', '', 'trim');
        $messageObj = new MessageModel();
        $data = $messageObj->getUserSMessage($uid, $sid);
        return replace(config('status.succeed'),'ok',$data,200);
    }


}