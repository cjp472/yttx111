'use strict';//启用严格模式

// Declare app level module which depends on filters, and services
var app = angular.module('bsmmApp', [
  'ui.router' //路由
  ,'DHBApp.bsmmControllers' //控制器
  ,'DHBApp.directives'  //自定义指令
  ,'DHBApp.services'  //服务
])
.run(function($rootScope){
    //$rootScope.isLoading = false;
    $rootScope.toggleHead = {};
    $rootScope.$on('$locationChangeSuccess',function(evt){
        
        var reg = /(customer-info|order-info|money-info|goods-info|customer-add)$/;
        if(reg.test(location.href.split('#')[1])){
            $rootScope.toggleHead = {top: '0'};
        }else{
            $rootScope.toggleHead = {top: '10rem'};
        }
        
        
        if(/customer|order-list$/.test(location.href.split('#')[1])){
            //移除上一个选项的class
            $('#menu > ul').children('li').eq(DHB.menuIndex).removeClass('menu-active');
            
            //为当前选项添加class
            if(/customer$/.test(location.href.split('#')[1])){
                DHB.menuIndex = 3;
            }
            else if(/order-list$/.test(location.href.split('#')[1])){
                DHB.menuIndex = 1;
            }
            DHB.menuIndex = $('#menu > ul').children('li').eq(DHB.menuIndex).addClass('menu-active').index();
            
            
            // 移动下划线
            $('#menu > ul').next().css({
                '-webkit-transform': 'translateX(' + 100 * DHB.menuIndex + '%)',
                'transform': 'translateX(' + 100 * DHB.menuIndex + '%)'
            });
            
            //页面头部内容切换选择
            $('#navbar').children('.common').removeClass('common').addClass('hidden');
            $('#navbar').children('.hidden').each(function(index,item){
                if(index == DHB.menuIndex){
                    $(item).removeClass('hidden').addClass('common');
                }
            });
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
    $httpProvider.defaults.headers.post = {'Content-Type': 'application/x-www-form-urlencoded'};
    //跨域资源共享相关设置
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
})
/**
 * 路由配置 
 */
.config(['$stateProvider','$urlRouterProvider',function($stateProvider,$urlRouterProvider){
    
    $urlRouterProvider.when('my-home','/my-home') //我的
        .when('order-list','/order-list')  // 订单列表
        .when('goods-list','/goods-list')  // 商品列表
        .when('goods-hot','/goods-hot')  // 热销商品
        .when('order-money','/order-money')  // 款项
        .when('customer','/customer')  // 客户
        .when('customer-count','/customer-count')  // 客户统计
        .when('rece-money','/rece-money')  // 应收款项
        .when('sales','/sales')  // 销量
        .when('my-home','/my-home')  // 我的
        .otherwise('/home');  //首页
        
    $stateProvider.state('bsmm',{ 
        url: '/home', //默认显示首页
        templateUrl: 'home.html'
    })
    // 订单列表
    .state('order-list',{ 
        url: '/order-list',  
        templateUrl: 'order_list.html?v='+version
    })
    // 订单详情
    .state('order-list.order-info',{ 
        url: '/order-info',
        templateUrl: 'order_info.html?v=' + version
    })
    // 商品列表
    .state('goods-list',{ 
        url: '/goods-list',  
        templateUrl: 'goods_list.html?v='+version
    })
    // 商品详情
    .state('goods-list.goods-info',{ 
        url: '/goods-info',  
        templateUrl: 'goods_info.html?v='+version
    })
    // 热销商品
    .state('goods-hot',{ 
        url: '/goods-hot',  
        templateUrl: 'goods_hot.html?v='+version
    })
    // 销量
    .state('sales',{ 
        url: '/sales',  
        templateUrl: 'sales.html?v='+version
    })
    // 意见建议
    .state('my-home.feed-back',{ 
        url: '/feed-back',  
        templateUrl: 'feed_back.html?v='+version
    })
    // 我的
    .state('my-home',{ 
        url: '/my-home',  
        templateUrl: 'my_home.html?v='+version
    })
    // 公司信息
    .state('my-home.company-info',{ 
        url: '/company-info',  
        templateUrl: 'company_info.html?v='+version
    })
    // 关于医统天下
    .state('my-home.about',{ 
        url: '/about',  
        templateUrl: 'about.html?v='+version
    })
    // 款项
    .state('order-money',{ 
        url: '/order-money',
        templateUrl: 'order_money.html?v=' + version
    })
    // 收款单明细
    .state('order-money.money-info',{ 
        url: '/money-info',
        templateUrl: 'order_moneyinfo.html?v=' + version
    })
    // 应收款项
    .state('rece-money',{ 
        url: '/rece-money',
        templateUrl: 'rece_money.html?v=' + version
    })
    // 客户统计
    .state('customer-count',{ 
        url: '/customer-count',
        templateUrl: 'customer_count.html?v=' + version
    })
//2017.11添加开始
    // 新增药店
    .state('add-shop',{ 
        url: '/add-shop',
        templateUrl: 'addShop.html?v=' + version
    })
    //上传资质
	  .state('addShop_upload',{ 
	    url: '/addShop_upload',
	    templateUrl: 'addShop_upload.html?v=' + version
    })
//2017.11添加结束
    // 客户
    .state('customer',{ 
        url: '/customer',
        templateUrl: 'customer.html?v=' + version
    })
    // 客户详情
    .state('customer.customer-info',{ 
        url: '/customer-info',
        templateUrl: 'customer_info.html?v=' + version
    })
    // 客户新增
    .state('customer.customer-add',{ 
        url: '/customer-add',
        templateUrl: 'customer_add.html?v=' + version
    })
    ;
}])
.factory('myInterceptor', function($q,$rootScope) {
    var interceptor = {
        'request': function(config) {
            // 请求拦截，如果没有sKey重定向到登录页面
            if(!localStorage['m_sKey']){
                window.location.href='../bsmm_login.html';
            }
            // 全局设置request的时候显示loading图标
            //$rootScope.isLoading = true;
            $('div.loading').show();
            //为请求参数加上sKey
            if(config.method.toLowerCase() == 'post'){ //post 请求参数为data
                if(config.data && config.data.v){
                    config.data.v.sKey = window.localStorage['m_sKey'];
                    config.data.v = JSON.stringify(config.data.v);
                }
                return config; // 或者 $q.when(config);
            }
            if(config.params && config.params.v){  //get请求参数为params
                config.params.v.sKey = window.localStorage['m_sKey'];
                config.params.v = JSON.stringify(config.params.v);
            }
            return config; // 或者 $q.when(config);
        
        },
        'response': function(response) {
            if(response.data && response.data.rStatus){
                if(response.data.rStatus == '119'){
                    window.location.href='../bsmm_login.html';
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
            DHB.showMsg('请求失败，请检查您的网络')
            // 请求发生了错误，如果能从错误中恢复，可以返回一个新的响应或promise
            return rejection; // 或新的promise
            // 或者，可以通过返回一个rejection来阻止下一步
            // return $q.reject(rejection);
        }
    };
    return interceptor;
});
