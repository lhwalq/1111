$(function () {
	var f = null;
	var d = $("#divLoading");
	var c = $("#ul_list");
	var g = 10;
	var a = {
		FIdx : 1,
		EIdx : g,
		isCount : 1,
	};
	var b = 0;
	var e = function () {
		var h = function () {
			var i = function () {
				return "/FIdx/" + a.FIdx + "/EIdx/" + a.EIdx + "/isCount/" + a.isCount;
			};
			var j = function () {
				//GetJPData("", "getUserBuyList", i()
				$.getJSON(roots+"/seller/getSellerOrderList"+i()
				, function (q) {
					if (q.code == 0) {
						
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
										n += '<cite><img src="' + Gobal.loadpic + '" src2="'+pub+'/uploads/' + k.thumb + '" /><i>'+(p[r].huode!="0"? '中奖' : "未中奖")+'</i>'+"</cite>";
										n += "<dl>";
										n += '<dt>(第' + k.shopqishu + "期)" + k.shopname + "</dt>";
                                                                                n += '<dd>订单号： ' + k.code + "</dd>";
										n += '<dd>参与：<em class="orange">' + k.gonumber + "</em>人次</dd>";
										n += '<dd>购买用户：<a href="/mobile/mobile/userindex/' + k.uid + '" class="blue">' + k.username + "</a></dd>";
										n += "<dd>购买时间：<em>" + k.time + "</em></dd>";
										n += "<dd>订单状态：<em>" + k.status + "</em></dd>";
										n += "</dl>";
										n += "</li>";
							n += "</ul>";
							var o = $(n);
                                                        o.bind("click", function () {
								location.href = roots+"/seller/get_dingdan/id/" + $(this).attr("id") + "";
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
