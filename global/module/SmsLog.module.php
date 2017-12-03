<?php

!defined('SYSTEM_ACCESS') && exit('Access deny!');

class SmsLog extends Common{
	
	private $companyid  = 0,	//卖家ID
			$clientid 	= 0;	//买家ID
	
	public function __construct($companyid = 0, $clientid = 0){
		
		parent::__construct();
		$this->companyid = $companyid;
		$this->clientid  = $clientid;
		
	}
	
	/**
	 * 记录短信发送记录
	 * @param string $mobile
	 * @param string $msg
	 */
	public function logSmsMsg($mobile = '', $msg = '', $flag = ''){
		
		if(empty($mobile) || empty($msg)) return false;

		$sql = "insert into ".DATATABLE."_order_sms_post(PostCompany,PostClient,PostDate,PostPhone,PostContent,PostNumber) values(".$this->companyid.",".$this->clientid.",".time().",'".$mobile."', '".$msg."', '".$flag."')";
		$this->db->query($sql);
	}
}