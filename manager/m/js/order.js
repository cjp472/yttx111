/*********/

$(function(){
    $("body").on('click','.blockOverlay',function(){
        $.unblockUI();
    });
});

var old_bg="";
function inStyle(obj)
{
    old_bg=obj.style.background;
	obj.style.background="#edf3f9";
}
function outStyle(obj)
{
    obj.style.background=old_bg;
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 

function CheckAll(form)
{
  for (var i=0;i<form.elements.length;i++)
  {
    var e = form.elements[i];
    if (e.name != 'chkall' && e.name !='copy')       e.checked = form.chkall.checked; 
  }
}

function closewindowui()
{
	$.unblockUI();
	//window.setTimeout($.unblockUI, 1000);
}

function closewindowui2()
{
	window.location.reload();
}

/**
 * 指定特价订单
 * @param info
 * @author hxtgirq
 * @since 2015-08-13
 */
function setting_special_form(info) {
    var win = $("#windowFormSpecial");
    win.find("td[data-order-no]").html(info.OrderSN);
    win.find("td[data-order-total]").html('¥' + info.OrderTotal);
    win.find("input[name='order_id']").val(info.OrderID);
    $.blockUI({
        message : win
    });
}

/**
 * 提交特价单
 * @author hxtgirq
 * @since 215-08-13
 */
function setting_special() {
    //提交特价
    var special_form = $("#special_form");
    //TODO::限制
    $.post(special_form.attr('action'),special_form.serialize(),function(data){
        data = Jtrim(data);
        if(data == 'ok') {
            $.blockUI({
                message : '<p>特价设置成功!</p>'
            });
            setTimeout(function(){
                location.reload();
            },710);
        } else {
            $.blockUI({
                message : '<p>'+data+'</p>'
            });
        }
    },'text');
}

/**
 * 确认付款单到账
 * @param fid
 * @returns {boolean}
 */
function sure_finance(fid) {
    if(!confirm('确认已到账了吗?此操作不可逆')) {
        return false;
    }
    $.post("do_finance.php",{
        m:"validate_finance",
        ID:fid
    },function(data) {
        data = Jtrim(data);
        if(data == 'error') {
            alert("设置不成功!");
        } else if(data == 'nopope') {
            alert("对不起您没有些项权限!");
        } else {
            $("#finance_status_" + fid).html("已到账");
            $("#finance_short_" + fid).html("--");
            window.finance_undone--;
            //操作成功
            //如果所有款项都为已到账状态则刷新当前页面
            if(window.finance_undone == 0) {
                window.location.reload();
            }
        }
    },'text');
}

/**
 * 确认发货单已收货
 * @param cid
 * @return {boolean}
 */
function sure_consignment(cid) {
    if(!confirm('确定货物已签收吗?')) {
        return false;
    }
    $.post("do_consignment.php",{
        m:"setSendFlag",
        ConsignmentID:cid
    },function(data){
        data = Jtrim(data);
        if(data != 'ok') {
            alert(data);
        } else {
            //操作成功
            $("#consignment_status_" + cid).html("已签收");
            $("#consignment_short_" + cid).html('--');
            window.consignment_undone--;
            if(window.consignment_undone == 0) {
                window.location.reload();
            }
        }
    },'text');
}

function do_order_status(action,oid,info)
{
    if(action == "Send") {
        info.OrderPayType = parseInt(info.OrderPayType);
        info.OrderPayStatus = parseInt(info.OrderPayStatus);
        // 验证是否允许发货 先货后款、已付款
        if(-1 !== $.inArray(info.OrderPayType,[1,2,3,7]) && (info.OrderPayStatus != 2 && info.OrderPayStatus !=3)) {
            //先付 & 未付
            $.blockUI({
                message : '<p>需要先付款後才能发货!</p>'
            });
        } else {
            window.location.href = "consignment_add.php?ID=" + oid;
        }
        return false;
    } else if(action == 'Pay') {
        //已付款  有付款单则展示出来 让用户确认到款 没有付款单则跳至新增付款单
        $.post("do_order.php",{
            m:'ajax_finance',
            OrderSN:info.OrderSN
        },function(data){

            var tContent = "";
            if(data.status == 1 && data.data) {
                window.finance_undone = 0; //未完成的付款单数量
                $(data.data).each(function(){
                    tContent += '<tr>';
                    tContent += '<td> '+this.FinanceID+' </td>';
                    tContent += '<td> '+this.FinanceToDate+' </td>';
                    var flag = "已到账";
                    var act = '--';
                    if(this.FinanceFlag == 0) {
                        flag = "待确认";
                        act = "<a href='javascript:;' onclick='sure_finance("+this.FinanceID+");'>确认到账</a>";
                        window.finance_undone++;
                    }
                    tContent += '<td id="finance_status_'+this.FinanceID+'"> '+ flag +' </td>';
                    tContent += '<td> <span id="finance_short_'+this.FinanceID+'">' + act + '</span></td>';
                    tContent += '</tr>';
                });
            }
            if(data.status == 0 || window.finance_undone == 0) {
                //没有付款单 或付款单都已确认(未付完)
                window.location.href = "finance_add.php?oid=" + info.OrderID;
            } else if(data.status == -1) {
                window.location.reload();
            } else {
                $("#windowFormShort .windowtitle").html("订单 [ "+info.OrderSN+" ] 付款信息");
                $("#dan_content").html(tContent);
                $.blockUI({message:$("#windowFormShort")});
            }
            return false;
        },'json');
        return false;
    } else if(action == 'Incept') {
        //fixme 确认收货
        $.post("do_order.php",{
            m:'ajax_consignment',
            OrderSN:info.OrderSN
        },function(data){
            if(data.status === 0) {
                //未找到有发货单
                window.location.href = "consignment_add.php?ID=" + info.OrderID;
            } else if(data.status == -1) {
                //没有发货单,但是又没有可发货的商品
                //这里就直接更新订单状态为已收货
                $.post("do_order.php",{
                    m:'ajax_order_incept',
                    OrderSN:info.OrderSN
                },function(rdata){
                    rdata = Jtrim(rdata);
                    if(rdata == 'ok') {
                        $.blockUI({
                            message : '<p>操作完成!</p>'
                        });
                        setTimeout(function(){
                            $.unblockUI();
                            window.location.reload();
                        },710);
                    } else {
                        $.blockUI({
                            message : '<p>'+rdata+'</p>'
                        });
                    }
                },'text');
            } else {
                var tContent = "";
                window.consignment_undone = 0;//未确认收货的发货单
                $(data.data).each(function(){
                    tContent += '<tr>';
                    tContent += '<td>'+this.ConsignmentID+'</td>';
                    tContent += '<td>'+this.ConsignmentDate+'</td>';
                    var flag = '已签收';
                    var act = '--';
                    if(this.ConsignmentFlag == 0) {
                        flag = "未确认";
                        act = '<a href="javascript:;" onclick="sure_consignment('+this.ConsignmentID+');">确认收货</a>';
                        window.consignment_undone++;
                    }
                    tContent += '<td id="consignment_status_'+this.ConsignmentID+'">'+flag+'</td>';
                    tContent += '<td id="consigment_short_'+this.ConsignmentID+'">'+act+'</td>';
                    tContent += '</tr>';
                });
                $("#windowFormShort .windowtitle").html("订单 [ "+info.OrderSN+" ] 发货信息");
                $("#dan_content").html(tContent);
                $.blockUI({message:$("#windowFormShort")});
            }
        },'json');
        return false;
    }else if(action=='Cancel'){

		var content=$('#data_OrderContent').val();
		$(".piaochecked").removeClass("on_check");
		if(content != ''){
			
			$("#replycontent").val(content);
			$(".piaochecked:last").addClass("on_check"); 
			$("#replycontent").removeAttr("readonly");
			
		}else{
			$("#replycontent").val("");
			$("#replycontent").attr("readonly","readonly");
			$(".piaochecked:first").addClass("on_check"); 
		}
		
		$(".mask").css("display","block");
		return false;
		
	}
	
	if(action=='Message' || confirm('确认进行此操作吗!') )
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php",
			{m:action, ID: oid, Content: $('#data_OrderContent').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" });
					//alert('操作成功!');
					if(action=="Delete"){
						window.location.href='order.php';
					}else{
						window.location.reload();
					}
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}else{
		return false;
	}
	
	
	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	window.setTimeout($.unblockUI, 2000); 
}

function do_cancel_orders(oid,action){
	
	var val=$(".on_check").find("span").html();
	if(val == "其他"){
		val=$("#replycontent").val();
		if(val ==''){
			$.blockUI({ message: "<p>请输入其他原因！!</p>" });
			return false;
		}
	}
	$.post("do_order.php",
			{m:action, ID: oid, Content: val},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" });
						window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function do_save_order_product(t,oid)
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php?m=edit_order_product_save&t="+t, $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
					if(t=="back" && data.length < 20)
					{
						window.location.href='order_manager.php?ID='+oid;
					}else{
						window.location.reload();
					}			
			}		
		);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

function addtocart(pid,oid,cid)
{
	var selectcolor = $("#color_"+pid).val();
	var selectspec  = $("#spec_"+pid).val();
		
	$.post("do_order.php",
		{m:"addtocart", pid: pid, oid: oid, cid: cid, pcolor: selectcolor, pspec: selectspec },
		function(data){
			data = Jtrim(data);
			if(data=="ok"){
				$.growlUI('已成功将该商品添加到订单中！<br />点击 <a href="order_product_edit.php?ID='+oid+'" target="_top">下一步，订购商品管理</a>');				
			}else{
				$.growlUI(data);
			}
		}			
	);
}

function addtocart_new(pid,oid,cid)
{
	var selectcolor = $("#color_"+pid).val();
	var selectspec  = $("#spec_"+pid).val();
		
	$.post("do_order.php",
		{m:"addtocart", pid: pid, oid: oid, cid: cid, pcolor: selectcolor, pspec: selectspec },
		function(data){
		    data = Jtrim(data);
			if(data=="ok"){
				$.growlUI('已成功将该商品添加到订单中！<br />点击 <a href="new_order_product_edit.php?ID='+oid+'" target="_top">下一步，订购商品管理</a>');				
			}else{
				$.growlUI(data);
			}
		}			
	);
}

function addtocart_select(pid,oid,cid)
{
	var selectcolor = $("#color_"+pid).val();
	var selectspec  = $("#spec_"+pid).val();
		
	$.post("do_order.php",
		{m:"addtocart", pid: pid, oid: oid, cid: cid, pcolor: selectcolor, pspec: selectspec },
		function(data){
			data = Jtrim(data);
			if(data=="ok"){
				$.growlUI('已成功将该商品添加到订单中！<br /><a href="javascript:void();" onclick="parent.closewindowui2();">点击返回</a>');				
			}else{
				$.growlUI(data);
			}
		}			
	);
}

function addtocart_gifts(pid,oid,cid)
{
	var selectcolor = $("#color_"+pid).val();
	var selectspec  = $("#spec_"+pid).val();
		
	$.post("do_order.php",
		{m:"addtocart_gifts", pid: pid, oid: oid, cid: cid, pcolor: selectcolor, pspec: selectspec },
		function(data){
			data = Jtrim(data);
			if(data=="ok"){
				$.growlUI('已成功将该赠品添加到订单中！<br />点击 <a href="order_gifts_product.php?ID='+oid+'">下一步，订单赠品管理</a>');				
			}else{
				$.growlUI(data);
			}
		}			
	);
}

function do_save_order_product_new(t,oid)
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php?m=new_edit_order_product_save&t="+t, $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
					$.blockUI({ message: "<p>"+data+"</p>" }); 
					if(t=="back")
					{
						window.location.href='order_manager.php?ID='+oid;
					}else{
						window.location.reload();
					}			
			}		
		);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

function do_save_gifts_product(t,oid)
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php?m=order_gifts_product_save&t="+t, $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
					$.blockUI({ message: "<p>"+data+"</p>" }); 
					if(t=="back" && data.length < 20)
					{
						window.location.href='order_manager.php?ID='+oid;
					}else{
						window.location.reload();
					}			
			}		
		);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

function do_checkoff_order_product(t,oid)
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		
		//商品名称
		var $cart_name = $('input[name="cart_name[]"]');
		//商品单价
		var $cart_price = $('input[name="cart_price[]"]');
		//商品折扣
		var $cart_percent = $('input[name="cart_percent[]"]');
		
		//商品库存
		var $product_number = $('input[name="product_number[]"]');
		//产品已发货
		var $send_num  = $('input[name="send_num[]"]');
		//产品数量
		var $cart_num  = $('input[name="cart_num[]"]');
		var $old_cart_num  = $('input[name="old_cart_num[]"]');	
		
		
		for(var i=0; i<$cart_name.length; i++){
			 var cart_name			= $cart_name.eq(i).val();
			 var product_number	    = parseInt($product_number.eq(i).val());
			 var cart_price	    	= $cart_price.eq(i).val();
			 var cart_percent	    = $cart_percent.eq(i).val();
			 var send_num	    	= parseInt($send_num.eq(i).val());
			 var cart_num	    	= parseInt($cart_num.eq(i).val());
			 var old_cart_num	    = parseInt($old_cart_num.eq(i).val());			
			 
			//商品订购数量不能 > 商品库存
			if($product_number.length > 0 && cart_num > old_cart_num && (cart_num-old_cart_num) > product_number ){

				$.blockUI({ message: "<p>商品："+cart_name+" , 库存不足！</p>" });
				$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
				$cart_num.eq(i).focus();
				return false;
			}else{
				//商品订购数量不能 < 已发货数量
				if(cart_num<send_num){
					$.blockUI({ message: "<p>商品："+cart_name+" , 订购数量不能小于已发货数量！</p>" });
					$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
					$cart_num.eq(i).focus();
					return false;
				}
			}
			
			//商品价格不能小于0
			if(cart_price < 0 ){
				$.blockUI({ message: "<p>商品："+cart_name+" , 单价必须≥0！</p>" });
				$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
				$cart_price.eq(i).focus();
				return false;
			}
			
			//商品折扣 区间为 0-10 
			if(cart_percent < 0 || cart_percent>10){
				$.blockUI({ message: "<p>商品："+cart_name+" , 折扣区间为0~10！</p>" });
				$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
				$cart_percent.eq(i).focus();
				return false;
			}
		}
		
		
		
		$.post("do_order.php?m=checkoff_order_product_save&t="+t, $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
					if(t=="back" && data.length < 20)
					{
						window.location.href='order_manager.php?ID='+oid;
					}else{
						window.location.reload();
					}			
			}		
		);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

function show_hide(ty,idname)
{
	if(ty == "show")
	{
		$("#"+idname).animate({opacity: 'show'}, 'slow');
	}else{
		$("#"+idname).animate({opacity: 'hide'}, 'slow');
	}
}

function hideshow(divid)
{
	$("#"+divid).animate({opacity: 'hide'}, 'slow');
}

function contral_list(ty)
{
	if(ty == "show")
	{
		$("#show_library_list").animate({opacity: 'show'}, 'slow');
		$("#show_library").html('<img src="img/shou.gif" border="0" class="img" onclick="contral_list(\'hide\');" /> <span onclick="contral_list(\'hide\');">收起</span>');
	}else{
		$("#show_library_list").animate({opacity: 'hide'}, 'slow');
		$("#show_library").html('<img src="img/jia.gif" border="0" class="img" onclick="contral_list(\'show\');" /> <span onclick="contral_list(\'show\');">展开</span>');
	}
}

function contral_list_order(ty,oid)
{
	if(ty == "show")
	{
		if($('#show_old_order_list').html() == "")
		{
			$('#show_old_order_list').html('<iframe src="show_bak_order.php?oid='+oid+'" width="100%" marginwidth="0" height="400" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
		}
		$("#show_old_order_list").animate({opacity: 'show'}, 'slow');
		$("#show_order_img").html('<img src="img/shou.gif" border="0" class="img" onclick="contral_list_order(\'hide\',\''+oid+'\');" /> <span onclick="contral_list_order(\'hide\');">收起</span>');
	}else{
		$("#show_old_order_list").animate({opacity: 'hide'}, 'slow');
		$("#show_order_img").html('<img src="img/jia.gif" border="0" class="img" onclick="contral_list_order(\'show\',\''+oid+'\');" /> <span onclick="contral_list_order(\'show\');">展开</span>');
	}
}

/******** 订单 ******************/
function get_addlist(oid)
{
	$("#showuserorder").html("<p>正在载入数据，请稍后...</p>" ); 
	$.post("do_order.php",
		{m:"loadaddlist", ID: oid},
		function(data){
			$("#showuseraddress").html(data);
		}		
	);
}

function set_address_value(aid,acompany,acontact,aphone,aaddress)
{
	$("#data_OrderReceiveCompany").val(acompany);
	$("#data_OrderReceiveName").val(acontact);
	$("#data_OrderReceivePhone").val(aphone);
	$("#data_OrderReceiveAdd").val(aaddress);
	selectorderline(aid);
}

function selectorderline(foid)
{
	if($("#orderadd_"+foid).attr('checked')==false){
		$("#orderadd_"+foid).attr("checked",true);
		$("#showuseraddress").children().find("tr").each(function(){
			$(this).css("background-color","#ffffff");
		});
		$("#selected_line_"+foid).css("background-color","#efefef");
	}
}

/*********** 新订单 *****************/
function do_save_new_order()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php?m=saveaddneworder", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data.indexOf('error-')+1){
					$.blockUI({ message: "<p>"+data.substr(6)+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}
				else if(data.length > 11){
					$.blockUI({ message: "<p>保存不成功！</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "error"){
					$.blockUI({ message: "<p>保存不成功！</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = 'new_order_add_product.php?oid='+data;
				}				
			}		
		);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

/********************/
function do_return_status(action,oid)
{
	if(confirm('确认进行此操作吗!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_return.php",
			{m:action, ID: oid, Content: $('#data_OrderContent').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" });
					alert('操作成功!');
					if(action=="Delete"){
						window.location.href='return.php';
					}else{
						window.location.reload();
					}
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}else{
		return false;
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	window.setTimeout($.unblockUI, 2000); 
}


/*********** 新退货单 *****************/
function do_save_new_return()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });

	if($('#ReturnAbout').val() == "")
	{
		$.blockUI({ message: "<p>请输入退货原因!</p>" });
	}else if($('#ReturnProductW').val() == "" || $('#ReturnProductB').val() == ""){
		$.blockUI({ message: "<p>请选择产品外观和包装情况!</p>" });
	}else{
		$.post("do_return.php?m=save_return_add", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data=="ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = 'return.php';
				}else if(data == "error"){
					$.blockUI({ message: "<p>保存不成功！</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

//商品
function do_save_new_return_product()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });

	if($('#ReturnAbout').val() == "")
	{
		$.blockUI({ message: "<p>请输入退货原因!</p>" });
	}else if($('#ReturnProductW').val() == "" || $('#ReturnProductB').val() == ""){
		$.blockUI({ message: "<p>请选择产品外观和包装情况!</p>" });
	}else{
		$.post("do_return.php?m=save_return_product_add", $("#MainForm").serialize(),
			function(data){

				data = Jtrim(data);
				if(data=="ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					top.window.location.href = 'return.php';
				}else if(data == "error"){
					$.blockUI({ message: "<p>保存不成功！</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}


/***********  *****************/
function select_return_product()
{
	$("#windowtitle").html('请选择退货商品');
	$('#windowContent').html('<iframe src="select_return_product.php?kw='+$('#kw').val()+'&selectid='+$('#selectid_return').val()+'&cid='+$('#cid').val()+'" width="100%" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'480px',top:'8%'
            }			
		});
	$('#kw').val('');
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function add_select_product()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	$('#buttonselected').attr("disabled","disabled");
	$.post("do_return.php?m=add_to_select_product", $("#MainForm").serialize(),
		function(data){
			if(data.backtype == "ok")
			{					
				parent.set_select_product(data.htmldata,data.selectiddata);
			}else{
				closewindowui();
				parent.closewindowui();
			}
		},"json");
	closewindowui();
}

function set_select_product(htmldata,selectiddata)
{
	$('#come_add_sel_pro').append(htmldata);
	$('#selectid_return').val(selectiddata);
	closewindowui();
}

function del_line_select_product(lineid)
{
	$('#'+lineid).remove();
}


/***********************************************/

//通知经销商已收到订单
function notify_client(el) {
    var order_ids = $.map($("input[name='selectedID[]']:checked"),function(el){
            return $(el).val();
    }) || [];
    if(order_ids.length == 0) {
        return false;
    }
    $(el).html('通知中...').attr('disabled','disabled');
    $.post("do_order.php",{
        m:'notify_client',
        order_ids:order_ids.join(',')
    },function(json){
    	/** 更改对应订单页面显示信息 **/
    	var succ_ids = json.success;
    	var succArr = succ_ids.split(',');
    	for (i=0;i<succArr.length ;i++ ) 
		{
    		$("#sms_"+succArr[i]).html("[已短信通知]");
		}
    	/** ENd **/
        if(json.status == 'ok') {
            $.blockUI({
                message : '<p>通知成功</p>'
            })
        } else {
            $.blockUI({
                message : '<p>' + json.message + '</p>'
            })
        }
        $(el).html('收到订单').removeAttr('disabled');
        setTimeout(function(){
            $.unblockUI();
        },1000);
    },'json');

}

function out_order_excel()
{
//	document.MainForm.action = 'order_excel.php';
    document.MainForm.submit();
}
function out_orderlist_excel(id)
{
	if(id !== '' && id != '0')
		document.MainForm.action = 'order_list_excel_'+id+'.php';
	else
		document.MainForm.action = 'order_list_excel.php';
    document.MainForm.submit();
}
//获取所用订单信息
function out_all_orderlist_excel(){
//document.MainForm.action = 'order_all_list_excel.php';
// document.MainForm.submit();
	$("#windowtitle").html('导出全部商品数据');

	$('#windowContent').html('<iframe src="order_out_number.php" width="100%" marginwidth="0" height="350" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'380px',top:'8%',left:"20%"
            }			
		});
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}
function out_orderph_excel()
{
	document.MainForm.action = 'order_ph_excel.php';
    document.MainForm.submit();
}

function out_order_kis_excel()
{
	document.MainForm.action = 'order_kis_excel.php';
    document.MainForm.submit();
}

function out_order_kis2_excel()
{
	document.MainForm.action = 'order_kis2_excel.php';
    document.MainForm.submit();
}

function out_order_k3_excel()
{
	document.MainForm.action = 'order_k3_excel.php';
    document.MainForm.submit();
}
function out_order_sd_excel()
{
	document.MainForm.action = 'order_sd_excel.php';
    document.MainForm.submit();
}
function out_order_gjp_excel()
{
	document.MainForm.action = 'order_gjp_excel.php';
    document.MainForm.submit();
}
function out_return_excel()
{
	document.MainForm.action = 'return_excel.php';
    document.MainForm.submit();
}
function out_order_product_excel()
{
	document.MainForm.action = 'order_product_excel.php';
    document.MainForm.submit();
}
function out_order_product_gifts_excel()
{
	document.MainForm.action = 'order_product_gifts_excel.php';
    document.MainForm.submit();
}
/********************************/
function show_bak_order(oid)
{
	var hw = window.screen.width;
	var leftp = 0;
	leftp = hw/2 - 400;
	$('#windowtitle').html('查看原始订单');
	$('#windowForm6').css("width","800px");
	$('#windowContent').html('<iframe src="show_bak_order.php?oid='+oid+'" width="100%" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '800px',height:'480px',top:'8%',left:leftp
            }			
		});
	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function show_nosend_orderproduct(oid)
{
	var hw = window.screen.width;
	var leftp = 0;
	leftp = hw/2 - 400;

	$('#windowtitle').html('查看未发货订单商品');
	$('#windowForm6').css("width","800px");
	$('#windowContent').html('<iframe src="show_nosend_product.php?oid='+oid+'" width="100%" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '800px',height:'480px',top:'8%',left:leftp
            }			
		});
	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

//快速订购
function select_product(oid)
{
	var hw = window.screen.width;
	var leftp = 0;
	leftp = hw/2 - 400;
	$('#windowtitle').html('快速订购');
	$('#windowForm6').css("width","800px");
	$('#windowContent').html('<iframe src="order_select_product.php?oid='+oid+'&kw='+$('#inputsp').val()+'" width="100%" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '800px',height:'480px',top:'8%',left:leftp
            }			
		});
	
	//$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

//退单修改
function do_save_return_product(t,oid)
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php?m=edit_return_product_save&t="+t, $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
					if(t=="back" && data.length < 20)
					{
						window.location.href='return_manager.php?ID='+oid;
					}else{
						window.location.reload();
					}			
			}		
		);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

//原始退单
function contral_list_return(ty,oid)
{
	if(ty == "show")
	{
		if($('#show_old_return_list').html() == "")
		{
			$('#show_old_return_list').html('<iframe src="show_bak_return.php?oid='+oid+'" width="100%" marginwidth="0" height="400" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
		}
		$("#show_old_return_list").animate({opacity: 'show'}, 'slow');
		$("#show_return_img").html('<img src="img/shou.gif" border="0" class="img" onclick="contral_list_return(\'hide\',\''+oid+'\');" /> <span onclick="contral_list_return(\'hide\');">收起</span>');
	}else{
		$("#show_old_return_list").animate({opacity: 'hide'}, 'slow');
		$("#show_return_img").html('<img src="img/jia.gif" border="0" class="img" onclick="contral_list_return(\'show\',\''+oid+'\');" /> <span onclick="contral_list_return(\'show\');">展开</span>');
	}
}

function advsearch(ty)
{
	if(ty=='show')
	{
		$("#advseaerchdiv").animate({opacity: 'show'}, 'slow');
	}else{
		$("#advseaerchdiv").animate({opacity: 'hide'}, 'slow');
	}
}

function resetadvform()
{
	$('#AdvSearch input').val("");
	$('#AdvSearch select').val("");
	$("#advbutton").val(" 搜 索 ");
	$("#resetladvbutton").val(" 重置 ");
	$("#canceladvbutton").val(" 关闭 ");
}

function addtoorder(pid,cstype,oid)
{
	if(cstype=="N")
	{
        showinnumcart(pid);
		window.setTimeout("hideshow('shareit-box')",60000);
	}else{
		$('#shareit-box').hide();
		window.setTimeout("showcartdiv("+pid+","+oid+")",1);
	}	
}

function showinnumcart(pid)
{
	var cartid = $("#shareit_"+pid);
	//var top  = cartid.offset().top-120;
	//var left = cartid.offset().left + (cartid.width() / 2) - ($('#shareit-box').width() / 2);
    //console.log(cartid.offset());
    var top = cartid.offset().top - ($("#shareit-box").height() / 2) + 15;
    var left = cartid.offset().left - $("#shareit-box").width() - 10;
	$('#shareit-box').show();
	$('#shareit-box').css({'top':top, 'left':left});
	$('#shareit-field').focus();
	$('#togoodsid').val(pid);
	$('#shareit-field').val(1);
}

function showcartdiv(pid,oid)
{
		$('#windowContent').html('<iframe src="tocart.php?ID='+pid+'&oid='+oid+'" width="540" marginwidth="0" height="340" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');	
		$.blockUI({ 
			message: $('#windowForm'),
			css:{ 
					width: '540px',height:'350px',top:'15%'
				}			
			});
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
}

function add_input_number()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	$('#addtocart').attr("disabled","disabled");
	$.post("do_order.php?m=add_input_number_save", $("#MainFormNumber").serialize(),
		function(data){
			if(data == "ok")
			{					
				parent.updatecartnumber();
			}else{
				alert(data);
				$('#addtocart').removeAttr('disabled');
			}
		});
	closewindowui();
}

function changeupnumber(sspec,hcolor,packagenum)
{
	var iputid = "inputn_"+sspec+"_"+hcolor;
	var inum = document.getElementById(iputid).value;
	inum = parseInt(inum);
	if(packagenum==0 || inum%packagenum == 0 )
	{
		$.post("do_order.php?m=change_input_number&color="+hcolor+"&spec="+sspec+"", $("#MainFormNumber").serialize(),
			function(data){
				if(data.backtype == "ok")
				{					
					document.getElementById("inputn_"+sspec+"_hj").value = data.hjvalue;
					document.getElementById("inputn_"+hcolor+"_sj").value = data.sjvalue;
					document.getElementById("inputn_total").value = data.totalvalue;
				}
			},"json");
	}else{
		alert('订购数量必需为 “'+packagenum+'”的整倍数！');	
		document.getElementById(iputid).value = packagenum;
	}
}

function saveonetocart()
{
		$.growlUI('正在操作，请稍候...');
		$.post("do_order.php",
			{m:"addtoorderone", oid: $('#toorderid').val(), pid: $('#togoodsid').val(), pnum:$('#shareit-field').val(), pcolor: '', pspec: '' },
			function(data){
				if(data.backtype=='ok'){
					$.growlUI('该商品预订成功！您目前共订购 '+data.cartnum+' 种商品。');						
					//carths(data.cartnum);						
				}else{
					$.growlUI(data.cartnum);
				}
			},"json");

		hideshow('shareit-box');
		window.setTimeout("hideshow('tip')",20000);
}

function updatecartnumber()
{
	$.growlUI('添加成功！');
	closewindowui();
}

function do_change_cart_number(pid,packagenum,oid)
{
	var allnumber = document.getElementById("inputall_cart_number").value;
	allnumber = parseInt(allnumber);
	if(packagenum==0 || allnumber%packagenum == 0 )
	{
		window.location.href = 'tocart.php?oid='+oid+'&ID='+pid+'&allnumber='+$('#inputall_cart_number').val()+'';
	}else{
		alert('订购数量必需为 “'+packagenum+'”的整倍数！');		
	}
}

function show_content(sid,lid,cid,oid)
{
	$("#jf_menu_id li a").each(function(e){
		$(this).removeClass();
	});
	$("#"+cid).addClass('jf_menu_hover');
	if(lid == "product_list"){
		$("#"+sid).html($("#old_show_list").html());
	}else{
		$("#"+sid).load('load_'+lid+'.php?oid='+oid);
	}

}

function delete_guestbook(pid)
{
	if(confirm('确认要删除此留言吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php",
			{m:"guestbook_delete", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					var delline = "linesub_" + pid;
					$("#"+delline).hide();
					$.blockUI({ message: "<p>删除成功!</p>" }); 
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}else{
		return false;
	}	
	window.setTimeout($.unblockUI, 1000); 
}

function set_invoice(iid)
{
	if(confirm('确认已开票了吗? 此操作不可逆'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_order.php",
			{m:"set_invoice", ID: iid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>操作成功!</p>" });
					$("#show_invoice_div").html('已开票');
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}else{
		return false;
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	window.setTimeout($.unblockUI, 2000); 
}