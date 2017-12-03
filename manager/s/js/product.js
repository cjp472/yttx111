
//去空隔函数 
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 

function CheckAll(form)
{
	for(var i=0;i<form.elements.length;i++)
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
	if(confirm('确认下架吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"delete", ID: pid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){
					var delline = "line_" + pid;
					$("#"+delline).hide();
					$.blockUI({ message: "<p>下架成功!</p>" }); 
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
	if(confirm('确认上架吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
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

function do_quite_delete(pid,sid)
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"quite_delete", ID: pid, SiteID:sid},
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
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
	window.setTimeout($.unblockUI, 1000); 
}

function do_change_order(oid,iid)
{
	var nid = $("#order_"+iid).val();
	if(oid!=nid)
	{
		$.post("do_product.php",
			{m:"update_order", ID: iid, orderid: nid},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.growlUI("修改成功!");
				}else{
					$.growlUI(""+data+"");
				}				
			}		
		);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 1000);
}

 function going(o, sid)
 {
	document.MainForm.referer.value = document.location; 

		switch(o) {
 
			case 'update':
					document.MainForm.action = 'do_product.php?m=update_order';
                    document.MainForm.submit();
				break;		
				
			case 'move':
					var targetNodeID = showModalDialog("select_product.php","color","dialogWidth:340px;dialogHeight:340px;help:0;status:0;scroll:no");
			
					if(targetNodeID != null && targetNodeID != '') {
						document.MainForm.action = 'do_product.php?m=moveContent&SiteID='+sid+'&targetNodeID=' + targetNodeID;
						document.MainForm.submit();
					}
				break;
				
			case 'save':				
				   
					$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
					if($('#data_SiteID').val()==""){ 				
						$.blockUI({ message: "<p>请先选择商品分类！</p>" }); 
					
					}else if($('#data_Name').val()==""){ 
						$.blockUI({ message: "<p>请输入商品名称！</p>" });
					
					}else if($('#data_Price1').val()=="" && $('#data_Price2').val()==""){ 
						$.blockUI({ message: "<p>请输入商品价格！</p>" });		
					
					}else if($('#data_Units').val()==""){ 
						$.blockUI({ message: "<p>请输入计量单位！</p>" });		
					
					}else{
						document.MainForm.Shield.value = setContentLinkValue();
						document.MainForm.action = 'do_product.php?m=content_add_save&sid='+sid;
						document.MainForm.submit();
					}
				break;	

			case 'editsave':
					$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
				    if($('#data_SiteID').val()==""){ 				
						$.blockUI({ message: "<p>请先选择商品分类！</p>" }); 
					
					}else if($('#data_Name').val()==""){ 
						$.blockUI({ message: "<p>请输入商品名称！</p>" });
					
					}else if($('#data_Price1').val()=="" && $('#data_Price2').val()==""){ 
						$.blockUI({ message: "<p>请输入商品价格！</p>" });		
					
					}else if($('#data_Units').val()==""){ 
						$.blockUI({ message: "<p>请输入计量单位！</p>" });		
					
					}else{
						document.MainForm.Shield.value = setContentLinkValue();
						document.MainForm.action = 'do_product.php?m=content_edit_save&sid='+sid;
						document.MainForm.submit();
					}
				break;	

				
			case 'del':
				    if(confirm("确认批量下架吗?")) {
					document.MainForm.action = 'do_product.php?m=delarr&sid='+sid;
                    document.MainForm.submit();
				}
				break;	
				
			case 'quite_delete':
				    if(confirm("确认批量删除吗?")) {
					document.MainForm.action = 'do_product.php?m=quite_delete_arr&sid='+sid;
					document.MainForm.target = 'exe_iframe';
                    document.MainForm.submit();
				}
				break;

			case 'restore':
				    if(confirm("确认批量上架吗?")) {
					document.MainForm.action = 'do_product.php?m=restorearr&sid='+sid;
                    document.MainForm.submit();
				}
				break;

			case 'commend':
					document.MainForm.action = 'do_product.php?m=update_commend';
                    document.MainForm.submit();
				break;

			case 'outexcel':
					document.MainForm.action = 'product_excel.php';
                    document.MainForm.submit();
				break;	
			}

			$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

/****************************/
function upload_mu_img(fildname)
{	
	$('#windowContent').html('<iframe src="../plugin/SWFUpload/upimg.php" width="500" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('上传图片');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'470px',top:'2%'
            }			
		});
	$('#windowForm').css("width","500px");
    $('#set_filename').val(fildname);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function set_mu_img()
{
		$.post("do_upload.php",
			{m:"set_upload_mu_img"},
			function(data){
				data = Jtrim(data);
				if(data != ""){
					$("#show_thumb_mu_img").html(data);
				}				
			}	
		);
	$.unblockUI();
}

function remove_up_img(arrkey)
{
	var img_id = "mu_img_id_"+arrkey;
	$('#'+img_id).remove();
	$.post("do_upload.php",
		{m:"remove_upload_mu_img",rkey: arrkey},
		function(data){

		}		
	);		
}

/***********************/
function upload_file(fildname)
{
	$("#windowtitle").html('上传图片');
	$('#windowContent').html('<iframe src="../plugin/jqUploader/uploadfile.php" width="500" marginwidth="0" height="220" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px'
            }			
		});
	$('#windowForm').css("width","500px");
    $('#set_filename').val(fildname);
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function closewindowui()
{
	$.unblockUI();
	//window.setTimeout($.unblockUI, 1000);
}

function setinputfile(fpn)
{
	var filevalue = $('#set_filename').val();
	if(fpn!='' && fpn!=null)
	{
		$("#"+filevalue).val(fpn);
		$("#"+filevalue+"_text").html('[<a href="../resource/'+fpn+'" target="_blank">预览图片</a>]');
	}	
	$.unblockUI();
}


function setinputok(ty)
{
	if(ty == "ok")
	{
		$.blockUI({ message: "<p>商品保存成功，点击继续添加商品!</p>" });
		$('input').val('');
		SetContents();

		$('#data_OrderID').val('500');
		$('#Package').val('0');

		$("#show_thumb_mu_img").html('<input type="text" name="data_Picture" id="data_Picture" tabindex="8"  />');
		$("#product_commendtype").html('&nbsp;<input name="data_CommendID" type="radio" value="0" checked="checked"  /> 默认&nbsp;&nbsp; <input name="data_CommendID" type="radio" value="1"  /> 推荐&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="2"  title="特价商品不执行药店折扣" /> 特价&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="3"  /> 新款&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="4" /> 热销&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="8"  /> 赠品&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="9"  /> 缺货');
		
		$('#newbutton').val('等级价格');
		$('#saveproductid').val('保 存');
		$('#resetproductid').val('重 置');
		$('#backid').val('返 回');
		$('#saveproductid2').val('保 存');
		$('#resetproductid2').val('重 置');
		$('#backid2').val('返 回');

		$('#shield_b1').val('删除');
		$('#shield_b2').val('清空');
		$('#shield_b4').val('选择');
		clear_client();

		$('.blockOverlay').attr('title','点击继续添加商品').click($.unblockUI);
		window.setTimeout($.unblockUI, 3000);
	}else{
		$.blockUI({ message: "<p>商品提交不成功!</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	}		 
}

function setinputeditok(ty,tourl)
{
	if(ty == "ok")
	{
		$.blockUI({ message: "<p>商品资料修改成功!</p>" });
		window.location.href = tourl;
		window.setTimeout($.unblockUI, 1000);
	}else{
		$.blockUI({ message: "<p>商品资料提交不成功!</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	}		 
}


function do_save_sort()
{
	if($('#data_ParentID').val()=="")
	{
		$.blockUI({ message: "<p>请先选择上级商品分类！</p>" });

	}else if($('#data_SiteName').val()==""){
		$.blockUI({ message: "<p>请输入商品分类名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"save_sort", ParentID: $('#data_ParentID').val(), SiteName: $('#data_SiteName').val(), SiteOrder: $('#data_SiteOrder').val(), Content: $('#data_Content').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href="product_sort.php?sid="+$('#data_ParentID').val();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}


function set_edit_sort(SiteID,ParentID,SiteName,SiteOrder,Content)
{
	if(SiteID != "")
	{
		$('#edit_SiteID').val(SiteID);
		$('#edit_ParentID').val(ParentID);
        //$('#edit_ParentID').select2();
		$('#edit_SiteName').val(SiteName);
		$('#edit_SiteOrder').val(SiteOrder);
		$('#edit_Content').val(Content);
	}		 
}

function do_save_edit_sort()
{
	if($('#edit_ParentID').val()=="")
	{
		$.blockUI({ message: "<p>请先选择上级商品分类！</p>" });

	}else if($('#edit_SiteName').val()==""){
		$.blockUI({ message: "<p>请输入商品分类名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"save_edit_sort",SiteID:$('#edit_SiteID').val(), ParentID: $('#edit_ParentID').val(), SiteName: $('#edit_SiteName').val(), SiteOrder: $('#edit_SiteOrder').val(), Content: $('#edit_Content').val()},
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
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
}


function do_delete_sort()
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"delete_sort",SiteID:$('#edit_SiteID').val()},
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
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
}

function SetContents()
{
	// Get the editor instance that we want to interact with.
	var oEditor = CKEDITOR.instances.editor1;
	var value = '';
	// Set the editor contents (replace the actual one).
	oEditor.setData( value );
}

/***************** 通知 *******************/
function do_notice_message(nid)
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"notice_message",ID:nid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){					
					$.blockUI({ message: "<p>操作成功,正在载入页面...</p>" });
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
				}				
			}		
		);

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
}

function do_notice_delete(nid)
{
	if(confirm('您确认要删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"quite_notice_delete",ID:nid},
			function(data){
			data = Jtrim(data);
				if(data == "ok"){					
					$.blockUI({ message: "<p>操作成功,正在载入页面...</p>" });
					window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 2000);
}


 function notice_going(o, cid)
 {	
	document.MainForm.referer.value = document.location;
	switch(o){		
		case 'notice':
				    if(confirm("确认批量通知吗?")) {
					document.MainForm.action = 'do_product.php?m=notice_message_arr&cid='+cid;
                    document.MainForm.submit();
				}
				break;	
				
		case 'del':
				    if(confirm("确认批量删除吗?")) {
					document.MainForm.action = 'do_product.php?m=delete_notice_arr&cid='+cid;
					document.MainForm.target = 'exe_iframe';
                    document.MainForm.submit();
				}
				break;
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function hideshow(divid,ty)
{
	if(ty=='show')
	{
		$("#"+divid).animate({opacity: 'show'}, 'slow');
	}else{
		$("#"+divid).animate({opacity: 'hide'}, 'fast');
	}
}

function set_units_val(valmsg)
{
	$("#data_Units").val(valmsg);
	hideshow('units_div','hide');
}

/************ client price ********************/
function set_level_price(fildname)
{
	$('#windowContent').html('<iframe src="set_product_level_price.php?vmsg='+$('#data_Price3').val()+'" width="500" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('药店等级价格设置');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'440px',top:'10%'
            }			
		});
		$('#windowForm').css("width","500px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function set_input_price(varmsg)
{
	$('#data_Price3').val(varmsg);
	closewindowui();
}

/**********************************/
function set_shield_client()
{
	var setclientv = setContentLinkValue();
	$('#windowContent').html('<iframe src="select_shield_client.php?selectid='+setclientv+'" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('选择被屏蔽的药店');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '620px',height:'445px',top:'2%'
            }			
		});
	$('#windowForm').css("width","620px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function add_client(param_index_id,param_title) 
{
	if(param_title!='')
	{
		eval("obj = document.MainForm.selectshield");
		with(obj) {
			length = obj.length;
			options[length]=new Option(param_title,param_index_id)	;	
		}
	}
}

function set_add_client(htmlmsg) 
{
	$("#selectshield").append(htmlmsg);
}

function del_client() 
{	
	eval("obj = document.MainForm.selectshield");
	with(obj)
	{
		options[selectedIndex]=null;
	}
}

function clear_client() 
{
       eval("obj = document.MainForm.selectshield");
       var count = obj.options.length;
       for(var i = 0;i<count;i++){
             obj.options.remove(0);//每次删除下标都是0
       }
}

function setContentLinkValue()
{
	eval("obj = document.MainForm.selectshield");
 	var returnValue;

	with(obj) {
 		for(i=0; i <  obj.length ; i++){
			if(i==0) {
				returnValue = options[i].value;
			} else {
				returnValue = returnValue + ',' + options[i].value;
			}
 		} 		
	}
	if(returnValue == 'undefined') {
		returnValue = '';
	}
 	return returnValue;
}

function do_change_price(ptype,iid)
{
	var nprice= $("#"+ptype+"_"+iid).val();
	if(nprice!='')
	{
		$.post("do_product.php",
			{m:"update_price", nprice: nprice, pid: iid, pricen:ptype},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.growlUI("修改成功!");
				}else{
					$.growlUI(""+data+"");
				}				
			}		
		);
	}else{
		return false;
	}
	window.setTimeout($.unblockUI, 2000);
}


function do_change_price3(iid)
{
	$('#windowContent').html('<iframe src="change_product_level_price.php?pid='+iid+'" width="500" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('药店等级价格设置');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'440px',top:'10%'
            }			
		});
		$('#windowForm').css("width","500px");

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}