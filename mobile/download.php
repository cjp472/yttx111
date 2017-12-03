<?php
download::download_file('app/dhb.apk');
/**
 * +++++++++++++++++++++++++++++++++++++
 * +++++++++++	文件下载类	++++++++++++
 * +++++++++++++++++++++++++++++++++++++
 * */

class download{

	public function __construct(){}
	
	/**
	 * @name 适应浏览器, 文件下载
	 * @author hugh
	 * @param 
	 * 	$file_path ： string 文件下载路径
	 * 	$rename ： string 重命名下载的文件, 支持中文
	 * */
	public function download_file($file_path = "", $rename = ""){
		
		empty($file_path) && exit("缺少下载文件路径");
		
		if(ini_get('zlib.output_compression')){	// 关闭压缩输出
			ini_set('zlib.output_compression', 'Off');
		}

		clearstatcache();	//清除文件的缓存状态
		
		$file_name = end(explode("/", $file_path));		//文件名		
		$file_data = explode(".", $file_name);
		$file_type = $file_data[1];
		$download_name = empty($rename) ? $file_data[0] : $rename;
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
		header("Content-Length: ".filesize($file_path));
		header("Content-Transfer-Encoding: binary");
		header("Expires: -1");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		@readfile($file_path); 
		
	}
}
?>