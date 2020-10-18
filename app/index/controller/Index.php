<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/11
 * Time: 9:24
 */
namespace app\index\controller;
use think\facade\View;
use think\facade\Db;
use think\Facade\Cookie;
use think\Facade\Session;
class Index
{
    public function index()
    {
        if(!Cookie::has('PHPSESSID')){
            Cookie('PHPSESSID',Session::getid(),86400*5);
        }
        $visiter = substr(Cookie::get('PHPSESSID'), 0,8);
        VIew::assign('visiter',$visiter);
        return View::fetch();
    }

    //获取列表
    public function getList()
    {
        //查询自己的信息
        $mine = Db::name('chatuser')->where('id', '13')->find();
        $other = Db::name('chatuser')->select();

        //查询当前用户的所处的群组
        $groupArr = [];
        $groups = Db::name('groupdetail')->field('groupid')->where('userid', cookie('uid'))->group('groupid')->select();
        if( !empty( $groups ) ){
            foreach( $groups as $key=>$vo ){
                $ret = Db::name('chatgroup')->where('id', $vo['groupid'])->find();
                if( !empty( $ret ) ){
                    $groupArr[] = $ret;
                }
            }
        }
        unset( $ret, $groups );

        $online = 0;
        $group = [];  //记录分组信息
        $userGroup = [//默认显示的用户分组
                '1' => '前端组',
                '2' => '后端组',
                '3' => '运维组'
            ];
        $list = [];  //群组成员信息
        $i = 0;
        $j = 0;

        foreach( $userGroup as $key=>$vo ){
            $group[$i] = [
                'groupname' => $vo,
                'id' => $key,
                'online' => 0,
                'list' => []
            ];
            $i++;
        }
        unset( $userGroup );

        foreach( $group as $key=>$vo ){

            foreach( $other as $k=>$v ) {

                if ($vo['id'] == $v['groupid']) {

                    $list[$j]['username'] = $v['username'];
                    $list[$j]['id'] = $v['id'];
                    $list[$j]['avatar'] = $v['avatar'];
                    $list[$j]['sign'] = $v['sign'];

                    if ('online' == $v['status']) {
                        $online++;
                    }

                    $group[$key]['online'] = $online;
                    $group[$key]['list'] = $list;

                    $j++;
                }
            }
            $j = 0;
            $online = 0;
            unset($list);
        }
        //print_r($group);die;
        unset( $other );

        $return = [
            'code' => 0,
            'msg'=> '',
            'data' => [
                'mine' => [
                    'username' => $mine['username'],
                    'id' => $mine['id'],
                    'status' => 'online',
                    'sign' => $mine['sign'],
                    'avatar' => $mine['avatar']
                ],
                'friend' => $group,
                'group' => $groupArr
            ],
        ];

        return json( $return );

    }

    //获取组员信息
    public function getMembers()
    {
        $id = 2;

        //群主信息
        $owner = db('chatgroup')->field('owner_name,owner_id,owner_avatar,owner_sign')->where('id = ' . $id)->find();
        //群成员信息
        $list = db('groupdetail')->field('userid id,username,useravatar avatar,usersign sign')
            ->where('groupid = ' . $id)->select();

        $return = [
            'code' => 0,
            'msg' => '',
            'data' => [
                'owner' => [
                    'username' => $owner['owner_name'],
                    'id' => $owner['owner_id'],
                    'owner_id' => $owner['owner_avatar'],
                    'sign' => $owner['owner_sign']
                ],
                'list' => $list
            ]
        ];

        return json( $return );
    }
}