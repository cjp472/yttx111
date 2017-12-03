<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//订单数据处理

class NetPaySeria extends Common{
	
	
	
	//获取流水支付号
	public function getSeria($merchantId = 0, $orderNo = ''){
		$sql = "select * from ".DATABASEU.DATATABLE."_order_netpay where MerchantNO='".$merchantId."' and OrderNO='".$orderNo."' order by PayID desc limit 1";
		return  $this->db->get_row($sql);
	}
	
}