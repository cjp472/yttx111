<?php
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");

if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="content_notice_save")
{	

	if($in['content'] =='') exit('提示内容不能为空!');
	$type=empty($in['type'])? 1 : $in['type'] ;
	if($in['bdate'] !=''){
		$start_date=strtotime($in['bdate']);
		if(!$start_date)  exit('开始时间格式错误！');
	}else{
		exit('开始时间不能为空！');
	}
	if($in['edate'] !=''){
		$end_date=strtotime($in['edate']);
		if(!$end_date)  exit('结束时间格式错误！');
	}else{
		exit('结束时间不能为空！');
	}
	if($end_date <= $start_date) exit('结束时间必须大于开始时间！');
	$insql = "insert into ".DATATABLE."_pay_notice(title,content,addtime,start_date,end_date,type,important) values('".$in['title']."','".$in['content']."','".time()."','".$start_date."','".$end_date."','".$type."','".$in['important']."')";
	if($db->query($insql))
	{
		exit('ok');
	}else{
		exit('添加失败!');
	}
	
}else if($in['m']=="delete"){
	
	if(!empty($in['ID']) && $in['ID'] >0){
		
		$delsql = "delete from ".DATATABLE."_pay_notice where ID=".$in['ID']." limit 1";
		if($db->query($delsql)){
			exit('ok');
		}
			exit('删除失败！');
		
	}else{
		exit('参数错误！');
	}
	
}else if($in['m']=="content_notice_edit"){
	if($in['content'] =='') exit('提示内容不能为空!');
	if($in['bdate'] !=''){
		$start_date=strtotime($in['bdate']);
		if(!$start_date)  exit('开始时间格式错误！');
	}else{
		exit('开始时间不能为空！');
	}
	if($in['edate'] !=''){
		$end_date=strtotime($in['edate']);
		if(!$end_date)  exit('结束时间格式错误！');
	}else{
		exit('结束时间不能为空！');
	}
	if($end_date <= $start_date) exit('结束时间必须大于开始时间！');
	 $upsql = $db->query("UPDATE ".DATATABLE."_pay_notice SET title='".$in['title']."',content='".$in['content']."',start_date='".$start_date."',end_date='".$end_date."',important='".$in['important']."' WHERE ID='".$in['id']."'");
    
	if($upsql)
	{
		exit('ok');
	}else{
		exit('修改失败!');
	}
}else if($in['m']=="content_Q_A_save"){
	if($in['title'] =='') exit('问题不能为空!');
	if($in['content'] =='') exit('答案不能为空!');
	$type=empty($in['type'])? 1 : $in['type'] ;
	
	$insql = "insert into ".DATATABLE."_pay_notice(title,content,addtime,start_date,end_date,type,important) values('".$in['title']."','".$in['content']."','".time()."','0','0','".$type."','".$in['important']."')";
	if($db->query($insql))
	{
		exit('ok');
	}else{
		exit('添加失败!');
	}
	
}else if($in['m']=="content_Q_A_edit"){
	if($in['title'] =='') exit('问题不能为空!');
	if($in['content'] =='') exit('答案不能为空!');
	
	 $upsql = $db->query("UPDATE ".DATATABLE."_pay_notice SET title='".$in['title']."',content='".$in['content']."',important='".$in['important']."' WHERE ID='".$in['id']."'");
	 //var_dump($upsql);exit;
	if($upsql)
	{
		exit('ok');
	}else{
		exit('修改失败!');
	}
	
}


exit('非法操作!');