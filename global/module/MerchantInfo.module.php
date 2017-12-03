<?php 

/**
 * 全局供应商资料读取
 * 
 * @author wnajun
 * @version 1.0 @ 2015/07/3
 */

!defined('SYSTEM_ACCESS') && exit('Access deny!');

class MerchantInfo extends Common{

	/**
	 * Enter description here...
	 *
	 * @param int $companyID 供应商ID
	 * @return array
	 */
	public function getMerchantInfo($companyID = 0){
		
		$companyID	= intval($companyID);
		
		$sql = "select * from ".DATABASEU.DATATABLE."_order_company where CompanyID=".$companyID;
		return $this->db->get_row($sql);

	}//END getMerchantInfo
	
	public function __destruct(){
		
	}
	
}//EOC MerchantInfo

?>