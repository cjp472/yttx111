<?php

if($_GET['m']=='accounts'){
	$path			= "http://resource.dhb.hk/file/dhb_allinpay.rar";
	
	$download_name 	= '通联支付网上支付商户信息表';
	$currentFile	= 'tonglian.docx';
	$fileName		= 'dhbpay/file/' . $currentFile;
	
}else{
    echo "非法操作";
    exit();
}


//开始下载
if(ini_get('zlib.output_compression')){	// 关闭压缩输出
	ini_set('zlib.output_compression', 'Off');
}
clearstatcache();	//清除文件的缓存状态

$file_data =  explode(".", $currentFile);
$file_type = end($file_data);
$download_file = rawurlencode($download_name) . "." . $file_type;


$ua = $_SERVER["HTTP_USER_AGENT"];
if (preg_match("/MSIE/", $ua)) {
	header('Content-Disposition: attachment; filename="' . $download_file . '"');
}
else if (preg_match("/Firefox/", $ua)) {
	header('Content-Disposition: attachment; filename*="utf8\'\'' . $download_file . '"');
}
else {
	header('Content-Disposition: attachment; filename="' . $download_file . '"');
}
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Length: ".filesize($fileName));
header("Content-Transfer-Encoding: binary");
header("Expires: -1");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");
@readfile($fileName); 
exit;