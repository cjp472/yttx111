<?php 

//++++++++++++++++++++++++++++++++++++++
//		易支付同步通知页面
//
//++++++++++++++++++++++++++++++++++++++++

include_once ('../common.php');

ini_set('display_errors',0);
error_reporting(E_ALL);

$string = 'merchOrderNo=20161215172113429500&message=%E5%BC%80%E9%80%9A%E6%94%AF%E4%BB%98%E8%B4%A6%E6%88%B7%E5%AE%A1%E6%A0%B8%E4%B8%AD&notifyUrl=http%3A%2F%2Ftest.yitong111.com%2Fmanager%2Fm%2Fasync.php&orderNo=20161215172113429500&outUserId=etong0000506&partnerId=20160612020000748352&protocol=httpGet&registerUserType=PERSONAL&resultCode=EXECUTE_SUCCESS&resultMessage=%E6%88%90%E5%8A%9F&returnUrl=http%3A%2F%2Ftest.yitong111.com%2Fmanager%2Fm%2Fonline.php&service=openPaymentAccount&sign=c706878a27af4cdb382518b7f31aac73&signType=MD5&status=SUCCESS&success=true&userId=20161215010000768205&version=1.0';

parse_str($string, $_POST);



$in = array_merge($_GET, $_POST);

// debug($in, 1);

$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区

$logType = $in['resultCode'] == 'EXECUTE_SUCCESS' ? 'logInfo' : 'logError';
$logMsg = "【".$in['resultMessage']."】【Service：".$in['service']."】：".http_build_query($in);

//转码
$logMsg = urldecode($logMsg);

// //直接跳转[只有支付有同步返回结果]
// if(!empty($in['m']) && $in['m'] == 'show'){
	
// 	$in = array_merge($in, $_SESSION['YJFSynInfo']);
// 	unset($_SESSION['YJFSynInfo']);
// }

//初始化安全校验
$security = new SecurityPay();

//resultCode、orderNo无报文返回或支付报文已被记录时，直接跳转
if(empty($in['resultCode']) || empty($in['orderNo']) || $security->getSecurityMd5('yijifu', $logMsg)){
	header('location:paytype.php?type='.$in['registerUserType'].'&message='.$in['message']);
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
$YOpenApiDo = new YOpenApiDo($in, $dhbOrder['CompanyID'], true);
$kLog->logInfo($logMsg);
if( !empty($resultCode) ){//还原回来不然通不过验签
	$in['resultCode'] = $send_result;
}
//end 2016-1-19
//成功状态
if(($in['resultCode'] == 'EXECUTE_SUCCESS')){
	
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