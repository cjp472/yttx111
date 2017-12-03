<?php
	 header('Content-Type: application/json; charset=utf-8'."\n"); 
	 header("Access-Control-Allow-Origin:*"); //跨域访问
	 define("SITE_ROOT_PATH",str_replace("\\","/",dirname(__FILE__)));
	 date_default_timezone_set('PRC'); 
	 //ini_set('display_errors', 1);
	 error_reporting(0);
	 //Site Infomation
	 define("IS_TIYAN","T");
	 define("CART_PATH", '/mnt/data/web/tc_dhb_net_cn/data/cart/');
	 define("RESOURCE_PATH", 'http://ty.dhb.net.cn/resource/');
	 define("RESOURCE", '/mnt/data/web/exp/resource');
	 define("DATA_PATH", 'data/');
	 define("CACHE_LIFETIME", '12');
	 define("CONF_PATH_CACHE", 'data/cache');	 
	 define("LOG_PATH", 'data/log/');
	 define("API_KEY", 'dhb_dkfji39_fj3Kdiekfogf');
	 define('WEB_APP_URL', 'http://sj.dhb.hk/');
	 define('WEB_API_URL', 'http://wy.dhb.net.cn/mobileApi/');

     //Database
     define("DB_HOST", "testdhbhk.mysql.rds.aliyuncs.com");
     define("DB_USER", "dbexpuser");
     define("DB_PASSWORD", "exp_kdeIue38fdjf");
     define("DB_DATABASE", "db_exp");
     define("DB_DATABASEU", "db_exp_user.");
     define("DATATABLE", "rsung");	 	 
	 
	 include_once (SITE_ROOT_PATH."/class/ezsql/shared/ez_sql_core.php");
	 include_once (SITE_ROOT_PATH."/class/ezsql/mysql/ez_sql_mysql.php");
	 include_once (SITE_ROOT_PATH."/class/db.class.php");
	 include_once (SITE_ROOT_PATH."/class/input.class.php");
	 include_once (SITE_ROOT_PATH."/class/functions.php");
	 include_once (SITE_ROOT_PATH."/class/KLogger.php");
	 
?>