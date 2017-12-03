<?php 
$menu_flag = "client";
$pope	   = "pope_view";
include_once ("header.php");

$ucid = $_SESSION['uinfo']['ucompany'];

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$clientinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_client  where ClientCompany=".$ucid." and ClientID=".intval($in['ID'])." limit 0,1");
	$in['aid']     = $clientinfo['ClientArea'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/client.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>        
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<input name="data_ClientID" type="hidden" id="data_ClientID" value="<? echo $clientinfo['ClientID'];?>"  />
		
		<?php include_once ("inc/menu_client.php");?>

        <div class="bline" >
			<fieldset  class="fieldsetstyle" style="min-height:400px;">		
			<legend>应收款</legend>
           
 <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
               <thead>
                <tr>
				  <td width="6%" class="bottomlinebold">行号</td>
                  <td width="10%" class="bottomlinebold">日期</td>
				  <td  class="bottomlinebold">药店</td>
                  <td width="12%" class="bottomlinebold">&nbsp;单据号</td>
				  <td width="14%" class="bottomlinebold">&nbsp;科目名称</td>
                  <td width="12%" class="bottomlinebold" align="right">应收增加&nbsp;</td>
				  <td width="12%" class="bottomlinebold" align="right">应收减少&nbsp;</td>
				  <td width="15%" class="bottomlinebold" align="right">期末应收(元)</td>
                </tr>
     		 </thead>      		
      		<tbody>

<?php
if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate']))   $in['enddate'] = date("Y-m-d");
	$sqlmsg  =" and FinanceClient = ".intval($in['ID'])." ";
	$sqlmsg2 =" and OrderUserID   = ".intval($in['ID'])." ";
	$sqlmsg3 =" and ReturnClient  = ".intval($in['ID'])." ";
	$sqlmsg4 =" and ClientID	  = ".intval($in['ID'])." ";

		if(!empty($in['begindate']))
		{
			   $sqlunion = " and FROM_UNIXTIME(FinanceUpDate) < '".$in['begindate']." 00:00:00' "; 		
				$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O')  ";
				$statdata2 = $db->get_row($statsql2);

				$sqlunion = " and FROM_UNIXTIME(OrderDate) < '".$in['begindate']." 00:00:00' "; 
// 				$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." ".$sqlunion." and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 ";
				$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." ".$sqlunion." and OrderStatus!=8 and OrderStatus!=9 ";
				$statdatat = $db->get_row($statsqlt);
				
				$sqlunion = " and FROM_UNIXTIME(ReturnDate) < '".$in['begindate']." 00:00:00' ";
				$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg3." ".$sqlunion." and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) ";
				$statdata1 = $db->get_row($statsqlt1);

			    $sqlunion = " and ExpenseDate <= '".$in['begindate']."' "; 		
				$statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg4." ".$sqlunion." and FlagID = '2' ";
				$statdata4 = $db->get_row($statsql4);
				
				$begintotal = $statdatat['Ftotal'] - $statdata4['Ftotal'] - $statdata2['Ftotal'] - $statdata1['Ftotal'];
		}else{
				$begintotal = 0;
		}	
?>
                <tr id="line_1" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td height="28">1</td>
                  <td ><? echo $in['begindate'];?></td>
				  <td ><? echo $clientarr[$in['cid']];?>&nbsp;</td>
				  <td >&nbsp;</td>
                  <td ><strong>期初应收</strong>&nbsp;</td>
				  <td align="right"> &nbsp;</td>
				  <td align="right"> &nbsp;</td>
                  <td align="right"> <? echo sprintf("%01.2f", round($begintotal,2)); ?>&nbsp;</td>
                </tr>
<?php
$billdata = $db->get_results("select BillID,BillName from ".DATATABLE."_order_expense_bill where CompanyID=".$_SESSION['uinfo']['ucompany']." ");
foreach($billdata as $var)
{
	$billarr[$var['BillID']] = $var['BillName'];
}

if(empty($in['enddate'])) $in['enddate'] = date("Y-m-d");
$financesql   = "SELECT FinanceID,FinanceClient,FinanceOrder,FinanceTotal,FinanceUpDate FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and FROM_UNIXTIME(FinanceUpDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O')  Order by FinanceID ASC";
$finance_data = $db->get_results($financesql);

$expensesql   = "SELECT ExpenseID,ClientID,BillID,ExpenseTotal,ExpenseDate,ExpenseTime FROM ".DATATABLE."_order_expense where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg4." and ExpenseDate between '".$in['begindate']."' and '".$in['enddate']."' and FlagID='2' Order by ExpenseID ASC";
$expense_data = $db->get_results($expensesql);

// $ordersql   = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderIntegral,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 Order by OrderID ASC";
$ordersql   = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderIntegral,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=8 and OrderStatus!=9 Order by OrderID ASC";
$order_data = $db->get_results($ordersql);

$returnsql   = "SELECT ReturnID,ReturnSN,ReturnOrder,ReturnClient,ReturnTotal,ReturnDate FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg3." and FROM_UNIXTIME(ReturnDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) Order by ReturnID ASC";
$return_data = $db->get_results($returnsql);

if(empty($finance_data)) $finance_data[0]['FinanceID'] = 0;
if(empty($order_data))   $order_data[0]['OrderID'] = 0;
if(empty($return_data))  $return_data[0]['ReturnID'] = 0;
if(empty($expense_data))  $expense_data[0]['ExpenseID'] = 0;

$dataarr = array_merge($finance_data,$expense_data, $order_data, $return_data);
if(!empty($dataarr))
{
	foreach($dataarr as $dv)
	{
		if(!empty($dv['FinanceID']))
		{
			$key = $dv['FinanceUpDate'];
			$larr['atype'] = "收款单";
			$larr['SN']    = "F".date("Ymd",$dv['FinanceUpDate'])."-".$dv['FinanceID'];
			$larr['Date']  = date("Y-m-d",$dv['FinanceUpDate']);
			$larr['Total'] = $dv['FinanceTotal'];
			$larr['TotalType'] = "-";
			$larr['LinkUrl'] = "finance_content.php?ID=".$dv['FinanceID'];
			$larr['Client']  = $clientarr[$dv['FinanceClient']];
		
		}elseif(!empty($dv['ExpenseID'])){

			$key = strtotime($dv['ExpenseDate']." ".date("H:i:s",$dv['ExpenseTime']));
			$larr['atype'] = "其他款项 - ".$billarr[$dv['BillID']];
			$larr['SN']    = "E".date("Ymd",$dv['ExpenseTime'])."-".$dv['ExpenseID'];
			$larr['Date']  = $dv['ExpenseDate'];
			$larr['Total'] = $dv['ExpenseTotal'];
			$larr['TotalType'] = "-";
			$larr['LinkUrl'] = "expense_content.php?ID=".$dv['ExpenseID'];
			$larr['Client']  = $clientarr[$dv['ClientID']];

		}elseif(!empty($dv['OrderID'])){

			$key = $dv['OrderDate'];
			$larr['atype'] = "订单";
			$larr['SN']    = $dv['OrderSN'];
			$larr['Date']  = date("Y-m-d",$dv['OrderDate']);
			$larr['Total'] = $dv['OrderTotal'];
			$larr['TotalType'] = "+";
			$larr['LinkUrl'] = "order_manager.php?ID=".$dv['OrderID'];
			$larr['Client']  = $clientarr[$dv['OrderUserID']];

		}elseif(!empty($dv['ReturnID'])){

			$key = $dv['ReturnDate'];
			$larr['atype'] = "退货单";
			$larr['SN']    = $dv['ReturnSN'];
			$larr['Date']  = date("Y-m-d",$dv['ReturnDate']);
			$larr['Total'] = $dv['ReturnTotal'];
			$larr['TotalType'] = "-";
			$larr['LinkUrl'] = "return_manager.php?ID=".$dv['ReturnID'];
			$larr['Client']  = $clientarr[$dv['ReturnClient']];
		}
		$darr[$key] = $larr;
	}
	ksort($darr);

	$tall = 0;
	$tjian = 0;
	$tjia  = 0;
	$n=1;
    foreach($darr as $var)
	{
		if(empty($var['Total'])) continue;
		if($var['TotalType']=="-")
		{
			$tall = $tall - $var['Total'];
			$tjian = $tjian + $var['Total'];
		}else{
			$tall = $tall + $var['Total'];
			$tjia = $tjia + $var['Total'];
		}
		$n++;
?>
                <tr id="line_<? echo $n;?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td height="28"><? echo $n;?></td>
                  <td ><? echo $var['Date'];?></td>
				  <td ><? echo $var['Client'];?>&nbsp;</td>
				  <td ><a href="<? echo $var['LinkUrl'];?>" target="blank"><? echo $var['SN'];?></a>&nbsp;</td>
                  <td ><? echo $var['atype'];?></td>
				  <td align="right"> <? if($var['TotalType']=="+") echo number_format($var['Total'],2,'.',',');?>&nbsp;</td>
				  <td align="right"> <? if($var['TotalType']=="-") echo number_format($var['Total'],2,'.',',');?>&nbsp;</td>
                  <td align="right"> <? echo number_format($begintotal+$tall,2,'.',','); ?>&nbsp;</td>
                </tr>
<? }?>
                <tr id="line_<? echo $n+1;?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td >&nbsp;</td>
                  <td >&nbsp; <strong>合计：</strong></td>
				  <td >&nbsp;</td>
				  <td >&nbsp;</td>
                  <td >&nbsp;</td>
				  <td align="right"> <strong><?php number_format($tjia,2,'.',','); ?>&nbsp;</strong></td>
				  <td align="right"> <strong><?php echo number_format($tjian,2,'.',','); ?>&nbsp;</strong></td>
                  <td align="right"> <strong class="font12h"><?php echo number_format($begintotal+$tall,2,'.',','); ?>&nbsp;</strong></td>
                </tr>
<? }else{ ?>
     			 <tr>
       				 <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>

 				</tbody>                
              </table>


           </fieldset>              

            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close();">关闭</a></li></ul></div>
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
            </form>
        <br style="clear:both;" />		
    </div>
    
<? include_once ("bottom_content.php");?>
</body>
</html>