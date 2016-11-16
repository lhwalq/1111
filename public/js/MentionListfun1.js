$(function() {
    var a = function() {
        var d = $("#mentionList");
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
            var k = function() {
                return "type=2&FIdx=" + b.FIdx + "&EIdx=" + b.EIdx + "&isCount=" + b.isCount
            };
            var i = function(n) {
                var m = "";
                    if (n == "4") {
                        m = "审核通过"
                    } else {
                        if (n == "2") {
                            m = "审核失败"
                        } 
                       
                    
                }
                return m
            };
            var j = function(n) {
                var m = $(n).attr("applyID");
                $.getJSON("/mobile"+"/ajax/getBrokerageInfo", "applyID=" + m,
                function(p) {
                    if (p.code == 0) {
                        var o = '<div class="record-pop-ups gray6 clearfix"><h4>提现记录详情</h4><ul><li><span>申请时间：</span><p>' + p.data.applyTime + '</p></li><li><span>提现金额：</span><p class="orange">￥' + p.data.fetchMoney + "</p></li><li><span>提现银行：</span><p>" + p.data.bankName + "</p></li><li><span>开户支行：</span><p>" + p.data.subBank + "</p></li><li><span>银行账号：</span><p>" + p.data.bankAccount + "</p></li><li><span>开&nbsp;&nbsp;户&nbsp;人：</span><p>" + p.data.userName + "</p></li><li><span>申请状态：</span><p>" + i(p.data.applyState) + '</p></li></ul><a id="closeDiag" href="javascript:;" class="orange z-closeBtn">关闭</a></div>';
                        $("body").attr("style", "overflow:hidden;");
                        $("#wrapper").append(o).find("#closeDiag").click(function() {
                            $("div.record-pop-ups").remove();
                            $("div.weixin-mask").hide();
                            $("body").attr("style", "");
                            IsMasked = false
                        });
                        var q = function() {
                            var r = $("div.record-pop-ups");
                            if (r.length > 0) {
                                var s = ($(window).width() > $(window).height() ? $(window).height() : $(window).width()) - 80;
                                r.css({
                                    width: s,
                                    top: ($(window).height() - r.height() - 40) / 2,
                                    left: ($(window).width() - s - 30) / 2
                                });
                                $("div.weixin-mask").css("height", $(document).height() > $(window).height() ? $(document).height() : $(window).height()).show()
                            }
                        };
                        q();
                        IsMasked = true;
                        $(window).resize(q)
                    }
                })
            };
            var l = function() {
                  $.getJSON(roots+"/Invite/getrecord1", k(),
                function(t) {
                    if (t.code == 0) {
                        if (b.isCount == 1) {
                            b.isCount = 0;
                            f = t.str.totalCount
                        }
                        var s = t.str.listItems;
                        var q = s.length;
                        var n = "";
                        for (var o = 0; o < q; o++) {
                            n = "";
                            var r = 0;
							
                            if (s[o].logType2 == "4" && s[o].applyState != "") {
                                n += "<span>" + i(s[o].applyState) + "</span>";
                                n += "<span>详情></span>";
                                r = 1
                            } else {
                                if (s[o].logType2 == "3") {
                                    n += "<span></span>"
                                } else {
                                    if (s[o].logType2 == "2") {
                                        n += "<span>充值成功</span>"
                                    } else {
                                        n += "<span></span>"
                                    }
                                }
                                n += "<span></span>"
                            }
                            var m = s[o].buyTime.split(" ");
                            n = "<dd " + (r == 1 ? 'name="detail" applyID="' + s[o].applyID + '"': "") + "><span>" + m[0] + '</span><span class="orange">￥' + CastMoney(s[o].brokerage) + "</span>" + n + "</dd> ";
                            var p = $(n);
                            if (r == 1) {
                                p.click(function() {
                                    j($(this))
                                })
                            }
                            d.append(p)
                        }
                        if (b.EIdx < f) {
                            _IsLoading = false
                        } else {
                            e.hide();
                            if (f > 0) {
                                $("#wrapper").append(Gobal.LookForPC)
                            }
                        }
                    } else {
                        if (t.code == 10) {
                            location.reload()
                        } else {
                            if (t.code == -1) {
                                e.hide();
                                if (b.FIdx == 1) {
                                    d.html(Gobal.NoneHtmlEx(t.tips))
                                }
                            } else {
                                e.hide();
                                d.html(Gobal.ErrorHtml(t.code));
                                _IsLoading = false
                            }
                        }
                    }
                })
            };
            this.getInitPage = function() {
                l()
            };
            this.getNextPage = function() {
                b.FIdx += h;
                b.EIdx += h;
                l()
            }
        };
        g = new c();
        g.getInitPage();
        scrollForLoadData(g.getNextPage)
    };
    a()
});