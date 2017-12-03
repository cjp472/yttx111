var mycookie = document.cookie;
function readcookie(name) 
{ 
	var start1 = mycookie.indexOf(name + "="); 
	if (start1== -1){ 
		return '';
	}
	else 
	{ 
		start=mycookie.indexOf("=",start1)+1;  
		var end = mycookie.indexOf(";",start); 
		if (end==-1) 
		{ 
			end=mycookie.length;
		} 
		var value=unescape(mycookie.substring(start,end)); 
		if (value==null) 
		{
			return '';
		} 
		else 
		{
			return value;
		} 
	} 
}

//去空隔函数 
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

function closewindowui()
{
	$.unblockUI();
	//window.setTimeout($.unblockUI, 1000);
}

/*********** 入库单 *****************/
function select_product()
{
	$("#windowtitle").html('请选择入库商品');
	$('#windowContent').html('<iframe src="select_storage_product.php?kw='+$('#inputsp').val()+'&selectid='+$('#selectid_storage').val()+'" width="100%" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'480px',top:'8%'
            }			
		});
	$('#inputsp').val('');
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}


function add_select_product()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	
	$.post("do_inventory.php?m=add_to_select_storage", $("#MainForm").serialize(),
		function(data){
			if(data.backtype == "ok")
			{					
				parent.set_select_product(data.htmldata,data.selectiddata);
			}else{
				parent.closewindowui();
			}
		},"json");
	closewindowui();
}

function set_select_product(htmldata,selectiddata)
{
	//alert(htmldata);
	$('#come_add_sel_pro').append(htmldata);
	$('#selectid_storage').val(selectiddata);
	closewindowui();
}

function del_sp_line(idd)
{
	$.post("do_inventory.php",
		{m:"remove_line", ID: idd, selectid: $('#selectid_storage').val()},
			function(data){
			data = Jtrim(data);
			if(data == "")
			{
				$('#selectid_storage').val('');
			}else{
				$('#selectid_storage').val(data);
			}
		});
	$("#del_"+idd).remove(); 
}


function add_storage_save()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	if($("#StorageAttn").val()=="")
	{
		$.blockUI({ message: "<p>请输入经办人!</p>" });
	}else{	
		$.post("do_inventory.php?m=add_storage_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
			if(data == "ok")
			{					
				$.blockUI({ message: "<p>保存成功!</p>" });
				window.location.href = "storage_add.php";
				$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
			}else{
				$.blockUI({ message: "<p>"+data+"</p>" });
				$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
			}
			});
		}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}




/*** input number ***********************/
function inputnumber(pid)
{
	$("#windowtitle").html('请输入商品数量');
	$('#windowContent').html('<iframe src="input_product_number.php?pid='+pid+'&amsg='+$('#inputn_arr_'+pid).val()+'" width="100%" marginwidth="0" height="350" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'400px',top:'8%'
            }			
		});
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function add_input_number()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	
	$.post("do_inventory.php?m=add_input_number_save", $("#MainForm").serialize(),
		function(data){
			if(data.backtype == "ok")
			{					
				parent.set_input_number(data.iddata,data.tnumber,data.pid);
			}else{
				parent.closewindowui();
			}

		},"json");
	closewindowui();
}

function set_input_number(iddata,tnumber,pid)
{
	$('#inputn_'+pid).val(tnumber);
	$('#inputn_arr_'+pid).val(iddata);
	closewindowui();
}

function changeupnumber(sspec,hcolor)
{
	$.post("do_inventory.php?m=change_input_number&color="+hcolor+"&spec="+sspec+"", $("#MainForm").serialize(),
		function(data){
			if(data.backtype == "ok")
			{					
				$("#inputn_"+sspec+"_hj").val(data.hjvalue);
				$("#inputn_"+hcolor+"_sj").val(data.sjvalue);
				$("#inputn_total").val(data.totalvalue);				
			}
		},"json");
}

/**************************/
function showproductnumber(pid,sid)
{
	$("#windowtitle").html('入库商品数量');

	$.post("storage_content_number.php",
		{pid: pid, sid: sid},
			function(data){
				$('#windowContent').html(data);
		});	
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'380px',top:'8%',left:"20%"
            }			
		});
	//alert($('#windowContent').html());
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

/**************************/
function show_inventory(pid,stype)
{
	if(stype=='order')
	{
		$("#windowtitle").html('商品-可用库存数量');
	}else{
		$("#windowtitle").html('商品-实际库存数量');
	}
	$('#windowContent').html('<iframe src="inventory_content_number.php?pid='+pid+'&stype='+stype+'" width="100%" marginwidth="0" height="330" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'380px',top:'8%',left:"20%"
            }			
		});
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}


/**************************/
function change_library_number(pid)
{
	$("#windowtitle").html('调整-订单库存数量');

	$('#windowContent').html('<iframe src="library_product_number.php?pid='+pid+'" width="100%" marginwidth="0" height="340" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'380px',top:'8%',left:"20%"
            }			
		});
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function change_up_library_number(sspec,hcolor)
{
	$.post("do_inventory.php?m=change_input_library_number&color="+hcolor+"&spec="+sspec+"", $("#MainForm").serialize(),
		function(data){
			if(data.backtype == "ok")
			{					
				$("#inputn_"+sspec+"_hj").val(data.hjvalue);
				$("#inputn_"+hcolor+"_sj").val(data.sjvalue);
				$("#inputn_total").val(data.totalvalue);				
			}
		},"json");
}

function save_library_input_number()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });

	$.post("do_inventory.php?m=change_library_number_save", $("#MainForm").serialize(),
		function(data){
			if(data.backtype == "ok")
			{					
				$.blockUI({ message: "<p>调整成功！</p>" });
				parent.set_library_number(data.tnumber,data.tordernumber,data.pid);
			}else{
				alert("没有变动(注：只能减少库存!)");
			}

		},"json");
	closewindowui();
}

function set_library_number(tnumber,tordernumber,pid)
{
	$('#change_number_'+pid).val(tnumber);
	$('#order_number_'+pid).html(tordernumber);

	closewindowui();
}

function save_library_input_number_one()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	
	$.post("do_inventory.php?m=change_library_number_save_one", $("#MainForm").serialize(),
		function(data){
			if(data.backtype == "ok")
			{					
				$.blockUI({ message: "<p>调整成功！</p>" });
				parent.set_library_number(data.tnumber,data.tordernumber,data.pid);
			}else{
				alert("没有变动(注：只能减少库存!)");
			}

		},"json");
	closewindowui();
}

function out_inventory_excel()
{
	document.MainForm.action = 'inventory_excel.php';
    document.MainForm.submit();
}

function out_inventory_list_excel()
{
	document.MainForm.action = 'inventory_list_excel.php';
    document.MainForm.submit();
}

/**************************/
function out_all_inventory_excel()
{
	$("#windowtitle").html('导出全部库存数据');

	$('#windowContent').html('<iframe src="inventory_out_number.php" width="100%" marginwidth="0" height="350" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'380px',top:'8%',left:"20%"
            }			
		});
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

/*******************************/
function implode_storage_save()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
}

function change_lib_num(idd,oldnumber)
{
	var newnumber = $("#"+idd).val();
	if(newnumber != oldnumber)
	{
		$.growlUI('正在执行，请稍候...');
		$.post("do_inventory.php",
			{m:"change_lib_num_save", ID: idd, changenumber: newnumber},
				function(data){
				if(data.backtype == "ok")
				{
					if(data.htmldataid != '')
					{
						$("#"+data.htmldataid).html(data.htmldata);
					}
					$.growlUI('修改成功!');
				}else{
					$.growlUI(data.backtype);
				}
			},"json");
	}
}

function save_library_input_mul_number()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	$('#savbutton').attr("disabled","disabled");
	$('#savbutton2').attr("disabled","disabled");
	$.post("do_inventory.php?m=change_library_mul_number_save", $("#MainForm").serialize(),
		function(data){
			data = Jtrim(data);
			$.blockUI({ message: data });
			if(data == "ok")
			{					
				$.blockUI({ message: "<p>调整成功！</p>" });				
				window.location.reload();
			}else{
				alert("没有变动");
				$('#savbutton').removeAttr('disabled');
				$('#savbutton2').removeAttr('disabled');
			}

		});
	//closewindowui();
}