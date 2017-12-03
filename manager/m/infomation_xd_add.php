<?php 
$menu_flag = "infomation";
$pope	   = "pope_form";
include_once("header.php"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/infomation.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	window.resourceUrl = '<?=RESOURCE_URL?>';
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="do_infomation.php?m=xd_add_save">
         <input type="hidden" name="set_filename" id="set_filename" value="" />
		
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="infomation_xd.php">广告管理</a>  &#8250;&#8250; <a href="infomation_xd_add.php">新增广告</a> </div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="save_xd();" />
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
                  <select name="data_ArticleSort" id="data_ArticleSort">
					<option value="1">┠- 首页多图广告</option>
                  </select>
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">广告名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_ArticleName" id="data_ArticleName" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">图片：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_ArticlePicture" id="data_ArticlePicture" style="width:78%;" />&nbsp;<input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_img('data_ArticlePicture');" value="上 传" title="上传" style="width:52px; height:25px; font-size:12px;" /></td>
                  <td bgcolor="#FFFFFF" id="data_ArticlePicture_text" title="文件">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0">&nbsp;</td>
                  <td bgcolor="#FFFFFF">图片尺寸：宽：1920px ， 高：450px</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">链接：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_ArticleLink" id="data_ArticleLink" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;请填写完整的链接</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">说明：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_ArticleContent" rows="5"  id="data_ArticleContent" style="width:90%; height:48px;"></textarea></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
	</fieldset>
    
               
	<fieldset  class="fieldsetstyle">
			<legend >设置</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" >                            
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">排序权重：</div></td>
                  <td width="55%"><label>
                    <input name="data_ArticleOrder" type="text" id="data_ArticleOrder" value="0"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="8" />
                 </label></td>
                  <td width="29%"> (排序依据，数字大的靠前)</td>
                </tr>
              </table>
           </fieldset>    
            
            <div class="rightdiv sublink" style="padding-right:20px;">			
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="save_xd();" />
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
			<h3 id="windowtitle">上传图片</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        
        </div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>