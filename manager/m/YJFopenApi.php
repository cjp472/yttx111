<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$openApi	= new YopenApiBackend($_SESSION['uc']['CompanyID']);
$method		= strval($in['type']);
$acType		= strval($in['actype']);

$netGetWay = new NetGetWay();
$accinfo = $netGetWay->getWayByType('yijifu', $_SESSION['uc']['CompanyID'], $acType);

if(method_exists($openApi, $method)){
	$info = $openApi->setGetway($accinfo['SignNO'])->$method($in);
}else{
	echo '<p align="center">参数错误</p>';
}
exit;




?>