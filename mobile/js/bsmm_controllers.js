'use strict';
// 格式化日期
function fmtDate(obj){
    var y = obj.getFullYear();
    var m = obj.getMonth() + 1;
    var d = obj.getDate();
    m = m < 10 ? '0' + m : m;
    d = d < 10 ? '0' + d : d;
    return y + '-' + m +'-' + d;
}
function beginDate(obj){
    var y = obj.getFullYear();
    var m = obj.getMonth() + 1;
    m = m < 10 ? '0' + m : m;
    return y + '-' + m +'-01';
}
function maxDay(year,month){
    // month 取自然值，从 1-12 而不是从 0 开始
    return new Date(year, month, 0).getDate();
}
//$filter('currency')(123534);
angular.module('DHBApp.bsmmControllers',[])

/**
 *  首页控制器 主要做一些页面的切换 以及一些通用功能 
 */
.controller('indexController',[
    '$scope','$location','commonService',
    function($scope,$location,commonService){
    
    // 是否是体验用户
    $scope.isTiYan = false;
    if(window.sessionStorage['tradeId'] && window.sessionStorage['role'] == 'M'){
        $scope.isTiYan = true;
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
                f: 'managerStoreLinkMan',
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
    // 搜索框切换
    $scope.layerManager = {
        orderLayer: 0,
        moneyLayer: 0,
        customerLayer: 0,
        goodsLayer: 0,
        accounts: null, // 收款账户
        goodsSort: null, // 商品分类
        area: null,     // 地区
        sites: null,     // 分类
        firstLevel: [],
        g_firstLevel: [], // 存储商品查询一级分类
        secondLevel: [],
        getAccounts: function(){
            if(!this.accounts){
                var _this = this;
                // 获取收款账户
                commonService.getDataList({f: 'managerAccounts',v: {}}).success(function(data){
                    if(data && data.rStatus == '100'){
                        if(data.rData){
                            _this.accounts = data.rData;
                        }
                    }
                }).error(function(data){
                    //alert('获取数据失败!');
                });
            }
        },
        toggleLayer: function(type){
            switch(type){
                case 0:   // 订单筛选
                    this.orderLayer = this.orderLayer === 0 || this.orderLayer === 2 ? 1 : 2;
                    break;
                case 1:   // 款项筛选
                    this.moneyLayer = this.moneyLayer === 0 || this.moneyLayer === 2 ? 1 : 2;
                    break;
                case 2:   //客户筛选
                    if(!this.area){
                        var _this = this;
                        // 获取客户地区
                        commonService.getDataList({f: 'managerArea',v: {}}).success(function(data){
                            if(data && data.rStatus == '100'){
                                if(data.rData){
                                    _this.area = data.rData;
                                    _this.area.forEach(function(item,index){
                                        if(item.ParentID === '0'){
                                            _this.firstLevel.push(item);
                                        }
                                    });
                                }
                            }
                        }).error(function(data){
                            //alert('获取数据失败!');
                        });
                    }
                    this.customerLayer = this.customerLayer === 0 || this.customerLayer === 2 ? 1 : 2;
                    if(this.customerLayer !== 1){
                        $('#area').removeClass('fadeInRight').addClass('fadeOutRight');
                        $scope.customerSearch.allLevel = [];
                    }
                    break;
                case 3:   //商品筛选
                    this.goodsLayer = this.goodsLayer === 0 || this.goodsLayer === 2 ? 1 : 2;
                    if(!this.sites){
                        var _this = this;
                        // 获取客户地区
                        commonService.getDataList({f: 'managerGoodsSort',v: {}}).success(function(data){
                            if(data && data.rStatus == '100'){
                                if(data.rData){
                                    _this.sites = data.rData;
                                    _this.sites.forEach(function(item,index){
                                        if(item.ParentID === '0'){
                                            _this.g_firstLevel.push(item);
                                        }
                                    });
                                }
                            }
                        }).error(function(data){
                            //alert('获取数据失败!');
                        });
                    }
                    if(this.goodsLayer !== 1){
                        $('#goods').removeClass('fadeInRight').addClass('fadeOutRight');
                        $scope.goodsSearch.allLevel = [];
                    }
                    break;
            }
        }/*,
        resetLayer: function(){
            this.orderLayer = 2;
            this.moneyLayer = 2;
            this.customerLayer = 2;
            this.goodsLayer = 2;
        }*/
    };
    
    // 订单搜索条件管理
    $scope.orderSearch = {
        status: '-1',
        sendStatus: '-1',
        payStatus: '-1',
        beginDate: '',
        endDate: '',
        kw: '',
        setStatus: function(e,type){
            //$(e.currentTarget).children().removeClass('filter-active'); //filter-active
            var target = e.target;
            if(target.nodeName.toLowerCase() === 'span'){
                $(target).addClass('filter-active')
                switch(type){
                    case 0:   // 订单状态
                        this.status = target.getAttribute('data-status');
                        break;
                    case 1:   // 支付状态
                        this.payStatus = target.getAttribute('data-status');
                        break;
                    case 2:   //发货状态
                        this.sendStatus = target.getAttribute('data-status');
                        break;
                    case 3:   //
                        
                        break;
                }
            }
        },
        clearFilter: function(){
            this.status = '-1';
            this.sendStatus = '-1';
            this.payStatus = '-1';
            this.beginDate = '';
            this.endDate = '';
            this.kw = '';
        }
    };
    // 款项搜索条件管理
    $scope.moneySearch = {
        status: '-1',
        accountsId: '-1',
        accountName: '',
        beginDate: '',
        endDate: '',
        kw: '',
        setStatus: function(e,type){
            var target = e.target;
            if(target.nodeName.toLowerCase() === 'span'){
                $(target).addClass('filter-active')
                // 付款状态
                this.status = target.getAttribute('data-status');
            }
        },
        //设置收款账户
        setAccount: function(e){
            var target = e.target;
            if(target.nodeName.toLowerCase() === 'li'){
                var index = target.getAttribute('data-index');
                this.accountsId = $scope.layerManager.accounts[index].AccountsID;
                this.accountName = $scope.layerManager.accounts[index].AccountsName;
            }
        },
        clearFilter: function(){
            this.status = '-1';
            this.accountsId = '-1';
            this.accountName = '';
            this.beginDate = '';
            this.endDate = '';
            this.kw = '';
        }
    };
    // 药店搜索条件管理
    $scope.customerSearch = {
        areaId: '',
        kw: '',
        allLevel: [],
        setArea: function(e,type){
            var target = e.target;
            if(target.nodeName.toLowerCase() === 'li'){
                // 地区
                this.areaId = target.getAttribute('data-areaid');
                var _this = this;
                var temp = [];
                $scope.layerManager.area.forEach(function(item,index){
                    if(item.ParentID === _this.areaId){
                        temp.push(item);
                    }
                });
                if(temp.length > 0){
                    this.allLevel.push($scope.layerManager.secondLevel);
                    $scope.layerManager.secondLevel = temp;
                    $('#area').removeClass('fadeOutRight').addClass('fadeInRight');
                }
                // 如果没有下级分类 通知子控制器执行搜索
                else{
                    $scope.listSearch.search(type);
                    $('#area').removeClass('fadeInRight').addClass('fadeOutRight');
                }
            }
        },
        backLeave: function(){
            if(this.allLevel.length > 0){
                $scope.layerManager.secondLevel = this.allLevel[this.allLevel.length-1];
                this.allLevel.splice(this.allLevel.length-1,1);
            }
            if(this.allLevel.length === 0){
                $('#area').removeClass('fadeInRight').addClass('fadeOutRight');
            }
        },
        clearFilter: function(){
            this.areaId = '';
            this.kw = '';
        }
    };
    // 商品搜索条件管理
    $scope.goodsSearch = {
        siteId: '-1',
        //parentId: '',
        kw: '',
        allLevel: [],
        setSite: function(e,type){
            var target = e.target;
            if(target.nodeName.toLowerCase() === 'li'){
                // 地区
                this.siteId = target.getAttribute('data-siteid');
                //this.parentId = target.getAttribute('data-parentid');
                var _this = this;
                var temp = [];
                $scope.layerManager.sites.forEach(function(item,index){
                    if(item.ParentID === _this.siteId){
                        temp.push(item);
                    }
                });
                if(temp.length > 0){
                    this.allLevel.push($scope.layerManager.secondLevel);
                    $scope.layerManager.secondLevel = temp;
                    $('#goods').removeClass('fadeOutRight').addClass('fadeInRight');
                }
                // 如果没有下级分类 通知子控制器执行搜索
                else{
                    $scope.listSearch.search(type);
                    $('#goods').removeClass('fadeInRight').addClass('fadeOutRight');
                }
            }
        },
        backLeave: function(){
            if(this.allLevel.length > 0){
                $scope.layerManager.secondLevel = this.allLevel[this.allLevel.length-1];
                this.allLevel.splice(this.allLevel.length-1,1);
            }
            if(this.allLevel.length === 0){
                $('#goods').removeClass('fadeInRight').addClass('fadeOutRight');
            }
        },
        clearFilter: function(){
            this.siteId = '-1';
            this.kw = '';
        }
    };
    // 向下级广播 通知查询
    $scope.listSearch = {
        search: function(type,e){
            if(e){
                if(e.keyCode === 13){
                    $scope.$broadcast('list.search',type);
                }
                else if(e.type !== 'keyup'){
                    $scope.$broadcast('list.search',type);
                }
            }
            else{
                /*switch(type){
                    case 0:   // 订单搜索
                        
                        break;
                    case 1:   // 款项搜索
                        
                        break;
                    case 2:   //客户搜索
                        
                        break;
                    case 3:   //商品搜索
                        
                        break;
                }*/
                $scope.$broadcast('list.search',type);
                $scope.layerManager.toggleLayer(type);
            }
        }
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
    
    // 当前登录用户信息
    $scope.myInfo = {};
    // 获取当前登录用户信息
    commonService.getDataList({f: 'managerUserInfo',v: {}}).success(function(data){
        if(data && data.rStatus == '100'){
            $scope.myInfo = data.rData;
        }
    }).error(function(data){
        //alert('获取数据失败!');
    });
}])
.controller('homeController',['$scope','commonService',function($scope,commonService){
    $scope.homeInfo;
    // 获取首页信息
    commonService.getDataList({f: 'managerHome',v: {}}).success(function(data){
        if(data && data.rStatus == '100'){
            if(data.rData){
                $scope.homeInfo = data.rData;
            }
        }
    }).error(function(data){
        //alert('获取数据失败!');
    });
    $scope.needProcessOrder = function(){
        window.localStorage['frompage'] = 'index';
        $scope.changePath('/order-list');
    };
}])
/**
 *  订单列表控制器 
 */
.controller('orderController',['$scope','$timeout','commonService',function($scope,$timeout,commonService){
    // 清楚查询条件
    $scope.orderSearch.clearFilter();
    // 订单
    $scope.orderes = {
        index: -1,
        showLayer: 0, //1、显示 添加class bounceIn 2、隐藏 添加class bounceOut
        selectOrder: {},
        noData: false,
        totalPage: 0,
        currPage: 0,
        countAudit: 0,
        countPay: 0,
        //isCollect: false,
        orderList: [],
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.noData = false;
            this.orderList = [];
            this.params.v = {
                begin: 0,
                step: 10
            };
            /*if(this.isCollect){
                this.params.v.isCollect = 1;
            }*/
        },
        params: {
            f: 'managerOrderList',
            v: {
                begin: 0, // 起始下标
                //orderStatus: 44, //0:待审核,1:备货中,2:已出库,3:已收货,5:已收款,7:已完成,8:客户端取消,9:管理端取消
                //orderBy: 1, //排序
                step: 10 //每次请求条数
            }
        },
        getOrderList: function(){
            if(window.localStorage['frompage'] === 'index'){
                this.params.v.orderStatus = '0';
                delete window.localStorage['frompage'];
            }
            var _this = this;
            // 获取订单列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.orderList.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                if(data && data.rStatus == '100'){
                    _this.countAudit = parseInt(data.countAudit,10);
                    _this.countPay = parseInt(data.countPay,10);
                    /*$scope.dateParam.beginDate = '';
                    $scope.dateParam.endDate = '';*/
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i += 1) {
                            // 开启二级审核
                            if(data.rData[i].OrderSalerStatusName){
                                //myInfo.UserFlag==='9'&&myInfo.UserType==='M'
                                if(data.rData[i].OrderSalerStatusName === '待初审' && $scope.myInfo.UserType ==='S'){
                                    if($scope.myInfo.module && $scope.myInfo.module.order.pope_audit==='Y'){
                                        data.rData[i].showAuditBtn = true;
                                    }
                                }
                                else if(data.rData[i].OrderSalerStatusName === '已初审' && $scope.myInfo.UserType ==='M'){
                                    if($scope.myInfo.module && $scope.myInfo.module.order.pope_audit==='Y'){
                                        data.rData[i].showAuditBtn = true;
                                    }
                                }
                                if(data.rData[i].OrderSalerStatusName === '已初审' && $scope.myInfo.UserFlag==='9'){
                                    data.rData[i].showAuditBtn = true;
                                }
                            }
                            else if(($scope.myInfo.module && $scope.myInfo.module.order.pope_audit === 'Y') || $scope.myInfo.UserFlag === '9'){
                                data.rData[i].showAuditBtn = true;
                            }
                            
                            _this.orderList.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300);
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        // 条件筛选
        getListByFilter: function(){
            this.clear();
            if($scope.orderSearch.status !== '-1') {
                this.params.v.orderStatus = $scope.orderSearch.status;
            }
            if($scope.orderSearch.sendStatus !== '-1') {
                this.params.v.orderSendStatus = $scope.orderSearch.sendStatus;
            }
            if($scope.orderSearch.payStatus !== '-1') {
                this.params.v.orderPayStatus = $scope.orderSearch.payStatus;
            }
            if($scope.orderSearch.beginDate) {
                this.params.v.beginDate = $scope.orderSearch.beginDate;
            }
            if($scope.orderSearch.endDate) {
                this.params.v.endDate = $scope.orderSearch.endDate;
            }
            if($scope.orderSearch.kw) {
                this.params.v.kw = $scope.orderSearch.kw;
            }
            
            this.getOrderList();
        },
        // 未审核  未付款
        getListByStatus: function(type){
            this.clear();
            if(type === 0){
                this.params.v.orderStatus = '0';
            }else{
                this.params.v.orderPayStatus = '0';
            }
            
            this.getOrderList();
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
                f: 'managerOrderContent',
                v: {
                    orderId: orderId //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data.rData && data.rStatus == '100'){
                    data.rData.showAuditBtn = _this.orderList[_this.index].showAuditBtn;
                    _this.selectOrder = data.rData;
                    $scope.changePath('/order-list/order-info');
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                DHB.showMsg('获取订单详情失败');
                //alert('获取数据失败!');
            });
        },
        // 列表直接审核需要获取点击的订单
        setChioceOrder: function(index){
            this.index = index;
        },
        checkStatus: function(action){
            if(this.selectOrder.header.OrderStatus === '0'){
                if(action === 'audit'){
                    $('div.order-audit').show();
                }
                else if(action === 'cancel'){
                    $('div.order-cancel').show();
                }
            }
            else if(action === 'unaudit'){
                $('div.order-rAudit').show();
            }
        },
        // 订单操作
        orderOperator: function(action){
            if(this.submit) return;
            //audit:审核，unaudit:反审，cancel:取消
            if(!this.orderList[this.index]){
                return;
            }
            var params = {
                f: 'managerSetOrder',
                v: {
                    orderId: this.orderList[this.index].OrderID,
                    action: action
                }
            };
            this.submit = true;
            var _this = this;
            commonService.saveData(params).success(function(data){
                $timeout(function(){
                    _this.submit = false;
                },452);
                if(data && data.rStatus == '100'){
                    // 审核操作
                    if(action === 'audit'){
                        _this.orderList[_this.index].OrderStatus = '1';
                        _this.orderList[_this.index].OrderStatusName = '备货中';
                        if(_this.orderList[_this.index].OrderSalerStatusName === '待初审'){
                            _this.orderList[_this.index].OrderStatusName = '待审核';
                            _this.orderList[_this.index].OrderSalerStatusName = '已初审';
                            if($scope.myInfo.UserType === 'S'){
                                _this.orderList[_this.index].showAuditBtn = false;
                            }
                        }
                        if(_this.selectOrder.header){
                            _this.selectOrder.header.OrderStatus = '1';
                            _this.selectOrder.header.OrderStatusName = '备货中';
                            if(_this.selectOrder.header.OrderSalerStatusName === '待初审'){
                                _this.selectOrder.header.OrderStatusName = '待审核';
                                _this.selectOrder.header.OrderSalerStatusName = '已初审';
                                if($scope.myInfo.UserType === 'S'){
                                    _this.selectOrder.showAuditBtn = false;
                                }
                            }
                        }
                        _this.countAudit -= 1;
                    }
                    // 反审核操作
                    else if(action === 'unaudit'){
                        _this.orderList[_this.index].OrderStatus = '0';
                        _this.orderList[_this.index].OrderStatusName = '待审核';
                        //showAuditBtn = true;
                        if(_this.selectOrder.header){
                            _this.selectOrder.header.OrderStatus = '0';
                            _this.selectOrder.header.OrderStatusName = '待审核';
                        }
                        _this.countAudit += 1;
                    }
                    // 订单取消
                    else if(action === 'cancel'){
                        _this.orderList[_this.index].OrderStatus = '9';
                        _this.orderList[_this.index].OrderStatusName = '管理端取消';
                        if(_this.selectOrder.header){
                            _this.selectOrder.header.OrderStatus = '9';
                            _this.selectOrder.header.OrderStatusName = '管理端取消';
                        }
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(){
                $timeout(function(){
                    _this.submit = false;
                },452);
                DHB.showMsg('订单操作失败');
            });
        }
    };
    $scope.orderes.getOrderList();
    
    $scope.$on('list.search',function(){
        $scope.orderes.getListByFilter();
    });
}])
/**
 *  药店控制器 
 */
.controller('customerController',['$scope','$timeout','commonService',function($scope,$timeout,commonService){
    // 清楚查询条件
    $scope.customerSearch.clearFilter();
    
    $scope.toggle = {
        logininfo: 0,
        logininfoShow: true,
        baseinfo: 0,
        baseinfoShow: true,
        other: 0,
        otherShow: true,
        toggleLogininfo: function(){
            if(this.flag1) return;
            this.flag1 = true;
            $timeout(function(){
                $scope.toggle.flag1 = false;
            },452);
            this.logininfo = this.logininfo === 0 || this.logininfo === 2 ? 1 : 2;
            
            if(this.logininfo === 2){
                this.logininfoShow = true;
            }else{
                $timeout(function(){
                    $scope.toggle.logininfoShow = false;
                },450);
            }
            
        },
        toggleBaseinfo: function(){
            if(this.flag2) return;
            this.flag2 = true;
            $timeout(function(){
                $scope.toggle.flag2 = false;
            },452);
            this.baseinfo = this.baseinfo === 0 || this.baseinfo === 2 ? 1 : 2;
            
            if(this.baseinfo === 2){
                this.baseinfoShow = true;
            }else{
                $timeout(function(){
                    $scope.toggle.baseinfoShow = false;
                },450);
            }
        },
        toggleOther: function(){
            if(this.flag3) return;
            this.flag3 = true;
            $timeout(function(){
                $scope.toggle.flag3 = false;
            },452);
            this.other = this.other === 0 || this.other === 2 ? 1 : 2;
            
            if(this.other === 2){
                this.otherShow = true;
            }else{
                $timeout(function(){
                    $scope.toggle.otherShow = false;
                },450);
            }
        }
    };
    // 药店
    $scope.customers = {
        index: -1,
        selectCustomer: {},
        noData: false,
        totalPage: 0,
        currPage: 0,
        countAudit: 0,
        list: [],
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.list = [];
            this.noData = false;
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'managerClientList',
            v: {
                begin: 0, // 起始下标
                step: 10 //每次请求条数
            }
        },
        getCustomerList: function(){
            var _this = this;
            // 获取药店列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.list.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                if(data && data.rStatus == '100'){
//              	console.log(data.rData)
                    _this.countAudit = parseInt(data.countAudit,10);
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i += 1) {
                            if(!data.rData[i].ClientMobile){
                                data.rData[i].ClientMobile = '暂无联系方式';
                            }
                            if(data.rData[i].C_Flag === 'T'){
                                data.rData[i].flagText = '已审核';
                            }else if(data.rData[i].C_Flag === 'F'){
                                data.rData[i].flagText = '审核不过';
                            }else if(data.rData[i].C_Flag === 'D'){
                                data.rData[i].flagText = '待审核';
                            }else if(data.rData[i].C_Flag === 'W'){
                                data.rData[i].flagText = '未上传';
                            };
                            _this.list.push(data.rData[i]);
//                          console.log(_this.list)
                           
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300);
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getCustomerList();
            }
        },
        //获取药店详情
        getCustomerInfo: function(index,clientId){
            this.index = index;
            var _this = this;
            // query param
            var params = {
                f: 'managerClientContent',
                v: {
                    clientId: clientId, //客户编号
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data && data.rStatus == '100'){
                    $scope.changePath('/customer/customer-info');
                    if(data.rData.C_Flag ==='T' || data.rData.C_Flag==='F' ){
                    	console.log(123)
                        data.rData.flagText = '已审核';
                        data.rData.flagText2 = '已审核';//显示在customer-info页面
                    }
                    else{
                        data.rData.flagText = '未审核';
                        data.rData.flagText2 = '未审核';
                    }
                    _this.selectCustomer = data.rData;
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                DHB.showMsg('获取订单详情失败');
                //alert('获取数据失败!');
            });
        },
        // 药店审核 反审核   
        operatorCustomer: function(index){
            if(this.auditing) return;
            this.auditing = true;
            if(index === 0 || index){
                this.index = index;
                this.selectCustomer = this.list[index];
                console.log(this.selectCustomer)
            }
            var params = {
                f: 'managerSetClient',
                v: {
                    clientId: this.selectCustomer.ClientID, //客户编号
                    action: this.selectCustomer.ClientFlag === '9' ? 'audit' : 'unaudit'
                }
            }
            params.v.C_Flag = "";
            var _this = this;
            commonService.saveData(params).success(function(data){
                $timeout(function(){
                    _this.auditing = false;
                },452);
                if(data && data.rStatus == '100'){
                    if(_this.selectCustomer.ClientFlag === '9'){
                        _this.selectCustomer.ClientFlag = '0';
                        _this.selectCustomer.flagText = '反审核';
                        _this.selectCustomer.flagText2 = '已审核';
                        _this.list[_this.index].ClientFlag = '0';
                        _this.list[_this.index].flagText = '已审核';
                    }
                    else{
                        _this.selectCustomer.ClientFlag = '9';
                        _this.selectCustomer.flagText = '审核';
                        _this.selectCustomer.flagText2 = '未审核';
                        _this.list[_this.index].ClientFlag = '9';
                        _this.list[_this.index].flagText = '未审核';
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                $timeout(function(){
                    _this.auditing = false;
                },452);
                DHB.showMsg('模拟药店登录失败');
            });
        },
        getListByFlag: function(flag){
            this.clear();
            this.params.v.flagId = flag;
            
            this.getCustomerList();
        },
        // 条件筛选
        getListByFilter: function(){
            this.clear();
            if($scope.customerSearch.areaId !== '-1') {
                this.params.v.areaId = $scope.customerSearch.areaId;
            }
            if($scope.customerSearch.kw) {
                this.params.v.kw = $scope.customerSearch.kw;
            }
            
            this.getCustomerList();
        },
        // 代下订单
        insteadSubmitOrder: function(index){
            if(index === 0 || index){
                this.selectCustomer = this.list[index];
                console.log(this.selectCustomer)
            }
            var params = {
                f: 'managerChangeClient',
                v: {
                    clientId: this.selectCustomer.ClientID //客户编号
                }
            }
            console.log(params)
            commonService.getDataList(params).success(function(data){
            	console.log(data)
                if(data && data.rStatus == '100'){
                    window.localStorage['sKey'] = data.sKey;
                    window.sessionStorage['orderType'] = $scope.myInfo.UserType;
                    window.sessionStorage['userId'] = $scope.myInfo.UserID;
                    window.location.href = '../html/index.html';
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                DHB.showMsg('模拟药店登录失败');
            });
        }
    };
    $scope.customers.getCustomerList();
    // 监听父controller广播事件
    $scope.$on('list.search',function(){
        $scope.customers.getListByFilter();
    });
    
    $scope.customerManager = {
        pageTitle: '药店新增',
        action: 'add',
        isSubmit: false,
        customer: {},
        relatedData: null, // prefix area level
        // 药店保存（增删改）
        submitCustomer: function(){
            var params = {
                f: 'managerSubmitClient',
                v: this.customer
            };
            params.v.action = this.action;
            if(this.action !== 'add'){
                params.v.clientId = params.v.ClientID;
            }
            if(!this.customer.ClientName){
                DHB.showMsg('账号不能为空')
                return;
            }
            if(!this.customer.ClientPassword){
                DHB.showMsg('密码不能为空')
                return;
            }
            if(!this.customer.ClientCompanyName){
                DHB.showMsg('名称不能为空')
                return;
            }
            if(!this.isSubmit){
                this.isSubmit = true;
                var _this = this;
                commonService.saveData(params).success(function(data){
                    _this.isSubmit = false;
                    if(data && data.rStatus == '100'){
                        DHB.showMsg('保存成功');
                        if(_this.action === 'add'){
                            _this.customer = {};
                            _this.customer.ClientFlag = '0';
                            _this.customer.ClientSetPrice = 'Price1';
                            _this.customer.ClientPriceText = _this.relatedData.price1Name;
                            $scope.customers.clear();
                            $scope.customers.getCustomerList();
                        }
                        else if(_this.action === 'edit'){
                            $scope.customers.list[$scope.customers.index].ClientCompanyName = _this.customer.ClientCompanyName;
                            $scope.customers.list[$scope.customers.index].ClientTrueName = _this.customer.ClientTrueName;
                            $scope.customers.list[$scope.customers.index].ClientMobile = _this.customer.ClientMobile;
                            $scope.customers.list[$scope.customers.index].ClientAdd = _this.customer.ClientAdd;
                            if(_this.customer.ClientFlag === '9'){
                                $scope.customers.list[$scope.customers.index].ClientFlag = '9';
                                $scope.customers.list[$scope.customers.index].flagText = '未审核';
                            }
                            else{
                                $scope.customers.list[$scope.customers.index].ClientFlag = '0';
                                $scope.customers.list[$scope.customers.index].flagText = '已审核';
                            }
                        }
                        
                    }
                    else{
                        DHB.showMsg(data.error);
                    }
                })
                .error(function(){
                    _this.isSubmit = false;
                });
            }
        },
        // click add btn
        gotoAdd: function(){
            this.action = 'add';
            this.customer = {};
            this.customer.ClientFlag = '0';
            this.pageTitle = '药店新增';
            if(!this.relatedData){
                var params = {
                    f: 'managerClientAdd',
                    v: {}
                };
                var _this = this;
                commonService.getDataList(params).success(function(data){
                    if(data && data.rStatus == 100){
                        _this.relatedData = data.rData;
                        _this.relatedData.price1Name = data.price1Name;
                        _this.relatedData.price2Name = data.price2Name;
                        _this.customer.ClientSetPrice = 'Price1';
                        _this.customer.ClientPriceText = data.price1Name;
                        $scope.changePath('/customer/customer-add');
                    }
                    else{
                        DHB.showMsg(data.error);
                    }
                })
                .error(function(){
                    DHB.showMsg('获取区域和级别数据失败');
                });
            }
            else{
                $scope.changePath('/customer/customer-add');
            }
            
        },
        // 药店删除
        deleteCustomer: function(id){
            var params = {
                f: 'managerSubmitClient',
                v: {
                    action: 'del',
                    clientId: id
                }
            };
            commonService.saveData(params).success(function(data){
                if(data && data.rStatus == '100'){
                    $scope.customers.list.splice($scope.customers.index,1);
                    window.history.back();
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(){
                DHB.showMsg('删除失败')
            });
            $('div.sure-delete').hide();
        },
        // click edit btn
        gotoEdit: function(id){
            this.pageTitle = '药店修改';
            this.action = 'edit';
            var params = {
                f: 'managerClientEdit',
                v: {
                    clientId: id
                }
            };
            var _this = this;
            commonService.getDataList(params).success(function(data){
                if(data && data.rStatus == 100){
                    _this.customer = data.rData.content;
                    _this.relatedData = {};
                    _this.relatedData.area = data.rData.area;
                    _this.relatedData.level = data.rData.level;
                    _this.relatedData.prefix = data.rData.prefix;
                    _this.relatedData.price1Name = data.price1Name;
                    _this.relatedData.price2Name = data.price2Name;
                    if(data.rData.content.ClientSetPrice === 'Price1'){
                        _this.customer.ClientPriceText = data.price1Name;
                    }else{
                        _this.customer.ClientPriceText = data.price2Name;
                    }
                    // 循环找出当前区域文本
                    if(data.rData.area){
                        for(var i = 0;i < data.rData.area.length; i+= 1){
                            if(data.rData.area[i].AreaID === data.rData.content.ClientArea){
                                _this.customer.ClientAreaText = data.rData.area[i].AreaName;
                                break;
                            }
                        }
                    }
                    if(data.rData.level){
                        for(var k in data.rData.level){
                            if(k === data.rData.content.ClientLevel){
                                _this.customer.ClientLevelText = data.rData.level[k];
                                break;
                            }
                        }
                    }
                    
                    $scope.replacePath('/customer/customer-add');
                }
                else{
                    DHB.showMsg(data.error);
                }
            })
            .error(function(){
                DHB.showMsg('获取区域和级别数据失败');
            });
            
        }
    };
    $scope.customerLayer = {
        areaLayer: 0,
        levelLayer: 0,
        currArea: null,
        currLevel: null,
        toggleLayer: function(type){
            if(this.flag) return;
            this.flag = true;
            $timeout(function(){
                $scope.customerLayer.flag = false;
            },452);
            switch(type){
                case 0:   // 区域
                    this.areaLayer = this.areaLayer === 0 || this.areaLayer === 2 ? 1 : 2;
                    if(this.areaLayer === 1){
                        this.currArea = [];
                        $scope.customerManager.relatedData.area.forEach(function(item,index){
                            if(item.ParentID == '0'){
                               $scope.customerLayer.currArea.push(item); 
                            }
                        });
                    }
                    break;
                case 1:   // 级别
                    this.levelLayer = this.levelLayer === 0 || this.levelLayer === 2 ? 1 : 2;
                    break;
            }
        },
        // 设置地区
        setArea: function(e){
            var target = e.target;
            if(target.nodeName.toLowerCase() === 'li'){
                // 地区
                var areaId = target.getAttribute('data-areaid');
                var temp = [];
                $scope.customerManager.relatedData.area.forEach(function(item,index){
                    if(item.ParentID === areaId){
                        temp.push(item);
                    }
                });
                if(temp.length > 0){
                    this.currArea = temp;
                }
                // 如果没有下级分类 则设置该区域
                else{
                    $scope.customerManager.customer.ClientArea = areaId;
                    $scope.customerManager.customer.ClientAreaText = target.getAttribute('data-areaname');
                    this.toggleLayer(0);
                }
            }
        },
        // 设置 级别
        setLevel: function(e){
            var target = e.target;
            if(target.nodeName.toLowerCase() === 'li'){
                // 级别
                var level = target.getAttribute('data-level');
                for (var k in $scope.customerManager.relatedData.level){
                    if($scope.customerManager.relatedData.level[k] == level){
                        $scope.customerManager.customer.ClientLevel = k;
                        $scope.customerManager.customer.ClientLevelText = level;
                        break;
                    }
                }
                this.toggleLayer(1);
            }
        },
        // 设置价格
        setPrice: function(price){
            //ClientPriceText ClientSetPrice
            $('div.price').hide();
            if(price === 1){
                $scope.customerManager.customer.ClientPriceText = $scope.customerManager.relatedData.price1Name;
                $scope.customerManager.customer.ClientSetPrice = 'Price1';
            }
            else if(price === 2){
                $scope.customerManager.customer.ClientPriceText = $scope.customerManager.relatedData.price2Name;
                $scope.customerManager.customer.ClientSetPrice = 'Price2';
            }
        },
        // 设置审核状态
        setStatus: function(e){
            if(this.flag1) return;
            this.flag1 = true;
            $timeout(function(){
                $scope.customerLayer.flag1 = false;
            },452);
            if(e.currentTarget.className.search(/yes/) > -1){
                $scope.customerManager.customer.ClientFlag = '0';
            }
            else{
                $scope.customerManager.customer.ClientFlag = '9';
            }
        },
        // 检查输入的折扣是否合法
        checkPercent:function(){
            var percent = parseFloat($scope.customerManager.customer.ClientPercent);
            if(percent < 0 || percent > 10){
                DHB.showMsg('您输入的值不符合规定');
                return
            }
            $('div.percent').hide();
        }
    };
}])
/**
 *  款项控制器 
 */
.controller('orderMoneyController',['$scope','commonService',function($scope,commonService){
    // 清楚查询条件
    $scope.moneySearch.clearFilter();
    
    // 款项
    $scope.orderMoney = {
        index: -1,
        selectOrder: {},
        noData: false,
        totalPage: 0,
        currPage: 0,
        countAudit: 0,
        countToday: 0,
        list: [],
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.noData = false;
            this.list = [];
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'managerFinanceList',
            v: {
                begin: 0, // 起始下标
                step: 10 //每次请求条数
            }
        },
        getList: function(){
            var _this = this;
            // 获取款项列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.list.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                if(data && data.rStatus == '100'){
                    _this.countAudit = parseInt(data.countAudit,10);
                    _this.countToday = parseInt(data.countToday,10);
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i += 1) {
                            _this.list.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300);
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        // 未审核  未付款
        getListByStatus: function(type){
            this.clear();
            if(type === 0){ // 为确认
                this.params.v.flagId = '0';
            }else{  // 今日
                this.params.v.beginDate = fmtDate(new Date());
                this.params.v.endDate = this.params.v.beginDate;
            }
            
            this.getList();
        },
        // 条件筛选
        getListByFilter: function(){
            this.clear();
            if($scope.moneySearch.status !== '-1') {
                this.params.v.flagId = $scope.moneySearch.status;
            }
            if($scope.moneySearch.accountsId !== '-1') {
                this.params.v.accountsId = $scope.moneySearch.accountsId;
            }
            if($scope.moneySearch.beginDate) {
                this.params.v.beginDate = $scope.moneySearch.beginDate;
            }
            if($scope.moneySearch.endDate) {
                this.params.v.endDate = $scope.moneySearch.endDate;
            }
            if($scope.moneySearch.kw) {
                this.params.v.kw = $scope.moneySearch.kw;
            }
            
            this.getList();
        },
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getList();
            }
        },
        //获取款项详情
        getMoneyInfo: function(index,id){
            this.index = index;
            var _this = this;
            // query param
            var params = {
                f: 'managerFinanceContent',
                v: {
                    financeId: id //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data && data.rStatus == '100'){
                    $scope.changePath('/order-money/money-info');
                    _this.selectOrder = data.rData;
                    _this.selectOrder.FinanceToDate = _this.list[index].FinanceToDate;
                    _this.selectOrder.FinanceDate = _this.list[index].FinanceDate*1000 ;
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                DHB.showMsg('获取订单详情失败');
                //alert('获取数据失败!');
            });
        },
        // 是否收款
        checkIsReceive: function(){
            if(this.selectOrder.FinanceFlag === '0'){
                $('div.money-sure').show();
            }
        },
        // 款项确认
        receiveMoney: function(action){
            
            if(!this.selectOrder.FinanceID){
                return;
            }
            this.s
            var params = {
                f: 'managerSetFinance',
                v: {
                    financeId: this.selectOrder.FinanceID,
                    action: action
                }
            };
            var _this = this;
            commonService.saveData(params).success(function(data){
                if(data && data.rStatus == '100'){
                    // 确认到账
                    if(action === 'confirm'){
                        _this.selectOrder.FinanceFlag = '2';
                        _this.selectOrder.FinanceFlagName = '确认到账';
                        _this.list[_this.index].FinanceFlag = '2';
                        _this.list[_this.index].FinanceFlagName = '确认到账';
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(){
                DHB.showMsg('款项操作失败');
            });
        }
    };
    $scope.orderMoney.getList();
    
    $scope.$on('list.search',function(){
        $scope.orderMoney.getListByFilter();
    });
}])
/**
 *  商品控制器 
 */
.controller('goodsController',['$scope','$timeout','commonService',function($scope,$timeout,commonService){
    // 清楚查询条件
    $scope.goodsSearch.clearFilter();
    
    // 商品
    $scope.goodses = {
        index: -1,
        selectGoods: {},
        price1Name: '',
        price2Name: '',
        noData: false,
        totalPage: 0,
        currPage: 0,
        list: [],
        countAudit: 0,
        countUnAudit: 0,
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.list = [];
            this.noData = false;
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'managerGoodsList',
            v: {
                begin: 0, // 起始下标
                //orderStatus: 44, //0:待审核,1:备货中,2:已出库,3:已收货,5:已收款,7:已完成,8:客户端取消,9:管理端取消
                //orderBy: 1, //排序
                step: 10 //每次请求条数
            }
        },
        getList: function(){
            var _this = this;
            // 获取商品列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.list.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                if(data && data.rStatus == '100'){
                    _this.price1Name = data.price1Name;
                    _this.price2Name = data.price2Name;
                    _this.countAudit = data.countAudit ? parseInt(data.countAudit,10) : 0;
                    _this.countUnAudit = data.countUnAudit ? parseInt(data.countUnAudit,10) : 0;
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i += 1) {
                            if(!data.rData[i].Picture){
                                data.rData[i].Picture = '../img/default_pic.png';
                            }
                            if(data.rData[i].FlagID === '0'){
                               data.rData[i].FlagName = '下架';
                            }else{
                                data.rData[i].FlagName = '上架';
                            }
                            _this.list.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300);
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        // 已上架 未上架筛选
        toggleOnOff: function(flag){
            this.clear();
            this.params.v.flagId = flag;
            
            this.getList();
        },
        // 条件筛选
        getListByFilter: function(){
            this.clear();
            if($scope.goodsSearch.siteId !== '-1') {
                this.params.v.siteId = $scope.goodsSearch.siteId;
            }
            if($scope.goodsSearch.kw) {
                this.params.v.kw = $scope.goodsSearch.kw;
            }
            
            this.getList();
        },
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getList();
            }
        },
        //获取商品详情
        getGoodsInfo: function(index,id){
            this.index = index;
            var _this = this;
            // query param
            var params = {
                f: 'managerGoodsContent',
                v: {
                    contentId: id //订单编号
                }
            }
            commonService.getDataList(params).success(function(data){
                if(data && data.rStatus == '100'){
                    _this.selectGoods = data.rData;
                    if(_this.selectGoods.Color){
                        _this.selectGoods.Color = _this.selectGoods.Color.split(',')
                    }
                    if(_this.selectGoods.Specification){
                        _this.selectGoods.Specification = _this.selectGoods.Specification.split(',')
                    }
                    if(_this.selectGoods.FlagID === '0'){
                        _this.selectGoods.flagName = '下架商品';
                    }else{
                        _this.selectGoods.flagName = '上架商品';
                    }
                    $scope.changePath('/goods-list/goods-info');
                }
                
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                DHB.showMsg('获取订单详情失败');
                //alert('获取数据失败!');
            });
        },
        // 商品上下架
        setGoodsFlag: function(index){
            if(this.clicked){
                return;
            }
            this.clicked = true;
            var params = {
                f: 'managerSetGoods',
                v: {
                    
                }
            };
            if(index === 0 || index){
                this.index = index;
                this.selectGoods = this.list[this.index];
            }
            params.v.action = this.selectGoods.FlagID === '0' ? 'unshelve' : 'shelve';
            params.v.contentId = this.selectGoods.ID;
            var _this = this;
            commonService.saveData(params).success(function(data){
                $timeout(function(){
                    _this.clicked = false;
                },452);
                if(data && data.rStatus == '100'){
                    if(_this.selectGoods.FlagID === '0'){
                        _this.list[_this.index].FlagID = '1';
                        _this.list[_this.index].FlagName = '上架';
                        _this.selectGoods.FlagID = '1';
                        _this.selectGoods.FlagName = '上架';
                        _this.selectGoods.flagName = '上架商品';
                        _this.countAudit -= 1;
                        _this.countUnAudit += 1;
                    }
                    else{
                        _this.list[_this.index].FlagID = '0';
                        _this.list[_this.index].FlagName = '下架';
                        _this.selectGoods.FlagID = '0';
                        _this.selectGoods.FlagName = '下架'; 
                        _this.selectGoods.flagName = '下架商品';
                        _this.countAudit += 1; 
                        _this.countUnAudit -= 1; 
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(){
                $timeout(function(){
                    _this.clicked = false;
                },452);
                DHB.showMsg('操作失败')
            });
        }
    };
    $scope.goodses.getList();
    $scope.$on('list.search',function(){
        $scope.goodses.getListByFilter();
    });
}])
/**
 *  应收款控制器 
 */
.controller('receController',['$scope','commonService',function($scope,commonService){
    // 应收款项
    $scope.receMoney = {
        index: -1,
        noData: false,
        totalPage: 0,
        allTotal: 0,
        currPage: 0,
        kw: '',
        list: [],
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.noData = false;
            this.list = [];
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'managerFinanceClient',
            v: {
                begin: 0, // 起始下标
                step: 10 //每次请求条数
            }
        },
        getList: function(){
            var _this = this;
            // 获取应收款项列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.list.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
                    _this.noData = true;
                } 
                if(data && data.rStatus == '100'){
                    _this.allTotal = data.rCountTotal;
                    if(data.rData){
                        if(_this.totalPage == 0){
                            _this.totalPage = Math.ceil(data.rAllTotal/_this.params.v.step);
                        }
                        _this.currPage++;
                        _this.params.v.begin += _this.params.v.step;   
                        _this.hasMore = true;  
                        for (var i=0; i < data.rData.length; i += 1) {
                            if(!data.rData[i].ClientMobile){
                                data.rData[i].ClientMobile = '暂无联系方式';
                            }
                            _this.list.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300);
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        searchReceMoney: function(){
            this.clear();
            this.params.v.kw = this.kw;
            this.getList();
        },
        enterSearch: function(e){
            if(e.keyCode == 13){
                this.searchReceMoney();
            }
        },
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getList();
            }
        }
    };
    $scope.receMoney.getList();
}])
/**
 *  药店统计控制器 
 */
.controller('countCustomerController',['$scope','commonService',function($scope,commonService){
    // 药店
    $scope.countCustomers = {
        index: -1,
        noData: false,
        totalPage: 0,
        currPage: 0,
        list: [],
        hasMore: false,
        beginDate: beginDate(new Date()),
        endDate: fmtDate(new Date()),
        currentMonth: new Date().getMonth() + 1,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.list = [];
            this.noData = false;
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'managerClientCount',
            v: {
                begin: 0, // 起始下标
                step: 10 //每次请求条数
            }
        },
        getList: function(){
            this.params.v.beginDate = this.beginDate;
            this.params.v.endDate = this.endDate;
            var _this = this;
            // 获取药店列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.list.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
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
                        for (var i=0; i < data.rData.length; i += 1) {
                            if(!data.rData[i].ClientMobile){
                                data.rData[i].ClientMobile = '暂无联系方式';
                            }
                            _this.list.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300);
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        // 上一月
        preMonth: function(){
            if(typeof this.params.v !== 'object') return;
            this.beginDate = this.beginDate.replace(new RegExp('^(\\d{4})-(\\d{2})'),function(m,p1,p2,index){
                // 计算月份
                var month = parseInt(p2,10) === 1 ? 12 : parseInt(p2,10) - 1;
                month = month > 9 ? month : '0' + month; 
                
                // 计算年份 月份为1 则年份减一
                p1 = parseInt(p2,10) === 1 ? parseInt(p1,10) - 1 : p1;
                
                $scope.countCustomers.endDate = p1 + '-' + month + '-' + maxDay(p1,month);
                return p1 + '-' + month;
            });
            this.clear();
            this.getList();
        },
        // 下一月
        nextMonth: function(){
            if(typeof this.params.v !== 'object') return;
            var flag = false;
            this.beginDate = this.beginDate.replace(new RegExp('^(\\d{4})-(\\d{2})'),function(m,p1,p2,index){
                if(parseInt(p1,10) == new Date().getFullYear() && parseInt(p2,10) == $scope.countCustomers.currentMonth){
                    flag = true;
                    return p1 + '-' + p2;
                }
                // 计算月份
                var month = parseInt(p2,10) === 12 ? 1 : parseInt(p2,10)+ 1;
                month = month > 9 ? month : '0' + month; 
                
                // 计算年份 月份为12 则年份 + 1
                p1 = parseInt(p2,10) === 12 ? parseInt(p1,10) + 1 : p1;
                
                $scope.countCustomers.endDate = p1 + '-' + month + '-' + maxDay(p1,month);
                return p1 + '-' + month;
            });
            if(!flag){
                this.clear();
                this.getList();
            }
            
        },
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getList();
            }
        }
    };
    $scope.countCustomers.getList();
}])
/**
 *  热销商品控制器 
 */
.controller('goodsHotController',['$scope','commonService',function($scope,commonService){
    // 热销商品
    $scope.goodsHot = {
        index: -1,
        noData: false,
        totalPage: 0,
        currPage: 0,
        list: [],
        hasMore: false,
        beginDate: beginDate(new Date()),
        endDate: fmtDate(new Date()),
        currentMonth: new Date().getMonth() + 1,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.list = [];
            this.noData = false;
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'managerGoodsCount',
            v: {
                begin: 0, // 起始下标
                //orderStatus: 44, //0:待审核,1:备货中,2:已出库,3:已收货,5:已收款,7:已完成,8:客户端取消,9:管理端取消
                //orderBy: 1, //排序
                step: 10 //每次请求条数
            }
        },
        getList: function(){
            this.params.v.beginDate = this.beginDate;
            this.params.v.endDate = this.endDate;
            var _this = this;
            // 获取热销商品列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.list.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
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
                        for (var i=0; i < data.rData.length; i += 1) {
                            _this.list.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                         
                        setTimeout(function(){
                            DHB.iscroll.refresh();
                        },300);
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        // 上一月
        preMonth: function(){
            if(typeof this.params.v !== 'object') return;
            this.beginDate = this.beginDate.replace(new RegExp('^(\\d{4})-(\\d{2})'),function(m,p1,p2,index){
                // 计算月份
                var month = parseInt(p2,10) === 1 ? 12 : parseInt(p2,10) - 1;
                month = month > 9 ? month : '0' + month; 
                
                // 计算年份 月份为1 则年份减一
                p1 = parseInt(p2,10) === 1 ? parseInt(p1,10) - 1 : p1;
                
                $scope.goodsHot.endDate = p1 + '-' + month + '-' + maxDay(p1,month);
                return p1 + '-' + month;
            });
            this.clear();
            this.getList();
        },
        // 下一月
        nextMonth: function(){
            if(typeof this.params.v !== 'object') return;
            var flag = false;
            this.beginDate = this.beginDate.replace(new RegExp('^(\\d{4})-(\\d{2})'),function(m,p1,p2,index){
                if(parseInt(p1,10) == new Date().getFullYear() && parseInt(p2,10) == $scope.goodsHot.currentMonth){
                    flag = true;
                    return p1 + '-' + p2;
                }
                // 计算月份
                var month = parseInt(p2,10) === 12 ? 1 : parseInt(p2,10)+ 1;
                month = month > 9 ? month : '0' + month; 
                
                // 计算年份 月份为12 则年份 + 1
                p1 = parseInt(p2,10) === 12 ? parseInt(p1,10) + 1 : p1;
                
                $scope.goodsHot.endDate = p1 + '-' + month + '-' + maxDay(p1,month);
                return p1 + '-' + month;
            });
            if(!flag){
                this.clear();
                this.getList();
            }
            
        },
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getList();
            }
        }
    };
    $scope.goodsHot.getList();
}])

/**
 *  销量一览控制器 
 */
.controller('salesController',['$scope','commonService',function($scope,commonService){
    // 销量
    $scope.sales = {
        index: -1,
        noData: false,
        totalPage: 0,
        currPage: 0,
        list: [],
        hasMore: false,
        clear: function(){
            this.totalPage = 0;
            this.currPage = 0;
            this.index = -1;
            this.list = [];
            this.noData = false;
            this.params.v = {
                begin: 0,
                step: 10
            };
        },
        params: {
            f: 'managerMonthCount',
            v: {
                begin: 0, // 起始下标
                step: 10 //每次请求条数
            }
        },
        getList: function(){
            var _this = this;
            // 获取销量列表
            commonService.getDataList(this.params).success(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                if(_this.list.length === 0 && (data.rStatus == '101' || data.rTotal == '0')) {
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
                        for (var i=0; i < data.rData.length; i += 1) {
                            data.rData[i].sDate = data.rData[i].ODate.slice(-2);
                            _this.list.push(data.rData[i]);
                        }
                        if(_this.totalPage == _this.currPage){
                            _this.hasMore = false;
                        }
                    }
                }
                else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                _this.params.v = JSON.parse(_this.params.v);
                //alert('请求失败!');
            });
        },
        
        // 滚动加载更多
        loadMore: function(){
            if(this.hasMore && typeof this.params.v == 'object'){
                this.getList();
            }
        }
    };
    $scope.sales.getList();
}])

/**
 * 我的主页控制器 
 */
.controller('myhomeController',['$scope','commonService',function($scope,commonService){
    // weixin
    $scope.isWeiXin = DHB.device.WeiXin && is_weixin() && !window.sessionStorage['tradeId'];
    $scope.companyInfo;
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
                f: 'managerSetPassword',
                v: {
                    oldPassword: this.oldPwd,
                    newPassword: this.newPwd
                }
            };
            this.isCommit = true;
            commonService.saveData(params).success(function(data){
                $scope.updatePwd.isCommit = false;
                if(data.rStatus == '100'){
                    DHB.showMsg('密码修改成功!');
                    $('div.edit-pwd').hide();
                    //重新登录
                    //$scope.setLoginOut();
                }else{
                    DHB.showMsg(data.error);
                }
            }).error(function(data){
                DHB.showMsg('密码修改失败!');
                $scope.updatePwd.isCommit = false;
            });
        }
    };
    
    // 登出
    $scope.setLoginOut = function(){
        var params = {
            f: 'managerLoginOut',
            v: {}
        }
        commonService.saveData(params).success(function(){
            delete window.localStorage['m_sKey'];
            window.location.href = '../login.html';
        }).error(function(){
            window.location.href = '../login.html';
        });
    };
    // 接触绑定微信
    $scope.setRemoveWeixin = function(){
        var params = {
            f: 'managerRemoveWeixin',
            v: {
                sKey: window.localStorage['m_sKey'],
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
    };
    // 获取公司信息
    $scope.getCompanyInfo = function(){
        var params = {
            f: 'managerUserInfo',
            v: {}
        }
        commonService.saveData(params).success(function(data){
            if(data.rStatus == '100'){
                $scope.changePath('/my-home/company-info');
                $scope.companyInfo = data.rData;
            }
            else{
                DHB.showMsg(data.error);
            }
        }).error(function(){
            DHB.showMsg('获取公司信息失败');
        });
    };
}])

/**
 * 意见反馈
 */
.controller('feedBackController',['$scope','commonService',function($scope,commonService){
    $scope.feedBack = {
        showLayer: 0,
        feedType: '',
        content: '',
        contact: 1,//$scope.myInfo.ClientMobile || $scope.myInfo.ClientPhone,
        isSubmit: false,
        reset: function(){
            this.feedType = '';
            this.content = '';
            this.contact = '';
        },
        setFeedType: function(type){
            this.feedType = type;
            if(!DHB.isPushState){
                DHB.isPushState = true;
                window.history.pushState(null,null,location.href);
            }
            $('div.feedback-layer').removeClass('fadeOutRight').addClass('fadeInRight');
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
                f: 'managerSubmitFeedback',
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
            $('div.feedback-layer').removeClass('fadeInRight').addClass('fadeOutRight');
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


/**
 * 上传资质第一步   选择药店 手机号
 */
.controller('addshopController',['$scope','$rootScope','commonService',function($scope,$rootScope,commonService){
	$scope.data = {
	    shopShow    : false ,//搜索药店点击显示
	    noData      : false,//无数据显示
	};
	$scope.actions = {
		inputClick : function () {
	        $scope.data.shopShow = true;

	   },
	    nextAction : function () {//药店输入框不为空进入下一步
	    	if($('#addSearch').val() != ""){
	    		$scope.changePath('/addShop_upload');
	    	};
	    }
	}
	
	
	var params = {
        f: 'managerSearch',
        v: {
        	search : ''
        }
   };
    commonService.getDataList(params).success(function(data){
    	$scope.datas = eval(data);
//  	console.log($scope.datas.rData.length)
    	if($scope.datas.rData.length == 0){
//  		console.log(2);
    		$scope.data.noData = true;
//  		DHB.showMsg("没有符合条件的数据");
    	};
    }).error(function(error){
		console.log(error)
    });
    
    $rootScope.shopname = $('#addSearch').val();
    $rootScope.tel      = $('')
}])
/**
 * 上传资质第二步   选择图片信息上传
 */
.controller('adduploadController',['$scope','$rootScope','commonService',function($scope,$rootScope,commonService){

	$scope.data = {
	    ClientID        : "",//药店ID
		ClientName      : "",//手机号
		IDCard          : "",//身份证号码
		IDCardImg       : "",//身份证图片
		BusinessCard    : "",//营业执照号码
		BusinessCardImg : "",//营业执照图片
		IDLicenceImg    : "",//药品/经营许可证图片
		IDLicenceCard   : "",//药品/经营许可证号码
		GPImg           : "",//GMP/GSP认证图片
		GPCard          : "",//GSP认证号码
	};
	$scope.actions = {
	    open  : function () {//药店输入框不为空进入下一步
	    	var params = {
		        f: 'managerSubmitAccount',
		        v: {}
		    };
		    params.v.ClientID        = parseInt(window.localStorage.getItem('ClientID'));
		    params.v.ClientName      = parseInt(window.localStorage.getItem('clientmobile'));
		    params.v.IDCard          = $scope.data.IDCard;
		    params.v.IDCardImg       = $('.IDCard').attr('data-path');
		    params.v.BusinessCard    = $scope.data.BusinessCard;
		    params.v.BusinessCardImg = $('.BusinessCard').attr('data-path');
		    params.v.IDLicenceCard   = $scope.data.IDLicenceCard;
		    params.v.IDLicenceImg    = $('.IDLicenceCard').attr('data-path');
		    params.v.GPCard          = $scope.data.GPCard;
		    params.v.GPImg           = $('.GPCard').attr('data-path');
		    if(params.v.IDCard && params.v.BusinessCard && params.v.IDLicenceCard && params.v.GPCard && params.v.IDCardImg && params.v.BusinessCardImg && params.v.IDLicenceImg && params.v.GPImg){
		    	commonService.getDataList(params).success(function(data){
		    		console.log(data)
		    	$scope.datas = eval(data);
				    if(data.codes == 100){
						$scope.changePath('/customer');
					};
			    }).error(function(error){
					console.log(error)
			    });
			    
		    }else if(params.v.IDCard == ""){
		    	DHB.showMsg("身份证号码不能为空");
		    }else if(params.v.BusinessCard == ""){
		    	DHB.showMsg("营业执照号码不能为空");
		    }else if(params.v.IDLicenceCard == ""){
		    	DHB.showMsg("药品经营许可证号码不能为空");
		    }else if(params.v.GPCard == ""){
		    	DHB.showMsg("GSP执照号码不能为空");
		    }else if(params.v.IDCardImg == null){
		    	DHB.showMsg("请选择身份证图片");
		    }else if(params.v.BusinessCardImg == null){
		    	DHB.showMsg("请选择营业执照图片");
		    }else if(params.v.IDLicenceImg == null){
		    	DHB.showMsg("请选择药品经营许可证图片");
		    }else if(params.v.GPImg == null){
		    	DHB.showMsg("请选择GSP图片");
		    };
	    }
	}
 		
}])
;
