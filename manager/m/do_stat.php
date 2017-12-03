<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("../class/data.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

/*****************************/
if($in['m']=="showorderlist")
{
	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $cvar)
	{
		$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
	}

	if($in['stype'] == "day")
	{
		$headersmg = '<table width="94%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td align="left" width="28%" class="bottomlinebold">订单号</td>
				  <td align="left" class="bottomlinebold">药店</td>
                  <td align="right" width="20%" class="bottomlinebold">订单金额</td>
                </tr>
     		 </thead>			 
			 <tbody>';

		if(!empty($in['cid'])) $sqll = " and OrderUserID = ".$in['cid']." "; else $sqll = "";
		$statsql  = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderDate from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and left(OrderSN,8)='".$in['did']."' and OrderStatus!=8 and OrderStatus!=9 order by OrderID asc limit 0,1000";
		$statdata = $db->get_results($statsql);
		$totalm = 0;
		if(!empty($statdata))
		{			
			foreach($statdata as $var)
			{
				$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left">'.$var['OrderSN'].'</td>
				  <td align="left">'.$clientarr[$var['OrderUserID']].'</td>
                  <td class="TitleNUM">¥ '.$var['OrderTotal'].'</td>
			 </tr>';
			 $totalm = $totalm + $var['OrderTotal'];
			}
		}
		$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left"><strong>合计：</strong></td>
				  <td align="left"><strong>&nbsp;'.count($statdata).'个</strong></td>
                  <td  class="TitleNUM"><strong>¥ '.$totalm.'</strong></td>
			 </tr>
			 </tbody>
			</table>';
		echo $headersmg;
	}
	elseif($in['stype'] == "orderlistproduct")
	{

		$statsql  = "SELECT c.OrderID,c.ClientID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentNumber from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID WHERE c.CompanyID=".$_SESSION['uinfo']['ucompany']." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and c.ContentID=".$in['did']." and OrderStatus!=8 and OrderStatus!=9 order by ID ASC limit 0,1000";
		$statdata = $db->get_results($statsql);
		$totalm = 0;
		if(!empty($statdata))
		{			
			$headersmg = '<div align="left"><strong>'.$statdata[0]['ContentName'].'</strong></div><table width="94%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td align="left" width="12%" class="bottomlinebold">订单号</td>
				  <td align="left" class="bottomlinebold">药店</td>
				  <td align="left" width="20%" class="bottomlinebold">商品属性</td>
                  <td align="right" width="20%" class="bottomlinebold">订购数量</td>
                </tr>
     		 </thead>			 
			 <tbody>';	
			foreach($statdata as $var)
			{
				$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left">'.$var['OrderID'].'</td>
				  <td align="left">'.$clientarr[$var['ClientID']].'</td>
                  <td align="left">'.$var['ContentColor'].'/'.$var['ContentSpecification'].'</td>
                  <td class="TitleNUM">'.$var['ContentNumber'].'</td>
			 </tr>';
			 $totalm = $totalm + $var['ContentNumber'];
			}
		}
		$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td >&nbsp;</td>
				  <td align="left"><strong>合计：</strong></td>
				  <td align="left"><strong>&nbsp;'.count($statdata).' 个</strong></td>
                  <td  class="TitleNUM"><strong> '.$totalm.' 件</strong></td>
			 </tr>
			 </tbody>
			</table>';
		echo $headersmg;
	}

exit();
}

if($in['m']=="showreturnlist")
{
	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $cvar)
	{
		$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
	}

	if($in['stype'] == "day")
	{
		$headersmg = '<table width="94%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td align="left" width="28%" class="bottomlinebold">退单号</td>
				  <td align="left" class="bottomlinebold">药店</td>
                  <td align="right" width="20%" class="bottomlinebold">退单金额</td>
                </tr>
     		 </thead>			 
			 <tbody>';

		if(!empty($in['cid'])) $sqll = " and ReturnClient = ".$in['cid']." "; else $sqll = "";
		$statsql  = "SELECT ReturnID,ReturnSN,ReturnClient,ReturnTotal,ReturnDate from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and left(ReturnSN,9)='R".$in['did']."' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) order by ReturnID asc limit 0,1000";
		$statdata = $db->get_results($statsql);
		$totalm = 0;
		if(!empty($statdata))
		{			
			foreach($statdata as $var)
			{
				$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left">'.$var['ReturnSN'].'</td>
				  <td align="left">'.$clientarr[$var['ReturnClient']].'</td>
                  <td class="TitleNUM">¥ '.$var['ReturnTotal'].'</td>
			 </tr>';
				$totalm = $totalm + $var['ReturnTotal'];
			}
		}
		$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left"><strong>合计：</strong></td>
				  <td align="left"><strong>&nbsp;'.count($statdata).'个</strong></td>
                  <td  class="TitleNUM"><strong>¥ '.$totalm.'</strong></td>
			 </tr>
			 </tbody>
			</table>';
		echo $headersmg;
	}

exit();
}

exit('非法操作!');
?>