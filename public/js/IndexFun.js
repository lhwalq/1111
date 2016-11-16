$(function () {
    var a = function () {
        var c = "_downApp";
        var m = $.cookie(c);
        if (m == null || m == "") {
            if ($("#hidAppType").val() == "0") {
                $("#downLink").attr("href", "https://www.pgyer.com/dzmM");
                $("#divDownApp").show().find("a.close").click(function () {
                    $(this).parent().hide();
                    $.cookie(c, "1", {
                        expires: 7,
                        path: "/"
                    })
                })
            }
        }
        var j = new Object();
        var k = function () {
            var w = $.cookie("_indexads");
            if (w != null && typeof (w) != "undefined") {
                var v = $(w);
                v.addClass("slides");
                $("#sliderBox").empty().append(v).flexslider()
            } else {
                // GetJPData("http://poster.1yyg.com", "getbysortid", "ID=15",
                //roots+"/Slide/getSlides"
                $.getJSON(roots + "/Slide/getSlides",
                        function (s) {
                            if (s.state == 0) {
                                var r = s.listItems;

                                var n = $("<ul/>");
                                n.addClass("slides");
                                var p = "";
                                for (var o = 0; o < r.length; o++) {
                                    var m = '<li style="background-color:' + r[o].alt + ';"><a href="' + r[o].url + '"><img src="' + publics + r[o].src + '" alt="" width="' + r[o].width + '" height="' + r[o].height + '" /></a></li>';
                                    n.append(m)
                                }
                                var q = $("#sliderBox");
                                q.append(n).flexslider()
                            }
                        });
            }
        };
        Base.getScript(pub + "/JS/Flexslider.js?v=151105", k);
        var f = function (v) {
            $.PageDialog('<div class="Prompt">' + v + "</div>", {
                W: 150,
                H: 45,
                close: false,
                autoClose: true,
                submit: function () {
                    location.reload()
                }
            })
        };
        var u = function (v) {
            $.PageDialog.fail(v)
        };
        var g = function (v) {
            $.PageDialog.ok(v)
        };
        var q = $("#goodsNav");
        var b = q.offset().top;
        var r = b + q.height();
        $("#ulOrder li").each(function () {
            $(this).click(function () {
                p();
                $(this).addClass("current").siblings().removeClass("current");
                i.orderFlag = parseInt($(this).attr("order"));
                h.initPage();
                if (i.orderFlag == 30) {
                    $(this).attr("order", "31")
                } else {
                    if (i.orderFlag == 31) {
                        $(this).attr("order", "30")
                    }
                }
            })
        });
        var p = function () {
            $("body").attr("style", "");
            t.removeClass("current").next("div.select-total").hide();
            n = false
        };
        var n = false;
        var t = $("#divSort");
        t.click(function (v) {
            //alert(stopBubble);
            stopBubble(v);
            if (n) {
                p()
            } else {
                $("body").attr("style", "overflow:hidden;");
                t.addClass("current").next("div.select-total").show();
                n = true
            }
        });
        t.next("div.select-total").find("li").each(function () {
            $(this).click(function () {
                i.sortid = parseInt($(this).attr("sortid"));
                //alert(i.sortid);
                //exit;
                location.href = roots + "/goods/glist" + "?orderFlag=" + i.orderFlag + "&sort=" + i.sortid
            })
        });
        $("body").click(function () {
            p();
        });
        var h = null;
        var s = 60;
        var d = 0;
        var i = {
            sortid: 0,
            orderFlag: 10,
            fIdx: 1,
            eIdx: s,
            isCount: 1
        };
        var l = $("#ulGoodsList");
        var o = $("div.loading");
        var e = function () {
            var v = function () {
                return "sortid=" + i.sortid + "&orderFlag=" + i.orderFlag + "&fIdx=" + i.fIdx + "&eIdx=" + i.eIdx + "&isCount=" + i.isCount
            };
            var w = function (B, A, z, y) {
                ;
                B.addClass("add");


                //    GetJPData("http://weixin.1yyg.com", "addShopCart", "codeid=" + A + "&shopNum=" + z,
                $.getJSON(roots + "/Goods/addShopCart", "codeid=" + A + "&shopNum=" + z,
                        function (C) {
                            //alert(C.code);
                            if (C.code == 0) {
                                addNumToCartFun(C.num);
                                if (typeof (y) == "function") {
                                    y()
                                } else {
                                    //幸运号码
                                    var chooselist = localStorage.getItem('chooselist');
                                    if (!chooselist) {
                                        var data = {};
                                    } else {
                                        var data = jQuery.parseJSON(chooselist);
                                        if ((typeof data) !== 'object') {
                                            var data = {};
                                        }
                                    }
                                    delete data[A];
                                    localStorage.setItem('chooselist', JSON.stringify(data));
                                    g("添加成功")
                                }
                            } else {
                                if (C.code == 1) {
                                    f("已满员")
                                } else {
                                    if (C.code == 4) {
                                        u("您参与人次已达上限！")
                                    } else {
                                        u("添加失败，请重试")
                                    }
                                }
                            }
                            B.removeClass("add")
                        })
            };

            function GetJPData(d, c, a, b) {
                $.getJSON(roots + "/Goods/getGoodsList_ajax?" + v(), b);
            }
            var x = function () {
                GetJPData("", "", v(),
                        function (D) {
                            if (D.code == 0) {
                                var B = D.listItems;
                                //console.info(B[C]);
                                if (i.isCount == 1) {
                                    d = D.count;
                                    //alert(d);
                                    i.isCount = 0
                                }
                                var E = B.length;
                                var F = 0;
                                var H = 0;
                                var y = 0;
                                var I = 0;
                                var z = "";
                                for (var C = 0; C < E; C++) {
                                    var A = B[C];
                                    //console.info(B[C]);
                                    F = parseInt(A.codesales);
                                    H = parseInt(A.codequantity);
                                    y = parseInt(H - F);
                                    I = parseInt(F * 100) / H;
                                    I = F > 0 && I < 1 ? 1 : I;
                                    if (A.yunjiage == 10) {
                                        z = '<li id="' + A.goodsid + '"><i class="ico ico-label ico-label-ten"></i><a href="javascript:;" class="g-pic"><img src="' + publics + A.goodspic + '" src2="' + A.goodspic + '" width="136" height="136" />' + (A.goodstag == 10 ? '<div class="pTitle pPurchase">限购</div>' : "") + '</a><p class="g-name">(第' + A.codeperiod + "期)" + A.goodssnme + '</p><ins class="gray9">价值:￥' + CastMoney(A.codeprice) + '</ins><div class="Progress-bar"><p class="u-progress"><span class="pgbar" style="width: ' + I + '%;"><span class="pging"></span></span></p></div><div class="btn-wrap"><a href="javascript:;" class="buy-btn' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '">立即1元云购</a><div class="gRate' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '"><a href="javascript:;"><s></s></a></div></div></li>';

                                    } else if (A.yunjiage == 100) {
                                        z = '<li id="' + A.goodsid + '"><i class="ico ico-label ico-label-ten1"></i><a href="javascript:;" class="g-pic"><img src="' + publics + A.goodspic + '" src2="' + A.goodspic + '" width="136" height="136" />' + (A.goodstag == 10 ? '<div class="pTitle pPurchase">限购</div>' : "") + '</a><p class="g-name">(第' + A.codeperiod + "期)" + A.goodssnme + '</p><ins class="gray9">价值:￥' + CastMoney(A.codeprice) + '</ins><div class="Progress-bar"><p class="u-progress"><span class="pgbar" style="width: ' + I + '%;"><span class="pging"></span></span></p></div><div class="btn-wrap"><a href="javascript:;" class="buy-btn' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '">立即1元云购</a><div class="gRate' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '"><a href="javascript:;"><s></s></a></div></div></li>';

                                    } else if (A.fahuo == 1) {
                                        z = '<li id="' + A.goodsid + '"><i class="ico ico-label ico-label-tenxg"></i><a href="javascript:;" class="g-pic"><img src="' + publics + A.goodspic + '" src2="' + A.goodspic + '" width="136" height="136" />' + (A.goodstag == 10 ? '<div class="pTitle pPurchase">限购</div>' : "") + '</a><p class="g-name">(第' + A.codeperiod + "期)" + A.goodssnme + '</p><ins class="gray9">价值:￥' + CastMoney(A.codeprice) + '</ins><div class="Progress-bar"><p class="u-progress"><span class="pgbar" style="width: ' + I + '%;"><span class="pging"></span></span></p></div><div class="btn-wrap"><a href="javascript:;" class="buy-btn' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '">立即1元云购</a><div class="gRate' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '"><a href="javascript:;"><s></s></a></div></div></li>';

                                    } else {
                                        z = '<li id="' + A.goodsid + '"><a href="javascript:;" class="g-pic"><img src="' + publics + A.goodspic + '" src2="' + A.goodspic + '" width="136" height="136" />' + (A.goodstag == 10 ? '<div class="pTitle pPurchase">限购</div>' : "") + '</a><p class="g-name">(第' + A.codeperiod + "期)" + A.goodssnme + '</p><ins class="gray9">价值:￥' + CastMoney(A.codeprice) + '</ins><div class="Progress-bar"><p class="u-progress"><span class="pgbar" style="width: ' + I + '%;"><span class="pging"></span></span></p></div><div class="btn-wrap"><a href="javascript:;" class="buy-btn' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '">立即1元云购</a><div class="gRate' + (y == 0 ? " unAdd" : "") + '" codeid="' + A.codeid + '"><a href="javascript:;"><s></s></a></div></div></li>';

                                    }
                                    var G = $(z);
                                    G.click(function () {
                                        location.href = roots + "/Goods/items/goodsId/" + $(this).attr("id");
                                    }).find("div.gRate").click(function (K) {
                                        stopBubble(K);
                                        var J = $(this);
                                        if (!J.hasClass("unAdd")) {
                                            w(J, J.attr("codeid"), 1)
                                        }
                                    });
                                    G.find("a.buy-btn").click(function (K) {
                                        stopBubble(K);
                                        var J = $(this);
                                        if (!J.hasClass("unAdd")) {
                                            w(J, J.attr("codeid"), 1,
                                                    function () {
                                                        location.href = roots + "/Goods/cartlist";
                                                    })
                                        }
                                    });
                                    l.append(G)
                                }
                                if (i.eIdx < d) {
                                    _IsLoading = false
                                    o.hide()
                                } else {
                                    _IsLoading = true;
                                    o.hide()
                                }
                                loadImgFun(0)
                            } else {
                                o.hide();
                                if (i.fIdx == 1) {
                                    _IsLoading = true;
                                    l.append(Gobal.NoneHtml)
                                }
                            }
                        })
            };
            this.getNextPage = function () {
                i.fIdx = i.fIdx + s;
                i.eIdx = i.eIdx + s;
                x()
            };
            this.initPage = function () {
                i.fIdx = 1;
                i.eIdx = s;
                i.isCount = 1;
                l.empty();
                x()
            }
        };
        h = new e();
        h.initPage();
        scrollForLoadData(h.getNextPage);
    };
    Base.getScript(pub + "/JS/pageDialog.js?v=151104", a);
});