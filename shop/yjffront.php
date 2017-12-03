<?php 

//++++++++++++++++++++++++++++++++++++++
//		易支付同步通知页面
//
//++++++++++++++++++++++++++++++++++++++++

include_once ('common.php');

$in = array_merge($_GET, $_POST);

$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区

$logType = $in['resultCode'] == 'EXECUTE_SUCCESS' ? 'logInfo' : 'logError';
$logMsg = "【".$in['resultMessage']."】【Service：".$in['service']."】：".http_build_query($in);

//转码
$logMsg = urldecode($logMsg);

//直接跳转[只有支付有同步返回结果]
if(!empty($in['m']) && $in['m'] == 'show'){
	
	$in = array_merge($in, $_SESSION['YJFSynInfo']);
	unset($_SESSION['YJFSynInfo']);
}

//初始化安全校验
$security = new SecurityPay();

//resultCode、orderNo无报文返回或支付报文已被记录时，直接跳转
if(empty($in['resultCode']) || empty($in['orderNo']) || $security->getSecurityMd5('yijifu', $logMsg)){
	header('location:./my.php');
	exit;
}

//记录支付报文
$security->setSecurityMd5('yijifu', $logMsg);

//获取映射关系
$YOpenApiSet 	= new YOpenApiSet();
$dhbOrder		= $YOpenApiSet->getMap($in['orderNo']);

//备份异步通知数据，供系统背景通知使用
$backendSyn = $in;

//准备验签
$sign	= $in['sign'];
unset($in['request_method'], $in['sign'], $in['m']);

//初始化
//判断是不是为支付服务service为qftIntegratedPayment tubo 2016-1-19 是的话同步进去锁单
$resultCode = '';
if($in['service']=='qftIntegratedPayment'){
	$resultCode = 'DHB_WAIT';
	$send_result = $in['resultCode'];
	$in['resultCode'] = 'DHB_WAIT';
}
$YOpenApiDo = new YOpenApiDo($in, $dhbOrder['CompanyID']);
$kLog->logInfo($logMsg);
if( !empty($resultCode) ){//还原回来不然通不过验签
	$in['resultCode'] = $send_result;
}
//end 2016-1-19
//成功状态
if(($in['resultCode'] == 'PAY_SUCCESS')||($in['resultCode'] == 'EXECUTE_PROCESSING')||($in['resultCode'] == 'EXECUTE_SUCCESS')||($resultCode == 'DHB_WAIT') ){
	
	//本地验签字符
	ksort($in);
	$YOpenApiDo->setGetway()->sign($in);
	
	//验签
	if($sign == $YOpenApiDo->commonPost['sign']){
		//执行的方法
		$service = $in['service'];
		$YOpenApiDo->$service($in);
	}else{
		//同步跳转
		$status = array(
				'service'		=> $service,
				'showInfo'		=> '验签失败',
				'verify_Result'	=> '验签失败',
				'merchantId'	=> $in['partnerId'] ? $in['partnerId'] : '-',
				'orderNo'		=> $in['tradeNo'] ? $in['tradeNo'] : $in['orderNo'],
				'orderDatetime'	=> $in['notifyTime'] ? $in['notifyTime'] : date('Y-m-d H:i:s'),
				'payAmount'		=> $in['payAmount'] / 100,
				'returnStatus'	=> false,
				'payDatetime' 	=> date('Y-m-d H:i:s')
		);

		$kLog->logError('验签失败>>>>>>'.$logMsg);
		$YOpenApiDo->showStatus($status);
	}

}else{//失败状态
	$kLog->logError('resultCode 非法！');
	
	//同步跳转
	$status = array(
			'verify_Result'	=> '本次操作失败',
			'showInfo'		=> $in['resultMessage'] ? $in['resultMessage'] : '本次操作失败',
			'pay_Result'	=> $in['resultMessage'] ? $in['resultMessage'] : '本次操作失败',
			'merchantId'	=> $in['partnerId'] ? $in['partnerId'] : '-',
			'orderNo'		=> $in['tradeNo'] ? $in['tradeNo'] : $in['orderNo'],
			'orderDatetime' => $in['notifyTime'] ? $in['notifyTime'] : date('Y-m-d H:i:s'),
			'payAmount'		=> $in['payAmount'] / 100,
			'returnStatus'	=> false,
			'payDatetime'	=> date('Y-m-d H:i:s')
	);
	$YOpenApiDo->showStatus($status);
}
?>