<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>后台首页</title>
        <link rel="stylesheet" href="/Public/plugin/style/global/css/global.css" type="text/css"/>
        <link rel="stylesheet" href="/Public/plugin/style/global/css/style.css" type="text/css"/>
        <style>
            th{ border:0px solid #000;}
            tr{ line-height:30px;}
        </style>

    </head>
    <body>
        <div class="header lr10">
            <?php echo R('admin/headerment',array($ment));?>
        </div>
        <div class="bk10"></div>
        <div class="table-list lr10">
            <table width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="90">排序</th>
                        <th width="100">id</th>
                        <th align='center'>品牌名称</th>
                        <th align='center'>所属栏目</th>
                        <th align='center'>管理操作</th>
                    </tr>
                </thead>
                <form action="#" method="post" name="myform">
                    <tbody>
                        <?php foreach($pinpais as $pinpai){ ?>
                        <tr>
                            <td align='center'><input name='listorders[<?php echo $pinpai['id']; ?>]' type='text' size='3' value='<?php echo $pinpai['order']; ?>' class='input-text-c'></td>        
                            <td align='center'><?php echo $pinpai['id']; ?></td>
                            <td align='center'><?php echo $pinpai['name']; ?></td>
                            <td align='center'>			
                                <?php  $cateids = explode(",",$pinpai['cateid']); foreach($cateids as $v){ if(isset($fenleis[$v]['name'])){ echo "[".$fenleis[$v]['name']."]　"; }else{ echo "<font color='red'>不存在</font>"; } } ?>
                            </td>
                            <td align='center'>
                                [<a href="/brand/edit/id/<?php echo $pinpai['id']; ?>">修改</a>]		
                                [<a href="/brand/del/id/<?php echo $pinpai['id']; ?>">删除</a>]
                            </td>
                        </tr>
                        <?php } ?>
                        </table>
                </form>
                <div class="btn_paixu">
                    <div style="width:80px; text-align:center;">
                        <input type="button" class="button" value=" 排序 "
                               onclick="myform.action = '/brand/listorder/dosubmit';myform.submit();"/>
                    </div>
                </div>
                <div id="pages"><ul><li>共 <?php echo $zongji; ?> 条</li><?php echo $fenye->show('one','li'); ?></ul></div>

        </div><!--table-list end-->

    </body>
</html>