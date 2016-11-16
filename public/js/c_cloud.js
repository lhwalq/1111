// 2015-5-22   start  js代码新增
$(function(){
	// 2015-6-18 start js代码新增
    $(".w_newadd_list li").click(function(){
       var index=$(this).index(".w_newadd_list li");
       $(".w_newadd_list li").removeClass("w_lines_color");
       $(this).addClass("w_lines_color");
       $(".w_common_question_box .c_common_question").eq(index).show().siblings().hide();
    })
   // 2015-6-18 end js代码新增
	$(".y_common_question dl h4").click(function(){
		if($(this).next().css("display")=="none"){
			$(".y_common_question dl .y_hide_dd").slideUp("fast");
			$(".y_common_question dl h4 span").css({backgroundPosition:"0 0"});
			$(this).next().slideDown("fast");
			$(this).children("span").css({backgroundPosition:"0 -20px"});	
		}else{
			$(this).next().slideUp("fast");
			$(this).children("span").css({backgroundPosition:"0 0"});
		}			
	})
})
// 2015-5-22   end   js代码新增
$(function(){
	//我的购物袋 start
	$(".c_pop_list li").hover(function(){
		$(".c_pop_list li").find(".c_pop_hover").hide();
		$(this).find(".c_pop_hover").show();
	},function(){
		$(".c_pop_list li").find(".c_pop_hover").hide();
	})
	$(".c_pop_list li").click(function(){
		$("#cartTable tbody").append("<tr>"+$("#cartTable tbody tr").eq(0).html()+"</tr>");
		getTotal();
		select();
		addOnReduce();
	})
	//$(".c_pop_list").width(($(".c_pop_list li").outerWidth())*$(".c_pop_list li").length);
	$(".c_pop_right").click(function(){
		var c_pop_list=$(this).siblings(".c_pop_list");
		if (!(c_pop_list.is(":animated"))) {
			if(c_pop_list.position().left<=-($(".c_pop_list li").outerWidth())*($(".c_pop_list li").length-4)){
			}else{
				c_pop_list.animate({left:c_pop_list.position().left-($(".c_pop_list li").outerWidth())+"px"},600);
			}
		}
	})
	$(".c_pop_left").click(function(){
		var c_pop_list=$(this).siblings(".c_pop_list");
		if (!(c_pop_list.is(":animated"))) {
			if(c_pop_list.position().left>=0){
			}else{
				c_pop_list.animate({left:c_pop_list.position().left+($(".c_pop_list li").outerWidth())+"px"},600);
			}
		}
	})
	$(".c_pop_left").hover(function(){
        if($(".yfixed-divs-rf")){
	        $(this).show();
        }
    },function(){
        if($(".yfixed-divs-rt")){
            $(this).hide();
        }
    })
	//充值'了解云购体验卡'
	$(".c_exp_info").hover(function(){
		$(".c_exp_info i").show();
	},function(){
		$(".c_exp_info i").hide();
	})
	//支付方式类型切换
	$(".c_pay_way li").click(function(){
		var index=$(this).index(".c_pay_way li");
		$(this).addClass("c_pay_this").siblings().removeClass("c_pay_this");
		/*if(index!=3){
			$(".c_pay_bank").hide();
			$(".c_select_money").show();
		}else{
			$(".c_pay_bank").show();
			$(".c_select_money").hide();
		}*/
		switch (index){ 
			case 4 : 
 				$(".c_pay_bank").hide();
 				$(".tab_btn_cz").show();
 				$(".payment").hide();
			    $($(".payment")[0]).show();
			break; 

			case 5 :  
				$(".c_pay_bank").hide();
				$(".tab_btn_cz").show(); 
				$(".payment").hide();
				$($(".payment")[1]).show();
			break;

			case 6 :
				$(".tab_btn_cz").hide();
			    $(".c_pay_bank").show(); 
			break; 

			default :
				$(".tab_btn_cz").hide();
				$(".c_pay_bank").hide();
			break; 
		} 
		
	})
	$(".c_pay_bank dd li").append("<span></span>");
	$(".c_pay_bank dd li").click(function(){
		var index=$(this).index(".c_pay_bank dd li");
		$(this).addClass("c_pay_this").siblings().removeClass("c_pay_this");
		$(".c_pay_bank dd li").append("<span></span>");
	})
	
	
	/*充值 start*/
	$(".c_select_money li").eq(0).append("<span></span>");
	$(".c_select_money li").click(function(){
		var index=$(this).index(".c_select_money li");
		$(this).addClass("c_pay_this").siblings().removeClass("c_pay_this");
		if(index<5){
			$(".c_select_money li").eq(index).append("<span></span>");
		}
	})
	$(".c_input_num").focus(function(){
		var input_num=$(".c_input_num").val();
		if(input_num=="请输入其他金额"){
			$(".c_input_num").val("").css({color:"#333"});
		}
	})
	$(".c_input_num").blur(function(){
		var input_num=$(".c_input_num").val();
		if(input_num==""){
			$(".c_input_num").val("请输入其他金额").css({color:"#999"});
		}
	})
	/*充值 end*/
	
	
	/*交流群弹窗 start*/
	$(window).resize(function(){
		$("#Contract").css({left:($(window).width()-$("#Contract").width())/2+"px",top:($(window).height()-$("#Contract").height())/2+"px"});
		$(".c_qq_bj").height($("body").height());
		$(".c_qq_detail").css({left:($(window).width()-$(".c_qq_detail").outerWidth())/2+"px",top:($(window).height()-$(".c_qq_detail").outerHeight())/2+"px"});
		$(".c_update_tel").css({left:($(window).width()-$(".c_update_tel").outerWidth())/2+"px",top:($(window).height()-$(".c_update_tel").outerHeight())/2+"px"});
		$(".c_remind_bj").height($("body").height());
		$(".c_recharge_remind").css({left:($(window).width()-$(".c_recharge_remind").width())/2+"px",top:($(window).height()-$(".c_recharge_remind").height())/2+"px"})
	})
	$(window).resize();
	$(".c_group_name li").hover(function(){
		var index=$(this).index(".c_group_name li");
		$(".c_group_name em").hide();
		$(".c_group_name i").hide();
		$(".c_group_name em").eq(index).show();
		$(".c_group_name i").eq(index).show();
	},function(){
		$(".c_group_name em").hide();
		$(".c_group_name i").hide();
	})
	$(".c_group_name li").click(function(){
		var index=$(this).index(".c_group_name li");
		$(".c_qq_bj").show();
		$(".c_qq_detail").eq(index).show().siblings().hide();
	})
	$(".c_qq_close").click(function(){
		$(".c_qq_bj").hide();
		$(".c_qq_detail").hide();
	})
	/*交流群弹窗 end*/
	/*修改手机、邮箱、绑定邮箱 start*/
	$(".c_tel_update").click(function(){
		$(".c_tel_one").show();
		$(".c_qq_bj").show();
	})
	$(".c_qq_close").click(function(){
		$(".c_qq_bj").hide();
		$(".c_update_tel").hide();
	})
	/*修改手机、邮箱、绑定邮箱 end*/
	
	$(".c_remind_close").click(function(){
		$(".c_recharge_remind").hide();
		$(".c_remind_bj").hide();
	})
	
	/*云购记录 start*/
	$(".c_choose_day li").click(function(){
		$(this).addClass("c_choose_this").siblings().removeClass("c_choose_this");
	})
	/*云购记录 end*/
	/*奖励专区 start*/
	$(".c_reward_classify li").click(function(){
		var a = $(this).children()[0];
		if(a){
			window.location.href=a;
		}
	})
	/*奖励专区 end*/
	/*好友邀请 start*/
	bShareOpt = {
	   uuid: "", 
	   url: "http://www.ygqq.com", //商品的永久链接
	   summary: "{云购全球}", //商品描述
	   pic: "./../{$goods_info.goods_img}", //商品图片链接
	   vUid: "$_SESSION['user_id']|$_SESSION['user_name']", //用户id，为了让您能够知道您网站的注册用户分享、喜欢了哪些商品
	   product: "{$goods_info.goods_name}", //商品名称
	   price: "{$goods_info.promote_price}", //商品价格
	   brand: "{$brand_name}", //商品品牌
	   tag: "{$value.label}", //商品标签
	   category: "{$cat_data.cat_name}", //商品分类
	   template: "1" 
	};
	/*好友邀请 end*/
	/*2015.4.30 start*/
	var newda=new Date();
	newdaHours=newda.getHours();
	if(newdaHours>=7&&newdaHours<=10){
		$(".c_time_period").html("早上好");
		$(".c_person_info").css({background:"url(../../static/img/front/cloud_global/morning.jpg) no-repeat"});//2015.06.09 新增
	}else if(newdaHours>10&&newdaHours<=13){
		$(".c_time_period").html("中午好");
		$(".c_person_info").css({background:"url(../../static/img/front/cloud_global/noon.jpg) no-repeat"});//2015.06.09 新增
	}else if(newdaHours>13&&newdaHours<=18){
		$(".c_time_period").html("下午好");
		$(".c_person_info").css({background:"url(../../static/img/front/cloud_global/afternoon.jpg) no-repeat"});//2015.06.09 新增
	}else{
		$(".c_time_period").html("晚上好");
		$(".c_person_info").css({background:"url(../../static/img/front/cloud_global/evening.jpg) no-repeat"});//2015.06.09 新增
	}
	/*2015.4.30 end*/
	/*2015.5.5 start*/
	$(window).resize(function(){
		$(".c_pay_bj").height($("body").height());
		$(".c_pay_wait").css({left:($(window).width()-$(".c_pay_wait").width())/2+"px",top:($(window).height()-$(".c_pay_wait").height())/2+"px"});		
	})
	$(window).resize();
	/*2015.5.5 end*/
	//我的购物袋 end
	/*2015.06.23 安全设置 start*/
	$(".c_safe_real").click(function(){
		$(".c_realname_window").show();
		$(".c_qq_bj").show();
	})
	$(".c_safe_close").click(function(){
		$(".c_realname_window").hide();
		$(".c_qq_bj").hide();
	})
	$(".c_safe_updatepwd").click(function(){
		$(".c_updatepwd_window").show();
		$(".c_qq_bj").show();
	})
	$(".c_safe_pwdclose").click(function(){
		$(".c_nickname_window").hide();
		$(".c_qq_bj").hide();
	})
	/*2015.06.23 安全设置 end*/
	/*2015.06.23 关于我们专题 start*/
	//办公环境
	//2015 6 27 start
	// $(".c_office_box li").hover(function(){
	// 	$(this).animate({width:"400px"},200).siblings().animate({width:"275px"},200);
	// 	$(".c_office_opacity").fadeIn();
	// 	$(this).children(".c_office_opacity").fadeOut(100);
	// })
	//2015 6 27 end
	//联系我们
	$(".c_contact_hover").hover(function(){
		$(this).next().css({color:"#ff4a00"});
	},function(){
		$(this).next().css({color:"#999"});
	})
	//发展历程
	$(".c_history_content ul").css({width:$(window).width()*$(".c_history_content ul li").length});
	//右滚
	$(".c_history_right").click(function(){
		var c_history_content=$(".c_history_content ul");
		if (!(c_history_content.is(":animated"))) {
			c_history_content.stop();
			if(c_history_content.position().left<=-(c_history_content.find("li").width())*(c_history_content.find("li").length-4)){
				c_history_content.animate({left:0});
			}else{
				c_history_content.animate({left:c_history_content.position().left-(c_history_content.find("li").width())+"px"},600);
			}
		}
	})
	//左滚
	$(".c_history_left").click(function(){
		var c_history_content=$(".c_history_content ul");
		if (!(c_history_content.is(":animated"))) {
			c_history_content.stop();
			if(c_history_content.position().left>=0){
				c_history_content.animate({left:0});
			}else{
				c_history_content.animate({left:c_history_content.position().left+(c_history_content.find("li").width())+"px"},600);
			}
		}
	})
	// 2015 7 18 start
	function strimg(index){
		$(".c_certificate_content ul").html($(".c_office_boxsin ul").eq(index).html());
		$(".c_certificate_content ul li").eq(0).clone().appendTo(".c_certificate_content ul");
		var indexs=$(".c_office_boxsin ul").eq(index).find("li").length;
		for (var i = 0; i < indexs; i++) {
			var laststr =$($(".c_certificate_content ul li img")[i]).attr("src").slice(-7,-3);
			var firstr = $($(".c_certificate_content ul li img")[i]).attr("src").slice(0,-10);
			var centertr = $($(".c_certificate_content ul li img")[i]).attr("src").slice(-10,-7);
			$($(".c_certificate_content ul li img")[i]).attr("src",firstr+centertr+"big/"+laststr+"jpg");

		};
	}
	strimg(1);
	// 2015 7 12  end
	//查看证件弹窗 start
	//2015 6 27 start
	// 2015 7 18 start
	// $(".c_contact_bj").height($("body").height());
	// $(window).resize(function(){
	// 	$(".c_certificate_content").height($(window).height());
	// 	$(".c_certificate_content ul").css({width:$(window).width()*$(".c_certificate_content ul li").length});
	// 	$(".c_certificate_content ul li").width($(window).width());
	// })
	// $(window).resize();
	// 办公环境new
	for (var i = 0; i <= 2; i++) {
		$(".c_office_boxsin ul").eq(i).css({width:$(".c_office_boxsin ul").eq(i).find("li").length*360+"px"});
	};
	$(".c_office_boxsin .y_office_bl").click(function(){
		if (!($(this).parent().find("ul").is(":animated"))) {
			var lefts=$(this).parent().find("ul").position().left;
			if(lefts<0){
			console.log(lefts);
			$(this).parent().find("ul").animate({left:lefts+360},800);
			};
		};
	})
	$(".c_office_boxsin .y_office_br").click(function(){
		if (!($(this).parent().find("ul").is(":animated"))) {
			var lefts=$(this).parent().find("ul").position().left;
			if(lefts>-($(this).parent().find("ul").find("li").length-3)*360){
			console.log(lefts);
			$(this).parent().find("ul").animate({left:lefts-360},800);
			};
		};
	})
	
	$(".c_office_title h2").click(function(){
		var index=$(this).index(".c_office_title h2");
		$(this).addClass("c_contact_title").siblings().removeClass("c_contact_title");
		$(".c_office_boxsin").hide();
		$(".c_office_boxsin").eq(index).show();
	})
	// 2015 6 27 end
	//右滚
	// $(".c_cer_right").click(function(){
	// 	var c_certificate_content=$(this).siblings("ul");
	// 	if (!(c_certificate_content.is(":animated"))) {
	// 		c_certificate_content.stop();
	// 		if(c_certificate_content.position().left<=-(c_certificate_content.find("li").width())*(c_certificate_content.find("li").length-2)){
	// 			c_certificate_content.animate({left:0});
	// 		}else{
	// 			c_certificate_content.animate({left:c_certificate_content.position().left-(c_certificate_content.find("li").width())+"px"},600);
	// 		}
	// 	}
	// })
	//左滚
	// $(".c_cer_left").click(function(){
	// 	var c_certificate_content=$(this).siblings("ul");
	// 	if (!(c_certificate_content.is(":animated"))) {
	// 		c_certificate_content.stop();
	// 		if(c_certificate_content.position().left>=0){
	// 			c_certificate_content.animate({left:0});
	// 		}else{
	// 			c_certificate_content.animate({left:c_certificate_content.position().left+(c_certificate_content.find("li").width())+"px"},600);
	// 		}
	// 	}
	// })
	//显示弹窗
	// 2015 6 27 start
	// 2015 7 12 start
	// $(".c_office_boxsin ul li").click(function(){
	// 	var index=$(this).parents(".c_office_boxsin").index(".c_office_boxsin");
	// 	strimg(index);
	// 	var lilength=$($(".c_office_boxsin ul")[index]).find("li");
	// 	var indexli=$(lilength).index(this);
	// 	$(".c_contact_bj").height($("body").height());
	// 	$(window).resize();
	// 	$(".c_certificate_content ul").css({left:-indexli*$(window).width()});
	// 	$(".c_certificate_box").show();
	// 	$(".c_contact_bj").show();
	// })
	// 2015 7 12 end
	//隐藏弹窗
	$(".c_cer_close").click(function(){
		$(".c_certificate_box").hide();
		$(".c_contact_bj").hide();
	})
	//查看证件弹窗 end
	/*2015.06.23 关于我们专题 end*/
	/*2015.06.11 start 服务协议*/
	$(".c_pay_protocal i").click(function(){
		$("#Contract").show();
		$(".c_remind_bj").show();
	})
	$(".close").click(function(){
		$("#Contract").hide();
		$(".c_remind_bj").hide();
	})
	$(".btn-primary").click(function(){
		$("#Contract").hide();
		$(".c_remind_bj").hide();
	})
	$(".c_pay_protocal b").click(function(){
		if($(".c_pay_protocal input").attr("checked")==true){
			$(".c_pay_protocal input").removeAttr("checked");
		}else{
			$(".c_pay_protocal input").attr("checked","checked");
		}
	})
	
})
/**参数说明：
 * 根据长度截取先使用字符串，超长部分追加…
 * str 对象字符串
 * len 目标字节长度
 * 返回值： 处理结果字符串
 */
function cutString(str, len) {
 //length属性读出来的汉字长度为1
 if(str.length*2 <= len) {
  return str;
 }
 var strlen = 0;
 var s = "";
 for(var i = 0;i < str.length; i++) {
  s = s + str.charAt(i);
  if (str.charCodeAt(i) > 128) {
   strlen = strlen + 2;
   if(strlen >= len){
    return s.substring(0,s.length-1) + "...";
   }
  } else {
   strlen = strlen + 1;
   if(strlen >= len){
    return s.substring(0,s.length-2) + "...";
   }
  }
 }
 return s;
}
/*购物计算 start*/

