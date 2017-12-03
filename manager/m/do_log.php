<?php
include_once ("../common.php");
$inv   = new Input();
$in    = $inv->parse_incoming();
$db	   = dbconnect::dataconnect()->getdb();

/*****************************/
if(empty($_SESSION['uinfo']['userid']) || empty($_SESSION['uc']['CompanyID']))
{
	exit('isouttime');
}


if($in['m'] == "printlog")
{
	$insql = "insert into ".DATATABLE."_order_print_log(CompanyID,LogType,LogContent,logUser,LogDate) values({$_SESSION['uinfo']['ucompany']},'{$in['ty']}','{$in['ID']}','{$_SESSION['uinfo']['username']}',".time().")";
	$db->query($insql);
}
exit();
?>