<?php

class AlipayApiController{
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
	function show_alipay($param,$acType)
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select AccountsID,AccountsNO, AccountsName, PayPartnerID,PayKey,APPID from ".$param['Database'].DATATABLE."_order_accounts where AccountsCompany = ".$param['CompanyID']."  and PayType='alipay' and AliPhone='T' and AccountsID=".$acType." limit 0,1 ";    
		$result	= $db->get_row($sql_l);
		
		if(empty($result['AccountsNO']) || empty($result['PayPartnerID']) || empty($result['PayKey'])){
			unset($result);
			$result['rStatus'] = 101;
			$result['error']   = '支付宝集成信息有误，暂不能在线支付，请使用其他支付方式！';
		}else{
			$result['rStatus'] = 100;
			return $result;
			unset($result);
		}
	}
	
	
		//保存网银支付信息
	function save_alipay($inv, $param)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sqlin = "insert into ".DB_DATABASEU.DATATABLE."_order_alipay(PayCompany,PayClient,PaySN,PayOrder,PayBody,PayMoney,PayType,PayMethod,PayDate,PayAliType) values( ".$param['CompanyID'].",".$param['ClientID'].",'".$inv['out_trade_no']."','".$inv['orderNO']."','".$inv['AliBody']."','".$inv['AliMoney']."','alipay','transter',".time().",'mobile')";

		$status = $db->query($sqlin);
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