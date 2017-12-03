<?php
/**
 * Class sms
 * 发送短信
 * 
 * @author seekfor seekfor@gmail.com
 * @version 1.2 Mon Sep 25 20:43:17 CST 2006 
 */

include_once ("../WebService/include/Client.php");
include_once ("../soap.inc.php");

class sms
{	
  	public function __construct() {
		$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
		$client->setOutgoingEncoding("UTF-8");
  	}

	public function get_setsms($sid,$cidarr,$message)
	{
		global $db;

		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cidarr['CompanyID']." and SetName='sms' limit 0,1");

		if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']); else $valuearr = array("0");
		if(!empty($setinfo['SetValue']) && in_array($sid, $valuearr))
		{
			$cinfo = $db->get_row("SELECT ClientID,ClientCompanyName,ClientMobile FROM ".DATATABLE."_order_client where ClientCompany = ".$cidarr['CompanyID']." and ClientID=".$cidarr['ClientID']." limit 0,1");
			if(!empty($_SESSION['uc']['SmsNumber']) && $_SESSION['uc']['SmsNumber'] > 1) self::send_sms($cinfo['ClientMobile'],$message,$cid);

			return true;
		}
		return false;
	}


	public function send_sms($mobile,$message,$cid)
	{
		global $client,$db;

		if(!empty($mobile) && self::isPhone($mobile))
		{
			$mobilearr[]    = $mobile;
			$statusCode2    = $client->login();
			$statusCode     = $client->sendSMS($mobilearr,$message);
			
			$db->query("insert into ".DATATABLE."_order_sms_post(PostCompany,PostUser,PostClient,PostDate,PostPhone,PostContent,PostFlag) values(".$_SESSION['uinfo']['ucompany'].",'".$_SESSION['uinfo']['username']."',".$cid.",".time().",'".$mobile."','".$message."','".$statusCode."')");
			$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber-1 where CS_Company=".$_SESSION['uinfo']['ucompany']."");
			$smsdatanum = $db->get_row("SELECT CS_ID,CS_SmsNumber FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$_SESSION['uinfo']['ucompany']." limit 0,1");	
			
			return $statusCode;
		}else{
			return false;
		}
	}


	function isPhone( $var )
	{
		$var = trim($var);
		if(preg_match ("/^[-]?[0-9]+([\.][0-9]+)?$/", $var))
		{
			if(strlen($var) == 11) return true; else return false;
		}else{
			return false;
		}
	}

//END	
}
?>