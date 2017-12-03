<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

include_once ("../common.php");
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

$inv = new Input();
$in  = $inv->parse_incoming();
$db  = dbconnect::dataconnect()->getdb();
$in  = $inv->_htmlentities($in);

//↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓文本记录↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓//begin tubo 增加日志接收记录 2015-11-18
$kLog = KLogger::instance(AliPay_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
//转码
$logMsg = '异步通知：' . urldecode(http_build_query($in));
$kLog->logInfo($logMsg);
//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑文本记录↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑//

//商户订单号
$out_trade_no = $in['out_trade_no'];
//支付宝交易号
$trade_no = $in['trade_no'];
//交易状态
$trade_status = $in['trade_status'];
$total_fee    = $in['total_fee'];	    //获取总价格

$cinfo = $db->get_row("select PayID,PayCompany,PayClient,PaySN,PayStatus from ".DB_DATABASEU.DATATABLE."_order_alipay where PaySN = '".$out_trade_no."' order by PayID desc limit 0,1");
$companyid = $cinfo['PayCompany'];
$clientid  = $cinfo['PayClient'];

if(!empty($companyid)){
	$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
	if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
	
    $payinfo = $db->get_row("select AccountsID,AccountsNO, AccountsName, PayPartnerID,PayKey from ".$datacbase.".".DATATABLE."_order_accounts where AccountsCompany=".$companyid." and AliPhone='T'  and PayType='alipay' limit 0,1");
    $mainname    = $payinfo['AccountsName'];
    $accountid   = $payinfo['AccountsID'];
}

//合作身份者id，以2088开头的16位纯数字
$alipay_config['partner']               = $payinfo['PayPartnerID'];

//安全检验码，以数字和字母组成的32位字符
$alipay_config['key']                   = $payinfo['PayKey'];

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	$kLog->logInfo('验签成功：  '.$trade_no);
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
	if(empty($in['subject']) || $in['subject']=="预付款" )  $in['subject'] = "0";
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
	
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	 if($in['trade_status'] == 'TRADE_FINISHED' || $in['trade_status'] == 'TRADE_SUCCESS') {
	 		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
			$db->query("update ".DB_DATABASEU.DATATABLE."_order_alipay set PayMoney='".$total_fee."',PayTradeNO='".$trade_no."',PayBuyer='".$in['buyer_email']."',PayStatus='".$in['trade_status']."' where PayID = ".$cinfo['PayID']." limit 1");

			$finfo = $db->get_row("select FinanceID,FinanceCompany,FinanceOrder from ".$datacbase.".".DATATABLE."_order_finance where FinanceCompany = ".$companyid." and FinancePaysn = '".$out_trade_no."' limit 0,1");

	    	if(empty($finfo['FinanceID'])){
				$sql_l  = "insert into ".$datacbase.".".DATATABLE."_order_finance(FinanceCompany,FinanceClient,FinanceOrder,FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,FinanceToDate,FinanceUpDate,FinanceDate,FinanceUser,FinanceFlag,FinancePaysn,FinanceType,FinanceFrom,FinanceDevice) values(".$companyid.", ".$clientid.", '".$in['subject']."', ".$accountid.", '".$total_fee."', '', '".$in['body']."', '".date("Y-m-d")."', ".time().", ".time().",'-',2,'".$out_trade_no."','O','alipay','Mobile')";       
				$status	= $db->query($sql_l);

				if(!empty($in['subject']) && $in['subject']!="0" ){
					$sql_o  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".$datacbase.".".DATATABLE."_order_orderinfo where OrderCompany=".$companyid." and INSTR('".$in['subject'].",',OrderSN) > 0 order by OrderID asc ";
					//$log->logInfo('allinpay-sql_o', $sql_o);
					$olist  =  $db->get_results($sql_o);
					if(!empty($olist))
					{
						$chatotal = $total_fee;
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
								}else{
									$uptotal = $chatotal + $osv['OrderTotal'];
									$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=3, OrderIntegral='".$uptotal."' where OrderID = '".$osv['OrderID']."' limit 1";
									$isup  = $db->query($upsql);

									break;
								}
							}
							$lastosv = $osv['OrderSN'];
						}				
					}
				}
			}
		$db->query("update ".DATABASEU.DATATABLE."_order_alipay set PayStatus='".$in['trade_status']."' where PayID = ".$cinfo['PayID']." limit 1");
    }
	/*
    if($_POST['trade_status'] == 'TRADE_FINISHED') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
				
		//注意：
		//该种交易状态只在两种情况下出现
		//1、开通了普通即时到账，买家付款成功后。
		//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
				
		//注意：
		//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }*/

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
	echo "success";		//请不要修改或删除
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
	$kLog->logInfo('验签失败：  '.$trade_no);
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>