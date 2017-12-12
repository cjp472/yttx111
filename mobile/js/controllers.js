'use strict';
//$filter('currency')(123534);
angular.module('DHBApp.controllers',[])

/**
 *  首页控制器 主要做一些页面的切换 以及一些通用功能 
 */
.controller('indexController',[
    '$scope','$location','commonService',
    function($scope,$location,commonService){
    
    //浏览器直接访问 隐藏扫描图片
    $scope.showScanner = true;
    if(DHB.device.WeiXin && !is_weixin()){
       $scope.showScanner = false; 
    }
    
    // 是否是业务员下单
    $scope.instead = false;
    if(window.sessionStorage['orderType']){
        $scope.instead = true;
    }
    // 业务员下单返回
    $scope.insteadBack = function(){
        delete window.localStorage['cart'];
        window.location.href = '../bsmm/index.html#/customer';
    };
    
    // 是否是体验用户
    $scope.isTiYan = false;
    if(window.sessionStorage['tradeId'] && window.sessionStorage['role'] == 'O'){
        $scope.isTiYan = true;
        $scope.instead = false;
    }
    
    // 体验提交信息
    $scope.regTiyan = {
        mobile: '',
        name: '',
        notRegTiyan: true,
        regTiyanInfo: function(){
            if(!/^1\d{10}$/.test(this.mobile)){
                DHB.showMsg('请输入正确的手机号');
                return;
            }
            if(!/^[\u4E00-\u9FA5\uF900-\uFA2D]{2,6}$/.test(this.name)){
                DHB.showMsg('请输入合法的联系人(中文2-6位)');
                return;
            }
            var params = {
                f: 'storeLinkMan',
                v: {
                    Name: this.name,
                    Phone: this.mobile,
                    industry: window.sessionStorage['tradeId']
                }
            };
            var _this = this;
            commonService.saveData(params).success(function(data){
                if(data && data.rStatus == 100){
                    window.localStorage['regTiyan'] = 'true';
                    _this.notRegTiyan = false;
                }
                else{
                    DHB.showMsg(data.error);
                }
            });
            
        }
    };
    if(!$scope.isTiYan || window.localStorage['regTiyan']){
        $scope.regTiyan.notRegTiyan = false;
    }else{
        $('#tiyan').show();
    }
    
    $scope.switchType = function(){
        window.location.href = '../choice_type.html';
    }
    //从localStorage中取出数据
    /*var cartList = window.localStorage.cart ? JSON.parse() : [];*/
   //购物车需要结算的数量
    $scope.cartGoods = {
        length: window.localStorage.cart ? JSON.parse(window.localStorage.cart).length : 0,
        noData: false
    }
    $scope.goodses = [];
    if(window.sessionStorage['orderType']){
        $scope.goodses = window.localStorage.cart ? JSON.parse(window.localStorage.cart) : [];
        if($scope.goodses.length === 0){
            $scope.cartGoods.noData = true;
        }
    }
    //服务器端购物车数据
    $scope.getCartCache = function(){
        if(window.sessionStorage['orderType']) return;
        var params = {
            f: 'getCartCache',
            v: {}
        };
        commonService.getDataList(params).success(function(data){
            if(data.rData && data.rStatus == '100'){
                $scope.goodses = data.rData;
                $scope.goodses.forEach(function(item){
                    item.isShow = data.isShow;;
                });
                $scope.cartGoods.length = $scope.goodses.length;
                if($scope.goodses.length == 0){
                    $scope.cartGoods.noData = true;
                }
                window.localStorage['cart'] = JSON.stringify($scope.goodses);
            }
            else{
                $scope.cartGoods.noData = true;
            }
            
            //$scope.refreshCart();
        }).error(function(data){
            
        });
    };
    $scope.getCartCache();
    
    $scope.history = {
        path: '',
        count: 0,
    };
    // 当前登录用户信息
    $scope.myInfo = {};
    
    //商品搜索关键字
    $scope.globalSearch = {
        keyWord: '',
        commendId: ''
    };
    // 商品分类
    $scope.goodsSort = {
        sortList: [],
        firstSort: [],
        defaultSite: {},
        clickIndex: 1,
        secondSort: []
    };
    
    //页面切换
    $scope.changePath = function(path,e){
        if(e){
            e.preventDefault();
        }
        $location.path(path); 
    };
    //页面切换
    $scope.replacePath = function(path,e){
        if(e){
            e.preventDefault();
        }
        $location.path(path).replace(); 
    };
    
    //在线支付 相关属性
    $scope.onlinePay = {
        //支付方式  1:快捷支付 2 线下转账 3 支付宝
        payWay: 0,
        // 支付宝相关对象
        aliPay: null,
        payAccount: [],
        orderId: '',
        currIndex: 0,
        openapi: '',
        notActive: true,
        currAccount: {}, //SignNO
        getPayAccount: function(orderNO,orderID){
            this.aliPay = null;
            if(orderID){
                this.orderId = orderID;
            }else{
                this.orderId = '';
            }
            var _this = this;
            commonService.getDataList({f: 'getGetWay',v: {sKey: window.localStorage['sKey']}}).success(function(data){
                if(data && data.rStatus == '100'){
                    if(data.AliData && !DHB.device.WeiXin){
                        _this.aliPay = data.AliData;
                    }
                    
                    _this.payAccount = data.rData || [];
				
                    _this.openapi = data.openapi;
                    _this.notActive = data.openapiIscat == '0'; // 1 激活
                    if(_this.payAccount.length > 1){
                        for(var i = 0; i < _this.payAccount.length; i += 1){
                            _this.payAccount[i].name = _this.payAccount[i].AccountType === 'company' ? '企业账户' : '个人账户';
                            if(_this.payAccount[i].IsDefault === 'Y'){
                                _this.currIndex = i;
                                _this.payAccount[i].checked = true;
                                _this.currAccount = _this.payAccount[i];
                            }
                        }
                    }
                    else if(_this.payAccount.length === 1){
                        _this.payAccount[0].name = _this.payAccount[0].AccountType === 'company' ? '企业账户' : '个人账户';
                        _this.payAccount[0].checked = true;
                        _this.currAccount = _this.payAccount[0];
                    }
                    $scope.changePath('/pay-choice/'+orderNO);
                }
                else if(data.rStatus == '101'){
                    $scope.changePath('/add-pay/'+orderNO);
                }
                
                //$scope.refreshCart();
            }).error(function(data){
                
            });
        },
        setPayAccount: function(index){
            if(index == this.currIndex) return;
            this.payAccount[this.currIndex].checked = false;
            this.payAccount[index].checked = true;
            this.currIndex = index;
            this.currAccount = this.payAccount[index];
        }
    };
    
    
    // 获取所有商品分类信息，获取一次
    $scope.getGoodsSort = function(params){
        commonService.getDataList(params).success(function(data){
            if(data && data.rStatus == '100'){
                if(data.rData){
                    for (var i=0; i < data.rData.length; i++) {
                        $scope.goodsSort.sortList.push(data.rData[i]);
                        if(data.rData[i].ParentID == 0){
                            $scope.goodsSort.firstSort.push(data.rData[i]);
                        }
                    }
                    // 显示第一级分类的第一个下级列表
                    var temp = $scope.goodsSort.firstSort[0];
                    $scope.goodsSort.defaultSite = $scope.goodsSort.firstSort[0];
                    for (var j=0; j < $scope.goodsSort.sortList.length; j++) {
                        if($scope.goodsSort.sortList[j].ParentID == temp.SiteID){
                            $scope.goodsSort.secondSort.push($scope.goodsSort.sortList[j]);
                        }
                    }
                }
            }
        }).error(function(data){
            //alert('获取数据失败!');
        });
    };
    
    // 获取当前登录用户信息
    commonService.getDataList({f: 'getMyInfo',v: {}}).success(function(data){
        if(data && data.rStatus == '100'){
            if(data.rData){
                $scope.myInfo = data.rData;
                if($scope.myInfo.CompanyName && DHB.device.WeiXin){
                    var _body = $('body');
                    document.title = $scope.myInfo.CompanyName;
                    // hack在微信等webview中无法修改document.title的情况
                    var _iframe = $('<iframe src="/favicon.ico"></iframe>');
                    _iframe.on('load',function() {
                        setTimeout(function() {
                            _iframe.off('load').remove();
                        }, 0);
                    }).appendTo(_body);
                }
            }
        }
    }).error(function(data){
        //alert('获取数据失败!');
    });
    // 获取易极付认证状态
    //$scope.payUrl = 'http://yaoliankeji.dhb.net.cn/mobileApi/yijifuurl.php'; //wy.dhb.net.cn //wyy.dhb.hk
//    $scope.yijifuStatus = {
//        certifyStatus: 'F'
//    };
//    commonService.saveData({v:{m:'certifyStatus'}},window.location.href.replace(window.location.pathname,'')+'/mobileApi/yijifuurl.php').success(function(data){
//        if(data && data.rStatus == '100'){
//            $scope.yijifuStatus.certifyStatus = data.certifyStatus;
//        }else if(data.rStatus == '101'){
//            //DHB.showMsg(data.error);
//        }
//    }).error(function(data){
//        DHB.showMsg('注册失败');
//        _this.isSubmit = false;
//    }); 
    // 联系方式
    $scope.contacts = null;
    $scope.getContactTools = function(){
        if($scope.contacts != null) return;
        // query param
        var params = {
            f: 'getContactTools',
            v: {}
        }
        commonService.getDataList(params).success(function(data){
            if(data.rData){
               $scope.contacts = data.rData;
               if($scope.contacts.contact){
                   for(var i = 0 ;i < $scope.contacts.contact.length; i += 1){
                       if(/^\d{3,4}-?\d{7,8}$/.test($scope.contacts.contact[i].ContactValue) || /^1\d{10}$/.test($scope.contacts.contact[i].ContactValue) || /^((400|800)\d{7,9})|((400|800)-\d{3,4}-\d{3,4})|((400|800)-\d{7,9})$/.test($scope.contacts.contact[i].ContactValue)){
                           $scope.contacts.contact[i].tellPhone = 'tel:'+$scope.contacts.contact[i].ContactValue;
                           continue;
                       }
                       $scope.contacts.contact[i].tellPhone = 'javascript:;';
                   }
               }
            }
        }).error(function(data){
            //alert('获取数据失败!');
        });
    };
}])


/** 
 * 默认加载的首页面控制器 
 */
.controller('homeController',['$scope','$location','commonService',function($scope,$location,commonService){
    // type 0表示特价  1收藏 2 表示 我订过的
    $scope.goSort = function(path){
        $location.path(path);
    };
    $scope.changePath = function(path,type){
        switch(type){
            case 0:
                $scope.globalSearch.commendId = 2;
                break;
            case 1:
                $scope.globalSearch.commendId = 10;
                break;
            case 2:
                $scope.globalSearch.commendId = 11;
                break;
        }
        if($scope.history.path && path.indexOf('goods-list') > -1){
            if('/goods-list/0' == $scope.history.path){
                path = '/goods-list/00';
            }else{
                 path = '/goods-list/0';
            }
        }else if(path.indexOf('goods-list') == -1){
            path = path + '/' + (++$scope.history.count);
        }
        $location.path(path);
        
    }
    // 首页图片
    $scope.pics = [];
    $scope.getPicture = function(){
        var params = {
            f: 'getPicture',
            v: {}
        };
        commonService.getDataList(params).success(function(data){
            if(data && data.rStatus == '100'){
                if(data.rData){
                    $scope.pics = data.rData;                       
                }
            }else{
                //alert('参数错误!');
            }
            
        }).error(function(data){
            
        });
    };
    $scope.getPicture();
    //获取推荐商品列表
    $scope.goodses = {
        index: -1,
        totalPage: 0,
        currPage: 0,
        goodsList: [],
        hasMore: false,
        params: {
            f: 'getGoodsList',
            v: {
                commendId: 4, //商品类型  1:推荐，2：特价，3：新款，4：热销，9：缺货
                begin: 0,
                //orderBy: 1, //1:价格降序，2：价格升序，3：上架时间，4：商品人气
                step: 10
            }
        },
        getGoodsList: function(){
            var _this = this;
            // 获取推荐商品列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(data && data.rStatus == '100'){
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            if(!data.rData[i].Picture || !DHB.isLoadImg){
                                data.rData[i].Picture = '../img/default_pic.png';
                            }
                            _this.goodsList.push(data.rData[i]);
                        } 
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }                       
                    }
                }else{
                    //alert('参数错误!');
                }
                
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
            });
        },
        
        // 滚动加载更多
        loadMore: function(){
            // 手机端滑动过快会请求两次 加条件限制typeof this.params.v == 'object'
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getGoodsList();
            }
        }
    };
    $scope.goodses.getGoodsList(); 
}])

/** 
 * 商品列表控制器 
 */
.controller('goodsController',['$scope','$location','$stateParams','commonService',function($scope,$location,$stateParams,commonService){
    $scope.history.path = $location.path();
    
    // 监听路由改变
    $scope.$on('$locationChangeStart',function(){
        if(arguments[1].indexOf('#/goods-list') == -1){
            $scope.globalSearch.keyWord = '';
            $scope.globalSearch.commendId = '';
        }
        $scope.globalSearch.urlFix = $stateParams.siteId;
    });
    // 改变路由
    $scope.changePath = function(path,bool,e){
        if(e && e.target.nodeName.toLowerCase() == 'a') return;
        if(bool){
            path="/goods-list/" + $stateParams.siteId + path
        }
        $location.path(path);
    }
    $scope.showList = window.sessionStorage['showStyle'] !== 'grid';
    var cart = window.localStorage.cart ? JSON.parse(window.localStorage.cart) : [];
    var goodsIds = [];
    for(var k = 0;k < cart.length ;k +=1){
        goodsIds.push(cart[k].id);
    }
    //获取分类ID
    var siteId = $stateParams.siteId;
    //添加购物车获取商品详情
    $scope.goodsDetail = {};
    
    //获取商品列表
    $scope.goodses = {
        index: -1,
        totalPage: 0,
        noData: false,
        currPage: 0,
        goodsList: [],
        goodsList2: [],
        brands: [],
        sites: [],
        choiceBrand: {
            brandId: '-1',
            brandName: '全部',
        },
        choiceSite: {
            siteId: '-1',
            siteName: '全部',
        },
        choiceCommend: {
            commendId: '-1',
            commendName: '全部',
        },
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.goodsList = [];
            this.goodsList2 = [];
            this.noData = false;
            this.params.v = {
                begin: 0, 
                step: 10
            };
        },
        params: {
            f: 'getGoodsList',
            v: {
                //commendId: 1, //商品类型  1:推荐，2：特价，3：新款，4：热销，9：缺货
                begin: 0,
                //orderBy: 1, //1:价格降序，2：价格升序，3：上架时间，4：商品人气
                step: 10
            }
        },
        toggleList: function(){
            if(!$scope.showList){
                this.goodsList = this.goodsList2;
            }
            else{
                this.goodsList2 = this.goodsList;
            }
        },
        getGoodsList: function(){
            DHB.scannResult = ''
            var _this = this;
            // 获取商品列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.goodsList.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                //$scope.globalSearch.keyWord = '';
                if(data && data.rStatus == '100'){
                    /*_this.choiceBrand = null;
                    _this.choiceSite = null;
                    _this.choiceCommend = null;*/
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            
                            if(!data.rData[i].Picture || !DHB.isLoadImg){
                                data.rData[i].Picture = '../img/default_pic.png';
                            }
                            for(var j=0; j < goodsIds.length; j += 1){
                                if(data.rData[i].ID == goodsIds[j]){
                                    data.rData[i].ordered = true;
                                    break;
                                }
                            }
                            if(data.rData[i].CommendName){
                                data.rData[i].CommendName = data.rData[i].CommendName.replace(/^\[|\]$/g,'');//slice(1,data.rData[i].CommendName.length-1);
                            }
                            if($scope.showList){
                                _this.goodsList.push(data.rData[i]);
                            }
                            else{
                                _this.goodsList2.push(data.rData[i]);
                            }
                        } 
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                        
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300)
                    }
                }else{
                    //DHB.showMsg('参数错误!');
                }
                
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
            });
        },
        
        //排序条件
        refreshBySort: function(sort){
            if(sort != 2 && sort == this.params.v.orderBy){
                return;
            }
            //对于价格有升序和降序排序  1 价格降序  2 价格升序
            if(sort == 2){
                if(this.params.v.orderBy == sort){
                    sort = 1;
                }
            }
            this.clear();
            
            if(this.choiceBrand && this.choiceBrand.brandId != '-1'){
                this.params.v.brandId = this.choiceBrand.brandId;
            }
            if(this.choiceCommend && this.choiceCommend.commendId != '-1'){
                this.params.v.commendId = this.choiceCommend.commendId;
            }
            if(this.choiceSite && this.choiceSite.siteId != '-1'){
                this.params.v.siteId = this.choiceSite.siteId;
            }
            this.params.v.orderBy = sort;
            this.getGoodsList();
        },
        // 滚动加载更多
        loadMore: function(){
            // 手机端滑动过快会请求两次 加条件限制typeof this.params.v == 'object'
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getGoodsList();
            }
        },
        //获取商品详情
        getGoodsContent: function(goodsId,index){
            $scope.choiceMore.isChoiceMore = false;
            DHB.colorIndex = 0;
            DHB.specIndex = 0;
            $('#color').children('.goods-active').removeClass('goods-active');
            $('#color').children('span').eq(0).addClass('goods-active');
            $('#spec').children('.goods-active').removeClass('goods-active');
            $('#spec').children('span').eq(0).addClass('goods-active');
            if(!DHB.isPushState){
                DHB.isPushState = true;
                window.history.pushState(null,null,location.href);
            }
            
            
            this.index = index;
            var contentParams = {
                f: 'getGoodsContent',
                v: {
                    contentId: goodsId
                }
            };
            var _this = this;
            commonService.getDataList(contentParams).success(function(data){
                if(data.rData){
                    $scope.goodsDetail = data.rData;
                    $scope.goodsDetail.Picture = $scope.goodsDetail.Picture == '' || !DHB.isLoadImg ? '../img/default_pic.png' : $scope.goodsDetail.Picture
                    $scope.favText = $scope.goodsDetail.isFav == 'Y' ? '已收藏' : '收藏';
                    if(data.rData.Color){
                        $scope.goodsDetail.Color = data.rData.Color.split(',');
                        $scope.goods.color = $scope.goodsDetail.Color[0];
                    }
                    if(data.rData.Specification){
                        $scope.goodsDetail.Specification = data.rData.Specification.split(',');
                        $scope.goods.specify = $scope.goodsDetail.Specification[0];
                    }
                    
                    $scope.goods.id = $scope.goodsDetail.ID;
                    // 默认选中颜色规格  计算数量
                    if(data.rData.Color || data.rData.Specification){
                        // md5 加密 hex_md5('256红色0.5') id + color + specification
                        var compare = hex_md5($scope.goods.id + $scope.goods.color + $scope.goods.specify);
                        if($scope.goodsDetail.library){
                            for(var key in $scope.goodsDetail.library){
                                if(key == compare){
                                    $scope.goodsDetail.allLibrary = $scope.goodsDetail.library[key];
                                    break;
                                }
                            }
                        }
                    }
                    
                    $scope.goods.units = $scope.goodsDetail.Units;
                    $scope.goods.code = $scope.goodsDetail.Coding;
                    $scope.goods.num = $scope.goodsDetail.Package && $scope.goodsDetail.Package != 0 && $scope.goodsDetail.Package > $scope.goodsDetail.allLibrary ? $scope.goodsDetail.Package : 
                        ($scope.goodsDetail.allLibrary <= 0 ? 0 : 1 );
                    if($scope.goodsDetail.Package && parseInt($scope.goodsDetail.Package) !== 0){
                        $scope.goods.num = 0;
                    }
                    $scope.goods.pack = $scope.goodsDetail.Package;
                    $scope.goods.stock = $scope.goodsDetail.allLibrary;
                    $scope.goods.isStock = $scope.goodsDetail.controllerLibrary && $scope.goodsDetail.controllerLibrary == 'Y';
                    $scope.goods.isShow = $scope.goodsDetail.showLibrary;
                }
            }).error(function(data){
                
            });
        },
        //获取品牌
        getBrand: function(site_id){ 
            if(this.brands.length == 0){
                var _this = this;
                var grandParams = {
                    f: 'getGoodsBrand',
                    v: {}
                };
                if($stateParams.siteId){
                    grandParams.v.siteId = $stateParams.siteId;
                }
                commonService.getDataList(grandParams).success(function(data){
                    if(data.rData){
                        _this.brands = data.rData
                    }
                }).error(function(data){
                    
                });
            }
        },

        //获取分类
        getSite: function(){ 
            if(this.sites.length == 0){
                var parentId;
                // 查找当前分类id的parentid
                if(siteId == 0){
                    parentId = 0;
                }else{
                    for (var i=0; i < $scope.goodsSort.sortList.length; i++) {
                        if($scope.goodsSort.sortList[i].SiteID == siteId){
                            parentId = $scope.goodsSort.sortList[i].ParentID;
                            break;
                        }
                    }  
                }
                  
                // 将parentid相同的分类放入sites
                for (var i=0; i < $scope.goodsSort.sortList.length; i++) {
                    if($scope.goodsSort.sortList[i].ParentID == parentId){
                        this.sites.push($scope.goodsSort.sortList[i]);
                    }
                }  
            }
            
        },
        //设置用户筛选条件 type 0 品牌，1 类型，2 分类
        setFilter: function(type,e){
           var target = e.target;
            if(target.tagName.toLowerCase() == 'li'){
                if(type == 0){
                    this.choiceBrand = {
                        brandId: target.getAttribute('data-brandid'),
                        brandName: target.innerText || target.innerText.contentText,
                    };
                }else if(type == 1){
                    this.choiceCommend = {
                        commendId: target.getAttribute('data-commendid'),
                        commendName: target.innerText || target.innerText.contentText,
                    };
                }else if(type == 2){
                    this.choiceSite = {
                        siteId: target.getAttribute('data-siteid'),
                        siteName: target.innerText || target.innerText.contentText,
                    };
                    this.brands = [];
                    this.getBrand(this.choiceSite.siteId);
                }
            }
        },
        clearFilter: function(){
            this.choiceBrand = {
                brandId: '-1',
                brandName: '全部',
            };
            this.choiceSite = {
                siteId: '-1',
                siteName: '全部',
            };
            this.choiceCommend = {
                commendId: '-1',
                commendName: '全部',
            };
            $scope.globalSearch.commendId = '';
        },
        //根据筛选条件查询列表
        getGoodsListByFilter: function(){
            this.clear();
            if(this.choiceBrand && this.choiceBrand.brandId != '-1'){
                this.params.v.brandId = this.choiceBrand.brandId;
            }
            if(this.choiceCommend && this.choiceCommend.commendId != '-1'){
                this.params.v.commendId = this.choiceCommend.commendId;
            }
            if(this.choiceSite && this.choiceSite.siteId != '-1'){
                this.params.v.siteId = this.choiceSite.siteId;
            }
            if($scope.globalSearch.keyWord){
                this.params.v.kw = $scope.globalSearch.keyWord;
            }
            this.getGoodsList();
            //dom 操作 不推荐
            DHB.toggleMenu(1);
            $('div.filter-layer').removeClass('fadeInRight').addClass('fadeOutRight');
        }
    };
    if(siteId == 0){
        delete $scope.goodses.params.v.siteId;
        // 是否有扫描数据
        if(DHB.scannResult != ''){
            $scope.globalSearch.keyWord = DHB.scannResult;
        }
        // 是否有按分类查询
        if($scope.globalSearch.commendId != ''){
            $scope.goodses.params.v.commendId = $scope.globalSearch.commendId;
            this.choiceCommend = {
                commendId: '-1',
                commendName: '全部',
            };
            // 2表示特价  10收藏  11 表示 我订过的
            switch($scope.globalSearch.commendId){
                case 2:
                    $scope.goodses.choiceCommend.commendId = 2;
                    $scope.goodses.choiceCommend.commendName = '特价';
                    break;
                case 10:
                    $scope.goodses.choiceCommend.commendId = 10;
                    $scope.goodses.choiceCommend.commendName = '收藏';
                    break;
                case 11:
                    $scope.goodses.choiceCommend.commendId = 11;
                    break;
            }
        }
        $scope.goodses.params.v.kw = $scope.globalSearch.keyWord;
    }else{
        $scope.goodses.params.v.siteId = siteId;
    }
    $scope.goodses.getGoodsList(); 
    window.onpopstate = function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            DHB.toggleMenu(1);
            $('div.cart-layer').removeClass('fadeInRight').addClass('fadeOutRight');
        }
    };
    
    
    
    //加入购物车需要存储的属性
    $scope.goods = {  // 1、比较相同  id +规格+ 颜色    2、库存 3、预留   4、是否需要比较库存
        id: '',
        code: '',
        name: '',
        units: '',
        num: 0,   // 订购数量
        compare: '',// 比较属性 相同表示已添加
        stock: 0,  //库存
        isStock: false,  // 是否需要判断库存
        isShow: 'Y',  // 是否需要显示库存 Y 显示 N 不显示
        pack: '',
        price: 0,
        color: '',
        specify: '',
        pic: ''
    };
    
    //存储用户当前点击的商品
    $scope.currentGoods = {};
    $scope.setCurrentGoods = function(goods){
        $scope.currentGoods = goods;
    };
    
    //收藏 action add为收藏 remove为取消收藏
    $scope.favText = '';
    $scope.setContentFav = function(action){
        if($scope.goodsDetail.isFav == 'Y'){
            action = 'remove';
        }
        commonService.setContentFav($scope.goodsDetail.ID,action).success(function(data){
            if(data.rStatus == '100'){
                $scope.goodsDetail.isFav = $scope.goodsDetail.isFav == 'Y' ? 'N' : 'Y';
                $scope.favText = $scope.goodsDetail.isFav == 'Y' ? '已收藏' : '收藏';
                if(action=='remove'){
                    DHB.showMsg('取消成功!');
                    return;
                }
                DHB.showMsg('收藏成功!');
            }
        }).error(function(data){
            
        });
    };
    
    // 刷新购物车
    $scope.refreshCart = function(){
        $scope.goodses.goodsList[$scope.goodses.index].ordered = true;
        var cartData = [];
        var temp = JSON.parse(window.localStorage.cart);
        for(var i = 0; i < temp.length; i += 1){
            cartData[i] = {
                id: temp[i].id,
                color: temp[i].color,
                specify: temp[i].specify,
                num: temp[i].num,
            };
            
        }
        if(window.sessionStorage['orderType']) return;
        var params = {
            f: 'submitCartCache',
            v: {
               cartData: JSON.stringify(cartData)
            }
        };
        commonService.saveData(params).success(function(data){
            if(data.rStatus == '100'){
                //DHB.showMsg('添加成功')
            }
            else{
                DHB.showMsg(data.error)
            }
        }).error(function(data){
            DHB.showMsg('添加失败');
        });
    };
    
    // 启用多选相关
    $scope.choiceMore = {
        isChoiceMore: false,
        //切换是否开启多选
        toggleChoiceMore: function(){
            this.isChoiceMore = !this.isChoiceMore;
        }
    };
}])

/**
 *  购物车控制器 
 */
.controller('cartController',['$scope','commonService',function($scope,commonService){
    //从localStorage中取出数据
    var cart = window.localStorage.cart ? JSON.parse(window.localStorage.cart) : [];
    $scope.goodses = cart;
    // 监听路由改变
    $scope.$on('$locationChangeStart',function(){
        $scope.submitCartCache();
        window.localStorage['cart'] = JSON.stringify($scope.goodses);
    });
    // 显示无数据提示块
    $scope.cartGoods.noData = false;
    if($scope.goodses.length == 0){
        $scope.cartGoods.noData = true;
    }
    //总金额
    $scope.totalAmount = 0; 
    $scope.refreshTotalAmount = function(){
        $scope.totalAmount = 0;
        for(var i = 0; i < $scope.goodses.length; i += 1){
            // 获取选中的商品
            if($scope.goodses[i].isChecked){
                $scope.totalAmount += Number(parseFloat($scope.goodses[i].num * $scope.goodses[i].price).toFixed(2));
            }
        }
        // 总金额
        $scope.totalAmount = $scope.totalAmount.toFixed(2);
    };
    
    // 刷新购物车
    $scope.refreshCart = function(){
        for(var i = 0; i < $scope.goodses.length; i += 1){
            // 默认选中商品
            $scope.goodses[i].isChecked = true;
        }
        $scope.cartGoods.length = $scope.goodses.length;
        if($scope.goodses.length == 0){
            $scope.cartGoods.noData = true;
        }
        $scope.refreshTotalAmount();
    };
    if(cart.length ==0){
        $scope.getCartCache();
    }else{
        $scope.refreshCart();
    }
    $scope.clickedGoods = true;
    //商品切换选择
    $scope.toggleChecked = function(goods){
        if(this.clickedGoods){
            $scope.clickedGoods = false;
            setTimeout(function(){
                $scope.clickedGoods = true;
            },452);
            goods.isChecked = !goods.isChecked;
        
            // 如果有一个未选中 全选按钮不选中
            for(var i = 0; i < $scope.goodses.length; i += 1){
                if(!$scope.goodses[i].isChecked){
                    $scope.selectAllBtn = false;
                    break;
                }
                $scope.selectAllBtn = true;
            }
            $scope.refreshTotalAmount();
        }
    };
    $scope.cartEditText = '编辑';
    $scope.isEdit = false;
    $scope.cartEdit = function(){
        $scope.isEdit = !$scope.isEdit;
        if($scope.isEdit){
            $scope.cartEditText = '完成';
            $scope.selectAllBtn = true;
            $scope.selectAll();
        }
        else {
            $scope.cartEditText = '编辑';
            $scope.selectAllBtn = false;
            $scope.selectAll();
        }
    };
    $scope.setContentFav = function(action){
        var ids = [];
        for(var i = 0; i < $scope.goodses.length; i += 1){
            // 清楚选中的商品
            if($scope.goodses[i].isChecked){
                if(ids.indexOf($scope.goodses[i].id) < 0){
                    ids.push($scope.goodses[i].id);
                }
            }
        }
        if(ids.length === 0){
            DHB.showMsg('未选中任何商品');
            return;
        }
        commonService.setContentFav(ids.join(','),action).success(function(data){
            if(data.rStatus == '100'){
                DHB.showMsg('收藏成功!');
            }else{
                DHB.showMsg(data.error);
            }
        }).error(function(data){
            
        });
    };
    //全选商品
    $scope.selectAllBtn = true;
    $scope.selectAll = function(){
        $scope.selectAllBtn = !$scope.selectAllBtn;
        for(var i = 0; i < $scope.goodses.length; i += 1){
            // 如果全选按钮为选中状态，则所有商品选中
            if($scope.selectAllBtn){
                $scope.goodses[i].isChecked = true;
                continue;
            }
            //所有商品不选中  即反选
            $scope.goodses[i].isChecked = false;
        }
        $scope.refreshTotalAmount();
    };
    $scope.submitCartCache = function(){
        var cartData = [];
        for(var i = 0; i < $scope.goodses.length; i += 1){
            cartData[i] = {
                id: $scope.goodses[i].id,
                color: $scope.goodses[i].color,
                specify: $scope.goodses[i].specify,
                num: $scope.goodses[i].num,
            };
            
        }
        var params = {
            f: 'submitCartCache',
            v: {
               cartData: JSON.stringify(cartData)
            }
        };
        if($scope.goodses.length == 0){
            params.v.action = 'clear';
            delete params.v.cartData;
        }
        window.localStorage.cart = JSON.stringify($scope.goodses);
        if(window.sessionStorage['orderType']) return;
        commonService.saveData(params).success(function(data){
            if(data.rStatus == '100'){
                $scope.refreshTotalAmount();
            }
        }).error(function(data){
            
        });
    };
    //清除购物车选中商品
    $scope.clearSelectGoods = function(){
        var hasSelected = false;
        for(var i = 0; i < $scope.goodses.length; i += 1){
            // 清楚选中的商品
            if($scope.goodses[i].isChecked){
                $scope.goodses.splice(i,1);
                hasSelected = true;
                i--;
            }
        }
        if(hasSelected){
            $scope.cartGoods.length = $scope.goodses.length;
            // 提交当前购物车数据
            $scope.submitCartCache();
        }
        if($scope.goodses.length == 0){
            $scope.cartGoods.noData = true;
        }
    };
    
    // getSetOrder 配送方式   付款方式
    $scope.sendType = []; //配送方式
    $scope.payType = []; // 支付方式
    $scope.address = []; // 送货地址
    $scope.selectSend = {}; //选择的配送方式
    $scope.selectPay = {}; //选择的付款方式
    $scope.selectAddress = {}; //选择的送货地址
    $scope.invoice = ''; // 开票类型
    $scope.deliveryTime = '';
    $scope.orderSubmit = {
        deliveryTime: '', // 交货日期  B:必填，Y：选填
        orderRemark: '' // 备注
    }; 
    var s_index = 0; // 配送方式列表下标
    var p_index = 0; // 付款方式列表下标
    var a_index = 0; // 送货地址列表下标
    var d_index = 0; // 默认送货地址列表下标
    // 配送方式   付款方式
    $scope.getSetOrder = function(){
        var sendParams = {
            f: 'getSetOrder',
            v: {}
        };
        commonService.getDataList(sendParams).success(function(data){
            if(data.rData && data.rStatus == '100'){
                $scope.deliveryTime = data.rData.deliveryTime;
                
                $scope.sendType = data.rData.send;
                $scope.sendType[0].checked = true;
                $scope.selectSend = $scope.sendType[0];
                
                $scope.payType = data.rData.pay;
                $scope.payType[0].checked = true;
                $scope.selectPay = $scope.payType[0];
                
                $scope.getAddress();
                $scope.changePath('/my-cart/order-confirm');
            }
            else {
                DHB.showMsg(data.error);
            }
        }).error(function(data){
            
        });
    };
    // 地址管理
    $scope.addrs = {
        addrAddress: '',
        addrCompany: '',
        addrContact: '',
        addrPhone: '',
        addrTitle:'',
        addrIndex: -1,
        isSubmit: false,
        selectAddr: {},
        actionType: 0, // 0 表示新增 ，1表示编辑 ，2表示删除
        params: {
            f: 'setAddress',
            v: {
                action: '',
                addressContact: '',//联系人
                addressPhone: '', // 电话
                addressAddress: '',//地址
                addressCompany: '',//单位
                addressId: ''
                
            }
        },
        setActionType: function(type,index){
            if(index || index === 0){
                this.addrIndex = index;
            }
            this.actionType = type;
            this.addrTitle = type===0 ? '新增地址' : (type===1 ? '修改地址' : '确定删除该地址吗?');
            this.params.v.action = type===0 ? 'add' : (type===1 ? 'edit' : 'del');
            if(type !== 0){
                this.selectAddr = $scope.address[index];
            }else{
                this.selectAddr = {};
                /*this.params.v.addressAddress = '';
                this.params.v.addressCompany = '';
                this.params.v.addressContact = '';
                this.params.v.addressPhone = '';
                this.params.v.addressId = '';*/
            }
        },
        setDefault: function(index,e){
            e.stopPropagation();
            var select_addr = $scope.address[index];
            if(select_addr.AddressFlag == '1') return;
            var params = {
                f: 'setAddress',
                v: {
                    action: 'default',
                    addressId: select_addr.AddressID
                }
            };
            commonService.saveData(params).success(function(data){
                if(data.rStatus == '100'){
                    $scope.address[d_index].AddressFlag = '0';
                    $scope.address[d_index].defaultText = '设为默认';
                    d_index = index;
                    select_addr.AddressFlag = '1';
                    select_addr.defaultText = '默认地址';
                }
            }).error(function(data){
                
            });
        },
        setAddress: function(isValid){
            if(!isValid && this.actionType !== 2){
                DHB.showMsg('信息不完整!')
                return;
            }
            //防止重复提交
            if(this.isSubmit){
                return;
            }
            
            // 设置参数
            this.params.v.addressAddress = this.selectAddr.AddressAddress;
            this.params.v.addressCompany = this.selectAddr.AddressCompany;
            this.params.v.addressContact = this.selectAddr.AddressContact;
            this.params.v.addressPhone = this.selectAddr.AddressPhone;
            this.params.v.addressId = this.selectAddr.AddressID;
            
            if(this.actionType === 2){
                this.params.v = {
                    action: 'del',
                    addressId: this.selectAddr.AddressID
                }
            }
            this.isSubmit = true;
            var _this = this;
            commonService.saveData(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                _this.isSubmit = false;
                if(data.rStatus == '100'){
                    $('div.addr-manager').hide();
                    // 从当前列表删除该地址
                    if(_this.actionType === 2){
                        $scope.address.splice(_this.addrIndex,1);
                    }
                    // 更新当前地址列表
                    if(_this.actionType === 0){
                        $scope.address.push(_this.selectAddr);
                        _this.selectAddr.AddressID = data.insertId;
                        $scope.selectAddress = _this.selectAddr;
                    }
                }else{
                   DHB.showMsg(data.error); 
                }
            }).error(function(){
                _this.params = JSON.parse(_this.params);
                _this.isSubmit = false;
            });
        }
    };
    $scope.getAddress = function(){
        var params = {
            f: 'getAddress',
            v: {}
        };
        commonService.getDataList(params).success(function(data){
            if(data.rData){
                $scope.address = data.rData;
                // 循环找出默认地址
                for (var i = 0; i < $scope.address.length; i += 1) {
                    if($scope.address[i].AddressFlag == 1){
                        $scope.address[i].checked = true;
                        $scope.address[i].defaultText = '默认地址';
                        $scope.selectAddress = $scope.address[i];
                        a_index = i;
                        d_index = i;
                    }else{
                        $scope.address[i].defaultText = '设为默认';
                    }
                };
            }
        }).error(function(data){
            
        });
    };
    // 当前选择配送方式
    $scope.setSendType = function(index){
        if(index == s_index) return;
        $scope.sendType[s_index].checked = false;
        $scope.selectSend = $scope.sendType[index];
        $scope.sendType[index].checked = true;
        s_index = index;
        
        //此处不推荐使用jquery
        $('div.send-type').hide();
    };
    //当前选择付款方式
    $scope.setPayType = function(index){
        if(index == p_index) return;
        $scope.payType[p_index].checked = false;
        $scope.selectPay = $scope.payType[index];
        $scope.payType[index].checked = true;
        p_index = index;
        
        //此处不推荐使用jquery
        $('div.pay-type').hide();
    };
    //当前选择收货地址
    $scope.setAddress = function(index){
        if(index == a_index) {
            //此处不推荐使用jquery
            $('div.select-address').hide();
            return;
        }
        $scope.address[a_index].checked = false;
        $scope.selectAddress = $scope.address[index];
        $scope.address[index].checked = true;
        a_index = index;
        
        //此处不推荐使用jquery
        $('div.select-address').hide();
    };
    
    //存储用户当前点击的商品
    $scope.goods = {};
    $scope.goodsDetail = {}; //兼容商品详细页面共同指令用到的goodsDetail
    $scope.setCurrentGoods = function(goods){
        $scope.goods = goods;
        var bool = false;
        if(typeof goods.isStock === 'string' && $scope.goods.isStock == 'true'){
            bool = true;
        }else if(typeof goods.isStock === 'boolean' && $scope.goods.isStock){
            bool = true;
        }
        $scope.goodsDetail = {
            allLibrary: goods.stock,
            controllerLibrary: bool ? 'Y' : 'N',
            Package: goods.pack
        }
    };
    
    //选中商品进行结算
    $scope.checkedGoodses = [];
    
    //购物车结算
    $scope.settleMent = function(){
        $scope.checkedGoodses = [];
        
        var totals=$("#totals").attr("datas-total");	
		commonService.getDataList({f: 'get_OrderAmount',v: {sKey: window.localStorage['sKey']} }).success(function(data){
			
			if(data && data.rStatus == '100'){
				if(data.rData !== null){
					if(data.rData > 0 && (totals <= data.rData) ){
						DHB.showMsg(" 起订金额是 ¥"+data.rData+"，请继续采购 ");
						return false;
					}
				}else{
					DHB.showMsg('无法获取订单的小金额!');
					return false;
				}
			}else{
			
			 DHB.showMsg('无法获取订单的小金额!');
			 return false;
			}
			
			for(var i = 0; i < $scope.goodses.length; i += 1){
				// 获取选中的商品
				if($scope.goodses[i].isChecked){
					$scope.checkedGoodses.push($scope.goodses[i]);
				}
			}
			if($scope.checkedGoodses.length == 0){
				DHB.showMsg('没有结算的商品!');
				return;
			}
			$scope.getSetOrder();
			
		});
    };
    
    //去付款
    $scope.newOrderSN = '';
    $scope.newOrderId = '';
    $scope.payObj = {userInputMoney: ''};
    $scope.payAccount = null;
    $scope.isQuickPay = false;
    $scope.onLine = true; // 快捷支付
    $scope.c_aliPay = false; // 支付宝
    $scope.c_weiXinPay = false; // 快捷支付
    $scope.offLine = false; // 线下转账
    $scope.msgText = '您还未开通移动支付，现在去开通吗?';
    $scope.isAction = false;
	$scope.BankNoticeTitle = '';
	$scope.BankNoticeContent = '';
	$scope.phone = '';
    $scope.goPay = function(){
		
        if($scope.onLine){
          commonService.getDataList({f: 'get_pay_notice',v: {} }).success(function(data){
				if(data && data.rStatus == '100'){
				   $scope.BankNoticeTitle = data.rData.title;
				   $scope.BankNoticeContent = data.rData.content;
				   $('#bank-notice').show();
				   return false;
				  
				}else{
					
					$scope.yjf_goPay();
			
				return false;
				
				}
				
			});
        }
        else if($scope.c_aliPay){
            if(isNaN($scope.payObj.userInputMoney)){
                DHB.showMsg('请输入合法的金额');
                return;
            }
            var params = {
                v: {
                    AliPayID: $scope.onlinePay.aliPay.AccountsID,
                    orderNO: $scope.newOrderSN,
                    AliMoney: $scope.payObj.userInputMoney,
                    AliBody: '无'
                }
            };
            commonService.getDataList(params,DHB.aliPayUrl).success(function(data){
            	if(data.rStatus == 100){
                	//在页面中生成一个存放提交支付信息的容器
                	$('#pay-to-alipay-form').html(data.para);
                	
                	//将支付信息放在容器中
                    return false;
                }
            });
        }
        else if($scope.offLine){
            if($scope.newOrderSN){
                window.localStorage['orderTotal'] = $scope.payObj.userInputMoney;
                $scope.replacePath('/add-pay/'+$scope.newOrderSN);
            }
        }
		
		//去易极付付款
		$scope.yjf_goPay = function(){
			commonService.getDataList({f: 'check_pay_account',v: {sKey: window.localStorage['sKey']}}).success(function(data){
						if(data && data.rStatus != '100'){
							if(data.rStatus =='102'){
								$scope.phone = data.rData.phone;
								$('#edit_phone').show();
							}else{
								 DHB.showMsg(data.error);
							}
							 return false;
						}else{
                            //隐藏修改手机号弹框@maxy add at 20171212 13:28
							$('#edit_phone').hide();
							// 在线支付
						window.sessionStorage['payTotal'] = $scope.payObj.userInputMoney;
						window.sessionStorage['payOid'] = $scope.newOrderId;
						$scope.replacePath('/pay-online/' + $scope.newOrderSN + '/pay-jump');
						}
					});
		}
		
		//开通易极付帐号
		$scope.yjf_add_accounts = function(){
			var newphone=$("#new_phones").val();
			//alert(newphone);
			if(!(/^1[34578]\d{9}$/.test(newphone))){ 
				 DHB.showMsg("手机号码格式不对！");  
				return false; 
			} 
			
			commonService.getDataList({f: 'onlinepay',v: {sKey: window.localStorage['sKey'],phone:newphone} }).success(function(data){
								if(data && data.rStatus == '100'){
									
								// 在线支付
								window.sessionStorage['payTotal'] = $scope.payObj.userInputMoney;
								window.sessionStorage['payOid'] = $scope.newOrderId;
								$scope.replacePath('/pay-online/' + $scope.newOrderSN + '/pay-jump');
								  
								}else{
									 DHB.showMsg(data.message);  
								}
								
							});	
		}
        
        /*if($scope.newOrderSN){
            //$scope.replacePath('/add-pay/'+$scope.newOrderSN);
            $scope.onlinePay.getPayAccount($scope.newOrderSN,$scope.newOrderId);
        }*/
        
        //$scope.changePath('/add-pay/'+orderNO);
    };
    // 注册 or 激活
    $scope.regOrAction = function(){
        delete window.sessionStorage['hasReg'];
        delete window.sessionStorage['hasJump'];
        //TODO 易极付新接口 切换 新接口 启用 $scope.changePath('/act-account'); 注释 if else
        $scope.changePath('/act-account');
        /*if($scope.isAction){
            $scope.replacePath('/act-account');
        }
        else {
            $scope.replacePath('/pay-reg/' + $scope.orderCode);
        }*/
    };
    // 设置支付方式
    $scope.setPayType2 = function(flag){
        if(flag === 1){
            $scope.onLine = true;
            $scope.offLine = false;
            $scope.c_aliPay = false; // 支付宝
            $scope.c_weiXinPay = false; // 快捷支付
        }
        else if(flag === 3){
            $scope.onLine = false;
            $scope.offLine = false;
            $scope.c_aliPay = true; // 支付宝
            $scope.c_weiXinPay = false; // 快捷支付
        }
        else if(flag === 2){
            $scope.offLine = true;
            $scope.onLine = false;
            $scope.c_aliPay = false; // 支付宝
            $scope.c_weiXinPay = false; // 快捷支付
        }
        else if(flag === 4){
            $scope.offLine = false;
            $scope.onLine = false;
            $scope.c_aliPay = false; // 支付宝
            $scope.c_weiXinPay = true; // 快捷支付
        }
            
    };
    // 优化
    $scope.getPayAccountInfo = function(){
        $scope.onlinePay.aliPay = null;
        commonService.getDataList({f: 'getGetWay',v: {sKey: window.localStorage['sKey']}}).success(function(data){
            if(data && data.rStatus == '100'){
                if(data.AliData && !DHB.device.WeiXin){
                    $scope.onlinePay.aliPay = data.AliData;
                }
                $scope.openapi = data.openapi;
                $scope.notActive = data.openapiIscat == '0'; // 1 激活
                if(!data.rData){
                    data.rData = [];
                }
                for(var i = 0; i < data.rData.length; i += 1){
                    if(data.rData[i].IsDefault === 'Y'){
                        $scope.payAccount = data.rData[i];
                        $scope.onlinePay.currAccount = data.rData[i];
                        break;
                    }
                }
                if(data.rData.length > 0 && !$scope.payAccount){
                    $scope.payAccount = data.rData[0];
                }
            }
			
            if($scope.payAccount){
					
					//$scope.onLine = true;
					$scope.isQuickPay=true;
            }else{
                $scope.onLine = false;
                $scope.offLine = true;
            }
            $scope.replacePath('/my-cart/submit-success');
        }).error(function(){
            $scope.replacePath('/my-cart/submit-success');
        });
    };
    
    
    //查看订单
    $scope.lookOrder = function(){
        window.localStorage['newOrderId'] = $scope.newOrderId;
        $scope.replacePath('/order-list/0');
    };
    // 提交订单 isSubmit用于控制用户重复提交表单
    $scope.isSubmit = false;
    $scope.submitOrder = function(){
        if($scope.isSubmit){
            return;
        }
        if(!$scope.selectPay.TypeID){
            DHB.showMsg('请选择付款方式');
            return;
        }
        if(!$scope.selectSend.TypeID){
            DHB.showMsg('请选择运货方式');
            return;
        }
        if(!$scope.selectAddress.AddressID){
            DHB.showMsg('请选择收货地址');
            return;
        }
        if($scope.deliveryTime == 'B' && !$scope.orderSubmit.deliveryTime){
            DHB.showMsg('请选择交货日期');
            return;
        }
        $scope.isSubmit = true;
        var cartItems = [];
        for(var i = 0; i < $scope.checkedGoodses.length; i += 1){
            cartItems[i] = {
                contentId: $scope.checkedGoodses[i].id,
                color: $scope.checkedGoodses[i].color,
                spec: $scope.checkedGoodses[i].specify,
                number: $scope.checkedGoodses[i].num
            };
        }
        var orderParams = {
            f: 'submitOrder',
            v: {
                payType: $scope.selectPay.TypeID,
                sendType: $scope.selectSend.TypeID,
                invoiceType: 'N', // 开票类型
                addressId: $scope.selectAddress.AddressID,
                deliveryDate: $scope.orderSubmit.deliveryTime, // 交货时间
                orderRemark: $scope.orderSubmit.orderRemark, // 备注
                orderFrom: DHB.device.WeiXin ? 'WeiXin' : (DHB.device.Ios ? 'Ios' : 'Android'), // 订单来源
                cartItems: cartItems
            }
        };
        if(DHB.device.WeiXin && !is_weixin()){
            orderParams.v.orderFrom = 'Mobile';
        }
        if(window.sessionStorage['orderType']){
            orderParams.v.orderType = window.sessionStorage['orderType'];
            orderParams.v.userId = window.sessionStorage['userId'];
        }
        commonService.getDataList(orderParams).success(function(data){
            $scope.isSubmit = false;
            if(data.rStatus == 100){
                
                // DHB.showMsg('提交成功!');
                if(data.rData.OrderTotal){
                    window.localStorage['orderTotal'] = $scope.payObj.userInputMoney = data.rData.OrderTotal; 
                } else {
                    window.localStorage['orderTotal'] = $scope.payObj.userInputMoney = $scope.totalAmount; 
                }    
                $scope.totalAmount2 = data.rData.OrderTotal || $scope.totalAmount; 
                //清楚已结算的商品
                $scope.clearSelectGoods();
                $scope.newOrderSN = data.rData.OrderSN;
                $scope.newOrderId = data.rData.OrderID;
                
                $scope.getPayAccountInfo();
            }else if(data.rStatus == '101'){
                DHB.showMsg(data.error); 
            }
            
        }).error(function(){
            $scope.isSubmit = false;
        });
    };
}])

/**
 *  订单控制器 
 *  commonService 公共service 主要作用是为每个控制器提供需要用到加载数据以及依稀数据操作的方法
 */
.controller('orderController',['$scope','commonService','$location',function($scope,commonService,$location){
    $scope.service = commonService;
    // 绑定查询日期区间
    $scope.dateParam = {
        beginDate: '',
        endDate: ''
    }
    // 订单
    $scope.orderes = {
        index: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        selectOrder: {},
        noData: false,
        orderMsg: '',
        totalPage: 0,
        currPage: 0,
        isCollect: false,
        orderList: [],
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.orderList = [];
            this.noData = false;
            this.params.v = {
                begin: 0,
                step: 10
            };
            if(this.isCollect){
                this.params.v.isCollect = 1;
            }
        },
        params: {
            f: 'getOrderList',
            v: {
                begin: 0, // 起始下标
                //orderStatus: 44, //0:待审核,1:备货中,2:已出库,3:已收货,5:已收款,7:已完成,8:客户端取消,9:管理端取消
                //orderBy: 1, //排序
                step: 10 //每次请求条数
            }
        },
        getOrderList: function(){
            var _this = this;
            // 获取订单列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.orderList.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                if(data && data.rStatus == '100'){
                    $scope.dateParam.beginDate = '';
                    $scope.dateParam.endDate = '';
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            _this.orderList.push(new Order(data.rData[i]));
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                        // 新增订单 点击查看按钮
                        if(window.localStorage['newOrderId']){
                            _this.getOrderInfo(0,window.localStorage['newOrderId']);
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300)
                    }
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getOrderList();
            }
        },
        //获取订单详情
        getOrderInfo: function(index,orderId){
            this.index = index;
            var _this = this;
            // query param
            var params = {
                f: 'getOrderContent',
                v: {
                    orderId: orderId //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                var order = _this.orderList[index];
                if(data.rData){
                    if(!DHB.isPushState){
                        DHB.isPushState = true;
                        window.history.pushState(null,null,location.href);
                    }
                    
                    window.localStorage['orderTotal'] = (parseFloat(data.rData.header.OrderTotal) - parseFloat(data.rData.header.OrderIntegral)).toFixed(2);
                    //order detail header
                    order.OrderStatus = data.rData.header.OrderStatus; // 订单状态
                    order.OrderStatusName = data.rData.header.OrderStatusName; // 订单状态
                    order.OrderSendStatus = data.rData.header.OrderSendStatus; // 发货状态
                    order.OrderSendStatusName = data.rData.header.OrderSendStatusName; // 发货状态
                    order.OrderPayStatus = data.rData.header.OrderPayStatus; // 付款状态
                    order.OrderPayStatusName = data.rData.header.OrderPayStatusName; // 付款状态
                    
                    order.OrderReceiveCompany = data.rData.header.OrderReceiveCompany; // 收货单位
                    order.OrderReceiveName = data.rData.header.OrderReceiveName; // 收货人
                    order.OrderReceivePhone = data.rData.header.OrderReceivePhone; // 联系电话
                    order.OrderReceiveAdd = data.rData.header.OrderReceiveAdd; // 收货地址
                    order.InvoiceType = data.rData.header.InvoiceType; // 开票
                    order.InvoiceTax = data.rData.header.InvoiceTax; // 税点
                    order.DeliveryDate = data.rData.header.DeliveryDate; //  交货时间
                    order.OrderType = data.rData.header.OrderType; // 下单用户
                    order.OrderSaler = data.rData.header.OrderSaler; // 是否销售代表代下
                    order.OrderFrom = data.rData.header.OrderFrom; // 来源*
                    order.OrderRemark = data.rData.header.OrderRemark; // 备注
                    order.OrderIntegral = data.rData.header.OrderIntegral; // 已支付金额
                   //详细 body
                    order.goodses = data.rData.body;
                    // 操作日志
                    order.log = data.rData.log;
                    
                    if(window.localStorage['newOrderId']){
                        delete window.localStorage['newOrderId'];
                        setTimeout(function(){
                            DHB.toggleMenu(0);
                        },100);
                    }else{
                        DHB.toggleMenu(0);
                    }
                    $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                    //_this.showLayer = 1;
                    _this.selectOrder = order;
                }
            }).error(function(data){
                DHB.toggleMenu(1);
                //alert('获取数据失败!');
            });
        },
        //close layer
        closeLayer: function(isBack){
            //this.showLayer = 2;
            $('div.info-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            DHB.toggleMenu(1);
            this.orderMsg = '';
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
        },
        
        setIsCollect: function(){
            this.isCollect = !this.isCollect;
        },
        //根据筛选条件查询列表 type 0 订单状态，1 订单发货状态，2 订单付款状态
        getOrderListByFilter: function(type,e){
            if(type == 3){
                if(!this.isCollect && !$scope.dateParam.beginDate && !$scope.dateParam.endDate){
                    DHB.toggleMenu(1);
                    $('div.filter-layer').removeClass('fadeInRight').addClass('fadeOutRight');
                    return;
                }
            }
            var target = e.target;
            if(target.tagName.toLowerCase() == 'li' || target.tagName.toLowerCase() == 'span'){
                this.clear();
                if(type == 0){
                    this.params.v.orderStatus = target.getAttribute('data-orderstatus');
                }else if(type == 1){
                    this.params.v.orderSendStatus = target.getAttribute('data-sendstatus');
                }else if(type == 2){
                    this.params.v.orderPayStatus = target.getAttribute('data-paystatus');
                }else if(type == 3){
                    if($scope.dateParam.beginDate){
                        this.params.v.beginDate = $scope.dateParam.beginDate;
                    }
                    if($scope.dateParam.endDate){
                        this.params.v.endDate = $scope.dateParam.endDate;
                    }
                }
                
                this.getOrderList();
                //dom 操作 不推荐
                DHB.toggleMenu(1);
                $('div.filter-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            }
        },
        // 复制订单
        submitCartCache: function(data){
            var goodses = data;
            var cartData = [];
            /*for(var k = 0; k < data.length; k += 1){
                goodses.push(data[k])
            }*/
            for(var i = 0; i < goodses.length; i += 1){
                cartData[i] = {
                    id: goodses[i].id,
                    color: goodses[i].color,
                    specify: goodses[i].specify,
                    num: goodses[i].num,
                };
                
            }
            var params = {
                f: 'submitCartCache',
                v: {
                   cartData: JSON.stringify(cartData)
                }
            };
            
            window.localStorage.cart = JSON.stringify(goodses);
            if(window.sessionStorage['orderType']) return;
            commonService.saveData(params).success(function(data){
                if(data.rStatus == '100'){
                    DHB.showMsg('操作成功!');
                    $scope.changePath('/my-cart');
                }
            }).error(function(data){
                
            });
        },
        // 复制订单
        copyOrder: function(index,e){
            e.stopPropagation();
            var orderId = this.orderList[index].OrderID;
            var _this = this;
            var params = {
                f: 'getCopyOrder',
                v: {
                    orderId: orderId //订单ID
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data.rStatus == '100'){
                    _this.submitCartCache(data.rData);
                }
            }).error(function(data){
                
            });
        }
    };
    $scope.orderes.getOrderList();
    
    $scope.orderPay = function(){
        // 后台请求供应商是否开通在线支付
        DHB.isPushState = false;
        $scope.onlinePay.getPayAccount($scope.orderes.selectOrder.OrderSN,$scope.orderes.selectOrder.OrderID);
    };
    
    
    window.onpopstate = function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.orderes.closeLayer(true);
        }
    };
}])

/**
 *  发货单控制器 
 *  commonService 公共service 主要作用是为每个控制器提供需要用到加载数据以及依稀数据操作的方法
 */
.controller('transportController',['$scope','commonService',function($scope,commonService){
    // 订单
    $scope.transportOrders = {
        index: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        selectOrder: null,
        noData: false,
        logists: [],
        totalPage: 0,
        currPage: 0,
        orderList: [],
        hasMore: false,
        clear: function(){
            this.index = -1;
            this.orderList = [];
            this.noData = false;
            this.params.v.begin = 0;
        },
        params: {
            f: 'getConsignmentList',
            v: {
                begin: 0, // 起始下标
                //orderStatus: 44, //0:待审核,1:备货中,2:已出库,3:已收货,5:已收款,7:已完成,8:客户端取消,9:管理端取消
                orderBy: 1, //排序
                step: 10 //每次请求条数
            }
        },
        getOrderList: function(){
            var _this = this;
            // 获取发货单列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.orderList.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                }
                if(data && data.rStatus == '100'){
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            _this.orderList.push(new Transport(data.rData[i]));
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }                       
                    }
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getOrderList();
            }
        },
        //获取发货单详情
        getOrderInfo: function(index,consignmentId){
            this.logists = [];
            var _this = this;
            // query param
            var params = {
                f: 'getConsignmentContent',
                v: {
                    consignmentId: consignmentId //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                var order = _this.orderList[index];
                if(data.rData){
                    if(!DHB.isPushState){
                        DHB.isPushState = true;
                        window.history.pushState(null,null,location.href);
                    }
                    //order detail header
                    order.ConsignmentFlag = data.rData.header.ConsignmentFlag; // 收货状态
                    order.ConsignmentFlagName = data.rData.header.ConsignmentFlagName || order.ConsignmentFlagName; // 收货状态
                    order.ConsignmentRemark = data.rData.header.ConsignmentRemark; // 备注
                    order.ConsignmentMoneyType = data.rData.header.ConsignmentMoneyType; // 运费付款方式
                    order.ConsignmentMoney = data.rData.header.ConsignmentMoney; // 运费
                    order.InceptMan = data.rData.header.InceptMan; // 收货人
                    order.InceptCompany = data.rData.header.InceptCompany;// 收货单位
                    order.InceptPhone = data.rData.header.InceptPhone; // 联系电话
                    order.InputDate = data.rData.header.InputDate; // 操作是时间
                    order.LogisticsName = data.rData.header.LogisticsName; // 物流公司
                    order.LogisticsCode = data.rData.header.LogisticsCode; // 物流公司CODE
                    
                   //详细 body
                    order.goodses = data.rData.body;
                    
                    DHB.toggleMenu(0);
                    $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                    //_this.showLayer = 1;
                    _this.selectOrder = order;
                }
            }).error(function(data){
                DHB.toggleMenu(1);
            });
        },
        //获取物流信息.
        getLogist: function(){
            if(this.logists.length > 0){return;}
            if(this.selectOrder.ConsignmentNO=='暂无'){
                DHB.showMsg('暂无物流信息!');
                return;
            }
            var _this = this;
            // query param
            var params = {
                f: 'getKuaidi',
                v: {
                    kuaidiNo: this.selectOrder.ConsignmentNO, //物流编号
                    kuaidiCode: this.selectOrder.LogisticsCode //物流公司编号
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData){
                    if(data.rData.status == '0'){
                        DHB.showMsg('暂无物流信息!');
                        return;
                    }
                    _this.logists = data.rData.data;
                }
            }).error(function(data){
                
            });
        },
        // 确认收货
        sureReceive: function(){
            var _this = this;
            // query param
            var params = {
                f: 'setConsignment',
                v: {
                    action: 'confirm', //操作
                    consignmentId: this.selectOrder.ConsignmentID //运单id
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data.rStatus){
                    _this.selectOrder.ConsignmentFlag = '1';
                    _this.selectOrder.ConsignmentFlagName = '确认收货';
                }
            }).error(function(data){
                
            });
        },
        //close layer
        closeLayer: function(isBack){
            //this.showLayer = 2;
            $('div.info-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            DHB.toggleMenu(1);
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
        }
    };
    $scope.transportOrders.getOrderList();
    window.onpopstate =function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.transportOrders.closeLayer(true);
        }
    };
}])

.controller('actionController',['$scope','$stateParams','commonService',function($scope,$stateParams,commonService){
    delete window.sessionStorage['hasReg'];
    delete window.sessionStorage['hasJump'];
    $scope.changePath('/order-payment/0');
}])
/**
 *  付款单控制器 
 *  commonService 公共service 主要作用是为每个控制器提供需要用到加载数据以及依稀数据操作的方法
 */
.controller('paymentController',['$scope','$stateParams','commonService',function($scope,$stateParams,commonService){
    /*$scope.$on('$stateChangeStart',
        function(evt, toState, roParams, fromState, fromParams) {
        // 可以阻止这一状态完成
        console.log(toState);
        if(toState.name == 'action'){
            evt.preventDefault();
        }
        
    });*/
    var from = $stateParams.v;
    // 订单
    $scope.paymentOrders = {
        index: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        selectOrder: null,
        noData: false,
        totalPage: 0,
        currPage: 0,
        orderList: [],
        hasMore: false,
        clear: function(){
            this.index = -1;
            this.orderList = [];
            this.noData = false;
            this.params.v.begin = 0;
        },
        params: {
            f: 'getFinanceList',
            v: {
                begin: 0, // 起始下标
                //orderStatus: 44, //0:待审核,1:备货中,2:已出库,3:已收货,5:已收款,7:已完成,8:客户端取消,9:管理端取消
                orderBy: 1, //排序
                step: 10 //每次请求条数
            }
        },
        getOrderList: function(){
            var _this = this;
            // 获取收款单列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.orderList.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                }
                if(data && data.rStatus == '100'){
                    if(data.rData && data.rData.length){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            _this.orderList.push(new Payment(data.rData[i]));
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }                       
                    }
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getOrderList();
            }
        },
        //获取收款单详情
        getOrderInfo: function(index,financeId){
            var _this = this;
            // query param
            var params = {
                f: 'getFinanceContent',
                v: {
                    financeId: financeId //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                var order = _this.orderList[index];
                if(data.rData){
                    if(!DHB.isPushState){
                        DHB.isPushState = true;
                        window.history.pushState(null,null,location.href);
                    }
                    //order detail
                    order.FinanceFlag = data.rData.FinanceFlag; // 状态 
                    order.FinanceFlagName = data.rData.FinanceFlagName; // 状态 
                    order.FinanceAbout = data.rData.FinanceAbout; // 备注
                    order.FinanceType = data.rData.FinanceType; // 类型
                    order.AccountsBank = data.rData.AccountsBank; // 银行
                    order.AccountsNO = data.rData.AccountsNO; // 帐号
                    order.AccountsName = data.rData.AccountsName; // 开户名
                    
                    DHB.toggleMenu(0);
                    $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                    //_this.showLayer = 1;
                    _this.selectOrder = order;
                }
                
                //alert(JSON.stringify(_this.selectOrder));
            }).error(function(data){
                //alert('获取数据失败!');
                DHB.toggleMenu(1);
            });
        },
        //close layer
        closeLayer: function(isBack){
            //this.showLayer = 2;
            $('div.info-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            DHB.toggleMenu(1);
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
        }
    };
    $scope.paymentOrders.getOrderList();
    window.onpopstate =function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.paymentOrders.closeLayer(true);
        }
        if(from == 'pay'){
            $scope.changePath('/order-payment/pay');
            $scope.$apply();
        }
    };
}])

/**
 *  退款单控制器 
 *  commonService 公共service 主要作用是为每个控制器提供需要用到加载数据以及依稀数据操作的方法
 */
.controller('returnsController',['$scope','commonService',function($scope,commonService){
    // 退单
    $scope.returnsOrders = {
        index: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        selectOrder: null,
        noData: false,
        totalPage: 0,
        currPage: 0,
        orderList: [],
        hasMore: false,
        clear: function(){
            this.index = -1;
            this.orderList = [];
            this.noData = false;
            this.params.v.begin = 0;
        },
        params: {
            f: 'getReturnList',
            v: {
                begin: 0, // 起始下标
                //orderStatus: 44, //0:待审核,1:备货中,2:已出库,3:已收货,5:已收款,7:已完成,8:客户端取消,9:管理端取消
                orderBy: 1, //排序
                step: 10 //每次请求条数
            }
        },
        getOrderList: function(){
            var _this = this;
            // 获取退货单列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.orderList.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                }
                if(data && data.rStatus == '100'){
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            _this.orderList.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }                       
                    }
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getOrderList();
            }
        },
        //获取退货单详情
        getOrderInfo: function(index,returnId){
            var _this = this;
            // query param
            var params = {
                f: 'getReturnContent',
                v: {
                    returnId: returnId //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                var order = _this.orderList[index];
                if(data.rData){
                    if(!DHB.isPushState){
                        DHB.isPushState = true;
                        window.history.pushState(null,null,location.href);
                    }
                    //order detail header
                    
                    //详细 body
                    order.goodses = data.rData.body;
                    //log
                    order.log = data.rData.log;
                    
                    DHB.toggleMenu(0);
                    $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                    //_this.showLayer = 1;
                    _this.selectOrder = order;
                }
                
                //alert(JSON.stringify(_this.selectOrder));
            }).error(function(data){
                //alert('获取数据失败!');
                DHB.toggleMenu(1);
            });
        },
        //close layer
        closeLayer: function(isBack){
            //this.showLayer = 2;
            $('div.info-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            DHB.toggleMenu(1);
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
        }
    };
    $scope.returnsOrders.getOrderList();
    window.onpopstate =function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.returnsOrders.closeLayer(true);
        }
    };
}])

/**
 * 商品详情控制器 
 */
.controller('goodsDetailController',['$scope','$stateParams','commonService',function($scope,$stateParams,commonService){
    //$scope.goodsDetail = {};
    $scope.choiceMore.isChoiceMore = false;
    var params = {
        f: 'getGoodsContent',
        v: {
            contentId: $stateParams.goodsId
        }
    };
    
    commonService.getDataList(params).success(function(data){
        DHB.colorIndex = 0;
        DHB.specIndex = 0;
        if(data.rData){
            switch(data.rData.CommendID){
                case '1':
                    data.rData.CommendName = '推荐';
                    break;
                case '2':
                    data.rData.CommendName = '特价';
                    break;
                case '3':
                    data.rData.CommendName = '新款';
                    break;
                case '4':
                    data.rData.CommendName = '热销';
                    break;
                case '10':
                    data.rData.CommendName = '收藏';
                    break;
            }
            $scope.goodsDetail = data.rData;
            $scope.favText = $scope.goodsDetail.isFav == 'Y' ? '已收藏' : '收藏';
            if(data.rData.Color){
                $scope.goodsDetail.Color = data.rData.Color.split(',');
                $scope.goods.color = $scope.goodsDetail.Color[0];
            }
            if(data.rData.Specification){
                $scope.goodsDetail.Specification = data.rData.Specification.split(',');
                $scope.goods.specify = $scope.goodsDetail.Specification[0];
            }
            
            $scope.goods.id = $scope.goodsDetail.ID;
            // 默认选中颜色规格  计算数量
            if(data.rData.Color || data.rData.Specification){
                // md5 加密 hex_md5('256红色0.5') id + color + specification
                var compare = hex_md5($scope.goods.id + $scope.goods.color + $scope.goods.specify);
                if($scope.goodsDetail.library){
                    for(var key in $scope.goodsDetail.library){
                        if(key == compare){
                            $scope.goodsDetail.allLibrary = $scope.goodsDetail.library[key];
                            break;
                        }
                    }
                }
            }

            
            $scope.goods.units = $scope.goodsDetail.Units;
            $scope.goods.code = $scope.goodsDetail.Coding;
            $scope.goods.num = $scope.goodsDetail.Package && $scope.goodsDetail.Package != 0 && $scope.goodsDetail.Package > $scope.goodsDetail.allLibrary ? $scope.goodsDetail.Package : 
                ($scope.goodsDetail.allLibrary == 0 ? 0 : 1 );
            if($scope.goodsDetail.Package && parseInt($scope.goodsDetail.Package) !== 0){
                $scope.goods.num = 0;
            }
            $scope.goods.pack = $scope.goodsDetail.Package;
            $scope.goods.stock = $scope.goodsDetail.allLibrary;
            $scope.goods.isStock = $scope.goodsDetail.controllerLibrary && $scope.goodsDetail.controllerLibrary == 'Y';
            $scope.goods.isShow = $scope.goodsDetail.showLibrary;
        }
    }).error(function(data){
        
    });
    
        
    //加入购物车需要存储的属性
    $scope.goods = {  // 1、比较相同  id+规格+ 颜色    2、库存 3、预留   4、是否需要比较库存
        id: '',
        code: '',
        units: '',
        name: '',
        num: 0,   // 订购数量
        compare: '',// 比较属性 相同表示已添加
        stock: 0,  //库存
        isStock: false,  // 是否需要判断库存
        isShow: 'Y',  // 是否需要显示库存 Y 显示 N 不显示
        pack: '',
        price: 0,
        color: '',
        specify: '',
        pic: ''
    };
    
    //收藏 action add为收藏 remove为取消收藏
    $scope.favText = '';
    $scope.setContentFav = function(action){
        if($scope.goodsDetail.isFav == 'Y'){
            action = 'remove';
        }
        commonService.setContentFav($stateParams.goodsId,action).success(function(data){
            if(data.rStatus == '100'){
                $scope.goodsDetail.isFav = $scope.goodsDetail.isFav == 'Y' ? 'N' : 'Y';
                $scope.favText = $scope.goodsDetail.isFav == 'Y' ? '已收藏' : '收藏';
                if(action=='remove'){
                    DHB.showMsg('取消成功!');
                    return;
                }
                DHB.showMsg('收藏成功!');
            }
        }).error(function(data){
            
        });
    };
    
    // 刷新购物车
    $scope.refreshCart = function(){
        var cartData = [];
        var temp = JSON.parse(window.localStorage.cart);
        for(var i = 0; i < temp.length; i += 1){
            cartData[i] = {
                id: temp[i].id,
                color: temp[i].color,
                specify: temp[i].specify,
                num: temp[i].num,
            };
            
        }
        if(window.sessionStorage['orderType']) return;
        var params = {
            f: 'submitCartCache',
            v: {
               cartData: JSON.stringify(cartData)
            }
        };
        
        commonService.saveData(params).success(function(data){
            if(data.rStatus == '100'){
                //DHB.showMsg('添加成功');
            }
            else{
                DHB.showMsg(data.error);
            }
        }).error(function(data){
            DHB.showMsg('添加失败');
        });
    };

}])

/**
 * 商品分类控制器 
 */
.controller('goodsSortController',['$scope',function($scope){
    var params = {
        f: 'getGoodsSort',
        v: {
            //parentId: 0
        }
    };
    if($scope.goodsSort.sortList.length == 0){
        $scope.getGoodsSort(params);
    }
    
    //下级商品分类
    $scope.nextSort = function(e){
        var target = e.target;
        if(target.nodeName.toLowerCase() == 'li'){
            if(target.getAttribute('data-siteId') == '-1'){
                $scope.globalSearch.commendId = '';
                $scope.changePath('/goods-list/' + 0);
                return;
            }
            if($scope.goodsSort.clickIndex != -1){
                // 此处操作了DOM 不推荐
                $(target).parent().find('li').eq($scope.goodsSort.clickIndex).removeClass('type-active')
            }
            $scope.goodsSort.clickIndex = $(target).addClass('type-active').index();
            $scope.goodsSort.secondSort = [];
            var siteId = target.getAttribute('data-siteId');
            var findDefault = false;
            for (var i=0; i < $scope.goodsSort.sortList.length; i++) {
                if($scope.goodsSort.sortList[i].ParentID == siteId){
                    $scope.goodsSort.secondSort.push($scope.goodsSort.sortList[i]);
                }
                if(!findDefault && $scope.goodsSort.sortList[i].SiteID == siteId){
                    $scope.goodsSort.defaultSite = $scope.goodsSort.sortList[i];
                    findDefault = true;
                }
            }
            if($scope.goodsSort.secondSort.length == 0){
                $scope.changePath('/goods-list/' + siteId)
            }
        }
    };
}])
.controller('updatePayController',['$scope',function($scope){
    if(window.localStorage['updatePay']){
        delete window.localStorage['updatePay'];
        window.history.back();
    }else{
        var params = {
            m: 'goLogin',
            isApp: 'true',
            sKey: window.localStorage.sKey
        }
        if(DHB.device.WeiXin){
            $('#update-pay-form').removeAttr('target');
            params.isApp = 'false';
        }
        $('#update-pay').val(JSON.stringify(params));
        $('#update-pay-form').parent().parent().show();
        
        window.localStorage['updatePay'] = 'true';
        
//        $('#update-pay-form').attr('action', window.location.href.replace(window.location.pathname,'')+'/mobileApi/yijifuurl.php');
        $('#update-pay-form').submit();
    }
    $(window).one('message',function(e){
        $scope.changePath('/my-home');
        $scope.$apply();
    },false);
}])
/**
 * 我的主页控制器 
 */
.controller('myhomeController',['$scope','commonService',function($scope,commonService){
    delete window.localStorage['updatePay'];
    // 余额
    $scope.amount = 0;
    $scope.isWeiXin = DHB.device.WeiXin && is_weixin() && !window.sessionStorage['tradeId'];
    
    // 积分
    $scope.point = 0;
    
    var params = {
        f: 'getAmount',
        v: {}
    }
    commonService.getDataList(params).success(function(data){
        if(data.rStatus == '100'){
            $scope.amount = data.rAmount;
            $scope.point = data.rPoint == '' ? 0 : data.rPoint;
            
        }
    }).error(function(data){
        DHB.showMsg('获取数据失败!');
    });
    
    // 密码修改
    $scope.updatePwd = {
        isCommit: false,
        oldPwd: '',
        newPwd: '',
        repeatPwd: '',
        setPassword: function(){
            if(this.isCommit){
                return;
            }
            if(this.oldPwd == '' || this.newPwd == '' || this.repeatPwd == ''){
                DHB.showMsg('输入信息不完整!');
                return;
            }
            if(this.newPwd != this.repeatPwd){
                DHB.showMsg('两次输入的密码不一致!');
                return;
            }
            var params = {
                f: 'setPassword',
                v: {
                    oldPassword: this.oldPwd,
                    newPassword: this.newPwd
                }
            };
            this.isCommit = true;
            commonService.saveData(params).success(function(data){
                $('div.edit-pwd').hide();
                $scope.updatePwd.isCommit = false;
                if(data.rStatus == '100'){
                    DHB.showMsg('密码修改成功!');
                    
                    //重新登录
                    //$scope.setLoginOut();
                }else{
                    DHB.showMsg('密码修改失败!');
                }
            }).error(function(data){
                DHB.showMsg('密码修改失败!');
                $scope.updatePwd.isCommit = false;
            });
        }
    };
    $scope.updatePay = function(){
        $scope.changePath('update-pay');
        
    };
    // 登出
    $scope.setLoginOut = function(){
        var params = {
            f: 'setLoginOut',
            v: {}
        }
        commonService.saveData(params).success(function(){
            delete window.localStorage['sKey'];
            delete window.localStorage['cart'];
            delete window.sessionStorage['orderType'];
            delete window.sessionStorage['userId'];
            location.href = '../login.html';
        }).error(function(){
            location.href = '../login.html';
        });
    };
    $scope.setRemoveWerxin = function(){
        if(!window.localStorage['openId']){
            $scope.setLoginOut();
        }
        else{
            var params = {
                f: 'setRemoveWerxin',
                v: {
                    sKey: window.localStorage['sKey'],
                    openId: window.localStorage['openId']
                }
            }
            commonService.saveData(params).success(function(data){
                if(data.rStatus == '100'){
                    window.sessionStorage['loginOut'] = true;
                    $scope.setLoginOut();
                }else{
                    DHB.showMsg(data.error);
                }
            });
        }
    };
    // 地址管理
    $scope.address = [];
    $scope.noData = false;
    var d_index = 0; // 默认送货地址列表下标
    $scope.addrs = {
        addrAddress: '',
        addrCompany: '',
        addrContact: '',
        addrPhone: '',
        addrTitle:'',
        addrIndex: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        isSubmit: false,
        selectAddr: {},
        actionType: 0, // 0 表示新增 ，1表示编辑 ，2表示删除
        params: {
            f: 'setAddress',
            v: {
                action: '',
                addressContact: '',//联系人
                addressPhone: '', // 电话
                addressAddress: '',//地址
                addressCompany: '',//单位
                addressId: ''
                
            }
        },
        setActionType: function(type,index){
            if(index || index === 0){
                this.addrIndex = index;
            }
            this.actionType = type;
            this.addrTitle = type===0 ? '新增地址' : (type===1 ? '修改地址' : '确定删除该地址吗?');
            this.params.v.action = type===0 ? 'add' : (type===1 ? 'edit' : 'del');
            if(type !== 0){
                this.selectAddr = $scope.address[index];
            }else{
                this.selectAddr = {};
            }
        },
        setDefault: function(index,e){
            e.stopPropagation();
            var select_addr = $scope.address[index];
            if(select_addr.AddressFlag == '1') return;
            var params = {
                f: 'setAddress',
                v: {
                    action: 'default',
                    addressId: select_addr.AddressID
                }
            };
            commonService.saveData(params).success(function(data){
                if(data.rStatus == '100'){
                    $scope.address[d_index].AddressFlag = '0';
                    $scope.address[d_index].defaultText = '设为默认';
                    d_index = index;
                    select_addr.AddressFlag = '1';
                    select_addr.defaultText = '默认地址';
                }
            }).error(function(data){
                
            });
        },
        setAddress: function(isValid){
            if(!isValid && this.actionType !== 2){
                DHB.showMsg('信息不完整!')
                return;
            }
            //防止重复提交
            if(this.isSubmit){
                return;
            }
            
            // 设置参数
            this.params.v.addressAddress = this.selectAddr.AddressAddress;
            this.params.v.addressCompany = this.selectAddr.AddressCompany;
            this.params.v.addressContact = this.selectAddr.AddressContact;
            this.params.v.addressPhone = this.selectAddr.AddressPhone;
            this.params.v.addressId = this.selectAddr.AddressID;
            
            if(this.actionType === 2){
                this.params.v = {
                    action: 'del',
                    addressId: this.selectAddr.AddressID
                }
            }
            this.isSubmit = true;
            var _this = this;
            commonService.saveData(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                _this.isSubmit = false;
                if(data.rStatus == '100'){
                    $('div.addr-manager').hide();
                    // 从当前列表删除该地址
                    if(_this.actionType === 2){
                        $scope.address.splice(_this.addrIndex,1);
                    }
                    // 更新当前地址列表
                    if(_this.actionType === 0){
                        _this.selectAddr.AddressFlag = '0';
                        _this.selectAddr.defaultText = '设为默认';
                        $scope.address.push(_this.selectAddr);
                        _this.selectAddr.AddressID = data.insertId;
                    }
                    if($scope.address.length == 0){
                        $scope.noData = true;
                    }else{
                        $scope.noData = false;
                    }
                }else{
                   DHB.showMsg(data.error); 
                }
            }).error(function(){
                _this.params = JSON.parse(_this.params);
                _this.isSubmit = false;
            });
        },

        closeLayer: function(isBack){
            DHB.toggleMenu(1);
            $('div.info-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
            //this.showLayer = 2;
        }
    };
    
    window.onpopstate =function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.addrs.closeLayer(true);
        }
    };
    
    
    $scope.getAddress = function(){
        if(this.address.length > 0){
            DHB.toggleMenu(0);
            if(!DHB.isPushState){
                DHB.isPushState = true;
                window.history.pushState(null,null,location.href);
            }
            $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
            //$scope.addrs.showLayer = 1;
            return;
        }
        var payParams = {
            f: 'getAddress',
            v: {}
        };
        commonService.getDataList(payParams).success(function(data){
            if(!DHB.isPushState){
                DHB.isPushState = true;
                window.history.pushState(null,null,location.href);
            }
            DHB.toggleMenu(0);
            $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
            //$scope.addrs.showLayer = 1;
            if(data.rStatus == '100' && data.rData){
                $scope.address = data.rData;
                // 循环找出默认地址
                for (var i = 0; i < $scope.address.length; i += 1) {
                    if($scope.address[i].AddressFlag == 1){
                        $scope.address[i].checked = true;
                        $scope.address[i].defaultText = '默认地址';
                        $scope.selectAddress = $scope.address[i];
                        d_index = i;
                    }else{
                        $scope.address[i].defaultText = '设为默认';
                    }
                }
            }
            if($scope.address.length == 0){
                $scope.noData = true;
            }
        }).error(function(data){
            
        });
    };
}])

/**
 * 发现控制器 
 */
.controller('findController',['$scope','commonService',function($scope,commonService){
    if($scope.contacts == null){
        $scope.getContactTools();
    }
    
}])

/**
 * 在线客服控制器 
 */
.controller('forumController',['$scope','commonService',function($scope,commonService){
    
    // 在线客服
    $scope.forums = {
        index: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        selectForum: null,
        totalPage: 0,
        currPage: 0,
        forumList: [],
        noData: false,
        msgTitle: '',
        msgType: 0,
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.forumList = [];
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'getForum',
            v: {
                begin: 0, // 起始下标
                step: 10 //每次请求条数
            }
        },
        getForumList: function(){
            var _this = this;
            // 获取留言列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.forumList.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                if(data && data.rStatus == '100'){
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            data.rData[i]['Date'] = data.rData[i]['Date'] * 1000;
                            _this.forumList.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }                       
                    }
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        getForumInfo: function(index){
            if(!DHB.isPushState){
                DHB.isPushState = true;
                window.history.pushState(null,null,location.href);
            }
            
            this.index = index;
            this.selectForum = this.forumList[index];
            DHB.toggleMenu(0);
            $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
            //this.showLayer = 1;
        },
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getForumList();
            }
        },
        
        //close layer
        closeLayer: function(isBack){
            //this.showLayer = 2;
            $('div.info-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            DHB.toggleMenu(1);
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
        },
        
        setMsgType: function(type){
            this.msgType = type;
            if(type === 0 ){
                this.msgTitle = '我要留言';
                return;
            }
            this.msgTitle = '回复留言';
        }
    };
    $scope.forums.getForumList();
    window.onpopstate =function(e) {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.forums.closeLayer(true);
        }
    };
    
    // 留言个回复留言对象
    $scope.leaveMsg = {
        isSubmit: false,
        forumTitle: '',
        forumName: $scope.myInfo.ClientTrueName,
        forumContent: '',
        params: {
            f: 'submitForum',
            v: {}
        },
        submitForumOrReply: function(valid){
            //如果已经提交处理 则返回
            if(this.isSubmit){
                return;
            }
            //验证用户是否输入信息
            if(!valid){
                DHB.showMsg('输入信息不完整!');
                return;
            }
            this.isSubmit = true;
            this.params.v = {
                forumContent: this.forumContent
            };
            //留言操作
            if($scope.forums.msgType === 0 ){ 
                if(this.forumTitle == ''){
                    DHB.showMsg('输入信息不完整!');
                    return; 
                }
                this.params.v.forumTitle = this.forumTitle;
                this.params.v.forumName = this.forumName;
            }
            // 回复留言操作
            else {
                this.params.f = 'replyForum';
                delete this.params.v.forumContent;
                this.params.v.parentId = $scope.forums.selectForum.ID;
                this.params.v.replyName = this.forumName;
                this.params.v.replyContent = this.forumContent;
            }
            var _this = this;
            commonService.saveData(this.params).success(function(data){
                _this.isSubmit = false;
                // $scope.forums.params.v.begin = 0;
                if(data.rStatus == '100'){
                    // 动态添加一条数据到留言列表中
                    if($scope.forums.msgType === 0){
                        var msgObj = {
                            Content: _this.forumContent,
                            Date: new Date().getTime(),
                            ID: data.insertId,
                            Name: _this.forumName,
                            Reply: 0,
                            ReplyDate: 0,
                            Title: _this.forumTitle,
                            replyData: []
                        };
                        $scope.forums.forumList.splice(0,0,msgObj);
                    }else{
                        var replyObj = {
                            Content: _this.forumContent,
                            Date: (new Date().getTime())/1000,
                            ID: '',
                            Name: _this.forumName,
                            PID: $scope.forums.selectForum.ID
                        };
                        $scope.forums.selectForum.Reply++;
                        if($scope.forums.selectForum.replyData){
                            $scope.forums.selectForum.replyData.splice(0,0,replyObj);
                        }else{
                            $scope.forums.selectForum.replyData.push(replyObj);
                        }
                        
                    }
                    DHB.showMsg('保存成功!');
                    _this.forumTitle = '';
                    _this.forumContent = '';
                    $('div.online-msg').hide();
                }else{
                    DHB.showMsg('保存失败!');
                }
            }).error(function(){
                _this.isSubmit = false;
            });
        }
    };
}])

/**
 * 信息公告控制器 
 */
.controller('informationController',['$scope','commonService',function($scope,commonService){
    // 信息
    $scope.infos = {
        index: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        selectInfo: null,
        totalPage: 0,
        sortName: '公告信息',
        currPage: 0,
        infoList: [],
        hasMore: false,
        infoSorts: [],
        clear: function(){
            this.index = -1;
            this.infoList = [];
            this.params.v.begin = 0;
        },
        params: {
            f: 'getInfoList',
            v: {
                sortId: 0,
                begin: 0, // 起始下标
                step: 10 //每次请求条数
            }
        },
        getInfoList: function(sortId){
            if(this.infoList.length > 0 && sortId == this.params.v.sortId) return;
            this.params.v.sortId = sortId;
            var _this = this;
            // 获取退货单列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(data && data.rStatus == '100'){
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i++) {
                            _this.infoList.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }                       
                    }
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                DHB.showMsg('请求失败!');
            });
        },
        refreshInfoList: function(sortId,sortName){
            this.clear();
            this.sortName = sortName;
            this.getInfoList(sortId);
        },
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getInfoList();
            }
        },
        //获取栏目
        getInfoSort: function(){
            var _this = this;
            // query param
            var params = {
                f: 'getInfoSort',
                v: {}
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData){
                   _this.infoSorts = data.rData;
                }
            }).error(function(data){
                //alert('获取数据失败!');
            });
        },
        //获取信息明细
        getInfoContent: function(e){
            var tagName = e.target.tagName.toLowerCase();
            var _this = this;
            // query param
            var params = {
                f: 'getInfoContent',
                v: {
                    articleId: tagName == 'li' ? e.target.getAttribute('data-infoid') : e.target.parentNode.getAttribute('data-infoid') //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData){
                    if(!DHB.isPushState){
                        DHB.isPushState = true;
                        window.history.pushState(null,null,location.href);
                    }
                    _this.selectInfo = data.rData;
                    DHB.toggleMenu(0);
                    $('div.info-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                    //_this.showLayer = 1;
                }
            }).error(function(data){
                //alert('获取数据失败!');
            });
        },
        //close layer
        closeLayer: function(isBack){
            //this.showLayer = 2;
            $('div.info-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            DHB.toggleMenu(1);
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
        }
    };
    
    // 获取栏目
    $scope.infos.getInfoSort();
    
    // 默认加载栏目id为0的信息列表
    $scope.infos.getInfoList(0);
    window.onpopstate =function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.infos.closeLayer(true);
        }
    };
}])

/**
 * 添加付款单控制器 
 */
.controller('addPayController',['$scope','commonService','$stateParams',function($scope,commonService,$stateParams){
    $scope.orderCode = $stateParams.orderCode;
    $scope.orderTotal = '0.00';
    if($scope.orderCode != 0){
        $scope.orderTotal = window.localStorage['orderTotal'];
    }
    function fmtDate(obj){
        var y = obj.getFullYear();
        var m = obj.getMonth() + 1;
        var d = obj.getDate();
        m = m < 10 ? '0' + m : m;
        d = d < 10 ? '0' + d : d;
        return y + '-' + m +'-' + d;
    }
    // 付款对象
    $scope.finance = {
        financeAbout: '',
        financeToDate: fmtDate(new Date()),
        financeTotal: $scope.orderTotal,
        financeYufu: false,
        isSubmit: false,
        accounts: [],
        selectOrderText: '请选择订单',
        payOrderList: [],
        selectAccount: null,
        accountIndex: -1,
        setPrepay: function(){
            this.financeYufu = !this.financeYufu;
            if(this.financeYufu){
                this.financeTotal = '0.00';
            }
            this.selectOrderText = '请选择订单';
        },
        /*reset: function(){
            this.financeAbout = '';
            this.financeToDate = '';
            this.financeTotal = '';
            this.financeYufu = false;
            this.selectAccount = null;
        },*/
        getAccounts: function(){
           var params = {
                f: 'getAccounts',
                v: {}
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData){
                   $scope.finance.accounts = data.rData;
                   $scope.finance.accounts[0].isChecked = true;
                   $scope.finance.selectAccount = $scope.finance.accounts[0];
                   $scope.finance.accountIndex = 0;
                }
            }).error(function(data){
                //alert('获取数据失败!');
            }); 
        },
        getPayOrder: function(){
            if(this.payOrderList.length > 0){return;}
            var params = {
                f: 'getPayOrderList',
                v: {}
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData){
                   $scope.finance.payOrderList = data.rData;
                }
            }).error(function(data){
                //alert('获取数据失败!');
            }); 
        },
        setChoiceOrder: function(type){
            if(type==0){
                this.financeTotal = '0.00';
                this.selectOrderText = '';
                var count = 0;
                for(var i=0; i < this.payOrderList.length; i++) {
                    if(this.payOrderList[i].isChecked){
                        count++;
                        this.selectOrderText += this.payOrderList[i].OrderSN + ',';
                        this.financeTotal = parseFloat(this.financeTotal) + parseFloat(this.payOrderList[i].OrderTotal - this.payOrderList[i].OrderIntegral);
                    }
                }
                if(count != 0){
                    this.financeTotal = this.financeTotal.toFixed(2);
                }
                return;
            }
            for (var i=0; i < this.payOrderList.length; i++) {
                this.payOrderList[i].isChecked = false;
            }
            this.selectOrderText = '请选择订单';
            this.financeTotal = '0.00';
        },
        clicked: true,
        // 商品切换选择
        toggleChecked: function(index,isPayOrder,e){
            if(this.clicked){
                this.clicked = false;
                setTimeout(function(){
                    $scope.finance.clicked = true;
                },452)
                if(isPayOrder){
                    this.payOrderList[index].isChecked = !this.payOrderList[index].isChecked;
                    return;
                }
                if(this.accountIndex != -1){
                    this.accounts[this.accountIndex].isChecked = false;
                }
                this.accountIndex = index;
                this.selectAccount = this.accounts[index];
                this.selectAccount.isChecked = true;
            }
            
        },
        submitFinance: function(isValid){
            if(!isValid){
                if(!this.financeToDate){
                    DHB.showMsg('请填写付款日期!');
                    return;
                }
                if(!this.financeTotal){
                    DHB.showMsg('请填写付款金额!');
                    return;
                }
            }
            if(this.selectAccount == null){
                DHB.showMsg('请选择收款账户!');
                return;
            }
            if(parseFloat(this.financeTotal).toFixed(2) == 0){
                DHB.showMsg('请填写付款金额!');
                return;
            }
            //防止重复提交
            if(this.isSubmit){
                return;
            }
            this.isSubmit = true;
            var params = {
                f: 'submitFinance',
                v: {
                    financeTotal: this.financeTotal, // 金额
                    financeAccounts: this.selectAccount.AccountsID, // 付款账户
                    financeToDate: this.financeToDate ,  // 付款时间
                    financeAbout: this.financeAbout
                }
            };
            
            var financeOrder = [];
            
            // 动态添加预付 如果为true 则为预付
            if(this.financeYufu && $scope.orderCode == 0){
                params.v.financeYufu = 'Y';
            }else{
                if($scope.orderCode != 0){
                    financeOrder.push($scope.orderCode); 
                }else{
                   for (var i=0; i < this.payOrderList.length; i++) {
                       if(this.payOrderList[i].isChecked){
                           financeOrder.push(this.payOrderList[i].OrderSN);
                       }
                   };
                }
                params.v.financeOrder = financeOrder.join(',');
            }
            if(!this.financeYufu && financeOrder.length == 0){
                DHB.showMsg('请选择订单!');
                return;
            }
            var _this = this;
            // 提交付款单
            params.v.financeDevice = DHB.device.WeiXin ? 'WeiXin' : (DHB.device.Ios ? 'Ios' : 'Android'); // 订单来源
            if(DHB.device.WeiXin && !is_weixin()){
                params.v.financeDevice = 'Mobile';
            }
            commonService.saveData(params).success(function(data){
                _this.isSubmit = false;
                if(data.rStatus == '100'){
                    //_this.reset();
                    DHB.showMsg('添加付款单成功!');
                    $scope.changePath('/order-payment/0');
                }else if(data.rStatus == '101'){
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                //alert('获取数据失败!');
                _this.isSubmit = false;
            }); 
        }
    };
    $scope.finance.getAccounts();
    //$scope.finance.getPayOrder();
}])
/**
 *  选择支付方式 
 */
.controller('choicePayController',['$scope','$stateParams',function($scope,$stateParams){
    $scope.orderCode = $stateParams.orderNO;
    
    /*if($scope.onlinePay.payAccount.length === 0){
        $scope.onlinePay.getPayAccount($scope.orderCode);
    }*/
    $scope.msgText = '您还未开通移动支付，现在去开通吗?';
    //TODO 易极付新接口注释以下6行代码
    /*$scope.isAction = false;
    if($scope.onlinePay.openapi > 0 && $scope.onlinePay.notActive){
        $scope.isAction = true;
        $scope.msgText = '您还未激活支付账号，现在去激活吗?';
        $('#is-payreg').show();
    }*/
    
    $scope.payNext = function(type){
        $scope.onlinePay.payWay = type;
        // type 1:online  2:offline 3：支付宝
        if(type === 1){
            /*if($scope.onlinePay.openapi > 0 && !$scope.onlinePay.notActive){
                $scope.changePath('/pay-online/' + $scope.orderCode);
            }*/
            //TODO 易极付新接口 启用该 代码 注释上面的if
            if($scope.onlinePay.openapi > 0){
                $scope.changePath('/pay-online/' + $scope.orderCode);
            }
            // 用户未开通易极付
            else{
               // $('#is-payreg').show();
			    $scope.changePath('/pay-online/' + $scope.orderCode);
            }
            
        }
        // 线下转款
        else if(type === 2){
            $scope.changePath('/add-pay/' + $scope.orderCode);
        }
        // 支付宝
        else if(type === 3){
            $scope.changePath('/pay-online/' + $scope.orderCode);
        }
    };
    $scope.regOrAction = function(){
        delete window.sessionStorage['hasReg'];
        delete window.sessionStorage['hasJump'];
        //TODO 易极付新接口启用 $scope.changePath('/act-account'); 注释 if else
        $scope.changePath('/act-account');
        /*if($scope.isAction){
            $scope.changePath('/act-account');
        }
        else {
            $scope.changePath('/pay-reg/' + $scope.orderCode);
        }*/
    };
}])

/**
 *  action account
 */
.controller('actAccountController',['$scope','commonService',function($scope,commonService){
    if(!is_weixin() && DHB.device.WeiXin){
        $(window).one('popstate',function(){
            $scope.changePath('/order-payment/0');
            $scope.$apply();
        });
    }
    // 激活账号
    $scope.goActiveAccount = function(){
        // 开户成功跳转激活
        var actObj = {
            //m: 'actAccount',
            m: 'qftSetAccount',
            isApp: 'true',
            sKey: window.localStorage['sKey']
        };
        if(DHB.device.WeiXin){
            $('#payreg-form').removeAttr('target');
            actObj.isApp = 'false';
        }
        $('#reg-v').val(JSON.stringify(actObj));
        //TODO 易极付新接口启用window.sessionStorage['hasJump'] = 'true';
        window.sessionStorage['hasJump'] = 'true';
        /*$('#payreg-input').hide();
        $('#payreg-h').hide();*/
        $('#payreg-div').show();
        $('#payreg-form').submit();
    };
    //TODO 易极付新接口启用 以下注释代码  注释获取数据代码commonService.getDataList
    if(!window.sessionStorage['hasJump']){
        $scope.goActiveAccount();
    }
    else{
        window.sessionStorage.removeItem('hasJump');
        window.history.back();
    }
    /*commonService.getDataList({f: 'getGetWay',v: {sKey: window.localStorage['sKey']}}).success(function(data){
        if(data.rData && data.rStatus == '100'){
            //_this.notActive = data.openapiIscat == '0'; // 1 激活
            if(data.openapiIscat == '0'){
                if(window.sessionStorage['hasJump']){
                    $scope.changePath('/order-payment/0/action'); 
                }
                else{
                    window.sessionStorage['hasJump'] = 'true';
                    $scope.goActiveAccount();
                }
                
            }
            else{
               $scope.changePath('/order-payment/0/action'); 
            }
        }
    });*/
    
    
    $(window).one('message',function(e){
        $scope.changePath('/order-payment/0/action');
        /*if(e.data === 'success'){
            DHB.showMsg('恭喜您，激活成功');
            $scope.changePath('/order-payment/0/action');
        }
        else if(e.data === 'fail'){
            DHB.showMsg('很抱歉，激活失败');
            window.sessionStorage['hasReg']
            //$scope.replacePath('/pay-online/' + $scope.orderCode);
            $scope.changePath('/pay-reg/' + $scope.orderCode);
        }*/
        $scope.$apply();
    },false);
}])
/**
 * 开通移动支付 
 */
.controller('payRegController',['$scope','$stateParams','commonService',function($scope,$stateParams,commonService){
    $scope.orderCode = $stateParams.orderNO;
    
    if(window.sessionStorage['hasReg']){
        $scope.onlinePay.getPayAccount($scope.orderCode);
        return;
    }
    
    //$scope.payUrl = 'http://yaoliankeji.dhb.net.cn/mobileApi/yijifuurl.php'; //wy.dhb.net.cn //wyy.dhb.hk
    //http://mobiledhbtest.dhb.net.cn/yijifuurl.php?v={"sKey":"ad8b34e338a37f1a893a091eb78eef82","m":"setAccount","mobile":"13088070799","realName":"张三",
    //"certNo":"510XXXXXXXXXXXXX","email":"xxx@xx"}
    
    //TODO 易极付新接口注释以下代码
    /*$scope.payReg = {
        regObj: {
            m: 'setAccount',
            mobile: '',
            certNo: '',
            email: '',
            realName: ''
        },
        isSubmit: false,
        // 开通移动支付
        submitReg: function(){
            if(this.isSubmit) return;
            
            if(!this.regObj.realName){
                DHB.showMsg('请输入真实姓名!');
                return;
            }
            if(!this.regObj.mobile){
                DHB.showMsg('请输入手机号!');
                return;
            }
            if(!/^\d{11}$/.test(this.regObj.mobile)){
                DHB.showMsg('请输入合法的手机号!');
                return;
            }
            if(!this.regObj.certNo){
                DHB.showMsg('请输入身份证号!');
                return;
            }
            if(!/(^\d{15}$)|(^\d{17}([0-9]|X|x)$)/.test(this.regObj.certNo)){
                DHB.showMsg('请输入有效身份证号!');
                return;
            }
            var params = {
                v: this.regObj
            };
            this.isSubmit = true;
            var _this = this;
            //$scope.payUrl = 'http://yaoliankeji.dhb.net.cn/mobileApi/yijifuurl.php'; //wy.dhb.net.cn //wyy.dhb.hk
            commonService.saveData(params,$scope.payUrl).success(function(data){
                _this.isSubmit = false;
                if(data && data.rStatus == '100'){
                    window.sessionStorage['hasReg'] = 'true';
                    DHB.showMsg('开通成功!');
                    //$scope.goActiveAccount();
                    //$scope.onlinePay.getPayAccount($scope.orderCode);
                    $scope.changePath('/act-account');
                }else if(data.rStatus == '101'){
                    DHB.showMsg("开户失败，请检查你输入的身份证号和姓名是否一致");
                }
            }).error(function(data){
                DHB.showMsg('注册失败');
                _this.isSubmit = false;
            }); 
            return false;
        }
    };*/
}])
/**
 * 添加付款单控制器 
 */
.controller('onlinePayController',['$scope','commonService','$stateParams',function($scope,commonService,$stateParams){
    $scope.orderCode = $stateParams.orderCode;
    
    $scope.orderTotal = '0.00';
    if($scope.orderCode != 0){
        $scope.orderTotal = window.localStorage['orderTotal'];
    }
    function fmtDate(obj){
        var y = obj.getFullYear();
        var m = obj.getMonth() + 1;
        var d = obj.getDate();
        m = m < 10 ? '0' + m : m;
        d = d < 10 ? '0' + d : d;
        return y + '-' + m + '-' + d;
    }
    // 付款对象
    $scope.finance = {
        financeAbout: '',
        financeToDate: fmtDate(new Date()),
        financeTotal: $scope.orderTotal,
        financeYufu: false,
        accounts: [],
        selectOrderText: '请选择订单',
        payOrderList: [],
        financeOrder: '',
        setPrepay: function(){
            this.financeYufu = !this.financeYufu;
            if(this.financeYufu){
                this.financeTotal = '0.00';
            }
            this.selectOrderText = '请选择订单';
        },

        getAccounts: function(){
           var params = {
                f: 'getAccounts',
                v: {}
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData){
                   $scope.finance.accounts = data.rData;
                }
            }).error(function(data){
                //alert('获取数据失败!');
            }); 
        },
        getPayOrder: function(){
            if(this.payOrderList.length > 0){return;}
            var params = {
                f: 'getPayOrderList',
                v: {}
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData){
                   $scope.finance.payOrderList = data.rData;
                }
            }).error(function(data){
                //alert('获取数据失败!');
            }); 
        },
        setChoiceOrder: function(type){
            if(type==0){
                this.financeTotal = '0.00';
                this.selectOrderText = '';
                var count = 0;
                for(var i=0; i < this.payOrderList.length; i++) {
                    if(this.payOrderList[i].isChecked){
                        count++;
                        this.selectOrderText += this.payOrderList[i].OrderSN + ',';
                        this.financeTotal = parseFloat(this.financeTotal) + parseFloat(this.payOrderList[i].OrderTotal - this.payOrderList[i].OrderIntegral);
                    }
                }
                if(count != 0){
                    this.financeTotal = this.financeTotal.toFixed(2);
                }
                return;
            }
            for (var j=0; j < this.payOrderList.length; j++) {
                this.payOrderList[j].isChecked = false;
            }
            this.selectOrderText = '请选择订单';
            this.financeTotal = '0.00';
        },
        clicked: true,
        // 商品切换选择
        toggleChecked: function(index,isPayOrder,e){
            if(this.clicked){
                this.clicked = false;
                setTimeout(function(){
                    $scope.finance.clicked = true;
                },452)
                if(isPayOrder){
                    this.payOrderList[index].isChecked = !this.payOrderList[index].isChecked;
                    return;
                }
                if(this.accountIndex != -1){
                    this.accounts[this.accountIndex].isChecked = false;
                }
                this.accountIndex = index;
                this.selectAccount = this.accounts[index];
                this.selectAccount.isChecked = true;
            }
            
        },
        submitFinance: function(){
            if(parseFloat(this.financeTotal).toFixed(2) <= 0){
                DHB.showMsg('请输入付款金额且大于0!');
                return;
            }

            var financeOrder = [];
            var financeOrderNO = [];
            // 动态添加预付 如果为true 则为预付
            if(this.financeYufu && $scope.orderCode == 0){
                //params.v.financeYufu = 'Y';
            }else{
                //if($scope.orderCode != 0){
                if($scope.onlinePay.orderId){
                    //financeOrder.push($scope.orderCode); 
                    financeOrderNO.push($scope.orderCode); 
                    financeOrder.push($scope.onlinePay.orderId); 
                }else{
                   for (var i=0; i < this.payOrderList.length; i++) {
                       if(this.payOrderList[i].isChecked){
                           financeOrderNO.push(this.payOrderList[i].OrderSN);
                           financeOrder.push(this.payOrderList[i].OrderID);
                       }
                   };
                }
                //params.v.financeOrder = financeOrder.join(',');
            }
            if(!this.financeYufu && financeOrder.length == 0){
                DHB.showMsg('请选择订单!');
                return; 
            }
            /*if(financeOrder.length == 0 && !this.financeYufu){
                DHB.showMsg('订单不能为空!');
                return;
            }*/
            this.financeOrder = financeOrder.join(',');
            // 支付宝支付
            if($scope.onlinePay.payWay === 3){
                var params = {
                    v: {
                        AliPayID: $scope.onlinePay.aliPay.AccountsID,
                        orderNO: financeOrderNO.join(','),
                        AliMoney: $scope.finance.financeTotal,
                        AliBody: '无'
                    }
                };

                commonService.getDataList(params,DHB.aliPayUrl).success(function(data){
                    if(data.rStatus == 100){
                    	//在页面中生成一个存放提交支付信息的容器
                    	$('#pay-to-alipay-form').html(data.para);
                    	//将支付信息放在容器中
                        return false;
                    }
                });
            }
            // 快捷支付
            else if($scope.onlinePay.payWay === 1){
				commonService.getDataList({f: 'get_pay_notice',v: {}}).success(function(data){
					if(data && data.rStatus == '100'){
						//alert('存在通道提示信息');'
						
						$scope.finance.BankNoticeTitle = data.rData.title;
						$scope.finance.BankNoticeContent = data.rData.content;
						 $('#bank-notice').show();
						 return false;
					}else{
						$scope.finance.yjf_goPay();  
					}
				});
                
            }
        },
		yjf_goPay: function(){
			commonService.getDataList({f: 'check_pay_account',v: {sKey: window.localStorage['sKey']}}).success(function(data){
						if(data && data.rStatus != '100'){
							if(data.rStatus =='102'){
								$scope.finance.phone = data.rData.phone;
								$('#edit_phone').show();
							}else{
								 DHB.showMsg(data.error);
							}
							 return false;
						}else{
                            //隐藏修改手机号弹框@maxy add at 20171212 13:28
							$('#edit_phone').hide();
							// 在线支付
						$scope.replacePath('/pay-online/' + $scope.orderCode + '/pay-jump');
						}
					});

		},
		yjf_add_accounts: function(){
			var newphone=$("#new_phones").val();
			//alert(newphone);return false;
			if(!(/^1[34578]\d{9}$/.test(newphone))){ 
				 DHB.showMsg("手机号码格式不对！");  
				return false; 
			} 
			
			commonService.getDataList({f: 'onlinepay',v: {sKey: window.localStorage['sKey'],phone:newphone} }).success(function(data){
				if(data && data.rStatus == '100'){
					
				// 在线支付
				$scope.replacePath('/pay-online/' + $scope.orderCode + '/pay-jump');
				  
				}else{
					 DHB.showMsg(data.message);  
				}
				
			});	
		}
    };
}])
/**
 * 支付跳转页面 
 */
.controller('jumpController',['$scope',function($scope){
    /*$('#jump').on('load',function(){
        window.frames['jump'].document.querySelector('body > div:nth-of-type(1)').style.display = 'none';
    });*/
    if(!$scope.onlinePay.currAccount.SignNO) {
        $scope.changePath('/order-payment/0');
        return;
    }
    if(!is_weixin() && DHB.device.WeiXin){
        $(window).one('popstate',function(){
            $scope.changePath('/order-payment/0');
            $scope.$apply();
        });
    }
    
    var obj = {
        sKey: window.localStorage['sKey'],
        //m: 'yijifu',
        m: 'payOrder',
        total: $scope.finance.financeTotal,
        OID: $scope.finance.financeOrder,
        isApp: 'true',
        acType: $scope.onlinePay.currAccount.SignNO
    };
    if(window.sessionStorage['payTotal'] && window.sessionStorage['payOid']){
        obj.total = window.sessionStorage['payTotal'];
        obj.OID = window.sessionStorage['payOid'];
        delete window.sessionStorage['payTotal'];
        delete window.sessionStorage['payOid'];
    }
    if(DHB.device.WeiXin){
        $('#pay-form').removeAttr('target');
        obj.isApp = 'false';
    }
    //console.log(obj)
    $scope.payinfo = JSON.stringify(obj);
    //$scope.payinfo = '{"sKey":"d26e45438ed52c911b9758d2c520fe33","m":"yijifu","total":"0.01","OID":"43336","acType":"20140423020000000097"}';
    $('#pay-v').val($scope.payinfo);
    
    $('#pay-form').submit();
    $(window).one('message',function(e){
        /*if(e.data === 'success'){
            DHB.showMsg('恭喜您，支付成功');
            // 跳转付款单页面
            $scope.replacePath('/order-payment/0');
            //window.location.replace('#/order-payment/0');
        }
        else if(e.data === 'fail'){
            DHB.showMsg('很抱歉，支付失败');
            // 跳转付款单页面
            $scope.replacePath('/pay-online/' + $scope.orderCode);
            //window.location.replace('#/pay-online/' + $scope.orderCode);
        }
        else if(e.data === 'wait'){
            DHB.showMsg('已完成支付，系统确认中');
            // 跳转付款单页面
            $scope.replacePath('/order-payment/0');
            //window.location.replace('#/order-payment/0');
        }*/
        $scope.changePath('/order-payment/0/action');
        $scope.$apply();
    });
    
}])
/**
 * 意见反馈
 */
.controller('feedBackController',['$scope','commonService',function($scope,commonService){
    $scope.feedBack = {
        showLayer: 0,
        feedType: '',
        content: '',
        contact: $scope.myInfo.ClientMobile || $scope.myInfo.ClientPhone,
        isSubmit: false,
        reset: function(){
            this.feedType = '';
            this.content = '';
            this.contact = '';
        },
        setFeedType: function(type){
            this.feedType = type;
            //this.showLayer = 1;
            if(!DHB.isPushState){
                DHB.isPushState = true;
                window.history.pushState(null,null,location.href);
            }
            $('div.feedback-layer').removeClass('fadeOutRight').addClass('fadeInRight');
            DHB.toggleMenu(0);
        },
        saveFeedBack: function(){
            if(this.isSubmit){
                return;
            }
            if(this.content == ''){
                DHB.showMsg('请填写描述信息!');
                return;
            }
            if(this.contact == ''){
                DHB.showMsg('请填写联系方式!');
                return;
            }
            if(!/^\d{3,4}-?\d{7,8}$/.test(this.contact) && !/^1\d{10}$/.test(this.contact) && !/^((400|800)\d{7,9})|((400|800)-\d{3,4}-\d{3,4})|((400|800)-\d{7,9})$/.test(this.contact)){
                DHB.showMsg('请输入正确的电话号码!');
                return;
            }
            var params = {
                f: 'submitFeedback',
                v: {
                    feedbackType: this.feedType,
                    contact: this.contact,
                    content: this.content
                }
            };
            this.isSubmit = true;
            var _this = this;
            commonService.saveData(params).success(function(data){
                _this.isSubmit = false;
                if(data.rStatus == '100'){
                    _this.reset();
                    DHB.showMsg('您的反馈我们已收到，会尽快为您查看!');
                    //_this.showLayer = 2;
                    $('div.feedback-layer').removeClass('fadeInRight').addClass('fadeOutRight');
                    DHB.toggleMenu(1);
                }else if(data.rStatus == '101'){
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                //alert('获取数据失败!');
                DHB.showMsg('保存失败!');
                _this.isSubmit = false;
            }); 
        },
        closeLayer: function(isBack){
            //this.showLayer = 2;
            $('div.feedback-layer').removeClass('fadeInRight').addClass('fadeOutRight');
            DHB.toggleMenu(1);
            if(!isBack){
                DHB.isPushState = false;
                window.history.back();
            }
        }
    };
    window.onpopstate = function() {
        if(DHB.isPushState){
            DHB.isPushState = false;
            $scope.feedBack.closeLayer(true);
        }
    };
}])
;
