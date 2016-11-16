var formatDate = function(date,format){if(!format)format="yyyy-MM-dd HH:mm:ss";date=new Date(parseInt(date));var dict={"yyyy":date.getFullYear(),"M":date.getMonth()+1,"d":date.getDate(),"H":date.getHours(),"m":date.getMinutes(),"s":date.getSeconds(),"S":(""+(date.getMilliseconds()+1000)).substr(1),"MM":(""+(date.getMonth()+101)).substr(1),"dd":(""+(date.getDate()+100)).substr(1),"HH":(""+(date.getHours()+100)).substr(1),"mm":(""+(date.getMinutes()+100)).substr(1),"ss":(""+(date.getSeconds()+100)).substr(1)};return format.replace(/(y+|M+|d+|H+|s+|m+|S)/g,function(a){return dict[a]})};
//将字符串分割成当个字符，并添加样式
var insertChar = function(str, start, end){
	var arr = str.split('');
	var s = [];
	$(arr).each(function(index, a){
		s.push(start+a+end);
	});
	return s.join('');
}
/**
 * 描述：图片延迟加载
 * 
 * */
function lazyload(size){
	//设置图片延迟加载  距离屏幕100像素开始加载图片
	$("img.lazy"+size).lazyload({
		effect : "fadeIn",
		placeholder : "/static/img/front/loading_"+size+".gif",
		threshold : 100
	});
}
setTimeout(function(){
	//购买次数输入框的事件
	$(".w_detailsinputs").live('keyup', function() {
		if(!$(".w_detailsinputs").val() || isNaN($(".w_detailsinputs").val())){
			$(".w_detailsinputs").val($(".w_detailsinputs").attr("min"));
		}else{
			var max = parseInt($(".w_detailsinputs").attr("max")),
			min = parseInt($(".w_detailsinputs").attr("min"));
			if (parseInt($(".w_detailsinputs").val())%min != 0) {
				$(".w_detailsinputs").val(parseInt(parseInt($(".w_detailsinputs").val())/min)*min);
			}
			if (parseInt($(".w_detailsinputs").val()) > max) {
				$(".w_detailsinputs").val(max);
			};
			if (parseInt($(".w_detailsinputs").val()) < min) {
				$(".w_detailsinputs").val(min);
			}
		}
		change_input();
	})
	//购买框左右的加减按钮
   	$(".w_pluss,.w_pluss_one").live('click', function(){
   		var max = parseInt($(".w_detailsinputs").attr("max")),
        min = parseInt($(".w_detailsinputs").attr("min"));
   		if (max - parseInt($(".w_detailsinputs").val()) >= min) {
			$(".w_detailsinputs").val(parseInt($(".w_detailsinputs").val())+min);
   		}
   		change_input();
	})
   	$(".w_subtracts,.w_subtracts_one").live('click', function(){
   		var min = parseInt($(".w_detailsinputs").attr("min"));
		if (parseInt($(".w_detailsinputs").val()) - min > 0) {
			$(".w_detailsinputs").val(parseInt($(".w_detailsinputs").val())-min);
		};
		change_input();
	})
 	//tab标签
	$(".w_calculate_results .w_calculate_nav dd").click(function(){
 		var index= $(this).index(".w_calculate_results .w_calculate_nav dd") 
 		
 		if (parseInt(index) == 1){ timeline(); }
		if (parseInt(index) == 2){ sun(); }
 		$(".w_calculate_results .w_calculate_nav dd").removeClass("w_results_arrow");
 		$(this).addClass("w_results_arrow");
 		$(".w_calculate_one").css("display","none");
		$($(".w_calculate_one")[index]).css("display","block");
  	})
  	
  	$(".w_prize .w_calculate_nav dd").click(function() {
		
		var index = $(this).index(".w_prize .w_calculate_nav dd");
		
		if (parseInt(index) == 1){ timeline(); }
		if (parseInt(index) == 2){ sun(); }
		if (index==1){
			//alert(1);
			$($(".w_calculate_one")[1]).css("display","none");
			$(".w_prize_con").css("display", "none");
			$(".w_calculate_two").css("display", "block");
		}else if (index==2)
		{
			$($(".w_calculate_one")[1]).css("display","block");
			$(".w_prize_con").css("display", "none");
			$(".w_calculate_two").css("display", "none");

		}else{
			$($(".w_calculate_one")[1]).css("display","none");
			$(".w_prize_con").css("display", "block");
			$(".w_calculate_two").css("display", "none");

		}
		$(".w_prize .w_calculate_nav dd").removeClass("w_results_arrow");
		$(this).addClass("w_results_arrow");
		//$(".w_prize_con").css("display", "block");
		//$($(".w_prize_con")[index]).css("display", "block");
	})
  	/*2015-7-7 修改start */
    $(".w_tips").hover(function(){
      $(".w_count .w_answer_xiu").css({display:"inline-block"});
    },function(){
       $(".w_count .w_answer_xiu").css({display:"none"});
    })
    /*2015-7-7 修改end */
	$(".w_details_second").live('mouseenter',function() {
		$(".w_calculate .w_two_con").show();
	})
	$(".w_details_second").live('mouseleave',function() {
		$(".w_calculate .w_two_con").hide();
	})
	$(".w_details_third").live('mouseenter',function() {
		$(".w_calculate .w_three_con").show();
	})
	$(".w_details_third").live('mouseleave', function() {
		$(".w_calculate .w_three_con").hide();
	})
	
	$(window).resize(function(){
		$("#pro-view-7").css({left:($(window).width()-$("#pro-view-7").width())/2,top:($(window).height()-$("#pro-view-7").height())/2});
		$("#pro-view-8").css({left:($(window).width()-$("#pro-view-8").width())/2,top:($(window).height()-$("#pro-view-8").height())/2});
		$(".c_msgbox_bj").height($("body").height());
	    $(".once_shop_con").css({left:($(window).width()-$(".once_shop_con").width())/2, top:($(window).height()-$("#once_shop_con").height())/2})
	})
	$(window).resize();
  	
  	// 2015-6-15 start 点击下拉出现
    $(".w_specific .w_special_li").click(function(){
    	$(".w_specific .w_lose_con").show();
    	$(".w_specific .w_special_li").hide();
    	$(".w_specific .w_special_li_other").show();
    })
    $(".w_specific .w_special_li_other").click(function(){
    	$(".w_specific .w_lose_con").hide();
    	$(".w_specific .w_special_li").show();
    	$(".w_specific .w_special_li_other").hide();
    })
  	
    // 揭晓详情页-所有期数 2015-6-17 start
     $(".w_all_nper a").click(function(){
     	var index=$(this).index(".w_all_nper a");
     	$(".w_all_nper a").removeClass("w_nper_color");
     	$(this).addClass("w_nper_color");
     })
     $(".m-detail-codesDetail-one  dd").click(function(){
     	var index=$(this).index(".m-detail-codesDetail-one  dd");
     	$(".m-detail-codesDetail-one  dd").removeClass("w_nper_color");
     	$(this).addClass("w_nper_color");
     })
     $(".w_all_nper span").click(function(){
		$("#pro-view-8").show();
		$(".c_msgbox_bj").show();
	})
     $(".w-msgbox-close").click(function(){
		$(".w-msgbox").hide();
		$(".c_msgbox_bj").hide();
	})
    // 揭晓详情页-所有期数 2015-6-17 end
	
	$(".w_small_img li").mousemove(function() {
		var index = $(this).index(".w_small_img li");
		$(".w_small_img li").removeClass("w_small_color");
		$(this).addClass("w_small_color");
		$(".w_big_img dd").css({
			display : "none"
		});
		$($(".w_big_img dd")[index]).css({
			display : "block"
		})
		$(".w_modified").css({
			left : ($(".w_small_img li").outerWidth() + 12) * index + 38
		});
	})

	//查看详情的按钮
	$('.w_period').delegate('li a', 'click', function(ev) {
		var ev = ev || window.event;
		var target = ev.target || ev.srcElement;
		$('.w_period li a').removeClass("w_the");
		$(target).addClass("w_the");
	    var pid = $(target).attr("data-pid");
	    info(pid);
	})

	//查看云购码关闭弹窗
	$(".w-msgbox-close").click(function(){
		$("#pro-view-7").hide();
		$(".c_msgbox_bj").hide();
	})
	
	//我要包圆 返回云购
	$(".w_rob .w_slip_out").hover(function(){
       $(".w_slip_in_con").show();
	},function(){
       $(".w_slip_in_con").hide();
	})
	
	//购买次数的快捷键
	$(".w_cumulative_another i").click(function(){
		var index=$(this).index(".w_cumulative_another i");
		var vals=$(this).html();
		if (vals > parseInt($(".w_detailsinputs").attr("max"))) {
			vals = parseInt($(".w_detailsinputs").attr("max"))
		}
		if (vals < parseInt($(".w_detailsinputs").attr("min"))) {
			vals = parseInt($(".w_detailsinputs").attr("min"))
		}
		$(".w_cumulative_another em").removeClass("w_num_color");
		$(".w_cumulative_another i").removeClass("w_num_color");
		$(this).addClass("w_num_color");
		$(".w_detailsinputs_one").val(vals);
		change_input();
	})
	$(".w_cumulative_another em").click(function(){
		var valone=$(".w_amount_val").html();
		if (valone > parseInt($(".w_detailsinputs").attr("max"))) {
			valone = parseInt($(".w_detailsinputs").attr("max"))
		}
		if (valone < parseInt($(".w_detailsinputs").attr("min"))) {
			valone = parseInt($(".w_detailsinputs").attr("min"))
		}
		$(".w_cumulative_another i").removeClass("w_num_color");
		$(".w_cumulative_another em").addClass("w_num_color");
		$(".w_detailsinputs_one").val(valone);
		change_input();
	})
	// 2015-6-15 start 商品详情上下轮转
	var num1 = 0;
	var outH=$(".w_record_out").height();
	var recordH=$(".w_record").outerHeight();
    var count=parseInt(outH/recordH); 
    var numbers=$(".w_record").length;
    if(numbers>5){ 
	    $(".w_record_in .w_record").clone(true).insertAfter($($(".w_record_in .w_record")[$(".w_record_in .w_record").length - 1]));
	     
	    function move() {
	        num1 = num1 - recordH;
	        if (num1 >= -($(".w_record_in .w_record").length - (count +1)) * recordH) {
	            $(".w_record_in").animate({
	                marginTop: num1
	            }, 2000);
	        } else {
	            $(".w_record_in").animate({
	                marginTop: num1
	            }, 2000, function() {
	                num1 = 0;
	                $(".w_record_in").css({
	                    marginTop: 0
	                });
	            });
	        }
	    };
	    var t = setInterval(move, 4000);
	    $(".w_record_in").hover(function() {
	        clearInterval(t);
	    }, function() {
	        t = setInterval(move, 4000);
	    })
    }
    //2015-6-15 轮转结束
    $(".c_pop_shop").hover(function(){
    	$(this).find(".c_pop_btn").show();
    },function(){
    	$(this).find(".c_pop_btn").hide();
    })
    
    // 2015 7 24  start
    // 概率
    var sttims=null,_times=true;
    function change_input(){
    	if(sttims!=null){
	    	clearTimeout(sttims);
	    	sttims=null;
    	}
    	var i = (parseFloat($(".w_detailsinputs").val())/parseFloat($("#cart_priceTotal").text())*100).toFixed(3);
    	$(".y-hide-span span").html("获得机率"+(i>=0.001?i:"<0.001")+"%<i></i>");
    	if(_times){
		    $(".y-hide-span").show(10,function(){
	    		sttims=setTimeout(function(){
			    	$(".y-hide-span").fadeOut("slow");
			    	sttims=null;
			    },3000);
		    });
		};
	};
	// 2015 7 24  end
	
	$(".w_detailsinputs_one").bind("input propertychange change", function (event) {
    	change_input();
    });
	
},1000);



//展示云购码的方法
function showCodes(mid){
	var win = $(".w_results").text();
	$.ajax({
		url:roots+'/index/getBuyCode',
		type:'post',
		dataType:"json",
		data:{
			mid:mid,
			gid:$("#gid").val(),
			pid:$("#pid").val()
		},
		success:function(result){
			
			if(result.status){
				
				$("#pro-view-7 .m-detail-codesDetail-wrap").html("");
				$('#pro-view-7 h3').html(mid?'奖品获得者':'您'+'本期总共参与了<span class="txt-red">'+result.size+'</span>人次');
				var str = [];
				var arr = [];
				$(result.codes).each(function(index, yg){
					str.push('<dl class="m-detail-codesDetail-list f-clear">');
					
					
					arr = yg.buyCodes.replace('[','').replace(']','').replace(/"/g,'').split(',');
					$(arr).each(function(i, code){
						if(code!=win)
							str.push('<dd>'+code+'</dd>');
						else
							str.push('<dd class="txt-red selected">'+code+'</dd>');
					});
					str.push('</dl>');
				});
				$("#pro-view-7 .m-detail-codesDetail-wrap").html(str.join(''));
				$("#pro-view-7").css({left:($(window).width()-$("#pro-view-7").width())/2,top:($(window).height()-$("#pro-view-7").height())/2});
				$("#pro-view-7").show();
				$(".c_msgbox_bj").height($("body").height());
				$(".c_msgbox_bj").show();
			}
		},
		error:function(){

			
		}
	});
}

//展示用户购买的云购码
function showMyCodes(){
	$.ajax({
		url:'/goods/queryusercodes.do',
		type:'post',
		dataType:"json",
		data:{
			gid:$("#gid").val(),
			pid:$("#pid").val()
		},
		success:function(result){
			if(result.status){
				//alert(result.codes);
				$("#pro-view-7 .m-detail-codesDetail-wrap").html("");
				$('#pro-view-7 h3').html('您本期总共参与了<span class="txt-red">'+result.size+'</span>人次');
				var arr = result.codes;//.replace('[','').replace(']','').replace(/"/g,'').split(',');
				var str = [];
				str.push('<dl class="m-detail-codesDetail-list f-clear">');
				//str.push('<dt>'+formatDate(result.time)+'</dt>');
				$(arr).each(function(index, a){
					str.push('<dd>'+a+'</dd>');
				});
				str.push('</dl>');
				$("#pro-view-7 .m-detail-codesDetail-wrap").html(str.join(''));
			}
		},
		error:function(){
			
		}
	});
	$("#pro-view-7").css({left:($(window).width()-$("#pro-view-7").width())/2,top:($(window).height()-$("#pro-view-7").height())/2});
	$("#pro-view-7").show();
	$(".c_msgbox_bj").height($("body").height());
	$(".c_msgbox_bj").show();
}

//获取面包屑
var getCB = function(){
	$.ajax({
		url:'/goods/getGoodsCB.do',
		type:'post',
		dataType:"json",
		data:{
			gid:$("#gid").val(),
		},
		success:function(result){
			if(result.status){
				if(result.goodsCBMap.fname === '奢侈品区'){
					$('.w_con').prepend('<a href="/footer/luxury.html"><img src="/static/img/front/goods/luxury.jpg"/></a>');
				}
				$(".w_guide").html('<a href="/">首页</a><a href="/goods/allCat.html">全部商品</a><a href="/goods/allCat'+result.goodsCBMap.fid+'.html">'+result.goodsCBMap.fname+'</a><a href="/goods/allCat'+result.goodsCBMap.cid+'.html">'+result.goodsCBMap.cname+'</a><a class="w_accord" href="javascript:void 0">商品详情</a>');//<a href="/goods/allCat.do?cid='+result.goodsCBMap.cid+'&bid='+result.goodsCBMap.bid+'">'+result.goodsCBMap.bname+'</a>
			}
		},
		error:function(){
			
		} 
	});
}





//弹出层揭晓期数按钮
var toP = function(){
	if($("#p").val()==''){
		alert("输入期数");
	}else{
		window.location.href='/goods/goods'+$("#gid").val()+'-'+$("#p").val()+'.html';
	}
}

var jumpDetail = function(gid, pid){
	window.location.href='/goods/goods'+gid+'-'+pid+'.html';
}


//立即购买
function gotoCart(n){
	var cart_need = $("#cart_need").html();
	var times = $(".times").val() || $(".w_detailsinputs").attr("min");
	if(times==cart_need&&typeof(n)=="undefined"){
		$("#cartMsg").show();
		$(".once_shop_con").show();
		$(".w_consider").click(function(){
			$("#cartMsg").hide();
			$(".once_shop_con").hide();
		});
	}else{
		addCart()
		window.location.href="/goods/cartlist";
	}
}

//图片更换动画
function picChange(){  
	var index = Math.floor(Math.random()*7+1);   
	var img = document.getElementById("pic");   //获取图片ID  
	switch(index){  
    	case 1: img.src="/static/img/front/goods/zi/one.png"; break;   //切换到1.jpg  
        case 2: img.src="/static/img/front/goods/zi/two.png"; break;   //切换到2.jpg  
        case 3: img.src="/static/img/front/goods/zi/three.png"; break;   //切换到3.jpg  
        case 4: img.src="/static/img/front/goods/zi/four.png"; break;   //切换到4.jpg 
        case 5: img.src="/static/img/front/goods/zi/five.png"; break;   //切换到4.jpg 
        case 6: img.src="/static/img/front/goods/zi/six.png"; break;   //切换到4.jpg 
        case 7: img.src="/static/img/front/goods/zi/seven.png"; break;   //切换到4.jpg 
         
	}  
       
}

function closeCodesPane(){
	$("#pro-view-7").hide();
	$(".c_msgbox_bj").hide();
}


var period = function(pid){
	pid = (pid!='')?pid:$("#pid").val();
	if(pid==1){
		$(".pageUp").addClass("w_page_in");
		$(".pageDown").addClass("w_page_in");
		return;
	}
	$(".w_period").html('');
	$(".w_period").append('<li><a class="w_the" data-pid="'+(pid-1)+'" href="javascript:void 0">第'+(pid-1)+'期</a></li>');
	for(var i=2; i<=8 && pid-i>0; i++){
		$(".w_period").append('<li><a data-pid="'+(pid-i)+'" href="javascript:void 0">第'+(pid-i)+'期</a></li>');
	}
	if($(".w_period>li:first>a").attr("data-pid")>=$("#pid").val()-1){
		$(".pageUp").addClass("w_page_in");
	}else{
		$(".pageUp").removeClass("w_page_in");
	}
	if($(".w_period>li:last>a").attr("data-pid")==1){
		$(".pageDown").addClass("w_page_in");
	}else{
		$(".pageDown").removeClass("w_page_in");
	}
}
var pageUp = function(){
	var pid = $(".w_period>li:first>a").attr("data-pid");
	if(pid>=$("#pid").val()-1 || isNaN(pid)){
		return;
	}
	period(parseInt(pid)+9);
	info(parseInt(pid)+8);
}
var pageDown = function(){
	var pid = $(".w_period>li:last>a").attr("data-pid");
	if(pid==1 || isNaN(pid)){
		return;
	}
	period(pid);
	info(pid);
}
var topGoods = function(){
	$.ajax({
		url:'/goods/getGoods.do',
		type:'post',
		dataType:"json",
		data:{
			order:"addTime",
			size:"4"
		},
		success:function(result){
			if(result.status){
				var topoutput=$('#shelves_top').parseTemplate(result.goods);  
				$('.w_shelves_top').append(topoutput);
				lazyload('200');
			}
		},
		error:function(){
			
		} 
	});
};

//获取当前商品有关的最近的五条云购记录
var winGoods = function(){
	$.ajax({
		url:'/goods/winGoods.do',
		type:'post',
		dataType:"json",
		data:{
			gid :$("#gid").val(),
			size:"20"
		},
		success:function(result){
			if(result.status){
				var bottomoutput=$('#shelves_bottom').parseTemplate(result);  
				$('.w_shelves_bottom').append(bottomoutput);
				lazyload('54');
			}
		},
		error:function(){
			
		} 
	});
}

//包圆
var buyAllShow = function(){
	$(".zhengchang").hide();
	$(".baoyuan").show();
}
//云购
var ygShow = function(){
	$(".baoyuan").hide();
	$(".zhengchang").show();
}

//加入购物车动画
function cartoon(){
	addCart();
	var img = $(".w_rob_out").parents(".w_details_top").find('.w_big_img').css({display:"block"});
	var flyElm = img.clone().css('opacity', 0.75);
	$('body').append(flyElm);
	flyElm.css({
		'z-index': 9000,
		'display': 'block',
		'position': 'absolute',
		'top': img.offset().top +'px',
		'left': img.offset().left +'px',
		'width': img.width() +'px',
		'height': img.height() +'px'
	});
	flyElm.animate({
		top: $('.shoppingCartRightFix').offset().top,
		left: $('.shoppingCartRightFix').offset().left,
		width: 40,
		height: 37
	}, 'slow', function() {
		flyElm.remove();
	});
} 


//加入购物车
function addCart(n){
	var gid = $("#gid").val();
	var pid = $("#pid").val();
	var cart_need = $("#cart_need").html();
	var times = $(".times").val();
	times = times!=undefined?times:1;
	var cart = jaaulde.utils.cookies.get("Cartlist")
	if (cart == null || cart=='' || cart == "undefined") {
		cart = '{"'+gid+'":{"num":1,"shenyu":99,"money":99}}';
	} else {
		var check = 0;
		var list = eval(cart);
		if(list.length>=30){
			return;
		}else{
			for (var i = 0; i < list.length; i++) {
				if (list[i].gid == gid && (list[i].type==2)) {
					list[i].times = parseInt(list[i].times+parseInt(times));
					check = 1;
					break;
				}
			}
			if (check == 0) {
				if(typeof(cart)=="object"){
					cart = JSON.stringify(cart);
				}
				cart = cart.substring(0, cart.length -1);
				cart = cart + ',{"buyPeriod":1,"client":1,"gid":'+gid+',"times":'+times+',"type":2}]';
			} else {
				cart = JSON.stringify(list)+"";
			}
		}
	}
	jaaulde.utils.cookies.set('Cartlist', cart,{path:"/"});
	loadCart();
	cartCount();
}
//我要包圆
function buyAll(){
	var gid = $("#gid").val();
	var pid = $("#pid").val();
	var times = $(".times").val();
	times = times!=undefined?times:1;
	var cart = jaaulde.utils.cookies.get("cart")
	if (cart == null || cart=='' || cart == "undefined") {
		cart = '[{"buyPeriod":1,"client":1,"gid":'+gid+',"times":1,"type":3}]';
	} else {
		var check = 0;
		var list = eval(cart);
		for (var i = 0; i < list.length; i++) {
			if (list[i].gid == gid && list[i].type==3) {
				list[i].times = list[i].times / 1 + 1;
				check = 1;
				break;
			}
		}
		if (check == 0) {
			if(typeof(cart)=="object"){
				cart = JSON.stringify(cart);
			}
			cart = cart.substring(0, cart.length -1);
			cart = cart + ',{"buyPeriod":1,"client":1,"gid":'+gid+',"times":1,"type":3}]';
		} else {
			cart = JSON.stringify(list)+"";
		}
	}
	jaaulde.utils.cookies.set('cart', cart,{path:"/"});
	loadCart();
	cartCount();
	window.location.href="/goods/cartlist";
}

/**
 * 跑秒动画产生效果函数
 * 参数说明：times - 要跑秒时长+new Date().getTime()
 * 			 objc  - 跑秒要显示的位置
 * 特别说明：① - 此句中的new Date().getTime()只是为形成跑秒动画效果而使用的，和跑秒的时间长短无关
 * 				  即使用户浏览器或电脑系统时间不同，但每次打开网页显示的时间跑秒动画是统一的
 */
var t = {}
function Time_fun(times,objc, fun){               
	t.time = times - (new Date().getTime());
	t.h = parseInt((t.time/1000)/60/60%24);//时
	t.i = parseInt((t.time/1000)/60%60);
	t.s =  parseInt((t.time/1000)%60);
	t.ms =  String(Math.floor(t.time%1000));
	t.ms = parseInt(t.ms.substr(0,2));
	if(t.h<10)t.h='0'+t.h; //剩余时
	if(t.i<10)t.i='0'+t.i; //剩余分钟
	if(t.s<10)t.s='0'+t.s; //剩余秒
	if(t.ms<0)t.ms='00'; //剩余毫秒
	t.oh=String(t.h).slice(0,1);
	t.th=String(t.h).slice(1);
	t.oi=String(t.i).slice(0,1);
	t.ti=String(t.i).slice(1);
	t.os=String(t.s).slice(0,1);
	t.ts=String(t.s).slice(1);
	t.oms=String(t.ms).slice(0,1);
	t.tms=String(t.ms).slice(1);
	if(t.h>0)
		objc.find("p").html("<b>"+t.oh+"</b><b>"+t.th+"</b><span>:</span><b>"+t.oi+"</b><b>"+t.ti+"</b><span>:</span><b>"+t.os+"</b><b>"+t.ts+"</b>");   
	else
		objc.find("p").html("<b>"+t.oi+"</b><b>"+t.ti+"</b><span>:</span><b>"+t.os+"</b><b>"+t.ts+"</b><span>:</span><b>"+t.oms+"</b><b>"+t.tms+"</b>");   
	if(t.time<=0){     
		objc.find("p").addClass("w_timeing");           
	    objc.find("p").html('正在计算，请稍后...');
	    setTimeout(function(){
	    	fun();
//	    	info($("#pid").val()-1);
//	    	getDetail();
//	    	window.location.reload();//揭晓成功的话刷新页面
	    },15000);                             
	    return;                     
	}
	setTimeout(function(){                                 
    	Time_fun(times,objc,fun);                 
	},30); 
}
//详情右边的倒计时方法
var timeFunDetails = function(times){
	var objc = $($(".w_time_backward .w_addBg")[0])
	Time_fun(times, objc, function(){info($("#pid").val()-1);});
}
//揭晓倒计时方法	    	
var timeFunDuring = function(times){
	var objc=$($(".w_during_wen .w_addBg")[0])
	Time_fun(times, objc, function(){ window.location.reload(); });
}
//获得人气商品
function getGoodsList(page){
	$.ajax({
		url:'/goods/getGoods.do',
		type:'post',
		dataType:"json",
		data:{
			size:"8",
			order:"periods",
			page:page
		},
		success:function(result){
			if(result.status){
				if(result.goods.dataList.length > 0){
					var goodsList = result.goods.dataList;
					for(i=0;i<goodsList.length;i++){
						var str ='';
						var surplus = goodsList[i].priceTotal-goodsList[i].priceSell;
						str +='<li><span class="span" ><img height="210px" width="204px" id="goodsImg_'+i+'"';
						str +='src="'+imageGoodsPath+goodsList[i].showImages.split(",")[0]+'" /></span>';
						str +='<b title="'+goodsList[i].title+'">'+cutString(goodsList[i].title,40)+'</b>';
						str +='<i>剩余<em >'+surplus+'</em>人次</i>';
						str +='<div class="c_pop_hover">';
						str +='<div class="c_pop_bj"></div>';
						str +='<div class="c_divide_btn">';
						str +='<a href="javascript:;" class="c_add_cart" onclick="cartoon2('+i+')">加入购物袋</a>';
						str +='<a href="javascript:gotoGoods('+goodsList[i].gid+','+goodsList[i].periodCurrent+')" class="c_know_detail">查看详情</a>';
						str +='</div>';
						str +='</div>';
						str +='<input type="hidden" id="soon_gid_'+i+'" value="'+goodsList[i].gid+'"/>';
						str +='<input type="hidden" id="soon_pid_'+i+'" value="'+goodsList[i].periodCurrent+'"/>';
						str +='<input type="hidden" id="soon_priceArea_'+i+'" value="'+goodsList[i].priceArea+'"/>';
						str +='<input type="hidden" id="soon_period_'+i+'" value="'+goodsList[i].period+'"/>';
						str +='<input type="hidden" id="soon_priceTotal_'+i+'" value="'+goodsList[i].priceTotal+'"/>';
						str +='<input type="hidden" id="soon_surplus_'+i+'" value="'+surplus+'"/>';
						str +='<input type="hidden" id="soon_thumbPath_'+i+'" value="'+goodsList[i].thumbPath+'"/>';
						str +='<input type="hidden" id="soon_title_'+i+'" value="'+goodsList[i].title+'"/>';
						str +='</li>';
						$("#goodsList").append(str);
					}
					$(".c_pop_list li").hover(function(){
						$(".c_pop_list li").find(".c_pop_hover").hide();
						$(this).find(".c_pop_hover").show();
					},function(){
						$(".c_pop_list li").find(".c_pop_hover").hide();
					})
				}
			}
		}
	});
}
//加入购物车动画
function cartoon2(a,gid){
		addCart2(gid);
		var img = $("#goodsImg_"+a);
		var flyElm = img.clone().css('opacity', 0.75);
		$('body').append(flyElm);
		flyElm.css({
			'z-index': 9000,
			'display': 'block',
			'position': 'absolute',
			'top': img.offset().top +'px',
			'left': img.offset().left +'px',
			'width': img.width() +'px',
			'height': img.height() +'px'
		});
		flyElm.animate({
			top: $('.shoppingCartRightFix').offset().top,
			left: $('.shoppingCartRightFix').offset().left,
			width: 40,
			height: 37
		}, 'slow', function() {
			flyElm.remove();
		});
} 
//人气商品加入购物车
function addCart2(gid){
	var times = 1;
	var cart = jaaulde.utils.cookies.get("Cartlist")
	if (cart == null || cart=='' || cart == "undefined") {
	cart = '{"'+gid+'":{"num":1,"shenyu":99,"money":99}}';
	} else {
		var check = 0;
		var list = eval(cart);
		
		if(list.length>=30){
			return;
		}else{
			for (var i = 0; i < list.length; i++) {
				if (list[i].gid == gid && (list[i].type==2)) {
					list[i].times = list[i].times / 1 + 1*$("#priceArea_"+gid).val();
					check = 1;
					break;
				}
			}
			
			if (check == 0) {
				if(typeof(cart)=="object"){
					cart = JSON.stringify(cart);
					
				}
				cart = cart.substring(0, cart.length -1);
				
				cart = cart + ',"' + gid
			+ '":{"num":1,"shenyu":99,"money":99},"MoenyCount":"0.00"}';


				
			} else {
				cart = JSON.stringify(list)+"";
			}
		}
	}
	jaaulde.utils.cookies.set('Cartlist', cart,{path:"/"});
	loadCart();
	cartCount();
}
