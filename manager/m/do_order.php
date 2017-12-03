<?php
$menu_flag = "order";
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

/*****************************/
if($in['m'] == 'notify_client') {
    $order_ids = $in['order_ids'];
    $company_id = $_SESSION['uinfo']['ucompany'];
    $list = $db->get_results("SELECT o.OrderID,o.OrderSN,c.ClientName,c.ClientCompanyName,c.clientTrueName,c.ClientMobile,c.ClientID FROM ".DATATABLE."_order_orderinfo AS o LEFT JOIN ".DATATABLE."_order_client AS c ON c.ClientID = o.OrderUserID WHERE o.OrderCompany={$company_id} AND c.ClientCompany={$company_id} AND INSTR(',{$order_ids},',CONCAT(',',o.OrderID,','))");
    //XXXX,我们已收到您的订单XXXX-XX，会尽快为您安排发货。感谢您在(m.dhb.hk)购物;
    $err = array();
    $succ = array();
    if($_SESSION['uc']['SmsNumber'] < 1){
    	$result = array(
            'status'  => 'error',
            'success' => '0,0',
            'message' => '短信用完了，请充值'
        );
    }else{

   	if(!empty($list)) {
        $prefix = $_SESSION['uc']['CompanyPrefix'];
        $prefix = empty($prefix) ? 'c' : $prefix;
        foreach($list as $info) {
        	if(empty($_SESSION['uc']['SmsNumber']) || $_SESSION['uc']['SmsNumber'] < 1){
        		break;
        	}

            if(empty($info['ClientMobile'])) {
                continue;
            }

            $msg = "【".$_SESSION['uc']['CompanySigned']."】".$info['ClientCompanyName'] . ',我们已收到您的订单' . $info['OrderSN'] . ',会尽快为您安排发货。感谢您在(http://'.$prefix.'.dhb.hk)购物';
            $send_rst = sms::send_sms($info['ClientMobile'],$msg,$info['ClientID']);
            if($send_rst !== '0') {
                $err[] = $info['ClientCompanyName'] . '订单' . $info['OrderSN'] . '通知失败';
            }
            else 
            {
                //短信发送成功后修改订单发送记录值
                $succ[] = $info['OrderID'];
                $upsql = "UPDATE ".DATATABLE."_order_orderinfo set SMSNotified='T' where OrderID = ".$info['OrderID']." and OrderCompany={$company_id}";
                $db->query($upsql);
            }
        }
    }
    if($err) {
        $result = array(
            'status' => 'error',
            'success' => implode("," , $succ),
            'message' => implode("<br/>" , $err),
        );
    } else {
        $result = array(
            'status' => 'ok',
            'success' => implode("," , $succ)
        );
    }
	}

    exit (json_encode($result));
} 
else if($in['m']=="Audit")
{
	if(!intval($in['ID'])) exit('error');
	$in['ID'] = intval($in['ID']);
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
    $company_id = $_SESSION['uinfo']['ucompany'];
    $erp_is_run = erp_is_run($db,$company_id);
    $client_id = $db->get_var("SELECT OrderUserID FROM ".DATATABLE."_order_orderinfo WHERE OrderCompany=" . $company_id . " AND OrderID=" . $in['ID'] . " LIMIT 1");
    $client_erp = $db->get_var("SELECT ERP FROM ".DATATABLE."_order_client WHERE ClientID=" . $client_id . " AND ClientCompany=" . $company_id);

    if((empty($client_erp) || $client_erp == 'F' ) && $erp_is_run) {
        exit("提示：ERP里面没有此药店,请先在ERP中维护药店并同步!");
    }
    
    //判断订单有没有商品 addby lxc
    $goodsCNT = $db->get_var("SELECT COUNT(*) AS CNT FROM ".DATATABLE."_order_cart WHERE CompanyID=" . $company_id . " AND OrderID=" . $in['ID']);
    //加上赠品判断 addby tubo
    $giftsCNT = $db->get_var("SELECT COUNT(*) AS CNT FROM ".DATATABLE."_order_cart_gifts WHERE CompanyID=" . $company_id . " AND OrderID=" . $in['ID']);
    if(($goodsCNT == 0 || empty($goodsCNT))&&($giftsCNT == 0 || empty($giftsCNT))) {
    	exit("提示：此订单还未添加商品，请添加后再审核!");
    }
    
	$upsql = "update ".DATATABLE."_order_orderinfo set OrderStatus=1,OrderSendStatus=1 where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=0 ";
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '审核订单，对接ERP', '".$in['Content']."')";
		$db->query($sqlin);

		$loinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderTotal FROM ".DATATABLE."_order_orderinfo where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");

		chang_point($db,$in,$loinfo,"jia");

		$message = "【".$_SESSION['uc']['CompanySigned']."】您提交的订单(".$loinfo['OrderSN'].")已通过审核,如需了解最新订单状态，请登录医统天下系统。退订回复TD";

		sms::get_setsms("8",$loinfo['OrderUserID'],$message);

		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="UnAudit")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql = "update ".DATATABLE."_order_orderinfo set OrderStatus=0,OrderSendStatus=0 where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=1 ";	
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '反审核', '".$in['Content']."')";
		$db->query($sqlin);
		
		$loinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderTotal FROM ".DATATABLE."_order_orderinfo where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
		chang_point($db,$in,$loinfo,"jian");

		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
} else if($in['m'] == 'Special') {
    //特价
    //再次验证是否有权限 && 订单允许特价
    $order_id = $in['order_id'];
    $company_id = $_SESSION['uinfo']['ucompany'];
    $special_amount = (float)$in['amount'];//特价金额
    if(empty($in['order_id'])) {
        exit("参数错误!");
    }
    $order_info = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo WHERE OrderCompany={$company_id} AND OrderID=" . $order_id);
    if(empty($order_info)) {
        exit("订单不存在,请刷新重试!");
    }
    if(!is_allow_access($menu_flag,array('pope_view','pope_form','pope_audit'))) {
        exit("对不起,您没有此项操作权限!");
    }
    if(!in_array($order_info['OrderStatus'],array(0,1))) { // 只有待审核、备货中允许设置特价
        exit("当前订单不允许设置特价!");
    }

    /*
    if($special_amount < $order_info['OrderIntegral']) {
        exit("特价金额不能小于当前已付金额!");
    }
    */

    $adminName = $_SESSION['uinfo']['usertruename'];
    $adminUser = $_SESSION['uinfo']['username'];
    $title = "设置特价";
    $content = "将订单金额" . $order_info['OrderTotal'] . ',设置为:' . number_format($special_amount,2);

    //将订单价格置于特价 并　添加特价标记
    $special_sql = "UPDATE ".DATATABLE."_order_orderinfo SET OrderTotal={$special_amount},OrderSpecial='T' WHERE OrderCompany={$company_id} AND OrderID={$order_id}";
    if(false === $db->query($special_sql)) {
        exit('特价订单设置失败,请重置!' );
    }
    //订单操作日志
    $order_log_sql = "INSERT INTO ".DATATABLE."_order_ordersubmit (CompanyID,OrderID,AdminUser,Name,Date,Status,Content) VALUES ({$company_id},{$order_info['OrderID']},'{$adminUser}','{$adminName}',".time().",'{$title}','{$content}')";
    $db->query($order_log_sql);
    //更新为特价后验证订单是否已支付完成

    $integral = $order_info['OrderIntegral'];//原先的已付款金额
    $status = $order_info['OrderPayStatus'];//原先的订单状态
    if($order_info['OrderPayStatus'] == 2 || $order_info['OrderPayStatus'] == 3) { //已付款、预付款
        if($special_amount <= $order_info['OrderIntegral']) {
            $integral = $special_amount;
            $status = 2;
        } else {
            $status = 3;
        }
    }
    //根据订单金额、已支付金额判断改为特价后的订单支付状态
    $db->query("UPDATE ".DATATABLE."_order_orderinfo SET OrderPayStatus={$status},OrderIntegral={$integral} WHERE OrderCompany={$company_id} AND OrderID={$order_info['OrderID']}");

    exit('ok');
}
elseif($in['m']=="Send")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['consignment']['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	$upinfo  = $db->get_row("SELECT OrderID,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");

	//检查库存	
	$upsql   =  "update ".DATATABLE."_order_orderinfo set OrderSendStatus=2 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ";
	$upsql2 =  "update ".DATATABLE."_order_orderinfo set OrderStatus=2 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=1";	
	$db->query($upsql2);
	if($db->query($upsql))
	{
		if(empty($upinfo['OrderSendStatus']) || $upinfo['OrderSendStatus']=="1"  || $upinfo['OrderSendStatus']=="3")
		{
			chang_number($db,$in,'Send');
			$db->query("update ".DATATABLE."_order_cart set ContentSend=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			$db->query("update ".DATATABLE."_order_cart_gifts set ContentSend=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
		}
		
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$upinfo['OrderID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '已发货', '".$in['Content']."')";
		$db->query($sqlin);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Pay")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['finance']['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_orderinfo set OrderStatus=5,OrderPayStatus=2,OrderIntegral=OrderTotal where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ";	
	if($db->query($upsql))
	{
		$upinfo  = $db->get_row("SELECT OrderID,OrderSN FROM ".DATATABLE."_order_orderinfo where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
		if(!empty($upinfo['OrderSN'])) $db->query("update ".DATATABLE."_order_finance set FinanceUpDate=".time().",FinanceAdmin='".$_SESSION['uinfo']['username']."',FinanceFlag=2 where FinanceCompany=".$_SESSION['uinfo']['ucompany']."  and  FinanceOrder='".$upinfo['OrderSN']."' limit 1");

		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$upinfo['OrderID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '确认到账', '".$in['Content']."')";
		$db->query($sqlin);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Incept")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['consignment']['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	$upinfo  = $db->get_row("SELECT OrderID,OrderSN,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");

	$db->query("update ".DATATABLE."_order_consignment set ConsignmentFlag=2 where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentOrder='".$upinfo['OrderSN']."' ");
	$upsql =  "update ".DATATABLE."_order_orderinfo set OrderSendStatus=4 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ";

	if(empty($upinfo['OrderSendStatus']) || $upinfo['OrderSendStatus']=="1"  || $upinfo['OrderSendStatus']=="3")
	{		
		chang_number($db,$in,'Incept');

		$db->query("update ".DATATABLE."_order_cart set ContentSend=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
		$db->query("update ".DATATABLE."_order_cart_gifts set ContentSend=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
	}

	$upsql2 =  "update ".DATATABLE."_order_orderinfo set OrderStatus=3 where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus<3 ";
	$db->query($upsql2);
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$upinfo['OrderID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '管理员确认收货', '".$in['Content']."')";
		$db->query($sqlin);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Cancel")
{
	if(!intval($in['ID'])) exit('error');
	$in['ID'] = intval($in['ID']);
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_orderinfo set OrderStatus=9,OrderSendStatus=0,OrderPayStatus=0 where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=0 limit 1";	
	if($db->query($upsql))
	{
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
		{
			$sql    = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".DATATABLE."_order_cart where OrderID=".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			$data_c = $db->get_results($sql);
				
			$tykey = str_replace($fp,$rp,base64_encode("统一"));
			foreach($data_c as $dvar)
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");
			
				if(strlen($dvar['ContentColor']) || strlen($dvar['ContentSpecification']))
				{
					if(!strlen($dvar['ContentColor'])) {
                        $keycolor = $tykey;
                    } else {
                        $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
                    }
					if(!strlen($dvar['ContentSpecification'])) {
                        $keyspec = $tykey;
                    } else {
                        $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
                    }
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
				}
				$dnumber = intval("-".$dvar['ContentNumber']);
				$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$in['ID']},{$dnumber},'mcancel')");
			}

			//赠品
			$sqlg     = "select OrderID,ContentID,ContentColor,ContentSpecification,ContentNumber,ContentSend from ".DATATABLE."_order_cart_gifts where OrderID=".intval($in['ID'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			$data_g = $db->get_results($sqlg);
			if(!empty($data_g))
			{
				$tykey = str_replace($fp,$rp,base64_encode("统一"));
				foreach($data_g as $dvar)
				{
					$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");
				
					if(strlen($dvar['ContentColor']) || strlen($dvar['ContentSpecification']))
					{
						if(!strlen($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
						if(!strlen($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
						$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
					}
					$dnumber = intval("-".$dvar['ContentNumber']);
					$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$in['ID']},{$dnumber},'mcancel')");
				}
			}
		}		
		
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '管理员取消', '".$in['Content']."')";
		$db->query($sqlin);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Over")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upinfo  = $db->get_row("SELECT OrderID,OrderSN,OrderUserID, OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");

	$upsql =  "update ".DATATABLE."_order_orderinfo set OrderStatus=7,OrderSendStatus=4,OrderPayStatus=2,OrderIntegral=OrderTotal where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ";	
	if($db->query($upsql))
	{	
		$db->query("update ".DATATABLE."_order_consignment set ConsignmentFlag=2 where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentOrder='".$upinfo['OrderSN']."' ");

		if(empty($upinfo['OrderSendStatus']) || $upinfo['OrderSendStatus']=="1" || $upinfo['OrderSendStatus']=="3")
		{		
			chang_number($db,$in,'Over');

			$db->query("update ".DATATABLE."_order_cart set ContentSend=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			$db->query("update ".DATATABLE."_order_cart_gifts set ContentSend=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
		}

		//提成
// 		$productarr  = get_set_arr('product');
// 		if(!empty($productarr['deduct_type']) && $productarr['deduct_type']=="on")
// 		{
// 			$salerrow = $db->get_row("select SalerID from ".DATATABLE."_order_salerclient where CompanyID=".$_SESSION['uinfo']['ucompany']." and ClientID=".$upinfo['OrderUserID']." limit 0,1");
// 			if(!empty($salerrow['SalerID']))
// 			{
// 				$cartarr = $db->get_results("SELECT c.ID,c.ContentID,c.ContentPrice,c.ContentNumber,c.ContentPercent,i.Deduct FROM ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_1 i ON c.ContentID=i.ContentIndexID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$upinfo['OrderID']." ORDER BY c.ID ASC ");
// 				$alltotal = 0;
// 				if(!empty($cartarr))
// 				{	
// 					foreach($cartarr as $cv)
// 					{
// 						if(!empty($cv['Deduct']))
// 						{
// 							$ptotal  = $cv['ContentPrice'] * $cv['ContentNumber'] * ($cv['ContentPercent'] / 10);
// 							$dtotal  = $ptotal * $cv['Deduct'] / 100;
// 							if($dtotal > 0)
// 							{
// 								$alltotal = $alltotal + $dtotal;
// 								$db->query("insert into ".DATATABLE."_order_deduct_cart(CartID,CompanyID,ClientID,OrderID,ProductDeduct,ProductTotal,DeductTotal) values(".$cv['ID'].",".$_SESSION['uinfo']['ucompany'].",".$upinfo['OrderUserID'].",".$upinfo['OrderID'].",'".$cv['Deduct']."','".$ptotal."','".$dtotal."')");
// 							}
// 						}
// 					}
// 					if($alltotal > 0)
// 					{
// 						$db->query("insert into ".DATATABLE."_order_deduct(OrderID,OrderSN,CompanyID,ClientID,DeductUser,DeductTotal,DeductDate) values(".$upinfo['OrderID'].", '".$upinfo['OrderSN']."',".$_SESSION['uinfo']['ucompany'].",".$upinfo['OrderUserID'].",".$salerrow['SalerID'].",'".$alltotal."',".time().")");
// 					}
// 				}
// 			}
// 		}

		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$upinfo['OrderID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '订单已完结', '".$in['Content']."')";
		$db->query($sqlin);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Delete")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9") exit('对不起，您没有此项操作权限！');
	
	$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where  OrderID = ".$in['ID']." and OrderCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$infodatamsg = serialize($InfoData);

	$upsql =  "delete from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$InfoData['OrderID']." and (OrderStatus=8 or OrderStatus=9)";	
	if($db->query($upsql))
	{
		$sqld = "delete from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$InfoData['OrderID']."";	
		$db->query($sqld);
		$sqld = "delete from ".DATATABLE."_order_cart_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$InfoData['OrderID']."";	
		$db->query($sqld);
		$sqld = "delete from ".DATATABLE."_order_ordersubmit where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$InfoData['OrderID']."";
		$db->query($sqld);

        $sqlf = "UPDATE rsung_order_finance SET FinanceOrderID=0,FinanceOrder=0 WHERE FinanceOrderID=".$in['ID'];
        $db->query($sqlf);
        //begin 以上修改付款单的时候，快捷支付和在线支付的FinanceOrderID为0修改不到，会出现账务不对应问题 2015-9-17 by tubo
        $sqlf = "UPDATE rsung_order_finance SET FinanceOrderID=0,FinanceOrder=0 WHERE FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceClient =".$InfoData['OrderUserID']." and (FinanceOrder='".$InfoData['OrderSN']."' or FinanceOrder='".$InfoData['OrderSN'].",')"; 
        $db->query($sqlf);
		//end 备注：暂时未考虑多订单情况
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_order.php?m=Delete&ID=".$InfoData['OrderID']."','删除订单(".$InfoData['OrderID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Message")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_view'] != 'Y') exit('对不起，您没有此项操作权限！');

	if(!empty($in['Content']))
	{
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '留言', '".$in['Content']."')";
		$db->query($sqlin);
		echo 'ok';
		exit();
	}else{
		exit('请填写留言内容!');
	}
}
elseif($in['m']=="guestbook_delete")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$sqlin = "delete from ".DATATABLE."_order_ordersubmit where ID=".intval($in['ID'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 1";
	$db->query($sqlin);
	echo 'ok';
	exit();

}
elseif($in['m']=="set_invoice") //开票状态
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$sta = $db->query("update ".DATATABLE."_order_invoice set InvoiceFlag='T',InvoiceSendDate=".time()." where CompanyID=".$_SESSION['uinfo']['ucompany']." and InvoiceID='".$in['ID']."' ");
	if($sta) echo 'ok'; else echo '设置不成功';
	exit();

}
elseif($in['m']=="edit_order_product_save")
{
	if(empty($in['order_id'])) exit('参数错误!');

	$cartdata = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent
                from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");

    $orderBeforeSql = "SELECT * FROM ".DATATABLE."_order_orderinfo WHERE OrderID=".$in['order_id'].' limit 0,1';
    //修改订单前的订单信息
    $orderBefore = $db->get_row($orderBeforeSql);
    //修改订单前的订单总金额
    $beforeTotal = $orderBefore['OrderTotal'];
    //已支付金额
    $integral = $orderBefore['OrderIntegral'];

	$status = false;
	$totalorder = 0;
	$idmsg = "0";

	$tykey = str_replace($fp,$rp,base64_encode("统一"));
	$cospnumarr = null;
	$library = '保存成功!';

	foreach($cartdata as $cvar)
	{
		$cvar['kid'] = make_kid($cvar['ContentID'], $cvar['ContentColor'], $cvar['ContentSpecification']);
		$cartarr[$cvar['ID']] = $cvar;
		$idmsg  .= ",".$cvar['ContentID'];
	}

	$valuearr = get_set_arr('product');
	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
	{
		$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID in ( ".$idmsg." ) and CompanyID = ".$_SESSION['uinfo']['ucompany']." ";
		$data_all = $db->get_results($sql_l);

		$sql      = "select ContentID,ContentColor,ContentSpec,OrderNumber from ".DATATABLE."_order_inventory_number where  CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in ( ".$idmsg." )";
		$data_cs  = $db->get_results($sql);

		if(!empty($data_cs))
		{
			foreach($data_cs as $cospvar)
			{	
				$cospkey = $cospvar['ContentID'];
				if(!empty($cospvar['ContentColor']) && $cospvar['ContentColor']!=$tykey)
				{
					$cospkey .= "_p_".$cospvar['ContentColor'];
				}
				if(!empty($cospvar['ContentSpec']) && $cospvar['ContentSpec']!=$tykey)
				{
					$cospkey .= "_s_".$cospvar['ContentSpec'];
				}
				$cospnumarr[$cospkey] = $cospvar['OrderNumber'];
			}
		}
		if(!empty($data_all))
		{
			foreach($data_all as $allvar)
			{	
				$cospnumarr[$allvar['ContentID']] = $allvar['OrderNumber'];
			}
		}
	}

	for($i=0;$i<count($in['cart_id']);$i++)
	{
		$inkid  = $cartarr[$in['cart_id'][$i]]['kid'];
		$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
		$sqlmsg = "";

		if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']))
		{
			$ckeycolor = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentColor']));
		}else{
			$ckeycolor = $tykey;
		}
		if(!empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
		{
			$ckeyspec = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentSpecification']));
		}else{
			$ckeyspec = $tykey;
		}

		$delid  = "cart_del_".$in['cart_id'][$i];
		if(empty($in['cart_num'][$i]) || !empty($in[$delid]))
		{
			$db->query("delete from ".DATATABLE."_order_cart
                        where CompanyID=".$_SESSION['uinfo']['ucompany']."
                        and OrderID=".$in['order_id']." and ID=".$in['cart_id'][$i] );
			$status = true;
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." " );
				
				if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
				{
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
				}
				$dnumber = intval("-".$cartarr[$in['cart_id'][$i]]['ContentNumber']);
				$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mdel')");
			}
		}else{

			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				if($in['cart_num'][$i] < $cartarr[$in['cart_id'][$i]]['ContentNumber'])
				{
					$chanum  = $cartarr[$in['cart_id'][$i]]['ContentNumber']-$in['cart_num'][$i];
					$sqlmsg .= " OrderNumber = OrderNumber+".$chanum." ";	
					$dnumber = intval("-".$chanum);
				}
				elseif($in['cart_num'][$i] > $cartarr[$in['cart_id'][$i]]['ContentNumber'])
				{					
					$chanum  = $in['cart_num'][$i]-$cartarr[$in['cart_id'][$i]]['ContentNumber'];
					if((!empty($valuearr['product_negative']) && $valuearr['product_negative']=="on") || $chanum <= $cospnumarr[$inkid])
					{
						$sqlmsg .= " OrderNumber = OrderNumber-".$chanum." ";
						$dnumber = intval($chanum);
					}else{
						$library = '商品：”'.$cartarr[$in['cart_id'][$i]]['ContentName'].'('.$cartarr[$in['cart_id'][$i]]['ContentColor'].'/'.$cartarr[$in['cart_id'][$i]]['ContentSpecification'].')“ 库存数量不够!';
						echo $library;
						exit();
					}
				}
				if(!empty($sqlmsg))
				{
					$status = true;
					$db->query("update ".DATATABLE."_order_number set ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." limit 1");					
					if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
					{
						$db->query("update ".DATATABLE."_order_inventory_number set  ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
					}					
					$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mchange')");
				}
			}
			if($in['cart_price'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentPrice'] || $in['cart_num'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentNumber'] || $in['ContentPercent'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentPercent'])
			{
				$sql = "update ".DATATABLE."_order_cart set ContentPrice='".$in['cart_price'][$i]."',ContentNumber=".$in['cart_num'][$i].",ContentPercent='".$in['cart_percent'][$i]."' where ID=".$in['cart_id'][$i]." and OrderID=".$in['order_id']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
				$isupcart = $db->query($sql);
				$status = true;
			}
	   }
	}

	if($status)
	{
		$cartdatat = $db->get_results("select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");
		$totalorder = 0;
		foreach($cartdatat as $tvar)
		{
			$totalorder = $totalorder + ($tvar['ContentPrice'] * $tvar['ContentNumber'] * $tvar['ContentPercent'] * 0.1);
		}

        $totalPure = $totalorder;//纯订单金额
        $stair_count = get_stair(round($totalPure));//当前订单能优惠的金额
        $totalorder = $totalorder - $stair_count;//优惠后订单金额


		$oinfo = $db->get_row("select OrderID,InvoiceType,InvoiceTax from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." limit 0,1");
		if($oinfo['InvoiceType'] != 'N' && !empty($oinfo['InvoiceTax'])){
			$totalorder = $totalorder + $totalorder * $oinfo['InvoiceTax'] / 100;
		}

        //订单更改后已付金额初始化为更改前已付金额
        $integral = round($integral,2);
        $totalorder = round($totalorder,2);

        $afterIntegral = $integral;
        $afterPayStatus = $orderBefore['OrderPayStatus'];
        if( $integral >= $totalorder){
            //已支付金额大于等于订单金额
            //by zjb 20161031  因为订单已经和ERP对接，所以允许订单修改，清空所有商品
            if($totalorder>=0){
                $afterPayStatus = 2;//已付款
                $afterIntegral = $totalorder;
            }
        }elseif( $integral < $totalorder && $integral >= 0){
            //已支付金额小于订单金额
			/** 2015-04-08 modify seekfor***/
			$finfo = $db->get_row("select FinanceTotal from ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." and FinanceOrderID = ".$in['order_id']." order by FinanceID desc limit 0,1 ");
			if(!empty($finfo['FinanceTotal']) && $integral < $finfo['FinanceTotal']){
				if($totalorder > $finfo['FinanceTotal']){
					$afterIntegral	= $finfo['FinanceTotal'];
					$afterPayStatus = 3;//预付款
				}else{
					$afterIntegral	= $totalorder;
					$afterPayStatus = 2;//已付款
				}
			}else{
				$afterPayStatus = 3;//预付款
				$afterIntegral  = $integral;
			}
           
        }else{
            $afterPayStatus = 0;//未付款
        }
        $orderSpecial = 'F';
        if($stair_count > 0) {
            $orderSpecial = 'T';
        }

        if($in['t']=="back")
        {
            $submit_content = '--';
            if($stair_count > 0) {
                $stair_amount = get_stair($totalPure , 'amount');
                $submit_content = '订单满 ¥' . $stair_amount . ' 省 ¥' . $stair_count . " ，金额： ¥" . $totalorder;
            }
            $sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['order_id'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '修改订单商品', '{$submit_content}')";
            $db->query($sqlin);
        }

		$db->query("update ".DATATABLE."_order_orderinfo set OrderSpecial='{$orderSpecial}', OrderPayStatus='".$afterPayStatus."',OrderIntegral='".$afterIntegral."', OrderTotal='".$totalorder."'
		        where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']);
		
		//修改订单通知相应客情官  2015-12-16
		$clientinfo = $db->get_row("select ClientCompanyName from ".DATATABLE."_order_client
		        where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".$orderBefore['OrderUserID']." limit 0,1");
		
		$msg = "【".$_SESSION['uc']['CompanySigned']."】您有一个订单:NO.{$orderBefore['OrderSN']},来自:".$clientinfo['ClientCompanyName'].",金额为:".$totalorder." 元已被管理员修改,请尽快登录医统天下系统处理。退订回复TD";
		sms::get_setsms("9",$orderBefore['OrderUserID'],$msg);
	}
	echo $library;
	exit();

}
elseif($in['m']=="checkoff_order_product_save")
{
	if(empty($in['order_id'])) exit('参数错误!');

	$cartdata = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent
                from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");

    $orderBeforeSql = "SELECT * FROM ".DATATABLE."_order_orderinfo WHERE OrderID=".$in['order_id'].' limit 0,1';
    //修改订单前的订单信息
    $orderBefore = $db->get_row($orderBeforeSql);
    //修改订单前的订单总金额
    $beforeTotal = $orderBefore['OrderTotal'];
    //已支付金额
    $integral = $orderBefore['OrderIntegral'];
    
	$status = false;
	$totalorder = 0;
	$idmsg = "0";

	$tykey = str_replace($fp,$rp,base64_encode("统一"));
	$cospnumarr = null;
	$library = '保存成功!';

	foreach($cartdata as $cvar)
	{
		$cvar['kid'] = make_kid($cvar['ContentID'], $cvar['ContentColor'], $cvar['ContentSpecification']);
		$cartarr[$cvar['ID']] = $cvar;
		$idmsg  .= ",".$cvar['ContentID'];
	}

	$valuearr = get_set_arr('product');
	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
	{
		$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID in ( ".$idmsg." ) and CompanyID = ".$_SESSION['uinfo']['ucompany']." ";
		$data_all = $db->get_results($sql_l);

		$sql      = "select ContentID,ContentColor,ContentSpec,OrderNumber from ".DATATABLE."_order_inventory_number where  CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in ( ".$idmsg." )";
		$data_cs  = $db->get_results($sql);

		if(!empty($data_cs))
		{
			foreach($data_cs as $cospvar)
			{	
				$cospkey = $cospvar['ContentID'];
				if(!empty($cospvar['ContentColor']) && $cospvar['ContentColor']!=$tykey)
				{
					$cospkey .= "_p_".$cospvar['ContentColor'];
				}
				if(!empty($cospvar['ContentSpec']) && $cospvar['ContentSpec']!=$tykey)
				{
					$cospkey .= "_s_".$cospvar['ContentSpec'];
				}
				$cospnumarr[$cospkey] = $cospvar['OrderNumber'];
			}
		}
		if(!empty($data_all))
		{
			foreach($data_all as $allvar)
			{	
				$cospnumarr[$allvar['ContentID']] = $allvar['OrderNumber'];
			}
		}
	}

	for($i=0;$i<count($in['cart_id']);$i++)
	{
		$inkid  = $cartarr[$in['cart_id'][$i]]['kid'];
		$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
		$sqlmsg = "";

		if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']))
		{
			$ckeycolor = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentColor']));
		}else{
			$ckeycolor = $tykey;
		}
		if(!empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
		{
			$ckeyspec = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentSpecification']));
		}else{
			$ckeyspec = $tykey;
		}

		$delid  = "cart_del_".$in['cart_id'][$i];
		if(empty($in['cart_num'][$i]) || !empty($in[$delid]))
		{
			$db->query("delete from ".DATATABLE."_order_cart
                        where CompanyID=".$_SESSION['uinfo']['ucompany']."
                        and OrderID=".$in['order_id']." and ID=".$in['cart_id'][$i] );
			$status = true;
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." " );
				
				if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
				{
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
				}
				$dnumber = intval("-".$cartarr[$in['cart_id'][$i]]['ContentNumber']);
				$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mdel')");
			}
		}else{

			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				if($in['cart_num'][$i] < $cartarr[$in['cart_id'][$i]]['ContentNumber'])
				{
					$chanum  = $cartarr[$in['cart_id'][$i]]['ContentNumber']-$in['cart_num'][$i];
					$sqlmsg .= " OrderNumber = OrderNumber+".$chanum." ";	
					$dnumber = intval("-".$chanum);
				}
				elseif($in['cart_num'][$i] > $cartarr[$in['cart_id'][$i]]['ContentNumber'])
				{					
					$chanum  = $in['cart_num'][$i]-$cartarr[$in['cart_id'][$i]]['ContentNumber'];
					if((!empty($valuearr['product_negative']) && $valuearr['product_negative']=="on") || $chanum <= $cospnumarr[$inkid])
					{
						$sqlmsg .= " OrderNumber = OrderNumber-".$chanum." ";
						$dnumber = intval($chanum);
					}else{
						$library = '商品：”'.$cartarr[$in['cart_id'][$i]]['ContentName'].'('.$cartarr[$in['cart_id'][$i]]['ContentColor'].'/'.$cartarr[$in['cart_id'][$i]]['ContentSpecification'].')“ 库存数量不够!';
						echo $library;
						exit();
					}
				}
				if(!empty($sqlmsg))
				{
					$status = true;
					$db->query("update ".DATATABLE."_order_number set ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." limit 1");					
					if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
					{
						$db->query("update ".DATATABLE."_order_inventory_number set  ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
					}					
					$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mchange')");
				}
			}
			if($in['cart_price'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentPrice'] || $in['cart_num'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentNumber'] || $in['ContentPercent'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentPercent'])
			{
				$sql = "update ".DATATABLE."_order_cart set ContentPrice='".$in['cart_price'][$i]."',ContentNumber=".$in['cart_num'][$i].",ContentPercent='".$in['cart_percent'][$i]."' where ID=".$in['cart_id'][$i]." and OrderID=".$in['order_id']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
				$isupcart = $db->query($sql);
				$status = true;
			}
	   }
	}

	if($status)
	{
		if($in['t']=="back")
		{
			$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['order_id'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '订单核准！', '--')";
			$db->query($sqlin);
		}

		$cartdatat = $db->get_results("select ContentPrice,ContentNumber,ContentPercent,ContentSend from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");
		$totalorder = 0;
		$afterSendStatus  = 2;//已发货
		foreach($cartdatat as $tvar)
		{
			$totalorder = $totalorder + ($tvar['ContentPrice'] * $tvar['ContentNumber'] * $tvar['ContentPercent'] * 0.1);
			
			if($tvar['ContentNumber'] != $tvar['ContentSend']){
				$afterSendStatus  = 3;//未发完
			}
		}
		
		$oinfo = $db->get_row("select OrderID,InvoiceType,InvoiceTax from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." limit 0,1");
		if($oinfo['InvoiceType'] != 'N' && !empty($oinfo['InvoiceTax'])){
			$totalorder = $totalorder + $totalorder * $oinfo['InvoiceTax'] / 100;
		}

        //订单更改后已付金额初始化为更改前已付金额
        $integral = round($integral,2);
        $totalorder = round($totalorder,2);
        $afterIntegral = $integral;
        $afterPayStatus = $orderBefore['OrderPayStatus'];
        
        if( $integral>=$totalorder){
            //已支付金额大于等于订单金额
            if($totalorder>0){
                $afterPayStatus = 2;//已付款
                $afterIntegral = $totalorder;
            }
        }elseif( $integral < $totalorder && $integral > 0){
            //已支付金额小于订单金额
			/** 2015-04-08 modify seekfor***/
			$finfo = $db->get_row("select FinanceTotal from ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." and FinanceOrderID = ".$in['order_id']." order by FinanceID desc limit 0,1 ");
			
			if($integral < $finfo['FinanceTotal']){
				if($totalorder > $finfo['FinanceTotal']){
					$afterIntegral	= $finfo['FinanceTotal'];
					$afterPayStatus = 3;//预付款
				}else{
					$afterIntegral	= $totalorder;
					$afterPayStatus = 2;//已付款
				}
			}else{
				$afterPayStatus = 3;//预付款
				$afterIntegral  = $integral;
			}
           
        }else{
            $afterPayStatus = 0;//未付款
        }

		$db->query("update ".DATATABLE."_order_orderinfo 
										set 
											OrderPayStatus='".$afterPayStatus."',
											OrderSendStatus='".$afterSendStatus."',
											OrderIntegral='".$afterIntegral."',
						 					OrderTotal='".$totalorder."',
						 					OrderSpecial='F'
		        						where 
											OrderCompany=".$_SESSION['uinfo']['ucompany']." 
											and OrderID=".$in['order_id']
				);
	}
	echo $library;
	exit();

}
elseif($in['m']=="new_edit_order_product_save")
{
	if(empty($in['order_id'])) exit('参数错误!');
    $orderApi = $db->get_var("SELECT OrderApi,OrderSN,OrderUserID FROM ".DATATABLE."_order_orderinfo WHERE OrderID=".$in['order_id']);
    $erparr = get_set_arr('erp');
    if($orderApi=='T' && $erparr['erp_interface']=='Y'){
        exit("订单信息已传至ERP处理,禁止修改订单商品信息!");
    }
    if($erparr['erp_interface']=='Y'){
        $db->query("update ".DATATABLE."_order_orderinfo set OrderStatus=0 where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']);
    }

	$cartdata = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");

	$status       = false;
	$totalorder = 0;
	$idmsg       = "0";

	$tykey = str_replace($fp,$rp,base64_encode("统一"));
	$cospnumarr = null;
	$library = '保存成功!';

	foreach($cartdata as $cvar)
	{
		$cvar['kid'] = make_kid($cvar['ContentID'], $cvar['ContentColor'], $cvar['ContentSpecification']);
		$cartarr[$cvar['ID']] = $cvar;
		$idmsg  .= ",".$cvar['ContentID'];
	}

	$valuearr = get_set_arr('product');
	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
	{
		$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID in ( ".$idmsg." ) and CompanyID = ".$_SESSION['uinfo']['ucompany']." ";
		$data_all = $db->get_results($sql_l);

		$sql      = "select ContentID,ContentColor,ContentSpec,OrderNumber from ".DATATABLE."_order_inventory_number where  CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in ( ".$idmsg." )";
		$data_cs  = $db->get_results($sql);

		if(!empty($data_cs))
		{
			foreach($data_cs as $cospvar)
			{	
				$cospkey = $cospvar['ContentID'];
				if(!empty($cospvar['ContentColor']) && $cospvar['ContentColor']!=$tykey)
				{
					$cospkey .= "_p_".$cospvar['ContentColor'];
				}
				if(!empty($cospvar['ContentSpec']) && $cospvar['ContentSpec']!=$tykey)
				{
					$cospkey .= "_s_".$cospvar['ContentSpec'];
				}
				$cospnumarr[$cospkey] = $cospvar['OrderNumber'];
			}
		}
		if(!empty($data_all))
		{
			foreach($data_all as $allvar)
			{	
				$cospnumarr[$allvar['ContentID']] = $allvar['OrderNumber'];
			}
		}
	}

	for($i=0;$i<count($in['cart_id']);$i++)
	{
		$inkid  = $cartarr[$in['cart_id'][$i]]['kid'];
		$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
		$sqlmsg = "";

		if(strlen($cartarr[$in['cart_id'][$i]]['ContentColor']))
		{
			$ckeycolor = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentColor']));
		}else{
			$ckeycolor = $tykey;
		}
		if(strlen($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
		{
			$ckeyspec = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentSpecification']));
		}else{
			$ckeyspec = $tykey;
		}

		$delid  = "cart_del_".$in['cart_id'][$i];
		if(empty($in['cart_num'][$i]) || !empty($in[$delid]))
		{
			$db->query("delete from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." and ID=".$in['cart_id'][$i] );
			$status = true;
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." " );
				
				if(strlen($cartarr[$in['cart_id'][$i]]['ContentColor']) || strlen($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
				{					
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
				}
				$dnumber = intval("-".$cartarr[$in['cart_id'][$i]]['ContentNumber']);
				$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mndel')");
			}
		}else{

			if($in['cart_num'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentNumber'])
			{
				if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
				{
					if($in['cart_num'][$i] < $cartarr[$in['cart_id'][$i]]['ContentNumber'])
					{
						$chanum  = $cartarr[$in['cart_id'][$i]]['ContentNumber']-$in['cart_num'][$i];
						$sqlmsg .= " OrderNumber = OrderNumber+".$chanum." ";
						$dnumber = intval("-".$chanum);
					}
					elseif($in['cart_num'][$i] > $cartarr[$in['cart_id'][$i]]['ContentNumber'])
					{					
						$chanum  = $in['cart_num'][$i]-$cartarr[$in['cart_id'][$i]]['ContentNumber'];
						$dnumber = intval($chanum);

						if((!empty($valuearr['product_negative']) && $valuearr['product_negative']=="on") || $chanum <= $cospnumarr[$inkid])
						{
							$sqlmsg .= " OrderNumber = OrderNumber-".$chanum." ";
						}else{
							$library = '商品：”'.$cartarr[$in['cart_id'][$i]]['ContentName'].'('.$cartarr[$in['cart_id'][$i]]['ContentColor'].'/'.$cartarr[$in['cart_id'][$i]]['ContentSpecification'].')“ 库存数量不够!';
							echo $library;
							exit();
						}
					}
					if(!empty($sqlmsg))
					{
						$status = true;
						$db->query("update ".DATATABLE."_order_number set ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." limit 1");					
						if(strlen($cartarr[$in['cart_id'][$i]]['ContentColor']) || strlen($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
						{
							$db->query("update ".DATATABLE."_order_inventory_number set  ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
						}
						$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mnchange')");
					}
				}
				$sql = "update ".DATATABLE."_order_cart set ContentNumber=".$in['cart_num'][$i]." where ID=".$in['cart_id'][$i]." and OrderID=".$in['order_id']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
				$isupcart = $db->query($sql);
				$status = true;
			}
	   }
	}

	if($status)
	{
		if($in['t']=="back")
		{
			$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['order_id'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '管理端下单', '--')";
			$db->query($sqlin);
		}

		$cartdatat = $db->get_results("select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");
		$totalorder = 0;
		foreach($cartdatat as $tvar)
		{
			$totalorder = $totalorder + ($tvar['ContentPrice'] * $tvar['ContentNumber'] * $tvar['ContentPercent'] * 0.1);
		}

        $orderSpecial = 'F';
        $stair_count = get_stair($totalorder);
        $totalorder = $totalorder - $stair_count;
        if($stair_count > 0) {
            $orderSpecial = 'T';
        }

		$db->query("update ".DATATABLE."_order_orderinfo set OrderSpecial='{$orderSpecial}', OrderTotal='".$totalorder."' where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']);

	}

	echo $library;
	exit();

}
elseif($in['m']=="order_gifts_product_save")
{
	if(empty($in['order_id'])) exit('参数错误!');

    // 验证接口取数据状态
    $order = $db->get_row("SELECT OrderApi FROM ".DATATABLE."_order_orderinfo WHERE OrderID=".$in['order_id']);
    $erpArr = get_set_arr('erp');
    if($erpArr['erp_interface']=='Y' && $order['OrderApi']=='T'){
        exit("订单信息已传至ERP禁止修改赠品信息!");
    }

	$cartdata = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");

	$status = false;
	$totalorder = 0;
	$idmsg = "0";

	$tykey = str_replace($fp,$rp,base64_encode("统一"));
	$cospnumarr = null;
	$library = '保存成功!';

	foreach($cartdata as $cvar)
	{
		$cvar['kid'] = make_kid($cvar['ContentID'], $cvar['ContentColor'], $cvar['ContentSpecification']);
		$cartarr[$cvar['ID']] = $cvar;
		$idmsg  .= ",".$cvar['ContentID'];
	}

	$valuearr = get_set_arr('product');
	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
	{
		$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID in ( ".$idmsg." ) and CompanyID = ".$_SESSION['uinfo']['ucompany']." ";
		$data_all = $db->get_results($sql_l);

		$sql      = "select ContentID,ContentColor,ContentSpec,OrderNumber from ".DATATABLE."_order_inventory_number where  CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in ( ".$idmsg." )";
		$data_cs  = $db->get_results($sql);

		if(!empty($data_cs))
		{
			foreach($data_cs as $cospvar)
			{	
				$cospkey = $cospvar['ContentID'];
				if(!empty($cospvar['ContentColor']) && $cospvar['ContentColor']!=$tykey)
				{
					$cospkey .= "_p_".$cospvar['ContentColor'];
				}
				if(!empty($cospvar['ContentSpec']) && $cospvar['ContentSpec']!=$tykey)
				{
					$cospkey .= "_s_".$cospvar['ContentSpec'];
				}
				$cospnumarr[$cospkey] = $cospvar['OrderNumber'];
			}
		}
		if(!empty($data_all))
		{
			foreach($data_all as $allvar)
			{	
				$cospnumarr[$allvar['ContentID']] = $allvar['OrderNumber'];
			}
		}
	}

	for($i=0;$i<count($in['cart_id']);$i++)
	{
		$inkid  = $cartarr[$in['cart_id'][$i]]['kid'];
		$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
		$sqlmsg = "";

		if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']))
		{
			$ckeycolor = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentColor']));
		}else{
			$ckeycolor = $tykey;
		}
		if(!empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
		{
			$ckeyspec = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentSpecification']));
		}else{
			$ckeyspec = $tykey;
		}

		$delid  = "cart_del_".$in['cart_id'][$i];
		if(empty($in['cart_num'][$i]) || !empty($in[$delid]))
		{
			$db->query("delete from ".DATATABLE."_order_cart_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." and ID=".$in['cart_id'][$i] );
			$status = true;
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." " );
				
				if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
				{					
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$cartarr[$in['cart_id'][$i]]['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
				}
				$dnumber = intval("-".$cartarr[$in['cart_id'][$i]]['ContentNumber']);
				$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mgdel')");
			}
		}else{

			if($in['cart_num'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentNumber'] || $in['cart_price'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentPrice'])
			{
				if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
				{
					if($in['cart_num'][$i] < $cartarr[$in['cart_id'][$i]]['ContentNumber'])
					{
						$chanum  = $cartarr[$in['cart_id'][$i]]['ContentNumber']-$in['cart_num'][$i];
						$sqlmsg .= " OrderNumber = OrderNumber+".$chanum." ";
						$dnumber = intval("-".$chanum);
					}
					elseif($in['cart_num'][$i] > $cartarr[$in['cart_id'][$i]]['ContentNumber'])
					{					
						$chanum  = $in['cart_num'][$i]-$cartarr[$in['cart_id'][$i]]['ContentNumber'];
						$dnumber = intval($chanum);
						if((!empty($valuearr['product_negative']) && $valuearr['product_negative']=="on") || $chanum <= $cospnumarr[$inkid])
						{
							$sqlmsg .= " OrderNumber = OrderNumber-".$chanum." ";
						}else{
							$library = '商品：”'.$cartarr[$in['cart_id'][$i]]['ContentName'].'('.$cartarr[$in['cart_id'][$i]]['ContentColor'].'/'.$cartarr[$in['cart_id'][$i]]['ContentSpecification'].')“ 库存数量不够!';
							echo $library;
							exit();
						}
					}
					if(!empty($sqlmsg))
					{
						$status = true;
						$db->query("update ".DATATABLE."_order_number set ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." limit 1");					
						if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
						{
							$db->query("update ".DATATABLE."_order_inventory_number set  ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
						}
						$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mgchange')");
					}
				}
				$sql = "update ".DATATABLE."_order_cart_gifts set ContentNumber=".$in['cart_num'][$i].",ContentPrice='".$in['cart_price'][$i]."' where ID=".$in['cart_id'][$i]." and OrderID=".$in['order_id']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
				$isupcart = $db->query($sql);
				$status = true;
			}
	   }
	}

	if($status)
	{
		if($in['t']=="back")
		{
			$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['order_id'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '赠品管理', '--')";
			$db->query($sqlin);
		}
	}
	echo $library;
	exit();
}elseif($in['m']=='save_order_base'){
    //修改订单基本资料
    $usql = "UPDATE rsung_order_orderinfo SET DeliveryDate='".$in['DeliveryDate']."',OrderSendType=".$in['OrderSendType'].",OrderPayType=".$in['OrderPayType'].", OrderReceiveAdd='".$in['OrderReceiveAdd']."',OrderReceiveCompany='".$in['OrderReceiveCompany']."',OrderReceiveName='".$in['OrderReceiveName']."',OrderReceivePhone='".$in['OrderReceivePhone']."',OrderRemark='".$in['OrderRemark']."' WHERE OrderID=".$in['OrderID'];
    $rst = $db->query($usql);
    $result = array(
        'status'=>0,
        'msg'=>'error',
    );
    if($rst){
        $result = array(
            'status'=>1,
            'msg'=>'ok'
        );
    }

    echo json_encode($result);
    exit();
}
elseif($in['m']=="addtocart")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$in['oid'] = intval($in['oid']);
	if(!intval($in['pid'])) exit('error');
	if($in['pcolor'] == 'null') echo $in['pcolor'] = '';
	if($in['pspec']  == 'null') echo $in['pspec']  = '';
	$kid = make_kid($in['pid'], $in['pcolor'], $in['pspec']);
	$total  = 0;
	$incart = null;

	$cartdata = $db->get_results("select ID,ContentID,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['oid']." order by ID asc");
	if(!empty($cartdata))
	{
		foreach($cartdata as $cvar)
		{
			$incart[] = make_kid($cvar['ContentID'], $cvar['ContentColor'], $cvar['ContentSpecification']);
			$total    = $total + $cvar['ContentPrice'] * $cvar['ContentNumber'] * $cvar['ContentPercent'] * 0.1;
		}
	}
	if(@in_array($kid, $incart))
	{
		exit("此商品已经在订单中了！");
	}else{
		$sqlnall = '';
		$sqlncs  = '';

		$pinfo = $db->get_row("SELECT ID,BrandID,CommendID,Name,Price1,Price2,Price3 FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID=".$in['pid']." limit 0,1");
		$cinfo = $db->get_row("SELECT ClientID,ClientLevel,ClientName,ClientSetPrice,ClientPercent,ClientBrandPercent FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$in['cid']." limit 0,1");
		if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
		{
			$cinfo['ClientLevel'] = "A_".$cinfo['ClientLevel'];
		}
		$cartprice = $pinfo[$cinfo['ClientSetPrice']];
		if($pinfo['CommendID']=="2")
		{
			$cinfo['ClientPercent'] = '10.0';
		}else{
			$BrandPercent = unserialize($cinfo['ClientBrandPercent']);
			if(!empty($pinfo['BrandID']) && !empty($BrandPercent[$pinfo['BrandID']]))
			{
				$cinfo['ClientPercent'] = $BrandPercent[$pinfo['BrandID']];
			}
		}
		if(empty($cinfo['ClientPercent'])) $cinfo['ClientPercent'] = '10.0';
		
		if(!empty($pinfo['Price3']))
		{
			$price3 = setprice3($pinfo['Price3'],$cinfo);
			if(!empty($price3))
			{
				$cartprice = $price3;
				$cinfo['ClientPercent'] = '10.0';
			}
		}
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
		{
			if(empty($in['pcolor']) && empty($in['pspec']))
			{
				$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1";
				$data_all = $db->get_row($sql_l);
				if(($data_all['OrderNumber']<1) && $valuearr['product_negative']!="on")
				{
					exit('该商品库存数量不够！');
				}
			}else{
				if(!empty($in['pcolor']))
				{
					$kcolor = str_replace($fp,$rp,base64_encode($in['pcolor']));
				}else{
					$kcolor = str_replace($fp,$rp,base64_encode("统一"));
				}
				if(!empty($in['pspec']))
				{
					$kspec  = str_replace($fp,$rp,base64_encode($in['pspec']));
				}else{
					$kspec  = str_replace($fp,$rp,base64_encode("统一"));
				}
				$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_inventory_number where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentColor='".$kcolor."' and ContentSpec='".$kspec."' limit 0,1";
				$data_all = $db->get_row($sql_l);
				if(($data_all['OrderNumber']<1) && $valuearr['product_negative']!="on")
				{
					exit('该商品库存数量不够！');
				}else{
					$sqlncs = "update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-1 where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentColor='".$kcolor."' and ContentSpec='".$kspec."' limit 1";
				}
			}
			$sqlnall = "update ".DATATABLE."_order_number set OrderNumber=OrderNumber-1 where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 1";

			$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['pid']},{$in['oid']},1,'morder')");

		}
		$sql = "insert into ".DATATABLE."_order_cart(OrderID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent) values(".$in['oid'].",".$_SESSION['uinfo']['ucompany'].",".$cinfo['ClientID'].",".$pinfo['ID'].",'".$pinfo['Name']."', '".$in['pcolor']."', '".$in['pspec']."', '".$cartprice."', 1,'".$cinfo['ClientPercent']."')";
		$isin = $db->query($sql);

		if($isin)
		{			
			$total = $total + $cartprice * $cinfo['ClientPercent'] * 0.1;
			$db->query("update ".DATATABLE."_order_orderinfo set OrderTotal='".$total."' where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['oid']." limit 1");
			if(!empty($sqlnall)) $db->query($sqlnall);
			if(!empty($sqlncs))  $db->query($sqlncs);
			
			echo 'ok';		
			exit();
		}else{
			exit('添加不成功!');
		}
	}
}
elseif($in['m']=="addtocart_gifts")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$in['oid'] = intval($in['oid']);
	if(!intval($in['pid'])) exit('error');
	if($in['pcolor'] == 'null') echo $in['pcolor']='';
	if($in['pspec']  == 'null') echo $in['pspec']='';
	$kid = make_kid($in['pid'], $in['pcolor'], $in['pspec']);
	$total = 0;
	$incart = null;

	$cartdata = $db->get_results("select ID,ContentID,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['oid']." order by ID asc");
	if(!empty($cartdata))
	{
		foreach($cartdata as $cvar)
		{
			$incart[] = make_kid($cvar['ContentID'], $cvar['ContentColor'], $cvar['ContentSpecification']);
			$total     = $total + $cvar['ContentPrice'] * $cvar['ContentNumber'];
		}
	}
	if(@in_array($kid, $incart))
	{
		exit("此商品已经在订单中了！");
	}else{
		$sqlnall = '';
		$sqlncs  = '';
		$pinfo = $db->get_row("SELECT ID,Name,Price1,Price2,Price3 FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID=".$in['pid']." limit 0,1");
		$cinfo = $db->get_row("SELECT ClientID,ClientLevel,ClientName,ClientSetPrice,ClientPercent FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$in['cid']." limit 0,1");
		$cartprice = $pinfo[$cinfo['ClientSetPrice']];
		
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
		{
			if(empty($in['pcolor']) && empty($in['pspec']))
			{
				$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1";
				$data_all = $db->get_row($sql_l);

				if(($data_all['OrderNumber']<1) && $valuearr['product_negative']!="on")
				{
					exit('该商品库存数量不够！');
				}
			}else{
				if(!empty($in['pcolor']))
				{
					$kcolor = str_replace($fp,$rp,base64_encode($in['pcolor']));
				}else{
					$kcolor = str_replace($fp,$rp,base64_encode("统一"));
				}
				if(!empty($in['pspec']))
				{
					$kspec  = str_replace($fp,$rp,base64_encode($in['pspec']));
				}else{
					$kspec  = str_replace($fp,$rp,base64_encode("统一"));
				}
				$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_inventory_number where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentColor='".$kcolor."' and ContentSpec='".$kspec."' limit 0,1";
				$data_all = $db->get_row($sql_l);
				if(($data_all['OrderNumber']<1) && $valuearr['product_negative']!="on")
				{
					exit('该商品库存数量不够！');
				}else{
					$sqlncs = "update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-1 where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentColor='".$kcolor."' and ContentSpec='".$kspec."' limit 1";
				}
			}
			$sqlnall = "update ".DATATABLE."_order_number set OrderNumber=OrderNumber-1 where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 1";
			$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['pid']},{$in['oid']},1,'mgorder')");
		}
		$sql = "insert into ".DATATABLE."_order_cart_gifts(OrderID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber) values(".$in['oid'].",".$_SESSION['uinfo']['ucompany'].",".$cinfo['ClientID'].",".$pinfo['ID'].",'".$pinfo['Name']."', '".$in['pcolor']."', '".$in['pspec']."', '".$cartprice."', 1)";
		$isin = $db->query($sql);

		if($isin)
		{			
			if(!empty($sqlnall)) $db->query($sqlnall);
			if(!empty($sqlncs))  $db->query($sqlncs);			
			echo 'ok';		
			exit();
		}else{
			exit('添加不成功!');
		}
	}
}
elseif($in['m']=="loadaddlist")
{
	if(!intval($in['ID'])) exit('error');

	$orderlistuser = $db->get_results("SELECT AddressID,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag FROM ".DATATABLE."_order_address where CompanyID=".$_SESSION['uinfo']['ucompany']." and AddressClient=".$in['ID']."  order by AddressID desc limit 0,50");
	$bodymsg = '';
	$headermsg = '<table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0">
                        <td width="12%">&nbsp;</td>
                        <td width="28%"><strong>&nbsp;收货人</strong></td>
                        <td ><strong>&nbsp;送货地址</strong></td>
                      </tr>';

	if(empty($orderlistuser))
	{
		$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$in['ID']." limit 0,1");

		$bodymsg = '<tr height="28" id="selected_line_'.$cinfo['ClientID'].'">
                        <td ><input id="orderadd_'.$cinfo['ClientID'].'" name="orderadd" type="radio" onfocus="set_address_value(\''.$cinfo['ClientID'].'\',\''.$cinfo['ClientCompanyName'].'\',\''.$cinfo['ClientTrueName'].'\',\''.$cinfo['ClientPhone'].'\',\''.$cinfo['ClientAdd'].'\')" value="'.$cinfo['ClientID'].'" /></td>
                        <td onclick="set_address_value(\''.$cinfo['ClientID'].'\',\''.$cinfo['ClientCompanyName'].'\',\''.$cinfo['ClientTrueName'].'\',\''.$cinfo['ClientPhone'].'\',\''.$cinfo['ClientAdd'].'\')" >&nbsp;'.$cinfo['ClientCompanyName'].'</td>
                        <td onclick="set_address_value(\''.$cinfo['ClientID'].'\',\''.$cinfo['ClientCompanyName'].'\',\''.$cinfo['ClientTrueName'].'\',\''.$cinfo['ClientPhone'].'\',\''.$cinfo['ClientAdd'].'\')">&nbsp;'.$cinfo['ClientAdd'].'</td>
                      </tr>';
	}else{
		foreach($orderlistuser as $olvar)
		{
			$bodymsg .= '<tr height="28" id="selected_line_'.$olvar['AddressID'].'">
                        <td ><input id="orderadd_'.$olvar['AddressID'].'" name="orderadd" type="radio" onfocus="set_address_value(\''.$olvar['AddressID'].'\',\''.$olvar['AddressCompany'].'\',\''.$olvar['AddressContact'].'\',\''.$olvar['AddressPhone'].'\',\''.$olvar['AddressAddress'].'\')" value="'.$olvar['AddressID'].'" /></td>
                        <td onclick="set_address_value(\''.$olvar['AddressID'].'\',\''.$olvar['AddressCompany'].'\',\''.$olvar['AddressContact'].'\',\''.$olvar['AddressPhone'].'\',\''.$olvar['AddressAddress'].'\')" >&nbsp;'.$olvar['AddressCompany'].'</td>
                        <td onclick="set_address_value(\''.$olvar['AddressID'].'\',\''.$olvar['AddressCompany'].'\',\''.$olvar['AddressContact'].'\',\''.$olvar['AddressPhone'].'\',\''.$olvar['AddressAddress'].'\')">&nbsp;'.$olvar['AddressAddress'].'</td>
                      </tr>';
		}
	}
	$endmsg = '</table>';

	echo $headermsg.$bodymsg.$endmsg;
	exit();
}
elseif($in['m']=="saveaddneworder")
{
	if(empty($in['data_OrderUserID'])) exit('error');
	
	//验证该药店是否可正常下单
	$sql_c   = "select ClientFlag from ".DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['data_OrderUserID'])." limit 1";
	$cinfo = $db->get_row($sql_c);
	
	if(empty($cinfo) || $cinfo['ClientFlag'] == "1" || $cinfo['ClientFlag'] == "8" || $cinfo['ClientFlag'] == "9"){
		echo ($cinfo['ClientFlag'] == "8" || $cinfo['ClientFlag'] == "9") ? 'error-该药店还处于待审核状态，暂不能下单！' : 'error-该药店已冻结，暂不能下单！';
		exit;
	}
	
	//验证结束
	
	$in['data_OrderRemark'] = $in['data_OrderRemark'].'（管理员：'.$_SESSION['uinfo']['usertruename'].' 下单）';

	$osn = $db->get_row("SELECT OrderID,OrderSN from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." order by OrderID desc limit 0,1");
	if(empty($osn['OrderSN']))
	{
		$ordersnmo = date("Ymd")."-".mt_rand(1999,5999);
	}else{
// 		$nextid	   = intval(substr($osn['OrderSN'],strpos($osn['OrderSN'], '-')+1))+1;
// 		$ordersnmo = date("Ymd")."-".$nextid;
		
		$today   = date("Ymd");
		$nowDate = substr($osn['OrderSN'], 0, 8);
		$nextid	 = intval(substr($osn['OrderSN'],strpos($osn['OrderSN'], '-')+1))+1;
		$ordersnmo = $nowDate == $today ? (date("Ymd")."-".$nextid) : (date("Ymd")."-".mt_rand(1999,5999));
	}
	$valuearr = get_set_arr('product');
    $erparr = get_set_arr('erp');
	//if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on") $autidstatus = 'F'; else $autidstatus = 'T';
	$autidstatus = 'T';
    $orderStatus = $erparr['erp_interface']=='Y' ? 9 : 0;

	$sqlin = "insert into ".DATATABLE."_order_orderinfo(OrderSN,OrderCompany,OrderUserID,OrderSendType,OrderPayType,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,OrderReceiveAdd,OrderRemark,OrderDate,OrderType,OrderSaler,OrderStatus,OrderFrom) values('".$ordersnmo."', ".$_SESSION['uinfo']['ucompany'].", ".$in['data_OrderUserID'].", ".$in['data_OrderSendType'].",  ".$in['data_OrderPayType'].", '".$in['data_OrderReceiveCompany']."', '".$in['data_OrderReceiveName']."', '".$in['data_OrderReceivePhone']."', '".$in['data_OrderReceiveAdd']."', '".$in['data_OrderRemark']."',".time().",'M','".$autidstatus."',{$orderStatus},'Compute')";
	$isup = $db->query($sqlin);

	if($isup)
	{
		$osn = $db->get_row("SELECT OrderID,OrderSN from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$ordersnmo."' order by OrderID desc limit 0,1");
		if(empty($osn['OrderID'])) exit('error');
		$orderid = $osn['OrderID'];

		$sqlex= "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_order.php?m=saveaddneworder&ID=".$orderid."','新增订单(".$orderid.")',".time().")";
		$db->query($sqlex);
		echo $orderid;
		exit();
	}else{
		exit('error');
	}
}
elseif($in['m']=="edit_return_product_save")
{
	if(empty($in['return_id'])) exit('参数错误!');

	$cartdata = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID=".$in['return_id']." order by ID asc");

	$status = false;
	$totalorder = 0;
	$idmsg = "0";

	$tykey = str_replace($fp,$rp,base64_encode("统一"));
	$cospnumarr = null;
	$library = '保存成功!';

	foreach($cartdata as $cvar)
	{
		$cartarr[$cvar['ID']] = $cvar;
		$idmsg  .= ",".$cvar['ContentID'];
	}

	for($i=0;$i<count($in['cart_id']);$i++)
	{
		$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
		$sqlmsg = "";

		if(empty($in['cart_num'][$i]))
		{
			$db->query("delete from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID=".$in['return_id']." and ID=".$in['cart_id'][$i] );
			$status = true;
		}else{

			if($in['cart_price'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentPrice'] || $in['cart_num'][$i]!=$cartarr[$in['cart_id'][$i]]['ContentNumber'])
			{
				$sql = "update ".DATATABLE."_order_cart_return set ContentPrice='".$in['cart_price'][$i]."',ContentNumber=".$in['cart_num'][$i]." where ID=".$in['cart_id'][$i]." and ReturnID=".$in['return_id']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
				$isupcart = $db->query($sql);
				$status = true;
			}
	   }
	}

	if($status)
	{
		if($in['t']=="back")
		{
			$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['return_id'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '修改退单商品', '--')";
			$db->query($sqlin);
		}

		$cartdatat = $db->get_results("select ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID=".$in['return_id']." order by ID asc");
		$totalorder = 0;
		foreach($cartdatat as $tvar)
		{
			$totalorder = $totalorder + ($tvar['ContentPrice'] * $tvar['ContentNumber']);
		}
		$db->query("update ".DATATABLE."_order_returninfo set ReturnTotal='".$totalorder."' where ReturnCompany=".$_SESSION['uinfo']['ucompany']." and ReturnID=".$in['return_id']);
	}
	echo $library;
	exit();

}elseif($in['m']=="add_input_number_save"){

	$snarr   = null;
	$totalnumber = 0;
	if(empty($in['inputpid'])) exit('参数错误!'); else $pid = intval($in['inputpid']);
	if(empty($in['orderid'])) exit('参数错误!'); else $oid = intval($in['orderid']);
	if(!empty($in['cart_number_id']))
	{
		for($i=0;$i<count($in['cart_number_id']);$i++)
		{
			if(!empty($in['cart_number'][$i])) $snarr[$in['cart_number_id'][$i]] = abs(intval($in['cart_number'][$i]));
		}
		if(!empty($snarr))
		{
			add_items_arr($pid,$oid,$snarr);
			exit();
		}
	}
	exit('您还没有订购任何商品！');

}elseif($in['m']=="change_input_number"){

	$snarr = null;
	$totalnumber = 0;
	$stotal = 0;
	$ctotal = 0;
	if(!empty($in['cart_number_id']))
	{
		for($i=0;$i<count($in['cart_number_id']);$i++)
		{
			$snarr[$in['cart_number_id'][$i]] = abs(intval($in['cart_number'][$i]));
			$keyarr = explode("_",$in['cart_number_id'][$i]);
			$sarr[] = $keyarr[1];
			$carr[] = $keyarr[2];
			$totalnumber = $totalnumber + abs(intval($in['cart_number'][$i]));
		}
		$sarr = array_unique($sarr);
		$carr = array_unique($carr);
		
		foreach($carr as $cvar)
		{
			$stotal = $stotal + $snarr['inputn_'.$in['spec'].'_'.$cvar];
		}
		foreach($sarr as $svar)
		{
			$ctotal = $ctotal + $snarr['inputn_'.$svar.'_'.$in['color']];
		}
	}
	//echo "application/json;charset=UTF-8";
	$arrmsg = urlencode(serialize($snarr));

	$omsg = '{"backtype":"ok", "hjvalue":"'.$stotal.'", "sjvalue":"'.$ctotal.'","totalvalue":"'.$totalnumber.'"}';
	echo $omsg;
	exit();

}elseif($in['m']=="addtoorderone"){

	if(empty($in['pid'])) 
	{
	    $rdata['backtype'] = 'error';
	    $rdata['cartnum']  = '参数错误!';
	    echo json_encode($rdata);
	    exit();
	} 
	else 
	    $p_id = intval($in['pid']);
	if(empty($in['oid'])) 
	{
	    $rdata['backtype'] = 'error';
	    $rdata['cartnum']  = '参数错误!';
	    echo json_encode($rdata);
	    exit();
	} 
	else 
	    $o_id = intval($in['oid']);	
	$pnum = intval($in['pnum']);
	if(empty($pnum)) 
	{
	    $rdata['backtype'] = 'error';
	    $rdata['cartnum']  = '请输入正确的数量!';
	    echo json_encode($rdata);
	    exit();
	}
	
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." order by OrderID desc limit 0,1");	
	$pinfo = $db->get_row("SELECT ID,BrandID,CommendID,Name,Price1,Price2,Price3 FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID=".$p_id." limit 0,1");
	$cinfo = $db->get_row("SELECT ClientID,ClientLevel,ClientName,ClientSetPrice,ClientPercent,ClientBrandPercent FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

	if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
	{
		$cinfo['ClientLevel'] = "A_".$cinfo['ClientLevel'];
	}
	$cartprice = $pinfo[$cinfo['ClientSetPrice']];
	
	//判断是否特价
	if($pinfo['CommendID']=="2")
	{
		$cinfo['ClientPercent'] = '10.0';
	}else{
		$BrandPercent = unserialize($cinfo['ClientBrandPercent']);
		if(!empty($pinfo['BrandID']) && !empty($BrandPercent[$pinfo['BrandID']]))
		{
			$cinfo['ClientPercent'] = $BrandPercent[$pinfo['BrandID']];
		}
	}
	if(empty($cinfo['ClientPercent'])) $cinfo['ClientPercent'] = '10.0';
	
	//如果是单独指定过价格
	if(!empty($pinfo['Price3']))
	{
		$price3 = setprice3($pinfo['Price3'],$cinfo);
		if(!empty($price3))
		{
			$cartprice = $price3;
			$cinfo['ClientPercent'] = '10.0';
		}
	}
	$valuearr = get_set_arr('product');
	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on" && $valuearr['product_negative'] != "on")
	{
		$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID = ".$p_id." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1";
		$data_all = $db->get_row($sql_l);
		if($data_all['OrderNumber'] < 1){
			$rdata['backtype'] = 'error';
			$rdata['cartnum']  = '库存不够！';
			echo json_encode($rdata);
			exit();
		}
		if($data_all['OrderNumber'] < $pnum) $pnum = $data_all['OrderNumber'];
	}
	
	/***  验证商品是否已加入订单 存在则数量累加  ***/
	$sql = "";
	$cart = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." and ContentID=".$p_id." ");
	if(!empty($cart['allrow']))
	{
	    $sql = "UPDATE ".DATATABLE."_order_cart SET ContentPrice={$cartprice},ContentNumber=ContentNumber+{$pnum},ContentPercent={$cinfo['ClientPercent']} WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." and ContentID=".$p_id;
	}
	else 
	{
	    $sql = "insert into ".DATATABLE."_order_cart(OrderID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent) values(".$o_id.",".$_SESSION['uinfo']['ucompany'].",".$cinfo['ClientID'].",".$pinfo['ID'].",'".$pinfo['Name']."', '', '', '".$cartprice."', ".$pnum.",'".$cinfo['ClientPercent']."')";
	}
	
	$isin = $db->query($sql);
	if($isin){
		if($valuearr['product_number']=="on"){
			$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber-".$pnum." where ContentID = ".$pinfo['ID']." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 1");
			$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$pinfo['ID']},{$o_id},{$pnum},'morder')");
		}
		$total = $db->get_row("SELECT sum(ContentPrice*ContentNumber*ContentPercent/10) as mtotal from ".DATATABLE."_order_cart where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." ");
		$totalnum = sprintf("%01.2f", round($total['mtotal'],2));

        $stair_count = get_stair($totalnum);
        $totalnum = $totalnum - $stair_count;
        $orderSpecial = 'F';
        if($stair_count > 0) {
            $orderSpecial = 'T';
        }
		$db->query("update ".DATATABLE."_order_orderinfo set OrderSpecial='{$orderSpecial}', OrderTotal='".$totalnum."' where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." limit 1");
		
		$cartnum = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." ");
		$rdata['backtype'] = 'ok';
		$rdata['cartnum'] = $cartnum['allrow'];
		echo json_encode($rdata);
		exit();
	}else{
		$rdata['backtype'] = 'error';
		$rdata['cartnum']  = '添加不成功！';
		echo json_encode($rdata);
		exit();
	}
	
} else if($in['m'] == 'ajax_consignment') {
    $rData = array();
    //fixme 指定查询字段
    $sn = $in['OrderSN'];
    $company_id = $_SESSION['uinfo']['ucompany'];

    $list = $db->get_results("SELECT * FROM ".DATATABLE."_order_consignment WHERE ConsignmentCompany={$company_id} AND ConsignmentOrder='{$sn}'");
    if(count($list) > 0) {
        $rData['status'] = 1;
        $rData['data'] = $list;
    } else {
        //没有发货单
        $order_id = $db->get_var("SELECT OrderID FROM ".DATATABLE."_order_orderinfo WHERE OrderSN='{$sn}' AND OrderCompany=" . $company_id);
        $cart_count = $db->get_var("SELECT COUNT(*) as Total FROM ".DATATABLE."_order_cart WHERE CompanyID={$company_id} AND OrderID={$order_id} AND ContentNumber > ContentSend");
        if((int)$cart_count > 0) {
            //有商品未发货
            $rData['status'] = 0;
            $rData['data'] = array();
        } else {
            //都发完了
            $rData['status'] = -1;
            $rData['data'] = array();
        }
    }
    echo json_encode($rData);
    exit;
} else if($in['m'] == 'ajax_finance') {
    $result = array();
    //fixme 指定查询字段
    $sn = $in['OrderSN'];
    $company_id = $_SESSION['uinfo']['ucompany'];
    $list = $db->get_results("SELECT * FROM ".DATATABLE."_order_finance WHERE FinanceCompany={$company_id} AND INSTR(FinanceOrder,'{$sn}')");

    $total = $db->get_var("SELECT OrderTotal FROM ".DATATABLE."_order_orderinfo WHERE OrderCompany={$company_id} AND OrderSN='{$sn}' LIMIT 1");
    if($total == 0) {
        //订单金额为0
        //直接将订单状态改为已到账
        $db->query("UPDATE ".DATATABLE."_order_orderinfo SET OrderPayStatus=2,OrderStatus=5 WHERE OrderCompany={$company_id} AND OrderSN='{$sn}' LIMIT 1");
        $result['status'] = -1;
    } else {
        $result['status'] = count($list) > 0 ? 1 : 0;
        $result['data'] = $list;
    }

    echo json_encode($result);
    exit;
} else if($in['m'] == 'ajax_order_incept') {
    //ajax修改订单状态为已收货
    $sn = $in['OrderSN'];
    $company_id = $_SESSION['uinfo']['ucompany'];
    //验证是否允许更新 fixme
    $order_cart = $db->get_results("SELECT * FROM ".DATATABLE."_order_cart WHERE CompanyID={$company_id} AND OrderID={$order_info['OrderID']} AND ContentNumber > ContentSend");
    $order_cart = $order_cart ? $order_cart : array();
    if(count($order_cart) > 0) {
        exit('订单含用未发完的商品,请选发完!');
    }
    $result = $db->query("UPDATE ".DATATABLE."_order_orderinfo SET OrderStatus=3,OrderSendStatus=4 WHERE OrderCompany={$company_id} AND OrderSN='{$sn}' LIMIT 1");
    if($result !== false) {
        exit('ok');
    } else {
        exit('操作失败,请重试!');
    }
}

function add_items_arr($p_id,$o_id,$cartarr) 
{ 
	 $fp = array('+','/','=','_');
	 $rp = array('-','|','DHB',' ');
	 $qty = 1;
	 global $db;

	 $cy   = str_replace($fp,$rp,base64_encode("统一"));
	 if(!empty($cartarr))
	 {				
		 //begin
		$cartdata = $db->get_results("select ID,ContentID,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." order by ID asc");
		if(!empty($cartdata))
		{
			foreach($cartdata as $cvar)
			{
				$incart[] = make_kid($cvar['ContentID'], $cvar['ContentColor'], $cvar['ContentSpecification']);
				$total    = $total + $cvar['ContentPrice'] * $cvar['ContentNumber'] * $cvar['ContentPercent'] * 0.1;
			}
		}
		$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." order by OrderID desc limit 0,1");	
		$pinfo = $db->get_row("SELECT ID,BrandID,CommendID,Name,Price1,Price2,Price3 FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID=".$p_id." limit 0,1");
		$cinfo = $db->get_row("SELECT ClientID,ClientLevel,ClientName,ClientSetPrice,ClientPercent,ClientBrandPercent FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

		if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
		{
			$cinfo['ClientLevel'] = "A_".$cinfo['ClientLevel'];
		}
		$cartprice = $pinfo[$cinfo['ClientSetPrice']];
		if($pinfo['CommendID']=="2")
		{
			$cinfo['ClientPercent'] = '10.0';
		}else{
			$BrandPercent = unserialize($cinfo['ClientBrandPercent']);
			if(!empty($pinfo['BrandID']) && !empty($BrandPercent[$pinfo['BrandID']]))
			{
				$cinfo['ClientPercent'] = $BrandPercent[$pinfo['BrandID']];
			}
		}
		if(empty($cinfo['ClientPercent'])) $cinfo['ClientPercent'] = '10.0';
		
		if(!empty($pinfo['Price3']))
		{
			$price3 = setprice3($pinfo['Price3'],$cinfo);
			if(!empty($price3))
			{
				$cartprice = $price3;
				$cinfo['ClientPercent'] = '10.0';
			}
		}
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
		{
			$sql_l    = "SELECT ContentID,OrderNumber,ContentColor,ContentSpec FROM ".DATATABLE."_order_inventory_number where ContentID = ".$p_id." and CompanyID = ".$_SESSION['uinfo']['ucompany']." ";
			$data_all = $db->get_results($sql_l);
			foreach($data_all as $v){
				$kkid = $v['ContentID'].'_p_'.$v['ContentColor'].'_s_'.$v['ContentSpec'];
				$numarr[$kkid] = $v['OrderNumber'];
			}
		}

		 foreach($cartarr as $key=>$var)
		 {
			if(!empty($var)) $qty = intval($var);
			$karr = explode("_",$key);
			if($karr[1] == $cy) $pspec  = ''; else $pspec  = base64_decode(str_replace($rp,$fp,$karr[1]));
			if($karr[2] == $cy) $pcolor = ''; else $pcolor = base64_decode(str_replace($rp,$fp,$karr[2]));
			$kkid = $pinfo['ID'].'_p_'.$karr[2].'_s_'.$karr[1];

			if($valuearr['product_number']=="on" && $valuearr['product_negative'] != "on"){
				if($numarr[$kkid] < 1) continue;
				if($numarr[$kkid] < $qty) $qty = $numarr[$key];
			}
			/** 验证多规格是否重复 重复则数量累加 **/
			$sql = "";
			$cnum = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." and ContentID=".$p_id." AND ContentColor='".$pcolor."' AND ContentSpecification='".$pspec."'");
			if(!empty($cnum['allrow']))
			{
			    $sql = "UPDATE ".DATATABLE."_order_cart SET ContentPrice={$cartprice},ContentNumber=ContentNumber+{$qty},ContentPercent={$cinfo['ClientPercent']} WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." and ContentID=".$p_id." AND ContentColor='".$pcolor."' AND ContentSpecification='".$pspec."'";
			}
			else 
			{
			    $sql = "insert into ".DATATABLE."_order_cart(OrderID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent) values(".$o_id.",".$_SESSION['uinfo']['ucompany'].",".$cinfo['ClientID'].",".$pinfo['ID'].",'".$pinfo['Name']."', '".$pcolor."', '".$pspec."', '".$cartprice."', ".$qty.",'".$cinfo['ClientPercent']."')";
			}

			$isin = $db->query($sql);
			$isok = false;
			if($isin){
				if($valuearr['product_number']=="on"){
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$qty." where ContentID = ".$pinfo['ID']." and CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentColor='".$karr[2]."' and ContentSpec='".$karr[1]."' limit 1");

                    $db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber-".$qty." where ContentID = ".$pinfo['ID']." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 1");

					$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$pinfo['ID']},{$o_id},{$qty},'morder')");
				}
				$isok = true;
			}
		 }		
		if($isok){	
			$total = $db->get_row("SELECT sum(ContentPrice*ContentNumber*ContentPercent/10) as mtotal from ".DATATABLE."_order_cart where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." ");
			$totalnum = sprintf("%01.2f", round($total['mtotal'],2));

            $stair_count = get_stair($totalnum);
            $totalnum = $totalnum - $stair_count;
            $orderSpecial = 'F';
            if($stair_count > 0) {
                $orderSpecial = 'T';
            }

			$db->query("update ".DATATABLE."_order_orderinfo set OrderSpecial='{$orderSpecial}', OrderTotal='".$totalnum."' where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$o_id." limit 1");
			echo 'ok';		
			exit();
		}else{
			exit('添加不成功!');
		}
	}else{
		exit('您没有输入任何商品!');
	}
	//end
}


function make_kid($product_id, $product_color='', $product_spec='')
{
	$kid = $product_id;
	$fp = array('+','/','=','_');
	$rp = array('-','|','DHB',' ');
	if(strlen($product_color))
	{
		$kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
	}
	if(strlen($product_spec))
	{
		$kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
	}
	return $kid;
}


function chang_number($db,$in,$ac)
{
	$fp = array('+','/','=','_');
	$rp = array('-','|','DHB',' ');
		
	$valuearr = get_set_arr('product');

	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
	{
		$sql      = "select OrderID,ContentID,ContentColor,ContentSpecification,ContentNumber,ContentSend from ".DATATABLE."_order_cart where OrderID=".intval($in['ID'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
		$data_c = $db->get_results($sql);

		$sqlg     = "select OrderID,ContentID,ContentColor,ContentSpecification,ContentNumber,ContentSend from ".DATATABLE."_order_cart_gifts where OrderID=".intval($in['ID'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
		$data_g = $db->get_results($sqlg);

		$tykey = str_replace($fp,$rp,base64_encode("统一"));
		foreach($data_c as $dvar)
		{
			$jnum = $dvar['ContentNumber'] - $dvar['ContentSend'];
			if(empty($jnum)) continue;
			if(!empty($dvar['ContentColor']) || !empty($dvar['ContentSpecification']))
			{
				if(empty($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
				if(empty($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec = str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
				
				$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=ContentNumber-".$jnum." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
			}
			$db->query("update ".DATATABLE."_order_number set ContentNumber=ContentNumber-".$jnum." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");

			$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$dvar['OrderID']},{$jnum},'".$ac."')");
		}

		if(!empty($data_g))
		{
			foreach($data_g as $dvar)
			{			
				$jnum = $dvar['ContentNumber'] - $dvar['ContentSend'];
				if(empty($jnum)) continue;
				if(!empty($dvar['ContentColor']) || !empty($dvar['ContentSpecification']))
				{
					if(empty($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
					if(empty($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
					
					$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=ContentNumber-".$jnum." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
				}
				$db->query("update ".DATATABLE."_order_number set ContentNumber=ContentNumber-".$jnum." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");

				$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$dvar['OrderID']},{$jnum},'".$ac."')");
			}
		}

		$underdata = $db->get_col("select ContentID from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and (ContentNumber < 0 or ContentNumber < OrderNumber)");
		if(!empty($underdata))
		{
			$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=0 where CompanyID=".$_SESSION['uinfo']['ucompany']."  and ContentNumber < 0 ");				
			$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']."  and OrderNumber > ContentNumber ");

			$conidarr = array_unique($underdata);
			foreach($conidarr as $v)
			{
				$allnumber = $db->get_row("select sum(OrderNumber) as onum,sum(ContentNumber) as cnum from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$v."  ");
				$db->query("update ".DATATABLE."_order_number set OrderNumber=".$allnumber['onum']." ,ContentNumber=".$allnumber['cnum']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$v." limit 1");
			}
		}			

		$db->query("update ".DATATABLE."_order_number set ContentNumber=0 where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentNumber < 0");
		$db->query("update ".DATATABLE."_order_number set OrderNumber=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderNumber > ContentNumber");

	 }
}

function setprice3($p3,$cinfo)
{
	$rp3 = '';
	$lkey = '';

	if(!empty($p3))
	{
		$pricearr = unserialize(urldecode($p3));
		//单个指定
		if(!empty($pricearr['clientprice'][$cinfo['ClientID']]))
		{
			$rp3 = $pricearr['clientprice'][$cinfo['ClientID']];
		}else{
			if(empty($pricearr['typeid'])) $pricearr['typeid'] = 'A';
			if(!empty($cinfo['ClientLevel']))
			{
				if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
				{
					$cinfo['ClientLevel'] = "A_".$cinfo['ClientLevel'];
				}
				$clientlevelarr = explode(",", $cinfo['ClientLevel']);
				foreach($clientlevelarr as $cvar)
				{
					if($pricearr['typeid']==substr($cvar,0,1))
					{
						$lkey = substr($cvar,2);
						break;
					}
				}
			}
			if(!empty($pricearr[$lkey])) $rp3 = $pricearr[$lkey];
		}
	}
	return $rp3;
}


function chang_point($db,$in,$ot,$ty)
{
	$pointarr = get_set_arr('point');

	if(!empty($pointarr) && $pointarr['pointtype'] != "1" )
	{
		if(!empty($ty) && $ty=="jian")
		{
			$db->query("delete from ".DATATABLE."_order_point where PointCompany=".$_SESSION['uinfo']['ucompany']." and PointOrder='".$ot['OrderSN']."' ");
		}else{
			$pointv = 0;
			if($pointarr['pointtype'] == "2")
			{
				if(empty($pointarr['pointpencent'])) $pointarr['pointpencent'] = 1;
				$pointv = abs(intval($ot['OrderTotal'] * $pointarr['pointpencent']));
			}else{
				$sql      = "select c.ContentID,c.ContentNumber,i.ContentPoint from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_1 i on c.ContentID=i.ContentIndexID where c.OrderID=".intval($in['ID'])." and c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID= ".$_SESSION['uinfo']['ucompany']." and i.ContentPoint !=0 ";
				$data_c = $db->get_results($sql);
				foreach($data_c as $cv)
				{
					$pointv = $pointv + ($cv['ContentNumber'] * $cv['ContentPoint']);
				}
			}
			$pointv = intval($pointv);
			if(!empty($pointv)) $db->query("insert into ".DATATABLE."_order_point(PointCompany,PointClient,PointOrder,PointValue,PointDate,PointUser) value(".$_SESSION['uinfo']['ucompany'].", ".$ot['OrderUserID'].", '".$ot['OrderSN']."', ".$pointv.", ".time().", ".$_SESSION['uinfo']['userid'].")");
		}
	}
}

exit('非法操作!');
?>