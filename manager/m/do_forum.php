<?php
$menu_flag = "forum";
include_once ("header.php");
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
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法操作!');

	$upsql =  "delete from ".DATATABLE."_order_forum where ID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

if($in['m']=="delete_all")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法参数!');

	$upsql =  "delete from ".DATATABLE."_order_forum where ID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
	
	if($db->query($upsql))
	{
		$spsql =  "delete from ".DATATABLE."_order_forum where PID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
		$db->query($spsql);
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


/***********save**************/
if($in['m']=="submitreply")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');

		$sql = "insert into ".DATATABLE."_order_forum(CompanyID,PID,User,Name,Content,Date,IP,Flag) values(".$_SESSION['uinfo']['ucompany'].",".$in['pid'].", '".$_SESSION['uinfo']['username']."', '".$in['replyname']."', '".$in['replycontent']."', ".time().", '".RealIp()."',1)";

		$sqlu = "update ".DATATABLE."_order_forum set Reply=Reply+1 where ID=".$in['pid'];
		if($db->query($sql))
		{
			$db->query($sqlu);
			echo '<div class="line"><span class="numberbg">&nbsp;</span>&nbsp;&nbsp;<strong>'.$in['replyname'].' </strong>&nbsp;&nbsp; '.date("Y-m-d H;i").'</div>
				<div class="line" style="padding:4px;">'.nl2br($in['replycontent']).'</div>';
				exit();
		}else{
			exit('Error');
		}
}



/***********save_sort**************/
if($in['m']=="save_tool")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['ToolNO']))
	{
		if(empty($in['ToolType'])) $in['ToolType'] = "QQ";
		$upsql = "insert into ".DATATABLE."_order_tool(ToolCompany,ToolType,ToolName,ToolNO) values(".$_SESSION['uinfo']['ucompany'].", '".$in['ToolType']."', '".$in['ToolName']."', '".$in['ToolNO']."')";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

if($in['m']=="save_edit_tool")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['ToolNO']))
	{
		if(empty($in['ToolType'])) $in['ToolType'] = "QQ";
		$upsql = "update ".DATATABLE."_order_tool set ToolType='".$in['ToolType']."',ToolName='".$in['ToolName']."',ToolNO='".$in['ToolNO']."' where ToolID=".intval($in['ToolID'])." and ToolCompany=".$_SESSION['uinfo']['ucompany'];
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
	exit("号码不能为空!");
}


if($in['m']=="delete_tool")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法操作!');

	$upsql =  "delete from ".DATATABLE."_order_tool where ToolID = ".$in['ID']." and ToolCompany=".$_SESSION['uinfo']['ucompany']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


/******* Conatct *************/
if($in['m']=="save_contact")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['ContactValue']))
	{

		$upsql = "insert into ".DATATABLE."_order_contact(ContactCompany,ContactName,ContactValue) values(".$_SESSION['uinfo']['ucompany'].", '".$in['ContactName']."', '".$in['ContactValue']."')";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

if($in['m']=="save_edit_contact")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['ContactValue']))
	{

		$upsql = "update ".DATATABLE."_order_contact set ContactName='".$in['ContactName']."', ContactValue='".$in['ContactValue']."' where ContactID=".intval($in['ContactID'])." and ContactCompany=".$_SESSION['uinfo']['ucompany'];
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
	exit("内容不能为空!");
}

if($in['m']=="delete_contact")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法操作!');

	$upsql =  "delete from ".DATATABLE."_order_contact where ContactID = ".$in['ID']." and ContactCompany=".$_SESSION['uinfo']['ucompany']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

exit('非法操作!');
?>