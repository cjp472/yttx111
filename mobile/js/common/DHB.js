//版本号
var version = '20171216';

// 判断当前是否微信访问
function is_weixin(){
    var ua = navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i)=="micromessenger") {
        return true;
    } else {
        return false;
    }
}

// 全局对象
var DHB = DHB || {};
//历史记录添加
DHB.isPushState = false;
//滚动
DHB.iscroll;
// 当前被选中菜单
DHB.menuIndex = 0;
// 颜色选中下标
DHB.colorIndex = 0;
// 规格选中下标
DHB.specIndex = 0;
DHB.toggleMenu = function(type){
    // type==0 隐藏菜单
    if(type===0){
        $('#view').css('bottom',0);
        $('#index').css('bottom',0);
        $('#menu').removeClass('slideInLeft').addClass('slideOutLeft');
        return;
    }
    $('#menu').removeClass('slideOutLeft').addClass('slideInLeft');
    setTimeout(function(){
        $('#view').css('bottom','5rem');
        $('#index').css('bottom','5rem');
    },450);
};
// 客户端类型
DHB.device = {
    WeiXin: is_weixin(),
    Ios: false,
    Android: false
};
// 支付宝接口
//wangd 2017-11-30 修改url
DHB.aliPayUrl = 'http://bmb.yitong111.com/mobileApi/alipaytrade/alipayapi.php'; //
// 全局提示信息
DHB.showMsg = function(msgText,duration){
    $('#global').text(msgText).show();
    
    // 默认显示3秒钟
    duration = duration || 3000;
    setTimeout(function(){
        $('#global').hide();
    },duration);
};

//日期控件配置项
DHB.opt = {
    preset: 'date', //日期
    theme: 'android', //皮肤样式
    display: 'top', //显示方式 modal top bottom bubble
    mode: 'scroller', //日期选择模式 clickpick scroller mixed
    dateFormat: 'yy-mm-dd', // 日期格式
    setText: '确定', //确认按钮名称
    cancelText: '取消',//取消按钮名
    dateOrder: 'yymmdd', //面板中日期排列格式
    dayText: '日', monthText: '月', yearText: '年', //面板中年月日文字
    // minDate: new Date(),
    endYear:2040 //结束年份
};

// 存储扫描结果
DHB.scannResult = ''; 