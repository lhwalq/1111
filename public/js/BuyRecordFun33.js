$(function() {
    var c = null;
    var l = $("#hidCodeID").val();
    var f = $("#hidIsEnd").val() == "1";
    var d = $("#divLoading");
    var g = $("#divRecordList");
    var e = new Object();
    var k = [];
    var h = 20;
    var a = 0;
    var b = f ? "GetUserBuyListByCodeEnd": "GetUserBuyListByCode";
    var i = {
        codeID: l,
        FIdx: 1,
        EIdx: h,
        isCount: 1,
        sort: 0
    };
    var j = function() {
        var m = function(o) {
            $.PageDialog('<div class="Prompt">' + o + "</div>", {
                W: 150,
                H: 45,
                close: false,
                autoClose: true,
                submit: function() {
                    location.reload()
                }
            })
        };
        var n = function() {
            var o = function() {
                return "codeID=" + i.codeID + "&FIdx=" + i.FIdx + "&EIdx=" + i.EIdx + "&isCount=" + i.isCount + "&sort=" + i.sort
            };
            var p = function() {
                //GetJPData("http://api.1yyg.com", b, o(),
					$.getJSON("/mobile/"+"ajax/"+b, o(),
                function(u) {
                    if (u.Code == 0) {
                        if (i.isCount == 1) {
                            a = u.Count;
                            i.isCount = 0
                        }
                        var t = null;
                        t = i.sort == 0 ? u.Data.Tables.BuyList.Rows: u.Rows;
                        var s = t.length;
                        var q = "";
                        for (var r = 0; r < s; r++) {
                            q += '<li buyID="' + t[r].buyID + '" buyNum="' + t[r].buyNum + '" userName="' + t[r].userName + '">';
                            q += '<i class="fr z-set"></i>';
                            q += '<p><a href="/mobile/mobile/userindex/' + t[r].userWeb + '"><img src="/love/uploads/' + t[r].userPhoto + '" /></a></p>';
                            q += "<dl>";
                            q += '<dt><span class="fl"><a href="/mobile/mobile/userindex/' + t[r].userWeb + '" class="blue">' + t[r].userName + '</a></span><cite class="fl">云购了<b class="orange">' + t[r].buyNum + "</b>人次</cite></dt>";
                            q += '<dd class="gray9">' + t[r].buyTime + "</dd>";
                            q += "</dl>";
                            q += "</li>"
                        }
                        var v = $(q);
                        v.click(function(D) {
                            stopBubble(D);
                            var G = $(this);
                            var C = G.attr("userName");
                            var I = G.attr("buyID");
                            var E = parseInt(G.attr("buyNum"));
                            var w;
                            var F = 400;
                            var x = 0;
                            var A = "";
                            var H = function() {
                                var K = $(".jspPane", w).children("div");
                                if (k.length > 0) {
                                    if (x == (k.length - 1)) {
                                        return
                                    }
                                    x++;
                                    var J = K.html();
                                    J += k[x];
                                    K.html(J);
                                    e["userBuyRnoStr" + I] = J;
                                    z()
                                }
                            };
                            var z = function() {
                                var J = $("#dd_container", w).jScrollPane({
                                    verticalDragMinHeight: 15
                                });
                                J.unbind("scroll").bind("scroll",
                                function() {
                                    var L = parseInt($(".jspTrack", w).css("height"));
                                    var K = parseInt($(".jspDrag", w).css("height"));
                                    var M = parseInt($(".jspDrag", w).css("top"));
                                    if (K + M >= (L - 10)) {
                                        H()
                                    }
                                });
                                J.unbind("mousewheel").bind("mousewheel",
                                function(O, P) {
                                    var M = P > 0 ? "Up": "Down";
                                    var N = J.scrollTop();
                                    var L = J[0].scrollHeight;
                                    var K = J.height();
                                    if (N + K >= L && P < 0) {
                                        preventDefault(O)
                                    } else {
                                        if (N == 0 && P > 0) {
                                            preventDefault(O)
                                        }
                                    }
                                })
                            };
                            var y = $(window).width();
                            var B = function(K) {
                                $("body").attr("style", "overflow:hidden;");
                                IsMasked = true;
                                var L = function() {
                                    var O = '<div class="codes-box clearfix">';
                                    O += '<a id="a_close" href="javascript:;" class="z-set box-close"></a>';
                                    O += '<div class="buy_codes">';
                                    O += "<dl>";
                                    O += '<dt class="gray9"><span class="fl"><a href="javascript:;" class="blue">' + C.substring(0, 7) + '</a></span>本次参与<em class="orange">' + E + "</em>人次</dt>";
                                    O += '<dd class="gray9" id="dd_container">';
                                    O += '<div id="div_list">' + K + "</div>";
                                    O += "</dd>";
                                    O += "</dl>";
                                    O += "</div>";
                                    O += "</div>";
                                    return O
                                };
                                var N = function() {
                                    w = $("#pageDialog");
                                    $("#a_close", w).click(function() {
                                        M.cancel();
                                        $("body").attr("style", "");
                                        IsMasked = false
                                    });
                                    w.bind("click",
                                    function(O) {
                                        stopBubble(O)
                                    });
                                    $("body").bind("click",
                                    function() {
                                        M.cancel();
                                        $("body").attr("style", "");
                                        IsMasked = false
                                    });
                                    $("#pageDialogBG").bind("click",
                                    function() {
                                        M.cancel();
                                        $("body").attr("style", "");
                                        IsMasked = false
                                    });
                                    z()
                                };
                                var J = 835;
                                if (y >= 1000) {
                                    y = J
                                } else {
                                    if (y >= 900) {
                                        y = J - 80 * 1
                                    } else {
                                        if (y >= 800) {
                                            y = J - 80 * 2
                                        } else {
                                            if (y >= 700) {
                                                y = J - 80 * 3
                                            } else {
                                                if (y >= 600) {
                                                    y = J - 80 * 4
                                                } else {
                                                    if (y >= 500) {
                                                        y = J - 80 * 5
                                                    } else {
                                                        if (y >= 400) {
                                                            y = J - 80 * 6
                                                        } else {
                                                            if (y >= 300) {
                                                                y = J - 80 * 7
                                                            } else {
                                                                y = J - 80 * 8
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                var M = new $.PageDialog(L(), {
                                    W: y,
                                    H: 300,
                                    close: true,
                                    autoClose: false,
                                    ready: N
                                })
                            };
                            if (e["userBuyRnoStr" + I] != null && typeof(e["userBuyRnoStr" + I]) != "undefined") {
                                A = e["userBuyRnoStr" + I];
                                B(A)
                            } else {
                                GetJPData("http://api.1yyg.com", "GetUserBuyCodeByBuyid", "codeid=" + l + "&buyid=" + I,
                                function(J) {
                                    if (J.code == 0) {
                                        var M = J.data;
                                        A = "";
                                        x = 0;
                                        var L = 0;
                                        for (var K = 0; K < M.length; K++) {
                                            L++;
                                            A += "<span>" + M[K].rnoNum + "</span>";
                                            if (E > F) {
                                                if (L % F == 0 && L < E) {
                                                    A += "#"
                                                }
                                            }
                                        }
                                        if (E > F) {
                                            k = A.split("#");
                                            if (k.length > 0) {
                                                B(k[x]);
                                                e["userBuyRnoStr" + I] = k[x]
                                            }
                                        } else {
                                            B(A);
                                            e["userBuyRnoStr" + I] = A
                                        }
                                    } else {
                                        m("抱歉，查询失败")
                                    }
                                })
                            }
                        });
                        g.append(v);
                        if (i.EIdx < a) {
                            _IsLoading = false
                        } else {
                            _IsLoading = true;
                            d.hide()
                        }
                    } else {
                        d.hide();
                        if (i.FIdx == 1) {
                            _IsLoading = true;
                            g.html(Gobal.NoneHtml)
                        }
                    }
                })
            };
            this.initData = function() {
                i.FIdx = 1;
                i.EIdx = h;
                i.isCount = 1;
                g.empty();
                p()
            };
            this.getNextPage = function() {
                i.FIdx += h;
                i.EIdx += h;
                p()
            }
        };
        c = new n();
        c.initData();
        scrollForLoadData(c.getNextPage)
    };
    Base.getScript(pub+"/JS/jquery.mousewheel.js?date=20150513",
    function() {
        Base.getScript(pub+"/JS/jquery.jscrollpane.js?date=20150513",
        function() {
            Base.getScript(pub+"/JS/pageDialog.js?v=151104", j)
        })
    })
});