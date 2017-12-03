<?php 

/**
 * 易极付绑定银行卡回传数据处理
 * 
 */

!defined('SYSTEM_ACCESS') && exit('Access deny!');

class Signbank extends Common{
	
	/**
	 * 保存签约回传数据
	 *
	 * @param intege $companyID
	 * @param intege $clientID
	 * @param string $msg
	 * @author wanjun
	 * @since 2015/07/23
	 * @return bool
	 */
	public function saveSignMsg($companyID = 0, $clientID = 0, $info = ''){
		
		if(empty($info)) return false;

		$signNum = $this->getSignBankNum($companyID, $clientID, $info['bankCode']);
		
		if($signNum) return true;
		
		$sql = "insert ".DATABASEU.DATATABLE."_yjf_signbank set 
							CompanyID=".intval($companyID).",
							ClientID=".intval($clientID).",
							bankCode='".$info['bankCode']."',
							bankName='".$info['bankName']."',
							cardNo='".$info['cardNo']."',
							cardTypeName='".$info['cardTypeName']."',
							msg='".serialize($info)."',
							signTime='".date('Y-m-d H:i:s')."'
						";
		
		return $this->db->query($sql);
		
	}//END saveSignMsg
	
	/**
	 * 获取指定经销商已绑卡数量
	 *
	 * @param intege $companyID
	 * @param intege $clientID
	 */
	public function getSignBankNum($companyID = 0, $clientID = 0, $bankCode = ''){
		
		$where = empty($bankCode) ? "" : " and bankCode='".$bankCode."'";
		$sql = "select count(*) as total from ".DATABASEU.DATATABLE."_yjf_signbank where CompanyID=".$companyID." and ClientID=".$clientID." ".$where;
		
		$result = $this->db->get_row($sql);
		
		return intval($result['total']);
		
	}// getSignBankNum
        public function  PwdSetSel($cid,$CompanyID){
            $PwdSql = "select CreditStatus from ".DATABASEU.DATATABLE."_credit_main where ClientID=".$cid." and CompanyID=".$CompanyID."";
            $PwdSel = $this->db->get_row($PwdSql);
            return $PwdSel['CreditStatus'];
        }
        
        public function  CompanyCredit($CompanyID){
            $companyCreditSql = "select CompanyCredit from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$CompanyID."";
            $companyCreditSel = $this->db->get_row($companyCreditSql);
            return $companyCreditSel['CompanyCredit'];
        }
	
	
}//EOC 