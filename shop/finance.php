<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/orderdata.php");
include_once (SITE_ROOT_PATH."/class/sms.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();

if($in['m']=="delfinance"){
	
	$status = orderdata::delfinance($in['kid']);
	if($status)
	{
		exit("ok");
	}else{
		exit('操作失败，已确认到帐的付款单不能再删除！');
	}

}elseif($in['m']=="new"){
	
	$urlmsg		= "";
	$alipayarr	= null;
	$payTotal	= $ytotal = 0;
	$acclist	= orderdata::listaccounts();
	$getway		= orderdata::show_getway('allinpay');
	
	//易极付网关信息
	$NetGetWay = new NetGetWay();
	$netInfo = $NetGetWay->showGetway('yijifu', $_SESSION['ucc']['CompanyID'], '', true);
	
	//当前经销商在易极付是否已开户
	$myYJF = array();
	$YOpenApiSet = new YOpenApiSet();
	$myYJF = $YOpenApiSet->getSignInfo(intval($_SESSION['cc']['cid']));

	var_dump($_SESSION);
	var_dump($myYJF);
	
	//若开通易极付、支付宝、通联，默认使用在线支付
	if(empty($in['ty'])){
		if(!empty($myYJF) || !empty($getway['MerchantNO']) || !empty($alipayarr)){
			$in['ty'] = 'O';
		}else{
			$in['ty'] = 'Y';
		}
	}
	
	foreach($acclist as $v){
		$accarr[$v['AccountsID']] = $v['AccountsBank']."(".$v['AccountsNO'].")";
		if($v['PayType']=='alipay') $alipayarr = $v;
	}
	if($in['ty'] == "Y"){
		$ytotal = orderdata::get_client_money();
		if($payTotal > $ytotal) $payTotal = $ytotal;
		if($ytotal <= 0) $payTotal = 0;
		$ytotal = round($ytotal,2);
	}

	if(!empty($in['id'])){
		$oinfo   = orderdata::getorderinfo($in['id']);
		if($oinfo['OrderPayStatus'] == 2 || $oinfo['OrderPayStatus'] == 4){//该订单已支付完毕
			header("location:./myorder.php");
			exit;
		}
		$pinfo   = orderdata::getpaylist($oinfo);
		if($oinfo['OrderIntegral'] > $oinfo['OrderTotal']){
			$oinfo['OrderIntegral'] = $oinfo['OrderTotal'];
		}
		$payTotal = $oinfo['OrderTotal']-$oinfo['OrderIntegral'];
		/**
		if(!empty($pinfo)){
			foreach($pinfo as $p){
				$payTotal = $payTotal - $p['FinanceTotal'];
			}
		}
		**/
		if($payTotal < 0) $payTotal = 0;
		$payTotal = round($payTotal,2);
	}else{
		$ordlist = orderdata::listordersn();
	}
	
	include template("finance_add");

}elseif($in['m']=="pay"){
	
	$accinfo = orderdata::showaccounts();
	if(empty($accinfo['AccountsNO']) || empty($accinfo['PayPartnerID']) || empty($accinfo['PayKey'])) $ispay = "no"; else $ispay = 'pay';

	if(!empty($in['OID'])){
	 	$oinfo = orderdata::getorderinfo($in['OID']); 
	 	$oinfo['OrderTotal'] = $oinfo['OrderTotal'] - $oinfo['OrderIntegral'];
	 	
	 	if($oinfo['OrderPayStatus'] == 2 || $oinfo['OrderPayStatus'] == 4){//该订单已支付完毕
			header("location:./myorder.php");
			exit;
		}
	}
	else{ 
		$oinfo['OrderSN'] = "0";
	}
	if(!empty($in['total'])) $oinfo['OrderTotal'] = $in['total'];

	if(!empty($in['osn'])){
		$oinfo['OrderSN'] = $in['osn'];	
	}

	$paysn = date("Ymd").microtime_float();
	include template("finance_pay");

}elseif($in['m']=="yijifu"){

	$prePayTotal = $in['total'];	//提交的支付金额
	$accinfo = orderdata::show_getway('yijifu');
	if(!empty($in['OID'])){
		$oinfo 			= orderdata::getorderinfo($in['OID']);
		$oinfo['OrderTotal'] = $oinfo['OrderTotal'] - $oinfo['OrderIntegral'];
		$in['osn']		= $oinfo['OrderSN'].",";
		//$in['total']	= $oinfo['OrderTotal'];  tubo 修改注释这句，修改total金额为用户输入金额，而不是订单剩余金额
		
		if($oinfo['OrderPayStatus'] == 2 || $oinfo['OrderPayStatus'] == 4){//该订单已支付完毕
			header("location:./myorder.php");
			exit;
		}
	}
	else{
		$oinfo['OrderSN'] = "0";
	}
	if(!empty($in['total'])) $oinfo['OrderTotal'] = $in['total'];
	
	//无手续费
	$show_money = $oinfo['OrderTotal'];
	$show_money = round($show_money,2);
	
	if(!empty($in['osn'])){
		$oinfo['OrderSN'] = $in['osn'];
	}
	
	//准备写入数据
	//支付金额
	$in['orderMoney']		= $oinfo['OrderTotal'];
	$in['orderAmount']		= $show_money;
	$in['ext2']				= $in['osn'];
	$in['orderDatetime']	= date('Ymdhis');
	$in['payType']			= '';
	$in['errorCode']		= '';
	$in['verifyMsg']		= '';
	$in['issuerId']			= '';
	$in['cartdetail']		= orderdata::getCartDetail($in['osn']);
	
	//加单验证是否是预付款
	if(empty($in['osn']) || empty($in['osn'])){
		$in['cartdetail'] = array();
	}
	
	//定商户ID，暂时解决方案
	$accinfo['MerchantNO'] = YAPI_PARTNERID;
	$in['acType'] = strval($in['acType']);
	
	//获取默认账户
	$NetGetWay = new NetGetWay();
	$actype = $NetGetWay->getDefaultWay('yijifu', $_SESSION['cc']['ccompany']);
	$in['acType'] = $actype['SignNO'];
	
	//提交数据
	$openApi = new YopenApiFront($_SESSION['cc']['cid']);
	$in['orderNo'] = $openApi->orderNo;
	$isin = orderdata::save_netpay($in,$accinfo, 'yijifu'); //保存支付信息
	
	$openApi->setGetway($in['acType'])->payOrder($in);
}elseif($in['m']=="netpay"){
	
	$accinfo = orderdata::show_getway('allinpay');
	if(empty($accinfo['MerchantNO']) || empty($accinfo['SignMsgKey']) || empty($accinfo['SignMsg'])) $ispay = "no"; else $ispay = 'pay';

	if(!empty($in['OID'])){
	 	$oinfo = orderdata::getorderinfo($in['OID']); 
	 	$oinfo['OrderTotal'] = $oinfo['OrderTotal'] - $oinfo['OrderIntegral'];
	 	
	 	if($oinfo['OrderPayStatus'] == 2 || $oinfo['OrderPayStatus'] == 4){//该订单已支付完毕
			header("location:./myorder.php");
			exit;
		}
	}
	else{ 
		$oinfo['OrderSN'] = "0";
	}
	if(!empty($in['total'])) $oinfo['OrderTotal'] = $in['total'];
	//手续费
	if($accinfo['Fee'] == 'Pay'){
		$show_money = $oinfo['OrderTotal'] * (1+0.005);
		$sxf = round($oinfo['OrderTotal'] * 0.005,2);
	}else{
		$show_money = $oinfo['OrderTotal'];
	}
	$show_money = round($show_money,2);
	if(!empty($in['osn'])){
		$oinfo['OrderSN'] = $in['osn'];	
	}
	$paysn = "N".$_SESSION['cc']['ccompany']."-".date("Ymd").microtime_float();
	$paysn = str_replace(".", "_", $paysn);

	include template("netpay");

}elseif($in['m']=="to_netpay"){
	
	$accinfo = orderdata::show_getway('allinpay');
	if(empty($accinfo['MerchantNO']) || empty($accinfo['SignMsgKey']) || empty($accinfo['SignMsg'])) $ispay = "no"; else $ispay = 'pay';
	if(!empty($_SERVER['SERVER_NAME'])) $paybackurl = 'http://'.$_SERVER['SERVER_NAME'].'/'; else $paybackurl = PAY_URL;
	
	$pickupUrl  = $paybackurl.'allinpay.php';
	$receiveUrl = $paybackurl.'receive_allinpay.php';
	
	$merchantId = $accinfo['MerchantNO'];
	$serviceUrl = 'https://service.allinpay.com/gateway/index.do';
	$in['ext1'] = $_SESSION['cc']['ccompany'].'_'.$_SESSION['cc']['cid'];

	//手续费支付方
	$in['orderMoney'] = round($in['orderAmount'],2);
	if($accinfo['Fee'] == 'Pay'){
		if($in['payType'] == "4"){
			$in['orderAmount'] = $in['orderMoney'] + 20;
			$in['extTL'] = ($in['orderMoney']*100).'|'.(20*100);
		}else{
			$in['orderAmount'] = $in['orderMoney'] * (1+0.005);
			$in['extTL'] = ($in['orderMoney']*100).'|'.($in['orderMoney'] * 0.005 * 100);
		}
		//$in['extTL'] = '<U00010>'.$in['extTL'].'</U00010>';
	}else{
		$in['extTL'] = '';	
	}

	$in['orderAmount'] = round($in['orderAmount'],2);
	
	$in['verifyMsg'] = md5($merchantId.'-'.$in['orderNo'].'-'.$in['orderDatetime'].'-'.$in['orderAmount'].'-'.$in['ext1'].'-'.$in['ext2'].'-'.$accinfo['SignMsg']);
	
	$isin = orderdata::save_netpay($in,$accinfo); //保存支付信息
//	if(!$isin) exit('支付信息有误！');
	$in['orderAmount'] = $in['orderAmount'] * 100;
	
	// 生成签名字符串。
	$bufSignSrc=""; 
	if($in['inputCharset'] != "")
	$bufSignSrc=$bufSignSrc."inputCharset=".$in['inputCharset']."&";		
	if($pickupUrl != "")
	$bufSignSrc=$bufSignSrc."pickupUrl=".$pickupUrl."&";		
	if($receiveUrl != "")
	$bufSignSrc=$bufSignSrc."receiveUrl=".$receiveUrl."&";		
	if($in['version'] != "")
	$bufSignSrc=$bufSignSrc."version=".$in['version']."&";		
	if($in['language'] != "")
	$bufSignSrc=$bufSignSrc."language=".$in['language']."&";		
	if($in['signType']!= "")
	$bufSignSrc=$bufSignSrc."signType=".$in['signType']."&";		
	if($merchantId != "")
	$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";		
	if($in['payerName'] != "")
	$bufSignSrc=$bufSignSrc."payerName=".$in['payerName']."&";		
	if($in['payerEmail'] != "")
	$bufSignSrc=$bufSignSrc."payerEmail=".$in['payerEmail']."&";		
	if($in['payerTelephone'] != "")
	$bufSignSrc=$bufSignSrc."payerTelephone=".$in['payerTelephone']."&";			
	if($in['payerIDCard'] != "")
	$bufSignSrc=$bufSignSrc."payerIDCard=".$in['payerIDCard']."&";			
	if($in['pid'] != "")
	$bufSignSrc=$bufSignSrc."pid=".$in['pid']."&";		
	if($in['orderNo'] != "")
	$bufSignSrc=$bufSignSrc."orderNo=".$in['orderNo']."&";
	if($in['orderAmount'] != "")
	$bufSignSrc=$bufSignSrc."orderAmount=".$in['orderAmount']."&";
	if($in['orderCurrency'] != "")
	$bufSignSrc=$bufSignSrc."orderCurrency=".$in['orderCurrency']."&";
	if($in['orderDatetime'] != "")
	$bufSignSrc=$bufSignSrc."orderDatetime=".$in['orderDatetime']."&";
	if($in['orderExpireDatetime'] != "")
	$bufSignSrc=$bufSignSrc."orderExpireDatetime=".$in['orderExpireDatetime']."&";
	if($in['productName'] != "")
	$bufSignSrc=$bufSignSrc."productName=".$in['productName']."&";
	if($in['productPrice'] != "")
	$bufSignSrc=$bufSignSrc."productPrice=".$in['productPrice']."&";
	if($in['productNum'] != "")
	$bufSignSrc=$bufSignSrc."productNum=".$in['productNum']."&";
	if($in['productId'] != "")
	$bufSignSrc=$bufSignSrc."productId=".$in['productId']."&";
	if($in['productDesc'] != "")
	$bufSignSrc=$bufSignSrc."productDesc=".$in['productDesc']."&";
	if($in['ext1'] != "")
	$bufSignSrc=$bufSignSrc."ext1=".$in['ext1']."&";
	if($in['ext2'] != "")
	$bufSignSrc=$bufSignSrc."ext2=".$in['ext2']."&";
	if($in['extTL'] != "")
	$bufSignSrc=$bufSignSrc."extTL=".$in['extTL']."&";
	if($in['payType'] != "")
	$bufSignSrc=$bufSignSrc."payType=".$in['payType']."&";		
	if($in['issuerId'] != "")
	$bufSignSrc=$bufSignSrc."issuerId=".$in['issuerId']."&";
	if($in['pan'] != "")
	$bufSignSrc=$bufSignSrc."pan=".$in['pan']."&";	
	if($in['tradeNature'] != "")
	$bufSignSrc=$bufSignSrc."tradeNature=".$in['tradeNature']."&";	
	$bufSignSrc=$bufSignSrc."key=".$accinfo['SignMsgKey']; //key为MD5密钥，密钥是在通联支付网关商户服务网站上设置。
	
	//签名，设为signMsg字段值。
	$signMsg = strtoupper(md5($bufSignSrc));

	include template("to_netpay");

}elseif($in['m']=="content"){

	if(empty($in['ID'])) exit("错误参数!");
	$urlmsg  = "";
	$finance = orderdata::showfinance($in['ID']);

	include template("finance_content");

}elseif($in['m']=="guestadd"){
	
	if(empty($in['data_FinanceAccounts']) && $in["data-ty"]=="Z") exit("收款账号不能为空!");
	$status = orderdata::subaccounts($in);
	if($status) exit('ok'); else exit('提交不成功，请检查数据格式是否正确!');

}elseif($in['m']=="expense"){
	
	$expense = orderdata::listexpense();
	
	include template("expense");


}else{

	$accinfo = orderdata::showaccounts();
	if(empty($accinfo['AccountsNO']) || empty($accinfo['PayPartnerID']) || empty($accinfo['PayKey'])) $ispay = "no"; else $ispay = 'pay';
	$accinfo = orderdata::show_getway('allinpay');

	$conlist = orderdata::listfinance($in['status'],12,'finance.php');

	include template("finance");
//END
}
?>