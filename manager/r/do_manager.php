<?php
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
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
	
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_company(CompanyArea,CompanyIndustry,CompanyAgent,CompanyName,CompanySigned,CompanyPrefix,CompanyCity,CompanyContact,CompanyMobile,CompanyPhone,CompanyFax,CompanyAddress,CompanyEmail,CompanyWeb,CompanyUrl,CompanyRemark,CompanyDate) values(".$in['data_CompanyArea'].",".$in['data_CompanyIndustry'].",".intval($in['data_CompanyAgent']).",'".$in['data_CompanyName']."','".$in['data_CompanySigned']."','".$in['data_CompanyPrefix']."','".$in['data_CompanyCity']."','".$in['data_CompanyContact']."','".$in['data_CompanyMobile']."','".$in['data_CompanyPhone']."','".$in['data_CompanyFax']."','".$in['data_CompanyAddress']."','".$in['data_CompanyEmail']."','".$in['data_CompanyWeb']."','".$in['data_CompanyUrl']."','".$in['data_CompanyRemark']."',".time().")";
	$update  = $db->query($upsql);	
	if($update)
	{
		$insert_id = mysql_insert_id();
		if(empty($in['CS_UpDate'])) $in['CS_UpDate'] = date("Y-m-d");
		$insql = "insert into ".DATABASEU.DATATABLE."_order_cs(CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_UpDate,CS_UpdateTime,CS_SmsNumber) values(".$insert_id.",".$in['CS_Number'].",'".$in['CS_BeginDate']."','".$in['CS_EndDate']."','".$in['CS_UpDate']."',".time().",".$in['CS_SmsNumber'].")";
		$db->query($insql);
		
		if($insert_id > 500)
		{
			$databaseid = floor($insert_id/500);
			$db->query("update ".DATABASEU.DATATABLE."_order_company set CompanyDatabase=".$databaseid." where CompanyID=".$insert_id." limit 1");
		}

		if(!(file_exists (RESOURCE_PATH.$insert_id)))
		{
			_mkdir(RESOURCE_PATH,$insert_id);
		}
		$status = SaveCompanyLog($insert_id,'入驻医统天下('.$in['CS_Number'].'用户)');
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m'] == 'company_check') {

    $company_id = $in['id'];
    $flag = $in['flag'];
    $remark = $in['remark'];
    $admin_user = $_SESSION['uinfo']['username'];
    $log_sql = "INSERT INTO ".DATABASEU.DATATABLE."_order_company_check_log (CompanyID,Flag,Remark,Time,AdminUser) VALUES ({$company_id},'{$flag}','{$remark}',".time().",'{$admin_user}')";
    $db->query($log_sql);
    $cs_sql = "UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_Flag='{$flag}' WHERE CS_Company={$company_id} LIMIT 1";
    if($db->query($cs_sql) !== false) {
        if($flag == 'T') {

            //审核通过后 将营业执照号&身份证号回写入company
            $data_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_data WHERE CompanyID=" . $company_id);
            $db->query("UPDATE ".DATABASEU.DATATABLE."_order_company SET BusinessLicense='{$data_info['BusinessName']}',IdentificationNumber='{$data_info['BusinessCard']}' WHERE CompanyID=" . $company_id);

            $check_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DATABASEU.DATATABLE."_order_company_check_log WHERE CompanyID={$company_id} LIMIT 1");
            //审核通过 赠送300条短信
            if($check_cnt == 1) {
                $cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company=" . $company_id . " LIMIT 1");
                $db->query("UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_SmsNumber=CS_SmsNumber+300 WHERE CS_Company={$company_id} LIMIT 1");
                SaveCompanyLog($company_id,'审核通过修改短信数量从 '.$cs_info['CS_SmsNumber'].'条到 '.($cs_info['CS_SmsNumber'] + 300).'条');
            }

        }
        exit('ok');
    } else {
        exit('审核发生错误!');
    }
}

/*********** edit company **************/
if($in['m']=="content_edit_company_save")
{
	$in['ID']     = intval($in['ID']);

	$in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));	
	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where CompanyName='".$in['data_CompanyName']."' limit 0,1");
	if($clientinfo['orwname'] > 1) exit('repeat');

	$Prefixinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".$in['data_CompanyPrefix']."' limit 0,1");
	if(!empty($Prefixinfo['orwname']) && $Prefixinfo['orwname'] > 1) exit('repeatprefix');
	$infoc = $db->get_row("SELECT CompanyName FROM ".DATABASEU.DATATABLE."_order_company where CompanyID='".$in['ID']."' limit 0,1");

	$upsql = "update ".DATABASEU.DATATABLE."_order_company set CompanyArea ='".$in['data_CompanyArea']."', CompanyIndustry ='".$in['data_CompanyIndustry']."',CompanyAgent=".intval($in['data_CompanyAgent']).", CompanyName='".$in['data_CompanyName']."',CompanySigned='".$in['data_CompanySigned']."', CompanyPrefix='".$in['data_CompanyPrefix']."', CompanyCity='".$in['data_CompanyCity']."', CompanyContact='".$in['data_CompanyContact']."', CompanyMobile='".$in['data_CompanyMobile']."', CompanyPhone='".$in['data_CompanyPhone']."', CompanyFax='".$in['data_CompanyFax']."', CompanyAddress='".$in['data_CompanyAddress']."', CompanyEmail='".$in['data_CompanyEmail']."', CompanyWeb='".$in['data_CompanyWeb']."', CompanyUrl='".$in['data_CompanyUrl']."', CompanyRemark='".$in['data_CompanyRemark']."' where CompanyID=".$in['ID'];
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
	if(empty($in['pw'])) exit('请填写管理员密码！');

	$rinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");
	if(empty($rinfo)) exit('您发送的数据不存在');

	$uinfo = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$rinfo['CompanyID']." and UserFlag='9' order by UserID asc limit 0,1");
	if($uinfo['UserPass'] != ChangeMsg($uinfo['UserName'],$in['pw'])){
		exit('密码不正确，请重新设置密码！');
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
.wechat h1{ line-height:40px; font-size:14px; color:#a38643; padding-left:40px;}
.wechat_info{ width:600px; height:120px; border:1px solid #ececec; margin:0 auto}
.wechat_info_left{ float:left; overflow:hidden; width:480px; height:120px;}
.wechat_info_left dt{ width:457px; border-bottom:#ececec 1px solid; height:40px; display:block; border-right:#ececec 1px solid; line-height:40px; padding-left:20px;}
.ewm_text{ width:457px; height:40px; line-height:40px; padding-left:20px;}
.wechat_info_right{ width:112px; height:112px; float:right; padding:4px;}

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
    <td class="user_info_td1">'.$uinfo['UserName'].' </td>
    <td class="user_info_td1">'.$in['pw'].' </td>
  </tr>
</table>
</div>
<!--试用账号结束-->
<!--微信客户端开始-->
<div class="wechat">
<h1>微信客户端</h1>
<div class="wechat_info">
	<div class="wechat_info_left">
    	<dt>微信公众帐号：订货宝手机微信端 </dt>
        <dt>微信号：dhb_hk</dt>
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
			$message = "【订货宝】已为您开通正式帐号,信息如下,管理端网址:m.dhb.hk,帐号:".$uinfo['UserName'].",密码:".$in['pw'].",订货端网址：".$rinfo['CompanyPrefix'].".dhb.hk,微信订货端帐号：订货宝手机微信端(dhb_hk).订货宝服务中心会为您提供专业细致的服务支持,若有需要请致电客服中心4006311682.";
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
.wechat h1{ line-height:40px; font-size:14px; color:#198700; padding-left:40px;}
.wechat_info{ width:600px; height:120px; border:1px solid #ececec; margin:0 auto}
.wechat_info_left{ float:left; overflow:hidden; width:480px; height:120px;}
.wechat_info_left dt{ width:457px; border-bottom:#ececec 1px solid; height:40px; display:block; border-right:#ececec 1px solid; line-height:40px; padding-left:20px;}
.ewm_text{ width:457px; height:40px; line-height:40px; padding-left:20px;}
.wechat_info_right{ width:112px; height:112px; float:right; padding:4px;}

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
<h1>微信客户端</h1>
<div class="wechat_info">
	<div class="wechat_info_left">
    	<dt>微信公众帐号：订货宝手机微信端 </dt>
        <dt>微信号：dhb_hk</dt>
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
		$rmsg = send_mail($tomail,$rinfo['CompanyName'],"订货宝 网上订货系统 重置帐号 ",$content);

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
			echo $rmsg;
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
        $api = $db->get_row("SELECT * FROM rsung_order_serial WHERE CompanyID={$in['cid']} LIMIT 0,1");
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
	//$mail->SMTPDebug = true;
	$mail->CharSet = 'utf-8';
	$mail->Encoding = 'base64';
	$mail->From = 'dhb@rsung.com';
	$mail->FromName = '订货宝正式帐号';

	$mail->Host = 'smtp.qq.com';
	$mail->Port = 25; //default is 25, gmail is 465 or 587
	$mail->SMTPAuth = true;
	$mail->Username = "1730407198@qq.com";
	$mail->Password = "rsungdhb123456";

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

//保存体验行业配置
if($in['m']=="content_ty_industry_save")
{

	if(is_array($in['num_value'])){
		foreach($in['num_value'] as $key=>$val){
			if(!array_key_exists($key,$in['set_value'])){
				unset($in['num_value'][$key]);
			}
		}
	}else{
		$in['num_value'] = array();
	}
	
	$sNumValue = json_encode($in['num_value']);
	$upsql = "update ".DATABASEU.DATATABLE."_ty_option set Value = '{$sNumValue}' where Name = 'industry' ";

	if($db->query($upsql))
	{
		exit("ok");
	}else{
		exit("保存行业配置不成功!");
	}
}

//跟踪记录
function SaveCompanyLog($companyid,$content)
{
	global $db;

	$upsql = "insert into ".DATABASEU.DATATABLE."_order_company_log(CompanyID,CreateDate,CreateUser,Content) values('".intval($companyid)."',".time().",'".$_SESSION['uinfo']['username']."','".$content."')";
	return $db->query($upsql);
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
		$r_msg = str_replace($rnamearray, "", $rpass);
	}else{
		$r_msg = $msgp;
	}
	return $r_msg;
} 



exit('非法操作!');
?>