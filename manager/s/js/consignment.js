
/*********/

var old_bg="";
function inStyle(obj)
{
    old_bg=obj.style.background;
	obj.style.background="#edf3f9";
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 

function outStyle(obj)
{
    obj.style.background=old_bg;
}

function CheckAll(form)
{
	  for (var i=0;i<form.elements.length;i++)
	  {
			var e = form.elements[i];
			if (e.name != 'chkall' && e.name !='copy')       e.checked = form.chkall.checked; 
	   }
}


function closewindowui()
{
	$.unblockUI();
}

function setSendFlag(cid)
{
	if(confirm('确定货物已签收吗?'))
	{		
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_consignment.php",
			{m:"setSendFlag", ConsignmentID: cid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					var delline = "setflagline_" + cid;
					var dellinel = "setflagline_link_" + cid;
					$("#"+delline).html('已签收');
					$("#"+dellinel).html('<a href="consignment_content.php?ID='+cid+'"  target="_blank" >查看发货单</a>');
					$.blockUI({ message: "<p>设置成功!</p>" }); 
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


function jumpurl(urlmsg)
{
	window.location.href = urlmsg;
}