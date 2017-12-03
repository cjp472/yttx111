<?php 
$menu_flag = "infomation";
$pope	   = "pope_form";
include_once ("header.php"); 

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$info = $db->get_row("SELECT * FROM ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['uinfo']['ucompany']." and ArticleID=".intval($in['ID'])." limit 0,1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link href="../plugin/colpickColorPicker/css/colpick.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="../plugin/colpickColorPicker/js/colpick.js" type="text/javascript"></script>
<script src="js/infomation.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script>
$(function(){
	$('#data_ArticleColor').colpick({
		onSubmit:function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).colpickHide();
		}}
	);
});

</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="set_filename" id="set_filename" value="" />
			<input type="hidden" name="update_id" id="update_id" value="<? echo $info['ArticleID'];?>" />
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="infomation.php">信息管理</a>  &#8250;&#8250; <a href="infomation_add.php">新增信息</a> </div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="going('editsave','0')" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >        

			<fieldset title="“*”为必填项！" class="fieldsetstyle">
		<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所属栏目：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                  <select name="data_ArticleSort" id="data_ArticleSort" class="select2" style="width:555px;">
                    <option value="0">┠- 公告信息</option>
                    <? 
					$sinfo = $db->get_results("SELECT SortID,SortName FROM ".DATATABLE."_order_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");
					foreach($sinfo as $svar)
					{
						if($svar['SortID'] == $info['ArticleSort'])
						{
							echo '<option value="'.$svar['SortID'].'" selected="selected">┠-'.$svar['SortName'].'</option>';
						}else{
							echo '<option value="'.$svar['SortID'].'">┠-'.$svar['SortName'].'</option>';
						}
					}
					?>
                  </select>
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;[<a href="sort.php">新增栏目</a>]</td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">标题：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_ArticleTitle" id="data_ArticleTitle" value="<? echo $info['ArticleTitle'];?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF"><input type="text" name="data_ArticleColor" id="data_ArticleColor" value="<? echo $info['ArticleColor'];?>" title="标题颜色" style="width:100px;" />&nbsp;(标题颜色)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">作者：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_ArticleAuthor" id="data_ArticleAuthor" value="<? echo $info['ArticleAuthor'];?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">文件：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_ArticlePicture"  id="data_ArticlePicture" value="<? echo $info['ArticlePicture'];?>" style="width:78%;" />&nbsp;<input name="bt_Picture" type="button"  onClick="upload_file('data_ArticlePicture');" value="上 传" title="上传"  class="bluebtn" style="width:52px; height:25px; font-size:12px;" /></td>
                  <td bgcolor="#FFFFFF" id="data_ArticlePicture_text" title="文件">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">文件名称：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_ArticleFileName" id="data_ArticleFileName" value="<? echo $info['ArticleFileName'];?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
	</fieldset>
    
	<fieldset  class="fieldsetstyle">
		<legend>正文内容</legend>
		<script src="../ckeditor/ckeditor.js?v=3"></script>
		<textarea class="ckeditor" cols="80" id="editor1" name="editor1" rows="10">
		<?php
				echo $info['ArticleContent'];

			?>
		</textarea>
			
            </fieldset>
            
            
			<fieldset class="fieldsetstyle">
			<legend>设置</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" >                            
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">排序权重：</div></td>
                  <td width="55%"><label>
                    <input name="data_ArticleOrder" type="text" id="data_ArticleOrder" value="<? echo $info['ArticleOrder'];?>"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" />
                 </label></td>
                  <td width="29%"> (排序依据，数字大的靠前)</td>
                </tr>
              </table>
           </fieldset>    
            
            <div class="rightdiv sublink" style="padding-right:20px;">
			
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="going('editsave','0')" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			
			</div>
        </div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">上传文件</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
      
        </div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
<div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle6">选择颜色</h3>
			<div class="windowClose"><div class="close-form" onclick="$.unblockUI();" title="关闭" >x</div></div>
		</div>
		<div id="windowContent6"></div>
	</div> 
</body>
</html>