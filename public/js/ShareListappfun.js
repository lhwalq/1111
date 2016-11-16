$(function() {
    var b = function(f) {
        $.PageDialog.fail(f)
    };
    var d = function(f) {
        $.PageDialog.ok(f)
    };
    var e = function() {
        if ($("#hidAppID").val() == "") {
            return
        }
        _RID = $("#hidUserID").val();

        if (_RID > 0) {
            var g = function() {
				
                return escape( + _RID)
            };
            var f = function(j) {
				
                try {
                    if (j.urls[0].result) {
                        var i = j.urls[0].title;
                        var l = j.urls[0].url_short.replace("t.cn", "t.yyygcms.cn");
						
                        $("#txtInfo").html(i + l)
                    }
                } catch(k) {
                    console.log(k.message)
                }
            };
			
            $.getJSON("/mobile"+"/ajax/getshorturl", "url=" + escape("" + g()), f)
        }
         var h = $("#hidLineLink").val();
		var im = $("#hidLineimage").val();
		var ti = $("#hidLinetitle").val();
        wxShareFun({
             shareTitle: ti,
            shareImg: im,
            shareLink: h,
            shareDesc: "1元买iphone6s！最奇(ci)葩(ji)的玩法！快来挑战！",
            shareMoney: true,
            showMask: false
        });
        $("#btnShare").bind("click",
        function(i) {
            wxShowMaskFun(true);
            return false
        })
    };
    var c = function() {
        var g = $("div.weixin-mask");
        var f = parseFloat($("#hidCurrMoney").val());
        $("#liMention").click(function() {
            if (f >= 100) {
                location.href = "/mobile/invite/cashout"
            } else {
                b("佣金满100才可提现哦")
            }
        });
        $("#liRechagre").click(function() {
            if (parseInt(f) > 0) {
                var h = '<div class="record-pop-ups f-pop-balance clearfix"><dl><dt class="gray9">可转入金额<em class="orange">￥' + CastMoney(parseInt(f)) + '</em></dt><dd class="gray6">确认将以上金额转入云购账户？</dd><dd class="comm-pop-btn"><cite class="return-modify"><a id="closeDiag" href="javascript:;" class="gray6">取消</a></cite><cite><a id="btnOK" href="javascript:;" class="orangeBtn">确认</a></cite></dd></dl></div>';
                $("body").attr("style", "overflow:hidden;");
                $("#wrapper").append(h).find("#closeDiag").click(function() {
                    $("div.f-pop-balance").remove();
                    g.hide();
                    $("body").attr("style", "");
                    IsMasked = false
                });
                var i = function() {
                    var j = $("div.f-pop-balance");
                    if (j.length > 0) {
                        j.css({
                            top: ($(window).height() - j.height() - 40) / 2,
                            left: ($(window).width() - j.width() - 30) / 2
                        });
                        g.css("height", $(document).height() > $(window).height() ? $(document).height() : $(window).height()).show()
                    }
                };
                i();
                IsMasked = true;
                $(window).resize(i);
                $("#btnOK").click(function() {
                    $.getJSON("/mobile"+"/ajax/memberCenterApplyToAccount", "",
                    function(l) {
                        if (l.code == 0) {
                            f = f - l.money;
                            $("div.f-pop-balance").remove();
                            var j = '<div class="record-pop-ups f-pop-transferred clearfix"><b></b><h5 class="gray6">已成功转入云购账户<em class="orange">￥' + l.money + '</em></h5><a id="btnSure" href="javascript:;" class="orangeBtn">确定</a></div>';
                            $("#wrapper").append(j);
                            var k = function() {
                                var m = $("div.f-pop-transferred");
                                if (m.length > 0) {
                                    m.css({
                                        top: ($(window).height() - m.height() - 40) / 2,
                                        left: ($(window).width() - m.width() - 30) / 2
                                    });
                                    g.css("height", $(document).height() > $(window).height() ? $(document).height() : $(window).height()).show()
                                }
                            };
                            k();
                            IsMasked = true;
                            $(window).resize(k);
                            $("#btnSure").click(function() {
                                $("div.f-pop-transferred").remove();
                                g.hide();
                                $("body").attr("style", "");
                                IsMasked = false;
                                $("#emMoney").html("￥" + CastMoney(f))
                            })
                        } else {
                            if (l.code == -1) {
                                b("您的佣金已不满1元")
                            } else {
                                b("佣金转入失败")
                            }
                        }
                    })
                });
                $("#wrapper").append(_Obj)
            } else {
                b("佣金满1元才可转入哦")
            }
        })
    };
    var a = function() {
        if ($("#divUnbind").length <= 0) {
            return
        }
        var u = "http://weixin.1yyg.com";
        var x = $("#userMobile");
        var B = $("#mobileCode");
        var p = $("#btnGetCode");
        var r = $("#btnBind");
        var h = function(F) {
            var E = /^\d+$/;
            return E.test(F)
        };
        var j = function(F) {
            var E = /^1\d{10}$/;
            return E.test(F)
        };
        var q = function(F) {
            var E = /^[0-9a-zA-Z]{6,}$/;
            return E.test(F)
        };
        var t = {
            txtMobileStr: "请输入您的手机号码",
            txtCodeStr: "请输入手机验证码",
            merror: "请输入正确的手机号码",
            cerror: "请输入正确的验证码",
            many: "验证码请求次数过多，请稍后再试",
            retry: "验证码发送失败，请重试",
            sendok: "验证码发送成功",
            bindok: "绑定成功",
            bindfail: "绑定失败,请重试",
            outtime: "验证码错误或过期",
            isbind: "已被绑定，请更换手机号码"
        };
        var y = {
            btncode: "获取验证码",
            btnbind: "立即绑定",
            getcode: "重新发送",
            binding: "正在绑定..."
        };
        var m = false;
        var s = function() {
            if (m) {
                return
            }
            if (v()) {
                var F = C;
                var E = function(G) {
                    if (F == C) {
                        if (G.state == 1) {
                            b(t.isbind)
                        } else {
                            if (G.state == 0) {
                                g();
                                return
                            } else {
                                b(t.retry)
                            }
                        }
                    }
                    m = false;
                    p.html(y.btncode).bind("click", s).removeClass("notClick")
                };
                m = true;
                p.html(y.getcode).addClass("notClick").unbind("click");
                GetDominData("http://weixin.1yyg.com", "getpassportdata", "action=checkname&name=" + F, E)
            }
        };
        var g = function() {
            var F = C;
            var E = function(G) {
                if (G.state == 0) {
                    b(t.sendok);
                    r.unbind("click").bind("click", D)
                } else {
                    if (G.state == 2) {
                        b(t.many)
                    } else {
                        b(t.retry)
                    }
                }
            };
            n();
            GetDominData("http://weixin.1yyg.com", "getpassportdata", "action=send2Msg&userMobile=" + F, E)
        };
        var C = "";
        var v = function() {
            C = x.val();
            if (C == "" || C == t.txtMobileStr) {
                b(t.txtMobileStr);
                return false
            } else {
                if ((C.length < 11 || C.length >= 11) && !j(C)) {
                    b(t.merror);
                    return false
                }
            }
            return true
        };
        var k = false;
        var D = function() {
            if (k) {
                return
            }
            if (v()) {
                var F = B.val();
                if (F == "" || F == t.txtCodeStr) {
                    b(t.txtCodeStr);
                    return
                } else {
                    if (!q(F)) {
                        b(t.cerror);
                        return
                    } else {
                        var E = function(G) {
                            if (G.state == 0) {
                                A(F);
                                return
                            } else {
                                b(t.outtime)
                            }
                            k = false;
                            r.html(y.btnbind).removeClass("notClick").bind("click", D)
                        };
                        k = true;
                        r.html(y.binding).addClass("notClick").unbind("click");
                        GetDominData("http://weixin.1yyg.com", "getpassportdata", "action=verifymobilesn&mobile=" + x.val() + "&sn=" + F, E)
                    }
                }
            }
        };
        var A = function(F) {
            var E = function(G) {
                if (G.code == 0) {
                    b(t.bindok);
                    window.location.reload();
                    return
                } else {
                    b(t.bindfail)
                }
                k = false;
                r.html(y.btnbind).removeClass("notClick").bind("click", D)
            };
            GetJPData("", "bindusermobile", "mobile=" + x.val() + "&sn=" + F, E)
        };
        var n = function() {
            p.html(y.getcode).addClass("notClick").unbind("click");
            x.attr("disabled", true);
            var F = 120;
            var E = function() {
                if (F < 1) {
                    p.html(y.btncode).bind("click", s).removeClass("notClick");
                    m = false;
                    x.attr("disabled", false);
                    return
                } else {
                    F--;
                    p.html(y.getcode + "(" + F + ")")
                }
                setTimeout(E, 1000)
            };
            E()
        };
        var l = true;
        var w = true;
        var o = "";
        var f = function() {
            var E = x.val();
            if (h(E) || E == "" || E == t.txtMobileStr) {
                i = E
            } else {
                x.val(i).focus()
            }
            if (l) {
                setTimeout(f, 200)
            }
        };
        var i = "";
        var z = function() {
            var E = B.val();
            if (i != E) {
                if (h(E) || E == "" || E == t.txtCodeStr) {
                    i = E
                } else {
                    B.val(i).focus()
                }
            }
            if (w) {
                setTimeout(z, 200)
            }
        };
        x.bind("focus",
        function() {
            if ($(this).val() == t.txtMobileStr) {
                $(this).val("")
            }
            l = true;
            f();
            $(this).css("color", "#666666")
        }).bind("blur",
        function() {
            l = false;
            if ($(this).val() == "") {
                $(this).val(t.txtMobileStr);
                $(this).css("color", "#bbbbbb")
            }
        });
        B.bind("focus",
        function() {
            if ($(this).val() == t.txtCodeStr) {
                $(this).val("")
            }
            w = true;
            z();
            $(this).css("color", "#666666")
        }).bind("blur",
        function() {
            w = false;
            if ($(this).val() == "") {
                $(this).val(t.txtCodeStr);
                $(this).css("color", "#bbbbbb")
            }
        });
        p.bind("click", s);
        r.bind("click",
        function() {
            b("请先获取短信验证码")
        })
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104",
    function() {
        a();
        c();
       
        function() {
            e()
        })
    })
});