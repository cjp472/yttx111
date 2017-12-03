function SubmitForum()
{
	$("#showtext").animate({opacity: 'show'}, 'slow');
	$("#showtext").html('正在提交数据，请稍候...');
	if($("#forumname").val()=="" || $("#forumtitle").val()=="" || $("#froumcontent").val()=="")
	{
		$("#showtext").html('呢称 / 标题 / 内容 不能为空!');
		$("#showtext").animate({opacity: 'show'}, 'slow');	

	}else{
		$('#submitbtn').attr("disabled","disabled");
		$.post("forum.php",
			{m:"submitforum", forumname: $("#forumname").val(), forumtitle: $("#forumtitle").val(), froumcontent: $("#froumcontent").val()},
			function(data){
				data = Trim(data);
				if(data == "ok"){
					$("#showtext").html('发表成功，正在转入列表页面...');
					$("#showtext").animate({opacity: 'show'}, 'slow');
					window.location.reload();
				}else{
					$("#showtext").html(data);
					$("#showtext").animate({opacity: 'show'}, 'slow');
					$('#submitbtn').removeAttr('disabled');
				}
			}			
		);
	}
}


function SubmitReply(){
	
	if($("#replycontent").val()=="" || $("#replyname").val()=="")
	{
		$.growlUI('呢称 / 内容 不能为空!');

	}else{
		$('#replybuttom').attr("disabled","disabled");
		$.post("forum.php",
			{m:"submitreply", pid:$("#replypid").val(), replycontent: $("#replycontent").val(), replyname: $("#replyname").val()},
			function(data){
				data = Trim(data);
				if(data == "Error"){
					$.growlUI('提交不成功！');
					$('#replybuttom').removeAttr('disabled');
				}else{
					$("#replyinput_"+$("#replypid").val()).html(data);
					$('#replybuttom').removeAttr('disabled');
				}
			}			
		);
	}
}


function jumpurl(tourl)
{
	window.location.href = tourl;
}

function showreply(divid)
{
	CancelReply();
	$("#replyinput_"+divid).html($("#replayinputtext").html());
	$("#replypid").val(divid);
	$("#replyinput_"+divid).animate({opacity: 'show'}, 'slow');
}

function CancelReply()
{
	var idmsg = '';
	idmsg = $("#replypid").val();
	$("#replyinput_"+idmsg).html('');
	$("#replyinput_"+idmsg).animate({opacity: 'hide'}, 'slow');
}

function Trim(str)
{ 
	return str.replace(/^\s*|\s*$/g,""); 
}

function showpostforum()
{
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '540px',height:'370px',top:'18%'
            }			
		});
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function closewindowui()
{
	$.unblockUI();
}