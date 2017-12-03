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

/*********/

var old_bg="";
function inStyle(obj)
{
    old_bg=obj.style.background;
	obj.style.background="#edf3f9";
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 

function outStyle(obj)
{
    obj.style.background=old_bg;
}

function CheckAll(form)
{
	  for (var i=0;i<form.elements.length;i++)
	  {
			var e = form.elements[i];
			if (e.name != 'chkall' && e.name !='copy')       e.checked = form.chkall.checked; 
	   }
}

function selectorderline(foid)
{
	$("#data_ConsignmentOrder_"+foid).attr("checked",true);
	$("#showuserorder").children().find("tr").each(function(){
		$(this).css("background-color","#ffffff");
	});
	$("#selected_line_"+foid).css("background-color","#efefef");
	setinceptvaue(foid);
	setcartlist(foid);
}

function setcartlist(oid)
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
	$.post("do_consignment.php",
		{m:"loadcartlist", ID: oid},
		function(data){
			$("#listcartdataid").html(data);
		}		
	);
	$.blockUI({ message: "<p>数据载入成功...</p>" });
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
}

function do_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php",
			{m:"delete", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					var dellineB = "lineB_" + pid;
					$("#"+dellineB).hide();
					$.blockUI({ message: "<p>删除成功!</p>" }); 
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
	}else{
		return false;
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	window.setTimeout($.unblockUI, 2000); 
}


 function going(o)
 {
	document.MainForm.referer.value = document.location; 

	switch(o) {					
		case 'del':
				if(confirm("确认批量删除吗?")) {
				document.MainForm.action = 'do_consignment.php?m=delarr';
				document.MainForm.submit();
			}
			break;			
		}
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}


function closewindowui()
{
	$.unblockUI();
}

function setSendFlag(cid)
{
	if(confirm('确定货物已签收吗?'))
	{		
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php",
			{m:"setSendFlag", ConsignmentID: cid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					var delline = "setflagline_" + cid;
					var dellinel = "setflagline_link_" + cid;
					$("#"+delline).html('已签收');
					$("#"+dellinel).html('<a href="consignment_content.php?ID='+cid+'"  target="_blank" >查看发货单</a>');
					$.blockUI({ message: "<p>设置成功!</p>" }); 
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}					
			}		
		);
		window.setTimeout($.unblockUI, 1000);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 1000);
}

function setinceptvaue(oid)
{
	if(oid != "")
	{
		$.post("do_consignment.php",
			{m:"getinceptvaue", OrderID: oid},
			function(data){

				if(data.backtype == "ok")
				{
					$('#data_InceptMan').val(data.InceptMan);
					$('#data_InceptAddress').val(data.InceptAddress);
					$('#data_InceptCompany').val(data.InceptCompany);
					$('#data_InceptPhone').val(data.InceptPhone);
					$('#data_ConsignmentClient').val(data.InceptUser);
					
					//$.blockUI({ message: "<p>收货信息设置成功...</p>" });
					//window.setTimeout($.unblockUI, 100);
				}else{
					//window.setTimeout($.unblockUI, 100);
				}
			},"json");		
	}	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	//window.setTimeout($.unblockUI, 2000);
}


function get_orderlist(oid)
{
	$("#showuserorder").html("<p>正在载入数据，请稍后...</p>" ); 
	$('#data_ConsignmentClient').val(oid);
	$.post("do_consignment.php",
		{m:"loadorderlist", ID: oid},
		function(data){
			$("#showuserorder").html(data);
		}		
	);
	get_logisticslist(oid);
	set_con_blank();
}

function set_con_blank()
{
	$('#data_InceptMan').val('');
	$('#data_InceptAddress').val('');
	$('#data_InceptCompany').val('');
	$('#data_InceptPhone').val('');

	$('#listcartdataid').html('');
}

function get_logisticslist(oid)
{
		$.post("do_consignment.php",
			{m:"logisticslist", ID: oid},
			function(data){
				data = Jtrim(data);
				if(data=="error")
				{
					$("#show_logistics_nomal").html('');
					$("#logistics_all").show();
					$("#logistics_nomal").hide();
				}else{
					$("#show_logistics_nomal").html(data);
					$("#logistics_all").hide();
					$("#logistics_nomal").show();
				}
			}		
		);
}

function show_logisticslistall()
{
	$("#logistics_all").show();
	$("#logistics_nomal").hide();
}


function do_save_consignment()
{
	document.MainForm.referer.value = document.location;
	var backlink = readcookie("backurl");
	if(backlink == "") backlink = "consignment.php";
	
	document.MainForm.referer.value = document.location;

	if($('#data_ConsignmentOrder').val()=="")
	{
		$.blockUI({ message: "<p>请先选择发货订单！</p>" });

	}else if(($('#data_ConsignmentLogistics').val()=="")&&($('#ConsignmentLogistics_nomal').val()=="")){
		$.blockUI({ message: "<p>请先选择发货物流公司！</p>" });
	
	}else if($('#data_ConsignmentDate').val()==""){
		$.blockUI({ message: "<p>请输入发货时间！</p>" });

	}else if($('#data_InceptAddress').val()==""){
		$.blockUI({ message: "<p>请输入收货人地址！</p>" });

	}else if($('#data_InceptPhone').val()==""){
		$.blockUI({ message: "<p>请输入联系电话！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php?m=content_add_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data=="ok"){
					$.blockUI({ message: "<p>保存成功!  <a href='consignment_add.php'>-点击继续添加发货单</a></p>" });
					//window.location.href = backlink;
					window.setTimeout(jumpurl(backlink), 15000);
				}else if(data=="iswwf"){
					$.blockUI({ message: "<p>保存成功，库存数量不够，货未发全!  <a href='"+backlink+"'>-点击返回</a></p>" });
					alert('保存成功，库存数量不够，货未发全! ');
					window.setTimeout(jumpurl(backlink), 50000);
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


function do_save_edit_consignment()
{
	document.MainForm.referer.value = document.location;
	var backlink = readcookie("backurl");
	if(backlink == "") backlink = "consignment.php";

	if($('#data_ConsignmentOrder').val()=="")
	{
		$.blockUI({ message: "<p>请先选择发货订单！</p>" });

	}else if($('#data_ConsignmentLogistics').val()==""){
		$.blockUI({ message: "<p>请先选择发货物流公司！</p>" });

	}else if($('#data_ConsignmentDate').val()==""){
		$.blockUI({ message: "<p>请输入发货时间！</p>" });

	}else if($('#data_InceptAddress').val()==""){
		$.blockUI({ message: "<p>请输入收货人地址！</p>" });

	}else if($('#data_InceptPhone').val()==""){
		$.blockUI({ message: "<p>请输入联系电话！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php?m=content_edit_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = backlink;

					$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
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



/****** Logistics  **************************************************/
function do_save_logistics()
{
	document.MainForm.referer.value = document.location;

	if($('#data_LogisticsName').val()=="")
	{
		$.blockUI({ message: "<p>请输入物流/快递公司！</p>" });
		$("#data_LogisticsName")[0].focus();
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php?m=logistics_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功,点击继续增加!</p>" });
					$('#data_LogisticsPinyi').val('');
					$('#data_LogisticsName').val('');
					$('#data_LogisticsContact').val('');
					$('#data_LogisticsPhone').val('');
					$('#data_LogisticsFax').val('');
					$('#data_LogisticsMobile').val('');
					$('#data_LogisticsAddress').val('');
					$('#data_LogisticsUrl').val('');
					$('#data_LogisticsAbout').val('');
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
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


///***********//
function do_save_edit_logistics()
{
	document.MainForm.referer.value = document.location;

	if($('#data_LogisticsName').val()=="")
	{
		$.blockUI({ message: "<p>请输入物流/快递公司！</p>" });
		$("#data_LogisticsName")[0].focus();

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php?m=logistics_edit_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = "logistics.php";

					$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
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


function do_logistics_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php",
			{m:"delete_logistics", ID: pid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					$.blockUI({ message: "<p>删除成功!</p>" }); 
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

function out_consignment_excel()
{
	document.MainForm.action = 'consignment_excel.php';
    document.MainForm.submit();
}

function out_consignment_send_excel(id,ty)
{
	if(id !== '' && id != '0')
		document.MainForm.action = 'consignment_send_excel_'+id+'.php?stype='+ty;
	else
		document.MainForm.action = 'consignment_send_excel.php?stype='+ty;
	
//	document.MainForm.action = 'consignment_send_excel.php?stype='+ty;
    document.MainForm.submit();
}

/******************************/
function remove_library_line(lid)
{
	var delline = "linegoods_" + lid;
	$("#"+delline).remove();
}

function checknumber(inputid,packagenum)
{
	var inum  = $("#"+inputid).val();
	inumu = parseInt(inum);
	if(inumu > packagenum )
	{
		alert('发货数量不能大于订购数量和库存数量！');	
		$("#"+inputid).val(packagenum);
	}
}

function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}

function setCompanyCode(kuaidiname)
{
	 var sarr = kuaidiname.split("-");
	 $('#data_LogisticsName').val(sarr[1]);
}

function show_consignment_product_data()
{
	document.MainForm.action = 'consignment_product.php';
	document.MainForm.target = '_self';
	document.MainForm.submit();
}

function output_consignment_product_excel()
{	
	document.MainForm.action = 'consignment_product_excel.php';
	document.MainForm.target = 'exe_iframe';
	document.MainForm.submit();
}

//补发短信
function send_message()
{
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'250px',top:'18%'
            }			
		});
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function sendtomessage()
{
		$('#sendmessagebtn').attr("disabled","disabled");
		$.post("do_consignment.php?m=send_to_message", $("#sendmessageform").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					alert('发送成功');
				}else{
					alert(data);
					$('#sendmessagebtn').removeAttr('disabled');
				}				
			}		
		);
}