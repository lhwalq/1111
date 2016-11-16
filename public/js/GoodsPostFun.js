$(function () {
    var h = null;
    var g = $("#hidgoodsid").val();
    var d = $("#divLoading");
    var c = $("#ul_list");
    var b = 20;
    var f = 0;
    var a = {
        goodsid: g,
        fIdx: 1,
        eIdx: b,
        isCount: 1
    };
    var e = function () {
        var j = function (m) {
            $.PageDialog('<div class="Prompt">' + m + "</div>", {
                W: 150,
                H: 45,
                close: false,
                autoClose: true,
                submit: function () {
                    location.reload()
                }
            })
        };
        var k = function (m) {
            $.PageDialog.fail(m)
        };
        var l = function (m) {
            $.PageDialog.ok(m)
        };
        var i = function () {
            var m = function () {
                return "goodsid=" + a.goodsid + "&fIdx=" + a.fIdx + "&eIdx=" + a.eIdx + "&isCount=" + a.isCount
            };
            var n = function () {
                //  GetJPData("", "getGoodsPostList", m(),
                $.getJSON(roots + "/mobile/shaidan2", m(),
                        function (u) {
                            //alert(u.code);
                            if (u.code == 0) {
                                var t = u.Data;
                                if (a.isCount == 1) {
                                    f = u.Count;
                                    $("#div_title").html('已有<em class="gray6">' + u.Count + '</em>位幸运用户晒单<span>总共<em class="orange">' + u.CountEx + "</em>条评论</span>").show();
                                    a.isCount = 0
                                }
                                var s = t.length;
                                for (var r = 0; r < s; r++) {
                                    var p = "";
                                    var q = t[r];
                                    var o = q.postAllPic.split(",");
                                    //alert(o[0]);
                                    p += "<li postid=" + q.postid + ">";
                                    p += '<p class="fl"><img  src="' + Gobal.LoadPic + '" src2=' + pub + '"/uploads/' + o[0] + '"></p>';
                                    p += "<dl>";
                                    p += '<dt><a href="' + roots + '/mobile/userindex/id/' + q.userweb + '" class="blue">' + q.username + "</a></dt>";
                                    p += '<dd class="gray6">' + q.posttitle + "</dd>";
                                    p += '<dd class="gray9">' + q.postContent + "</dd>";
                                    p += '<dd class="colorbbb">' + q.postTimeEx + "</dd>";
                                    p += "</dl>";
                                    p += "</li>";
                                    var v = $(p);
                                    v.click(function () {
                                        location.href = roots + "/mobile/detail/id/" + $(this).attr("postid")
                                    });
                                    c.append(v)
                                }
                                if (a.eIdx < f) {
                                    _IsLoading = false
                                } else {
                                    d.hide()
                                }
                                loadImgFun(0)
                            } else {
                                d.hide();
                                if (a.fIdx == 1) {
                                    _IsLoading = true;
                                    c.html(Gobal.NoneHtml)
                                }
                            }
                        })
            };
            this.getFirstPage = function () {
                n()
            };
            this.getNextPage = function () {
                a.fIdx += b;
                a.eIdx += b;
                n()
            }
        };
        h = new i();
        h.getFirstPage();
        scrollForLoadData(h.getNextPage)
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104", e)
});