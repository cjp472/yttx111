<?php
include_once ("common.php");
if(!empty($_GET['code'])){
	$param['code'] = trim($_GET['code']); 
	$param['wid']  = intval($_GET['state']); 
    $param['ip']   = RealIp();
	$var = '?f=weixinqyGetTokenValue&v='.json_encode($param);
	$baseurl = WEB_API_URL.'api.php';
	$geturl  = $baseurl.$var;
	$rdata   = file_get_contents($geturl);
	$rarr 	 = json_decode($rdata,true);
	$rarr['wid']  = $param['wid'];
	$rdata = json_encode($rarr);

	$url = WEB_APP_URL."login_c.html?back=".$rdata."&cid=".$_GET['cid']."&url=".$_GET['url'];
	//error_log ($url, 3, SITE_ROOT_PATH."/data/log/openid.txt");
	header("location: ".$url);
	exit;
}
?>