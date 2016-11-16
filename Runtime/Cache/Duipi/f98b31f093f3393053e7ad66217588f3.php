<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>
        <link rel="stylesheet" href="/Public/plugin/style/global/css/global.css" type="text/css"/>
        <link rel="stylesheet" href="/Public/plugin/style/global/css/style.css" type="text/css"/>
        <style>
            tr{height:40px;line-height:40px}
        </style>
    </head>
    <body>
        <div class="header lr10">
            <?php echo R('admin/headerment',array($ment));?>
        </div>
        <div class="bk10"></div>
        <div class="table-list lr10">
            <!--start-->
            <form name="myform" action="" method="post">
                <table width="100%" cellspacing="0">	
                    <tr>
                        <td width="200" align="right">允许上传附件大小：</td>
                        <td><input type="text" name="upsize" value="<?php echo C('upsize');?>"  class="input-text"> bit字节  1024字节=1K</td>

                    </tr>
                    <tr>
                        <td width="200" align="right">允许上传图片类型：</td>
                        <td><input type="text" name="up_image_type" value="<?php echo C('up_image_type');?>" class="input-text wid250">
                                <span class="span_meg"> 允许上传的图片类型： 用 , 号分隔</span>
                        </td>
                    </tr>	
                    <tr>
                        <td width="200" align="right">允许上传附件类型：</td>
                        <td><input type="text" name="up_soft_type" value="<?php echo C('up_soft_type');?>" class="input-text wid250">
                                <span class="span_meg"> 允许上传附件类型： 用 , 号分隔</span>
                        </td>
                    </tr>	
                    <tr>
                        <td width="200" align="right">允许上传媒体类型： </td>
                        <td><input type="text" name="up_media_type" value="<?php echo C('up_media_type');?>" class="input-text wid250">
                                <span class="span_meg"> 允许上传媒体类型： 用 , 号分隔</span>
                        </td>
                    </tr>	
                    <tr>
                        <td width="200" align="right"></td>
                        <td><input type="submit" class="button" name="dosubmit"  value=" 提交 " ></td>		
                    </tr>
                </table>
            </form>

        </div><!--table-list end-->

        <script>

        </script>
    </body>
</html>