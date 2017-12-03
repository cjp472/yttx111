<?php
$menu_flag = "sms";
include_once ("header.php");
include_once ("../WebService/include/Client.php");


$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="shownumber1")
{
	include_once ("../soap.inc.php");
	if($_SESSION['uinfo']['userflag']!="9") exit('对不起，您没有此项操作权限！');
	$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
	$client->setOutgoingEncoding("UTF-8");
	$statusCode = $client->login();

	$balance_end = $client->getBalance();
	$feed = $client->getEachFee();
	echo $numberend = $balance_end/$feed;
	error_log ( $numberend." ".date("Y-m-d H:i").'\n\r', 3, SITE_ROOT_PATH."/data/sms_number.txt");
	exit();
}

if($in['m']=="shownumber2")
{
	include_once ("../soap2.inc.php");
	if($_SESSION['uinfo']['userflag']!="9") exit('对不起，您没有此项操作权限！');
	$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
	$client->setOutgoingEncoding("UTF-8");
	$statusCode = $client->login();

	$balance_end = $client->getBalance();
	$feed = $client->getEachFee();
	echo $numberend = $balance_end/$feed;
	exit();
}

if($in['m']=="shownumber3")
{
	include_once ("../pro/soap.inc.php");
	if($_SESSION['uinfo']['userflag']!="9") exit('对不起，您没有此项操作权限！');
	$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
	$client->setOutgoingEncoding("UTF-8");
	$statusCode = $client->login();

	$balance_end = $client->getBalance();
	$feed = $client->getEachFee();
	echo $numberend = $balance_end/$feed;
	exit();
}

/*****************************/

exit('非法操作!');
?>