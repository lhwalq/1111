$(function() {
    var h = null;
    var f = 10;
    var a = 0;
    var j = {
        FIdx: 0,
        EIdx: f,
        isCount: 1
    };
    var d = $("#divLotteryLoading");
    var l = $("#btnLoadMore");
    var m = false;
    var c = function(o) {
        if (o && o.stopPropagation) {
            o.stopPropagation()
        } else {
            window.event.cancelBubble = true
        }
    };
    var b = function() {
        var o = function() {
            return "/" + j.FIdx + "/" + j.EIdx + "/" + j.isCount
        };
		 
        var p = function() {
            d.show();
            $.getJSON(roots + "/goods/getLotteryList"+o(),
            //GetJPData(Gobal.Webpath, "ajax", "getLotteryList"+o(),
            function(s) {			 
                if (s.code == 0) {
                    if (j.isCount == 1) {
                        a = s.count						
                    }
                    var r = s.listItems;
                    var t = r.length;
                    for (var q = 0; q < t; q++) {
                        var v = '<ul id="' + r[q].id + '"><li class="revConL">' + (r[q].codeType == 1 ? '<span class="z-limit-tips">限时揭晓</span>': "") + '<img src="' + Gobal.LoadPic + '" src2="'+Gobal.imgpath+'/uploads/' + r[q].thumb + '"></li><li class="revConR"><dl><dd><img name="uImg" uweb="' + r[q].q_uid + '" src="'+Gobal.imgpath+'/uploads/' + r[q].userphoto + '"></dd><dd><span>获得者<strong>：</strong><a name="uName" uweb="' + r[q].q_uid + '" class="rUserName blue">' + r[q].q_user + '</a></span>本期一元云购<strong>：</strong><em class="orange arial">' + r[q].gonumber + '</em>人次</dd></dl><dt>幸运一元云购码：<em class="orange arial">' + r[q].q_user_code + '</em><br/>揭晓时间：<em class="c9 arial">' + r[q].q_end_time + '</em></dt><b class="fr z-arrow"></b></li></ul>';
                        var u = $(v);
                        u.click(function() {
                            location.href = roots+"/goods/items/goodId/" + $(this).attr("id")
                        }).find('img[name="uImg"]').click(function(w) {
                            location.href = roots+"/mobile/userindex/id/" + $(this).attr("uweb");
                            c(w)
                        });
                        u.find('a[name="uName"]').click(function(w) {
                            location.href = roots+"/mobile/userindex/id/" + $(this).attr("uweb");
                            c(w)
                        });
                        d.before(u)
                    }
                    if (j.EIdx < a) {
                        m = false;
                        l.show()
                    }
                    loadImgFun()
                }
                d.hide()
            })
        };
        this.getInitPage = function() {
            p()
        };
        this.getNextPage = function() {
            j.FIdx += f;
            j.EIdx += f;
            p()
        }
    };
    l.click(function() {
        if (!m) {
            m = true;
            l.hide();
            h.getNextPage()
        }
    });
    h = new b();
    h.getInitPage();
    var e = ",";
    var n = false;
    var g = 0;
    var i = $("#divLottery");
	
    (function(){

		var div = $('#divLottery');

		var update = function(info){
			
			
		var html = '';
			html += '<ul class="rNow rFirst" id="'+info.id+'">';
			html += '	<li class="revConL">';
             html += '  <a href="'+roots+'gooods/items/goodId/'+info.id+'">';
			html += '		<img  src="' + pub + '/' + info.thumb + '"></li><a>';
			html += '		<li class="revConR"><h4>(第'+info.qishu+'期)'+info.title+'</h4><h5>价值：￥'+ info.zongrenshu +'</h5>';
			html += '		<p name="pTime"><s></s>揭晓倒计时';
			html += '			<strong><span class="minute">99</span>:<span class="second">99</span>:<span class="millisecond">99</span></strong>';
			html += '		<b class="fr z-arrow"></b></li>';
			html += '		<div class="rNowTitle">正在揭晓</div></ul>';
			html += '	</p>';
			html += '</ul>';





			if (div.find("div.m-lott-conduct").length >= 5) {
				div.find("div.m-lott-conduct:last").remove();
			}
			div.prepend(html);

			var mydiv = div.find('ul#'+info.id);
			var minute = mydiv.find('span.minute');
			var second = mydiv.find('span.second');
			var millisecond = mydiv.find('span.millisecond');
			var times = (new Date().getTime()) + info.times * 1000;
			var timer = setInterval(function(){
				var time = times - (new Date().getTime());
				if ( !info.times || time < 1 ) {
					clearInterval(timer);
					minute.parent().html('正在计算……');
					 setTimeout(function(){
							setTimeout("location.reload();",500);
							$.ajaxSetup({
								async : false
							});				
							$.post(roots+"/goods/lottery_shop_set/",{'lottery_sub':'true','gid':data['id']},null);
						},5000);

					setTimeout(checker,1000);
					return;
				}

				i =  parseInt((time/1000)/60);
				s =  parseInt((time/1000)%60);
				ms =  String(Math.floor(time%1000));
				ms = parseInt(ms.substr(0,2));
				if(i<10)i='0'+i;
				if(s<10)s='0'+s;
				if(ms<10)ms='0'+ms;
				minute.html(i);
				second.html(s);
				millisecond.html(ms);
			}, 41);
		};

		var gid = '';
		var thread = function(){
			$.getJSON(roots+"/goods/lottery_shop_json/"+new Date().getTime(),{'mobile':true,'test':true,'gid':gid},function(info){
				if(info.error == '0' && info.id != 'null'){
					if ( ('_'+gid+'_').indexOf('_'+info.id+'_') === -1 ) {
						gid =  gid + '_' + info.id;
						update(info);
					}
				}
			});
		};

		setInterval(thread, 4000);
		thread();
	})();
    Base.getScript(pub+"/js/mobile/comm.js", k)
});