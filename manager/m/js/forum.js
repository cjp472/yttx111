
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
		$.post("do_forum.php",
			{m:"delete", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					var delline = "reply_" + pid;
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

function do_delete_all(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"delete_all", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					window.location.href='forum.php';
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

function do_delete_list(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"delete_all", ID: pid},
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

/**************************/
function SubmitReply(){
	
	if($("#replycontent").val()=="" || $("#replyname").val()=="")
	{
		$("#allertidtext").html('呢称 / 内容 不能为空!');
		$("#allertidtext").animate({opacity: 'show'}, 'slow');	

	}else{
			$.post("do_forum.php",
				{m:"submitreply", pid:$("#replypid").val(), replycontent: $("#replycontent").val(), replyname: $("#replyname").val()},
				function(data){
					data = Jtrim(data);
					if(data == "Error"){
						$("#allertidtext").html('提交不成功！');
						$("#allertidtext").animate({opacity: 'show'}, 'slow');
					}else{
						$("#replyinput_").animate({opacity: 'show'}, 'slow');
						$("#replyinput_").html(data);
					}
				}			
			);
	}
}

//////////////******** tool **********////

function SubmitTool()
{
	if($('#ToolName').val()=="" || $('#ToolNO').val()=="")
	{
		$.blockUI({ message: "<p>请入 名称 和 帐号</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"save_tool", ToolType: $('#ToolType').val(), ToolName: $('#ToolName').val(), ToolNO: $('#ToolNO').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href="forum_tool.php";
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}


function do_delete_tool(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"delete_tool", ID: pid},
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


function do_set_edit_tool(eid,etype,ename,eno)
{
	//$("#edittools").show();
	document.getElementById("edittools").style.display = 'block';
	$("#edit_ToolID").val(eid);
	$("#edit_ToolType").val(etype);
	$("#edit_ToolName").val(ename);
	$("#edit_ToolNO").val(eno);	
}

function CancelEditTool()
{
	$("#edittools").hide();
}

function SubmitEditTool()
{
	if($('#edit_ToolName').val()=="" || $('#edit_ToolNO').val()=="")
	{
		$.blockUI({ message: "<p>请入 名称 和 帐号</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"save_edit_tool", ToolID: $('#edit_ToolID').val(), ToolType: $('#edit_ToolType').val(), ToolName: $('#edit_ToolName').val(), ToolNO: $('#edit_ToolNO').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href="forum_tool.php";
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}

//////////////******** contact **********////

function SubmitContact()
{
	if($('#ContactName').val()=="" || $('#ContactValue').val()=="")
	{
		$.blockUI({ message: "<p>请入 名称 和 内容</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"save_contact", ContactName: $('#ContactName').val(), ContactValue: $('#ContactValue').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href="forum_contact.php";
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}


function do_delete_contact(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"delete_contact", ID: pid},
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

function closewindowui()
{
	$.unblockUI();
}

function do_set_edit_contact(eid,ename,evalue)
{
	$("#editcontact").show();
	$("#edit_ContactID").val(eid);
	$("#edit_ContactName").val(ename);
	$("#edit_ContactValue").val(evalue);	
}

function CancelEditContact()
{
	$("#editcontact").hide();
}

function SubmitEditContact()
{
	if($('#edit_ContactName').val()=="" || $('#edit_ContactValue').val()=="")
	{
		$.blockUI({ message: "<p>请入 名称 和 内容</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_forum.php",
			{m:"save_edit_contact",ContactID: $('#edit_ContactID').val(), ContactName: $('#edit_ContactName').val(), ContactValue: $('#edit_ContactValue').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href="forum_contact.php";
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}
