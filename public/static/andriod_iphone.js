function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
if (IsPC()) {

}
		
if (/Android\s(\d+\.\d+)/.test(navigator.userAgent)) {
            
    var version = parseFloat(RegExp.$1);
    if (version > 2.3) {
        var phoneScale = parseInt(window.screen.width) / 640;
				
        document.write('<meta name="viewport" content="width=640, minimum-scale = ' + phoneScale + ', maximum-scale = ' + phoneScale + ', target-densitydpi=device-dpi">');
    } else {
        document.write('<meta name="viewport" content="width=640, target-densitydpi=device-dpi">');
    }
} else {
    document.write('<meta name="viewport" content="width=640, user-scalable=no, target-densitydpi=device-dpi">');
}
if (navigator.userAgent.indexOf('MicroMessenger') >= 0) {
    document.addEventListener('WeixinJSBridgeReady', function () {

        });
}