<?php 
$menu_flag = "manager";
include_once ("header.php");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.tc thead tr td{font-weight:bold; background: #efefef; height:28px; padding:2px;}
.tc tbody tr td{ background: #ffffff; font-weight:bold; height:28px; padding:2px;}
.tcheader{font-weight:bold; background: #efefef; height:25px; padding:2px;}
input{font-weight:bold; font-size:12px;font-family: Verdana, Arial, Helvetica, sans-serif; color:#333333;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#f0f0f0; background:url(./img/f1s.jpg); }

.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}


.button_1{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/anns.jpg) 0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_1:hover {background:url(./img/anns.jpg) 0 -26px no-repeat;}

.button_3{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/dnn5.jpg)  0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_3:hover {background:url(./img/dnn5.jpg) 0 -26px no-repeat;}
-->
</style>
<script type="text/javascript">
	$(function() {
		$("#up_to_EndDate").datepicker();
	});
</script>
</head>

<body>
<?
	if(empty($in['pid']))
	{
		exit('错误参数!');
	}

	$payinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_pay where PayID=".intval($in['pid'])." and PayFlag=0 limit 0,1");

	if(!empty($payinfo['PayID']))
	{
		$cominfo = $db->get_row("SELECT CompanyID,CompanyName FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$payinfo['PayCompany']." limit 0,1");
		$csinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$payinfo['PayCompany']." limit 0,1");
		if($payinfo['PayType']=="system")
		{
?>
<div >
         <form id="SonForm" name="SonForm" enctype="multipart/form-data" method="post"  action="">
		  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="tc">
     			 <tr>
                 	<td width="30%" align="right">业务类型：</td>
					<td >系统交值</td>
     			 </tr>
     			 <tr>
                 	<td width="30%" align="right">标题：</td>
					<td ><? echo $payinfo['PayOrder'];?></td>
     			 </tr>
     			 <tr>
                 	<td width="30%" align="right">备注说明：</td>
					<td ><? echo $payinfo['PayBody'];?></td>
     			 </tr>
     			 <tr>
                 	<td align="right">客户：</td>
					<td ><? echo $cominfo['CompanyName'];?></td>
     			 </tr>
     			 <tr>
                 	<td align="right">充值金额：</td>
					<td >¥ <? echo $payinfo['PayMoney'];?> 元</td>
     			 </tr>
     			 <tr>
                 	<td align="right">用户数：</td>
					<td > <input type="text" name="clientnumber" id="clientnumber" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10"  style="width:150px;" value="<? echo $csinfo['CS_Number']; ?>"  /></td>
     			 </tr>
     			 <tr>
                 	<td align="right">开通时间：</td>
					<td ><? echo $csinfo['CS_BeginDate']; ?> </td>
     			 </tr>
     			 <tr>
                 	<td align="right">到期时间：</td>
					<td ><? echo $csinfo['CS_EndDate']; ?> </td>
     			 </tr>
     			 <tr>
                 	<td align="right">续费时间：</td>
					<td ><? echo $csinfo['CS_UpDate']; ?> </td>
     			 </tr>
     			 <tr>
                 	<td align="right">续 费 到：</td>
					<td ><input type="text" name="up_to_EndDate" id="up_to_EndDate"  maxlength="10"  style="width:150px;" value="<? echo date('Y-m-d',strtotime('+1 years', strtotime($csinfo['CS_EndDate'])));?>"  /> </td>
     			 </tr>
     			 <tr>
                 	<td align="right">短信余额：</td>
					<td > <? echo $csinfo['CS_SmsNumber'];?> 条</td>
     			 </tr>
     			 <tr>
                 	<td align="right">充入条数：</td>
					<td > 
					<?
					$newnumber = 0;
					if($csinfo['CS_Number']=="20") $newnumber = 300; 
					elseif($csinfo['CS_Number']=="50") $newnumber = 500;
					elseif($csinfo['CS_Number']=="100") $newnumber = 1000; 
					elseif($csinfo['CS_Number']=="10000") $newnumber = 2000; 
					?>
					<input type="text" name="smsnumber" id="smsnumber" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10"  style="width:150px;" value="<? echo $newnumber;?>"  /> 条</td>
     			 </tr>

          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
					 <td height="35" align="center"><label>
                 	   <input type="button" name="buttonset" id="buttonset" value=" 提 交 " class="button_1" onclick="save_confirm_finance('<? echo $in['pid']; ?>');" />
                 	 </label>                 	   
               	     &nbsp;&nbsp;
               	     <label>
               	     <input type="button" name="button2" id="button2" value=" 返 回 " class="button_3"  onclick="parent.closewindowui()" />
               	     </label></td>
       			     
     			 </tr>
          </table>
		  </form>
</div>
<? }else{?>
<div >
          <form id="SonForm" name="SonForm" enctype="multipart/form-data" method="post"  action="">
		  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="tc">
     			 <tr>
                 	<td width="30%" align="right">业务类型：</td>
					<td >短信充值</td>
     			 </tr>
     			 <tr>
                 	<td width="30%" align="right">标题：</td>
					<td ><? echo $payinfo['PayOrder'];?></td>
     			 </tr>
     			 <tr>
                 	<td width="30%" align="right">备注说明：</td>
					<td ><? echo $payinfo['PayBody'];?></td>
     			 </tr>
     			 <tr>
                 	<td align="right">客户：</td>
					<td ><? echo $cominfo['CompanyName'];?></td>
     			 </tr>
     			 <tr>
                 	<td align="right">充值金额：</td>
					<td >¥ <? echo $payinfo['PayMoney'];?> 元</td>
     			 </tr>
     			 <tr>
                 	<td align="right">短信余额：</td>
					<td > <? echo $csinfo['CS_SmsNumber'];?> 条</td>
     			 </tr>
     			 <tr>
                 	<td align="right">充入条数：</td>
					<td > <input type="text" name="smsnumber" id="smsnumber" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10"  style="width:150px;" value="<? echo $payinfo['PayMoney']/0.1; ?>"  /> 条</td>
     			 </tr>

          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
					 <td height="35" align="center"><label>
                 	   <input type="button" name="buttonset" id="buttonset" value=" 提 交 " class="button_1" onclick="save_confirm_finance('<? echo $in['pid']; ?>');" />
                 	 </label>                 	   
               	     &nbsp;&nbsp;
               	     <label>
               	     <input type="button" name="button2" id="button2" value=" 返 回 " class="button_3"  onclick="parent.closewindowui()" />
               	     </label></td>
       			     
     			 </tr>
          </table>
		  </form>
</div>

<? }}else{?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
					 <td height="100">操作数据不存在!</td>
       			     
     			 </tr>
          </table>
 <? }?>     
</body>
</html>