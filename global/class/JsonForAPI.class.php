<?php
class JsonForAPI{
	
	public function __construct(){}
	
	
	/**
	 * 格式化API数据
	 * @param string $status
	 * @param string $message
	 */
	static public function formatMsg($status = '', $message = ''){
		
		return json_encode(array('status' => $status, 'message' => $message), JSON_UNESCAPED_UNICODE);
	}
	
}
