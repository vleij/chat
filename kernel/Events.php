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
       switch ($message['type']) {
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
               if(!array_key_exists($message['user_id'], $userList)){
                   do{
                       $NewUserList = $userList;
                       $NewUserList[$message['user_id']] = [
                           'user_id' => $message['user_id'],
                           'name' => $message['name'],
                           'avatar' => $message['avatar'],
                           'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                           'group' => $message['group'],
                           'client_id' => $client_id
                       ];

                   }while(!self::$globalSc->cas('userList', $userList, $NewUserList));
                   unset($NewUserList, $userList);
               }
               // 绑定 client_id 和 user_id（uid）
               Gateway::bindUid($client_id,$message['user_id']);
                // 尝试分配新客户进入服务
               self::informOnlineTask($client_id,$message['group']);
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
                           'type' => empty($message['data']['mine']['type'])?'':$message['data']['mine']['type'],
                           'content' => htmlspecialchars($message['data']['mine']['content']),
                       ]
                   ];
                   Gateway::sendToClient($client['0'], json_encode($chat_message));

                   unset($chat_message);
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

   public static function informOnlineTask($client_id,$group)
   {
       //客服列表
       $serviceList = self::$globalSc->serviceList;
       //用户列表
       $userList = self::$globalSc->userList;

       //将一个元素移出(数组开头)
       $service = array_shift($serviceList[$group]);

       $user = array_shift($userList);

       $noticeUser = [
           'message_type' => 'connect',
           'data' => [
               'service_id' => $service['id'],
               'service_name' => $service['name'],
           ]
       ];
       // 通知会员发送信息绑定客服的id
       Gateway::sendToClient($client_id, json_encode($noticeUser));
       unset($noticeUser);

           // 通知客服端绑定会员的信息
           $noticeKf = [
               'message_type' => 'connect',
               'data' => [
                   'user_info' => [
                       'id' => $user['user_id'],
                       'name' => $user['name'],
                       'avatar' => $user['avatar'],
                       'ip' => $_SERVER['REMOTE_ADDR'],
                       'time' => time(),
                   ]
               ]
           ];
           Gateway::sendToClient($service['client_id'], json_encode($noticeKf));
           unset($noticeKf);

   }
}
