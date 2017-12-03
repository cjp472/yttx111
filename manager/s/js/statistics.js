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

function CheckAll(form)
{
	for(var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if (e.name != 'chkall' && e.name !='copy') e.checked = form.chkall.checked; 
	}
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

function output_excel(taction)
{
	if(taction!="")
	{		
		document.MainForm.action = 'statistics_excel.php?action='+taction;
		document.MainForm.target = 'exe_iframe';
		document.MainForm.submit();
	}
}

function output_product_excel(taction)
{
	if(taction!="")
	{		
		document.MainForm.action = 'statistics_product_excel.php?action='+taction;
		document.MainForm.target = 'exe_iframe';
		document.MainForm.submit();
	}
}

function output_reconciliation_excel()
{
	document.searchform.action = 'reconciliation_excel.php';
	document.searchform.target = 'exe_iframe';
	document.searchform.submit();
}

function output_deduct_excel()
{
	document.MainForm.action = 'statistics_deduct_excel.php';
	document.MainForm.target = 'exe_iframe';
	document.MainForm.submit();
}

function output_reconciliation()
{
	document.searchform.action = 'reconciliation.php';
	document.searchform.target = '_self';
	document.searchform.submit();
}

function show_stat_data(taction)
{

	if(taction=="day")
	{
		document.MainForm.action = 'statistics_d.php';
	}
	else if(taction=="month")
	{
		document.MainForm.action = 'statistics_m.php';
	}
	else if(taction=="year")
	{
		document.MainForm.action = 'statistics_y.php';
	}else{
		document.MainForm.action = 'statistics.php';
	}
	document.MainForm.target = '_self';
	document.MainForm.submit();
}

function show_stat_return_data()
{
	document.MainForm.target = '_self';
	document.MainForm.submit();
}

function show_order_list(showtype,did,cid)
{
	$('#windowContentList').html('数据载入中... ');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'350px',top:'10%'
            }			
		});

		$.post("do_stat.php",
			{m:"showorderlist", stype: showtype, did: did, cid: cid},
			function(data){
				$('#windowContentList').html(data);				
			}		
		);

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function show_return_list(showtype,did,cid)
{
	$('#windowContentList').html('数据载入中... ');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'350px',top:'10%'
            }			
		});

		$.post("do_stat.php",
			{m:"showreturnlist", stype: showtype, did: did, cid: cid},
			function(data){
				$('#windowContentList').html(data);				
			}		
		);

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function show_product_order_list(showtype,did,bd,ed)
{
	$('#windowContentList').html('数据载入中... ');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'350px',top:'10%'
            }			
		});

		$.post("do_stat.php",
			{m:"showorderlist", stype: showtype, did: did, begindate: bd,enddate:ed},
			function(data){
				$('#windowContentList').html(data);				
			}		
		);
	
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function closewindowui()
{
	$.unblockUI();
}

function show_product_stat_data(taction)
{

	if(taction=="day")
	{
		document.MainForm.action = 'statistics_d.php';
	}
	else if(taction=="month")
	{
		document.MainForm.action = 'statistics_m.php';
	}
	else if(taction=="year")
	{
		document.MainForm.action = 'statistics_y.php';
	}else{
		document.MainForm.action = 'product_statistics.php';
	}
	document.MainForm.target = '_self';
	document.MainForm.submit();
}

//提成明细
function show_deduct_list(did,bd,ed)
{
	$('#windowContent').html('<iframe src="show_deduct_list.php?sid='+did+'&begindate='+bd+'&enddate='+ed+'" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('业务员提成明细');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'445px',top:'2%'
            }			
		});
	$('#windowForm6').css("width","620px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}