<?php 

/**
 * 全局经销商资料读取，可替代client目录下面的module
 * 
 * @author wangd 
 * @version 1.0 2017-12-09，copy from pc version
 */

!defined('SYSTEM_ACCESS') && exit('Access deny!');

class ClientInfo extends Common{

	public function getClientInfo($companyID = 0, $clientID = 0){
		
		$companyID	= intval($companyID);
		$clientID	= intval($clientID);
		
		$sql_l   = "select ClientCompany,ClientArea,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber from ".DATATABLE."_order_client where ClientCompany=".$companyID." and ClientID=".$clientID." limit 0,1";
		$result1 = $this->db->get_row($sql_l);

		//wangd 2017-12-09 从PC端复制ClientInfo到Mobile端
		$sql_2   = "select ClientID,ClientName,ClientMobile as ClientLoginMobile,LoginIP,LoginDate,LoginCount from ".DB_DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$companyID." and ClientID=".$clientID." limit 0,1";
		$result2 = $this->db->get_row($sql_2);
		$result	 = array_merge($result1, $result2);
		
		return $result;
	}//END getClientInfo
	
}//EOC ClientInfo

?>