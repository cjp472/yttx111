<?php
/**
 * Login
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/sms.class.php");
$input		= new Input;
$in			= $input->parse_incoming();
$in			= $input->_htmlentities($in);
$db			= dbconnect::dataconnect()->getdb();
$fromurl	= strtolower($_SERVER["HTTP_REFERER"]);

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

if(empty($in['m']))
{
	header("location: /");
}
if($in['m']=="login" && (!empty($in['openid']) || (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dinghuobao.cn")  || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "dhb.cn.com") || strpos($fromurl, "rs.com") || strpos($fromurl, "online.com"))))
{
	$sOldIndustry_experience = trim($in['industry']);
	$industry_experience = getSafeIndustry($sOldIndustry_experience);
	
	if(empty($industry_experience)) exit('行业数据不能为空！');
	// 判断行业是否存在
	$industryData = $db->get_row("SELECT IndustryID FROM ".DATABASEU.DATATABLE."_order_industry where IndustryID = {$industry_experience} limit 0,1 ");
	if(empty($industryData['IndustryID'])){
		exit('行业数据不存在！');
	}
	
	if(!empty($in['openid'])){
		$openid = strtolower(trim($in['openid']));
		$datasql   = "SELECT UserID FROM ".DATABASEU.DATATABLE."_order_qq where OpenID='".$openid."' and UserType='C' limit 0,1";
		$uoinfo = $db->get_row($datasql);
		if(!empty($uoinfo['UserID'])){
			$loginsql = "select ClientID,ClientCompany,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount,ClientFlag from ".DATABASEU.DATATABLE."_order_dealers where ClientID = ".$uoinfo['UserID']." limit 0,1";
			$logininfo = $db->get_row($loginsql);
			$ps = $logininfo['ClientPassword'];
		}else{
			exit('参数错误！');
		}
	}else{
		$ip = RealIp();	
		
		// 分析公司
		$sOldCp = trim($in['cp']);
		$cp = getSafeIndustry($sOldCp);
		if($cp){
			$arrCompanyData = $db->get_row("select CompanyID from ".DATABASEU.DATATABLE."_order_company where CompanyFlag = '0' and CompanyIndustry = {$industry_experience} and CompanyID = {$cp} order by CompanyID asc limit 0,1 ");
		}

		if(empty($arrCompanyData['CompanyID'])){
			// 尝试通过IP定位一个公司
			$arrCompanyData = $db->get_row("select CompanyID from ".DATABASEU.DATATABLE."_order_company where CompanyFlag = '0' and CompanyIndustry = {$industry_experience} and IsUse = 1 and LoginIP = '{$ip}' and IsSystem=0 order by CompanyID asc limit 0,1 ");
			if(empty($arrCompanyData['CompanyID'])){
				// 从行业中读取未使用的一个公司
				$arrCompanyData = $db->get_row("select CompanyID from ".DATABASEU.DATATABLE."_order_company where CompanyFlag = '0' and CompanyIndustry = {$industry_experience} and IsUse = 0 and IsSystem=0 order by CompanyID asc limit 0,1 ");
				if(empty($arrCompanyData['CompanyID'])){
					exit('行业帐号已使用完毕！请联系客服！');
				}
			}
		}
		
		$loginsql = "select ClientID,ClientCompany,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount,ClientFlag from ".DATABASEU.DATATABLE."_order_dealers where ClientCompany = {$arrCompanyData['CompanyID']} and ClientFlag='0' order by ClientID asc limit 0,1";
		$logininfo = $db->get_row($loginsql);
	}
	if(empty($logininfo['ClientID']))
	{
		exit('无可用经销商，请切换到后台随意添加一个！');
		
		//$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginPass,LoginIP,LoginDate,LoginUrl,LoginContent) values('c','".$us."','".$ps."','".$ip."',".time().",'Compute','帐号不存在')");
		//$_SESSION['count_login'] = intval($_SESSION['count_login']) + 1;
		//exit('notin');
	}
	else{
		$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyLogo,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$logininfo['ClientCompany']." limit 0,1");
		if($ucinfo['CompanyFlag'] == "1") exit('companylock');
		
		$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$logininfo['ClientCompany']." limit 0,1");
		if(time() > (strtotime($csinfo['CS_EndDate'])+60*60*24)) exit('companyexpired');

		if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;
		$upsql =  "select ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientMobile,ClientAdd,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientPay,ClientFlag,YapiUserId from ".$datacbase.".".DATATABLE."_order_client where ClientID=".$logininfo['ClientID']." and ClientCompany = ".$logininfo['ClientCompany']." and ClientFlag=0 limit 0,1";	
		$cinfo = $db->get_row($upsql);
		
		//对应供应商的网银开户情况
		$netSql = "select GetWay from ".DATABASEU.DATATABLE."_order_getway where CompanyID=".$logininfo['ClientCompany'];
		$netInfo = $db->get_results($netSql);

		if(!empty($_SESSION['cc']['cid']) && $_SESSION['cc']['cid'] != $logininfo['ClientID'])
		{
			unset($_SESSION['cc']);
			unset($_SESSION['ucc']);
			$_SESSION['cartitems'] = null;
			unset($_SESSION['cartitems']);
		}

		$_SESSION['cc'] = null;
		$_SESSION['cc']['cid']			= $logininfo['ClientID'];
		$_SESSION['cc']['yopenapi']		= $cinfo['YapiUserId'] ? true : false;
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
		
		//设置开户情况
		foreach($netInfo as $nv){
			$_SESSION['cc'][$nv['GetWay']] = true;
		}

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
		$db->query("update ".DATABASEU.DATATABLE."_order_dealers set LoginCount=LoginCount+1,LoginDate=".time().",LoginIP='".$ip."' where ClientID=".$logininfo['ClientID']);

		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_client_log(LoginCompany,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl) values(". $logininfo['ClientCompany'].",".$logininfo['ClientID'].",'".$us."','".$ip."',".time().",'Compute')");
		$_SESSION['count_login'] = 0;
		
		$_SESSION['industry'] = $sOldIndustry_experience;
		
		// 标识这个公司已经使用
		$db->query("update ".DATABASEU.DATATABLE."_order_company set IsUse = 1,LoginIP = '{$ip}' where CompanyID = {$arrCompanyData['CompanyID']} ");
		
		setcookie("CompanyPrefix", $us, time()+60*60*24*365);
		echo '<script language="javascript">window.location.href="/home.php";</script>';
		exit('登陆成功，正在载入页面...');
	}
}
elseif($in['m']=="regiester" && (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dinghuobao.cn") || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "rs.com")))
{
	$us = strtolower(trim($in['RegUserName']));
	$ps = strtolower(trim($in['RegPassword']));
	$ip = RealIp();
	$betweentime = time() - 60*60*24;
	$countinfo = $db->get_row("select count(*) as allrow from ".DATABASEU.DATATABLE."_order_dealers where LoginIP = '".$ip."' and LoginDate > ".$betweentime." limit 0,1");
	if(!empty($countinfo['allrow']) && $countinfo['allrow'] > 30) exit('请不要重复注册<br />同一个地址一天最多只能注册三个帐号！');

	if(empty($in['RegCompanyFlag'])) exit('路径错误!');
	$companyflag = passport_decrypt($in['RegCompanyFlag'],ENCODE_KEY);
	$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanyPrefix,CompanySigned,CompanyLogo,CompanyLogin,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyPrefix = '".$companyflag."' limit 0,1");
	if(empty($ucinfo)) exit('路径错误');
	if(!empty($ucinfo['CompanyDatabase'])) $setdbname = DB_DATABASE."_".$ucinfo['CompanyDatabase']."."; else $setdbname = '';

	$username = $ucinfo['CompanyPrefix']."-".$us;
	if(!is_filename($us)) exit('请输入合法的帐号！(3-18位数字、字母和下划线)');
	if(strlen($username) < 3 || strlen($username) > 18 ) exit('请输入合法的帐号！(3-18位数字、字母和下划线)');
	if(!is_filename($ps)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($ps) < 3 || strlen($ps) > 30 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(empty($in['UserVC']) || $in['UserVC'] != $_SESSION['securimage_code_value']) exit('请输入正确的验证码!');
	if(!empty($in['RegMobile']) && !is_phone($in['RegMobile']))	exit('请输入正确的手机号码!');
	if(empty($in['RegMobile']) && empty($in['RegPhone'])) exit('手机号和联系电话必需填一个项！');

	$uninfo = $db->get_row("select count(*) as allrow from ".DATABASEU.DATATABLE."_order_dealers where ClientName = '".$username."' limit 0,1");
	if(!empty($uninfo['allrow'])) exit('此帐号已存在，请换名再试!');

	$upsql = "insert into ".DATABASEU.DATATABLE."_order_dealers(ClientCompany,ClientName,ClientPassword,LoginIP,LoginDate,ClientFlag) values(".$ucinfo['CompanyID'].", '".$username."', '".$ps."','".$ip."',".time().",9)";
	if($db->query($upsql))
	{
		$inid =  $db->insert_id;
		$insql	 = "insert into ".$setdbname.DATATABLE."_order_client(ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,ClientFlag) values(".$inid.",".$ucinfo['CompanyID'].",'',".intval($in['RegArea']).", '".$username."', '".$in['RegName']."','', '', '".$in['RegContact']."', '".$in['RegEmail']."', '".$in['RegPhone']."', '".$in['RegFax']."', '".$in['RegMobile']."', '".$in['RegAddress']."', '".$in['RegRemark']."',".time().",9)";
		$db->query($insql);

		$message = "【".$ucinfo['CompanySigned']."】有新用户注册系统帐号,帐号:(".$username."),请尽快登录订货宝系统审核。退订回复TD";
		sms::reg_setsms($ucinfo['CompanyID'],"9",$message,$setdbname);
		exit('ok');
	}else{
		exit('注册不成功!');
	}

}
elseif($in['m']=="bangding" && (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dinghuobao.cn")  || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "dhb.cn.com") || strpos($fromurl, "rs.com")))
{
	$us = strtolower(trim($in['UserName']));
	$ps = strtolower(trim($in['UserPass']));
	$vc = strtolower(trim($in['UserVC']));
	if(empty($in['UserVC']) || $in['UserVC'] != $_SESSION['securimage_code_value']) exit('请输入正确的验证码！');

	$openid = strtolower(trim($in['openid']));
	$accesstoken = strtolower(trim($in['accesstoken']));
	$nickname = strtolower(trim($in['nickname']));
	
	if(!is_filename($us)) exit('请输入合法的用户！(3-18位数字、字母和下划线)');
	if(strlen($us) < 3 || strlen($us) > 18 ) exit('请输入合法的用户！(3-18位数字、字母和下划线)');

	if(!is_filename($ps)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($ps) < 3 || strlen($ps) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(empty($openid)) exit('参数错误！');

	$datasql   = "SELECT count(*) as lrow FROM ".DATABASEU.DATATABLE."_order_qq where OpenID='".$openid."' and UserType='C' limit 0,1";
	$ulinfo = $db->get_row($datasql);
	if(!empty($ulinfo['lrow'])){
		header("location: /login.php?m=login&openid=".$openid);
		exit;
	}else{
		if(is_phone($us)){
			$loginwh = " ClientMobile = '".$us."' ";
		}else{
			$loginwh = " ClientName = '".$us."' ";
		}
		$loginsql = "select ClientID,ClientCompany,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount,ClientFlag from ".DATABASEU.DATATABLE."_order_dealers where ".$loginwh." and ClientPassword='".$ps."' limit 0,1";
		$uinfo = $db->get_row($loginsql);
		if(!empty($uinfo)){
			$isi = $db->query("insert into ".DATABASEU.DATATABLE."_order_qq(UserID,UserType,AccessToken,OpenID,QQ)  values(".$uinfo['ClientID'].",'C','".$accesstoken."','".$openid."','".$nickname."')");
			if($isi){
				header("location: /login.php?m=login&openid=".$openid);
				exit;
			}else{
				exit('此QQ帐号已经绑定过了！');
			}
		}else{
			exit('帐号或密码不正确！');
		}
	}
}
elseif($in['m']=="logout")
{
		if(DHB_RUNTIME_MODE === 'experience'){
			$industry = isset($_SESSION['industry']) ? $_SESSION['industry'] : '';
			$burl = $industry ? M_SITE.'/experience/industry.php?industry='.$industry : './';
		}else{
			//$burl =  "./".$_SESSION['ucc']['CompanyPrefix']."/";
			$burl = "./";
		}
		
		unset($_SESSION['cc']);
		unset($_SESSION['ucc']);
		unset($_SESSION['cartitems']);
		//setcookie("cartlistitems",  "", time()-60*60*24);
		session_unset(); 
		session_destroy();
		header("Location: ".$burl."");
}
?>