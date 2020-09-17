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

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    public static $global_queue;
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
               $serviceList = self::$global_queue->serviceList;
               //新客服
               if(!isset($serviceList[$message['group']]) || !array_key_exists($message['service_id'], $serviceList[$message['group']])){
                   self::$global_queue->serviceList = $message;
                    Gateway::bindUid($client_id,$message['service_id']);
               }else{
                   //已在内存中客服
               }
               break;
           case 'user_init';
               Gateway::bindUid($client_id,$message['user_id']);
               self::informOnlineTask($client_id,$message);
               break;
           case 'server_chat':

               break;
           case 'user_chat':
               Gateway::sendToUid();
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
       GateWay::sendToAll("$client_id logout\r\n");
   }

   public static function informOnlineTask($client_id,$message)
   {
       // 通知会员发送信息绑定客服的id
       $serviceList = self::$global_queue->serviceList;

       $noticeUser = [
           'message_type' => 'addUser',
           'data' => [
               'id' => $serviceList['service_id'],
               'username' => $serviceList['name'],
               'type' =>'friend',
               'avatar'=>$serviceList['avatar'],
               'groupid'  => 1,
               'sign'     => '555'
           ]
       ];
       Gateway::sendToClient($client_id, json_encode($noticeUser));
       unset($noticeUser);

       // 通知客服端绑定会员的信息
       $noticeKf = [
           'message_type' => 'connect',
           'data' => [
               'user_info' => [
                   'id'=>$message['user_id'],
                   'name'=>$message['name'],
                   'avatar'=>$message['avatar'],
                   'ip'=>$_SERVER['REMOTE_ADDR'],
               ]
           ]
       ];
       Gateway::sendToUid($serviceList['service_id'], json_encode($noticeKf));
       unset($noticeKf);
   }
}
