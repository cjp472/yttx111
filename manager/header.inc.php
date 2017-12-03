<?php
	 ini_set('display_errors', 0);
	 //error_reporting(E_ALL^E_NOTICE^E_WARNING);
	 error_reporting(0);
	 header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	 header('Content-Type: text/html; charset=utf-8'."\n");
	 define("SITE_ROOT_PATH",str_replace("\\","/",dirname(__FILE__)));
	 date_default_timezone_set('PRC');
	 ini_set('session.save_path',dirname(SITE_ROOT_PATH)."/sessiontemp/m");
	 $lifeTime = 24 * 3600;  // 保存一天 
	 session_set_cookie_params($lifeTime); 
	 ini_set('session.gc_maxlifetime',$lifeTime); 
	 session_start();
	 setcookie(session_name(), session_id(), time() + $lifeTime, "/"); 

	// 载入模式文件
	 include_once (SITE_ROOT_PATH.'/../global/mode.php');

	 //exit('本公司将于2014年12月14日00:00-01:30 对在线业务服务器进行调整，在调整期间内暂停访问， 特此通知！');
?>