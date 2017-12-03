<?php
	
	if(isset($_GET['burl'])){
		$burl = trim($_GET['burl']);
	}else{
		//$burl = 'http://tmapi.dhb.net.cn/Api/wx_back_api.php';
		$burl = 'http://yaoliankeji.dhb.net.cn/mobileApi/wxback.php';
	}
	$backurl = urlencode($burl);
	//独立微信需传入参数 appid的值
	if(empty($_GET['appid'])){
		include ("./wx/wx_505.php");
		$appid = APPID;
	}else{
		$appid = trim($_GET['appid']);		 
	}

	$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$backurl.'&response_type=code&scope=snsapi_base&state='.$appid.'#wechat_redirect';
	//error_log ( $url, 3, SITE_ROOT_PATH."/data/log/test.txt");
	header("location: ".$url);
?>