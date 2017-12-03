/**********Profile*********/
function subeditprofile()
{	
	//$("#tip").animate({opacity: 'show'}, 'slow');
	if($("#ClientCompanyName").val()=="")
	{
		$.growlUI('公司/单位名称不能为空!');
	}else if($("#ClientTrueName").val()==""){
		$.growlUI('联系人不能为空!');
	}else if($("#ClientPhone").val()==""){
		$.growlUI('联系电话不能为空!');
	}else{
		$.post("my.php",
			{m:"editprofile", ClientTrueName: $('#ClientTrueName').val(), ClientPhone: $('#ClientPhone').val(), ClientEmail: $('#ClientEmail').val(), ClientFax: $('#ClientFax').val(), ClientMobile: $('#ClientMobile').val(), ClientAdd: $('#ClientAdd').val(), ClientAbout: $('#ClientAbout').val(), AccountName: $('#AccountName').val(), BankName: $('#BankName').val(), BankAccount: $('#BankAccount').val(), InvoiceHeader: $('#InvoiceHeader').val(), TaxpayerNumber: $('#TaxpayerNumber').val()},
			function(data){
				if(data == "ok"){						
					$.growlUI('提交成功，正在载入页面...');
					alert('修改成功!');
					window.location.reload();
				}else{
					$.growlUI(data);
				}
			}			
			);
	}
	window.setTimeout("hideshow('tip')",20000);
}

/**********Password*********/
function subeditpassword()
{	
	//$("#tip").animate({opacity: 'show'}, 'slow');
	if($("#OldPass").val()=="")
	{
		$.growlUI('请输入原密码!');
	}else if($("#NewPass").val()==""){
		$.growlUI('请输入新密码!');
	}else if($("#NewPass").val()!=$("#ConfirmPass").val()){
		$.growlUI('两次输入密码不一致，请检查重新输入密码!');
		$("#NewPass").val('');
		$("#ConfirmPass").val('');
		$("#NewPass")[0].focus();
	}else{
		$.post("my.php",
			{m:"editpass", OldPass: $('#OldPass').val(), NewPass: $('#NewPass').val(), ConfirmPass: $('#ConfirmPass').val()},
			function(data){		
				if(data == "ok"){						
					$.growlUI('提交成功，正在载入页面...');
					alert('提交成功');
					window.location.reload();
				}else{
					$.growlUI(data);
				}
			}			
			);
	}
}
function setinputfile(fpn)
{	
	var filevalue = $('#set_filename').val();
	fpn = fpn.replace('thumb_','img_');
	if(fpn!='' && fpn!=null)
	{
		$("#"+filevalue).val(fpn);
		$("#"+filevalue+"_text").html('<a href="../resource/'+fpn+'" target="_blank"><img src="../resource/'+fpn+'" border="0" height="150" /></a>');
	}	
	$.unblockUI();
}

/**********Address************/
function subaddaddress()
{	
	var dAddressFlag = 0;

	if($("#AddressContact").val()=="")
	{
		$.growlUI('联系人不能为空!');
	}else if($("#AddressPhone").val()==""){
		$.growlUI('联系电话不能为空!');
	}else if($("#AddressAddress").val()==""){
		$.growlUI('收货地址不能为空!');
	}else{

		$('#subaddress').attr("disabled","disabled");		
		if($("#defaultadddress")[0].checked){ dAddressFlag = 1;}

		$.post("my.php",
			{m:"addaddress", AddressCompany: $('#AddressCompany').val(), AddressContact: $('#AddressContact').val(), AddressPhone: $('#AddressPhone').val(), AddressAddress: $('#AddressAddress').val(),AddressFlag: dAddressFlag},
			function(data){		
				if(data == "ok"){						
					$.growlUI('提交成功，正在载入页面...');
					window.location.reload();
				}else{
					$.growlUI(data);
					$('#subaddress').removeAttr('disabled');
				}
			}			
			);
	}
	window.setTimeout("hideshow('tip')",20000);
}


function do_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$("#tip").animate({opacity: 'show'}, 'slow');
		$.post("my.php",
			{m:"deladdress", ID: pid},
			function(data){
				if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					$.growlUI("删除成功!");
				}else{
					$.growlUI(data);
				}					
			}		
		);
	}else{
		return false;
	}
}


function do_set_edit(pid,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag)
{
	
	$.growlUI("请修改左侧输入框中的信息...");
	$("#editAddressID").val(pid);
	$("#editAddressCompany").val(AddressCompany);
	$("#editAddressContact").val(AddressContact);
	$("#editAddressPhone").val(AddressPhone);
	$("#editAddressAddress").val(AddressAddress);

	if(AddressFlag == "1") $("#defaulteditaddress").attr("checked",true); else $("#defaulteditaddress").attr("checked",false);
	
	$("#line_add_add").animate({opacity: 'hide'}, 'slow');
	$("#line_edit_add").animate({opacity: 'show'}, 'slow');
}


function subeditaddress()
{	
	var dAddressFlag = 0;
	if($("#editAddressContact").val()=="")
	{
		$.growlUI("联系人不能为空!");
	}else if($("#editAddressPhone").val()==""){
		$.growlUI("联系电话不能为空!");
	}else if($("#editAddressAddress").val()==""){
		$.growlUI("收货地址不能为空!");
	}else{

		$('#subaddress2').attr("disabled","disabled");		
		if($("#defaulteditaddress")[0].checked){ dAddressFlag = 1;}

		$.post("my.php",
			{m:"editaddress", AddressID: $('#editAddressID').val(), AddressCompany: $('#editAddressCompany').val(), AddressContact: $('#editAddressContact').val(), AddressPhone: $('#editAddressPhone').val(), AddressAddress: $('#editAddressAddress').val(),AddressFlag: dAddressFlag},
			function(data){		
				if(data == "ok"){						
					$.growlUI("提交成功，正在载入页面...");
					window.location.reload();
				}else{
					$.growlUI(data);
					$('#subaddress2').removeAttr('disabled');
				}
			}			
			);
	}
}

function closewindowui()
{
	$.unblockUI();
	//window.setTimeout($.unblockUI, 2000);
}

function setweixin(pid)
{
	if(confirm('确定要解除绑定吗?'))
	{
		$.post("my.php",
			{m:"remove_weixin", ID: pid},
			function(data){
				if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					$.growlUI("解除成功!");
				}else{
					$.growlUI(data);
				}					
			}		
		);
	}else{
		return false;
	}
}

function setqq(pid)
{
	if(confirm('确定要解除绑定吗?'))
	{
		$.post("my.php",
			{m:"remove_qq", OpenID: pid},
			function(data){
				if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					$.growlUI("解除成功!");
				}else{
					$.growlUI(data);
				}					
			}		
		);
	}else{
		return false;
	}
}

//对账记录
function getSear(){
	
	$.post(
			"openpay.php",
			{
				type: 'getSear'
			},
			function(msg){
				console.log(msg);
			},
			'json'
		);
}

//获取验证码[重新获取验证码有效期60S]
function getSecurityCode(eleId, codeBtnEle, msgBoxEle){

	//获取手机号码
	var ele = $("#"+eleId),
		codeBtn = $("#"+codeBtnEle),
		msgBox  = $("#"+msgBoxEle);
	var eleVal = ele.val();
	
	if(eleVal.length != 11 || isNaN(eleVal)){
		msgBox.html('请输入正确的手机号码');
		ele.focus();
		return false;
	}
	
	//验证手机号码
	
	//获取手机号成功后，需要把手机号的input框设置为不能修改且背景是灰色状态,并缓存手机号一分钟
	ele.attr("disabled", true).addClass('global-gray');
	codeBtn.attr("disabled", true);
	$.cookie("tel", eleVal, {path: '/', expires: (1/86400)*60});
	//验证短信获取时间并获取验证码
	$.get('my.php', {m: 'getSecurityCode', mobile: eleVal}, function(response){
//		$.get('my.php', {m: 'getSecurityCode', mobile: eleVal}, function(response){
		
		if(response['status'] == 'error'){
			msgBox.html(response['message']);
			//手机号验证失败，input框变为可输入状态
			ele.removeAttr("disabled").removeClass('global-gray');
			codeBtn.removeAttr("disabled");
		}else if(response['status'] == 'unreach'){
			CountDown(response['residuetime'], eleId, codeBtnEle);	//开始倒计时
			msgBox.html(response['message']);
			codeBtn.attr("disabled", true);
		}else{//成功获取
			
			CountDown(60, eleId, codeBtnEle);	//开始倒计时
			msgBox.html(response['message']);
		}
	}, 'json');
	
}

//默认60S倒计时
function CountDown(seconds, eleId, codeBtnEle) {
	
	seconds = (seconds == undefined) ? 60 : seconds;	//默认60秒
	
    if (seconds == 0) {
        $("#"+codeBtnEle).val("重新获取").removeAttr("disabled").removeClass('global-gray');
        $('#valicode:disabled').length ? '' : 		$("#"+eleId).removeAttr("disabled").removeClass('global-gray');
    }else{
    	$.cookie("seconds", seconds, {path: '/', expires: (1/86400)*seconds});
    	$("#"+codeBtnEle).val(seconds+"s后重新获取").addClass('global-gray');
    	seconds--;
    	setTimeout(function(){CountDown(seconds, eleId, codeBtnEle)}, 1000);
    }
}

