$(function() {
    var g = null;
    var h = $("div.goodList");
    var d = $("#divLoading");
    var b = 60;
    var f = 0;
    var c = $(".weixin-mask");
    var a = {
        sortid: $("#hidsortid").val(),
        brandID: 0,
        orderFlag: $("#hidOrderFlag").val(),
       fIdx: 1,
        eIdx: b,
        isCount: 1
    };
    var e = function() {
        var i = function(m) {
            $.PageDialog('<div class="Prompt">' + m + "</div>", {
                W: 150,
                H: 45,
                close: false,
                autoClose: true,
                submit: function() {
                    location.reload()
                }
            })
        };
        var j = function(m) {
            $.PageDialog.fail(m)
        };
        var l = function(m) {
            $.PageDialog.ok(m)
        };
        var k = function() {
            var r = function() {
                return "sortid=" + a.sortid + "&brandID=" + a.brandID + "&orderFlag=" + a.orderFlag + "&fIdx=" + a.fIdx + "&eIdx=" + a.eIdx + "&isCount=" + a.isCount
            };
            var m = false;
            var q = false;
            var p = function(v, u) {
                c.bind("click",
                function() {
                    $(this).hide();
                    if (m) {
                        v.next("div").hide();
                        v.find("cite").remove();
                        _IsLoading = false;
                        m = false;
                        $("body").attr("style", "");
                        IsMasked = false
                    } else {
                        if (q) {
                            u.next("div").hide();
                            u.find("cite").remove();
                            _IsLoading = false;
                            q = false;
                            $("body").attr("style", "");
                            IsMasked = false
                        }
                    }
                });
                v.bind("click",
                function(x) {
                    stopBubble(x);
                    var w = $(this).next("div");
                    if (m) {
                        w.hide();
                        $(this).find("cite").remove();
                        c.hide();
                        _IsLoading = false;
                        $("body").attr("style", "");
                        IsMasked = false;
                        m = false;
                        return
                    }
                    if (v.find("cite").length == 0) {
                        v.append("<cite><em></em></cite>")
                    }
                    u.find("cite").remove();
                    w.show().siblings("div").hide();
                    c.height($(document).height()).show();
                    _IsLoading = true;
                    $("body").attr("style", "overflow:hidden;");
                    IsMasked = true;
                    q = false;
                    m = true
                });
                u.bind("click",
                function(w) {
                    stopBubble(w);
                    o(v, u)
                })
            };
            var o = function(w, v) {
                if (v.html().indexOf("#bbbbbb") > -1) {
                    return
                }
                var u = v.next("div");
                if (q) {
                    u.hide();
                    $(this).find("cite").remove();
                    c.hide();
                    _IsLoading = false;
                    $("body").attr("style", "");
                    IsMasked = false;
                    q = false;
                    return
                }
                if (v.find("cite").length == 0) {
                    v.append("<cite><em></em></cite>")
                }
                w.find("cite").remove();
                u.show().siblings("div").hide();
                c.height($(document).height()).show();
                _IsLoading = true;
                $("body").attr("style", "overflow:hidden;");
                IsMasked = true;
                m = false;
                q = true
            };
            var n = function(x, u) {
                var v = $(x).next("div");
                var w = $(u).next("div");
                v.children("a").each(function() {
                    var y = $(this);
                    y.bind("click",
                    function(A) {
                        stopBubble(A);
                        a.sortid = y.attr("type");
                        a.orderFlag = 10;
                        u.html('即将揭晓<span></span><b class="fl"></b>');
                        var z = u.next("div.sort_list").find("li:first").find("a");
                        z.addClass("hover").parent().siblings("li").find("a").removeClass("hover");
                        if (a.sortid == 400) {
                            u.html('<font color="#bbbbbb">最新</font>')
                        }
                        y.addClass("hover").siblings().removeClass("hover");
                        v.hide();
                        x.html(y.html() + '<span></span><b class="fr"></b>');
                        m = false;
                        c.hide();
                        _IsLoading = false;
                        $("body").attr("style", "");
                        IsMasked = false;
                        g.getInitPage()
                    })
                });
                w.find("ul li").each(function() {
                    var y = $(this);
                    y.bind("click",
                    function(B) {
                        stopBubble(B);
                        a.orderFlag = y.attr("order");
                        var z = y.find("a");
                        var A = z.html().replace("<s></s>", "").replace("<i></i>", "");
                        z.addClass("hover").parent().siblings("li").find("a").removeClass("hover");
                        w.hide();
                        u.html(A + '<span></span><b class="fl"></b>');
                        q = false;
                        c.hide();
                        _IsLoading = false;
                        $("body").attr("style", "");
                        IsMasked = false;
                        g.getInitPage()
                    })
                })
            };
            var s = function(w, v, u) {
                w.addClass("add");
               // GetJPData("http://weixin.1yyg.com", "addShopCart", "codeid=" + v + "&shopNum=" + u,
					 $.getJSON(roots+"/Goods/addShopCart", "codeid=" + v + "&shopNum=" + u,     
                function(x) {
                    if (x.code == 0) {
                        addNumToCartFun(x.num);
                        l("添加成功")
                    } else {
                        if (x.code == 1) {
                            i("已满员")
                        } else {
                            if (x.code == 4) {
                                j("您参与人次已达上限！")
                            } else {
                                j("添加失败，请重试")
                            }
                        }
                    }
                    w.removeClass("add")
                })
            };
            var t = function() {
                $.getJSON("/mobile/"+"ajax/"+ "getGoodsPageList10", r(),
                function(y) {
					//alert(y.code);
                    if (y.code == 0) {
                        var x = y.listItems;
                        if (a.isCount == 1) {
                            f = y.count;
                            a.isCount = 0
                        }
                        var A = x.length;
                        var B = 0;
                        var D = 0;
                        var u = 0;
                        var E = 0;
                        for (var z = 0; z < A; z++) {
                            var w = x[z];
                            B = parseInt(w.codesales);
                            D = parseInt(w.codequantity);
                            u = parseInt(D - B);
                            E = parseInt(B * 100) / D;
                            E = B > 0 && E < 1 ? 1 : E;
                            var v = "";
                            v += '<ul id="' + w.goodsid + '">';
                             v += "<i class='ico ico-label ico-label-ten'></i><li>";
                            v += '<span class="gList_l fl">';
                            v += '<img src="' + Gobal.LoadPic + '" src2="' + w.goodspic + '" />';
                            if (w.goodstag == 10) {
                                v += '<div class="pTitle pPurchase">限购</div>'
                            }
                            v += "</span>";
                            v += '<div class="gList_r">';
                            v += '<h3 class="gray6">(第' + w.codeperiod + "云)" + w.goodssnme + "</h3>";
                            v += '<em class="gray9">价值：￥' + CastMoney(w.codeprice);
                            v += "</em>";
                            v += '<div class="gRate">';
                            v += '<div class="Progress-bar">';
                            v += '<p class="u-progress"><span style="width: ' + E + '%;" class="pgbar"><span class="pging"></span></span></p>';
                            v += '<ul class="Pro-bar-li">';
                            v += '<li class="P-bar01"><em>' + B + "</em>已参与</li>";
                            v += '<li class="P-bar02"><em>' + D + "</em>总需人次</li>";
                            v += '<li class="P-bar03"><em>' + u + "</em>剩余</li>";
                            v += "</ul>";
                            v += "</div>";
							
                            if (u == 0) {
                                v += '<a href="javascript:" codeid="' + w.codeid + '" class="unAdd"><s></s></a></div>'
                            } else {
                                v += '<a href="javascript:" codeid="' + w.codeid + '" ><s></s></a></div>'
                            }
                            v += "</div>";
                            v += "</li>";
                            v += "</ul>";
                            var C = $(v);
                            C.click(function() {
                                location.href = "/mobile/mobile/item/" + $(this).attr("id") 
                            }).find("div.gRate").find("a").each(function() {
                                var F = $(this);
                                F.click(function(G) {
                                    stopBubble(G);
                                    if (!F.hasClass("unAdd")) {
                                        s(F, F.attr("codeid"), 1)
                                    }
                                })
                            });
                            h.append(C)
                        }
                        if (a.eIdx < f) {
                            _IsLoading = false
                        } else {
                            _IsLoading = true;
                            d.hide()
                        }
                        loadImgFun(0)
                    } else {
                        if (a.fIdx == 1) {
                            _IsLoading = true;
                            h.html(Gobal.NoneHtml)
                        } else {
                            j("加载失败，请重试[" + y.code + "]！")
                        }
                        d.hide()
                    }
                })
            };
            this.BindSortOrder = function() {
                var w = $(".column").children("a");
                var u = $(w.get(0));
                var v = $(w.get(1));
                p(u, v);
                n(u, v)
            };
            this.getInitPage = function() {
                a.fIdx = 1;
                a.eIdx = b;
                a.isCount = 1;
                f = 0;
                h.empty();
                t()
            };
            this.getFirstPage = function() {
                t()
            };
            this.getNextPage = function() {
                a.fIdx += b;
                a.eIdx += b;
                t()
            }
        };
        g = new k();
        g.BindSortOrder();
        g.getFirstPage();
        scrollForLoadData(g.getNextPage)
    };
    Base.getScript(pub+ "/JS/pageDialog.js?v=151104", e)
});