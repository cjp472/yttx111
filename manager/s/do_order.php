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
if($in['m']=="Audit1")
{
	if($_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('error');
	$in['ID'] = intval($in['ID']);
	$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderStatus,OrderTotal FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['ID']."  limit 0,1");
	if (!in_array($oinfo['OrderUserID'], $sclientidarr ))
	{		
		exit('对不起，您没有此项操作权限！');
	}

	$upsql = "update ".DATATABLE."_order_orderinfo set OrderSaler='T' where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=0 and OrderSaler='F' ";
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '初审订单', '".$in['Content']."')";
		$db->query($sqlin);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Audit2")
{
	if($_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(!intval($in['ID'])) exit('error');
	$in['ID'] = intval($in['ID']);
	$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
	$loinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderStatus,OrderTotal FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['ID']."  limit 0,1");
	if (!in_array($loinfo['OrderUserID'], $sclientidarr ))
	{		
		exit('对不起，您没有此项操作权限！');
	}
	$upsql = "update ".DATATABLE."_order_orderinfo set OrderStatus=1,OrderSendStatus=1 where OrderID = ".$in['ID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=0 ";
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '审核订单', '".$in['Content']."')";
		$db->query($sqlin);

		chang_point($db,$in,$loinfo,"jia");

		$message = "【".$_SESSION['uc']['CompanySigned']."】感谢您的订购!您的订单(".$loinfo['OrderSN'].")已通过审核,我们会尽快安排出货,请注意查收";
		sms::get_setsms("8",$loinfo['OrderUserID'],$message);

		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="Message")
{
	if(!intval($in['ID'])) exit('error');
	//if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

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
elseif($in['m']=="edit_order_product_save")
{
	if(empty($in['order_id'])) exit('参数错误!');
	if($_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$cartdata = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']." order by ID asc");
    $order_info = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo WHERE OrderCompany=" . $_SESSION['uinfo']['ucompany'] . " AND OrderID=" . $in['order_id']);
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

		if(strlen($cartarr[$in['cart_id'][$i]]['ContentColor']) > 0)
		{
			$ckeycolor = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentColor']));
		}else{
			$ckeycolor = $tykey;
		}
		if(strlen($cartarr[$in['cart_id'][$i]]['ContentSpecification']) > 0)
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
				
				if(strlen($cartarr[$in['cart_id'][$i]]['ContentColor'])>0 || strlen($cartarr[$in['cart_id'][$i]]['ContentSpecification']) > 0)
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

        $stair_count = get_stair($totalorder);
        $totalorder = $totalorder - $stair_count;
        $orderSpecial = 'F';
        if($stair_count > 0) {
            $orderSpecial = 'T';
        }
        if($in['t']=="back")
        {
            $stair_amount = get_stair($stair_count + $totalorder,'amount');
            $submit_content = '--';
            if($stair_count > 0) {
                $stair_amount = get_stair($stair_count + $totalorder , 'amount');
                $submit_content = '订单满 ¥' . $stair_amount . ' 省 ¥' . $stair_count . ' ，金额： ¥' . $totalorder;
            }
            $sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['order_id'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '修改订单商品', '{$submit_content}')";
            $db->query($sqlin);
        }

        //将税率计算在内 计算最终订单金额
        if($order_info['InvoiceType'] != 'N' && !empty($order_info['InvoiceTax'])){
            $totalorder = $totalorder + $totalorder * $order_info['InvoiceTax'] / 100;
            $totalorder = round($totalorder , 2);
        }

		$db->query("update ".DATATABLE."_order_orderinfo set OrderSpecial='{$orderSpecial}', OrderTotal='".$totalorder."' where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']);
	}
	echo $library;
	exit();

}
elseif($in['m']=="new_edit_order_product_save")
{
	if(empty($in['order_id'])) exit('参数错误!');
	if($_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

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

		if(strlen($cartarr[$in['cart_id'][$i]]['ContentColor']) > 0)
		{
			$ckeycolor = str_replace($fp,$rp,base64_encode($cartarr[$in['cart_id'][$i]]['ContentColor']));
		}else{
			$ckeycolor = $tykey;
		}
		if(strlen($cartarr[$in['cart_id'][$i]]['ContentSpecification']) > 0)
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
				
				if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
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
						if(strlen($cartarr[$in['cart_id'][$i]]['ContentColor'])>0 || strlen($cartarr[$in['cart_id'][$i]]['ContentSpecification']) > 0)
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
		$db->query("update ".DATATABLE."_order_orderinfo set OrderTotal='".$totalorder."' where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['order_id']);
	}

	echo $library;
	exit();

}
elseif($in['m']=="order_gifts_product_save")
{
	if(empty($in['order_id'])) exit('参数错误!');
	if($_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

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
						if(!empty($cartarr[$in['cart_id'][$i]]['ContentColor']) || !empty($cartarr[$in['cart_id'][$i]]['ContentSpecification']))
						{
							$db->query("update ".DATATABLE."_order_inventory_number set  ".$sqlmsg." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['cart_content_id'][$i]." and ContentColor='".$ckeycolor."' and ContentSpec='".$ckeyspec."' limit 1");
						}
						$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['cart_content_id'][$i]},{$in['order_id']},{$dnumber},'mgchange')");
					}
				}
				$sql = "update ".DATATABLE."_order_cart_gifts set ContentNumber=".$in['cart_num'][$i]." where ID=".$in['cart_id'][$i]." and OrderID=".$in['order_id']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
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
}
elseif($in['m']=="addtocart")
{
	if($_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$in['oid'] = intval($in['oid']);
	if(!intval($in['pid'])) exit('error');
	if($in['pcolor'] == 'null') echo $in['pcolor']='';
	if($in['pspec']  == 'null') echo $in['pspec']='';
    $num = $in['num'] ? $in['num'] : 1; //订购数量
	$kid = make_kid($in['pid'], $in['pcolor'], $in['pspec']);
	$total = 0;
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
// 	if(@in_array($kid, $incart))
// 	{
// 		exit("此商品已经在订单中了！");
// 	}
// 	else
// 	{
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
		}
		else
		{
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
			if(!strlen($in['pcolor']) && !strlen($in['pspec']))
			{
				$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1";
				$data_all = $db->get_row($sql_l);
				if(($data_all['OrderNumber']<1) && $valuearr['product_negative']!="on")
				{
					exit('该商品库存数量不够！');
				}
			}else{
				if(strlen($in['pcolor']))
				{
					$kcolor = str_replace($fp,$rp,base64_encode($in['pcolor']));
				}else{
					$kcolor = str_replace($fp,$rp,base64_encode("统一"));
				}
				if(strlen($in['pspec']))
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
					$sqlncs = "update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$num." where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." and ContentColor='".$kcolor."' and ContentSpec='".$kspec."' limit 1";
				}
			}
			$sqlnall = "update ".DATATABLE."_order_number set OrderNumber=OrderNumber-".$num." where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 1";

			$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$in['pid']},{$in['oid']},".$num.",'morder')");

		}
		
		$sql = "";
		if(@in_array($kid, $incart))
		{
		    $sql = "UPDATE ".DATATABLE."_order_cart SET ContentPrice={$cartprice},ContentNumber=ContentNumber+{$num},ContentPercent={$cinfo['ClientPercent']} WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['oid']." and ContentID=".$pinfo['ID']." AND ContentColor='".$in['pcolor']."' AND ContentSpecification='".$in['pspec']."'";
		}
		else 
		{
		    $sql = "insert into ".DATATABLE."_order_cart(OrderID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent) values(".$in['oid'].",".$_SESSION['uinfo']['ucompany'].",".$cinfo['ClientID'].",".$pinfo['ID'].",'".$pinfo['Name']."', '".$in['pcolor']."', '".$in['pspec']."', '".$cartprice."', ".$num.",'".$cinfo['ClientPercent']."')";
		}
		
		$isin = $db->query($sql);

		if($isin)
		{			
			$total = $total + $num * $cartprice * $cinfo['ClientPercent'] * 0.1;

            $stair_count = get_stair($total);
            $total = $total - $stair_count;
            $orderSpecial = 'F';
            if($stair_count > 0) {
                $orderSpecial = 'T';
            }

			$db->query("update ".DATATABLE."_order_orderinfo set OrderSpecial='{$orderSpecial}', OrderTotal='".$total."' where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['oid']." limit 1");
			if(!empty($sqlnall)) $db->query($sqlnall);
			if(!empty($sqlncs))  $db->query($sqlncs);
			
			echo 'ok';		
			exit();
		}else{
			exit('添加不成功!');
		}
// 	}
}
elseif($in['m']=="addtocart_gifts")
{
	if($_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

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
			if(!strlen($in['pcolor']) && !strlen($in['pspec']))
			{
				$sql_l    = "SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID = ".intval($in['pid'])." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1";
				$data_all = $db->get_row($sql_l);

				if(($data_all['OrderNumber']<1) && $valuearr['product_negative']!="on")
				{
					exit('该商品库存数量不够！');
				}
			}else{
				if(strlen($in['pcolor']))
				{
					$kcolor = str_replace($fp,$rp,base64_encode($in['pcolor']));
				}else{
					$kcolor = str_replace($fp,$rp,base64_encode("统一"));
				}
				if(strlen($in['pspec']))
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

	$orderlistuser = $db->get_results("SELECT AddressID,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag FROM ".DATATABLE."_order_address where CompanyID=".$_SESSION['uinfo']['ucompany']." and AddressClient=".$in['ID']."  order by AddressID desc");
	$bodymsg = '';
	$headermsg = '<table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0">
                        <td width="8%">&nbsp;</td>
                        <td width="32%"><strong>&nbsp;收货人</strong></td>
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
                        <td >&nbsp;<input id="orderadd_'.$olvar['AddressID'].'" name="orderadd" type="radio" onfocus="set_address_value(\''.$olvar['AddressID'].'\',\''.$olvar['AddressCompany'].'\',\''.$olvar['AddressContact'].'\',\''.$olvar['AddressPhone'].'\',\''.$olvar['AddressAddress'].'\')" value="'.$olvar['AddressID'].'" /></td>
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
	if($_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['data_OrderUserID'])) exit('error');
	$in['data_OrderRemark'] = $in['data_OrderRemark'].'（业务员：'.$_SESSION['uinfo']['usertruename'].' 下单）';

	$osn = $db->get_row("SELECT OrderID,OrderSN from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." order by OrderID desc limit 0,1");
	if(empty($osn['OrderSN']))
	{
		$ordersnmo = date("Ymd")."-1";
	}else{
		$nextid	   = intval(substr($osn['OrderSN'],strpos($osn['OrderSN'], '-')+1))+1;
		$ordersnmo = date("Ymd")."-".$nextid;
	}
	$valuearr = get_set_arr('product');
	if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on") $autidstatus = 'F'; else $autidstatus = 'T';

	$sqlin = "insert into ".DATATABLE."_order_orderinfo(OrderSN,OrderCompany,OrderUserID,OrderSendType,OrderPayType,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,OrderReceiveAdd,OrderRemark,OrderDate,OrderType,OrderSaler) values('".$ordersnmo."', ".$_SESSION['uinfo']['ucompany'].", ".$in['data_OrderUserID'].", ".$in['data_OrderSendType'].",  ".$in['data_OrderPayType'].", '".$in['data_OrderReceiveCompany']."', '".$in['data_OrderReceiveName']."', '".$in['data_OrderReceivePhone']."', '".$in['data_OrderReceiveAdd']."', '".$in['data_OrderRemark']."',".time().",'S','".$autidstatus."')";
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


function make_kid($product_id, $product_color='', $product_spec='')
{
	$kid = $product_id;
	$fp = array('+','/','=','_');
	$rp = array('-','|','DHB',' ');
	if(strlen($product_color) > 0)
	{
		$kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
	}
	if(strlen($product_spec) > 0)
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