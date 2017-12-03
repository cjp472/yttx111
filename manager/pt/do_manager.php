<?php
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");

function get_buy_conf($db , $type = 'product') {
    $result = array();
    $list = $db->get_results("SELECT * FROM ".DATABASEU.DATATABLE."_buy_conf WHERE type='{$type}'");
    foreach($list ? $list : array() as $key => $val) {
        $val['data'] = json_decode($val['data'],true);
        $result[$key] = $val;
    }
    unset($list);
    return $result;
}

if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="delete")
{
	if(!intval($in['ID'])) exit('非法操作!');

	$upsql =  "update ".DATABASEU.DATATABLE."_order_company set CompanyFlag='1' where CompanyID = ".$in['ID']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

//*****recycle************/
if($in['m']=="restore")
{
	if(!intval($in['ID'])) exit('非法参数!');

	$upsql =  "update ".DATABASEU.DATATABLE."_order_company set CompanyFlag='0' where CompanyID = ".$in['ID']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('还原不成功!');
	}
}

if($in['m']=="quite_delete")
{
	if(!intval($in['ID'])) exit('非法参数!');

	$upsql =  "delete from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$in['ID']."";	
	if($db->query($upsql))
	{
		$upsql_cs =  "delete from ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$in['ID']."";
		$db->query($upsql_cs);
		$upsql_user =  "delete from ".DATABASEU.DATATABLE."_order_user where UserCompany = ".$in['ID']."";
		$db->query($upsql_user);
		$upsql_client =  "delete from ".DATATABLE."_order_client where ClientCompany = ".$in['ID']."";
		$db->query($upsql_client);
		$upsql_i =  "delete from ".DATATABLE."_order_content_index where CompanyID = ".$in['ID']."";
		$db->query($upsql_i);
		$upsql_1 =  "delete from ".DATATABLE."_order_content_1 where CompanyID = ".$in['ID']."";
		$db->query($upsql_1);
		$upsql_cart =  "delete from ".DATATABLE."_order_cart where CompanyID = ".$in['ID']."";
		$db->query($upsql_cart);
		$upsql_acc =  "delete from ".DATATABLE."_order_accounts where AccountsCompany = ".$in['ID']."";
		$db->query($upsql_acc);
		$upsql_area =  "delete from ".DATATABLE."_order_area where AreaCompany = ".$in['ID']."";
		$db->query($upsql_area);
		$upsql_consignment =  "delete from ".DATATABLE."_order_consignment where ConsignmentCompany = ".$in['ID']."";
		$db->query($upsql_consignment);
		$upsql_finance =  "delete from ".DATATABLE."_order_finance where ConsignmentCompany = ".$in['ID']."";
		$db->query($upsql_finance);

		$upsql_logistics =  "delete from ".DATATABLE."_order_logistics where LogisticsCompany = ".$in['ID']."";
		$db->query($upsql_logistics);		
		$upsql_orderinfo =  "delete from ".DATATABLE."_order_orderinfo where OrderCompany = ".$in['ID']."";
		$db->query($upsql_orderinfo);
		$upsql_resource =  "delete from ".DATATABLE."_order_resource where CompanyID = ".$in['ID']."";
		$db->query($upsql_resource);
		$upsql_site =  "delete from ".DATATABLE."_order_site where CompanyID = ".$in['ID']."";
		$db->query($upsql_site);

		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


/*********** save company **************/
if($in['m']=="content_add_company_save")
{
	$in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where CompanyName='".$in['data_CompanyName']."' limit 0,1");
	if(!empty($clientinfo['orwname'])) exit('repeat');

	$Prefixinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".$in['data_CompanyPrefix']."' limit 0,1");
	if(!empty($Prefixinfo['orwname'])) exit('repeatprefix');
	
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_company(CompanyArea,CompanyIndustry,CompanyAgent,CompanyName,CompanySigned,CompanyPrefix,CompanyCity,CompanyContact,CompanyMobile,CompanyPhone,CompanyFax,CompanyAddress,CompanyEmail,CompanyWeb,CompanyUrl,CompanyRemark,CompanyDate,CompanyFlag,BusinessLicense,IdentificationNumber) values(".$in['data_CompanyArea'].",".$in['data_CompanyIndustry'].",".intval($in['data_CompanyAgent']).",'".$in['data_CompanyName']."','".$in['data_CompanySigned']."','".$in['data_CompanyPrefix']."','".$in['data_CompanyCity']."','".$in['data_CompanyContact']."','".$in['data_CompanyMobile']."','".$in['data_CompanyPhone']."','".$in['data_CompanyFax']."','".$in['data_CompanyAddress']."','".$in['data_CompanyEmail']."','".$in['data_CompanyWeb']."','".$in['data_CompanyUrl']."','".$in['data_CompanyRemark']."',".time().",'0','".$in['data_BusinessLicense']."','".$in['data_IdentificationNumber']."')";
	$update  = $db->query($upsql);	
	if($update)
	{
		$insert_id = mysql_insert_id();
		if(empty($in['CS_UpDate'])) $in['CS_UpDate'] = date("Y-m-d");
		$insql = "insert into ".DATABASEU.DATATABLE."_order_cs(CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_UpDate,CS_UpdateTime,CS_SmsNumber) values(".$insert_id.",".$in['CS_Number'].",'".$in['CS_BeginDate']."','".$in['CS_EndDate']."','".$in['CS_UpDate']."',".time().",".$in['CS_SmsNumber'].")";
		$db->query($insql);
		
		if($insert_id > 100)
		{
			//$databaseid = floor($insert_id / 500) + 5;
            $database = floor($company_id / 2000) + 10;
			$db->query("update ".DATABASEU.DATATABLE."_order_company set CompanyDatabase=".$databaseid." where CompanyID=".$insert_id." limit 1");
			$sdatabse = DB_DATABASE."_".$databaseid.".";
		}else{
			$sdatabse = DB_DATABASE.".";
		}

		if(!(file_exists (RESOURCE_PATH.$insert_id)))
		{
			_mkdir(RESOURCE_PATH,$insert_id);
		}

		InputDefaultValue($db,$insert_id,$in['data_CompanyMobile'],$sdatabse);

		$status = SaveCompanyLog($insert_id,'入驻医统天下('.$in['CS_Number'].'用户)');
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

/*********** edit company **************/
if($in['m']=="content_edit_company_save")
{       
        //是否开通医统账期
        $CreditCheck = $in['CreditCheck'];
	$in['ID']     = intval($in['ID']);
        
	$in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));	
	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where BusinessLicense='".$in['data_BusinessLicense']."' AND CompanyID<>".$in["ID"]." limit 0,1");
	if($clientinfo['orwname'] > 0) exit('repeat');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".$in['data_CompanyPrefix']."' AND CompanyID<>".$in["ID"]." limit 0,1");
	if($clientinfo['orwname'] > 0) exit('repeatPrefix');

// 	$Prefixinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".$in['data_CompanyPrefix']."' limit 0,1");
// 	if(!empty($Prefixinfo['orwname']) && $Prefixinfo['orwname'] > 1) exit('repeatprefix');
	$infoc = $db->get_row("SELECT CompanyName FROM ".DATABASEU.DATATABLE."_order_company where CompanyID='".$in['ID']."' limit 0,1");

	$upsql = "update ".DATABASEU.DATATABLE."_order_company set CompanyArea ='".$in['data_CompanyArea']."', CompanyIndustry ='".$in['data_CompanyIndustry']."',CompanyAgent=".intval($in['data_CompanyAgent']).", CompanyName='".$in['data_CompanyName']."',CompanySigned='".$in['data_CompanySigned']."', CompanyPrefix='".$in['data_CompanyPrefix']."', CompanyCity='".$in['data_CompanyCity']."', CompanyContact='".$in['data_CompanyContact']."', CompanyMobile='".$in['data_CompanyMobile']."', CompanyPhone='".$in['data_CompanyPhone']."', CompanyFax='".$in['data_CompanyFax']."', CompanyAddress='".$in['data_CompanyAddress']."', CompanyEmail='".$in['data_CompanyEmail']."', CompanyWeb='".$in['data_CompanyWeb']."', CompanyUrl='".$in['data_CompanyUrl']."', CompanyRemark='".$in['data_CompanyRemark']."', BusinessLicense='".$in['data_BusinessLicense']."', IdentificationNumber='".$in['data_IdentificationNumber']."',CompanyCredit=".$CreditCheck." where CompanyID=".$in['ID'];
	$update  = $db->query($upsql);
	
	$info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company='".$in['ID']."' limit 0,1");
	
	$insql = "update ".DATABASEU.DATATABLE."_order_cs set CS_Number=".intval($in['CS_Number']).", CS_BeginDate='".$in['CS_BeginDate']."', CS_EndDate='".$in['CS_EndDate']."',CS_UpDate='".$in['CS_UpDate']."',CS_UpdateTime=".time().",CS_SmsNumber=".intval($in['CS_SmsNumber'])." where CS_Company=".$in['ID'];
	$isup  = $db->query($insql);	
	if($update || $isup)
	{	
            
		$content = '';

		if($infoc['CompanyName'] != $in['data_CompanyName']) $content .= '修改了公司名称从 ‘'.$infoc['CompanyName'].'’到‘'.$in['data_CompanyName'].'’.\n\r';
		if($info['CS_Number'] != $in['CS_Number']) $content .= '修改了用户数从 '.$info['CS_Number'].'用户到'.$in['CS_Number'].'用户.\n\r';
		if($info['CS_EndDate'] != $in['CS_EndDate']) $content .= '修改了到期时间从 '.$info['CS_EndDate'].'到'.$in['CS_EndDate'].'.\n\r';
		if($info['CS_SmsNumber'] != $in['CS_SmsNumber']) $content .= '修改了短信数量从 '.$info['CS_SmsNumber'].'条到'.$in['CS_SmsNumber'].'条.\n\r';
		if(!empty($content)) $status = SaveCompanyLog($in['ID'],$content);
		exit("ok");
                
	}else{
		exit("资料无变化!");
	}
}

/***********save company user**************/
if($in['m']=="company_user_save")
{
	if(empty($in['CompanyID'])) exit('error');

	$in['UserName']  = strtolower($in['UserName']);
	$in['UserPass']    = strtolower($in['UserPass']);
	
	if(!is_filename($in['UserName'])) exit('okname');
	if(strlen($in['UserName']) < 3 || strlen($in['UserName']) > 18 ) exit('okname');
	
	if(empty($in['UserID']))
	{
		if(!is_filename($in['UserPass'])) exit('okpass');
		if(strlen($in['UserPass']) < 3 || strlen($in['UserPass']) > 18 ) exit('okpass');
		$passmsg = ChangeMsg($in['UserName'],$in['UserPass']);
		
		$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserName='".$in['UserName']."'");
		if(!empty($clientinfo['orwname'])) exit('repeat');
	
		$upsql = "insert into ".DATABASEU.DATATABLE."_order_user(UserName,UserPass,UserCompany,UserTrueName,UserPhone,UserDate,UserRemark,UserFlag) values('".$in['UserName']."','".$passmsg."',".$in['CompanyID'].",'".$in['UserTrueName']."','".$in['UserPhone']."', ".time().",'".$in['UserRemark']."','9')";	
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}else{

		$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserName='".$in['UserName']."'");
		if($clientinfo['orwname'] > 1) exit('repeat');

		if(empty($in['UserPass']))
		{
			$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."', UserTrueName='".$in['UserTrueName']."', UserPhone='".$in['UserPhone']."', UserRemark='".$in['UserRemark']."' where UserID=".$in['UserID']." and UserCompany=".$in['CompanyID']."";	
		}else{

			if(!is_filename($in['UserPass'])) exit('UserPass');
			if(strlen($in['UserPass']) < 3 || strlen($in['UserPass']) > 18 ) exit('okpass');
		    $passmsg = ChangeMsg($in['UserName'],$in['UserPass']);

			$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."',UserPass='".$passmsg."', UserTrueName='".$in['UserTrueName']."', UserPhone='".$in['UserPhone']."', UserRemark='".$in['UserRemark']."' where UserID=".$in['UserID']." and UserCompany=".$in['CompanyID']."";
		}

		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("资料无变化!");
		}
	}
}



/*********** Industry **************/
if($in['m']=="content_add_industry_save")
{
	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_industry where IndustryName='".$in['IndustryName']."' limit 0,1");
	if(!empty($clientinfo['orwname'])) exit('repeat');

	if(!empty($in['IndustryName']))
	{
		$upsql = "insert into ".DATABASEU.DATATABLE."_order_industry(IndustryName,IndustryAbout) values('".$in['IndustryName']."','".$in['IndustryAbout']."')";
		
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("没有任何变动!");
		}
	}else{
		exit('参数错误!');
	}
}


if($in['m']=="content_edit_industry_save")
{

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_industry where IndustryName='".$in['IndustryName']."' limit 0,1");
	if($clientinfo['orwname'] > 1) exit('repeat');

	if(!empty($in['IndustryID']))
	{
		$upsql = "update ".DATABASEU.DATATABLE."_order_industry set IndustryName='".$in['IndustryName']."', IndustryAbout='".$in['IndustryAbout']."' where IndustryID=".$in['IndustryID']."";
		
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("没有任何变动!");
		}
	}else{
		exit('参数错误!');
	}
}


if($in['m']=="delete_industry")
{
	if(!empty($in['ID']))
	{
		
		$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATABASEU.DATATABLE."_order_company where CompanyIndustry=".intval($in['ID'])." ");
		if(!empty($cinfo['lrow'])) exit('该行业已在使用，不能删除!');

		$upsql = "delete from ".DATABASEU.DATATABLE."_order_industry  where IndustryID=".intval($in['ID'])."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}


/***********save_sort**************/
if($in['m']=="save_sort")
{
	if(!empty($in['AreaName']))
	{
		$upsql = "insert into ".DATABASEU.DATATABLE."_order_city(AreaParent,AreaName,AreaAbout) values(".$in['AreaParent'].", '".$in['AreaName']."', '".$in['AreaAbout']."')";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

if($in['m']=="save_edit_sort")
{
	if(!empty($in['AreaID']))
	{
		if(empty($in['AreaName'])) exit('地区名称不能为空!');
		$upsql = "update ".DATABASEU.DATATABLE."_order_city set AreaParent=".$in['AreaParent'].", AreaName='".$in['AreaName']."',AreaAbout='".$in['AreaAbout']."' where AreaID=".$in['AreaID']."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("无变化，没修改任何内容!");
		}
	}
}


if($in['m']=="delete_sort")
{
	if(!empty($in['AreaID']))
	{
		$sinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATABASEU.DATATABLE."_order_city where AreaParent=".intval($in['AreaID'])." ");
		if(!empty($sinfo['lrow'])) exit('请先删除下级地区!');

		$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATABASEU.DATATABLE."_order_company where CompanyArea=".intval($in['AreaID'])." ");
		if(!empty($cinfo['lrow'])) exit('该地区已在使用，不能删除!');

		$upsql = "delete from ".DATABASEU.DATATABLE."_order_city where AreaID=".$in['AreaID']." limit 1";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}


if($in['m']=="set_save_confirm_finance")
{
	$payinfo = $db->get_row("SELECT PayID,PayCompany,PayType FROM ".DATABASEU.DATATABLE."_order_pay where PayID=".intval($in['pid'])." and PayFlag=0 limit 0,1");
	
	if(!empty($payinfo))
	{
		$InfoData = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$payinfo['PayCompany']." limit 0,1");
		$infodatamsg = serialize($InfoData);
		$sqlex = "insert into ".DATABASEU.DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(1, '".$_SESSION['uinfo']['username']."', 'do_manager.php?m=set_save_confirm_finance&pid=".$in['pid']."','确认收款(".$in['pid'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);

		if($payinfo['PayType']=="sms")
		{
			$jianumber = abs(intval($in['smsnumber']));
			if($jianumber > 0)
			{
				$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber+".$jianumber." where CS_Company=".$payinfo['PayCompany']." limit 1");
				$db->query("update ".DATABASEU.DATATABLE."_order_pay set PayFlag=1,PayStatus='TRADE_FINISHED' where PayID=".$payinfo['PayID']." limit 1");
				$status = SaveCompanyLog($payinfo['PayCompany'],'购买短信：'.$jianumber.'条(原有：'.$InfoData['CS_SmsNumber'].'条)');
			}
			exit('');
		}
		elseif($payinfo['PayType']=="system")
		{
			$clientnumber = abs(intval($in['clientnumber']));
			if($clientnumber > 10)
			{
				$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_Number=".$clientnumber." where CS_Company=".$payinfo['PayCompany']." and CS_Number < ".$clientnumber." limit 1");
				$status = SaveCompanyLog($payinfo['PayCompany'],'购买用户数：从'.$InfoData['CS_Number'].'用户到'.$clientnumber.'用户');	
			}
			if(!empty($in['up_to_EndDate']))
			{
				$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_EndDate='".$in['up_to_EndDate']."' where CS_Company=".$payinfo['PayCompany']." and CS_EndDate < '".$in['up_to_EndDate']."' limit 1");
				$status = SaveCompanyLog($payinfo['PayCompany'],'续费：从'.$InfoData['CS_EndDate'].'到'.$in['up_to_EndDate'].'');
			}
			if(!empty($in['smsnumber']))
			{
				$jianumber = abs(intval($in['smsnumber']));
				$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber+".$jianumber." where CS_Company=".$payinfo['PayCompany']." limit 1");
				$status = SaveCompanyLog($payinfo['PayCompany'],'购买短信：'.$jianumber.'条(原有：'.$InfoData['CS_SmsNumber'].'条)');
			}

			$db->query("update ".DATABASEU.DATATABLE."_order_pay set PayFlag=1,PayStatus='TRADE_FINISHED' where PayID=".$payinfo['PayID']." limit 1");
			exit('');
		}	
		
	}
	exit('确认不成功,数据不存在!');
}


if($in['m']=="delete_finance_log")
{
	if(!empty($in['ID']))
	{
		$upsql = "delete from ".DATABASEU.DATATABLE."_order_pay where PayID=".intval($in['ID'])." limit 1";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}

/***********save_money**************/
if($in['m']=="content_money_save")
{
	if(!empty($in['data_PayCompany']))
	{
		if($in['data_PayOrder']=="sms")
		{
			$in['aliorder'] = "订货宝 短信充值";
		}else{
			$in['aliorder'] = "订货宝 系统续费";
		}
		$isin = "insert into ".DATABASEU.DATATABLE."_order_pay(PayCompany,PayUser,PaySN,PayOrder,PayBody,PayMoney,PayType,PayMethod,PayDate,PayStatus) values( ".$in['data_PayCompany'].",".$_SESSION['uinfo']['userid'].",'-','".$in['aliorder']."','".$in['data_PayBody']."','".$in['data_PayMoney']."','".$in['data_PayOrder']."','underling',".time().",'')";
		if($db->query($isin))
		{
			$status = SaveCompanyLog($in['data_PayCompany'],$in['aliorder'].' ￥'.$in['data_PayMoney'].'元');
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

if($in['m']=="delete_reg")
{
	if(!intval($in['ID'])) exit('非法操作!');

	$upsql =  "delete from ".DATABASEU.DATATABLE."_order_company_reg where CompanyID = ".$in['ID']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

//开通邮件
if($in['m']=="sendto_email")
{
	if(empty($in['ID'])) exit('参数错误！');
	if(empty($in['us'])) exit('请填写管理员帐号！');
	if(empty($in['pw'])) exit('请填写管理员密码！');

	$in['us'] = strtolower(trim($in['us']));
	$in['pw'] = strtolower(trim($in['pw']));

	$rinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");
	if(empty($rinfo)) exit('您发送的数据不存在');

	$repeatinfo = $db->get_row("select count(*) as allrow from ".DATABASEU.DATATABLE."_order_user where UserCompany <> ".$rinfo['CompanyID']." and  UserName='".$in['us']."' limit 0,1");
	if($repeatinfo['allrow'] > 0) exit('此帐号名称重复了，请换名再试！');

	$uinfo = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$rinfo['CompanyID']." and UserFlag='9' order by UserID asc limit 0,1");
	$passmsg = ChangeMsg($in['us'],$in['pw']);
	if(!empty($uinfo['UserName'])){
		$db->query("update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['us']."', UserPass='".$passmsg."' where UserCompany=".$rinfo['CompanyID']." and UserID=".$uinfo['UserID']." limit 1");
	}else{
		$upsql = "insert into ".DATABASEU.DATATABLE."_order_user(UserName,UserPass,UserCompany,UserTrueName,UserPhone,UserDate,UserRemark,UserFlag) values('".$in['us']."','".$passmsg."',".$rinfo['CompanyID'].",'".$rinfo['CompanyContact']."','', ".time().",'','9')";
		$db->query($upsql);
	}

	$content = '<style type="text/css">
body{ background-color:#f3f3f3; margin:0px; padding:20px; }
a,dt, dl, span, ul, li, div, h1, h2, h3{font-size:12px; font-family:"微软雅黑",Arial, Helvetica, sans-serif; color:#5b5b5b;font-size:12px; margin:0px;}
a{ cursor:pointer;}
a:link,a:visited{
 text-decoration:none;
}
/*------顶部样式------*/
.header{ width:650px; height:80px; background-color:#fff; margin:0 auto; margin-top:25px;}
.logo{ float:left; margin-top:20px; margin-left:20px;}
.menu{ width:230px; float:right; height:14px; margin-top:40px;}
.menu dt{ float:left; color:#a38643; margin-left:20px; line-height:14px;}
.menu dt a{ float:left; color:#a38643;}
.banner{ width:650px; height:137px;margin:0 auto;}

/*------试用账号样式------*/
.dear{ width:610px; margin:0 auto; background-color:#FFF; height:90px; padding:20px; line-height:20px;}
.gongxi{ width:250px; margin:0 auto; font-size:14px; font-weight:bold; color:#6a6a6a; text-align:center; line-height:30px; height:50px}
.gongxi span{ width:30px; height:30px; display:block; float:left}
.dear span{ color:#a38643; font-size:14px; font-weight:bold}
.kehu{ width:600px; margin:0 auto; margin-top:20px; height:40px;}

.user_info{ width:650px; height:165px; margin:0 auto; background:#fff;}
.user_info_th1{border-top:#ececec 1px solid; border-bottom:#cbab62 2px solid; color:#a38643}
.user_info_th2{ color:#a38643; text-align:left; padding-left:42px;}
.user_info_td1{ border-bottom:#ececec 1px solid;}
.user_info table tr{ height:40px;}
.user_info table td{ text-align:center;}

/*------微信客户端样式------*/
.wechat{ width:650px; background-color:#fff; margin:0 auto; margin-top:0px; height:162px;}
.wechat h1{ line-height:40px; font-size:14px; color:#0098aa; padding-left:40px;}
.wechat_info{ width:600px; height:120px; border:1px solid #ececec; margin:0 auto}
.wechat_info_left{ float:left; overflow:hidden; width:180px; height:120px;}
.wechat_info_left dt{ width:157px; border-bottom:#ececec 1px solid; height:40px; display:block; border-right:#ececec 1px solid; line-height:40px; padding-left:20px;}
.ewm_text{ width:157px; height:40px; line-height:40px; padding-left:20px;}
.wechat_info_right{ width:112px; height:112px; float:right; padding:4px;}
.wechat_info_l{ width:112px; height:112px; float:left; padding:4px;}

/*------底部样式------*/
.footer{ width:650px; background-color:#fff; margin:0 auto;}
.company{ border-bottom:1px solid #ececec; line-height:24px; font-size:12px; color:#afafaf; padding-left:20px; width:630px; padding-top:20px;padding-bottom:20px;}
.company span{ font-size:14px; font-weight:bold; color:#a38643; margin-left:5px;}
.company b{ font-size:14px;}
.footer_info_left{ width:400px; float:left; overflow:hidden; padding:20px;}
.footer_info_right{ width:81px; height:89px; float:right; overflow:hidden; padding:20px; margin-top:10px; }
</style>
<div style="background-color:#f5f5f5; width:100%; height:auto;">
<!--顶部开始-->
<div class="header">
	<div class="logo"><a href="http://www.dhb.hk" target="_blank"><img src="http://www.dhb.hk/email/images/logo1.jpg?v=dhb" width="215" height="38" /></a></div>
    <div class="menu">
    	<dt><a href="http://www.dhb.hk/site/TypicalFeatures/" target="_blank" style="text-decoration:none;">产品介绍</a></dt>
        <dt><a href="http://www.dhb.hk/site/PriceSolutions/" target="_blank" style="text-decoration:none;">价格方案</a></dt>
        <dt><a href="http://help.dhb.net.cn/manager.php" target="_blank" style="text-decoration:none;">操作指南</a></dt>
    </div>
</div>
<!--顶部结束-->
<!--banner开始-->
<div class="banner" >
	<img src="http://www.dhb.hk/email/images/banner1.jpg?v=dhb" width="650" height="137" />
</div>
<!--banner结束-->
<!--试用账号开始-->
<div class="dear">
	<div class="gongxi"><span><img src="http://www.dhb.hk/email/images/suc.png" width="30" height="30" /></span>您的订货宝正式账号开通成功！</div>
   <b>亲爱的用户：</b><span>'.$rinfo['CompanyName'].' - '.$rinfo['CompanyContact'].'</span><br/> 
您好，《订货宝》网上订货系统正式帐号是： 
</div>

<div class="user_info">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="24%" class="user_info_th1 user_info_th2" scope="col">端口</th>
    <th width="23%" class="user_info_th1" scope="col">登陆地址</th>
    <th width="28%" class="user_info_th1" scope="col">账号</th>
    <th width="25%" class="user_info_th1" scope="col">密码</th>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">客户订货端</th>
    <td class="user_info_td1"><a href="http://'.$rinfo['CompanyPrefix'].'.dhb.hk" target="_blank" style="text-decoration:none;" >http://'.$rinfo['CompanyPrefix'].'.dhb.hk</a></td>
    <td class="user_info_td1">在管理端设置经销商帐号 </td>
    <td class="user_info_td1">在管理端设置经销商密码 </td>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">手机订货端</th>
    <td class="user_info_td1"><a href="http://sj.dhb.hk" target="_blank" style="text-decoration:none;" >http://sj.dhb.hk</a></td>
    <td class="user_info_td1">在管理端设置经销商帐号 </td>
    <td class="user_info_td1">在管理端设置经销商密码</td>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">管理端</th>
    <td class="user_info_td1"><a href="http://m.dhb.hk" target="_blank" style="text-decoration:none;" >http://m.dhb.hk</a></td>
    <td class="user_info_td1">'.$in['us'].' </td>
    <td class="user_info_td1">'.$in['pw'].' </td>
  </tr>
</table>
</div>
<!--试用账号结束-->
<!--微信客户端开始-->
<div class="wechat">
<h1>订货宝手机移动端</h1>
<div class="wechat_info">
	<div class="wechat_info_left">
    	<dt>手机APP下载 </dt>
		<dt>Android, IOS </dt>
      <div class="ewm_text">扫描右侧二维码下载</div>
    </div>
	<div class="wechat_info_l">
    <img src="http://www.dhb.hk/email/images/app.jpg" width="111" height="111" /> </div>

	<div class="wechat_info_left">
    	<dt>微信公众号：订货宝手机客户端 </dt>
        <dt>微信号：vdhb_hk</dt>
      <div class="ewm_text">扫描右侧二维码关注</div>
    </div>
	<div class="wechat_info_right">
    <img src="http://www.dhb.hk/email/images/ewm2.jpg" width="111" height="111" /> </div>
</div>
</div>
<!--微信客户端结束-->
<!--底部开始-->
<div class="footer">
	<div class="company" ><b>订货宝客服中心为您提供专业细致的服务支持</b><br  />
若有需要，请随时致电我们的客服中心 <span>400-6311-682</span> <br />
售后技术支持QQ：<span>1656591743</span> , <span>1058595421</span></div>
    <div class="footer_info">
   	  <div class="footer_info_left">
        	服务热线：400-6311-682   028-84191729 
            <br/>传 真：028-84191728
             <br/>官网：<a href="http://www.dhb.hk" target="_blank" style="text-decoration:none;">www.dhb.hk</a> 
            <br/> <br/>软件企业编号：川R-2010-0006 | 软件著作权登记号：2011SR027284 
		    <br/>阿商信息技术有限公司 版权所有 © 2005-2014 
      </div>
        <div class="footer_info_right">
   	    <img src="http://www.dhb.hk/email/images/ewm2_03.jpg" width="81" height="89" /> </div>
        <div style="clear:both"></div>
    </div>
</div>
<!--底部结束-->
</div>
		';

		if(empty($rinfo['CompanyEmail'])) exit('没有填写邮箱！'); else $tomail = $rinfo['CompanyEmail'];
		$rmsg = send_mail($tomail,$rinfo['CompanyName'],"订货宝 网上订货系统 正试帐号 ",$content);

		//短信通知
		$tomobile = '';
		if(!empty($rinfo['CompanyMobile']) && is_phone($rinfo['CompanyMobile'])){
			$message = "【订货宝】已为您开通正式帐号,信息如下,管理端网址:m.dhb.hk,帐号:".$in['us'].",密码:".$in['pw'].",订货端网址：".$rinfo['CompanyPrefix'].".dhb.hk,微信订货端帐号：订货宝手机微信端(dhb_hk).订货宝服务中心会为您提供专业细致的服务支持,若有需要请致电客服中心4006311682.";
			$mobilearr[]    = $rinfo['CompanyMobile'];
			$statusCode2    = $client->login();
			$statusCode     = $client->sendSMS($mobilearr,$message);
			$tomobile = "、手机：".$rinfo['CompanyMobile'];
		}

		if($rmsg=="ok")
		{
			$status = SaveCompanyLog($in['ID'],'发送开通帐号通知 ：'.$tomail."".$tomobile);
			echo '开通邮件已发送至：'.$tomail."".$tomobile; 
		}else{
			echo $rmsg;
		}
		exit;
}


//重置帐号邮件
if($in['m']=="resetpass_email")
{
	if(empty($in['ID'])) exit('参数错误！');
	if(empty($in['pw'])) exit('请填写管理员密码！');

	$rinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");
	if(empty($rinfo)) exit('您发送的数据不存在');

	$uinfo = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$rinfo['CompanyID']." and UserFlag='9' order by UserID asc limit 0,1");
	$pwmsg =  ChangeMsg($uinfo['UserName'],$in['pw']);
	$status = $db->query("update ".DATABASEU.DATATABLE."_order_user set UserPass='".$pwmsg."' where UserID=".$uinfo['UserID']." and UserCompany=".$rinfo['CompanyID']." limit 1");
	if(!$status) exit('重置失败！');

	$content = '<style type="text/css">
body{ background-color:#f3f3f3; margin:0px; padding:20px; }
a,dt, dl, span, ul, li, div, h1, h2, h3{font-size:12px; font-family:"微软雅黑",Arial, Helvetica, sans-serif; color:#5b5b5b;font-size:12px; margin:0px; text-decoration:none;}
a{ cursor:pointer;}
a:link,a:visited{
 text-decoration:none;
}
a:hover{
 text-decoration:none; color:#000;
}
/*------顶部样式------*/
.header{ width:650px; height:80px; background-color:#fff; margin:0 auto; margin-top:25px;}
.logo{ float:left; margin-top:20px; margin-left:20px;}
.menu{ width:230px; float:right; height:14px; margin-top:40px;}
.menu dt{ float:left; color:#1b9bdd; margin-left:20px; line-height:14px;}
.menu dt a{text-decoration:none; color:#198700;}
.banner{ width:650px; height:137px;margin:0 auto;}

/*------试用账号样式------*/
.dear{ width:610px; margin:0 auto; background-color:#FFF; height:90px; padding:20px; line-height:20px;}
.gongxi{ width:250px; margin:0 auto; font-size:14px; font-weight:bold; color:#6a6a6a; text-align:center; line-height:30px; height:50px}
.gongxi span{ width:30px; height:30px; display:block; float:left}
.dear span{ color:#198700; font-size:14px; font-weight:bold}

.user_info{ width:650px; height:165px; margin:0 auto; background:#fff;}
.user_info_th1{border-top:#ececec 1px solid; border-bottom:#64bd4f 2px solid; color:#198700}
.user_info_th2{ color:#198700; text-align:left; padding-left:42px;}
.user_info_td1{ border-bottom:#ececec 1px solid;}
.user_info table tr{ height:40px;}
.user_info table td{ text-align:center;}

/*------微信客户端样式------*/
.wechat{ width:650px; background-color:#fff; margin:0 auto; margin-top:0px; height:162px;}
.wechat h1{ line-height:40px; font-size:14px; color:#0098aa; padding-left:40px;}
.wechat_info{ width:600px; height:120px; border:1px solid #ececec; margin:0 auto}
.wechat_info_left{ float:left; overflow:hidden; width:180px; height:120px;}
.wechat_info_left dt{ width:157px; border-bottom:#ececec 1px solid; height:40px; display:block; border-right:#ececec 1px solid; line-height:40px; padding-left:20px;}
.ewm_text{ width:157px; height:40px; line-height:40px; padding-left:20px;}
.wechat_info_right{ width:112px; height:112px; float:right; padding:4px;}
.wechat_info_l{ width:112px; height:112px; float:left; padding:4px;}

/*------底部样式------*/
.footer{ width:650px; background-color:#fff; margin:0 auto;}
.company{ border-bottom:1px solid #ececec; line-height:24px; font-size:12px; color:#afafaf; padding-left:20px; width:630px; padding-top:20px;padding-bottom:20px;}
.company span{ font-size:14px; font-weight:bold; color:#ea8151; margin-left:5px;}
.company b{ font-size:14px;}
.footer_info_left{ width:400px; float:left; overflow:hidden; padding:20px;}
.footer_info_right{ width:81px; height:89px; float:right; overflow:hidden; padding:20px; margin-top:10px; }
</style>
<div style="background-color:#f5f5f5; width:100%; height:auto;">
<!--顶部开始-->
<div class="header">
	<div class="logo"><a href="http://www.dhb.hk" target="_blank"><img src="http://www.dhb.hk/email/images/logo2.jpg?v=dhb" width="215" height="38" /></a></div>
    <div class="menu">
    	<dt><a href="http://www.dhb.hk/site/TypicalFeatures/" target="_blank" style="text-decoration:none;">产品介绍</a></dt>
        <dt><a href="http://www.dhb.hk/site/PriceSolutions/" target="_blank" style="text-decoration:none;">价格方案</a></dt>
        <dt><a href="http://help.dhb.net.cn/manager.php" target="_blank" style="text-decoration:none;">操作指南</a></dt>
    </div>
</div>
<!--顶部结束-->
<!--banner开始-->
<div class="banner" >
	<img src="http://www.dhb.hk/email/images/banner2.jpg?v=dhb" width="650" height="137" />
</div>
<!--banner结束-->
<!--试用账号开始-->
<div class="dear">
	<div class="gongxi"><span><img src="http://www.dhb.hk/email/images/suc.png" width="30" height="30" /></span>您的订货宝正式账号重置成功！</div>
   <b>亲爱的用户：</b><span>'.$rinfo['CompanyName'].' - '.$rinfo['CompanyContact'].'</span><br/> 
您好，《订货宝》网上订货系统正式帐号是： 
</div>

<div class="user_info">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="24%" class="user_info_th1 user_info_th2" scope="col">端口</th>
    <th width="23%" class="user_info_th1" scope="col">登陆地址</th>
    <th width="28%" class="user_info_th1" scope="col">账号</th>
    <th width="25%" class="user_info_th1" scope="col">密码</th>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">客户订货端</th>
    <td class="user_info_td1"><a href="http://'.$rinfo['CompanyPrefix'].'.dhb.hk" target="_blank" style="text-decoration:none;" >http://'.$rinfo['CompanyPrefix'].'.dhb.hk</a></td>
    <td class="user_info_td1">在管理端设置经销商帐号 </td>
    <td class="user_info_td1">在管理端设置经销商密码 </td>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">手机订货端</th>
    <td class="user_info_td1"><a href="http://sj.dhb.hk" target="_blank" style="text-decoration:none;" >http://sj.dhb.hk</a></td>
    <td class="user_info_td1">在管理端设置经销商帐号 </td>
    <td class="user_info_td1">在管理端设置经销商密码</td>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">管理端</th>
    <td class="user_info_td1"><a href="http://m.dhb.hk" target="_blank" style="text-decoration:none;" >http://m.dhb.hk</a></td>
    <td class="user_info_td1">'.$uinfo['UserName'].' </td>
    <td class="user_info_td1">'.$in['pw'].' </td>
  </tr>
</table>
</div>
<!--试用账号结束-->
<!--微信客户端开始-->
<div class="wechat">
<h1>订货宝手机移动端</h1>
<div class="wechat_info">
	<div class="wechat_info_left">
    	<dt>手机APP下载 </dt>
		<dt>Android, IOS </dt>
      <div class="ewm_text">扫描右侧二维码下载</div>
    </div>
	<div class="wechat_info_l">
    <img src="http://www.dhb.hk/email/images/app.jpg" width="111" height="111" /> </div>

	<div class="wechat_info_left">
    	<dt>微信公众号：订货宝手机客户端 </dt>
        <dt>微信号：vdhb_hk</dt>
      <div class="ewm_text">扫描右侧二维码关注</div>
    </div>
	<div class="wechat_info_right">
    <img src="http://www.dhb.hk/email/images/ewm2.jpg" width="111" height="111" /> </div>
</div>
</div>
<!--微信客户端结束-->
<!--底部开始-->
<div class="footer">
	<div class="company" ><b>订货宝客服中心为您提供专业细致的服务支持</b><br  />
若有需要，请随时致电我们的客服中心 <span>400-6311-682</span> <br />
售后技术支持QQ：<span>1656591743</span> , <span>1058595421</span></div>
    <div class="footer_info">
   	  <div class="footer_info_left">
        	服务热线：400-6311-682   028-84191729 
            <br/>传 真：028-84191728
             <br/>官网：<a href="http://www.dhb.hk" target="_blank" style="text-decoration:none;">www.dhb.hk</a> 
            <br/> <br/>软件企业编号：川R-2010-0006 | 软件著作权登记号：2011SR027284 
		    <br/>阿商信息技术有限公司 版权所有 © 2005-2014 
      </div>
        <div class="footer_info_right">
   	    <img src="http://www.dhb.hk/email/images/ewm2_03.jpg" width="81" height="89" /> </div>
        <div style="clear:both"></div>
    </div>
</div>
<!--底部结束-->
</div>
		';
		
		$rmsg = '没有填邮箱';
		if(!empty($rinfo['CompanyEmail'])){
			$tomail = $rinfo['CompanyEmail'];
			$rmsg = send_mail($tomail,$rinfo['CompanyName'],"订货宝 网上订货系统 重置帐号 ",$content);
		}


		//短信通知
		$tomobile = '';
		if(!empty($rinfo['CompanyMobile']) && is_phone($rinfo['CompanyMobile'])){
			$message = "【订货宝】已为您重置帐号,信息如下,管理端网址:m.dhb.hk,帐号:".$uinfo['UserName'].",密码:".$in['pw'].",订货端网址：".$rinfo['CompanyPrefix'].".dhb.hk,微信订货端帐号：订货宝手机微信端(dhb_hk).订货宝服务中心会为您提供专业细致的服务支持,若有需要请致电客服中心4006311682.";
			$mobilearr[]    = $rinfo['CompanyMobile'];
			$statusCode2    = $client->login();
			$statusCode     = $client->sendSMS($mobilearr,$message);
			$tomobile = "、手机：".$rinfo['CompanyMobile'];
		}

		if($rmsg=="ok")
		{
			$status = SaveCompanyLog($in['ID'],'发送重置帐号通知：'.$tomail.''.$tomobile);
			echo '重置帐号已发送至邮件：'.$tomail."".$tomobile; 
		}else{
			echo $rmsg.$tomobile;
		}
		exit;
}

if($in['m'] == 'set_erp_info') {
    //设置ERP信息
    $company_id = $in['company'];
    $serial     = $in['serial'];
    $password   = $in['password'];
    $status     = $in['status'];
    $version    = $in['version'];
    $cpCompany  = $in['cpCompany'];
    $run_status = $in['isOpen'];
    $transfer   = $in['transferCheck'];
    $develop    = $in['develop'];
    $transStart = strtotime($in['transStart']);//订单传输开始时间
    $transStart = $transStart ? $transStart : 0;
    $token      = md5(md5($serial) . time());
    $erp_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_api_serial WHERE CompanyID={$company_id} LIMIT 1");
    $company_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company WHERE CompanyID={$company_id} LIMIT 0,1");
    $company_set = $db->get_var("SELECT SetValue FROM ".DATABASEU.DATATABLE."_order_companyset WHERE SetCompany={$company_id} AND SetName='erp' LIMIT 1");
    $company_set = $company_set ? unserialize($company_set) : array();
    
    if(empty($cpCompany)) exit('请指定合作方!');
    
    if($erp_info) {
        //判断是否发生变更，存入变更信息   2015-12-09
        if(($status != $erp_info['Status']) || ($run_status != $erp_info['RunStatus']) || ($in['changeReason'] != ''))
        {
            if($in['changeReason'] == '')
                exit('开通或运行状态更改，变更原因不能为空！');
            else
            {
                $change_result = $db->query("INSERT INTO ".DATABASEU.DATATABLE."_api_serial_changeinfo (CompanyID,ChangeUser,ChangeTime,ChangeStatus,ChangeRunStatus,ChangeReason) VALUES ({$company_id},'{$_SESSION['uinfo']['username']}',".time().",'{$erp_info['Status']}','{$erp_info['RunStatus']}','{$in['changeReason']}')");
            }
        }

        $api_result = $db->query("UPDATE ".DATABASEU.DATATABLE."_api_serial SET Status='{$status}',Version='{$version}',RunStatus='{$run_status}',TransferCheck='{$transfer}',Develop='{$develop}',TransStart='{$transStart}',isPrice='".$in['isPrice']."' WHERE CompanyID=" . $company_id . " LIMIT 1");    
    } else {
        $sql = "INSERT INTO ".DATABASEU.DATATABLE."_api_serial (SerialNumber,Password,Token,CompanyID,Status,CompanyDatabase,Version,RunStatus,TransferCheck,Develop,TransStart,Time) VALUES ('{$serial}','{$password}','{$token}',{$company_id},'{$status}','{$company_info['CompanyDatabase']}','{$version}','{$run_status}','{$transfer}','{$develop}','{$transStart}',".time().")";
        $api_result = $db->query($sql);
        
        if($in['changeReason'] != '')
        {
            $change_result = $db->query("INSERT INTO ".DATABASEU.DATATABLE."_api_serial_changeinfo (CompanyID,ChangeUser,ChangeTime,ChangeStatus,ChangeRunStatus,ChangeReason) VALUES ({$company_id},'{$_SESSION['uinfo']['username']}',".time().",'F','F','{$in['changeReason']}')");
        }
    }
    
    if($api_result === false) {
        exit('操作失败!');
    }

    //erp_interface,erp_order_check
    $erpSet = array(
        'erp_interface' => $run_status == 'T' ? 'Y' : 'N',
        'erp_order_check' => $transfer == 'T' ? 'Y' : 'N',
    );

    if($status == 'T' && !empty($transStart) && $develop == 'YTTX') {
        //将订单传输开始时间以前的订单的OrderApi更新为T
        if(empty($company_info['CompanyDatabase'])) {
            $sdatabase = DB_DATABASE.'.';
        } else {
            $sdatabase = DB_DATABASE."_".$company_info['CompanyDatabase'].'.';
        }

        $db->query("UPDATE ".$sdatabase.DATATABLE."_order_orderinfo SET OrderApi='T' WHERE OrderCompany=" . $company_id . " AND OrderDate <=" . $transStart);

    }
    
    //begin tubo 修改开通Erp时关闭客户注册 2015-11-05\
    /*if($run_status == 'T'  && $status == 'T'){
	    $product_set_info = $db->get_var("SELECT SetValue FROM ".DATABASEU.DATATABLE."_order_companyset WHERE SetCompany={$company_id} AND SetName='product' LIMIT 1");
	
	    if(empty($product_set_info)){
	    	$product_set_info =  array();
	    	$product_set_info['regiester_type'] = 'off';
	    	$result = $db->query("INSERT INTO ".DATABASEU.DATATABLE."_order_companyset (SetCompany,SetName,SetValue) VALUES ({$company_id},'product','".serialize($product_set_info)."')");
	    }else{
	    	$product_set_info = unserialize($product_set_info);
	    	$product_set_info['regiester_type'] = 'off';
	    	$result = $db->query("UPDATE ".DATABASEU.DATATABLE."_order_companyset SET SetValue='".serialize($product_set_info)."' WHERE SetCompany={$company_id} AND SetName='product' ");
	    }
    }*/
    //end 

    $erp_set_info = $db->get_row("SELECT SetID FROM ".DATABASEU.DATATABLE."_order_companyset WHERE SetCompany={$company_id} AND SetName='erp' LIMIT 1");
    if($erp_set_info) {
        $result = $db->query("UPDATE ".DATABASEU.DATATABLE."_order_companyset SET SetValue='".serialize($erpSet)."' WHERE SetCompany={$company_id} AND SetName='erp' AND SetID={$erp_set_info['SetID']}");
    } else {
        $result = $db->query("INSERT INTO ".DATABASEU.DATATABLE."_order_companyset (SetCompany,SetName,SetValue) VALUES ({$company_id},'erp','".serialize($erpSet)."')");
    }

    if($result !== false) {
        //修改供应商归属那个ERP合作方
        $db->query("update ".DATABASEU.DATATABLE."_order_company set CompanyType='".$cpCompany."' WHERE CompanyID={$company_id} LIMIT 1");
        exit('ok');
    } else {
        exit("操作失败!");
    }

    exit;
}

if($in['m'] == 'dredgeErp'){
    //开通
    $result = array('status'=>0,'info'=>'开通失败!');
    $serial = $in['serial'];
    $password = $in['password'];
    $cid = $in['company'];
    $version = $in['version'];
    $token = md5($serial . $password . time());
    $company = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company WHERE CompanyID={$cid} LIMIT 0,1");
    if($company){
        $sql = "INSERT INTO ".DATABASEU.DATATABLE."_api_serial (SerialNumber,Password,Token,CompanyID,Status,CompanyDatabase,Version) VALUES ('{$serial}','{$password}','{$token}',{$cid},'T','{$company['CompanyDatabase']}','{$version}')";
        $rst = $db->query($sql);
        if($rst!==false){
            $result['status'] = 1;
            $result['info'] = '开通成功!';
        }else{
            $result['info'] = '开通失败,请重试!';
        }
    }else{
        $result['info'] = '公司信息不存在!';
    }
    echo json_encode($result);
    exit;
}

if($in['m']=='erpDisabled'){
    //打开/关闭ERP接口功能
    $result = array('status'=>1,'info'=>'操作成功!');
    $cid = $in['company'];
    $status = $in['status'];
    $version = $in['version'];
    $sql = "UPDATE ".DATABASEU.DATATABLE."_api_serial SET Status='{$status}',Version='{$version}' WHERE CompanyID={$cid}";
    $rst = $db->query($sql);
    if($rst===false){
        $result['status'] = 0;
        $result['info'] = '操作失败';
    }
    echo json_encode($result);
    exit;
}

if($in['m'] == 'buildErp'){
    echo json_encode(array(
        'serial'=>create_guid(),
        'password'=>rand(100000,999999),
    ));
    exit;
}

if($in['m'] == 'erpDredge'){
    $result = array('status'=>0,'info'=>'操作失败');
    //开通ERP接口
    $company = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company WHERE CompanyID={$in['cid']} LIMIT 0,1");
    if($company){
        $api = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_serial WHERE CompanyID={$in['cid']} LIMIT 0,1");
        if($api){
            $result['info'] = '接口已开通,无需重复开通!';
        }else{
            $serial = create_guid();
            $password = rand(100000,999999);
            $token = md5($serial . $password . time());
            $sql = "INSERT INTO ".DATABASEU.DATATABLE."_api_serial (SerialNumber,Password,Token,CompanyID,Status,CompanyDatabase) VALUES ('{$serial}','{$password}','{$token}',{$in['cid']},'T','{$company['CompanyDatabase']}')";
            $rst = $db->query($sql);
            if($rst!==false){
                $result['status'] = 1;
                $result['info'] = '开通成功!';
                $result['data'] = array();
            }else{
                $result['info'] = '开通失败,请重试!';
                $result['sql'] = $sql;
            }
        }

    }else{
        $result['info'] = '企业信息不存在!';
    }
    echo json_encode($result);
    exit;
}

function create_guid($namespace = '') {
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['LOCAL_ADDR'];
    $data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = '' .
        substr($hash,  0,  8) .
        '-' .
        substr($hash,  8,  4) .
        '-' .
        substr($hash, 12,  4) .
        '-' .
        substr($hash, 16,  4) .
        '-' .
        substr($hash, 20, 12) .
        '';
    return $guid;
}



function send_mail($to_address, $to_name ,$subject, $body, $attach = "")
{
	
	//使用phpmailer发送邮件
	require_once("../class/phpmailer/class.phpmailer.php");
	$mail = new PHPMailer();
	$mail->IsSMTP(); // set mailer to use SMTP
// 	$mail->SMTPDebug = true;
	$mail->CharSet = 'utf-8';
	$mail->Encoding = 'base64';
// 	$mail->From = 'support@rsung.com';
	$mail->From = '316174705@qq.com';
	$mail->FromName = '沃订货正式帐号';
// 	$qp = think_decrypt('MDAwMDAwMDAwMI7OrWaErrJrtKp3soiFc3M','rsdhbhk');
	$mail->Host = 'smtp.qq.com';
	$mail->Port = 25; //default is 25, gmail is 465 or 587
	$mail->SMTPAuth = true;
// 	$mail->Username = "398219088@qq.com";
	$mail->Username = "316174705@qq.com";
	$mail->Password = '131420mimi';

	$mail->AddAddress($to_address, $to_name);

	$mail->WordWrap = 50;
	if (!empty($attach)) $mail->AddAttachment($attach);
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $body;
	//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

	if(!$mail->Send())
	{
		$backmsg = "发送失败: " . $mail->ErrorInfo . "";
		return $backmsg;
	}
	else
	{
		return "ok";
	}

}


if($in['m']=="content_agent_save")
{
	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where AgentName='".$in['data_AgentName']."' limit 0,1");
	if(!empty($clientinfo['orwname'])) exit('该名称已经存在！请不要重复添加');
	
	if(empty($in['AgentID']))
	{
		$upsql = "insert into ".DATABASEU.DATATABLE."_order_agent(AgentName,
AgentContact,
AgentPhone,
AgentAddress,
AgentQQ,
AgentEmail,
AgentArea,
AgentType,
AgentBegin,
AgentEnd,
AgentMoney,
AgentRemark,
CreateDate,
CreateUser) values('".$in['data_AgentName']."','".$in['data_AgentContact']."','".$in['data_AgentPhone']."','".$in['data_AgentAddress']."','".$in['data_AgentQQ']."','".$in['data_AgentEmail']."',".$in['data_AgentArea'].",'".$in['data_AgentType']."','".$in['data_AgentBegin']."','".$in['data_AgentEnd']."','".$in['data_AgentMoney']."','".$in['data_AgentRemark']."','".time()."','".$_SESSION['uinfo']['username']."')";
		
	}else{
		$upsql = "update ".DATABASEU.DATATABLE."_order_agent set AgentName='".$in['data_AgentName']."',AgentContact='".$in['data_AgentContact']."',
AgentPhone='".$in['data_AgentPhone']."',
AgentAddress='".$in['data_AgentAddress']."',
AgentQQ='".$in['data_AgentQQ']."',
AgentEmail='".$in['data_AgentEmail']."',
AgentArea=".$in['data_AgentArea'].",
AgentType='".$in['data_AgentType']."',
AgentBegin='".$in['data_AgentBegin']."',
AgentEnd='".$in['data_AgentEnd']."',
AgentMoney='".$in['data_AgentMoney']."',
AgentRemark='".$in['data_AgentRemark']."' where AgentID=".intval($in['AgentID'])." limit 1";
	}
	$update  = $db->query($upsql);
	if($update)
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}


if($in['m'] == 'delete_agent')
{
	if(!empty($in['ID']))
	{
		
		$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATABASEU.DATATABLE."_order_company where CompanyAgent=".intval($in['ID'])." ");
		if(!empty($cinfo['lrow'])) exit('该代理商已在使用，不能删除!');

		$upsql = "delete from ".DATABASEU.DATATABLE."_order_agent where AgentID=".intval($in['ID'])." limit 1";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}


}

//跟踪记录
if($in['m']=="save_company_log")
{
	if(empty($in['CompanyID'])) exit('参数错误！');
	if(empty($in['Content'])) exit('请填定内容！');

	$status = SaveCompanyLog($in['CompanyID'],$in['Content']);
	if($status)
	{
		exit('ok');
	}else{
		exit('提交不成功！');
	}

}

if($in['m'] == 'company_check') {

    $saleid = $in['sale'];
    $company_id = $in['id'];
    $flag = $in['flag'];
    
    if($flag == 'F' && $in['remark'] == ''){
    	exit('请填写未通过原因');
    }
    
    if($flag == 'T')
    {
        if(empty($in["area"]) || !intval($in["area"]))
        {
            exit('请选择所属地区!');
        }
        else if(empty($in["industry"]) || !intval($in["industry"]))
        {
            exit('请选择所属行业!');
        }
        else if(empty($in["name"]))
        {
            exit('请输入公司名称!');
        }
        else if(empty($in["card"]))
        {
            exit('请选择营业执照号码!');
        }
        else 
        {
            //判断营业执照是否重复 取通过的审核的客户数据进行比较
            $bcard = $db->get_var("SELECT COUNT(*) AS CNT FROM ".DATABASEU.DATATABLE."_order_company c 
                                    INNER JOIN ".DATABASEU.DATATABLE."_order_cs cs 
                                    ON c.CompanyID = cs.CS_Company
                                    WHERE c.IdentificationNumber='{$in["card"]}' AND cs.CS_Flag = 'T'");
            
            if($bcard > 0)
                exit('营业执照重复!');
        }
    }
    
    $remark = $in['remark'];
    $admin_user = $_SESSION['uinfo']['username'];
    $log_sql = "INSERT INTO ".DATABASEU.DATATABLE."_order_company_check_log (CompanyID,Flag,Remark,Time,AdminUser) VALUES ({$company_id},'{$flag}','{$remark}',".time().",'{$admin_user}')";
    $db->query($log_sql);
    $cs_sql = "UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_Flag='{$flag}',CS_SaleUID='{$saleid}',CS_OpenDate='".date("Y-m-d")."' WHERE CS_Company={$company_id} LIMIT 1";
    if($db->query($cs_sql) !== false) {
        if($flag == 'T') {

            //审核通过后 将营业执照号&身份证号回写入company
            $db->query("UPDATE ".DATABASEU.DATATABLE."_order_company SET CompanyArea = {$in["area"]},CompanyIndustry = {$in["industry"]},BusinessLicense='{$in["name"]}',IdentificationNumber='{$in["card"]}' WHERE CompanyID=" . $company_id);
           
        }
        $db->query("UPDATE ".DATABASEU.DATATABLE."_order_company_data SET BusinessName = '{$in["name"]}',BusinessCard = '{$in["card"]}',Notice='{$remark}' WHERE CompanyID=" . $company_id);
        exit('ok');
    } else {
        exit('审核发生错误!');
    }
}


//跟踪记录
function SaveCompanyLog($companyid,$content)
{
	global $db;

	$upsql = "insert into ".DATABASEU.DATATABLE."_order_company_log(CompanyID,CreateDate,CreateUser,Content) values('".intval($companyid)."',".time().",'".$_SESSION['uinfo']['username']."','".$content."')";
	return $db->query($upsql);
}

//支付信息
if($in['m']=="save_getway")
{
	if(empty($in['CompanyID'])) exit('参数错误！');
	if($in['GetWay'] == 'allinpay' && (empty($in['MerchantNO']) || empty($in['SignMsgKey']) || empty($in['SignMsg']))) {
        exit('请输入指定的内容！');
    } else if($in['GetWay'] == 'yijifu' && (empty($in['SignNO']) || empty($in['SignAccount']) || empty($in['MerchantName']))) {
        exit('请输入指定的内容！');
    }

    $in['IsDefault'] = empty($in['IsDefault']) ? 'N' : 'Y';
    $company_id = $in['CompanyID'];
    
    //T:已开通，F：未开通。默认F。个人账号和企业账号都可以开通POS支付，款项是打到客户的易极付账户
    $in['open_pos'] = empty($in['open_pos']) ? 'F' : $in['open_pos'];
    if($in['IsDefault'] == 'N' && $in['GetWay'] == 'yijifu') {
        //易极付 且 非默认账户 需要验证是否包含易极付默认账户
        $yijifu_dft = $db->get_var("SELECT COUNT(*) FROM rsung_order_getway WHERE CompanyID={$company_id} AND GetWay='yijifu'");
        if($yijifu_dft == 0) {
            $in['IsDefault'] = 'Y';
        }
    }

    
	if(empty($in['GetWayID'])){
		$sql = "insert into ".DATABASEU.DATATABLE."_order_getway(GetWay,
                CompanyID,
                MerchantNO,
                SignMsgKey,
                SignMsg,
                B2B,
                Fee,
                Status,
                SignNO,
                SignAccount,
                AccountType,
                IsDefault,
                MerchantName,
		        open_pos) values('".$in['GetWay']."',
                ".$in['CompanyID'].",
                '".$in['MerchantNO']."',
                '".$in['SignMsgKey']."',
                '".$in['SignMsg']."',
                '".$in['B2B']."',
                '".$in['Fee']."',
                '".$in['Status']."',
                '".$in['SignNO']."',
                '".$in['SignAccount']."',
                '".$in['AccountType']."',
                '".$in['IsDefault']."',
                '".$in['MerchantName']."',
                '".$in['open_pos']."')";
	}else{
		$sql = "update ".DATABASEU.DATATABLE."_order_getway set SignNO='".$in['SignNO']."',SignAccount='".$in['SignAccount']."',AccountType='".$in['AccountType']."',IsDefault='".$in['IsDefault']."',MerchantName='".$in['MerchantName']."', MerchantNO='".$in['MerchantNO']."', SignMsgKey='".$in['SignMsgKey']."', SignMsg ='".$in['SignMsg']."', B2B='".$in['B2B']."',Fee='".$in['Fee']."', Status='".$in['Status']."',open_pos='".$in['open_pos']."' where GetWayID=".$in['GetWayID']." and CompanyID = ".$in['CompanyID']." limit 1";
	}
	
	if($db->query($sql))
	{
        if($in['IsDefault'] == 'Y') {
            $get_way_id = empty($in['GetWayID']) ? $db->insert_id : $in['GetWayID'];
            $db->query("UPDATE ".DATABASEU.DATATABLE."_order_getway SET IsDefault='N' WHERE CompanyID=" . $in['CompanyID'] . " AND GetWay='yijifu' AND GetWayID <> " . $get_way_id);
        }

        //若是易极付，则统一修改账号的POS开通状态,保持两条记录同步 addby wanjun @20160128
        if($in['GetWay'] == 'yijifu'){
           $yjfSql = "update ".DATABASEU.DATATABLE."_order_getway set open_pos='".$in['open_pos']."' where CompanyID = ".$in['CompanyID']." and GetWay='yijifu'";
            $db->query($yjfSql);
        }
        
		exit('ok');
	}else{
		exit('提交不成功！');
	}

}

//检查易极付的POS开户状态
if($in['m'] == 'checkpaytype'){
    
    $csql = "select count(*) as cnt from ".DATABASEU.DATATABLE."_order_getway where CompanyID=".intval($in['cpid'])." and GetWay='yijifu' and open_pos='T'";
    $result = $db->get_row($csql);
    echo intval($result['cnt']);
    exit;
}

/********** Function ************/
function ChangeMsg($msgu,$msgp)
{
	if(!empty($msgu) && !empty($msgp))
	{
		$delmsg = md5($msgu);
		$rname  = substr($delmsg,5,1).",".substr($delmsg,7,1).",".substr($delmsg,15,1).",".substr($delmsg,17,1);
		$rnamearray = explode(',',$rname);
		$rpass  = md5($msgp);
		$r_msg  = str_replace($rnamearray, "", $rpass);
	}else{
		$r_msg  = $msgp;
	}
	return $r_msg;
} 
//药企资质审核
if($in['m'] == 'client_check') {

	$saleid = $in['sale'];
	$company_id = $in['id'];
	$user_id = $in['uid'];
	$flag = $in['flag'];

	if($flag == 'T')
	{
		if(empty($in["name"]))
		{
			exit('请输入公司名称!');
		}
		else if(empty($in["card"]))
		{
			exit('请选择营业执照号码!');
		}
		else
		{
			//判断营业执照是否重复 取通过的审核的客户数据进行比较
			$bcard = $db->get_var("SELECT COUNT(*) AS CNT FROM ".DATATABLE."_order_client AS c LEFT JOIN ".DATABASEU.DATATABLE."_three_sides_merchant AS m ON c.ClientCompany=m.CompanyID AND c.ClientID=m.MerchantID WHERE m.BusinessCard='{$in["card"]}' AND c.C_Flag = 'T'");
				
			if($bcard > 0)
				exit('营业执照重复!');
		}
	}

	$remark = $in['remark'];
	$admin_user = $_SESSION['uinfo']['username'];
	$ms_sql = "UPDATE ".DATATABLE."_order_client SET C_Flag='{$flag}', validUser='".$_SESSION['uinfo']['username']."[".$_SESSION['uinfo']['usertruename']."]' WHERE ClientCompany={$company_id} and ClientID={$user_id} LIMIT 1";
        if($db->query($ms_sql) !== false) {
		$sm_sql = "UPDATE ".DATABASEU.DATATABLE."_three_sides_merchant set Notice='{$remark}' WHERE CompanyID={$company_id} and MerchantID={$user_id} LIMIT 1";
		$db->query($sm_sql);

		exit('ok');
	} else {
		exit('审核发生错误!');
	}
}
if($in['m'] == 'OneApprove'){
    //一级审核表
    $CompanyIDSe = $_SESSION['uc']['CompanyID'];
    $ClientID = $in['ClientID'];
    $CompanyID = $in['CompanyID'];
    $Amount = $in['Amount'];
    $ResidueAmount = $in['Amount'];
    $OpenDate = date('Y-m-d h:i:s',time());
    $OneApprove = $in['OneApprove'];
    $CreditStatus = 'one';
    $OneContentOne = $in['OneContentOne'];
    //查询表
    $OneAppSql = "select * from ".DATABASEU.DATATABLE."_credit_main where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $OneAppSel = $db->get_row($OneAppSql);
    if(!empty($OneAppSel)){
        $OneAppUpSql = "UPDATE ".DATABASEU.DATATABLE."_credit_main  SET Amount='".$Amount."',OpenDate='".$OpenDate."',OneApprove='".$OneApprove."',CreditStatus='".$CreditStatus."',OneContent='".$OneContentOne."' where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
        $OneAppUpSel = $db->query($OneAppUpSql);
        if($OneAppUpSel){
            //写入记录
            $oldAmount = $OneAppSel['Amount'];
            $OneApprove1 = $in['OneApprove'].(一审);
            $bodyAddSql = "insert into ".DATABASEU.DATATABLE."_credit_body ( CompanyID,ClientID,OldAmount,NowAmount,ModifyTime,ModifyUser)values(".$CompanyID.",".$ClientID.",'".$oldAmount."','".$Amount."','".$OpenDate."','".$OneApprove1."')";
            $bodyAddSel = $db->query($bodyAddSql);
            if($bodyAddSel){
                exit('ok');
            }else{
                exit('操作失败1');
            }
            
        }else{
            exit('操作失败');
        }
        
    }else{
        $OneAppAddSql = "INSERT into ".DATABASEU.DATATABLE."_credit_main (CompanyID,ClientID,Amount,ResidueAmount,OpenDate,OneApprove,CreditStatus,OneContent)values(".$CompanyID.",".$ClientID.",'".$Amount."','".$ResidueAmount."','".$OpenDate."','".$OneApprove."','".$CreditStatus."','".$OneContentOne."') ";
        $OneAppAddSel = $db->query($OneAppAddSql);
        if($OneAppAddSel){
                        //写入记录
            $oldAmount = 0;
            $OneApprove1 = $in['OneApprove'].(一审);
            $bodyAddSql = "insert into ".DATABASEU.DATATABLE."_credit_body ( CompanyID,ClientID,OldAmount,NowAmount,ModifyTime,ModifyUser)values(".$CompanyID.",".$ClientID.",'".$oldAmount."','".$Amount."','".$OpenDate."','".$OneApprove1."')";
            $bodyAddSel = $db->query($bodyAddSql);
            if($bodyAddSel){
                exit('ok');
            }else{
                exit('操作失败1');
            }
        }else{ 
            exit('操作失败2');
        }
    }
    
}
//二审
if($in['m'] == 'TwoApprove'){

    $ClientID = $in['ClientID'];
    $CompanyIDSe = $_SESSION['uc']['CompanyID'];
    $CompanyID = $in['CompanyID'];
    $OneAppSql = "select * from ".DATABASEU.DATATABLE."_credit_main where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $OneAppSel = $db->get_row($OneAppSql);
    $Amount = $OneAppSel['Amount'];
    $TwoApprove = $in['TwoApprove'];
    $CreditStatus = 'open';
    $TwoContent = $in['TwoContent'];
    $OpenDate = date('Y-m-d h:i:s',time());
    $TwoAppUpSql = "UPDATE ".DATABASEU.DATATABLE."_credit_main SET  TwoApprove='".$TwoApprove."',CreditStatus='".$CreditStatus."',TwoContent='".$TwoContent."' where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $TwoAppUpSel = $db->query($TwoAppUpSql);

    if($TwoAppUpSel){
        $TwoApproveE = $in['TwoApprove'].'(二审)';
            $bodyAddSql = "insert into ".DATABASEU.DATATABLE."_credit_body ( CompanyID,ClientID,OldAmount,NowAmount,ModifyTime,ModifyUser)values(".$CompanyID.",".$ClientID.",'".$Amount."','".$Amount."','".$OpenDate."','".$TwoApproveE."')";
            $bodyAddSel = $db->query($bodyAddSql);
            if($bodyAddSel){
            	
//             	$sms = new SmsApp();
//             	$sms->getSmsTpl('YTZQSETSUCCESS')
//             	->bulidContent(array('{LASTNUM}', '{ORDERTOTAL}'), array($code, $lastnum))
//             	->SendSMS($in['mobile']);
            	
                exit('ok');
            }else{
                exit('操作失败');
            }
    }else{
        exit('操作失败');
    }
}
//关闭账期
if($in['m'] == CloseApprove){
    $ClientID = $in['ClientID'];
    $CompanyIDSe = $_SESSION['uc']['CompanyID'];
    $CompanyID = $in['CompanyID'];
    $OneAppSql = "select * from ".DATABASEU.DATATABLE."_credit_main where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $OneAppSel = $db->get_row($OneAppSql);
    $Amount = $OneAppSel['Amount'];
    $SOneApprove = $_SESSION['uinfo']['username'].关闭;
    $OpenDate = date('Y-m-d h:i:s',time());
    $CreditStatus = 'closed';
    $CloseUpSql = "UPDATE ".DATABASEU.DATATABLE."_credit_main SET  CreditStatus ='".$CreditStatus."',OneApprove='".$SOneApprove."' where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $CloseUpSel = $db->query($CloseUpSql);
    if($CloseUpSel){
            $bodyAddSql = "insert into ".DATABASEU.DATATABLE."_credit_body ( CompanyID,ClientID,OldAmount,NowAmount,ModifyTime,ModifyUser)values(".$CompanyID.",".$ClientID.",'".$Amount."','".$Amount."','".$OpenDate."','".$SOneApprove."')";
            $bodyAddSel = $db->query($bodyAddSql);
            if($bodyAddSel){
                exit('ok');
            }else{
                exit('操作失败');
            }
    }else{
        exit('操作失败');
    }  
}
//一审不通过
if($in['m'] == OneWeiApprove){
    $ClientID = $in['ClientID'];
    $CompanyIDSe = $_SESSION['uc']['CompanyID'];
    $CompanyID =  $in['CompanyID'];
    $OneApprove = $in['OneApprove'].一审不通过;
    $Amount = $in['Amount'];
    $OneContent = $in['OneContent'];
    $OpenDate = date('Y-m-d h:i:s',time());
    //查询是否已存在审核信息
    $OneAppSql = "select * from ".DATABASEU.DATATABLE."_credit_main where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $OneAppSel = $db->get_row($OneAppSql);
    $OldAmount = $OneAppSel['Amount']; // 取出原审核额度
    if(!empty($OneAppSel)){
        //存在修改
        $OneWeiApproveSql = "update ".DATABASEU.DATATABLE."_credit_main SET OpenDate='".$OpenDate."',OneApprove='".$OneApprove."',CreditStatus='oneunapprove',OneContent='".$OneContent."' where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
        $OneWeiApproveSel = $db->query($OneWeiApproveSql);
        if($OneWeiApproveSel){
            //书写操作记录
            $ShenHeOneBodSql = "insert into ".DATABASEU.DATATABLE."_credit_body (CompanyID,ClientID,OldAmount,NowAmount,ModifyTime,ModifyUser) value(".$CompanyID.",".$ClientID.",'".$OldAmount."',".$Amount.",'".$OpenDate."','".$OneApprove."')";
            $ShenHeOneBodSel = $db->query($ShenHeOneBodSql);
            if($ShenHeOneBodSel){
                echo'ok';die;
            }else{
                echo'操作失败';die;
            }
        }else{
            echo'操作失败';die;
        }       
    }else{
        //不存在添加
        $OneWeiApproveAddSql = "insert into ".DATABASEU.DATATABLE."_credit_main (CompanyID,ClientID,Amount,ResidueAmount,OpenDate,OneApprove,CreditStatus,OneContent)value(".$CompanyID.",".$ClientID.",0,0,'".$OpenDate."','".$OneApprove."','oneunapprove','".$OneContent."')";
        $OneWeiApproveAddsel = $db->query($OneWeiApproveAddSql);
        if($OneWeiApproveAddsel){
            $ShenHeOneBodAddSql = "insert into ".DATABASEU.DATATABLE."_credit_body (CompanyID,ClientID,OldAmount,NowAmount,ModifyTime,ModifyUser) value(".$CompanyID.",".$ClientID.",0,0,'".$OpenDate."','".$OneApprove."')";
            $ShenHeOneBodAddSel = $db->query($ShenHeOneBodAddSql);
            if($ShenHeOneBodAddSel){
                echo'ok';die;
            }else{
                echo'操作失败';die;
            }
        }else{
            echo'操作失败';die;
        }
        
    }  
}
//二审不通过
if($in['m'] == TwoWeiApprove){
    $ClientID = $in['ClientID'];
    $CompanyIDSe = $_SESSION['uc']['CompanyID'];
    $CompanyID = $in['CompanyID'];
    $TwoContent = $in['TwoContent'];
    $TwoApprove = $in['TwoApprove'].二审不通过;
    $OpenDate = date('Y-m-d h:i:s',time());
    //查询是否已存在审核信息
    $OneAppSql = "select * from ".DATABASEU.DATATABLE."_credit_main where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $OneAppSel = $db->get_row($OneAppSql);
    $OldAmount = $OneAppSel['Amount']; // 取出原审核额度
    $TwoShenSql = "UPDATE ".DATABASEU.DATATABLE."_credit_main SET OpenDate='".$OpenDate."',TwoApprove='".$TwoApprove."',CreditStatus='twounapprove',TwoContent='".$TwoContent."' where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
    $TwoShenSel = $db->query($TwoShenSql);
    if($TwoShenSel){
        //操作记录
        $TwoShenBodSql = "insert into ".DATABASEU.DATATABLE."_credit_body (CompanyID,ClientID,OldAmount,NowAmount,ModifyTime,ModifyUser)value(".$CompanyID.",".$ClientID.",".$OldAmount.",".$OldAmount.",'".$OpenDate."','".$TwoApprove."')";
        $TwoShenBodSel = $db->query($TwoShenBodSql);
        if($TwoShenBodSel){
            echo 'ok';die;
        }else{
            echo '操作失败';die;
        }  
    }else{
        echo '操作失败';die;
    }
}

if($in['m']==creditdata){
    $bdata=isset($_POST['bdata'])?$_POST['bdata']:'';
    $edata=isset($_POST['edata'])?$_POST['edata']:'';
    $creditid=isset($_POST['creditid'])?$_POST['creditid']:'';
    
}
function InputDefaultValue($db,$cid,$phone,$sdatabase)
{
	$settype = 'product';
	$valuemsg = 'a:12:{s:15:"checkandapprove";s:2:"on";s:11:"producttype";s:7:"imglist";s:14:"product_number";s:2:"on";s:16:"product_negative";s:3:"off";s:19:"product_number_show";s:3:"off";s:11:"return_type";s:5:"order";s:11:"deduct_type";s:2:"on";s:10:"audit_type";s:3:"off";s:14:"regiester_type";s:2:"on";s:13:"delivery_time";s:1:"N";s:10:"show_money";s:2:"on";s:13:"product_price";a:4:{s:11:"price1_show";s:2:"on";s:11:"price1_name";s:9:"参考价";s:11:"price2_show";s:3:"off";s:11:"price2_name";s:9:"订货价";}}';
	$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");

	$smsarr = Array
	(
		0 => 1,
		1 => 2,
		2 => 3,
		3 => 4,
		4 => 5,
		5 => 6,
		6 => 7,
		7 => 8,
		9 => 9,
		'Mobile' => Array
			(
				'MainPhone' => $phone,
				'FinancePhone' => $phone,
				'FinancePhone' => $phone
			)
	);
	$settype = 'sms';
	$valuemsg = serialize($smsarr);
	$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");
	
	$settype = 'clientlevel';
	$valuemsg = 'a:2:{s:1:"A";a:5:{s:7:"level_1";s:15:"一级经销商";s:7:"level_2";s:15:"二级经销商";s:7:"level_3";s:15:"三级经销商";s:2:"id";s:1:"A";s:4:"name";s:15:"经销商类型";}s:9:"isdefault";s:1:"A";}';
	$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");

	$settype = 'printf';
	$valuemsg = 'a:18:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:6:"Price1";a:3:{s:4:"name";s:9:"零售价";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:0:"";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:0:"";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"批发价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";s:5:"order";a:16:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"PercentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:16:"CompanyInfoPrint";s:1:"1";}s:4:"send";a:16:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:2:"4%";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";s:1:"1";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"PercentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:16:"CompanyInfoPrint";s:1:"1";}s:6:"return";a:12:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:2:"5%";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";s:0:"";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:16:"CompanyInfoPrint";s:1:"1";}}';
	$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");

	$insql = "insert into ".$sdatabase.DATATABLE."_order_expense_bill(BillNO,BillName,CompanyID) values
	('1001','期初',$cid),
	('1002','返利',$cid),
	('1003','补差',$cid),
	('1004','促销费',$cid)";
	$isq = $db->query($insql);

    	//地区
    	$areasql = "insert into ".$sdatabase.DATATABLE."_order_area (AreaCompany,
			AreaParentID,
			AreaName,
			AreaPinyi,
			AreaAbout) values($cid,0,'通用','TY','')";    	
    	$db->query($areasql);
    	
    	//分类
    	$sitesql = "insert into ".$sdatabase.DATATABLE."_order_site (CompanyID,
			ParentID,
			SiteNO,
			SiteOrder,
			SiteName,
			SitePinyi,
			SiteAdmin,
			Content,
			Disabled) values($cid,0,'0.',0,'通用','TY','System','',0)";
    	$db->query($sitesql);
    	$insert_id = mysql_insert_id();
    	$db->query("update ".$sdatabase.DATATABLE."_order_site set SiteOrder = '0.".$insert_id.".' where SiteID=".$insert_id." limit 1");


}

exit('非法操作!223');
?>