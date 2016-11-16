$(function() {
    var a = function() {
        $("#divPeriod").touchslider();
        Base.getScript(pub+"/js/mobile/GoodsPicSlider.js",
        function() {
            $("#sliderBox").picslider()
        });
        $("div.pOngoing").click(function() {
            location.href = roots+"/goods/item/goodsId/" + $(this).attr("codeid")
        })
    };
    Base.getScript(pub+"/js/mobile/PeriodSlider.js", a)
});