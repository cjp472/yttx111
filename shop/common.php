<?php
/**
 * Class 公共类
 *
 * @author seekfor seekfor@gmail.com
 * @version 1.0 Tue March 01 10:02:08 CST 2010 
 */
	 include_once ("header.inc.php");

	 if(DHB_RUNTIME_MODE === 'experience'){
		include_once (SITE_ROOT_PATH.'/../manager/experience/site_config.php');
		include_once (SITE_ROOT_PATH."/../manager/experience/common.php");
	 }else{
		include_once (SITE_ROOT_PATH."/site_config.php");
	 }

	 //Site Infomation
     define("SITE_NAME", $Site_Config['sitename']);
	 define("DATA_PATH", SITE_ROOT_PATH.'/data/');
	 define("CART_PATH", SITE_ROOT_PATH.'/data/cart/');
	 define("RESOURCE_PATH", $Site_Config['resourceurl']);	
	 define("RESOURCE_NAME", dirname(SITE_ROOT_PATH).$Site_Config['resourcepath']);
	 define("ENCODE_KEY", 'fordhb');
	 define("Error_Display", 'html');
	 define("CONTTENT_SEARCH", 'like');
	 define("VERID", '2017051801');
	 define("KUDAIDIAPPKEY", '38ed54197745ff33');
	 define("WEIXIN_URL", 'http://wyy.dhb.hk/');
	 define("HELP_URL",'c,'.$_SESSION['cc']['cid'].','.$_SESSION['cc']['cusername']);
	 define("PAY_URL", $Site_Config['payurl']);
	 define("LOG_PATH", dirname(SITE_ROOT_PATH).'/tempdata/client/log');
	 //define("LOG_PATH", SITE_ROOT_PATH.'/data/log');
     //Database
     define("DB_HOST", $Site_Config['database']['host']);
     define("DB_USER", $Site_Config['database']['username']);
     define("DB_PASSWORD", $Site_Config['database']['password']);
     define("DB_DATABASE", $Site_Config['database']['dataname']);
     define("DATATABLE", $Site_Config['database']['datatable']);
     define("DATABASEU", $Site_Config['database']['dataname']."_user.");

	 //Template
	 $settmsg = @file_get_contents(RESOURCE_NAME.$_SESSION['cc']['ccompany']."/config.txt");	
	 if(!empty($settmsg)) $setarr = unserialize($settmsg);
	 if(!empty($setarr['template'])) $sv = $setarr['template']; else $sv = 'blue';
 	 $sv = 'red';

	 if($sv == 'default')
	 {
		define("CONF_PATH_COMPILE",SITE_ROOT_PATH."/data/temp/default");
		define("CONF_PATH_TPL",SITE_ROOT_PATH."/template/default");
	 }else{
		define("CONF_PATH_COMPILE",SITE_ROOT_PATH."/data/temp/blue");
		define("CONF_PATH_TPL",SITE_ROOT_PATH."/template/tpl");
	 }    
	 define("CONF_PATH_IMG","template/".$sv."/");
	 define("CACHE_LIFETIME","2");	 	 	 
	 define("CONF_PATH_CACHE",SITE_ROOT_PATH."/data/cache");
	 define("CONF_CACHE_ONOFF",false);
	 
	 include_once (SITE_ROOT_PATH."/license.php");
	 include_once (SITE_ROOT_PATH."/class/template.func.php");
	 include_once (SITE_ROOT_PATH."/class/ezsql/shared/ez_sql_core.php");
	 include_once (SITE_ROOT_PATH."/class/ezsql/mysql/ez_sql_mysql.php");
	 include_once (SITE_ROOT_PATH."/class/db.class.php");
	 include_once (SITE_ROOT_PATH."/class/input.class.php");
	 include_once (SITE_ROOT_PATH."/class/functions.php");
	 include_once (SITE_ROOT_PATH."/class/page.class.php");
	 include_once (SITE_ROOT_PATH."/class/error.class.php");
	 include_once (SITE_ROOT_PATH."/module/commondata.php");
	 
	 //define root url
	 $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
	 define("WEB_ROOT_URL", trim($protocol.$_SERVER['HTTP_HOST'], '/'));
	 
	 //define global cpath
	 define("WEB_SITE_PATH", dirname(SITE_ROOT_PATH));

	 //load global config
	 include_once (WEB_SITE_PATH."/global/global.config.php");
	 include_once (GLOBAL_CLASS_PATH."autoload.class.php");

	 if(DHB_RUNTIME_MODE === 'experience'){
		$sOldIndustry_experience = isset($_SESSION['industry']) ? trim($_SESSION['industry']) : '';
		$sIndustry_experience = getSafeIndustry($sOldIndustry_experience);
		$bError_experience = false;
		if(empty($sIndustry_experience) || !in_array($sIndustry_experience,$EXPERIENCE_INDUSTRY)){
			$bError_experience = true;
		}
		$sCp_experience = isset($_SESSION['ucc']['CompanyID']) ? encodeData(trim($_SESSION['ucc']['CompanyID'])) : '';

		// 判断用户是否已经提交过账号信息了
		if(!empty($_SESSION['cc']['cid']) && !empty($_SESSION['ucc']['CompanyID'])){
			$sCookieContact = isset($_COOKIE['experience_contact']) ? getSafeIndustry($_COOKIE['experience_contact']) : '';
			$bContact_excerience = $sCookieContact ? true : false;
		}

	}

	if($_SESSION['ucc']['CompanyID']){
		//动态获取客服信息
		$db = dbconnect::dataconnect()->getdb();	
		$companyid=$_SESSION['ucc']['CompanyID'];
		
		$sql_l  = "SELECT ContactName,ContactValue FROM ".DATATABLE."_order_contact where ContactCompany=".$companyid." limit 1";
		
		$customer_service	= $db->get_row($sql_l);
		$Symbol=empty($customer_service)? "" : ":";
		
		//获取用户的等级
		$valuearr = get_set_arr('clientlevel');
		$cid=$_SESSION['cc']['cid'];   
		$sql_l="select ClientLevel from ".DATATABLE."_order_client where ClientID=".$cid;
		$level=$db->get_var($sql_l);
		$c_level="";
		if($level){
			$level=explode(",",$level);
			$level=explode("_",$level[0]);
			$c_level= $level[0]=="A" ? $valuearr['A'][$level[1].'_'.$level[2]] : $valuearr['A']["level_1"] ;
		}else{
			$c_level=$valuearr['A']["level_1"];
		}
		$c_level="({$c_level})";
		
		
	}
	
	
	
?>