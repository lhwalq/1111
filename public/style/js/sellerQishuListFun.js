$(function () {
    var f = null;
    var d = $("#divLoading");
    var c = $("#ul_list");
    var z = $("#type").val();
    var g = 10;
    var a = {
        FIdx: 1,
        EIdx: g,
        Shopid: z,
    };
    var b = 0;
    var e = function () {
        var h = function () {
            var i = function () {
                return "/FIdx/" + a.FIdx + "/EIdx/" + a.EIdx + "/Shopid/" + a.Shopid;
            };
            var j = function () {
                $.getJSON(roots+"/seller/getQishuList" + i()
                        , function (q) {
                            if (q.code == 1) {
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
                                    var y = "";
                                    t = parseInt(k.canyurenshu);
                                    u = parseInt(k.zongrenshu);
                                    l = parseInt(u - t);
                                    v = parseInt(t * 100) / u;
                                    v = t > 0 && v < 1 ? 1 : v;
//                                    alert(k['q_end_time']);
                                    if (k['q_end_time'] != null && k['q_showtime'] == 'N') {
                                        y = "已揭晓";
                                    } else if (k['q_end_time'] != null && k['q_showtime'] == 'Y') {
                                        y = "已满员";
                                    } else {
                                        y = "进行中";
                                    }
                                    n += '<li id="' + k.id + '" class="in_progress">';
                                    n += '<a href="javascript:;">';
                                    n += '<cite><img src="' + Gobal.loadpic + '" src2="'+pub+'/uploads/' + k.thumb + '" /><i>'+ y+'</i>' + (p[r].codeType == 3 ? '<div class="pPurchase">限购</div>' : "") + "</cite>";
                                    n += "<dl>";
                                    n += "<dt>(第" + k.qishu + "期)" + k.title + "</dt>";
                                    n += '<dd>已参与<em class="orange">' + k.canyurenshu + "</em>人次</dd>";
                                    n += "<dd>";
                                    n += '<div class="gRate short" style="border-right: 0px solid #ddd;padding-right: 0px;">';
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
                                    //n += '<a href="javascript:;" goodsname="' + k.goodsname + '" goodsPic="/love/uploads/' + k.goodsPic + '" codeid="' + k.codeid + '" class="z-set-wrap"><div class="z-set"></div>分享</a>';
                                    n += "</li>"
                                    var o = $(n);
                                    o.bind("click", function () {
                                        location.href = roots+"/seller/goods_go_one/id/" + $(this).attr("id");
                                    });
                                    c.append(o);
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
    Base.getScript(pub+"/style/js/pageDialog.js?v=151104", function () {
        Base.getScript(pub+"/style/js/WxShare.js?v=151104", e)
    })
});
