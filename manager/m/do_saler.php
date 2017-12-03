<?php
$menu_flag = "saler";
include_once ("header.php");
include_once ("../class/letter.class.php");
include_once ("../class/data.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="delete")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATABASEU.DATATABLE."_order_user set UserFlag='1' where UserID = ".$in['ID']." and UserType='S' and UserCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=delete&ID=".$in['ID']."','删除客情官(".$in['ID'].")','-',".time().")";
		$db->query($sqlex);
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

//*****recycle************/
if($in['m']=="restore")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATABASEU.DATATABLE."_order_user set UserFlag='0' where UserID = ".$in['ID']." and UserCompany=".$_SESSION['uinfo']['ucompany']." and UserType='S' ";	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=restore&ID=".$in['ID']."','恢复客情官帐号(".$in['ID'].")','-',".time().")";
		$db->query($sqlex);
		exit('ok');
	}else{
		exit('还原不成功!');
	}
}

if($in['m']=="quite_delete")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	$CountData = $db->get_row("SELECT count(*) as num FROM ".DATATABLE."_order_deduct where DeductUser = ".$in['ID']." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1");

	if($CountData['num'] > 0) exit('此客情官已发生业务数据，不能删除！');

	$upsql =  "delete from ".DATABASEU.DATATABLE."_order_user where UserID = ".$in['ID']." and UserType='S' and UserCompany=".$_SESSION['uinfo']['ucompany']."  limit 1";
	
	$InfoData = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$in['ID']." and UserCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$infodatamsg = serialize($InfoData);

	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=quite_delete&ID=".$in['ID']."','彻底删除客情官帐号(".$in['ID'].")','".$infodatamsg."',".time().")";
		
		$db->query("delete from ".DATABASEU.DATATABLE."_order_pope where pope_company=".$_SESSION['uinfo']['ucompany']." and pope_user=".$in['ID']);
		$db->query($sqlex);
		$db->query("delete from ".DATATABLE."_order_salerclient where CompanyID=".$_SESSION['uinfo']['ucompany']." and SalerID=".$in['ID']);

		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


//选择药店
if($in['m']=="sub_add_client")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$jsmsg = "";
	if(!empty($in['selectclient']))
	{	
		$comma_separated = implode(",", $in['selectclient']);
		$clientdata = $db->get_results("SELECT ClientID,ClientCompanyName,ClientCompanyPinyi FROM ".DATATABLE."_order_client where ClientID in (".$comma_separated.") and ClientCompany = ".$_SESSION['uinfo']['ucompany']."  and ClientFlag=0  ");
		if(!empty($clientdata))
		{
			foreach($clientdata as $cvar)
			{
				$cvar['ClientCompanyName'] = preg_replace('/"([^"]*)"/', '“${1}”', $cvar['ClientCompanyName']);
				$cvar['ClientCompanyName'] = str_replace('"',"“",$cvar['ClientCompanyName']);
				if(!empty($cvar['ClientCompanyName'])) $jsmsg .= '<option value=\"'.$cvar['ClientID'].'\">'.$cvar['ClientCompanyName'].'</option>';
			}
			$omsg .= '{"backtype":"ok", "htmldata":"'.$jsmsg.'"}';
		}else{
			$omsg .= '{"backtype":"请先选择您要管辖的药店!"}';
		}
	}else{
		$omsg .= '{"backtype":"请先选择您要管辖的药店!"}';
	}
	echo $omsg;
	exit();
}
//wangkk 2017-11-28选择商品
if($in['m']=="sub_add_goods")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$jsmsg = "";
	if(!empty($in['selectclient']))
	{

		$comma_separated = implode(",", $in['selectclient']);
		$clientdata = $db->get_results("SELECT ID,Name,Pinyi FROM ".DATATABLE."_order_content_index where ID in (".$comma_separated.") and CompanyID = ".$_SESSION['uinfo']['ucompany']."");
                if(!empty($clientdata))
		{
			foreach($clientdata as $cvar)
			{
				$cvar['Name'] = preg_replace('/"([^"]*)"/', '“${1}”', $cvar['Name']);
				$cvar['Name'] = str_replace('"',"“",$cvar['Name']);
				if(!empty($cvar['Name'])) $jsmsg .= '<option value=\"'.$cvar['ID'].'\">'.$cvar['Name'].'</option>';
			}
			$omsg .= '{"backtype":"ok", "htmldata":"'.$jsmsg.'"}';
		}else{
			$omsg .= '{"backtype":"请选择正确的药品！"}';
		}
	}else{
		$omsg .= '{"backtype":"请选择正确的药品！"}';
	}
	echo $omsg;
	exit();
}

//按地区
if($in['m']=="loadclientlist")
{
	if(!intval($in['ID'])) exit('error');

	$parr = $db->get_col("SELECT AreaID FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and AreaParentID=".$in['ID']." ORDER BY AreaID ASC ");
	if(empty($parr))
	{
		$sqla = " and ClientArea=".$in['ID']." ";
	}else{
		$pidmsg = implode(",", $parr);
		$sqla = " and ClientArea IN (".$in['ID'].",".$pidmsg.") ";
	}

	$orderlistuser = $db->get_results("SELECT ClientID,ClientCompanyName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ".$sqla." order by ClientID ASC limit 0,1000");
	$bodymsg = '';
	$headermsg = '';
	if(!empty($orderlistuser))
	{
		if(!empty($in['sid'])) $smsg = " and SalerID <> ".$in['sid'].""; else  $smsg = "";
		$cinfo = $db->get_col("select ClientID FROM  ".DATATABLE."_order_salerclient where CompanyID=".$_SESSION['uinfo']['ucompany']." ".$smsg." ");
		foreach($orderlistuser as $olvar)
		{
			if(in_array($olvar['ClientID'], $cinfo)) continue;
			$bodymsg .= '<option value="'.$olvar['ClientID'].'">'.$olvar['ClientCompanyName'].'</option>';
		}
	}
	$endmsg = '';
	echo $headermsg.$bodymsg.$endmsg;
	exit();
}


if($in['m']=="saler_add_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	$in['UserName'] = strtolower($in['data_UserName']);
	$in['UserPass']   = strtolower($in['data_UserPass']);

	if(!is_filename($in['UserName'])) exit('okname');
	if(strlen($in['UserName']) < 3 || strlen($in['UserName']) > 18 ) exit('okname');

	if(!is_filename($in['UserPass'])) exit('okpass');
	if(strlen($in['UserPass']) < 3 || strlen($in['UserPass']) > 18 ) exit('okpass');
	
	if(!is_phone($in['data_UserMobile'])) exit('okmobile');
	if(strlen($in['data_UserMobile']) != 11) exit('okmobile');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserName='".$in['UserName']."'");
	if(!empty($clientinfo['orwname'])) exit('repeat');
	$passmsg = ChangeMsg($in['UserName'],$in['UserPass']);
	
	//Tah 2017/11/28
	$upper_id = '0';
	$user_flag = trim($_SESSION['uinfo']['userflag']);
	if ($user_flag == '2')
	{	
		//增加代理商客情
		$upper_id = $_SESSION['uinfo']['userid'];
	}
	else {	
		//增加商业公司客情
		//商业公司主账号和子账号都可以创建客情的，其创建的客情的user_flag都设置为0
		//当下写死：管理员只能增加商业公司客情
		$user_flag = 0;
	}
	
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_user(UpperID,UserFlag,UserName,UserPass,UserCompany,UserTrueName,UserMobile,UserPhone,UserDate,UserRemark,UserType) values($upper_id,'$user_flag','".$in['UserName']."','".$passmsg."',".$_SESSION['uinfo']['ucompany'].",'".$in['data_UserTrueName']."','".$in['data_UserMobile']."','".$in['data_UserPhone']."', ".time().",'".$in['data_UserRemark']."','S')";
	if($db->query($upsql))
	{
		$uid = mysql_insert_id();
		if(!empty($in['Shield']))
		{
			$shieldarr1 = explode(",",$in['Shield']);
			$shieldarr   = array_unique($shieldarr1);
			foreach($shieldarr as $var)
			{				
				if(!empty($var)) $db->query("insert into ".DATATABLE."_order_salerclient(CompanyID,SalerID,ClientID) values(".$_SESSION['uinfo']['ucompany'].",".$uid.",".intval($var).")");
			}
		}
		$pkey = 'order';
		if(empty($in['view_order'])) $in['view_order'] = 'N';
		if(empty($in['form_order'])) $in['form_order'] = 'N';
		if(empty($in['audi_order'])) $in['audi_order'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$uid.",'".$pkey."','".$in['view_'.$pkey]."','".$in['form_'.$pkey]."','".$in['audi_'.$pkey]."')");

		$pkey = 'consignment';
		if(empty($in['view_consignment'])) $in['view_consignment'] = 'N';
		if(empty($in['audi_consignment'])) $in['audi_consignment'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$uid.",'".$pkey."','".$in['view_'.$pkey]."','N','".$in['audi_'.$pkey]."')");

		$pkey = 'inventory';
		if(empty($in['view_inventory'])) $in['view_inventory'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$uid.",'".$pkey."','".$in['view_'.$pkey]."','N','N')");

		$pkey = 'client';
		if(empty($in['view_client'])) $in['view_order'] = 'N';
		if(empty($in['form_client'])) $in['form_order'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$uid.",'".$pkey."','".$in['view_'.$pkey]."','".$in['form_'.$pkey]."','N')");
		
		$infodatamsg = serialize($in);
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=saler_add_save','添加客情官帐号(".$uid.")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}


if($in['m']=="saler_edit_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$in['UserName'] = strtolower($in['data_UserName']);
	$in['UserPass'] = strtolower($in['data_UserPass']);

	if(empty($in['UserID'])) exit('参数错误!');
	if(!is_filename($in['UserName'])) exit('okname');
	if(strlen($in['UserName']) < 3 || strlen($in['UserName']) > 18 ) exit('okname');
	
	if(!is_phone($in['data_UserMobile'])) exit('okmobile');
	if(strlen($in['data_UserMobile']) != 11) exit('okmobile');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserName='".$in['UserName']."' limit 0,1");
	if($clientinfo['orwname'] > 1) exit('repeat');

	if(empty($in['UserPass']))
	{
		$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."', UserTrueName='".$in['data_UserTrueName']."', UserMobile='".$in['data_UserMobile']."', UserPhone='".$in['data_UserPhone']."', UserRemark='".$in['data_UserRemark']."' where UserID=".$in['UserID']." and UserCompany=".$_SESSION['uinfo']['ucompany']." and UserType='S' ";
	}else{
		if(!is_filename($in['UserPass'])) exit('okpass');
		if(strlen($in['UserPass']) < 3 || strlen($in['UserPass']) > 18 ) exit('okpass');
		$passmsg = ChangeMsg($in['UserName'],$in['UserPass']);

		$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."',UserPass='".$passmsg."', UserTrueName='".$in['data_UserTrueName']."', UserMobile='".$in['data_UserMobile']."', UserPhone='".$in['data_UserPhone']."', UserRemark='".$in['data_UserRemark']."' where UserID=".$in['UserID']." and UserCompany=".$_SESSION['uinfo']['ucompany']."  and UserType='S' ";
	}
	$db->query($upsql);

		//客户关系
		$db->query("delete from ".DATATABLE."_order_salerclient where CompanyID=".$_SESSION['uinfo']['ucompany']." and SalerID=".$in['UserID']);
		if(!empty($in['Shield']))
		{
			$shieldarr1 = explode(",",$in['Shield']);
			$shieldarr   = array_unique($shieldarr1);
			foreach($shieldarr as $var)
			{				
				if(!empty($var)) $db->query("insert into ".DATATABLE."_order_salerclient(CompanyID,SalerID,ClientID) values(".$_SESSION['uinfo']['ucompany'].",".$in['UserID'].",".intval($var).")");
			}
		}

		//权限
		$db->query("delete from ".DATABASEU.DATATABLE."_order_pope where pope_company=".$_SESSION['uinfo']['ucompany']." and pope_user=".$in['UserID']);
		$pkey = 'order';
		if(empty($in['view_order'])) $in['view_order'] = 'N';
		if(empty($in['form_order'])) $in['form_order'] = 'N';
		if(empty($in['audi_order'])) $in['audi_order'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$in['UserID'].",'".$pkey."','".$in['view_'.$pkey]."','".$in['form_'.$pkey]."','".$in['audi_'.$pkey]."')");

		$pkey = 'consignment';
		if(empty($in['view_consignment'])) $in['view_consignment'] = 'N';
		if(empty($in['audi_consignment'])) $in['audi_consignment'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$in['UserID'].",'".$pkey."','".$in['view_'.$pkey]."','N','".$in['audi_'.$pkey]."')");

		$pkey = 'inventory';
		if(empty($in['view_inventory'])) $in['view_inventory'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$in['UserID'].",'".$pkey."','".$in['view_'.$pkey]."','N','N')");

		$pkey = 'client';
		if(empty($in['view_client'])) $in['view_client'] = 'N';
		if(empty($in['form_client'])) $in['form_client'] = 'N';
		$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$in['UserID'].",'".$pkey."','".$in['view_'.$pkey]."','".$in['form_'.$pkey]."','N')");

        $pkey = 'statistics';
        if(empty($in['view_statistics'])) $in['view_statistics'] = 'N';
        $db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$in['UserID'].",'".$pkey."','".$in['view_'.$pkey]."','N','N')");

		$infodatamsg = serialize($clientflag);
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=saler_edit_save','修改客情官帐号(".$in['UserID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		exit("ok");
}


//发放提成
if($in['m']=="validate_deduct")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_deduct set DeductStatus='T', DeductToDate=".time()." where DeductID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=validate_deduct&ID=".$in['ID']."','发放提成(".$in['ID'].")','-',".time().")";
		$db->query($sqlex);
		exit('ok');
	}else{
		exit('操作不成功!');
	}
}

//批量发放提成
if($in['m']=="do_more_validate")
{
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	
	$deleteidmsg = implode(",", $in['selectedID']);
	$upsql =  "update ".DATATABLE."_order_deduct set DeductStatus='T', DeductToDate=".time()." where DeductID IN ( ".$deleteidmsg." ) and CompanyID=".$_SESSION['uinfo']['ucompany']." and DeductStatus='F' ";	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=do_more_validate&ID=".$in['ID']."','批量发放提成(".$deleteidmsg.")','-',".time().")";
		$db->query($sqlex);
		Error::Alert('操作成功!',$in['referer']);
	}else{
		Error::AlertJs('操作不成功!');
	}
}

//获取客情官相关客户
if($in['m'] == 'loadclientselect')
{
	$optionmsg = '<option value="0">⊙ 请选择客户（药店）</option>';
	$cinfo = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi FROM ".DATATABLE."_order_client c inner join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".intval($in['ID'])."");
	if(!empty($cinfo))
	{
		foreach($cinfo as $cv)
		{
			$optionmsg .=  '<option value="'.$cv['ClientID'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$cv['ClientCompanyPinyi']).'" >'.substr($cv['ClientCompanyPinyi'],0,1).' - '.$cv['ClientCompanyName'].'</option>';
		}
	}
	echo $optionmsg;
}

//保存提成
if($in['m'] == 'deduct_add_save')
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	$in['data_DeductTotal'] = floatval($in['data_DeductTotal']);

	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('CompanyID', $_SESSION['uinfo']['ucompany']);
	$data_->addData('DeductDate', time());
	$data_->addData('DeductType', 'R');
	$insert_id = $data_->dataInsert ("_order_deduct");

	if(!empty($insert_id)){
		exit('ok');
	}else{
		exit('保存不成功!');
	}
}

if($in['m'] == 'deduct_delete')
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "delete from ".DATATABLE."_order_deduct where DeductID = ".intval($in['ID'])." and DeductStatus='F' and CompanyID=".$_SESSION['uinfo']['ucompany']." and DeductType = 'R' limit 1";	

	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_saler.php?m=deduct_delete&ID=".$in['ID']."','删除业务提成(".$in['ID'].")','-',".time().")";
		$db->query($sqlex);
		exit('ok');
	}else{
		exit('删除不成功!');
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