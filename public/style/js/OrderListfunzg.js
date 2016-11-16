$(function() {
    var f = null;
    var d = 0;
    var g = 20;
    var a = {
        FIdx: 1,
        EIdx: g,
        isCount: 1
    };
    var c = $("#divLoading");
    var b = $("#ul_list");
    var e = function() {
        var h = function() {
            var i = function() {
                var k = "";
                k += "FIdx=" + a.FIdx;
                k += "&EIdx=" + a.EIdx;
                k += "&isCount=" + a.isCount;
                return k
            };
            var j = function() {
                $.getJSON( roots+"/user/getMemberCenterUserWinListzg", i(),
                function(p) {
                    if (p.code == 0) {
                        if (a.isCount == 1) {
                            d = p.totalCount;
                            a.isCount = 0
                        }
                        var o = p.listItems;
                        var r = o.length;
                        var l = function(w, v) {
                            var u = "";
							
                            if (s == 0) {
								if (o[q].yuanjia!="")
								{
		                                u += '<dd><a href="'+roots+'/user/orderdetailzg/id/' + o[q].codeID + '" class="orangeBtn">完善收货地址</a><a href="excorderdetail/' + o[q].codeID + '"  class="orangeBtn">兑换积分</a></dd>'

								}else{
                                u += '<dd><a href="'+roots+'/user/orderdetailzg/id/' + o[q].codeID + '" class="orangeBtn">完善收货地址</a></dd>'
						
								}
							} else {
                                if (s == 1) {
                                    u += '<dd><a href="'+roots+'/user/orderdetailzg/id/' + o[q].orderNo + '" class="grayBtn">待发货</a></dd>'
                                } else {
                                    if (s == 2) {
                                        u += '<dd><a href="'+roots+'/user/orderdetailzg/id/' + o[q].orderNo + '" class="grayBtn">已发货</a><a href="javascript:qurenshouhuo(' + o[q].orderNo + ')" class="orangeBtn">确认收货</a>'
										if(o[q].leixing==2){
										  u += '<a  class="orangeBtn">卡号：'+ o[q].ka +' </a><a  class="orangeBtn">密码：'+ o[q].mi +' </a></dd>'
										}else{
										  u += '<a  class="orangeBtn">物流公司：'+ o[q].company +' </a><a  class="orangeBtn">快递单号：'+ o[q].company_code +' </a></dd>'
										}
                                    } else {
                                        if (s == 3) {
                                            u += '<dd><a href="'+roots+'/user/orderdetailzg/id/' + o[q].orderNo + '" class="grayBtn">已确认收货</a></dd>'
                                        } else {
                                            if (s == 4) {
                                                if (parseInt(o[q].IsPostSingle) == 0) {
                                                    u += '<dd><a href="'+roots+'/user/orderdetailzg/id/' + o[q].orderNo + '" class="grayBtn">已完成</a></dd>'
                                                } else {
                                                    if (v == 0) {
                                                        u += '<dd><a  class="orangeBtn">已确认收货</a></dd>'
                                                    }
                                                }
                                            } else {
                                                if (s == 11) {
                                                    u += '<dd><a href="'+roots+'/user/orderdetailzg/id/' + o[q].orderNo + '" class="grayBtn">已取消</a></dd>'
                                                } else {
                                                    if (s == -10 && v == 2) {
                                                        u += '<dd><a href="javascript:;" class="grayBtn">超时未确认，已取消领奖资格</a></dd>'
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            return u
                        };
                        for (var q = 0; q < r; q++) {
                            var s = parseInt(o[q].orderState);
                            var k = parseInt(o[q].orderType);
                            var t = parseInt(o[q].orderNo);
							 var t1 = parseInt(o[q].codeIDs);
                            var m = "";
                            if (k == 0) {
								
                                m += '<li orderState="' + s + '" orderType="' + k + '" orderNo="' + t + '" codeIDs="' + t1 + '">';
                                m += '<cite><a href="'+roots+'/goodszg/itemszg/goodsId/' + t1 + '">';
                                m += '<img src="' + Gobal.LoadPic + '" src2="' + o[q].goodsPic + '" />';
                                
                                m += "</a></cite>";
                                m += "<dl>";
                                m += '<dt><a href="'+roots+'/goodszg/itemszg/goodsId/' + t1 + '" class="gray6">' + o[q].goodsName + "</a></dt>";
                                m += "<dd>销量：<em>" + o[q].canyurenshu + "</em></dd>";
                                m += "<dd>库存：<em>" + o[q].shenyurenshu + "</em></dd>";
                              
                                m += l(s, k);
                                m += "</dl>";
                                if (s != 11) {
                                    m += '<a goodsName="' + o[q].goodsName + '" goodsPic="' + o[q].goodsPic + '" codeID="' + o[q].codeID + '" href="javascript:;" class="z-set-wrap"><div class="z-set"></div>分享</a>'
                                }
                                m += "</li>"
                            } else {
                                if (k == 1 || k == 3) {
                                    m += '<li orderState="' + s + '" orderType="' + k + '" orderNo="' + t + '">';
                                    m += '<cite><a href="javascript:;">';
                                    m += '<img src="' + Gobal.LoadPic + '" src2="' + o[q].goodsPic + '" />';
                                    m += '<div class="pExchange">换货</div>';
                                    m += "</a></cite>";
                                    m += "<dl>";
                                    m += '<dt><a href="javascript:;" class="gray6">' + o[q].goodsName + "</a></dt>";
                                    m += "<dd>订单编号：<em>" + o[q].orderNo + "</em></dd>";
                                    m += l(s, k);
                                    m += "</dl>";
                                    m += "</li>"
                                } else {
                                    if (k == 2) {
                                        m += '<li orderState="' + s + '" orderType="' + k + '" orderNo="' + t + '">';
                                        m += '<cite><a href="javascript:;">';
                                        m += '<img src="' + Gobal.LoadPic + '" src2="' + o[q].goodsPic + '" />';
                                        m += '<div class="pActivity">活动</div>';
                                        m += "</a></cite>";
                                        m += "<dl>";
                                        m += '<dt><a href="javascript:;" class="gray6">' + o[q].goodsName + "</a></dt>";
                                        m += "<dd>参与活动：<em>" + o[q].orderActDesc + "</em></dd>";
                                        m += "<dd>获得时间：<em>" + o[q].orderAddTime + "</em></dd>";
                                        m += "<dd>订单编号：<em>" + o[q].orderNo + "</em></dd>";
                                        m += l(s, k);
                                        m += "</dl>";
                                        m += "</li>"
                                    }
                                }
                            }
                            var n = $(m);
                            if (k == 0) {
                                n.find("a.z-set-wrap").bind("click",
                                function(y) {
                                    stopBubble(y);
                                    var u = "在吗？看看这是真的嘛，好嗨森好激动！1块钱居然真的可以买到！╮(╯▽╰)╭";
                                    var x = $(this).attr("goodsName");
                                    var v = "http://yyygcms.cn/lottery/detail-" + $(this).attr("codeID") + ".do";
                                    var w = $(this).attr("goodsPic");
                                    wxShareFun({
                                        shareLink: v,
                                        shareImg: w,
                                        shareTitle: u,
                                        shareDesc: x
                                    });
                                    return false
                                })
                            }
                            n.bind("click",
                            function() {
                                if ($(this).attr("orderType") == "2") {
                                    if ($(this).attr("orderState") != "-10") {
                                        location.href =  roots+"/user/orderdetailzg/id/" + $(this).attr("orderNo") 
                                    }
                                } 
                            });
                            b.append(n)
                        }
                        if (a.EIdx < d) {
                            _IsLoading = false
                        } else {
                            c.hide();
                            if (d > 0) {
                                b.append(Gobal.LookForPC)
                            }
                        }
                        loadImgFun(0)
                    } else {
                        if (p.code == 10) {
                            location.reload()
                        } else {
                            if (p.code == -1) {
                                c.hide();
                                if (a.FIdx == 1) {
									//alert(Gobal.NoneHtmlEx);
                                    b.html(Gobal.NoneHtmlEx)
                                }
                            } else {
                                c.hide();
                                b.html(Gobal.ErrorHtml(p.code));
                                _IsLoading = false
                            }
                        }
                    }
                })
            };
            this.getFirstPage = function() {
                j()
            };
            this.getNextPage = function() {
                a.FIdx += g;
                a.EIdx += g;
                j()
            }
        };
        f = new h();
        f.getFirstPage();
        scrollForLoadData(f.getNextPage)
    };
    Base.getScript(pub+"/style/js/pageDialog.js?v=151104",
    function() {
        Base.getScript(pub+"/style/js/WxShare.js?v=151104", e)
    })
});