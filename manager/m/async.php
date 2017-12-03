<?php 

//++++++++++++++++++++++++++++++++++++++
//		易极付异步通知页面
//
//++++++++++++++++++++++++++++++++++++++++

include_once ("../common.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

$in = array_merge($_GET, $_POST);

$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区

$logType	= $in['resultCode'] == 'EXECUTE_SUCCESS' ? 'logInfo' : 'logError';
$logMsg		= "【异步通知：".$in['resultMessage']."】【Service：".$in['service']."】：".http_build_query($in);
//转码
$logMsg = urldecode($logMsg);

//初始化
$YOpenApiDo = new YOpenApiDo($in);

//获取映射关系
$YOpenApiSet 	= new YOpenApiSet();
$dhbOrder		= $YOpenApiSet->getMap($in['orderNo']);

//成功状态[这里可优化]
if(($in['resultCode'] == 'EXECUTE_SUCCESS')){
	$kLog->logInfo($logMsg);
	
	//接口验签初始化
	$sign	= $in['sign'];
	unset($in['request_method'], $in['sign']);
	$YOpenApi = new YopenApiBackend($dhbOrder['CompanyID']);
	ksort($in);
	$YOpenApi->setGetway()->sign($in);
	
	//验签
	if($sign == $YOpenApi->commonPost['sign']){
		//自动执行
		$service = $in['service'];
		if(method_exists($YOpenApiDo, $service)){
			$YOpenApiDo->$service();
			echo 'success';
		}
	}else{
		$kLog->logError('【验签失败】'.$logMsg);
	}
	
}else{//失败状态
	$kLog->logError($logMsg);
}
?>