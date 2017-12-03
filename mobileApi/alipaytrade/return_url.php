<?php
/* *
 * 功能：支付宝页面跳转同步通知页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 */
header("Content-type: text/html; charset=utf-8");
include_once ("../common.php");

$input		= new Input;
$in			= $input->parse_incoming();
// $in  		= $input->_htmlentities($in);
$db  = dbconnect::dataconnect()->getdb();
unset($in['request_method']);

if(empty($in)) exit('Access Deny');

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'AlipayApiController.class.php';

//↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓文本记录↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓//begin
$kLog = KLogger::instance(AliPay_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
//转码
$logMsg = '支付宝同步通知：' . urldecode(http_build_query($in));
$kLog->logInfo($logMsg);
//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑文本记录↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑//

//处理商户信息
//商户订单号
$out_trade_no = $in['out_trade_no'];
//支付宝交易号
$trade_no = $in['trade_no'];
//交易状态
$total_amount = $in['total_amount'];	    //获取总价格
$cinfo = $db->get_row("select PayID,PayCompany,PayClient,PaySN,PayStatus from ".DB_DATABASEU.DATATABLE."_order_alipay where PaySN = '".$out_trade_no."' order by PayID desc limit 0,1");
$companyid = $cinfo['PayCompany'];
$clientid  = $cinfo['PayClient'];

if(!empty($companyid)){//当前交易订单号是否存在，不存在时不通过
	$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
	if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;

	$payinfo = $db->get_row("select AccountsID,AccountsNO, AccountsName, PayPartnerID,PayKey,AppID from ".$datacbase.".".DATATABLE."_order_accounts where AccountsCompany=".$companyid." and AliPhone='T'  and PayType='alipay' limit 0,1");
	$mainname    = $payinfo['AccountsName'];
	$accountid   = $payinfo['AccountsID'];
}

//当前返回的商户支付宝ID和平台中的商户支付宝ID是否一致，不一致时不通过
if($payinfo['PayPartnerID'] != $in['seller_id']){
	
}

//重组支付宝配置参数
$config['app_id'] = $payinfo['AppID'];

$alipaySevice = new AlipayTradeService($config); 
$result = $alipaySevice->check($in);

/* 实际验证过程建议商户添加以下校验。
1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
4、验证app_id是否为该商户本身。
*/
if($result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	$url = WEB_ROOT_URL.'/mobileApi/template/yjf_web.html';
	header("location: ".$url);
	exit;
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

// 	//商户订单号

// 	$out_trade_no = htmlspecialchars($in['out_trade_no']);

// 	//支付宝交易号

// 	$trade_no = htmlspecialchars($in['trade_no']);
		
// 	echo "验证成功<br />外部订单号：".$out_trade_no;

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
	$url = WEB_ROOT_URL.'/mobileApi/template/out_yjf_web.html';
	header("location: ".$url);
	exit;
    //验证失败
//     echo "验证失败";
}
?>