
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 


function CheckAll(form)
{
	for (var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if (e.name != 'chkall' && e.name !='copy') e.checked = form.chkall.checked; 
	}
}

function closewindowui()
{
	$.unblockUI();
	//window.setTimeout($.unblockUI, 1000);
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


var maxlen=60;
function smsCount(frm, maxlimit) {	
	var chrlen=0;
	chrlen=frm.Msg.value.length;
//	if (chrlen > (maxlimit*maxlen)) {
//		frm.Msg.value = frm.Msg.value.substring(0, maxlimit*maxlen);
//		alert("您最多可以同时发"+maxlimit+"条短信("+(maxlimit*maxlen)+"个字符)");
//	}
	chrlen=frm.Msg.value.length;
	frm.chrLen.value=chrlen;
	frm.smsLen.value=Math.ceil(chrlen/maxlen);
} 

function addphone() {
	eval("obj = document.newsms.postlistno");
	with(obj) {
		length=obj.length;
		opval = document.getElementById('mobileno').value;
		if(opval.length == 11)
		{	
			options[length]=new Option(opval,opval);
			document.getElementById('mobileno').value = "";
			document.getElementById('mobileno').focus();
		}else{
			alert('请输入正确的号码！');
			document.getElementById('mobileno').focus();
		}
		showlength=obj.length;
		$("#shownumberlength").html(showlength);
	}	
}

function insertphone(opval) 
{
	eval("obj = document.newsms.postlistno");
	with(obj) {
		length=obj.length;
		opval = Jtrim(opval);

		if(opval.length > 11)
		{
			opvalarr	= opval.split("\n");
			var alength = opvalarr.length;
			if(alength<2)
			{
				opvalarr = opval.split(",");
				alength  = opvalarr.length;
			}
			if(alength<2)
			{
				opvalarr = opval.split("，");
				alength  = opvalarr.length;
			}

			if(alength>1)
			{
				for(i=0;i<alength;i++)
				{
					opvalarr[i] = Jtrim(opvalarr[i]);
					if(opvalarr[i].length == 11)
					{	
						options[length] = new Option(opvalarr[i],opvalarr[i]);
					}
				}
			}
		}else{
			if(opval.length==11) options[length] = new Option(opval,opval);
		}
		var showlength=obj.length;
		$("#shownumberlength").html(showlength);
	}
	closewindowui();
}

function selectphone(opval) 
{
	eval("obj = document.newsms.postlistno");
	with(obj) {
		length=obj.length;
		opvalarr = opval.split(",");
		alength  = opvalarr.length;

		if(alength>0)
		{
			for(i=0;i<alength;i++)
			{
				if(opvalarr[i].length == 11)
				{	
					options[length]=new Option(opvalarr[i],opvalarr[i]);
				}
			}
		}
		var showlength=obj.length;
		$("#shownumberlength").html(showlength);
	}	
}

function delphone() 
{	
	eval("obj1 = document.newsms.postlistno");
	with(obj1)
	{
		options[selectedIndex]=null;
		//selectedIndex=length-1;
		var showlength=obj1.length;
		$("#shownumberlength").html(showlength);
	}
}

function clearphone() 
{
       var obj = document.getElementById("postlistno");
       var count = obj.options.length;
       for(var i = 0;i<count;i++){
               obj.options.remove(0);//每次删除下标都是0
       }
		$("#shownumberlength").html('0');
}

function clearrepeat() 
{
       var obj = document.getElementById("postlistno");
       var count = obj.options.length;
	   var j=0;
       for(var i=count-1; i > 0;i--)
	   {
		   for(j=0; j<i; j++)
		   {
			  if(obj.options[j].value == obj.options[i].value)
			   {
				  obj.options[i] = null;
				  continue;
			   }
		   }
       }
		var showlength=obj.length;
		$("#shownumberlength").html(showlength);
}

function setContentLinkValue(fieldName)
{
	var obj = document.getElementById("postlistno");
 	var returnValue;

	with(obj) {
 		for(i=0; i <  obj.length ; i++){
			if(i==0) {
				returnValue = options[i].value;
			} else {
				returnValue = returnValue + ';' + options[i].value;
			}
 		} 		
	}
	if(returnValue == 'undefined') {
		returnValue = '';
	}
 	return returnValue;
}


function insertmulphone()
{	
	$('#windowContent').html('<iframe src="phoneinsert.php" width="500" marginwidth="0" height="380" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('批量号码录入');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'410px',top:'2%'
            }			
		});
	$('#windowForm').css("width","500px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function insert_phonebook_phone()
{
	var setclientv = setContentLinkValue();
	$('#windowContent').html('<iframe src="select_phonebook.php?stype=client&selectid='+setclientv+'" width="620" marginwidth="0" height="418" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
	$("#windowtitle").html('选择您要发送的联系人');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '620px',height:'450px',top:'2%'
            }			
		});
	$('#windowForm').css("width","620px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function set_add_phonebook(htmlmsg) 
{
	$("#postlistno").append(htmlmsg);
	eval("obj = document.newsms.postlistno");
	var showlength=obj.length;
	$("#shownumberlength").html(showlength);
}

function insert_template_select()
{
	var setclientv = setContentLinkValue();
	$('#windowContent').html('<iframe src="select_template.php" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('选择您要插入的模板');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '620px',height:'445px',top:'2%'
            }			
		});
	$('#windowForm').css("width","620px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function insert_template(templatemsg)
{
	$("#Msg").val(templatemsg);
	closewindowui();
	eval("objform = document.newsms");
	smsCount(objform,240);
}

function sentmsg()
{
	var postphone = setContentLinkValue('postlistno');
	var companyflag = "0";
	var sendtimemsg = "";
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
	
	if(postphone=="" || postphone==undefined  || postphone=="undefined")
	{
		$.blockUI({ message: "<p>请输入您要发送的手机号码!</p>" }); 
	}else if($("#Msg").val()==""){
		$.blockUI({ message: "<p>请输入您要发送的内容!</p>" });
	}
	else
	{			
			$('#postmsgbutton').attr("disabled","disabled");
			$.post("do_sms.php",
				{m:"PostMsg",PhoneList:postphone, Msg: $("#Msg").val()},
				function(data){
					data = Jtrim(data);
					if(data == "ok"){
						$.blockUI({ message: "<p>发送成功...</p>" });
						clearphone();
						$("#Msg").val('');
						set_sms_number();						
						window.setTimeout($.unblockUI, 2000);
						$('#postmsgbutton').removeAttr('disabled');
						$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
					}else{
						$.blockUI({ message: "<p>"+data+"</p>" });
						$('#postmsgbutton').removeAttr('disabled');
						$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
					}
				}			
			);
	}
	//$('#postmsgbutton').removeAttr('disabled');
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}


function set_sms_number()
{
		$.post("do_sms.php",
			{m:"set_sms_number"},
			function(data){
				data = Jtrim(data);
				if(data.length < 8){
					$("#sms_number_id").html(data);
				}		
			}
		);
}

function view_sms_info(sid)
{	
	$('#windowContent').html('<iframe src="sms_view.php?ID='+sid+'" width="500" marginwidth="0" height="420" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('批量号码录入');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'450px',top:'8%'
            }			
		});
		$('#windowForm').css("width","500px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}


/////////////******** sort **********////

function do_save_sort()
{
	if($('#data_SortName').val()=="")
	{
		$.blockUI({ message: "<p>请输入分组名称！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php",
			{m:"save_sort", SortName: $('#data_SortName').val(), SortOrder: $('#data_SortOrder').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);	
}


function set_edit_sort(SiteID,SiteName,SiteOrder)
{
	if(SiteID != "")
	{
		$('#edit_SortID').val(SiteID);
		$('#edit_SortName').val(SiteName);
		$('#edit_SortOrder').val(SiteOrder);
	}		 
}

function do_save_edit_sort()
{
	if($('#edit_SortName').val()=="")
	{
		$.blockUI({ message: "<p>请输入分组名称!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php",
			{m:"save_edit_sort",SortID:$('#edit_SortID').val(), SortName: $('#edit_SortName').val(), SortOrder: $('#edit_SortOrder').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>修改成功,正在载入页面...</p>" });
					window.setTimeout(window.location.reload(), 1000);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}

function do_delete_sort()
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php",
			{m:"delete_sort",SortID:$('#edit_SortID').val()},
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
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
	}
}

function add_line()
{
	window.location.href = 'sms_phonebook_add.php?linenumber='+$("#linenumber").val();
}

function add_phonebook_save()
{
	if($('#data_PhoneSort').val()=="")
	{
		$.blockUI({ message: "<p>请选择所属分组!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php?m=phonebook_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('sms_phonebook_add.php'), 3000);
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 3000);
}

function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}

function do_phonebook_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php",
			{m:"phonebook_delete", ID: pid},
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

function edit_phonebook_info(id)
{	
	$('#windowContent').html('<iframe src="sms_phonebook_edit.php?ID='+id+'" width="500" marginwidth="0" height="270" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'300px',top:'18%'
            }			
		});
		$('#windowForm').css("width","500px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}


/////////////********Template sort **********////

function do_save_template_sort()
{
	if($('#data_SortName').val()=="")
	{
		$.blockUI({ message: "<p>请输入分组名称！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php",
			{m:"save_template_sort", SortName: $('#data_SortName').val(), SortOrder: $('#data_SortOrder').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 3000);	
}


function set_edit_template_sort(SiteID,SiteName,SiteOrder)
{
	if(SiteID != "")
	{
		$('#edit_SortID').val(SiteID);
		$('#edit_SortName').val(SiteName);
		$('#edit_SortOrder').val(SiteOrder);
	}		 
}

function do_save_edit_template_sort()
{
	if($('#edit_SortName').val()=="")
	{
		$.blockUI({ message: "<p>请输入分组名称!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php",
			{m:"save_edit_template_sort",SortID:$('#edit_SortID').val(), SortName: $('#edit_SortName').val(), SortOrder: $('#edit_SortOrder').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>修改成功,正在载入页面...</p>" });
					window.setTimeout(window.location.reload(), 1000);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}

function do_delete_template_sort()
{
	if($('#edit_SortID').val()=="")
	{
		$.blockUI({ message: "<p>请先选择您要删除的分类!</p>" });
	}else{	
		if(confirm('确认彻底删除吗?此操作不可还原!'))
		{
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			$.post("do_sms.php",
				{m:"delete_template_sort",SortID:$('#edit_SortID').val()},
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
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
		window.setTimeout($.unblockUI, 2000);
}

function add_template_save()
{
	if($('#data_TemplateSort').val()=="")
	{
		$.blockUI({ message: "<p>请选择所属模板分类!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php?m=template_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(jumpurl('sms_template_add.php'), 3000);
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 3000);
}

function add_template_line()
{
	window.location.href = 'sms_template_add.php?linenumber='+$("#linenumber").val();
}


function do_template_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_sms.php",
			{m:"template_delete", ID: pid},
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

function edit_template_info(id)
{	
	$('#windowContent').html('<iframe src="sms_template_edit.php?ID='+id+'" width="500" marginwidth="0" height="270" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'300px',top:'18%'
            }			
		});
	$('#windowForm').css("width","500px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}


 function going(o)
 {
	document.MainForm.referer.value = document.location;
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		switch(o){				
			case 'template_del':
				    if(confirm("确认批量删除吗?")) {
					document.MainForm.action = 'do_sms.php?m=del_template_arr';
					document.MainForm.target = 'exe_iframe';
                    document.MainForm.submit();
				}
				break;				
			case 'phonebook_del':
				    if(confirm("确认批量删除吗?")) {
					document.MainForm.action = 'do_sms.php?m=del_phonebook_arr';
					document.MainForm.target = 'exe_iframe';
                    document.MainForm.submit();
				}
				break;

			$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
		}
 }