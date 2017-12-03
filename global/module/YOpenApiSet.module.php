<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//易支付开户数据处理

class YOpenApiSet extends Common{
	
	
	public function UserRegister($info = array()){
		
		//获取该经销商对应的companyid
		$companyID	= $this->_getCompanyID($info['dhbUserid']);
		
		//检查是否已存在[可通过数据库处理]
		$csql = "select count(*) as cnt from ".DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=" . $companyID. " and ClientID=" . $info['dhbUserid'];
		$cinfo = $this->db->get_row($csql);
		if($cinfo['cnt']) return true;
		
		//记录
		$isql		= "insert ".DATABASEU.DATATABLE."_yjf_openapi set 
							ClientCompany='".$companyID."',
							ClientID='".$info['dhbUserid']."',
							YapiUserId='".$info['YapiUserId']."',
							YapiuserName='".$info['YapiuserName']."',
							YapiUserType='".$info['YapiUserType']."',
							CreateTime='".date('Y-m-d H:i:s')."'
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
					".DATABASEU.DATATABLE."_yjf_openapi 
				where 
					ClientCompany=".$companyID." and
					ClientID=".$cid;

		return $this->db->get_row($sql);
	}
	
		/**
	 * @name 获取用户在易极付的最近30天支付总额
	 * @author tubo
	 * @param
	 * 	$cid int 用户ID
	 * @return 金额
	 */
	/*public function getFinanceInfo($cid){
		
		//获取companyid
		$companyID	= $this->_getCompanyID($cid);
		
		$ucinfo = $this->db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$companyID." limit 0,1");
			
		if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
		
		$sql = "select 
					sum(FinanceTotal) as total 
				from 
					".$datacbase.".".DATATABLE."_order_finance 
				where  
					FinanceCompany=".$companyID." and FinanceFrom='yijifu' and FinanceToDate>='".date('Y-m-d',strtotime('-30 day'))."
					FinanceClient=".$cid;

		return $this->db->get_row($sql);
	}*/
	
	
	//映射关系
	public function SetMap($payno = '', $dhbno = '', $other = '', $sign = '', $total = 0, $service = '', $companyID = 0, $clientID = 0){
		
		$sql = "insert into ".DATABASEU.DATATABLE."_neypay_map set
					CompanyID	= '".$companyID."',
					ClientID	= '".$clientID."',
					PayNO		= '".$payno."',
					DHBOrderNO	= '".$dhbno."',
					Other		= '".$other."',
					Total		= '".$total."',
					Service		= '".$service."',
					Type		= 'YJF',
					Sign		= '".$sign."'
				";
		$this->db->query($sql);
	}
	
	//查询出映射关系
	public function getMap($payno = ''){
		
		$sql = "select CompanyID,ClientID,DHBOrderNO,Sign,Total from ".DATABASEU.DATATABLE."_neypay_map where Type='YJF' and PayNO = '".$payno."'";
		return $this->db->get_row($sql);
	}
	
	/**
	 * 医统账期还款流水专用
	 * @param array $params
	 * @return boolean
	 */
	public function saveCreditByYJF($params = array()){
	
		if(empty($params)) return false;
		$insertSql = "insert
							into " . DATABASEU . DATATABLE . "_credit_serialnumber(CompanyID,ClientID,SerialNumber,Money,Service,repaymentDate)
					values(" . $params ['CompanyID'] . ", " . $params ['ClientID'] . ",'" . $params ['SerialNumber'] . "'," . $params ['Money'] . ",'".$params['Service']."', now())";
	
		return  $this->db->query($insertSql);
	}
	
	//提现日志记录
	public function logTx($logInfo = array()){
		
		$map = $this->getMap($logInfo['orderNo']);
		
		$sql = "insert into ".DATABASEU.DATATABLE."_yjf_draw_log set
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
		
		$sql	= "select ClientCompany from ".DATABASEU.DATATABLE."_order_dealers where ClientID=".$userID." limit 1";
		$cinfo	= $this->db->get_row($sql);
		return intval($cinfo['ClientCompany']);
	}
}


?>