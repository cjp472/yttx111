<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//易支付开户数据处理

class NetGetWay extends Common{

	//在线支付网关帐号
	function showGetway($getway = 'allinpay', $comanyID = 0, $signno = '', $muti = false){
	    
	    $comanyID = intval($comanyID);
		$sql_l  = "select 
						MerchantNO,SignNO,SignAccount,SignMsgKey,SignMsg,B2B,Fee,AccountType,IsDefault 
					from 
						".DATABASEU.DATATABLE."_order_getway 
					where 
						CompanyID=".$comanyID." 
						and Status='T' 
						and GetWay='".$getway."' 
						".($signno ? " and SignNO='".$signno."'" : "")."
				    group by AccountType
					order by AccountType desc 
					limit 2";
		
		$return = array();
		if($muti){
			$info = $this->db->get_results($sql_l);
			for($i = 0; $i < count($info); $i++){
				foreach($info[$i] as $key=>$value){
					$return[$i][$key] = trim($value);
				}
			}

		}else{
			$info = $this->db->get_row($sql_l);
			foreach($info as $key=>$value){
					$return[$key] = trim($value);
				}
		}
		
		return $return;
	}//END showGetway
	
	public function getDefaultWay($getway = 'allinpay', $comanyID = 0){
		
		$comanyID = intval($comanyID);
		$sql_l  = "select
						MerchantNO,SignNO,SignAccount,SignMsgKey,SignMsg,B2B,Fee,AccountType,IsDefault
					from
						".DATABASEU.DATATABLE."_order_getway
					where
						CompanyID=".$comanyID."
						and GetWay='".$getway."'
						and IsDefault='Y'
					limit 1";
		return $this->db->get_row($sql_l);
	}
	
	//[功能重复]根据账户类型获取账户ID
	public function getWayByType($getway = 'allinpay', $comanyID = 0, $getwayType = ''){
	
		$comanyID = intval($comanyID);
		$sql_l  = "select
						MerchantNO,SignNO,SignAccount,SignMsgKey,SignMsg,B2B,Fee,AccountType,IsDefault
					from
						".DATABASEU.DATATABLE."_order_getway
					where
						CompanyID=".$comanyID."
						and GetWay='".$getway."'
						and AccountType='".$getwayType."'
					limit 1";
		return $this->db->get_row($sql_l);
	}
	
	//保存开户资料
	//日志：今天事情有点多，先验证了正常数据情况
	public function saveGetWay(
    	                           $comanyID = 0, $merchantNO = '', $signNO = '', 
    	                           $signAccount = '', $accountType = '', 
    	                           $merchantName = '', $ewpayNO = '',
	                               $getWay = 'yijifu'
	                           ){
	    
	    $comanyID = intval($comanyID);
	    $isDefault = 'Y';
	    if($getWay == 'yijifu'){//处理默认账户，如果系统已存在易极付账户了(首个开户默认)，后面的就不再默认
	        $check  = "select 
	                           count(*) as total 
	                       from 
	                           ".DATABASEU.DATATABLE."_order_getway 
	                       where 
	                           CompanyID=".$comanyID." 
	                           and GetWay='yijifu'";
	        
	        $result    = $this->db->get_var($check);
	        
	        $isDefault = $result ? 'N' : 'Y';
	        
	        //账户类型
	        $accountType = $accountType == 'ENTERPRISE' ? 'company' : 'personal';
	        
	        //账户是否已存在
	        $checkSql  = "select 
	                           count(*) as total 
	                       from 
	                           ".DATABASEU.DATATABLE."_order_getway 
	                       where 
	                           CompanyID=".$comanyID." 
	                           and GetWay='yijifu'
	                           and SignNO='".$signNO."'";
	        
	        $cr    = $this->db->get_var($checkSql);
	        
	        if($cr) return false;
	    }


	    //写入数据
	    $insert = "insert into 
	                   ".DATABASEU.DATATABLE."_order_getway set 
	                       GetWay       = '".$getWay."',
	                       CompanyID    = ".$comanyID.",
	                       MerchantNO   = '".$merchantNO."',
	                       SignNO       = '".$signNO."',
	                       SignAccount  = '".$signAccount."',
	                       SignMsgKey   = '',
	                       AccountType  = '".$accountType."',
	                       IsDefault    = '".$isDefault."',
	                       MerchantName = '".$merchantName."',
	                       ewpayOrderNo = '".$ewpayNO."'
	                       ";

	    if($this->db->query($insert)) return true;
	    
// 	    //这里是发送短信的，该逻辑应该在controller里，缺少session，短信暂时取消
// 	    include_once ("../class/sms.class.php");
// 	    $GLOBALS['DB'] = $this->db;
// 	    $send_rst = sms::send_sms('13088070799',$msg,$info['ClientID']);
	}//END saveGetWay
	
}//EOC NetGetWay

?>