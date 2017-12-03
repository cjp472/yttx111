function do_companyorder_pay()
{
	var ct = $("#windowContent");
	var pid = ct.find("input[name='payID']").val();
	var account = ct.find("input[name='account']").val();
	if(account == "")
	{
		$.blockUI({ message: "<p>请输入转入账号！</p>" });
	}
	else if(confirm('确认到账吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_company.php",
			{m:"do_companyorder_pay", ID: pid, account: account},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
				}					
			}		
		);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 2000); 
}

function do_companyorder_status(pid)
{
	if(confirm('确认开通吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_company.php",
			{m:"do_companyorder_status", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_order.php');
				}					
			}		
		);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 2000); 
}

function do_companyinvoice_edit()
{
	var ct = $("#windowContent");
	var pid = ct.find("input[name='invoiceID']").val();
	var account = ct.find("input[name='invoice_no']").val();
	var to_time = ct.find("input[name='to_time']").val();
	if(account == "")
	{
		$.blockUI({ message: "<p>请输入开票票号！</p>" });
	}
	else if(to_time == "")
	{
		$.blockUI({ message: "<p>请输入开票日期！</p>" });
	}
	else if(confirm('确认开票吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_company.php",
			{m:"do_companyinvoice_edit", ID: pid, account: account, to_time: to_time},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_invoice.php');
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='company_invoice.php');
				}					
			}		
		);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 2000); 
}

function showPay(pid,company,total)
{
	var ct = $("#windowContent");
	ct.find("span[data-company]").html(company);
	ct.find("span[data-total]").html(total);
	ct.find("input[name='account']").val('');
	ct.find("input[name='payID']").val(pid);
    $.blockUI({ message : $("#windowForm")});
}

function showInvoice(pid,company,total)
{
	var ct = $("#windowContent");
	ct.find("span[data-company]").html(company);
	ct.find("span[data-total]").html(total);
	ct.find("input[name='invoice_no']").val('');
	var myDate = new Date();
	var year = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
	var month = myDate.getMonth()+1;       //获取当前月份(0-11,0代表1月)
	var day = myDate.getDate();        //获取当前日(1-31)

	ct.find("input[name='to_time']").val(year+'-'+month+'-'+day);
	ct.find("input[name='invoiceID']").val(pid);
    $.blockUI({ message : $("#windowForm")});
}

function closewindowui()
{
	$.unblockUI();
}