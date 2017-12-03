<?php
    isLogin();
    function isLogin($tf='')
	{
		if(empty($_SESSION['cc']['cid']) || empty($_SESSION['ucc']['CompanyID']))
		{
      	 	session_unset();
			session_destroy();
			$_SESSION['eMsg'] = "请先登陆！";
		 	Error::outAdmin('登陆超时，请重新登陆！','./index.php');
        } else {
            return true;
            //不实时查询
            $db	= dbconnect::dataconnect()->getdb();
            $client_id = $_SESSION['cc']['cid'];
            $company_id = $_SESSION['ucc']['CompanyID'];
            $clientFlag = $db->get_var("SELECT ClientFlag FROM ".DATATABLE."_order_client WHERE ClientCompany={$company_id} AND ClientID={$client_id} LIMIT 1");
            if($clientFlag == '1') {
                $_SESSION['eMsg'] = '登陆超时，请重新登陆!';
                unset($_SESSION['cc'],$_SESSION['ucc']);
                Error::outAdmin('登陆超时，请重新登陆！','./index.php');
            }
        }
	}
?>