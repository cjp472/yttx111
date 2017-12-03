<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//易支付开户数据处理

class NetGetWay extends Common{

	//在线支付网关帐号
	function showGetway($getway = 'allinpay', $comanyID = 0, $signno = '', $muti = false){
		
		$sql_l  = "select 
						MerchantNO,SignNO,SignAccount,SignMsgKey,SignMsg,B2B,Fee,AccountType,IsDefault 
					from 
						".DB_DATABASEU.DATATABLE."_order_getway 
					where 
						CompanyID=".$comanyID." 
						and Status='T' 
						and GetWay='".$getway."' 
						".($signno ? " and SignNO='".$signno."'" : "")."
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
			if(!empty($info)){
				foreach($info as $key=>$value){
						$return[$key] = trim($value);
					}
			}
		}
		
		return $return;
	}//END showGetway
	
}//EOC NetGetWay

?>