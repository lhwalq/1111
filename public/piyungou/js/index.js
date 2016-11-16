$(function(){
	//广告位图片
	var  ggwtp1=0;
	var  ggwtp2=0;
	var  ggwtp3=0;
	var  ggwtp4=0;
	var  ggwtp5=0;
	var  ggwtp6=0;
	$($($(".aBJCon")[0]).find("a").eq(0)).css({display:"block"});
	$($($(".aBJCon")[1]).find("a").eq(0)).css({display:"block"});
	$($($(".aBJCon")[2]).find("a").eq(0)).css({display:"block"});
	$($($(".aBJCon")[3]).find("a").eq(0)).css({display:"block"});
	$($($(".aBJCon")[4]).find("a").eq(0)).css({display:"block"});
	$($($(".aBJCon")[5]).find("a").eq(0)).css({display:"block"});
		

	var Tggwtp1=setInterval( function  move  () {
	    ggwtp1++;	
		if (ggwtp1==$($(".aBJCon")[0]).find("a").length) {
	      ggwtp1=0;
		}
		$($(".aBJCon")[0]).find("a").fadeOut(600)
	        $($($(".aBJCon")[0]).find("a")[ggwtp1]).stop()
	   	$($($(".aBJCon")[0]).find("a")[ggwtp1]).fadeIn(600);
		},4000);

	var Tggwtp2=setInterval( function  move  () {
	    ggwtp2++;	
		if (ggwtp2==$($(".aBJCon")[1]).find("a").length) {
	      ggwtp2=0;
		}
		$($(".aBJCon")[1]).find("a").fadeOut(600)
	        $($($(".aBJCon")[1]).find("a")[ggwtp2]).stop()
	   	$($($(".aBJCon")[1]).find("a")[ggwtp2]).fadeIn(600);
		},4000);

	var Tggwtp3=setInterval( function  move  () {
	    ggwtp3++;	
		if (ggwtp3==$($(".aBJCon")[2]).find("a").length) {
	      ggwtp3=0;
		}
		$($(".aBJCon")[2]).find("a").fadeOut(600)
	        $($($(".aBJCon")[2]).find("a")[ggwtp3]).stop()
	   	$($($(".aBJCon")[2]).find("a")[ggwtp3]).fadeIn(600);
		},4000);


	var Tggwtp4=setInterval( function  move  () {
	    ggwtp4++;	
		if (ggwtp4==$($(".aBJCon")[3]).find("a").length) {
	      ggwtp4=0;
		}
		$($(".aBJCon")[3]).find("a").fadeOut(600)
	        $($($(".aBJCon")[3]).find("a")[ggwtp4]).stop()
	   	$($($(".aBJCon")[3]).find("a")[ggwtp4]).fadeIn(600);
		},4000);

	var Tggwtp5=setInterval( function  move  () {
	    ggwtp5++;	
		if (ggwtp5==$($(".aBJCon")[4]).find("a").length) {
	      ggwtp5=0;
		}
		$($(".aBJCon")[4]).find("a").fadeOut(600)
	        $($($(".aBJCon")[4]).find("a")[ggwtp5]).stop()
	   	$($($(".aBJCon")[4]).find("a")[ggwtp5]).fadeIn(600);
		},4000);	
	var Tggwtp6=setInterval( function  move  () {
	    ggwtp6++;	
		if (ggwtp6==$($(".aBJCon")[5]).find("a").length) {
	      ggwtp6=0;
		}
		$($(".aBJCon")[5]).find("a").fadeOut(600)
	        $($($(".aBJCon")[5]).find("a")[ggwtp6]).stop()
	   	$($($(".aBJCon")[5]).find("a")[ggwtp6]).fadeIn(600);
		},4000);	
	// 顶部搜索框
		$(".yheadSearch input").focus(function(){
			if($(this).val()=="请输入要搜索的商品"){
				$(this).val("");
			}
		})
		$(".yheadSearch input").blur(function(){
			if($(this).val()==""){
				$(this).val("请输入要搜索的商品");
			}
		})
	// 顶部购物车
		/*$(".yShoppingCart").hover(function(){
			$(this).find(".yShoppingCart1").addClass("yShoppingCart1hover");
			$(this).find(".yShoppingCart2").show();
		},function(){
			$(this).find(".yShoppingCart1").removeClass("yShoppingCart1hover");
			$(this).find(".yShoppingCart2").hide();
		})*/
		$(".yCon3 .yCon2Li").hover(function(){
			$(this).find(".yCon3Shopping").show();
		},function(){
			$(this).find(".yCon3Shopping").hide();
		});
		/*// 数字转动
		var attr=0;
		 attr=$(".yJoinNum input").val();

		var attr1=[];
		var nums=0;
		for(i=0;i<attr.length;i++){
			var nums=attr.slice(i,i+1);
			attr1.push(nums);
		}
		$(".yNumList ul").css("marginTop","-270px");
	 	var list=0;
	 	for(i=0;i<attr1.length;i++){
	 		list=attr[i];
			$($(".yNumList ul")[i]).animate({marginTop:(list*30-270)},2000)
		}
		setInterval(function(){
			$(".yJoinNum input").val(parseInt($(".yJoinNum input").val())+111);
			attr1=[];
			attr=$(".yJoinNum input").val();
			for(i=0;i<attr.length;i++){
				var nums=attr.slice(i,i+1);
				attr1.push(nums);
			}
			if($(".yNumList").length<attr1.length){
				var more=attr1.length-$(".yNumList").length;
				for(i=0;i<more;i++){
					$($(".yNumList")[0]).clone(true).insertAfter($($(".yNumList")[$(".yNumList").length-1]))
				}
			}
			$(".yNumList ul").css("marginTop","-270px");
			for(i=0;i<attr1.length;i++){
		 		list=attr[i];
				$($(".yNumList ul")[i]).animate({marginTop:(list*30-270)},2000)
			}
		},5000)*/
		// 导航左侧栏js效果 start
		$(".pullDownList li").hover(function(){
			$(".yMenuListCon").fadeIn();
			var index=$(this).index(".pullDownList li");
			if (!($(this).hasClass("menulihover")||$(this).hasClass("menuliselected"))) {
				$($(".yBannerList")[index]).css("display","block").siblings().css("display","none");
				$($(".yBannerList")[index]).removeClass("ybannerExposure");
				setTimeout(function(){
				$($(".yBannerList")[index]).addClass("ybannerExposure");
				},60)
			}else{	
			}
			$(this).addClass("menulihover").siblings().removeClass("menulihover");
				$(this).addClass("menuliselected").siblings().removeClass("menuliselected");
			$($(".yMenuListConin")[index]).fadeIn().siblings().fadeOut();
		},function(){
			
		})
		$(".pullDown").mouseleave(function(){
			$(".yMenuListCon").fadeOut();
			$(".yMenuListConin").fadeOut();
			$(".pullDownList li").removeClass("menulihover");

		})
		// 导航左侧栏js效果  end
	})
$(function(){
	//alert($(".yCon2").length);
	$(window).scroll(function(){
		var tops=$(".yCon1").offset().top+300;
		if($(window).scrollTop()>=tops){
			$(".y-fixed-divs").show();
			for(i=0;i<$(".yCon2").length;i++){
				var tops=$($(".yCon2")[i]).offset().top;
				if($(window).scrollTop()>tops&&$(window).scrollTop()<(tops+487)){
					$($(".y-fixed-divs li")[i]).addClass("clickemyy").siblings().removeClass("clickemyy")
				}
			}		
		}else{
			$(".y-fixed-divs").hide();
		}
	})
	$(window).resize(function(){
		$(".y-fixed-divs").css({left:($(window).width()-1200)/2-40+"px"});
	})
	$(window).resize();
	$(".y-fixed-divs li").click(function(){
		var index=$(this).index(".y-fixed-divs li");
		var topss=$($(".yCon2")[index]).offset().top;
		$(document.documentElement).animate({
			scrollTop: topss
			},200);
		//支持chrome
		$(document.body).animate({
			scrollTop: topss
			},200);

	})
})