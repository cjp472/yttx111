<?php 

//++++++++++++++++++++++++++++++++++++++
//		易支付 供应商 同步通知页面
//
//++++++++++++++++++++++++++++++++++++++++

include_once ("../common.php");

$in = array_merge($_GET, $_POST);

//直接跳转[只有支付有同步返回结果]
if(empty($in['resultCode'])){
	header('location:./home.php');
	exit;
}

$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区

$logType = $in['resultCode'] == 'EXECUTE_SUCCESS' ? 'logInfo' : 'logError';
$logMsg = $in['resultMessage'].'：'.http_build_query($in);

//初始化
$YOpenApiDo = new YOpenApiDo($in);

//成功状态
if($in['resultCode'] == 'EXECUTE_SUCCESS'){
	$kLog->logInfo($logMsg);
	
}else{//失败状态
	$kLog->logError($logMsg);
	
}
?>





