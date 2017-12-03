<?php
include_once ("common.php");
if(!empty($_GET['code'])){
	$param['code'] = trim($_GET['code']); 
	$param['wid']  = intval($_GET['state']); 
    $param['ip']   = RealIp();
	$var = '?f=managerWeixinGetTokenValue&v='.json_encode($param);
	$baseurl = WEB_API_URL.'api.php';
	$geturl  = $baseurl.$var;
	$rdata   = file_get_contents($geturl);
	$rarr 	 = json_decode($rdata,true);
	$rarr['wid']  = $param['wid'];
	$rdata = json_encode($rarr);
	
	if($rarr['rStatus'] != '100'){
		$param['openId'] = $rarr['openId'];
		$var = '?f=managerWeixinGetTokenValue&v='.json_encode($param);
		$baseurl = WEB_API_URL_TY.'api.php';
		$geturl  = $baseurl.$var;
		$rdata_ty   = file_get_contents($geturl);
		$rarr_ty	= json_decode($rdata_ty,true);
		if($rarr_ty['rStatus'] == '100'){
			$rdata = $rdata_ty;
		}
	}

	$url = WEB_APP_URL."bsmm_login.html?back=".$rdata;

	header("location: ".$url);
	exit;
}
?>