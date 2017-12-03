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
	if(!empty($in['data_UserSite'])) $shieldmsg = implode(",", $in['data_UserSite']); else exit('oksite');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user_made where UserName='".$in['UserName']."'");
	if(!empty($clientinfo['orwname'])) exit('repeat');
	
	$passmsg = ChangeMsg($in['UserName'],$in['UserPass']);
	
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_user_made(UserName,UserPass,UserCompany,UserTrueName,UserPhone,UserMobile,UserDate,UserRemark,UserSite) values('".$in['UserName']."','".$passmsg."',".$_SESSION['uinfo']['ucompany'].",'".$in['data_UserTrueName']."','".$in['data_UserPhone']."', '".$in['data_UserMobile']."', ".time().",'".$in['data_UserRemark']."','".$shieldmsg."')";
	if($db->query($upsql))
	{
		$uid = mysql_insert_id();
		$infodatamsg = serialize($in);
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system_made.php?m=content_add_save','添加用户(".$uid.")','".$infodatamsg."',".time().")";
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
	if(!empty($in['data_UserSite'])) $shieldmsg = implode(",", $in['data_UserSite']); else exit('oksite');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_user_made where UserName='".$in['UserName']."' limit 0,1");
	if($clientinfo['orwname'] > 1) exit('repeat');

	if(empty($in['UserPass']))
	{
		$clientflag = $db->get_row("SELECT UserID,UserName FROM ".DATABASEU.DATATABLE."_order_user_made where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserID='".$in['UserID']."' limit 0,1");
		if($in['UserName'] != $clientflag['UserName']) exit('请填写您的密码！修改帐号时需要填写您的密码！');

		$upsql = "update ".DATABASEU.DATATABLE."_order_user_made set UserName='".$in['UserName']."', UserTrueName='".$in['data_UserTrueName']."', UserPhone='".$in['data_UserPhone']."', UserMobile='".$in['data_UserMobile']."', UserRemark='".$in['data_UserRemark']."',UserSite='".$shieldmsg."' where UserID=".$in['UserID']." and UserCompany=".$_SESSION['uinfo']['ucompany'];
	}else{
		if(!is_filename($in['UserPass'])) exit('okpass');
		if(strlen($in['UserPass']) < 3 || strlen($in['UserPass']) > 18 ) exit('okpass');
		$passmsg = ChangeMsg($in['UserName'],$in['UserPass']);

		$upsql = "update ".DATABASEU.DATATABLE."_order_user_made set UserName='".$in['UserName']."',UserPass='".$passmsg."', UserTrueName='".$in['data_UserTrueName']."', UserPhone='".$in['data_UserPhone']."', UserMobile='".$in['data_UserMobile']."', UserRemark='".$in['data_UserRemark']."',UserSite='".$shieldmsg."' where UserID=".$in['UserID']." and UserCompany=".$_SESSION['uinfo']['ucompany'];
	}
	$db->query($upsql);	
	$clientflag = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_user_made where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserID='".$in['UserID']."' limit 0,1");

	if($clientflag['UserID'] == $_SESSION['uinfo']['userid'])
	{
		$_SESSION['uinfo']['username']			= $clientflag['UserName'];
		$_SESSION['uinfo']['usertruename']  = $clientflag['UserTrueName'];
	}

	$infodatamsg = serialize($clientflag);
	$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system_made.php?m=content_add_save','修改用户(".$in['UserID'].")','".$infodatamsg."',".time().")";
	$db->query($sqlex);
	exit("ok");
}

if($in['m']=="delete")
{
	if(!intval($in['ID'])) exit('非法参数!');

	$upsql =  "update ".DATABASEU.DATATABLE."_order_user_made set UserFlag='1' where UserID = ".$in['ID']." and UserFlag!='9' and UserCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_system.php?m=delete&ID=".$in['ID']."','删除用户(".$in['ID'].")','-',".time().")";
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