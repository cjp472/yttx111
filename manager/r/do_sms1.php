<?php
$menu_flag = "sms";
include_once ("header.php");
include_once ("../WebService/include/Client.php");
include_once ("../soap.inc.php");

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="shownumber")
{
	if($_SESSION['uinfo']['userflag']!="9") exit('对不起，您没有此项操作权限！');
	$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
	$client->setOutgoingEncoding("UTF-8");

	$balance_end = $client->getBalance();
	$feed = $client->getEachFee();
	echo $numberend = $balance_end/$feed;
	exit();
}

/*****************************/

exit('非法操作!');
?>