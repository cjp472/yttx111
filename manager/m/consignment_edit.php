<?php 
$menu_flag = "consignment";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".intval($in['ID'])." limit 0,1");

	if(!empty($cinfo['ConsignmentFlag']))
	{
		echo '
		<script language="javascript">
		alert("非法操作!");
		window.location.href="consignment.php";
		</script>
		';
		exit();
	}
}
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

<script src="js/consignment.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#data_ConsignmentDate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			 <input type="hidden" name="ConsignmentID" id="ConsignmentID" value="<? echo $cinfo['ConsignmentID'];?>" />
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="consignment.php">发货</a> &#8250;&#8250; <a href="#">修改发货单</a></div>
   	        </div>
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_edit_consignment();" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='consignment.php'" />
			</div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>发货信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户（药店）：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <strong>
                    <? 
					$orderintomsg = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName FROM ".DATATABLE."_order_client where ClientID=".$cinfo['ConsignmentClient']." and ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFLag=0 ORDER BY ClientID ASC limit 0,1");
					echo $orderintomsg['ClientCompanyName']." ( ".$orderintomsg['ClientTrueName']." )";
					?></strong>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;</td>
                </tr> 
				
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户订单：</div></td>
                  <td >
				    <table width="95%" border="0" cellspacing="0" cellpadding="0">
					  <?
					  $orderlistuser = $db->get_row("SELECT OrderID,OrderSN,OrderTotal,OrderStatus FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$cinfo['ConsignmentClient']."  and  OrderSN='".$cinfo['ConsignmentOrder']."' limit 0,1");
					  if(!empty($orderlistuser))
					  {
					  ?>
                      <tr height="28" id="selected_line_<? echo $orderlistuser['OrderID'];?>"  >
                        <td >&nbsp;<? echo $orderlistuser['OrderSN'];?></td>
                        <td >&nbsp;¥ <? echo $orderlistuser['OrderTotal'];?></td>
                        <td >&nbsp;<? echo $order_status_arr[$orderlistuser['OrderStatus']];?></td>
                      </tr>
                      <? }?>
                  </table>			  
				  </td>
				  </td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">物流公司：</div></td>
                  <td>
                  <select name="data_ConsignmentLogistics" id="data_ConsignmentLogistics" class="select2" style="width:554px;">
                    <option value="">⊙ 请选择物流货运公司</option>
					<option value="0"> ┠- 上门自提</option>
                    <? 
					$logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsPinyi,LogisticsContact,LogisticsPhone FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY LogisticsID ASC Limit 0,500");
					foreach($logisticsarr as $logisticsvar)
					{
						if($cinfo['ConsignmentLogistics'] == $logisticsvar['LogisticsID'])
						{
							echo '<option value="'.$logisticsvar['LogisticsID'].'" selected="selected" >'.$logisticsvar['LogisticsPinyi'].' -- '.$logisticsvar['LogisticsName'].'</option>';
						}else{
							echo '<option value="'.$logisticsvar['LogisticsID'].'">'.$logisticsvar['LogisticsPinyi'].' -- '.$logisticsvar['LogisticsName'].'</option>';
						}						
					}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td>物流、托运、快递公司 [<a href="logistics_add.php">新增物流公司</a>]</td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">运单号：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_ConsignmentNO" id="data_ConsignmentNO" value="<? echo $cinfo['ConsignmentNO'];?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">发货单号，物流跟踪依据</td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">发货经办人：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_ConsignmentMan" id="data_ConsignmentMan"  value="<? echo $cinfo['ConsignmentMan'];?>" />
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>  
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">发货时间：</div></td>
                  <td bgcolor="#FFFFFF"><label><input type="text" name="data_ConsignmentDate" id="data_ConsignmentDate"  value="<? echo $cinfo['ConsignmentDate'];?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr> 
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">运费付款方式：</div></td>
                  <td bgcolor="#FFFFFF">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="inputstyle">
  <tr>
    <td width="31%"><select name="data_ConsignmentMoneyType" id="data_ConsignmentMoneyType">
                    <?										
					foreach($pay_send_arr as $pay_send_key => $pay_send_var)
					{
						if($cinfo['ConsignmentMoneyType'] == $pay_send_key)
						{
							echo '<option value="'.$pay_send_key.'" selected="selected">┠- '.$pay_send_var.'</option>';
						}else{
							echo '<option value="'.$pay_send_key.'">┠- '.$pay_send_var.'</option>';
						}
					}
					?>
                </select>&nbsp;</td>
    <td width="16%"><div align="right">运费金额：</div></td>
    <td width="47%"><input type="text" name="data_ConsignmentMoney" id="data_ConsignmentMoney" value="<? echo $cinfo['ConsignmentMoney'];?>"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" /></td>
    <td width="6%">&nbsp;</td>
  </tr>
</table>
                 </td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>  
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">备注/说明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="data_ConsignmentRemark" rows="4" id="data_ConsignmentRemark"><? echo $cinfo['ConsignmentRemark'];?></textarea>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>                  
            </table>
          </fieldset>  
            
            <br style="clear:both;" />
            <fieldset title="content" class="fieldsetstyle">
		<legend>收货信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">收货人：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label><span class="red">
                        <input type="text" name="data_InceptMan" id="data_InceptMan" value="<? echo $cinfo['InceptMan'];?>"  />
                  *</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>               
                <tr style="display:none;">
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">到达城市：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_InceptArea" id="data_InceptArea" value="<? echo $cinfo['InceptArea'];?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收货人地址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_InceptAddress" id="data_InceptAddress" value="<? echo $cinfo['InceptAddress'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收货人公司：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_InceptCompany" id="data_InceptCompany" value="<? echo $cinfo['InceptCompany'];?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_InceptPhone" id="data_InceptPhone" value="<? echo $cinfo['InceptPhone'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">可以写多个</td>
                </tr>
              </table>
		  </fieldset>

			<br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_edit_consignment();" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='consignment.php'" />
		 </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>