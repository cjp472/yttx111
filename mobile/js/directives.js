'use strict';
/**
 * 所有自定义指令 
 */
var directiveModule = angular.module('DHBApp.directives',[]);
directiveModule
//首页，分类，药企公共头部
.directive('commonHeader',[function(){
	return {
		restrict: 'AE',
		template: `
			<header>
				<img src="../img/menu.png" alt="" ui-sref="homeMenu" ng-click="actions.setCurrent(2)"/>
				<p>
					<input type="text" name="search" placeholder="搜索你想要的"/>
					<img src="../img/search.png"/>
				</p>
				<img src="../img/scan.png"/>
			</header>
		`,
		replace: true,	
	}
}])
/**
 *上传药店资质：搜索药店出现搜索按钮并搜索
 */
.directive('addSearch', ['commonService',function(commonService){  
    return {  
        link: function (scope, el, attrs) { 
            el.bind('input propertychange',function(){
		    	if($(this).val() !="" ){
		    		$(this).css({
			    		'background':'none'
			    	});
			    	var params = {
				        f: 'managerSearch',
				        v: {}
				    };
				    params.v.search = $(this).val();
				    commonService.getDataList(params).success(function(data){
				    	scope.datas = eval(data);
				    	if(scope.datas.rData.length == 0){
				    		scope.data.noData = true;
				//  		DHB.showMsg("没有符合条件的数据");
				    	}else{
				    		scope.data.noData = false;
				    	};
				    }).error(function(error){
						console.log(error)
				    });
		    	}else{
		    		$(this).css({
			    		'background': 'url(../img/upload_search.png) no-repeat 35% center',
			    		"background-size": "6%"
			    	});
			    	
		    	};
    		});
    		el.bind('blur',function(){
		    	if($(this).val() =="" ){
		    		$(this).css({
			    		'background': 'url(../img/upload_search.png) no-repeat 35% center',
			    		"background-size": "6%"
			    	});
		    	};
				scope.data.shopShow = false;
		    });
        },
    };  
}]) 
/**
 *上传药店资质：选择搜索的药店
 */
.directive('addChoose', ['commonService',function(commonService){  
    return {  
        link: function (scope, el, attrs) { 
            el.bind('click',function(){
//          	console.log($(this).attr('data-ClientID'))
            	window.localStorage.ClientID = $(this).attr('data-ClientID');//缓存药店id
            	$('#addSearch').val($(this).text());
            	$('#addTel').val($(this).attr('data-ClientMobile'));
            	window.localStorage.clientmobile = $('#addTel').val();//缓存电话
		    	scope.data.shopShow = false;
    			if($('#addSearch').val() != ""){
    				$('#addSearch').css({
			    		'background':'none'
			    	});
    			};
    		});
    		
        },
    };  
}]) 
/**
 *上传药店资质：input focus出现后面的删除图片
 */
.directive('addShow', function () {  
    return {  
        link: function (scope, el, attrs) {  
            el.bind('focus',function(){
		    	$(this).next().show();
    		});
    		el.bind('blur',function(){
		    	$(this).next().hide();
		    });
        },
    };  
})
/**
 *上传药店资质：点击删除图片清空input
 */
.directive('addDel', function () {  
    return {  
        link: function (scope, el, attrs) {  
            el.bind('click',function(){
		    	$(this).prev().val('').focus();
    		});
        },
    };  
})
/**
 *上传药店资质：选择文件
 */
.directive('file',['commonService',function(commonService){    
    return {  
//      scope: { 
//          file: '='  
//      }, 
//      template: 
        link: function (scope, el, attrs) {  
            el.bind('change', function (event) {
//          	console.log($(this).attr('id'))
            	var _id     = $(this).attr('id');
				var _prev   = $(this).prev();
				var _next   = $(this).next().next();
				var _hidden = $(this).next().next().next();
				var _input  = $(this).parent().next().children()[0];//input输入框
//				console.log($(this).parent().next().children()[0])
//				console.log(event.target.files)
                var file = event.target.files[0];  
//				console.log(file)
      			var reader  = new FileReader();
      			
      			var str = file.name;
				var subStr = getSubStr(str);

				function getSubStr (str){
				    if(str.length > 20){
				    	var subStr1 = str.substr(0,10);
					    var subStr2 = str.substr(str.length-10,10);
					    var subStr = subStr1 + "..." + subStr2 ;
				    }else{
				    	var subStr = str;
				    };
				    
				    return subStr;
				};
				//上传的参数
      			var params = {
	                f: 'managerQualifications',
	                v: { 
	                }
	            };

				reader.onload = function (e) {
//					console.log(e)

				   _prev.text(subStr).addClass('uploaded');
				   _next.addClass('uploaded').removeAttr('disabled');
				   _hidden.val(e.target.result);

				/* 压缩图片 */
			        lrz(file, {
			            width: 280,           
			            height: 350,
			            quality: 0.8 //设置压缩参数
			        }).then(function (rst) {
			            /* 处理成功后执行 */
			           console.log(rst)
//			            alert(rst.base64);
//			               _hidden.val(rst.base64);

						params.v.param = rst.base64;
						params.v.name = _id;
      					params.v.user_id = parseInt(window.localStorage.getItem('ClientID'));

						commonService.getDataList(params).success(function(data){
//							console.log(data);
//							alert(data.path);
							$(_input).attr('data-path',data.path);
							console.log($(_input))
		                }).error(function(error){
							console.log(error)	
		                }); 
			        }).catch(function (err) {
			            /* 处理失败后执行 */
			        }).always(function () {
			            /* 必然执行 */
			        });
				};
				
				if (file) {
				    reader.readAsDataURL(file);
				};

            });  
        },

    };  
}]) 

/**
 *上传药店资质：预览选择文件
 */
.directive('preview', function () {  
    return {  
        link: function (scope, el, attrs) {  
            el.bind('click', function (event) {
				var _next = $(this).next().val();
//				console.log(_next)
				$('.Imglayer img').eq(0).attr('src',_next);
	
      		
            });  
        },
    };  
}) 







//首页banner
.directive('bannerChange',[function(){
	return {
		restrict: 'AE',
		link: function(scope,ele,attrs){
		}
	}
}])
//点击删除按钮
.directive('cartDel',[function(){
	return {
		restrict: 'AE',
		link: function(scope,ele,attrs){
			console.log(ele)
		}
	}
}])

.directive('showStyle',[function(){
    // 商品显示样式 grid形式 list形式两种
    return {
        restrict: 'AE',
        link: function(scope,ele,attrs){
            ele.on('click',function(){
                if(ele.hasClass('icon-grid')){
                    ele.removeClass('icon-grid').addClass('icon-list');
                    $('#list-layout').hide();
                    $('#grid-layout').removeClass('ng-hide').show();
                    window.sessionStorage['showStyle'] = 'grid';
                    return;
                }
                ele.removeClass('icon-list').addClass('icon-grid');
                $('#list-layout').removeClass('ng-hide').show();
                $('#grid-layout').hide();
                window.sessionStorage['showStyle'] = 'list';
            });
        }
    };
}])
.directive('toggleLayer',[function(){
    // 关闭弹出层
    return {
        restrict: 'AE',
        scope: {
            className: '@'
        },
        link: function(scope,ele,attrs){
            ele.on('click',function(e){
                e.stopPropagation();
                e.preventDefault();
                if(scope.className == 'fadeOutRight'){
                    DHB.toggleMenu(1);
                    $('div.filter-layer').removeClass('fadeInRight').addClass('fadeOutRight');
                }else if(scope.className == 'fadeInRight'){
                    DHB.toggleMenu(0);
                    $('div.filter-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                }else{
                    DHB.toggleMenu(1);
                    $('div.cart-layer').removeClass('fadeInRight').addClass('fadeOutRight');
                }
                if(DHB.isPushState){
                    DHB.isPushState = false;
                    window.history.back();
                }
            });
        }
    };
}])
/*.directive('proxyClose',[function(){
    // 关闭弹出层
    return {
        restrict: 'AE',
        scope: {
            className: '@'
        },
        link: function(scope,ele,attrs){
            ele.on('click',function(e){
                if(e.target.tagName.toLowerCase() == 'li'){
                    if(scope.className == 'fadeOutRight'){
                        //DHB.toggleMenu(1);
                        $('div.filter-layer').removeClass('fadeInRight').addClass('fadeOutRight');
                    }else if(scope.className == 'fadeInRight'){
                        //DHB.toggleMenu(0);
                        $('div.filter-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                    }else{
                        //DHB.toggleMenu(1);
                        $('div.cart-layer').removeClass('fadeInRight').addClass('fadeOutRight');//.hide().removeClass('flipOutY');
                    }
                }
            });
        }
    };
}])*/
.directive('bannerSwitch',[function(){
    // 图片切换
    return {
        restrict: 'AE',
        scope: {
            imgLength: '@'
        },
        link: function(scope,ele,attrs){
            var currIndex = 0,initCss='',
            startX = 0,startL = 0,endX = 0;
            scope.$watch('imgLength',function(newValue){
                currIndex = 0;
                startX = 0;
                startL = 0;
                endX = 0;
                ele.css('-webkit-transform','translate3d(0,0,0)'); 
                if(newValue != '' || newValue != 0){
                    ele.css('width',100*scope.imgLength + '%');
                    ele.children().css('width',$(window).width() + 'px');
                    // 动态添加表示当前图片的 小圆形图标
                    var str = '';
                    for(var i = 0; i < scope.imgLength; i += 1){
                        str += '<li></li>';
                    }
                    $(ele[0]).parent().find('ul').html(str);
                    //设置小圆形图标为第一个
                    setActive($(ele[0]).parent().find('ul li'));
                }
            });
            
            ele.on('touchstart',function(e){
                //e.preventDefault();
                if(e.targetTouches.length == 1){
                    startL = -$(window).width() * currIndex;
                    ele.css('-webkit-transition-duration',0);
                    var touch = e.targetTouches[0];
                    startX = touch.pageX;
                    initCss =  ele.css('-webkit-transform');
                }
            })
            .on('touchmove',function(e){
                //e.preventDefault();
                if(e.targetTouches.length == 1){
                    var touch = e.targetTouches[0];
                    endX = touch.pageX;
                    var diff = endX - startX ;
                    ele.css('-webkit-transform','translate3d(' + (diff + startL) +'px,0,0)'); 
                }
            })
            
            //
            .on('touchend',function(e){
                ele.css('-webkit-transition-duration','300ms');

                // 向右滑动
                if(endX - startX > 0){
                    if(currIndex == 0 || Math.abs(endX - startX) < $(window).width()/3){
                        ele.css('webkitTransform',initCss);
                        return;
                    }

                    ele.css('webkitTransform','translate3d(' + (startL + $(window).width()) + 'px,0,0)'); 
                    currIndex--;
                }
                // 向左滑动
                if(endX - startX < 0){
                    if(currIndex == scope.imgLength - 1 || Math.abs(endX - startX) < $(window).width()/3){
                        ele.css('webkitTransform',initCss); 
                        return;
                    }
                    
                    ele.css('webkitTransform','translate3d(' + (startL - $(window).width()) + 'px,0,0)'); 
                    currIndex++;
                }
                setActive($(this).parent().find('ul li'));
            });
            
            //设置当前显示图片对应的小圆形图标
            function setActive(obj){
                obj.each(function(index,item){
                    if(index == currIndex){
                        $(item).addClass('banner-active');
                        return;
                    }
                    $(item).removeClass('banner-active');
                });
            }
        }
    };
}])
.directive('back',function(){
    //后退返回
    return {
        restrict: 'AE',
        link: function(scope,ele,attr){
            ele.on('click',function(e){
                //取消点击事件的默认行为以及阻止冒泡
                e.preventDefault();
                e.stopPropagation();
                window.history.back();
            });
            /*ele.on('tap',function(e){
                window.history.back();
            });*/
        }
    };
})
.directive('addcart',function(){
    //购物车点击 使用事件代理提升效率
    return {
        restrict: 'AE',
        link: function(scope,ele,attr){
            ele.on('click',function(e){
                var target = e.target;
                //此处使用事件代理 提高效率
                if(target.tagName.toLowerCase() == 'a'){
                    // 颜色选中下标
                    DHB.colorIndex = 0;
                    // 规格选中下标
                    DHB.specIndex = 0;
                    e.preventDefault();
                    e.stopPropagation();
                    DHB.toggleMenu(0);
                    $('div.cart-layer').removeClass('fadeOutRight').addClass('fadeInRight');
                }
            });
        }
    };
})
.directive('replaceInput',function(){
    //js控制input框获取焦点，阻止手机软键盘的弹出
    return {
        restrict: 'AE',
        link: function(scope,ele,attr){
            ele.on('click',function(e){
                e.stopPropagation();
                $('div.num-key').show().removeClass('slideOutRight').addClass('slideInLeft');
                $('input.input-num').val('');
                if(scope.setCurrentGoods){
                    scope.setCurrentGoods(scope.goods);
                }
                /*var obj = this.nextSibling.nextSibling;
                //设置光标在文字末尾
                obj.setSelectionRange(obj.value.length, obj.value.length+1);  
                obj.focus();  */
            });
            $(document).on('click',function(e){
                e.stopPropagation();
                $('div.num-key').removeClass('slideInLeft').addClass('slideOutRight');
            });
        }
    };
})
.directive('inputFinish',function(){
    return function(scope,ele,attr){
        ele.on('click',function(){
            $('div.num-key').removeClass('slideInLeft').addClass('slideOutRight');
            if(parseInt($('.input-num').val()) == 0) return;
            if($('.input-num').val() != ''){
                var isCtr = false,isPackage = false;
                
                var bool = false;
                if(typeof scope.goods.isStock === 'string' && scope.goods.isStock == 'true'){
                    bool = true;
                }else if(typeof scope.goods.isStock === 'boolean' && scope.goods.isStock){
                    bool = true;
                }
                if(scope.goodsDetail.controllerLibrary == 'Y' || bool){
                    isCtr = true;
                    if(parseInt($('.input-num').val()) > parseInt(scope.goodsDetail.allLibrary) || parseInt($('.input-num').val()) > parseInt(scope.goods.stock)){
                        DHB.showMsg('库存不足');
                        return;
                    }
                }
                
                //包装数量
                if(scope.goodsDetail.Package && scope.goodsDetail.Package != '0'){
                    isPackage = true;
                }
                
                else if(scope.goods.pack && scope.goods.pack != '0'){
                    isPackage = true;
                }
                var pack = scope.goodsDetail && scope.goodsDetail.Package ? scope.goodsDetail.Package : scope.goods.pack;
                if(isPackage){
                    if(parseInt($('.input-num').val()) % parseInt(pack) !== 0){
                       DHB.showMsg('输入数据不是整包装数量的倍数'); 
                       return;
                    }
                }
                
                scope.goods.num = parseInt($('.input-num').val());
                
                scope.$apply(scope.goods.num);
            }
        });
        
    };
})
.directive('inputInfofinish',function(){
    return function(scope,ele,attr){
        ele.on('click',function(){
            $('div.num-key').removeClass('slideInLeft').addClass('slideOutRight');
            if(parseInt($('#input-num').val()) == 0) return;
            if($('#input-num').val() != ''){
                var isCtr = false,isPackage = false;
                
                var bool = false;
                if(typeof scope.goods.isStock === 'string' && scope.goods.isStock == 'true'){
                    bool = true;
                }else if(typeof scope.goods.isStock === 'boolean' && scope.goods.isStock){
                    bool = true;
                }
                
                if(scope.goodsDetail.controllerLibrary == 'Y' || bool){
                    isCtr = true;
                    if(parseInt($('#input-num').val()) > parseInt(scope.goodsDetail.allLibrary) || parseInt($('#input-num').val()) > parseInt(scope.goods.stock)){
                        DHB.showMsg('库存不足');
                        return;
                    }
                }
                
                //包装数量
                if(scope.goodsDetail.Package && scope.goodsDetail.Package != '0'){
                    isPackage = true;
                }
                
                else if(scope.goods.pack && scope.goods.pack != '0'){
                    isPackage = true;
                }
                var pack = scope.goodsDetail && scope.goodsDetail.Package ? scope.goodsDetail.Package : scope.goods.pack;
                if(isPackage){
                    if(parseInt($('#input-num').val()) % parseInt(pack) !== 0){
                       DHB.showMsg('输入数据不是整包装数量的倍数'); 
                       return;
                    }
                }
                
                scope.goods.num = parseInt($('#input-num').val());
                
                scope.$apply(scope.goods.num);
            }
        });
        
    };
})
.directive('loadMore', [function() {
    //下拉加载更多
    return function(scope, ele, attr) {
        /*ele.on('scroll', function() { 
            if(ele[0].scrollTop+ele[0].offsetHeight >= ele[0].scrollHeight) { 
                scope.$apply(attr.loadMore); 
            } 
        }); */
        
        DHB.iscroll = new iScroll(ele[0],{ checkDOMChanges: true,mouseWheel: false ,hideScrollbar: true,
            fadeScrollbar: true,scrollbarClass: 'myScrollbar',
            onScrollEnd: function(){
                if(this.y == this.maxScrollY){
                    //this.y = this.maxScrollY + 5;
                    scope.$apply(attr.loadMore);
                }
            }/*,
            onScrollMove: function(){
               if(this.y == this.maxScrollY){
                   this.maxScrollY = this.offsetHeight;
               } 
            }*/
        });
    };
}])

.directive('thirdSort',function(){
    //动态绑定三级分类
    return {
        restrict: 'AE',
        scope: {
            sortList: '='
        },
        link: function(scope,ele,attr){
            var tempList = [];
            ele.on('click',function(e){
                var target = e.target;
                if(target.tagName.toLowerCase() == 'li'){
                    var siteId = target.getAttribute('data-siteId');
                    //控制分类信息隐藏显示
                    if($(target).next()[0] && $(target).next()[0].tagName.toLowerCase() == 'ul'){
                        $(target).next().toggle();
                    }
                    
                    if(target.getAttribute('loaded') == 'true'){
                        return;
                    }
                    target.setAttribute('loaded',true);
                    
                    for (var i=0; i < scope.sortList.length; i++) {
                        if(scope.sortList[i].ParentID == siteId){
                            tempList.push(scope.sortList[i]);
                        }
                    }
                    if(tempList.length > 0){
                        appendSort(target);
                    }else{
                       location.href = '#/goods-list/' + siteId ;
                    }
                }
            });
            
            function appendSort(obj){
                var ul = document.createElement('ul'),
                li_str = '';
                // data-third-sort data-sort-list="goodsSort.sortList"
                ul.setAttribute('data-third-sort','');
                ul.setAttribute('data-sort-list','goodsSort.sortList');
                for (var i=0; i < tempList.length; i++) {
                    li_str += '<li data-siteId='+tempList[i].SiteID+'>'+tempList[i].SiteName+'</li>';
                }
                ul.innerHTML = li_str;
                //为ul绑定click事件 目的为 将li的click事件委托给它
                /*$(ul).click(function(e){
                    var target = e.target;
                    if(target.nodeName.toLowerCase() == 'li'){
                        var siteId = target.getAttribute('data-siteId');
                        //location.href = '#/goods-list/' + siteId;
                    }
                });*/
                $(obj).after(ul);
                tempList = [];
            }
        }
    };
})
.directive('goodsSearch',function(){
    //商品搜索
    return function(scope, ele,attr){
        ele.on('click',function(){
            var keyWord = $(this).next().val();
            if(keyWord){
                scope.globalSearch.keyWord = keyWord;
                if(location.href.split('#') && location.href.split('#')[1]){
                    if(location.href.split('#')[1]=='/goods-list/0'){
                        location.replace('#/goods-list/00');
                    }else if(location.href.split('#')[1]=='/goods-list/00'){
                        location.replace('#/goods-list/0');
                    }else{
                        location.replace('#/goods-list/0');
                    }
                }
                else if(scope.globalSearch.urlFix){
                    if(scope.globalSearch.urlFix === '00'){
                        location.href = '#/goods-list/0';
                    }
                    else{
                        location.href = '#/goods-list/00';
                    }
                }
                else{
                    location.href = '#/goods-list/0';
                }
                
            }
        });
        ele.next().on('keyup',function(e){
            if(e.keyCode == 13){
               ele.triggerHandler('click'); 
            }
        });
    };
})
.directive('inputNum',function(){
    //小键盘输入
    return {
        restrict: 'AE',
        link: function(scope,ele,attr){
            ele.on('click',function(e){
                if(e.target.tagName.toLowerCase() == 'span'){
                    var num = $(e.target).text();
                    var oldNum = $(this).prev().children().val();
                    if(num != '' && num != '完成'){
                        $(this).prev().children().val(oldNum + num);
                    }else{
                        $(this).prev().children().val(oldNum.slice(0,oldNum.length-1));
                    }
                    if(scope.refreshTotalAmount){
                        scope.refreshTotalAmount();
                        scope.$apply(scope.totalAmount);
                    }
                }
            })
        }
    };
})
.directive('plus',function(){
    //数量加
    return function(scope,ele,attr){
        ele.on('click',function(){
            if(!scope.goodsDetail){
                return;
            }
            var num = 1;
            var isCtr = false,isPackage = false;
            if(scope.goodsDetail && scope.goodsDetail.controllerLibrary){
                if(scope.goodsDetail.controllerLibrary == 'Y'){
                    isCtr = true;
                    if(scope.goods.num >= parseInt(scope.goodsDetail.allLibrary)){
                        DHB.showMsg('库存不足');
                        return;
                    }
                }
            }else{
                var bool = false;
                if(typeof scope.goods.isStock === 'string' && scope.goods.isStock == 'true'){
                    bool = true;
                }else if(typeof scope.goods.isStock === 'boolean' && scope.goods.isStock){
                    bool = true;
                }
                if(bool){
                    isCtr = true;
                    if(scope.goods.num >= parseInt(scope.goods.stock)){
                        DHB.showMsg('库存不足');
                        return;
                    }
                }
            }
            if(scope.goodsDetail.Package && scope.goodsDetail.Package != '0'){
                isPackage = true;
                num = parseInt(scope.goodsDetail.Package);
            }
            else if(scope.goods.pack && scope.goods.pack != '0'){
                isPackage = true;
                num = parseInt(scope.goods.pack);
            }
            
            scope.goods.num = parseInt(scope.goods.num) + num;
            
            var gtLibrary = false;
            if(scope.goodsDetail.allLibrary && scope.goods.num > parseInt(scope.goodsDetail.allLibrary)){
                gtLibrary = true;
            }else if(scope.goods.stock && scope.goods.num > parseInt(scope.goods.stock)){
                gtLibrary = true;
            }
            if(!isCtr) gtLibrary = false;
            if(isPackage && gtLibrary){
                DHB.showMsg('库存不足');
                scope.goods.num = parseInt(scope.goods.num) - num;
            }
            scope.$apply(scope.goods.num);
            if(scope.refreshTotalAmount){
                scope.refreshTotalAmount();
                scope.$apply(scope.totalAmount);
            }
            /*var num =$(this).prev().val();
            $(this).prev().val(parseInt(num) + 1);*/
        });
    };
})
.directive('minus',function(){
    //数量减
    return function(scope,ele,attr){
        ele.on('click',function(){
            
            //包装数量
            if(scope.goodsDetail.Package && scope.goodsDetail.Package != '0'){
                if(scope.goods.num > parseInt(scope.goodsDetail.Package)){
                   scope.goods.num = parseInt(scope.goods.num) - parseInt(scope.goodsDetail.Package);
                    scope.$apply(scope.goods.num) 
                }
            }else{
                if(scope.goods.num > 1){
                    
                    if(scope.goods.pack && scope.goods.pack != '0'){
                        if(scope.goods.pack == parseInt(scope.goods.num)){
                            DHB.showMsg('订货数量必须为整包装数量的倍数');
                        }else{
                            scope.goods.num = parseInt(scope.goods.num) - scope.goods.pack;
                        }
                    }else{
                        scope.goods.num = parseInt(scope.goods.num) - 1;
                    }
                    scope.$apply(scope.goods.num);
                }
            }
            //刷新金额
            if(scope.refreshTotalAmount){
                scope.refreshTotalAmount();
                scope.$apply(scope.totalAmount);
            }
            
        });
    }
})
.directive('choiceColor',function(){
    //颜色选择
    return function(scope,ele,attr){
        DHB.colorIndex = 0;
        ele.on('click',function(e){
            if(e.target.nodeName.toLowerCase() == 'span'){
                //启用多选
                if(scope.choiceMore.isChoiceMore){
                    if($(ele[0]).children().length <= 0) return;
                    $(e.target).toggleClass('goods-active');
                    //DHB.colorIndex = $(e.target).index();
                    return;
                }
                
                //未启用多选
                if($(e.target).index() == DHB.colorIndex) return;
                if(DHB.colorIndex != -1){
                    $(this).children().eq(DHB.colorIndex).removeClass('goods-active')
                }
                scope.goods.color = $(e.target).addClass('goods-active').text();
                DHB.colorIndex = $(e.target).index();
            }
            
            // md5 加密 hex_md5('256红色0.5') id + color + specification
            var compare = hex_md5(scope.goods.id + scope.goods.color + scope.goods.specify);
            if(scope.goodsDetail.library){
                for(var key in scope.goodsDetail.library){
                    if(key == compare){
                        scope.goodsDetail.allLibrary = scope.goodsDetail.library[key];
                        scope.$apply(scope.goodsDetail.allLibrary);
                        scope.goods.stock = scope.goodsDetail.allLibrary;
                        scope.$apply(scope.goods.stock);
                        break;
                    }
                }
            }
        });
    };
})
.directive('choiceSpec',function(){
    //规格选择
    return function(scope,ele,attr){
        DHB.specIndex = 0;
        ele.on('click',function(e){
            if(e.target.nodeName.toLowerCase() == 'span'){
                //启用多选
                if(scope.choiceMore.isChoiceMore){
                    if($(ele[0]).children().length <= 1) return;
                    $(e.target).toggleClass('goods-active');
                    //DHB.specIndex = $(e.target).index();
                    return;
                }
                
                //未启用多选
                if($(e.target).index() == DHB.specIndex) return;
                if(DHB.colorIndex != -1){
                    $(this).children().eq(DHB.specIndex).removeClass('goods-active')
                }
                scope.goods.specify = $(e.target).addClass('goods-active').text();
                DHB.specIndex = $(e.target).index();
            }
            
            // md5 加密 hex_md5('256红色0.5') id + color + specification
            var compare = hex_md5(scope.goods.id + scope.goods.color + scope.goods.specify);
            if(scope.goodsDetail.library){
                for(var key in scope.goodsDetail.library){
                    if(key == compare){
                        scope.goodsDetail.allLibrary = scope.goodsDetail.library[key];
                        scope.$apply(scope.goodsDetail.allLibrary);
                        scope.goods.stock = scope.goodsDetail.allLibrary;
                        scope.$apply(scope.goods.stock);
                        break;
                    }
                }
            }
        });
    };
})
.directive('toggleGoods',function(){
    // 订单商品隐藏显示切换
    return function(scope,ele,attr){
        var flag = true;
        ele.on('click',function(e){
            e.stopPropagation();
            if(flag){
                flag = false;
                setTimeout(function(){
                    flag = true;
                },452);
                if($('div.order-goods').eq(0).hasClass('fadeInDown')){
                    $('div.order-goods').eq(0).removeClass('fadeInDown').addClass('fadeOutUp');
                    setTimeout(function(){
                        $('div.order-goods').eq(0).hide();
                    },450)
                    return;
                }
                $('div.order-goods').eq(0).show().removeClass('fadeOutUp').addClass('fadeInDown');
            }
        });
    };
})
.directive('toggleLog',function(){
    // 订单商品隐藏显示切换
    return function(scope,ele,attr){
        var flag = true;
        ele.on('click',function(){
            if(flag){
                flag = false;
                setTimeout(function(){
                    flag = true;
                },452);
                
                if($('div.order-log').hasClass('fadeInDown')){
                    $('div.order-log').removeClass('fadeInDown').addClass('fadeOutUp');
                    setTimeout(function(){
                        $('div.order-log').hide();
                    },450)
                    return;
                }
                $('div.order-log').show().removeClass('fadeOutUp').addClass('fadeInDown');
            }
        });
    };
})
.directive('menuActive',function(){
    //首页点击添加文字颜色为红色
    return function(scope,ele,attr){
        ele.on('click',function(e){
            if(e.target.tagName.toLowerCase() == 'p'){
                //如果是当前显示页面 则不做任何操作
                if($(e.target).parent().parent().index() == DHB.menuIndex)return;
                
                //移除index对应的class
                $(this).children().eq(DHB.menuIndex).find('a').removeClass('active');
                //将当前点击元素的位置赋给index
                DHB.menuIndex = $(e.target).parent().parent().index();
                // 为当前点击元素添加class
                $(e.target).parent().addClass('active');
            }
        });
    };
})
.directive('toggleOperator',function(){
    return {
        rectrict: 'AE',
        scope: {
            isShow: '@',
            toggleId: '@'
        },
        link: function(scope,ele,attr){
            ele.on('click',function(){
                if(scope.isShow == 'true'){
                    $('#'+scope.toggleId).removeClass('fadeOutRight').addClass('fadeInRight');
                    return;
                }
                $('#'+scope.toggleId).removeClass('fadeInRight').addClass('fadeOutRight');
            });
        }
    };
})

.directive('toggleSwitch',function(){
    //switch 切换
    return {
        rectrict: 'AE',
        link: function(scope,ele,attr){
            ele.on('click',function(){
                if(ele.hasClass('yes')){
                    $(this).removeClass('yes').children().css('float','left');
                    return;
                }
                $(this).addClass('yes').children().css('float','right');
            });
        }
    };
})
.directive('toggle',function(){
    // 显示隐藏切换
    return {
        rectrict: 'AE',
        scope: {
            selector: '@'
        },
        link: function(scope,ele,attr){
            ele.on('click',function(e){
                e.preventDefault();
                e.stopPropagation();
//              console.log($(scope.selector))
                $(scope.selector).toggle();
            });
        }
    };
})
.directive('datePicker',function(){
    // 日期控件
    return {
        rectrict: 'AE',
        scope: {ctrlMin: '@'},
        link: function(scope,ele,attr){
            if(scope.ctrlMin == 'true'){
                DHB.opt.minDate = new Date();
            }else{
                delete DHB.opt.minDate;
            }
            $(ele[0]).mobiscroll(DHB.opt);
        }
    };
})
.directive('transHtml',function(){
    // html转义
    return {
        rectrict: 'AE',
        scope: {htmlText: '='},
        link: function(scope,ele,attr){
            scope.$watch('htmlText',function(){
                if(typeof arguments[0] != 'undefined'){
                    ele.html(unescape(arguments[0]));
                }
            });
            
            function unescape(str) {
                var elem = document.createElement('div');
                elem.innerHTML = str;
                return elem.innerText || elem.textContent;
            }
        }
    };
})
.directive('addCart',function(){
    // 添加购物车
    return {
        rectrict: 'AE',
        link: function(scope,ele,attr){
            ele.on('click',function(){
                if(scope.choiceMore.isChoiceMore){
                    addCartMore();
                }
                else{
                    addCart(); 
                }
                
            });
                
            //添加购物车(启用多选)
            var addCartMore = function(){
                // 选中的规格 以及 选中个数
                var select_spec = $('#' + attr.from + '-spec').children('.goods-active'),
                spec_length = select_spec.length,
                
                // 选中的颜色 以及 选中的个数
                select_color = $('#' + attr.from + '-color').children('.goods-active'),
                color_length = select_color.length;
                
                if(scope.goodsDetail.Specification && spec_length === 0){
                    DHB.showMsg('请选择规格');
                    return;
                }
                if(scope.goodsDetail.Color && color_length === 0){
                    DHB.showMsg('请选择颜色');
                    return;
                }
                if(parseInt(scope.goods.num) <= 0){
                    DHB.showMsg('订货数量必须大于零!');
                    return;
                }
                if(scope.goodsDetail.Package && parseInt(scope.goodsDetail.Package) > 0){
                    if(parseInt(scope.goods.num) % parseInt(scope.goodsDetail.Package) !=0){
                        DHB.showMsg('订货数量必须为包装数量' + scope.goodsDetail.Package + '的倍数!');
                        return;
                    }
                }
                /**  
                 *   多选功能实现
                 *   1 判断规格和颜色是否同时存在（三种情况）
                 *   2 判断是否控制库存
                 *   3 不控制库存 判断是否整包装（是：输入数量是否是整包装倍数） 判断购物车是否已经添加（md5 加密 hex_md5('256红色0.5') id + color + specification）
                 *   4 控制库存 任意两种组合 库存是否够 判断购物车是否已经添加（md5 加密 hex_md5('256红色0.5') id + color + specification）
                 */
                // 存储已添加的商品名称 提示用户
                var hasAdd = [],
                // 库存不足
                noGoods = [],
                // 是否控制库存
                isStock = scope.goods.isStock,
                num = scope.goods.num; // 订购数量
                
                scope.goods.name = scope.goodsDetail.Name;
                scope.goods.price = scope.goodsDetail.Price;
                scope.goods.pic = scope.goodsDetail.Picture;
                //如果购物车为空
                if(!window.localStorage.cart){
                    window.localStorage.cart = JSON.stringify([]);
                }
                var cart = JSON.parse(window.localStorage.cart);
                
                // 只存在规格
                if(spec_length > 0 && color_length <= 0){
                    //select_spec
                    var _s_index;
                    for (var s = 0; s < spec_length; s += 1) {
                        // 没添加过购物车
                        _s_index = select_spec.eq(s).index();
                        if(!isAdd(scope.goodsDetail.Specification[_s_index],'')){
                            // 控制库存
                            if(isStock){
                                //scope.goodsDetail.Specification
                                if(isEnough(scope.goodsDetail.Specification[_s_index],'')){
                                    scope.goods.specify = scope.goodsDetail.Specification[_s_index]; //select_spec.eq(s).text();
                                    cart.push(cloneObj(scope.goods))
                                }
                            }
                            // 不控制库存
                            else{
                                scope.goods.specify = scope.goodsDetail.Specification[_s_index]; //select_spec.eq(s).text();
                                cart.push(cloneObj(scope.goods))
                            }
                        }
                    }
                } 
                // 只存在颜色
                else if(spec_length <= 0 && color_length > 0){
                    //select_color
                    var _c_index;
                    for (var c = 0; c < color_length; c += 1) {
                        // 没添加过购物车
                        _c_index = select_color.eq(c).index();
                        if(!isAdd('',scope.goodsDetail.Color[_c_index])){
                            // 控制库存
                            if(isStock){
                                if(isEnough('',scope.goodsDetail.Color[_c_index])){
                                    scope.goods.color = scope.goodsDetail.Color[_c_index] //select_color.eq(c).text();
                                    cart.push(cloneObj(scope.goods))
                                }
                            }
                            // 不控制库存
                            else{
                                scope.goods.color = select_color.eq(c).text();
                                cart.push(cloneObj(scope.goods))
                            }
                        }
                    }
                } 
                // 规格颜色同时存在
                else if(spec_length > 0 && color_length > 0){
                    var _m_index,_n_index;
                    for (var m = 0; m < spec_length; m += 1) {
                        for (var n = 0; n < color_length; n += 1) {
                            _m_index = select_spec.eq(m).index();
                            _n_index = select_color.eq(n).index();
                            if(!isAdd(scope.goodsDetail.Specification[_m_index],scope.goodsDetail.Color[_n_index])){
                                // 控制库存
                                if(isStock){
                                    if(isEnough(scope.goodsDetail.Specification[_m_index],scope.goodsDetail.Color[_n_index])){
                                        scope.goods.specify = scope.goodsDetail.Specification[_m_index]; //select_spec.eq(m).text();
                                        scope.goods.color = scope.goodsDetail.Color[_n_index]; //select_color.eq(n).text();
                                        cart.push(cloneObj(scope.goods))
                                    }
                                }
                                // 不控制库存
                                else{
                                    scope.goods.specify = scope.goodsDetail.Specification[_m_index]; //select_spec.eq(m).text();
                                    scope.goods.color = scope.goodsDetail.Color[_n_index]; //select_color.eq(n).text();
                                    cart.push(cloneObj(scope.goods))
                                }
                            }
                        }
                    }
                }
                if(noGoods.length > 0 && hasAdd.length == 0){
                    alert('库存不足: ' + noGoods.join(',') + '\n系统已自动过滤');
                }
                else if(noGoods.length == 0 && hasAdd.length > 0){
                    alert('购物车已存在：' + hasAdd.join(',') + '\n系统已自动过滤');
                }
                else if(noGoods.length > 0 && hasAdd.length > 0){
                    alert('购物车已存在：' + hasAdd.join(',') + '\库存不足: ' + noGoods.join(',') + '\n系统自动过滤');
                }
                scope.cartGoods.length = cart.length;
                scope.$apply(scope.cartGoods.length);
                window.localStorage.cart = JSON.stringify(cart);
                scope.$apply(attr.refreshCart);
                
                // 判断购物车是否已经添加
                function isAdd(specify, color){
                    color = color || '';
                    specify = specify || '';
                    // md5 加密 hex_md5('256红色0.5') id + color + specification
                    var compare = hex_md5(scope.goods.id + color + specify);
                    scope.goods.compare = compare;
                    for(var i = 0; i < cart.length; i += 1){
                        if(cart[i].compare == compare){
                            hasAdd.push((specify + '+' + color).replace(/^\+|\+$/g,''));
                            return true;
                        }
                    }
                    return false;
                }
                // 库存是否充足
                function isEnough(specify, color){
                    color = color || '';
                    specify = specify || '';
                    // md5 加密 hex_md5('256红色0.5') id + color + specification
                    var compare = hex_md5(scope.goods.id + color + specify);
                    scope.goods.compare = compare;
                    if(scope.goodsDetail.library){
                        for(var key in scope.goodsDetail.library){
                            if(key == compare){
                                if(num <= scope.goodsDetail.library[key]){
                                    return true
                                }
                                noGoods.push((specify + '+' + color).replace(/^\+|\+$/g,''));
                                return false;
                            }
                        }
                    }
                    return false;
                }
                // 克隆商品对象
                function cloneObj(obj){
                    var o = {};
                    for(var k in obj){
                        o[k] = obj[k];
                    }
                    return o;
                }
            };
            
            //添加购物车(未启用多选)
            var addCart = function(){
                if(scope.goodsDetail.Specification && scope.goods.specify == ''){
                    DHB.showMsg('请选择规格');
                    return;
                }
                if(scope.goodsDetail.Color && scope.goods.color == ''){
                    DHB.showMsg('请选择颜色');
                    return;
                }
                
                // 判断是否控制库存
                if(scope.goodsDetail.controllerLibrary == 'Y'){
                    if(parseInt(scope.goods.num) > parseInt(scope.goodsDetail.allLibrary)){
                        DHB.showMsg('库存不足!');
                        return;
                    }
                }
                if(parseInt(scope.goods.num) <= 0){
                    DHB.showMsg('订货数量必须大于零!');
                    return;
                }
                if(scope.goodsDetail.Package && parseInt(scope.goodsDetail.Package) > 0){
                    if(parseInt(scope.goods.num) % parseInt(scope.goodsDetail.Package) !=0){
                        DHB.showMsg('订货数量必须为包装数量' + scope.goodsDetail.Package + '的倍数!');
                        return;
                    }
                }
                if(!scope.goodsDetail.Color){
                    scope.goods.color = '';
                }
                if(!scope.goodsDetail.Specification){
                    scope.goods.specify = '';
                }
                // md5 加密 hex_md5('256红色0.5') id + color + specification
                var compare = hex_md5(scope.goods.id + scope.goods.color + scope.goods.specify);
                
                scope.goods.name = scope.goodsDetail.Name;
                scope.goods.compare = compare;
                scope.goods.price = scope.goodsDetail.Price;
                scope.goods.pic = scope.goodsDetail.Picture;
                
                //如果购物车为空
                if(!window.localStorage.cart){
                    window.localStorage.cart = JSON.stringify([]);
                }
                var cart = JSON.parse(window.localStorage.cart);
                for(var i = 0; i < cart.length; i += 1){
                    if(cart[i].compare == scope.goods.compare){
                        DHB.showMsg('该商品已添加!');
                        return;
                    }
                }
                
                cart.push(scope.goods)
                scope.cartGoods.length = cart.length;
                scope.$apply(scope.cartGoods.length);
                window.localStorage.cart = JSON.stringify(cart);
                scope.$apply(attr.refreshCart); 
            };
        }
    };
})
.directive('startScanner',function(){
    //二维码扫描
    return function(scope,ele,attr){
        ele.on('click',function(){
            if(!DHB.device.WeiXin){
                uexScanner.open();
            }/*else{
                //wxConfig.scanner();
            }*/
        });
    };    
})
.directive('iscroll',function(){
    //
    return function(scope,ele,attr){
        new iScroll(ele[0],{ checkDOMChanges: true,mouseWheel: false ,hideScrollbar: true,
            fadeScrollbar: true,scrollbarClass: 'myScrollbar',
            onBeforeScrollStart: function (e) {
                var target = e.target;
                while (target.nodeType != 1) {
                    target = target.parentNode;
                }
                if (target.tagName.toUpperCase() != 'SELECT' && target.tagName.toUpperCase() != 'TEXTAREA' && target.tagName.toUpperCase() != 'INPUT') {
                    e.preventDefault();
                }
            }
        });
        /*if(DHB.device.Ios){
            new iScroll(ele[0],{ checkDOMChanges: true,mouseWheel: false ,
                scrollbars: true
            });
        }else{
            $(ele[0]).children().eq(0).css({
                top: 0,
                left: 0,
                bottom: '50px',
                'overflow-y': 'auto'
            });
        }*/
    };    
})
.directive('toggleFocus',function(){
    // 获取焦点向上移动
    return function(scope,ele,attr){
        ele.css('-webkit-transition','-webkit-transform .45s');
        $(ele[0]).on('focusin',function(){
            $(this).css('-webkit-transform','translateY(-150%)');
        })
        .on('focusout',function(){
            $(this).css('-webkit-transform','translateY(0)');
        });
    };    
})
.directive('zoomImg',function(){
    // 图片放大
    return function(scope,ele,attr){
        ele.on('click',function(e){
            e.stopPropagation();
            if(ele.hasClass('zoom-img')){
                ele.removeClass('zoom-img');
                $('#detail-img').css({
                    top: '5rem',
                    bottom: '5rem',
                    'z-index': 0
                });
                return;
            }
            
            ele.addClass('zoom-img');
            $('#detail-img').css({
                top: 0,
                bottom: 0,
                'z-index': 2
            }).scrollTop(0);
        })
        .on('touchmove',function(e){
            e.preventDefault();
        })
    };    
})
.directive('stopBubble',function(){
    // 阻止冒泡
    return function(scope,ele,attr){
        ele.on('click',function(e){
            e.stopPropagation();
        })
    };    
})
.directive('focus',function(){
    // 获取焦点
    return function(scope,ele,attr){
        ele.on('focus',function(){
            if(parseFloat(this.value) == 0){
                this.value = '';
            }
        })
    };    
})
.directive('focusInput',function(){
    // 获取焦点
    return function(scope,ele,attr){
        $(ele[0]).on('click',function(){
            $(this).focus();
        })
    };    
})
.directive('insteadBack',function(){
    // 代下订单返回按钮添加拖动事件
    return function(scope,ele,attr){
        var s_l,s_t;
        ele.on('touchstart',function(e){
        	console.log(e.targetTouches[0])
            e.stopPropagation();
            //e.preventDefault();
            if(e.targetTouches.length == 1){
                var touch = e.targetTouches[0];
                s_l = touch.pageX;
                s_t = touch.pageY;
            }
        })
        .on('touchmove',function(e){
            e.stopPropagation();
            e.preventDefault();
            if(e.targetTouches.length == 1){
                var touch = e.targetTouches[0];
                var diff_x = touch.pageX - s_l ;
                var diff_y = touch.pageY - s_t ;
                ele.css('-webkit-transform','translate3d(' + diff_x +'px,' + diff_y + 'px,0)');
            }
        })
        .on('touchend',function(e){
            if(e.changedTouches.length == 1){
                var touch = e.changedTouches[0];
                var l = (touch.pageX - 22);
                var t = (touch.pageY - 22);
                if(l < 0){
                    l = 0;
                }
                else if(l > $(window).width()-40){
                    l = $(window).width()-40;
                }
                if(t < 0){
                    t = 0;
                }
                else if(t > $(window).height()-40){
                    t = $(window).height()-40;
                }
                
                ele.css({
                    top: t + 'px',
                    left: l + 'px',
                    '-webkit-transform': 'translate3d(0,0,0)'
                });
            }
        })
    };
})
/**
 *  bsmm directive 
 */
.directive('bsmmMenuActive',function(){
    //首页点击添加文字颜色为红色
    return function(scope,ele,attr){
        $(ele[0]).on('click','li',function(e){
            
            if(DHB.menuIndex == $(this).index()) return;
            //移除上一个选项的class
            ele.find('li').eq(DHB.menuIndex).removeClass('menu-active');
            
            //为当前选项添加class
            DHB.menuIndex = $(this).addClass('menu-active').index();
            
            // 移动下划线
            ele.next().css({
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
        });
    };
})

.directive('imgLoaded',function(){
    // 图片加载完成
    return {
        restrict: 'AE',
        scope: {
            page: '@'
        },
        link: function(scope,ele){
            // 浏览器（微信）访问不用特殊处理 
            if(DHB.device.WeiXin || DHB.device.Ios) return;
            
            // 兼容app 图片加载完成重新设置宽高
            ele.on('load',function(){
                // index img loaded
                if(scope.page == 'index'){
                    if(this.width/this.height > 5/4){
                        this.style.width = 10 + 'rem';
                    }
                    else{
                        this.style.height = 8 + 'rem';
                    }
                }
                // goods-list img loaded
                else if(scope.page == 'list'){
                    if(this.width/this.height >= $(ele[0]).parent().width()/$(ele[0]).parent().height()){
                        this.style.width = $(ele[0]).parent().width()/10 + 'rem';
                    }
                    else{
                        this.style.height = $(ele[0]).parent().height()/10 + 'rem';
                    }
                }
                
                // page-info img loaded
                else if(scope.page == 'info'){
                    if(this.width/this.height >= $(ele[0]).parent().width()/230){
                        if(this.width > $(window).width()){
                            this.style.width = $(window).width()/10 + 'rem';
                        }
                        else{
                            this.style.width = this.width/10 + 'rem';
                        }
                    }
                    else{
                        if(this.height > 230){
                            this.style.height = 23 + 'rem';
                        }
                        else{
                            this.style.height = this.height/10 + 'rem';
                        }
                    }
                }
            });
            
        }
    };
})
.directive('toggleDay',['commonService',function(commonService){
    // 动态追加当月日销量
    return {
        restrict: 'AE',
        scope: {month: '@'},
        link: function(scope,ele,attr){
            var flag = false,loadFlag = false;
            ele.on('click',function(){
                if(loadFlag) return;
                if(ele.attr('load')){
                    if(!flag){
                        flag = true;
                        setTimeout(function(){
                            flag = false;
                        },452);
                        $(ele[0]).next().toggle();
                    }
                    
                    return;
                }
                loadFlag = true;
                scope.month = scope.month.replace(new RegExp('^(\\d{4})'),function(m,p1,index){
                    return p1 + '-';
                });
                // 获取日销量
                commonService.getDataList({f: 'managerDayCount',v: {month: scope.month}}).success(function(data){
                    loadFlag = false;
                    if(data && data.rStatus == '100'){
                        if(data.rData){
                            appendDay(data.rData);
                        }
                    }
                }).error(function(data){
                    loadFlag = false;
                    //alert('获取数据失败!');
                    DHB.showMsg('获取日销量失败')
                }); 
            });
            //动态追加当月日销量
            function appendDay(data){
                var ul = document.createElement('ul');
                ul.className = 'dayCount';
                var li_str = '';
                for(var i = 0,len = data.length; i < len; i += 1){
                    li_str += '<li><p><strong>' + data[i].ODate.slice(-2) + '日</strong></p><p><span>¥ ' + data[i].CountTotal + 
                               '</span><span>' + data[i].CountNumber + '笔</span></p></li>';
                }
                ul.innerHTML = li_str;
                $(ele[0]).attr('load','true').after(ul);
            }
        }
    };
}])
;
