setTimeout(function(){
	$('.w_yun_con').delegate(".w_hover_add", 'mouseenter', function(ev) {
		var ev = ev || window.event;
	    var target = ev.target || ev.srcElement;
	   	$(target).find("span").show();
	})
	$('.w_yun_con').delegate(".w_hover_add", 'mouseleave', function(ev) {
		$(".w_yun_con td span").hide();
	})
	$('.w_yun_con').delegate(".w_hover_add em", 'mouseenter', function(ev) {
		var ev = ev || window.event;
	    var target = ev.target || ev.srcElement;
	    var index=$(target).index(".w_hover_add em");
	   	$($(".w_add_explain")[index]).show();
	})
	$('.w_yun_con').delegate(".w_hover_add em", 'mouseleave', function(ev) {
		$(".w_add_explain").hide();
	})
	//给查看云购码的按钮添加点击事件
	$('.w_yun_con').delegate('.w_yun_con td span', 'click', function(ev) {
		var ev = ev || window.event;
	    var target = ev.target || ev.srcElement;
	    $("#pro-view-9 .m-detail-codesDetail-list").html("");
	    $('#pro-view-9 h3').html('<a style="color: #ff4a00;" href="'+$(target).parent().parent().find("a").attr("href")+'">'+$(target).parent().parent().find(".name").text()+'</a>本次总共参与了<span class="txt-red">'+$(target).prev().text()+'</span>人次');
	    var codes = $(target).next(".code").text().split(",");
	    var str = [];
		str.push('<dt>云购时间：'+$(target).parent().parent().find("td:eq(0)").text()+'</dt>');//时间
		var win = $('.w_results').text();
		$(codes).each(function(index, code){
			if(code == win)
				str.push('<dd class="txt-red selected">'+code+'</dd>');//云购码
			else
				str.push('<dd>'+code+'</dd>');//云购码
		});
		$("#pro-view-9 .m-detail-codesDetail-list").html(str.join(''));
		$("#pro-view-9").css({left:($(window).width()-$("#pro-view-9").width())/2,top:($(window).height()-$("#pro-view-9").height())/2});
	    $("#pro-view-9").show();
		$(".c_msgbox_bj").height($("body").height());
		$(".c_msgbox_bj").show();
	})
	$('.w_timeline').delegate('.w_view_left', 'click', function(ev) {
		var ev = ev || window.event;
	    var target = ev.target || ev.srcElement;
		$(target).parent().next(".w_list").css({
			display : "none"
		});
		$(target).css({
			display : "none"
		});
	})
	
	//查看云购码关闭弹窗
	$(".w-msgbox-close").click(function(){
		$(".w-msgbox").hide();
		$(".c_msgbox_bj").hide();
	})
	
},600);

var timeline = function(page){
	$.ajax({
		url:'/goods/timeline.do',
		type:'post',
		dataType:"json",
		data:{
			gid:$("#gid").val(),
			pid:$("#pid").val(),
			size:"25",
			page:page
		},
		success:function(result){
			if(result.status){
				//分页插件
				if(result.pagemodel && result.pagemodel.dataList && $("#kkpager2")){
					var timelineoutput=$('#timeline').parseTemplate(result.pagemodel);
					$('.w_yun_con').html(timelineoutput);
					lazyload('20');
					kkpager2.generPageHtml({
						pno : result.pagemodel.page,//当前页码
						total : result.pagemodel.totalPage,//总页码
						totalRecords : result.pagemodel.total,//总数据条数
						isToTop : true,
						pagerParent : "pgp",
						mode:'click',
						click:function(n){
							timeline(n);
							//this.selectPage(n); //处理完后可以手动条用selectPage进行页码选中切换
							//return false;
						}
					});
				}else{
					$("#kkpager2").html("<div style='margin:80px auto;'><img src='/static/img/no-ru.png'/></div>");
				}
			}
			/* var spans = $(".w_timeline_header");
			for(var i=1,len=spans.length; i<len; i++){
				if($(spans[i]).children("span").html()===$(spans[i-1]).children("span").html())
					$(spans[i]).hide();
			} */
		},
		error:function(){
			
		} 
	});
}