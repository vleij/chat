<?php /*a:4:{s:55:"D:\phpstudy_pro\WWW\chat\app\admin\view\home\index.html";i:1599908844;s:56:"D:\phpstudy_pro\WWW\chat\app\admin\view\public\left.html";i:1599908520;s:57:"D:\phpstudy_pro\WWW\chat\app\admin\view\public\right.html";i:1599809976;s:54:"D:\phpstudy_pro\WWW\chat\app\admin\view\public\js.html";i:1599908822;}*/ ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">

    <title> ChatAdmin- 主页</title>

    <meta name="keywords" content="">
    <meta name="description" content="">

    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->

    <link rel="shortcut icon" href="/static/admin/ico/favicon.ico"> <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.css" rel="stylesheet">
    <link href="/static/admin/css/style.css?v=4.1.0" rel="stylesheet">
</head>

<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
    <div class="nav-close"><i class="fa fa-times-circle"></i>
    </div>
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <span class="block m-t-xs" style="font-size:20px;">
                                        <i class="fa fa-area-chart"></i>
                                        <strong class="font-bold">ChatAdmin</strong>
                                    </span>
                                </span>
                    </a>
                </div>
                <div class="logo-element">ChatAdmin
                </div>
            </li>
            <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$one): $mod = ($i % 2 );++$i;if($one['pid'] == '0'): ?>
            <li class="line dk"></li>
            <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                <span class="ng-scope"><?php echo htmlentities($one['name']); ?></span>
            </li>
            <?php endif; ?>
            <li>
                <?php if($one['pid'] == '0'): ?>
                <a href="#"><i class="fa fa-edit"></i> <span class="nav-label"><?php echo htmlentities($one['name']); ?></span><span class="fa arrow"></span></a>
                <?php endif; if(!(empty($one['child']) || (($one['child'] instanceof \think\Collection || $one['child'] instanceof \think\Paginator ) && $one['child']->isEmpty()))): if(is_array($one['child']) || $one['child'] instanceof \think\Collection || $one['child'] instanceof \think\Paginator): $i = 0; $__LIST__ = $one['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$two): $mod = ($i % 2 );++$i;?>
                <ul class="nav nav-second-level">
                    <?php if(empty($two['child']) || (($two['child'] instanceof \think\Collection || $two['child'] instanceof \think\Paginator ) && $two['child']->isEmpty())): ?>
                    <li><a class="J_menuItem" href="<?php echo htmlentities($two['controller']); ?>/<?php echo htmlentities($two['function']); ?>"><?php echo htmlentities($two['name']); ?></a>
                    </li>
                    <?php endif; if(!(empty($two['child']) || (($two['child'] instanceof \think\Collection || $two['child'] instanceof \think\Paginator ) && $two['child']->isEmpty()))): ?>
                    <li>
                        <a href="#"><?php echo htmlentities($two['name']); ?><span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <?php if(is_array($two['child']) || $two['child'] instanceof \think\Collection || $two['child'] instanceof \think\Paginator): $i = 0; $__LIST__ = $two['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$three): $mod = ($i % 2 );++$i;?>
                            <li><a class="J_menuItem" href="form_webuploader.html"><?php echo htmlentities($three['name']); ?></a>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li>

                    <?php endif; ?>

                </ul>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <?php endif; ?>

            <?php endforeach; endif; else: echo "" ;endif; ?>


        </ul>
    </div>
</nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-info " href="#"><i class="fa fa-bars"></i> </a>
                <form role="search" class="navbar-form-custom" method="post" action="search_results.html">
                    <div class="form-group">
                        <input type="text" placeholder="请输入您需要查找的内容 …" class="form-control" name="top-search" id="top-search">
                    </div>
                </form>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i> <span class="label label-warning">16</span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li class="m-t-xs">
                            <div class="dropdown-messages-box">
                                <a href="profile.html" class="pull-left">
                                    <img alt="image" class="img-circle" src="/static/admin/img/a7.jpg">
                                </a>
                                <div class="media-body">
                                    <small class="pull-right">46小时前</small>
                                    <strong>小四</strong> 是不是只有我死了,你们才不骂爵迹
                                    <br>
                                    <small class="text-muted">3天前 2014.11.8</small>
                                </div>
                            </div>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <div class="dropdown-messages-box">
                                <a href="profile.html" class="pull-left">
                                    <img alt="image" class="img-circle" src="/static/admin/img/a4.jpg">
                                </a>
                                <div class="media-body ">
                                    <small class="pull-right text-navy">25小时前</small>
                                    <strong>二愣子</strong> 呵呵
                                    <br>
                                    <small class="text-muted">昨天</small>
                                </div>
                            </div>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <div class="text-center link-block">
                                <a class="J_menuItem" href="mailbox.html">
                                    <i class="fa fa-envelope"></i> <strong> 查看所有消息</strong>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i> <span class="label label-primary">8</span>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="mailbox.html">
                                <div>
                                    <i class="fa fa-envelope fa-fw"></i> 您有16条未读消息
                                    <span class="pull-right text-muted small">4分钟前</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="profile.html">
                                <div>
                                    <i class="fa fa-qq fa-fw"></i> 3条新回复
                                    <span class="pull-right text-muted small">12分钟钱</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <div class="text-center link-block">
                                <a class="J_menuItem" href="notifications.html">
                                    <strong>查看所有 </strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
    <div class="row J_mainContent" id="content-main">
        <iframe id="J_iframe" width="100%" height="100%" src="main.html?v=4.0" frameborder="0" data-id="index_v1.html" seamless></iframe>
    </div>
</div>
        <!--右侧部分结束-->
    </div>

    <!-- 全局js -->
    <script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/static/admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>

    <!-- 自定义js -->
    <script src="/static/admin/js/hAdmin.js?v=4.1.0"></script>
    <script type="text/javascript" src="/static/admin/js/index.js"></script>

    <!-- 第三方插件 -->
    <script src="/static/admin/js/plugins/pace/pace.min.js"></script>

</body>

</html>
