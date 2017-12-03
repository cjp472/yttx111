<?php 
include_once ("header.php");
$menu_flag = "manager";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#CS_BeginDate").datepicker();
		$("#CS_EndDate").datepicker();
		$("#CS_UpDate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="finance_log.php">财务管理</a> &#8250;&#8250; <a href="#">新增记录</a></div>
   	        </div>
            
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="savemoneyid" type="button" class="button_1" id="savemoneyid" value="保 存" onclick="do_save_money();" />
			<input name="resetmoneyid" type="reset" class="button_3" id="resetmoneyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
            <fieldset  class="fieldsetstyle">
			<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">             
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">类型：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label class="selectinput">
					<input  id="data_PayOrder_1" name="data_PayOrder" type="radio" checked="checked"  value="system" />系统续费
					<input  id="data_PayOrder_3" name="data_PayOrder" type="radio"  value="sms" />短信充值
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备注：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_PayBody" id="data_PayBody"  />&nbsp;<span class="red">*</span>	 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户：</div></td>
                  <td bgcolor="#FFFFFF">
				  <select id="data_PayCompany" name="data_PayCompany" class="select2" >
				<option value="" >⊙ 请选择客户</option>
				<?
				$sortarr = $db->get_results("SELECT CompanyID,CompanyName,CompanySigned FROM ".DATABASEU.DATATABLE."_order_company where CompanyID > 20 and CompanyFlag='0' ORDER BY CompanyID ASC");
				$n = 1;
				foreach($sortarr as $areavar)
				{
					echo '<option value="'.$areavar['CompanyID'].'" '.$smsg.' title="'.$areavar['CompanyName'].'"  >'.$areavar['CompanyID'].' 、 '.$areavar['CompanyName'].'</option>';
				}				  
				 ?>
				  </select>&nbsp;<span class="red">*</span>
				  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">金额：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_PayMoney" id="data_PayMoney" />
                  &nbsp;<span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savemoneyid" type="button" class="button_1" id="savemoneyid2" value="保 存" onclick="do_save_money();" />
			<input name="resetmoneyid" type="reset" class="button_3" id="resetmoneyid2" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid2" value="返 回" onclick="history.go(-1)" />
		  </div>

        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>



<? include_once ("bottom.php");?>	

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>