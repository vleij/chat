<?php /*a:1:{s:56:"D:\phpstudy_pro\WWW\chat\app\index\view\index\index.html";i:1600419939;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>LayIM测试</title>
    <link rel="stylesheet" href="/static/index/layui/css/layui.css" media="all">

    <script src="/static/admin/js/jquery.min.js"></script>
    <script src="/static/index/layui/layui.js"></script>
</head>
<body >

<script type="text/javascript">
    //localStorage.clear();
    layui.use('layim', function(layim){
        //基础配置
        // layim.config({
        //
        //     //获取主面板列表信息
        //     init: {
        //         url: "<?php echo url('./getList'); ?>"//接口地址（返回的数据格式见下文）
        //         ,type: 'get' //默认get，一般可不填
        //         ,data: {} //额外参数
        //     }
        //     //获取群员接口
        //     ,members: {
        //         url: "<?php echo url('./getMembers'); ?>" //接口地址（返回的数据格式见下文）
        //         ,type: 'get' //默认get，一般可不填
        //         ,data: {} //额外参数
        //     },
        //     uploadFile: {
        //         url: "<?php echo url('upload/uploadFile'); ?>"
        //     }
        //     ,uploadImage: {
        //         url: "<?php echo url('upload/uploadimg'); ?>"
        //     }
        //     ,brief: true //是否简约模式（默认false，如果只用到在线客服，且不想显示主面板，可以设置 true）
        //     ,title: '我的LayIM' //主面板最小化后显示的名称
        //     ,maxLength: 3000 //最长发送的字符长度，默认3000
        //     ,isfriend: true //是否开启好友（默认true，即开启）
        //     ,isgroup: true //是否开启群组（默认true，即开启）
        //     ,right: '0px' //默认0px，用于设定主面板右偏移量。该参数可避免遮盖你页面右下角已经的bar。
        //     ,chatLog: "<?php echo url('Chatlog/index'); ?>" //聊天记录地址（如果未填则不显示）
        //     ,find: "<?php echo url('findgroup/index'); ?>" //查找好友/群的地址（如果未填则不显示）
        //     ,copyright: false //是否授权，如果通过官网捐赠获得LayIM，此处可填true
        // });


        //建立WebSocket通讯
        var socket = new WebSocket('ws://127.0.0.1:8282');

        //连接成功时触发
        socket.onopen = function(){
            // 登录

            var login_data = '{"type":"user_init", "user_id":"1509641629", "name" : "秀秀", "avatar" : "/static/index/images/1.jpg", "group": "1"}';
            socket.send( login_data );
            console.log("websocket握手成功!");
        };
        var service
        //监听收到的消息
        socket.onmessage = function(res){
            var data = eval("("+res.data+")");
            console.log(data.data);
            switch(data['message_type']){
                // 服务端ping客户端
                case 'ping':
                    socket.send('{"type":"ping"}');
                    break;
                // 登录 更新用户列表
                case 'init':
                    //console.log(data['id']+"登录成功");
                    //layim.getMessage(res.data); //res.data即你发送消息传递的数据（阅读：监听发送的消息）
                    break;
                case 'connect':
                    service = data.data;
                    console.log(service)
                    //自定义客服窗口
                    layim.config({
                        brief: true //简约模式，不显示主面板
                    }).chat({
                        name: '在线客服二' //名称
                        ,type: 'service' //聊天类型
                        ,avatar: 'http://tp1.sinaimg.cn/5619439268/180/40030060651/1' //头像
                        ,id: service['service_id'] //定义唯一的id方便你处理信息
                    });
                    layim.setChatMin(); //收缩聊天面板
                    break;
                // 检测聊天数据
                case 'chatMessage':
                    //console.log(data.data);
                    layim.getMessage(data.data);
                    break;
                // 离线消息推送
                case 'logMessage':
                    setTimeout(function(){layim.getMessage(data.data)}, 1000);
                    break;
                // 用户退出 更新用户列表
                case 'logout':
                    break;
                //聊天还有不在线
                case 'ctUserOutline':
                    console.log('11111');
                    //layer.msg('好友不在线', {'time' : 1000});
                    break;

            }
        };

        layim.on('sendMessage', function(res){

            // 发送消息
            var mine = JSON.stringify(res.mine);
            var to = JSON.stringify(res.to);
            var login_data = '{"type":"chatMessage","data":{"mine":'+mine+', "to":'+to+'}}';
            socket.send( login_data );

        });

    });
</script>
</body>
</html>