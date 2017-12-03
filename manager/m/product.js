
//去空隔函数 
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 

function CheckAll(form)
{
	for(var i=0;i<form.elements.length;i++)
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
		window.setTimeout($.unblockUI, 1000); 
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

 //执行批量操作
 function apply_muledit(targetNodeID) {
     if(!targetNodeID) {
         return false;
     }
     var sid = 0;
     document.MainForm.action = 'do_product.php?m=muleditContent&SiteID='+sid+'&targetNodeID=' + targetNodeID;
     document.MainForm.submit();
 }

 //执行商品移动
 function apply_move(targetNodeID) {
     if(!targetNodeID) {
         return false;
     }
     var sid = 0;
     document.MainForm.action = 'do_product.php?m=moveContent&SiteID='+sid+'&targetNodeID=' + targetNodeID;
     document.MainForm.submit();
 }

 //iframe调用关闭blockUI
 function cancel_muledit() {
     $.unblockUI();
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
                $('#windowContent_T').html('<iframe src="select_product.php" width="340" marginwidth="0" height="340" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
                $.blockUI({
                    message: $('#windowForm_T'),
                    css:{
                        width: '340px',height:'340px',top:'15%'
                    }
                });
                $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
				break;
				
			case 'muledit':

                $('#windowContent_T').html('<iframe src="muledit_product.php" width="240" marginwidth="0" height="240" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
                $.blockUI({
                    message: $('#windowForm_T'),
                    css:{
                        width: '240px',height:'240px',top:'15%'
                    }
                });
                $('.blockOverlay').attr('title','点击返回').click($.unblockUI);

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
					
					}else if($('#show_thumb_mu_img #thumbimg li').length>24){
						$.blockUI({ message: "<p>图片最多保留24张，请删除一些再提交！</p>" });		

					}else{
						/* 将图片附件的值写入表单中 */
						var arrThumbValue = new Array();
						$('#show_thumb_mu_img #thumbimg li').each(function(n){
							arrThumbValue[n] = {};
							arrThumbValue[n]['oldname'] = $(this).attr('_oldname');
							arrThumbValue[n]['filesize'] = $(this).attr('_filesize');
							arrThumbValue[n]['filepath'] = $(this).attr('_filepath');
							arrThumbValue[n]['filename'] = $(this).attr('_filename');

							if($(this).find('input:radio[name="DefautlImg"]').is(':checked')){ 
								$(this).find('input:radio[name="DefautlImg"]:checked').val(n);
							}
						});

						if($('#show_thumb_mu_img #data_Thumb').length<=0){ 
							$('#show_thumb_mu_img').append("<input type='hidden' name='data_Thumb' id='data_Thumb' value='' />");
						}

						$('#data_Thumb').val(JSON.stringify(arrThumbValue));
						
						document.MainForm.Shield.value = setContentLinkValue();
						document.MainForm.Relation.value = setRelationValue();
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
					
					}else if($('#show_thumb_mu_img #thumbimg li').length>24){
						$.blockUI({ message: "<p>图片最多保留24张，请删除一些再提交！</p>" });		

					}else{
						/* 将图片附件的值写入表单中 */
						var arrThumbValue = new Array();
						$('#show_thumb_mu_img #thumbimg li').each(function(n){
							arrThumbValue[n] = {};
							arrThumbValue[n]['oldname'] = $(this).attr('_oldname');
							arrThumbValue[n]['filesize'] = $(this).attr('_filesize');
							arrThumbValue[n]['filepath'] = $(this).attr('_filepath');
							arrThumbValue[n]['filename'] = $(this).attr('_filename');

							if($(this).find('input:radio[name="DefautlImg"]').is('checked')){ 
								$(this).find('input:radio[name="DefautlImg"]:checked').val(n);
							}
						});

						if($('#show_thumb_mu_img #data_Thumb').length<=0){ 
							$('#show_thumb_mu_img').append("<input type='hidden' name='data_Thumb' id='data_Thumb' value='' />");
						}

						$('#data_Thumb').val(JSON.stringify(arrThumbValue));

						document.MainForm.Shield.value = setContentLinkValue();
						document.MainForm.Relation.value = setRelationValue();
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
				
			case 'recycle_outexcel':
					document.MainForm.action = 'product_recycle_excel.php';
                    document.MainForm.submit();
				break;	

			}

			$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function out_all_product_excel()
{
	$("#windowtitle").html('导出全部商品数据');

	$('#windowContent').html('<iframe src="product_out_number.php" width="100%" marginwidth="0" height="350" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'380px',top:'8%',left:"20%"
            }			
		});
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

function set_mu_img(arrUploadinfo)
{
		$.post("do_upload.php",
			{m:"set_upload_mu_img",maxnum:$('#show_thumb_mu_img #thumbimg li').length,'updata':JSON.stringify(arrUploadinfo)},
			function(data){
				data = Jtrim(data);
				if(data != ""){
					$("#show_thumb_mu_img #thumbimg").append(data);
					bindImgOrder();
				}				
			}	
		);
	$.unblockUI();
}

function remove_up_img(arrkey)
{
	var img_id = "mu_img_id_"+arrkey;
	$('#'+img_id).remove();
	/*$.post("do_upload.php",
		{m:"remove_upload_mu_img",rkey: arrkey},
		function(data){

		}		
	);*/		
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
		$("#"+filevalue+"_text").html('[<a href="'+fpn+'" target="_blank">预览图片</a>]');
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
		$('#Deduct').val('0');
		$("#show_thumb_mu_img").html('<ul id="thumbimg"></ul>');
		$("#product_commendtype").html('&nbsp;<input name="data_CommendID" type="radio" value="0" checked="checked"  /> 默认&nbsp;&nbsp; <input name="data_CommendID" type="radio" value="1"  /> 推荐&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="2"  title="特价商品不执行经销商折扣" /> 特价&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="3"  /> 新款&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="4" /> 热销&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="8"  /> 赠品&nbsp;&nbsp;<input name="data_CommendID" type="radio" value="9"  /> 缺货');
		
		$('#newbutton3').val('等级价');
		$('#newbutton4').val('指定价');
		$('#saveproductid').val('保 存');
		$('#resetproductid').val('重 置');
		$('#backid').val('返 回');
		$('#saveproductid2').val('保 存');
		$('#resetproductid2').val('重 置');
		$('#backid2').val('返 回');

		$('#shield_b1').val('删除');
		$('#shield_b2').val('清空');
		$('#shield_b4').val('选择');

		$('#relation_r1').val('删除');
		$('#relation_r2').val('清空');
		$('#relation_r4').val('选择');
		clear_client();
		clear_relation();

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
        $('#edit_ParentID').select2();
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
					$.blockUI({ message: "<p>操作成功</p>" });
					$('#set_notice_'+nid).html('<font color=gray>已通知</font>');
					//window.location.reload();
					window.setTimeout($.unblockUI, 1000);
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
	$('#windowContent').html('<iframe src="set_product_level_price.php?vmsg='+$('#set_Price3').val()+'" width="500" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('经销商等级价格设置');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'440px',top:'10%'
            }			
		});
		$('#windowForm').css("width","500px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function set_client_price(fildname)
{
	$('#windowContent').html('<iframe src="set_product_client_price.php?vmsg='+$('#set_Price4').val()+'" width="500" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('指定经销商价格设置');
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
	$('#set_Price3').val(varmsg);
	closewindowui();
}

function set_input_price4(varmsg)
{
	$('#set_Price4').val(varmsg);
}

/**********************************/
function set_shield_client()
{
	var setclientv = setContentLinkValue();
	$('#windowContent').html('<iframe src="select_shield_client.php?selectid='+setclientv+'" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('选择被屏蔽的经销商');
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

/*************关联商品*********************/
function set_relation()
{
	var setclientv = setRelationValue();
	$('#windowContent').html('<iframe src="relation_select_product.php?selectid='+setclientv+'" width="620" marginwidth="0" height="410" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('选择关联商品');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '620px',height:'445px',top:'2%'
            }			
		});
	$('#windowForm').css("width","620px");
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function add_relation_product(param_index_id,param_title) 
{
	if(param_title!='')
	{
		eval("obj = document.MainForm.selectrelation");
		with(obj) {
			length = obj.length;
			options[length]=new Option(param_title,param_index_id)	;	
		}
	}
}

function set_add_relation(htmlmsg) 
{
	$("#selectrelation").append(htmlmsg);
}

function del_relation() 
{	
	eval("obj = document.MainForm.selectrelation");
	with(obj)
	{
		options[selectedIndex]=null;
	}
}

function clear_relation() 
{
       eval("obj = document.MainForm.selectrelation");
       var count = obj.options.length;
       for(var i = 0;i<count;i++){
             obj.options.remove(0);//每次删除下标都是0
       }
}

function setRelationValue()
{
	eval("obj = document.MainForm.selectrelation");
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
	$("#windowtitle").html('经销商等级价格设置');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',height:'440px',top:'10%'
            }			
		});
		$('#windowForm').css("width","500px");

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function do_change_price4(iid)
{
	$('#windowContent').html('<iframe src="change_product_client_price.php?pid='+iid+'" width="600" marginwidth="0" height="435" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$("#windowtitle").html('经销商单独指定价格');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '600px',height:'480px',top:'10%'
            }			
		});
		$('#windowForm').css("width","600px");

	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

//保存品牌
function do_save_brand()
{
	document.MainForm.referer.value = document.location;

	if($('#data_BrandNO').val()=="")
	{
		$.blockUI({ message: "<p>请先输入编号！</p>" });
	}else if(!/^[A-Za-z0-9]*$/.test($('#data_BrandNO').val())){
        $.blockUI({ message: "<p>只能输入数字或字母！</p>" });
    }else if($('#data_BrandName').val()==""){
		$.blockUI({ message: "<p>请先输入品牌名称！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php?m=brand_add_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.location.href = "product_brand.php";
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

function do_edit_brand()
{
	if($('#edit_BrandNO').val()=="")
	{
		$.blockUI({ message: "<p>请先输入编号！</p>" });

	}else if(!/^[A-Za-z0-9]*$/.test($('#edit_BrandNO').val())){
        $.blockUI({ message: "<p>只能输入数字或字母！</p>" });
    }else if($('#edit_BrandName').val()==""){
		$.blockUI({ message: "<p>请先输入名称！</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php?m=brand_edit_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 1000);
}

function set_edit_bill(uid,uno,uname)
{
//	$("#update_id").val(uid);
//	$("#edit_BrandNO").val(uno);
//	$("#edit_BrandName").val(uname);
//	$("#edit_brand").show();
	var _left = ($(window).width() - 700) / 2;
	
	$('#windowContent').html('<iframe src="./product_brand_edit.php?brandid='+uid+'" width="700" marginwidth="0" height="470" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '700px',
                top:'10%',
                left: _left
            }			
		});
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function set_search_logo(file){
	$("#brand_logo").val(file);
	$("#brand-logo-box").html('<img src="'+resourceurl+file+'" border="0" width="80" height="32" />');
}

function set_brand_logo(){
	var _left = ($('body').width() - 500) / 2;
	$('#windowContent').html('<iframe src="../plugin/jqUploader/uploadfile.php" width="500" marginwidth="0" height="200" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
	$.blockUI({ 
		message: $('#windowForm'),
		css:{ 
                width: '500px',top:'10%',left: _left
            }			
		});
	$('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI); 
}

function setinputimg(fpn)
{	
	if(fpn!='' && fpn!=null)
	{
		$("#brand_logo").val(fpn);
		$("#brand-logo-box").html('<img src="'+resourceurl+fpn+'" border="0" width="80" height="32" />');
	}	
	$.unblockUI();
}

function do_delete_bill(pid)
{
	if(confirm('确认删除吗?'))
	{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"delete_brand", ID: pid},
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

function set_orderby(ty,de)
{
	document.FormSearch.oby.value = ty;
	document.FormSearch.osc.value = de;
	document.FormSearch.submit();

}

function remove_cs(cs_id)
{
	document.getElementById(cs_id).innerHTML = '';
    $("#"+cs_id).remove();
    $("li[id='"+cs_id+"']").remove();

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
		$.post("do_product.php?m=content_import_save&sid="+$('#data_SiteID').val()+"&model="+$("input[name='model']:checked").val(), $("#MainFormImport").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok" || data.indexOf("ok") != -1){
					$.blockUI({ message: "<p>保存成功!</p>" });

					$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
                    setTimeout(function(){
                        window.location.href = "product_import.php";
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

function remove_content_line(pid)
{
	var delline = "line_" + pid;
	$("#"+delline).remove();
}

//检查商品编码是否存在
function check_coding(_this,id) {
    var val = $(_this).val();
    $.post("do_product.php",{
        m : 'check_coding',
        coding: val,
        id : id
    },function(data){
        data = Jtrim(data);
        if(data != 'ok') {
            $("#coding_unique").show();
        } else {
            $("#coding_unique").hide();
        }
    },'text');
}