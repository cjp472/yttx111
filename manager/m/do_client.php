<?php
$menu_flag = "client";
include_once ("header.php");
include_once ("../class/sms.class.php");
include_once ("../class/letter.class.php");


if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	
	exit();

}


$ip = $_SERVER["REMOTE_ADDR"];

$in = $inv->_htmlentities($in);


if($in['m'] == 'check_client_no') {
    $is_exists = empty($in['client_no']) ? false : check_client_no($db,$in['client_no'],(int)$in['client_id']);
    exit($is_exists ? ":-(" : "ok");
}

if($in['m'] == 'check_client_name') {
    $is_exists = empty($in['client_company_name']) ? false : check_client_name($db,$in['client_company_name'],(int)$in['client_id']);
    exit($is_exists ? ':-(' : 'ok');
}

if($in['m']=="delete")
{
    $in['ID'] = intval($in['ID']);
	if(!($in['ID'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	if(DHB_RUNTIME_MODE === 'experience'){
		// 禁止删除系统药店 | 2015/07/20 by 小牛New
		$arrDealers = $db->get_row("select ClientID,IsSystem from ".DATABASEU.DATATABLE."_order_dealers where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
		if(empty($arrDealers['ClientID'])){
			exit('待删除的数据不存在!');
		}
		
		if($arrDealers['IsSystem']=='1'){
			exit('体验版禁止删除系统预置药店，正式版无此功能限制!');
		}
	}
	
	$upsql = "update ".DATATABLE."_order_client c inner join ".DATABASEU.DATATABLE."_order_dealers d on c.ClientID = d.ClientID 
	        set c.ClientFlag=1,d.ClientFlag=1 
	        where c.ClientID = ".$in['ID']." and c.ClientCompany=".$_SESSION['uinfo']['ucompany'];	
	
	if($db->query($upsql))
	{
        $dSessID = $db->get_var("SELECT Session_id FROM ".DATABASEU.DATATABLE."_order_dealers WHERE ClientID=".$in['ID']." AND ClientCompany=" . $_SESSION['uinfo']['ucompany']);
        if(!empty($dSessID)) {
            $session_path = ini_get('session.save_path');
            $session_file = $session_path . '/sess_' . $dSessID;
            $session_file = str_replace('/m/','/c/', $session_file);

            if(is_file($session_file)) {
                @unlink($session_file);
            }
        }
        
// 		$db->query("update ".DATABASEU.DATATABLE."_order_dealers set ClientFlag=1 where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']);
		
		exit('ok');
	
	}else{
		exit('删除不成功!');
	
	}

}

if($in['m']=="delete_all")
{
	if(empty($in['ID'])) exit('非法操作!');
	$in['ID'] = substr($in['ID'],0,strlen($in['ID'])-1);
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	if(DHB_RUNTIME_MODE === 'experience'){
		// 禁止删除系统药店 | 2015/07/20 by 小牛New
		exit('体验版禁止批量删除药店，正式版无此功能限制!');
	}
	
	$upsql = "update ".DATATABLE."_order_client set ClientFlag=1 where ClientID IN (".$in['ID'].") and ClientCompany=".$_SESSION['uinfo']['ucompany'];	

	if($db->query($upsql))
	{
		$db->query("update ".DATABASEU.DATATABLE."_order_dealers set ClientFlag=1 where ClientID IN (".$in['ID'].") and ClientCompany=".$_SESSION['uinfo']['ucompany']);
		//删除微信绑定
		$sql_l = "delete from ".DATABASEU.DATATABLE."_order_weixin where UserID IN (".$in['ID'].") and UserType='C'" ;
    	$db->query($sql_l);
		exit('ok');
	
	}else{
		exit('删除不成功!');
	}
}


if($in['m']=="wxqy_follow") //邀请关注企业微信号
{
	 $fLog = KLogger::instance(LOG_PATH);
	//if(empty($in['ID'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$tempwx = $db->get_row("SELECT CorpID,CompanyID,Permanent_code,agentidinfo FROM ".DATABASEU.DATATABLE."_order_weixinqy WHERE CompanyID='{$_SESSION['uinfo']['ucompany']}'");
	if($tempwx['CompanyID']!=""){
		$tempnotinid = $db->get_results("select ClientID from ".DATABASEU.DATATABLE."_order_clientfollow where ClientCompany=".$_SESSION['uinfo']['ucompany']."");
		foreach($tempnotinid as $a=>$b){
			$newnotinid[$a]=$b['ClientID'];
		}
		if(empty($tempnotinid)){
			$clientdata=$db->get_results("SELECT ClientID,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientMobile,ClientEmail FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8)"); //取出未邀请的
		}else{
			$notinid=implode(",",$newnotinid);
			$clientdata=$db->get_results("SELECT ClientID,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientMobile,ClientEmail FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID not in (".$notinid.") and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8)"); //取出未邀请的
		}
		if(empty($clientdata)) exit('已经全部邀请完毕!');

		$tempsuite_ticket=file(WEB_ROOT_URL."/wxqy/data/ticket.txt");
		$tempsuite_access_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token",json_encode(array('suite_id'=>"tj4e38773e39a823b9",'suite_secret'=>"hnUJ3WVAK9eAVm8Gdn_I2Ieik3Ok3ilWlbcGk2Te94LWeVNj1aNFYIOBSXMBNPm5",'suite_ticket'=>trim($tempsuite_ticket[0])))); //获取应用套件令牌
	    $tempaccess_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token?suite_access_token=". $tempsuite_access_token['suite_access_token'],json_encode(array('suite_id'=>"tj4e38773e39a823b9",'auth_corpid'=>$tempwx['CorpID'],'permanent_code'=>$tempwx['Permanent_code']))); //通过永久授权码获取ACCESS_TOKEN

		$tempagentidinfo=explode(",",$tempwx['agentidinfo']);
		foreach($tempagentidinfo as $a=>$b){
			$temparr=explode("|",$b);
			if($temparr[1]=="系统管理"){
				$tempparty=explode("&",$temparr[2]);//取出授权部门
				$allowparty=$tempparty[0];  //取第一个部门
			}
		}
		//$fLog->logInfo("allowparty" . " params: ",$allowparty);
		foreach($clientdata as $a=>$b){
			$client_id = $b['ClientID'];
			$client_name=$b['ClientCompanyName'];
			$client_email=$b['ClientEmail'];
			$client_mobile=$b['ClientMobile'];
			$client_area=$b['ClientArea'];
			$client_companyname=$b['ClientCompanyName'];	
			$tempcreate=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=".$tempaccess_token['access_token'],wx_json_encode(array('userid'=>$client_id,'name'=>$client_name,'mobile'=>$client_mobile,'email'=>$client_email,'department'=>$allowparty)));//添加成员入通讯录
			$fLog->logInfo("create" . " params: ",var_export($tempcreate,TRUE));
				if($tempcreate['errmsg']=="created"){ //创建成功邀请关注
					/**
					$tempfollow=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/invite/send?access_token=".$tempaccess_token['access_token'],json_encode(array('userid'=>$client_id)));
					$fLog->logInfo("invite" . " params: ",$tempfollow);
					**/
						$db->query("insert into ".DATABASEU.DATATABLE."_order_clientfollow (ClientID,ClientCompany,ClientArea,ClientName,ClientEmail,ClientMobile,ClientDate,ClientCompanyName) values (".$b['ClientID'].",'".$_SESSION['uinfo']['ucompany']."','".$client_area."','".$client_name."','".$client_email."','".$client_mobile."',".time().",'".$client_companyname."')");
				}
			 }
	}else{
		exit('nofollow');
	}
	exit('ok');
}



if($in['m'] == 'wxqy_clientimport'){ //微信企业号药店导入
    //ini_set('display_errors',1);
    //error_reporting(E_ALL);
    //EXCEL导入药店
    $company_id = $_SESSION['uinfo']['ucompany'];
    $areaList = $db->get_results("SELECT AreaID,AreaName FROM ".DATATABLE."_order_area WHERE AreaCompany={$company_id}");
    $areaList = array_column($areaList ? $areaList : array(),'AreaID','AreaName');


    $InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");
    $allowCnt = (int)$_SESSION['uc']['Number'];
    $needCnt = (int)$InfoDataNum['clientrow'] + count($in['clientdata']);

    $msg = '您只有 '.$_SESSION['uc']['Number'].' 个授权药店， 您已全部用完，请联系开发商增加授权用户';
    if($InfoDataNum['clientrow'] >= $_SESSION['uc']['Number'])
	{
        exit( $msg );
    } else if($needCnt > $allowCnt){
        exit( "导入药店还需要" . ($needCnt - $allowCnt) . "个授权用户,请联系开发商增加授权用户" );
    }

    $list = array();
	$clientdata=explode(",",$in['clientdata']);
    foreach($clientdata as $a=>$b){
        if(empty($b)) {
            continue;
        }
        $data =explode("|",$b);
        $tmp['ClientNO']=""; //A 药店编号
        $tmp['ClientCompanyName']=$data[1]; //B 药店名称
        $tmp['ClientName']=$data[0];//C 登录账号
        $tmp['ClientPassword']=""; //D 登录密码
        $tmp['ClientMobile']=$data[2]; //E 手机号-可用手机号登录
        $tmp['ClientTrueName']="";//G 联系人
        $tmp['ClientPhone']=""; //H 联系电话
        $tmp['ClientFax']=""; //I 传真
        $tmp['ClientEmail']=$data[3]; //J 邮箱
        $tmp['ClientAdd']=""; //K 地址
        $tmp['ClientAbout']=""; //L 备注
        $tmp['ClientLevel_A']=""; //M 级别
        $tmp['ClientSetPrice']=""; //N 执行价格
        $tmp['ClientPercent']="";//O 折扣
        $tmp['AccountName']="";//P 开户名称
        $tmp['BankName']="";//Q 开户银行
        $tmp['BankAccount']=""; //R 银行账号
        $tmp['InvoiceHeader']=""; //S 开票抬头
        $tmp['TaxpayerNumber']=""; //T 纳税人识别号

        $tmp['ClientArea'] = $areaList[$tmp['ClientArea']];

        //密码生成
        $tmp['ClientPassword'] = $tmp['ClientPassword'] ? $tmp['ClientPassword'] : '123456';
        //手机号验证
        $tmp['ClientMobile'] = is_phone($tmp['ClientMobile']) ? $tmp['ClientMobile'] : '';
        //级别替换
        //$tmp['ClientLevel_A'] = $tmp['ClientLevel'];
        unset($tmp['ClientLevel']);
        //执行价格
        if($tmp['ClientSetPrice'] == '价格二'){
            $tmp['ClientSetPrice'] = 'Price2';
        }else{
            $tmp['ClientSetPrice'] = 'Price1';
        }
        //折扣
        $tmp['ClientPercent'] = $tmp['ClientPercent'] ? $tmp['ClientPercent'] : 10;
        if((int)$tmp['ClientPercent']<1 || (int)$tmp['ClientPercent']>10){
            $tmp['ClientPercent'] = 10;
        }

        $list[] = $tmp;
    }
    $err = array();
    $letter   = new letter();
	$rhtml='<fieldset title="导入结果" style="width:90%; height:96%;overflow-y:scroll;font-size:14px;line-height:25px;"><legend>导入结果</legend>默认密码为123456<br>';
    foreach($list as $key => $item) {
        $result = insertClient($item,$letter);
		
      if(is_string($result)){
           $rhtml.= "用户".$item['ClientCompanyName']."(登录账号:".$_SESSION['uc']['CompanyPrefix']."-".$item['ClientName'].")".$result."<br>";
       }else{
           $rhtml.= "用户".$item['ClientCompanyName']."(登录账号:".$_SESSION['uc']['CompanyPrefix']."-".$item['ClientName'].")导入成功!<br>";
	   }
    }
	$rhtml.='<input name="closebutton" type="button" onclick="testclose();" class="button_1"value="关　闭" /></fieldset>';
	exit($rhtml);
}

//*****recycle************/

if($in['m']=="restore")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0");
	$msg = '您只有 '.$_SESSION['uc']['Number'].'个授权药店， 您已全部用完，请联系开发商增加授权用户';
	if($InfoDataNum['clientrow'] >= $_SESSION['uc']['Number']) exit($msg);
	$upsql = "update ".DATATABLE."_order_client set ClientFlag=0 where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
    {
		$db->query("update ".DATABASEU.DATATABLE."_order_dealers set ClientFlag=0 where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']);
		exit('ok');
	}else{
		exit('还原不成功!');
	}
}



if($in['m']=="quite_delete")
{
	if(!intval($in['ID'])) exit('非法参数!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	$InfoDataNum = $db->get_row("SELECT count(*) AS orderrow FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$in['ID']);
	if(!empty($InfoDataNum['orderrow']))
	{
		exit('该药店已有订单数据，不能删除！');
	}else{
		$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_client where  ClientID = ".$in['ID']." and ClientCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
		
		if(!empty($InfoData['ClientGUID']) && ($InfoData['ERP'] == 'T'))
		{
		    exit('该档案已与ERP软件关联，不能删除!');
		}
		else 
		{
		    $infodatamsg = serialize($InfoData);
		    
		    $upsql =  "delete ".DATATABLE."_order_client,".DATABASEU.DATATABLE."_order_dealers from ".DATATABLE."_order_client inner join ".DATABASEU.DATATABLE."_order_dealers 
		            on ".DATATABLE."_order_client.ClientID = ".DATABASEU.DATATABLE."_order_dealers.ClientID 
		            where ".DATATABLE."_order_client.ClientID = ".$in['ID']." and ".DATATABLE."_order_client.ClientCompany=".$_SESSION['uinfo']['ucompany']." and ".DATATABLE."_order_client.ClientFlag=1 ";

		    if($db->query($upsql))
		    {
		        $sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_client.php?m=quite_delete&ID=".$in['ID']."','删除药店(".$in['ID'].")','".$infodatamsg."',".time().")";
		        $db->query($sqlex);
// 		        $db->query("delete from ".DATABASEU.DATATABLE."_order_dealers where ClientID = ".$in['ID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=1 limit 1");
				//删除微信绑定
				$sql_l = "delete from ".DATABASEU.DATATABLE."_order_weixin where UserID=".$in['ID']." and UserType='C' " ;
    			$db->query($sql_l);
		        	
		        exit('ok');
		    
		    }else{
		        exit('删除不成功!');
		    }
		}
	}

}

/***********药店**************/
if($in['m']=="content_add_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$in['ClientName']	  = strtolower($in['ClientName']);
	
	$in['ClientPassword'] = strtolower($in['ClientPassword']);
	if(!is_filename($in['ClientName'])) exit('okname');
	
	if(preg_match("/^\d+$/",$in['OrderAmount'])){
		$in['OrderAmount']=(int)$in['OrderAmount'];
	}else{   
		exit('最小下单金额格式不正确！');
	}
	
	if(strlen($in['ClientName']) < 1 || strlen($_SESSION['uc']['CompanyPrefix']."-".$in['ClientName']) > 30 ) exit('okname');
	if(!is_filename($in['ClientPassword'])) exit('UserPass');

	if(strlen($in['ClientPassword']) < 3 || strlen($in['ClientPassword']) > 18 ) exit('okpass');

	if(!empty($in['ClientMobile']))
	{
		if(!is_phone($in['ClientMobile'])) exit('请输入正确的手机号码!');
	}

    if(!empty($in['ClientNO']) && check_client_no($db,$in['ClientNO'],null)) {
        exit('药店编号已存在!');
    }

    if(!empty($in['ClientCompanyName']) && check_client_name($db,$in['ClientCompanyName'],null)) {
        exit('药店名称已存在!');
    }

	$InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");
	$msg = '您只有 '.$_SESSION['uc']['Number'].' 个授权药店， 您已全部用完，请联系开发商增加授权用户';
	if($InfoDataNum['clientrow'] >= $_SESSION['uc']['Number']) exit($msg);

	//$in['ClientName'] = $_SESSION['uc']['CompanyPrefix']."-".$in['ClientName'];

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientName='".$in['ClientName']."' limit 0,1");
	if(!empty($clientinfo['orwname'])) exit('repeat');	

	$DMobile = '';
	$smsmobilemsg = '';
	if(!empty($in['loginmobile']) && !empty($in['ClientMobile']))
	{
		$clientminfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientMobile='".$in['ClientMobile']."'  limit 0,1");
		if($clientminfo['orwname'] > 0) exit('此手机号码已使用，不能用此手机号码登录！');
		$DMobile = $in['ClientMobile'];
		$smsmobilemsg = '(或用手机号：'.$in['ClientMobile'].')';
	}
	
	if(!empty($in['ClientConsignment'])) $consignmentmsg = implode(",", $in['ClientConsignment']); else $consignmentmsg = '';
	if(!empty($in['ClientPay'])) $paymsg = implode(",", $in['ClientPay']); else $paymsg = '';
	if(!empty($in['ClientSortID'])) $shieldmsg = implode(",", $in['ClientSortID']); else $shieldmsg = '';
	if(empty($in['ClientPercent'])) $in['ClientPercent'] = '10.0';

	$upsql = "insert into ".DATABASEU.DATATABLE."_order_dealers(ClientCompany,ClientName,ClientPassword,ClientMobile,LoginDate) values(".$_SESSION['uinfo']['ucompany'].", '".$in['ClientName']."', '".$in['ClientPassword']."','".$DMobile."',".time().")";
	if($db->query($upsql))
    {
		$inid =  $db->insert_id;
		$levelmsg = "";
	    $letter   = new letter();
        $pinyima  = $letter->C($in['ClientCompanyName']);
		$levelmsg = $brandmsg = "";
		$brandarr = null;
		foreach($in as $ikey=>$ivar)
		{
			if(substr($ikey,0,11)=="ClientLevel")
			{
				if(!empty($in[$ikey]))
				{
					if(!empty($levelmsg)) $levelmsg .= ",";
					$levelmsg .= substr($ikey,12,1)."_".$in[$ikey];
				}
			}
			if(substr($ikey,0,12) == "BrandPercent")
			{
				if(!empty($in[$ikey]))
				{
					$brandarr[substr($ikey,13)] = floatval($in[$ikey]);
				}
			}
		}
		if(!empty($brandarr)) $brandmsg = serialize($brandarr);

		$insql	 = "insert into ".DATATABLE."_order_client(ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientConsignment,ClientPay,AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber,ClientGUID,OrderAmount) values(".$inid.",".$_SESSION['uinfo']['ucompany'].",'".$levelmsg."',".$in['ClientArea'].", '".$in['ClientName']."', '".$in['ClientCompanyName']."','".$pinyima."', '".$in['ClientNO']."', '".$in['ClientTrueName']."', '".$in['ClientEmail']."', '".$in['ClientPhone']."', '".$in['ClientFax']."', '".$in['ClientMobile']."', '".$in['ClientAdd']."', '".$in['ClientAbout']."',".time().", '".$shieldmsg."','".$in['ClientSetPrice']."', '".$in['ClientPercent']."', '".$brandmsg."', '".$consignmentmsg."', '".$paymsg."', '".$in['AccountName']."', '".$in['BankName']."', '".$in['BankAccount']."', '".$in['InvoiceHeader']."', '".$in['TaxpayerNumber']."','{$inid}','".$in['OrderAmount']."')";
		$db->query($insql);

        //关联客情官信息
        $company_id = $_SESSION['uinfo']['ucompany'];
        if($in['saler']){
            $db->query("INSERT INTO ".DATATABLE."_order_salerclient (CompanyID,SalerID,ClientID) VALUES ({$company_id},{$in['saler']},{$inid})");
        }

		if(!empty($in['sendsmsuser']) && !empty($in['ClientMobile']))
		{
			if(empty($_SESSION['uc']['CompanyUrl'])) $orderurl = 'http://'.$_SESSION['uc']['CompanyPrefix'].'.dhb.hk'; else $orderurl = $_SESSION['uc']['CompanyUrl']; 			
			$message = "【".$_SESSION['uc']['CompanySigned']."】".$_SESSION['uc']['CompanyName']." 为您开通了在线订货系统 网址: ".$orderurl ." 帐号:".$in['ClientName'].$smsmobilemsg." 密码:".$in['ClientPassword']." 请登录医统天下系统订货。退订回复TD";
			//未控制短信数量 modify by seekfor
			if(!empty($_SESSION['uc']['SmsNumber']) && $_SESSION['uc']['SmsNumber'] > 1) sms::send_sms($in['ClientMobile'],$message,$inid);
		}
		exit("ok");
		

	}else{

		exit("保存不成功!");

	}
}


/***********editsave**************/

if($in['m']=="content_edit_save")
{

	//var_dump($in);exit;
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$in['ClientName']     = strtolower(trim($in['ClientName']));
	$in['ClientPassword'] = strtolower(trim($in['ClientPassword']));
	if(!is_filename($in['ClientName'])) exit('okname');

	if(strlen($in['ClientName']) < 1 || strlen($_SESSION['uc']['CompanyPrefix']."-".$in['ClientName']) > 30 ) exit('okname');
	if(!empty($in['ClientPassword']) && !is_filename($in['ClientPassword'])) exit('okpass');
	if(!empty($in['ClientPassword']) && (strlen($in['ClientPassword']) < 3 || strlen($in['ClientPassword']) > 18) ) exit('okpass');
	
	if(preg_match("/^\d+$/",$in['OrderAmount'])){
		$in['OrderAmount']=(int)$in['OrderAmount'];
	}else{   
		exit('最小下单金额格式不正确！');
	}
	
	if(!empty($in['ClientMobile']))
	{
		if(!is_phone($in['ClientMobile'])) exit('请输入正确的手机号码!');
	}
	$in['OrderAmount']=(int)$in['OrderAmount'];

	//$in['ClientName'] = $_SESSION['uc']['CompanyPrefix']."-".$in['ClientName'];

	//if(!empty($in['ClientAudit'])) $clientflag = 0; else $clientflag = 9;

    $clientflag = (int)$in['ClientAudit'];
    if(!in_array($clientflag,array(0,8,9,1))) {
        exit("非法操作账号状态!");
    }

    if(!empty($in['ClientNO']) && check_client_no($db,$in['ClientNO'],$in['ClientID'])) {
        exit('药店编号已存在!');
    }

    if(!empty($in['ClientCompanyName']) && check_client_name($db,$in['ClientCompanyName'],$in['ClientID'])) {
        exit('药店名称已存在!');
    }

	if($clientflag == 0)
	{
		$InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");
	
		$msg = '您只有 '.$_SESSION['uc']['Number'].' 个授权药店， 您已全部用完，请联系软件提供者增加授权用户!';
		if($InfoDataNum['clientrow'] > $_SESSION['uc']['Number']) exit($msg);
	}

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientID <> ".$in['ClientID']." and  ClientName='".$in['ClientName']."' limit 0,1");
	if($clientinfo['orwname'] >= 1) exit('repeat');

	$sqlmobile = ", ClientMobile='' ";
	$smsmobilemsg = '';
	if(!empty($in['loginmobile']) && !empty($in['ClientMobile']))
	{
		$clientminfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientMobile='".$in['ClientMobile']."' and ClientID!=".$in['ClientID']." limit 0,1");
		if($clientminfo['orwname'] > 0) exit('此手机号码已使用，不能用此手机号码登录！');
		$sqlmobile = ", ClientMobile='".$in['ClientMobile']."' ";
		$smsmobilemsg = '(或用手机号：'.$in['ClientMobile'].')';
	}	
	
	$chk = $db->get_var("select count(*) as total from ".DATABASEU.DATATABLE."_order_dealers where ClientID=".$in['ClientID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']);
	
	if(!$chk){
		$insertT = "insert into ".DATABASEU.DATATABLE."_order_dealers(ClientCompany,ClientName,ClientPassword,LoginDate,ClientID) values(".$_SESSION['uinfo']['ucompany'].", '".$in['ClientName']."', '123456',".time().", ".$in['ClientID'].")";
		$db->query($insertT);
		
	}
	
	$insql = "update ".DATABASEU.DATATABLE."_order_dealers set ClientName='".$in['ClientName']."' ".(!empty($in['ClientPassword']) ? ", ClientPassword='".$in['ClientPassword']."'" : '')."  ".$sqlmobile.", ClientFlag=".$clientflag." where ClientID=".$in['ClientID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];
	$isu = $db->query($insql);

	if(empty($in['ClientPercent'])) $in['ClientPercent'] = '10.0';
	if(!empty($in['ClientConsignment'])) $consignmentmsg = implode(",", $in['ClientConsignment']); else $consignmentmsg = '';
	if(!empty($in['ClientPay'])) $paymsg = implode(",", $in['ClientPay']); else $paymsg = '';	
	if(!empty($in['ClientSortID'])) $shieldmsg = implode(",", $in['ClientSortID']); else $shieldmsg = '';



	if(!empty($in['ClientID']))
	{
        $company_id = $_SESSION['uinfo']['ucompany'];
        $client_single = $db->get_row("SELECT ClientGUID,ClientID,ERP FROM ".DATATABLE."_order_client WHERE ClientCompany={$company_id} AND ClientID=" . $in['ClientID']);
	    $letter   = new letter();
        $pinyima  = $letter->C($in['ClientCompanyName']);
		$levelmsg = $brandmsg = "";
		$brandarr = null;
		foreach($in as $ikey=>$ivar)
		{
			if(substr($ikey,0,11)=="ClientLevel"){
				if(!empty($in[$ikey])){
					if(!empty($levelmsg)) $levelmsg .= ",";
					$levelmsg .= substr($ikey,12,1)."_".$in[$ikey];
				}
			}
			if(substr($ikey,0,12) == "BrandPercent"){
				if(!empty($in[$ikey])){
					$brandarr[substr($ikey,13)] = floatval($in[$ikey]);
				}
			}
		}

		if(!empty($brandarr)) $brandmsg = serialize($brandarr);
		$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_client where  ClientID = ".$in['ClientID']." and ClientCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
		$infodatamsg = serialize($InfoData);
		$upsql = "update ".DATATABLE."_order_client set ClientLevel='".$levelmsg."', ClientArea=".$in['ClientArea']." ".($client_single['ERP'] == 'F' && empty($client_single['ClientGUID']) ? ",ClientGUID='".$in['ClientID']."'" : "").", ClientName='".$in['ClientName']."', ClientCompanyName='".$in['ClientCompanyName']."', ClientCompanyPinyi='".$pinyima."', ClientNO='".$in['ClientNO']."',  ClientTrueName='".$in['ClientTrueName']."', ClientEmail='".$in['ClientEmail']."', ClientPhone='".$in['ClientPhone']."', ClientFax='".$in['ClientFax']."', ClientMobile='".$in['ClientMobile']."', ClientAdd='".$in['ClientAdd']."', ClientAbout='".$in['ClientAbout']."',ClientShield='".$shieldmsg."', ClientSetPrice='".$in['ClientSetPrice']."', ClientPercent='".$in['ClientPercent']."',ClientBrandPercent='".$brandmsg."', ClientConsignment='".$consignmentmsg."', ClientPay='".$paymsg."', ClientFlag=".$clientflag.",OrderAmount='".$in['OrderAmount']."',BusinessValidity='".$in['BusinessValidity']."',GsmpValidity='".$in['GsmpValidity']."',LicenceValidity='".$in['LicenceValidity']."' where ClientID=".$in['ClientID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];
		$isup = $db->query($upsql);
		//财务信息
		$upsql2 = "update ".DATATABLE."_order_client set AccountName='".$in['AccountName']."', BankName='".$in['BankName']."', BankAccount='".$in['BankAccount']."', InvoiceHeader='".$in['InvoiceHeader']."', TaxpayerNumber='".$in['TaxpayerNumber']."' where ClientID=".$in['ClientID']." and ClientCompany=".$_SESSION['uinfo']['ucompany'];
		$isup2 = $db->query($upsql2);

        //关联客情官信息
        $company_id = $_SESSION['uinfo']['ucompany'];
        $db->query("DELETE FROM ".DATATABLE."_order_salerclient WHERE CompanyID={$company_id} AND ClientID=" . $in['ClientID']);
        if($in['saler']){
            $db->query("INSERT INTO ".DATATABLE."_order_salerclient (CompanyID,SalerID,ClientID) VALUES ({$company_id},{$in['saler']},{$in['ClientID']})");
        }

		if(!empty($in['sendsmsuser']) && !empty($in['ClientMobile']))
		{
			//begin tubo 增加，密码为空时去查密码，避免空发送 2015-11-10
			if(empty($in['ClientPassword'])){
				$in['ClientPassword'] = $db->get_var("SELECT ClientPassword FROM ".DATABASEU.DATATABLE."_order_dealers where  ClientID = ".$in['ClientID']." and ClientCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
			}
			//end tubo 2015-11-10
			if(empty($_SESSION['uc']['CompanyUrl'])) $orderurl = 'http://'.$_SESSION['uc']['CompanyPrefix'].'.dhb.hk'; else $orderurl = $_SESSION['uc']['CompanyUrl']; 
			$message = "【".$_SESSION['uc']['CompanySigned']."】".$_SESSION['uc']['CompanyName']." 为您开通了在线订货系统 网址: ".$orderurl ." 帐号:".$in['ClientName'].$smsmobilemsg." 密码:".$in['ClientPassword']." 请登录医统天下系统订货。退订回复TD";
			//未控制短信数量
			if(!empty($_SESSION['uc']['SmsNumber']) && $_SESSION['uc']['SmsNumber'] > 1)  sms::send_sms($in['ClientMobile'],$message,$in['ClientID']);
		}
	
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_client.php?m=content_edit_save','修改药店(".$in['ClientID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		exit("ok");
	}
}

/************* Point ****************/
if($in['m']=="content_point_save")
{	
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	if(!empty($in['data_ClientID']))
	{
		$pv = intval($in['point']);
		if(empty($pv))
		{
			exit('请输入正确的分值！');
		}else{
			$upsql = "insert into ".DATATABLE."_order_point(PointCompany,PointClient,PointValue,PointTitle,PointDate,PointUser) values(".$_SESSION['uinfo']['ucompany'].", '".$in['data_ClientID']."', ".$pv.",'".$in['title']."',".time().",".$_SESSION['uinfo']['userid'].")";	
		}
	}

	if($db->query($upsql))
	{
		exit("ok");		
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="search_client")
{
	if(empty($in['lurl'])) $in['lurl'] = 'order.php';
	$rdata['backtype'] = 'empty';
	$rmsg = '';
	if(!empty($in['ckw'])){
		$ckw = urldecode($in['ckw']);
		$ckw = str_replace(" ","%",trim($ckw));
		$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi,ClientTrueName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 and CONCAT(ClientCompanyName,ClientCompanyPinyi,ClientNO,ClientTrueName) like '%".$ckw."%' order by ClientNO asc,ClientID asc");
		if(!empty($clientdata)){
			$rmsg = '<ol>';
			foreach($clientdata as $v){
				$rmsg .= '<li><a href="'.$in['lurl'].'?cid='.$v['ClientID'].'">'.$v['ClientCompanyName'].' -- '.$v['ClientTrueName'].'</a></li>';
			}
			$rdata['backtype'] = 'ok';
			$rmsg .= '</ol>';
		}
	}
	$rdata['htmldata'] = $rmsg;

	echo json_encode($rdata);
	exit;
}

if($in['m']=="muledit_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['client']['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	$in['SelectClient'] = $in['SelectClient']."0";
	if(empty($in['mulType']) || empty($in['SelectClient'])) exit('请先选择您要操作的对象');

		$levelmsg = $brandmsg = '';
		foreach($in as $ikey=>$ivar)
		{
			if(substr($ikey,0,11)=="ClientLevel")
			{
				if(!empty($in[$ikey]))
				{
					if(!empty($levelmsg)) $levelmsg .= ",";
					$levelmsg .= substr($ikey,12,1)."_".$in[$ikey];
				}
			}
			if(substr($ikey,0,12) == "BrandPercent")
			{
				if(!empty($in[$ikey]))
				{
					$brandarr[substr($ikey,13)] = floatval($in[$ikey]);
				}
			}
		}
		if(!empty($brandarr)) $brandmsg = serialize($brandarr);
	
	switch ($in['mulType']){
	   case 'Audit':
           //当前已审核通过的药店数量
           $ccnt = $db->get_var("SELECT COUNT(1) as CNT FROM ".DATATABLE."_order_client WHERE ClientFlag=0 AND ClientCompany=".$_SESSION['uinfo']['ucompany']);

           if($_SESSION['uc']['Number']<=$ccnt && $in['ClientAudit']=='ok'){
               echo json_encode(array('status'=>'fail','info'=>'您只有 '.$_SESSION['uc']['Number'].' 个授权药店，<br/> 您已全部用完，<br/>请联系开发商增加授权用户'));
               exit();
           }

           if(($_SESSION['uinfo']['userflag']!=9 && $_SESSION['up']['client']['pope_audit'] != 'Y')){
               echo json_encode(array('status'=>'fail','info'=>'对不起，您没有此项操作权限！'));
               exit();
           }
		   if($in['ClientAudit']=="ok") $sqlu = " ClientFlag = 0 "; else $sqlu = " ClientFlag = 9 ";
		   break;
	   case 'Area':
		   $sqlu = " ClientArea = ".$in['ClientArea']." ";
		   break;
	   case 'Saler':
		   $sqlu = '';
		   break;
	   case 'Catalog':
		   $sidmsg = implode(",",$in['ClientSortID']);
		   $sqlu = " ClientShield = '".$sidmsg."' ";
		   break;
	   case 'Level':
		   $sqlu = " ClientLevel = '".$levelmsg."' ";
		   break;
	   case 'Price':
		   if(empty($in['ClientPercent'])) $in['ClientPercent'] = 10.0;
		   $sqlu = " ClientSetPrice = '".$in['ClientSetPrice']."', ClientPercent='".$in['ClientPercent']."', ClientBrandPercent='".$brandmsg."' ";
		   break;
	   case 'Consignment':
		   $sidmsg = implode(",",$in['ClientConsignment']);
		   $sqlu = " ClientConsignment = '".$sidmsg."' ";
		   break;
	   case 'Finance':
		   $sidmsg = implode(",",$in['ClientPay']);
		   $sqlu = " ClientPay = '".$sidmsg."' ";
		   break;
	   case 'Sms':
		   $sqlu = '';
		   break;
	   default:
		  exit('请选择正确的操作对象');
	}

	if($in['mulType'] == 'Sms'){
		if(!empty($in['sendsmsuser']))
		{
			if(empty($_SESSION['uc']['CompanyUrl'])) $orderurl = 'http://'.$_SESSION['uc']['CompanyPrefix'].'.dhb.hk'; else $orderurl = $_SESSION['uc']['CompanyUrl']; 
			$clientdata = $db->get_results("select ClientID,ClientName,ClientPassword,ClientMobile from ".DATABASEU.DATATABLE."_order_dealers where ClientID IN (".$in['SelectClient'].")");
			foreach($clientdata as $cv){
				$message = "【".$_SESSION['uc']['CompanySigned']."】".$_SESSION['uc']['CompanyName']." 为您开通了网上订货系统 网址: ".$orderurl ." 帐号:".$cv['ClientName']." 密码:".$cv['ClientPassword']." 请登录本系统订货";
				if(!empty($_SESSION['uc']['SmsNumber']) && $_SESSION['uc']['SmsNumber'] > 1)  sms::send_sms($cv['ClientMobile'],$message,$cv['ClientID']);	
			}
			$sta = true;
		}
	}elseif($in['mulType'] == 'Audit'){
		$sta = $db->query("update ".DATATABLE."_order_client set ".$sqlu." where ClientID IN (".$in['SelectClient'].")");
		$sta = $db->query("update ".DATABASEU.DATATABLE."_order_dealers set ".$sqlu." where ClientID IN (".$in['SelectClient'].")");
	}elseif($in['mulType'] == 'Saler'){
	    
	    $db->query("DELETE FROM ".DATATABLE."_order_salerclient WHERE ClientID IN (".$in['SelectClient'].") and CompanyID=".$_SESSION['uinfo']['ucompany']);
	    $SelectClient = explode(",",$in['SelectClient']);
	    //去掉数组中的重复元素
	    $SelectClient = array_unique($SelectClient);
	    //去掉数组中的空元素
	    $SelectClient = array_filter($SelectClient);
	    foreach ($SelectClient as $val){
	        $insert .= "({$_SESSION['uinfo']['ucompany']},{$in['saler']},{$val}),";
	    }
	    $insert    =  substr($insert, 0, -1);
	    if($insert){
    	    $sta   =  $db->query("INSERT INTO ".DATATABLE."_order_salerclient (CompanyID,SalerID,ClientID) VALUES ".$insert);
	    }
	}else{
		$sta = $db->query("update ".DATATABLE."_order_client set ".$sqlu." where ClientID IN (".$in['SelectClient'].")");
	}

	if($sta){
		$rdata['status'] = 'ok';
    	$rdata['info']   = '操作成功！';
	}else{
		$rdata['status'] = 'fail';
    	$rdata['info']   = '操作不成功！';
	}
	echo json_encode($rdata);
	exit;
}

if($in['m'] == 'delete_address')
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$sqlin = "delete from ".DATATABLE."_order_address where AddressID=".intval($in['ID'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
	$db->query($sqlin);
	echo 'ok';
	exit();

}

if($in['m'] == 'content_import_save') {
    //ini_set('display_errors',1);
    //error_reporting(E_ALL);
    //EXCEL导入药店
    $company_id = $_SESSION['uinfo']['ucompany'];
    $areaList = $db->get_results("SELECT AreaID,AreaName FROM ".DATATABLE."_order_area WHERE AreaCompany={$company_id}");
    $areaList = array_column($areaList ? $areaList : array(),'AreaID','AreaName');


    $InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ");
    $allowCnt = (int)$_SESSION['uc']['Number'];
    $needCnt = (int)$InfoDataNum['clientrow'] + count($in['linedata']);

    $msg = '您只有 '.$_SESSION['uc']['Number'].' 个授权药店， 您已全部用完，请联系开发商增加授权用户';
    if($InfoDataNum['clientrow'] >= $_SESSION['uc']['Number']){
        exit( $msg );
    } else if($needCnt > $allowCnt) {
        exit( "导入药店还需要" . ($needCnt - $allowCnt) . "个授权用户,请联系开发商增加授权用户" );
    }

    $list = array();
    foreach($in['linedata'] as $data) {
        if(empty($data)) {
            continue;
        }
        $data = json_decode(urldecode($data),true);
        $tmp = array();
        $data = array_values($data);
        list(
            $tmp['ClientNO'], //A 药店编号
            $tmp['ClientCompanyName'], //B 药店名称
            $tmp['ClientName'], //C 登录账号
            $tmp['ClientPassword'], //D 登录密码
            $tmp['ClientMobile'], //E 手机号-可用手机号登录
            $tmp['ClientArea'], //F 地区
            $tmp['ClientTrueName'],//G 联系人
            $tmp['ClientPhone'], //H 联系电话
            $tmp['ClientFax'], //I 传真
            $tmp['ClientEmail'], //J 邮箱
            $tmp['ClientAdd'], //K 地址
            $tmp['ClientAbout'], //L 备注
            $tmp['ClientLevel_A'], //M 级别
            $tmp['ClientSetPrice'], //N 执行价格
            $tmp['ClientPercent'], //O 折扣
            $tmp['AccountName'],//P 开户名称
            $tmp['BankName'],//Q 开户银行
            $tmp['BankAccount'], //R 银行账号
            $tmp['InvoiceHeader'], //S 开票抬头
            $tmp['TaxpayerNumber'], //T 纳税人识别号
        ) = $data;

        $tmp['ClientArea'] = $areaList[$tmp['ClientArea']];

        //密码生成
        $tmp['ClientPassword'] = $tmp['ClientPassword'] ? $tmp['ClientPassword'] : '123456';
        //手机号验证
        $tmp['ClientMobile'] = is_phone($tmp['ClientMobile']) ? $tmp['ClientMobile'] : '';
        //级别替换
        //$tmp['ClientLevel_A'] = $tmp['ClientLevel'];
        unset($tmp['ClientLevel']);
        //执行价格
        if($tmp['ClientSetPrice'] == '价格二') {
            $tmp['ClientSetPrice'] = 'Price2';
        } else {
            $tmp['ClientSetPrice'] = 'Price1';
        }
        //折扣
        $tmp['ClientPercent'] = $tmp['ClientPercent'] ? $tmp['ClientPercent'] : 10;
        if((int)$tmp['ClientPercent']<1 || (int)$tmp['ClientPercent']>10) {
            $tmp['ClientPercent'] = 10;
        }

        $list[] = $tmp;
    }
    $err = array();
    $letter   = new letter();
    foreach($list as $key => $item) {
        $result = insertClient($item,$letter);
        if(is_string($result)) {
            $err[] = $result;
        }
    }


    $err = "";
    $status = "T";
    if(count($err)>0) {
        if(count($err) == count($list)) {
            //exit("导入失败,请重试!");
            $err = "导入失败,请重试!";
            $status = "F";
        } else {
            //exit("部分导入失败,请更新重试!");
            $err = "部分导入失败,请更新重试!";
            $status = "F";
        }
    }
    $err = $err ? $err : "导入成功";
    $sql = "UPDATE ".DATATABLE."_order_import_log SET Status='{$status}',ImportTime=".time().",Remark=concat(Remark,'=>','".$err."') WHERE ID=" . $in['log_id'];
    $db->query($sql);
    exit("ok");
}

function insertClient($in,$letter) {
    global $db;
    if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['client']['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
    $in['ClientName']	  = strtolower($in['ClientName']);

    $in['ClientPassword'] = strtolower($in['ClientPassword']);
    if(!is_filename($in['ClientName'])) {
        return "登录名称有误!";
    }

    if(strlen($in['ClientName']) < 1 || strlen($_SESSION['uc']['CompanyPrefix']."-".$in['ClientName']) > 30 ) {
        return "登录账号长度要求18位以下!";
    }
    if(!is_filename($in['ClientPassword'])) exit('UserPass');

    if(strlen($in['ClientPassword']) < 3 || strlen($in['ClientPassword']) > 18 ) {
        return '密码长度只能在3~18位之间!';
    }

    if(!empty($in['ClientMobile']))
    {
        if(!is_phone($in['ClientMobile'])) {
            return '请输入正确的手机号码';
        }
    }


    //$in['ClientName'] = $_SESSION['uc']['CompanyPrefix']."-".$in['ClientName'];
    $clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientName='".$in['ClientName']."' limit 0,1");
    if(!empty($clientinfo['orwname'])) {
        return "该药店已存在!";
    }

    $DMobile = '';
    $smsmobilemsg = '';
    if(!empty($in['ClientMobile']))
    {
        $clientminfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_dealers where ClientMobile='".$in['ClientMobile']."'  limit 0,1");
        if($clientminfo['orwname'] > 0) {
            $in['loginmobile'] = '';//exit('此手机号码已使用，不能用此手机号码登录！');
        } else {
            $DMobile = $in['ClientMobile'];
            $smsmobilemsg = '(或用手机号：'.$in['ClientMobile'].')';
        }
    }

    if(!empty($in['ClientConsignment'])) $consignmentmsg = implode(",", $in['ClientConsignment']); else $consignmentmsg = '';
    if(!empty($in['ClientPay'])) $paymsg = implode(",", $in['ClientPay']); else $paymsg = '';
    if(!empty($in['ClientSortID'])) $shieldmsg = implode(",", $in['ClientSortID']); else $shieldmsg = '';
    if(empty($in['ClientPercent'])) $in['ClientPercent'] = '10.0';

    $upsql = "insert into ".DATABASEU.DATATABLE."_order_dealers(ClientCompany,ClientName,ClientPassword,ClientMobile,LoginDate) values(".$_SESSION['uinfo']['ucompany'].", '".$in['ClientName']."', '".$in['ClientPassword']."','".$DMobile."',".time().")";

    if($db->query($upsql))
    {
        $inid =  $db->insert_id;

        $pinyima  = $letter->C($in['ClientCompanyName']);
        $levelmsg = $brandmsg = "";
        foreach($in as $ikey=>$ivar)
        {
            if(substr($ikey,0,11)=="ClientLevel" && (int)$in[$ikey] >=1 && (int)$in[$ikey] <=10)
            {
                if(!empty($in[$ikey]))
                {
                    if(!empty($levelmsg)) $levelmsg .= ",";
                    $levelmsg .= substr($ikey,12,1)."_level_".$in[$ikey];
                }
            }
        }

        $insql	 = "insert into ".DATATABLE."_order_client(ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientConsignment,ClientPay,AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber,ClientGUID) values(".$inid.",".$_SESSION['uinfo']['ucompany'].",'".$levelmsg."',".intval($in['ClientArea']).", '".$in['ClientName']."', '".$in['ClientCompanyName']."','".$pinyima."', '".$in['ClientNO']."', '".$in['ClientTrueName']."', '".$in['ClientEmail']."', '".$in['ClientPhone']."', '".$in['ClientFax']."', '".$in['ClientMobile']."', '".$in['ClientAdd']."', '".$in['ClientAbout']."',".time().", '".$shieldmsg."','".$in['ClientSetPrice']."', '".$in['ClientPercent']."', '".$brandmsg."', '".$consignmentmsg."', '".$paymsg."', '".$in['AccountName']."', '".$in['BankName']."', '".$in['BankAccount']."', '".$in['InvoiceHeader']."', '".$in['TaxpayerNumber']."','')";


        $rst = $db->query($insql);
        if(!$rst) {
            $db->query("DELETE FROM ".DATABASEU.DATATABLE."_order_dealers WHERE ClientID={$inid} AND ClientCompany=" . $_SESSION['uinfo']['ucompany']);
            return "保存不成功!";
        }

        if(!empty($in['sendsmsuser']) && !empty($in['ClientMobile']))
        {
            if(empty($_SESSION['uc']['CompanyUrl'])) $orderurl = 'http://'.$_SESSION['uc']['CompanyPrefix'].'.dhb.hk'; else $orderurl = $_SESSION['uc']['CompanyUrl'];
            $message = "【".$_SESSION['uc']['CompanySigned']."】".$_SESSION['uc']['CompanyName']." 为您开通了在线订货系统 网址: ".$orderurl ." 帐号:".$in['ClientName'].$smsmobilemsg." 密码:".$in['ClientPassword']." 请登录医统天下系统订货。退订回复TD";
			if(!empty($_SESSION['uc']['SmsNumber']) && $_SESSION['uc']['SmsNumber'] > 1)  sms::send_sms($in['ClientMobile'],$message,$inid);
        }
        return true;
    }else{
        return "保存失败!";
    }
}

/**
 * @desc 检查药店编号是否已存在
 * @param ez_sql $db
 * @param string $client_no
 * @param int $client_id default null
 * @return bool
 */
function check_client_no($db,$client_no,$client_id = null) {
    $company_id = $_SESSION['uinfo']['ucompany'];
    $where = " WHERE ClientCompany={$company_id} AND ClientNO='{$client_no}'";
    if(!empty($client_id)) {
        $where .= " AND ClientID <> " . (int)$client_id;
    }
    $cnt = $db->get_var("SELECT COUNT(*) AS Total FROM ".DATATABLE."_order_client " . $where . " LIMIT 1");
    return $cnt > 0;
}

/**
 * @desc 检查药店名称是否已存在
 * @param ez_sql $db
 * @param string $name
 * @param int $client_id default null
 * @return bool $cnt;
 */
function check_client_name($db,$name,$client_id = null) {
    $company_id = $_SESSION['uinfo']['ucompany'];
    $where = " WHERE ClientCompany={$company_id} AND ClientCompanyName='{$name}' ";
    if(!is_null($client_id)) {
        $where .= " AND ClientID <> " . (int)$client_id;
    }
    $cnt = $db->get_var("SELECT COUNT(*) AS Total FROM ".DATATABLE."_order_client {$where} LIMIT 1");
    return $cnt > 0;
}

exit('非法操作!');

?>