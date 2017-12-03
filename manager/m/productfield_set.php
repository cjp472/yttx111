<?php 
$menu_flag = "system";
include_once ("header.php"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
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
			<div id="oldinfo" class="line">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<fieldset  class="fieldsetstyle">		
			<legend>商品自定义字段设置</legend>
            <br style="clear:both;" />

              <table width="600" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="center">
				 <tr>
       			     <td  bgcolor="#FFFEF5" width="25%" class="font14" align="center">序号 ：</td>
					 <td  bgcolor="#FFFEF5" class="font14" align="center">  字段名称 </td>
					 <td  bgcolor="#FFFEF5" width="25%" class="font14" align="center">列表页显示</td>
     			 </tr>
			  <?
				$valuearr = null;
				$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='field' limit 0,1");
				if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);

				for($i=1;$i<11;$i++)
				{
			 ?>
                <tr>
				  <td  bgcolor="#F0F0F0"><div align="center" class="TitleNUM4"><? echo $i;?></div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="FieldName_<? echo $i;?>" id="FieldName_<? echo $i;?>" value="<? if(!empty($valuearr['FieldName_'.$i]['name'])) echo $valuearr['FieldName_'.$i]['name']; ?>" />
                    </label></td>
					 <td  bgcolor="#FFFFFF"><label> <input type="checkbox" name="FieldName_<? echo $i;?>_check" id="FieldName_<? echo $i;?>_check" value="1" <? if(!empty($valuearr['FieldName_'.$i]['check']) && $valuearr['FieldName_'.$i]['check']=="1") echo 'checked="checked"';?> /></label></td>
                </tr>
				<? }?>

              </table>
              <table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
     			 <tr>
       			     <td  align="center" height="32" ><input type="button" name="newbutton" id="newbutton" value="保存设置" class="button_2" onclick="savesettype('field');" style="margin-right:20px;" /></td>
     			 </tr>
				 <tr><td colspan="2" align="right" height="32">(注：最多 10 个字段，不需要设置字段不填. 客户端商品列表页最多只能显示两个字段。)&nbsp;</td></tr>
              </table>
			
			<br style="clear:both;" />
           </fieldset>  
            
			</form>
			<br style="clear:both;" />
            </div>
        	</div>              
        <br style="clear:both;" />
     </div>
	 </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>