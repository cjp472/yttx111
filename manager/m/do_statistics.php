<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
if(empty($in['action']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if(!empty($in['clientid']))
{
	$sqll = " and OrderUserID=".$in['clientid']." ";
	$sql2 = " and ReturnClient=".$in['clientid']." ";
	$clientrowdata = $db->get_row("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".$in['clientid']." limit 0,1");
	$titleend = ' ('.$clientrowdata['ClientCompanyName'].' ) ';
}else{
	$sqll = "";
	$sql2 = "";
	$titleend = "";
}

if($in['action'] == "product"){
	if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
	if(empty($in['enddate'])) $in['enddate'] = date("Y-m-d");	
	$outfilename = "product_stat_".$in['begindate']."_".$in['enddate'];
	$statsql  = "SELECT sum(ContentNumber) as cnum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' group by c.ContentID order by cnum desc";
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 商品订购数据 ";

}elseif($in['action']=="between")
{
	if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
	if(empty($in['enddate'])) $in['enddate'] = date("Y-m-d");	
	$outfilename = "stat_".$in['begindate']."_".$in['enddate'];
	$statsql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' group by left(OrderSN,8)";
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 订单数据 ";

					$statsql0  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus=0 group by left(OrderSN,8)";
					$rdata = $db->get_results($statsql0);
					
					$totalnumber0  = 0;
					$totalprice0   = 0;
					foreach($rdata as $rvar)
					{
						$rarr[$rvar['ODate']] = $rvar['totalnumber'];
						$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
						$totalprice0 = $totalprice0 + $rvar['OTotal'];
					}


}
elseif($in['action']=="between_return")
{
	if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
	if(empty($in['enddate'])) $in['enddate'] = date("Y-m-d");	
	$outfilename = "stat_".$in['begindate']."_".$in['enddate'];
	$statsql  = "SELECT left(ReturnSN,9) as ODate,sum(ReturnTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sql2." and FROM_UNIXTIME(ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) group by left(ReturnSN,9)";
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 退单数据 ";

}elseif($in['action']=="year"){
	if(empty($in['y'])) $in['y'] = date("Y");
	$outfilename = "stat_".$in['y'];
	$statsql  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." group by left(OrderSN,6)";
	$titlemsg = $in['y']."年 订单数据";


					$statsql0  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus=0 group by left(OrderSN,6)";
					$rdata = $db->get_results($statsql0);

					$totalnumber0  = 0;
					$totalprice0   = 0;
					foreach($rdata as $rvar)
					{
						$rarr[$rvar['ODate']] = $rvar['totalnumber'];
						$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
						$totalprice0  = $totalprice0 + $rvar['OTotal'];
					}


}elseif($in['action']=="month"){
	if(empty($in['y'])) $in['y'] = date("Y");
	if(empty($in['m'])) $in['m'] = date("m");
	$outfilename = "stat_".$in['y']."_".$in['m'];
	$statsql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['m']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." group by left(OrderSN,8)";
	$titlemsg = $in['y']." 年 ".$in['m']."月 订单数据";

					$statsql0  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['m']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus=0 group by left(OrderSN,8)";
					$rdata = $db->get_results($statsql0);

					$totalnumber0  = 0;
					$totalprice0   = 0;
					foreach($rdata as $rvar)
					{
						$rarr[$rvar['ODate']] = $rvar['totalnumber'];
						$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
						$totalprice0 = $totalprice0 + $rvar['OTotal'];
					}


}elseif($in['action']=="day"){
	if(empty($in['cordate'])) $in['cordate'] = date("Y-m-d");
	$outfilename = "stat_".$in['cordate'];
	$datemsg = str_replace("-","",$in['cordate']);
	$statsql  = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderDate,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and left(OrderSN,8)='".$datemsg."' order by OrderID asc";
	$titlemsg = $in['cordate']." 订单数据";

}

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=".$outfilename.".xls");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<style type="text/css">
<!--
body { margin:0; padding:0; font-size:12px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#333;}
h4{ font-size:14px;font-weight:bold; margin:0; padding:0; float:left; height:auto;}
h1{color:#000000; font-size:18px; line-height:24px; padding-top:20px; font-weight:bold; font-family:"黑体",Verdana, Arial, Helvetica, sans-serif;}

a{text-decoration:none; color:#277DB7; font-family:Verdana, Arial, Helvetica, sans-serif; }
a:hover{text-decoration:underline; color:#cc0000; font-family:Verdana, Arial, Helvetica, sans-serif;}

a.buttonb{text-decoration:none; color:#277DB7;  border:#277DB7 solid 1px; padding:2px; }
a.buttonb:hover{text-decoration:none; color:#cc0000;  border:#cc0000 solid 1px; padding:2px;}

td,div,p{color:#333333; font-size:12px; line-height:180%; font-family:Verdana, Arial, Helvetica, sans-serif;}
.td_line{border:solid .5pt #666666; padding:0cm 2pt 0cm 2pt; background-color:#ffffff; border-right:none; font-weight:bold;height:28px;}
.tdr_line{border:solid .5pt #666666; padding:2pt; background-color:#ffffff; font-weight:bold; }
.tl_line{border-top:none; border-left:solid .5pt #666666; border-bottom:solid .5pt #666666; border-right:none; padding:0cm 2pt 0cm 2pt;background-color:#FFFFFF; }
.tr_line{border-top:none; border-left:solid .5pt #666666; border-bottom:solid .5pt #666666; border-right:solid .5pt #666666; padding:2pt; background-color:#FFFFFF;height:28px;  }
-->
</style>

</head>
<body>
<h1 align="center"><? echo $titlemsg.$titleend;?> </h1>
<?
/*****************************/
if($in['action']=="product")
{
	$statdata = $db->get_results($statsql);
	if(!empty($statdata))
	{
?>

        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="12%" class="td_line">ID</td>
				  <td class="td_line">商品名称</td>
                  <td width="20%" class="tdr_line">订购数量</td>
                </tr>
     		   </thead>
			 
			 <tbody>
			 <?
				$totalm = 0;
				foreach($statdata as $var)
				{
					$totalm = $totalm + $var['cnum'];
			 ?>
			 <tr >

				  <td class="tl_line"><? echo $var['ContentID'];?></td>
				  <td class="tl_line"><? echo $var['ContentName'];?></td>
                  <td class="tr_line"><? echo $var['cnum'];?></td>
			 </tr>
			 <? 
				 }			 
			 ?>
			 <tr >

				  <td class="tl_line"><strong>合计：</strong></td>
				  <td class="tl_line"><strong>&nbsp;<? echo count($statdata);?>个</strong></td>
                  <td class="tr_line"><strong> <? echo $totalm;?> 件</strong></td>
			 </tr>
			 </tbody>
			</table>

<?
	}

}elseif($in['action']=="day")
{
	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $cvar)
	{
		$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
	}
?>		  

			  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="20%" class="td_line">订单号</td>
				  <td class="td_line">药店</td>
                  <td width="18%" class="td_line">订单金额</td>
				  <td width="20%" class="tdr_line">下单时间</td>
				  <td width="16%" class="tdr_line">状态</td>
                </tr>
     		 </thead>
			 
			 <tbody>
			 <?
			$totalm = 0;
			$statdata = $db->get_results($statsql);
			foreach($statdata as $var)
			{
			 ?>
			 <tr>
				  <td class="tl_line"><? echo $var['OrderSN'];?></td>
				  <td class="tl_line"><? echo $clientarr[$var['OrderUserID']];?></td>
                  <td class="tl_line">¥ <? echo $var['OrderTotal'];?></td>
                  <td class="tl_line"><? echo date("Y-m-d H:i",$var['OrderDate']);?></td>
				  <td class="tr_line"><? echo $order_status_arr[$var['OrderStatus']];?></td>
			 </tr>
			 <? 
					$totalm = $totalm + $var['OrderTotal'];
			 }			 
			 ?>
			 <tr>
				  <td class="tl_line"><strong>合计：</strong></td>
				  <td class="tl_line"><strong>&nbsp;<? echo count($statdata);?></strong></td>
                  <td class="tl_line"><strong>¥ <? echo $totalm;?> </strong></td>
                  <td class="tl_line">&nbsp;</td>
				  <td class="tr_line">&nbsp;</td>
			 </tr>
			 </tbody>
			</table>
<?
}
elseif($in['action']=="between_return")
{	 
			$statdata = $db->get_results($statsql);
			 ?>

			  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="25%" class="td_line">日期</td>
				  <td width="25%" class="td_line">退单金额</td>
                  <td class="tdr_line">退单数</td>
				  
                </tr>
     		 </thead>
			 
			 <tbody>
			 <?
				$totalm = 0;
				$totaln = 0;
				foreach($statdata as $var)
				{
					$var['ODate'] = substr($var['ODate'],1);
					$totalm = $totalm + $var['OTotal'];
					$totaln = $totaln + $var['totalnumber'];
			 ?>
			 <tr >
				  <td class="tl_line"><? echo $var['ODate'];?></td>
				  <td class="tl_line">¥ <? echo $var['OTotal'];?></td>
                  <td class="tr_line"><? echo $var['totalnumber'];?></td>

			 </tr>
			 <? 
				 }			 
			 ?>
			 <tr >

				  <td class="tl_line"><strong>合计：</strong></td>
				  <td class="tl_line">¥ <? echo $totalm;?> </td>
                  <td class="tr_line"> <? echo $totaln;?> </td>
			 </tr>
			 </tbody>
			</table>


<?
}else{
	$statdata = $db->get_results($statsql);
	if(!empty($statdata))
	{


?>

			  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="30%" class="td_line">日期</td>
				  <td width="30%" class="td_line">订单金额</td>
                  <td  class="td_line">订单数</td>
				  <td  class="tdr_line">未审核订单</td>
                </tr>
     		 </thead>
			 
			 <tbody>
			 <?
				$totalm = 0;
				$totaln = 0;
				foreach($statdata as $var)
				{
					$totalm = $totalm + $var['OTotal'];
					$totaln = $totaln + $var['totalnumber'];
			 ?>
			 <tr >

				  <td class="tl_line"><? echo $var['ODate'];?></td>
				  <td class="tl_line">¥ <? echo $var['OTotal'];?></td>
                  <td class="tl_line"><? echo $var['totalnumber'];?></td>
				 <td class="tr_line"><? if(empty($rarr[$var['ODate']])) echo '0'; else echo $rarr[$var['ODate']];?></td>

			 </tr>
			 <? 
				 }			 
			 ?>
			 <tr >
				  <td class="tl_line"><strong>合计：</strong></td>
				  <td class="tl_line"><strong>¥ <? echo $totalm;?> </strong></td>
                  <td class="tl_line"><strong> <? echo $totaln;?> </strong></td>
				  <td class="tr_line"><strong><? echo $totalnumber0.'个 (¥ '.$totalprice0.')';?></strong></td>
			 </tr>
			 </tbody>
			</table>
<?
	}
}
?>
</body>
</html>