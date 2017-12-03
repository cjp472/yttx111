<?php

/**
 * 系统日志记录操作类
 * 负责记录系统订单交互以及支付的日志信息
 * 在设置日志记录目录时，如果是采用类似 write_log/url 的参数，则表示在 write_log 
 * 目录下的的日志文件名前添加 url 字符作为前缀，write_log 目录将自动创建，参数最后不要跟 /
 * 记录的日志文件名将以文件前缀(如果有)-当日日期的文本文件存储，例如：url-2013-08-08.txt
 * */

class Log{

	private $default_path = "";
	private $file_path = "";
	
	public function __construct(){
		$this->default_path = GLOBAL_LOG_PATH;	//日志文件记录目录
		!file_exists($this->default_path) && mkdir($this->default_path, 0777);
	}
	
	/**
	 * @name 设置日志记录路径
	 * @author hugh
	 * @param 
	 * 	$file_name: 记录目录和文件前缀
	 * @return object
	 * */
	public function set_file($file_prefix = ""){
		$file_prefix = trim($file_prefix, "/");
		empty($file_prefix) && $this->_debug(__FUNCTION__, "日志记录目录不能为空");
		$path_name = explode("/", $file_prefix);
		$path = $path_name[0];	//路径
		$prefix = empty($path_name[1]) ? "" : $path_name[1];	//文件名
		!file_exists($this->default_path.$path) && mkdir($this->default_path.$path, 0777);	//创建指定的目录
		$this->file_path = $this->default_path.$path."/".date("Y-m");
		!file_exists($this->file_path) && mkdir($this->file_path, 0777);	//创建以年-月为单位的目录
		
		$this->file_path = $this->file_path."/".date("Y-m-d").".".$prefix.".txt";
		$this->write_index($path);
		
		return $this;
	}
	
	/**
	 * @name 写入index.html文件，防止服务器为设置安全参数列出目录
	 * 写入到设置文件夹一级目录
	 * @author hugh
	 * @param string $cpath:创建的目录一级 
	 * @return void
	 * */
	private function write_index($cpath){
		$path_data = explode("/", dirname($this->file_path));
		
		$path_index = count($path_data) - 1;
		for($i = $path_index; $i >= 0; $i--){
			$check_path = implode("/", $path_data);
			$index_file = $check_path."/index.html";
			!file_exists($index_file) && file_put_contents($index_file, "");
			
			if($path_data[$i] == $cpath) break;
			unset($path_data[$i]);
		}
	}
	
	//记录日志
	public function log($message = ""){
		empty($message) && $this->_debug(__FUNCTION__, "日志记录信息不能为空");
		$handle = fopen($this->file_path, "ab");
		fwrite($handle, $message);
		fclose($handle);
	}

	/**
	 * @name 调试信息
	 * @author hugh
	 * @param 
	 * 	$fun: 出现错误的方法名称
	 * 	$message: 错误信息
	 * @return void
	 * */
	private function _debug($fun = "", $message = ""){
		echo "<b>方法所在类名称:</b> ". __CLASS__."<br />";
		echo "<b>终止的方法名称:</b> ".$fun."<br />";
		echo "<b>错误信息:</b> <font color=\"red\">".$message."</font><br />";
		exit(0);	
	}

}
?>