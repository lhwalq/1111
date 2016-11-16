$(function() {
	
    var a = function(n) {
        $.PageDialog.fail(n)
    };
    var d = function(n) {
        $.PageDialog.ok(n)
    };
    var c = $("#hidgoodsid").val();
    var m = $("#hidCodeID").val();
    var j = $("#hidShowTime").val() == "1";
	//alert($("#hidShowTime").val());
    if (j) {
		//alert(789);
        Base.getScript(pub+"/JS/GoodsTimeFun.js?v=151106",
        function() {
			
            $("#divLotteryTime").StartTimeOut(m)
				
        })
    } else {
        var g = function(o) {
            var n = "shopNum=1&codeid=" + m;
           // GetJPData("", "addShopCart", n,
           $.getJSON(roots+"/Goodszg/addShopCart/"+"addShopCart", n,
			  
            function(p) {
				 //exit;
                if (p.code == 0) {
                    if (o == 1) {
                        addNumToCartFun(p.num);
                        d("添加成功")
                    } else {
                       location.href = roots+"/Goodszg/cartlistzg/"
                    }
                } else {
                    if (p.code == 1) {
                        location.reload()
                    } else {
                        if (p.code == 4) {
                            a("您参与人次已达上限！")
                        } else {
                            if (o == 1) {
                                a("添加失败")
                            } else {
                                a("添加失败，请重试")
                            }
                        }
                    }
                }
            })
        };
        var i = $(".pro_foot").find("div.btn").find("ul>li");
        i.children("a").eq(0).click(function() {
            g(0)
        });
        i.children("a").eq(1).click(function() {
            g(1)
        });
        Base.getScript(pub+"/style/js/WxShare.js?v=151104",
        function() {
            var n = "对不起，这么晚才告诉你！全部1块钱，随便选！";
            var q = $("#hidShareDesc").val();
            var o = "http://yyygcms.cn/products/" + c + ".do";
            var p = $("#hidShareImg").val();
            wxShareFun({
                shareLink: o,
                shareImg: p,
                shareDesc: q,
                shareTitle: n,
                showMask: false
            });
            Base.getScript(pub+"/JS/WxShare.js?v=151104",
            function() {
                $("#btnShare").bind("click",
                function() {
                    wxShowMaskFun();
                    return false
                })
            })
        })
    }
    if (c > 0) {
        var l = false;
        var e = $("#a_sc");
        var h = function() {
            if (l) {
                return
            }
            l = true;
            var n = function(o) {
                if (o.code == 0) {
                    d("已关注");
                    e.addClass("z-foot-fansed").unbind("click").bind("click",
                    function() {
                        b()
                    })
                } else {
                    if (o.code == 1) {
                        if (o.num == -1) {
                            a("关注失败，商品不存在！");
                            location.reload()
                        } else {
                            if (o.num == -2) {
                                e.addClass("z-foot-fansed").unbind("click").bind("click",
                                function() {
                                    b()
                                })
                            }
                        }
                    } else {
                        if (o.code == 10) {
                            location.href = "http://yyygcms.cn/Passport/login.do?forward=" + escape(location.href)
                        } else {
                            a("关注失败，请重试！");
                            location.reload()
                        }
                    }
                }
                l = false
            };
            GetJPData("http://yyygcms.cn", "addCollectGoods", "goodsid=" + c, n)
        };
        var b = function() {
            if (l) {
                return
            }
            l = true;
            var n = function(o) {
                if (o.code == 0) {
                    d("已取消关注");
                    e.removeClass("z-foot-fansed").unbind("click").bind("click",
                    function() {
                        h()
                    })
                } else {
                    a("取消关注失败，请重试！");
                    location.reload()
                }
                l = false
            };
            GetJPData("http://yyygcms.cn", "delCollectGoods", "goodsid=" + c, n)
        };
        var f = function(n) {
            if (n.code == 0) {
                e.addClass("z-foot-fansed").unbind("click").bind("click",
                function() {
                    b()
                })
            } else {
                e.bind("click",
                function() {
                    h()
                })
            }
        };
        
    }
    var k = $("#PicPostion").find("dd");
    if (k.length == 1) {
        $("div.flex-viewport").find("ul.slides").removeAttr("style");
        $("ul.direction-nav").remove()
    }
    Base.getScript(pub+"/JS/pageDialog.js?v=151104",
    function() {
        Base.getScript(pub+"/JS/GoodsPicSlider.js?v=151104",
        function() {

            $("#sliderBox").picslider()
        })
    })
});