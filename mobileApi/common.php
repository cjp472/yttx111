<?php
	 header('Content-Type: application/json; charset=utf-8'."\n"); 
	 header("Access-Control-Allow-Origin:*"); //跨域访问
	 define("SITE_ROOT_PATH",str_replace("\\","/",dirname(__FILE__)));
	 date_default_timezone_set('PRC'); 
	 ini_set('display_errors', 0);
	 error_reporting(E_ALL);
	 //Site Infomation
	 define("IS_TIYAN","T");
	 define("CART_PATH", dirname(SITE_ROOT_PATH).'/shop/data/cart/');
	 define("RESOURCE_PATH", 'http://res.yitong111.com/');
	 define("DATA_PATH", 'data/');
	 define("CACHE_LIFETIME", '12');
	 define("CONF_PATH_CACHE", 'data/cache');	 
	 define("LOG_PATH", 'data/log/');
	 define("AliPay_LOG_PATH", 'logs/alipayapi');
	 define("API_KEY", 'dhb_dkfji39_fj3Kdiekfogf');
	 define('WEB_APP_URL', 'http://bmb.yitong111.com/mobile/');
	 define('WEB_API_URL', 'http://bmb.yitong111.com/mobileApi/');
	 define('WEB_TM_URL', 'http://bmb.yitong111.com/wxqy/');//取ticket地址

	 define("CorpID","wxc227fd13cc51c6f1");//企业ID
	 define("Suite_id","tj4e38773e39a823b9");//套件ID
	 define("Suite_secret","hnUJ3WVAK9eAVm8Gdn_I2Ieik3Ok3ilWlbcGk2Te94LWeVNj1aNFYIOBSXMBNPm5");//套件Secret 
	 define("Token","Bv0cpwuINAXfIKn18w3khxGUPKlmIS");
     define("EncodingAESKey","ZgY4NhxPVoFva9JGrmvKaKypzHPvHjY4mDjGTyt6Lnt");
	 define("providersecret","Sl0RSi87JtCJhbP53nN-L4mlWE8I11JP22mLX2exdMfowXsrsSYETTy6DYXMwuOm");

	 //define root url
	 $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
	 define("WEB_ROOT_URL", rtrim(trim($protocol.$_SERVER['HTTP_HOST'], '/'),'.'));
	 
	 	 
     //Database
    define("DB_HOST", "172.19.224.205:3306");
    define("DB_USER", "ftp-etong");
    define("DB_PASSWORD", "yttx123456");
    define("DB_DATABASE", "etong_db_live");
    define("DB_DATABASEU", "etong_db_live_user.");
    define("DATATABLE", "rsung");	
	 
	 include_once (SITE_ROOT_PATH."/class/ezsql/shared/ez_sql_core.php");
	 include_once (SITE_ROOT_PATH."/class/ezsql/mysql/ez_sql_mysql.php");
	 include_once (SITE_ROOT_PATH."/class/db.class.php");
	 include_once (SITE_ROOT_PATH."/class/input.class.php");
	 include_once (SITE_ROOT_PATH."/class/functions.php");
	 include_once (SITE_ROOT_PATH."/class/KLogger.php");
?>