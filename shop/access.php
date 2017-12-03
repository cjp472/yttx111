<?php
include_once ('common.php');
$rootuRL = "http://" . $_SERVER['HTTP_HOST'] . "/";

//弱密文验证
$fixUser	= 'dhb';
$fixPpwd	= 'rsung-access';

$mixString	= 'sgstw5645^%';
$tocken		= $_GET['tocken'];
$pwd		= strval($_POST['pwd']);

//创建tocken
function createTocken(){
	global $fixUser, $fixPpwd, $mixString;
	return md5($fixUser . $fixPpwd . date('Y-m-d H') . $mixString);
}


//开始验证
if(
	empty($tocken) && (empty($pwd) || $fixPpwd != $pwd)
	|| $tocken && createTocken() != $tocken
	)
	{//要求输入密码
	echo '<form action="' . $rootuRL . 'access.php" method="post" enctype="multipart/form-data">';
	echo 'Enter Password：<input type="text" name="pwd" value="" />';
	echo '<br />';
	echo '<br />';
	echo '<input type="submit" value=" OK " />';
	echo '</form>';
	echo '<span style="display: block;font-size: 10px;font-style: italic;padding-top:10px;">powerd by DHB</span>';
	exit;
}elseif(empty($tocken)){
	header('location: ' . $rootuRL . 'access.php?tocken=' . createTocken());
	exit;
}

$path = GLOBAL_LOG_PATH."yopenapi/*.txt";

$files = glob($path);

$count = count($files);
$pageSize = 30;
$index = intval($_GET['ps']);
$index = (!$index || $index == 1) ? 1 : $index;

$start = (($index - 1) * $pageSize);

echo '文件总数：' . $count;
echo '<br /><br />';
for($i = $start; $i < ($index * $pageSize); $i++){
	
	if(empty($files[$i])) continue;
	
	$myName = explode("/", $files[$i]);
	$currentFile = end($myName);
	echo ($i + 1) . "、" . "文件名：" . "<a href=\"{$rootuRL}access.php?f={$currentFile}&ps={$index}&tocken={$tocken}\">" . $currentFile ."</a>" . "，";
	echo "大小：" . round(filesize($files[$i]) / 1024) . ' KB';
    echo '<br />';
    
    //读取并下载
    if($_GET['f'] && strpos($_GET['f'], 'txt') == true && strpos($_GET['f'], 'log')+1 && $currentFile == $_GET['f']){
    	ob_clean();
    	ob_end_clean();
    	ob_end_flush();
    	
		if(ini_get('zlib.output_compression')){	// 关闭压缩输出
			ini_set('zlib.output_compression', 'Off');
		}
		clearstatcache();	//清除文件的缓存状态
		
		$file_data =  explode(".", $currentFile);
		$file_type = $file_data[1];
		$download_name = $file_data[0];
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
		header("Content-Length: ".filesize($files[$i]));
		header("Content-Transfer-Encoding: binary");
		header("Expires: -1");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		@readfile($files[$i]); 
		exit;
    	
    }
}

//生成分页
echo '<br />';
$pageHtml = '';
for($j = 0; $j < ceil($count / $pageSize); $j++){
	$pageHtml .= '<a href="' . $rootuRL . 'access.php?ps='. ($j + 1) .'&tocken=' . $tocken . '">' . (($j + 1) == $index ? '[' . ($j + 1) . ']' : ($j + 1)) . '</a>';
	$pageHtml .= '&nbsp;&nbsp;';
}

echo $pageHtml;
echo '<br />';
echo '<span style="display: block;font-size: 10px;font-style: italic;padding-top:10px;">powerd by DHB</span>';
?>