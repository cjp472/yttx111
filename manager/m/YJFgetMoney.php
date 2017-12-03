<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");




$openApi = new YOpenApi($_SESSION['cc']['cid'], 'backend');



debug($openApi, 1);

if(method_exists($openApi, $method)){
	// 	$info = $openApi->setAccount();
	$info = $openApi->$method();
}else{
	//初始化
	$YOpenApiDo = new YOpenApiDo();
	$YOpenApiDo->setMsg('错误的操作', 'error');
	header('location: ./Yopenapi.php?m=show');
}
exit;




?>