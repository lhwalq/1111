$(function() {
    var b = function(d) {
        $.PageDialog.fail(d)
    };
    var c = function(d) {
        $.PageDialog.ok(d)
    };
    var a = function() {
        var o = "#bbbbbb";
        var k = "#666666";
        var j = parseFloat($("#emCurrMoney").html());
        var q = $("#txtMoney");
        var y = $("#txtUserName");
        var g = $("#txtBank");
        var n = $("#selectAreaP");
        var p = $("#selectAreaC");
        var i = $("#txtSubBank");
        var r = $("#txtAccount");
        var u = $("#txtPhone");
        var v = "选择省",
        s = "选择市";
        $.fn.formatInput = function(D) {
            var I = null;
            var C = "",
            B = /^\d{1,}$/;
            var G = {
                min: 1,
                max: 50,
                deimiter: "",
                onlyNumber: true,
                copy: true,
                callback: function() {}
            };
            var E = $.extend({},
            G, D);
            var F = $(this);
            F.css({
                imeMode: "Disabled"
            }).attr("maxlength", E.max);
            if (F.val() != "") {
                F.val(F.val().replace(/\s/g, "").replace(/(\d{4})(?=\d)/g, "$1" + E.deimiter))
            }
            var H = function() {
                if (F.val() != "" && F.val() != C) {
                    if (E.onlyNumber) {
                        if (!B.test(F.val().replace(/\s/g, ""))) {
                            F.val(F.val().replace(/\D/g, ""))
                        }
                    }
                    var J = E.max - (E.deimiter != "" ? (E.max % 4) : 0);
                    if (J > 0 && F.val().length > J) {
                        F.val(F.val().replace(/\s/g, "").substring(0, J))
                    }
                    F.val(F.val().replace(/\s/g, "").replace(/(\d{4})(?=\d)/g, "$1" + E.deimiter));
                    C = F.val();
                    if (E.callback) {
                        E.callback()
                    }
                }
            };
            F.bind("dragenter",
            function() {
                return false
            }).bind("onpaste",
            function() {
                return ! clipboardData.getData("text").match(/\D/)
            }).bind("focus",
            function() {
                $(this).css("color", k);
                I = setInterval(H, 200)
            }).bind("blur",
            function() {
                if ($(this).val() == "") {
                    $(this).css("color", o)
                }
                if (I != null) {
                    clearInterval(I)
                }
            })
        };
        $.fn.formatChnInput = function(C) {
            var H = null;
            var B = "";
            var F = {
                max: 50,
                copy: true,
                callback: function() {}
            };
            var D = $.extend({},
            F, C);
            var E = $(this);
            E.css({
                imeMode: "Disabled"
            }).attr("maxlength", D.max);
            if (E.val() != "") {
                E.val(E.val().replace(/[^\u4E00-\u9FA5]/g, ""))
            }
            var G = function() {
                return;
                if (E.val() != "" && E.val() != B) {
                    E.val(E.val().replace(/[^\u4E00-\u9FA5]/g, ""));
                    B = E.val();
                    if (D.callback) {
                        D.callback()
                    }
                }
            };
            E.bind("dragenter",
            function() {
                return false
            }).bind("onpaste",
            function() {
                return ! clipboardData.getData("text").match(/[^\u4E00-\u9FA5]/g)
            }).bind("focus",
            function() {
                $(this).css("color", k);
                if (H == null) {
                    H = setInterval(G, 200)
                }
            }).bind("blur",
            function() {
                if ($(this).val() == "") {
                    $(this).css("color", o)
                }
                if (H != null) {
                    clearInterval(H)
                }
            })
        };
        q.formatInput({
            max: (parseInt(j)).toString().length,
            onlyNumber: true,
            callback: function() {
                if (parseInt(q.val()) > parseInt(j)) {
                    q.val(parseInt(j))
                }
            }
        });
        y.formatChnInput();
        g.formatChnInput();
        i.formatChnInput();
        r.formatInput({
            max: 23,
            onlyNumber: true,
            deimiter: " "
        });
        u.focus(function() {
            $(this).css("color", k)
        }).blur(function() {
            if ($(this).val() == "") {
                $(this).css("color", o)
            }
        });
        var l = function() {
            GetJPData("", "getUserBrokerageApplyLastest", "",
            function(E) {
                if (E.code == 0) {
                    y.val(E.bankUser).css("color", k);
                    g.val(E.bankName).css("color", k);
                    var C = "",
                    D = "",
                    F = "";
                    var B = E.bankDetail.split(" ");
                    if (B.length > 2) {
                        C = B[0];
                        D = B[1];
                        F = B[2]
                    } else {
                        F = E.bankDetail
                    }
                    i.val(F).css("color", k);
                    r.val(E.bankAccount.replace(/\s/g, "").replace(/(\d{4})(?=\d)/g, "$1 ")).css("color", k);
                    u.val(E.telePho).css("color", k)
                } else {
                    if (E.code == 2) {
                        b("您的佣金余额不足100元");
                        setTimeout(function() {
                            location.href = "/member/sharelist-" + GetRandomNum(100000, 999999) + ".do"
                        },
                        2000)
                    }
                }
            })
        };
        var m = function(F, E, C, D) {
            E.attr("disabled", true);
            var B = function(G) {
                if (G.code == 0) {
                    E.empty();
                    var K = G.data;
                    var J = v;
                    if (C == "B") {
                        J = s
                    }
                    var I = '<option value="-1" zip="">' + J + "</option>";
                    for (var H = 0; H < K.length; H++) {
                        I += '<option value="' + K[H].areaID + '" zip="' + K[H].areaZip + '" txt="' + K[H].areaName + '">' + K[H].areaName + "</option>"
                    }
                    E.append(I);
                    E.attr("disabled", false);
                    if (D) {
                        D()
                    }
                }
            };
            if (F > -1) {
                $.getJSON(roots+"/user"+"/getChildArea", "areaID=" + F, B)
            }
        };
        m(1, n, "A", l);
        n.change(function() {
            var B = $(this).val();
            if (B == -1) {
                n.css("color", o);
                p.html('<option value="-1" zip="">' + s + "</option>").css("color", o)
            } else {
                n.css("color", k);
                m(B, p, "B");
                p.css("color", k)
            }
        });
        p.html('<option value="-1" zip="">' + s + "</option>");
        var t = "";
        var h = "";
        var x = "";
        var d = "",
        w = "",
        f = "";
        var e = "";
        var A = "";
        var z = function() {
            t = q.val().trim();
            var C = /^[1-9][0-9]+(\.[0-9]{1,2})?$/;
            if (t == "") {
                b("请填写提现金额");
                return false
            } else {
                if (!C.test(t)) {
                    b("请填写正确的提现金额");
                    return false
                } else {
                    if (parseFloat(t) < 100 || parseFloat(t) > j) {
                        b("提现金额为100-" + parseInt(j) + "之间哦！");
                        return false
                    }
                }
            }
            var B = /^[\u0391-\uFFE5]+$/;
            h = y.val().trim();
            if (h == "") {
                b("请填写开户人");
                return false
            } else {
                if (!B.test(h)) {
                    b("开户人只能填写中文");
                    return false
                }
            }
            x = g.val().trim();
            if (x == "") {
                b("请填写银行名称");
                return false
            } else {
                if (!B.test(x)) {
                    b("银行名称只能填写中文");
                    return false
                }
            }
            d = n.find("option:selected").text();
            w = p.find("option:selected").text();
            f = i.val().replace(/\s+/g, "");
            if (d == "" || d == v) {
                b("请选择开户支行所在省");
                return false
            }
            if (w == "" || w == s) {
                b("请选择开户支行所在市");
                return false
            }
            if (f == "") {
                b("请填写开户支行");
                return false
            } else {
                if (!B.test(f)) {
                    b("开户支行只能填写中文");
                    return false
                }
            }
            e = r.val().trim().replace(/[ ]/g, "");
            if (e == "") {
                b("请填写银行账号");
                return false
            } else {
                var E = /^\d{16,19}$/;
                if (!E.test(e)) {
                    b("银行账号为16-19位数字");
                    return false
                }
            }
            A = u.val().trim();
            if (A == "") {
                b("请填写联系电话");
                return false
            } else {
                var D = /(^1+[0-9]{10}$)|(^(0[0-9]{2,3}\-)+([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)/;
                if (!D.test(A)) {
                    b("联系电话格式有误");
                    return false
                }
            }
            return true
        };
        $("#btnSubmit").click(function() {
            if (z()) {
                var D = d + " " + w + " " + f;
                var F = function(H) {
                    var I = function() {
                        var J = "";
                        J += "money=" + t;
                        J += "&userName=" + encodeURI(h);
                        J += "&bankName=" + encodeURI(x);
                        J += "&subBank=" + encodeURI(D);
                        J += "&bankNo=" + e;
                        J += "&phone=" + encodeURI(A);
                        return J
                    };
                    H.addClass("grayBtn").html('正在提交<span class="dotting"></span>');
                    var G = function(J) {
                        $("div.record-pop-ups").remove();
                        $("div.weixin-mask").hide();
                        if (J.code == 1) {
                            c("申请成功");
                            setTimeout(function() {
                                location.href = roots+"/invite/friends"
                            },
                            2000)
                        } else {
                            if (J.code == -1) {
                                b("申请失败,提现金额最低为100元")
                            } else {
                                b("申请失败,请重试")
                            }
                        }
                        H.removeClass("grayBtn").html("确认提交")
                    };
                    $.getJSON(roots+"/user"+"/memberCenterApplyToBank", I(), G)
                };
                var B = new Date();
                var C = '<div class="record-pop-ups gray6 clearfix"><h4>确认提现信息</h4><ul><li><span>申请时间：</span><p>' + (B.getFullYear() + "-" + (B.getMonth() + 1) + "-" + B.getDate()) + '</p></li><li><span>提现金额：</span><p class="orange">￥' + CastMoney(t) + "</p></li><li><span>开&nbsp;&nbsp;户&nbsp;人：</span><p>" + h + "</p></li><li><span>银行名称：</span><p>" + x + "</p></li><li><span>开户支行：</span><p>" + D + "</p></li><li><span>银行账号：</span><p>" + e + "</p></li><li><span>联系电话：</span><p>" + A + '</p></li></ul><div class="comm-pop-btn"><cite class="return-modify"><a id="closeDiag" href="javascript:;" class="gray6">返回修改</a></cite><cite><a id="submitApply" href="javascript:;" class="orangeBtn">确认提交</a></cite></div></div>';
                $("body").attr("style", "overflow:hidden;");
                $("#wrapper").append(C).find("#closeDiag").click(function() {
                    $("div.record-pop-ups").remove();
                    $("div.weixin-mask").hide();
                    $("body").attr("style", "");
                    IsMasked = false
                });
                var E = function() {
                    var G = $("div.record-pop-ups");
                    if (G.length > 0) {
                        var H = ($(window).width() > $(window).height() ? $(window).height() : $(window).width()) - 80;
                        G.css({
                            width: H,
                            top: ($(window).height() - G.height() - 40) / 2,
                            left: ($(window).width() - H - 30) / 2
                        });
                        $("div.weixin-mask").css("height", $(document).height() > $(window).height() ? $(document).height() : $(window).height()).show()
                    }
                };
                E();
                IsMasked = true;
                $(window).resize(E);
                $("#submitApply").click(function() {
                    F($(this))
                })
            }
        })
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104",
    function() {
        a()
    })
});