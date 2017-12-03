<?php
class client
{
	//取客户资料
	function clientinfo($in)
	{
		$db	     = dbconnect::dataconnect()->getdb();

		$sql_l   = "select ClientCompany,ClientArea,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber from ".DATATABLE."_order_client where ClientCompany=".$in['ClientCompany']." and ClientID=".$in['ClientID']." limit 0,1";
		$result1 = $db->get_row($sql_l);
		$sql_2   = "select ClientID,ClientName,ClientMobile as ClientLoginMobile,LoginIP,LoginDate,LoginCount from ".DB_DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$in['ClientCompany']." and ClientID=".$in['ClientID']." limit 0,1";
		$result2 = $db->get_row($sql_2);
		//查询是否已开通易极付 by wanjun
		$sql_3   = "select YapiuserName from ".DB_DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=".$in['ClientCompany']." and ClientID=".$in['ClientID']." limit 1";
		$result3 = $db->get_row($sql_3);
		$result	 = count($result3) ? array_merge($result1, $result2, $result3) : array_merge($result1, $result2);
		
		//$db->debug();
		return $result;
	}


    //修改客户手机号码
	function edit_user_phone($in)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$result = null;

		$sql_l = "update ".DATATABLE."_order_client set  ClientMobile='".$in['ClientMobile']."' where ClientID=".$in['ClientID']." and ClientCompany=".$in['ClientCompany']."";
		if($db->query($sql_l))
		{		
			return true;
		}
		//$db->debug();
		return false;
	}


//END
}
?>