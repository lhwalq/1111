var myJdAutoHeight = {
    commonHeight: function() {
        var a1 = $(".new-header").outerHeight();
        var a2 = $("#footer").outerHeight();
        return a1 + a2
    },
    myJdCommonHeight: function() {
        var commonWwrapper = 20;
        var h = $(window).height();
        var a1 = $(".common-wrapper").outerHeight();
        var divHeight = h - myJdAutoHeight.commonHeight() - a1;
        return divHeight
    },
    myJdSetHeight: function(divId) {
        var divHeight = myJdAutoHeight.myJdCommonHeight();
        if (divHeight > 0) {
            $("#" + divId).css("height", (divHeight) + "px")
        }
    }
};
var autoFocusWareHeight_MyFocus = function(args) {
    myJdAutoHeight.myJdSetHeight('autoFocusWareHeight')
};
var autoFocusShopHeight_MyFocus = function(args) {
    myJdAutoHeight.myJdSetHeight('autoFocusShopHeight')
};
var autoHeight_history = function(args) {
    myJdAutoHeight.myJdSetHeight('autoHistoryHeight')
};
var autoHeight_wallet = function(args) {
    myJdAutoHeight.myJdSetHeight('autoWalletHeight')
};
var autoHeight_login = function(args) {
    myJdAutoHeight.myJdSetHeight('autoLoginHeight')
};