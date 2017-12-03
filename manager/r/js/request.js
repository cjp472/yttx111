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


/**************************/
function do_save_request()
{
	document.MainForm.referer.value = document.location;

	if($('#data_CompanyID').val()=="" || $('#data_CompanyID').val()=="0")
	{
		$.blockUI({ message: "<p>请选择试用帐号！</p>" });
	}
	else if($('#data_EndDate').val()=="")
	{
		$.blockUI({ message: "<p>请选择到期时间！</p>" });
	}
	else if($('#data_Password').val()=="")
	{
		$.blockUI({ message: "<p>请输入密码！</p>" });
	}
	else if($('#data_CompanyName').val()=="")
	{
		$.blockUI({ message: "<p>请输入客户名称！</p>" });
	}
	else if($('#data_Contact').val()=="")
	{
		$.blockUI({ message: "<p>请输入联系人！</p>" });
	}
	else if($('#data_QQ').val()=="" && $('#data_Email').val()=="" )
	{
		$.blockUI({ message: "<p>请输入QQ或者邮箱！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_request.php?m=content_add_request_save",$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='request.php');
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




function sendtoemail(RID)
{
	if(RID=="" || RID=="0")
	{
		$.blockUI({ message: "<p>参数错误!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_request.php?m=sendto_email&ID="+RID,$("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
}


function closewindowui()
{
	$.unblockUI();
}


function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}

function format_content()
{
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'400px',top:'10%'
            }			
		});
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function do_formart()
{

		$.post("do_request.php",
			{m:"format", content: $('#emailcontent').val()},
			function(data){

				if(data.backtype == "ok")
				{
					$('#data_CompanyName').val(data.CompanyName);
					$('#data_Contact').val(data.Name);
					$('#data_QQ').val(data.QQ);
					$('#data_Mobile').val(data.Mobile);
					$('#data_Phone').val(data.Phone);	
					$('#data_Email').val(data.Email);
					$('#data_Remark').val(data.Product);					

					closewindowui();
				}else{
					alert(data.backtype);
				}
			},"json");		

}