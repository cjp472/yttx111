/*********/
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

function do_order_status(action,oid)
{
	if(confirm('确认进行此操作吗!'))
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
    var num = $("#num_" + pid).val();
    num = isNaN(parseInt(num)) ? 1 : parseInt(num);
		
	$.post("do_order.php",
		{m:"addtocart", pid: pid, oid: oid, cid: cid, pcolor: selectcolor, pspec: selectspec ,num:num},
		function(data){
			data = Jtrim(data);
			if(data=="ok"){
				$.growlUI('已成功将该商品添加到订单中！<br />点击 <a href="order_product_edit.php?ID='+oid+'">下一步，订购商品管理</a>');				
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
    var num = $("#num_" + pid).val();
    num = isNaN(parseInt(num)) ? 1 : parseInt(num);
	$.post("do_order.php",
		{m:"addtocart", pid: pid, oid: oid, cid: cid, pcolor: selectcolor, pspec: selectspec,num:num },
		function(data){
		data = Jtrim(data);
			if(data=="ok"){
				$.growlUI('已成功将该商品添加到订单中！<br />点击 <a href="new_order_product_edit.php?ID='+oid+'">下一步，订购商品管理</a>');				
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
				if(data.length > 11){
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

function out_order_excel()
{
	document.MainForm.action = 'order_excel.php';
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

function out_return_excel()
{
	document.MainForm.action = 'return_excel.php';
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