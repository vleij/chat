var uinfo = {
    id:  'KF' + service_id,
    username: uname,
    avatar: avatar,
    group: group
};

var socket_server = '127.0.0.1:8282'


// 创建一个Socket实例
var socket = new WebSocket('ws://' + socket_server);

// 打开Socket
socket.onopen = function (res) {
    toastr.success('连接成功')
    // 登录
    var login_data = '{"type":"init", "service_id":"' + uinfo.id + '", "name" : "' + uinfo.username + '", "avatar" : "'
        + uinfo.avatar + '", "group": ' + uinfo.group + '}';
    socket.send(login_data);
};

// 监听消息
socket.onmessage = function (res) {
    var data = eval("(" + res.data + ")");
    console.log(data)
    switch (data['message_type']) {
        // 服务端ping客户端
        case 'ping':
            socket.send('{"type":"ping"}');
            break;
        // 添加用户
        case 'connect':
            addUser(data.data.user_info);
            break;
        // 移除访客到主面板
        case 'delUser':
            delUser(data.data);
            break;
        // 监测聊天数据
        case 'chatMessage':
            showUserMessage(data.data, data.data.content);
            break;
    }
};

// 监听失败
socket.onerror = function (err) {
    toastr.error('连接失败,请联系管理员')
};
$(function () {
    // 发送消息
    $(".send").click(function () {
        sendMessage();
    });
});

// 发送消息
function sendMessage(sendMsg) {
    var msg = (typeof(sendMsg) == 'undefined') ? $(".msg-area").val() : sendMsg;
    if ('' == msg) {
        toastr.warning('请输入回复内容!', 'Warning');
        return false;
    }

    var word = msgFactory(msg, 'mine', uinfo);
    var uid = $(".chat-user-list .active").data('id');
    var uname = $(".chat-user-list .active").data('name');

    socket.send(JSON.stringify({
        type: 'chatMessage',
        data: {'to':{id: uid, name: uname}, 'mine':{username: uinfo.username,
                id: uinfo.id, avatar: uinfo.avatar, content: msg, 'type': 'service'}}

    }));
    $("#chatMessage-"+uid).append(word);
    $(".msg-area").val('');
    // 滚动条自动定位到最底端
    wordBottom();
}

// 展示客户发送来的消息
function showUserMessage(uinfo, content) {
/*    if ($('#f-' + uinfo.id).length == 0) {
        addUser(uinfo);
    }*/

    //未读条数计数
    if (!$('#u-' + uinfo.id).hasClass('active')) {
        var num = $('#u-' + uinfo.id).find('#unread').text();
        if (num == '') num = 0;

        if(num < '09'){
            num = '0'+(parseInt(num) + 1);
        }else{
            num = parseInt(num) + 1;
        }
        $('#u-' + uinfo.id).find('#unread').text(num);
    }

    var word = msgFactory(content, 'user', uinfo);
    notifyMe(uinfo.username || '新访客', {
        body: replaceContent(content),
        icon: uinfo.avatar
    }, function(notification) {
        //可直接打开通知notification相关联的tab窗口
        window.focus();
        notification.close();
        //$('#v'+data.message.channel+' .visit_content').trigger('click');
    });
    setTimeout(function () {
        $("#chatMessage-" + uinfo.id).append(word);
        // 滚动条自动定位到最底端
        wordBottom();

    }, 200);
}

// 消息发送工厂
function msgFactory(content, type, uinfo) {
    var _html = '';
    if ('mine' == type) {
        _html += '<li class="right">';
    } else {
        _html += '<li>';
    }
    _html += '<div class="conversation-list">';
    _html += '<div class="chat-avatar"><img src="' + uinfo.avatar + '" alt=""></div>';
    _html += '<div class="user-chat-content"> <div class="ctext-wrap"> <div class="ctext-wrap-content">';
    _html += '<p class="mb-0">' + replaceContent(content) + '</p>';
    _html += '<p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle">10:02</span></p>';
    _html += '</div>'
    _html += `<div class="dropdown align-self-start">
            <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ri-more-2-fill"></i>
            </a>
            <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Copy <i class="ri-file-copy-line float-right text-muted"></i></a>
        <a class="dropdown-item" href="#">Save <i class="ri-save-line float-right text-muted"></i></a>
        <a class="dropdown-item" href="#">Forward <i class="ri-chat-forward-line float-right text-muted"></i></a>
        <a class="dropdown-item" href="#">Delete <i class="ri-delete-bin-line float-right text-muted"></i></a>
        </div>
        </div>
        </div>`;
    _html += `<div class="conversation-name">`+uinfo.username+`</div>
</div>
</div>`;
    _html += '</li>';

    return _html;
}


// 添加用户到面板
function addUser(data) {
    var repetition = '';
    $(".user_active").each(function(){
        var id = this.getAttribute("data-id")
        if(data.id == id){
            repetition = '1';
            return
        }
    });
    if(repetition){
        return
    }
    var _html = `<li class="user_active" id="u-${data.id }" data-id="${data.id }" data-avatar="${data.avatar }" data-name="${data.name }" data-temporary="1">
                                    <a href="#">
                                        <div class="media">
                                                                                        <div class="chat-user-img online align-self-center mr-3">
                                                <img src="${data.avatar }" class="rounded-circle avatar-xs" alt="">
                                                <span class="user-status"></span>
                                            </div>
                                            
                                            <div class="media-body overflow-hidden">
                                                <h5 class="text-truncate font-size-15 mb-1">${data.name }</h5>
                                                <p class="chat-user-message text-truncate mb-0"></p>
                                            </div>
                                            <div class="font-size-11">${data.time } min</div>
                                            <div class="unread-message">
                                                        <span class="badge badge-soft-danger badge-pill" id="unread"></span>
                                                    </div>
                                        </div>
                                    </a>
                                </li>`;

    // 添加左侧列表
    $(".chat-user-list").append(_html);

    // 如果没有选中人，选中第一个
    var hasActive = 0;
    $(".chat-user-list li").each(function(){
        if($(this).hasClass('active')){
            hasActive = 1;
        }
    });


    var _html2 = '';
    _html2 += `<div class='chat-conversation p-3 p-lg-4 chat-box tab-pane fade' data-simplebar="init" role="tabpanel"  id="uchat-${data.id }">
                    <ul class="list-unstyled mb-0 " id="chatMessage-${data.id }"></ul></div>`;
    // 添加主聊天面板
    $('#chat-main').append(_html2);

    checkUser()
}
// 操作新连接用户的 dom操作
function checkUser(one='') {

    $(".chat-user-list").find('li').unbind("click"); // 防止事件叠加

    // 切换用户
    $(".chat-user-list").find('li').bind('click', function () {
        changeUserTab($(this));
        var uid = $(this).data('id');
        var avatar = $(this).data('avatar');
        var name = $(this).data('name');
        var temporary = $(this).data("temporary");
        $('#u-' + uid).find('#unread').text("");
        // 展示相应的对话信息
        $('.chat-box').each(function () {
            if ('uchat-' + uid == $(this).attr('id')) {
                $(this).addClass('in active show').siblings().removeClass('in active show').attr('style', '');
                return false;
            }
        });


        // 设置当前会话的用户
        //$(".user_active").attr('data-id', uid).attr('data-name', name).attr('data-avatar', avatar);
        getChatLog(uid, avatar, name,temporary);
        wordBottom();
    });
    if(one == 1){
        var obj = $(".chat-user-list").find('li:eq(0)')
        var uid = obj.data('id');
        var avatar = obj.data('avatar');
        var name = obj.data('name');
        getChatLog(uid, avatar, name);
    }
    console.log(777)
}

// 滚动条自动定位到最底端
function wordBottom() {
    // var box = $("#chatMessage");
    // console.log(box[0].scrollHeight)
    // box.scrollTop(box[0].scrollHeight);
}

// 转义聊天内容中的特殊字符
function replaceContent(content) {
    // 支持的html标签
    var html = function (end) {
        return new RegExp('\\n*\\[' + (end || '') + '(pre|div|span|p|table|thead|th|tbody|tr|td|ul|li|ol|li|dl|dt|dd|h2|h3|h4|h5)([\\s\\S]*?)\\]\\n*', 'g');
    };
    content = (content || '').replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;') // XSS
        .replace(/@(\S+)(\s+?|$)/g, '@<a href="javascript:;">$1</a>$2') // 转义@

        .replace(/face\[([^\s\[\]]+?)\]/g, function (face) {  // 转义表情
            var alt = face.replace(/^face/g, '');
            return '<img alt="' + alt + '" title="' + alt + '" src="' + faces[alt] + '">';
        })
        .replace(/img\[([^\s]+?)\]/g, function (img) {  // 转义图片
            return '<img class="layui-whisper-photos" src="' + img.replace(/(^img\[)|(\]$)/g, '') + '" width="100px" height="100px">';
        })
        .replace(/file\([\s\S]+?\)\[[\s\S]*?\]/g, function (str) { // 转义文件
            var href = (str.match(/file\(([\s\S]+?)\)\[/) || [])[1];
            var text = (str.match(/\)\[([\s\S]*?)\]/) || [])[1];
            if (!href) return str;
            return '<a class="layui-whisper-file" href="' + href + '" download target="_blank"><i class="layui-icon">&#xe61e;</i><cite>' + (text || href) + '</cite></a>';
        })
        .replace(/a\([\s\S]+?\)\[[\s\S]*?\]/g, function (str) { // 转义链接
            var href = (str.match(/a\(([\s\S]+?)\)\[/) || [])[1];
            var text = (str.match(/\)\[([\s\S]*?)\]/) || [])[1];
            if (!href) return str;
            return '<a href="' + href + '" target="_blank">' + (text || href) + '</a>';
        }).replace(html(), '\<$1 $2\>').replace(html('/'), '\</$1\>') // 转移HTML代码
        .replace(/\n/g, '<br>') // 转义换行

    return content;
};

function getChatLog(uid, avatar, name, temporary) {
    var uid = uid;
    var avatar = avatar;
    var name = name;
    var sid = service_id;
    $("#user_avatar").empty()
    if(typeof avatar == "undefined" || avatar == null || avatar == ""){
        var avatar_html = `<div class="avatar-xs">
                              <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                               刘
                                 </span>`
        $("#user_avatar").append(avatar_html);
    }else{
        $("#user_avatar").append('<img src="'+avatar+'" class="rounded-circle avatar-xs" alt="">');
    }
    $("#user_name").text(name);
    if(temporary == '1'){
        return
    }
    $("#chatMessage-"+uid+" li").remove();
    $.ajax({
        url: './getUserData',
        type:'GET',
        data: {uid:uid,sid:sid},
        dataType: 'JSON',
        success: function (data) {
            if(data.status == '1'){
                var message = data.result
                var _html = ''
                $.each(message, function (index, value) {
                    if (value.send_id == 'kf'+value.s_id) {
                        _html += '<li class="right">';
                    } else {
                        _html += '<li>';
                    }
                    _html += '<div class="conversation-list">';
                    _html += '<div class="chat-avatar"><img src="' + value.avatar + '" alt=""></div>';
                    _html += '<div class="user-chat-content"> <div class="ctext-wrap"> <div class="ctext-wrap-content">';
                    _html += '<p class="mb-0">' + value.content+ '</p>';
                    _html += '<p class="chat-time mb-0"><i class="ri-time-line align-middle"></i> <span class="align-middle">'+value.create_time+'</span></p>';
                    _html += '</div>'
                    _html += `<div class="dropdown align-self-start">
            <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ri-more-2-fill"></i>
            </a>
            <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Copy <i class="ri-file-copy-line float-right text-muted"></i></a>
        <a class="dropdown-item" href="#">Save <i class="ri-save-line float-right text-muted"></i></a>
        <a class="dropdown-item" href="#">Forward <i class="ri-chat-forward-line float-right text-muted"></i></a>
        <a class="dropdown-item" href="#">Delete <i class="ri-delete-bin-line float-right text-muted"></i></a>
        </div>
        </div>
        </div>`;
                    _html += `<div class="conversation-name">`+value.send_name+`</div>
</div>
</div>`;
                    _html += '</li>\n';
                });
                $("#chatMessage-"+uid).append(_html);
            }
        }
    });
}

// 切换在线用户
function changeUserTab(obj) {
    obj.addClass('active').siblings().removeClass('active');
    wordBottom();
}
function notifyMe(title, options, callback) {
    // 检查浏览器是否支持 Notification
    if (!("Notification" in window)) {
        alert("你的不支持 Notification!  TAT");
    }

    // 检查用户是否已经允许使用通知
    else if (Notification.permission === "granted") {
        // 创建 Notification
        var notification = new Notification(title, options);
        notification.iconurl = 'http://img.hacpai.com/avatar/1450241301546-260.jpg?1451971807339';
        autoClose(notification);

    }

    // 重新发起请求，让用户同意使用通知
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {

            // 用户同意使用通知
            if (!('permission' in Notification)) {
                Notification.permission = permission;
            }

            if (permission === "granted") {
                // 创建 Notification
                var notification = new Notification("Hey guy!");
            }
        });
    }
    if (notification && callback) {
        notification.onclick = function(event) {
            callback(notification, event);
        }
    }
    // 注意：如果浏览器禁止弹出任何通知，将无法使用
}

function autoClose(notification) {
    if (typeof notification.time === 'undefined' || notification.time <= 0) {
        notification.close();
    } else {
        setTimeout(function () {
            notification.close();
        }, notification.time);
    }

    notification.addEventListener('click', function () {
        notification.close();
    }, false)
}

