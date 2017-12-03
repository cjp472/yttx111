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

if($in['m']=="login" && (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dhb.cn.com") || strpos($fromurl, "dinghuobao.cn")  || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "rs.com")))
{
	$us = strtolower(trim($in['UserName']));
	$ps = strtolower(trim($in['UserPass']));
	$vc = strtolower(trim($in['UserVc']));
	$ip = trim($in['LoginIP']);
	if(empty($ip)) $ip = RealIp();
	
	if(!is_filename($us)) exit('请输入合法的用户！(3-18位数字、字母和下划线)');
	if(strlen($us) < 3 || strlen($us) > 18 ) exit('请输入合法的用户！(3-18位数字、字母和下划线)');

	if(!is_filename($ps)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($ps) < 3 || strlen($ps) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');

	if(empty($vc) || $vc!=$_SESSION['securimage_code_value']) exit('errorcode');
	$psmsg = ChangeMsg($us,$ps);
	
	$upsql =  "select UserID,UserName,UserPass,UserCompany,UserTrueName,UserFlag,UserSessionID,UserType from ".DATABASEU.DATATABLE."_order_user where UserName = '".$us."' and UserFlag!='1' limit 0,1";		
	$uinfo = $db->get_row($upsql);

	if(empty($uinfo['UserID']))
	{
		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginIP,LoginDate,LoginUrl,LoginContent) values('m','".$us."','". $ip."',".time().",'".$fromurl."','帐号不存在')");
		exit('notin');
	}
	else if($uinfo['UserPass'] == $psmsg)
	{
		$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyEmail,CompanyUrl,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$uinfo['UserCompany']." limit 0,1");
		if($ucinfo['CompanyFlag'] == "1") exit('companylock');
		
		$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$uinfo['UserCompany']." limit 0,1");

		if(time() > (strtotime($csinfo['CS_EndDate'])+60*60*24))
		{
			$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginIP,LoginDate,LoginUrl,LoginContent) values('c','".$us."','". $ip."',".time().",'".$fromurl."','供应商帐号已到期')");
			exit('companyexpired');
		}
	
		$_SESSION['uinfo']['userid']        = $uinfo['UserID'];
		$_SESSION['uinfo']['ucompany'] = $uinfo['UserCompany'];
		$_SESSION['uinfo']['username']   = $uinfo['UserName'];
		$_SESSION['uinfo']['usertruename'] = $uinfo['UserTrueName'];
		$_SESSION['uinfo']['userflag']      = $uinfo['UserFlag'];
		$_SESSION['uinfo']['usertype']      = $uinfo['UserType'];

		$_SESSION['uc'] = $ucinfo;
		$_SESSION['uc']['Number']       =  $csinfo['CS_Number'];
		$_SESSION['uc']['BeginDate']    =  $csinfo['CS_BeginDate'];
		$_SESSION['uc']['EndDate']       =  $csinfo['CS_EndDate'];
		$_SESSION['uc']['SmsNumber'] =  $csinfo['CS_SmsNumber'];

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

		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_user_log(LoginCompany,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl) values(". $uinfo['UserCompany'].",".$uinfo['UserID'].",'".$uinfo['UserName']."','". $ip."',".time().",'".$fromurl."')");
		
		if(!empty($uinfo['UserSessionID']) && $uinfo['UserSessionID']!=session_id())
		{
			$sessionpath = session_save_path();
			$user_sessionfile = $sessionpath."/sess_".$uinfo['UserSessionID'];
			@unlink($user_sessionfile);
		}
		if($uinfo['UserType']=="S")
		{
			echo '<script language="javascript">window.location.href="/s/order.php";</script>';
		}else{
			echo '<script language="javascript">window.location.href="/m/home.php";</script>';
		}
		exit('登陆成功，正在载入页面...');
	}else{
		$db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginPass,LoginIP,LoginDate,LoginUrl,LoginContent) values('m','".$us."','".$ps."','". $ip."',".time().",'".$fromurl."','帐号与密码不匹配')");
		exit("isnot");
	}
	exit('error');
}
elseif($in['m'] == 'admintologin')
{
	if(!in_array($_SESSION['uinfo']['userid'],array(1))) exit('非法路径!');
	if(empty($in['companyid'])) exit('错误参数!');

	$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyEmail,CompanyUrl,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$in['companyid']." limit 0,1");

	$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$in['companyid']." limit 0,1");

	$_SESSION['uinfo']['ucompany'] = $ucinfo['CompanyID'];
	$_SESSION['uc'] = $ucinfo;
	$_SESSION['uc']['Number']    =  $csinfo['CS_Number'];
	$_SESSION['uc']['BeginDate'] =  $csinfo['CS_BeginDate'];
	$_SESSION['uc']['EndDate']   =  $csinfo['CS_EndDate'];
	$_SESSION['uc']['SmsNumber'] =  $csinfo['CS_SmsNumber'];

	     //对应供应商的网银开户情况
        $netSql = "select GetWay from ".DATABASEU.DATATABLE."_order_getway where CompanyID=".$ucinfo['CompanyID'];
        $netInfo = $db->get_results($netSql);
        //设置开户情况
        foreach($netInfo as $nv){
        	$_SESSION['uc'][$nv['GetWay']] = true;
        }


	echo '
		<script language="javascript">
		window.location.href="../m/home.php";
		</script>
		';
}
elseif($in['m']=="logout")
{	
	session_unset(); 
	session_destroy();

		echo '
		<script language="javascript">
		window.location.href="/";
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