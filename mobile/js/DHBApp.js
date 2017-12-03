'use strict';//启用严格模式

// Declare app level module which depends on filters, and services
var app = angular.module('DHBApp', [
  'ui.router' //路由
  ,'DHBApp.controllers' //控制器
  ,'DHBApp.directives'  //自定义指令
  ,'DHBApp.services'  //服务
])
.run(function($rootScope){
    //$rootScope.isLoading = false;
    $rootScope.toggleView = false;
    $rootScope.$on('$locationChangeSuccess',function(evt){
        //移除menuIndex对应的class
        $('#menu ul > li').eq(DHB.menuIndex).find('a').removeClass('active');
        if(location.href.indexOf('#/goods-sort') != -1
            || location.href.indexOf('#/goods-list') != -1){
            DHB.menuIndex = 1;
        }else if(location.href.indexOf('#/my-cart') != -1){
            DHB.menuIndex = 2;
        }else if(location.href.indexOf('#/find') != -1
                  || location.href.indexOf('#/info') != -1){
            DHB.menuIndex = 3;
        }else if(location.href.indexOf('#/my-home') != -1
                  || location.href.indexOf('#/order-list') != -1
                  || location.href.indexOf('#/order-payment') != -1
                  || location.href.indexOf('#/order-transport') != -1){
            DHB.menuIndex = 4;
        }else if(location.href.split('#').length == 1){
            DHB.menuIndex = 0;
        }
        // 为当前点击元素添加class
        $('#menu ul > li').eq(DHB.menuIndex).find('a').addClass('active');
        
        if(location.href.split('#').length > 1 && location.href.split('#')[1].length > 1){
            $rootScope.toggleView = true;
        }else{
            $rootScope.toggleView = false;
        }
        if(location.href.split('#')[1] && location.href.split('#')[1].indexOf('goods-detail') > -1){
            DHB.toggleMenu(0);
        }else{
            DHB.toggleMenu(1);
        }
    });

})
.constant('url',window.localStorage['url'])
.config(function ($httpProvider) {
    $httpProvider.interceptors.push('myInterceptor');
    //跨域资源共享
    $httpProvider.defaults.useXDomain = true;
    //转换请求参数 如：{name:'zhangsam',age:40} -> name=zhangsan&age=40
    $httpProvider.defaults.transformRequest = function(obj){
        var str = [];  
        for(var p in obj){  
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));  
        }  
        return str.join("&"); 
    }
    //post 请求设置content-type
    $httpProvider.defaults.headers.post={'Content-Type': 'application/x-www-form-urlencoded'};
    //跨域资源共享相关设置
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
})
/**
 * 路由配置 
 */
.config(['$stateProvider','$urlRouterProvider',function($stateProvider,$urlRouterProvider){
    
    $urlRouterProvider.when('my-home','/my-home') //我的
        .when('my-cart','/my-cart')  // 购物车
        .when('find','/find')  // 购物车
        .when('goods-sort','/goods-sort')  // 分类
       // .when('goods-detail','/goods-detail')  // 商品详情
        .when('order-list','/order-list')  // 商品列表
        .when('order-payment','/order-payment')  // 付款单
        .when('order-transport','/order-transport')  // 运货单
        .when('order-returns','/order-returns')  // 退货单
        .when('info','/info') // 信息公告
        .when('add-pay','/add-pay')  // 收货地址管理
        .when('pay-choice','/pay-choice')  // 选择支付方式
        .when('pay-reg','/pay-reg')  // 支付开通
        .when('update-pay','/update-pay')  // 支付开通
        .when('act-account','/act-account')  // 支付开通
        .when('goods-list','/goods-list')  // 商品列表
        .when('feed-back','/feed-back')  // 意见反馈
        
        //.when('action','/action')  // 意见反馈
        
        //.when('empty','/empty')  // 商品列表
        .when('forum','/forum');  // 收货地址管理
        //.otherwise('/home');  //首页
        
    $stateProvider.state('empty',{ 
        url: '/empty', //默认显示首页
        template: ''
    })
    // 订单列表
    .state('order-list',{ 
        url: '/order-list/:v',  
        templateUrl: 'order_list.html?v='+version
    })
    // 付款单列表
    .state('order-payment',{ 
        url: '/order-payment/:v',  
        templateUrl: 'order_payment.html?v=' + version
    })
    .state('order-payment.action',{ 
        url: '/action',
        templateUrl: 'action.html?v=' + version
    })
    // 发货单列表
    .state('order-transport',{ 
        url: '/order-transport/:v',  
        templateUrl: 'order_transport.html?v=' + version
    })
    // 意见反馈
    .state('feed-back',{ 
        url: '/feed-back',  
        templateUrl: 'feed_back.html?v=' + version
    })
    .state('act-account',{ 
        url: '/act-account',  
        templateUrl: 'action_account.html?v=' + version
    })
    // 退货单列表
    .state('order-returns',{ 
        url: '/order-returns',  
        templateUrl: 'order_returns.html?v=' + version
    })
    // 我的
    .state('my-home',{ 
        url: '/my-home',
        templateUrl: 'my_home.html?v=' + version
    })
    // 我的
    .state('update-pay',{ 
        url: '/update-pay',
        templateUrl: 'update_pay.html?v=' + version
    })
    
    //购物车
    .state('my-cart',{ 
        url: '/my-cart',
        templateUrl: 'my_cart.html?v=' + version
    })
    //发现
    .state('find',{ 
        url: '/find',
        templateUrl: 'find.html?v=' + version
    })
    //信息公告
    .state('info',{ 
        url: '/info/:v',
        templateUrl: 'information.html?v=' + version
    })
    //添加付款单
    .state('add-pay',{ 
        url: '/add-pay/:orderCode',
        templateUrl: 'add_payment.html?v=' + version
    })
    //选择支付方式
    .state('pay-choice',{ 
        url: '/pay-choice/:orderNO',
        templateUrl: 'pay_choice.html?v=' + version
    })
    //支付开通
    .state('pay-reg',{ 
        url: '/pay-reg/:orderNO',
        templateUrl: 'pay_reg.html?v=' + version
    })
    //在线支付
    .state('pay-online',{ 
        url: '/pay-online/:orderCode',
        templateUrl: 'pay_online.html?v=' + version
    })
    .state('pay-online.pay-jump',{ 
        url: '/pay-jump',
        templateUrl: 'pay_jump.html?v=' + version
    })
    //在线客服
    .state('forum',{ 
        url: '/forum',
        templateUrl: 'forum.html?v=' + version
    })
    //确认订单
    .state('my-cart.order-confirm',{ 
        url: '/order-confirm',
        templateUrl: 'order_confirm.html?v=' + version
    })
    //订单提交成功
    .state('my-cart.submit-success',{ 
        url: '/submit-success',
        templateUrl: 'submit_success.html?v=' + version
    })
    //分类
    .state('goods-sort',{ 
        url: '/goods-sort',
        templateUrl: 'goods_sort.html?v=' + version
    })
    // 商品列表
    .state('goods-list',{ 
        url: '/goods-list/:siteId',
        templateUrl: 'goods_list.html?v=' + version
    })
    // 商品详情
    .state('goods-list.goods-detail',{ 
        url: '/goods-detail/:goodsId',
        templateUrl: 'goods_detail.html?v=' + version
    });
}])
.factory('myInterceptor', function($q,$rootScope) {
    var interceptor = {
        'request': function(config) {
            // 请求拦截，如果没有sKey重定向到登录页面
            if(!localStorage['sKey']){
                window.location.href='../login.html';
            }
            // 全局设置request的时候显示loading图标
            //$rootScope.isLoading = true;
            $('div.loading').show();
            //为请求参数加上sKey
            if(config.method.toLowerCase() == 'post'){ //post 请求参数为data
                if(config.data && config.data.v){
                    config.data.v.sKey = window.localStorage['sKey'];
                    config.data.v = JSON.stringify(config.data.v);
                }
                return config; // 或者 $q.when(config);
            }
            if(config.params && config.params.v){  //get请求参数为params
                config.params.v.sKey = window.localStorage['sKey'];
                config.params.v = JSON.stringify(config.params.v);
            }
            return config; // 或者 $q.when(config);
        
        },
        'response': function(response) {
            if(response.data && response.data.rStatus){
                if(response.data.rStatus == '119'){
                    window.location.href='../login.html';
                }
            }
            // 响应成功  loading 图标隐藏
            //$rootScope.isLoading = false;
            $('div.loading').hide();
            return response; // 或者 $q.when(config);
        },
        'requestError': function(rejection) {
            // 请求发生了错误，如果能从错误中恢复，可以返回一个新的请求或promise
            return response; // 或新的promise
            // 或者，可以通过返回一个rejection来阻止下一步
            // return $q.reject(rejection);
        },
        'responseError': function(rejection) {
            $rootScope.isLoading = false;
            rejection.data = {rStatus: 101};
            // 解决界面显示[object object]的问题
            if(rejection.config.url.indexOf('.html?') > -1){
                rejection.data = DHB.reponseErrorData;
            }
            DHB.showMsg('请求失败，请检查您的网络');
            
            // 请求发生了错误，如果能从错误中恢复，可以返回一个新的响应或promise
            return rejection; // 或新的promise
            // 或者，可以通过返回一个rejection来阻止下一步
            // return $q.reject(rejection);
        }
    };
    return interceptor;
});
