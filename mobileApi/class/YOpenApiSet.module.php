<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//易支付开户数据处理

class YOpenApiSet extends Common{
	
	
	public function UserRegister($info = array(),$Isact = 0){
		
		//获取该经销商对应的companyid
		$companyID	= $this->_getCompanyID($info['dhbUserid']);
		
		//检查是否已存在[可通过数据库处理]
		$csql = "select count(*) as cnt from ".DB_DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=" . $companyID. " and ClientID=" . $info['dhbUserid'];
		$cinfo = $this->db->get_row($csql);

		if($cinfo['cnt']) return true;
		
		//记录

		$isql		= "insert ".DB_DATABASEU.DATATABLE."_yjf_openapi set 
							ClientCompany='".$companyID."',
							ClientID='".$info['dhbUserid']."',
							YapiUserId='".$info['YapiUserId']."',
							YapiuserName='".$info['YapiuserName']."',
							YapiUserType='".$info['YapiUserType']."',
							YapiIsmobile='1',
							YapiIsact='".$Isact."',
							CreateTime='".date('Y-m-d H:i:s')."'
						";

		$this->db->query($isql);
		return true;		
	} 
	
		public function UserUpdate($info = array()){
		
		//获取该经销商对应的companyid
		//$companyID	= $this->_getCompanyID($info['dhbUserid']);
		
		//检查是否已存在[可通过数据库处理]
		$csql = "select count(*) as cnt from ".DB_DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=" . $info['ClientCompany']. " and ClientID=" . $info['dhbUserid'];
		
		$cinfo = $this->db->get_row($csql);

		if(empty($cinfo['cnt'])) return false;
		
		$isql		= "update ".DB_DATABASEU.DATATABLE."_yjf_openapi set 
							YapiIsact='1' where  						
							ClientCompany=".$info['ClientCompany']." and 
							ClientID=".$info['dhbUserid']." and 
							YapiUserId='".$info['YapiUserId']."' 
						";

		$this->db->query($isql);
		return true;		
	} 
	
	/**
	 * @name 获取用户在易极付的签约ID
	 * @author wanjun
	 * @param
	 * 	$cid int 用户ID
	 * @return array 签约信息
	 */
	public function getSignInfo($cid){
		
		//获取companyid
		$companyID	= $this->_getCompanyID($cid);
		
		$sql = "select 
					YapiUserId,YapiuserName 
				from 
					".DB_DATABASEU.DATATABLE."_yjf_openapi 
				where 
					ClientCompany=".$companyID." and
					ClientID=".$cid;

		return $this->db->get_row($sql);
	}
	
	//映射关系
	public function SetMap($payno = '', $dhbno = '', $other = '', $sign = '', $total = 0, $service = '', $companyID = 0, $clientID = 0, $VisitType = 'WEB'){
		
		$sql = "insert into ".DB_DATABASEU.DATATABLE."_neypay_map set
					CompanyID	= '".$companyID."',
					ClientID	= '".$clientID."',
					PayNO		= '".$payno."',
					DHBOrderNO	= '".$dhbno."',
					Other		= '".$other."',
					Total		= '".$total."',
					Service		= '".$service."',
					Type		= 'YJF',
					Sign		= '".$sign."',
					VisitType	= '".$VisitType."' 
				";
		$this->db->query($sql);
	}
	
	//查询出映射关系
	public function getMap($payno = ''){
		
		$sql = "select CompanyID,ClientID,DHBOrderNO,Sign,Total,VisitType from ".DB_DATABASEU.DATATABLE."_neypay_map where Type='YJF' and PayNO = '".$payno."'";
		return $this->db->get_row($sql);
	}
	
	//提现日志记录
	public function logTx($logInfo = array()){
		
		$map = $this->getMap($logInfo['orderNo']);
		
		$sql = "insert into ".DB_DATABASEU.DATATABLE."_yjf_draw_log set
					CompanyID	= ".$map['CompanyID'].",
					amountIn	= ".$logInfo['amountIn'].",
					orderNo		= '".$logInfo['orderNo']."',
					amount		= ".$logInfo['amount'].",
					bankName	= '".$logInfo['bankName']."',
					bankCardNo	= '".$logInfo['bankCardNo']."',
					CreateTime	= ".time().",
					AllInfo 	= '".serialize($logInfo)."'
				";
		
		$this->db->query($sql);
	}
	
	//用户所属供应商ID
	private function _getCompanyID($userID = 0){
		
		if(!$userID) return 0;
		
		$sql	= "select ClientCompany from ".DB_DATABASEU.DATATABLE."_order_dealers where ClientID=".$userID." limit 1";
		$cinfo	= $this->db->get_row($sql);
		return intval($cinfo['ClientCompany']);
	}
}


?>