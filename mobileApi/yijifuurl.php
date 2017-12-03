<?php
	include_once ("common.php");
	include_once ("global.config.php");

	$db		= dbconnect::dataconnect()->getdb();
	$db->cache_dir  = CONF_PATH_CACHE;
	$log   = KLogger::instance(LOG_PATH, KLogger::INFO);

	$input		=	new Input;
	$in			=	$input->parse_incoming();

	$param		=   json_decode($in['v'],true);

	if (empty ( $param['sKey'] ))
	{
		$rdata['rStatus'] = 110;
		$rdata['error']   = '参数错误';
	}
	else{
		if($param['m']=="yijifu"){
		    $rdata   = '您的APP版本过低，我们的快捷支付已全面升级，请升级最新版本！';
			echo $rdata;
			exit;
			
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			$sdatabase  = $cidarr['Database'];
				
				$prePayTotal   = $param['total'];	//提交的支付金额
				$send['total'] = 0;
				$orderTotal    = 0;
				$accinfo = YOpenApiController::show_getway('yijifu',$cidarr,$param['acType']);			
				if(!empty($param['OID'])){
					$arrayOID = explode(',',$param['OID']);
					foreach ($arrayOID as $key => $var){
						$oinfo 				  = YOpenApiController::getorderinfo($var,$cidarr);
						$orderTotal			 += $oinfo['OrderTotal'] - $oinfo['OrderIntegral'];
						$send['osn']		 .= $oinfo['OrderSN'].",";
					//	$send['total']		= $oinfo['OrderTotal'];
					}
				}			
		
				$oinfo['OrderTotal'] = $orderTotal;
				
				//无手续费
				$show_money = $prePayTotal;
				$show_money = round($show_money,2);
				
				//准备写入数据
				//支付金额
				$send['total']          = $prePayTotal;
				$send['orderMoney']		= $oinfo['OrderTotal'];
				$send['orderAmount']	= $show_money;
				$send['ext2']			= $send['osn'];
				$send['orderDatetime']	= date('Ymdhis');
				$send['payType']		= '';
				$send['errorCode']		= '';
				$send['verifyMsg']		= '';
				$send['issuerId']		= '';
				if($param['isApp'] == 'true'){
					$send['VisitType']       =   'APP';
				}else{
					$send['VisitType']       =   'WEB';
				}
				
				$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
				$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
				$kLog->logInfo('orderSN:	'.$send['osn']);
				$kLog->logInfo('orderAmount:	'.$send['orderAmount']);

				//定商户ID，暂时解决方案
				$accinfo['MerchantNO'] = YAPI_PARTNERID;
				$send['acType'] = strval($param['acType']);

				//提交数据
				$openApi = new YopenApiFront($clientid,$cid);

				$send['orderNo'] = $openApi->orderNo;

				$isin = YOpenApiController::save_netpay($send,$accinfo, 'yijifu',$cidarr); //保存支付信息
	
				$openApi->setGetway($param['acType'])->createOrder($send);
			}
		}
		elseif($param['m']=="userInfo"){
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			
    			$openApi    = new YopenApiFront($clientid,$cid);
    			$send['userId']			   = $param['userId'];
    			$returnarr = $openApi->setGetway($param['acType'])->userInfo($send);
    			
    			if(!empty($returnarr)){
    				$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
					$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
					
					if(($returnarr['resultCode'] == 'EXECUTE_SUCCESS')){
					 	$logType = 'logInfo';
					}else{
						$logType = 'logError';
					}
					$logMsg = "【".$returnarr['resultMessage']."】【Service：".$returnarr['service']."】：".http_build_query($returnarr);
					
					//获取映射关系
					$YOpenApiSet 	= new YOpenApiSet();
					$dhbOrder		= $YOpenApiSet->getMap($returnarr['orderNo']);
	    			
	    			//初始化
					$YOpenApiDo = new YOpenApiDo($returnarr, $dhbOrder['CompanyID']);
					$kLog->logInfo($logMsg);
					if($returnarr['userStatus'] == 'NORMAL'){
						$service = $returnarr['service'];
						$returnarr['dhbUserid'] = $clientid;
						$returnarr['ClientCompany'] = $cid;
						$YOpenApiDo->$service($returnarr);
						$rdata['rStatus'] = 100;
						$rdata['error']   = '激活成功！';
					}
    			}
    			
    		}
		}
		elseif($param['m']=="setAccount"){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '您的APP版本过低，我们的快捷支付已全面升级，请升级最新版本！';
		    $rdatamsg = json_encode($rdata);
			$rdatamsg = str_replace("\n","",$rdatamsg);
			$rdatamsg = str_replace("\t","",$rdatamsg);
			$rdatamsg = str_replace('"rData":null','"rData":[]',$rdatamsg);
			$rdatamsg = str_replace('null','""',$rdatamsg);
			echo $rdatamsg = str_replace("\r","",$rdatamsg);
			exit;
			
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			
    			do {
    				$openApi   = new YopenApiFront($clientid,$cid);
	    			//$send['username'] 		   = $param['mobile'].'_1452';
	    			$send['username'] 		   = $param['mobile'].'_'.rand(1000,9999);
	    			//提交数据
	    			$returnarr = $openApi->setGetway($param['acType'])->checkUserName($send);
    			}
    			while (($returnarr['resultCode'] != 'EXECUTE_SUCCESS')||(!empty($returnarr['isExist'])));
    			
    			$openApi   = new YopenApiFront($clientid,$cid);
    			$send['mobile']			   = $param['mobile'];
    			$send['email']		  	   = $param['email'];
    			$send['realName']		   = $param['realName'];
    			$send['regAddress']		   = ($cidarr['ClientInfo']['ClientAdd'] ? $cidarr['ClientInfo']['ClientAdd'] : '无');
    			$send['certNo']	  		   = strtoupper($param['certNo']);	
    			$send['clientid']	  	   = $clientid;	
    			
    			$returnAcc = $openApi->setGetway($param['acType'])->setAccount($send);
    			
    			if(empty($returnAcc['resultCode'])){
    				$rdata['rStatus'] = 101;
					$rdata['error']   = '开户失败！';
				}
				else{			
					$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
					$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
					
					if(($returnAcc['resultCode'] == 'EXECUTE_SUCCESS')){
					 	$logType = 'logInfo';
					}else{
						$logType = 'logError';
					}
					$logMsg = "【".$returnAcc['resultMessage']."】【Service：".$returnAcc['service']."】：".http_build_query($returnAcc);
					if($returnAcc['success']){
	    				$returnAcc['success'] = 'true';
					}else{
						$returnAcc['success'] = 'false';
					}
					$sign	= $returnAcc['sign'];
					unset($returnAcc['sign']);

	    			//获取映射关系
					$YOpenApiSet 	= new YOpenApiSet();
					$dhbOrder		= $YOpenApiSet->getMap($returnAcc['orderNo']);
	    			
	    			//初始化
					$YOpenApiDo = new YOpenApiDo($returnAcc, $dhbOrder['CompanyID']);
	    			if($returnAcc['resultCode'] == 'EXECUTE_SUCCESS'){
						//成功状态
						$kLog->logInfo($logMsg);
						
						//本地验签字符
						ksort($returnAcc);
						$YOpenApiDo->setGetway()->sign($returnAcc);
						//验签
						if($sign == $YOpenApiDo->commonPost['sign']){
							//执行的方法
							$service = $returnAcc['service'];
							$returnAcc['dhbUserid'] = $clientid;
							$YOpenApiDo->$service($returnAcc);
							$rdata['rStatus'] = 100;
							$rdata['error']   = '开户成功！';
						}else{
							$rdata['rStatus'] = 101;
							$rdata['error']   = '开户失败！';
							$kLog->logError('验签失败>>>>>>'.$logMsg);
						}
	    			}
	    			else{
	    				$kLog->logError($logMsg);
	
	    				$rdata['rStatus'] = 101;
						$rdata['error']   = '开户失败！';
	    			}
				}
				
    			$rdatamsg = json_encode($rdata);
				$rdatamsg = str_replace("\n","",$rdatamsg);
				$rdatamsg = str_replace("\t","",$rdatamsg);
				$rdatamsg = str_replace('"rData":null','"rData":[]',$rdatamsg);
				$rdatamsg = str_replace('null','""',$rdatamsg);
				echo $rdatamsg = str_replace("\r","",$rdatamsg);
    		}
		}
		elseif($param['m']=="actAccount"){
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			
    			$openinfo = YOpenApiController::getOpenapiInfo($clientid,$cid);
    			
    			$send['userId']   = $openinfo['YapiUserId'];
    			$send['clientid'] = $clientid;
				//提交数据
				$openApi = new YopenApiFront($clientid,$cid);		
				
				if($param['isApp'] == 'true'){
					$send['VisitType']       =   'APP';
				}else{
					$send['VisitType']       =   'WEB';
				}
				
				$isin = YOpenApiController::save_netpay($send,$accinfo, 'yijifu',$cidarr); //保存支付信息
							
				$openApi->setGetway($param['acType'])->actAccount($send);
    		}
		}
		elseif($param['m']=="qftSetAccount"){
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			
    			$openApi   = new YopenApiFront($clientid,$cid);
    			
    			$send['clientid'] = $clientid;
    			
				if($param['isApp'] == 'true'){
					$send['VisitType']       =   'APP';
				}else{
					$send['VisitType']       =   'WEB';
				}
				$isin = YOpenApiController::save_netpay($send,$accinfo, 'yijifu',$cidarr); //保存开户信息
    			
    			//提交数据
    			$returnarr = $openApi->setGetway($param['acType'])->qftSetAccount($send);
    			
    		}
		}
		elseif($param['m']=="payOrder"){
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			$sdatabase  = $cidarr['Database'];
				
				$prePayTotal   = $param['total'];	//提交的支付金额
				$send['total'] = 0;
				$orderTotal    = 0;
				$accinfo = YOpenApiController::show_getway('yijifu',$cidarr,$param['acType']);	
				$cartinfo   = YOpenApiController::getCartDetail($param['OID'],$cidarr);		
				if(!empty($param['OID'])){
					$arrayOID = explode(',',$param['OID']);
					foreach ($arrayOID as $key => $var){
						$oinfo 				  = YOpenApiController::getorderinfo($var,$cidarr);
						$orderTotal			 += $oinfo['OrderTotal'] - $oinfo['OrderIntegral'];
						$send['osn']		 .= $oinfo['OrderSN'].",";
					}
				}
				$oinfo['OrderTotal'] = $orderTotal;

				//无手续费
				$show_money = $prePayTotal;
				$show_money = round($show_money,2);
				
				//准备写入数据
				//支付金额
				$send['total']          = $prePayTotal;
				$send['orderMoney']		= $oinfo['OrderTotal'];
				$send['orderAmount']	= $show_money;
				$send['ext2']			= $send['osn'];
				$send['orderDatetime']	= date('Ymdhis');
				$send['payType']		= '';
				$send['errorCode']		= '';
				$send['verifyMsg']		= '';
				$send['issuerId']		= '';
				$send['cartdetail']		= $cartinfo;
				if($param['isApp'] == 'true'){
					$send['VisitType']       =   'APP';
				}else{
					$send['VisitType']       =   'WEB';
				}
				
				$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
				$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
				$kLog->logInfo('orderSN:	'.$send['osn']);
				$kLog->logInfo('orderAmount:	'.$send['orderAmount']);
				$kLog->logInfo('VisitType:	'.$send['VisitType']);

				//定商户ID，暂时解决方案
				$accinfo['MerchantNO'] = YAPI_PARTNERID;
				$send['acType'] = strval($param['acType']);

				//提交数据
				$openApi = new YopenApiFront($clientid,$cid);

				$send['orderNo'] = $openApi->orderNo;

				$isin = YOpenApiController::save_netpay($send,$accinfo, 'yijifu',$cidarr); //保存支付信息
				
				$openApi->setGetway($param['acType'])->payOrder($send);
    		}
		}
		elseif($param['m']=="goLogin"){
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			
    			if($param['isApp'] == 'true'){
					$send['VisitType']       =   'APP';
				}else{
					$send['VisitType']       =   'WEB';
				}
    			
    			$openApi   = new YopenApiFront($clientid,$cid);
    			//提交数据
    			$returnarr = $openApi->setGetway($param['acType'])->goLogin($send);
    			
    		}
		}elseif($param['m']=="certifyStatus"){
			$cidarr = YOpenApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			
    			$openApi   = new YopenApiFront($clientid,$cid);
    			//提交数据
    			$msg  = $openApi->certifyStatus();
    			
    			$status = array(
                    'UNAUTHERIZED'  => '未认证',
                    'AUTHORIZED'    => '已认证',
                    'INAUTHORIZED'  => '认证中',
                    'REJECT'        => '被驳回',
                    'UNOPEN'        => '未开通快捷支付'
   				);
   				
   				if($msg['certifyStatus'] == 'AUTHORIZED'){
   					$rdata['certifyStatus'] = 'T';
   				}else{
   					$rdata['certifyStatus'] = 'F';
   				}
    			
    			$rdata['rStatus'] = 100;
				$rdata['error']   = '';
				$rdata['certifyStatus']   = $rdata['certifyStatus'];
    			$rdatamsg = json_encode($rdata);
				$rdatamsg = str_replace("\n","",$rdatamsg);
				$rdatamsg = str_replace("\t","",$rdatamsg);
				$rdatamsg = str_replace('"rData":null','"rData":[]',$rdatamsg);
				$rdatamsg = str_replace('null','""',$rdatamsg);
				echo $rdatamsg = str_replace("\r","",$rdatamsg);
    		}
		}
    }
    
    

    
    
	
?>