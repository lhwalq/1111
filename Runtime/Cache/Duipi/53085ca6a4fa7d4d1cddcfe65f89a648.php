<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title><?php if(isset($biaoti)): echo ($biaoti); else: echo C("web_name"); endif; ?></title>
            <meta name="keywords" content=<?php if(isset($guanjianzi)): ?>"<?php echo ($guanjianzi); ?>"<?php else: ?>"<?php echo C('web_key');?>"<?php endif; ?> />
                <meta name="description" content=<?php if(isset($miaoshu)): ?>"<?php echo ($miaoshu); ?>"<?php else: ?>"<?php echo C('web_des');?>"<?php endif; ?> />
                    <link rel="stylesheet" type="text/css" href="/Public/piyungou/css/GoodsDetail.css"/>
                    <link rel="stylesheet" type="text/css" href="/Public/piyungou/css/Comm.css"/>
                    <script type="text/javascript" src="/Public/plugin/style/global/js/jquery-1.8.3.min.js"></script>	
                    <style> 
                        .AllRecW .AllRecR .AllRecR_T span.user_see_code{
                            color: #fff;
                        }
                        .AllRecR{ border:1px solid #f8f8f8; background:#f8f8f8; padding:5px 0px; position:relative;}
                        .user_codes_box{ display:none }
                        .user_codes { color:#aaa; padding-left:35px;word-break:break-all;overflow:hidden;}
                        .user_codes i{ width:75px; display:inline-block; text-align:center; height:20px;}
                        .user_codes_js { width:100%; line-height:40px; background:#f8f8f8;text-align:center; color:#999; cursor:pointer;}
                        .user_see_code{border-radius:5px; position:absolute; right:10px; width:75px; text-align:center; height:25px; line-height:25px; background:#ED610C; cursor:pointer; display:none;}
                        #buyWay0{background: url(/Public/style/css/img/buyWay.png); background-position: -228px 0; display: inline-block; height: 15px; margin: 0 2px 0 5px; overflow: hidden; vertical-align: -3px; width: 17px;}
                        #buyWay1{background: url(/Public/style/css/img/buyWay.png); background-position: -364px 0; display: inline-block; height: 15px; margin: 0 2px 0 5px; overflow: hidden; vertical-align: -3px; width: 17px;}
                    </style>
                    </head>
                    <body style="width:970px; min-height:150px;padding-top:20px;background-color:#fff">
                        <?php if(!$yys_record_list): ?><h1 style=" text-align:center;width:100%;font-size:22px; font-weight:bold;color:#555;">还没有购买记录,赶快购买吧!</h1><?php endif; ?>       
                        <!--获取当前会商品的购买记录50条-->		
                        <?php  foreach($yys_record_list as $k=>$weer){ if($k==0){ echo '<div class="AllRecW AllRecTime"><p>'.date("Y-m-d",$weer['time']).'</p> <b></b></div>'; } if($k >0 && date("Ymd",$yys_record_list[$k-1]['time']) > date("Ymd",$weer['time'])){ echo '<div class="AllRecW AllRecTime"><p>'.date("Y-m-d",$weer['time']).'</p> <b></b></div>'; } ?>       
                        <div class="AllRecW AllReclist"><div class="AllRecL fl"><?php echo R("base/microt",array($weer['time']));?><i></i></div>
                            <div class="AllRecR fl">
                                <p class="AllRecR_T">            	
                                    <span name="spCodeInfo" class="AllRecR_Over">

                                        <a class="Headpic" href="/user/uname/d/<?php echo R('base/idjia',array($weer['uid']));?>.html" target="_blank"><img src="/Public/uploads/<?php echo ($weer['uphoto']); ?>" border="0" width="20" height="20"></a>
                                        <a href="/user/uname/d/<?php echo R('base/idjia',array($weer['uid']));?>.html" target="_blank" class="blue"><?php echo R("base/strcut",array($weer['username'],6));?></a>

                                        <?php if($weer['ip']): ?>(<?php echo R('base/huode_ip',array($weer['uid'],'ipcity'));?> IP:<?php echo R('base/huode_ip',array($weer['id'],'ipmac'));?>)<?php endif; ?>
                                        云购了<em class="Fb orange"><?php echo ($weer['gonumber']); ?></em>人次

                                    </span>     
                                    <span class="user_see_code" see="off">查看云购码</span>           
                                </p>
                                <div class="user_codes_box">
                                    <div class="user_codes" style="max-height:260px;">
                                        <?php  $codes = explode(",",$weer['goucode']); foreach($codes as $c){ echo "<i>".$c."</i>"; } ?> 
                                    </div> 
                                    <?php if($weer['gonumber'] > '98'): ?><div class="user_codes_js" click="off" num="<?php echo ($weer['gonumber']); ?>">展开查看全部↓</div><?php endif; ?>                                 
                                </div>
                            </div>
                        </div>


                        <?php }?>
                        



                        
                        <!--/获取当前会商品的购买记录-->
                        <?php if($zongji > $num): ?><div class="pagesx" style=" padding-right:30px;"><?php echo $fenye->show('two'); ?></div><?php endif; ?>

                        <script>
                            $(function(){
                            <!--补丁3.1.6_b.0.1 -->
                                    <!--补丁3.1.6_b.0.1-->
                                    $(".AllRecR").mousemove(function(){
                            $(this).css("border", "1px solid #eee");
                            $(this).find(".user_see_code").show();
                            });
                            $(".AllRecR").mouseleave(function(){
                            $(this).css("border", "1px solid #f8f8f8");
                            if ($(this).find(".user_see_code").attr("see") == 'off'){
                            $(this).find(".user_see_code").hide();
                            }
                            });
                            $(".user_see_code").click(function(){
                            if ($(this).attr("see") == 'off'){
                            $(this).attr("see", "on");
                            $(this).text("关闭云购码");
                            $(this).parent().next().show();
                            } else{
                            $(this).text("显示云购码")
                                    $(this).attr("see", "off");
                            $(this).parent().next().hide();
                            }
                            });
                            $(".user_codes_js").click(function(){
                            var codes = $(this).prev();
                            if ($(this).attr("click") == 'off'){
                            var num = $(this).attr("num");
                            var line = Math.ceil(num / 7) * 20;
                            codes.css("max-height", line + "px");
                            $(this).attr("click", "on");
                            $(this).text("收起全部↑");
                            } else{
                            codes.css("max-height", "260px");
                            $(this).attr("click", "off");
                            $(this).text("展开查看全部↓");
                            }

                            });
                            });
                        </script>
                    </body>
                    </html>