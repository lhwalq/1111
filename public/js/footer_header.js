var formatDate = function(date,format){if(!format)format="yyyy-MM-dd HH:mm:ss";date=new Date(parseInt(date));var dict={"yyyy":date.getFullYear(),"M":date.getMonth()+1,"d":date.getDate(),"H":date.getHours(),"m":date.getMinutes(),"s":date.getSeconds(),"S":(""+(date.getMilliseconds()+1000)).substr(1),"MM":(""+(date.getMonth()+101)).substr(1),"dd":(""+(date.getDate()+100)).substr(1),"HH":(""+(date.getHours()+100)).substr(1),"mm":(""+(date.getMinutes()+100)).substr(1),"ss":(""+(date.getSeconds()+100)).substr(1)};return format.replace(/(y+|M+|d+|H+|s+|m+|S)/g,function(a){return dict[a]})};
setTimeout(function  () {
	
    // shoppingCartNoneCon  无数据时的js效果
    $(".shoppingCartNone").hover(function(){
        $(".shoppingCartNoneCon").show();
    },function(){
        $(".shoppingCartNoneCon").hide();
    })
    // 我的云购全球
    $(".header1 ul li.MyzhLi").hover(function(){
        $(".header1 ul li.MyzhLi .Myzh").show();
        $(".MyzhLi a i").removeClass("top");
        $(".MyzhLi a i").addClass("bottom");
    },function(){
        $(".header1 ul li.MyzhLi .Myzh").hide();
        $(".MyzhLi a i").removeClass("bottom");
        $(".MyzhLi a i").addClass("top");
    })
    // 搜索
    $(".search_header2 input").focus(function(){
        $(this).css({color:"#333"});
//        var vals=$(this).val();
//        if(vals=="搜索您需要的商品"){
//            $(this).val("");
//        }
        //$(".search_span_a").hide();
    })
//    $(".search_span_a a").live('click', function(){
//        var htmls=$(this).html();
//        $(".search_header2 input").val("");
//        $(".search_header2 input").css({color:"#333"});
//        $(".search_header2 input").val(htmls);
//    })
    $(".search_header2 input").blur(function(){
//        var vals=$(this).val();
//        if(vals==""){
//            $(this).val("搜索您需要的商品");
//            $(".search_span_a").show();
            $(this).css({color:"#a9a9a9"});
//        }
    })
    
    $('.search_header2 input').bind('keypress',function(event){
        if(event.keyCode == "13"){
        	location.href='/s_tag/'+$("#q").val();
        }
    });
    
    // 2015-5-22 -修改 start 悬浮菜单
    if($(".yNavIndexOut_fixed").offset()){
    	
    	var s_top=$(".yNavIndexOut_fixed").offset().top;
    	$(window).scroll(function(){
    		if($(window).scrollTop()>s_top){
    			$(".header_fixed").css({marginBottom:"46px"});
    			$(".yNavIndexOut_fixed").addClass("yNavIndexOutfixed");
    		}else{
    			$(".yNavIndexOut_fixed").removeClass("yNavIndexOutfixed");
    			$(".header_fixed").css({marginBottom:"0px"});
    		}
    	})
    }
    // 2015-5-22 -修改 end
    
    $($(".yBannerList")[0]).addClass("ybannerExposure");
    
    $(window).resize(function(){
	  $(".Left-fixed-divs").css({height:$(window).height()+"px"});// 右侧悬浮框的位置
	  $(".Left-fixed-divs2").css({height:$(window).height()+"px"});// 购物袋悬浮框的位置
	  $(".y-fixed-divs").css({left:($(window).width()-1210)/2-40+"px"});// 左侧left
	  $(".Left-fixed-login").css({top:($(window).height()-425)+"px"});// 登陆框的位置
	  $(".yNocommodity").css({lineHeight:$(window).height()+"px"});
    })
    $(window).resize();
    $(".Left-fixed-divs .lifixTop").click(function(e){     // 置顶
        e.preventDefault();
        $(document.documentElement).animate({
            scrollTop: 0
        },300);
        // 支持chrome
        $(document.body).animate({
            scrollTop: 0
        },300);
    });
    
    // 导航左侧栏js效果 start
	$('.pullDownList li').mouseover(function() {
//		var ev = ev || window.event;
//	    var target = ev.target || ev.srcElement;
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
		if(index == 8){
			$(".yMenuListCon").css("display","none");
			$(".yMenuListConin").css("display","none");
			return;
		}
		$(".yMenuListCon").fadeIn();
		$(".yMenuListConin").css("display","none");
		$($(".yMenuListConin")[index]).css("display","block");
	})
	$(".pullDown").mouseleave(function(){
		$(".yMenuListCon").css("display","none");
		$(".yMenuListConin").css("display","none");
		$(".pullDownList li").removeClass("menulihover");
	})
		setInterval(function(){
		showSun();
	},8000);
	// 导航左侧栏js效果 end
	
}, 800);

// 数字转动
var showSun = function(){
	
	var attr=0;
	 attr=$(".yJoinNum input").val();
	//alert(attr);
	var attr1=[];
	var nums=0;
	$('.yNumList').remove();
	
	for(i=0;i<attr.length;i++){
		var nums=attr.slice(i,i+1);
		attr1.push(nums);
		$('.w_ci_bg').before('<span class="yNumList"><ul style="margin-top: -270px;">'+
				'<li t="9">9</li><li t="8">8</li><li t="7">7</li><li t="6">6</li><li t="5">5</li>'+
		'<li t="4">4</li><li t="3">3</li><li t="2">2</li><li t="1">1</li><li t="0">0</li></ul></span>');
	}
	$(".yNumList ul").css("marginTop","-270px");
	var list=0;
	for(i=0;i<attr1.length;i++){
		list=attr[i];
		$($(".yNumList ul")[i]).animate({marginTop:(list*30-270)},1000)
	}
	if($(".yNumList").length<attr1.length){
			var more=attr1.length-$(".yNumList").length;
			for(i=0;i<more;i++){
				$($(".yNumList")[0]).clone(true).insertAfter($($(".yNumList")[$(".yNumList").length-1]))
			}
		}
	
}

//时间显示
var showLeftTime = function(time){
	$(".sysTime").text(formatDate(time + new Date().getTime(),"HH:mm:ss"));
	//一秒刷新一次显示时间
	setTimeout(function(){showLeftTime(time);},100);
}



var menuIndex = function(){
	$.ajax({
		url:"/menuIndexHead.do",
		type:"post",
		dataType:"json",
		data:{
			
		},
		success:function(result){
			if(result.status){
				var lis = "";
				$(result.menus).each(function(index,menu){
					if(menu.title == '全球免税店'){
						lis += '<li><a href="..'+menu.url+'" ><img src="/static/img/front/index/gif_qq.gif" style="position:absolute;top:12px;left:8px;width:20px;"><img src="/static/img/front/index/qq_gif2.gif" style="position:absolute;top:10px;left:33px;width:96px;"></a></li>';
					}else{
						
						lis += '<li><a href="..'+menu.url+'" ';
						/* if(index == 0)
							lis += 'class="yMenua"'; */
						lis += '>'+menu.title+'</a></li>';
					}
				});
				$(".yMenuIndex").html(lis);
			}
		},
		error:function(){
			
		}
	});
}

//加载banner


//签到
function sign(){
	var signTime = $('#signTime').val();
	var memberMid = $('#mid').val();
	if(memberMid!=''){
		var date = new Date(signTime*1000);
	    signTime = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
		date = new Date();
	    var today = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
	    if(today!=signTime){
			$.ajax({
				type:'get',
				url:"/member/sign.do",
				dataType:'json',
				success:function(result){
					if(result.status){
						$("#sign").html("已签到");
						$("#memberSign").html("已连续签到"+result.days+"天");
						location.reload(true);
					}
				}
			});
	    }
	}else{
		window.location.href ="/api/uc/login.do";
	}
}
//检查是否签到
function checkSign(){
	var signTime = $('#signTime').val();
	var date = new Date(signTime*1000);
    signTime = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
	date = new Date();
    var today = date.getFullYear().toString()+(date.getMonth() + 1)+date.getDate().toString()+"";
    if(today==signTime || signTime>today){
    	$("#sign").html("已签到")
    	$("#memberSign").html('已连续签到'+$("#signDays").val()+'天');
    }
   
}

//替换undefined
function replaceUndefined(data){
	if(typeof(data) == "undefined"){
		return '';
	}else{
		return data;
	}
}
//跳转商品页面
function gotoGoods(gid,pid){
	window.location.href = "/products/"+gid+".html";
}






