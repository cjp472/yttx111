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
/************************选择药店2017年11月24日**************/
function set_shield_client()
{
	$('#windowContent').html('<iframe src="system_area_goods.php" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
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
function clear_client() 
{
       eval("obj = document.MainForm.selectshield");
       var count = obj.options.length;
       for(var i = 0;i<count;i++){
             obj.options.remove(0);//每次删除下标都是0
       }
}
function del_client() 
{	
	eval("obj = document.MainForm.selectshield");
	with(obj)
	{
		options[selectedIndex]=null;
	}
}
function set_add_client(htmlmsg) 
{
	$("#selectshield").append(htmlmsg);
}
/*********************End****************/


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
		$("#"+filevalue+"_text").html('<a href="'+fpn+'" target="_blank"><img src="'+fpn+'" border="0" /></a>');
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
		$("#"+filevalue+"_text").html('<a href="'+resourceurl+fpn+'" target="_blank"><img src="'+resourceurl+fpn+'" border="0" /></a>');
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
	var regpwd=/^1\d{10}$/g;

	if($('#data_UserName').val()=="" || $('#data_UserPass').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号和密码！</p>" });

	}else if($('#data_UserTrueName').val()==""){
		$.blockUI({ message: "<p>请输入管理员姓名！</p>" });
	
	}else if($('#data_UserMobile').val()==""){
		$.blockUI({ message: "<p>请输入管理员移动电话！</p>" });
		
	}else if(!regpwd.test($('#data_UserMobile').val())){
		$.blockUI({ message: "<p>请输入合法的移动电话！</p>" });
		
	}else if($('#CompanyType').val() == '-1'){
		$.blockUI({ message: "<p>请选择所创建账号类型！</p>" });
		
	}else{
            var  selectshield= '';  //定义数组
            $("#selectshield option").each(function(){  //遍历所有option
                var txt = $(this).val();   //获取option值
                if(txt!=''){
                    selectshield+=txt+",";  //添加到数组中
                }
            });
            $('#Shield').val(selectshield);
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
				}else if(data == 'okmobile'){
					$.blockUI({ message: "<p>请输入正确的移动电话号码，以便使用找回密码功能!</p>" });
					$("#data_UserMobile")[0].focus();
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
	var regpwd=/^1\d{10}$/g;
	if($('#data_UserName').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号！</p>" });

	}else if($('#data_UserTrueName').val()==""){
		$.blockUI({ message: "<p>请输入管理员姓名！</p>" });

	}else if($('#data_UserMobile').val()==""){
		$.blockUI({ message: "<p>请输入管理员移动电话！</p>" });
		
	}else if(!regpwd.test($('#data_UserMobile').val())){
		$.blockUI({ message: "<p>请输入合法的移动电话！</p>" });
		
	}else{
             var  selectshield= '';  //定义数组
            $("#selectshield option").each(function(){  //遍历所有option
                var txt = $(this).val();   //获取option值
                if(txt!=''){
                    selectshield+=txt+",";  //添加到数组中
                }
            });
            $('#Shield').val(selectshield);
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
				}else if(data == 'okmobile'){
					$.blockUI({ message: "<p>请输入正确的移动电话号码，以便使用找回密码功能!</p>" });
					$("#data_UserMobile")[0].focus();
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

	}else if($('#data_CompanyMobile').val()==""){
		$.blockUI({ message: "<p>移动电话不能为空!</p>" });
		//todo 验证手机号码

	}else if($('#data_CompanyPhone').val()==""){
		$.blockUI({ message: "<p>公司电话不能为空!</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
			{m:"companyinfo_edit_save",CompanyContact: $('#data_CompanyContact').val(), CompanyMobile: $('#data_CompanyMobile').val(), CompanyPhone: $('#data_CompanyPhone').val(),CompanyFax: $('#data_CompanyFax').val(), CompanyAddress: $('#data_CompanyAddress').val(),CompanyEmail: $('#data_CompanyEmail').val(),CompanyRemark: $('#data_CompanyRemark').val()},
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
	var ordertime = $('input[name="order_time"][checked="checked"]').val();
	if(ordertime == 'on'){
		if($('#ordertime_datestart').val() =='0' || $('#ordertime_dateend').val() =='0'){
			alert('请选择工作日时间段');return;
		}
		if($('#ordertime_timestart').val() =='' || $('#ordertime_timeend').val() ==''){
			alert('请选择工作时间段');return;
		}
		if(!RegTime($('#ordertime_timestart').val()) || !RegTime($('#ordertime_timeend').val())){
			alert('请输入正确的工作时间');return;
		}	
		if($('#ordertime_datestart').val() == $('#ordertime_dateend').val()){
			var s = Date.parse('2000/1/1 '+$('#ordertime_timestart').val());
			var e = Date.parse('2000/1/1 '+$('#ordertime_timeend').val());
			if(((e-s)/3600/1000*60)<0){
				alert("同一天开始时间不能小于结束时间！");return;
			}
			else if(((e-s)/3600/1000*60)<30){
				alert("同一天工作时间不能小于30分钟！");return;
			}	
		}
	}
    if(ty == 'product' && $("input[name='stair_status']:checked").val() == 'Y') {
        var err = "";
        $("#stair_div tr:not('.stair_clone')").each(function(){
            var intAmount = 0;
            var amount = $(this).find("input:eq(0)").val();
            var free = $(this).find("input:eq(1)").val();


            if(!/^\d+$/.test(amount)) {
                err = "请输入正确的满省条件(只能输入正整数)!";
            } else if(!/^\d+(\.?)\d*$/.test(free)) {
                err = "请输入正确的满省金额(只能输入数字)!";
            } else {
                amount = parseFloat(amount);
                free = parseFloat(free);
                intAmount = Math.floor(amount);


                if(amount <= 0) {
                    err = "满省条件必需大于0!";
                } else if(intAmount != amount) {
                    err = "满省条件只能为整数!";
                } else if(free <=0 ) {
                    err = "满省金额必需大于0!";
                } else if(amount < free) {
                    err = "省的金额不能大于满省条件金额!";
                }
            }

        });
        if(err) {
            alert(err);
            return false;
        }
    }
    
    if(ty == 'printf' && $("#ptype").val() == 'order')
	{
    	var vals = [];
    	$("#print_field td input:checkbox:checked").each(function () {
    		if($(this).attr('id') != 'CompanyInfoPrint')
    			vals.push($(this).attr('value'));
        });
    	
    	if(vals.length < 6)
		{
    		 alert("请选择不少于6个商品打印字段，您已选择了"+vals.length+"个！");
             return false;
		}
	}


	document.MainForm.action = 'do_system.php?m=update_settype&at='+ty;
    document.MainForm.submit();

	//$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	//window.setTimeout($.unblockUI, 1000);
}

function RegTime(time){
	var reg = /^[0-2]\d:\d\d$/g;
	
	return reg.test(time);
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
		$.blockUI({ message: "<p>请先选择上级地区！</p>", css:{ 'left':'48%' }});

	}else if($('#data_SiteName').val()==""){
		$.blockUI({ message: "<p>请输入地区名称！</p>", css:{ 'left':'48%' }});

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" , css:{ 'left':'48%' }});
		$.post("do_system.php",
			{m:"save_sort", AreaParentID: $('#data_AreaParentID').val(), AreaName: $('#data_AreaName').val(), AreaAbout: $('#data_AreaAbout').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>", css:{ 'left':'48%' }});
					window.location.href = "client_area.php";
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" , css:{ 'left':'48%' }});
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
		$('#edit_AreaParentID').val(ParentID).select2();
		$('#edit_AreaName').val(SiteName);
		$('#edit_AreaPinyi').val(SitePinyi);		
		$('#edit_AreaAbout').val(Content);
	}	 
		 
}


function do_save_edit_sort()
{
	if($('#edit_ParentID').val()=="")
	{
		$.blockUI({ message: "<p>请先选择上级地区！</p>", css:{ 'left':'48%' }});

	}else if($('#edit_SiteName').val()==""){
		$.blockUI({ message: "<p>请输入地区名称！</p>" , css:{ 'left':'48%' }});

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>", css:{ 'left':'48%' }});
		$.post("do_system.php",
			{m:"save_edit_sort",AreaID:$('#edit_AreaID').val(), AreaParentID: $('#edit_AreaParentID').val(), AreaName: $('#edit_AreaName').val(), AreaPinyi: $('#edit_AreaPinyi').val(), AreaAbout: $('#edit_AreaAbout').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>修改成功,正在载入页面...</p>", css:{ 'left':'48%' }});
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" , css:{ 'left':'48%' }});
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
		$.blockUI({ message: "<p>请先选择您要删除的地区！</p>" , css:{ 'left':'45%' }});
	}else{
		if(confirm('确认彻底删除吗?此操作不可还原!'))
		{
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" , css:{ 'left':'45%' }});
			$.post("do_system.php",
				{m:"delete_sort",AreaID:$('#edit_AreaID').val()},
				function(data){
				data = Jtrim(data);
					if(data == "ok"){
						$.blockUI({ message: "<p>删除成功,正在载入页面...</p>" , css:{ 'left':'45%' }});
						window.location.reload();
					}else{
						$.blockUI({ message: "<p>"+data+"</p>", css:{ 'left':'45%' }});
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
                    $("#data_PayPartnerID").val('');
                    $("#data_PayKey").val('');
                    $("#data_PayType1").attr('checked','checked');
                    selectpayclick();
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

//风格设置
function subeditlogoimg()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
	$.post("do_system.php",
		{m:"template_logo_edit_save",CompanyLogo: $('#data_CompanyLogo').val(), CompanyLogin: $('#data_CompanyLogin').val()},
		function(data){
		data = Jtrim(data);
			if(data == "ok"){
				$.blockUI({ message: "<p>保存成功!</p>" });
				//window.setTimeout(jumpurl('templateset.php'), 5000);
			}else{
				$.blockUI({ message: "<p>"+data+"</p>" });
				$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
			}				
		}		
	);

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}

//底部信息
function subeditbuttoninfo()
{
	document.ButtonForm.action = 'do_system.php?m=update_buttoninfo';
    document.ButtonForm.submit();
}


//保存费用类型
function do_save_bill()
{
	document.MainForm.referer.value = document.location;

	if($('#data_BillNO').val()=="")
	{
		$.blockUI({ message: "<p>请先输入费用类型编号！</p>" });

	}else if($('#data_BillName').val()==""){
		$.blockUI({ message: "<p>请先输入费用类型名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php?m=bill_add_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = "expense_bill.php";
					$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
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

function do_edit_bill()
{
	document.MainForm.referer.value = document.location;

	if($('#edit_BillNO').val()=="")
	{
		$.blockUI({ message: "<p>请先输入费用类型编号！</p>" });

	}else if($('#edit_BillName').val()==""){
		$.blockUI({ message: "<p>请先输入费用类型名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php?m=bill_edit_save", $("#MainForm").serialize(),
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = "expense_bill.php";
					$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
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

function set_edit_bill(uid,uno,uname)
{
	$("#update_id").val(uid);
	$("#edit_BillNO").val(uno);
	$("#edit_BillName").val(uname);
	$("#edit_bill").show();
}

function do_delete_bill(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_system.php",
			{m:"delete_bill", ID: pid},
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
	window.setTimeout($.unblockUI, 2000); 
}

function orderitem_select(){
	
	
}
