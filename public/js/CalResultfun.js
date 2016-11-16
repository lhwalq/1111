$(function() {
    var c = function(m) {
        $.PageDialog.fail(m)
    };
    var d = function(m) {
        $.PageDialog.ok(m)
    };
    var l = $("#hidCodeID").val();
    var e = $("#calResult");
    var i = $("#hidRaffleTime").val();
    var f = $("#hidRaffType").val();
    var a = $("#divLoading");
    var k = $("#hidBaseNum").val();
    var b = $("#hidcodequantity").val();
    var h = $("#hidYuShu").val();
	
    var j = parseInt(h) + parseInt("10000001");
	//alert(j);
    if (i > f) {
        $.getJSON(roots+"/mobile/getCalResult/","codeid=" + l,
        function(q) {
			
            if (q.code == 0) {
                var o = '<div class="g-formula clearfix">';
                o += '<div class="for-con1 z-oval clearfix">';
                o += '<em class="orange">' + j + "</em>";
                o += '<i class="colorbbb">最终计算结果</i>';
                o += "</div>";
                o += "<p></p>";
                o += '<div class="for-con2 clearfix">';
                o += "<cite>(</cite>";
                o += '<span class="z-oval">';
                o += '<em class="orange">' + k + "</em>";
                o += '<i class="colorbbb">时间取值之和</i>';
                o += "</span>";
                o += "<cite>%</cite>";
                o += '<span class="z-oval">';
                o += '<em class="orange">' + b + "</em>";
                o += '<i class="colorbbb">商品总需人次</i>';
                o += "</span>";
                o += "<cite>)</cite>";
                o += "<cite>+</cite>";
                o += '<span class="z-oval">';
                o += '<em class="orange">10000001</em>';
                o += '<i class="colorbbb">固定数值</i>';
                o += "</span>";
                o += "</div>";
                o += '<div class="orange z-and">';
                o += '截止该商品最后购买时间</br>';
                o += "网站所有商品的最后100条购买时间取值之和";
                o += '<a id="a_showway" href="javascript:;" class="orange">如何计算<i class="z-set"></i></a>';
                o += "</div>";
                o += "</div>";
                o += '<div class="calCon clearfix">';
                o += '<dl class="dl1">';
                o += "<dt><span>云购时间</span><span></span><span>转换数据</span><span>会员</span></dt>";
                o += "</dl>";
                o += '<dl id="dl_nginner" class="dl2">';
                var m = [];
                var n = q.record1;
				
                for (var p = 0; p < n.length; p++) {
                    m = n[p].buytime.split(" ");
                    o += "<dd><span>" + m[0] + "<b></b></span><span>" + m[1] + "<s></s></span><span><i><em></em></i>" + n[p].timeCodeVal + "<s></s></span><span>" + n[p].buyName + "</span></dd>"
                }
                o += "</dl>";
                o += '<dl class="dl3">';
                o += '<dt id="dt_showmore">展开全部100条数据<cite><b></b></cite></dt>';
                o += "</dl>";
                o += "</div>";
                e.html(o);
                var r = $("#dl_nginner");
                $("#dt_showmore").bind("click",
                function() {
                    var s = $(this);
                    if (parseInt(r.height()) < 3500) {
                        r.animate({
                            height: "3500px"
                        },
                        800,
                        function() {
                            s.html('收起<cite class="up"><b></b></cite>')
                        })
                    } else {
                        r.height("350px");
                        s.html("展开全部100条数据<cite><b></b></cite>");
                        $("body,html").animate({
                            scrollTop: 0
                        },
                        0)
                    }
                });
                $("#a_showway").bind("click",
                function(u) {
                    stopBubble(u);
                    var s = function() {
                        var w = "";
                        w += '<div id="div_container" class="acc-pop clearfix z-box-width">';
                        w += '<a id="a_cancle" href="javascript:;" class="z-set box-close"></a>';
                        w += "<dl>";
                        w += '<dt class="gray6">如何计算？</dt>';
                        w += "<dd>1、取该商品最后购买时间前网站所有商品的最后100条购买时间记录；</dd>";
                        w += "<dd>2、按时、分、秒、毫秒排列取值之和，除以该商品总参与人次后取余数；</dd>";
                        w += "<dd>3、余数加上10000001 即为“幸运云购码”；</dd>";
                        w += "<dd>4、余数是指整数除法中被除数未被除尽部分， 如7÷3 = 2 ......1，1就是余数。</dd>";
                        w += "</dl>";
                        w += "</div>";
                        return w
                    };
                    var v = function() {
                        _DialogObj = $("#pageDialog");
                        $("#a_cancle", _DialogObj).click(function(w) {
                            t.cancel()
                        });
                        $("#div_container", _DialogObj).click(function(w) {
                            stopBubble(w)
                        });
                        $("#pageDialogBG").click(function() {
                            t.cancel()
                        })
                    };
                    var t = new $.PageDialog(s(), {
                        W: 290,
                        H: 257,
                        close: true,
                        autoClose: false,
                        ready: v
                    })
                })
            } else {
                e.html(Gobal.NoneHtml)
            }
            a.hide()
        })
    } else {
        _ResultHtml = '<div style="text-align: center;color: #999999; line-height: 30px">对不起，该商品的揭晓时间早于' + f + "<br>没有采用当前的揭晓机制揭晓！</div>";
        e.html(_ResultHtml)
    }
    var g = function() {
        var p = $("#hidgoodsid").val();
        if (p > 0) {
            var r = false;
            var q = $("#a_sc");
            var n = function() {
                if (r) {
                    return
                }
                r = true;
                var s = function(t) {
                    if (t.code == 0) {
                        d("已关注");
                        q.addClass("z-foot-fansed").unbind("click").bind("click",
                        function() {
                            o()
                        })
                    } else {
                        if (t.code == 1) {
                            if (t.num == -1) {
                                c("关注失败，商品不存在！");
                                location.reload()
                            } else {
                                if (t.num == -2) {
                                    q.addClass("z-foot-fansed").unbind("click").bind("click",
                                    function() {
                                        o()
                                    })
                                }
                            }
                        } else {
                            if (t.code == 10) {
                                location.href = "http://weixin.1yyg.com/Passport/login.html?forward=" + escape(location.href)
                            } else {
                                c("关注失败，请重试！");
                                location.reload()
                            }
                        }
                    }
                    r = false
                };
                GetJPData("http://api.1yyg.com", "addCollectGoods", "goodsid=" + p, s)
            };
            var o = function() {
                if (r) {
                    return
                }
                r = true;
                var s = function(t) {
                    if (t.code == 0) {
                        d("已取消关注");
                        q.removeClass("z-foot-fansed").unbind("click").bind("click",
                        function() {
                            n()
                        })
                    } else {
                        c("取消关注失败，请重试！");
                        location.reload()
                    }
                    r = false
                };
                GetJPData("http://api.1yyg.com", "delCollectGoods", "goodsid=" + p, s)
            };
            var m = function(s) {
                if (s.code == 0) {
                    q.addClass("z-foot-fansed").unbind("click").bind("click",
                    function() {
                        o()
                    })
                } else {
                    q.bind("click",
                    function() {
                        n()
                    })
                }
            };
           
        }
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104",
    function() {
        g()
    })
});