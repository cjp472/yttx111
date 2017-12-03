<?php
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");

$input		=	new Input;
$in			=	$input->parse_incoming();

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

if($in['m']=="feedback_add_save")
{
	$in['type']   = Functions::text($in['type']);
	$in['title'] = Functions::text($in['title']);
	$in['message']   = Functions::strip($in['message']);

	if(!$in['type']) exit('反馈类型不能为空！');
	if(!$in['title']) exit('手机号不能为空！');
	if(!$in['message']) exit('反馈内容不能为空！');
	if(strlen($in['title'])>30) exit('手机号太长！');
	if(strlen($in['message'])>500) exit('反馈内容太长！');
	
	$upsql = "insert into ".DATABASEU.DATATABLE."_common_feedback(CompanyID,ClientID,FeedbackType,ClientName,Contact,Content,CreateDate,Type) values({$_SESSION['cc']['ccompany']}, {$_SESSION['cc']['cid']}, '{$in['type']}', '{$_SESSION['cc']['cusername']}', '{$in['title']}', '{$in['message']}' ,".time().",'C' )";

	$db = dbconnect::dataconnect()->getdb();
	
	if($db->query($upsql))
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="contact_add_save")
{
	$ip = RealIp();
	
	$in['Name']   = Functions::text($in['Name']);
	$in['Phone'] = Functions::text($in['Phone']);

	if(!$in['Name']) exit('手机号码不能为空！');
	if(!$in['Phone']) exit('联系名字不能为空！');
	//if(strlen($in['Name'])>12) exit('名字不能超过4个汉字！');
	if(strlen($in['Phone'])>11) exit('手机号码不能超过11位！');

	$upsql = "insert into ".DATABASEU.DATATABLE."_experience_contact(ContactName,Phone,Date,Status,Remark,CompanyID,IP,Industry,VisitType)
	values('{$in['Name']}','{$in['Phone']}', ".time().", '0', '','{$_SESSION['cc']['ccompany']}','{$ip}','".getSafeIndustry($_SESSION['industry'])."','PC' )";

	$db = dbconnect::dataconnect()->getdb();
	
	if($db->query($upsql))
	{
		setcookie('experience_contact', encodeData($ip), time() + 86400, "/" ,substr(M_SITE,strpos(M_SITE,'.')+1)); 
		
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="contact_add_save2")
{
	$ip = RealIp();
	setcookie('experience_contact', encodeData($ip), time() + 86400, "/" ,substr(M_SITE,strpos(M_SITE,'.')+1)); 
	exit('ok');
}

if($in['m']=="contact_add_save_un")
{
	setcookie('experience_contact', null, time() - 864000000, "/" ,substr(M_SITE,strpos(M_SITE,'.')+1));
	if(!empty($_COOKIE['experience_contact'])){
		unset($_COOKIE['experience_contact']);
	}
	setcookie('experience_contact_un', 1, time() + 3600, "/" ,DHB_RUNTIME_MODE === 'experience' ? substr(M_SITE,strpos(M_SITE,'.')+1) : '');
	exit('ok');
}

exit('非法操作!');
?>