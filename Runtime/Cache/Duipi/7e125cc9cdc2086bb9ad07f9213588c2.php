<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo (L("web_user_login")); ?>-<?php echo C("web_name");?></title>
<link type="text/css" rel="stylesheet" href="/Public/App/Css/us.css" />
<!--[if lte IE 6]>

<script src="/App/Js/iepng.js" type="text/javascript"></script>
<script type="text/javascript">
        DD_belatedPNG.fix('div,ul,img,li,p,a,i,span,em,h3,th,td');
</script>
<![endif]-->
<script type="text/javascript" src="/Public/piyungou/js/jquery-1.8.3.min.js"></script>
<script src="/Public/piyungou/js/jquery.JPlaceholder.js"></script>
</head>
<body>
<div class="top_bar">
	<a class="logo" href="/index/index" style="background: url(/Public/uploads/<?php echo R('base/Getlogo',array());?>) no-repeat;"><h1><?php echo C('web_name_two');?>网</h1></a>
    
    <p class="clear"></p>
</div>
<script type="text/javascript">
$(function(){
	var demo=$(".registerform").Validform({
		tiptype:2,
	});
})
</script>
<div class="log_reg_wrap"  style="background-image:url(/Public/App/Img/login_bg.jpg);">
	<div class="log_reg_box">
    	<h2><?php echo (L("web_user_login")); echo C('web_name_two'); echo (L("web_user_web")); ?></h2>
        <p class="blank30"></p>
        <form class="registerform" method="post" action="">
            <table width="260" border="0" cellspacing="0" cellpadding="0" class="form_table2" align="center">
              <tr>
                <td >
                		<div class="form_input_wrap">
							<input class="form_input" name="username" type="text" placeholder=<?php echo (L("web_user_phone_web")); ?> datatype="m | e" nullmsg=<?php echo (L("web_user_user_account")); ?> errormsg=<?php echo (L("web_user_phone_web")); ?> />
                            <div class="Validform_checktip"></div>
                            <div class="info"><?php echo (L("web_user_please_account")); ?><span class="dec"></span></div>
                        </div>
                 </td>
              </tr>
              <tr>
                <td>
                		<div class="form_input_wrap">
							<input class="form_input" name="password" type="password" placeholder=<?php echo (L("web_user_password")); ?> datatype="*6-20" nullmsg=<?php echo (L("web_user_set_password")); ?> errormsg=<?php echo (L("web_user_hini_password")); ?>/>
                            <div class="Validform_checktip"></div>
                            <div class="info"><?php echo (L("web_user_please_password")); ?><span class="dec"></span></div>
                        </div>
                 </td>
              </tr>
              <tr>
                <td><input name="submit" type="submit" value=<?php echo (L("web_user_login")); ?> class="login_btn" ></td>
              </tr>
            </table>
        </form>
        <div class="text_bar"><a href="/user/register" class="ceb4">[<?php echo (L("web_user_register")); ?>]</a> | <a href="/user/findpassword" class="c999">[<?php echo (L("web_user_forget_password")); ?>]</a></div>
        <!--<div class="cooperative_logging"><p>第三方登录</p><a href="/Qqlogin/index" class="qq_ico"></a> -->
		<a href="/user/wxloginpc" class="qq_ico1"></a>
		</div>
    </div>
</div>
<div class="footer_jian_wrap footer_jian_nbg">
	<div class="footer_jian">
		<p><a target="_blank" href="/index/help/code/15"><?php echo (L("web_user_about_me")); ?></a><em class="line"></em>
		<a target="_blank" href="/index/help/code/16"><?php echo (L("web_user_add_me")); ?></a><em class="line"></em>
		<a target="_blank" href="/index/help/code/13"><?php echo (L("web_user_suggest")); ?></a><em class="line"></em>
		<a target="_blank" href="/index/help/code/3"><?php echo (L("web_user_agreement")); ?></a><em class="line"></em>
		<a target="_blank" href="/index/help/code/18"><?php echo (L("web_user_contact")); ?></a></p>
             <p><a href="" target="_blank" title="wangzhi"><?php echo C("web_name");?></a> &nbsp;<?php echo C('web_copyright');?></p>
    </div>
</div>
</body>
</html>