<?php
include_once ("common.php");
if(!empty($_GET['code'])){
	$param['code'] = trim($_GET['code']); 
	$param['wid']  = intval($_GET['state']); 
    $param['ip']   = RealIp();
	$var = '?f=managerWeixinqyGetTokenValue&v='.json_encode($param);
	$baseurl = WEB_API_URL.'api.php';
	$geturl  = $baseurl.$var;
	$rdata   = file_get_contents($geturl);
	
	//error_log ($rdata,3, SITE_ROOT_PATH."/data/log/openid.txt");
	$rarr 	= json_decode($rdata,true);
	//error_log (var_export($rarr,TRUE),3, SITE_ROOT_PATH."/data/log/openid.txt");

	$rarr['wid']  = $param['wid'];
	$rdata = json_encode($rarr);

	$url = WEB_APP_URL."login_m.html?back=".$rdata."&cid=".$_GET['cid']."&url=".$_GET['url'];
	//error_log ($url, 3, SITE_ROOT_PATH."/data/log/openid2.txt");
	header("location: ".$url);
	exit;
}
?>