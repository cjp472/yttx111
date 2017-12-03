<?php 

/**
 * 支付结果安全校验，接收网关返回的报文，并MD5加密验证，
 * 在每个页面通知或异步通知页面进行验证，避免抓包等行为带来的重复数据写入
 * 
 * @author wnajun
 * @version 1.0 @ 2015/07/16
 */

!defined('SYSTEM_ACCESS') && exit('Access deny!');

class SecurityPay extends Common{

	public function getSecurityMd5($type = 'allinpay', $message = ''){
		
		$sql = "select count(*) as total from ".DATABASEU.DATATABLE."_netpay_security where TypeMd5='".md5($type.$message)."'";
		$result = $this->db->get_row($sql);
		return intval($result['total']);
		
	}//END getSecurityMd5
	
	public function setSecurityMd5($type = 'allinpay', $message = ''){
		$sql = "insert into ".DATABASEU.DATATABLE."_netpay_security set TypeMd5	= '".md5($type.$message)."'";
		
		$result = $this->db->query($sql);
		
	}//END setSecurityMd5
	
}//EOC SecurityPay

?>