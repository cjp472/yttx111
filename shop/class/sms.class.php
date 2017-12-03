<?php
/**
 * Class sms
 * 数据操作
 * 
 * @author seekfor seekfor@gmail.com
 * @version 1.2 Mon Sep 25 20:43:17 CST 2006 
 */
// include_once (SITE_ROOT_PATH."/WebService/include/Client.php");
// include_once (SITE_ROOT_PATH."/soap.inc.php");

// $client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
// $client->setOutgoingEncoding("UTF-8");
// $GLOBALS['smsClient'] = $client;	//异步发送需要设置该项

class sms
{	
	function sms()
	{

	}

	function get_setsms($sid,$message)
	{
		
// 		global $client;
// 		$client = empty($client) ? $GLOBALS['smsClient'] : $client;
		
// 		$db = dbconnect::dataconnect()->getdb();	

// 		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['cc']['ccompany']." and SetName='sms' limit 0,1");
// 		if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);  else $valuearr = array("0");
		
// 		if(!empty($setinfo['SetValue']) && in_array($sid, $valuearr))
// 		{
// 		    //Changed at 2015-12-16 By lxc Remark:订货端新增订单短信通知业务员
// 		    $sendphone = '';
// 		    if($sid == '9')
// 		    {
// 		        $saler = $db->get_row("SELECT u.UserMobile,r.SalerID FROM ".DATATABLE."_order_salerclient r inner join 
// 		                ".DATABASEU.DATATABLE."_order_user u on r.SalerID = u.UserID
// 		                where r.ClientID=".$_SESSION['cc']['cid']." and  UserCompany = ".$_SESSION['ucc']['CompanyID']." and UserFlag!='1' and UserType='S' limit 0,1" );

// 	            if(!empty($saler['UserMobile']))
// 	            {
// 	                $sendphone = $saler['UserMobile'];
// 	            }
// 		    }
// 		    else 
// 		    {
// 		        if(!empty($valuearr['Mobile']['FinancePhone']) && $sid=="3") $sendphone = $valuearr['Mobile']['FinancePhone'];
// 		        if(!empty($valuearr['Mobile']['LibaryPhone']) && $sid=="6")  $sendphone = $valuearr['Mobile']['LibaryPhone'];
// 		        if(empty($sendphone))
// 		        {
// 		            if(!empty($valuearr['Mobile']['MainPhone']))
// 		            {
// 		                $sendphone = $valuearr['Mobile']['MainPhone'];
// 		            }
// 		            else if(!empty($_SESSION['ucc']['CompanyMobile']))
// 		            {
// 		                $sendphone = $_SESSION['ucc']['CompanyMobile'];
// 		            }
// 		        }
// 		    }
			
//             if(!empty($sendphone)) {
//                 $phones = explode(",",$sendphone);
//                 foreach($phones as $phone) {
//                     if(self::isPhone($phone) && !empty($_SESSION['ucc']['SmsNumber']) && $_SESSION['ucc']['SmsNumber'] > 1) self::send_sms($phone,$message,$_SESSION['cc']['cid']);
//                 }
//                 return true;
//             }
// 			/*if(!empty($sendphone) && self::isPhone($sendphone))
// 			{
// 				if(!empty($_SESSION['ucc']['SmsNumber']) && $_SESSION['ucc']['SmsNumber'] > 1) self::send_sms($sendphone,$message,$_SESSION['cc']['cid']);
// 				return true;
// 			}*/
// 			return false;
// 		}
// 		return false;
	}


	function send_sms($mobile,$message,$cid)
	{
// 		global $client;
// 		$client = empty($client) ? $GLOBALS['smsClient'] : $client;
		
// 		$db	    = dbconnect::dataconnect()->getdb();
// 		if(!empty($mobile) && self::isPhone($mobile))
// 		{
// 			$mobilearr[] = $mobile;
// 			$statusCode2 = $client->login();
// 			$statusCode = $client->sendSMS($mobilearr,$message);
			
// 			$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['cc']['ccompany']." limit 0,1");

			
// 			if(!empty($ucinfo['CompanyDatabase'])) $datacbase = DB_DATABASE."_".$ucinfo['CompanyDatabase']; else $datacbase = DB_DATABASE;

// 			$db->query("insert into ".$datacbase.".".DATATABLE."_order_sms_post(PostCompany,PostUser,PostClient,PostDate,PostPhone,PostContent,PostFlag) values(".$_SESSION['cc']['ccompany'].",'".$_SESSION['cc']['cusername']."',".$_SESSION['cc']['cid'].",".time().",'".$mobile."','".$message."','".$statusCode."')");
// 			$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber-1 where CS_Company=".$_SESSION['cc']['ccompany']);
// 			$smsdatanum = $db->get_row("SELECT CS_ID,CS_SmsNumber FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$_SESSION['cc']['ccompany']." limit 0,1");
// 			$_SESSION['ucc']['SmsNumber'] = $smsdatanum['CS_SmsNumber'];	
			
// 			return $statusCode;
// 		}else{
// 			return false;
// 		}
	}

	function reg_setsms($cid,$sid,$message,$dbname)
	{
// 		global $client;
// 		$db = dbconnect::dataconnect()->getdb();	

// 		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cid." and SetName='sms' limit 0,1");
// 		if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);  else $valuearr = array("0");
		
// 		if(!empty($setinfo['SetValue']) && in_array($sid, $valuearr))
// 		{
// 			$sendphone = $valuearr['Mobile']['MainPhone'];
// 			if(self::isPhone($sendphone)){
// 				$smsdatanum = $db->get_row("SELECT CS_ID,CS_SmsNumber FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$cid." limit 0,1");
// 				if($smsdatanum['CS_SmsNumber'] >= 1){

// 					$mobilearr[] = $sendphone;
// 					$statusCode2 = $client->login();
// 					$statusCode  = $client->sendSMS($mobilearr,$message);

// 					$db->query("insert into ".$dbname.DATATABLE."_order_sms_post(PostCompany,PostUser,PostClient,PostDate,PostPhone,PostContent,PostFlag) values(".$cid.",'reg',0,".time().",'".$sendphone."','".$message."','".$statusCode."')");
// 					$db->query("update ".DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber-1 where CS_Company=".$cid);
// 				}
// 			}
// 		}
	}
	

	function isPhone($var)
	{
// 		$var = trim($var);
// 		if(preg_match ("/^[-]?[0-9]+([\.][0-9]+)?$/", $var))
// 		{
// 			if(strlen($var) == 11) return true; else return false;
// 		}else{
// 			return false;
// 		}
	}

//END	
}
?>