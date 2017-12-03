<?php 
set_time_limit(120);
error_reporting(0);
define("SITE_ROOT_PATH","/mnt/web/m_dhb_hk");
include_once (SITE_ROOT_PATH."/site_config.php");

     //Database
     define("DB_HOST", $Site_Config['database']['host']);
     define("DB_USER", $Site_Config['database']['username']);
     define("DB_PASSWORD", $Site_Config['database']['password']);
     define("DB_DATABASE", $Site_Config['database']['dataname']);
     define("DATATABLE", $Site_Config['database']['datatable']);   
     define("DATABASEU", $Site_Config['database']['dataname']."_user.");

   include_once (SITE_ROOT_PATH."/class/ezsql/shared/ez_sql_core.php");
   include_once (SITE_ROOT_PATH."/class/ezsql/mysql/ez_sql_mysql.php");
   include_once (SITE_ROOT_PATH."/class/db.class.php");


$db  = dbconnect::dataconnect()->getdb();

$sql = "call ".DATABASEU."sum_companyInfo();";

$db->query($sql);
exit('over');

?>