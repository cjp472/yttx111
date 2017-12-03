<?php
$menu_flag = "system";
include_once ("header.php");
include_once ("../class/letter.class.php");
include_once ("../class/data.class.php");
include_once ("arr_data.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m'] == 'content_edit_company_save') {
    $status = false;
    //完善账套资料
    $company_id = $_SESSION['uc']['CompanyID'];
    $in['ID'] = $company_id = $_SESSION['uc']['CompanyID'];

    $in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));

    if(!empty($in['data_CompanyPrefix'])){
        $exists = $db->get_row("SELECT CompanyId FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".$in['data_CompanyPrefix']."' limit 0,1");
		if($exists > 0) exit('账号前缀已存在');
    }
    $infoc = $db->get_row("SELECT CompanyName FROM ".DATABASEU.DATATABLE."_order_company where CompanyID='".$in['ID']."' limit 0,1");

    $upsql = "update ".DATABASEU.DATATABLE."_order_company set CompanyArea ='".$in['data_CompanyArea']."', CompanyIndustry ='".$in['data_CompanyIndustry']."',CompanyAgent=".intval($in['data_CompanyAgent']).", CompanyName='".$in['data_CompanyName']."',CompanySigned='".$in['data_CompanySigned']."', CompanyCity='".$in['data_CompanyCity']."', CompanyContact='".$in['data_CompanyContact']."', CompanyPhone='".$in['data_CompanyPhone']."', CompanyFax='".$in['data_CompanyFax']."', CompanyAddress='".$in['data_CompanyAddress']."', CompanyEmail='".$in['data_CompanyEmail']."', CompanyWeb='".$in['data_CompanyWeb']."', CompanyUrl='".$in['data_CompanyUrl']."', CompanyRemark='".$in['data_CompanyRemark']."' where CompanyID=".$in['ID'];

    $update  = $db->query($upsql);
    $info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company='".$in['ID']."' limit 0,1");

    if($update > 0)
    {
		//标识生成账户已经完善资料
        $content = '';
        if($infoc['CompanyName'] != $in['data_CompanyName']) $content .= '修改了公司名称从 ‘'.$infoc['CompanyName'].'’到‘'.$in['data_CompanyName'].'’.\n\r';
        if(!empty($content)) $status = ESaveCompanyLog($in['ID'],$content);
        exit("ok");
    }else{
        exit("资料无变化!");
    }

}

//云平台开通首次登陆的账号时需要完善资料
if($in['m'] == 'content_edit_alimerchant_save') {
    //完善账套资料
    $company_id = $_SESSION['uc']['CompanyID'];
    if(empty($in['data_CompanyPrefix'])){
        exit("个性域名不能为空");
    }
    $in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));
    if(!is_prefix($in['data_CompanyPrefix'])){
        exit('个性化域名请在3-10位数字字母之间');
    }
    if(!is_numeric($in['data_CompanyMobile']) || strlen($in['data_CompanyMobile']) != 11){
        exit('手机号码格式不正确');
    }


    //前缀是否重复
    $exists = $db->get_row("SELECT CompanyId FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".mysql_escape_string($in['data_CompanyPrefix'])."' limit 0,1");
    if($exists['CompanyId']) exit('账号前缀已存在');

	$CompanyType = $db->get_row("SELECT CompanyType FROM ".DATABASEU.DATATABLE."_order_company where CompanyId='".$company_id."' limit 0,1");

    $ip = hcget_client_ip();
    $companyIniSql = "update ".DATABASEU.DATATABLE."_order_company set CompanyArea ='".intval($in['data_CompanyArea'])."', CompanyIndustry ='".intval($in['data_CompanyIndustry'])."',CompanyAgent=0, CompanyName='".$in['data_CompanyName']."',CompanySigned='".$in['data_CompanySigned']."', CompanyContact='".$in['data_CompanyContact']."', CompanyMobile='".$in['data_CompanyMobile']."',CompanyPhone='".$in['data_CompanyMobile']."',CompanyPrefix = '".$in['data_CompanyPrefix']."' , CompanyFlag='0' where CompanyID=".$company_id;
    $buyCtSql = "update ".DATABASEU.DATATABLE."_buy_account set mobile ='{$in['data_CompanyMobile']}',prefix ='{$in['data_CompanyPrefix']}', name='{$in['data_CompanyContact']}',industry='".intval($in['data_CompanyIndustry'])."',platform_name='{$in['data_CompanyName']}',ip='{$ip}' where company_id=".$company_id;

    $rs_company = $db->query($companyIniSql);
    $db->query($buyCtSql);

    if($rs_company > 0)
    {
		//标识生成账户已经完善资料
        $_SESSION['uc']['CompanyFlag'] = '0';
        $_SESSION['uc']['CompanyPrefix'] = $in['data_CompanyPrefix'];
		$marktType = array('ali'=>'阿里云','shuan'=>'曙安CV3');
        ESaveCompanyLog($company_id,$marktType[$CompanyType['CompanyType']].'首次完善资料');
        exit("ok");
    }else{
        exit("资料无变化!");
    }
}


//苏宁,寄云云平台开通首次登陆的账号时需要完善资料
if($in['m'] == 'content_edit_suningmerchant_save'){
    //完善账套资料
    $company_id = $_SESSION['uc']['CompanyID'];
	
	$in['UserName']	= trim(strtolower($in['data_UserName']));
	if(!is_filename($in['UserName'])) exit('okname');
	if(strlen($in['UserName']) < 3 || strlen($in['UserName']) > 18 ) exit('okname');
	

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserID <> ".$_SESSION['uinfo']['userid']." and UserName='".$in['UserName']."' limit 0,1");
	if($clientinfo['orwname']) exit('repeat');//判断重复

	$newpass = strtolower($in['data_NewPass']);
	$npsmsg = ChangeMsg($in['UserName'],$newpass);

	if(!is_filename($newpass)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($newpass) < 3 || strlen($newpass) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');

	$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."',UserPass='".$npsmsg."' where UserID=".$_SESSION['uinfo']['userid']." and UserCompany=".$_SESSION['uinfo']['ucompany']."";
	$db->query($upsql);

	if($in['companytype']=="netcloud"){
		$editsql="update ".DATABASEU.DATATABLE."_order_netcloudinfo set editpass='1' where CompanyID=".$_SESSION['uinfo']['ucompany'].""; //更新是否修改密码
	}else{
		$editsql="update ".DATABASEU.DATATABLE."_order_suninginfo set editpass='1' where CompanyID=".$_SESSION['uinfo']['ucompany'].""; //更新是否修改密码
	}

	$db->query($editsql);

    if(empty($in['data_CompanyPrefix'])){
        exit("个性域名不能为空");
    }
    $in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));
    if(!is_prefix($in['data_CompanyPrefix'])){
        exit('个性化域名请在3-10位数字字母之间');
    }
    if(!is_numeric($in['data_CompanyMobile']) || strlen($in['data_CompanyMobile']) != 11){
        exit('手机号码格式不正确');
    }

    //前缀是否重复
    $exists = $db->get_row("SELECT CompanyId FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".mysql_escape_string($in['data_CompanyPrefix'])."' limit 0,1");
    if($exists['CompanyId']) exit('账号前缀已存在');

	$CompanyType = $db->get_row("SELECT CompanyType FROM ".DATABASEU.DATATABLE."_order_company where CompanyId='".$company_id."' limit 0,1");

    $ip = hcget_client_ip();
    $companyIniSql = "update ".DATABASEU.DATATABLE."_order_company set CompanyArea ='".intval($in['data_CompanyArea'])."', CompanyIndustry ='".intval($in['data_CompanyIndustry'])."',CompanyAgent=0, CompanyName='".$in['data_CompanyName']."',CompanySigned='".$in['data_CompanySigned']."', CompanyContact='".$in['data_CompanyContact']."', CompanyMobile='".$in['data_CompanyMobile']."',CompanyPhone='".$in['data_CompanyMobile']."',CompanyPrefix = '".$in['data_CompanyPrefix']."' , CompanyFlag='0' where CompanyID=".$company_id;
    $buyCtSql = "update ".DATABASEU.DATATABLE."_buy_account set mobile ='{$in['data_CompanyMobile']}',prefix ='{$in['data_CompanyPrefix']}', name='{$in['data_CompanyContact']}',industry='".intval($in['data_CompanyIndustry'])."',platform_name='{$in['data_CompanyName']}',ip='{$ip}' where company_id=".$company_id;

    $rs_company = $db->query($companyIniSql);
    $db->query($buyCtSql);

	$_SESSION['uinfo']['username']= $in['UserName'];

    if($rs_company > 0)
    {
		//标识生成账户已经完善资料
        $_SESSION['uc']['CompanyFlag'] = '0';
        $_SESSION['uc']['CompanyPrefix'] = $in['data_CompanyPrefix'];
		$marktType = array('ali'=>'阿里云','shuan'=>'曙安CV3',"suning"=>"苏宁");
        ESaveCompanyLog($company_id,$marktType[$CompanyType['CompanyType']].'首次完善资料');
        exit("ok");
    }else{
        exit("资料无变化!");
    }
}



//微信企业号开通首次登陆的账号时需要完善资料
if($in['m'] == 'content_edit_wxqymerchant_save'){
    //完善账套资料
    $company_id = $_SESSION['uc']['CompanyID'];
	
	$in['UserName']	= trim(strtolower($in['data_UserName']));
	if(!is_filename($in['UserName'])) exit('okname');
	if(strlen($in['UserName']) < 3 || strlen($in['UserName']) > 18 ) exit('okname');
	

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserID <> ".$_SESSION['uinfo']['userid']." and UserName='".$in['UserName']."' limit 0,1");
	if($clientinfo['orwname']) exit('repeat');//判断重复

	$newpass = strtolower($in['data_NewPass']);
	$npsmsg = ChangeMsg($in['UserName'],$newpass);

	if(!is_filename($newpass)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($newpass) < 3 || strlen($newpass) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');

	$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."',UserPass='".$npsmsg."' where UserID=".$_SESSION['uinfo']['userid']." and UserCompany=".$_SESSION['uinfo']['ucompany']."";

	$editsql="update ".DATABASEU.DATATABLE."_order_weixinqy set editpass='1',UserName='".$in['UserName']."' where CompanyID=".$_SESSION['uinfo']['ucompany'].""; //更新是否修改密码
	$db->query($upsql);
	$db->query($editsql);

    if(empty($in['data_CompanyPrefix'])){
        exit("个性域名不能为空");
    }
    $in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));
    if(!is_prefix($in['data_CompanyPrefix'])){
        exit('个性化域名请在3-10位数字字母之间');
    }
    if(!is_numeric($in['data_CompanyMobile']) || strlen($in['data_CompanyMobile']) != 11){
        exit('手机号码格式不正确');
    }

    //前缀是否重复
    $exists = $db->get_row("SELECT CompanyId FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".mysql_escape_string($in['data_CompanyPrefix'])."' limit 0,1");
    if($exists['CompanyId']) exit('账号前缀已存在');

	$CompanyType = $db->get_row("SELECT CompanyType FROM ".DATABASEU.DATATABLE."_order_company where CompanyId='".$company_id."' limit 0,1");

    $ip = hcget_client_ip();
    $companyIniSql = "update ".DATABASEU.DATATABLE."_order_company set CompanyArea ='".intval($in['data_CompanyArea'])."', CompanyIndustry ='".intval($in['data_CompanyIndustry'])."',CompanyAgent=0, CompanyName='".$in['data_CompanyName']."',CompanySigned='".$in['data_CompanySigned']."', CompanyContact='".$in['data_CompanyContact']."', CompanyMobile='".$in['data_CompanyMobile']."',CompanyPhone='".$in['data_CompanyMobile']."',CompanyPrefix = '".$in['data_CompanyPrefix']."' , CompanyFlag='0' where CompanyID=".$company_id;
    $buyCtSql = "update ".DATABASEU.DATATABLE."_buy_account set mobile ='{$in['data_CompanyMobile']}',prefix ='{$in['data_CompanyPrefix']}', name='{$in['data_CompanyContact']}',industry='".intval($in['data_CompanyIndustry'])."',platform_name='{$in['data_CompanyName']}',ip='{$ip}' where company_id=".$company_id;

    $rs_company = $db->query($companyIniSql);
    $db->query($buyCtSql);

	$_SESSION['uinfo']['username']= $in['UserName'];

    if($rs_company > 0)
    {
		//标识生成账户已经完善资料
        $_SESSION['uc']['CompanyFlag'] = '0';
        $_SESSION['uc']['CompanyPrefix'] = $in['data_CompanyPrefix'];
		$marktType = array('ali'=>'阿里云','shuan'=>'曙安CV3',"suning"=>"苏宁","netcloud"=>"寄云","wxqy"=>"微信企业号");
        ESaveCompanyLog($company_id,$marktType[$CompanyType['CompanyType']].'首次完善资料');
        exit("ok");
    }else{
        exit("资料无变化!");
    }
}



if($in['m'] == 'do_company_upload') {
    $company_id = $_SESSION['uc']['CompanyID'];
    $cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");
    if($cs_info['CS_Flag'] == 'T') {
        //有上传资料且已审核 不允许再修改
        exit('非法修改资料!');
    }

    $data_sql = "REPLACE INTO ".DATABASEU.DATATABLE."_order_company_data (CompanyID,BusinessCard,BusinessCardImg,IDCard,IDCardImg,BusinessName,IDLicenceImg,GPImg,IDLicence,IDGP) VALUES ({$company_id},'{$in['data_BusinessCard']}','{$in['data_BusinessCardImg']}','{$in['data_IDCard']}','{$in['data_IDCardImg']}','{$in['data_BusinessName']}','{$in['data_IDLicenceImg']}','{$in['data_GPImg']}','{$in['data_IDLicence']}','{$in['data_IDGP']}')";

    $result = $db->query($data_sql);
    if($result !== false) {
        //重新上传资料后将审核状态置为待审状态
        $db->query("UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_Flag='D' where CS_Company={$company_id} LIMIT 1");
        exit('ok');
    } else {
        exit('资料保存失败,请重试!');
    }
}

if($in['m']=="delete")
{
	if(!intval($in['ID'])) exit('非法参数!');

	$upsql =  "update ".DATABASEU.DATATABLE."_order_user set UserFlag='1' where UserID = ".$in['ID']." and UserFlag!='9' and UserCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=delete&ID=".$in['ID']."','删除用户(".$in['ID'].")','-',".time().")";
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

	$upsql =  "update ".DATABASEU.DATATABLE."_order_user set UserFlag='0' where UserID = ".$in['ID']." and UserCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=restore&ID=".$in['ID']."','恢复用户(".$in['ID'].")','-',".time().")";
		$db->query($sqlex);
		exit('ok');
	}else{
		exit('还原不成功!');
	}
}

if($in['m']=="quite_delete")
{
	if(!intval($in['ID'])) exit('非法参数!');
	
	$upsql =  "delete from ".DATABASEU.DATATABLE."_order_user where UserID = ".$in['ID']." and UserFlag!='9' and UserCompany=".$_SESSION['uinfo']['ucompany'];
	
	$InfoData = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$in['ID']." and UserCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$infodatamsg = serialize($InfoData);

	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=quite_delete&ID=".$in['ID']."','彻底删除用户(".$in['ID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		$db->query("delete from ".DATABASEU.DATATABLE."_order_pope where pope_company=".$_SESSION['uinfo']['ucompany']." and pope_user=".$in['ID']);
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


/***********save user**************/
if($in['m']=="content_add_save")
{
	$in['UserName']	= trim(strtolower($in['data_UserName']));
	$in['UserPass']		= trim(strtolower($in['data_UserPass']));	

	if(!is_filename($in['UserName'])) exit('okname');
	if(strlen($in['UserName']) < 3 || strlen($in['UserName']) > 18 ) exit('okname');

	if(!is_filename($in['UserPass'])) exit('okpass');
	if(strlen($in['UserPass']) < 3 || strlen($in['UserPass']) > 18 ) exit('okpass');
	
	if(!is_phone($in['data_UserMobile'])) exit('okmobile');
	if(strlen($in['data_UserMobile']) != 11) exit('okmobile');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserName='".$in['UserName']."'");
	if(!empty($clientinfo['orwname'])) exit('repeat');
	
	$passmsg = ChangeMsg($in['UserName'],$in['UserPass']);
	
	//wangkk 2017-11-28 增加管理员用户
    $upsql = "insert into ".DATABASEU.DATATABLE."_order_user(UserName,UserPass,UserCompany,UserTrueName,UserPhone,UserMobile,UserDate,UserRemark,UserFlag) values('".$in['UserName']."','".$passmsg."',".$_SESSION['uinfo']['ucompany'].",'".$in['data_UserTrueName']."','".$in['data_UserPhone']."', '".$in['data_UserMobile']."', ".time().",'".$in['data_UserRemark']."','".$in['UserFlag']."')";
    if($db->query($upsql))
	{
        $uid = mysql_insert_id();
        //wangkk 2017-11-28 获取添加用户的ID
        $UserSql = "select UserID from ".DATABASEU.DATATABLE."_order_user where UserName = '".$in['UserName']."'";
        $UserSel = $db->get_row($UserSql);
        //代理商增加商品管理
        if($in['UserFlag'] == 2){
            $AgentID = substr($in['Shield'],0,strlen($in['Shield'])-1);
            if(!empty($in['Shield'])){
                $AgentSql = "update ".DATATABLE."_order_content_index set AgentID = ".$UserSel['UserID']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID in(".$AgentID.")";
                $AgentInt = $db->query($AgentSql);
            }  
        }
		foreach($pope_arr as $pkey=>$pvar)
		{
			if($pkey == "system") continue;
			if(empty($in['view_'.$pkey])) $in['view_'.$pkey] = 'N';
			if(empty($in['form_'.$pkey])) $in['form_'.$pkey] = 'N';
			if(empty($in['audi_'.$pkey])) $in['audi_'.$pkey] = 'N';
			$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$uid.",'".$pkey."','".$in['view_'.$pkey]."','".$in['form_'.$pkey]."','".$in['audi_'.$pkey]."')");                        
                }
		$infodatamsg = serialize($in);
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=content_add_save','添加用户(".$uid.")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

/***********editsave**************/
if($in['m']=="content_edit_save")
{
	$in['UserName'] = trim(strtolower($in['data_UserName']));
	$in['UserPass']   = trim(strtolower($in['data_UserPass']));	

	if(empty($in['UserID'])) exit('参数错误!');
	if(!is_filename($in['UserName'])) exit('okname');
	if(strlen($in['UserName']) < 3 || strlen($in['UserName']) > 18 ) exit('okname');
	
	if(!is_phone($in['data_UserMobile'])) exit('okmobile');
	if(strlen($in['data_UserMobile']) != 11) exit('okmobile');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user where UserID <> ".$in['UserID']." and UserName='".$in['UserName']."' limit 0,1");
	if($clientinfo['orwname']) exit('repeat');

	if(empty($in['UserPass']))
	{
		$clientflag = $db->get_row("SELECT UserID,UserName FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserID='".$in['UserID']."' limit 0,1");
		if($in['UserName'] != $clientflag['UserName']) exit('请填写您的密码！修改帐号时需要填写您的密码！');

		$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."', UserTrueName='".$in['data_UserTrueName']."', UserPhone='".$in['data_UserPhone']."', UserMobile='".$in['data_UserMobile']."', UserRemark='".$in['data_UserRemark']."' where UserID=".$in['UserID']." and UserCompany=".$_SESSION['uinfo']['ucompany'];
	}else{
		if(!is_filename($in['UserPass'])) exit('okpass');
		if(strlen($in['UserPass']) < 3 || strlen($in['UserPass']) > 18 ) exit('okpass');
		$passmsg = ChangeMsg($in['UserName'],$in['UserPass']);

		$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserName='".$in['UserName']."',UserPass='".$passmsg."', UserTrueName='".$in['data_UserTrueName']."', UserPhone='".$in['data_UserPhone']."', UserMobile='".$in['data_UserMobile']."', UserRemark='".$in['data_UserRemark']."' where UserID=".$in['UserID']." and UserCompany=".$_SESSION['uinfo']['ucompany'];
	}
	$db->query($upsql);
        
        //商品关系
        	$db->query("update ".DATATABLE."_order_content_index set AgentID= '0' where CompanyID=".$_SESSION['uinfo']['ucompany']." and AgentID=".$in['UserID']);
                if(!empty($in['Shield']))
		{
			$shieldarr1 = substr($in['Shield'],0,strlen($in['Shield'])-1);			
			$db->query("update ".DATATABLE."_order_content_index set AgentID = ".$in['UserID']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID in(".$shieldarr1.")");                        
                }
        
        
	$clientflag = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserID='".$in['UserID']."' limit 0,1");

	if($clientflag['UserID'] == $_SESSION['uinfo']['userid'])
	{
		$_SESSION['uinfo']['username']			= $clientflag['UserName'];
		$_SESSION['uinfo']['usertruename']  = $clientflag['UserTrueName'];
	}

	if($clientflag['UserFlag']!="9")
	{
		$db->query("delete from ".DATABASEU.DATATABLE."_order_pope where pope_company=".$_SESSION['uinfo']['ucompany']." and pope_user=".$in['UserID']);
		foreach($pope_arr as $pkey=>$pvar)
		{			
			if($pkey == "system") continue;
			if(empty($in['view_'.$pkey])) $in['view_'.$pkey]  = 'N';
			if(empty($in['form_'.$pkey])) $in['form_'.$pkey] = 'N';
			if(empty($in['audi_'.$pkey])) $in['audi_'.$pkey]   = 'N';
			$db->query("insert into ".DATABASEU.DATATABLE."_order_pope(pope_company,pope_user,pope_module,pope_view,pope_form,pope_audit) values(".$_SESSION['uinfo']['ucompany'].",".$in['UserID'].",'".$pkey."','".$in['view_'.$pkey]."','".$in['form_'.$pkey]."','".$in['audi_'.$pkey]."')");
		}
	}
	$infodatamsg = serialize($clientflag);
	$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=content_add_save','修改用户(".$in['UserID'].")','".$infodatamsg."',".time().")";
	$db->query($sqlex);
	exit("ok");
}


/***********company info**************/
if($in['m']=="companyinfo_edit_save")
{	
	if(!empty($_SESSION['uinfo']['ucompany']))
	{
		$upsql = "update ".DATABASEU.DATATABLE."_order_company set CompanyContact='".$in['CompanyContact']."',CompanyMobile='".$in['CompanyMobile']."', CompanyPhone='".$in['CompanyPhone']."', CompanyFax='".$in['CompanyFax']."', CompanyAddress='".$in['CompanyAddress']."', CompanyEmail='".$in['CompanyEmail']."', CompanyRemark='".$in['CompanyRemark']."' where CompanyID=".$_SESSION['uinfo']['ucompany']."";

		$InfoData = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
		$infodatamsg = serialize($InfoData);
		if($db->query($upsql))
		{
			$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=companyinfo_edit_save','修改公司资料','".$infodatamsg."',".time().")";
			$db->query($sqlex);		
			
			exit("ok");
		}else{
			exit("没有任何变动!");
		}
	}else{
		exit('参数错误!');
	}
}


/*********** set **************/
if($in['m']=="update_settype")
{	
	$valuemsg = '';
	if($in['at'] == "send" && !empty($in['sendtypeID']))
	{	
		$valuemsg = serialize($in['sendtypeID']);
		$updatetype = "发货方式";
	}
	else if($in['at'] == "pay" && !empty($in['paytypeID']))
	{
		$valuemsg = serialize($in['paytypeID']);
		$updatetype = "付款方式";
	}
	else if($in['at'] == "sms")
	{
		$smsarr = $in['smstypeID'];
        //允许多个手机号，对每个手机号进行验证
        if(!empty($in['MainPhone'])) {
            $MainPhones = array_unique(array_filter(explode(",",$in['MainPhone'])));
            $MainPhoneSucc = array();
            foreach($MainPhones as $phone) {
                if(is_phone($phone)) {
                    $MainPhoneSucc[] = $phone;
                }
            }
            $smsarr['Mobile']['MainPhone'] = implode(",",$MainPhoneSucc);
        }

        if(!empty($in['FinancePhone'])) {
            $FinancePhones = array_unique(array_filter(explode(",",$in['FinancePhone'])));
            $FinancePhoneSucc = array();
            foreach($FinancePhones as $phone) {
                if(is_phone($phone)) {
                    $FinancePhoneSucc[] = $phone;
                }
            }
            $smsarr['Mobile']['FinancePhone'] = implode(",",$FinancePhoneSucc);
        }

        if(!empty($in['LibaryPhone'])) {
            $LibaryPhone = array_unique(array_filter(explode(",",$in['LibaryPhone'])));
            $LibaryPhoneSucc = array();
            foreach($LibaryPhone as $phone) {
                if(is_phone($phone)) {
                    $LibaryPhoneSucc[] = $phone;
                }
            }
            $smsarr['Mobile']['LibaryPhone']  = implode(",",$LibaryPhoneSucc);
        }


		/*if(!empty($in['MainPhone']) && is_phone($in['MainPhone']))
            $smsarr['Mobile']['MainPhone']    = $in['MainPhone'];
		if(!empty($in['FinancePhone']) && is_phone($in['FinancePhone']))
            $smsarr['Mobile']['FinancePhone'] = $in['FinancePhone'];
		if(!empty($in['LibaryPhone']) && is_phone($in['LibaryPhone']))
            $smsarr['Mobile']['LibaryPhone']  = $in['LibaryPhone'];*/
		$valuemsg = serialize($smsarr);
		$updatetype = "短信通知";
	}
	else if($in['at'] == "product")
	{
		if(!empty($in['checkandapprove'])) $proarr['checkandapprove']	    = $in['checkandapprove'];
		if(!empty($in['producttype'])) $proarr['producttype']				= $in['producttype'];
		if(!empty($in['producttype_number'])) $proarr['product_number']		= $in['producttype_number'];
		if(!empty($in['producttype_negative'])) $proarr['product_negative'] = $in['producttype_negative'];
		if(!empty($in['producttype_number_show'])) $proarr['product_number_show'] = $in['producttype_number_show'];
		if(!empty($in['return_type'])) $proarr['return_type']	= $in['return_type'];
		if(!empty($in['deduct_type'])) $proarr['deduct_type']	= $in['deduct_type'];
		if(!empty($in['audit_type'])) $proarr['audit_type']		= $in['audit_type'];
		if(!empty($in['regiester_type'])) $proarr['regiester_type'] = $in['regiester_type'];
        if(!isset($in['regiester_type_status'])) {
            $in['regiester_type_status'] = 9;
        }
        $proarr['regiester_type_status'] = $in['regiester_type_status'];
		if(!empty($in['invoice_type_p'])){ $proarr['invoice_p'] = 'Y'; $proarr['invoice_p_tax'] = intval($in['taxingpppoint_p']); }
		if(!empty($in['invoice_type_z'])){ $proarr['invoice_z'] = 'Y'; $proarr['invoice_z_tax'] = intval($in['taxingpppoint_z']); }
		if(!empty($in['delivery_time'])) $proarr['delivery_time'] = $in['delivery_time'];
        $proarr['show_money'] = $in['show_money'];
        $product_price = array();
        $product_price['price1_show'] = $in['producttype_price1_show'] ? 'on' : 'off';
        $product_price['price1_name'] = $in['producttype_price1_name'] ? $in['producttype_price1_name'] : '价格一';
        $product_price['price2_show'] = $in['producttype_price2_show'] ? 'on' : 'off';
        $product_price['price2_name'] = $in['producttype_price2_name'] ? $in['producttype_price2_name'] : '价格二';

        $proarr['product_price'] = $product_price;

        //满省设置
        $stair = $_POST['stair'] ? $_POST['stair'] : array();
        $proarr['stair_status'] = $in['stair_status'];
        //TODO::验证满省
        sort($stair);
        $proarr['stair'] = $stair;

        $order_time = array();
        $order_time['time_show'] = empty($in['order_time']) ? 'off' : $in['order_time'];
        if($order_time['time_show'] == 'on'){
            if(empty($in['ordertime_datestart']) || empty($in['ordertime_dateend']) || !isset($in['ordertime_datestart']) || !isset($in['ordertime_dateend']))
            { 
                Error::AlertJs('工作日输入不正确!');
		        exit('参数错误!');
            }else if(empty($in['ordertime_timestart']) || empty($in['ordertime_timeend']) || !isset($in['ordertime_timestart']) || !isset($in['ordertime_timeend'])){
                Error::AlertJs('工作时间输入不正确!');
                exit('参数错误!');
            }else if(!checkDatetime($in['ordertime_timestart']) || !checkDatetime($in['ordertime_timeend'])){
                Error::AlertJs('工作时间格式不正确!');//
                exit('参数错误!');
            }else if($in['ordertime_datestart'] == $in['ordertime_dateend']){
                $v = (strtotime('2000/1/1 '.$in['ordertime_timeend']) - strtotime('2000/1/1 '.$in['ordertime_timestart']))/60;

                if($v < 0){
                    Error::AlertJs('同一天开始时间不能小于结束时间！');
                    exit('参数错误!');
                }else if($v < 30){
                    Error::AlertJs("同一天工作时间不能小于30分钟！");
                    exit('参数错误!');
                }
            }           
        }

        $order_time['date_start'] = $in['ordertime_datestart'];
        $order_time['date_end'] = $in['ordertime_dateend'];
        $order_time['time_start'] = $in['ordertime_timestart'];
        $order_time['time_end'] = $in['ordertime_timeend'];
        $proarr['ordertime'] = $order_time;
    
		$valuemsg   = serialize($proarr);
		$updatetype = "模式设置";
	}
    else if($in['at']=='erp') {
        $erp = array();
        if(!empty($in['erp_interface'])) { $erp['erp_interface'] = $in['erp_interface']; }
        if(!empty($in['erp_order_check'])) { $erp['erp_order_check'] = $in['erp_order_check']; }
        $_SESSION['uc']['ERP'] = $in['erp_interface'] == "Y" ? "ON" : "OFF";
        $valuemsg = serialize($erp);
        $updatetype = 'ERP接口配置';
    }
	else if($in['at'] == "field")
	{
		$n = 0;
		for($i=1;$i<11;$i++)
		{
			if(!empty($in['FieldName_'.$i]))
			{
				$fieldarr['FieldName_'.$i]['name']  = trim($in['FieldName_'.$i]);
				if(!empty($in['FieldName_'.$i.'_check']) && $n<2)
				{
					$n++;
					$fieldarr['FieldName_'.$i]['check']  = trim($in['FieldName_'.$i.'_check']);
				}else{
					$fieldarr['FieldName_'.$i]['check'] = '';
				}
			}
		}
		$valuemsg    = serialize($fieldarr);
		$updatetype  = "商品字段设置";
	}
	else if($in['at'] == "printf")
	{
		$n = 0;
		if(empty($in['ptype'])) $in['ptype'] = 'order';
		if($in['ptype'] == 'paper')
		{
			$fieldarr['PrintWidth']  = intval($in['PrintWidth']);
			$fieldarr['PrintHeight'] = intval($in['PrintHeight']);
		}else{
			$fieldnamearr = $setfieldarr[$in['ptype']];
			$shownum = 0;
			foreach($fieldnamearr as $k=>$v)
			{			
				if(empty($in['FieldShowName_'.$k])) $in['FieldShowName_'.$k] = $v;
				if(empty($in['FieldShowPrint_'.$k])) $in['FieldShowPrint_'.$k] ='';
				$fieldarr[$k]['name']   = trim($in['FieldShowName_'.$k]);
				$fieldarr[$k]['width']  = trim($in['FieldShowWidth_'.$k]);
				$fieldarr[$k]['show']   = $in['FieldShowPrint_'.$k];
				if(in_array($k,$disarr[$in['ptype']]))  $fieldarr[$k]['show'] = 1;
				
				if(!empty($fieldarr[$k]['show'])) $shownum ++;
			}
			if(empty($in['CompanyInfoPrint'])) $fieldarr['CompanyInfoPrint'] = '2'; else $fieldarr['CompanyInfoPrint'] = '1';
			
			if($in['ptype'] == 'order' && $shownum < 6)
    	    {
    	        Error::AlertJs("请选择不少于6个商品打印字段，您已选择了{$shownum}个！");
    	        exit();
    	    }
		}

		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
		if(!empty($setinfo['SetValue'])) $valuearr1 = unserialize($setinfo['SetValue']);
		$valuearr1[$in['ptype']] = $fieldarr;

		$valuemsg   = serialize($valuearr1);
		$updatetype = "打印字段设置";
	}
	else if($in['at'] == "clientlevel")
	{
		if(empty($in['data_LevelName_1']))
		{
			Error::AlertJs('第一个级别不能为空！');
			exit();
		}
		for($i=1;$i<11;$i++)
		{
			$keyname = 'level_'.$i;
			$inkey   = 'data_LevelName_'.$i;
			if(!empty($in[$inkey]))
			{
				$proarr[$keyname] = trim($in[$inkey]);
			}			
		}
		$valuearr = null;
		$valuearr1 = null;
		if(empty($in['data_LevelID'])) $in['data_LevelID'] = 'A';
		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='".$in['at']."' limit 0,1");
		if(!empty($setinfo['SetValue'])) $valuearr1 = unserialize($setinfo['SetValue']);

		if(count($valuearr1) > 9)
		{
			Error::AlertJs('最多只有设置9几种不同级别!');
			exit();
		}
		if(!empty($valuearr1))
		{
			if(count($valuearr1, COUNT_RECURSIVE)==count($valuearr1))
			{
				$valuearr['A'] = $valuearr1;
				$valuearr['A']['id'] = "A";
				$valuearr['A']['name'] = "方式A";
				if(empty($valuearr['isdefault'])) $valuearr['isdefault'] = "A";
			}else{
				$valuearr = $valuearr1;
			}			
		}
		if(!empty($proarr) )
		{
			$valuearr[$in['data_LevelID']] = $proarr;
			$valuearr[$in['data_LevelID']]['id'] = $in['data_LevelID'];
			$valuearr[$in['data_LevelID']]['name'] = $in['data_LevelName'];
			if(!empty($in['data_isdefault']))  $valuearr['isdefault'] = trim($in['data_isdefault']);
			if(empty($valuearr['isdefault'])) $valuearr['isdefault'] = $in['data_LevelID'];
			$valuemsg = serialize($valuearr);
		}
		$updatetype = "药店级别设置";

	}else if($in['at'] == "point"){

		if(!empty($in['pointtype']))
		{
			$valuearr['pointtype'] = $in['pointtype'];
		}else{
			$valuearr['pointtype'] = "1";
		}
		if(!empty($in['pointpencent']))
		{
			if(strpos($in['pointpencent'],"%")) $in['pointpencent'] = str_replace("%","",$in['pointpencent'])/100;
			$valuearr['pointpencent'] = abs(floatval($in['pointpencent']));
		}
		if(empty($valuearr['pointpencent']))	$valuearr['pointpencent'] = "1";		

		$valuemsg    = serialize($valuearr);
		$updatetype = "积分设置";

	}

	if(!empty($in['at']))
	{
		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='".$in['at']."' limit 0,1");

		if(empty($valuemsg)) $valuemsg = '';
		if(!empty($setinfo['SetID']))
		{
			$isq = $db->query("update ".DATABASEU.DATATABLE."_order_companyset set SetValue = '".$valuemsg."' where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetID='".$setinfo['SetID']."'");
		}else{
			$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$_SESSION['uinfo']['ucompany'].",'".$in['at']."','".$valuemsg."')");
		}

		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=update_settype&at=".$in['at']."','".$updatetype."','".$valuemsg."',".time().")";
		$db->query($sqlex);

		Error::AlertJs('设置成功');
		exit("ok");
	}else{
		Error::AlertJs('参数错误!');
		exit('参数错误!');
	}
}

if($in['m']=="delete_level")
{
	
	if(!empty($in['levelid']))
	{
		$oldinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='clientlevel' limit 0,1");
		if(!empty($oldinfo['SetValue']))
		{
			$oldarr = unserialize($oldinfo['SetValue']);
			$clientsue = $db->get_row("SELECT count(*) as orwname FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and  ClientLevel  like '%%".$in['levelid']."_level_%%' limit 0,1");
			if(!empty($clientsue['orwname'])) exit('此分级方式已使用，不能删除！');
			if($in['levelid']==$oldarr['isdefault']) exit('默认分级方式不能删除，请先修改!');
			unset ($oldarr[$in['levelid']]);

			$varmsg = serialize($oldarr);
			$isup   = $db->query("update ".DATABASEU.DATATABLE."_order_companyset set SetValue = '".$varmsg."' where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetID='".$oldinfo['SetID']."'");
			exit("ok");
		}
	}else{
		exit("删除不成功!");
	}
}

if($in['m']=="change_template_value")
{
    //这里注释了免费用户不能自定义模板的代码  by wanjun @20160229
//     $cinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
// 	if(!empty($cinfo) && $cinfo['CS_Number'] > 50)
// 	{
// ----------- END
	    $fullPath = RESOURCE_PATH.$_SESSION['uc']['CompanyID']."/config.txt";
	    $Fp = @fopen($fullPath, "w+");
	    
	    //验证所需目录是否存在，不存在则创建  add by wanjun @2016-02-29
	    Functions::chekcAndMakeDir(RESOURCE_PATH, $fullPath);
	    
	    @flock($Fp, 3);
	    $setarrmsg = @fread($Fp, filesize(RESOURCE_PATH.$_SESSION['uc']['CompanyID']."/config.txt"));
	    
	    if(!empty($setarrmsg))
	    {
	        $setarr = unserialize($setarrmsg);
	    }
	    $setarr['template'] = trim($in['setvalue']);
	    
	    $setsmg				= serialize($setarr);
	    $fl					= @fwrite($Fp, $setsmg);
	    
	    @fclose($Fp);
	    exit('ok');
// ---------- START
// 	}
// 	else 
// 	    exit("免费版不支持模板与专属登录界面功能，请升级至无限用户版");
// ----------- END
    
}

/***********logo**************/
if($in['m']=="template_logo_edit_save")
{	
	if(!empty($_SESSION['uinfo']['ucompany']))
	{
	    //这里注释了免费用户不能自定义模板的代码  by wanjun @20160229
// 	    $cinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
// 	    if(!empty($cinfo) && $cinfo['CS_Number'] > 50)
// 	    {
// ----------- END
    		$upsql = "update ".DATABASEU.DATATABLE."_order_company set  CompanyLogo='".$in['CompanyLogo']."', CompanyLogin='".$in['CompanyLogin']."' where CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
    
    		$InfoData = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
    		$infodatamsg = serialize($InfoData);
    		if($db->query($upsql))
    		{
    			$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=companyinfo_edit_save','修改LOGO','".$infodatamsg."',".time().")";
    			$db->query($sqlex);		
    			
    			exit("ok");
    		}else{
    			exit("没有任何变动!");
    		}
// ---------- START
// 	    }
// 	    else 
// 	        exit("免费版不支持模板与专属登录界面功能，请升级至无限用户版");
// ----------- END

	}
	else
	{
		exit('参数错误!');
	}
}


/***********save_sort**************/
if($in['m']=="save_sort")
{
	if(!empty($in['AreaName']))
	{
	    $letter     = new letter();
        $pinyima = $letter->C($in['AreaName']);	
	
		$upsql = "insert into ".DATATABLE."_order_area(AreaCompany,AreaParentID,AreaName,AreaPinyi,AreaAbout) values(".$_SESSION['uinfo']['ucompany'].", ".$in['AreaParentID'].", '".$in['AreaName']."', '".$pinyima."', '".$in['AreaAbout']."')";
		
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
		
	    if(!empty($in['AreaPinyi']))
		{
			$pinyima = $in['AreaPinyi'];
		}else{
			$letter  = new letter();
			$pinyima = $letter->C($in['AreaName']);
		}

        //地区父级不能是当前地区及其子地区
        if($in['AreaParentID'] == $in['AreaID']) {
            exit("上级地区不能设置为自己!");
        }

        $list = get_area_item($in['AreaID']);
        if(in_array($in['AreaParentID'],$list)) {
            exit("上级地区不能设置为自己的下级地区!");
        }

		$upsql = "update ".DATATABLE."_order_area set AreaParentID=".$in['AreaParentID'].", AreaName='".$in['AreaName']."', AreaPinyi= '".$pinyima."', AreaAbout='".$in['AreaAbout']."' where AreaCompany = ".$_SESSION['uinfo']['ucompany']." and AreaID=".$in['AreaID']."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("无变化，没修改任何内容!");
		}
	}
}

//获取地区下所有子地区ID
function get_area_item($area_id) {
    global $db;
    $result = array();
    $company_id = $_SESSION['uinfo']['ucompany'];
    $list = $db->get_col("SELECT AreaID FROM ".DATATABLE."_order_area WHERE AreaCompany={$company_id} AND AreaParentID=" . $area_id);
    if($list) {
        foreach($list as $item_area_id) {
            $item = get_area_item($item_area_id);
            $result = array_merge($result , $item);
        }
        $result = array_merge($result,$list);
    }
    return $result;
}

if($in['m']=="delete_sort")
{
	if(!empty($in['AreaID']))
	{		
		$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientArea=".intval($in['AreaID'])." ");
		if(!empty($cinfo['lrow'])) exit('该地区已在使用，不能删除!');

		$ainfo = $db->get_row("SELECT count(*) as lrow FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and AreaParentID=".intval($in['AreaID'])." ");
		if(!empty($ainfo['lrow'])) exit('请先删除下级地区!');

		$upsql = "delete from ".DATATABLE."_order_area where AreaCompany = ".$_SESSION['uinfo']['ucompany']." and AreaID=".$in['AreaID']."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}


/*************accounts****************/
if($in['m']=="delete_accounts")
{
	if(!intval($in['ID'])) exit('非法操作!');
	
	$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceAccounts=".intval($in['ID'])." ");
	if(!empty($cinfo['lrow'])) exit('该帐号已在使用，请先删除相应转帐记录!');
	
	$upsql =  "delete from ".DATATABLE."_order_accounts where AccountsID = ".$in['ID']." and AccountsCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


/***********save**************/
if($in['m']=="accounts_add_save")
{

	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('AccountsCompany', $_SESSION['uinfo']['ucompany']);
	$data_->addData('AccountsDate', time());

	$insert_id = $data_->dataInsert ("_order_accounts");
	if(!empty($insert_id))
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

/***********editsave**************/
if($in['m']=="accounts_edit_save")
{
	if(empty($in['data_PayType'])) $in['data_PayType'] = 'transfer';
	if($in['data_PayType']=="transfer") 
	{
		$in['data_PayPartnerID'] = '';
		$in['data_PayKey'] = '';
	}else{
		if(strlen($in['data_PayPartnerID']) != 16) exit('请输入正确的“合作身份者(Partner ID)”');
		if(strlen($in['data_PayKey']) != 32) exit('请输入正确的“安全校验码(Key)”');
	}
	
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	
	$wheremsg =" where AccountsCompany=".$_SESSION['uinfo']['ucompany']." and AccountsID=".$in['AccountsID'];

	$update = $data_->dataUpdate("_order_accounts",$wheremsg);
	if(!empty($update))
	{
		exit("ok");
	}else{
		exit("无变化!");
	}
}

/*********** update_buttoninfo **************/
if($in['m']=="update_buttoninfo")
{	
	$valuemsg = $in['ButtonContent'];
	$in['at']   = "template";

	if($in['at'] == "template")
	{
	    $cinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	    if(!empty($cinfo) && $cinfo['CS_Number'] > 50)
	    {
    		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='".$in['at']."' limit 0,1");
    
    		if(empty($valuemsg)) $valuemsg = '';
    		if(!empty($setinfo['SetID']))
    		{
    			$isq = $db->query("update ".DATABASEU.DATATABLE."_order_companyset set SetValue = '".$valuemsg."' where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetID='".$setinfo['SetID']."'");
    		}else{
    			$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$_SESSION['uinfo']['ucompany'].",'".$in['at']."','".$valuemsg."')");
    		}
    
    		Error::Alert('设置成功');
    		exit("ok");
	    }
	    else 
	        exit("免费版不支持模板与专属登录界面功能，请升级至无限用户版!");
	}else{
		Error::Alert('参数错误!');
		exit('参数错误!');
	}
}

//费用单
if($in['m']=="bill_add_save")
{
	$setinfo = $db->get_row("SELECT BillID FROM ".DATATABLE."_order_expense_bill where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BillNO='".$in['data_BillNO']."' limit 0,1");
	if(!empty($setinfo)) exit('编号不能重复！');
	
	$isin = $db->query("insert into ".DATATABLE."_order_expense_bill(BillNO,BillName,CompanyID) values('".$in['data_BillNO']."','".$in['data_BillName']."',".$_SESSION['uinfo']['ucompany'].")");
	if($isin)
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="bill_edit_save")
{
	if(empty($in['update_id'])) exit('参数错误 ，请指定您要修改的内容！');
	$setinfo = $db->get_row("SELECT BillID FROM ".DATATABLE."_order_expense_bill where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BillNO='".$in['edit_BillNO']."' and BillID != ".$in['update_id']." limit 0,1");
	if(!empty($setinfo)) exit('编号不能重复！');
	
	$isin = $db->query("replace into ".DATATABLE."_order_expense_bill(BillID,BillNO,BillName,CompanyID) values(".$in['update_id'].",'".$in['edit_BillNO']."','".$in['edit_BillName']."',".$_SESSION['uinfo']['ucompany'].")");
	if($isin)
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="delete_bill")
{
	if(empty($in['ID'])) exit('参数错误 ，请指定您要删除的内容！');
	$in['ID'] = intval($in['ID']);
	$setinfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_expense where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BillID='".$in['ID']."' limit 0,1");

	if(!empty($setinfo['allrow'])) exit('此类型已在使用，不能删除！');
	
	$isin = $db->query("delete from ".DATATABLE."_order_expense_bill where CompanyID = ".$_SESSION['uinfo']['ucompany']." and BillID = ".$in['ID']." limit 1");
	if($isin)
	{
		exit("ok");
	}else{
		exit("删除不成功!");
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
   
   function checkDatetime($str, $format="Y-m-d H:i"){
       $str = '2000-01-01 '.$str;
       $unixTime=strtotime($str);
       $checkDate= date($format, $unixTime);
       if($checkDate==$str)
           return true;
       else
           return false;
   }
    

exit('非法操作!');
?>