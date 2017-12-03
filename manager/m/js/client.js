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


function selectorderline(foid)
{
	if($("#ClientConsignment_"+foid).attr('checked')==false){
		$("#ClientConsignment_"+foid).attr("checked",true);
		$("#selected_line_"+foid).css("background-color","#efefef");
	} else {
		$("#ClientConsignment_"+foid).attr("checked",false);
		$("#selected_line_"+foid).css("background-color","#ffffff");
	}
}

function selectorderlinefocus(foid)
{
	if($("#ClientConsignment_"+foid).attr('checked')==false){
		$("#selected_line_"+foid).css("background-color","#efefef");
	} else {
		$("#selected_line_"+foid).css("background-color","#ffffff");
	}
}

function selectorder2line(foid)
{
	if($("#ClientPay_"+foid).attr('checked')==false){
		$("#ClientPay_"+foid).attr("checked",true);
		$("#selected2_line_"+foid).css("background-color","#efefef");
	} else {
		$("#ClientPay_"+foid).attr("checked",false);
		$("#selected2_line_"+foid).css("background-color","#ffffff");
	}
}

function selectorder2linefocus(foid)
{
	if($("#ClientPay_"+foid).attr('checked')==false){
		$("#selected2_line_"+foid).css("background-color","#efefef");
	} else {
		$("#selected2_line_"+foid).css("background-color","#ffffff");
	}
}




function do_delete(pid)

{

	if(confirm('确认删除吗?'))

	{

		$.blockUI({ message: "<p>正在执行，请稍后...</p>", css:{ 'left':'48%' }});

		$.post("do_client.php",

			{m:"delete", ID: pid},

			function(data){
				data = Jtrim(data);

				if(data == "ok"){

					var delline = "line_" + pid;

					$("#"+delline).hide();

					$.blockUI({ message: "<p>删除成功!</p>" , css:{ 'left':'48%' }}); 
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client.php');
				}else{

					$.blockUI({ message: "<p>"+data+"</p>" }); 
					$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client.php');
				}					

			}		

		);

	}else{

		return false;

	}

	window.setTimeout($.unblockUI, 2000); 
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 

}



function do_restore(pid)

{

	if(confirm('确认还原吗?'))

	{

		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 

		$.post("do_client.php",

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

	window.setTimeout($.unblockUI, 2000); 
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 

}



function do_quite_delete(pid)
{

	if(confirm('确认彻底删除吗，此操将删除该用户下所有数据! 此操作不可还原!'))
	{
		//计算屏幕尺寸
		var width  = $(window).width();
		var _left =  (width - 500) / 2;

		$.blockUI({ message: "<p>正在执行，请稍后...</p>" , css:{ 'left':'48%' }});

		$.post("do_client.php",

			{m:"quite_delete", ID: pid},

			function(data){

				data = Jtrim(data);

				if(data == "error"){

					$.blockUI({ message: "<p>"+data+"</p>" , css:{ 'left':'45%' }});

				}else if(data == "ok"){

					var delline = "line_" + pid;

					$("#"+delline).hide();

					$.blockUI({ message: "<p>删除成功!</p>" , css:{ 'left':'48%' }});
				}else{

					$.blockUI({ message: "<p>"+data+"</p>", css:{ 'left':'48%' }});

				}				

			}		

		);

	}else{

		return false;

	}

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 

	window.setTimeout($.unblockUI, 2000); 

}





 function going(o, sid)

 {

	document.MainForm.referer.value = document.location; 

		switch(o) {			

				

			case 'del':

				    if(confirm("确认批量删除吗?")) {

					document.MainForm.action = 'do_client.php?m=delarr&sid='+sid;

                    document.MainForm.submit();

				}

				break;	

				

			case 'quite_delete':

				    if(confirm("确认批量删除吗?")) {

					document.MainForm.action = 'do_client.php?m=quite_delete_arr&sid='+sid;

					document.MainForm.target = 'exe_iframe';

                    document.MainForm.submit();

				}

				break;



			case 'restore':

				    if(confirm("确认批量还原吗?")) {

					document.MainForm.action = 'do_client.php?m=restorearr&sid='+sid;

                    document.MainForm.submit();

				}

				break;
				
			case 'outexcel':
			
					document.MainForm.action = 'client_excel.php';
					
                    document.MainForm.submit();
                    
				break;
			
			case 'all_outexcel':
			
					document.MainForm.action = 'client_excel.php?ty=all';
					
                    document.MainForm.submit();
                    
				break;		
					
			}	

			$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 

}

function delete_client(aid)
{
	var selectedid = chk();
	if(selectedid == ''){
		alert('请先选择您要删除的数据！');
	}else{	
		if(confirm('确认删除吗?'))
		{
	
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
	
			$.post("do_client.php",
	
				{m:"delete_all", ID: selectedid},
	
				function(data){
					data = Jtrim(data);
	
					if(data == "ok"){
	
						window.location.href = 'client.php';
	
						$.blockUI({ message: "<p>删除成功!</p>" }); 
						$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client.php');
					}else{
	
						$.blockUI({ message: "<p>"+data+"</p>" }); 
						$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client.php');
					}	
								
	
				}		
	
			);
	
		}else{
	
			return false;
	
		}

	}	
	window.setTimeout($.unblockUI, 2000); 
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function muledit_client(aid)
{
	var selectedid = chk();
	if(selectedid == ''){
		alert('请先选择您要更改的数据！');
	}else{	
		$("#windowtitle").html('批量修改经销商资料');
		$('#windowContent').html('<iframe src="muledit_client.php?s='+selectedid+'" width="100%" marginwidth="0" height="350" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
		$.blockUI({ 
			message: $('#windowForm6'),
			css:{ 
					width: '620px',height:'380px',top:'8%',margin:'auto',left:0,right:0
				}			
			});
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}	
}

function chk(){
  var obj=document.getElementsByName('selectedID[]');  
  var s='';
  for(var i=0; i<obj.length; i++){
    if(obj[i].checked) s+=obj[i].value+',';  //如果选中，将value添加到变量s中
  }
  return s;
}

function closewindowui()
{

	$.unblockUI();

}

function do_save_client()
{
	document.MainForm.referer.value = document.location;
	if($('#ClientName').val()=="" || $('#ClientPassword').val()=="" )
	{
		$.blockUI({ message: "<p>请输入登陆帐号和密码！</p>" });
	}else if($('#ClientCompanyName').val()==""){
		$.blockUI({ message: "<p>请输入经销商名称！</p>" });	

	}else if($('#ClientArea').val()=="" || $('#ClientArea').val()=="0"){
		$.blockUI({ message: "<p>请选择经销商所在地区！</p>" });		

	}else if($('#ClientTrueName').val()==""){
		$.blockUI({ message: "<p>请输入联系人！</p>" });	

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
		$.post("do_client.php?m=content_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					alert('保存成功!');
					window.location.href = 'client_add.php';
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else if(data == "okname"){
					$.blockUI({ message: "<p>请输入正确的用户名(可以是 1-18位 数字、字母、下划线组成)!</p>" });
					$("#ClientName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "okpass"){
					$.blockUI({ message: "<p>请输入正确的密码(可以是 3-18位 数字、字母、下划线组成)!</p>" });
					$("#ClientName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}else if(data == "repeat"){
					$.blockUI({ message: "<p>登陆名已存在，请使用另外的登陆名!</p>" });
					$("#ClientName")[0].focus();
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

function do_save_edit_client()
{
	document.MainForm.referer.value = document.location;
	var backlink = readcookie("backurl");
	if(backlink == "") backlink = "client.php";

	if($('#ClientName').val()=="")
	{
		$.blockUI({ message: "<p>请输入登陆帐号！</p>" });

	}else if($('#ClientCompanyName').val()==""){
		$.blockUI({ message: "<p>请输入经销商名称！</p>" });
	
	}else if($('#ClientArea').val()=="" || $('#ClientArea').val()=="0"){
		$.blockUI({ message: "<p>请选择经销商所在地区！</p>" });		

	}else if($('#ClientTrueName').val()==""){
		$.blockUI({ message: "<p>请输入联系人！</p>" });	

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
		$.post("do_client.php?m=content_edit_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = backlink;
				}else if(data == "okname"){
					$.blockUI({ message: "<p>请输入正确的用户名(可以是 1-18位 数字、字母、下划线组成)!</p>" });
					$("#ClientName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);

				}else if(data == "okpass"){
					$.blockUI({ message: "<p>请输入正确的密码(可以是 3-18位 数字、字母、下划线组成)!</p>" });
					$("#ClientName")[0].focus();
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);

				}else if(data == "repeat"){
					$.blockUI({ message: "<p>登陆名已存在，请使用另外的登陆名!</p>" });
					$("#ClientName")[0].focus();
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
		

function do_save_client_point(cid)
{
	if($('#point').val()=="")
	{
		$.blockUI({ message: "<p>请输入您要改变的分值！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
		$.post("do_client.php?m=content_point_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					alert('保存成功!');
					window.location.reload();
					$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
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

function delete_address(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_client.php",
			{m:"delete_address", ID: pid},
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
	window.setTimeout($.unblockUI, 2000); 
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}



function alert_uploading()
{
    $.blockUI({ message: "<p>正在上传，请稍候......</p>" });

    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
}

function setimprotmsg(msg,log_id)
{
    $('#showimportdata').html(msg);
    $("#log_id").val(log_id);
    $('#yz_div').show();
    $.unblockUI();
}

function subinportcontent()
{
    if(true){
        $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
        $.post("do_client.php?m=content_import_save", $("#MainFormImport").serialize(),
            function(data){
                data = Jtrim(data);
                if(data == "ok"){
                    $.blockUI({ message: "<p>保存成功!</p>" });

                    $('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
                    setTimeout(function(){
                        window.location.href = "client_import.php";
                    },300);

                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                    $('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
                }
            }
        );
    }
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);

}

/**
 * 检测经销商编码是否已存在
 * @param _this
 * @param id
 */
function check_client_no(_this,id) {
    var val = $(_this).val();
    $.post("do_client.php",{
        m : 'check_client_no',
        client_no : val,
        client_id : id
    },function(data){
        data = Jtrim(data);
        if(data != 'ok') {
            $("#client_no_unique").show();
        } else {
            $("#client_no_unique").hide();
        }
    },'text');
}

/**
 * 检测经销商名称是否已存在
 * @param _this
 * @param id
 */
function check_client_name(_this,id) {
    var val = $(_this).val();
    $.post("do_client.php",{
        m:'check_client_name',
        client_company_name: val,
        client_id : id
    },function(data) {
        data = Jtrim(data);
        if(data != 'ok') {
            $("#client_name_unique").show();
        } else {
            $("#client_name_unique").hide();
        }
    },'text');
}