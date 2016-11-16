$(function () {
    var f = null;
    var d = $("#divLoading");
    var c = $("#ul_list");
    var z = $("#type").val();
    var g = 10;
    var a = {
        FIdx: 1,
        EIdx: g,
        Type: z,
    };
    var b = 0;
    var e = function () {
        var h = function () {
            var i = function () {
                return "/FIdx/" + a.FIdx + "/EIdx/" + a.EIdx + "/Type/" + a.Type;
            };
            var j = function () {
                $.getJSON(roots + "/seller/getSellerShopList" + i()
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
                                    n += '<li id="' + k.id + '">';
                                    n += '<cite><img src="' + Gobal.loadpic + '" src2="' + pub + "/uploads/" + k.thumb + '" />' + "</cite>";
                                    n += "<dl>";
                                    n += '<dt>(第' + k.qishu + "期)" + k.title + "</dt>";
                                    n += '<dd>所属栏目： ' + k.catename + "</dd>";
                                    n += '<dd>参与情况：<em class="orange">' + k.canyurenshu + "/" + k.zongrenshu + "</em>人次</dd>";
                                    n += '<dd>总期数：<em class="">' + k.maxqishu + "</em>期</dd>";
                                    if (k.status == 0) {
                                        n += "<dd><a class='orangeBtn' href='"+roots+"/seller/goods_set_money/id/" + k.id + "'>重置价格</a><a class='orangeBtn' href='"+roots+"/seller/qishu_list/id/" + k.id + "'>查看往期</a></dd>";
                                    } else {
                                        n += "<dd><a class='orangeBtn' href='"+roots+"/seller/goods_del/id/" + k.id + "/type/1' onclick=\"return confirm('是否真的删除该商品！');\">删除</a></dd>";
                                    }
                                    n += "</dl>";
                                    n += "</li>";
                                    n += "</ul>";
                                    var o = $(n);
                                    o.bind("click", function () {
                                        location.href = roots+"/seller/goods_edit/id/" + $(this).attr("id") + "/type/" + a.Type;
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
