$(function() {
    var e = null;
    var a = $("#hidCodeID").val();
    var c = $("#divLoading");
    var b = $("#ul_list");
    var d = function() {
        var l = function(m) {
            $.PageDialog.fail(m)
        };
        var g = parseInt($("#hidBuyNum").val());
        var j = parseInt($("#hidCodeRno").val());
		 var j1 = parseInt($("#hidCodeRno1").val());
        var i = 1000;
        var h = [];
        var f = 0;
        $("#div_title").html('幸运得主本云总共参与<em class="orange">' + g + "</em>人次").show();
        var k = function() {
            var m = function(n) {
                b.html(n);
                _IsLoading = false
            };
            this.getNextPage = function() {
                if (h.length > 0) {
                    if (f == (h.length - 1)) {
                        _IsLoading = true;
                        c.hide();
                        return
                    }
                    f++;
                    var n = b.html();
                    var p = h[f];
                    var o = p.substring(0, 3);
                    if (o == "<li") {
                        n += h[f]
                    } else {
                        n = n.substring(0, n.length - 5) + h[f]
                    }
                    m(n)
                }
            };
            this.getListDataFun = function() {
                var n = function(w) {
					//alert(w.code);
                    if (w.code == 0) {
                        var p = w.data;
                        var t = p.length;
                        if (t > 0) {
                            var o = "";
                            var q = 0;
                            for (var s = 0; s < t; s++) {
                                var v = p[s];
                                o += '<li class="gray6">';
                                o += '<p class="colorbbb">' + v.buyTime + "</p>";
                                var u = v.rnoNum.split(",");
                                for (var r = 0; r < u.length; r++) {
                                    q++;
                                    if (j == parseInt(u[r])) {
                                        o += '<span class="orange">' + u[r] + "</span>"
                                    } else {
                                        o += "<span>" + u[r] + "</span>"
                                    }
                                    if (g > i) {
                                        if (q % i == 0 && q < g) {
                                            o += "#"
                                        }
                                    }
                                }
                                o += "</li>"
                            }
                            if (g > i) {
                                h = o.split("#");
                                if (h.length > 0) {
                                    m(h[f] + "</li>")
                                }
                            } else {
                                m(o);
                                _IsLoading = true;
                                c.hide()
                            }
                        } else {
                            l("获取数据失败！")
                        }
                    } else {
                        l("获取数据失败[" + w.code + "]！")
                    }
                };
            $.getJSON(roots+ "/goods/getUserBuyGoodsCodeInfo", "hidCodeRno="+j+"&hidCodeRno1="+j1+"&codeID=" + a, n)
			}
        };
        e = new k();
        e.getListDataFun();
        scrollForLoadData(e.getNextPage)
    };
    Base.getScript( "/public/js/mobile/pageDialog.js?v=151104", d)
});