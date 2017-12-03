<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/php_rsa.php");
include_once (SITE_ROOT_PATH."/class/KLogger.php");

define("LOG_PATH", SITE_ROOT_PATH.'/data/log/');

$input		=	new Input;
$in			=	$input->parse_incoming();
$db  = dbconnect::dataconnect()->getdb();


$log    = KLogger::instance(LOG_PATH, KLogger::INFO);
$log->logInfo('receive-in', $in);

	$sql_l  = "select MerchantNO,SignMsgKey,SignMsg from ".DATABASEU.DATATABLE."_order_getway where MerchantNO = '".$in['merchantId']."' and Status='T' order by GetWayID asc limit 0,1";       
	$accinfo	= $db->get_row($sql_l);

	if(empty($accinfo['MerchantNO']) || empty($accinfo['SignMsgKey']) || empty($accinfo['SignMsg'])) exit('未开通网银支付');

	$merchantId=$_POST["merchantId"];
	$version=$_POST['version'];
	$language=$_POST['language'];
	$signType=$_POST['signType'];
	$payType=$_POST['payType'];
	$issuerId=$_POST['issuerId'];
	$paymentOrderId=$_POST['paymentOrderId'];
	$orderNo=$_POST['orderNo'];
	$orderDatetime=$_POST['orderDatetime'];
	$orderAmount=$_POST['orderAmount'];
	$payDatetime=$_POST['payDatetime'];
	$payAmount=$_POST['payAmount'];
	$ext1=$_POST['ext1'];
	$ext2=$_POST['ext2'];
	$payResult=$_POST['payResult'];
	$errorCode=$_POST['errorCode'];
	$returnDatetime=$_POST['returnDatetime'];
	$signMsg=$_POST["signMsg"];
	
	
	$bufSignSrc="";
	if($merchantId != "")
	$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";		
	if($version != "")
	$bufSignSrc=$bufSignSrc."version=".$version."&";		
	if($language != "")
	$bufSignSrc=$bufSignSrc."language=".$language."&";		
	if($signType != "")
	$bufSignSrc=$bufSignSrc."signType=".$signType."&";		
	if($payType != "")
	$bufSignSrc=$bufSignSrc."payType=".$payType."&";
	if($issuerId != "")
	$bufSignSrc=$bufSignSrc."issuerId=".$issuerId."&";
	if($paymentOrderId != "")
	$bufSignSrc=$bufSignSrc."paymentOrderId=".$paymentOrderId."&";
	if($orderNo != "")
	$bufSignSrc=$bufSignSrc."orderNo=".$orderNo."&";
	if($orderDatetime != "")
	$bufSignSrc=$bufSignSrc."orderDatetime=".$orderDatetime."&";
	if($orderAmount != "")
	$bufSignSrc=$bufSignSrc."orderAmount=".$orderAmount."&";
	if($payDatetime != "")
	$bufSignSrc=$bufSignSrc."payDatetime=".$payDatetime."&";
	if($payAmount != "")
	$bufSignSrc=$bufSignSrc."payAmount=".$payAmount."&";
	if($ext1 != "")
	$bufSignSrc=$bufSignSrc."ext1=".$ext1."&";
	if($ext2 != "")
	$bufSignSrc=$bufSignSrc."ext2=".$ext2."&";
	if($payResult != "")
	$bufSignSrc=$bufSignSrc."payResult=".$payResult."&";
	if($errorCode != "")
	$bufSignSrc=$bufSignSrc."errorCode=".$errorCode."&";
	if($returnDatetime != "")
	$bufSignSrc=$bufSignSrc."returnDatetime=".$returnDatetime;

	$pinfo = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_netpay where MerchantNO='".$in['merchantId']."' and OrderNO='".$in['orderNo']."' order by PayID desc limit 0,1");
	$payAmount = $payAmount / 100;

	if(strpos($_SERVER['SERVER_NAME'], "dhb.hk")){
		//通联验签
		$publickeyarray = explode(PHP_EOL, $accinfo['SignMsg']);
		$publickey = explode('=',$publickeyarray[0]);
		$modulus   = explode('=',$publickeyarray[1]);
		$modulus[1] = trim($modulus[1]);
		$publickey[1] = trim($publickey[1]);
		$keylength = 1024;	
		$verifyResult = rsa_verify($bufSignSrc,$signMsg, $publickey[1], $modulus[1], $keylength,"sha1");
	}else{
		//自已验签
		$postMsg   = md5($merchantId.'-'.$orderNo.'-'.$orderDatetime.'-'.$payAmount.'-'.$ext1.'-'.$ext2.'-'.$accinfo['SignMsg']);
		if($pinfo['VerifyMsg'] == $postMsg) $verifyResult = true; else $verifyResult = false;
	}
	$verify_Result = null;
	$pay_Result = null;
	if($verifyResult){
		$verify_Result = "报文验签成功!";
		if($payResult == 1){
			$pay_Result = "订单支付成功!";
			save_netpay($in,$pinfo, $db, $log);

		}else{
			$pay_Result = "订单支付失败!";
		}
	}else{
		$verify_Result = "报文验签失败!";
		$pay_Result = "因报文验签失败，订单支付失败!";
	}
	$log->logInfo('receive-result', $verify_Result.','.$pay_Result);

	//保存网银支付信息
	function save_netpay($inv,$pinfo,$db,$log)
	{
		if($pinfo['PayResult'] != '1')
		{
			$extarr = explode("_",$inv['ext1']);
			$clientid  = $extarr[1];
			$companyid = $extarr[0];
			$inv['payAmount'] = $inv['payAmount'] / 100;
			$sqlin = "update ".DATABASEU.DATATABLE."_order_netpay set PayTradeNO='".$inv['paymentOrderId']."',PayDateTime='".$inv['payDatetime']."', PayResult='".$inv['payResult']."', ErrorCode='".$inv['errorCode']."',ReturnDateTime='".$inv['returnDatetime']."' where PayID='".$pinfo['PayID']."' and CompanyID=".$pinfo['CompanyID']." and ClientID=".$pinfo['ClientID']." limit 1";			
			$log->logInfo('receive-sqlin', $sqlin);

			$status = $db->query($sqlin);
			if($status)
			{
				$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyid." limit 0,1");
				if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
				if((!empty($inv['ext2']) && strpos($inv['ext2'],',')) || empty($inv['ext2'])){ 
					$FinanceOrderID = 0;
				}else{
					$oinfo = $db->get_row("select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderPayStatus,OrderStatus from ".$datacbase.".".DATATABLE."_order_orderinfo where OrderSN='".$inv['ext2']."' and OrderCompany=".$companyid." limit 0,1");
					$FinanceOrderID = $oinfo['OrderID'];
				}

				$sql_l  = "insert into ".$datacbase.".".DATATABLE."_order_finance(FinanceCompany,FinanceClient,FinanceOrderID,FinanceOrder,FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,FinanceToDate,FinanceUpDate,FinanceDate,FinanceUser,FinanceFlag,FinancePaysn,FinanceType,FinanceFrom) values(".$companyid.", ".$clientid.", ".$FinanceOrderID.",'".$inv['ext2']."', 0, '".$inv['payAmount']."', '', '网银支付', '".date("Y-m-d")."', ".time().", ".time().",'',2,'".$inv['orderNo']."','O','allinpay')"; 
				$log->logInfo('sqlin_finance', $sql_l);

				$status	= $db->query($sql_l);
				if(!empty($inv['ext2'])){
					$sql_o  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".$datacbase.".".DATATABLE."_order_orderinfo where OrderCompany=".$companyid." and INSTR('".$inv['ext2'].",',OrderSN) > 0 order by OrderID asc ";
					$olist  =  $db->get_results($sql_o);
					if(!empty($olist))
					{
						$chatotal = $inv['payAmount'];
						foreach($olist as $osv)
						{
							if(!empty($osv['OrderTotal']))
							{
								$chatotal = $chatotal - $osv['OrderTotal'] + $osv['OrderIntegral'];				
								if($chatotal >= 0)
								{
									$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=2, OrderIntegral='".$osv['OrderTotal']."' where OrderID = '".$osv['OrderID']."' limit 1";
									$isup  = $db->query($upsql);
									$log->logInfo('upsql', $upsql);
								}else{
									$uptotal = $chatotal + $osv['OrderTotal'];
									$upsql = "update ".$datacbase.".".DATATABLE."_order_orderinfo set OrderPayStatus=3, OrderIntegral='".$uptotal."' where OrderID = '".$osv['OrderID']."' limit 1";
									$isup  = $db->query($upsql);
									$log->logInfo('upsql', $upsql);
									break;
								}
							}
							$lastosv = $osv['OrderSN'];
						}				
					}
				}
				return '支付成功！';
			}else{
				return '支付不成功！';
			}
		}
	}

?>