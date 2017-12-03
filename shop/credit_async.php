<?php 

//++++++++++++++++++++++++++++++++++++++
//		易极付异步通知页面
//
//++++++++++++++++++++++++++++++++++++++++

include_once ('common.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$in = array_merge($_GET, $_POST);

$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区

$logType	= $in['resultCode'] == 'EXECUTE_SUCCESS' ? 'logInfo' : 'logError';
$logMsg		= "【异步通知：".$in['resultMessage']."】【Service：".$in['service']."】：".http_build_query($in);
//转码
$logMsg = urldecode($logMsg);

//获取映射关系
$YOpenApiSet 	= new YOpenApiSet();
$dhbOrder		= $YOpenApiSet->getMapForCredit($in['orderNo']);

//备份异步通知数据，供系统背景通知使用
$backendSyn = $in;

//准备验签
$sign	= $in['sign'];
unset($in['request_method'], $in['sign'], $in['m']);

//初始化
$YOpenApiDo = new YOpenApiDo($backendSyn, $dhbOrder['CompanyID']);
$kLog->logInfo($logMsg);
//成功状态[这里可优化]
if($in['resultCode'] == 'EXECUTE_SUCCESS'){

	//本地验签字符
	ksort($in);
	$YOpenApiDo->setCreditSpecificGetWay()->sign($in);
	
	//验签
	if($sign == $YOpenApiDo->commonPost['sign']){
		//自动执行
		$service = $in['service'];
		$service = $service . 'ForCredit';
		$YOpenApiDo->$service();
		echo 'success';
	}else{
		//同步跳转
		$status = array(
				'service'		=> $in['service'],
				'verify_Result'	=> '验签失败',
				'dhbPayOrder' 	=> trim($dhbOrder['DHBOrderNO'], ","),
				'showInfo'		=> $in['resultMessage'] ? $in['resultMessage'] : '验签失败',
				'pay_Result'	=> $in['resultMessage'] ? $in['resultMessage'] : '验签失败',
				'merchantId'	=> $in['partnerId'] ? $in['partnerId'] : '-',
				'orderNo'		=> $in['tradeNo'] ? $in['tradeNo'] : $in['orderNo'],
				'orderDatetime' => $in['notifyTime'] ? $in['notifyTime'] : date('Y-m-d H:i:s'),
				'payAmount'		=> $in['service'] == 'commonTradePay' ? $in['payAmount'] / 100 : $in['amount'],
				'amountIn' 		=> $in['amountIn'],
				'payDatetime'	=> date('Y-m-d H:i:s')
		);
	
		$kLog->logError('【验签失败】'.$logMsg);
// 		$YOpenApiDo->goToUrl($status);
	}
	
}else{//失败状态
	$kLog->logError('resultCode 非法！');
	
	//同步跳转
	$status = array(
			'service'		=> $in['service'],
			'verify_Result'	=> '本次操作失败',
			'dhbPayOrder' 	=> trim($dhbOrder['DHBOrderNO'], ","),
			'showInfo'		=> $in['resultMessage'] ? $in['resultMessage'] : '本次操作失败',
			'pay_Result'	=> $in['resultMessage'] ? $in['resultMessage'] : '本次操作失败',
			'merchantId' 	=> $in['partnerId'] ? $in['partnerId'] : '-',
			'orderNo' 		=> $in['tradeNo'] ? $in['tradeNo'] : $in['orderNo'],
			'orderDatetime' => $in['notifyTime'] ? $in['notifyTime'] : date('Y-m-d H:i:s'),
			'payAmount' 	=> $in['service'] == 'commonTradePay' ? $in['payAmount'] / 100 : $in['amount'],
			'amountIn' 		=> $in['amountIn'],
			'payDatetime' 	=> date('Y-m-d H:i:s')
	);

	$kLog->logError('【resultCode不为EXECUTE_SUCCESS】'.$logMsg);
// 	$YOpenApiDo->goToUrl($status);
}

?>