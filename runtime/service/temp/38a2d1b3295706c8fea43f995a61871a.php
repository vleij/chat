<?php /*a:1:{s:58:"D:\phpstudy_pro\WWW\chat\app\service\view\login\login.html";i:1601628504;}*/ ?>
<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Chat" name="description" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="">

    <!-- Bootstrap Css -->
    <link href="/static/assets/css/bootstrap-dark.min.css" id="bootstrap-dark-style" rel="stylesheet" type="text/css" />
    <link href="/static/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/static/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/static/assets/css/app-dark.min.css" id="app-dark-style" rel="stylesheet" type="text/css" />
    <link href="/static/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <link href="/static/common/css/plugins/toastr/toastr.min.css" id="toastr-style" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="account-pages my-5 pt-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="text-center mb-4">
                    <a href="index.html" class="auth-logo mb-5 d-block">
                        <img src="__ASSETSIMG_/logo-dark.png" alt="" height="30" class="logo logo-dark">
                        <img src="__ASSETSIMG_/logo-light.png" alt="" height="30" class="logo logo-light">
                    </a>

                    <h4>登录</h4>
                    <p class="text-muted mb-4">登录可继续使用 Chat.</p>

                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <div class="p-3">
                            <form action="" id="form">

                                <div class="form-group">
                                    <label>用户名</label>
                                    <div class="input-group mb-3 bg-soft-light input-group-lg rounded-lg">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text border-light text-muted">
                                                        <i class="ri-user-2-line"></i>
                                                    </span>
                                        </div>
                                        <input type="text" name="username" class="form-control bg-soft-light border-light" placeholder="输入用户名">

                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="float-right">
                                        <a href="auth-recoverpw.html" class="text-muted font-size-13">忘记密码?</a>
                                    </div>
                                    <label>密码</label>
                                    <div class="input-group mb-3 bg-soft-light input-group-lg rounded-lg">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text border-light text-muted">
                                                        <i class="ri-lock-2-line"></i>
                                                    </span>
                                        </div>
                                        <input type="password" name="password" class="form-control bg-soft-light border-light" placeholder="输入密码">

                                    </div>
                                </div>

                                <div class="custom-control custom-checkbox mb-4">
                                    <input type="checkbox" class="custom-control-input" id="remember-check">
                                    <label class="custom-control-label" for="remember-check">记得我</label>
                                </div>

                                <div>
                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="button">登入</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <p>没有账户 ? <a href="auth-register.html" class="font-weight-medium text-primary"> 现在注册 </a> </p>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- JAVASCRIPT -->
<script src="/static/assets/libs/jquery/jquery.min.js"></script>
<script src="/static/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/static/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/static/assets/libs/node-waves/waves.min.js"></script>
<script src="/static/common/js/plugins/toastr/toastr.min.js"></script>
<script src="/static/assets/js/app.js"></script>
<script>
    $(function () {
        //初始化
        toastr.options.positionClass = 'toast-top-right';
    });
    $('.btn-block').on('click', function () {
        var username = $('input[name="username"]').val()
        var password = $('input[name="password"]').val()
        if(typeof username == "undefined" || username == null || username == ""){
            toastr.error('请填写用户名')
            return false
        }
        if(typeof password == "undefined" || password == null || password == ""){
            toastr.error('请填写密码')
            return false
        }
        $.ajax({
            url: "./login",
            data: $('#form').serialize(),
            method: 'POST',
            success: function (data) {
                if(data.status == 0){
                    toastr.error(data.message)
                }else{
                    setInterval("myInterval()",1500);//1000为1秒钟
                    toastr.success(data.message)
                }
            }
        });
    })
    function myInterval()
    {
        window.location.href='/service.php/index/index';
    }
</script>
</body>
</html>
