<?php
include_once ("header.php");
include_once ("../class/data.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

//*****contact start************/
/**
 * 删除联系人信息
 */
if($in['m']=="delete")
{
	if(!intval($in['CompanyID'])) exit('非法操作!');
	if(!intval($in['ContactID'])) exit('非法操作!');

	$delsql =  "delete from ".DATABASEU.DATATABLE."_order_company_contact where CompanyID = ".$in['CompanyID']." and ID = ".$in['ContactID'];	
	
	if($db->query($delsql))
	{
		exit('ok');
	}
	else
	{
		exit('删除不成功!');
	}
}

/**
 * 保存联系人信息
 */
if($in['m']=="save")
{
	if(!intval($in['CompanyID']))
	{
	    exit('非法参数!');
	}
	if(intval($in['CompanyID'])<=0)
	{
	    exit('非法参数!');
	}
	if(empty($in['ContactName']))
	{
	    exit('联系人不能为空');
	}
	if(empty($in['ContactPhone']) && empty($in['ContactMobile']))
	{
	    exit('电话和手机至少输入一个');
	}

	//允许多个固话号，对每个固话号进行验证
	if(!empty($in['ContactPhone'])) 
	{
	    $Phones = array_unique(array_filter(explode(",",$in['ContactPhone'])));
	    $PhoneSucc = array();
	    foreach($Phones as $phone) 
	    {
	        if(is_telephone($phone)) 
	        {
	            $PhoneSucc[] = trim($phone);
	        }
	    }
	    if(count($PhoneSucc) < 1 && empty($in['ContactMobile']))
	    {
	        exit('至少输入一个正确的电话或手机');
	    }
	    if(count($PhoneSucc) > 3) 
	    {
	        exit('最多输入三个电话号码');
	    }
	
	    $in['ContactPhone'] = implode(",",$PhoneSucc);
	}
	
    //允许多个手机号，对每个手机号进行验证
    if(!empty($in['ContactMobile'])) 
    {
        $MainPhones = array_unique(array_filter(explode(",",$in['ContactMobile'])));
        $MainPhoneSucc = array();
        foreach($MainPhones as $phone) 
        {
            if(is_phone($phone)) 
            {
                $MainPhoneSucc[] = trim($phone);
            }
        }
        if(count($MainPhoneSucc) < 1 && empty($in['ContactPhone']))
        {
            exit('至少输入一个正确的电话或手机');
        }
        if(count($MainPhoneSucc) > 3) 
        {
            exit('最多输入三个手机号码');
        }
        
        $in['ContactMobile'] = implode(",",$MainPhoneSucc);
    }
    
    if(!empty($in['ContactQQ']) && !preg_match('/^[1-9][0-9]{4,}$/' , $in['ContactQQ']))
    {
        exit('QQ号输入错误，请输入数字且最少5位');
    }
    if(!empty($in['ContactEmail']) && !is_email($in['ContactEmail']))
    {
        exit('邮箱格式不正确');
    }
    
    //验证CompanyID 对应公司是否通过审核
    $sql = "Select CS_ID FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$in['CompanyID']." AND CS_Flag = 'T' limit 0,1";
    $rs = $db->get_row($sql);
    if(empty($rs) || count($rs) < 1)
    {
        exit('该公司未通过审核!');
    }
    
    if(!empty($in['ID']))
    {
        $sqlex = "update ".DATABASEU.DATATABLE."_order_company_contact set ContactName='".trim($in['ContactName'])."',ContactJob='".trim($in['ContactJob'])."',ContactPhone='".trim($in['ContactPhone'])."',ContactMobile='".trim($in['ContactMobile'])."',ContactQQ='".trim($in['ContactQQ'])."',ContactEmail='".trim($in['ContactEmail'])."',UpdateDate=".time().",UpdateUID=".$_SESSION['uinfo']['userid']." where ID=".$in["ID"]." and CompanyID=".$in['CompanyID'];
    }
    else
    {
        $sqlex = "insert into ".DATABASEU.DATATABLE."_order_company_contact(CompanyID,ContactName,ContactJob,ContactPhone,ContactMobile,ContactQQ,ContactEmail,CreateDate,CreateUID,UpdateDate,UpdateUID) values(".$in['CompanyID'].", '".trim($in['ContactName'])."', '".trim($in['ContactJob'])."','".trim($in['ContactPhone'])."','".trim($in['ContactMobile'])."','".trim($in['ContactQQ'])."','".trim($in['ContactEmail'])."',".time().",".$_SESSION['uinfo']['userid'].",".time().",".$_SESSION['uinfo']['userid'].")";
    }  

    $rs = $db->query($sqlex);

    if($rs)
    {
        exit("ok");
    }  
    else 
    {
        exit("操作失败,请重试");
    }  
}

/**
 * 取联系人信息
 */
if($in['m']=="edit")
{
    if(!intval($in['ContactID']))
    {
        exit('非法参数!');
    }

    $sql = "SELECT * FROM ".DATABASEU.DATATABLE."_order_company_contact where ID=".$in['ContactID']." limit 0,1";
    $info = $db->get_row($sql);
    echo json_encode($info);
    
    exit;
}
//*****contact end************/

//*****visit start************/
/**
 * 取回访信息
 */
if($in['m']=="get_feedback")
{
	if(!intval($in['CompanyID']))
	{
	    exit('非法参数!');
	}

    $sql = "SELECT * FROM ".DATABASEU.DATATABLE."_order_company_visit where CompanyID=".$in['CompanyID']." order by RecordDate DESC,ID DESC limit 0,5";
    $info = $db->get_results($sql);
    echo json_encode($info);
    
    exit;
}

/**
 * 保存回访信息
 */
if($in['m']=="save_visit")
{
    if(empty($in['ID']) && empty($in['CompanyID']))
    {
        exit('请选择回访公司!');
    }
    if(empty($in['RecordDate']))
    {
        exit("记录时间不能为空");
    }
    if(!checkDateForm($in['RecordDate'])){
        exit("记录时间格式不正确");
    }
    
    if(empty($in['ContactName']))
    {
        exit("联系人不能为空!");
    }
    if(empty($in['ContactPhone']))
    {
        exit("联系人电话不能为空!");
    }
    if(!is_telephone($in['ContactPhone']) && !is_phone($in['ContactPhone']))
    {
        exit('联系人电话格式不正确!');
    }
    if(!empty($in['ContactQQ']) && !is_telephone($in['ContactQQ']))
    {
        exit('联系人QQ格式不正确!');
    }
    if(!empty($in['ContactEmail']) && !is_email($in['ContactEmail']))
    {
        exit('联系人邮箱格式不正确!');
    }
    if(empty($in['VisitGeneral']))
    {
        exit("回访简情不能为空!");
    }
    if(empty($in['ContactType']))
    {
        exit("回访方式不能为空!");
    }
    if(empty($in['UseFlag']))
    {
        exit("使用状态不能为空!");
    }
    
    //验证CompanyID 对应公司是否通过审核
    $sql = "Select CS_ID FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company = ".$in['CompanyID']." AND CS_Flag = 'T' limit 0,1";
    $rs = $db->get_row($sql);
    if(empty($rs) || count($rs) < 1)
    {
        exit('该公司未通过审核!');
    }
    
    if(!empty($in['ID']))
    {
        $sqlex = "update ".DATABASEU.DATATABLE."_order_company_visit set ContactName='".trim($in['ContactName'])."',ContactJob='".trim($in['ContactJob'])."',ContactPhone='".trim($in['ContactPhone'])."',ContactQQ='".trim($in['ContactQQ'])."',ContactEmail='".trim($in['ContactEmail'])."',VisitGeneral='".trim($in['VisitGeneral'])."',VisitContent='".trim($in['VisitContent'])."',ContactType='".trim($in['ContactType'])."',UseFlag='".trim($in['UseFlag'])."',RecordDate='".trim($in['RecordDate'])."',UpdateDate=".time().",UpdateUID=".$_SESSION['uinfo']['userid'].' where ID='.$in['ID'];
    }
    else if(empty($in['ID']) && !empty($in['CompanyID']))
    {
        $sqlex = "insert into ".DATABASEU.DATATABLE."_order_company_visit(CompanyID,ContactName,ContactJob,ContactPhone,ContactQQ,ContactEmail,VisitName,VisitGeneral,VisitContent,ContactType,UseFlag,RecordDate,CreateDate,CreateUID,UpdateDate,UpdateUID) values(".$in['CompanyID'].", '".trim($in['ContactName'])."', '".trim($in['ContactJob'])."','".trim($in['ContactPhone'])."','".trim($in['ContactQQ'])."','".trim($in['ContactEmail'])."','".trim($in['VisitName'])."','".trim($in['VisitGeneral'])."','".trim($in['VisitContent'])."','".$in['ContactType']."','".$in['UseFlag']."','".$in['RecordDate']."',".time().",".$_SESSION['uinfo']['userid'].",".time().",".$_SESSION['uinfo']['userid'].")";
		$updateisvisit="update ".DATABASEU.DATATABLE."_common_count set isvisit=isvisit+1 where company_id={$in['CompanyID']}";
		$db->query($updateisvisit);
    }
    else
    { 
        exit('非法参数!');
    }
		 $rs = $db->query($sqlex);

    if($rs)
    {
        exit("ok");
    }
    else 
    {
        exit("操作失败,请重试");
    }
}



if($in['m'] == 'change_remarks_info') {
    $ID = intval($in['company']);
    
    if(empty($ID) || !isset($ID))
        exit('参数错误');
    
    $sql = "";
    $info = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_company_remarks where CompanyID = {$ID} limit 0,1");
    if(empty($info))
    {
        $sql = "insert into ".DATABASEU.DATATABLE."_order_company_remarks 
                (CompanyID,OpenDate,VisitDate,ClientInfo,CreateTime,UpDateTime,CreateUserID,UpdateUserID) values 
                ({$ID},'{$in['open_date']}','{$in['visit_date']}','{$in['remarks']}',".time().",".time().",{$_SESSION['uinfo']['userid']},{$_SESSION['uinfo']['userid']})";
    }
    else 
    {
        $sql = "UPDATE ".DATABASEU.DATATABLE."_order_company_remarks SET OpenDate='{$in['open_date']}',VisitDate='{$in['visit_date']}',ClientInfo='{$in['remarks']}',UpDateTime=".time().",UpdateUserID={$_SESSION['uinfo']['userid']}";
    }
    
    if($db->query($sql) !== false) 
        exit('ok');
    else 
        exit('操作失败!');
}
//*****visit end************/

exit('非法操作!');
?>