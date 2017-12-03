<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$accinfoarr = $db->get_results("SELECT AccountsID,AccountsBank,AccountsNO,AccountsName FROM ".DATATABLE."_order_accounts where AccountsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AccountsID ASC");
foreach($accinfoarr as $av){
	$accarr[$av['AccountsID']] = $av['AccountsBank'].'('.$av['AccountsNO'].')';
}
$accarr[0] = '余额支付';
$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderIntegral FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['oid'])." limit 0,1");
$sqlmsg	= " and (FinanceOrderID = ".$oinfo['OrderID']." OR FinanceOrder like '%".$oinfo['OrderSN']."%') ";
$datasql   = "SELECT * FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." and FinanceClient=".$oinfo['OrderUserID']." ".$sqlmsg." Order by FinanceID ASC";
$list_data = $db->get_results($datasql);
?>
<div class="line bgw">
						
  				<?php
					if(!empty($list_data)){  
				?>
				<table width="100%" border="0" cellspacing="1" cellpadding="2" >
				<thead>
				<tr>
					<td >收款日期</td>
					<td >收款金额</td>
					<td >收款帐号</td>
					<td >支付订单</td>
					<td >状态</td>
					<td >查看</td>
				</tr>
				<thead>
				<tbody>
			    <?php 
				$n=0;
				$payTotal = $oinfo['OrderTotal'];
				foreach($list_data as $v){
					$n++;
					/*if($v['FinanceType']=='Y'){
						$accmsg = '余额支付';
					}*/
					if($v['FinanceFrom'] == 'alipay'){
					    $accmsg = '支付宝支付';
					}
					else if($v['FinanceFrom']=='allinpay'){
						$accmsg = '网银支付';
					}
					else if($v['FinanceFrom'] == 'yijifu'){
					    $accmsg = '快捷支付';
					}
					else{
						$accmsg = $accarr[$v['FinanceAccounts']];
					}
					?>
					<tr id="linegoods_<? echo $n;?>" <? if(fmod($n,2)!=0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
				<?php
					echo '
					<td height="30">'.$v['FinanceToDate'].'</td>
					<td>¥ '.$v['FinanceTotal'].'</td>
					<td>'.$accmsg.'</td>
					<td>'.$v['FinanceOrder'].'</td>
					<td>'.$finance_arr[$v['FinanceFlag']].'</td>
					<td>[<a href="finance_content.php?ID='.$v['FinanceID'].'" target="_blank">查看</a>]</td>
					</tr>';
					$payTotal = $payTotal - $v['FinanceTotal'];
				}

					echo '<tr>
					<td height="30" class="font12" colspan="2">订单金额：<span class="font12h">&nbsp;&nbsp;¥&nbsp; '.number_format($oinfo['OrderTotal'],2).'</span></td>
					<td class="font12">已收金额：<span class="font12h">&nbsp;&nbsp;¥&nbsp;'.(number_format($oinfo['OrderIntegral'],2)).'</span></td>
					<td class="font12">未收金额：<span class="font12h">&nbsp;&nbsp;¥&nbsp; '.number_format($oinfo['OrderTotal']-$oinfo['OrderIntegral'],2).'</span></td>
					<td class="font12h"></td>
					</tr>';

				?>
				</tbody>
				</table>
				<?php
				}else{
					echo '<br /><br /><p align="center">暂无收款记录!</p><br /><br />';
				}
				 ?>
		</div>	
