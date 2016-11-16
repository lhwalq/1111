$(function () {
    var f = null;
    var d = $("#divLoading");
    var c = $("#ul_list");
    var g = 10;
    var a = {
        FIdx: 1,
        EIdx: g,
        isCount: 1,
        state: -1
    };
    var b = 0;
    var e = function () {
        var h = function () {
            var i = function () {
                return "/FIdx/" + a.FIdx + "/EIdx/" + a.EIdx + "/isCount/" + a.isCount + "/state/" + a.state
            };
            var j = function () {
                //GetJPData("", "getUserBuyList", i()
                $.getJSON(roots + "/user/getUserBuyListNew" + i()
                        , function (q) {
                            if (q.code == 0) {
                                if (a.isCount == 1) {
                                    b = q.count;
                                    a.isCount = 0
                                }
                                var p = q.listItems;
                                var s = p.length;
                                var t = 0;
                                var u = 0;
                                var l = 0;
                                var v = 0;
                                for (var r = 0; r < s; r++) {
                                    var m = parseInt(p[r].codeState);
                                    var k = p[r];
                                    var n = "";

                                    if (m == 1) {
                                        //alert(k.codequantity);
                                        t = parseInt(k.codesales);
                                        u = parseInt(k.codequantity);
                                        l = parseInt(u - t);
                                        //alert(t);

                                        v = parseInt(t * 100) / u;
                                        v = t > 0 && v < 1 ? 1 : v;
                                        n += '<li codeid="' + k.codeid + '" class="in_progress">';
                                        n += '<a href="javascript:;">';
                                        n += '<cite><img src="' + Gobal.loadpic + '" src2="' + pub + '/uploads/' + k.goodsPic + '" /><i>进行中</i>' + (p[r].codeType == 3 ? '<div class="pPurchase">限购</div>' : "") + "</cite>";
                                        n += "<dl>";
                                        n += "<dt>(第" + k.codeperiod + "云)" + k.goodsname + "</dt>";
                                        n += '<dd>已参与<em class="orange">' + k.buynum + "</em>人次</dd>";
                                        n += "<dd>";
                                        n += '<div class="gRate short">';
                                        n += '<div class="Progress-bar">';
                                        n += '<p class="u-progress"><span style="width:' + v + '%;" class="pgbar"><span class="pging"></span></span></p>';
                                        n += '<ul class="Pro-bar-li">';
                                        n += '<li class="P-bar01"><em>' + t + "</em>已参与</li>";
                                        n += '<li class="P-bar02"><em>' + u + "</em>总需人次</li>";
                                        n += '<li class="P-bar03"><em>' + l + "</em>剩余</li>";
                                        n += "</ul>";
                                        n += "</div>";
                                        n += "</div>";
                                        n += "</dd>";
                                        n += "</dl>";
                                        n += "</a>";
                                        n += '<a href="javascript:;" goodsname="' + k.goodsname + '" goodsPic="' + pub + '/uploads/' + k.goodsPic + '" codeid="' + k.codeid + '" class="z-set-wrap"><div class="z-set"></div>分享</a>';
                                        n += "</li>"
                                    } else {
                                        if (m == 2) {
                                            n += '<li codeid="' + k.codeid + '" class="in_progress">';
                                            n += '<a href="' + roots + '/user/buyDetail/goodsid/' + k.codeid + '">';
                                            n += '<cite><img src="' + Gobal.loadpic + '" src2="' + pub + '/uploads/' + k.goodsPic + '" /><i>已满员</i>' + (p[r].codeType == 3 ? '<div class="pPurchase">限购</div>' : "") + "</cite>";
                                            n += "<dl>";
                                            n += "<dt>(第" + k.codeperiod + "云)" + k.goodsname + "</dt>";
                                            n += '<dd>已参与<em class="orange">' + k.buynum + "</em>人次</dd>";
                                            n += '<dd><span class="z-announced-btn">正在揭晓...</span></dd>';
                                            n += "</dl>";
                                            n += "</a>";
                                            n += "</li>"
                                        } else {
                                            if (m == 3) {
                                                n += '<li codeid="' + k.codeid + '">';
                                                n += '<cite><a href="' + roots + '/user/buyDetail/goodsid/' + k.codeid + '"><img src="' + Gobal.loadpic + '" src2="' + pub + '/uploads/' + k.goodsPic + '" /></a><i>已揭晓</i>' + (p[r].codeType == 3 ? '<div class="pPurchase">限购</div>' : "") + "</cite>";
                                                n += "<dl>";
                                                n += '<dt><a href="' + roots + '/user/buyDetail/goodsid/' + k.codeid + '" class="gray6">(第' + k.codeperiod + "云)" + k.goodsname + "</a></dt>";
                                                n += '<dd>已参与<em class="orange">' + k.buynum + "</em>人次</dd>";
                                                n += '<dd>获得者：<a href="'+roots+'/mobile/userindex/id/' + k.q_uid + '" class="blue">' + k.username + "</a></dd>";
                                                n += "<dd>揭晓时间：<em>" + k.codeRTime + "</em></dd>";
                                                n += "<dd>本期幸运云购码：<em>" + k.q_user_code + "</em></dd>";
                                                n += "</dl>";
                                                n += "</li>"
                                            }
                                        }
                                    }
                                    n += "</ul>";
                                    var o = $(n);
                                    o.bind("click", function () {
                                        location.href = roots + "/user/buyDetail/goodsid/" + $(this).attr("codeid") + ""
                                    }).find("a.z-set-wrap").bind("click", function () {
                                        var w = "想你了，能祝福我吗，1元就可能得到它哦！";
                                        var z = $(this).attr("goodsname");
                                        var x = "http://weixin.1yyg.com/lottery/detail-" + $(this).attr("codeid") + "";
                                        var y = $(this).attr("goodsPic");
                                        wxShareFun({
                                            shareLink: x,
                                            shareImg: y,
                                            shareDesc: z,
                                            shareTitle: w
                                        });
                                        return false
                                    });
                                    c.append(o)
                                }
                                if (a.EIdx < b) {
                                    _IsLoading = false
                                } else {
                                    d.hide();
                                    c.append(Gobal.LookForPC)
                                }
                                loadImgFun(0)
                            } else {
                                d.hide();
                                if (a.FIdx == 1) {
                                    _IsLoading = true;
                                    c.html(Gobal.NoneHtmlEx)
                                }
                            }
                        })
            };
            this.getFirstPage = function () {
                j()
            };
            this.getNextPage = function () {
                a.FIdx += g;
                a.EIdx += g;
                j()
            }
        };
        f = new h();
        f.getFirstPage();
        scrollForLoadData(f.getNextPage)
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104", function () {
        Base.getScript(pub+"/JS/WxShare.js?v=151104", e)
    })
});
