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
			{m:"save_sort", ParentID: $('#data_ParentID').val(), SiteName: $('#data_SiteName').val(), SiteOrder: $('#data_SiteOrder').val(), types: $('#types').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					if($('#types').val() ==1){
						window.location.href="base_classify.php?sid="+$('#data_ParentID').val();
					}else{
						window.location.href="special_classify.php?sid="+$('#data_ParentID').val();
					}
					
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
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
			{m:"save_edit_sort",SiteID:$('#edit_ParentID').val(), ParentID: $('#edit_ParentID option:selected').attr("pid"), SiteName: $('#edit_SiteName').val(), SiteOrder: $('#edit_SiteOrder').val()},
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
			{m:"delete_sort",SiteID:$('#edit_ParentID').val()},
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

//去空隔函数 
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 



function do_save_pay_type(){
	if($('#data_SiteName').val()=="")
	{
		$.blockUI({ message: "<p>请输入分类名称！</p>" });

	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"add_notice_type", name: $('#data_SiteName').val(), view_type: $('#add_notice_view_type').val()},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					
						window.location.href="notice_type.php";
					
					
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}


function do_edit_pay_type(){
	
	if($('#edit_SiteName').val()=="")
	{
		$.blockUI({ message: "<p>请输入分类名称！</p>" });

	}else{
		
		var id=$("#edit_ID").find("option:selected").val();
		if(id == ""){
			$.blockUI({ message: "<p>获取不到id!</p>" }); return false;
		}
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"edit_notice_type", name: $('#edit_SiteName').val(), view_type: $('#save_notice_view_type').val(),id:id},
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>修改成功!</p>" });
					
						window.location.href="notice_type.php";
					
					
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}		
	window.setTimeout($.unblockUI, 3000);
}

function do_delete_type()
{
	if(confirm('确认彻底删除吗?此操作不可还原!'))
	{
		var id=$("#edit_ID").find("option:selected").val();
		if(id == ""){
			$.blockUI({ message: "<p>获取不到id!</p>" }); return false;
		}
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php",
			{m:"delete_notice_type",id:id},
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


