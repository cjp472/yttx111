<?php
/*********************************
 * 自动载入需要的类文件
 * 
 * 
 */

class Aloader{
	
	static function loader($class_name = '') {
		
		//定义允许载入的目录
		$loader_path = array("class", "module");
		
		//自动载入Client端
		foreach($loader_path as $ctype){
			$file = SITE_ROOT_PATH.'/'.$ctype.'/'.$class_name.'.'.$ctype.'.php';
			file_exists($file) && include_once($file);
		}
		
		//自动载入Client端为包含class的文件
		$file = SITE_ROOT_PATH.'/class/'.$class_name.'.php';
		file_exists($file) && include_once($file);
		
		//自动载入Client端为包含Module的文件
		$file = SITE_ROOT_PATH.'/module/'.$class_name.'.php';
		file_exists($file) && include_once($file);
		
		//自动载入Manager端
		foreach($loader_path as $ctype){
			$file = SITE_ROOT_PATH.'/'.$ctype.'/'.$class_name.'.'.$ctype.'.php';
			file_exists($file) && include_once($file);
		}
		
		//自动载入 Global
		foreach($loader_path as $ctype){
			$file = WEB_SITE_PATH.'/global/'.$ctype.'/'.$class_name.'.'.$ctype.'.php';
			file_exists($file) && include_once($file);
		}
		
		return true; 
	}
}

//注册自动载入
spl_autoload_register(array('Aloader', 'loader'));

?>