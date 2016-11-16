$(function() {
    var a = function() {
        var b = function(n) {
            $.PageDialog.fail(n)
        };
        var f = function(n) {
            $.PageDialog.ok(n)
        };
        var d = $("#hidGoodsID").val();
        if (d > 0) {
            var m = false;
            var g = $("#a_sc");
            var j = function() {
                if (m) {
                    return
                }
                m = true;
                var n = function(o) {
                    if (o.code == 0) {
                        f("已关注");
                        g.addClass("z-foot-fansed").unbind("click").bind("click",
                        function() {
                            e()
                        })
                    } else {
                        if (o.code == 1) {
                            if (o.num == -1) {
                                b("关注失败，商品不存在！");
                                location.reload()
                            } else {
                                if (o.num == -2) {
                                    g.addClass("z-foot-fansed").unbind("click").bind("click",
                                    function() {
                                        e()
                                    })
                                }
                            }
                        } else {
                            if (o.code == 10) {
                                location.href = "http://weixin.1yyg.com/Passport/login.do?forward=" + escape(location.href)
                            } else {
                                b("关注失败，请重试！");
                                location.reload()
                            }
                        }
                    }
                    m = false
                };
                GetJPData("http://api.1yyg.com", "addCollectGoods", "goodsID=" + d, n)
            };
            var e = function() {
                if (m) {
                    return
                }
                m = true;
                var n = function(o) {
                    if (o.code == 0) {
                        f("已取消关注");
                        g.removeClass("z-foot-fansed").unbind("click").bind("click",
                        function() {
                            j()
                        })
                    } else {
                        b("取消关注失败，请重试！");
                        location.reload()
                    }
                    m = false
                };
                GetJPData("http://api.1yyg.com", "delCollectGoods", "goodsID=" + d, n)
            };
            var h = function(n) {
                if (n.code == 0) {
                    g.addClass("z-foot-fansed").unbind("click").bind("click",
                    function() {
                        e()
                    })
                } else {
                    g.bind("click",
                    function() {
                        j()
                    })
                }
            };
            
        }
        var c = "啊！城会玩儿！" + $("#hidBuyNum").val() + "块钱就可以买到这货！";
        var k = $("#hidShareDesc").val();
        var l = "http://weixin.1yyg.com/lottery/detail-" + $("#hidCodeID").val() + ".do";
        var i = $("#hidShareImg").val();
        wxShareFun({
            shareLink: l,
            shareImg: i,
            shareDesc: k,
            shareTitle: c,
            showMask: false
        });
        $("#btnShare").bind("click",
        function() {
            wxShowMaskFun();
            return false
        })
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104",
    function() {
        Base.getScript(pub+"/JS/WxShare.js?v=151104", a)
    })
});