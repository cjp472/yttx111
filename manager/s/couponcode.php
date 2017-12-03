<?php

include_once ("../class/Code.class.php");

// 未生成
$code = new Code();
$codeStr = $code->encodeID($_SESSION['uinfo']['userid'], 5);
$codedata = $db->get_row("select ID,Mobile,Num,Code from ".DATABASEU.DATATABLE."_order_coupon where AdminID = {$_SESSION['uinfo']['userid']} and Type='S' limit 0,1");
if(empty($codedata['ID'])){ 
	$db->query("insert into ".DATABASEU.DATATABLE."_order_coupon(Mobile,AdminID,Code,Date) values('', '{$_SESSION['uinfo']['userid']}', '{$codeStr}',".time().")");
	header('location:order.php');
}

// 保存手机号
if(!empty($in['m']) && $in['m'] == 'edit_mobile_save'){ 
	$in['mobile'] = trim($in['mobile']);
	if( $db->query("update ".DATABASEU.DATATABLE."_order_coupon set Mobile = '{$in['mobile']}' where AdminID = {$_SESSION['uinfo']['userid']} and `Type`='S'")){ 
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