$(function() {
    var a = function() {
        var d = $("#divInviteList");
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
                return "FIdx=" + b.FIdx + "&EIdx=" + b.EIdx + "&isCount=" + b.isCount
            };
            var j = function() {
                $.getJSON("/mobile"+"/ajax/getinvitelist3", i(),
                function(p) {
                    if (p.code == 0) {
                        if (b.isCount == 1) {
                            b.isCount = 0;
                            f = p.str.totalCount
                        }
                        var o = p.str.listItems;
                        var n = o.length;
                        var l = "";
                        for (var m = 0; m < n; m++) {
                            var k = parseInt(o[m].state) == 1 ? "已消费": "未消费";
                            l += '<dd><span><a href="/mobile/mobile/userindex/' + o[m].userWeb + '" class="blue">';
                            l += "<em>" + o[m].userName + "</em></a></span><span>" + o[m].regTime + "</span><span>" + o[m].userCode + "</span><span>" + k + "</span></dd>"
                        }
                        d.append(l);
                        if (b.EIdx < f) {
                            _IsLoading = false
                        } else {
                            e.hide();
                            if (f > 0) {
                                $("#wrapper").append(Gobal.LookForPC)
                            }
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