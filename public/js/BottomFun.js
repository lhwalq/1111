var Gobal = new Object();
function GetJPData(d, c, a, b) {
    $.getJSON(d + "/JPData?action=" + c + "&" + a + "&fun=?", b)
}
function GetDominData(d, c, b, a) {
    $.getJSON(d + "/JPData?action=" + c + "&fun=?", {
        parms: b
    },
    a)
}
function loadImgFun(c) {
    var b = $("#loadingPicBlock");
    if (b.length > 0) {
        var i = "src2";
        Gobal.LoadImg = b.find("img[" + i + "]");
        var a = function() {
            return $(window).scrollTop()
        };
        var e = function() {
            return $(window).height() + a() + 50
        };
        var h = function() {
            Gobal.LoadImg.each(function(j) {
                if ($(this).offset().top <= e()) {
                    var k = $(this).attr(i);
                    if (k) {
                        $(this).attr("src", k).removeAttr(i).show()
                    }
                }
            })
        };
        var d = 0;
        var f = -100;
        var g = function() {
            d = a();
            if (d - f > 50) {
                f = d;
                h()
            }
        };
        if (c == 0) {
            $(window).bind("scroll", g)
        }
        g()
    }
}
var IsMasked = false;
var addNumToCartFun = null;
var _IsLoading = false;
function scrollForLoadData(a) {
    $(window).scroll(function() {
        var c = $(document).height();
        var b = $(window).height();
        var d = $(document).scrollTop() + b;
        if (c - d <= b * 4) {
            if (!_IsLoading && a) {
                _IsLoading = true;
                a()
            }
        }
    })
} (function() {
		var Path = new Object();
    Gobal.Skin = Path.Skin;
    Gobal.LoadImg = null;
    Gobal.LoadHtml = '<div class="loadImg">正在加载</div>';
    Gobal.LoadPic = "/style/images/loading.gif?v=130820";
    Gobal.NoneHtml = '<div class="noRecords colorbbb clearfix"><s></s>暂无记录</div>';
    Gobal.NoneHtmlEx = function(b) {
        return '<div class="noRecords colorbbb clearfix"><s></s>暂无记录 <div class="z-use"></div></div>'
    };
    Gobal.LookForPC = '<div class="g-suggest clearfix"></div>';
    Gobal.ErrorHtml = function(b) {
        return '<div class="g-suggest clearfix">抱歉，加载失败，请重试[' + b + "]</div>"
    };
    Gobal.unlink = "javascript:void(0);";
    loadImgFun(0);
    var a = function() {
        var j = $("#btnCart");
        if (j.length > 0) {
             $.getJSON(roots + "/goods/cartnum",
            function(m) {
                if (m.code == 0 && m.num > 0) {
                    j.find("i").html(m.num > 99 ? '<b class="tomore" num="' + m.num + '">...</b>': "<b>" + m.num + "</b>")
                }
            })
        }
        addNumToCartFun = function(m) {
            j.find("i").html(m > 99 ? '<b class="tomore" num="' + m + '">...</b>': "<b>" + m + "</b>")
        };
        var i = function(n) {
            var m = new Date();
            n.attr("src", n.attr("data") + "?v=" + GetVerNum()).removeAttr("id").removeAttr("data")
        };
        var f = $("#pageJS", "head");
        if (f.length > 0) {
            i(f)
        } else {
            f = $("#pageJS", "body");
            if (f.length > 0) {
                i(f)
            }
        }
        document.body.addEventListener("touchmove",
        function(m) {
            if (IsMasked) {
                m.preventDefault()
            }
        },
        false);
        var e = $("body").attr("fnav");
        if (e == "1" || e == "2" || e == "3") {
            var k = true;
            var g = '<div id="div_fastnav"  class="fast-nav-wrapper">';
            g += '<ul class="fast-nav">';
            if (e != "3") {
                g += '<li id="li_menu"><a href="javascript:;"><i class="nav-menu"></i></a></li>'
            }
            if (e != "2") {
                g += '<li id="li_top" style="display:none;"><a href="javascript:;"><i class="nav-top"></i></a></li>'
            }
            g += "</ul>";
            if (e != "3") {
                g += '<div class="sub-nav" style="display:none;">';
                g += '<a href="'+roots+'/Mobile/index"><i class="home"></i>云购</a>';
                g += '<a href="'+roots+'/Mobile/lottery"><i class="announced"></i>最新揭晓</a>';
                g += '<a href="'+roots+'/mobile/shaidan/"><i class="single"></i>晒单</a>';
                g += '<a href="'+roots+'/user/login"><i class="personal"></i>我</a>';
                g += "</div>"
            }
            g += "</div>";
            var l = $("#div_fastnav");
            if (l.length == 0) {
                l = $(g)
            }
            if (e != "3") {
                var c = $(".sub-nav", l);
                var b = $("#li_menu", l);
                var d = null;
                b.bind("click",
                function() {
                    if (k == false) {
                        return
                    }
                    if (d != null) {
                        clearTimeout(d)
                    }
                    if ($(this).attr("isshow") == "1") {
                        c.fadeOut("fast");
                        $(this).attr("isshow", "0")
                    } else {
                        c.fadeIn("fast",
                        function() {
                            d = setTimeout(function() {
                                c.fadeOut("fast");
                                b.attr("isshow", "0")
                            },
                            5000)
                        });
                        $(this).attr("isshow", "1")
                    }
                });
                l.bind("click",
                function(m) {
                    stopBubble(m)
                });
                $("html").bind("click",
                function() {
                    c.fadeOut("fast");
                    b.attr("isshow", "0")
                })
            }
            if (e != "2") {
                var h = $("#li_top", l);
                h.bind("click",
                function() {
                    $(this).hide();
                    $("body,html").animate({
                        scrollTop: 0
                    },
                    500)
                });
                $(window).scroll(function() {
                    if ($(window).scrollTop() > 100) {
                        h.show()
                    } else {
                        h.hide()
                    }
                })
            }
            l.appendTo("body")
        }
        $("div.footer").find("a").each(function() {
            $(this).bind("click",
            function() {
                $(this).addClass("active")
            })
        })
    };
    Base.getScript(pub+ "/JS/Comm.js?v=151202", a);
})();