//展示云购码的方法
function showResidueCodes(gid) {
    $.ajax({
        url: roots+'/index/getChoose',
        type: 'post',
        dataType: "json",
        data: {
            gid: gid,
        },
        success: function (result) {

            if (result.status) {
                $("#pro-view-10 .m-detail-codesDetail-wrap").html("");
                $('#pro-view-10 h3').html("选购幸运号码");
                var str = [];
                var arr = [];
                str.push('<dl class="m-detail-codesDetail-list f-clear" id="codes">');
                $(result.codes).each(function (index, yg) {


                    str.push('<dd>' + yg + '</dd>');
//                    str.push('<dd class="txt-red selected">' + code + '</dd>');

                });
                str.push('</dl>');
                $("#pro-view-10 .m-detail-codesDetail-wrap").html(str.join(''));
                $("#pro-view-10").css({left: ($(window).width() - $("#pro-view-10").width()) / 2, top: ($(window).height() - $("#pro-view-10").height()) / 2});
                $("#pro-view-10").show();
                $("#codes dd").toggle(
                        function () {
                            $(this).addClass("txt-red selected");
                        },
                        function () {
                            $(this).removeClass("txt-red selected");
                        }
                );
                $(".c_msgbox_bj").height($("body").height());
                $(".c_msgbox_bj").show();
            }
        },
        error: function () {


        }
    });
}


function showResidueCodesForMobie(gid) {
    //不使用
    $.ajax({
        url: '/index/so/getChoose',
        type: 'post',
        dataType: "json",
        data: {
            gid: gid,
        },
        success: function (result) {

            if (result.status) {
                $("body").attr("style", "overflow:hidden;");
                $(".weixin-mask").attr("style", "display:block;z-index:21;height:" + $(window).height() + "px");
                document.ontouchmove = function (e) {
                    e.preventDefault();
                };

                var h = '<div class="record-pop-ups" style="top:20px;left:-5px;width:100%;height:100%">\n\
                        <dl><dt class="gray9">选购幸运号码</dt>\n\
                            <dd class="comm-pop-btn"><cite class="return-modify">\n\
                            <a id="closeDiag" href="javascript:;" class="gray6">取消</a></cite><cite><a id="btnOK" href="javascript:;" class="orangeBtn">确认</a></cite>\n\
                        </dd></dl></div>';

                $("body").append(h).find("#closeDiag").click(function () {
                    $("div.record-pop-ups").remove();
                    $("body").attr("style", "");
                    $(".weixin-mask").attr("style", "display:none;");
                    document.ontouchmove = function (e) {
                    };
                });
                ;
            }
        },
        error: function () {


        }
    });
}

