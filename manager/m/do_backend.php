<?php
include_once ("header.php");

$in = $inv->_htmlentities($in);

if($in['m'] == 'execute'){
	
	//$_SESSION['YJForderNo'] 来自于 YOpenApi 接口
	$fileName = YOPENAPI_MESSAGE_PATH.md5($_SESSION['YJForderNo']);
	if(file_exists($fileName)){
		parse_str(file_get_contents($fileName), $parse);
		$_SESSION['YJFSynInfo'] = '';
		$_SESSION['YJFSynInfo'] = $parse;
		unlink($fileName);
	}
	
	echo $in['jp']."('".(isset($_SESSION['YJFSynInfo']) && !empty($_SESSION['YJFSynInfo']))."')";
}

unset($_SESSION['YJFSynInfo']);



?>