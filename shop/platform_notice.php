<?php
include_once ('common.php');
//echo 2332;
$input		=	new Input;
$in			=	$input->parse_incoming();

$return=array();
$return['status']=0;
$return['msg']="error";

if($in['m'] == "important_notice"){
	$db  = dbconnect::dataconnect()->getdb();
	$now=time();
	$sql = "select content from ".DATATABLE."_pay_notice where type=2 and important = 1 and start_date <= '".$now."' and end_date >= '".$now."' order by addtime desc limit 1";
	$res=$db->get_var($sql);
    ///var_dump($res);exit;
	//echo $sql;exit;
	if(!empty($res)){
		$return['status']=1;
		$return['msg']="获取成功！";
		$return['data']=html_entity_decode($res, ENT_QUOTES,'UTF-8');
	}
}

exit(json_encode($return));
?>