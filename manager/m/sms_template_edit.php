<?php 
$menu_flag = "sms";
$pope	       = "pope_form";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">
function edit_template_save()
{
	if($('#data_TemplateSort').val()=="")
	{
		$.blockUI({ message: "<p>请选择所属分组!</p>" });
	}else if($('#data_Template').val()==""){
		$.blockUI({ message: "<p>请输入模板内容!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$('#newbutton').attr("disabled","disabled");
		$.post("do_sms.php?m=edit_template_save", $("#MainForm").serialize(),
			function(data){
				data = Jtrim(data);
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					parent.window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('#newbutton').attr("disabled","");
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 3000);
}

function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 
</script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
thead tr td{font-weight:bold;}
.bluebtn {
    background: #3366CC; color: #FFF; font-weight: bold; font-size: 12px;  padding: .2em .3em !important; padding: .1em .2em; cursor: pointer; height:24px;
}
.darkbtn {
    background: #666666; color: #FFF;font-weight: bold; font-size: 12px; padding: .2em .3em !important; padding: .1em .2em; height:24px; cursor: pointer;
}
-->
</style>
</head>

<body>
     		  <?
				if(!intval($in['ID']))
				{
					exit('非法操作!');
				}else{	 
					$smsinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_sms_template  where TemplateCompany=".$_SESSION['uinfo']['ucompany']." and TemplateID=".intval($in['ID'])." limit 0,1");
				}
				if(empty($smsinfo['TemplateID'])) exit('此信息不存在，或已经删除!');
			  ?>
	<div style="width:100%;   margin-top:20px;">
		<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			  <input name="data_TemplateID" type="hidden" id="data_TemplateID" value="<? echo $smsinfo['TemplateID'];?>"    />
        	  <table width="96%" border="0" cellpadding="4" cellspacing="1"  align="center" bgcolor="#cccccc">               
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">联系人分组：</td>
					 <td height="30"  bgcolor="#ffffff">	<select name="data_TemplateSort" id="data_TemplateSort"  style="width:200px;">
                    <option value="0">⊙ 请选择联系人分组</option>
					<?
					$sinfo = $db->get_results("SELECT SortID,SortName,SortOrder FROM ".DATATABLE."_order_template_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");
					for($i=0;$i<count($sinfo);$i++)
					{
						if($sinfo[$i]['SortID'] == $smsinfo['TemplateSort'])
						{
							echo '<option value="'.$sinfo[$i]['SortID'].'">'.$sinfo[$i]['SortName'].'</option>';
						}else{
							echo '<option value="'.$sinfo[$i]['SortID'].'" selected="selected">'.$sinfo[$i]['SortName'].'</option>';
						}
					}
					?>
					</select>
                    <font color="red">*</font></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right"><font color="red">*</font> 模板内容：</td>
					 <td height="30"  bgcolor="#ffffff">	<textarea name="data_TemplateContent"  id="data_TemplateContent" cols="40" rows="4"  ><? echo $smsinfo['TemplateContent'];?></textarea></td>
   			  </tr>

			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">&nbsp;</td>
					 <td height="30"  bgcolor="#ffffff"><input type="button" name="newbutton" id="newbutton" value=" 保 存 " class="bluebtn" onclick="edit_template_save();" style="width:80px; height:25px;" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="cancelbutton" id="cancelbutton" value=" 返 回 " class="bluebtn" style="width:80px; height:25px;" onclick="parent.closewindowui();" /></td>
   			  </tr>

 				</tbody>                
              </table>
              </form>
       	  </div>       
</body>
</html>