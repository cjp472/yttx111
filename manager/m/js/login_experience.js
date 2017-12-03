var childWindow;
function toQzoneLogin()
{
	childWindow = window.open("qq/qq_login.php","TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
} 

function closeChildWindow()
{
	childWindow.close();
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
}

function loginto()
{
	$("#warning").show();
	$("#warning").html('正在执行，请稍候...');
	var remberme = "";
	if($("#user_login").val()=="")
	{
		$("#warning").html('请输入帐号!');
	}
	else if($("#user_pass").val()==""){
		$("#warning").html('请输入密码!');
	}
	else
	{
			$('#wp-submit').attr("disabled","disabled");
			$.post("m/do_login_experience.php",
				{m:"login", UserName: $("#user_login").val(), UserPass: $("#user_pass").val(), UserVc: $("#user_vc").val(), industry: $("#industry").val(), cp: $("#cp").val()},
				function(data){
					data = Jtrim(data);
					if(data == "ok"){
						$("#warning").html("登陆成功!请稍候,正在载入页面...");
						var usurl = "m/home.php";
						window.location = usurl;
					}else if(data == "notin"){
						$("#warning").html("帐号不存在!");
						$("#user_login").val('');
						$("#user_pass").val('');
						$("#user_vc").val('');
						$("#user_login")[0].focus();
						$('#wp-submit').removeAttr('disabled');
						document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random();
					}else if(data == "isnot"){
						$("#warning").html("帐号和密码不匹配，请输入正确帐号和密码!");
						$("#user_pass").val('');
						$("#user_vc").val('');
						$("#user_login")[0].focus();
						$('#wp-submit').removeAttr('disabled');
						document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random();
					}else if(data == "companylock"){
						$("#warning").html("此公司帐号已锁定，请与管理员联系!");
						$("#user_pass").val('');
						$("#user_login")[0].focus();
					}else if(data == "companyexpired"){
						$("#warning").html("此公司帐号已过期，请与管理员联系!");
						$("#user_pass").val('');
						$("#user_login")[0].focus();
					}else if(data == "errorcode"){
						$("#warning").html("请输入正确的验证码!");
						$("#user_vc").val('');
						$("#user_vc")[0].focus();
						$('#wp-submit').removeAttr('disabled');
						document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random();
					}else{
						$("#warning").html(data);
						$('#wp-submit').removeAttr('disabled');
						$("#user_login")[0].focus();
					}
				}			
			);
		return false; 
	}
	return false; 
}

function do_save_change()
{
	document.MainForm.referer.value = document.location;

	if($('#data_OldPass').val()=="" )
	{
		$.blockUI({ message: "<p>请输入原密码！</p>" });

	}else if($('#data_NewPass').val()==""){
		$.blockUI({ message: "<p>请输入新密码！</p>" });

	}else if($('#data_NewPass').val()!=$('#data_ConfirmPass').val()){
		$.blockUI({ message: "<p>新密码与确认密码不一致！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_login.php",
			{m:"change_pass", OldPass: $('#data_OldPass').val(), NewPass: $('#data_ConfirmPass').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>修改成功!</p>" });
					$('#data_OldPass').val('');
					$('#data_NewPass').val('');
					$('#data_ConfirmPass').val('');
					$("#data_OldPass")[0].focus();
					$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
					
				}else if(data == "olderror"){
					$.blockUI({ message: "<p>原密码不正确，请重新输入原密码!</p>" });
					$('#data_OldPass').val('');
					$("#data_OldPass")[0].focus();
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

function jumpurl($urlmsg)
{
	window.location = $urlmsg;
}

function binto()
{
	$("#warning").show();
	$("#warning").html('正在执行，请稍候...');
	var remberme = "";
	if($("#bname").val()=="")
	{
		$("#warning").html('请输入订货宝帐号!');
	}
	else if($("#bpass").val()==""){
		$("#warning").html('请输入订货宝密码!');
	}
	else
	{
			$('#wp-submit').attr("disabled","disabled");
			$.post("../m/do_login.php",
				{m:"bangding", UserName: $("#bname").val(), UserPass: $("#bpass").val(), UserVC: $("#bvc").val(), openid: $("#openid").val(), accesstoken: $("#accesstoken").val(), nickname: $("#nickname").val()},
				function(data){
					data = Jtrim(data);
					if(data == "ok"){
						$("#warning").html("登陆成功!请稍候,正在载入页面...");
						var usurl = "/m/home.php";
						window.location = usurl;
					}else{
						$("#warning").html(data);
						$('#wp-submit').removeAttr('disabled');
						$("#user_login")[0].focus();
					}
				}			
			);
		return false; 
	}
	return false; 
}

function checkCode(e){
	var _e = window.event ? window.event : e;
	if(_e.keyCode == 13) {binto();}
}

function do_cancel_qq(oid)
{
	if(confirm('确定要解除绑定吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_login.php",
			{m:"cancel_qq", openid: oid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>执行成功!</p>" });
					window.location.href = 'change_pass.php';
					
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
				}				
			}		
		);
	}
}