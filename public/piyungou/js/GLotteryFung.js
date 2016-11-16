var t = {}
function gg_show_Time_fun(times, objc, uhtml, data) {

    time = times - (new Date().getTime());
    i = parseInt((time / 1000) / 60);

    s = parseInt((time / 1000) % 60);
    ms = String(Math.floor(time % 1000));
    if (i < 10)
        i = '0' + i; //剩余时
    if (s < 10)
        s = '0' + s; //剩余分钟
    if (ms < 10)
        ms = '0' + ms; //剩余秒
    if (ms < 0)
        ms = '00';
    i1 = String(i).slice(0, 1);
    i2 = String(i).slice(1);
    i21 = i2.substr(i2.length - 2, 1);
    i22 = i2.substr(i2.length - 1, 1);
    s1 = String(s).slice(0, 1);
    s2 = String(s).slice(1);
    ms = parseInt(ms.substr(0, 2));
    ms1 = String(ms).slice(0, 1);
    ms2 = String(ms).slice(1);


    // objc.find(".specialFamily").html(i+'：'+s);	
    //objc.find(".specialFamily").html(i+'：'+s+'：'+ms);	


    if (time > 0) {
        if (i2.toString().length == 2) {

            objc.find(".specialFamily").html("<b>" + i1 + "</b><b>" + i21 + "</b><b>" + i22 + "</b><span>:</span><b>" + s1 + "</b><b>" + s2 + "</b><span>:</span><b>" + ms1 + "</b><b>" + ms2 + "</b>");
        } else {
            objc.find(".specialFamily").html("<b>" + i1 + "</b><b>" + i2 + "</b><span>:</span><b>" + s1 + "</b><b>" + s2 + "</b><span>:</span><b>" + ms1 + "</b><b>" + ms2 + "</b>");

        }
        //objc.find(".specialFamily").html("<b>"+i1+"</b><b>"+i2+"</b><span>:</span><b>"+s1+"</b><b>"+s2+"</b><span>:</span><b>"+ms1+"</b><b>"+ms2+"</b>");
    }
    if (time <= 0) {
        var obj = objc.parent();
        obj.find(".yTimes").html('<p>在计算，请稍后...</p>');
        setTimeout(function () {
            setTimeout("location.reload();", 500);
            //obj.attr('class',"wenzi");
            $.ajaxSetup({
                async: false
            });
            $.post(roots + "/goods/lottery_shop_set/", {'lottery_sub': 'true', 'gid': data['id']}, null);
        }, 5000);
        return;
    }

    setTimeout(function () {
        gg_show_Time_fun(times, objc, uhtml, data);
    }, 30);

}
function gg_show_time_add_li(div, path, info) {

    var html = '';
    html += '<li class="w_latest w_latest_color goods" id="yTimesLi"><div class="w_latestImg"><a class="w_goods_img" href="' + path + 'goods/items/goodsId/' + info.id + '.html' + '" target="_blank"><img src="' + pub + '/uploads/' + info.thumb + '" /></a>';
    html += '</div><dl class="yTimesDl">';
    html += '<a class="w_goods_three" href="' + path + 'goods/items/goodsId/' + info.id + '.html' + '">(第' + info.qishu + '期)' + info.title + '</a><b>商品价值：￥' + info.zongrenshu + ' <i>已满员</i></b>';
    html += '<div class="w_different">';
    html += '<div class="w_countdown"><div class="w_countdown"><strong>揭晓倒计时</strong><p>';
    html += '<p class="specialFamily">120:00:00</p>';
    html += '</p></div></div>';
    html += '</li>';

    var uhtml = '';
    uhtml += '<dl>';
    uhtml += '<dd class="yddImg"><a href="' + path + 'goods/items/goodsId/' + info.id + '.html' + '" target="_blank" title="' + info.title + '"><img class="lazyjxn" src="' + pub + '/uploads/' + info.thumb + '" style="display: block;"><noscript><img src="' + info.upload + '/' + info.thumb + '" alt="" /> </noscript></a></dd>';
    uhtml += '<dd class="yddName">恭喜2 <a href="' + path + 'user/uname/d/' + (1000000000 + parseInt(info.uid)) + '.html' + '" class="yddNameas">' + info.user + '</a> 获得</dd>';
    uhtml += '<dd class="yGray"><a href="' + path + 'goods/items/goodsId/' + info.id + '.html' + '" title="' + info.title + '" target="_blank" >(第' + info.qishu + '期)' + info.title + '</a></dd>';
    uhtml += '<dd class="yGray">本期幸运号码：' + info.q_user_code + '</dd>';
    uhtml += '</dl><i></i>';
    var divl = $("#" + div).find('li');
    var len = divl.length;
    if (len == 4 && len > 0) {
        var this_len = len - 1;
        divl.eq(this_len).remove();
    }
    $("#" + div).prepend(html);

    var div_li_obj = $("#yTimesLi");
    var data = new Array();
    data['id'] = info.id;
    data['path'] = path;
    info.times = (new Date().getTime()) + (parseInt(info.times)) * 1000;
    gg_show_Time_fun(info.times, div_li_obj, uhtml, data, info.id);
}

function gg_show_time_initg(div, path, gid) {
    window.setTimeout("gg_show_time_initg()", 5000);
    if (!window.GG_SHOP_TIME) {
        window.GG_SHOP_TIME = {
            gid: '',
            path: path,
            div: div,
            arr: new Array()
        };
    }
    $.get(roots + "/Goods/lottery_shop_json/" + new Date().getTime(), {'gid': GG_SHOP_TIME.gid}, function (indexData) {
        var info = jQuery.parseJSON(indexData);
        //alert(info.q_user_code);
        if (info.error == '0' && info.id != 'null' && typeof (info.q_end_cp) == 'undefined') {
            if (!GG_SHOP_TIME.arr[info.id]) {
                GG_SHOP_TIME.gid = GG_SHOP_TIME.gid + '_' + info.id;
                GG_SHOP_TIME.arr[info.id] = true;

                gg_show_time_add_li(GG_SHOP_TIME.div, GG_SHOP_TIME.path, info);
            }
        }
    });
}