<?php
$menu_flag = "consignment";
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("arr_data.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}
$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

/*****************************/
if($in['m']=="setSendFlag")
{
	if(!intval($in['ConsignmentID'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_consignment set ConsignmentFlag=2 where ConsignmentID = ".$in['ConsignmentID']." and ConsignmentCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{		
		$loinfo = $db->get_row("SELECT ConsignmentID,ConsignmentOrder,ConsignmentFlag FROM ".DATATABLE."_order_consignment where ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".$in['ConsignmentID']." limit 0,1");
		if(!empty($loinfo['ConsignmentOrder']))
		{
			$upinfo  = $db->get_row("SELECT OrderID,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderSN = '".$loinfo['ConsignmentOrder']."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
			$sendline = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where ContentSend < ContentNumber and CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			$sendlineg = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart_gifts where ContentSend < ContentNumber and CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			if($sendline['allrow'] > 0 || $sendlineg['allrow'] > 0)
			{
				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
			}else{
				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=4 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
			}
			$db->query("update ".DATATABLE."_order_orderinfo set OrderStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=2 ");			
			
			$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$upinfo['OrderID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '收货并确认', '管理方确认收到货...')";
			$db->query($sqlin);		
		}
		exit('ok');
	}else{
		exit('设置无变化!');
	}
}

exit('非法操作!');
?>