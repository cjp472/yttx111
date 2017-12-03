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

function do_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"delete", ID: pid},
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
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 1000); 
}

function do_restore(pid)
{
	if(confirm('确认还原吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"restore", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "error"){
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}else if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					$.blockUI({ message: "<p>还原成功!</p>" }); 
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

function do_quite_delete(pid)
{
	if(confirm('确认彻底删除吗(此操作将删除该公司全部资料)?此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"quite_delete", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "error"){
					$.blockUI({ message: "<p>"+data+"</p>" }); 
				}else if(data == "ok"){
					var delline = "line_" + pid;
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
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
	window.setTimeout($.unblockUI, 1000); 
}

/**************************/
function do_save_company()
{
	document.MainForm.referer.value = document.location;

	if($('#data_CompanyArea').val()=="" || $('#data_CompanyArea').val()=="0")
	{
		$.blockUI({ message: "<p>请选择所属地区！</p>" });
	
	}
	else if($('#data_CompanyIndustry').val()=="" || $('#data_CompanyIndustry').val()=="0")
	{
		$.blockUI({ message: "<p>请选择所属行业！</p>" });
	
	}
	else if($('#data_BusinessLicense').val()=="")
	{
		$.blockUI({ message: "<p>请输入公司名称！</p>" });
	}
	else if($('#data_IdentificationNumber').val()=="")
	{
		$.blockUI({ message: "<p>请输入营业执照号码！</p>" });
	}
	else if($('#data_CompanyName').val()=="")
	{
		$.blockUI({ message: "<p>请输入系统名称！</p>" });
	}
	else if($('#data_CompanySigned').val()=="")
	{
		$.blockUI({ message: "<p>请输入平台简称！</p>" });
	}
	else if($('#data_CompanyContact').val()=="")
	{
		$.blockUI({ message: "<p>请输入联系人！</p>" });
	}
	else if($('#data_CompanyMobile').val()=="")
	{
		$.blockUI({ message: "<p>请输入手机号码！</p>" });
	
	}
	else if($('#CS_Number').val()=="")
	{
		$.blockUI({ message: "<p>请输入用户数！</p>" });
	
	}
	else if($('#CS_BeginDate').val()=="")
	{
		$.blockUI({ message: "<p>请选择开通时间！</p>" });
	}
	else if($('#CS_EndDate').val()=="")
	{
		$.blockUI({ message: "<p>请选择到期时间！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php?m=content_add_company_save",$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='manager.php');
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>客户已存在，请不要重复添加!</p>" });
					$("#data_CompanyName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "repeatprefix"){
					$.blockUI({ message: "<p>帐号前缀不能重复!</p>" });
					$("#data_CompanyPrefix")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
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




function do_edit_company()
{
	document.MainForm.referer.value = document.location;

	if($('#data_CompanyArea').val()=="" || $('#data_CompanyArea').val()=="0")
	{
		$.blockUI({ message: "<p>请选择所属地区！</p>" });
	
	}
	else if($('#data_CompanyIndustry').val()=="" || $('#data_CompanyIndustry').val()=="0")
	{
		$.blockUI({ message: "<p>请选择所属行业！</p>" });
	
	}
	else if($('#data_BusinessLicense').val()=="")
	{
		$.blockUI({ message: "<p>请输入公司名称！</p>" });
	}
	else if($('#data_IdentificationNumber').val()=="")
	{
		$.blockUI({ message: "<p>请输入营业执照号码！</p>" });
	}
	else if($('#data_CompanyName').val()=="")
	{
		$.blockUI({ message: "<p>请输入系统名称！</p>" });
	}
	else if($('#data_CompanySigned').val()=="")
	{
		$.blockUI({ message: "<p>请输入平台简称！</p>" });
	}
	else if($('#data_CompanyContact').val()=="")
	{
		$.blockUI({ message: "<p>请输入联系人！</p>" });
	}
	else if($('#data_CompanyMobile').val()=="")
	{
		$.blockUI({ message: "<p>请输入手机号码！</p>" });
	
	}
	else if($('#CS_Number').val()=="")
	{
		$.blockUI({ message: "<p>请输入用户数！</p>" });
	
	}
	else if($('#CS_BeginDate').val()=="")
	{
		$.blockUI({ message: "<p>请选择开通时间！</p>" });
	}
	else if($('#CS_EndDate').val()=="")
	{
		$.blockUI({ message: "<p>请选择到期时间！</p>" });

	}else{
            if($('#CreditCheck').is(':checked')){
                
            }else{
                
            }
		var backlink = readcookie("backurl");
		if(backlink == "") backlink = "manager.php";

		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php?m=content_edit_company_save",$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href=backlink;
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>客户已存在，请不要重复添加!</p>" });
					$("#data_CompanyName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "repeatPrefix"){
					$.blockUI({ message: "<p>账号前缀已存在，请不要重复添加!</p>" });
					$("#data_CompanyName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
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


function closewindowui()
{
	$.unblockUI();
}


function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}


/***************************/
function do_save_company_user()
{
	document.MainForm.referer.value = document.location;

	if($('#data_UserName').val()=="")
	{
		$.blockUI({ message: "<p>请输入登陆帐号！</p>" });

	}else if($('#data_UserPass').val()=="" && $('#UserID').val()==""){
		$.blockUI({ message: "<p>请输入登陆密码！</p>" });
	
	}else{
		var backlink = readcookie("backurl");
		if(backlink == "") backlink = "manager.php";

		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"company_user_save", CompanyID:$('#CompanyID').val(), UserID:$('#UserID').val(), UserName: $('#data_UserName').val(), UserPass: $('#data_UserPass').val(),UserTrueName: $('#data_UserTrueName').val(), UserPhone: $('#data_UserPhone').val(),UserRemark: $('#data_UserRemark').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					alert('保存成功!');
					window.location.href=backlink;
				}else if(data == "error"){
					$.blockUI({ message: "<p>参数错误!</p>" });
					$("#data_UserName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "okname"){
					$.blockUI({ message: "<p>请输入正确的用户名(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "okpass"){
					$.blockUI({ message: "<p>请输入正确的密码(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserPass")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>登陆帐号已存在，请换名再试!</p>" });
					$("#data_UserName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
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


/**************************/
function do_save_industry()
{
	document.MainForm.referer.value = document.location;

	if($('#data_IndustryName').val()=="")
	{
		$.blockUI({ message: "<p>请输入行业名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"content_add_industry_save", IndustryName: $('#data_IndustryName').val(), IndustryAbout: $('#data_IndustryAbout').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					//$('input').val('');
					$('#data_IndustryName').val('');
					$('#data_IndustryAbout').val('');
					$('.blockOverlay').attr('title','点击继续增加,查看新增的行业，请刷新本页!').click($.unblockUI);
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>行业称名已存在，请不要重复添加!</p>" });
					$("#data_IndustryName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
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


function do_save_edit_industry()
{
	document.MainForm.referer.value = document.location;

	if($('#edit_IndustryName').val()=="" )
	{
		$.blockUI({ message: "<p>请输入行业名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"content_edit_industry_save", IndustryID: $('#edit_IndustryID').val(), IndustryName: $('#edit_IndustryName').val(), IndustryAbout: $('#edit_IndustryAbout').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.reload();
					
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>行业称名已存在，请不要重复添加!</p>" });
					$("#data_IndustryName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
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


function set_edit_industry(ID,Name,About)
{
	if(ID != "")
	{
		$('#edit_IndustryID').val(ID);
		$('#edit_IndustryName').val(Name);
		$('#edit_IndustryAbout').val(About);
	}	 
}

/**
 * 审核列表中供应商
 * @param company_id
 * @param company_title
 */
function do_delete_company(company_id,company_title) {
    if(confirm('确认删除 ' + company_title + ' 公司?')) {
        $.post("do_company.php",{
            m:'del_company',
            company_id:company_id
        },function(data){
            data = Jtrim(data);
            if(data == 'ok') {
                $.blockUI({
                    message : '<p>删除成功!</p>'
                });
                setTimeout(function(){
                    location.reload();
                },500);
            } else {
                $.blockUI({
                    message : '<p>'+data+'</p>'
                });
            }
        },'text');
    }
}


function do_delete_industry()
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{

		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"delete_industry",ID:$('#edit_IndustryID').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>删除成功,正在载入页面...</p>" });
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
				}				
			}		
		);
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
	}
}


//////////////******** area **********////

function do_save_sort()
{

	if($('#data_AreaParent').val()=="")
	{
		$.blockUI({ message: "<p>请先选择上级地区！</p>" });

	}else if($('#data_AreaName').val()==""){
		$.blockUI({ message: "<p>请输入地区名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"save_sort", AreaParent: $('#data_AreaParent').val(), AreaName: $('#data_AreaName').val(), AreaAbout: $('#data_AreaAbout').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('#data_AreaName').val('');
					$('#data_AreaAbout').val('');
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}


function set_edit_sort(SiteID,ParentID,SiteName,Content)
{
	if(SiteID != "")
	{
		$('#edit_AreaID').val(SiteID);
		$('#edit_AreaParent').val(ParentID);
		$('#edit_AreaName').val(SiteName);
		$('#edit_AreaAbout').val(Content);
		$(".select2").select2();
	}	 
		 
}

function do_save_edit_sort()
{
	if($('#edit_AreaParent').val()=="")
	{
		$.blockUI({ message: "<p>请先选择上级地区！</p>" });

	}else if($('#edit_AreaName').val()==""){
		$.blockUI({ message: "<p>请输入地区名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"save_edit_sort",AreaID:$('#edit_AreaID').val(), AreaParent: $('#edit_AreaParent').val(), AreaName: $('#edit_AreaName').val(), AreaAbout: $('#edit_AreaAbout').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>修改成功,正在载入页面...</p>" });
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}

function do_delete_sort()
{
	if($('#edit_AreaID').val()=="")
	{
		$.blockUI({ message: "<p>请先选择您要删除的地区！</p>" });
	}else{	
		if(confirm('确认彻底删除吗?此操作不可还原!'))
		{
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			$.post("do_manager.php",
				{m:"delete_sort",AreaID:$('#edit_AreaID').val()},
				function(data){
				data = Jtrim(data);
					if(data == "ok"){
						$.blockUI({ message: "<p>删除成功,正在载入页面...</p>" });
						window.location.reload();
					}else{
						$.blockUI({ message: "<p>"+data+"</p>" });
					}				
				}		
			);
		}
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
}

/**********finance_log************************/
function confirm_finance(pid)
{	
	$('#windowContent').html('<iframe src="confirm_finance.php?pid='+pid+'" width="500" marginwidth="0" height="460" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('到帐确认');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'500px',top:'10%'
            }			
		});

	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function save_confirm_finance(pid)
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	$('#buttonset').attr("disabled","disabled");
	$.post("do_manager.php?m=set_save_confirm_finance&pid="+pid, $("#SonForm").serialize(),
		function(data){
			parent.set_confirm_finance(data);
		});
	closewindowui();
}

function set_confirm_finance(altmsg)
{
	if(altmsg!='')
	{
		alert(altmsg);
	}
	window.location.reload();
}



function show_sms_number1()
{
	$.post("do_sms1.php?m=shownumber1", $("#alipayment").serialize(),
		function(data){
			alert(data);
		});
}

function show_sms_number2()
{
	$.post("do_sms1.php?m=shownumber2", $("#alipayment").serialize(),
		function(data){
			alert(data);
		});
}

function show_sms_number3()
{
	$.post("do_sms1.php?m=shownumber3", $("#alipayment").serialize(),
		function(data){
			alert(data);
		});
}

function del_finance_field(pid)
{
	if(confirm('确认要删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"delete_finance_log", ID: pid},
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
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 1000); 
}

function do_save_money()
{
	if($('#data_PayBody').val()=="")
	{
		$.blockUI({ message: "<p>请输入交费内容</p>" });

	}else if($('#data_PayCompany').val()==""){
		$.blockUI({ message: "<p>请选择交费客户！</p>" });

	}else if($('#data_PayMoney').val()==""){
		$.blockUI({ message: "<p>请选择交费金额！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php?m=content_money_save",$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href='finance_log.php';
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}

function do_delete_reg(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"delete_reg", ID: pid},
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
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 1000); 
}

function sendtoemail(RID)
{
	if(RID=="" || RID=="0")
	{
		$.blockUI({ message: "<p>参数错误!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php?m=sendto_email&ID="+RID,$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>发送成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
}

function resetpasstoemail(RID)
{
	if(RID=="" || RID=="0")
	{
		$.blockUI({ message: "<p>参数错误!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php?m=resetpass_email&ID="+RID,$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>发送成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
}

//代理商
function do_save_agent()
{
	document.MainForm.referer.value = document.location;

	if($('#data_AgentArea').val()=="" || $('#data_AgentArea').val()=="0")
	{
		$.blockUI({ message: "<p>请选择所属地区！</p>" });
	
	}
	else if($('#data_AgentName').val()=="")
	{
		$.blockUI({ message: "<p>请输入公司名称！</p>" });
	}
	else if($('#data_AgentContact').val()=="")
	{
		$.blockUI({ message: "<p>请输入联系人！</p>" });
	}
	else if($('#data_AgentPhone').val()=="")
	{
		$.blockUI({ message: "<p>请输入联系电话！</p>" });
	
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php?m=content_agent_save",$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='agent.php');
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

//支付提示信息
function do_save_notice()
{
	document.MainForm.referer.value = document.location;
	var titles=$('#data_title').val();
	var url=window.location.href;
	//alert(url);return false;
	if(titles=="")
	{
		$.blockUI({ message: "<p>请填写标题！</p>" });
	
	}else if(titles.length >=225){
		$.blockUI({ message: "<p>标题过长！</p>" });
	}else if($('#data_notice').val()==""){
		$.blockUI({ message: "<p>请填写提示内容！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_notice.php?m=content_notice_save",$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href=url);
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

//平台提示信息保存
function do_save_plat_notice()
{
	document.MainForm.referer.value = document.location;
	var titles=$('#data_title').val();
	var url=window.location.href;
	var editor =CKEDITOR.instances.content.getData();   
	var bdate =$("#bdate").val();
	var edate =$("#edate").val();
	var important=$('#important input[name="important"]:checked ').val();
	var type=$('#type').val();
	
	if(titles=="")
	{
		$.blockUI({ message: "<p>请填写标题！</p>" });
	
	}else if(titles.length >=225){
		$.blockUI({ message: "<p>标题过长！</p>" });
	}else if(editor==""){
		$.blockUI({ message: "<p>请填写提示内容！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_notice.php?m=content_notice_save",{title:titles,content:editor,bdate:bdate,edate:edate,type:type,important:important},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href=url);
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

//问答信息保存
function do_save_Q_A(){
	document.MainForm.referer.value = document.location;
	var titles=$('#data_title').val();
	var url=window.location.href;
	var type=$('#type').val();
	var content=$('#data_notice').val();
	var important=$('#important input[name="important"]:checked ').val();
	
	if(titles=="")
	{
		$.blockUI({ message: "<p>请填写问题！</p>" });
	
	}else if(titles.length >=225){
		$.blockUI({ message: "<p>问题过长！</p>" });
	}else if(content==""){
		$.blockUI({ message: "<p>请填写答案！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_notice.php?m=content_Q_A_save",{title:titles,content:content,type:type,important:important},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href=url);
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

//修改问答信息
function do_edit_Q_A(){
	document.MainForm.referer.value = document.location;
	var titles=$('#data_title').val();
	var url=window.location.href;
	var content=$('#data_notice').val();
	var important=$('#important input[name="important"]:checked ').val();
	var id=$('#noticeid').val();
	
	if(titles=="")
	{
		$.blockUI({ message: "<p>请填写问题！</p>" });
	
	}else if(titles.length >=225){
		$.blockUI({ message: "<p>问题过长！</p>" });
	}else if(content==""){
		$.blockUI({ message: "<p>请填写答案！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_notice.php?m=content_Q_A_edit",{title:titles,content:content,important:important,id:id},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href=url);
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

//修改支付提示信息
function do_edit_notice()
{
	document.MainForm.referer.value = document.location;
	var url=window.location.href;
	var titles=$('#data_title').val();
	var id=$('#noticeid').val();
	if(titles=="")
	{
		$.blockUI({ message: "<p>请填写标题！</p>" });
	
	}else if(titles.length >=225){
		$.blockUI({ message: "<p>标题过长！</p>" });
	}else if($('#data_notice').val()==""){
		$.blockUI({ message: "<p>请填写提示内容！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_notice.php?m=content_notice_edit",$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href=url);
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

//修改支付提示信息
function do_edit_plat_notice()
{
	document.MainForm.referer.value = document.location;
	var url=window.location.href;
	var titles=$('#data_title').val();
	var id=$('#noticeid').val();
	var editor =CKEDITOR.instances.content.getData();   
	var bdate =$("#bdate").val();
	var edate =$("#edate").val();
	var important=$('#important input[name="important"]:checked ').val();
	if(titles=="")
	{
		$.blockUI({ message: "<p>请填写标题！</p>" });
	
	}else if(titles.length >=225){
		$.blockUI({ message: "<p>标题过长！</p>" });
	}else if(editor==""){
		$.blockUI({ message: "<p>请填写提示内容！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_notice.php?m=content_notice_edit",{title:titles,id:id,content:editor,bdate:bdate,edate:edate,important:important},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href=url);
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

//删除支付提示信息
function do_delete_pay_notice(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_notice.php",
			{m:"delete", ID: pid},
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
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 1000); 
}

function do_delete_agent(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php",
			{m:"delete_agent", ID: pid},
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
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 1000); 
}

function showImg(img)
{
	var ct = $("#windowContent");
	ct.find("img[name='showimg']").attr("src",img);
    $.blockUI({ message : $("#windowForm")});
}

function change_company_remarks()
{
	$("#open_date").datepicker({changeMonth: true,	changeYear: true});
	$("#visit_date").datepicker({changeMonth: true,	changeYear: true});
	$("#ui-datepicker-div").css({'zIndex' : 10001});
	$.blockUI({
        message : $("#windowForm"),
        css: {
            top: '15%'
        }
    });
}

