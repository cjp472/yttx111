<?php
class client
{
	//取客户资料
	function clientinfo()
	{
		$db	     = dbconnect::dataconnect()->getdb();

		$sql_l   = "select ClientCompany,ClientArea,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber,BusinessValidity,GsmpValidity,LicenceValidity from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." limit 0,1";
		$result1 = $db->get_row($sql_l);

		$sql_2   = "select ClientID,ClientName,ClientMobile as ClientLoginMobile,LoginIP,LoginDate,LoginCount from ".DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." limit 0,1";
		$result2 = $db->get_row($sql_2);
		
		//查询是否已开通易极付 by wanjun
		$sql_3   = "select YapiuserName from ".DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." limit 1";
		$result3 = $db->get_row($sql_3);
		$result	 = count($result3) ? array_merge($result1, $result2, $result3) : array_merge($result1, $result2);
		
		//$db->debug();
		return $result;
	}

	//取微信帐号
	function weixininfo()
	{
		$db	     = dbconnect::dataconnect()->getdb();
		$encode  = md5(ENCODE_KEY.'_'.date('Y-m-d H'));

		$sql_2  = "select ID,WeiXinID,UserID,NickName from ".DATABASEU.DATATABLE."_order_weixin where UserID=".$_SESSION['cc']['cid']." and UserType='C' ";
		$result = $db->get_results($sql_2);
        $result = $result ? $result : array();
		foreach($result as $k=>$v){
			if(empty($v['NickName'])){
				$nickname = file_get_contents(WEIXIN_URL.'getuserinfo.php?encode='.$encode.'&openid='.$v['WeiXinID']);
				$nickname = urldecode($nickname);
				if(!empty($nickname)){
					$db->query("update ".DATABASEU.DATATABLE."_order_weixin set NickName='".$nickname."' where ID=".$v['ID']);
					$result[$k]['NickName'] = $nickname;
				}
			}
		}

		//$db->debug();
		return $result;
	}

	//取QQ信息
	function listqqinfo($num=10)
	{
		$db	    = dbconnect::dataconnect()->getdb();

		$sql_l  =  "SELECT UserID,OpenID,QQ FROM ".DATABASEU.DATATABLE."_order_qq where UserID=".$_SESSION['cc']['cid']." and UserType='C' limit 0,".$num;
		$result	= $db->get_results($sql_l);
		//$db->debug();
		return $result;
	}

	function removeweixin($id)
	{
		$db	     = dbconnect::dataconnect()->getdb();
		$is = $db->query("delete from ".DATABASEU.DATATABLE."_order_weixin where ID=".$id." and UserID=".$_SESSION['cc']['cid']." and UserType='C' limit 1");
		if($is) exit('ok'); else exit('操作不成功!');
	}

	function removeqq($id)
	{
		$db	     = dbconnect::dataconnect()->getdb();
		$is = $db->query("delete from ".DATABASEU.DATATABLE."_order_qq where OpenID='".$id."' and UserID=".$_SESSION['cc']['cid']." and UserType='C' limit 1");
		if($is) exit('ok'); else exit('操作不成功!');
	}

	//修改客户资料
	function editprofile($in)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$result = null;

		$sql_l = "update ".DATATABLE."_order_client set  ClientTrueName='".$in['ClientTrueName']."', ClientEmail='".$in['ClientEmail']."', ClientPhone='".$in['ClientPhone']."', ClientFax='".$in['ClientFax']."', ClientMobile='".$in['ClientMobile']."', ClientAdd='".$in['ClientAdd']."', ClientAbout='".$in['ClientAbout']."', AccountName='".$in['AccountName']."', BankName='".$in['BankName']."', BankAccount='".$in['BankAccount']."', InvoiceHeader='".$in['InvoiceHeader']."', TaxpayerNumber='".$in['TaxpayerNumber']."' where ClientID=".$_SESSION['cc']['cid']." and ClientCompany=".$_SESSION['cc']['ccompany']."";
		if($db->query($sql_l))
		{		
			return true;
		}
		//$db->debug();
		return false;
	}

    //修改客户手机号码
	function edit_user_phone($in)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$result = null;

		$sql_l = "update ".DATATABLE."_order_client set  ClientMobile='".$in['ClientMobile']."' where ClientID=".$_SESSION['cc']['cid']." and ClientCompany=".$_SESSION['cc']['ccompany']."";
		if($db->query($sql_l))
		{		
			return true;
		}
		//$db->debug();
		return false;
	}
	//修改密码
	function editpass($opass,$npass)
	{
		$db	     = dbconnect::dataconnect()->getdb();
		$result = null;

		$sql_l = "update ".DATABASEU.DATATABLE."_order_dealers set ClientPassword='".$npass."' where ClientID=".$_SESSION['cc']['cid']." and ClientCompany=".$_SESSION['cc']['ccompany']." and ClientPassword='".$opass."'";
		if($db->query($sql_l))
		{
			return true;
		}
		//$db->debug();
		return false;
	}


	//收货地址
	function listaddress($num=10)
	{
		$db	    = dbconnect::dataconnect()->getdb();

		$sql_l  = "select * from ".DATATABLE."_order_address where AddressClient = ".$_SESSION['cc']['cid']." and CompanyID=".$_SESSION['cc']['ccompany']." order by AddressID DESC limit 0,".$num;
		$result	= $db->get_results($sql_l);
		//$db->debug();
		return $result;
	}

	//保存收货地址
	function addaddress($in)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$result = null;

		if(!empty($in['AddressFlag'])) $db->query("update ".DATATABLE."_order_address set AddressFLag=0 where AddressClient=".$_SESSION['cc']['cid']." and CompanyID=".$_SESSION['cc']['ccompany']);

		$sql_l = "insert into ".DATATABLE."_order_address (CompanyID, AddressClient, AddressClientName, AddressCompany, AddressContact, AddressPhone, AddressAddress,AddressDate, AddressFlag) values(".$_SESSION['cc']['ccompany'].", ".$_SESSION['cc']['cid'].",'".$_SESSION['cc']['cusername']."','".$in['AddressCompany']."','".$in['AddressContact']."','".$in['AddressPhone']."','".$in['AddressAddress']."',".time().", ".$in['AddressFlag'].") ";
		if($db->query($sql_l))
		{
			return true;
		}
		//$db->debug();
		return false;
	}

	//修改地址
	function editaddress($in)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$result = null;

		if(!empty($in['AddressFlag'])) $db->query("update ".DATATABLE."_order_address set AddressFLag=0 where AddressClient=".$_SESSION['cc']['cid']." and CompanyID=".$_SESSION['cc']['ccompany']);

		$sql_l = "update ".DATATABLE."_order_address set AddressCompany='".$in['AddressCompany']."', AddressContact='".$in['AddressContact']."', AddressPhone='".$in['AddressPhone']."', AddressAddress='".$in['AddressAddress']."', AddressFlag='".$in['AddressFlag']."' where AddressClient=".$_SESSION['cc']['cid']." and CompanyID=".$_SESSION['cc']['ccompany']." and AddressID=".$in['AddressID']."";
		if($db->query($sql_l))
		{
			return true;
		}
		//$db->debug();
		return false;
	}

	//删除地址
	function deladdress($aid)
	{
		$db	    = dbconnect::dataconnect()->getdb();

		$sql_l  = "delete from ".DATATABLE."_order_address where AddressID = ".$aid." and AddressClient = ".$_SESSION['cc']['cid']." and CompanyID=".$_SESSION['cc']['ccompany']." ";

		if($db->query($sql_l))
		{
			return true;
		}

		return false;
	}

	//积分
	function get_point()
	{	
		$db = dbconnect::dataconnect()->getdb();

		$cvalue = $db->get_row("select sum(PointValue) as pv from ".DATATABLE."_order_point where PointCompany=".$_SESSION['cc']['ccompany']." and PointClient=".$_SESSION['cc']['cid']." ");

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_point where PointCompany=".$_SESSION['cc']['ccompany']." and  PointClient=".$_SESSION['cc']['cid']." ";
		$sql_l  = "select PointID,PointOrder,PointValue,PointTitle,PointDate from ".DATATABLE."_order_point where PointCompany=".$_SESSION['cc']['ccompany']." and PointClient=".$_SESSION['cc']['cid']." ORDER BY PointDate desc ";
		
		$rs       = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= 50;
        $page->Total		    = $rs['allrow'];
		$page->LinkAry		= array("m"=>"point");
        
        $result['total']			= $rs['allrow'];
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;
		$result['list']			    = $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink('my.php');
		$result['pv']               = $cvalue;
		//$db->debug();
		return $result;
		unset($result);
	}
	
	//验证银行提示
	function notice_info()
	{	
		$db = dbconnect::dataconnect()->getdb();
		$now=time();
		$result = $db->get_row("select title,content,start_date,end_date  from ".DATATABLE."_pay_notice where start_date <='".$now."' and end_date >='".$now."' and type=1 order by addtime limit 1 ");
		return $result;
		unset($result);
	}
        //公司资质查询
        function get_Merchant(){      
            $db = dbconnect::dataconnect()->getdb();
            $getCompanyID = $_SESSION['ucc']['CompanyID'];
            $c_id = $_SESSION['cc']['cid'];
//            $getMerchant = $db->get_row("select * from ".DATATABLE."_three_sides_merchant where CompanyID = ".$getCompanyID." ");
            $sql = "select m.*,c.C_Flag from ".DATABASEU.DATATABLE."_three_sides_merchant as m left join  ".DATATABLE."_order_client as c on m.CompanyID=c.ClientCompany and m.MerchantID=c.ClientID where CompanyID = ".$getCompanyID." and MerchantID=".$_SESSION['cc']['cid'];
            $sqlCid = "select ClientTrueName,ClientMobile from ".DATATABLE."_order_client where ClientCompany='".$getCompanyID."' and ClientID ='".$c_id."'";

            $data = $db->get_row($sql);
            $Cdata = $db->get_row($sqlCid);
            $result['data'] = $data; 
            $result['Cdata'] = $Cdata; 
            return $result;
            
        }
        //增加信息
        function add_Merchant($in){
            $db = dbconnect::dataconnect()->getdb();
            $filename1 = $in['IDCardImg'];
            foreach($filename1 as $k=>$v)
            {
                $v = str_replace(RESOURCE_PATH, '', $v);
                $filenamenew[$k]['FlieName']=substr($v, strrpos($v, 'files'));
                $fian[] = $filenamenew[$k]['FlieName'];
                $fan = implode(",", $fian);
            }
            $TureUserName = $in['TureUserName'];
            $UserPhone = $in['UserPhone'];
            $IDCard = $in['IDCard'];
            $IDCardImg = $fan;
            $getCompanyID = $_SESSION['ucc']['CompanyID'];
            $MerchantID = $_SESSION['cc']['cid'];
            $select = "select CompanyID from ".DATABASEU.DATATABLE."_three_sides_merchant where CompanyID = ".$getCompanyID." and MerchantID=".$MerchantID;
            $selectID = $db-> query($select);
            // 查询 判断是否已存在
            if(empty($selectID)){
                $addSql = "insert into ".DATABASEU.DATATABLE."_three_sides_merchant(CompanyID,BusinessName,BusinessCard,BusinessCardImg,IDLicenceImg,GPImg,MerchantID,TureUserName,UserPhone,IDCard,IDCardImg,SanBusinessCard,SanBusinessCardImg,IDLicenceCard,GPCard)values(".$getCompanyID .",'".$in['BusinessName']."','".$in['BusinessCard']."','".$in['BusinessCardImg']."','".$in['IDLicenceImg']."','".$in['GPImg']."',".$MerchantID.",'".$TureUserName."',".$UserPhone.",".$IDCard.",'".$IDCardImg."','".$in['SanBusinessCard']."','".$in['SanBusinessCardImg']."','".$in['IDLicenceCard']."','".$in['GPCard']."')";
                }else{
                $addSql = "update ".DATABASEU.DATATABLE."_three_sides_merchant set BusinessName='".$in['BusinessName']."',BusinessCard='".$in['BusinessCard']."',BusinessCardImg='".$in['BusinessCardImg']."',IDLicenceImg ='".$in['IDLicenceImg']."',GPImg='".$in['GPImg']."',TureUserName='".$TureUserName."',UserPhone='".$UserPhone."',IDCard='".$IDCard."',IDCardImg='".$IDCardImg."',SanBusinessCard='".$in['SanBusinessCard']."',SanBusinessCardImg='".$in['SanBusinessCardImg']."',IDLicenceCard='".$in['IDLicenceCard']."',GPCard='".$in['GPCard']."' where CompanyID = ".$getCompanyID." and MerchantID=".$MerchantID;                
                }  
            $ClientID = $_SESSION['cc']['cid'];
            //提交数据从新审核
            $updateClient = "update ".DATATABLE."_order_client set C_Flag ='D' where ClientID = '".$ClientID."'";
            $db->query($updateClient);
            $is = $db-> query($addSql);
            return $is;
        }
        //增加支付密码
        function PaypwdAdd($in,$type){ 
           $db = dbconnect::dataconnect()->getdb();
           $mobile 		= $in['mobile'];// 手机号
           $payPwd 		= $in['payPwd'];// 密码
           $CompanyID	= $_SESSION['cc']['ccompany'];
           $ClientID	= $_SESSION['cc']['cid'];
           
//            $PayPassword = md5($Password.PASSWORD_MIX_UP);
           $PayPassword = think_encrypt($payPwd, PASSWORD_MIX_UP);
           
           $isExist = $db->get_var("select count(*) as total from ".DATABASEU.DATATABLE."_credit_password where CompanyID=".$CompanyID." and ClientID=".$ClientID." limit 1");
            $dateErrorSet = date("Y-m-d H:i:s");
            if($type == 'add' && !$isExist){//增加
               $netSql = "insert into ".DATABASEU.DATATABLE."_credit_password (CompanyID,ClientID,PayPassword,Mobile)value(".$CompanyID.",".$ClientID.",'".$PayPassword."', '".$mobile."')";
            }else if($type == 'update' && $isExist){//修改
            	$netSql = "update ".DATABASEU.DATATABLE."_credit_password set PayPassword='".$PayPassword."',ErrorTimes='0',ErrorDate='".$dateErrorSet."' where CompanyID=".$CompanyID." and ClientID=".$ClientID." and Mobile='".$mobile."' limit 1";
                
            }
            
            return $db-> query($netSql);
                    
        }
         //查询支付密码
         function PaypwdSel($in){
			$db = dbconnect::dataconnect ()->getdb ();
			$CompanyID = $_SESSION ['cc'] ['ccompany'];
			$ClientID = $_SESSION ['cc'] ['cid'];
			$PayPassword = trim ( $in ['PayPassword'] );
			// $MdPayPassword = md5($PayPassword.PASSWORD_MIX_UP);
			
			$MdPayPassword = think_encrypt ( $PayPassword, PASSWORD_MIX_UP );
			$PaySelSql = "select * from " . DATABASEU . DATATABLE . "_credit_password where CompanyID=" . $CompanyID . " and ClientID=" . $ClientID . " and PayPassword='" . $MdPayPassword . "'";
			return $db->get_row ( $PaySelSql );
        }
        
        //修改每日密码次数
        function updateErrorTimes(){
        	$db = dbconnect::dataconnect ()->getdb ();
        	$CompanyID = $_SESSION ['cc'] ['ccompany'];
        	$ClientID = $_SESSION ['cc'] ['cid'];
        	$today = date('Y-m-d');
        	$sql = "select ErrorTimes,ErrorDate from " . DATABASEU . DATATABLE . "_credit_password where CompanyID=" . $CompanyID . " and ClientID=" . $ClientID;
        	$pwdInfo = $db->get_row($sql);
        	if($today == date('Y-m-d', strtotime($pwdInfo['ErrorDate']))){//计数器累加
        		$ErrorTimes = $pwdInfo['ErrorTimes'] + 1;
        	}else{//不是当天时，重置计数器
        		$ErrorTimes = 1;
        		$pwdInfo['ErrorTimes'] = 0;
        	}
        	$upSql = "update " . DATABASEU . DATATABLE . "_credit_password set ErrorTimes=".$ErrorTimes.", ErrorDate='".date('Y-m-d H:i:s')."' where CompanyID=" . $CompanyID . " and ClientID=" . $ClientID;
        	$db->query($upSql);
        	
        	return $pwdInfo;
        }
        
        //查询支付密码设置的手机号
        function getPayPwdMobile(){ 
        	$db = dbconnect::dataconnect()->getdb();
        	$CompanyID = $_SESSION['cc']['ccompany'];
        	$ClientID  = $_SESSION['cc']['cid'];
        	$Sql = "select Mobile from ".DATABASEU.DATATABLE."_credit_password where ClientID=".$ClientID." and CompanyID=".$CompanyID;
        	
        	return $db-> get_row($Sql);
        }
        //查询该账户账期余额
        function CreditMain($in){
            $db = dbconnect::dataconnect()->getdb();
            $CompanyID 	= $_SESSION['cc']['ccompany'];
            $ClientID 	= $_SESSION['cc']['cid'];
            $MainSql 	= "select Amount,ResidueAmount,CreditStatus from ".DATABASEU.DATATABLE."_credit_main where CompanyID=".$CompanyID." and ClientID=".$ClientID;
            $MainSel = $db->get_row($MainSql);
            return $MainSel;
            
        }
        //修改该账户账期余额
        function MainUpdate($in, $OrderTotal){
            $db = dbconnect::dataconnect()->getdb();
            $ClientID = $_SESSION['cc']['cid'];
            $CompanyID = $_SESSION['cc']['ccompany'];
            $MainUpdateSql = "UPDATE ".DATABASEU.DATATABLE."_credit_main set ResidueAmount=ResidueAmount-".$OrderTotal." where ClientID=".$ClientID." and CompanyID=".$CompanyID;
            $MainUpdateQuery = $db->query($MainUpdateSql);
            return $MainUpdateQuery;
        }
        //添加支付账期支付订单
        function  detailAdd($in){
                //分元转换
                $db = dbconnect::dataconnect()->getdb();
                $CompanyID	= $_SESSION['cc']['ccompany'];
                $ClientID	= $_SESSION['cc']['cid'];
                $Record_date = date('Y-m-d h:i:s',time());
                $OrderID	= $in['orderID'];
                $OrderTotal = MoneyFormat::MoneyOfYuanToFen($in['OrderTotal']);
                $CalendarMonth = 1;
                $Type		= 'out';
                $orderSn	= $in['orderSn'];
                $Describe	= "订单："."$orderSn"."，金额：".$in['OrderTotal']."元";
                $detailAddSql = "insert into ".DATABASEU.DATATABLE."_credit_detail (CompanyID,ClientID,PayDate,RecordDate,OrderID,OrderTotal,CalendarMonth,Type,DescribeContent,CreditStatus) value('".$CompanyID."','".$ClientID."','".$Record_date."','".$Record_date."','".$OrderID."','".$OrderTotal."','".$CalendarMonth."','".$Type."','".$Describe."','normal')";
                $DeatilAddSel = $db->query($detailAddSql);
                return $DeatilAddSel;
        }
        //我的账期
        function MyCredit(){
            $db = dbconnect::dataconnect()->getdb();
            $CompanyID = $_SESSION['ucc']['CompanyID'];
            $ClientID = $_SESSION['cc']['cid'];
            $MyCreditSql = "select Amount,ResidueAmount,CreditStatus,OneContent,TwoContent from ".DATABASEU.DATATABLE."_credit_main  where CompanyID=".$CompanyID." and ClientID=".$ClientID;
            $MyCreditSel = $db->get_row($MyCreditSql);
            return $MyCreditSel;
        }
        
        //我的账期申请状态
        function MyCreditApplyStatus(){
        	$db = dbconnect::dataconnect()->getdb();
        	$CompanyID = $_SESSION['ucc']['CompanyID'];
        	$ClientID = $_SESSION['cc']['cid'];
        	$MyCreditSql = "select count(*) as total from ".DATABASEU.DATATABLE."_credit_apply  where CompanyID=".$CompanyID." and ClientID=".$ClientID;
        	$MyCreditSel = $db->get_row($MyCreditSql);
        	return $MyCreditSel;
        }
        
        //账期欠款查询
        function UpMonth ($in){
            $db = dbconnect::dataconnect()->getdb();         
            $CompanyID = $_SESSION['ucc']['CompanyID'];
            $ClientID = $_SESSION['cc']['cid'];
            $last_month = date('Y-m', strtotime('last month'));
            $last['first'] = $last_month . '-01';
            $last['end'] = date('Y-m-d', strtotime("$last_month +1 month -1 day +23 hours +59 minutes +59 seconds"));
            $CreditDetSql = "select sum(OrderTotal+Interest+OverdueFine) as TotalSum from ".DATABASEU.DATATABLE."_credit_detail where  CompanyID=".$CompanyID."  and ClientID = ".$ClientID." and Type = 'out' and  left(RecordDate,10)>='". $last['first']."' and left(RecordDate,10)<='".$last['end']."'";
            $CreditDetSum = $db->get_row($CreditDetSql);
            return $CreditDetSum;
        }
        //账期详情
        function creditDetail($params){
            $db = dbconnect::dataconnect()->getdb();
            $CompanyID = $_SESSION['ucc']['CompanyID'];
            $ClientID  = $_SESSION['cc']['cid'];
            
            //默认搜索条件
            $sqlCondition = " cr.CompanyID=".$CompanyID." AND cr.ClientID=".$ClientID;
            
            //增加搜索条件
			if(!empty($params['begindate'])) $sqlCondition .= " AND cr.RecordDate>='".$params['begindate']." 00:00:00'";
			if(!empty($params['enddate']))   $sqlCondition .= " AND cr.RecordDate<='".$params['enddate']." 23:59:59'";	//含当天全天时间
			if(!empty($params['billtype']))  $sqlCondition .= " AND cr.Type='".$params['billtype']."'";
			if(isset($params['isup']) && $params['isup']) $sqlCondition .= " AND (cr.CreditStatus='unrefund' or cr.CreditStatus='normal') ";
			//todo 需要优化
			if(isset($params['isunset'])) $params['billtype'] = 'isup';
			
			$sql_c = "SELECT COUNT(*) AS allrow FROM ".DATABASEU.DATATABLE."_credit_detail AS cr LEFT JOIN ".DATATABLE."_order_orderinfo AS o ON cr.CompanyID=o.OrderCompany AND cr.ClientID=o.OrderUserID AND cr.OrderID=o.OrderID WHERE ".$sqlCondition;
			
            $sql_l = "SELECT (cr.OrderTotal+cr.Interest+cr.OverdueFine) as sumTotalf,cr.CompanyID,cr.Type,cr.ClientID,UNIX_TIMESTAMP(cr.PayDate) AS PayDate,UNIX_TIMESTAMP(cr.RecordDate) AS RecordDate,cr.OrderID,cr.OrderTotal,cr.Interest,cr.DescribeContent,cr.OverdueFine,o.OrderSN FROM ".DATABASEU.DATATABLE."_credit_detail AS cr LEFT JOIN ".DATATABLE."_order_orderinfo AS o ON cr.CompanyID=o.OrderCompany AND cr.ClientID=o.OrderUserID AND cr.OrderID=o.OrderID WHERE ".$sqlCondition." order by ID desc";
            $sql_d = "SELECT cr.CompanyID,cr.ClientID,UNIX_TIMESTAMP(cr.PayDate) AS PayDate,UNIX_TIMESTAMP(cr.RecordDate) AS RecordDate,SUM(IF(cr.Type='out', cr.OrderTotal, -cr.OrderTotal)) AS TotalSum,SUM(cr.Interest) AS InterestSum,SUM(cr.OverdueFine) AS FineSum,o.OrderSN FROM ".DATABASEU.DATATABLE."_credit_detail AS cr LEFT JOIN ".DATATABLE."_order_orderinfo AS o ON cr.CompanyID=o.OrderCompany AND cr.ClientID=o.OrderUserID AND cr.OrderID=o.OrderID WHERE  ".$sqlCondition." ";
            $sql_Sel = $db->get_row($sql_d);
            
            $rs    = $db->get_row($sql_c);
           $page  = new ShowPage;
           $page->PageSize		= 50;
           $page->Total		    = $rs['allrow'];
           $page->LinkAry		= array("m" => "creditDetail", "begindate" => $params['begindate'], "enddate" => $params['enddate'], "billtype" => $params['billtype'], "isup" => $params['isup'], "timeselect" => $params['timeselect']);
           $CreditDeSel['sql_Sel'] = $sql_Sel;
           $CreditDeSel['total']		= $rs['allrow'];
           $CreditDeSel['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
           $CreditDeSel['pageend']		= $page->PageNum()*$page->PageSize;
           $CreditDeSel['list']			= $db->get_results($sql_l." ".$page->OffSet());
           $CreditDeSel['showpage']		= $page->ShowLink('my.php');
            return $CreditDeSel;
            
        }
       //资质查看
        function creditApply(){
            $db = dbconnect::dataconnect()->getdb();
            $CompanyID = $_SESSION['ucc']['CompanyID'];
            $ClientID = $_SESSION['cc']['cid'];
            $creditApplySql = "select * from ".DATABASEU.DATATABLE."_credit_apply where CompanyID=".$CompanyID." and ClientID=".$ClientID;
            $creditApplySel = $db->get_row($creditApplySql);
            return $creditApplySel;
        }
        //资质添加
        function AddcreditApply($in){
            $db = dbconnect::dataconnect()->getdb();
            $CompanyID = $_SESSION['ucc']['CompanyID'];
            $ClientID = $_SESSION['cc']['cid'];
//            查询
            $AddcreditselSql = "select * from ".DATABASEU.DATATABLE."_credit_apply where CompanyID=".$CompanyID." and ClientID=".$ClientID;
            $AddcreditselSel = $db->get_row($AddcreditselSql);
            $Level = $in['Level'];
            $LevelOther = $in['LevelOther'];
            $PurchaseAmount = $in['PurchaseAmount'];
            $Amount = $in['Amount'];
            if($Level == ''){
                $Level=0;
            }
            if($AddcreditselSel ==''){
                //添加
                $AddcreditSql = "insert into ".DATABASEU.DATATABLE."_credit_apply (CompanyID,ClientID,Level,LevelOther,Amount,PurchaseAmount)value('".$CompanyID."','".$ClientID."','".$Level."','".$LevelOther."','".$Amount."','".$PurchaseAmount."')";
                $AddcreditSel = $db->query($AddcreditSql);               
                return $AddcreditSel;
            }else{
                $AddcreditSql = "UPDATE ".DATABASEU.DATATABLE."_credit_apply SET CompanyID ='".$CompanyID."',ClientID='".$ClientID."',Level='".$Level."',LevelOther='".$LevelOther."',PurchaseAmount='".$PurchaseAmount."',Amount='".$Amount."' where CompanyID='".$CompanyID."' and ClientID= '".$ClientID."'";
                $AddcreditSel = $db->query($AddcreditSql);
                return $AddcreditSel;
            }
            

        }
        function SelQu($in){
            $db = dbconnect::dataconnect()->getdb();
            $CompanyID = $_SESSION['ucc']['CompanyID'];
            $ClientID = $_SESSION['cc']['cid'];
            $SelQusql = "select CreditStatus from ".DATABASEU.DATATABLE."_credit_main where CompanyID='".$CompanyID."' and ClientID= '".$ClientID."'";
            $SelQuSel =$db->get_row($SelQusql);
            return $SelQuSel;
        }

//END
}
?>