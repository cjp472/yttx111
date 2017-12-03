<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/statdata.php");

$input		=	new Input;
$in			=	$input->parse_incoming();

if($in['m']=="delfinance"){
	
}else{

	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate']	 = date("Y-m-d");

	$redata = statdata::showreconciliation($in);

	$n      = 1;
	$tall   = 0;
	$tjian = 0;
	$tjia   = 0;
	$btotal = $redata['begin'];
	unset($redata['begin']);

	foreach($redata as $key=>$var)
	{
		if(empty($var['Total'])) unset($redata[$key]);
		if($var['TotalType']=="-")
		{
			$tall = $tall - $var['Total'];
			$tjian = $tjian + $var['Total'];
		}else{
			$tall = $tall + $var['Total'];
			$tjia = $tjia + $var['Total'];
		}
		$redata[$key]['linetotal'] = $tall+$btotal; 
	}
	$tall = $tall + $btotal;
	include template("reconciliation");
//END
}
?>