<?php
include_once ("../common.php");

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

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

if($in['m']=="login" && (!empty($in['openid']) || (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dinghuobao.cn")  || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "dhb.cn.com") || strpos($fromurl, "rs.com") || strpos($fromurl, "exp.com") || strpos($fromurl, "online.com"))))
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
		$datasql   = "SELECT UserID FROM ".DATABASEU.DATATABLE."_order_qq where OpenID='".$openid."' and UserType='M' limit 0,1";
		$uoinfo = $db->get_row($datasql);
		if(!empty($uoinfo['UserID'])){
			$upsql =  "select UserID,UserName,UserPass,UserCompany,UserTrueName,UserFlag,UserSessionID,UserType from ".DATABASEU.DATATABLE."_order_user where UserID = ".$uoinfo['UserID']." and UserFlag!='1' limit 0,1";		
			$uinfo = $db->get_row($upsql);
			$psmsg = $uinfo['UserPass'];
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
		
		$upsql =  "select UserID,UserName,UserPass,UserCompany,UserTrueName,UserFlag,UserSessionID,UserType from ".DATABASEU.DATATABLE."_order_user where UserCompany = {$arrCompanyData['CompanyID']} and UserFlag='9' order by UserID asc limit 0,1";		
		$uinfo = $db->get_row($upsql);
	}

	if(empty($uinfo['UserID']))
	{
		exit('该体验公司未设置管理员帐号，请联系客服！');
		//$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginIP,LoginDate,LoginUrl,LoginContent) values('m','".$us."','". $ip."',".time().",'Compute','帐号不存在')");
		//exit('notin');
	}
	else{
		$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyEmail,CompanyUrl,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$uinfo['UserCompany']." limit 0,1");
		if($ucinfo['CompanyFlag'] == "1") exit('companylock');
		
		$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$uinfo['UserCompany']." limit 0,1");
		if(time() > (strtotime($csinfo['CS_EndDate'])+60*60*24))
		{
			$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginIP,LoginDate,LoginUrl,LoginContent) values('c','".$us."','". $ip."',".time().",'Compute','供应商帐号已到期')");
			exit('companyexpired');
		}

        $serial = $db->get_var("SELECT COUNT(*) as cnt FROM ".DATABASEU.DATATABLE."_api_serial WHERE CompanyID=" . $uinfo['UserCompany']);
	
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
        $_SESSION['uc']['ERP']              = $serial === 1 ? 'ON' : 'OFF';
        
        //对应供应商的网银开户情况
        $netSql = "select GetWay from ".DATABASEU.DATATABLE."_order_getway where CompanyID=".$uinfo['UserCompany'];
        $netInfo = $db->get_results($netSql);
        //设置开户情况
        foreach($netInfo as $nv){
        	$_SESSION['uc'][$nv['GetWay']] = true;
        }

		$db->query("update ".DATABASEU.DATATABLE."_order_user set UserLoginDate=".time().",UserLoginIP='". $ip."',UserLogin=UserLogin+1,UserSessionID='".session_id()."' where UserID=".$uinfo['UserID']);
		if($uinfo['UserFlag']=="0")
		{
			$pope_info = $db->get_results("SELECT pope_module,pope_view,pope_form,pope_audit FROM ".DATABASEU.DATATABLE."_order_pope where pope_company=".$uinfo['UserCompany']." and pope_user=".$uinfo['UserID']." ");
			$popearr = null;
			if(!empty($pope_info))
			{
				foreach($pope_info as $pvar)
				{
					$popearr[$pvar['pope_module']] = $pvar;
				}				
			}
			$_SESSION['up'] = $popearr;
		}	
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
		
		/*
		if(!empty($uinfo['UserSessionID']) && $uinfo['UserSessionID']!=session_id())
		{
			$sessionpath = session_save_path();
			$user_sessionfile = $sessionpath."/sess_".$uinfo['UserSessionID'];
			@unlink($user_sessionfile);
		}
		*/
		
		$_SESSION['industry'] = $sOldIndustry_experience;
		
		// 标识这个公司已经使用
		$db->query("update ".DATABASEU.DATATABLE."_order_company set IsUse = 1,LoginIP = '{$ip}' where CompanyID = {$arrCompanyData['CompanyID']} ");
		
		if($uinfo['UserType']=="S")
		{
			echo '<script language="javascript">window.location.href="/s/order.php";</script>';
		}else{
			echo '<script language="javascript">window.location.href="/m/home.php";</script>';
		}
		
		exit('登陆成功，正在载入页面...');
	}
	exit('error');
}
elseif($in['m']=="bangding" && (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dinghuobao.cn")  || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "dhb.cn.com") || strpos($fromurl, "rs.com")))
{
	$us = strtolower(trim($in['UserName']));
	$ps = strtolower(trim($in['UserPass']));
	$vc = strtolower(trim($in['UserVC']));
	$openid = strtolower(trim($in['openid']));
	$accesstoken = strtolower(trim($in['accesstoken']));
	$nickname = strtolower(trim($in['nickname']));

	if(empty($vc) || $vc!=$_SESSION['securimage_code_value']) exit('验证码错误！');
	
	if(!is_filename($us)) exit('请输入合法的用户！(3-18位数字、字母和下划线)');
	if(strlen($us) < 3 || strlen($us) > 18 ) exit('请输入合法的用户！(3-18位数字、字母和下划线)');

	if(!is_filename($ps)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($ps) < 3 || strlen($ps) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(empty($openid)) exit('参数错误！');

	$datasql = "SELECT count(*) as lrow FROM ".DATABASEU.DATATABLE."_order_qq where OpenID='".$openid."' and UserType='M' limit 0,1";
	$ulinfo  = $db->get_row($datasql);
	if(!empty($ulinfo['lrow'])){
		header("location: ../m/do_login.php?m=login&openid=".$openid);
		exit;
	}else{
		$psmsg = ChangeMsg($us,$ps);
		$upsql =  "select UserID,UserName,UserPass,UserCompany,UserTrueName,UserFlag from ".DATABASEU.DATATABLE."_order_user where UserName = '".$us."' and UserPass='".$psmsg."' and UserFlag!='1' limit 0,1";	
		$uinfo = $db->get_row($upsql);
		if(!empty($uinfo)){
			$isi = $db->query("insert into ".DATABASEU.DATATABLE."_order_qq(UserID,UserType,AccessToken,OpenID,QQ)  values(".$uinfo['UserID'].",'M','".$accesstoken."','".$openid."','".$nickname."')");
			if($isi){
				header("location: ../m/do_login.php?m=login&openid=".$openid);
				exit;
			}else{
				exit('绑定不成功！');
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
		$sUrl = $industry ? M_SITE.'/experience/industry.php?industry='.$industry : '/';
	}else{
		$sUrl = '/';
	}
	
	session_unset(); 
	session_destroy();

    setcookie('isPop',null,time()-1);

		echo '
		<script language="javascript">
		window.location.href="'.$sUrl.'";
		</script>
		';
}
elseif($in['m']=="change_pass")
{
	$oldpass = strtolower($in['OldPass']);
	$newpass = strtolower($in['NewPass']);

	$opsmsg = ChangeMsg($_SESSION['uinfo']['username'],$oldpass);
	$npsmsg = ChangeMsg($_SESSION['uinfo']['username'],$newpass);

	if(!is_filename($newpass)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($newpass) < 3 || strlen($newpass) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');

	$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserPass='".$npsmsg."' where UserID=".$_SESSION['uinfo']['userid']." and UserCompany=".$_SESSION['uinfo']['ucompany']." and UserPass='".$opsmsg."'";

	if($db->query($upsql))
	{
		exit("ok");
	}else{
		exit("olderror");
	}
}
elseif($in['m'] == 'cancel_qq')
{
	if(!empty($in['openid'])){
		$upsql = "delete from ".DATABASEU.DATATABLE."_order_qq where UserID=".$_SESSION['uinfo']['userid']." and OpenID='".$in['openid']."' limit 1";

		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("执行不成功！");
		}
	}else{
		exit('参数错误!');
	}

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