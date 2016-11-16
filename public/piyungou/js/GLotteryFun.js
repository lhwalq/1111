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
//alert(i1);
//alert(i2);
					s1=String(s).slice(0,1);
					s2=String(s).slice(1);
					ms = parseInt(ms.substr(0,2));
					ms1=String(ms).slice(0,1);
					ms2=String(ms).slice(1);


					// objc.find(".specialFamily").html(i+'：'+s);	
					//objc.find(".specialFamily").html(i+'：'+s+'：'+ms);	

					
if(time>0){

					objc.find(".specialFamily").html("<b>"+i1+"</b><b>"+i2+"</b><span>:</span><b>"+s1+"</b><b>"+s2+"</b><span>:</span><b>"+ms1+"</b><b>"+ms2+"</b>");
}
					if(time<=0){						
						var obj = objc.parent();	
						
						obj.find(".yTimes"+data['id']).html('<p>在计算，请稍后...</p>');	
						
						 setTimeout(function(){
							//setTimeout("location.reload();",500);
							obj.html(uhtml);						
								obj.attr('class',"goods"+data['id']);
								$.ajaxSetup({
									async : false
							});				
							$.post(roots + "/Goods/lottery_shop_set/",{'lottery_sub':'true','gid':data['id'],'ttime':time},null);
						},5000);							 
						return;						
					}
					
					 setTimeout(function(){										 	
							gg_show_Time_fun(times,objc,uhtml,data);				 
					 },30); 
				
				}
				function gg_show_time_add_li(div,path,info){
					var html = '';					
					html+='<li class="yTimesLi" id="yTimesLi"><dl class="yTimesDl">';					
					html+='<dl class="yTimesDl">';					
					html+='<dd class="yddImg"><a href="'+path+'/goods/dataserverForPc/goodsId/'+info.id+'.html'+'" target="_blank"><img class="lazyjxn" src="'+pub+'/uploads/'+info.thumb+'" style="display: block;"><noscript><img src="'+pub+'/uploads/'+info.thumb+'" alt="" /> </noscript></a></dd>';
					html+='<dd class="yddName"><a href="'+path+'/goods/dataserverForPc/goodsId/'+info.id+'.html'+'" title="'+info.title+'" target="_blank">(第'+info.qishu+'期)'+info.title+'</a></dd>';
					html+='<dd class="yGray">价值  <span>￥'+info.zongrenshu+'</span></dd>';
					html+='<dd class="yTimes yTimes'+info.id+'"><p><span class="specialFamily">120:00:00</span></p></dd>';
					html+='</dd></dl><strong></strong>';
					html+='</li>';
					var uhtml = '';				
					uhtml+='<dl>';					
					uhtml+='<dd class="yddImg"><a href="'+path+'/goods/dataserverForPc/goodsId/'+info.id+'.html'+'" target="_blank" title="'+info.title+'"><img class="lazyjxn" src="'+pub+'/uploads/'+info.thumb+'" style="display: block;"><noscript><img src="'+pub+'/uploads/'+info.thumb+'" alt="" /> </noscript></a></dd>';
					uhtml+='<dd class="yddName">恭喜 <a href="'+path+'/uname/'+(1000000000 + parseInt(info.uid))+'.html'+'" class="yddNameas">'+info.user+'</a> 获得</dd>';
					uhtml+='<dd class="yGray"><a href="'+path+'/goods/dataserverForPc/goodsId/'+info.id+'.html'+'" title="'+info.title+'" target="_blank" >(第'+info.qishu+'期)'+info.title+'</a></dd>';
					uhtml+='<dd class="yGray">本期幸运号码：'+info.q_user_code+'</dd>';
					uhtml+='</dl><i></i>';
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
					$.get(GG_SHOP_TIME.path+"/goods/lottery_shop_json/"+new Date().getTime(),{'gid':GG_SHOP_TIME.gid},function(indexData){					
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