'use strict';
angular.module('DHBApp.services',[])

/**
 * 公共service commonService
 * url 为首次加载app就保存起
 */
.factory('commonService',['$http','url',function($http,url){
    return {
        // 获取数据列表
        getDataList: function(params,paramUrl){
            return $http({
                method: 'POST',
                url: paramUrl || url,
                timeout: 10000,
                data: params
            });
        },
        // 保存数据
        saveData: function(params,paramUrl){
            return $http({
                method: 'POST',
                url: paramUrl || url,
                timeout: 15000,
                data: params
            });
        },
        
        // 商品收藏与取消
        setContentFav: function(contentId,action){
            var params = {
                f: 'setContentFav',
                v: {
                    contentId: contentId,
                    action: action
                }
            };
            return this.saveData(params);
        }
    };
}])

/**
 * 订单service 
 */
.factory('orderService',['$http',function($http){
    
}]);
