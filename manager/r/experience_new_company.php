<?php 
set_time_limit(60);


$menu_flag = "manager";
include_once ("header.php");

$arrReturnResult = array(
	'status' => 100,
	'message' => '生成成功'
);

$in['CompanyIndustry'] = intval($in['CompanyIndustry']);

if(empty($in['CompanyIndustry'])){
	resultMessage('未指定公司行业');
}

function makeNewCompany($nCompanyIndustry){
	global $in,$db;

	// 随机从该行业中读取一个公司作为蓝本
	/*$sSqlIndustry2 = " CompanyIndustry = {$nCompanyIndustry} and CompanyFlag = '0' and IsSystem = 1 ";
	$sSqlIndustry = " where ".$sSqlIndustry2;
	
	$sRandSql = "SELECT c1.CompanyID,c1.CompanyPrefix FROM ".DATABASEU.DATATABLE."_order_company AS c1 
    JOIN
	(
	    SELECT ROUND(
		   RAND() *((SELECT MAX(CompanyID) FROM ".DATABASEU.DATATABLE."_order_company {$sSqlIndustry})-(SELECT MIN(CompanyID) FROM ".DATABASEU.DATATABLE."_order_company {$sSqlIndustry}))+
		  (SELECT MIN(CompanyID) FROM ".DATABASEU.DATATABLE."_order_company {$sSqlIndustry})
	    )AS CompanyID
	)AS c2
WHERE c1.CompanyID >= c2.CompanyID and {$sSqlIndustry2}
ORDER BY c1.CompanyID LIMIT 0,1";
	
	*/
	
	$sRandSql = "select CompanyID,CompanyPrefix from ".DATABASEU.DATATABLE."_order_company where CompanyIndustry = {$nCompanyIndustry} and CompanyFlag = '0' and IsSystem = 1 order by CompanyID asc limit 0,1 ";
	
	$arrResultCompany = $db->get_row($sRandSql);
	if(empty($arrResultCompany['CompanyID'])){
		resultMessage('随机读取公司失败');
	}
	
	$nCompanyID = $arrResultCompany['CompanyID'];
	$ndatabaseid = $nCompanyIndustry;
	
	//$nCompanyID = 1;
	//$arrResultCompany['CompanyPrefix'] = 'rsung';
	//$ndatabaseid = 1;
	
	/**
	 * 复制公司中信息
	 * 
	 * 这家公司的所有其他数据
	 */
	$sRandCode = makeRandString('8');
	
	// 公司基本信息
	$sSqlCopy = "insert into ".DATABASEU.DATATABLE."_order_company(
			`CompanyArea`,`CompanyIndustry`,`CompanyAgent`,`CompanyName`,`CompanySigned`,
			`CompanyPrefix`,`CompanyCity`,`CompanyContact`,`CompanyMobile`,`CompanyPhone`,
			`CompanyFax`,`CompanyAddress`,`CompanyEmail`,`CompanyUrl`,`CompanyWeb`,
			`CompanyLogo`,`CompanyLogin`,`CompanyRemark`,`CompanyDate`,`CompanyFlag`,
			`CompanyDatabase`,`IsSystem`
		) 
		select 
		  	CompanyArea,{$nCompanyIndustry},CompanyAgent,CompanyName,CompanySigned,
			concat(CompanyPrefix,'$sRandCode'),CompanyCity,CompanyContact,CompanyMobile,CompanyPhone,
			CompanyFax,CompanyAddress,CompanyEmail,CompanyUrl,CompanyWeb,
			CompanyLogo,CompanyLogin,CompanyRemark,".time().",'0',
			{$ndatabaseid},0 
	    from ".DATABASEU.DATATABLE."_order_company where CompanyID = {$nCompanyID} ";
	
	$insertCopy  = $db->query($sSqlCopy);

	if($insertCopy!==false)
	{
		$insert_id = mysql_insert_id();

		// 更新公司前缀
		$db->query("update ".DATABASEU.DATATABLE."_order_company set CompanyPrefix = '{$arrResultCompany['CompanyPrefix']}{$insert_id}' where CompanyID = {$insert_id} ");

		$insql = "insert into ".DATABASEU.DATATABLE."_order_cs(CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_UpDate,CS_UpdateTime,CS_SmsNumber) values(".$insert_id.",50,'".date("Y-m-d")."','".date("Y-m-d",strtotime('+10 year'))."','".date("Y-m-d")."',".time().",20)";
		$db->query($insql);
	
		if(!(file_exists (RESOURCE_PATH.$insert_id)))
		{
			_mkdir(RESOURCE_PATH,$insert_id);
		}
	}else{
		resultMessage('公司基本信息保存不成功!');
	}
	
	$arrPrefix = $db->get_row("select CompanyPrefix from ".DATABASEU.DATATABLE."_order_company where CompanyID = {$insert_id} limit 0,1 ");
	$databaseidExtend = DB_DATABASE.'_'.$ndatabaseid.'.';
	
	// 复制公司配置信息
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_companyset
	(SetCompany,SetName,SetValue)
	select {$insert_id}, SetName, SetValue from ".DATABASEU.DATATABLE."_order_companyset
	where SetCompany = {$nCompanyID}
	";
	$db->query($upsql);

	// 拷贝收款帐号
	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_accounts
	(AccountsCompany,AccountsBank,AccountsNO,AccountsName,AccountsType,AccountsDate,PayType,PayPartnerID,PayKey,AliPayType,OldID)
	select {$insert_id}, AccountsBank, AccountsNO,AccountsName,AccountsType,".time().",PayType,PayPartnerID,PayKey,AliPayType,AccountsID from ".DATATABLE."_order_accounts
	where AccountsCompany = {$nCompanyID}
	";
	$db->query($upsql);

	// 处理一下对应的关系
	$resultAccounts= array();
	$sqlselect = "select AccountsID,OldID from {$databaseidExtend}".DATATABLE."_order_accounts where AccountsCompany = {$insert_id} ";
	$tempresult = $db->get_results($sqlselect);
	if(is_array($tempresult)){
		foreach($tempresult as $val){
			if($val['AccountsID'] && $val['OldID']){
				$resultAccounts[$val['OldID']] = $val['AccountsID'];
			}
		}
	}

	// 拷贝其它款项设置
	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_expense_bill
	(BillNO,BillName,CompanyID)
	select BillNO,BillName,{$insert_id} from ".DATATABLE."_order_expense_bill
	where CompanyID = {$nCompanyID}
	";
	$db->query($upsql);
	
	// 拷贝地区
	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_area
	(AreaCompany,AreaParentID,AreaName,AreaPinyi,AreaAbout,OldID)
	select {$insert_id},AreaParentID,AreaName,AreaPinyi,AreaAbout,AreaID from ".DATATABLE."_order_area
	where AreaCompany = {$nCompanyID}
	";
	$db->query($upsql);
	
	// 处理一下对应的关系
	$resultArea = array();
	$sqlselect = "select AreaID,OldID,AreaParentID from {$databaseidExtend}".DATATABLE."_order_area where AreaCompany = {$insert_id} ";
	$tempresult = $db->get_results($sqlselect);
	if(is_array($tempresult)){
		foreach($tempresult as $val){
			if($val['AreaID'] && $val['OldID']){
				$resultArea[$val['OldID']] = $val['AreaID'];
			}
		}
		
		foreach($tempresult as $val){
			if($val['AreaParentID']){
				$sParentID = $resultArea[$val['AreaParentID']];
				$updateSql = "update  {$databaseidExtend}".DATATABLE."_order_area set AreaParentID='{$sParentID}' where AreaID = {$val['AreaID']} ";
				$db->query($updateSql);
			}
		}
	}

	// 创建客户管理员帐号信息
	$sUserName = $arrPrefix['CompanyPrefix'];
	$sPassword = '123';
	$passmsg = ChangeMsg($sUserName,$sPassword);
	
	$insertSql = "insert  into ".DATABASEU.DATATABLE."_order_user(`UserName`,`UserPass`,`UserCompany`,`UserTrueName`,`UserPhone`,`UserRemark`,`UserDate`,`UserLoginDate`,`UserLoginIP`,`UserLogin`,`UserSessionID`,`UserFlag`,`UserDataBase`,`UserType`,`TokenValue`) values 
		('{$sUserName}','{$passmsg}',{$insert_id},'管理员','管理员','',".time().",'','',0,'','9',0,'M','')";
	
	if($db->query($insertSql)===false){
		resultMessage('公司管理员帐号信息保存不成功!');
	}

	// 复制商品信息

	// 1 = 复制品牌
	$sSqlCopy = "insert into {$databaseidExtend}".DATATABLE."_order_brand(
	`BrandNO`,`BrandName`,`BrandPinYin`,`CompanyID`,`OldID`
	)
	select
	BrandNO,BrandName,BrandPinYin,{$insert_id},BrandID
	from ".DATATABLE."_order_brand where CompanyID = {$nCompanyID} ";

	if($db->query($sSqlCopy)===false){
		resultMessage('复制品牌信息不成功!');
	}else{
		$resultBrand = array();
		$sqlselect = "select BrandID,OldID from {$databaseidExtend}".DATATABLE."_order_brand where CompanyID = {$insert_id} ";
		$tempresult = $db->get_results($sqlselect);

		if(is_array($tempresult)){
			foreach($tempresult as $val){
				if($val['BrandID'] && $val['OldID']){
					$resultBrand[$val['OldID']] = $val['BrandID'];
				}
			}
		}
	}
	
	// 2 = 复制商品分类
	$sSqlCopy = "insert into {$databaseidExtend}".DATATABLE."_order_site(
	`CompanyID`,`ParentID`,`SiteNO`,`SiteOrder`,
	`SiteName`,`SitePinyi`,`SiteAdmin`,`Content`,`Disabled`,`OldID`
	)
	select
	{$insert_id},ParentID,SiteNO,SiteOrder,SiteName,
	SitePinyi,SiteAdmin,Content,Disabled,SiteID
	from ".DATATABLE."_order_site where CompanyID = {$nCompanyID} ";
	if($db->query($sSqlCopy)===false){
		resultMessage('复制分类信息不成功!');
	}else{
		// 处理一下对应的关系
		$resultSite = array();
		$sqlselect = "select SiteID,OldID,ParentID,SiteNO from {$databaseidExtend}".DATATABLE."_order_site where CompanyID = {$insert_id} ";
		$tempresult = $db->get_results($sqlselect);
		if(is_array($tempresult)){
			foreach($tempresult as $val){
				if($val['SiteID'] && $val['OldID']){
					$resultSite[$val['OldID']] = $val['SiteID'];
				}
			}
		}
		
		if(is_array($tempresult)){
			foreach($tempresult as $val){
				$sParentID = isset($resultSite[$val['ParentID']]) ? $resultSite[$val['ParentID']] :0;
				$sSiteNO = $val['SiteNO'];
				
				if(is_array($resultSite)){
					foreach($resultSite as $ky=>$va){
						$sSiteNO = str_replace('.'.$ky.'.','.'.$va.'.',$sSiteNO);
					}
				}
				
				$updateSql = "update  {$databaseidExtend}".DATATABLE."_order_site set ParentID='{$sParentID}' ,SiteNO = '{$sSiteNO}' where SiteID = {$val['SiteID']} ";
				$db->query($updateSql);
			}
		}
	}

	// 3 = 拷贝商品主信息
	$sSqlCopy = "insert into {$databaseidExtend}".DATATABLE."_order_content_index(
	`CompanyID`,`SiteID`,`BrandID`,`OrderID`,`CommendID`,
	`Count`,`FlagID`,`Name`,`Pinyi`,`Coding`,
	`Barcode`,`Price1`,`Price2`,`Price3`,`Units`,
	`Casing`,`Picture`,`Color`,`Specification`,`Model`,
	`LibraryDown`,`LibraryUp`,`GUID`,`OldID`,`IsSystem`
	)
	select
	{$insert_id},SiteID,BrandID,OrderID,CommendID,
	Count,FlagID,Name,Pinyi,Coding,
	Barcode,Price1,Price2,Price3,Units,
	Casing,Picture,Color,Specification,Model,
	LibraryDown,LibraryUp,GUID,ID,1
	from ".DATATABLE."_order_content_index where CompanyID = {$nCompanyID} ";
	//from ".DATATABLE."_order_content_index where CompanyID = {$nCompanyID} order by ID DESC limit 0,100";

	if($db->query($sSqlCopy)===false){
		resultMessage('复制商品主信息不成功!');
	}else{
		// 更新商品的品牌和分类
		$arrProducts = $db->get_results("select ID,SiteID,BrandID,OldID from {$databaseidExtend}".DATATABLE."_order_content_index where CompanyID = {$insert_id} ");

		$resultProduct = array();

		if(is_array($arrProducts)){
			foreach($arrProducts as $vals){

				if($vals['OldID']){
					$resultProduct[$vals['OldID']] = $vals['ID'];

					// 分类和品牌
					$sqlupdate = "update {$databaseidExtend}".DATATABLE."_order_content_index set SiteID = ".(isset($resultSite[$vals['SiteID']]) ? $resultSite[$vals['SiteID']] : $vals['SiteID'])." , BrandID = ".(isset($resultBrand[$vals['BrandID']]) ? $resultBrand[$vals['BrandID']] : $vals['BrandID'])." where ID = {$vals['ID']} and CompanyID = {$insert_id} ";
					$db->query($sqlupdate);
					
					// 拷贝商品主内容
					$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_content_1
					(ContentIndexID,CompanyID,ContentCreateDate,ContentEditDate,ContentCreateUser,ContentEditUser,ContentKeywords,Content,ContentPoint,Package,Deduct,FieldContent) 
					select {$vals['ID']}, {$insert_id}, ".time().", ".time().", ContentCreateUser,ContentEditUser,ContentKeywords,Content, ContentPoint, Package,Deduct,FieldContent from ".DATATABLE."_order_content_1
					where ContentIndexID = {$vals['OldID']} and CompanyID = {$nCompanyID}
					";
					$db->query($upsql);
					
					// 拷贝图片
					$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_resource
					(CompanyID,IndexID,Name,OldName,Path,Size,OrderID)
					select {$insert_id}, {$vals['ID']}, Name, OldName, Path,Size,OrderID from ".DATATABLE."_order_resource
					where IndexID = {$vals['OldID']} and CompanyID = {$nCompanyID}
					";
					$db->query($upsql);
					
					// 拷贝库存
					$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_number
					(CompanyID,ContentID,OrderNumber,ContentNumber)
					select {$insert_id}, {$vals['ID']}, OrderNumber, ContentNumber from ".DATATABLE."_order_number
					where ContentID = {$vals['OldID']} and CompanyID = {$nCompanyID}
					";
					$db->query($upsql);
					
					// 拷贝库存明细
					$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_inventory_number
					(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber)
					select {$insert_id}, {$vals['ID']}, ContentColor, ContentSpec,OrderNumber,ContentNumber from ".DATATABLE."_order_inventory_number
					where ContentID = {$vals['OldID']} and CompanyID = {$nCompanyID}
					";
					$db->query($upsql);
				}
			}
		}
	}

	// 拷贝经销商资料
	
	// 复制经销商
	// [地区ID需要更新]
	// ClientShield 屏蔽的商品分类  需要更新
	// ClientBrandPercent 商品品牌 需要更新
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_dealers
	 (ClientCompany,ClientName,ClientPassword,ClientMobile,LoginIP,
	 LoginDate,LoginCount,ClientFlag,ClientDataBase,TokenValue,OldID,IsSystem)
	 select {$insert_id},REPLACE(ClientName,'".$arrResultCompany['CompanyPrefix']."-','".$arrPrefix['CompanyPrefix']."-'),ClientPassword,ClientMobile,'','',
	 '0',ClientFlag,'1','',ClientID,1
	 from ".DATABASEU.DATATABLE."_order_dealers
	 where ClientCompany = {$nCompanyID} order by ClientID asc
	 ";

	if($db->query($upsql)!==false){

		$resultClient = array();
		
		$arrClients  = $db->get_results("select ClientID,OldID,ClientName from ".DATABASEU.DATATABLE."_order_dealers where ClientCompany = {$insert_id} ");
		if(is_array($arrClients)){
			
			foreach($arrClients as $arrClient){
				if($arrClient['ClientID'] && $arrClient['OldID']){
					$resultClient[$arrClient['OldID']] = $arrClient['ClientID'];
				}
				
				// 复制经销商扩展信息
				$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_client
				(ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientCompanyPinyi,
				ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,
				ClientAdd,ClientAbout,ClientDate,ClientShield,ClientSetPrice,ClientPercent,
				ClientBrandPercent,ClientPay,ClientConsignment,ClientFlag,
				AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber,ClientGUID,
				YapiUserId,YapiuserName)
				select {$arrClient['ClientID']},{$insert_id},ClientLevel,ClientArea,'{$arrClient['ClientName']}',ClientCompanyName,ClientCompanyPinyi,
				ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,
				ClientAdd,ClientAbout,'".time()."',ClientShield,ClientSetPrice,ClientPercent,
		 ClientBrandPercent,ClientPay,ClientConsignment,ClientFlag,
		 AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber,ClientGUID,
		 YapiUserId,YapiuserName
		 from ".DATATABLE."_order_client
						 where ClientID = {$arrClient['OldID']}
					";

				if($db->query($upsql)!==false){
					

					$arrClientData = $db->get_row("select ClientID,ClientArea,ClientShield,ClientBrandPercent from {$databaseidExtend}".DATATABLE."_order_client where ClientID = {$arrClient['ClientID']} ");
					


					// 更新经销商中包含的数据
					
					// - 地区更新 
					$nNewAreaID  = !empty($arrClientData['ClientArea']) ? (!empty($resultArea[$arrClientData['ClientArea']])? $resultArea[$arrClientData['ClientArea']] : 0) : 0;
					
					// - 屏蔽的商品分类
					$sClientShield = $arrClientData['ClientShield'];
					if($sClientShield){
						$arrNewArray = array();
						foreach(explode(',', $sClientShield) as $ts){
							if($ts && !empty($resultSite[$ts]))
							$arrNewArray[]  = $resultSite[$ts];
						}
						$sClientShield = implode(',',$arrNewArray);
					}
					
					// 品牌折扣价格
					if($arrClientData['ClientBrandPercent']){
						$arrClientBrandPercent = unserialize($arrClientData['ClientBrandPercent']);
						if(!empty($arrClientBrandPercent)){
							$arrNewArray = array();
							foreach($arrClientBrandPercent as $arrs){
								if(!empty($resultBrand[$arrs['i']])){
									$arrNewArray[] = array(
											'i' => $resultBrand[$arrs['i']],
											'd' => $arrs['d']
									);
								}
							}
							
							$arrClientBrandPercent = $arrNewArray;
						}
						$sClientBrandPercent = !empty($arrClientBrandPercent) ? serialize($arrClientBrandPercent) : '';
					}else{
						$sClientBrandPercent = '';
					}
					
					$db->query("update {$databaseidExtend}".DATATABLE."_order_client set ClientArea ='{$nNewAreaID}',ClientShield='{$sClientShield}',ClientBrandPercent='{$sClientBrandPercent}'  where ClientID = {$arrClient['ClientID']} ");
				}
			}
		}
	
	}else{
		runMessage('拷贝经销商资料失败！');
	}

	// 复制订单
	// - 复制订单主信息
	// 经销商ID信息需要更新

	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_orderinfo
(OrderSN,OrderCompany,OrderUserID,OrderSendType,OrderSendStatus,
OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,
OrderReceiveAdd,InvoiceType,InvoiceTax,DeliveryDate,OrderRemark,
OrderTotal,OrderIntegral,OrderStatus,OrderDate,OrderType,
OrderSaler,OrderCollect,OrderApi,OrderFrom,OldID
)
select OrderSN,{$insert_id},OrderUserID,OrderSendType,OrderSendStatus,
OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,
OrderReceiveAdd,InvoiceType,InvoiceTax,DeliveryDate,OrderRemark,
OrderTotal,OrderIntegral,OrderStatus,".time().",OrderType,
OrderSaler,OrderCollect,OrderApi,OrderFrom,OrderID
	 from ".DATATABLE."_order_orderinfo
	 where OrderCompany = {$nCompanyID} ";
	 // where OrderCompany = {$nCompanyID} order by OrderID Asc limit 0,150 ";

	$resultOrder = array();
	
	if($db->query($upsql)!==false){
		// 读取订单更新订单的经销商ID
		
		
		$arrOldData = $db->get_results(" select OrderID,OrderUserID,OldID from {$databaseidExtend}".DATATABLE."_order_orderinfo where OrderCompany = {$insert_id} ");

		if(is_array($arrOldData)){
			foreach($arrOldData as $var){
				if($var['OrderID'] && $var['OldID']){
					$resultOrder[$var['OldID']] = $var['OrderID'];
				}
				
				// 更新订购经销商
				$db->query(" update {$databaseidExtend}".DATATABLE."_order_orderinfo set OrderUserID = ".( !empty($resultClient[$var['OrderUserID']]) ? $resultClient[$var['OrderUserID']] :'0' )." where OrderID = {$var['OrderID']} ");
		
				// 拷贝购物车
				// ClientID 需要更新
				// ContentID 需要更新
				$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_cart
(OrderID,CompanyID,ClientID,ContentID,ContentName,
ContentColor,ContentSpecification,ContentPrice,ContentNumber,
ContentPercent,ContentSend,OldID)
select {$var['OrderID']},{$insert_id},ClientID,ContentID,ContentName,
ContentColor,ContentSpecification,ContentPrice,ContentNumber,
ContentPercent,ContentSend,ID
	 from ".DATATABLE."_order_cart
	 where OrderID = {$var['OldID']}
	 ";
				$db->query($upsql);
				
				// 拷贝赠品
				$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_cart_gifts
				(OrderID,CompanyID,ClientID,ContentID,ContentName,
				ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentSend,OldID)
				select {$var['OrderID']},{$insert_id},ClientID,ContentID,ContentName,
				ContentColor,ContentSpecification,ContentPrice,ContentNumber,
				ContentSend,ID
				from ".DATATABLE."_order_cart_gifts
				where OrderID = {$var['OldID']}
				";
				$db->query($upsql);

				// 拷贝购物车备份
				$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_cartbak
(CompanyID,OrderID,Content)
select {$insert_id},{$var['OrderID']},Content
	 from ".DATATABLE."_order_cartbak
	 where OrderID = {$var['OldID']}
	 ";
				$db->query($upsql);
				

				// 拷贝订单操作日志
				$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_ordersubmit
(CompanyID,OrderID,AdminUser,Name,
Date,Status,Content)
select {$insert_id},{$var['OrderID']},'{$sUserName}',Name,
".time().",Status,Content
	 from ".DATATABLE."_order_ordersubmit
	 where OrderID = {$var['OldID']}
	 ";
				$db->query($upsql);
			}

			$resultCart = $resultCart2  = array();
			

			// 更新购物车中的ClientID和ContentID
			$arrCartData = $db->get_results("select ID,OrderID,ClientID,ContentID,OldID from {$databaseidExtend}".DATATABLE."_order_cart where CompanyID = {$insert_id} ");
			if(is_array($arrCartData)){
				foreach($arrCartData as $temp){
					if($temp['ID'] && $temp['OldID']){
						$resultCart[$temp['OldID']] = $temp['ID'];
					}

					$db->query("update {$databaseidExtend}".DATATABLE."_order_cart set ClientID =".( !empty($resultClient[$temp['OrderUserID']]) ? $resultClient[$temp['OrderUserID']] :'0' )." ,ContentID = ".( !empty($resultProduct[$temp['ContentID']]) ? $resultProduct[$temp['ContentID']] :'0' )." where ID = {$temp['ID']} ");
				}
			}
			
			// 更新购物车赠品中的ClientID和ContentID
			$arrCartData2 = $db->get_results("select ID,OrderID,ClientID,ContentID,OldID from {$databaseidExtend}".DATATABLE."_order_cart_gifts where CompanyID = {$insert_id} ");
			if(is_array($arrCartData2)){
				foreach($arrCartData2 as $temp){
					if($temp['ID'] && $temp['OldID']){
						$resultCart2[$temp['OldID']] = $temp['ID'];
					}
			
					$db->query("update {$databaseidExtend}".DATATABLE."_order_cart_gifts set ClientID =".( !empty($resultClient[$var['OrderUserID']]) ? $resultClient[$var['OrderUserID']] :'0' )." ,ContentID = ".( !empty($resultProduct[$var['ContentID']]) ? $resultProduct[$var['ContentID']] :'0' )." where ID = {$temp['ID']} ");
				}
			}
		}
		
	}else{
		runMessage('复制订单基础信息失败');
	}
	
	// 复制发货单
   // 1 - 复制物流公司
   $upsql = "insert into {$databaseidExtend}".DATATABLE."_order_logistics
(LogisticsCompany,LogisticsName,LogisticsPinyi,LogisticsContact,
LogisticsPhone,LogisticsFax,LogisticsMobile,LogisticsAddress,
LogisticsUrl,LogisticsAbout,LogisticsDate,OldID)
	select {$insert_id},LogisticsName,LogisticsPinyi,LogisticsContact,
LogisticsPhone,LogisticsFax,LogisticsMobile,LogisticsAddress,
LogisticsUrl,LogisticsAbout,".time().",LogisticsID from ".DATATABLE."_order_logistics
	 where LogisticsCompany = {$nCompanyID}
	 ";
	$db->query($upsql);

	$resultLogistics = array();
	$sqlselect = "select LogisticsID,OldID from {$databaseidExtend}".DATATABLE."_order_logistics where LogisticsCompany = {$insert_id} ";
	$tempresult = $db->get_results($sqlselect);
	if(is_array($tempresult)){
		foreach($tempresult as $val){
			if($val['LogisticsID'] && $val['OldID']){
				$resultLogistics[$val['OldID']] = $val['LogisticsID'];
			}
		}
	}

	// 2 - 复制发货单
	// ConsignmentClient需要更新
	// ConsignmentLogistics 需要更新
   $upsql = "insert into {$databaseidExtend}".DATATABLE."_order_consignment
(ConsignmentCompany,ConsignmentClient,ConsignmentOrder,ConsignmentLogistics,
ConsignmentNO,ConsignmentMan,ConsignmentDate,ConsignmentRemark,
ConsignmentMoneyType,ConsignmentMoney,InceptMan,InceptArea,
InceptAddress,InceptCompany,InceptPhone,InputDate,
ConsignmentUser,ConsignmentFlag,OldID)

	select {$insert_id},ConsignmentClient,ConsignmentOrder,ConsignmentLogistics,
ConsignmentNO,ConsignmentMan,'".date('Y-m-d')."',ConsignmentRemark,
ConsignmentMoneyType,ConsignmentMoney,InceptMan,InceptArea,
InceptAddress,InceptCompany,InceptPhone,InputDate,
ConsignmentUser,ConsignmentFlag,ConsignmentID from ".DATATABLE."_order_consignment
	 where ConsignmentCompany = {$nCompanyID}
	 ";
	$db->query($upsql);

	// 更新发货单中的ConsignmentClient和ConsignmentLogistics
	$arrConsignmentData = $db->get_results("select ConsignmentID,ConsignmentClient,ConsignmentLogistics,OldID from {$databaseidExtend}".DATATABLE."_order_consignment where ConsignmentCompany = {$insert_id} ");
	
	if(is_array($arrConsignmentData)){
		foreach($arrConsignmentData as $temp){
			$db->query("update {$databaseidExtend}".DATATABLE."_order_consignment set ConsignmentClient =".( !empty($resultClient[$temp['ConsignmentClient']]) ? $resultClient[$temp['ConsignmentClient']] :'0' )." ,ConsignmentLogistics = ".( !empty($resultLogistics[$temp['ConsignmentLogistics']]) ? $resultLogistics[$temp['ConsignmentLogistics']] :'0' )." where ConsignmentID = {$temp['ConsignmentID']} ");

				// 3 - 复制出库明细
				// OrderID 需要更新
				// CartID 需要更新
				// ContentID 需要更新
		   $upsql = "insert into {$databaseidExtend}".DATATABLE."_order_out_library
		(CompanyID,ConsignmentID,OrderID,CartID,
		ContentID,ContentNumber,ConType)

			select {$insert_id},{$temp['ConsignmentID']},OrderID,CartID,
		ContentID,ContentNumber,ConType from ".DATATABLE."_order_out_library
			 where ConsignmentID = {$temp['OldID']}
			 ";

			$db->query($upsql);
		}

		// 更新
		$arrOutlibraryData = $db->get_results("select ConsignmentID,OrderID,CartID,ContentID,ConType,ContentNumber,CompanyID from {$databaseidExtend}".DATATABLE."_order_out_library where CompanyID = {$insert_id} ");

		if(is_array($arrOutlibraryData)){
			foreach($arrOutlibraryData as $temp){
				$db->query("update {$databaseidExtend}".DATATABLE."_order_out_library set OrderID =".( !empty($resultOrder[$temp['OrderID']]) ? $resultOrder[$temp['OrderID']] :'0' )." ,CartID = ".($temp['ConType']=='c' ? ( !empty($resultCart[$temp['CartID']]) ? $resultCart[$temp['CartID']] :'0' ) : ( !empty($resultCart2[$temp['CartID']]) ? $resultCart2[$temp['CartID']] :'0' ) ).",ContentID = ".( !empty($resultProduct[$temp['ContentID']]) ? $resultProduct[$temp['ContentID']] :'0' )." where ConsignmentID = {$temp['ConsignmentID']} and OrderID ='{$temp['OrderID']}' and CartID ='{$temp['CartID']}' and  ContentID = '{$temp['ContentID']}' and CompanyID = '{$temp['CompanyID']}' and ContentNumber ='{$temp['ContentNumber']}' and ConType = '{$temp['ConType']}' ");
			}
		}

	}
	
	// 复制款项
	// FinanceClient 需要更新
	// FinanceOrderID 需要更新
	// FinanceAccounts 需要更新
	// FinanceUser 需要更新

	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_finance
(FinanceCompany,FinanceClient,FinanceOrderID,FinanceOrder,
FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,
FinanceToDate,FinanceUpDate,FinanceDate,FinanceUser,FinanceAdmin,
FinanceFlag,FinancePaysn,FinanceType,FinanceFrom,
FinanceApi,FinanceDevice)

	select {$insert_id},FinanceClient,FinanceOrderID,FinanceOrder,
FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,
'".date('Y-m-d')."',".time().",".time().",FinanceUser,'{$sUserName}',
FinanceFlag,FinancePaysn,FinanceType,FinanceFrom,
FinanceApi,FinanceDevice from ".DATATABLE."_order_finance
	 where FinanceCompany = {$nCompanyID}
	 ";
	$db->query($upsql);

	// 更新
	$arrFinanceData = $db->get_results("select FinanceID,FinanceClient,FinanceOrderID,FinanceAccounts from {$databaseidExtend}".DATATABLE."_order_finance where FinanceCompany = {$insert_id} ");
	if(is_array($arrFinanceData)){
		foreach($arrFinanceData as $temp){
			$db->query("update {$databaseidExtend}".DATATABLE."_order_finance set FinanceOrderID =".( !empty($resultOrder[$temp['FinanceOrderID']]) ? $resultOrder[$temp['FinanceOrderID']] :'0' )." ,FinanceClient = ".( !empty($resultClient[$temp['FinanceClient']]) ? $resultClient[$temp['FinanceClient']] :'0' ).",FinanceAccounts = ".( !empty($resultAccounts[$temp['FinanceAccounts']]) ? $resultAccounts[$temp['FinanceAccounts']] :'0' )." where FinanceID = {$temp['FinanceID']} ");
		}
	}

	// 拷贝公告分类数据

	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_sort
(SortCompany,SortParent,SortName,SortOrder,OldID)

	select {$insert_id},SortParent,SortName,SortOrder,SortID from ".DATATABLE."_order_sort
	 where SortCompany = {$nCompanyID}
	 ";
	$db->query($upsql);

	$resultSort = array();
	$sqlselect = "select SortID,OldID from {$databaseidExtend}".DATATABLE."_order_sort where SortCompany = {$insert_id} ";
	$tempresult = $db->get_results($sqlselect);
	if(is_array($tempresult)){
		foreach($tempresult as $val){
			if($val['SortID'] && $val['OldID']){
				$resultSort[$val['OldID']] = $val['SortID'];
			}
		}
	}

	// 拷贝公告数据
	// 更新SortID
	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_article
(ArticleCompany,ArticleSort,ArticleTitle,ArticleColor,
ArticleAuthor,ArticlePicture,ArticleFileName,ArticleContent,
ArticleDate,ArticleUser,ArticleFlag,ArticleOrder,
ArticleCount)

	select 
	{$insert_id},ArticleSort,ArticleTitle,ArticleColor,
ArticleAuthor,ArticlePicture,ArticleFileName,ArticleContent,
".time().",'{$sUserName}',ArticleFlag,ArticleOrder,
ArticleCount from ".DATATABLE."_order_article
	 where ArticleCompany = {$nCompanyID}
	 ";
	$db->query($upsql);

	// 更新
	$arrArticleData = $db->get_results("select ArticleID,ArticleSort from {$databaseidExtend}".DATATABLE."_order_article where ArticleCompany = {$insert_id} ");
	if(is_array($arrArticleData)){
		foreach($arrArticleData as $temp){
			$db->query("update {$databaseidExtend}".DATATABLE."_order_article set ArticleSort =".( !empty($resultSort[$temp['ArticleSort']]) ? $resultSort[$temp['ArticleSort']] :'0' )." where ArticleID = {$temp['ArticleID']} ");
		}
	}

	// 拷贝交流工具数据

	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_tool
(ToolCompany,ToolType,ToolName,ToolNO,ToolCode)

	select {$insert_id},ToolType,ToolName,ToolNO,ToolCode
	from ".DATATABLE."_order_tool
	 where ToolCompany = {$nCompanyID}
	 ";
	$db->query($upsql);


	// 拷贝联系方式

	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_contact
(ContactCompany,ContactName,ContactValue)

	select {$insert_id},ContactName,ContactValue
	from ".DATATABLE."_order_contact
	 where ContactCompany = {$nCompanyID}
	 ";
	$db->query($upsql);

	// 支付配置信息

	$upsql = "insert into ".DATABASEU.DATATABLE."_order_getway
(GetWay,CompanyID,MerchantNO,SignNO,SignAccount,SignMsgKey,
SignMsg,B2B,Fee,Status,AccountType,IsDefault,
MerchantName)

	select 
	GetWay,{$insert_id},MerchantNO,SignNO,SignAccount,SignMsgKey,
SignMsg,B2B,Fee,Status,AccountType,IsDefault,
MerchantName
	from ".DATABASEU.DATATABLE."_order_getway
	 where CompanyID = {$nCompanyID}
	 ";
	$db->query($upsql);


	// 拷贝退货单
	// ReturnClient 需要更新

   $upsql = "insert into {$databaseidExtend}".DATATABLE."_order_returninfo
(ReturnSN,ReturnOrder,ReturnCompany,ReturnClient,
ReturnSendType,ReturnSendStatus,ReturnSendAbout,ReturnProductW,
ReturnProductB,ReturnAbout,ReturnPicture,ReturnTotal,
ReturnStatus,ReturnDate,ReturnType,ReturnApi,OldID
)

	select 
	ReturnSN,ReturnOrder,{$insert_id},ReturnClient,
ReturnSendType,ReturnSendStatus,ReturnSendAbout,ReturnProductW,
ReturnProductB,ReturnAbout,ReturnPicture,ReturnTotal,
ReturnStatus,ReturnDate,ReturnType,ReturnApi,ReturnID
 from ".DATATABLE."_order_returninfo
	 where ReturnCompany = {$nCompanyID}
	 ";
	$db->query($upsql);
	
	// 更新退货单中的ReturnClient
	$arrReturnData = $db->get_results("select ReturnID,ReturnClient,OldID from {$databaseidExtend}".DATATABLE."_order_returninfo where ReturnCompany = {$insert_id} ");
	if(is_array($arrReturnData)){
		foreach($arrReturnData as $temp){
			
			$db->query("update {$databaseidExtend}".DATATABLE."_order_returninfo set ReturnClient =".( !empty($resultClient[$temp['ReturnClient']]) ? $resultClient[$temp['ReturnClient']] :'0' )." where ReturnID = {$temp['ReturnID']} ");

			// 拷贝退货明细数据
			// ClientID 需要更新
			// ContentID 需要更新
		    $upsql = "insert into {$databaseidExtend}".DATATABLE."_order_cart_return
		(ReturnID,CompanyID,ClientID,ContentID,
		ContentName,ContentColor,ContentSpecification,
		ContentPrice,ContentNumber)

			select
			{$temp['ReturnID']},{$insert_id},ClientID,ContentID,
		ContentName,ContentColor,ContentSpecification,
		ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return
			 where ReturnID = {$temp['OldID']}
			 ";
			$db->query($upsql);

			// 拷贝退货单备份
		   $upsql = "insert into {$databaseidExtend}".DATATABLE."_order_return_cartbak
		(CompanyID,OrderID,Content)

			select {$insert_id},{$temp['ReturnID']},Content from ".DATATABLE."_order_return_cartbak
			 where OrderID = {$temp['OldID']}
			 ";
			$db->query($upsql);
		}

		// 更新
		$arrCartReturnData = $db->get_results("select ID,ClientID,ContentID from {$databaseidExtend}".DATATABLE."_order_cart_return where CompanyID = {$insert_id} ");
		if(is_array($arrCartReturnData)){
			foreach($arrCartReturnData as $temp){
				$db->query("update {$databaseidExtend}".DATATABLE."_order_cart_return set ClientID =".( !empty($resultClient[$temp['ClientID']]) ? $resultClient[$temp['ClientID']] :'0' )." ,ContentID = ".( !empty($resultProduct[$temp['ContentID']]) ? $resultProduct[$temp['ContentID']] :'0' )." where ID = {$temp['ID']} ");
			}
		}
	}

	// 拷贝广告

	$upsql = "insert into {$databaseidExtend}".DATATABLE."_order_xd
(ArticleCompany,ArticleSort,ArticleName,
ArticlePicture,ArticleLink,ArticleContent,ArticleDate,
ArticleUser,ArticleFlag,ArticleOrder,ArticleCount)

	select 
	{$insert_id},ArticleSort,ArticleName,
ArticlePicture,ArticleLink,ArticleContent,".time().",
'{$sUserName}',ArticleFlag,ArticleOrder,ArticleCount
	from ".DATATABLE."_order_xd
	 where ArticleCompany = {$nCompanyID}
	 ";
	$db->query($upsql);

	// 更新
	$arrXdData = $db->get_results("select ArticleID,ArticleLink from {$databaseidExtend}".DATATABLE."_order_xd where ArticleCompany = {$insert_id} ");
	if(is_array($arrXdData)){
		foreach($arrXdData as $temp){
			$sLink = trim($temp['ArticleLink']);
			if($sLink){
				$sLink = intval(str_replace('http://tdh.dhb.net.cn/content.php?id=','',$sLink));
				if(!empty($resultProduct[$sLink])){
					$sLink = 'http://tdh.dhb.net.cn/content.php?id='.$resultProduct[$sLink];
				}else{
					$sLink = '';
				}
			}

			$db->query("update {$databaseidExtend}".DATATABLE."_order_xd set ArticleLink = '{$sLink}' where ArticleID = {$temp['ArticleID']} ");
		}
	}

	return true;
}

function makeRandString($nLength=10){
	if($nLength<1) $nLength = 1;
	
	$arrRand = array_merge(range(0,9),range('a','z'),range('A','Z'));
	shuffle($arrRand);
	$sResult =  implode('',array_slice($arrRand,0,$nLength));
	return $sResult;
}

function resultMessage($sMessage,$result=false){
	global $arrReturnResult;
	$arrReturnResult['message'] = $sMessage.' ('.(isset($_REQUEST['index'])?$_REQUEST['index']:'').')';
	if($result===true){
		$arrReturnResult['status'] = 101;
	}
	
	if(isset($_REQUEST['over']) && $_REQUEST['over']=='11'){
		$arrReturnResult['over'] = '11';
	}else{
		$arrReturnResult['over'] = '10';
	}

	exit(json_encode($arrReturnResult));
}

/********** Function ************/
function ChangeMsg($msgu,$msgp)
{
	if(!empty($msgu) && !empty($msgp))
	{
		$delmsg = md5($msgu);
		$rname  = substr($delmsg,5,1).",".substr($delmsg,7,1).",".substr($delmsg,15,1).",".substr($delmsg,17,1);
		$rnamearray = explode(',',$rname);
		$rpass  = md5($msgp);
		$r_msg = str_replace($rnamearray, "", $rpass);
	}else{
		$r_msg = $msgp;
	}
	return $r_msg;
}

makeNewCompany($in['CompanyIndustry']);

resultMessage('执行成功',true);

?>