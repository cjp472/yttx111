<?php 
$menu_flag = "finance";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

$accinfoarr = $db->get_results("SELECT AccountsID,AccountsBank,AccountsNO,AccountsName FROM ".DATATABLE."_order_accounts where AccountsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AccountsID ASC");
foreach($accinfoarr as $av){
	$accarr[$av['AccountsID']] = $av['AccountsBank'].'('.$av['AccountsNO'].')';
}
$accarr[0] = '余额支付';
if(empty($in['ty'])) $in['ty'] = 'Z';

if(!empty($in['oid'])){
	$oid = intval($in['oid']);
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderPayType,OrderPayStatus,OrderTotal,OrderIntegral,OrderStatus FROM ".DATATABLE."_order_orderinfo where OrderID=".$oid." and OrderCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	if($oinfo['OrderStatus'] == "8" || $oinfo['OrderStatus'] == "9" ) exit('此订单已取消！');
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

<script src="js/finance.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#data_FinanceToDate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		 <input type="hidden" name="finance_type" id="finance_type" value="<?php echo $in['ty'];?>" />
	     <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="finance.php">收款</a> &#8250;&#8250;  <a href="finance_add.php">新增收款单</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_finance();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='finance.php'" />
			</div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
            <fieldset title="“*” 为必填项"class="fieldsetstyle">
			<legend>转账信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <?php
				 $payTotal = 0;
				 if(!empty($oinfo)){
					$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientID=".$oinfo['OrderUserID']." limit 0,1");
					$payTotal = $oinfo['OrderTotal'] - $oinfo['OrderIntegral'];
				?>
                 <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">药店：</div></td>
                  <td  bgcolor="#FFFFFF" ><strong><?php echo $cinfo['ClientCompanyName'];?></strong></td>
                  <td  bgcolor="#FFFFFF" >&nbsp;<input type="hidden" name="data_FinanceClient" id="data_FinanceClient" value="<?php echo $cinfo['ClientID'];?>" /></td>
                </tr>
                 <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">订单：</div></td>
                  <td  bgcolor="#FFFFFF" >订单号：<span title='订单号' class=font12><?php echo $oinfo['OrderSN'];?></span>&nbsp;&nbsp;订单金额：<span title='金额' class=font12>¥  <?php echo $oinfo['OrderTotal'];?></span></td>
                  <td  bgcolor="#FFFFFF" >&nbsp;
				  <input type="hidden" name="data_FinanceOrderID" id="data_FinanceOrderID" value="<?php echo $oinfo['OrderID'];?>" />
				  <input type="hidden" name="FinanceOrder[]" id="FinanceOrder" value="<?php echo $oinfo['OrderSN'];?>" />
				  </td>
                </tr>
				<?php 
				$sqlmsg	= " and (FinanceOrderID = ".$oinfo['OrderID']." OR FinanceOrder like '%".$oinfo['OrderSN']."%') ";
				$datasql   = "SELECT * FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." and FinanceClient=".$oinfo['OrderUserID']." ".$sqlmsg." Order by FinanceID ASC";
				$list_data = $db->get_results($datasql);
						
				?>
                 <tr>
				  <td  bgcolor="#F0F0F0"><div align="right"> 收款记录：</div></td>
                  <td >	
				<?php
					if(!empty($list_data)){  
				?>
				<table cellspacing="0" rules="all" border="1"  style="width:100%;border-collapse:collapse;">
				<tr>
					<th scope="col">收款日期</th>
					<th scope="col">收款金额</th>
					<th scope="col">收款账号</th>
					<th scope="col">支付订单</th>
					<th scope="col">状态</th>
				</tr>
			    <?php 
				foreach($list_data as $v){
					if($v['FinanceType']=='Y'){
						$accmsg = '余额支付';
					}elseif($v['FinanceFrom']=='allinpay'){
						$accmsg = '网银支付';
					}else{
						$accmsg = $accarr[$v['FinanceAccounts']];
					}
					echo '<tr>
					<td>'.$v['FinanceToDate'].'</td>
					<td>¥ '.$v['FinanceTotal'].'</td>
					<td>'.$accmsg.'</td>
					<td>'.$v['FinanceOrder'].'</td>
					<td>'.$finance_arr[$v['FinanceFlag']].'</td>
					</tr>';
					//$payTotal = $payTotal - $v['FinanceTotal'];
				}
				?>				  
				</table>
				<?php
				}else{
					echo '暂无收款记录!';
				}
				 ?>
				 </td>
				  <td>&nbsp;</td>
				 <tr>
				<?php
				}else{
				 ?>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户（转款人）：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <select name="data_FinanceClient" id="data_FinanceClient" class="select2" style="width:544px;" onChange="javascript: get_orderlist(this.options[this.selectedIndex].value);"  >
                    <option value="0">⊙ 请选择客户（转款人）</option>
                    <? 
					$orderintoarr = $db->get_results("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFLag=0 ORDER BY ClientCompanyPinyi ASC");
					foreach($orderintoarr as $orderinfovar)
					{
						if($orderintoarr[0]['ClientID'] == $orderinfovar['ClientID']) $smsg = 'selected="selected"'; else $smsg='';

						echo '<option value="'.$orderinfovar['ClientID'].'" title="'.$orderinfovar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$orderinfovar['ClientCompanyPinyi']).'" '.$smsg.' >'.substr($orderinfovar['ClientCompanyPinyi'],0,1).' - '.$orderinfovar['ClientCompanyName'].'</option>';
					}
					?>
                  </select> <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;可输入名称首字母快速匹配</td>
                </tr> 

                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">预收款：</div></td>
                  <td width="55%" bgcolor="#FFFFFF" ><div align="left" class="selectinput">&nbsp;&nbsp;<input id="FinanceYufu" name="FinanceYufu" type="checkbox" onclick="selectyufuclick()" value="yufu"  /></div></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;</td>
                </tr>
				
                <tr id="finaceorderselectid">
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">收款订单：</div></td>
                  <td  bgcolor="#FFFFFF" colspan="2">
				  
				  <div style="width:60%; height:250px; overflow:auto;" id="showuserorder">
				    <table width="96%" border="0" cellspacing="1" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0" style="height:28px;background-color:#efefef;">
                        <td width="8%">&nbsp;</td>
                        <td width="30%"><strong>&nbsp;订单号</strong></td>
                        <td width="25%"><strong>&nbsp;订单金额</strong></td>
                        <td width="20%"><strong>&nbsp;已收金额</strong></td>
                        <td ><strong>&nbsp;状态</strong></td>
                      </tr>
					  <?php
					  $orderlistuser = $db->get_results("SELECT OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$orderintoarr[0]['ClientID']." and (OrderPayStatus=0 or OrderPayStatus=1 or OrderPayStatus=3) and OrderStatus < 5 order by OrderID desc limit 0,100");
					  foreach($orderlistuser as $olvar)
					  {
					  ?>
                      <tr height="28" id="selected_line_<? echo $olvar['OrderID'];?>">
                        <td class="selectinput"><input  id="data_FinanceOrder_<? echo $olvar['OrderID'];?>" name="FinanceOrder[]" type="checkbox" onclick="selectorderlinefocus('<? echo $olvar['OrderID'];?>')" value="<? echo $olvar['OrderSN'];?>" /></td>
                        <td onclick="selectorderline('<? echo $olvar['OrderID'];?>')" >&nbsp;<? echo $olvar['OrderSN'];?></td>
                        <td onclick="selectorderline('<? echo $olvar['OrderID'];?>')">&nbsp;¥ <? echo $olvar['OrderTotal'];?><input type="hidden" name="ordertotal[]" id="order_total_<? echo $olvar['OrderID'];?>" value="<? echo $olvar['OrderTotal']-$olvar['OrderIntegral'];?>" /></td>
						<td onclick="selectorderline('<? echo $olvar['OrderID'];?>')"><?  echo '&nbsp;¥ '.$olvar['OrderIntegral'].'';?></td>
                        <td onclick="selectorderline('<? echo $olvar['OrderID'];?>')">&nbsp;<? echo $order_status_arr[$olvar['OrderStatus']];?></td>
                      </tr>
                      <? }?>
                  </table>
                  <div>
				  </td>
                </tr>
				<?php }?>
				<?php if(!empty($in['oid'])){?>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收款方式 ：</div></td>
                  <td  bgcolor="#FFFFFF" class="select_finance">
				  <?php if($in['ty'] == 'Z'){?>
					<li class="selected_finance" ><a href="#">银行转账</a></li>
					<li><a href="finance_add.php?oid=<?php echo $oinfo['OrderID'];?>&ty=Y">余额支付</a></li>
				 <?php }else{?>
					<li  ><a href="finance_add.php?oid=<?php echo $oinfo['OrderID'];?>">银行转账</a></li>
					<li class="selected_finance"><a href="#">余额支付</a></li>
				 <?php }?>
				  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				<?php }?>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">转款日期 ：</div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_FinanceToDate" id="data_FinanceToDate" value="<?php echo date("Y-m-d");?>" style="width:340px" />
                  <span class="red">*</span></label></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				<?php
				if(!empty($in['ty']) && $in['ty']=="Y"){
				   $ytotal	= get_client_money($db,$oinfo['OrderUserID']);
				?>				
                <tr>
                  <td bgcolor="#F0F0F0" height="30"><div align="right">可用余额：</div></td>
                  <td bgcolor="#FFFFFF">
					<input name="data_FinanceAccounts" id="data_FinanceAccounts" value="0" type="hidden"  />
					<input name="ytotal" id="ytotal" value="<?php echo sprintf("%.2f",$ytotal);?>" type="hidden"  />
					<span class="font12">¥ <?php echo sprintf("%.2f",$ytotal);?></span>
                  </td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
				<?php }else{?>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收款账户：</div></td>
                  <td bgcolor="#FFFFFF"><select name="data_FinanceAccounts" id="data_FinanceAccounts" class="select2" style="width:345px">
                    <option value="0">⊙ 请选择收款账户</option>
                    <?php
					if(!empty($oinfo['OrderUserID'])){
						$cfinfo = $db->get_row("SELECT FinanceAccounts FROM ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceClient=".intval($oinfo['OrderUserID'])." AND FinanceAccounts <> 0 order by FinanceID desc limit 0,1");
						if(!empty($cfinfo['FinanceAccounts'])) $faid = $cfinfo['FinanceAccounts']; else $faid = 0;
					}
					foreach($accinfoarr as $accinfovar)
					{
						if($faid == $accinfovar['AccountsID']) $selectedmsg = 'selected="selected"'; else $selectedmsg = '';
						echo '<option value="'.$accinfovar['AccountsID'].'" '.$selectedmsg.' >┠- '.$accinfovar['AccountsBank'].' ('.$accinfovar['AccountsNO'].') </option>';
					}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">新增【<a href="accounts_add.php">账号</a>】</td>
                </tr>
				<?php }?>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收款金额：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_FinanceTotal" id="data_FinanceTotal"  maxlength="12" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" style="width:180px;" value="<?php 
					if($in['ty']=="Y"){
						if($ytotal <= 0) echo '0'; elseif($payTotal > $ytotal) echo $ytotal; else echo $payTotal;
					}else{
						if($payTotal <= 0) echo '0'; else echo $payTotal;
					}
					?>" />&nbsp;元
                  </label>
                  <span class="red">* </span>
				  &nbsp;(您选择的订单金额为：¥ <span class="font12" id="show_order_total"><?php echo $payTotal;?></span>)
				  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="data_FinanceAbout" rows="4" id="data_FinanceAbout"></textarea>
                  </label></td>
                  <td bgcolor="#FFFFFF">可注明转款原因，转款人</td>
                </tr>
               </table>
		  </fieldset>

			<br style="clear:both;" />
          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_finance();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='finance.php'" />
		 </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?php
	function get_client_money($db,$cid)
	{
		$cid       =  intval($cid);
		$sqlunion  = " and FinanceClient = ".$cid." "; 		
		$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
		$statdata2 = $db->get_row($statsql2);
		/***
		//$statsql3  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and FinanceFlag=2 and FinanceType='Y' ";
		$sqlunion  = " and f.FinanceClient = ".$cid." "; 
		$statsql3  = "SELECT sum(f.FinanceTotal) as Ftotal from ".DATATABLE."_order_finance f inner join ".DATATABLE."_order_orderinfo o ON f.FinanceOrderID=o.OrderID where o.OrderStatus=0 and (o.OrderPayStatus=2 or o.OrderPayStatus=3)  ".$sqlunion." and f.FinanceCompany=".$_SESSION['uinfo']['ucompany']." and f.FinanceFlag=2 and f.FinanceType='Y' ";
		$statdata3 = $db->get_row($statsql3);
		**/
		$sqlunion  = " and ClientID = ".$cid." "; 		
		$statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and FlagID = '2' ";
		$statdata4 = $db->get_row($statsql4);

		$sqlunion  = " and OrderUserID = ".$cid." "; 
		$statsqlt  = "SELECT sum(OrderIntegral) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and OrderStatus!=8 and OrderStatus!=9 ";
		$statdatat = $db->get_row($statsqlt);
		
		$sqlunion   = " and ReturnClient  = ".$cid." ";
		$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and (ReturnStatus=3 or ReturnStatus=5) ";
		$statdata1  = $db->get_row($statsqlt1);
		
		$begintotal = $statdata2['Ftotal'] + $statdata1['Ftotal'] - $statdatat['Ftotal'] + $statdata4['Ftotal'] - $statdata3['Ftotal'];
		
		return $begintotal;
	}

?>