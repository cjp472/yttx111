<?php
$menu_flag = "client";
include_once ("header.php");
include_once ("../class/sms.class.php");
include_once ("../class/letter.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m'] == 'check_client_name') {

    $is_exists = check_client_name($db,$in['client_company_name'],$in['client_id']);
    exit($is_exists ? ':-(' : 'ok');
}

if($in['m']=="delete")
{

	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法操作!');
	$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
	if (!in_array($in['ID'], $sclientidarr ))
	{		
		exit('对不起，您没有此项操作权限！');
	}	

	$upsql =  "update ".DATATABLE."_order_client set ClientFlag=1 where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		$db->query("update ".DATABASEU.DATATABLE."_order_dealers set ClientFlag=1 where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']);

		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

//*****recycle************/
if($in['m']=="restore")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法参数!');
	$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
	if (!in_array($in['ID'], $sclientidarr ))
	{		
		exit('对不起，您没有此项操作权限！');
	}

	$InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");

	$msg = '您只有 '.$_SESSION['uc']['Number'].'个授权药店， 您已全部用完，请联系开发商增加授权用户';

	if($InfoDataNum['clientrow'] >= $_SESSION['uc']['Number']) exit($msg);

	$upsql = "update ".DATATABLE."_order_client set ClientFlag=0 where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];
	if($db->query($upsql))
	{
		$db->query("update ".DATABASEU.DATATABLE."_order_dealers set ClientFlag=0 where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']);
		exit('ok');
	}else{
		exit('还原不成功!');
	}
}


if($in['m']=="quite_delete")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法参数!');
	$in['ID'] = intval($in['ID']);
	$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
	if (!in_array($in['ID'], $sclientidarr ))
	{		
		exit('对不起，您没有此项操作权限！');
	}
	$InfoDataNum = $db->get_row("SELECT count(*) AS orderrow FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$in['ID']);
	
	if(!empty($InfoDataNum['orderrow']))
	{
		exit('该药店已有订单数据，不能删除！');
	}else{
		$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_client where  ClientID = ".$in['ID']." and ClientCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
		$infodatamsg = serialize($InfoData);
		$upsql =  "delete from ".DATATABLE."_order_client where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=1 limit 1";	
		if($db->query($upsql))
		{

			$db->query("delete from ".DATATABLE."_order_salerclient where CompanyID=".$_SESSION['uinfo']['ucompany']." and SalerID=".$_SESSION['uinfo']['userid']." and ClientID=".$in['ID']." limit 1");
			$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_client.php?m=quite_delete&ID=".$in['ID']."','删除药店(".$in['ID'].")','".$infodatamsg."',".time().")";
			$db->query($sqlex);
			$db->query("delete from ".DATABASEU.DATATABLE."_order_dealers where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=1 limit 1 ");
			exit('ok');
		}else{
			exit('删除不成功!');
		}
	}
}

/***********药店**************/
if($in['m']=="content_add_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	$in['ClientName']	  = strtolower($in['ClientName']);
	
	$in['ClientPassword'] = strtolower($in['ClientPassword']);
	if(!is_filename($in['ClientName'])) exit('okname');
	if(strlen($in['ClientName']) < 1 || strlen($_SESSION['uc']['CompanyPrefix']."-".$in['ClientName']) > 18 ) exit('okname');
	if(!is_filename($in['ClientPassword'])) exit('UserPass');
	if(strlen($in['ClientPassword']) < 3 || strlen($in['ClientPassword']) > 18 ) exit('okpass');
	if(!empty($in['ClientMobile']))
	{
		if(!is_phone($in['ClientMobile'])) exit('请输入正确的手机号码!');
	}

    if(!empty($in['ClientCompanyName']) && check_client_name($db,$in['ClientCompanyName'],null)) {
        exit("药店名称已存在!");
    }

	$InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");
	$msg = '您只有 '.$_SESSION['uc']['Number'].' 个授权药店， 您已全部用完，请联系开发商增加授权用户';
	if($InfoDataNum['clientrow'] >= $_SESSION['uc']['Number']) exit($msg);
	$in['ClientName'] = $_SESSION['uc']['CompanyPrefix']."-".$in['ClientName'];
	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientName='".$in['ClientName']."' limit 0,1");
	if(!empty($clientinfo['orwname'])) exit('repeat');	

	$DMobile = '';
	$smsmobilemsg = '';
	if(!empty($in['loginmobile']) && !empty($in['ClientMobile']))
	{
		$clientminfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientMobile='".$in['ClientMobile']."'  limit 0,1");
		if($clientminfo['orwname'] > 0) exit('此手机号码已使用，不能用此手机号码登录！');
		$DMobile = $in['ClientMobile'];
		$smsmobilemsg = '(或用手机号：'.$in['ClientMobile'].')';
	}
	
	if(!empty($in['ClientConsignment'])) $consignmentmsg = implode(",", $in['ClientConsignment']); else $consignmentmsg = '';
	if(!empty($in['ClientPay'])) $paymsg = implode(",", $in['ClientPay']); else $paymsg = '';
	if(!empty($in['ClientSortID'])) $shieldmsg = implode(",", $in['ClientSortID']); else $shieldmsg = '';
	if(empty($in['ClientPercent'])) $in['ClientPercent'] = '10.0';
    if(!empty($in['ClientAudit'])) $clientflag = 0; else $clientflag = 9;
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_dealers(ClientCompany,ClientName,ClientPassword,ClientMobile,LoginDate,ClientFlag) values(".$_SESSION['uinfo']['ucompany'].", '".$in['ClientName']."', '".$in['ClientPassword']."','".$DMobile."',".time().",".$clientflag.")";
	if($db->query($upsql))
	{
		$inid =  $db->insert_id;
		$levelmsg = "";
	    $letter  = new letter();
        $pinyima = $letter->C($in['ClientCompanyName']);
		$levelmsg = $brandmsg = "";
		$brandarr = null;
		foreach($in as $ikey=>$ivar)
		{
			if(substr($ikey,0,11)=="ClientLevel")
			{
				if(!empty($in[$ikey]))
				{
					if(!empty($levelmsg)) $levelmsg .= ",";
					$levelmsg .= substr($ikey,12,1)."_".$in[$ikey];
				}
			}
			if(substr($ikey,0,12) == "BrandPercent")
			{
				if(!empty($in[$ikey]))
				{
					$brandarr[substr($ikey,13)] = floatval($in[$ikey]);
				}
			}
		}
		if(!empty($brandarr)) $brandmsg = serialize($brandarr);
		$insql	 = "insert into ".DATATABLE."_order_client(ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientConsignment,ClientPay,ClientFlag) values(".$inid.",".$_SESSION['uinfo']['ucompany'].",'".$levelmsg."',".$in['ClientArea'].", '".$in['ClientName']."', '".$in['ClientCompanyName']."','".$pinyima."', '".$in['ClientNO']."', '".$in['ClientTrueName']."', '".$in['ClientEmail']."', '".$in['ClientPhone']."', '".$in['ClientFax']."', '".$in['ClientMobile']."', '".$in['ClientAdd']."', '".$in['ClientAbout']."',".time().", '".$shieldmsg."','".$in['ClientSetPrice']."', '".$in['ClientPercent']."', '".$brandmsg."', '".$consignmentmsg."', '".$paymsg."',".$clientflag.")";
		$db->query($insql);

		//归属到药店下面
		$insqls = "insert into ".DATATABLE."_order_salerclient(CompanyID,SalerID,ClientID) values(".$_SESSION['uinfo']['ucompany'].",".$_SESSION['uinfo']['userid'].",".$inid.")";
		$db->query($insqls);
		
		if(!empty($in['sendsmsuser']) && !empty($in['ClientMobile']))
		{
			if(empty($_SESSION['uc']['CompanyUrl'])) $orderurl = 'http://'.$_SESSION['uc']['CompanyPrefix'].'.dhb.hk'; else $orderurl = $_SESSION['uc']['CompanyUrl']; 			
			$message = "【".$_SESSION['uc']['CompanySigned']."】".$_SESSION['uc']['CompanyName']."为您开通了网上订货系统 网址: ".$orderurl." 帐号:".$in['ClientName'].$smsmobilemsg." 密码:".$in['ClientPassword']." 欢迎您登录本系统订货";
			sms::send_sms($in['ClientMobile'],$message,$inid);
		}
		exit("ok");
		
	}else{
		exit("保存不成功!");
	}
}

/***********editsave**************/
if($in['m']=="content_edit_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	$in['ClientName']     = strtolower(trim($in['ClientName']));
	$in['ClientPassword'] = strtolower($in['ClientPassword']);
	if(!is_filename($in['ClientName'])) exit('okname');
	if(strlen($in['ClientName']) < 1 || strlen($_SESSION['uc']['CompanyPrefix']."-".$in['ClientName']) > 18 ) exit('okname');
	if(!is_filename($in['ClientPassword'])) exit('okpass');
	if(strlen($in['ClientPassword']) < 3 || strlen($in['ClientPassword']) > 18 ) exit('okpass');
		
	if(!empty($in['ClientMobile']))
	{
		if(!is_phone($in['ClientMobile'])) exit('请输入正确的手机号码!');
	}
	$in['ClientName'] = $_SESSION['uc']['CompanyPrefix']."-".$in['ClientName'];

    if(!empty($in['ClientCompanyName']) && check_client_name($db,$in['ClientCompanyName'],$in['ClientID'])) {
        exit("药店名称已存在!");
    }

	if(!empty($in['ClientAudit'])) $clientflag = 0; else $clientflag = 9;
	if($clientflag == 0)
	{
		$InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");
	
		$msg = '您只有 '.$_SESSION['uc']['Number'].' 个授权药店， 您已全部用完，请联系软件提供者增加授权用户!';
		if($InfoDataNum['clientrow'] > $_SESSION['uc']['Number']) exit($msg);
	}
	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientName='".$in['ClientName']."' limit 0,1");
	if($clientinfo['orwname'] > 1) exit('repeat');

	$sqlmobile = ", ClientMobile='' ";
	$smsmobilemsg = '';
	if(!empty($in['loginmobile']) && !empty($in['ClientMobile']))
	{
		$clientminfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientMobile='".$in['ClientMobile']."' and ClientID!=".$in['ClientID']." limit 0,1");
		if($clientminfo['orwname'] > 0) exit('此手机号码已使用，不能用此手机号码登录！');
		$sqlmobile = ", ClientMobile='".$in['ClientMobile']."' ";
		$smsmobilemsg = '(或用手机号：'.$in['ClientMobile'].')';
	}	
	$insql	     = "update ".DATABASEU.DATATABLE."_order_dealers set ClientName='".$in['ClientName']."', ClientPassword='".$in['ClientPassword']."' ".$sqlmobile.", ClientFlag=".$clientflag." where ClientID=".$in['ClientID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];
	$isu = $db->query($insql);

	if(empty($in['ClientPercent'])) $in['ClientPercent'] = '10.0';		
	if(!empty($in['ClientConsignment'])) $consignmentmsg = implode(",", $in['ClientConsignment']); else $consignmentmsg = '';
	if(!empty($in['ClientPay'])) $paymsg = implode(",", $in['ClientPay']); else $paymsg = '';	
	if(!empty($in['ClientSortID'])) $shieldmsg = implode(",", $in['ClientSortID']); else $shieldmsg = '';

	if(!empty($in['ClientID']))
	{		
	    $letter  = new letter();
        $pinyima = $letter->C($in['ClientCompanyName']);
		$levelmsg = $brandmsg = "";
		$brandarr = null;
		foreach($in as $ikey=>$ivar)
		{
			if(substr($ikey,0,11)=="ClientLevel")
			{
				if(!empty($in[$ikey]))
				{
					if(!empty($levelmsg)) $levelmsg .= ",";
					$levelmsg .= substr($ikey,12,1)."_".$in[$ikey];
				}
			}
			if(substr($ikey,0,12) == "BrandPercent")
			{
				if(!empty($in[$ikey]))
				{
					$brandarr[substr($ikey,13)] = floatval($in[$ikey]);
				}
			}
		}

		if(!empty($brandarr)) $brandmsg = serialize($brandarr);
		$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_client where  ClientID = ".$in['ClientID']." and ClientCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
		$infodatamsg = serialize($InfoData);

		$upsql = "update ".DATATABLE."_order_client set ClientLevel='".$levelmsg."', ClientArea=".$in['ClientArea'].", ClientName='".$in['ClientName']."', ClientCompanyName='".$in['ClientCompanyName']."', ClientCompanyPinyi='".$pinyima."', ClientNO='".$in['ClientNO']."',  ClientTrueName='".$in['ClientTrueName']."', ClientEmail='".$in['ClientEmail']."', ClientPhone='".$in['ClientPhone']."', ClientFax='".$in['ClientFax']."', ClientMobile='".$in['ClientMobile']."', ClientAdd='".$in['ClientAdd']."', ClientAbout='".$in['ClientAbout']."',ClientShield='".$shieldmsg."', ClientSetPrice='".$in['ClientSetPrice']."', ClientPercent='".$in['ClientPercent']."',ClientBrandPercent='".$brandmsg."', ClientConsignment='".$consignmentmsg."', ClientPay='".$paymsg."', ClientFlag=".$clientflag." where ClientID=".$in['ClientID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];
		$isup = $db->query($upsql);
		
		if(!empty($in['sendsmsuser']) && !empty($in['ClientMobile']))
		{
			if(empty($_SESSION['uc']['CompanyUrl'])) $orderurl = 'http://'.$_SESSION['uc']['CompanyPrefix'].'.dhb.hk'; else $orderurl = $_SESSION['uc']['CompanyUrl']; 
			$message = "【".$_SESSION['uc']['CompanySigned']."】".$_SESSION['uc']['CompanyName']." 为您开通了网上订货系统 网址: ".$orderurl ." 帐号:".$in['ClientName'].$smsmobilemsg." 密码:".$in['ClientPassword']." 欢迎您登录本系统订货";
			sms::send_sms($in['ClientMobile'],$message,$in['ClientID']);
		}
	
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_client.php?m=content_edit_save','修改药店(".$in['ClientID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		exit("ok");
	}
}

		
/************* Point ****************/
if($in['m']=="content_point_save")
{	
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	if(!empty($in['data_ClientID']))
	{
		$pv = intval($in['point']);
		if(empty($pv))
		{
			exit('请输入正确的分值！');
		}else{
			$upsql = "insert into ".DATATABLE."_order_point(PointCompany,PointClient,PointValue,PointTitle,PointDate,PointUser) values(".$_SESSION['uinfo']['ucompany'].", '".$in['data_ClientID']."', ".$pv.",'".$in['title']."',".time().",".$_SESSION['uinfo']['userid'].")";	
		}
	}

	if($db->query($upsql))
	{
		exit("ok");		
	}else{
		exit("保存不成功!");
	}
}

/**
 * @desc 检查药店名称是否已存在
 * @param ez_sql $db
 * @param string $name
 * @param int $client_id default null
 * @return bool $cnt;
 */
function check_client_name($db,$name,$client_id = null) {
    $company_id = $_SESSION['uinfo']['ucompany'];
    $where = " WHERE ClientCompany={$company_id} AND ClientCompanyName='{$name}' ";
    if(!is_null($client_id)) {
        $where .= " AND ClientID <> " . (int)$client_id;
    }
    $cnt = $db->get_var("SELECT COUNT(*) AS Total FROM ".DATATABLE."_order_client {$where} LIMIT 1");
    return $cnt > 0;
}

		
exit('非法操作!');
?>