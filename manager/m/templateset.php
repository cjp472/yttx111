<?php 
$menu_flag = "system";
include_once ("header.php");	 
$uinfo  = $db->get_row("SELECT c.*,cs.CS_Number FROM ".DATABASEU.DATATABLE."_order_company c INNER JOIN ".DATABASEU.DATATABLE."_order_cs cs ON c.CompanyID=cs.CS_Company where c.CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/system.js?v=<? echo VERID;?>89" type="text/javascript"></script>
<script type="text/javascript">var resourceurl = '<?php echo RESOURCE_URL;?>'</script>
<style type="text/css">
#data_CompanyLogin_text img{
	width:630px;
}
</style>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="companyinfo_edit.php">公司信息</a></div>
   	        </div>     
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong>系统设置</strong></div>
<!-- 系统设置菜单开始 -->
<?php include_once("inc/system_set_left_bar.php")  ;?>
<!-- 系统设置菜单结束 -->
<br style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">
			<div id="oldinfo" class="line" >
			<!--
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<fieldset class="fieldsetstyle">		
			<legend>客户端模板风格选择(勾选您要使用的风格)</legend>
			<div class="listtemplate">				
				<?
				$Fp = @fopen(RESOURCE_PATH.$_SESSION['uc']['CompanyID']."/config.txt", "r");
				@flock($Fp, 1);
				$setarrmsg = @fread($Fp, filesize(RESOURCE_PATH.$_SESSION['uc']['CompanyID']."/config.txt"));
				@fclose($Fp);
				if(!empty($setarrmsg))
				{
					$setarr = unserialize($setarrmsg);
				}
				if(!empty($setarr['template']))
				{
					$sv = $setarr['template'];
				}else{
					$sv = 'blue';
				}
				$templatedata = $db->get_results("SELECT TemplateID,TemplateName,TemplateValue FROM ".DATABASEU.DATATABLE."_order_template where TemplateID!=1 ORDER BY TemplateID ASC Limit 0,50");
				foreach($templatedata as $tvar)
				{
				?>
				<div>
				<dd>
				<a href="template_img/view_<? echo $tvar['TemplateValue'];?>.jpg" target="_blank"><img src="template_img/<? echo $tvar['TemplateValue'];?>.jpg" border="0" title="<? echo $tvar['TemplateName'];?>" /></a></dd>
				<dt>
				<label><input name="data_templateset" id="data_templateset_<? echo $tvar['TemplateID'];?>" type="radio" value="<? echo $tvar['TemplateValue'];?>" onclick="change_template('<? echo $tvar['TemplateValue'];?>');" <? if($sv==$tvar['TemplateValue']) echo 'checked="checked"';?>  />&nbsp;&nbsp;<? echo $tvar['TemplateName'];?><label></dt>
				</div>
				<? }?>		
			</div>
           </fieldset> 

			</form>
			-->
			<br style="clear:both;" />
			</div>
			<!-- 风格选择 -->

		<div id="logoinfo" class="line">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="" >
			<input type="hidden" name="set_filename" id="set_filename" value="" />
			<fieldset class="fieldsetstyle">		
			<legend>客户端自定义logo,图片管理</legend>
			<div >				
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">上传LOGO：</div></td>
                  <td width="55%" bgcolor="#FFFFFF" colspan="2"><input type="text" name="data_CompanyLogo" value="<? echo $uinfo['CompanyLogo'];?>" id="data_CompanyLogo" style="width:75%;" />&nbsp;<input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file('data_CompanyLogo');" value="上传LOGO" title="上 传" style="width:85px; height:26px; font-size:12px;"> </td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                  <td colspan="2" bgcolor="#FFFFFF">
				  <div>最大宽度：400像素、最大高度：90像素。上传的LOGO将会显示在客户端顶部  &nbsp;&nbsp;&nbsp;&nbsp;</div>
				 <div id="data_CompanyLogo_text" style="width:400px; height:90px; overflow:hidden;"><? if(!empty($uinfo['CompanyLogo'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$uinfo['CompanyLogo']).'" border="0" />';?></div>
				  </td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">上传登录页图：</div></td>
                  <td width="55%" bgcolor="#FFFFFF" colspan="2"><input type="text" name="data_CompanyLogin" value="<? echo $uinfo['CompanyLogin'];?>" id="data_CompanyLogin" style="width:75%;" />&nbsp;<input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file('data_CompanyLogin');" value="上传图片" title="上 传" style="width:85px; height:26px; font-size:12px;"> </td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                  <td colspan="2" bgcolor="#FFFFFF">
				  <div>最大宽度：2400像素、最大高度：650像素。上传的图片将会显示在客户端登录页  &nbsp;&nbsp;</div>
				 <div id="data_CompanyLogin_text" style="width:630px; height:325px; overflow:hidden;"><? if(!empty($uinfo['CompanyLogin'])) echo '<img src="'.RESOURCE_URL.str_replace("thumb_","img_",$uinfo['CompanyLogin']).'" border="0" />';?></div>
				  </td>
                </tr>
              </table>
				<div class="line" align="center"><input type="button" name="editbuttoninfo" id="editbuttoninfo" value=" 保存图片 " class="button_2" onclick="subeditlogoimg();" />&nbsp;&nbsp;&nbsp;&nbsp;<font color=red>(注：图片上传成功后请点保存资料)</font></div>
			</div>
           </fieldset> 
			</form>
			<br style="clear:both;" />
			</div>

            </div>
        	</div>
              
        <br style="clear:both;" />
		<!-- 底部版权信息 -->
		<div id="buttoninfo" class="line" style="margin:1px 0 0 1px;">
			<form id="ButtonForm" name="ButtonForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="" >
			<fieldset class="fieldsetstyle">		
			<legend>底部版权信息</legend>
			<div >				
			<?php
				$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='template' limit 0,1");
				if(empty($setinfo['SetValue']))
				{
					$initialValue =  '<p style="text-align:center"><a href="http://www.sda.gov.cn">国家食品药品监督管理总局</a></p><p style="text-align:center">医统天下（北京）网络科技有限公司 &nbsp;<span style="font-size:14px">重庆易极付科技有限公司</span>&nbsp;</p>';
				}else{
					$initialValue = html_entity_decode($setinfo['SetValue'], ENT_QUOTES,'UTF-8');
				}			

			?>
			<script src="../ckeditor/ckeditor.js?v=8"></script>
			<textarea class="ckeditor" cols="80" style="width:99%" id="ButtonContent" name="ButtonContent" rows="10">
				<?php echo $initialValue;?>
			</textarea>
		<script>
			CKEDITOR.replace( 'ButtonContent', {
				height:180,
				toolbar: [
					[ 'Image', 'Flash', 'Table', 'HorizontalRule' ],
					[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote',  '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
					[ 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink' ],
					[ 'FontSize', 'TextColor', 'BGColor' ],
					[ 'Source'] 
				]
			});

		</script>

			</div>
			<div class="line" align="right"><input type="button" name="editbuttoninfo" id="editbuttoninfo" value=" 保存资料 " class="button_2" onclick="subeditbuttoninfo();" />&nbsp;&nbsp;&nbsp;&nbsp;<font color=red> </font></div>
           </fieldset> 
			</form>
			<br style="clear:both;" />
			</div>
    </div>
    

    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">上传图片</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"></div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>