<?php

include_once ("../class/Code.class.php");

// 未生成
$code = new Code();
$codeStr = $code->encodeID($_SESSION['uinfo']['userid'], 4);
$codedata = $db->get_row("select ID,Mobile,Num,Code from ".DATABASEU.DATATABLE."_order_coupon where AdminID = {$_SESSION['uinfo']['userid']} and Type='M' limit 0,1");
if(empty($codedata['ID'])){ 
	$db->query("insert into ".DATABASEU.DATATABLE."_order_coupon(Mobile,AdminID,Code,Date,Type) values('', '{$_SESSION['uinfo']['userid']}', '{$codeStr}',".time().",'M')");
	header('location:home.php');
}

// 保存手机号
if(!empty($in['m']) && $in['m'] == 'edit_mobile_save'){ 
	$in['mobile'] = trim($in['mobile']);
	if( $db->query("update ".DATABASEU.DATATABLE."_order_coupon set Mobile = '{$in['mobile']}' where AdminID = {$_SESSION['uinfo']['userid']} and `Type`='M'")){ 
		exit('ok');
	}else{ 
		exit('fail');
	}
}

$strUrl = 'http://www.dhb.hk/dhb-'.$codedata['Code'];

// 保存
$sessionCode = $_SESSION['close_code'];
if(!empty($in['close_code'])){
	if($in['close_code']==1){
		$sessionCode = $_SESSION['close_code'] = 1;
	}else{ 
		$sessionCode = $_SESSION['close_code'] = 0;
	}
	exit();
}

?>