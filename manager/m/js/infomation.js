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


function do_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
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
	window.setTimeout($.unblockUI, 1000); 
}

function do_restore(pid)
{
	if(confirm('确认还原吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
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
	window.setTimeout($.unblockUI, 1000); 
}

function do_quite_delete(pid)
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
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


 function going(o, sid)
 {
	document.MainForm.referer.value = document.location;

		switch(o){			
				
			case 'del':
				    if(confirm("确认批量删除吗?")) {
					document.MainForm.action = 'do_infomation.php?m=delarr&sid='+sid;
					document.MainForm.target = 'exe_iframe';
                    document.MainForm.submit();
				}
				break;	
				
			case 'quite_delete':
				    if(confirm("确认批量删除吗?")) {
					document.MainForm.action = 'do_infomation.php?m=quite_delete_arr&sid='+sid;
					document.MainForm.target = 'exe_iframe';
                    document.MainForm.submit();
				}
				break;

			case 'restore':
				    if(confirm("确认批量还原吗?")) {
					document.MainForm.action = 'do_infomation.php?m=restorearr&sid='+sid;
					document.MainForm.target = 'exe_iframe';
                    document.MainForm.submit();
				}
				break;

			case 'save':				
				    if($('#data_ArticleSort').val()==""){ 				
						$.blockUI({ message: "<p>请先选择信息所属栏目！</p>" }); 
					
					}else if($('#data_ArticleTitle').val()==""){ 
						$.blockUI({ message: "<p>请输入信息标题！</p>" });
					
					}else{
						document.MainForm.action = 'do_infomation.php?m=content_add_save';
						document.MainForm.target = 'exe_iframe';
						document.MainForm.submit();
					}
				break;

			case 'editsave':
				
				    if($('#data_ArticleSort').val()==""){ 				
						$.blockUI({ message: "<p>请先选择信息所属栏目！</p>" }); 
					
					}else if($('#data_ArticleTitle').val()==""){ 
						$.blockUI({ message: "<p>请输入信息标题！</p>" });
					
					}else{
						document.MainForm.action = 'do_infomation.php?m=content_edit_save';
						document.MainForm.target = 'exe_iframe';
						//alert(document.MainForm.action);
						document.MainForm.submit();
					}
				break;	


			}
			$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}


/////////////******** sort **********////

function do_save_sort()
{
	if($('#data_SortName').val()=="")
	{
		$.blockUI({ message: "<p>请输入栏目名称！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
			{m:"save_sort", SortName: $('#data_SortName').val(), SortOrder: $('#data_SortOrder').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('#data_SortName').val('');
					$('#data_SortOrder').val('0');
					$('.blockOverlay').attr('title','点击继续增加!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
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
		$.blockUI({ message: "<p>请输入栏目名称!</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
			{m:"save_edit_sort",SortID:$('#edit_SortID').val(), SortName: $('#edit_SortName').val(), SortOrder: $('#edit_SortOrder').val()},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>修改成功,正在载入页面...</p>" });
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}

function do_delete_sort()
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{

		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
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
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
	}
}

function upload_file(fildname)
{
	$('#windowContent').html('<iframe src="../plugin/jqUploader/uploadfileall.php" width="500" marginwidth="0" height="250" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'250px',top:'18%'
            }			
		});
    $('#set_filename').val(fildname);
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function closewindowui()
{
	$.unblockUI();
}

function setinputfile(fpn)
{	
	var filevalue = $('#set_filename').val();
	if(fpn!='' && fpn!=null)
	{
		$("#"+filevalue).val(fpn);
		//$("#"+filevalue+"_text").html('[<a href="'+fpn+'" target="_blank">预览图片</a>]');
	}	
	$.unblockUI();
}


function upload_img(fildname)
{
	$('#windowContent').html('<iframe src="../plugin/jqUploader/uploadfile.php" width="500" marginwidth="0" height="250" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'250px',top:'18%'
            }			
		});
    $('#set_filename').val(fildname);
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function setinputimg(fpn)
{	
	var filevalue = $('#set_filename').val();
	if(fpn!='' && fpn!=null)
	{
		fpn = fpn.replace('thumb_','img_');
		$("#"+filevalue).val(fpn);
		$("#"+filevalue+"_text").html('[<a href="'+(window.resourceUrl ? window.resourceUrl+fpn : fpn)+'" target="_blank">预览图片</a>]');
	}	
	$.unblockUI();
}

function setinputok(ty)
{
	if(ty == "ok")
	{
		$.blockUI({ message: "<p>提交成功!</p>" });
		$('#data_ArticleTitle').val('');
		$('#data_ArticleColor').val('');
		$('#data_ArticleAuthor').val('');
		$('#data_ArticlePicture').val('');
		$('#data_ArticleFileName').val('');
		SetContents();
		$('#data_ArticleOrder').val('0');

		$('.blockOverlay').attr('title','点击继续添加信息').click($.unblockUI);
		window.setTimeout($.unblockUI, 3000);
	}else{
		$.blockUI({ message: "<p>提交不成功!</p>" });
		$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	}	 
		 
}

function setinputeditok(ty,tourl)
{
	if(ty == "ok")
	{
		$.blockUI({ message: "<p>提交成功!</p>" });
		window.location.href = tourl;
		window.setTimeout($.unblockUI, 1000);
	}else{
		$.blockUI({ message: "<p>提交不成功!</p>" });
		$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	}	 
}

function SetContents()
{
	// Get the editor instance that we want to interact with.
	var oEditor = CKEDITOR.instances.editor1;
	var value = '';
	// Set the editor contents (replace the actual one).
	oEditor.setData( value );
}

function do_change_order(oid,iid)
{
	var nid = $("#order_"+iid).val();
	if(oid!=nid)
	{
		//$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
			{m:"update_order", ID: iid, orderid: nid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					//$.blockUI({ message: "<p>修改成功!</p>" });
					$.growlUI("修改成功!");
				}else{
					//$.blockUI({ message: "<p>"+data+"</p>" });
					$.growlUI(""+data+"");
				}
			}
		);
	}else{
		return false;
	}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000);
}

function go_select_color(form, form_element) 
{
//	var arr = window.showModalDialog("../plugin/color/color.htm","color","dialogWidth:200pt;dialogHeight:175pt;help:0;status:0");
//	var arr = window.open("../plugin/color/color.htm","color","width=350px,height=235px");
//	if(arr != null && arr != 'undefined') {
//		var MSG =  arr;
//		with(form){
//			eval(form_element + ".value= '" + MSG + "'")
//		}		
//  	}
	
	var hw = window.screen.width;
	var leftp = 0;
	leftp = hw/2 - 300;
	$('#windowContent6').html('选择颜色');
	$('#windowForm6').css("width","360px");
	$('#windowForm6').css("height","235px");
	$('#windowContent6').html('<iframe src="../plugin/color/color.htm" width="360px" marginwidth="0" height="235px" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '360px',height:'275px',top:'8%',left:leftp
            }			
		});
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

/***********************************/
function save_xd()
{
			if($('#data_ArticleSort').val()==""){ 				
				$.blockUI({ message: "<p>请先选择所属栏目！</p>" }); 
			
			}else if($('#data_ArticleName').val()==""){ 
				$.blockUI({ message: "<p>请输入名称！</p>" });

			}else if($('#data_ArticlePicture').val()==""){ 
				$.blockUI({ message: "<p>请上传图片！</p>" });
						
			}else{
				document.MainForm.action = 'do_infomation.php?m=xd_add_save';
				document.MainForm.target = 'exe_iframe';
				document.MainForm.submit();
			}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000);
}


function setinput_xd_ok(ty)
{
	if(ty == "ok")
	{
		$.blockUI({ message: "<p>提交成功!</p>" });
		$('#data_ArticleName').val('');
		$('#data_ArticlePicture').val('');
		$('#data_ArticleLink').val('');
		$('#data_ArticleContent').val('');
		$('#data_ArticleOrder').val('0');

		$('.blockOverlay').attr('title','点击继续添加信息').click($.unblockUI);
		window.setTimeout($.unblockUI, 3000);
	}else{
		$.blockUI({ message: "<p>提交不成功!</p>" });
		$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	}		 
}

function save_edit_xd()
{
			if($('#data_ArticleSort').val()==""){ 				
				$.blockUI({ message: "<p>请先选择所属栏目！</p>" }); 
			
			}else if($('#data_ArticleName').val()==""){ 
				$.blockUI({ message: "<p>请输入名称！</p>" });

			}else if($('#data_ArticlePicture').val()==""){ 
				$.blockUI({ message: "<p>请上传图片！</p>" });
						
			}else{
				document.MainForm.action = 'do_infomation.php?m=xd_edit_save';
				document.MainForm.target = 'exe_iframe';
				document.MainForm.submit();
			}
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000);
}

function do_xd_delete(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_infomation.php",
			{m:"xd_delete", ID: pid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					$("#"+delline+"_1").hide();
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