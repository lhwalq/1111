$(function() {
    var a = function() {
        var d = $("#divList");
        var e = $("#divLoading");
        var f = 0;
        var h = 20;
        var b = {
            FIdx: 1,
            EIdx: h,
            isCount: 1
        };
        var g = null;
        var c = function() {
            var i = function() {
                return "type=1&FIdx=" + b.FIdx + "&EIdx=" + b.EIdx + "&isCount=" + b.isCount
            };
            var j = function() {
                $.getJSON("/mobile"+"/ajax/getcommissionlist3", i(),
                function(p) {
                    if (p.code == 0) {
                        if (b.isCount == 1) {
                            b.isCount = 0;
                            f = p.str.totalCount
                        }
                        var o = p.str.listItems;
                        var n = o.length;
                        var k = "";
                        for (var l = 0; l < n; l++) {
                            var m = (parseInt(o[l].logType) == 1 || parseInt(o[l].logType) == 3) ? "+": "";
                            k += '<dd><span><a href="/mobile/mobile/userindex/' + o[l].userWeb + '" class="blue">';
                            k += "<em>" + o[l].userName + "</em></a></span><span>" + o[l].buyTime + "</span><span>" + o[l].buyMoney + "</span><span>" + m + "" + o[l].brokerage + "</span></dd>"
                        }
                        d.append(k);
                        if (b.EIdx < f) {
                            _IsLoading = false
                        } else {
                            if (f > 0) {
                                $("#wrapper").append(Gobal.LookForPC)
                            }
                            e.hide()
                        }
                    } else {
                        if (p.code == 10) {
                            location.reload()
                        } else {
                            if (p.code == -1) {
                                e.hide();
                                if (b.FIdx == 1) {
                                    d.html(Gobal.NoneHtmlEx(p.tips))
                                }
                            } else {
                                e.hide();
                                d.html(Gobal.ErrorHtml(p.code));
                                _IsLoading = false
                            }
                        }
                    }
                })
            };
            this.getInitPage = function() {
                j()
            };
            this.getNextPage = function() {
                b.FIdx += h;
                b.EIdx += h;
                j()
            }
        };
        g = new c();
        g.getInitPage();
        scrollForLoadData(g.getNextPage)
    };
    a()
});