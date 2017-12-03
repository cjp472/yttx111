function loginin()
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
			$.post("./login.php",
				{m:"login", UserName: $("#user_login").val(), UserPass: $("#user_pass").val(), UserVC: $("#user_vc").val()},
				function(data){
					if(data == "ok"){
						$("#warning").html("登陆成功!请稍候,正在载入页面...");
						var usurl = "./home.php";
						window.location = usurl;
					}else if(data == "notin"){
						$("#warning").html("帐号不存在!");
						$('#wp-submit').removeAttr('disabled');
						$("#user_pass").val('');
						$("#user_login")[0].focus();
						$("#user_vc").val('');
						document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random();
					}else if(data == "isnot"){
						$("#warning").html("帐号和密码不匹配，请输入正确帐号和密码!");
						$('#wp-submit').removeAttr('disabled');
						$("#user_pass").val('');
						$("#user_login")[0].focus();
						$("#user_vc").val('');
						document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random();
					}else if(data == "companylock"){
						$("#warning").html("此供货商已锁定，暂停使用，请与供货商联系!");
						$('#wp-submit').removeAttr('disabled');
						$("#user_pass").val('');
						$("#user_login")[0].focus();
					}else if(data == "companyexpired"){
						$("#warning").html("此供货商帐号已到期，暂停使用，请与供货商联系!");
						$('#wp-submit').removeAttr('disabled');
						$("#user_pass").val('');
						$("#user_login")[0].focus();
					}else if(data == "errorcode"){
						$("#warning").html("请输入正确的验证码!");
						$('#wp-submit').removeAttr('disabled');
						$("#user_vc").val('');
						$("#user_vc")[0].focus();
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

function regin()
{
	$.growlUI('正在执行，请稍候...');
	if($("#RegUserName").val()=="")
	{
		$.blockUI({ message: "<p>请输入您的帐号！</p>" });
		window.setTimeout($.unblockUI, 3000); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}
	else if($("#RegPassword").val()==""){
		$.blockUI({ message: "<p>请输入您的密码</p>" });
		window.setTimeout($.unblockUI, 3000); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}
	else if($("#RegName").val()==""){
		$.blockUI({ message: "<p>请输入您的公司名称</p>" }); 
		window.setTimeout($.unblockUI, 3000); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}
	else if($("#RegArea").val()==""){
		$.blockUI({ message: "<p>请选择您所在地区</p>" });
		window.setTimeout($.unblockUI, 3000); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}
	else if($("#RegContact").val()==""){
		$.blockUI({ message: "<p>请输入联系人</p>" }); 
		window.setTimeout($.unblockUI, 3000); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}
	else if($("#RegPhone").val()!="" && $("#RegPhone").val().length < 7 && $("#RegPhone").val().length > 15){
		$.blockUI({ message: "<p>电话格式不正确！请重新输入！</p>" }); 
		window.setTimeout($.unblockUI, 3000); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}
   else if($("#RegEmail").val()!="" && !$("#RegEmail").val().match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)){ 
		$.blockUI({ message: "<p>邮箱格式不正确！请重新输入！</p>" }); 
		$("#RegEmail").focus(); 
		window.setTimeout($.unblockUI, 3000); 
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
   } 
	else
	{
			$('#wp-submit').attr("disabled","disabled");
			$.post("./login.php",
				{m:"regiester", RegCompanyFlag:$("#RegCompanyFlag").val(), RegUserName: $("#RegUserName").val(), RegPassword: $("#RegPassword").val(), RegMobile: $("#RegMobile").val(), RegName: $("#RegName").val(), RegArea: $("#RegArea").val(), RegContact: $("#RegContact").val(), RegPhone: $("#RegPhone").val(), RegFax: $("#RegFax").val(),RegEmail: $("#RegEmail").val(), RegAddress: $("#RegAddress").val(), RegRemark: $("#RegRemark").val(), UserVC: $("#user_vc").val()},
				function(data){
                    var flag,login_url;
                    login_url = $("#urlback").attr("href");
					if(/ok/.test(data)){
                        flag = data.replace('ok','');
                        if(flag == 9) {
                            //待审
                            $.blockUI({ message: "<p>您已成功注册，管理员会尽快审核，审核完成即可登录！<br /><br /> <a href='"+$("#urlback").attr("href")+"' >点此返回登录页面</a></p>" });
                        } else {
                            //正式 or 只读
                            $.blockUI({ message: "<p>您已成功注册，现在去看看！<br /><br /> <a href='"+$("#urlback").attr("href")+"' >点此返回登录页面</a></p>" });
                        }

					}else{
						$.blockUI({ message: "<p>"+data+"</p>" }); 
						$('#wp-submit').removeAttr('disabled');
						window.setTimeout($.unblockUI, 5000); 
						$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
					}
				}			
			);
	}

}

function jumpurl(urlmsg)
{
	window.location= urlmsg;
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
			$.post("/login.php",
				{m:"bangding", UserName: $("#bname").val(), UserPass: $("#bpass").val(),UserVC: $("#bvc").val(), openid: $("#openid").val(), accesstoken: $("#accesstoken").val(), nickname: $("#nickname").val()},
				function(data){
					if(data == "ok"){
						$("#warning").html("登陆成功!请稍候,正在载入页面...");
						var usurl = "/home.php";
						window.location = usurl;
					}else{
						$("#warning").html(data);
						$('#wp-submit').removeAttr('disabled');
						$("#bname")[0].focus();
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