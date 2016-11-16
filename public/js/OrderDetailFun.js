$(function() {
    var a = function() {
        var d = $("#hideOrderID").val();
        var h = $("#hidOrderState").val();
        var b = 0;
        var c = function(n, m, l, k) {
            $.PageDialog.fail(n, m, l, k)
        };
        var f = function(l, k) {
            $.PageDialog.ok(l, k)
        };
        function j(l, k) {
            $.PageDialog.confirm(l, k)
        }
        if (h == 1) {
            var e = function() {
                if (b > 0) {
                    $.getJSON(roots+"/user/getAddrByID", "cid=" + b,
                    function(k) {
                        if (k.code == 0) {
                            var n = k.data;
                            var l = function() {
                                var p = "";
                                p += '<div class="addnew-inner">';
                                p += '<h3 class="title">确认地址！</h3>';
                                p += '<div class="info">';
                                p += "<p>";
                                p += '<span class="name">' + n[0].contactName + "</span>";
                                p += "<span>" + (n[0].contactMobile == "" ? n[0].contactTel: n[0].contactMobile) + "</span>";
                                p += "</p>";
                                p += "<p>";
                                p += "<span>" + n[0].areaAName + n[0].areaBName + n[0].areaCName + n[0].areaDName + n[0].contactAddress + "</span>";
                                p += "</p>";
                                if (n[0].contactZip != "") {
                                    p += '<p><span class="name">邮编</span><span>' + n[0].contactZip + "</span></p>"
                                }
                                p += "</div>";
                                p += '<div class="btn-wrapper clearfix">';
                                p += '<a id="a_cancle" href="javascript:;" class="btn"><span class="cancle">取消</span></a>';
                                p += '<a id="a_submit" href="javascript:;" class="btn"><span class="submit">确认</span></a>';
                                p += "</div>";
                                p += "</div>";
                                return p
                            };
                            var o = function() {
                                _DialogObj = $("#pageDialog");
                                $("#a_cancle", _DialogObj).click(function() {
                                    m.cancel()
                                });
                                $("#a_submit", _DialogObj).click(function() {
                                    m.cancel();
                                    var p = function(u) {
                                        if (u.code == 0) {
                                            var r = "<p>";
                                            r += '<span class="name">' + n[0].contactName + "</span>";
                                            r += "<span>" + (n[0].contactMobile == "" ? n[0].contactTel: n[0].contactMobile) + "</span>";
                                            r += "</p>";
                                            r += "<p>";
                                            r += "<span>" + n[0].areaAName + n[0].areaBName + n[0].areaCName + n[0].areaDName + n[0].contactAddress + "</span>";
                                            r += "</p>";
                                            $("#div_share").find("#div_addr").html(r);
                                            $("#div_confirm").hide();
                                            $("#div_share").show();
                                            var q = "在吗？看看这是真的嘛，好嗨森好激动！1块钱居然真的可以买到！╮(╯▽╰)╭";
                                            var v = $("#hidShareDesc").val();
                                            var s = "http://weixin.1yyg.com/lottery/detail-" + $("#hidCodeID").val() + ".do";
                                            var t = $("#hidShareImg").val();
                                            wxShareFun({
                                                shareLink: s,
                                                shareImg: t,
                                                shareTitle: q,
                                                shareDesc: v,
                                                showMask: false
                                            });
                                            $("#btnShare").bind("click",
                                            function() {
                                                wxShowMaskFun();
                                                return false
                                            })
                                        } else {
                                            if (u.code == 1) {
                                                c("确认失败，试试修改保存下收货地址咯~")
                                            } else {
                                                if (u.code == 2) {
                                                    c("收货地址不正确")
                                                } else {
                                                    if (u.code == 3) {
                                                        c("收货人姓名不合法")
                                                    } else {
                                                        location.reload()
                                                    }
                                                }
                                            }
                                        }
                                    };
                                    $.getJSON(roots+"/user/confirmAddr", "oid=" + d + "&cid=" + b, p)
                                })
                            };
                            var m = new $.PageDialog(l(), {
                                W: 268,
                                H: 238,
                                close: true,
                                autoClose: false,
                                ready: o
                            })
                        } else {
                            c("获取收货地址失败")
                        }
                    })
                } else {
                    c("请选择收货地址")
                }
            };
            var i = $("#btnUpdateAddr");
            var g = $("#btnConfimAddr");
            $(".addre-list").children("li").each(function() {
                var k = $(this);
                if (k.find("i.z-set").is(":visible")) {
                    b = parseInt(k.attr("id"));
                    i.attr("href", "addressedit-" + d + ".do?cid=" + b)
                }
                k.click(function() {
                    b = parseInt(k.attr("id"));
                    k.find("i.z-set").show();
                    k.siblings().find("i.z-set").hide();
//					alert(b);
                    $("#btnUpdateAddr").attr("href", roots+"/user/orderdetail/ised/19884520/crodid/" + d);
                })
            });
            g.bind("click",
            function() {
                e()
            })
        } else {
            if (h == 3) {
                $("#btnShiped").click(function() {
                    var k = function() {
                        var l = function(m) {
                            if (m.code == 0) {
                                f("提交成功",
                                function() {
                                    location.replace(location.href)
                                })
                            } else {
                                if (m.code == 1) {
                                    c("提交失败，请重试")
                                } else {
                                    location.reload()
                                }
                            }
                        };
                        GetJPData("", "confirmShiped", "oid=" + d, l)
                    };
                    j("您确定收到货了吗？", k)
                })
            }
        }
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104",
    function() {
        Base.getScript(pub+"/JS/WxShare.js?v=151104", a)
    })
});