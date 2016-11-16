$.fn.StartTimeOut = function(b) {
    var a = $(this);
   // $.getJSON("/mobile/ajax", "GetCodeLotteryTimeOut", "codeid=" + b,
    $.getJSON(roots+"/goods/GetBarcodernoInfo", "codeid=" + b,
    function(w) {
        var i = w.code;
        var k = w.seconds;
		//alert(k);
        if (i == 0 || i == -3) {
            var c = new Date();
            c.setSeconds(c.getSeconds() + k);
            var q = 0;
            var t = 0;
            var s = 0;
            var p = function() {
                var y = new Date();
                if (c > y) {
                    var z = parseInt((c.getTime() - y.getTime()) / 1000);
                    var x = z % 60;
                    q = parseInt(z / 60);
                    t = parseInt(x);
                    if (x >= t) {
                        s = parseInt((x - t) * 10)
                    } else {
                        s = 0
                    }
                    setTimeout(p, 3000)
                }
            };
            var j = a.find("span");
            var d = j.eq(0);
            var n = j.eq(1);
            var o = $(j.eq(2)).find("i");
            var f = o.eq(0);
            var v = o.eq(1);
            var h = 9;
            var r = function() {
                h--;
                if (h < 0) {
                    h = 9
                }
                v.html(h);
                setTimeout(r, 10)
            };
            var e = function() {
                v.html("0");
                var x = '<div class="g-Countdown">';
                x += '<p class="orange">正在计算中，结果马上揭晓...</p>';
                x += '<div class="loading-progress"><span class="loading-pgbar"><span class="loading-pging"></span></span></div>';
                x += "</div>";
                a.html(x);
                var z = null;
                var y = function() {
                   $.getJSON(roots+"/goods/getCodeState", "codeID=" + b,
                    function(A) {
                        if (A.Code == 0 && A.State == 3) {
                            location.replace(roots+"/goods/dataserver/goodsId/" + b)
                        }
                    })
                };
                z = setInterval(y, 2000)
            };
            var m = function() {
                s--;
                if (s < 1) {
                    if (t < 1) {
                        if (q < 1) {
                            e();
                            return
                        } else {
                            q--
                        }
                        t = 59
                    } else {
                        t--
                    }
                    s = 9
                }
                setTimeout(m, 100)
            };
            var g = 0,
            u = 0;
            var l = function() {
                f.html(s);
                if (g != t) {
                    if (t < 10) {
                        n.html("0" + t)
                    } else {
                        n.html(t)
                    }
                    g = t
                }
                if (u != q) {
                    if (q < 10) {
                        d.html("0" + q)
                    } else {
                        d.html("00")
                    }
                    u = q
                }
                setTimeout(l, 100)
            };
            if (i == 0) {
                p();
                m();
                r();
                l()
            } else {
                e()
            }
        }
    })
};