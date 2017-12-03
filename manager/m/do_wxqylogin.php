<?php
include_once ("../common.php");

define("CorpID","wxc227fd13cc51c6f1");//微信企业号ID
define("providersecret","Sl0RSi87JtCJhbP53nN-L4mlWE8I11JP22mLX2exdMfowXsrsSYETTy6DYXMwuOm");//微信企业号secret

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

function ilog($title,$data){
    $file = 'log_' . date('Y-m-d') . '.txt';
    error_log(date('Y-m-d H:i:s') . ' - ' . $title . ' - ilog ;' . print_r($data,true) . "\n",3,'../data/log/' . $file);
}

/**
 * @author seekfor
 * @desc 提交数据
 * @param $url:提交地址,$data:要提交的数据（json格式）
 * @return 数组格式数据
 * @version 2013-12-24
 */
function curl_post_data_token($url,$data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$tmpInfo = curl_exec($ch);

	if (curl_errno($ch)){
		$tmpInfo = 'Errno'.curl_error($ch);
	}
	curl_close($ch);

	$rdata = json_decode($tmpInfo);
	$rdata = (array)$rdata;

	return $rdata;
}

if($in['m']=="login" && (!empty($in['us']) || (strpos($fromurl, "dhb.hk") || strpos($fromurl, "dinghuobao.cn")  || strpos($fromurl, "dhb.net.cn") || strpos($fromurl, "dhb.cn.com") || strpos($fromurl, "rs.com") || strpos($fromurl, "online.com")))){
	  //$fLog = KLogger::instance(LOG_PATH);
		$info['corpid']=urldecode($in['us']); //存在corpid取这个
		if($info['corpid']!=""){
			$corpinfo_sql = "select CompanyID,CorpID,username,editpass from ".DATABASEU.DATATABLE."_order_weixinqy where CorpID='".$info['corpid']."'";
			$result = $db->get_row($corpinfo_sql);//取授权信息
			if($result['CompanyID']!=""){//已经存在套件授权信息
				$us = $result['username'];//首次登录的用户名
			}else{
				exit('用户信息不存在!');
			}
	    }
	
		$ip = RealIp();
		 $upsql =  "select UserID,UserName,UserPass,UserCompany,UserTrueName,UserFlag,UserSessionID,UserType from ".DATABASEU.DATATABLE."_order_user where UserName = '".$us."' and UserFlag!='1' limit 0,1";
		$uinfo = $db->get_row($upsql);		


		$ucinfo = $db->get_row("select CompanyIndustry,CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyEmail,CompanyUrl,CompanyFlag,CompanyDatabase,CompanyType from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$uinfo['UserCompany']." limit 0,1");


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
		
		if(!empty($uinfo['UserSessionID']) && $uinfo['UserSessionID']!=session_id())
		{
			if(DHB_DEVELOPMENT_MODE !== 'development'){
				$sessionpath = session_save_path();
				$user_sessionfile = $sessionpath."/sess_".$uinfo['UserSessionID'];
				@unlink($user_sessionfile);
			}
		}

		if(DHB_RUNTIME_MODE === 'experience'){
			if(!empty($ucinfo['CompanyIndustry'])){
				$_SESSION['industry'] = encodeData($ucinfo['CompanyIndustry']);
			}
		}

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