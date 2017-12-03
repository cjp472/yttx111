<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");

$input		=	new Input;
$in			=	$input->parse_incoming();

$method = strval($in['type']);
$openApi = new YopenApiFront($_SESSION['cc']['cid']);


if(method_exists($openApi, $method)){
	$info = $openApi->setGetway()->$method();
}else{
	//初始化
	$YOpenApiDo = new YOpenApiDo();
	$YOpenApiDo->setMsg('错误的操作', 'error');
	header('location: ./Yopenapi.php?m=show');
}
exit;


