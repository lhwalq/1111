<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>
        <link rel="stylesheet" href="/Public/plugin/style/global/css/global.css" type="text/css"/>
        <link rel="stylesheet" href="/Public/plugin/style/global/css/style.css" type="text/css"/>
        <script src="/Public/plugin/style/global/js/jquery-1.8.3.min.js"></script>
        <script src="/Public/plugin/uploadify/api-uploadify.js" type="text/javascript"></script> 
        <style>
            table th{ border-bottom:1px solid #eee; font-size:12px; font-weight:100; text-align:right; width:200px;}
            table td{ padding-left:10px;}
            input.button{ display:inline-block}
        </style>
    </head>
    <body>
        <div class="header lr10">
            <?php echo R('admin/headerment',array($ment));?>
        </div>
        <div class="bk10"></div>
        <div class="table_form lr10">
            <!--start-->
            <form name="myform" action="" method="post" enctype="multipart/form-data">
                <table width="100%" cellspacing="0">
                    <tr>
                        <td width="120" align="right">资料昵称完善奖励：</td>
                        <td>
                            <input type="text" name="f_overziliao" value="<?php echo C('f_overziliao');?>" class="input-text"/>(福分)&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" name="z_overziliao" value="<?php echo C('z_overziliao');?>" class="input-text"/>(经验)
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">商品购买奖励：</td>
                        <td>
                            <input type="text" name="f_shoppay" value="<?php echo C('f_shoppay');?>" class="input-text"/>(福分)&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" name="z_shoppay" value="<?php echo C('z_shoppay');?>" class="input-text"/>(经验)
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">手机验证完善奖励：</td>
                        <td>
                            <input type="text" name="f_phonecode" value="<?php echo C('f_phonecode');?>" class="input-text"/>(福分)&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" name="z_phonecode" value="<?php echo C('z_phonecode');?>" class="input-text"/>(经验)
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">晒单奖励：</td>
                        <td>
                            <input type="text" name="shaim" value="<?php echo C('shaim');?>" class="input-text"/>(福分)&nbsp;&nbsp;&nbsp;&nbsp;

                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">邀请好友奖励：</td>
                        <td>
                            <input type="text" name="f_visituser" value="<?php echo C('f_visituser');?>" class="input-text"/>(福分)&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" name="z_visituser" value="<?php echo C('z_visituser');?>" class="input-text"/>(经验)
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">一元抵扣：</td>
                        <td>
                            <input type="text" name="fufen_yuan" value="<?php echo C('fufen_yuan');?>" class="input-text"/>(福分/元)&nbsp;&nbsp;&nbsp;&nbsp;备注：福分请输入10的整数
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">(百分比)冲值送：</td>
                        <td>
                            <input type="text" name="fufen_yuansong" value="<?php echo C('fufen_yuansong');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：按照冲值的百分比比如1.2代表冲100元送20元.设置为1以上,1代表原价
                        </td>
                    </tr>


                    <tr>
                        <td width="120" align="right">1级佣金返回：</td>
                        <td>
                            <input type="text" name="fufen_yongjin" maxlength="4" value="<?php echo C('fufen_yongjin');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：被邀请好友每消费一元所产生的佣金返回给邀请者！
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">2级佣金返回：</td>
                        <td>
                            <input type="text" name="fufen_yongjin2" maxlength="4" value="<?php echo C('fufen_yongjin2');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：被邀请好友每消费一元所产生的佣金返回给邀请者！
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">3级佣金返回：</td>
                        <td>
                            <input type="text" name="fufen_yongjin3" maxlength="4" value="<?php echo C('fufen_yongjin3');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：被邀请好友每消费一元所产生的佣金返回给邀请者！
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">签到送福分：</td>
                        <td>
                            <input type="text" name="fufen_yongjinqd" maxlength="4" value="<?php echo C('fufen_yongjinqd');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：签到赠送的福分！
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">实物兑换比例：</td>
                        <td>
                            <input type="text" name="fufen_yongjinqd0" maxlength="4" value="<?php echo C('fufen_yongjinqd0');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：比如0.96,代表96折！
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">QQ币兑换比例：</td>
                        <td>
                            <input type="text" name="fufen_yongjinqd1" maxlength="4" value="<?php echo C('fufen_yongjinqd1');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：比如0.96,代表96折！
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">卡密兑换比例：</td>
                        <td>
                            <input type="text" name="fufen_yongjinqd2" maxlength="4" value="<?php echo C('fufen_yongjinqd2');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：比如0.96,代表96折！
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">(百分比)全额购买送：</td>
                        <td>
                            <input type="text" name="fufen_yuansongzg" value="<?php echo C('fufen_yuansongzg');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：按照冲值的百分比比如0.2代表购买100元商品送20元.设置为1以下,1代表原价
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">佣金提现手续费：</td>
                        <td>
                            <input type="text" name="fufen_yongjintx" onkeyup="value = value.replace(/\D/g, '')"  value="<?php echo C('fufen_yongjintx');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;备注：提现一百元佣金所产生的手续费
                        </td>
                    </tr>


                    <tr>
                        <td width="120" align="right">微信登陆设置：</td>
                        <td>
                            <input type="text" name="appid"   value="<?php echo C('appid');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;微信公共平台appid信息
                            <input type="text" name="secret"   value="<?php echo C('secret');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;微信公共平台secret信息
                        </td>
                    </tr>

                    <tr>
                        <td width="120" align="right">限购人次：</td>
                        <td>
                            <input type="text" name="xiangou"   value="<?php echo C('xiangou');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;限购人次

                        </td>
                    </tr>


                    <tr>
                        <td width="120" align="right">微信通知设置：</td>
                        <td>
                            <input type="text" name="dengluid"   value="<?php echo C('dengluid');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;微信登陆模板ID号
                            <input type="text" name="tongzhiid"   value="<?php echo C('tongzhiid');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;微信通知模板ID号
                            <input type="text" name="yaoqingid"   value="<?php echo C('yaoqingid');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;邀请通知模板ID号
							<input type="text" name="goumaiid"   value="<?php echo C('goumaiid');?>" 
							class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;购买成功后模板ID号
			</td>除了购买成功模板ID为keyword1，keyword2，keyword3，keyword4的格式，其它只有keyword1，keyword2
                        
                    </tr>

                    <tr>
                        <td width="120" align="right">电脑微信登陆设置：</td>
                        <td>
                            <input type="text" name="appid1"   value="<?php echo C('appid1');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;微信开发者平台网站应用appid
                            <input type="text" name="secret1"   value="<?php echo C('secret1');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;微信开发者平台网站应用secret
                            <input type="text" name="fanhui"   value="<?php echo C('fanhui');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;微信开发者平台网站应用返回地址
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">大转盘设置：</td>
                        <td>
                            <input type="text" name="zhuanpank"   value="<?php echo C('zhuanpank');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;抽一次大转盘扣除福分<br/>
                            <input type="text" name="zhuanpan7"   value="<?php echo C('zhuanpan7');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;7等奖奖励余额设置<input type="text" name="zhuanpan7b"   value="<?php echo C('zhuanpan7b');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;7等奖概率设置<br/>
                            <input type="text" name="zhuanpan6"   value="<?php echo C('zhuanpan6');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;6等奖奖励余额设置<input type="text" name="zhuanpan6b"   value="<?php echo C('zhuanpan6b');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;6等奖概率设置<br/>
                            <input type="text" name="zhuanpan5"   value="<?php echo C('zhuanpan5');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;5等奖奖励余额设置<input type="text" name="zhuanpan5b"   value="<?php echo C('zhuanpan5b');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;5等奖概率设置<br/>
                            <input type="text" name="zhuanpan4"   value="<?php echo C('zhuanpan4');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;4等奖奖励余额设置<input type="text" name="zhuanpan4b"   value="<?php echo C('zhuanpan4b');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;4等奖概率设置<br/>
                            <input type="text" name="zhuanpan3"   value="<?php echo C('zhuanpan3');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;3等奖奖励余额设置<input type="text" name="zhuanpan3b"   value="<?php echo C('zhuanpan3b');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;3等奖概率设置<br/>
                            <input type="text" name="zhuanpan2"   value="<?php echo C('zhuanpan2');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;2等奖奖励余额设置<input type="text" name="zhuanpan2b"   value="<?php echo C('zhuanpan2b');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;2等奖概率设置<br/>
                            <input type="text" name="zhuanpan1"   value="<?php echo C('zhuanpan1');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;1等奖奖励余额设置<input type="text" name="zhuanpan1b"   value="<?php echo C('zhuanpan1b');?>" class="input-text"/>&nbsp;&nbsp;&nbsp;&nbsp;1等奖概率设置

                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right"></td>
                        <td>
                            <input type="submit" class="button" name="submit" value="提交" >
                        </td>
                    </tr>
                </table>
            </form>
        </div><!--table-list end-->
        <script>
            function upImage() {
                return document.getElementById('imgfield').click();
            }
        </script>
    </body>
</html>