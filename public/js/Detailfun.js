var ReplyFun = null;
$(function() {
    var e = "postAdmire";
    var c = $("#hidPostID").val();
    var b = $("#replyList");
    var f = $("div.loading");
    var a = $("#hidBindMobile").val();
    var d = function() {
        var n = {
            empty: "说点什么吧",
            cmtErr: "评论内容不能少于3个字！",
            contentErr: "评论内容不能多余150个字！",
            subFail: "提交失败！",
            notMine: "亲，不能对自已回复哦！",
            notrade: "亲，参与过云购就可以回复啦！",
            tooFast: "亲，回复太频繁，请稍候！",
            singleOver: "亲，说得太多啦，看看别的吧！",
            totalOver: "亲，说得太多啦，明天再来吧！",
            mobileErr: "亲，需绑定手机才能发表评论哦！"
        };
        var q = function(F) {
            $.PageDialog.ok(F)
        };
        var E = function(F) {
            $.PageDialog.fail(F)
        };
        var A = function(F) {
            return F.replace(/&/ig, "&amp;").replace(/</ig, "&lt;").replace(/>/ig, "&gt;").replace(/\[(\/)?(b|br)\]/ig, "<$1$2>").replace(/\[s:(\d+)\]/ig, '<img src="http://skin.1yyg.com/Images/Emoticons/$1.gif" alt="" />').replace(/\[url=([^\]]*)\]([^\[]+)\[\/url\]/ig, '<a href="$1" target="_blank" class="blue">$2</a>').replace(/\s{2}/ig, "&nbsp;&nbsp;")
        };
        var m = 0;
        var r = function() {
            var H = false;
            var K = false;
            var J = 1;
            var F = 10;
            var G = {
                PostID: c,
                FIdx: J,
                EIdx: F,
                IsCount: 1
            };
            var L = function() {
                var M = "";
                M += "PostID=" + G.PostID;
                M += "&fidx=" + G.FIdx;
                M += "&eidx=" + G.EIdx;
                M += "&iscount=" + G.IsCount;
                return M
            };
            var I = function() {
                var M = function(N) {
                    if (N.code == 0) {
                        if (G.IsCount == 1) {
                            G.IsCount = 0;
                            m = N.count
                        }
                        var Q = N.Rows;
                        var R = Q.length;
                        var O = "";
                        for (var P = 0; P < R; P++) {
                            O += '<div class="mess-list"><a href="http://zz.yyygcms.cn/userpage/' + Q[P].userWeb + '" class="photo"><img src="http://zz.yyygcms.cn/userface/' + Q[P].userPhoto + '" alt="头像"/></a><p class="name"><a href="http://weixin.1yyg.com/userpage/' + Q[P].userWeb + '" class="blue">' + Q[P].replyUserName + '</a><span class="fr time">' + Q[P].replyTime + "</span></p><p>" + A(Q[P].replyContent) + "</p></div>"
                        }
                        b.append(O);
                        if (m > G.EIdx) {
                            _IsLoading = false
                        } else {
                            _IsLoading = true;
                            f.hide()
                        }
                    } else {
                        b.append('<div class="null-mess">沙发耶，还不快坐？</div>');
                        _IsLoading = true;
                        f.hide()
                    }
                };
                GetJPData("http://weixin.1yyg.com", "GetReplyList", L(), M)
            };
            this.getInitPage = function() {
                I()
            };
            this.getNextPage = function() {
                G.FIdx += F;
                G.EIdx += F;
                I()
            }
        };
        ReplyFun = new r();
        ReplyFun.getInitPage();
        scrollForLoadData(ReplyFun.getNextPage);
        var y = parseInt($("#hidHits").val());
        var z = parseInt($("#hidReply").val());
        $("#liZan").find("em").html(y);
        $("#liReply").find("em").html(z);
        var j = "postHits";
        var t = $.cookie(j);
        if (t == null || t == "") {
            t = ","
        }
        var o = $("#liZan");
        var D = function() {
            var F = $('<b class="gray9">已羡慕</b>');
            o.append(F);
            F.fadeTo(2000, 0,
            function() {
                F.remove()
            })
        };
        var x = function() {
            if (c <= 0) {
                return
            }
            $.getJSON("/mobile"+"/shaidan/xianmu", "postid=" + c,
            function(G) {
                if (G.code == 0) {
                    t = t + c + ",";
                    $.cookie(j, t, {
                        expires: 1,
                        path: "/"
                    });
                    F()
                }
            });
            var F = function() {
                var L = o.find("img");
                var G = L.width();
                var H = L.height();
                var K = o.find("span");
                var J = -2;
                var M = -1;
                L.hide();
                var I = $('<img style="display: none" src=' + L.attr("src") + ">").prependTo(K);
                I.css({
                    position: "absolute",
                    left: J + "px",
                    top: M + "px",
                    width: G,
                    height: H,
                    "z-index": 9999
                }).show();
                I.animate({
                    width: G * 2,
                    height: H * 2,
                    left: J - G / 2,
                    top: M - H / 2,
                    opacity: 0
                },
                700,
                function() {
                    I.remove();
                    o.find("em").html(y + 1);
                    o.addClass("current")
                })
            }
        };
        if (t.indexOf("," + c + ",") >= 0) {
            o.bind("click",
            function() {}).addClass("current")
        } else {
            o.bind("click",
            function() {
                if (t.indexOf("," + c + ",") >= 0) {
                    return
                }
                x()
            })
        }
        var B = $("#comment");
        B.html(n.empty);
        var g = B.val();
        var w = false;
        var h = function() {
            var F = 150;
            var H = B.val();
            var G = H.length;
            if (H == n.empty) {
                G = 0
            }
            if (H.length > F) {
                E("评论内容已达上限！");
                B.val(H.substring(0, F))
            }
            $("#p_size").html(G + "/" + F);
            if (w) {
                setTimeout(h, 200)
            }
        };
        B.bind("focus",
        function(G) {
            stopBubble(G);
            w = true;
            var F = $(this).val();
            if (F == g) {
                $(this).val("").attr("style", "color:#666666")
            }
            h()
        }).bind("blur",
        function(G) {
            stopBubble(G);
            w = false;
            var F = $(this).val();
            if (F == "") {
                $(this).val(n.empty).attr("style", "color:#bbbbbb")
            }
        });
        var s = function() {
            var F = self.location.toString();
            location.href = "http://weixin.1yyg.com/passport/login.do?forward=" + encodeURIComponent(F);
            return false
        };
        var k = false;
        var l = function() {
            if (k) {
                return
            }
            var F = B.val();
            if (F == g || F == n.empty) {
                E(n.empty);
                B.focus();
                return
            } else {
                if (F.length < 3) {
                    E(n.cmtErr);
                    return
                } else {
                    if (F.length > 150) {
                        E(n.contentErr);
                        return
                    }
                }
            }
            k = true;
            $.post("http://weixin.1yyg.com/JPData", {
                action: "InsertPostReply",
                postid: c,
                originalContent: F
            },
            function(G, I) {
                if (G.code == 0) {
                    q("评论成功");
                    $("#comment").val("");
                    z++;
                    m++;
                    $("#emReplyNum").html(z);
                    $("#liReply").find("em").html(z);
                    $("div.null-mess").hide();
                    var H = '<div class="mess-list"><a href="http://zz.yyygcms.cn/userpage/' + G.userWeb + '" class="photo"><img src="http://zz.yyygcms.cn/userface/' + G.userPhoto + '" alt="头像"/></a><p class="name"><a href="http://weixin.1yyg.com/userpage/' + G.userWeb + '" class="blue">' + G.replyUserName + '</a><span class="fr time">' + G.replyTime + "</span></p><p>" + F + "</p></div>";
                    b.prepend(H);
                    setTimeout(function() {
                        $("#p_size").html("0/150");
                        $("div.s_comments").hide().prevAll("div").show();
                        $("html,body").animate({
                            scrollTop: i
                        },
                        0)
                    },
                    1000)
                } else {
                    if (G.code == 10) {
                        location.href = "http://m.1yyg.com/passport/login.html?forward=" + self.location.toString()
                    } else {
                        if (G.code == -201) {
                            E(n.mobileErr)
                        } else {
                            if (G.code == -358) {
                                E(n.singleOver)
                            } else {
                                if (G.code == -359) {
                                    E(n.totalOver)
                                } else {
                                    if (G.code == -353) {
                                        E(G.tips)
                                    } else {
                                        if (G.code == -354) {
                                            E(n.notMine)
                                        } else {
                                            if (G.code == -355) {
                                                E(n.notrade)
                                            } else {
                                                if (G.code == -303 || G.code == -356) {
                                                    E(n.tooFast)
                                                } else {
                                                    if (G.code == -301) {
                                                        E(n.cmtErr)
                                                    } else {
                                                        E(n.subFail)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                k = false
            },
            "json")
        };
        var i = 0;
        var C = function() {
            i = $(window).scrollTop();
            var I = $("#userState").val() == "1" ? true: false;
            if (I) {
                if (a == "") {
                    var F = '<div class="clearfix m-round u-tipsEject"><div class="u-tips-txt">亲，需要绑定手机才能发表评论哦！</div><div class="u-Btn"><div class="u-Btn-li"><a href="javascript:;" id="btnMsgCancel" class="z-CloseBtn">取消</a></div><div class="u-Btn-li"><a id="btnMsgOK" href="javascript:;" class="z-DefineBtn">立即绑定</a></div></div></div>';
                    var H = function() {
                        var J = $("#pageDialog");
                        J.find("a.z-DefineBtn").click(function() {
                            location.replace("/member/MobileBind.do?forward=" + escape(location.href));
                            G.close()
                        });
                        J.find("a.z-CloseBtn").click(function() {
                            G.cancel()
                        })
                    };
                    var G = new $.PageDialog(F, {
                        W: 300,
                        H: 126,
                        close: true,
                        autoClose: false,
                        ready: H
                    })
                } else {
                    $("div.s_comments").show().prevAll("div").hide();
                    $("#comment").focus();
                    $("#a_cancel").bind("click",
                    function() {
                        $("div.s_comments").hide().prevAll("div").show();
                        $("html,body").animate({
                            scrollTop: i
                        },
                        0)
                    });
                    $("#a_sendok").bind("click", l)
                }
            } else {
                s()
            }
        };
        $("#liReply").bind("click", C);
        var v = $("h1").html();
        var p = $("p.pro").text().substring(0, 30);
        var u = $("div.txt").find("img").eq(0).attr("src").replace("big", "small");
        Base.getScript(pub+"/JS/WxShare.js?v=151104",
        function() {
            wxShareFun({
                shareTitle: v,
                shareImg: u,
                shareLink: location.href,
                shareDesc: p,
                showMask: false
            });
            $("#liShare").bind("click",
            function() {
                wxShowMaskFun();
                return false
            })
        })
    };
    loadImgFun(0);
    Base.getScript(pub+"/JS/pageDialog.js?v=151104", d)
});