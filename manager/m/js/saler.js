//去空隔函数 
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 


function CheckAll(form)
{
	for(var i=0;i<form.elements.length;i++)
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
		$.post("do_saler.php",
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
		$.post("do_saler.php",
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
		$.post("do_saler.php",
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

function closewindowui()
{
	$.unblockUI();
}


function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}


/***********************************/
function get_clientlist(aid)
{
	$.post("do_saler.php",
		{m:"loadclientlist", ID: aid, sid: $("#UserID").val()},
		function(data){
			$("#selectshield").html(data);
		}		
	);
}


function set_shield_client()
{
	var setclientv = setContentLinkValue();
	$('#windowContent').html('<iframe src="select_area_client.php?selectid='+setclientv+'" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('选择管辖的经销商');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '620px',height:'445px',top:'2%'
            }			
		});
	$('#windowForm').css("width","620px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function add_client(param_index_id,param_title) 
{
	if(param_title!='')
	{
		eval("obj = document.MainForm.selectshield");
		with(obj) {
			length = obj.length;
			options[length]=new Option(param_title,param_index_id)	;	
		}
	}
}

function set_add_client(htmlmsg) 
{
	$("#selectshield").append(htmlmsg);
}

function del_client() 
{	
	eval("obj = document.MainForm.selectshield");
	with(obj)
	{
		options[selectedIndex]=null;
	}
}

function clear_client() 
{
       eval("obj = document.MainForm.selectshield");
       var count = obj.options.length;
       for(var i = 0;i<count;i++){
             obj.options.remove(0);//每次删除下标都是0
       }
}

function setContentLinkValue()
{
	eval("obj = document.MainForm.selectshield");
 	var returnValue;

	with(obj) {
 		for(i=0; i <  obj.length ; i++){
			if(i==0) {
				returnValue = options[i].value;
			} else {
				returnValue = returnValue + ',' + options[i].value;
			}
 		} 		
	}
	if(returnValue == 'undefined') {
		returnValue = '';
	}
 	return returnValue;
}


function do_save_saler()
{
	document.MainForm.referer.value = document.location;
	document.MainForm.Shield.value = setContentLinkValue();
	var regpwd=/^1\d{10}$/g;

	if($('#data_UserName').val()=="" || $('#data_UserPass').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号和密码！</p>" });

	}else if($('#data_UserTrueName').val()==""){
		$.blockUI({ message: "<p>请输入姓名！</p>" });
	
	}else if($('#data_UserMobile').val()==""){
		$.blockUI({ message: "<p>请输入移动电话！</p>" });
		
	}else if(!regpwd.test($('#data_UserMobile').val())){
		$.blockUI({ message: "<p>请输入合法的移动电话！</p>" });
		
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_saler.php?m=saler_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('saler_add.php'), 5000);
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
				}else if(data == "okmobile"){
					$.blockUI({ message: "<p>请输入合法的移动电话号码!</p>" });
					$("#data_UserMobile")[0].focus();
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



function do_save_edit_saler()
{
	document.MainForm.referer.value = document.location;
	document.MainForm.Shield.value = setContentLinkValue();
	var regpwd=/^1\d{10}$/g;

	if($('#data_UserName').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号！</p>" });

	}else if($('#data_UserTrueName').val()==""){
		$.blockUI({ message: "<p>请输入姓名！</p>" });
	
	}else if($('#data_UserMobile').val()==""){
		$.blockUI({ message: "<p>请输入移动电话！</p>" });
		
	}else if(!regpwd.test($('#data_UserMobile').val())){
		$.blockUI({ message: "<p>请输入合法的移动电话！</p>" });
		
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_saler.php?m=saler_edit_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('saler.php'), 5000);
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
				}else if(data == "okmobile"){
					$.blockUI({ message: "<p>请输入合法的移动电话号码!</p>" });
					$("#data_UserMobile")[0].focus();
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

function do_validate(fid)
{
	if(confirm('确认已发放了吗? 此操作不可逆!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_saler.php",
			{m:"validate_deduct", ID: fid},
			function(data){
				data = Jtrim(data);
				if(data == "error"){
					$.blockUI({ message: "<p>设置不成功!</p>" });
				}else if(data == "ok"){
					var delline2 = "status_" + fid;
					$("#"+delline2).html('<span class="title_green_w" title="已发放" >√</span>');
					$.blockUI({ message: "<p>设置成功！</p>" }); 			
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
	window.setTimeout($.unblockUI, 3000); 
}

function do_more_validate()
{
	if(confirm('确认这些提成已发放了吗? 此操作不可逆!'))
	{
		document.MainForm.action = 'do_saler.php?m=do_more_validate';
        document.MainForm.submit();
	}
}

function show_deduct(did)
{
	$('#windowContent').html('<iframe src="show_deduct.php?ID='+did+'" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('订单提成明细');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'445px',top:'2%'
            }			
		});
	$('#windowForm6').css("width","620px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function deduct_excel()
{
	document.MainForm.action = 'deduct_excel.php';
	document.MainForm.target = 'exe_iframe';
	document.MainForm.submit();
}

//获取业务员的客户
function get_clientselect(sid)
{
	$.post("do_saler.php",
		{m:"loadclientselect", ID: sid},
		function(data){
			$("#data_ClientID").html(data);
		}		
	);

}

function do_save_deduct()
{
	if($('#data_DeductUser').val()=="" || $('#data_DeductUser').val()=="0" )
	{
		$.blockUI({ message: "<p>请选择业务员！</p>" });

	}else if($('#data_ClientID').val()=="" || $('#data_ClientID').val()=="0" ){
		$.blockUI({ message: "<p>请选择经销商！</p>" });

	}else if($('#data_DeductTotal').val()=="" || $('#data_DeductTotal').val()=="0" ){
		$.blockUI({ message: "<p>请输入提成金额！</p>" });
		
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_saler.php?m=deduct_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('#data_DeductTotal').val('');
					$('#data_OrderSN').val('');
					$('#data_Remark').val('');
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function do_deduct_delete(pid)
{
	if(confirm('确认要删除吗? 此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_saler.php",
			{m:"deduct_delete", ID: pid},
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
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	window.setTimeout($.unblockUI, 1000); 
}