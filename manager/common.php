<?php
	 include_once ("header.inc.php");

	 if(DHB_RUNTIME_MODE === 'experience' || READ_EXP === true){
		include_once (SITE_ROOT_PATH.'/experience/site_config.php');
	 }else{
		include_once (SITE_ROOT_PATH."/site_config.php");
	 }

     if(DHB_RUNTIME_MODE == 'experience') {
         include_once (SITE_ROOT_PATH."/experience/common.php");
     }

	 //Site Infomation
     define("SITE_NAME", $Site_Config['sitename']);
	 define("RESOURCE_URL", $Site_Config['resourceurl']);	 
	 define("RESOURCE_PATH", dirname(SITE_ROOT_PATH).$Site_Config['resourcepath']);
	 define("DATA_PATH", SITE_ROOT_PATH.'/data');
	 //define("LOG_PATH", SITE_ROOT_PATH.'/data/log');
	 define("LOG_PATH", dirname(SITE_ROOT_PATH).'/tempdata/manager/log');
	 define("ENCODE_KEY", 'fordhb');
	 define("Error_Display", 'html');
	 define("CACHE_LIFETIME", '24');
	 define("CONF_PATH_CACHE",SITE_ROOT_PATH."/data/cache");
	 define("CONTTENT_SEARCH", 'like');
	 define("VERID", '20151105');
	 define("KUDAIDIAPPKEY", '38ed54197745ff33');

     //Database
     define("DB_HOST", $Site_Config['database']['host']);
     define("DB_USER", $Site_Config['database']['username']);
     define("DB_PASSWORD", $Site_Config['database']['password']);
     define("DB_DATABASE", $Site_Config['database']['dataname']);
     define("DATATABLE", $Site_Config['database']['datatable']);
     define("DATABASEU", $Site_Config['database']['dataname']."_user.");

     
	 include_once (SITE_ROOT_PATH."/license.php");
	 include_once (SITE_ROOT_PATH."/class/ezsql/shared/ez_sql_core.php");
	 include_once (SITE_ROOT_PATH."/class/ezsql/mysql/ez_sql_mysql.php");
	 include_once (SITE_ROOT_PATH."/class/db.class.php");
	 include_once (SITE_ROOT_PATH."/class/input.class.php");
	 include_once (SITE_ROOT_PATH."/class/functions.php");
	 include_once (SITE_ROOT_PATH."/class/page.class.php");
	 include_once (SITE_ROOT_PATH."/class/error.class.php");
	 include_once (SITE_ROOT_PATH."/class/KLogger.php");

	 //define root url
	 $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
	 define("WEB_ROOT_URL", trim($protocol.$_SERVER['HTTP_HOST'], '/'));
	 
	 //define global cpath
	 define("WEB_SITE_PATH", dirname(SITE_ROOT_PATH));
	 
	 //load global config
	 include_once (WEB_SITE_PATH."/global/global.config.php");
	 include_once (GLOBAL_CLASS_PATH."autoload.class.php");
?>
