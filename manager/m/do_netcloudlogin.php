<?php
include_once ("../common.php");

$inv = new Input();
$in  = $inv->parse_incoming();
$in  =  $inv->_htmlentities($in);
$db = dbconnect::dataconnect()->getdb();

$fromurl = strtolower($_SERVER["HTTP_REFERER"]);

if(empty($in['m']))
{
 	echo "error!";
 	exit();
}
$in = $inv->_htmlentities($in);

//$fromurl = 'dhb.rs.com';

function ilog($title,$data) {
    $file = 'log_' . date('Y-m-d') . '.txt';
    error_log(date('Y-m-d H:i:s') . ' - ' . $title . ' - ilog ;' . print_r($data,true) . "\n",3,'../data/log/' . $file);
}
if($in['m']=="login" && !empty($in['us'])){
        $us = strtolower(trim($in['us']));
		$ip = RealIp();

		$upsql =  "select UserID,UserName,UserPass,UserCompany,UserTrueName,UserFlag,UserSessionID,UserType from ".DATABASEU.DATATABLE."_order_user where UserName = '".$us."' and UserFlag!='1' limit 0,1";		
		$uinfo = $db->get_row($upsql);
		
		$ucinfo = $db->get_row("select CompanyIndustry,CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyEmail,CompanyUrl,CompanyFlag,CompanyDatabase,CompanyType from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$uinfo['UserCompany']." limit 0,1");

		$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$uinfo['UserCompany']." limit 0,1");
	
		$_SESSION['uinfo']['userid']        = $uinfo['UserID'];
		$_SESSION['uinfo']['ucompany'] 		= $uinfo['UserCompany'];
		$_SESSION['uinfo']['username']   	= $uinfo['UserName'];
		$_SESSION['uinfo']['usertruename'] 	= $uinfo['UserTrueName'];
		$_SESSION['uinfo']['userflag']      = $uinfo['UserFlag'];
		$_SESSION['uinfo']['usertype']      = $uinfo['UserType'];

		$_SESSION['uc'] = $ucinfo;
		$_SESSION['uc']['Number']       	=  $csinfo['CS_Number'];
		$_SESSION['uc']['BeginDate']    	=  $csinfo['CS_BeginDate'];
		$_SESSION['uc']['EndDate']       	=  $csinfo['CS_EndDate'];
		$_SESSION['uc']['SmsNumber'] 		=  $csinfo['CS_SmsNumber'];
        $_SESSION['uc']['ERP']              = 'OFF';
    
		$db->query("update ".DATABASEU.DATATABLE."_order_user set UserLoginDate=".time().",UserLoginIP='". $ip."',UserLogin=UserLogin+1,UserSessionID='".session_id()."' where UserID=".$uinfo['UserID']);

		//业务
		if($uinfo['UserType']=="S")
		{
			$_SESSION['uinfo']['clientidmsg'] = '';
			if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;

			$c_info = $db->get_col("SELECT ClientID FROM ".$datacbase.".".DATATABLE."_order_salerclient where CompanyID=".$_SESSION['uinfo']['ucompany']." and SalerID=".$uinfo['UserID']." ");
			if(!empty($c_info))
			{
				$comma_separated = implode(",", $c_info);
				$_SESSION['uinfo']['clientidmsg'] = $comma_separated;
			}
			$_SESSION['us'] = get_set_arr('product');
		}


		 $db->query("insert into ".DATABASEU.DATATABLE."_order_login_user_log(LoginCompany,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl) values(". $uinfo['UserCompany'].",".$uinfo['UserID'].",'".$uinfo['UserName']."','". $ip."',".time().",'Compute')");

		if($uinfo['UserType']=="S")
		{
			echo '<script language="javascript">window.location.href="/s/order.php";</script>';
		}else{
			echo '<script language="javascript">window.location.href="/m/home.php";</script>';
		}
		exit('登陆成功，正在载入页面...');
}
/********** Function ************/
function ChangeMsg($msgu,$msgp)
{
	if(!empty($msgu) && !empty($msgp))
	{
		$delmsg = md5($msgu);
		$rname  = substr($delmsg,5,1).",".substr($delmsg,7,1).",".substr($delmsg,15,1).",".substr($delmsg,17,1);
		$rnamearray = explode(',',$rname);
		$rpass = md5($msgp);
		$r_msg = str_replace($rnamearray, "", $rpass);
	}else{
		$r_msg = $msgp;
	}
	return $r_msg;
} 
exit('非法操作!');
?>