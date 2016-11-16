$(function() {
    var a = function() {
        var b = $("#divOrderLoading");
        var h = $("#btnLoadMore");
        var f = 0;
        var i = 10;
        var c = {
            FIdx: 0,
            EIdx: i,
            isCount: 1
        };
        var g = null;
        var e = false;
        var d = function() {
            var j = function() {
                return "/" + c.FIdx + "/" + c.EIdx + "/" + c.isCount
            };
            var k = function() {
                h.hide();
                b.show();				 
               // GetJPData("http://m.1yyg.com", "getUserOrderList", j(),
			    GetJPData(Gobal.Webpath, "shopajax", "getUserOrderList"+j(),
                function(p) {				 
                    if (p.code == 0) {
                        if (c.isCount == 1) {
                            c.isCount = 0;
                            f = p.count
                        }
                        var o = p.listItems;
                        var n = o.length;
                        var m = "";

						
                        for (var l = 0; l < n; l++) {	
							//alert(o[l].q_showtime);
							if(o[l].id!=undefined){
                            m += "<li " +222+'\'"><a class="fl z-Limg" href="'+Gobal.Webpath+'/mobile/mobile/item/' + o[l].id +'"><img src="' + Gobal.LoadPic + '" src2="'+Gobal.imgpath+'/uploads/' + o[l].thumb + '" border=0 alt=""/></a><div class="u-gds-r gray9"><p class="z-gds-tt"><a href="'+Gobal.Webpath+'/mobile/mobile/item/' + o[l].id +'" class="gray6">(第' + o[l].qishu + "期)" + o[l].title + '</a></p><p>幸运全民夺彩码：<em class="orange">' + o[l].q_user_code + "</em></p><p>揭晓时间：" + o[l].q_end_time + "</p>";
                            var q = parseInt(o[l].status);
							
							//var q = '未发货';
							//alert(q);
                            if (q == 0) {
                                m += '<a href="orderdetail/' + o[l].orderlist + '"  class="z-gds-btn">完善收货地址</a><a href="excorderdetail/' + o[l].orderlist + '"  class="z-gds-btn">兑换积分</a>'
                            } else {
                                if (q == 1) {
                                    m += '<a href="javascript:void(0);" class="z-gds-btn z-gds-btnDis">等待发货</a>'
                                } else {
                                    if (q == 2) {
                                        m += '<a href="javascript:void(0);" class="z-gds-btn z-gds-btnDis">已发货</a><a  href="javascript:qurenshouhuo('+ o[l].uid +','+ o[l].orderlist +')" class="z-gds-btn">确认收货</a><a  class="z-gds-btn z-gds-btn">物流公司：'+ o[l].company +' </a><a  class="z-gds-btn z-gds-btn">快递单号：'+ o[l].company_code +' </a>'
                                    } else {
                                        if (q == 3) {
                                            if (o[l].shaidan) {
                                                m += '<a href="javascript:void(0);" class="z-gds-btn z-gds-btnDis">订单已完成</a><a href="javascript:void(0);" class="z-gds-btn z-gds-btnDis">已晒单</a>'
                                            } else {
                                                m += '<a href="javascript:void(0);" class="z-gds-btn z-gds-btnDis">订单已完成</a><a href="singleinsert/' + o[l].id + '" class="z-gds-btn">去晒单</a>'
                                            }
                                        } else {
                                            if (q == 4) {
                                                m += '<a href="javascript:void(0);" class="z-gds-btn z-gds-btnDis">订单已作废</a>'
                                            }
                                        }
                                    }
                                }
                            }
                            m += '</div><b class="z-arrow"></b></li>'
							}
                        }

						

 

                        if (c.FIdx > 0) {
                            b.prev().removeClass("bornone")
                        }
							//alert(c.EIdx);
                        b.before(m).prev().addClass("bornone");						
                        if (c.EIdx < f) {
                            e = false;
                            h.show()
                        }
                        loadImgFun()
                    } else {
                        if (p.code == 10) {
                            location.reload()
                        } else {
                            if (c.FIdx == 0) {
                                b.before(Gobal.NoneHtml)
                            }
                        }
                    }
                    b.hide()
                })
            };
            this.getInitPage = function() {
                k()
            };
            this.getNextPage = function() {
                c.FIdx += i;
                c.EIdx += i;
                k()
            }
        };

		

 

        h.click(function() {
			
            if (!e) {
                e = true;
                g.getNextPage()
            }
        }).show();
        g = new d();
        g.getInitPage()
    };
    Base.getScript(pub+ "/js/Comm.js", a)
});