<?php
$menu_flag = "infomation";
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

	$upsql =  "update ".DATATABLE."_order_article set ArticleFlag=1 where ArticleID = ".$in['ID']." and ArticleCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

if($in['m']=="delarr")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	$deleteidmsg = implode(",", $in['selectedID']);

	$upsql =  "update ".DATATABLE."_order_article set ArticleFlag=1 where ArticleID IN ( ".$deleteidmsg." ) and ArticleCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		Error::Alert('成功删除!',$in['referer']);
	}else{
		Error::AlertJs('删除不成功!');
	}
}
//*****recycle************/
if($in['m']=="restore")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法参数!');

	$upsql =  "update ".DATATABLE."_order_article set ArticleFlag=0 where ArticleID = ".$in['ID']." and ArticleCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('还原不成功!');
	}
}

if($in['m']=="quite_delete")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('非法参数!');

	$upsql =  "delete from ".DATATABLE."_order_article where ArticleID = ".$in['ID']." and ArticleCompany=".$_SESSION['uinfo']['ucompany']." and ArticleFlag=1";
	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


if($in['m']=="restorearr")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	$deleteidmsg = implode(",", $in['selectedID']);

	$upsql =  "update ".DATATABLE."_order_article set ArticleFlag=0 where ArticleID IN ( ".$deleteidmsg." ) and ArticleCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		Error::Alert('成功还原!',$in['referer']);
	}else{
		Error::AlertJs('还原不成功!');
	}
}

if($in['m']=="quite_delete_arr")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	if(empty($in['selectedID'])) Error::AlertJs('请选择您要操作的记录!');
	$deleteidmsg = implode(",", $in['selectedID']);

	$upsql =  "delete from ".DATATABLE."_order_article where ArticleID IN ( ".$deleteidmsg." ) and ArticleCompany=".$_SESSION['uinfo']['ucompany']." and ArticleFlag=1";
	
	if($db->query($upsql))
	{
		Error::Alert('成功删除!',$in['referer']);
	}else{
		Error::AlertJs('删除不成功!');
	}
}

/***********save**************/
if($in['m']=="content_add_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('ArticleCompany', $_SESSION['uinfo']['ucompany']);
	$data_->addData('ArticleContent', $in['editor1']);
	$data_->addData('ArticleDate', time());
	$data_->addData('ArticleUser', $_SESSION['uinfo']['username']);
	
	$insert_id = $data_->dataInsert ("_order_article");
	if(!empty($insert_id))
	{
		Error::AlertSet("parent.setinputok('ok')");
	}else{
		Error::AlertSet("parent.setinputok('error')");
	}

}

/***********editsave**************/
if($in['m']=="content_edit_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('ArticleContent', $in['editor1']);
	$data_->addData('ArticleDate', time());
	$data_->addData('ArticleUser', $_SESSION['uinfo']['username']);
	$where = " WHERE ArticleID=".$in['update_id']." and ArticleCompany=".$_SESSION['uinfo']['ucompany'];
	$isindex = $data_->dataUpdate("_order_article", $where);

	if($isindex)
	{
		if(!empty($_COOKIE['backurl'])) $backurl = $_COOKIE['backurl']; else $backurl = "infomation.php";
		Error::AlertSet("parent.setinputeditok('ok','".$backurl."')");
	}else{
		Error::AlertSet("parent.setinputeditok('error','')");
	}
	exit();
}

if($in['m']=="update_order")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::Alert('对不起，您没有此项操作权限！');
	if(!empty($in['ID']))
	{
		$upsql = "update ".DATATABLE."_order_article set ArticleOrder=".$in['orderid']." where ArticleID = ".$in['ID']." and ArticleCompany = ".$_SESSION['uinfo']['ucompany']."";
		if($db->query($upsql))
		{
			exit('ok');
		}else{
			exit('error');
		}
	}
}


/***********save_sort**************/
if($in['m']=="save_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['SortName']))
	{
		if(empty($in['SortOrder'])) $in['SortOrder'] = 0; else $in['SortOrder'] = intval($in['SortOrder']);
		$upsql = "insert into ".DATATABLE."_order_sort(SortCompany,SortName,SortOrder) values(".$_SESSION['uinfo']['ucompany'].", '".$in['SortName']."', ".$in['SortOrder'].")";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

/***********XD save**************/
if($in['m']=="save_edit_sort")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['SortOrder'])) $in['SortOrder'] = 0;
	if(!empty($in['SortID']))
	{
		if(empty($in['SortName'])) exit('栏目名称不能为空!');
		$upsql = "update ".DATATABLE."_order_sort set SortName='".$in['SortName']."',SortOrder=".$in['SortOrder']." where SortCompany = ".$_SESSION['uinfo']['ucompany']." and SortID=".$in['SortID']."";
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
		$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['uinfo']['ucompany']." and ArticleSort=".intval($in['SortID'])." ");
		if(!empty($cinfo['lrow'])) exit('该栏目已在使用，不能删除!');

		$upsql = "delete from ".DATATABLE."_order_sort where SortCompany = ".$_SESSION['uinfo']['ucompany']." and SortID=".$in['SortID']."";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}

if($in['m']=="xd_add_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('ArticleCompany', $_SESSION['uinfo']['ucompany']);
	$data_->addData('ArticleDate', time());
	$data_->addData('ArticleUser', $_SESSION['uinfo']['username']);
	
	$insert_id = $data_->dataInsert ("_order_xd");
	if(!empty($insert_id))
	{
		Error::AlertSet("parent.setinput_xd_ok('ok')");
	}else{
		Error::AlertSet("parent.setinput_xd_ok('error')");
	}
}

/***********editsave**************/
if($in['m']=="xd_edit_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') Error::AlertJs('对不起，您没有此项操作权限！');
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('ArticleDate', time());
	$data_->addData('ArticleUser', $_SESSION['uinfo']['username']);
	$where  = " WHERE ArticleID=".$in['update_id']." and ArticleCompany=".$_SESSION['uinfo']['ucompany'];
	$isindex = $data_->dataUpdate("_order_xd", $where);

	if($isindex)
	{
		if(!empty($_COOKIE['backurl'])) $backurl = $_COOKIE['backurl']; else $backurl = "infomation.php";
		Error::AlertSet("parent.setinputeditok('ok','".$backurl."')");
	}else{
		Error::AlertSet("parent.setinputeditok('error','')");
	}
	exit();
}


if($in['m']=="xd_delete")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!empty($in['ID']))
	{		
		$upsql = "delete from ".DATATABLE."_order_xd where ArticleCompany = ".$_SESSION['uinfo']['ucompany']." and ArticleID=".$in['ID']." limit 1";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}
}

exit('非法操作!');
?>