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
			<legend>发货方式设置(勾选以下您使用的发货方式)</legend>
            <br style="clear:both;" />
			<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >                
                <?
				$valuearr = array("0");
				$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='send' limit 0,1");
				if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);

				$typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_sendtype ORDER BY TypeID DESC Limit 0,50");
				foreach($typedata as $tvar)
				{
				?>
				<tr>
                  <td height="30" width="20%" bgcolor="#F0F0F0"><input type="checkbox" name="sendtypeID[]" id="select_<? echo $tvar['TypeID'];?>" value="<? echo $tvar['TypeID'];?>" <? if (in_array($tvar['TypeID'], $valuearr)) echo 'checked="checked"';?> /><strong>&nbsp;<? echo $tvar['TypeName'];?>：</strong></td>
                  <td ><? echo $tvar['TypeAbout'];?></td>
                </tr>
				<?
				}	
				?>
                <tr>
                  <td ><div align="right"></div></td>
                  <td><input type="button" name="sendbuttoninfo" id="sendbuttoninfo" value="保存发货方式" class="button_2" onclick="savesettype('send');" /></td>

                </tr>
            </table><br style="clear:both;" />
           </fieldset>  <a name="paylocation"></a>
            <br style="clear:both;" />
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
		<legend>收款方式设置(勾选以下您使用的付款方式)</legend>
            <br style="clear:both;" />
			<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >                
                <?
				$valuearr = array("0");
				$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='pay' limit 0,1");
				if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);
				
				$typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_paytype where TypeClose=0 ORDER BY TypeID DESC Limit 0,50");
				foreach($typedata as $tvar)
				{
				?>
				<tr>
                  <td width="20%" bgcolor="#F0F0F0"><input type="checkbox" name="paytypeID[]" id="select_<? echo $tvar['TypeID'];?>" value="<? echo $tvar['TypeID'];?>" <? if(in_array($tvar['TypeID'], $valuearr)) echo 'checked="checked"';?> /><strong>&nbsp;<? echo $tvar['TypeName'];?>：</strong></td>
                  <td ><? echo $tvar['TypeAbout'];?></td>
                </tr>
				<?
				}	
				?>
                <tr>
                  <td><div align="right"></div></td>
                  <td><input type="button" name="paybuttoninfo" id="paybuttoninfo" value="保存收款方式" class="button_2" onclick="savesettype('pay');" /></td>

                </tr>
            </table><br style="clear:both;" />
		</fieldset>
			</form>
			<br style="clear:both;" />

            </div>
        	</div>
              
        <br style="clear:both;" />
    </div>
       <br style="clear:both;" />
    </div>

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>