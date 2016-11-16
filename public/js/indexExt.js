var formatDate = function(date,format){if(!format)format="yyyy-MM-dd HH:mm:ss";date=new Date(parseInt(date));var dict={"yyyy":date.getFullYear(),"M":date.getMonth()+1,"d":date.getDate(),"H":date.getHours(),"m":date.getMinutes(),"s":date.getSeconds(),"S":date.getMilliseconds(),"MM":(""+(date.getMonth()+101)).substr(1),"dd":(""+(date.getDate()+100)).substr(1),"HH":(""+(date.getHours()+100)).substr(1),"mm":(""+(date.getMinutes()+100)).substr(1),"ss":(""+(date.getSeconds()+100)).substr(1)};return format.replace(/(y+|M+|d+|H+|s+|m+|S)/g,function(a){return dict[a]})};
/**
 * 描述：图片延迟加载
 * 
 * */
function lazyload200(id){
	//设置图片延迟加载  距离屏幕100像素开始加载图片
	$("img.lazy"+id).lazyload({
		effect : "fadeIn",
		placeholder : "/static/img/front/loading_200.gif",
		threshold : 100,
		skip_invisible : false
	});
	
}


//根据商品的一级栏目加载商品楼层
var gailou = function(){
	
	
	
	
}
//加载最新揭晓的商品信息
var jiexiao = function(){
	$.ajax({
		url:"/api/ajaxcount/buy_count",
		type:"post",
		dataType:"json",
		data:{
			
		},
		success:function(result){
			if(result.status){
				$(".yConulout ul").empty();
				var str;
				$(result.pageModel.dataList).each(function(index,goods){
					str = [];
					
					str.push('<li class="goods'+goods.gid+'_'+goods.period+'"><dl>');
					str.push('<dd class="yddImg"><a href="/goods/goods'+goods.gid+'-'+goods.period+'.html" target="_blank"><img class="lazyjx" data-original="'+imageGoodsPath+goods.showImages.split(',')[0]+'"><noscript><img src="'+imageGoodsPath+goods.showImages.split(',')[0]+'" alt=""></noscript></a></dd>');
					str.push('<dd class="yddName">恭喜 <a href="/other/cloudRecord/'+eval('('+goods.userInfo+')').mid+'.html" class="yddNameas">'+eval('('+goods.userInfo+')').nickname+'</a> 获得</dd>');
					str.push('<dd class="yGray"><a href="/goods/goods'+goods.gid+'-'+goods.period+'.html" target="_blank" title="'+goods.title+'">(第'+goods.period+'期)'+goods.title+'</a></dd>');
					str.push('<dd class="yGray">幸运号码：'+goods.userWinCode+'</dd>');
					str.push('</dl><i></i></li>');
					
					$(".yConulout ul").width($(".yConulout ul").width()+243);
					$(".yConulout ul").append(str.join(""));
					
					lazyload200('jx');
					
				});
				//如果最新揭晓少于5个用图片补空
				var len = result.pageModel.dataList.length;
				for(var i=len;i<5;i++){
					$(".yConulout ul").width($(".yConulout ul").width()+243);
					$(".yConulout ul").append('<li style="background:url(/static/img/front/index/jiexiao.jpg) no-repeat center 0;"></li>');
				}
				jiexiaoNew();
			}
		},
		error:function(){
			
		}
	});
	
}
//获取首页通知




//加载广告位

var addAdFun = function(index, objs){
	if(objs){
		var str = [];
		str.push('<div class="aBJCon">');
		$(objs).each(function(index, obj){
			str.push('<a href="'+obj.link+'" title="'+obj.description+'"><img src="'+imagePath+obj.img+'"></a>');
		});
		str.push('</div>');
		$(".yCon"+index).before(str.join(""));//添加广告
	}
}
//为所有的商品添加点击跳转事件
var addClick = function(){
	$(".yContent").delegate(".w_goods_three,.w_imgOut,.w_slip", "click", function(ev){
	    var ev = ev || window.event;
	    var target = ev.target || ev.srcElement;
	    var gid = $(target).attr("data-gid");
	    var pid = $(target).attr("data-pid");
	    window.open(roots+'/goods/products/'+gid+'.html');
	});
	$('.yConCenterIn .w_rob dd a.w_slip2').live('click',function(ev){
    	var ev = ev || window.event;
	    var target = ev.target || ev.srcElement;
	    cartoon($(target).attr("data-gid"), $(target).attr("data-pid"));
    });
}

/**
 * 跑秒动画产生效果函数
 * 参数说明：times - 要跑秒时长+new Date().getTime()
 * 			 objc  - 跑秒要显示的位置
 * 特别说明：① - 此句中的new Date().getTime()只是为形成跑秒动画效果而使用的，和跑秒的时间长短无关
 * 				  即使用户浏览器或电脑系统时间不同，但每次打开网页显示的时间跑秒动画是统一的
 */
var t = {}
function Time_fun(times,objc,gid,pid){               
	t.time = times - (new Date().getTime());//①
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
		objc.find("p").addClass("timeing");           
	    objc.find("p").html('正在计算，请稍后...');
	    setTimeout(function(){
	    	info(gid,pid);
	    },15000);                             
	    return;                     
	}
	setTimeout(function(){                                 
    	Time_fun(times,objc,gid,pid);                 
	},30); 
}


//商品揭晓信息



$(function(){
	
	
	
	gailou();
	jiexiao();
	
	addClick();
	//alert(new Date().getTime());
	
	// 加载抢购咨询列表
	
	
	
});
//列表页加购物车

function cartoong(pid, gid){
	addCart(pid, gid)
	var img = $("#img_"+gid);
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
		height: 26
	}, 'slow', function() {
		flyElm.remove();
	});
}
//加入购物车动画
function cartoon(gid, pid){
	addCart(gid, pid)
	var img = $("#img_"+gid);
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
		height: 26
	}, 'slow', function() {
		flyElm.remove();
	});
}

//购买页加入购物车动画
function cartoonmai(gid){

	addCart(gid);
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
function addCart(gid){
	
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





