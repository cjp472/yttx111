<?php
	include_once ("common.php");
	if(isset($_GET['burl'])){
		$burl = trim($_GET['burl']);
	}else{
		$burl = WEB_API_URL."wxqyjumpurlback.php?cid=".$_GET['cid']."&url=".$_GET['url'];
	}
	$backurl = urlencode($burl);
	//wid=companyid

	if(empty($_GET['state'])){
		 $wid = $_GET['wid'];
	}else{
		 $wid = intval($_GET['state']);		 
	}

	$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_GET['cid'].'&redirect_uri='.$backurl.'&response_type=code&scope=snsapi_base&state='.$wid.'#wechat_redirect';

	//error_log ($url, 3, SITE_ROOT_PATH."/data/log/test.txt");
	header("location: ".$url);
?>