<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>
        <link rel="stylesheet" href="/yyg/Public/plugin/style/global/css/global.css" type="text/css"/>
        <link rel="stylesheet" href="/yyg/Public/plugin/style/global/css/style.css" type="text/css"/>
        <script src="/yyg/Public/plugin/style/global/js/jquery-1.8.3.min.js"></script>
    </head>
    <body>
        <div class="header lr10">
            <?php echo R('admin/headerment',array($ment));?>
        </div>
        <div class="bk10"></div>

        <div class="table_form lr10">
            <form action="/yyg/admin/save" method="post" id="myform">
                <table width="100%" class="lr10">
                    <tr>
                        <td width="80">用户名<input type="hidden" name="id" value="<?php echo ($info['uid']); ?>"></input></td> 
                        <td><?php if($info['uid']): echo ($info['username']); else: ?><input type="text" name="username"  class="input-text" id="username"></input><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>密码</td>
                        <td><input type="password" name="password" class="input-text" id="password" value=""></input></td>
                    </tr>
                    <tr>
                        <td>确认密码</td> 
                        <td><input type="password" name="pwdconfirm" class="input-text" id="pwdconfirm" value=""></input></td>
                    </tr>
                    <tr>
                        <td>E-mail</td>
                        <td><?php if($info['id']): echo ($info['useremail']); else: ?><input type="text" name="email" value="" class="input-text" id="email" size="30"></input><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>所属角色</td>
                        <td>
                            <select name="mid">
                                <option value="0">超级管理员</option>
                                <option value="1" <?php if($info['xianzhi']==1): ?>selected<?php endif; ?>>普通管理员</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="bk15"></div>
                <input type="hidden" name="submit-1" />
                <input type="button" value=" 提交 " id="dosubmit" class="button">
            </form>
        </div><!--table-list end-->
        <script type="text/javascript">
            var error = '';
            var bool = false;
            var id = '';
            $(document).ready(function () {
                document.getElementById('dosubmit').onclick = function () {
                    bool = false;
                    var myform = document.getElementById('myform');
                    if (!myform.password.value) {
                        error = '密码不能为空';
                        id = 'password';
                        bool = true;
                    }
                    if (!myform.pwdconfirm.value) {
                        error = '请在次输入密码';
                        id = 'pwdconfirm';
                        bool = true;
                    }

                    var isTrue = "<?php echo ($info['id']); ?>";
                    if (isTrue)
                    {
                        if (!myform.username.value) {
                            error = '用户名不能为空';
                            id = 'username';
                            bool = true;
                        }
                        if (!myform.email.value) {
                            error = '邮箱不能为空';
                            id = 'email';
                            bool = true;
                        }
                    }

                    if (bool) {
                        window.parent.message(error, 8, 2);
                        $('#' + id).focus();
                        return false;
                    } else {
                        if (myform.password.value != myform.pwdconfirm.value) {
                            window.parent.message("2次密码不相等", 8, 2);
                            return false;
                        }
                        if (isTrue)
                        {
                            $.post('/yyg/admin/checkmusername/', {username: myform.username.value, ajax: true}, function (data) {
                                if (data == 'no') {
                                    window.parent.message("用户名长度在15个字符内,1个汉字等于2个字符", 8, 2);
                                }
                            });
                        }
                        document.getElementById('myform').submit();

                    }
                }
            });

        </script>
    </body>
</html>