<?php
header("Content-Type:text/html;charset=UTF-8");
//从统一支付接口获取到code_url
$code_url = $unifiedOrderResult["code_url"];
$saomiao = "微信安全支付，请扫我";
//		//参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'

$def_url = '<html><head><link rel="stylesheet" type="text/css" href="' . __ROOT__ . '/public/css/c_cloud.css"></head><body><div class="c_recharge">
		<div class="c_kik_pay">
			<p>支付金额<span class="c_kik_money">' . $this->config[money] . '</span>元，请使用微信扫描下方二维码完成支付</p>
			<p class="c_kik_img">
				<span class="c_kik_left" id="qrodeimg"><img src="' . C("URL_DOMAIN") . 'tools/erweima?code=' . $code_url . '" alt="" border="0"></span>
				<span class="c_kik_right"><img src="' . __ROOT__ . '/public/img/wphone.jpg" alt="" border="0"></span>
			</p>
			<div class="c_kik_info">
		
				<p id="dingdan" ddcode=' . $this->config[code] . '>订单：' . $this->config[code] . '</p>
				<p>创建时间：' . date("Y-m-d H:i:s", time()) . '</p>
			</div>
		</div>
	</div></body>

<script>
		var d=$("#dingdan").attr("ddcode");
			setInterval(function(){
			  $.ajax({
			    url:"' . C("URL_DOMAIN") . '/pay/hui",
				type: "post", 		 
				dataType: "json",  
				data: {out_trade_no:d},  				
				async : true,
				success: function(res){
						if(res.code==4){
						  window.location.href="/member/cart/paysuccess";
						}
				}			  
			  });			
			},5000);
		</script>	
	</html>';
echo $def_url;
exit;
//商户自行增加处理流程
//......
?>
