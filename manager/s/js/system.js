//去空隔函数 
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 


function CheckAll(form,nameflag)
{
	var lenghtflag = nameflag.length;
	for (var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if(e.name != undefined && e.type=="checkbox")
		{
			var nameend = e.name.substring(0,5);
			tmpa = eval("form."+nameflag+".checked")
			if (e.name != 'chkall' && nameflag == nameend) e.checked = tmpa;
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

function do_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
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
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000); 
}

function do_restore(pid)
{
	if(confirm('确认还原吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
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
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000); 
}

function do_quite_delete(pid)
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
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


function upload_file(fildname)
{
	$('#windowContent').html('<iframe src="../plugin/jqUploader/uploadfile.php" width="500" marginwidth="0" height="280" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',top:'20%'
            }			
		});
    $('#set_filename').val(fildname);
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}


function setinputfile(fpn)
{	
	var filevalue = $('#set_filename').val();
	fpn = fpn.replace('thumb_','img_');
	if(fpn!='' && fpn!=null)
	{
		$("#"+filevalue).val(fpn);
		$("#"+filevalue+"_text").html('<a href="../resource/'+fpn+'" target="_blank"><img src="../resource/'+fpn+'" border="0" /></a>');
	}	
	$.unblockUI();
}

function setinputimg(fpn)
{	
	var filevalue = $('#set_filename').val();
	fpn = fpn.replace('thumb_','img_');
	if(fpn!='' && fpn!=null)
	{
		$("#"+filevalue).val(fpn);
		$("#"+filevalue+"_text").html('<a href="../resource/'+fpn+'" target="_blank"><img src="../resource/'+fpn+'" border="0" /></a>');
	}	
	$.unblockUI();
}

function closewindowui()
{
	$.unblockUI();
}


function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}


function do_save_user()
{
	document.MainForm.referer.value = document.location;

	if($('#data_UserName').val()=="" || $('#data_UserPass').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号和密码！</p>" });

	}else if($('#data_UserTrueName').val()==""){
		$.blockUI({ message: "<p>请输入管理员姓名！</p>" });
	
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php?m=content_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('user.php'), 5000);
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else if(data == "okname"){
					$.blockUI({ message: "<p>请输入正确的用户名(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "okpass"){
					$.blockUI({ message: "<p>请输入正确的密码(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserPass")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>此登陆帐号已使用，请换名再试!</p>" });
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


function do_save_edit_user()
{
	document.MainForm.referer.value = document.location;

	if($('#data_UserName').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号！</p>" });

	}else if($('#data_UserTrueName').val()==""){
		$.blockUI({ message: "<p>请输入管理员姓名！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
		$.post("do_system.php?m=content_edit_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('user.php'), 5000);
				}else if(data == "okname"){
					$.blockUI({ message: "<p>请输入正确的用户名(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "okpass"){
					$.blockUI({ message: "<p>请输入正确的密码(数字、字母和下划线 3-18位)!</p>" });
					$("#data_UserPass")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>登陆名已存在，请使用另外的登陆名!</p>" });
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

/********* Info **************************/
function showeditinfo()
{
	document.MainForm.referer.value = document.location;
	
	$("#oldinfo").animate({opacity: 'hide'}, 'slow');
	$("#editinfo").animate({opacity: 'show'}, 'slow');

}

function canceleditinfo()
{
	document.MainForm.referer.value = document.location;
	
	$("#oldinfo").animate({opacity: 'show'}, 'slow');
	$("#editinfo").animate({opacity: 'hide'}, 'slow');

}

function subeditinfo()
{
	document.MainForm.referer.value = document.location;

	if($('#data_CompanyContact').val()=="" )
	{
		$.blockUI({ message: "<p>联系人信息不能为空!</p>" });

	}else if($('#data_CompanyPhone').val()==""){
		$.blockUI({ message: "<p>联系电话不能为空!</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
			{m:"companyinfo_edit_save",CompanyLogo: $('#data_CompanyLogo').val(),CompanyContact: $('#data_CompanyContact').val(), CompanyMobile: $('#data_CompanyMobile').val(), CompanyPhone: $('#data_CompanyPhone').val(),CompanyFax: $('#data_CompanyFax').val(), CompanyAddress: $('#data_CompanyAddress').val(),CompanyEmail: $('#data_CompanyEmail').val(),CompanyRemark: $('#data_CompanyRemark').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('system.php'), 5000);
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

/********* set ***********/
function savesettype(ty)
{
	//$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
	
	document.MainForm.action = 'do_system.php?m=update_settype&at='+ty;
    document.MainForm.submit();

	//$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	//window.setTimeout($.unblockUI, 1000);
}

function change_template(tv)
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		
		$.post("do_system.php",
			{m:"change_template_value", setvalue: tv},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>设置成功!</p>" });
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
}

/******* area **************************/

function do_save_sort()
{
	if($('#data_ParentID').val()=="")
	{
		$.blockUI({ message: "<p>请先选择上级地区！</p>" });

	}else if($('#data_SiteName').val()==""){
		$.blockUI({ message: "<p>请输入地区名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
			{m:"save_sort", AreaParentID: $('#data_AreaParentID').val(), AreaName: $('#data_AreaName').val(), AreaAbout: $('#data_AreaAbout').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = "client_area.php";
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


function set_edit_sort(SiteID,ParentID,SiteName,SitePinyi,Content)
{
	if(SiteID != "")
	{
		$('#edit_AreaID').val(SiteID);
		$('#edit_AreaParentID').val(ParentID);
        //$("#edit_AreaParentID").select2();
		$('#edit_AreaName').val(SiteName);
		$('#edit_AreaPinyi').val(SitePinyi);		
		$('#edit_AreaAbout').val(Content);
	}	 
		 
}


function do_save_edit_sort()
{
	if($('#edit_ParentID').val()=="")
	{
		$.blockUI({ message: "<p>请先选择上级地区！</p>" });

	}else if($('#edit_SiteName').val()==""){
		$.blockUI({ message: "<p>请输入地区名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
			{m:"save_edit_sort",AreaID:$('#edit_AreaID').val(), AreaParentID: $('#edit_AreaParentID').val(), AreaName: $('#edit_AreaName').val(), AreaPinyi: $('#edit_AreaPinyi').val(), AreaAbout: $('#edit_AreaAbout').val()},
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
    window.setTimeout($.unblockUI, 2000);
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
			$.post("do_system.php",
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


/**********accounts************/
function do_delete_accounts(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
			{m:"delete_accounts", ID: pid},
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
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
	window.setTimeout($.unblockUI, 2000); 
}


function do_save_accounts()
{
	document.MainForm.referer.value = document.location;

	if($('#data_AccountsBank').val()=="")
	{
		$.blockUI({ message: "<p>请先输入开户行</p>" });

	}else if($('#data_AccountsNO').val()==""){
		$.blockUI({ message: "<p>请先输入帐号</p>" });
	
	}else if($('#data_AccountsName').val()==""){
		$.blockUI({ message: "<p>开户名称(收款人)</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php?m=accounts_add_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('#data_AccountsBank').val('');
					$('#data_AccountsNO').val('');
					$('#data_AccountsName').val('');
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
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


function do_save_edit_accounts()
{
	document.MainForm.referer.value = document.location;

	if($('#data_AccountsBank').val()=="")
	{
		$.blockUI({ message: "<p>请先输入开户行</p>" });

	}else if($('#data_AccountsNO').val()==""){
		$.blockUI({ message: "<p>请先输入帐号</p>" });
	
	}else if($('#data_AccountsName').val()==""){
		$.blockUI({ message: "<p>开户名称(收款人)</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php?m=accounts_edit_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = "accounts.php";

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


function delete_level(lid)
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
			{m:"delete_level",levelid:lid},
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

function selectpointclick()
{
	if($("#pointtype_2").attr('checked')==true)
	{
		$("#showpencentid").show();
	}else{
		$("#showpencentid").hide();
	}
}

function selectpointclick2()
{
	if($("#pointtype_2").attr('checked')=="checked")
	{
		$("#showpencentid").show();
	}else{
		$("#showpencentid").hide();
	}
}

function selectpayclick()
{
	if($("#data_PayType2").attr('checked')==true)
	{
		$("#show_alipay_id").show();
	}else{
		$("#show_alipay_id").hide();
	}
}

function selectpayclick2()
{
	if($("#data_PayType2").attr('checked')=="checked")
	{
		$("#show_alipay_id").show();
	}else{
		$("#show_alipay_id").hide();
	}
}