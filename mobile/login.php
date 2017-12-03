<?php
/**
 * Login
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */

exit('路径错误: 请访问 <a href="http://bmb.yitong111.com/">医统天下订货平台登录！</a> ');

include_once ('common.php');

$input		= new Input;
$in			= $input->parse_incoming();
$in			= $input->_htmlentities($in);
$encode		= md5(ENCODE_KEY.'_'.date('Y-m-d H'));
if(!empty($in['ecode']) && $in['ecode'] == $encode){
	$fromurl	= 'sj.dhb.hk';
}else{
	$fromurl	= strtolower($_SERVER["HTTP_REFERER"]);
}
if(empty($in['m']))
{
	header("location: /index.html");
}
elseif($in['m']=="login" && (strpos($fromurl, "dhb.hk") || strpos($fromurl, "marketcoms.com") || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "rs.com")))
{
	$us = strtolower(trim($in['UserName']));
	$ps = strtolower(trim($in['UserPass']));
	$ip = trim($in['LoginIP']);
	if(empty($ip)) $ip = RealIp();

	if(!is_filename($us)) exit('请输入合法的帐号！(3-18位数字、字母和下划线)');
	if(strlen($us) < 3 || strlen($us) > 18 ) exit('请输入合法的帐号！(3-18位数字、字母和下划线)');

	if(!is_filename($ps)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($ps) < 3 || strlen($ps) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');

	$db	= dbconnect::dataconnect()->getdb();

	if(is_phone($us))
	{
		$loginsql = "select ClientID,ClientCompany,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount from ".DATABASEU.DATATABLE."_order_dealers where ClientMobile = '".$us."' and ClientFlag=0 limit 0,1";
	}else{
		$loginsql = "select ClientID,ClientCompany,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount from ".DATABASEU.DATATABLE."_order_dealers where ClientName = '".$us."' and ClientFlag=0 limit 0,1";
	}
	$logininfo = $db->get_row($loginsql);
	
	if(empty($logininfo['ClientID']))
	{
		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginPass,LoginIP,LoginDate,LoginUrl,LoginContent) values('c','".$us."','".$ps."','".$ip."',".time().",'".$fromurl."','帐号不存在')");
		exit('notin');
	}
	else if($logininfo['ClientPassword'] == $ps)
	{	
		$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyLogo,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$logininfo['ClientCompany']." limit 0,1");
		if($ucinfo['CompanyFlag'] == "1") exit('companylock');
		
		$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$logininfo['ClientCompany']." limit 0,1");
		if(time() > (strtotime($csinfo['CS_EndDate'])+60*60*24)) exit('companyexpired');

		if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;

		$upsql =  "select ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientMobile,ClientAdd,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientPay,ClientFlag from ".$datacbase.".".DATATABLE."_order_client where ClientID=".$logininfo['ClientID']." and ClientCompany = ".$logininfo['ClientCompany']." and ClientFlag=0 limit 0,1";	
		$cinfo = $db->get_row($upsql);

		$_SESSION['cc'] = null;
		$_SESSION['cc']['cid']			= $logininfo['ClientID'];
		$_SESSION['cc']['ccompany']		= $logininfo['ClientCompany'];
		$_SESSION['cc']['cusername']	= $logininfo['ClientName'];
		$_SESSION['cc']['clevel']		= $cinfo['ClientLevel'];
		$_SESSION['cc']['ctruename']	= $cinfo['ClientTrueName'];
		$_SESSION['cc']['cemail']		= $cinfo['ClientEmail'];
		$_SESSION['cc']['cphone']		= $cinfo['ClientPhone'];
		$_SESSION['cc']['ccompanyname']	= $cinfo['ClientCompanyName'];
		$_SESSION['cc']['cmobile']		= $cinfo['ClientMobile'];
		$_SESSION['cc']['cadd']		    = $cinfo['ClientAdd'];
		$_SESSION['cc']['clogindate']	= $logininfo['LoginDate'];

		$_SESSION['cc']['csetshield']	= $cinfo['ClientShield'];
		$_SESSION['cc']['csetprice']	= $cinfo['ClientSetPrice'];
		if(empty($_SESSION['cc']['csetprice'])) $_SESSION['cc']['csetprice'] = "Price2";
		$_SESSION['cc']['csetpercent']	= $cinfo['ClientPercent'];
		if(empty($_SESSION['cc']['csetpercent'])) $_SESSION['cc']['csetpercent'] = '10.0';
		if(!empty($cinfo['ClientBrandPercent'])) $_SESSION['cc']['cbrandpercent'] = unserialize($cinfo['ClientBrandPercent']);
		$_SESSION['cc']['cclientpay']	= $cinfo['ClientPay'];

		$_SESSION['cc']['cflag']		= $cinfo['ClientFlag'];
		$_SESSION['ucc']				= $ucinfo;
		$_SESSION['ucc']['SmsNumber']   = $csinfo['CS_SmsNumber'];

		if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
		{
			$_SESSION['cc']['clevel'] = "A_".$cinfo['ClientLevel'];
		}
		//微信帐号
		if(!empty($in['openid'])){
			$db->query("insert into ".DATABASEU.DATATABLE."_order_weixin(WeiXinID,UserID,UserType) values('".$in['openid']."',".$logininfo['ClientID'].",'C')");
			$_SESSION['cc']['cwxopenid'] = $in['openid'];
		}

		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_client_log(LoginCompany,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl) values(". $logininfo['ClientCompany'].",".$logininfo['ClientID'].",'".$us."','".$ip."',".time().",'".$fromurl."&wid=".$in['openid']."')");
		if($in['RemberMe']=="on")
		{
			setcookie("CompanyPrefix", $us, time()+60*60*24*365);
		}else{
			setcookie("CompanyPrefix", $us, time()-60*60*24*365);
		}
		echo '<script language="javascript">window.location.href="/home.php";</script>';
		exit('登陆成功，正在载入页面...');
	}else{
		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginPass,LoginIP,LoginDate,LoginUrl,LoginContent) values('c','".$us."','".$ps."','".$ip."',".time().",'".$fromurl."','帐号与密码不匹配')");
		exit("isnot");
	}
}
elseif($in['m']=="logout")
{
		//$burl =  "./".$_SESSION['ucc']['CompanyPrefix']."/";
		$burl = "./";
		unset($_SESSION['cc']);
		unset($_SESSION['ucc']);
		unset($_SESSION['cartitems']);
		setcookie("cartlistitems",  "", time()-60*60*24);
		session_unset(); 
		session_destroy();
		header("Location: ".$burl."");
}
?>