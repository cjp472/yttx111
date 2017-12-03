var loginFrom = function(){
    var device = {
        weixin: true,
        android: false,
        ios: false,
        mobile: false
    }
    function is_weixin(){
        var ua = navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i) == "micromessenger") {
            return true;
        } else {
            return false;
        }
    }
    if(!is_weixin() && !device.android && !device.ios){
        device.moblile = true;
        device.weixin = false;
    }
    
    return device.weixin ? 'WeiXin' : (device.android ? 'Android' : (device.ios ? 'Ios' : 'Mobile'));
}();
