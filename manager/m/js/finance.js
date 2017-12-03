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

var selectordertotal = 0;
function selectyufuclick()
{
	if($("#FinanceYufu").attr('checked')=="checked")
	{
		//$("#FinanceYufu").attr("checked","checked");
		$("#finaceorderselectid").hide();
	}else{
		//$("#FinanceYufu").attr("checked","");
		$("#finaceorderselectid").show();
	}
}

function changeTotal(){
	selectordertotal = 0;
	$("#showuserorder tr").each(function(index, element) {
        if(index>0){
			var price = $(element).find("input[type=hidden]").val();
			var box = $(element).find("input[type=checkbox]").attr("checked");
			if(box == "checked" && price != ""){
				price = parseFloat(price);
				selectordertotal += price;
			}
		}
    });
	selectordertotal = Math.round(selectordertotal*100)/100;
	$("#show_order_total").html(selectordertotal);
}

function selectorderline(foid)
{
	if($("#data_FinanceOrder_"+foid).attr('checked')=="checked")
	{
		$("#data_FinanceOrder_"+foid).attr("checked",false);
		$("#selected_line_"+foid).css("background-color","#ffffff");
	}else{
		$("#data_FinanceOrder_"+foid).attr("checked",true);
		$("#selected_line_"+foid).css("background-color","#efefef");
	}
	changeTotal();
}

function selectorderlinefocus(foid)
{
	if($("#data_FinanceOrder_"+foid).attr('checked')=="checked")
	{
		$("#selected_line_"+foid).css("background-color","#efefef");
	}else{
		$("#selected_line_"+foid).css("background-color","#ffffff");
	}
	changeTotal();
}


 function going(o)
 {
	document.MainForm.referer.value = document.location;
		switch(o){
			case 'del':
				    if(confirm("确认批量删除吗?")) {
					document.MainForm.action = 'do_finance.php?m=delarr';
                    document.MainForm.submit();
				}
				break;				
			}
			$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function closewindowui()
{
	$.unblockUI();
}

/****** Finance  **************************************************/
function do_save_finance()
{
	document.MainForm.referer.value = document.location;
	var ftotal = parseFloat($('#data_FinanceTotal').val());
	var ytotal = parseFloat($('#ytotal').val());
	if($('#data_FinanceClient').val()=="" || $("#data_FinanceClient").val() == '0')
	{
		$.blockUI({ message: "<p>请输入转款客户！</p>" });
		$("#data_FinanceClient")[0].focus();

	}else if($('#data_FinanceToDate').val()==""){
		$.blockUI({ message: "<p>请选择转款日期！</p>" });
		$("#data_FinanceToDate")[0].focus();

	}else if($('#finance_type').val()!="Y" &&  ($('#data_FinanceAccounts').val()=="" || $("#data_FinanceAccounts").val() == '0')){
		$.blockUI({ message: "<p>请选择收款帐户！</p>" });
		$("#data_FinanceAccounts")[0].focus();

	}else if($('#data_FinanceTotal').val()=="" || $("#data_FinanceTotal").val() == '0'){
		$.blockUI({ message: "<p>请输入转款金额!</p>" });
		$("#data_FinanceTotal")[0].focus();

	}else if($('#finance_type').val()=="Y" && ftotal > ytotal){
		$.blockUI({ message: "<p>付款金额不能大于可支付余额!</p>" });
		$("#data_FinanceTotal")[0].focus();

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_finance.php?m=finance_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					//alert('保存成功!');
					window.location.href='finance.php';
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}


///***********//
function do_save_edit_finance()
{
	document.MainForm.referer.value = document.location;
	var backlink = readcookie("backurl");
	if(backlink == "") backlink = "finance.php";

	if($('#data_FinanceClient').val()=="")
	{
		$.blockUI({ message: "<p>请输入转款客户！</p>" });
		$("#data_FinanceClient")[0].focus();

	}else if($('#data_FinanceToDate').val()==""){
		$.blockUI({ message: "<p>请选择转款日期！</p>" });
		$("#data_FinanceToDate")[0].focus();

	}else if($('#data_FinanceAccounts').val()=="" || $("#data_FinanceAccounts").val() == '0'){
		$.blockUI({ message: "<p>请选择收款帐户！</p>" });
		$("#data_FinanceAccounts")[0].focus();

	}else if($('#data_FinanceTotal').val()=="" || $("#data_FinanceTotal").val() == '0'){
		$.blockUI({ message: "<p>请输入转款金额!</p>" });
		$("#data_FinanceTotal")[0].focus();

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_finance.php?m=finance_edit_save", $("#MainForm").serialize(),
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
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}


function do_delete_finance(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_finance.php",
			{m:"delete_finance", ID: pid},
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
		$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
	}else{
		return false;
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
	window.setTimeout($.unblockUI, 2000); 
}

function do_validate(fid)
{
	if(confirm('确认已到帐了吗? 此操作不可逆!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_finance.php",
			{m:"validate_finance", ID: fid},
			function(data){
			data = Jtrim(data);
				if(data == "error"){
					$.blockUI({ message: "<p>设置不成功!</p>" });
				}else if(data == "nopope"){
					$.blockUI({ message: "<p>对不起您没有此项权限!</p>" });
				}else{
					var delline2 = "line_set_" + fid;
					$("#"+delline2).html(data);
					var delline3 = "line_set_del_" + fid;
					$("#"+delline3).html('<font color="gray">修改</font>&nbsp;|&nbsp;<font color="gray">删除</font>');
					$.blockUI({ message: "<p>设置成功！</p>" }); 
				}					
			}		
		);
		$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
	}else{
		return false;
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
	window.setTimeout($.unblockUI, 3000); 
}

function get_orderlist(oid)
{
	$("#showuserorder").html("<p>正在载入数据，请稍后...</p>" ); 
	$.post("do_finance.php",
		{m:"loadorderlist", ID: oid},
		function(data){
			$("#showuserorder").html(data);
		}		
	);
	selectordertotal = 0;
	$("#show_order_total").html(selectordertotal);
}

function out_finance_excel()
{
	document.MainForm.action = 'finance_excel.php';
    document.MainForm.submit();
}

/********费用单**************************/
function do_save_expense()
{
	document.MainForm.referer.value = document.location;

	if($('#data_ClientID').val()=="")
	{
		$.blockUI({ message: "<p>请选择客户！</p>" });
		$("#data_ClientID")[0].focus();

	}else if($('#data_BillID').val()==""){
		$.blockUI({ message: "<p>请选择费用类型！</p>" });
		$("#data_BillID")[0].focus();

	}else if($('#data_ExpenseDate').val()==""){
		$.blockUI({ message: "<p>请选择日期！</p>" });
		$("#data_ExpenseDate")[0].focus();

	}else if($('#data_ExpenseTotal').val()==""){
		$.blockUI({ message: "<p>请输入金额!</p>" });
		$("#data_ExpenseTotal")[0].focus();

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_finance.php?m=expense_add_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					//alert('保存成功!');
					window.location.href='expense.php';
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

function do_validate_expense(eid)
{
	if(confirm('确认审核该费用吗? 此操作不可逆!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_finance.php",
			{m:"validate_expense", ID: eid},
			function(data){
			data = Jtrim(data);
				if(data == "error"){
					$.blockUI({ message: "<p>审核不成功!</p>" });
				}else if(data == "nopope"){
					$.blockUI({ message: "<p>对不起您没有此项权限!</p>" });
				}else{
					var delline2 = "line_set_" + eid;
					$("#"+delline2).html('<font color=green>√</font>');
					var delline3 = "line_set_del_" + eid;
					$("#"+delline3).html('<font color="gray">删除</font>');
					$.blockUI({ message: "<p>审核成功！</p>" }); 
				}					
			}		
		);
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}else{
		return false;
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	window.setTimeout($.unblockUI, 3000); 
}

function do_delete_expense(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_finance.php",
			{m:"delete_expense", ID: pid},
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

function out_expense_excel()
{
	document.MainForm.action = 'expense_excel.php';
    document.MainForm.submit();
}

function out_finace_client_excel()
{
	document.MainForm.action = 'finance_client_excel.php';
    document.MainForm.submit();
}


/********************* 易极付 ************************************/
//获取对账记录
window.pageSize = 10;
function getPayWithdraw(index, initPage){
	var sdate	= $("input[name='sdate']").val(),
		edate	= $("input[name='edate']").val(),
		acType	= $("select[name='accountType']").val();
		
	var currPage = index || 0;
	
	if(acType == ''){
		alert('请选择账户类型');
		return false;
	}
	
	$.blockUI({message: '<div class="clear draw-load" id="draw-load">数据载入中，请稍等...<br /><img src="./img/loading3.gif" /></div>' }); 
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 

		$.post(
				"do_finance.php",
				{
					m: "getSearInfo",
					sdate: sdate,
					edate: edate,
					acType: acType,
					pageSize: window.pageSize,
					currPage: currPage
				},
				function(msg){
					
					window.setTimeout($.unblockUI, 500); 

					//设置头信息
					$("#total-count").html(msg.count);				//总记录数
					$("#total-amounts").html(msg.amounts);			//本金总金额
					$("#total-amountsIn").html(msg.amountsIn);		//总金额
					$("#total-charges").html(msg.charges);			//总手续费
					
					if(msg == null) return false;
					
					//设置消息体(这块看起来有点眼花，要优化下)
					var sbody	= '';
					var index 	= 0;
					for(var i in msg.withdrawInfos){
						index++;
						
						sbody += '<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline">';
						sbody += '<td>'+index+'</td>';
						sbody += '<td id="log-status"><b>'+msg.withdrawInfos[i]['status']+'</b></td>';
						sbody += '<td id="log-payTime">'+msg.withdrawInfos[i]['payTime']+'</td>';
						sbody += '<td id="log-accountName">'+msg.withdrawInfos[i]['accountName']+'</td>';
						sbody += '<td id="log-bankName">'+msg.withdrawInfos[i]['bankName']+'</td>';
						sbody += '<td id="log-bankAccountNo">'+msg.withdrawInfos[i]['bankAccountNo']+'</td>';
						sbody += '<td id="log-amountIn">¥ '+msg.withdrawInfos[i]['amountIn']+'</td>';
						sbody += '<td id="log-amout">¥ '+msg.withdrawInfos[i]['amout']+'</td>';
						sbody += '<td id="log-charge">¥ '+msg.withdrawInfos[i]['charge']+'</td>';
						sbody += '<td id="log-payNo">'+msg.withdrawInfos[i]['payNo']+'</td>';
						sbody += '</tr>';
					}
					
					if(sbody == ''){
						sbody = '<tr><td align="center" colspan="10"><b>抱歉，该时间段内未查询到提现记录</b></td></tr>';
					}
					
					$("#log-draw").html(sbody);
					!initPage && initPagination(msg.count);
					
				},
				'json'
			);
}

// 创建分页
function initPagination(num_entries) {
	
	var $yopenapiPage = $("#yopenapi-page");
	
	if(!num_entries) {
		$yopenapiPage.html('');
		return false;
	}

	$yopenapiPage.pagination(num_entries, {
		num_edge_entries: 1, //边缘页数
		num_display_entries: 5, //主体页数
		items_per_page: window.pageSize, //每页显示1项
		prev_text: "前一页",
		next_text: "后一页",
		callback:createDrawHtml
	});
	
 };

//生成提现html
function createDrawHtml(pageIndex, jq){
	
	getPayWithdraw(pageIndex, true);
//	pageIndex && getPayWithdraw(pageIndex, true);
	return false;
}

//后台检测
function backendAction(){
	//检查支付是否完成
	var url = "do_backend.php?m=execute&jp=backend_check&"+Math.random();
	setInterval(function(){$.getJSONP(url, backend_check)}, 1500);
}

function backend_check(info){
	if(info){
		$.blockUI({message: '<p>恭喜，提现成功</p>' }); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
		window.setTimeout(function(){
			$.unblockUI;
			window.location.reload();
			}, 3000); 
	}
}