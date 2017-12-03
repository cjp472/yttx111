<?php 
$menu_flag = "inventory";
$pope	       = "pope_form";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tbody tr td{ background: #ffffff;}
-->
</style>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
  <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">	
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="storage_add.php">商品入库</a> </div>
   	        </div> 
			<? if($_SESSION['uinfo']['ucompany']=="51"){?>
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px;"><ul><li><a href="storage_import_51.php">导入库存</a></li></ul></div>
			<? }?>
			<? if($_SESSION['uinfo']['ucompany']=="33"){?>
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px;"><ul><li><a href="storage_import.php">导入库存</a></li></ul></div>
			<? }?>
			<? if($_SESSION['uinfo']['ucompany']=="1" || $_SESSION['uinfo']['ucompany']=="115"){?>
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px;"><ul><li><a href="storage_import_115.php">导入库存</a></li></ul></div>
			<? }?>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
		<fieldset class="fieldsetstyle">
			<legend>入库商品</legend>			
		    <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
              <thead>
              <tr>
                <td width="10%" bgcolor="#efefef">ID</td>
                <td bgcolor="#efefef">名称</td>
                <td width="18%" bgcolor="#efefef">编号/货号</td>
                <td width="12%" bgcolor="#efefef">包装</td>
                <td width="6%" align="center" bgcolor="#efefef">单位</td>
                <td width="10%" bgcolor="#efefef">&nbsp;数量</td>
				<td width="6%" bgcolor="#efefef" align="center">移除</td>
              </tr>
              </thead>
              <tbody id="come_add_sel_pro">

              </tbody>
            </table>
		    <table width="100%" border="0" cellspacing="0" cellpadding="4">
              <tr>
                <td height="15"></td><td height="15"></td>
              </tr>
              <tr>
                <td width="10%"><strong>录入商品： </strong></td><td>
                  <input name="inputsp" type="text" id="inputsp" size="50" onKeyDown="javascript: if(window.event.keyCode == 13) select_product();" /> 
                  <label>
                  <input name="buttonsp" type="button" class="bluebtn" id="buttonsp" value=" ... "  onClick="select_product();" />
                  </label>(可通录入商品的ID、名称、编号、拼音码查询)
               </td>
              </tr>
            </table>
		</fieldset>
            
		<fieldset class="fieldsetstyle">
			<legend>说明</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>    
    <td width="10%">经手人：</td>
    <td ><label>
      <input type="text" name="StorageAttn" id="StorageAttn" /><input type="hidden" name="selectid_storage" id="selectid_storage" value="" />&nbsp;<font color=red>*</font>&nbsp;&nbsp;&nbsp;&nbsp;	  
		<?
			$userdata = $db->get_results("select UserID,UserName,UserTrueName,UserPhone from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserFlag='0' and UserType='M' limit 0,50");
			if(!empty($userdata))
			{
				echo '<select name="_StorageAttn_default" onchange="this.form.StorageAttn.value = this.options[this.selectedIndex].value">
				<option value="">请选择</option>';
				foreach($userdata as $var)
				{
					if($var['UserTrueName']=="管理员") continue;
					echo '<option value="'.$var['UserTrueName'].'"> '.$var['UserTrueName'].' </option>';
				}
				echo '</select>';
			}
		?>	  
    </label></td>
  </tr>
  <tr>
    <td>附加说明：</td>
    <td ><label>
      <textarea name="StorageAbout" id="StorageAbout" cols="94%" rows="5"></textarea>
    </label></td>
    </tr>
</table>
        </fieldset>    
            
        <div class="rightdiv sublink" style="padding-right:20px;">
		<input name="save_storage" type="button" class="button_1" id="save_storage" value="保 存" onclick="add_storage_save();" />
		<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
		</div>
        
        </div>
        <INPUT TYPE="hidden" name="referer" value ="" >
        </form>
        <br style="clear:both;" />

    </div>    

    <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">请选择入库商品</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>