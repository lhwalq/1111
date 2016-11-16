$(function() {
    var j = $("#cartBody");
    var l = $("#divNone");
    var a = $("#mycartpay");
    var c = a.children("dl").children("dt").find("em");
    var b = $(c[1]);
    var h = $(c[0]);
    var i = parseInt($("#hidUserID").val());
    var k = i > 0 ? true: false;
    var d = $("#a_payment");
    var g = null;
    var m = function() {
        var o = function(A) {
            $.PageDialog.fail(A)
        };
        var y = function(H, B, C, A, J) {
            var D = 255;
            var G = 126;
            if (typeof(A) != "undefined") {
                D = A
            }
            if (typeof(A) != "undefined") {
                G = J
            }
            var I = null;
            var K = '<div class="clearfix m-round u-tipsEject"><div class="u-tips-txt">' + H + '</div><div class="u-Btn"><div class="u-Btn-li"><a href="javascript:;" id="btnMsgCancel" class="z-CloseBtn">取消</a></div><div class="u-Btn-li"><a id="btnMsgOK" href="javascript:;" class="z-DefineBtn">确定</a></div></div></div>';
            var F = function() {
                var L = $("#pageDialog");
                L.find("a.z-DefineBtn").click(function() {
                    if (typeof(B) != "undefined" && B != null) {
                        B()
                    }
                    E.close()
                });
                L.find("a.z-CloseBtn").click(function() {
                    if (typeof(C) != "undefined" && C != null) {
                        C()
                    }
                    E.cancel()
                })
            };
            var E = new $.PageDialog(K, {
                W: D,
                H: G,
                close: true,
                autoClose: false,
                ready: F
            })
        };
        var x = function() {
            var A = 0;
            var B = 0;
            $("input[name=pnum]", j).each(function(E) {
                var D = $(this).attr("id");
                var C = D.replace("txtNum", "");
                var G = parseInt($("#codeState" + C).val());
                if (G != 1) {
                    return false
                }
                var F = parseInt($(this).val());
                if (!isNaN(F)) {
                    B++;
                    A += F
                }
            });
            b.html(B);
            h.html("<span>￥</span>" + CastMoney(A));
            if (B == 0 && A == 0) {
                a.hide()
            }
        };
        var v = null;
        var p = function(C, A, F, B) {
            if (C != 3 || A <= 0) {
                return ""
            }
            var D = "";
            var E = F - B;
            if (A < E) {
                D = "剩余" + A + "人次，最多可参与" + A + "人次"
            } else {
                if (B == 0) {} else {
                    if (B < F) {
                        D = "您已参与" + B + "人次，最多可参与" + E + "人次"
                    }
                }
            }
            return D
        };
        var n = function() {
            $("div.footer").hide();
            a.css("bottom", "0px");
            var O = $(this);
            var H = O.attr("id");
            var K = H.replace("txtNum", "");
            var A = $("#oldshopNum" + K);
            var C = parseInt($("#suplus" + K).val());
            var N = C;
            var G = parseInt($("#codetype" + K).val());
            var D = (G == 3) ? true: false;
            var B = 0;
            var F = 0;
            if (D) {
                B = parseInt($("#limitbuynum" + K).val());
                F = parseInt($("#havebuynum" + K).val());
                var P = B - F;
                if (C > P) {
                    C = P
                }
            }
            var E, M, J = /^[1-9]{1}\d{0,6}$/;
            var I;
            var L = function() {
                E = A.val();
                M = O.val();
                if (E != M) {
                    var R = $(window).width();
                    var Q = (R) / 2 - O.offset().left - 127;
                    if (M == "") {
                        return
                    }
                    if (J.test(M)) {
                        I = parseInt(M);
                        if (I <= C) {
                            A.val(M)
                        } else {
                            I = C;
                            if (D) {
                                var S = p(G, N, B, F);
                                if (S != "") {
                                    o(S)
                                }
                            } else {
                                o("最多可参与" + I + "人次")
                            }
                            O.val(I);
                            A.val(I)
                        }
                        t(O, K, I);
                        x();
                        z(I, O)
                    } else {
                        o("只能输正整数哦");
                        O.val(E)
                    }
                }
            };
            v = setInterval(L, 200)
        };
        var z = function(B, E) {
            var D = E.parent().parent();
            var A = D.find("div.z-Cart-tips");
            if (B > 100) {
                if (A.length == 0) {
                    var C = $('<div class="z-Cart-tips">已超过100人次，云购存在一定风险，请谨慎参与！</div>');
                    D.prepend(C)
                }
            } else {
                A.remove()
            }
        };
        var r = function(G, B) {
            var A = parseInt($("#suplus" + G).val());
            var D = parseInt($("#codetype" + G).val());
            if (D == 3) {
                var F = parseInt($("#limitbuynum" + G).val());
                var C = parseInt($("#havebuynum" + G).val());
                var E = F - C;
                if (A > E) {
                    A = E
                }
            } else {
                if (B <= 0) {
                    B = 1
                }
            }
            if (B > A) {
                B = A
            }
            return B
        };
        var w = function() {
            $("div.footer").show();
            a.css("bottom", "49px");
            if (v != null) {
                clearInterval(v)
            }
            var E = $(this);
            var C = E.attr("id");
            var B = C.replace("txtNum", "");
            var A = parseInt($("#oldshopNum" + B).val());
            var D = parseInt(E.val());
            if (isNaN(D) || E.val() == "") {
                D = A
            } else {
                if (A == D) {
                    return
                }
            }
            E.val(r(B, D))
        };
        var t = function(A, D, B) {
            var C = function(G) {
                if (G.code == 1) {
                    var F = $(window).width();
                    var E = (F) / 2 - A.offset().left - 127;
                    o("商品已被抢光了");
                    window.location.reload()
                } else {
                    if (G.code == 0) {} else {
                        var F = $(window).width();
                        var E = (F) / 2 - A.offset().left - 127;
                        o("更改失败，请重试");
                        window.location.reload()
                    }
                }
            };
            GetJPData("", "updCartNum", "codeid=" + D + "&num=" + B, C)
        };
        $("input[name=pnum]", j).each(function(A) {
            var B = $(this).val();
            z(B, $(this));
            $(this).bind("focus", n).bind("blur", w).bind("touchstart",
            function() {
                $("div.footer").hide();
                a.css("bottom", "0px")
            })
        });
        $("a[name=delLink]", j).each(function(A) {
            $(this).bind("click",
            function() {
                var B = $(this);
                var D = B.attr("cid");
                var C = function() {
                    var E = function(F) {
                        if (F.code == 0) {
                            B.parent().parent().remove();
                            f(false);
                            x()
                        } else {
                            o("删除失败，请重试")
                        }
                    };
                    GetJPData("", "delCartItem", "codeid=" + D, E)
                };
                y("您确定要删除吗？", C)
            })
        });
        d.bind("click",
        function() {
			
            var C = false;
            var B = "";
            var E = "";
            var A = "";
            var D = 0;
            $("input[name=pnum]", j).each(function(G) {
                var H = parseInt($(this).val());
                if (H == "0") {
					
                    if (!C) {
                        C = true
                    }
                    B += $(this).attr("id").replace("txtNum", "") + ","
                } else {
                    E += $(this).attr("id").replace("txtNum", "") + ",";
                    A += H + ","
                }
                if (!isNaN(H)) {
                    D += H
                }
            });
			
            if (E == "" || A == "" || D == 0) {
                o("无有效商品，请删除重新添加");
                return
            }
            if (C) {
                var F = function() {
                    if (B == "") {
                        return
                    }
                    var G = function(H) {
                        if (H.code == 0) {
                            s(E, A)
                        }
                    };
                    GetJPData("", "delCartItem", "codeid=" + B, G)
                };
                y("部分商品已满限购人次，删掉继续结算？", F, null, 300)
            } else {

                s(E, A)
            }
        });
        var s = function(C, A) {
            var B = function(F) {
				alert(F.code);
                if (F.code == 0) {
                    location.href = "/mycart/Payment.do"
                } else {
                    if (F.code == 1) {
                        var D = function() {
                            var G = function(H) {
                                if (H.code == 0 || H.code == 1) {
                                    location.href = "/mycart/Payment.do"
                                } else {
                                    o("操作失败，请重试")
                                }
                            };
                            GetJPData("", "updateGoodsState", "TrueCode=" + C + "&buynum=" + A, G)
                        };
                        var E = function() {
                            location.href = "/mycart/Payment.do?useroper=1"
                        };
                        y("部分商品已失效，继续云购？", D, E)
                    } else {
                        if (F.code == 10) {
                            u()
                        } else {
                            o("操作失败，请重试")
                        }
                    }
                }
            };
            GetJPData("", "selectGoodsState", "codeStr=" + C, B)
        };
        var q = function(B) {
            var A = function(E, F, C) {
                var D = new RegExp(F, "g");
                return E.replace(D, C)
            };
            var B = escape(B);
            B = A(B, "\\+", "%2B");
            B = A(B, "/", "%2F");
            return B
        };
        var u = function() {
            location.href = "http://weixin.1yyg.com/passport/login.do?forward=" + q("/mycart/payment.do")
        }
    };
    var f = function(q) {
        var o = $("li", "#cartBody");
        if (o.length < 1) {
            a.hide();
		
            if (k == "1") {
                l.show()
            } else {
                l.append('<p>您当前状态为未登录，可能导致购物车为空</p><a href="/Passport/login.do?forward=' + encodeURIComponent("/mobile/cart/cartlist") + '" class="orangeBtn">立即登录</a>').show()
            }
            $("div.footer").show().find("#btnCart").addClass("hover").find("i").html("")
        } else {
            if (!q) {
                var p = $("div.footer").find("#btnCart").find("b");
                var n = parseInt(p.attr("num")) - 1;
                if (n < 100) {
                    p.attr("num", n).removeClass("tomore").html(n)
                }
            }
        }
    };
    var e = $("li", "#cartBody");
    if (e.length > 0) {
        Base.getScript("/JS/pageDialog.js?v=151104", m);
        a.show()
    } else {
        f(true)
    }
});