<?php
/*  [NewTools!] 2015.07.21
   图片批量处理(小牛New)*/

error_reporting(E_ERROR|E_PARSE|E_STRICT);
//error_reporting(E_ALL);
//error_reporting(0);
ini_set('default_charset', 'utf-8');
set_time_limit(0);
date_default_timezone_set('PRC'); 

if($arrSiteConfig['web']==1) { 
	define('TOOL_PATH',getcwd());
}else{ 
	define('TOOL_PATH', dirname(__FILE__).'\\');
}

include_once(TOOL_PATH.'/config.php');
include_once(TOOL_PATH.'/common.php');

define("RESOURCE_PATH",TOOL_PATH.'/../../../../resource/');

 //Database
 define("DB_HOST", $arrSiteConfig['database']['host']);
 define("DB_USER", $arrSiteConfig['database']['username']);
 define("DB_PASSWORD", $arrSiteConfig['database']['password']);
 define("DB_DATABASE", $arrSiteConfig['database']['dataname']);
 define("DATABASEU", DB_DATABASE."_user.");
 define("DATATABLE", $arrSiteConfig['database']['datatable']);

include_once (TOOL_PATH."/../class/ezsql/shared/ez_sql_core.php");
include_once (TOOL_PATH."/../class/ezsql/mysql/ez_sql_mysql.php");
include_once (TOOL_PATH."/../class/db.class.php");
include_once (TOOL_PATH."/../class/input.class.php");
include_once (TOOL_PATH."/../class/functions.php");
include_once (TOOL_PATH."/../class/file.class.php");

$db = dbconnect::dataconnect()->getdb();

$strSplit = $arrSiteConfig['web']==1 ? '<br/>' : "\n\r";

$arrReturnResult = array(
	'status' => 100,
	'message' => '生成成功'
);

echo " -- now start --{$strSplit}";

// 读取行业配置数据
$arrIndustryOption = array();
$arrTempOption = $db->get_row("select *from ".DATABASEU.DATATABLE."_ty_option where Name='industry' ");
if(!empty($arrTempOption['Value'])){
	$arrIndustryOption = json_decode($arrTempOption['Value'],true);
}


$nGlobalindex = 0;


// 行业循环
if($arrIndustryOption){
	foreach($arrIndustryOption as $key=>$var){
		echo " - now industry - {$key} -{$strSplit}";

		if($var>100){
			$var =100;
		}

		//if(in_array($key,array(1,2,4,5,6))){
			//continue;
		//}

		for($i=0;$i<=$var;$i++){
			echo " - now industry - index - {$key} - {$i} -{$strSplit}";

			makeNewCompany($key);

			//if($i%5 ==0){
			//	sleep(1);
			//}
		}
		
		//sleep(2);
	}
}

echo " -- all done --{$strSplit}";

?>