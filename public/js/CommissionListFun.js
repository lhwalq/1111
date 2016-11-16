$(function () {
    var reg = new RegExp("(^|&)" + "status" + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var rs = window.location.search.substr(1).match(reg);  //匹配目标参数
    var status = rs ? unescape(rs[2]) : "";
    var a = function () {
        var d = $("#divList");
        var e = $("#divLoading");
        var f = 0;
        var h = 20;
        var b = {
            FIdx: 1,
            EIdx: h,
            isCount: 1,
            status: status
        };
        var g = null;
        var c = function () {
            var i = function () {
                return "type=1&FIdx=" + b.FIdx + "&EIdx=" + b.EIdx + "&isCount=" + b.isCount + "&status=" + b.status
            };
            var j = function () {
                $.getJSON(roots + "/invite/getcommissionlist", i(),
                        function (p) {
                            if (p.code == 0) {
                                if (b.isCount == 1) {
                                    b.isCount = 0;
                                    f = p.str.totalCount
                                }
                                var o = p.str.listItems;
                                var n = o.length;
                                var k = "";
                                for (var l = 0; l < n; l++) {
                                    var m = (parseInt(o[l].logType) == 1 || parseInt(o[l].logType) == 3) ? "+" : "";
                                    k += '<dd><span><a href="' + roots + '/mobile/userindex/id/' + o[l].userWeb + '" class="blue">';
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
                                            //alert(Gobal.NoneHtmlEx(p.tips));
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
            this.getInitPage = function () {
                j()
            };
            this.getNextPage = function () {
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