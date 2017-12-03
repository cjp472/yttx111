<?php
include_once ("../common.php");
$inv   = new Input();
$in    = $inv->parse_incoming();
$db	 = dbconnect::dataconnect()->getdb();

/*****************************/
if(empty($_SESSION['uinfo']['userid']) || empty($_SESSION['uc']['CompanyID']))
{
	exit('isouttime');
}
if(empty($_SESSION['m_ordernumber'])) $_SESSION['m_ordernumber'] = 0;

if($in['m']=="refresh")
{
	$orinfo  = $db->get_row("SELECT count(*) as orow FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=0 limit 0,1");

	if(!empty($orinfo['orow']) && ($orinfo['orow'] > $_SESSION['m_ordernumber']))
	{
		$_SESSION['m_ordernumber'] = $orinfo['orow'];
		echo $orinfo['orow'];
		exit('');
	}else{		
		exit('');
	}
}
exit('');
?>