<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="">
<script type="text/javascript" src="/Public/piyungou/js/jquery-1.8.3.min.js"></script>
<script src="/Public/piyungou/js/jquery.JPlaceholder.js"></script>
<title>注册<?php echo C('web_name_two');?>网</title>
<link type="text/css" rel="stylesheet" href="/Public/App/Css/us.css" />
<!--[if lte IE 6]>
<script src="/App/Js/iepng.js" type="text/javascript"></script>
<script type="text/javascript">
        DD_belatedPNG.fix('div,ul,img,li,p,a,i,span,em,h3,th,td');
</script>
<![endif]-->
</head>
<body>
<div class="top_bar">
	<a class="logo" href="/index/index" style="background: url(/Public/uploads/<?php echo R('base/Getlogo',array());?>) no-repeat;"><h1><?php echo C('web_name_two');?></h1></a>
   
    <p class="clear"></p>
</div>
<script type="text/javascript" src="/Public/piyungou/js/jquery.Validform.min.js"></script>
<script type="text/javascript" src="/Public/piyungou/js/passwordStrength-min.js"></script>
<script type="text/javascript">

$(function(){
	var demo=$(".login_ConInput").Validform({
		tiptype:2,
		usePlugin:{
			passwordstrength:{
				minLen:6,
				maxLen:20,
				trigger:function(obj,error){
					if(error){
						obj.parent().next().find(".Validform_checktip").show();
						obj.parent().next().find(".passwordStrength").hide();
					}else{
						obj.parent().next().find(".Validform_checktip").hide();
						obj.parent().next().find(".passwordStrength").show();
					}
				}
			}
		}

	});

})
</script>


<div class="log_reg_wrap"  style="background-image:url(/Public/App/img/register_bg.jpg);">
	<div class="log_reg_box">
    	<h2 class="reg_tt"><?php echo (L("web_user_user_register")); echo C('web_name_two'); echo (L("web_user_web")); ?></h2>
        <p class="blank30"></p>
        <form method="post" action="" enctype="application/x-www-form-urlencoded">
           <input type="hidden" name="regcount" value="1" />
            <table width="260" border="0" cellspacing="0" cellpadding="0" class="form_table2" align="center">
              <tr>
                <td >
                		<div class="form_input_wrap">
							<input id="ipyanz" datatype="m | e" sucmsg=<?php echo (L("web_user_account_yes")); ?> placeholder=<?php echo (L("web_user_phone_web")); ?> nullmsg=<?php echo (L("web_user_add_account")); ?> errormsg=<?php echo (L("web_user_phone_web")); ?> type="text" name="name" class="form_input" />
                            <div class="Validform_checktip"></div>
                            <div class="info"><?php echo (L("web_user_please")); ?><span class="dec"></span></div>
                        </div>
                 </td>
              </tr>
              <tr>
                <td>
                		<div class="form_input_wrap">
							<input datatype="*6-20" plugin="passwordStrength" nullmsg=<?php echo (L("web_user_set_password")); ?> placeholder=<?php echo (L("web_user_password")); ?> errormsg=<?php echo (L("web_user_hini_password")); ?> type="password" name="userpassword" class="form_input strength_input" maxlength="20"/>
                            <div class="Validform_checktip"></div>
                            <div class="passwordStrength" style="display:none"><b><?php echo (L("web_user_password_intensity")); ?>：</b><span><?php echo (L("web_user_weak")); ?></span><span><?php echo (L("web_user_centre")); ?></span><span class="last"><?php echo (L("web_user_stubborn")); ?></span><span class="dec"></span></div>
                            <div class="info"><?php echo (L("web_user_password_content")); ?><br><?php echo (L("web_user_password_suggest")); ?><span class="dec"></span></div>
                        </div>
                 </td>
              </tr>
              <tr>
                <td>
                		<div class="form_input_wrap">
							<input tip="test"  datatype="*" recheck="userpassword"  placeholder=<?php echo (L("web_user_password_again_two")); ?> nullmsg=<?php echo (L("web_user_password_again")); ?> errormsg=<?php echo (L("web_user_password_again_two_no")); ?> type="password" name="userpassword2" class="form_input" maxlength="20"/>
                            <div class="Validform_checktip"></div>
                            <div class="info"><?php echo (L("web_user_password_again")); ?><span class="dec"></span></div>
                        </div>
                 </td>
              </tr>

			                <tr>
                <td><input name="submit" type="submit" class="reg_btn" value=<?php echo (L("web_user_user_register")); ?>></td>
              </tr>
            </table>
        <div class="text_bar"><?php echo (L("web_user_has_account")); ?>，<a href="/user/login" class="blue_link">[<?php echo (L("web_user_please_login")); ?>]</a></div>
    <div class="agree_to_the_terms form_input_wrap"><input name="haveread" id="haveread" type="checkbox" value="1" checked class="check_input" datatype="*"  nullmsg=<?php echo (L("web_user_please_read")); ?> errormsg=<?php echo (L("web_user_please_read")); ?> /><a href="/help/3" target="_blank">&nbsp;<?php echo (L("web_user_please_read")); ?></a> <div class="info"><?php echo (L("web_user_please_read")); ?><span class="dec"></span></div><div class="Validform_checktip"></div></div>
         </form>
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
<p style="display:none">
</p>
<!--购物车底部结束-->
</body>
</html>