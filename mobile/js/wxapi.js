'use strict';//严格模式
var wxConfig = {
    config: {},//appId nonceStr timestamp url signature rawString
    initConfig: function(){
        //alert(JSON.stringify(this.config))
        wx.config({
          debug: false,
          appId: this.config.appId,
          timestamp: this.config.timestamp,
          nonceStr: this.config.nonceStr,
          signature: this.config.signature,
          jsApiList: [
            'checkJsApi',
            'translateVoice',
            'startRecord',
            'stopRecord',
            'onRecordEnd',
            /*'playVoice',
            'pauseVoice',
            'stopVoice',*/
            'getNetworkType',
            'openLocation',
            'getLocation',
            'closeWindow',
            'scanQRCode'
          ]
      });
     
    },
    getWinXinConfig: function(){
        var param = {
            f: 'weixinGetConfig',
            v: JSON.stringify({
                sKey: window.localStorage['sKey']//'7419eb0fb493c7f38a66a563f78706f9'
            })
        };
        $.ajax({
            url: window.localStorage['url'],
            type: 'post',
            dataType: 'json',
            data: $.param(param),
            success: function(data){
                if(data && data.rStatus == '100'){
                    wxConfig.config = data.rData;//appId nonceStr timestamp url signature rawString
                    wxConfig.initConfig();
                    return;
                }
                DHB.showMsg('获取微信配置信息失败!');
                
            },
            error: function(){
                DHB.showMsg('获取微信配置信息失败!');
            }
        });
    },
    checkValid: function(){
        wx.checkJsApi({
            jsApiList: [
                'getNetworkType',
                'scanQRCode',
                'previewImage'
            ],
            success: function (res) {
                //alert('seccess: ' + JSON.stringify(res));
            }
        });
    },
    scanner: function(){
        wx.scanQRCode({
            needResult: 1,
            desc: 'scanQRCode desc',
            success: function (res) {
                if(!res.resultStr){
                    DHB.showMsg('没扫描结果!');
                    return;
                }
                DHB.scannResult = res.resultStr.indexOf('EAN_13') > -1 ? res.resultStr.substring(res.resultStr.indexOf(',')+1) : res.resultStr;
                if(location.href.split('#') && location.href.split('#')[1]){
                    if(location.href.split('#')[1]=='/goods-list/0'){
                        location.replace('#/goods-list/00');
                        return;
                    }else if(location.href.split('#')[1]=='/goods-list/00'){
                        location.replace('#/goods-list/0');
                        return;
                    }
                }
                location.href = '#/goods-list/0';
                //alert(JSON.stringify(res));
            }
        });
    }
};

wxConfig.getWinXinConfig();

wx.ready(function(){
    wxConfig.checkValid();
    // 扫描二维码并返回结果
    document.getElementById('scanner').addEventListener('click',function(){
        wxConfig.scanner();
    },false);
    document.getElementById('view').addEventListener('click',function(e){
        if(e.target.className == 'scanner' || e.target.className == 'find-scanner'){
            wxConfig.scanner();
        }
    },false);
});
wx.error(function (res) {
  //DHB.showMsg('微信接口初始化错误!扫一扫暂不可用');
});