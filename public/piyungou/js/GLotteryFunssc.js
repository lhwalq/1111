				var t = {}
				function gg_show_Time_fun(times,objc,uhtml,data){	
					
					time = times - (new Date().getTime());
					i =  parseInt((time/1000)/60);
					
					s =  parseInt((time/1000)%60);
					ms =  String(Math.floor(time%1000));
					if(i<10)i='0'+i; //剩余时
					if(s<10)s='0'+s; //剩余分钟
					if(ms<10)ms='0'+ms; //剩余秒
					if(ms<0)ms='00';
					i1=String(i).slice(0,1);
					i2=String(i).slice(1);
					
i21=i2.substr(i2.length-2,1);   
i22=i2.substr(i2.length-1,1); 

					s1=String(s).slice(0,1);
					s2=String(s).slice(1);
					ms = parseInt(ms.substr(0,2));
					ms1=String(ms).slice(0,1);
					ms2=String(ms).slice(1);


					// objc.find(".specialFamily").html(i+'：'+s);	
					//objc.find(".specialFamily").html(i+'：'+s+'：'+ms);	

				
if(time>0){
	if(i2.toString().length==2){
		
					objc.find(".specialFamily").html("<b>"+i1+"</b><b>"+i21+"</b><b>"+i22+"</b><span>:</span><b>"+s1+"</b><b>"+s2+"</b><span>:</span><b>"+ms1+"</b><b>"+ms2+"</b>");
}else{
						objc.find(".specialFamily").html("<b>"+i1+"</b><b>"+i2+"</b><span>:</span><b>"+s1+"</b><b>"+s2+"</b><span>:</span><b>"+ms1+"</b><b>"+ms2+"</b>");

}
					//objc.find(".specialFamily").html("<b>"+i1+"</b><b>"+i2+"</b><span>:</span><b>"+s1+"</b><b>"+s2+"</b><span>:</span><b>"+ms1+"</b><b>"+ms2+"</b>");
}
		//alert(data['id']);
					
					if(time<=0){						
						var obj = objc.parent();	
						
						obj.find(".yTimes"+data['id']).html('<p>在计算，请稍后...</p>');	
				var info='';		
						 setTimeout(function(){
	
							//setTimeout("location.reload();",500);
							
	$.ajax({
				type:"get",
					cache: false,
				url:GG_SHOP_TIME.path+"/api/getshop/lottery_shop_jsonssc/"+new Date().getTime(),
				data:{'gid':data['id']},
					async: false, 
				success:function(indexData){
					 info = jQuery.parseJSON(indexData);	
					// tt=4;
					//alert(info.title);
					
				}
			});
	//alert(info.q_user_code== '');
			if(info.error == '0' && info.id != 'null' && info.q_user_code != 'null'&& info.q_user_code != ''){
			var uhtml = '';				
					uhtml+='<dl>';					
					uhtml+='<dd class="yddImg"><a href="'+data['path']+'/lotterys/'+info.id+'.html'+'" target="_blank" title="'+info.title+'"><img class="lazyjxn" src="'+info.upload+'/'+info.thumb+'" style="display: block;"><noscript><img src="'+info.upload+'/'+info.thumb+'" alt="" /> </noscript></a></dd>';
					uhtml+='<dd class="yddName">恭喜 <a href="'+data['path']+'/uname/'+(1000000000 + parseInt(info.uid))+'.html'+'" class="yddNameas">'+info.user+'</a> 获得</dd>';
					uhtml+='<dd class="yGray"><a href="'+data['path']+'/lotterys/'+info.id+'.html'+'" title="'+info.title+'" target="_blank" >(第'+info.qishu+'期)'+info.title+'</a></dd>';
					uhtml+='<dd class="yGray">本期幸运号码：'+info.q_user_code+'</dd>';
					uhtml+='</dl><i></i>';
			}else{
			var uhtml = '';				
					uhtml+='<dl>';					
					uhtml+='<dd class="yddImg"><a href="'+data['path']+'/lotterys/'+info.id+'.html'+'" target="_blank" title="'+info.title+'"><img class="lazyjxn" src="'+info.upload+'/'+info.thumb+'" style="display: block;"><noscript><img src="'+info.upload+'/'+info.thumb+'" alt="" /> </noscript></a></dd>';
					uhtml+='<dd class="yddName">通讯出错，开奖失败</dd>';
					uhtml+='<dd class="yGray"><a href="'+data['path']+'/lotterys/'+info.id+'.html'+'" title="'+info.title+'" target="_blank" >(第'+info.qishu+'期)'+info.title+'</a></dd>';
					uhtml+='<dd class="yGray">通讯出错，开奖失败</dd>';
					uhtml+='</dl><i></i>';
			
			}
			//var div_li_obj = $("#yTimesLi .yTimes").eq(0);
		//gg_show_Time_fun(info.times,div_li_obj,678,data,info.id);
							
							obj.html(uhtml);						
								obj.attr('class',"goods"+data['id']);
								$.ajaxSetup({
									async : false
							});				
							$.post(data['path']+"/api/getshop/lottery_shop_set/",{'lottery_sub':'true','gid':data['id'],'ttime':time},null);
						},5000);							 
						return;						
					}
					
					 setTimeout(function(){										 	
							gg_show_Time_fun(times,objc,888,data);				 
					 },30); 
				
				}
				function gg_show_time_add_li(div,path,info){
					
					var html = '';					
					html+='<li class="yTimesLi" id="yTimesLi"><dl class="yTimesDl">';					
					html+='<dl class="yTimesDl">';					
					html+='<dd class="yddImg"><a href="'+path+'/lotterys/'+info.id+'.html'+'" target="_blank"><img class="lazyjxn" src="'+info.upload+'/'+info.thumb+'" style="display: block;"><noscript><img src="'+info.upload+'/'+info.thumb+'" alt="" /> </noscript></a></dd>';
					html+='<dd class="yddName"><a href="'+path+'/lotterys/'+info.id+'.html'+'" title="'+info.title+'" target="_blank">(第'+info.qishu+'期)'+info.title+'</a></dd>';
					html+='<dd class="yGray">价值  <span>￥'+info.zongrenshu+'</span></dd>';
					html+='<dd class="yTimes yTimes'+info.id+'"><p><span class="specialFamily">120:00:00</span></p></dd>';
					html+='</dd></dl><strong></strong>';
					html+='</li>';
					

                  
var uhtml = '';				
					

					
						//alert(tt);
					var divl = $("#"+div).find('li');
					var len = divl.length;	
					if(len==4 && len  >0){
						var this_len = len - 1;
						divl.eq(this_len).remove();
					}		
					$("#"+div).prepend(html);	

					var div_li_obj = $("#yTimesLi .yTimes").eq(0);
					//var div_li_obj = $(".yTimesLi .yTimes"+info.id+).eq(0);
					var data = new Array();
						data['id'] = info.id;
						data['path'] = path;							
					info.times = (new Date().getTime())+(parseInt(info.times))*1000;					
					gg_show_Time_fun(info.times,div_li_obj,uhtml,data,info.id);				
				}
				
				function gg_show_time_init(div,path,gid){	
					window.setTimeout("gg_show_time_init()",5000);	
					if(!window.GG_SHOP_TIME){	
						window.GG_SHOP_TIME = {
							gid : '',
							path : path,
							div : div,
							arr : new Array()
						};
					}
					$.get(GG_SHOP_TIME.path+"/api/getshop/lottery_shop_json/"+new Date().getTime(),{'gid':GG_SHOP_TIME.gid},function(indexData){					
						var info = jQuery.parseJSON(indexData);	
						//alert(info.q_user_code);
							if(info.error == '0' && info.id != 'null'){						
								if(!GG_SHOP_TIME.arr[info.id]){
											GG_SHOP_TIME.gid =  GG_SHOP_TIME.gid +'_'+info.id;		
											GG_SHOP_TIME.arr[info.id] = true;	
											
											gg_show_time_add_li(GG_SHOP_TIME.div,GG_SHOP_TIME.path,info);							
								}			
							}			
					});							
				}