<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=utf-8"> 
            <link rel="Shortcut Icon" href=">/favicon.ico" />
            <LINK  id=cssfile rel=stylesheet type=text/css href="/Public/plugin/style/images/skin_0.css"/>
            <link rel="stylesheet" href="/Public/plugin/style/global/css/global.css" type="text/css"/>
            <link rel="stylesheet" href="/Public/plugin/style/global/css/index.css" type="text/css"/>
            <LINK id=cssfile rel=stylesheet type=text/css href="/Public/plugin/style/images/skin_0.css"/>
            <script src="/Public/plugin/style/global/js/jquery-1.8.3.min.js"></script>
            <script src="/Public/plugin/layer/layer.min.js"></script>
            <script src="/Public/plugin/style/global/js/global.js"></script>
            <SCRIPT type=text/javascript src="/Public/plugin/style/images/jquery.js"></SCRIPT>
            <SCRIPT type=text/javascript src="/Public/plugin/style/images/jquery.validation.min.js"></SCRIPT>
            <SCRIPT type=text/javascript src="/Public/plugin/style/images/jquery.cookie.js"></SCRIPT>
            <title>后台首页</title>
            <script type="text/javascript">
                var ready = 1;
                var kj_width;
                var kj_height;
                var header_height = 91;
                var R_label;
                var R_label_one = "当前位置: 系统设置 >";


                function left(init) {
                    var left = document.getElementById("left");
                    var leftlist = left.getElementsByTagName("ul");

                    for (var k = 0; k < leftlist.length; k++) {
                        leftlist[k].style.display = "none";
                    }
                    document.getElementById(init).style.display = "block";
                }

                function secBoard(elementID, n, init, r_lable) {

                    var elem = document.getElementById(elementID);
                    var elemlist = elem.getElementsByTagName("li");
                    for (var i = 0; i < elemlist.length; i++) {
                        elemlist[i].className = "normal";
                    }
                    elemlist[n].className = "current";
                    R_label_one = "当前位置: " + r_lable + " >";
                    R_label.text(R_label_one);
                    left(init);
                }


                function set_div() {
                    kj_width = $(window).width();
                    kj_height = $(window).height();
                    if (kj_width < 1000) {
                        kj_width = 1000;
                    }
                    if (kj_height < 500) {
                        kj_height = 500;
                    }

                    $("#header").css('width', kj_width);
                    $("#header").css('height', header_height);
                    $("#left").css('height', kj_height - header_height);
                    $("#right").css('height', kj_height - header_height);
                    $("#left").css('top', header_height);
                    $("#right").css('top', header_height);

                    $("#left").css('width', 180);
                    $("#right").css('width', kj_width - 182);
                    $("#right").css('left', 182);

                    $("#right_iframe").css('width', kj_width - 146);
                    $("#right_iframe").css('height', kj_height - 108);

                    $("#iframe_src").css('width', kj_width - 208);
                    $("#iframe_src").css('height', kj_height - 150);

                    $("#off_on").css('height', kj_height - 180);

                    var nav = $("#nav");


                }


                $(document).ready(function () {
                    set_div();
                    $("#off_on").click(function () {
                        if ($(this).attr('val') == 'open') {
                            $(this).attr('val', 'exit');
                            $("#right").css('width', kj_width);
                            $("#right").css('left', 1);
                            $("#right_iframe").css('width', kj_width - 25);
                            $("iframe").css('width', kj_width - 27);
                        } else {
                            $(this).attr('val', 'open');
                            $("#right").css('width', kj_width - 182);
                            $("#right").css('left', 182);
                            $("#right_iframe").css('width', kj_width - 206);
                            $("iframe").css('width', kj_width - 208);
                        }
                    });

                    left('setting');
                    $(".left_date a").click(function () {
                        $(".left_date li").removeClass("set");
                        $(this).parent().addClass("set");
                        R_label.text(R_label_one + ' ' + $(this).text() + ' >');
                        $("#iframe_src").attr("src", $(this).attr("src"));
                    });
                    $(".left_date1 a").click(function () {
                        $(".left_date li").removeClass("set");
                        $(this).parent().addClass("set");
                        R_label.text(R_label_one + ' ' + $(this).text() + ' >');
                        $("#iframe_src").attr("src", $(this).attr("src"));
                    });
                    $("#iframe_src").attr("src", "/admin/Tdefault");
                    R_label = $("#R_label");
                    $('body').bind('contextmenu', function () {
                        return false;
                    });
                    $('body').bind("selectstart", function () {
                        return false;
                    });

                });

                function api_off_on_open(key) {
                    if (key == 'open') {
                        $("#off_on").attr('val', 'exit');
                        $("#right").css('width', kj_width);
                        $("#right").css('left', 1);
                        $("#right_iframe").css('width', kj_width - 25);
                        $("iframe").css('width', kj_width - 27);
                    } else {
                        $("#off_on").attr('val', 'open');
                        $("#right").css('width', kj_width - 182);
                        $("#right").css('left', 182);
                        $("#right_iframe").css('width', kj_width - 206);
                        $("iframe").css('width', kj_width - 208);
                    }
                }
            </script>

            <style>
                .header_case{  position:absolute; right:10px; top:10px; color:#fff}
                .header_case a{ padding-left:5px}
                .header_case a:hover{ color:#fff; }

                .left_date a{background: url() repeat-y scroll left top rgba(0, 0, 0, 0);}
                .left_date{overflow:hidden;}
                .left_date ul{ margin:0px; padding:0px;}
                .left_date li{line-height:25px; height:25px; margin:0px 10px; margin-left:15px; overflow:hidden;}
                .left_date li a{ display:block;text-indent:5px;  overflow:hidden}
                .left_date li a:hover{ background-color:#d3e8f2;text-decoration:none;border-radius:3px;}
                .left_date .set a{background-color:#d3e8f2;border-radius:3px; font-weight:bold}
                .head{ border-bottom:1px solid #c5e8f1; color:#2a8bbb; font-weight:bold; margin-bottom:10px;}

            </style>

    </head>
    <SCRIPT>
        
        $(document).ready(function () {
            $('span.bar-btn').click(function () {
                $('ul.bar-list').toggle('fast');
            });
        });

        $(document).ready(function () {
            var pagestyle = function () {
                var iframe = $("#iframe_src");
				
                var h = $(window).height() - iframe.offset().top;
                var w = $(window).width() - iframe.offset().left;
                if (h < 300)
                    h = 300;
                if (w < 973)
                    w = 973;
                iframe.height(h);
                iframe.width(w);
            }
            pagestyle();
            $(window).resize(pagestyle);
            //turn location
            if ($.cookie('now_location_act') != null) {
                openItem($.cookie('now_location_op') + ',' + $.cookie('now_location_act') + ',' + $.cookie('now_location_nav'));
            } else {
                $('#mainMenu>ul').first().css('display', 'block');
                //第一次进入后台时，默认定到欢迎界面
                $('#item_welcome').addClass('selected');
                $('#workspace').attr('src', '/admin/Tdefault');
            }
            $('#iframe_refresh').click(function () {
                var fr = document.frames ? document.frames("workspace") : document.getElementById("workspace").contentWindow;
                ;
                fr.location.reload();
            });

        });
        //收藏夹
        function addBookmark(url, label) {
            if (document.all)
            {
                window.external.addFavorite(url, label);
            } else if (window.sidebar)
            {
                window.sidebar.addPanel(label, url, '');
            }
        }


        function openItem(args) {
            closeBg();
            //cookie

            if ($.cookie('F81E_sys_key') === null) {
                location.href = 'index.php?act=login&op=login';
                return false;
            }

            spl = args.split(',');
            op = spl[0];
            try {
                act = spl[1];
                nav = spl[2];
            } catch (ex) {
            }
            if (typeof (act) == 'undefined') {
                var nav = args;
            }
            $('.actived').removeClass('actived');
            $('#nav_' + nav).addClass('actived');

            $('.selected').removeClass('selected');

            //show
            $('#mainMenu ul').css('display', 'none');
            $('#sort_' + nav).css('display', 'block');

            if (typeof (act) == 'undefined') {
                //顶部菜单事件
                html = $('#sort_' + nav + '>li>dl>dd>ol>li').first().html();
                str = html.match(/openItem\('(.*)'\)/ig);
                arg = str[0].split("'");
                spl = arg[1].split(',');
                op = spl[0];
                act = spl[1];
                nav = spl[2];
                first_obj = $('#sort_' + nav + '>li>dl>dd>ol>li').first().children('a');
                $(first_obj).addClass('selected');
                //crumbs
                $('#crumbs').html('<span>' + $('#nav_' + nav + ' > span').html() + '</span><span class="arrow">&nbsp;</span><span>' + $(first_obj).text() + '</span>');
            } else {
                //左侧菜单事件
                //location
                $.cookie('now_location_nav', nav);
                $.cookie('now_location_act', act);
                $.cookie('now_location_op', op);
                $("a[name='item_" + op + act + "']").addClass('selected');
                //crumbs
                $('#crumbs').html('<span>' + $('#nav_' + nav + ' > span').html() + '</span><span class="arrow">&nbsp;</span><span>' + $('#item_' + op + act).html() + '</span>');
            }
            src = 'index.php?act=' + act + '&op=' + op;
            $('#workspace').attr('src', src);

        }

        $(function () {
            bindAdminMenu();
        })
        function bindAdminMenu() {

            $("[nc_type='parentli']").click(function () {
                var key = $(this).attr('dataparam');
                if ($(this).find("dd").css("display") == "none") {
                    $("[nc_type='" + key + "']").slideDown("fast");
                    $(this).find('dt').css("background-position", "-322px -170px");
                    $(this).find("dd").show();
                } else {
                    $("[nc_type='" + key + "']").slideUp("fast");
                    $(this).find('dt').css("background-position", "-483px -170px");
                    $(this).find("dd").hide();
                }
            });
        }
    </SCRIPT>

    <SCRIPT type=text/javascript>
        //显示灰色JS遮罩层
        function showBg(ct, content) {
            var bH = $("body").height();
            var bW = $("body").width();
            var objWH = getObjWh(ct);
            $("#pagemask").css({width: bW, height: bH, display: "none"});
            var tbT = objWH.split("|")[0] + "px";
            var tbL = objWH.split("|")[1] + "px";
            $("#" + ct).css({top: tbT, left: tbL, display: "block"});
            $(window).scroll(function () {
                resetBg()
            });
            $(window).resize(function () {
                resetBg()
            });
        }
        function getObjWh(obj) {
            var st = document.documentElement.scrollTop;//滚动条距顶部的距离
            var sl = document.documentElement.scrollLeft;//滚动条距左边的距离
            var ch = document.documentElement.clientHeight;//屏幕的高度
            var cw = document.documentElement.clientWidth;//屏幕的宽度
            var objH = $("#" + obj).height();//浮动对象的高度
            var objW = $("#" + obj).width();//浮动对象的宽度
            var objT = Number(st) + (Number(ch) - Number(objH)) / 2;
            var objL = Number(sl) + (Number(cw) - Number(objW)) / 2;
            return objT + "|" + objL;
        }
        function resetBg() {
            var fullbg = $("#pagemask").css("display");
            if (fullbg == "block") {
                var bH2 = $("body").height();
                var bW2 = $("body").width();
                $("#pagemask").css({width: bW2, height: bH2});
                var objV = getObjWh("dialog");
                var tbT = objV.split("|")[0] + "px";
                var tbL = objV.split("|")[1] + "px";
                $("#dialog").css({top: tbT, left: tbL});
            }
        }

        //关闭灰色JS遮罩层和操作窗口
        function closeBg() {
            $("#pagemask").css("display", "none");
            $("#dialog").css("display", "none");
        }
    </SCRIPT>

    <SCRIPT type=text/javascript>
        $(function () {
            var $li = $("#skin li");
            $li.click(function () {
                $("#" + this.id).addClass("selected").siblings().removeClass("selected");
                $("#cssfile").attr("href", "/Public/templates/default/css/" + (this.id) + ".css");
                $.cookie("MyCssSkin", this.id, {path: '/', expires: 10});

                $('iframe').contents().find('#cssfile2').attr("href", "/Public/templates/default/css/" + (this.id) + ".css");
            });

            var cookie_skin = $.cookie("MyCssSkin");
            if (cookie_skin) {
                $("#" + cookie_skin).addClass("selected").siblings().removeClass("selected");
                $("#cssfile").attr("href", "/Public/templates/default/css/" + cookie_skin + ".css");
                $.cookie("MyCssSkin", cookie_skin, {path: '/', expires: 10});
            }
        });
        function addFavorite(url, title) {
            try {
                window.external.addFavorite(url, title);
            } catch (e) {
                try {
                    window.sidebar.addPanel(title, url, '');
                } catch (e) {
                    showDialog("请按 Ctrl+D 键添加到收藏夹", 'notice');
                }
            }
        }
    </SCRIPT>



    <TABLE style="WIDTH: 100%" id=frametable cellSpacing=0 cellPadding=0 
           width="100%" height="100%">
        <TBODY>
            <TR>
                <TD class=mainhd height=90 colSpan=2>
                    <DIV class=layout-header><!-- Title/Logo - can use text instead of image -->
                        <DIV id=title></DIV><!-- Top navigation -->
                        <DIV id=topnav class=top-nav>
                            <UL>
                                <LI class="adminid" title=<?php echo ($info['username']); ?>您好&nbsp;:&nbsp;><STRONG><?php echo ($info['username']); ?></STRONG></LI>
                                <LI><A  href="/admin/lists"  target=workspace><SPAN>修改密码</SPAN></A></LI>
                                <LI><A title=安全退出  href="/admin/out"><SPAN>安全退出</SPAN></A></LI>
                                <LI><A title=网站首页 href="" target=_blank><SPAN>网站首页</SPAN></A></LI></UL>
                        </DIV>
                        <NAV id="nav" class="main-nav">
                            <UL class="left_date1">
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/admin/Tdefault"><SPAN>后台首页</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/sgoods/goods_list"><SPAN>商城管理</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/goods/goods_add"><SPAN>添加商品</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/goods/goods_list"><SPAN>商品列表</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/member/lists"><SPAN>会员列表</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/menu/navigation"><SPAN>导航管理</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/config/qq_set_config"><SPAN>QQ登陆</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/card/lists"><SPAN>卡密系统</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/admin/lists"><SPAN>管理员设置</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/order/shaidan_admin"><SPAN>晒单管理</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/tools/cache"><SPAN>清理缓存</SPAN></A></LI>
				<LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/update/index"><SPAN>在线更新</SPAN></A></LI>
                                <LI><A id="nav_microshop" class="link"  href="javascript:void(0);" src="/Beifen/index"><SPAN>数据库管理</SPAN></A></LI>
                            </UL></NAV>

                        <DIV class=loca><STRONG></STRONG> `
                        </DIV>
                        <DIV class=toolbar>



                        </DIV></DIV></DIV>
                    <DIV></DIV></TD></TR>

            <!--header end-->
            <div id="left">
                <ul class="left_date" id="setting">   
                    <?php if(is_array($menu)): foreach($menu as $key=>$vo): ?><li class="head"><?php echo ($key); ?></li>
                        <?php if(is_array($vo)): foreach($vo as $k=>$v): ?><li><a href="javascript:void(0);" src="/<?php echo ($k); ?>"><?php echo ($v); ?></a></li><?php endforeach; endif; endforeach; endif; ?>
                    <!--                    <li class="head">模板风格</li>
                                        <li><a href="javascript:void(0);" src="<?php echo YYS_MODULE_PATH; ?>/template">模板设置</a></li>       
                                        <li><a href="javascript:void(0);" src="<?php echo YYS_MODULE_PATH; ?>/template/see">查看模板</a></li> 
                                        <li class="head">插件管理</li>
                                        <li><a href="javascript:void(0);" src="<?php echo YYS_MODULE_PATH; ?>/fund/fundset">公益基金</a></li>-->
                </ul>
                <div style="padding:30px 10px; color:#ccc">
                    <p>
                        (c) 2014 yiyuansha.com<br>
                            All Rights Reserved.
                    </p>
                </div>

            </div><!--left end-->
            <div id="right">
                <div class="right_top">
                    <ul class="R_label" id="R_label">
                        当前位置: 系统设置 >  后台主页 >
                    </ul>
                    <ul class="R_btn">
                        <a href="javascript:;" onClick="btn_iframef5();" class="system_button"><span>刷新框架</span></a>
                        <!-- <a href="javascript:;" onClick="btn_map('<?php echo YYS_MODULE_PATH; ?>/index/map');" class="system_button"><span>后台地图</span></a> -->
                    </ul>
                </div>
                <div class="right_left">
                    <a href="#" val="open" title="全屏" id="off_on">全屏</a>
                </div>
                <div id="right_iframe">

                    <iframe id="iframe_src" name="iframe" class="iframe"
                            frameborder="no" border="1" marginwidth="0" marginheight="0" 
                            src="" 
                            scrolling="auto" allowtransparency="yes" style="width:100%; height:100%">
                    </iframe>

                </div>
            </div><!--right end-->


            </body>
            </html>