<?php
	include_once ("common.php");
	if(isset($_GET['burl'])){
		$burl = trim($_GET['burl']);
	}else{
		$burl = WEB_API_URL.'mjumpurlback.php';
	}
	$backurl = urlencode($burl);
	
	if(empty($_GET['state'])){
		 $wid = '0';
	}else{
		 $wid = intval($_GET['state']);		 
	}
	$conf_file = SITE_ROOT_PATH."/wx/wx_".$wid.".php";
	if(file_exists($conf_file)){
		include ($conf_file);
	}else{
		include (SITE_ROOT_PATH."/wx/wx_0.php");
	}

	$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.APPID.'&redirect_uri='.$backurl.'&response_type=code&scope=snsapi_base&state='.$wid.'#wechat_redirect';
	header("location: ".$url);
?>