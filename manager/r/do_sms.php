<?php
$menu_flag = "sms";
include_once ("header.php");
include_once ("../WebService/include/Client.php");
include_once ("../soap2.inc.php");

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="shownumber")
{
	if($_SESSION['uinfo']['userflag']!="9") exit('对不起，您没有此项操作权限！');
	$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
	$client->setOutgoingEncoding("UTF-8");

	$balance_end = $client->getBalance();
	$feed = $client->getEachFee();
	echo $numberend = $balance_end/$feed;
	exit();
}

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

/*****************************/


if($in['m']=="set_sms_number")
{
	echo $_SESSION['uc']['SmsNumber'];
	exit();
}

if($in['m']=="PostMsg")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_view'] != 'Y') exit('对不起，您没有此项操作权限！');

	$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
	$client->setOutgoingEncoding("UTF-8");

	if(empty($in['PhoneList']) || empty($in['Msg']))
	{
		exit('发送号码和内容不能为空!');
	}else{
		$in['PhoneList'] = preg_replace("/ {1,}/is", "", $in['PhoneList']);
		$oldphonemsg = $in['PhoneList'];
		$in['Msg']         = str_replace("'","",$in['Msg']);
		$mr = array('&quot;','&rdquo;','&ldquo;');
		$mp = array('"','"','"');
		$in['Msg'] = str_replace($mr, $mp, $in['Msg']);
		$phonearray  = explode(";", $in['PhoneList']);
		for($i=0;$i<count($phonearray);$i++)
		{			
			$phonearray[$i] = trim($phonearray[$i]);
			if(is_phone($phonearray[$i])) $sendphonearray[] = $phonearray[$i]; else $errorphonearray[] = $phonearray[$i];
		}
		$sendphone		  = implode(";",$sendphonearray);
		if(!empty($errorphonearray)) $errorphone = implode(";",$errorphonearray); else $errorphone = "";
		$phonenumber	  = count($phonearray);
		$phonenumberc = count($sendphonearray);
		$errorphonenumber = count($errorphonearray);
		$in['Msg']  = trim($in['Msg']);
		$in['Msg']  = $in['Msg']."【".$_SESSION['uc']['CompanySigned']."】";
		$smsstrlen = countStrLength($in['Msg']);
		if($smsstrlen > 220) exit('信息长度不能超过 220 个字符');
		$shenn = ceil($smsstrlen / 60);
		$sendnum = $shenn * $phonenumberc;

		$smsdatanum = $db->get_row("SELECT CS_ID,CS_SmsNumber FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$_SESSION['uinfo']['ucompany']." limit 0,1");
		if($sendnum > $smsdatanum['CS_SmsNumber']) exit('对不起，您的短信余额不足，请先充值！');

		$balance_begin = $client->getBalance();
		if($phonenumberc > 199)
		{
			$sendphonearraysplit = null;
			for($k=0;$k<$phonenumberc;$k++)
			{
				$sendphonearraysplit[] = $sendphonearray[$k];
				if(count($sendphonearraysplit) > 199 )
				{
					$statusCode = $client->sendSMS($sendphonearraysplit,$in['Msg']);
					$sendphonearraysplit = null;
				}
			}
			if(!empty($sendphonearraysplit)) $statusCode = $client->sendSMS($sendphonearraysplit,$in['Msg']);
		}else{
			$statusCode = $client->sendSMS($sendphonearray,$in['Msg']);
		}
		$balance_end = $client->getBalance();
		$feed = $client->getEachFee();
		$numberend = ($balance_begin-$balance_end)/$feed;
		if($numberend < 0) $numberend = $phonenumberc;
		if($numberend > $sendnum) $numberend = $sendnum;

		$postsql =  "insert into ".DATATABLE."_order_sms_send (PostCompany,PostUser,PostDate,PostPhone,PostNumber,PostErrorPhone,PostErrorNumber,PostSmsCount,PostContent,PostFlag) values(".$_SESSION['uinfo']['ucompany'].",".$_SESSION['uinfo']['userid'].",".time().",'".$sendphone."',".$phonenumberc.",'".$errorphone."',".$errorphonenumber.",".$numberend.", '".$in['Msg']."','".$statusCode."')";
		$db->query($postsql);

		if ($numberend > 0)
		{
			$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber-".$numberend." where CS_Company=".$_SESSION['uinfo']['ucompany']."");
			$smsdatanum = $db->get_row("SELECT CS_ID,CS_SmsNumber FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$_SESSION['uinfo']['ucompany']." limit 0,1");
			$_SESSION['uc']['SmsNumber'] = $smsdatanum['CS_SmsNumber'];	
			exit('ok');
		}else{
			echo " 发送不成功!"."( 代码：".$statusCode." )";
			exit();
		}
	}
}

if($in['m']=="sub_add_phonebook")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$jsmsg = "";
	if(!empty($in['selectphonebook']))
	{
			foreach($in['selectphonebook'] as $cvar)
			{
				if(is_phone($cvar)) $jsmsg .= '<option value=\"'.$cvar.'\">'.$cvar.'</option>';
			}
			$omsg .= '{"backtype":"ok", "htmldata":"'.$jsmsg.'"}';
	}else{
		$omsg .= '{"backtype":"请先选择您要发送的联系人!"}';
	}
	echo $omsg;
	exit();
}

if($in['m']=="sub_add_sort")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$jsmsg = "";
	if(!empty($in['selectclientphonebook']))
	{
		$datasql   = "SELECT ClientMobile FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientMobile!='' and ClientFlag=0  ";
		$numbercol = $db->get_col($datasql);
		foreach($numbercol as $var)
		{
			if(is_phone($var)) $jsmsg .= '<option value=\"'.$var.'\">'.$var.'</option>';
		}
	}
	if(!empty($in['selectsort']))
	{
		if(count($in['selectsort']) > 1)
		{
			$comma_separated = implode(",", $in['selectsort']);
			$datasql   = "SELECT PhoneNumber FROM ".DATATABLE."_order_phonebook where PhoneCompany = ".$_SESSION['uinfo']['ucompany']." and PhoneSort in (".$comma_separated.")  ";
		}else{
			$datasql   = "SELECT PhoneNumber FROM ".DATATABLE."_order_phonebook where PhoneCompany = ".$_SESSION['uinfo']['ucompany']." and PhoneSort=".$in['selectsort'][0]."  ";
		}
		$numbercol = $db->get_col($datasql);
		foreach($numbercol as $var)
		{
			if(is_phone($var)) $jsmsg .= '<option value=\"'.$var.'\">'.$var.'</option>';
		}
	}
	if(!empty($jsmsg))
	{
		$omsg .= '{"backtype":"ok", "htmldata":"'.$jsmsg.'"}';
	}else{
		$omsg .= '{"backtype":"请先选择您要发送的联系人分组!"}';
	}
	echo $omsg;
	exit();
}


/***********save_sort**************/
if($in['m']=="save_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['SortName']))
	{
		if(empty($in['SortOrder'])) $in['SortOrder'] = 0; else $in['SortOrder'] = intval($in['SortOrder']);
		$upsql = "insert into ".DATATABLE."_order_phonebook_sort(SortCompany,SortName,SortOrder) values(".$_SESSION['uinfo']['ucompany'].", '".$in['SortName']."', ".$in['SortOrder'].")";
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
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['SortOrder'])) $in['SortOrder'] = 0;
	if(!empty($in['SortID']))
	{
		if(empty($in['SortName'])) exit('栏目名称不能为空!');
		$upsql = "update ".DATATABLE."_order_phonebook_sort set SortName='".$in['SortName']."',SortOrder=".$in['SortOrder']." where SortCompany = ".$_SESSION['uinfo']['ucompany']." and SortID=".$in['SortID']."";
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
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['SortID']))
	{		
		$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATATABLE."_order_phonebook where PhoneCompany=".$_SESSION['uinfo']['ucompany']." and PhoneSort=".intval($in['SortID'])." ");
		if(!empty($cinfo['lrow'])) exit('对不起，该分组已在使用，不能删除!');

		$upsql = "delete from ".DATATABLE."_order_phonebook_sort where SortCompany = ".$_SESSION['uinfo']['ucompany']." and SortID=".$in['SortID']."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}


if($in['m']=="phonebook_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	if(!empty($in['data_PhoneSort']))
	{
		if(!empty($in['data_PhoneNumber']))
		{
			for($i=0;$i<count($in['data_PhoneNumber']);$i++)
			{
				if(!empty($in['data_PhoneNumber'][$i]) && !empty($in['data_PhoneName'][$i]))
				{
					$upsql = "insert into ".DATATABLE."_order_phonebook(PhoneCompany,PhoneSort,PhoneName,PhoneNumber,PhoneBranch,PhoneUser) values(".$_SESSION['uinfo']['ucompany'].", ".intval($in['data_PhoneSort']).", '".$in['data_PhoneName'][$i]."', '".$in['data_PhoneNumber'][$i]."', '".$in['data_PhoneBranch'][$i]."', '".$_SESSION['uinfo']['username']."')";
					$db->query($upsql);
				}
			}
			exit('ok');
		}else{
			exit("您没有填写任何联系人信息!");
		}
	}else{
		exit('请先选择所属联系人分组!');
	}
}

if($in['m']=="phonebook_delete")
{
	if(!intval($in['ID'])) exit('参数错误!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "delete from ".DATATABLE."_order_phonebook  where PhoneID = ".$in['ID']." and PhoneCompany=".$_SESSION['uinfo']['ucompany']." limit 1";	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


if($in['m']=="edit_phonebook_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['data_TemplateSort'])) exit('参数错误!');

	if(!empty($in['data_PhoneSort']) && !empty($in['data_PhoneName']))
	{
		$in['data_PhoneNumber'] = trim($in['data_PhoneNumber']);
		if(!is_phone($in['data_PhoneNumber'])) exit('请输入正确的手机号!');
		$upsql = "update ".DATATABLE."_order_phonebook set PhoneSort=".$in['data_PhoneSort'].",PhoneName='".$in['data_PhoneName']."',PhoneName='".$in['data_PhoneName']."' ,PhoneNumber='".$in['data_PhoneNumber']."',PhoneBranch='".$in['data_PhoneBranch']."',PhoneUser='".$_SESSION['uinfo']['username']."'  where PhoneCompany = ".$_SESSION['uinfo']['ucompany']." and PhoneID=".intval($in['data_PhoneID'])."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("无变化，没修改任何内容!");
		}
	}else{
		exit('分组和联系人不能为空!');
	}
}


if($in['m']=="del_phonebook_arr")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	$deleteidmsg = implode(",", $in['selectedID']);

	$upsql =  "delete from ".DATATABLE."_order_phonebook where PhoneID IN ( ".$deleteidmsg." ) and PhoneCompany=".$_SESSION['uinfo']['ucompany']." ";
	
	if($db->query($upsql))
	{
		Error::Alert('删除成功!',$in['referer']);
	}else{
		Error::AlertJs('删除不成功!');
	}
}

/***********template save_sort**************/
if($in['m']=="save_template_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['SortName']))
	{
		if(empty($in['SortOrder'])) $in['SortOrder'] = 0; else $in['SortOrder'] = intval($in['SortOrder']);
		$upsql = "insert into ".DATATABLE."_order_template_sort(SortCompany,SortName,SortOrder) values(".$_SESSION['uinfo']['ucompany'].", '".$in['SortName']."', ".$in['SortOrder'].")";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

if($in['m']=="save_edit_template_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['SortOrder'])) $in['SortOrder'] = 0;
	if(!empty($in['SortID']))
	{
		if(empty($in['SortName'])) exit('栏目名称不能为空!');
		$upsql = "update ".DATATABLE."_order_template_sort set SortName='".$in['SortName']."',SortOrder=".$in['SortOrder']." where SortCompany = ".$_SESSION['uinfo']['ucompany']." and SortID=".$in['SortID']."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("无变化，没修改任何内容!");
		}
	}
}


if($in['m']=="delete_template_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['SortID']))
	{
		$upsql = "delete from ".DATATABLE."_order_template_sort where SortCompany = ".$_SESSION['uinfo']['ucompany']." and SortID=".$in['SortID']."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}


if($in['m']=="template_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	if(!empty($in['data_TemplateSort']))
	{
		if(!empty($in['data_TemplateContent']))
		{
			for($i=0;$i<count($in['data_TemplateContent']);$i++)
			{
				if(!empty($in['data_TemplateContent'][$i]))
				{
					$upsql = "insert into ".DATATABLE."_order_sms_template(TemplateCompany,TemplateSort,TemplateContent,TemplateUser) values(".$_SESSION['uinfo']['ucompany'].", ".intval($in['data_TemplateSort']).", '".$in['data_TemplateContent'][$i]."', '".$_SESSION['uinfo']['username']."')";
					$db->query($upsql);
				}
			}
			exit('ok');
		}else{
			exit("您没有填写任何模板内容!");
		}
	}else{
		exit('请先选择所属模板分类!');
	}
}

if($in['m']=="template_delete")
{
	if(!intval($in['ID'])) exit('参数错误!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "delete from ".DATATABLE."_order_sms_template  where TemplateID = ".$in['ID']." and TemplateCompany=".$_SESSION['uinfo']['ucompany']." limit 1";	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

if($in['m']=="edit_template_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	if(empty($in['data_TemplateSort'])) exit('参数错误!');
	if(!empty($in['data_TemplateSort']) && !empty($in['data_TemplateContent']))
	{
		$upsql = "update ".DATATABLE."_order_sms_template set TemplateSort=".$in['data_TemplateSort'].",TemplateContent='".$in['data_TemplateContent']."',TemplateUser='".$_SESSION['uinfo']['username']."'  where TemplateCompany = ".$_SESSION['uinfo']['ucompany']." and TemplateID=".intval($in['data_TemplateID'])."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("无变化，没修改任何内容!");
		}
	}else{
		exit('分类和模板内容不能为空!');
	}
}


if($in['m']=="del_template_arr")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	$deleteidmsg = implode(",", $in['selectedID']);

	$upsql =  "delete from ".DATATABLE."_order_sms_template where TemplateID IN ( ".$deleteidmsg." ) and TemplateCompany=".$_SESSION['uinfo']['ucompany']." ";
	
	if($db->query($upsql))
	{
		Error::Alert('成功删除!',$in['referer']);
	}else{
		Error::AlertJs('删除不成功!');
	}
}

exit('非法操作!');
?>