<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

header("Content-type: text/html; charset=utf-8");
include_once ("../common.php");
$input		=	new Input;
$in			=	$input->parse_incoming();
unset($in['request_method']);

if(empty($in)) exit('Access Deny');

$db  = dbconnect::dataconnect()->getdb();

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR."config.php";
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'AlipayApiController.class.php';

//↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓文本记录↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓//begin
$kLog = KLogger::instance(AliPay_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
//转码
$logMsg = '支付宝异步通知：' . urldecode(http_build_query($in));
$kLog->logInfo($logMsg);
//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑文本记录↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑//

//处理商户信息
//商户订单号
$out_trade_no = $in['out_trade_no'];
//支付宝交易号
$trade_no = $in['trade_no'];
//交易金额
$total_amount = $in['total_amount'];	//获取总价格

$cinfo = $db->get_row("select PayID,PayCompany,PayClient,PaySN,PayStatus from ".DB_DATABASEU.DATATABLE."_order_alipay where PaySN = '".$out_trade_no."' order by PayID desc limit 0,1");
$companyid = $cinfo['PayCompany'];
$clientid  = $cinfo['PayClient'];

if(!empty($companyid)){//当前交易订单号是否存在，不存在时不通过
	$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
	if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;

	$payinfo = $db->get_row("select AccountsID,AccountsNO, AccountsName, PayPartnerID,PayKey,AppID from ".$datacbase.".".DATATABLE."_order_accounts where AccountsCompany=".$companyid." and AliPhone='T'  and PayType='alipay' limit 0,1");
	$mainname    = $payinfo['AccountsName'];
	$accountid   = $payinfo['AccountsID'];
}else{
	echo "fail";
	exit;
}

//当前返回的商户支付宝ID和平台中的商户支付宝ID是否一致，不一致时不通过
if($payinfo['PayPartnerID'] != $in['seller_id']){
	echo "fail";
	exit;
}

//重组支付宝配置参数
$config['app_id'] = $payinfo['AppID'];

$alipaySevice = new AlipayTradeService($config); 
$alipaySevice->writeLog(var_export($in,true));
$result = $alipaySevice->check($in);

/* 实际验证过程建议商户添加以下校验。
1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
4、验证app_id是否为该商户本身。
*/
if($result) {//验证成功
	$kLog->logInfo('验签成功：  支付宝交易号==>'.$trade_no.'，商户订单号==>'.$out_trade_no);
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
	if(empty($in['subject']) || $in['subject']=="医统天下BMB平台预付款" )  $in['subject'] = "0";
	
	if($in['trade_status'] == 'TRADE_FINISHED' || $in['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
		//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
		//如果有做过处理，不执行商户的业务程序
		
		$db->query("update ".DB_DATABASEU.DATATABLE."_order_alipay set PayMoney='".$total_amount."',PayTradeNO='".$trade_no."',PayBuyer='".$in['buyer_id']."[".$in['buyer_logon_id']."]',PayStatus='".$in['trade_status']."' where PayID = ".$cinfo['PayID']." limit 1");
	
		$finfo = $db->get_row("select FinanceID,FinanceCompany,FinanceOrder from ".$datacbase.".".DATATABLE."_order_finance where FinanceCompany = ".$companyid." and FinancePaysn = '".$out_trade_no."' limit 0,1");
	
		if(empty($finfo['FinanceID'])){
			$sql_l  = "insert into ".$datacbase.".".DATATABLE."_order_finance(FinanceCompany,FinanceClient,FinanceOrder,FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,FinanceToDate,FinanceUpDate,FinanceDate,FinanceUser,FinanceFlag,FinancePaysn,FinanceType,FinanceFrom,FinanceDevice) values(".$companyid.", ".$clientid.", '".$in['subject']."', ".$accountid.", '".$total_amount."', '', '".$in['body']."', '".date("Y-m-d")."', ".time().", ".time().",'-',2,'".$out_trade_no."','O','alipay','Mobile')";
			$status	= $db->query($sql_l);
	
			if(!empty($in['subject']) && $in['subject']!="0" ){
				$sql_o  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".$datacbase.".".DATATABLE."_order_orderinfo where OrderCompany=".$companyid." and INSTR('".$in['subject'].",',OrderSN) > 0 order by OrderID asc ";
				//$log->logInfo('allinpay-sql_o', $sql_o);
				$olist  =  $db->get_results($sql_o);

				if(!empty($olist))
				{
					$chatotal = $total_amount;
					foreach($olist as $osv)
					{
						if(!empty($osv['OrderTotal']))
						{
							$chatotal = $chatotal - $osv['OrderTotal'] + $osv['OrderIntegral'];
							
							if($chatotal >= 0)
							{
								$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=2, OrderPayType=11, OrderIntegral='".$osv['OrderTotal']."' where OrderID = '".$osv['OrderID']."' limit 1";
								$isup  = $db->query($upsql);
								//$log->logInfo('allinpay-upsql', $upsql);
								$sqlin = "insert into ".$datacbase.".".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$companyid.", ".$osv['OrderID'].", '终端', '终端',".time().", '支付宝支付', '通过支付宝在线支付 ¥ ".round($osv['OrderTotal'], 2)." 元')";
								$db->query($sqlin);
							}else{
								$uptotal = $chatotal + $osv['OrderTotal'];
								$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=3, OrderIntegral='".$uptotal."' where OrderID = '".$osv['OrderID']."' limit 1";
								$isup  = $db->query($upsql);
	
								$sqlin = "insert into ".$datacbase.".".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$companyid.", ".$osv['OrderID'].", '终端', '终端',".time().", '支付宝支付', '通过支付宝在线支付 ¥ ".round($uptotal, 2)." 元')";
								$db->query($sqlin);
								break;
							}
						}
						$lastosv = $osv['OrderSN'];
					}
				}
			}
		}
		$db->query("update ".DB_DATABASEU.DATATABLE."_order_alipay set PayStatus='".$in['trade_status']."' where PayID = ".$cinfo['PayID']." limit 1");
	}

	echo "success";		//请不要修改或删除
		
}else {
    //验证失败
    echo "fail";	//请不要修改或删除

}

?>