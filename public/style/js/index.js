setTimeout(function(){
    
    // 最新揭晓
    $(".yConulout ul").width(($(".yConulout ul li").outerWidth())*$(".yConulout ul li").length);
    $(".yConuloutright").click(function(){
        var yConulout=$(this).siblings(".yConulout ul");
        if (!(yConulout.is(":animated"))) {
            if(yConulout.position().left<=-($(".yConulout ul li").outerWidth()-1)*($(".yConulout ul li").length-5)){
            	
            }else{
                yConulout.animate({left:yConulout.position().left-(($(".yConulout ul li").outerWidth()-1)*5)+"px"},600);
            }
        }
    })
	$(".yConuloutLeft").click(function(){
		var yConulout=$(this).siblings(".yConulout ul");
		if (!(yConulout.is(":animated"))) {
			if(yConulout.position().left>=0){
			
			}else{
			    yConulout.animate({left:yConulout.position().left+(($(".yConulout ul li").outerWidth()-1)*5)+"px"},600);
		    }
	    }
	})
	
	/*// 广告位图片 2
	var  ggwtp2=0;
	$($($(".aBJCon")[1]).find("a").eq(0)).css({display:"block"});
	var Tggwtp2=setInterval(function (){
		Tggwtp2++;
		if(Tggwtp2>$($(".aBJCon")[1]).find("a").length-1){
		Tggwtp2=0;
		}
		$($(".aBJCon")[1]).find("a").hide();
		$($(".aBJCon")[1]).find("a").hide();
		$($(".aBJCon")[1]).find("a").eq(Tggwtp2).show();
	},6000);*/
	
	// 最新动态
	var num1 = 0;
    $($(".yscroll_list_left li")[0]).clone(true).insertAfter($($(".yscroll_list_left li")[$(".yscroll_list_left li").length - 1]));

    function move() {
    	num1 = num1 - 44;
	    if (num1 >= -($(".yscroll_list_left li").length - 2) * 44) {
	    	$(".yscroll_list_left").animate({
	    		marginTop: num1
	    	}, 2000);
	    } else {
	    	$(".yscroll_list_left").animate({
	    		marginTop: num1
	    	}, 2000, function() {
	    		num1 = 0;
	    		$(".yscroll_list_left").css({
	    			marginTop: 0
	            });
	        });
	    }
	};
    var t = setInterval(move, 4000);
    $(".yscroll_list_left").hover(function() {
        clearInterval(t);
    }, function() {
        t = setInterval(move, 4000);
    })

    $(".yscroll_list_rightli1").click(function() {
    	if (!($(".yscroll_list_left").is(":animated"))) {
    		var mls = parseInt($(".yscroll_list_left").css("marginTop").slice(0,-2));
    		if (mls > -(($(".yscroll_list_left li").length - 2) * 44)) {
    			$(".yscroll_list_left").animate({marginTop: mls - 44 }, 2000);
    		}
    	}
    });
    $(".yscroll_list_rightli2").click(function() {
    	if (!($(".yscroll_list_left").is(":animated"))) {
        $(".yscroll_list_left").stop();
         	var mls = parseInt($(".yscroll_list_left").css("marginTop").slice(0,-2));
         	if (mls <=-44) {
         		$(".yscroll_list_left").animate({marginTop: mls + 44 }, 2000);
         	}
    	}
    });

    $(".yscroll_list_right li").hover(function() {
    	clearInterval(t);
    }, function() {
    	var mls = $(".yscroll_list_left").css("marginTop");
        num1 = mls;
        t = setInterval(move, 4000);
    })
    // 最新动态 结束
	
     $(".yConulout").hover(function(){
        $(this).find(".yConuloutbtn").show();
     },function(){
        $(this).find(".yConuloutbtn").hide();
     });
     // 内容区左右键盘事件
     // var pagnation=$("#pagnation");
     $(document).keyup(function(event){
		 
    	 switch(event.keyCode){
			 case 37 : 
	         var scrTops=$(window).scrollTop();
	         if(scrTops>=120&scrTops<=754){
	            $(".yConuloutLeft").click();
	         };
	         if(scrTops>=604&scrTops<=1087){
	            $($(".y_btn_left")[0]).click();
	         };
	         if(scrTops>=1070&scrTops<=1570){
	            $($(".y_btn_left")[1]).click();
	         };
	         if(scrTops>=1640&scrTops<=2128){
	            $($(".y_btn_left")[2]).click();
	         };
	         if(scrTops>=2093&scrTops<=2591){
	            $($(".y_btn_left")[3]).click();
	         };
	         if(scrTops>=2659&scrTops<=3153){
	            $($(".y_btn_left")[4]).click();
	         };
	          if(scrTops>=3127&scrTops<=3625){
	            $($(".y_btn_left")[5]).click();
	         };
	         if(scrTops>=3618&scrTops<=4086){
	            $($(".y_btn_left")[6]).click();
	         };
	         break;
			 case 39 :
	         var scrTops=$(window).scrollTop();
	        if(scrTops>=120&scrTops<=754){
	            $(".yConuloutright").click();
	        };
	        if(scrTops>=604&scrTops<=1087){
	            $($(".y_btn_right")[0]).click();
	        };
	        if(scrTops>=1070&scrTops<=1570){
	            $($(".y_btn_right")[1]).click();
	        };
	        if(scrTops>=1640&scrTops<=2128){
	            $($(".y_btn_right")[2]).click();
	        };
	        if(scrTops>=2093&scrTops<=2591){
	            $($(".y_btn_right")[3]).click();
	        };
	        if(scrTops>=2659&scrTops<=3153){
	            $($(".y_btn_right")[4]).click();
	        };
	        if(scrTops>=3127&scrTops<=3625){
	            $($(".y_btn_right")[5]).click();
	        };
	        if(scrTops>=3618&scrTops<=4086){
	            $($(".y_btn_right")[6]).click();
	        };
	          break;
		}
    });
    // 内容区轮转 结束
    
     
},500);
// 左侧悬浮
setTimeout(function(){
	var index = $("#index").html();
	if(index!=null){
		$(window).scroll(function(){
			
			// 2015-08-18 fanbin 首页滚动条在顶部时，不现实右侧栏
			if($(window).scrollTop()>0){
                $(".Left-fixed-divs").fadeIn(600);
            }else{
                $(".Left-fixed-divs").fadeOut(600);
            }
			
			
	        var tops=$(".yCon1").offset().top-100;
	        if($(window).scrollTop()>=tops){
	            $(".y-fixed-divs").show();
	            for(i=0;i<$(".yConCenter").length;i++){
	                var tops=$($(".yConCenter")[i]).offset().top;
	                if($(window).scrollTop()>tops&&$(window).scrollTop()<(tops+449)){
	                    $($(".y-fixed-divs li")[i]).addClass("clickemyy").siblings().removeClass("clickemyy")
	                }
	            }       
	        }else{
	            $(".y-fixed-divs").hide();
	        }
	    })
	}
    $(".y-fixed-divs li").click(function(){
        var index=$(this).index(".y-fixed-divs li");
        var topss=$($(".yConCenter")[index]).offset().top+10;
        $(document.documentElement).animate({
            scrollTop: topss
            },200);
        // 支持chrome
        $(document.body).animate({
            scrollTop: topss
        },200);
    })
    // 登录框
    $(".close-login").click(function(){
        $(".Left-fixed-login").hide(); 
    })
    

    if($("#yg_sq").val())
    	toYG();
    
    $(".w_empty_img .w_add_tiao").hover(function(){
        $(".w_empty_img .w_add_tiao").html("我要晒单");
    },function(){
    	$(".w_empty_img .w_add_tiao").html("你晒单，我送积分");
    });
    
},800);

var toYG = function(){
	var topss=$($(".yConCenter")[7]).offset().top+10;
    $(document.documentElement).animate({
        scrollTop: topss
        });
    // 支持chrome
    $(document.body).animate({
        scrollTop: topss
    });
}

var addHover = function(){
	$(".yCon h2 ul li").hover(function(){
     	var lefts=$(this).position().left;
     	var index=parseInt(lefts/115);
     	var that=$(this);
     	that.parents(".yCon")[0].index=index;
     	that.parent().find("a").removeClass("yhoversH1List");
     	that.find("a").addClass("yhoversH1List");
     	that.parent().find("span").css({left:lefts+15+"px"});
     	var Lists2=$(that.parents(".yConCenter").find(".yConCenterInList")[index])
     	var Listall=that.parents(".yConCenter").find(".yConCenterInList")
     	Listall.hide();
     	Lists2.show();
     });
	
	$(".yConCenterIn").hover(function(){
		alert(33);
     	$(this).find(".y_btn_all").show();
     },function(){
     	$(this).find(".y_btn_all").hide();
     });
	
	// 内容区轮转
	for(i=0;i<$(".yCon").length;i++){
		$(".yCon")[i].index=0;
	}
	// 内容区左按钮
    $(".y_btn_left").click(function(){
     	var thiss=$(this).parents(".yCon")[0].index-1;
     	var  asl=$(this).parent().parent().find("h2 ul a");
     	var  spansl=$(this).parent().parent().find("h2 ul span");
     	var Listss=$(this).parent().find(".yConCenterInList");
     	if(thiss<0){
     		thiss=asl.length-1;
     	}
     	$(this).parents(".yCon")[0].index=thiss;
     	asl.removeClass("yhoversH1List");
     	$(asl[thiss]).addClass("yhoversH1List");
     	spansl.css({left:thiss*115+15+"px"});
     	Listss.hide();
     	$(Listss[thiss]).show();
     })
     // 内容区右按钮
     $(".y_btn_right").click(function(){
		 alert(55);
     	var thiss=$(this).parents(".yCon")[0].index+1;
     	var  asl=$(this).parent().parent().find("h2 ul a");
     	var  spansl=$(this).parent().parent().find("h2 ul span");
     	var Listss=$(this).parent().find(".yConCenterInList");
        if(thiss>asl.length-1){
     		thiss=0;
     	}
     	$(this).parents(".yCon")[0].index=thiss;
     	asl.removeClass("yhoversH1List");
     	$(asl[thiss]).addClass("yhoversH1List");
     	spansl.css({left:thiss*115+15+"px"});
     	Listss.hide();
     	$(Listss[thiss]).show();
     })
}
var adHH = function(index){
	// 广告位图片 
	var  ggwtp=0;
	$($($(".aBJCon")[index]).find("a").eq(0)).css({display:"block"});
	
	var Tggwtp=setInterval(function moves1(){
		ggwtp++;
		if(ggwtp>$($(".aBJCon")[index]).find("a").length-1){
		ggwtp=0;
		}
		$($(".aBJCon")[index]).find("a").hide();
		$($(".aBJCon")[index]).find("a").hide();
		$($(".aBJCon")[index]).find("a").eq(ggwtp).show();
	},6000);
}
/*
$(function(){
	 // 2015 6 10 start
	var indexcookies = $.cookie("yungouworldindexcookies_k");
	if(indexcookies){
		$(".New-box-body").hide();   
		$(".New-box-bj").hide();
	}else{
		$.cookie("yungouworldindexcookies_k", "yungouworldindexcookies_v", { path: "/", expires: 30 * 12 }); //cookie设置有效期1年
	}
	$(".New-box-bodyone").css({display:"block"});
    $(window).resize(function(){
    	$(".New-box-bj").css({height:$(window).height()+"px"});  
    	$(".New-box-body").css({top:($(window).height()-433)/2+"px",left:($(window).width()-921)/2+"px"}); 
    })
    $(".New-box-body span").click(function(){
        var index=$(this).index(".New-box-body span");
        if(index==3){
         $(".New-box-body").hide();   
         $(".New-box-bj").hide();   
        }
        $(this).parent().hide();
        $(this).parent().next().show();
    })
    $(".new_img_alert").click(function(){
        $(".New-box-bj").show(); 
        $(".New-box-body").show(); 
        $(".New-box-bodyone").show();   
        
    })
    $(window).resize();
    // 2015 6 10 end
});*/
