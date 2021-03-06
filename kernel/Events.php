<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php kernel.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use \Workerman\MySQL;
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    //静态成员存储客服与客户列表
    public static $globalSc = null;
    public static $db = null;

    public static function onWorkerStart($worker)
    {
        if (empty(self::$db)) {
            self::$db = new MySQL\Connection('127.0.0.1', '3306', 'root', 'root', 'chat');
        }
        if (empty(self::$globalSc)) {
            self::$globalSc = new \GlobalData\Client('127.0.0.1:2207');
            // 客服列表
            if(is_null(self::$globalSc->serviceList)){
                self::$globalSc->serviceList = [];
            }

            // 会员列表[动态的，这里面只是目前未被分配的会员信息]
            if(is_null(self::$globalSc->userList)){
                self::$globalSc->userList = [];
            }
        }
    }
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
//        // 向当前client_id发送数据
//        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
//        // 向所有人发送
//        Gateway::sendToAll("$client_id login\r\n");
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {
       $message = json_decode($message, true);
       switch ($message['message_type']) {
           //客服初始
           case 'init':
               $serviceList = self::$globalSc->serviceList;
               //新客服
               if(!isset($serviceList[$message['group']]) || !array_key_exists($message['service_id'], $serviceList[$message['group']])){
                   #self::$globalQueue->serviceList = $message;
                   do{
                       $newServiceList = $serviceList;
                       $newServiceList[$message['group']][$message['service_id']] = [
                           'id' => $message['service_id'],
                           'name' => $message['name'],
                           'avatar' => $message['avatar'],
                           'client_id' => $client_id,
                           'task' => 0,
                           'user_info' => []
                       ];
                   }while(!self::$globalSc->cas('serviceList', $serviceList, $newServiceList));
                   unset($newServiceList, $serviceList);
               }else if(isset($serviceList[$message['group']][$message['service_id']])){
                   //已在内存中客服
                   do{
                       $newServiceList = $serviceList;
                       $newServiceList[$message['group']][$message['service_id']]['client_id'] = $client_id;
                   }while(!self::$globalSc->cas('serviceList', $serviceList, $newServiceList));
                   unset($newServiceList, $serviceList);
               }
               //客服id绑定客户端id（每个ws连接都有一个唯一的客户端id,不管是用户还是客服连接）
               Gateway::bindUid($client_id,$message['service_id']);
               break;
           case 'user_init';
               $userList = self::$globalSc->userList;
               // 如果该顾客未在内存中记录则记录
               $service_id = empty($message['service_id'])?'0':$message['service_id'];
               if(!array_key_exists($message['user_id'], $userList)){
                   do{
                       $NewUserList = $userList;
                       $NewUserList[$message['user_id']] = [
                           'user_id' => $message['user_id'],
                           'user_name' => $message['name'],
                           'user_avatar' => $message['avatar'],
                           'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                           'group' => $message['group'],
                           'client_id' => $client_id,
                           'addtime' => date('Y-m-d H:i:s'),
                           'service_id' => $service_id,
                       ];

                   }while(!self::$globalSc->cas('userList', $userList, $NewUserList));
                   $visitor_id = $message["user_id"];
                   $has = self::$db->select('id')->from('c_user')->where("user_id = '$visitor_id'")->row();

                   if(!empty($has)) {
                       self::$db->update('c_user')->cols($NewUserList[$message['user_id']])->where("id=".$has['id'])->query();
                   }else {
                       self::$db->insert('c_su_main')->cols(['user_id' => $message['user_id'],'service_id' => $service_id])->query();
                       self::$db->insert('c_user')->cols($NewUserList[$message['user_id']])->query();
                   }
                   unset($NewUserList, $userList);
               }

               // 绑定 client_id 和 user_id（uid）
               Gateway::bindUid($client_id,$message['user_id']);

                // 尝试分配新客户进入服务
               self::informOnlineTask(self::$globalSc->userList[$message['user_id']]);
               break;
           case 'chatMessage':
               $client = Gateway::getClientIdByUid($message['data']['to']['id']);
               if(!empty($client)) {
                   $chat_message = [
                       'message_type' => 'chatMessage',
                       'data' => [
                           'username' => $message['data']['mine']['username'],
                           'avatar' => $message['data']['mine']['avatar'],
                           'id' => $message['data']['mine']['id'],
                           'time' => date('H:i'),
                           'message_type' => empty($message['data']['mine']['type'])?'':$message['data']['mine']['type'],
                           'content' => htmlspecialchars($message['data']['mine']['content']),
                       ]
                   ];
                   $data = [
                       'send_id' => $message['data']['mine']['id'],
                       'receive_id' => $message['data']['to']['id'],
                       'content' => htmlspecialchars($message['data']['mine']['content']),
                       'create_time' => time(),
                       'send_name' => $message['data']['mine']['username'],
                       's_id' => empty($message['data']['mine']['type'])?$message['data']['to']['id']:$message['data']['mine']['id'],
                       'u_id' => empty($message['data']['mine']['type'])?$message['data']['mine']['id']:$message['data']['to']['id'],
                       'avatar' => $message['data']['mine']['avatar']
                   ];

                    // 如果不在线就先存起来
                    if(!Gateway::isUidOnline($data['s_id']))
                    {
                        $data['look'] = 0;
                    }
                    else
                    {
                        $data['look'] = 1;
                        // 在线就转发消息给对应的uid
                        Gateway::sendToClient($client['0'], json_encode($chat_message));
                    }
                   self::$db->insert('c_message')->cols($data)->query();
                   unset($chat_message, $data);
               }
               break;
       }
        // 向所有人发送 
        //Gateway::sendToAll("$client_id said $message\r\n");
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       // 向所有人发送 
       /*GateWay::sendToAll("$client_id logout\r\n");*/
   }

   public static function informOnlineTask($user)
   {
       $res = self::AssigningJob(self::$globalSc->serviceList, self::$globalSc->userList, $user['group'], $user['service_id']);
       if (1 == $res['code']) {
           while(!self::$globalSc->cas('serviceList', self::$globalSc->serviceList, $res['data']['4'])){}; // 更新客服数据
           while(!self::$globalSc->cas('userList', self::$globalSc->userList, $res['data']['5'])){}; // 更新会员数据
           $noticeUser = [
               'message_type' => 'connect',
               'data' => [
                   'service_id' => $res['data'][0],
                   'service_name' => $res['data'][1],
               ]
           ];
           // 通知会员发送信息绑定客服的id
           Gateway::sendToClient($user['client_id'], json_encode($noticeUser));
           unset($noticeUser);
           // 通知客服端绑定会员的信息
           $noticeKf = [
               'message_type' => 'connect',
               'data' => [
                   'user_info' => [
                       'id' => $user['user_id'],
                       'name' => $user['user_name'],
                       'avatar' => $user['user_avatar'],
                       'ip' => $_SERVER['REMOTE_ADDR'],
                       'time' => time(),
                   ]
               ]
           ];
           Gateway::sendToClient($res['data'][2], json_encode($noticeKf));

           unset($noticeKf);
       } else if(2 == $res['code']){

           $service_id = $user['service_id'];
           $service = self::$db->select('id, user_name')->from('c_service')->where("serial_number= '$service_id' ")->row();

           $noticeUser = [
               'message_type' => 'connect',
               'data' => [
                   'service_id' => $service['id'],
                   'service_name' => $service['user_name'],
               ]
           ];
           // 通知会员发送信息绑定客服的id
           Gateway::sendToUid($user['user_id'], json_encode($noticeUser));
           unset($noticeUser);
           if(Gateway::isUidOnline($service_id)){
               // 通知客服端绑定会员的信息
               $noticeKf = [
                   'message_type' => 'connect',
                   'data' => [
                       'user_info' => [
                           'id' => $user['user_id'],
                           'name' => $user['user_name'],
                           'avatar' => $user['user_avatar'],
                           'ip' => $_SERVER['REMOTE_ADDR'],
                           'time' => time(),
                       ]
                   ]
               ];
               Gateway::sendToUid($service_id, json_encode($noticeKf));
           }
           unset($noticeKf);
       }else {
           $Message = '';
           switch ($res['code']) {

               case -1:
                   $Message = '暂时没有客服上班,请稍后再咨询。';
                   break;
               case -2:
                   break;
               case -3:
                   break;
               case -4:
                   $number = count(self::$global->userList);
                   $Message = '您前面还有 ' . $number . ' 位会员在等待。';
                   break;
           }
           $waitMessage = [
               'message_type' => 'wait',
               'data' => [
                   'content' => $Message,
               ]
           ];

           Gateway::sendToUid($user['user_id'], json_encode($waitMessage));
           unset($waitMessage);
       }
   }

   private static function AssigningJob($serviceList, $userList, $group, $service_id)
   {
       $total='5';
        if(!empty($service_id)){
            return ['code' => 2];
        }
       // 没有客服上线
       if(empty($serviceList) || empty($serviceList[$group])){
           return ['code' => -1];
       }

       // 没有待分配的会员
       if(empty($userList)){
           return ['code' => -2];
       }

       // 未设置每个客服可以服务多少人
       if(0 == $total){
           return ['code' => -3];
       }

       // 查看该组的客服是否在线
       if(!isset($serviceList[$group])){
           return ['code' => -1];
       }

       $kf = $serviceList[$group];
       $user = array_shift($userList);
       $kf = array_shift($kf);
       $min = $kf['task'];
       $flag = $kf['id'];

       foreach($serviceList[$group] as $key=>$vo){
           if($vo['task'] < $min){
               $min = $vo['task'];
               $flag = $key;
           }
       }
       unset($kf);

       // 需要排队了
       if($serviceList[$group][$flag]['task'] == $total){
           return ['code' => -4];
       }

       $serviceList[$group][$flag]['task'] += 1;
       array_push($serviceList[$group][$flag]['user_info'], $user['client_id']); // 被分配的用户信息

       return [
           'code' => 1,
           'data' => [
               $serviceList[$group][$flag]['id'],
               $serviceList[$group][$flag]['name'],
               $serviceList[$group][$flag]['client_id'],
               $user,
               $serviceList,
               $userList
           ]
       ];

   }
}
