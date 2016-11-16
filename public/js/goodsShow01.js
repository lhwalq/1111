var sun = function(page){
	$.ajax({
		url:'/goods/shows.do',
		type:'post',
		dataType:"json",
		data:{
			gid:$("#gid").val(),
			//pid:$("#pid").val(),
			size:"15",
			page:page
		},
		success:function(result){
			if(result.status){
				//分页插件
				if(result.pagemodel && result.pagemodel.total && $("#kkpager1")){
					var sunoutput=$('#sun').parseTemplate(result.pagemodel);  
					$('.sun').html(sunoutput);
					lazyload('200');
					lazyload('54');
					kkpager1.generPageHtml({
						pno : result.pagemodel.page,//当前页码
						total : result.pagemodel.totalPage,//总页码
						totalRecords : result.pagemodel.total,//总数据条数
						pagerid : "kkpager1",
						isToTop : true,
						pagerParent : "pgp",
						mode:'click',
						click:function(n){
							sun(n);
							//this.selectPage(n); //处理完后可以手动条用selectPage进行页码选中切换
							//return false;
						}
					});
				}else{
					$("#kkpager1").html("<div style='margin:80px auto;text-align: center;'><img src='/static/img/no-single.png'/></div>");
				}
			}
		},
		error:function(){
			
		} 
	});
}