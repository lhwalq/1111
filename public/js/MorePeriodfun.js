$(function () {
    var e = null;
    var b = $("#hidGoodsID").val();
    var c = $("#winList");
    var a = $("#divLoading");
    var f = 18;
    var h = 0;
    var j = 0;
    var d = 0;
    var g = {
        goodsID: b,
        period: j,
        FIdx: 1,
        EIdx: f,
        isCount: 1
    };
    var i = function () {
        var n = function (r) {
            $.PageDialog('<div class="Prompt">' + r + "</div>", {
                W: 150,
                H: 45,
                close: false,
                autoClose: true,
                submit: function () {
                    location.reload()
                }
            })
        };
        var o = function (r) {
            $.PageDialog.fail(r)
        };
        var q = function (r) {
            $.PageDialog.ok(r)
        };
        var m = function () {
            var r = function () {
                return "goodsID=" + g.goodsID + "&period=" + g.period + "&FIdx=" + g.FIdx + "&EIdx=" + g.EIdx + "&isCount=" + g.isCount
            };
            var s = function () {
                $.getJSON(roots + "/goods/moreAjax/", r(),
                        function (A) {
                            if (A.code == 0) {
                                var y = A.listItems;
                                if (g.isCount == 1) {
                                    h = A.count;
                                    d = parseInt(y[0].codePeriod);
                                    g.isCount = 0
                                }
                                j = A.minPeriod;
                                var B = y.length;
                                var C = 0;
                                var D = 0;
                                var t = 0;
                                var F = 0;
                                var u = function (H) {
                                    var I = H;
                                    var G = H.split(" ");
                                    if (G.length > 0) {
                                        if (!isNaN(G[0].replace(/-/g, ""))) {
                                            I = G[0]
                                        }
                                    }
                                    return I
                                };
                                for (var z = 0; z < B; z++) {
                                    var v = "";
                                    var x = y[z];
                                    var E = parseInt(x.codeState);
                                    if (E == 1) {
                                        C = parseInt(x.codeSales);
                                        D = parseInt(x.codeQuantity);
                                        F = parseInt(C * 100) / D;
                                        F = C > 0 && F < 1 ? 1 : F;
                                        v += '<li class="have-in-hand" codeID=' + x.codeID + ">";
                                        v += "<cite>第" + x.codePeriod + "期</cite>";
                                        v += '<div class="win-con">';
                                        v += '<div class="during-pic"><img src="' + Gobal.LoadPic + '" src2="' + pub + "/uploads/" + x.goodsPic + '"></div>';
                                        v += '<h4 class="orange">进行中<span class="dotting"></span></h4>';
                                        v += '<p class="u-progress" title="已完成' + F + '%">';
                                        v += '<span class="pgbar" style="width:' + F + '%;">';
                                        v += '<span class="pging"></span>';
                                        v += "</span>";
                                        v += "</p>";
                                        v += "</div>";
                                        v += "</li>"
                                    } else {
                                        if (E == 2) {
                                            v += "<li codeID=" + x.codeID + ">";
                                            v += "<cite>第" + x.codePeriod + "期</cite>";
                                            v += '<div class="win-con">';
                                            v += '<h4 class="orange">正在揭晓</h4>';
                                            v += '<div class="loading-progress"><span class="loading-pgbar"><span class="loading-pging"></span></span></div>';
                                            v += '<h5 class="gray9">敬请期待</h5>';
                                            v += "</div>";
                                            v += "</li>"
                                        } else {
                                            if (E == 3) {
                                                v += "<li codeID=" + x.codeID + ">";
                                                v += "<cite>第" + x.codePeriod + "期</cite>";
                                                v += '<dl class="gray9">';
                                                v += '<dt><img src="' + Gobal.LoadPic + '" src2="' + pub + '/uploads/' + x.userPhoto + '"></dt>';
                                                v += '<dd class="win-name"><a href="javascript:;" class="blue">' + x.userName + "</a></dd>";
                                                v += '<dd class="z-font-size">幸运码：<em class="orange">' + x.codeRNO + "</em></dd>";
                                                v += '<dd class="z-font-size"> 参与人次：<em class="orange">' + x.buyNum + "</em></dd>";
                                                v += '<dd class="colorbbb">' + u(x.codeRTime) + "</dd>";
                                                v += "</dl>";
                                                v += "</li>"
                                            }
                                        }
                                    }
                                    var w = $(v);

                                    w.click(function () {
                                        location.href = roots + "/goods/items/goodsId/" + $(this).attr("codeID")
                                    });
                                    c.append(w)
                                }
                                if (g.EIdx < h) {
                                    _IsLoading = false
                                } else {
                                    _IsLoading = true;
                                    a.hide()
                                }
                                loadImgFun(0)
                            } else {
                                a.hide();
                                if (g.FIdx == 1) {
                                    _IsLoading = true;
                                    c.html(Gobal.NoneHtml)
                                }
                            }
                        })
            };
            this.getInitPage = function () {
                g.FIdx = 1;
                g.EIdx = f;
                g.isCount = 1;
                c.empty();
                s()
            };
            this.getFirstPage = function () {
                s()
            };
            this.getNextPage = function () {
                g.FIdx += f;
                g.EIdx += f;
                s()
            }
        };
        e = new m();
        e.getFirstPage();
        var k = $("#btnGo");
        var l = $("#txtPeriod");
        var p = "";
        l.focus(function () {
            if ($(this).val().trim() == "请输入数字") {
                $(this).css({
                    color: "#666666",
                    "font-size": "14px"
                }).val("")
            }
            k.addClass("z-active")
        }).blur(function () {
            if ($(this).val().trim() == "") {
                $(this).css({
                    color: "#bbbbbb",
                    "font-size": "12px"
                }).val("请输入数字")
            }
            k.removeClass("z-active")
        }).bind("keyup",
                function () {
                    if (isNaN($(this).val().trim())) {
                        $(this).val(p)
                    } else {
                        p = $(this).val().trim()
                    }
                });
        k.bind("click",
                function () {
                    if (l.val() == "" || d == 0) {
                        return
                    }
                    var s = parseInt(l.val());
                    if (isNaN(s)) {
                        return
                    }
                    if (s > 0 && s <= d) {
                        var r = function (t) {
                            if (t.code == 0) {
                                location.replace(roots + "/goods/items/goodsId/" + t.codeID);
                            }
                        };
                        $.getJSON(roots + "/goods/getGoodsPeriodInfo", "goodsID=" + b + "&period=" + s, r)
                    } else {
                        o("查无记录");
                        l.css({
                            color: "#bbbbbb",
                            "font-size": "12px"
                        }).val("请输入数字")
                    }
                });
        scrollForLoadData(e.getNextPage)
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104", i)
});