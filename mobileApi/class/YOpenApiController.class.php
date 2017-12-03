<?php

class YOpenApiController{
    	/**
    * 验证sKey,获取公司信息
	*@param string skey
	*@return array $rdata(rStatus,error,ClientID,CompanyID,CompanyName,CompanyDatabase) 状态，提示信息，公司ID,数据库
    *@author seekfor
    */
	public static function getCompanyInfo($param){
		$db	= dbconnect::dataconnect()->getdb();
		
		if (empty($param)){
			$rdata['rStatus'] = 110;
			$rdata['error']   = '参数错误!';
			return $rdata;
		}else{
			
			$rdata = get_cache($param);
			if(!empty($rdata) && $rdata['rStatus'] == '100'){
				return $rdata;
			}else{

				$cinfo = $db->get_row ( "select d.ClientID,c.CompanyID,c.CompanyName,c.CompanySigned,c.CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_dealers d inner join ".DB_DATABASEU.DATATABLE."_order_company c ON d.ClientCompany=c.CompanyID where d.TokenValue='".$param."' limit 0,1" );
		
				if(empty($cinfo['CompanyID'] )) {
					$rdata['rStatus'] = 119;
					$rdata['error']   = '登录超时，请重新登录！';
				}else{
					$rdata['rStatus'] 		= 100;
					$rdata['ClientID']    	= $cinfo['ClientID'];
					$rdata['CompanyID']   	= $cinfo['CompanyID'];
					$rdata['CompanyName']   = $cinfo['CompanyName'];
					$rdata['CompanySigned']   = $cinfo['CompanySigned'];
					if(empty($cinfo['CompanyDatabase'])) $rdata['Database'] = DB_DATABASE.'.'; else $rdata['Database'] = DB_DATABASE."_".$cinfo['CompanyDatabase'].'.';
				
					$clientInfo = $db->get_row("select ClientID,ClientLevel,ClientName,ClientCompanyName,ClientNO,ClientAdd,ClientTrueName,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientPay,ClientConsignment from ".$rdata['Database'].DATATABLE."_order_client where ClientID=".intval($cinfo['ClientID'])." limit 0,1");
					if(empty($clientInfo['ClientSetPrice'])) $clientInfo['ClientSetPrice'] = 'Price1';
					$rdata['ClientInfo']	= $clientInfo;
					
					//store_cache($param,$rdata);					
				}
				return $rdata;
			}						
		}		
	}
	
		//在线支付帐号
	function show_getway($getway='allinpay',$param,$acType)
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select MerchantNO,SignMsgKey,SignMsg,B2B,Fee,SignNO from ".DB_DATABASEU.DATATABLE."_order_getway where CompanyID = ".$param['CompanyID']." and Status='T' and signNO='".$acType."' and GetWay='".$getway."' order by GetWayID asc limit 0,1";       
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}
	
	
		//保存网银支付信息
	function save_netpay($inv,$acc, $getway = 'allinpay',$param)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sqlin = "insert into ".DB_DATABASEU.DATATABLE."_order_netpay(CompanyID,ClientID,GetWay,MerchantNO,OrderNO,OrderMoney,PayMoney,Remark,OrderDateTime,PayResult,ErrorCode,Bank,PayType,VerifyMsg,PayTradeNO,MerchantAccount) values(".$param['CompanyID'].", ".$param['ClientID'].", '".$getway."', '".$acc['MerchantNO']."', '".$inv['orderNo']."','".$inv['orderMoney']."','".$inv['orderAmount']."', '".$inv['ext2']."', '".$inv['orderDatetime']."', '0', '".$inv['errorCode']."', '".$inv['payType']."', '".$inv['issuerId']."', '".$inv['verifyMsg']."', '', '".$inv['acType']."')";

		$status = $db->query($sqlin);
		if($status)
		{
			return true;
		}else{
			$pinfo = $db->get_row("select count(*) as row from ".DB_DATABASEU.DATATABLE."_order_netpay where MerchantNO='".$acc['MerchantNO']."' and OrderNO='".$inv['orderNo']."' order by PayID desc limit 0,1");
			if($pinfo['row'] > 0) return true; else return false;			
		}
	}
	
		///读取购物车详情，易极付专用
	function getCartDetail($sn = '',$param){
		if(!$sn) return array();
		
		$psn	= explode(",", $sn);
		$snTmp	= array();
		foreach($psn as $v){
			if(empty($v)) continue;
			$snTmp[] = "'".$v."'";
		}
		$condition = implode(",", $snTmp);
		
		$db	  = dbconnect::dataconnect()->getdb();
		$csql = "select 
				  c.ContentName as title
				from
				  ".$param['Database'].DATATABLE."_order_cart AS c 
				  LEFT JOIN ".$param['Database'].DATATABLE."_order_orderinfo AS o 
				  ON c.OrderID=o.OrderID 
				where o.OrderID in (".$condition.")";
		
		$info = $db->get_results($csql);
		
		$length = count($info);
		$detail = array();
		for($i = 0; $i < $length; $i++){
			$detail[$i]['title'] = urlencode($info[$i]['title']);
		}
		unset($info);
				
		return $detail;
	}
	
		//订单详细
	function getorderinfo($sn,$param)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		if(strpos($sn,'-')) $sqlmsg = " OrderSN = '".$sn."' "; else $sqlmsg = " OrderID = ".intval($sn)." ";

				
		$sql_o = "select * from ".$param['Database'].DATATABLE."_order_orderinfo where ".$sqlmsg." and OrderCompany=".$param['CompanyID']." and OrderUserID=".$param['ClientID']." limit 0,1";

		$orderinfo    = $db->get_row($sql_o);

		return $orderinfo;
		unset($orderinfo);
	}
	
	//openapi开户详细
	function getOpenapiInfo($clientid,$company)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sql_o = "select * from ".DB_DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=".$company." and ClientID=".$clientid." limit 0,1";
		
		$openapiinfo    = $db->get_row($sql_o);

		return $openapiinfo;
		unset($openapiinfo);
	}
	
		//写入数据
	static function putMsg($filePath = '', $fileName = '', $message = ''){
		
		if(empty($filePath) || empty($fileName)) return false;
		
		if(!file_exists($filePath)){
			mkdir($filePath, 0777);
			chmod($filePath, 0777);
		}

		$rm = rtrim($filePath, '/')."/".$fileName;
		$handle = fopen($rm, "w+b") or die('Cannot open file: '.$rm);
		
		fwrite($handle, $message);
		fclose($handle);
	}
}
?>