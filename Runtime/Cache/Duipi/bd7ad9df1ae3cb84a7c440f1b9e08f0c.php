<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD><TITLE>您需要登录后才可以使用本功能</TITLE>
        <META content="text/html; charset=UTF-8" http-equiv=Content-Type>
        <LINK rel=stylesheet type=text/css href="/Public/plugin/style/images/login.css">
        <SCRIPT type=text/javascript src="/Public/plugin/style/images/jquery.js"></SCRIPT>
        <SCRIPT type=text/javascript src="/Public/plugin/style/images/common.js"></SCRIPT>
        <SCRIPT type=text/javascript src="/Public/plugin/style/images/jquery.tscookie.js"></SCRIPT>
        <SCRIPT type=text/javascript src="/Public/plugin/style/images/jquery.validation.min.js"></SCRIPT>
        <script src="/Public/plugin/style/global/js/jquery-1.8.3.min.js"></script>
        <script src="/Public/plugin/style/global/js/global.js"></script>
        <script src="/Public/plugin/layer/layer.min.js"></script>
        <STYLE type=text/css>
            BODY {
                PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 100%; PADDING-RIGHT: 0px; BACKGROUND: #666666 fixed no-repeat center top; PADDING-TOP: 0px; background-clip: border-box; background-size: cover; background-origin: padding-box
            }
        </STYLE>

        <META name=GENERATOR content="MSHTML 8.00.6001.23636"></HEAD>
    <BODY>
        <DIV class=bg-dot></DIV>
        <DIV class=login-layout>
            <DIV class=top>
                <H5><EM></EM></H5>
                <H2>云购网站源码系统YYYGCMS V4.5后台登陆</H2>
            </DIV>
            <DIV class=box>
                <form action="#" method="post" id="form">
                    <SPAN><LABEL>账号</LABEL> <input type="text" id="input-u" name="username" style="color:#f17564;" class=input-text value="请输入用户名" onfocus="javascript:if (this.value == '请输入用户名')
                                this.value = '';" /> </SPAN><SPAN>
                        <LABEL>密码</LABEL> 
                        <input type="password" id="input-p" name="password" style="color:#8ccfb3;" class=input-password  value="********" onfocus="javascript:if (this.value == '********')
                                    this.value = '';"/> </SPAN><SPAN>
                        <DIV class=code>
                            <DIV class=arrow></DIV>
                            <DIV class=code-img><IMG id=codeimage border=0 name=codeimage 
                                                     src="/tools/checkcode/style/image/type/80_27/"></DIV><A id=hide class=close title=关闭 
                                                                                                href="javascript:void(0);"><I></I></A></DIV><input type="text" id=captcha name="code" style="color:#9dcc5a;width:60px;text-transform:uppercase;" class=input-code value="code" onfocus="javascript:if (this.value == 'code')
                                                                                                            this.value = '';"/> </SPAN><SPAN>


                        <input type="button" id="form_but"  class="input-button" value="登录" />
                    </SPAN></FORM></DIV></DIV>
        <DIV class=bottom></DIV>

        <SCRIPT type=text/javascript>
            $(document).ready(function () {
                //Random background image
                var random_bg = Math.floor(Math.random() * 5 + 1);
                var bg = 'url(/Public/plugin/style/images/login/bg_' + random_bg + '.jpg)';
                $("body").css("background-image", bg);
                //Hide Show verification code
                $("#hide").click(function () {
                    $(".code").fadeOut("slow");
                });
                $("#captcha").focus(function () {
                    $(".code").fadeIn("fast");
                });
                //跳出框架在主窗口登录
                if (top.location != this.location)
                    top.location = this.location;
                $('#user_name').focus();
                if ($.browser.msie && $.browser.version == "6.0") {
                    window.location.href = '<?php echo YYS_MODULE_PATH; ?>/admin/templates/default/ie6update.html';
                }
               // $("#captcha").nc_placeholder();
            });
        </SCRIPT>
        <script type="text/javascript">
            var loading;
            var form_but;
            window.onload = function () {

                document.onkeydown = function () {
                    
                }
                form_but = document.getElementById('form_but');
                form_but.onclick = ajaxsubmit;
                var website_off = "<?php echo C(website_off);?>";
                if (parseInt(website_off)) {
                    var checkcode = document.getElementById('checkcode');
                   // checkcode.src = checkcode.src + new Date().getTime();
                   // var src = checkcode.src;
                   // checkcode.onclick = function () {
                       // this.src = src + '/' + new Date().getTime();
                  //  }
                }
            }

            $(document).ready(function () {
                $.focusblur("#input-u");
                $.focusblur("#input-p");
                $.focusblur("#input-c");
            });
            function ajaxsubmit() {
                var name = document.getElementById('form').username.value;
                var pass = document.getElementById('form').password.value;
                var website_off = "<?php echo C(website_off);?>";
                if (parseInt(website_off)) {
                    var codes = document.getElementById('form').code.value;
                } else {
                    var codes = '';
                }
                //document.getElementById('form').submit();
                $.ajaxSetup({
                    async: false
                });
                $.ajax({
                    "url": window.location.href,
                    "type": "POST",
                    "data": ({username: name, password: pass, code: codes, ajax: true}),
                    "beforeSend": beforeSend, //添加loading信息
                    "success": success//清掉loading信息
                });
            }
            function beforeSend() {
                form_but.value = "登录中...";
                loading = $.layer({
                    type: 3,
                    time: 0,
                    shade: [0.5, '#000', true],
                    border: [5, 0.5, '#7298a6', true],
                    loading: {type: 4}
                });
            }

            function success(data) {
                layer.close(loading);
                form_but.value = "登录";
                var obj = jQuery.parseJSON(data);
                if (!obj.error) {
                    window.location.href = obj.text;
                } else {
                    $.layer({
                        type: 0,
                        area: ['auto', 'auto'],
                        title: ['信息', true],
                        border: [5, 0.5, '#7298a6', true],
                        dialog: {msg: obj.text}
                    });
                    var checkcode = document.getElementById('checkcode');
                    var src = checkcode.src;
                    checkcode.src = '';
                    checkcode.src = src;
                }
            }
        </script>
    </BODY></HTML>