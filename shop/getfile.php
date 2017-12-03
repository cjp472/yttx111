<?php
header('Content-Type: text/html; charset=gbk'."\n");
function getlocalfile($filename, $readmod = 1, $range = 0) {
	if($readmod == 1 || $readmod == 3 || $readmod == 4) {
		if($fp = @fopen($filename, 'rb')) {
			@fseek($fp, $range);
			if(function_exists('fpassthru') && ($readmod == 3 || $readmod == 4)) {
				@fpassthru($fp);
			} else {
				echo @fread($fp, filesize($filename));
			}
		}
		@fclose($fp);
	} else {
		@readfile($filename);
	}
	@flush();
}

function dheader($string, $replace = true, $http_response_code = 0) {
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	if(empty($http_response_code) || PHP_VERSION < '4.3' ) {
		@header($string, $replace);
	} else {
		@header($string, $replace, $http_response_code);
	}
	if(preg_match('/^\s*location:/is', $string)) {
		exit();
	}
}

while(@ob_end_clean());
@ob_start();
if(strpos($_SERVER['SERVER_NAME'], "dhb.hk"))
{
	$baseurl = 'http://resource.dhb.hk/';
}else{
	$baseurl = 'http://resource.dinghuobao.cn/';
}

$sQueryString = $_SERVER['QUERY_STRING'];
//$sQueryString = base64_decode($_SERVER['QUERY_STRING']);
parse_str($sQueryString,$output);

$attachment = $baseurl.base64_decode($output['p']);
$filename     = base64_decode($output['f']);
$filename		= @iconv("UTF-8", "gb2312", $filename);
//$filename   = $output['f'];
$sExtension = substr($attachment, (strrpos($attachment, '.') + 1 ) );

if(empty($filename))
{
	$parr  = explode("/", $attachment);
	$fruit = array_pop($parr);
	$filename = $fruit;
}else{
	$filename = $filename.".".$sExtension;
}

if(empty($readmod)) $readmod = 4;
if(empty($range)) $range = 0;
	
if(empty($attachment)){
	@ob_end_clean();
	dheader("HTTP/1.0 404 Not Found");
	exit(0);
}

$fileModified = filemtime($attachment);
$fileSize = filesize($attachment);

dheader('Date: '.gmdate('D, d M Y H:i:s', $fileModified).' GMT');
dheader('Last-Modified: '.gmdate('D, d M Y H:i:s',$fileModified).' GMT');
dheader('Content-Encoding: none');
if($isimage) {
	dheader('Content-Disposition: inline; filename='.$filename);
} else {
	dheader('Content-Disposition: attachment; filename='.$filename);
}
dheader('Content-Type: '.$fileMine);
if($readmod == 4) {
	dheader('Accept-Ranges: bytes');
	if(!empty($_SERVER['HTTP_RANGE'])) {
		list($range) = explode('-',(str_replace('bytes=', '', $_SERVER['HTTP_RANGE'])));
		$rangesize = ($fileSize - $range) > 0 ?  ($fileSize - $range) : 0;
		dheader('Content-Length: '.$rangesize);
		dheader('HTTP/1.1 206 Partial Content');
		dheader('Content-Range: bytes='.$range.'-'.($fileSize-1).'/'.($fileSize));
	}else
		dheader('Content-Length: '.$fileSize);
}else
		dheader('Content-Length: '.$fileSize);
while(@ob_end_flush());
getlocalfile($attachment, $readmod, $range);
?>