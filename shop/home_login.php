<?
/**
 * Login
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');

$input		= new Input;
$in			= $input->parse_incoming();
$db			= dbconnect::dataconnect()->getdb();
$fromurl = strtolower($_SERVER["HTTP_REFERER"]);

if(!empty($fromurl) && (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dinghuobao.cn") || strpos($fromurl, "rs.com") || strpos($fromurl, "ywdjkx.com")))
{
	$us = strtolower(trim($in['cus']));
	$ps = strtolower(trim($in['cps']));
	$ip = trim($in['LoginIP']);
	if(empty($ip)) $ip = RealIp();

	if(!is_filename($us)) Error::Jump('请输入合法的帐号！(3-18位数字、字母和下划线)','./index.html');
	if(strlen($us) < 3 || strlen($us) > 18 ) Error::Jump('请输入合法的帐号！(3-18位数字、字母和下划线)','./index.html');

	if(!is_filename($ps))  Error::Jump('请输入合法的密码！(3-18位数字、字母和下划线)','./index.html');
	if(strlen($ps) < 3 || strlen($ps) > 18 ) Error::Jump('请输入合法的密码！(3-18位数字、字母和下划线)','./index.html');

	if(empty($in['vc']) || $in['vc'] != $_SESSION['securimage_code_value']) Error::Jump('验证码错误!','./index.html');
	
	if(is_phone($us))
	{
		$loginwh = " ClientMobile = '".$us."' ";
	}else{
		$loginwh = " ClientName = '".$us."' ";
	}
	$loginsql = "select ClientID,ClientCompany,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount,ClientFlag from ".DATABASEU.DATATABLE."_order_dealers where ".$loginwh."  limit 0,1";
	$logininfo = $db->get_row($loginsql);

	if(empty($logininfo['ClientID']))
	{
		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginPass,LoginIP,LoginDate,LoginUrl,LoginContent) values('c','".$us."','".$ps."','".RealIp()."',".time().",'".$fromurl."','帐号不存在')");
		 Error::Jump('帐号不存在!','./index.html');
	}
	else if($logininfo['ClientFlag'] == "1")
	{
		 Error::Jump('您的帐号已禁用，请与供货商联系!','./index.html');
	}
	else if($logininfo['ClientFlag'] == "9")
	{
		 Error::Jump('您的帐号正处于待审核状态，请耐心等待，我们会尽快与您联系!','./index.html');
	}
	else if($logininfo['ClientPassword'] == $ps)
	{
		$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyContact,CompanyMobile,CompanyLogo,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$logininfo['ClientCompany']." limit 0,1");
		if($ucinfo['CompanyFlag'] == "1") Error::Jump('此供货商已锁定，暂停使用，请与供货商联系!','./index.html');
		
		$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$logininfo['ClientCompany']." limit 0,1");
		if(time() > (strtotime($csinfo['CS_EndDate'])+60*60*24)) Error::Jump('此供货商帐号已到期，暂停使用，请与供货商联系!','./index.html');		
		
		if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
		$upsql =  "select ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientMobile,ClientShield,ClientSetPrice,ClientPercent,ClientPay,ClientFlag from ".DATATABLE."_order_client where ClientID=".$logininfo['ClientID']." and ClientName = '".$us."' and ClientFlag=0 limit 0,1";	
		$dbc->query("set names 'utf8'");
		$cinfo = $dbc->get_row($upsql);

		if(!empty($_SESSION['cc']['cid']) && $_SESSION['cc']['cid'] != $logininfo['ClientID'])
		{
			unset($_SESSION['cc']);
			unset($_SESSION['ucc']);
			$_SESSION['cartitems'] = null;
			unset($_SESSION['cartitems']);
			setcookie("cartlistitems",  "", time()-60*60*24);
		}

		$_SESSION['cc'] = null;
		$_SESSION['cc']['cid']			    = $logininfo['ClientID'];
		$_SESSION['cc']['ccompany']	= $logininfo['ClientCompany'];
		$_SESSION['cc']['cusername']	= $logininfo['ClientName'];
		$_SESSION['cc']['clevel']		    = $cinfo['ClientLevel'];
		$_SESSION['cc']['ctruename']	= $cinfo['ClientTrueName'];
		$_SESSION['cc']['cemail']		    = $cinfo['ClientEmail'];
		$_SESSION['cc']['cphone']		    = $cinfo['ClientPhone'];
		$_SESSION['cc']['ccompanyname']	= $cinfo['ClientCompanyName'];
		$_SESSION['cc']['cmobile']		= $cinfo['ClientMobile'];
		$_SESSION['cc']['cadd']		        = $cinfo['ClientAdd'];
		$_SESSION['cc']['clogindate']	= $logininfo['LoginDate'];

		$_SESSION['cc']['csetshield']	   = $cinfo['ClientShield'];
		$_SESSION['cc']['csetprice']	   = $cinfo['ClientSetPrice'];
		if(empty($_SESSION['cc']['csetprice'])) $_SESSION['cc']['csetprice'] = "Price2";
		$_SESSION['cc']['csetpercent']	= $cinfo['ClientPercent'];
		if(empty($_SESSION['cc']['csetpercent'])) $_SESSION['cc']['csetpercent'] = '10.0';
		$_SESSION['cc']['cclientpay']	    = $cinfo['ClientPay'];

		$_SESSION['cc']['cflag']					= $cinfo['ClientFlag'];
		$_SESSION['ucc']							= $ucinfo;
		$_SESSION['ucc']['SmsNumber']   = $csinfo['CS_SmsNumber'];

		if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
		{
			$_SESSION['cc']['clevel'] = "A_".$cinfo['ClientLevel'];
		}
		$db->query("update ".DATABASEU.DATATABLE."_order_dealers set LoginCount=LoginCount+1,LoginDate=".time().",LoginIP='".$ip."' where ClientID=".$logininfo['ClientID']);

		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_client_log(LoginCompany,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl) values(". $logininfo['ClientCompany'].",".$logininfo['ClientID'].",'".$us."','".$ip."',".time().",'".$fromurl."')");
		
		setcookie("CompanyPrefix", $us, time()+60*60*24*365);
		echo '
		<script language="javascript">
		window.location.href="./home.php";
		</script>
		';
	}else{
		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginPass,LoginIP,LoginDate,LoginUrl,LoginContent) values('c','".$us."','".$ps."','".RealIp()."',".time().",'".$fromurl."','帐号与密码不匹配')");
		Error::Jump('帐号和密码不匹配，请输入正确帐号和密码!','./index.html');
	}
}else{
	Error::Jump('此网站未认证，请从订货宝官网登录!','./index.html');
}
?>