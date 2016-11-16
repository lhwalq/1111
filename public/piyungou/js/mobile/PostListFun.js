$(function() {
    var b = $("#postBox10");
    var o = $("#postBox20");
    var k = $("#postBox30");
    var l = $("#postLoading");
    var p = $("#btnLoadMore");
    var n = 10;
    var e = 10;
    var m = null;
    var q = false;
    var j = false;
    var i = false;
    var g = false;
    var d = {
        FIdx: 1,
        EIdx: n,
        isCount: 1
    };
    var c = {
        FIdx: 1,
        EIdx: n,
        isLoaded: 0
    };
    var a = {
        FIdx: 1,
        EIdx: n,
        isLoaded: 0
    };
    var f = function(r) {
        if (r && r.stopPropagation) {
            r.stopPropagation()
        } else {
            window.event.cancelBubble = true
        }
    };
    var h = function() {
        var s = 0;
        var r = function() {
            var u = "";
            if (e == 10) {
                u = "FIdx=" + d.FIdx + "&EIdx=" + d.EIdx + "&isCount=" + d.isCount + "&order=10"
            } else {
                if (e == 20) {
                    u = "FIdx=" + c.FIdx + "&EIdx=" + c.EIdx + "&isCount=0&order=20"
                } else {
                    if (e == 30) {
                        u = "FIdx=" + a.FIdx + "&EIdx=" + a.EIdx + "&isCount=0&order=30"
                    }
                }
            }
            return u
        };
        var t = function() {
            p.hide();
            l.show();
           // GetJPData("http://m.1yyg.com", "getPostPageList", r(),

        this.initData = function() {
            t()
        };
        this.getNextPage = function() {
            t()
        }
    };
    m = new h();
    m.initData();
    $("#navBox").children("div").each(function(r) {
        var s = $(this);
        s.click(function() {
            s.addClass("z-sgl-crt");
            s.siblings().removeClass("z-sgl-crt");
            if (r == 0) {
                e = 10;
                b.show();
                o.hide();
                k.hide();
                if (!j) {
                    p.show()
                }
            } else {
                if (r == 1) {
                    e = 20;
                    b.hide();
                    o.show();
                    k.hide();
                    if (c.isLoaded == 0) {
                        m.initData()
                    } else {
                        if (!i) {
                            p.show()
                        }
                    }
                } else {
                    e = 30;
                    b.hide();
                    o.hide();
                    k.show();
                    if (a.isLoaded == 0) {
                        m.initData()
                    } else {
                        if (!g) {
                            p.show()
                        }
                    }
                }
            }
        })
    });
    p.click(function() {
        if (!q) {
            q = true;
            if (e == 10 && !j) {
                d.FIdx += n;
                d.EIdx += n;
                m.getNextPage()
            } else {
                if (e == 20 && !i) {
                    c.FIdx += n;
                    c.EIdx += n;
                    m.getNextPage()
                } else {
                    if (!g) {
                        a.FIdx += n;
                        a.EIdx += n;
                        m.getNextPage()
                    }
                }
            }
        }
    })
});