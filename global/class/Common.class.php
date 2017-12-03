<?php

class Common{
	protected $db;		//数据库
	
	/**
	 * @author WanJun
	 * */
	public function __construct(){
		$this->db = dbconnect::dataconnect()->getdb();
	}
	
	/**
	 * 获取供应商对应的数据库
	 * @author wanjun
	 * @param 
	 * 	$companyID int 供应商ID
	 * @return array 供应商信息
	 */
	protected function getCompanyInfo($companyID = 0){
		
		$sql = "select * from ".DATABASEU.DATATABLE."_order_company where CompanyID=".$companyID;
		return $this->db->get_row($sql);
	}
	
}

?>