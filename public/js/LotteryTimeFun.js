$.fn.StartTimeOut = function(u, h) {
    var t = $(this);
    var a = new Date();
    a.setSeconds(a.getSeconds() + h);
    var m = 0;
    var q = 0;
    var p = 0;
    var l = function() {
        var w = new Date();
		
        if (a > w) {
		
            var x = parseInt((a.getTime() - w.getTime()) / 1000);
            var v = x % 60;
            m = parseInt(x / 60);
            q = parseInt(v);
            if (v >= q) {
                p = parseInt((v - q) * 10)
            } else {
                p = 0
            }
            setTimeout(l, 3000)
        }
    };
    var g = t.find("p[name='pTime']").find("em");
	//alert(g);
    var b = g.eq(0);
	
    var k = g.eq(1);
    var n = g.eq(2).children("i");
	
    var d = $(n[0]);
    var s = $(n[1]);

    var f = 9;
    var o = function() {
        f--;
		
        if (f < 0) {
            f = 9
        }
        s.html(f);
        setTimeout(o, 10)
    };
    var c = function() {
        s.html("0");
        t.find("p[name='pTime']").html("正在计算，请稍候...");
        var w = null;
        var v = function() {
            $.getJSON(roots+"/goods/GetBarcodernoInfo", "codeid=" + u,
            function(y) {
				
                if (y.code == 0) {
                    var x = "";
                    x += "<dl>";
                    x += '<dt>获得者：<a href="/userpage/' + y.userweb + '" class="blue">' + y.username + "</a></dt>";
                    x += "<dd>商品价值：" + CastMoney(y.price) + "</dd>";
                    x += '<dd>本次参与：<em class="orange">' + y.buyCount + "</em>人次</dd>";
                    x += '<dd class="jx_time">揭晓时间：' + y.codeRTime + "</dd></dl>";
                    t.find("div.rNowTitle").remove();
                    t.removeClass("rNow").removeClass("rFirst").find("li.revConR").html(x);
                    if (w != null) {
                        clearInterval(w);
                        w = null
                    }
                }
            })
        };
        w = setInterval(v, 2000)
    };
    var j = function() {
        p--;
        if (p < 1) {
            if (q < 1) {
                if (m < 1) {
                    c();
                    return
                } else {
                    m--
                }
                q = 59
            } else {
                q--
            }
            p = 9
        }
        setTimeout(j, 100)
    };
    var e = 0,
    r = 0;
    var i = function() {
        d.html(p);
        if (e != q) {
            if (q < 10) {
                k.html("0" + q)
            } else {
                k.html(q)
            }
            e = q
        }
        if (r != m) {
            if (m < 10) {
                b.html("0" + m)
            } else {
                b.html("00")
            }
            r = m
        }
        setTimeout(i, 100)
    };
    l();
    j();
    o();
    i()
};