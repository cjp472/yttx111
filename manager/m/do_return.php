<?php
$menu_flag = "return";
include_once ("header.php");
include_once ("../class/data.class.php");

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
if($in['m']=="Audit")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql = "update ".DATATABLE."_order_returninfo set ReturnStatus=2 where ReturnID = ".$in['ID']." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." and ReturnStatus=0 ";	
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '审核通过', '".$in['Content']."')";
		$db->query($sqlin);
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

	$upsql = "update ".DATATABLE."_order_returninfo set ReturnStatus=1 where ReturnID = ".$in['ID']." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." and ReturnStatus=0 ";	
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '审核不通过', '".$in['Content']."')";
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
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	$upinfo  = $db->get_row("SELECT * FROM ".DATATABLE."_order_returninfo where ReturnID = ".$in['ID']." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
	if(empty($upinfo['ReturnSendStatus']))
	{	
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
		{
			$sql    = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".DATATABLE."_order_cart_return where ReturnID=".intval($in['ID'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			$data_c = $db->get_results($sql);
				
			$tykey = str_replace($fp,$rp,base64_encode("统一"));
			foreach($data_c as $dvar)
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber'].", ContentNumber=ContentNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");
				
				if(!empty($dvar['ContentColor']) || !empty($dvar['ContentSpecification']))
				{
					if(empty($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
					if(empty($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber'].", ContentNumber=ContentNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
				}
				$jnum = intval("-".$dvar['ContentNumber']);
				$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$in['ID']},{$jnum},'return')");
				$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$in['ID']},{$jnum},'return')");
			}
		}
	}

	$upsql =  "update ".DATATABLE."_order_returninfo set ReturnStatus=3,ReturnSendStatus=1 where ReturnID = ".$in['ID']." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." ";	
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '管理员确认收货', '".$in['Content']."')";
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
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_returninfo set ReturnStatus=9 where ReturnID = ".$in['ID']." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." and (ReturnStatus=0 OR ReturnStatus=1) limit 1";	
	if($db->query($upsql))
	{
		$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '管理员取消', '".$in['Content']."')";
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
		
	$upinfo  = $db->get_row("SELECT * FROM ".DATATABLE."_order_returninfo where ReturnID = ".$in['ID']." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");

	$upsql =  "update ".DATATABLE."_order_returninfo set ReturnStatus=5,ReturnSendStatus=1 where ReturnID = ".$in['ID']." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." ";	
	if($db->query($upsql))
	{
		if(empty($upinfo['ReturnSendStatus']))
		{			
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
		{
			$sql    = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".DATATABLE."_order_cart_return where ReturnID=".intval($in['ID'])." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			$data_c = $db->get_results($sql);
				
			$tykey = str_replace($fp,$rp,base64_encode("统一"));
			foreach($data_c as $dvar)
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber'].", ContentNumber=ContentNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");
				
				if(!empty($dvar['ContentColor']) || !empty($dvar['ContentSpecification']))
				{
					if(empty($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
					if(empty($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber'].", ContentNumber=ContentNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
				}
				$jnum = intval("-".$dvar['ContentNumber']);
				$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$in['ID']},{$jnum},'return')");
				$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$in['ID']},{$jnum},'return')");
			}
		  }
	    }
		
		$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$in['ID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '已完成', '".$in['Content']."')";
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
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
		
	$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_returninfo where ReturnID = ".$in['ID']." and ReturnCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$infodatamsg = serialize($InfoData);

	$upsql =  "delete from ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." and ReturnID=".intval($in['ID'])." and (ReturnStatus=8 or ReturnStatus=9)";	
	if($db->query($upsql))
	{
		$sqld = "delete from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID=".$in['ID']."";	
		$db->query($sqld);
		$sqld = "delete from ".DATATABLE."_order_returnsubmit where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['ID']."";
		$db->query($sqld);

		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_return.php?m=Delete&ID=".$in['ID']."','删除退货单(".$in['ID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		echo 'ok';
		exit();
	}else{
		exit('操作失败，请与管理员联系');
	}
}
elseif($in['m']=="save_return_add")
{
	if(empty($in['cartid'])) exit('提示：您还没有输入任何退货商品！');
	if(empty($in['ReturnProductW']) || empty($in['ReturnProductB'])) exit('提示：请选择产品外观和包装情况！');
	if(empty($in['ReturnAbout'])) exit('提示：请输入退货原因！');
	
	$cartidmsg = "";

	for($i=0;$i<count($in['cartid']);$i++)
	{
		if(!empty($in['cartid'][$i]))
		{	
			$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
			if(!empty($in['cart_num'][$i]))
			{
				$returnarr[$in['cartid'][$i]] = $in['cart_num'][$i];
				$cartidmsg .= ",".$in['cartid'][$i];
			}
		}
	}
	if(empty($cartidmsg))
	{
		exit('提示：您还没有输入任何退货商品!');
	}elseif(!empty($in['orderid'])){
		$order = getcartproduct($in['orderid'],$db);
		///修改
		if(!empty($order['return']))
		{
			foreach($returnarr as $rkey=>$rvar)
			{
				if($rvar > $order['ordercart'][$rkey]['rnumber'])
				{
					echo $msg = '['.$order['ordercart'][$rkey]['ContentName'].'] 退货数量不能大于订单可退数!';
					exit();
				}
			}
			$orderin = insertintoreturncart($in,$order,$returnarr,$db);
			if($orderin=="ok")
			{
				exit('ok');
			}else{
				exit('提交不成功!');
			}
		}
	}
	exit('提交不成功!');
}
elseif($in['m']=="save_return_product_add")
{
	if(empty($in['cartkid'])) exit('提示：您还没有输入任何退货商品！');
	if(empty($in['ReturnProductW']) || empty($in['ReturnProductB'])) exit('提示：请选择产品外观和包装情况！');
	if(empty($in['ReturnAbout'])) exit('提示：请输入退货原因！');	
	$cartidmsg = "";
	
	for($i=0;$i<count($in['cartkid']);$i++)
	{
		if(!empty($in['cartkid'][$i]))
		{	
			$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
			if(!empty($in['cart_num'][$i]))
			{				
				$returnarr[$in['cartkid'][$i]] = unserialize(urldecode($in['cartdata'][$i]));
				$returnarr[$in['cartkid'][$i]]['number'] = $in['cart_num'][$i];
				$cartidmsg .= ",".$in['cartkid'][$i];
				$cidarr[]   = $returnarr[$in['cartkid'][$i]]['ContentID'];
			}
		}
	}

	if(empty($cartidmsg))
	{
		exit('提示：您还没有输入任何退货商品!');
	}else{
		if(!empty($in['cid']))
		{			
			$cidmsg = implode(",",$cidarr);
			$oinfo = $db->get_results("SELECT c.ContentID,c.ContentColor,c.ContentSpecification,c.ContentName,c.ContentNumber,c.ContentSend FROM ".DATATABLE."_order_orderinfo o left join ".DATATABLE."_order_cart c on o.OrderID=c.OrderID where o.OrderCompany = ".$_SESSION['uinfo']['ucompany']." and o.OrderUserID=".intval($in['cid'])." and (o.OrderSendStatus=3 or o.OrderSendStatus=4) and c.ContentID in (".$cidmsg.") order by c.ID asc");
			foreach($oinfo as $ovar)
			{
				$kid = make_kid($ovar['ContentID'], $ovar['ContentColor'], $ovar['ContentSpecification']);				
				if(empty($cartarr[$kid]))
				{
					$cartarr[$kid] = $ovar;
					$cartarr[$kid]['onumber'] = $ovar['ContentSend'];
				}else{
					$cartarr[$kid]['onumber'] = $cartarr[$kid]['onumber']+$ovar['ContentSend'];
				}
			}
			$sql_cr = "select r.ContentID,r.ContentName,r.ContentColor,r.ContentSpecification,r.ContentNumber from ".DATATABLE."_order_cart_return r left join ".DATATABLE."_order_returninfo i on r.ReturnID=i.ReturnID where i.ReturnCompany=".$_SESSION['uinfo']['ucompany']." and i.ReturnClient=".$in['cid']." and i.ReturnStatus!=1 and i.ReturnStatus!=8 and i.ReturnStatus!=9 ".$insqlmsg." order by r.ID desc";
			$returncart	= $db->get_results($sql_cr);
			$retarr  = null;
			if(!empty($returncart))
			{
				foreach($returncart as $rc)
				{
					$kid = make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
					if(empty($retarr[$kid]))
					{
						$retarr[$kid] = $rc['ContentNumber'];
					}else{
						$retarr[$kid] = $retarr[$kid]+$rc['ContentNumber'];
					}
				}
			}
			
			foreach($cartarr as $ckey=>$cvar)
			{
				if(!empty($retarr[$ckey]))
				{
					$cartarr[$ckey]['rnumber'] = $cartarr[$ckey]['onumber'] - $retarr[$ckey];
				}else{
					$cartarr[$ckey]['rnumber'] = $cartarr[$ckey]['onumber'];
				}
			}
			
			
			foreach($returnarr as $rkey=>$rvar)
			{
				if($rvar['number'] > $cartarr[$rkey]['rnumber'])
				{
					echo $msg = '['.$rvar['ContentName'].'] 退货数量不能大于商品可退数!';
					exit();
				}
			}
			$orderin = insertintoreturncart_product($in,$returnarr,$db);
			if($orderin=="ok")
			{
				exit('ok');
			}else{
				exit('提交不成功!');
			}
		}

	}
	exit('提交不成功!');
}


function make_kid($product_id, $product_color='', $product_spec='')
{
	$kid = $product_id;
	$fp = array('+','/','=','_');
	$rp = array('-','|','DHB',' ');
	if(!empty($product_color))
	{
		$kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
	}
	if(!empty($product_spec))
	{
		$kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
	}
	return $kid;
}

	//订单详细
	function getcartproduct($sn,$db)
	{
		$sql_o = "select OrderID,OrderSN,OrderUserID,OrderSendStatus,OrderPayStatus,OrderTotal,OrderStatus,OrderDate,OrderType from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderSN='".$sn."' limit 0,1";
		$orderinfo    = $db->get_row($sql_o);

		if($orderinfo['OrderStatus'] > 2 && $orderinfo['OrderStatus'] < 8 && $orderinfo['OrderSendStatus'] > 2)
		{
			$orderinfo['return'] = 'ok';
		}else{
			$orderinfo['return'] = '';
			return $orderinfo;
		}
		$sql_c = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent,ContentSend from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$orderinfo['OrderID']." order by ID asc";
		$ordercart	= $db->get_results($sql_c);
		
			$sql_cr = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID in (select ReturnID from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." and ReturnOrder='".$orderinfo['OrderSN']."') order by ID asc";
			$returncart	= $db->get_results($sql_cr);
			$returnarr = null;
			if(!empty($returncart))
			{
				foreach($returncart as $rc)
				{
					$kid = make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
					if(empty($returnarr[$kid]))
					{
						$returnarr[$kid] = $rc['ContentNumber'];
					}else{
						$returnarr[$kid] = $returnarr[$kid]+$rc['ContentNumber'];
					}
				}
			}

		for($j=0;$j<count($ordercart);$j++)
		{
			$kid = make_kid($ordercart[$j]['ContentID'], $ordercart[$j]['ContentColor'], $ordercart[$j]['ContentSpecification']);
			if(!empty($returnarr[$kid]))
			{
				$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentSend'] - $returnarr[$kid];
			}else{
				$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentSend'];
			}
			$ordercart[$j]['Price_End']    = $ordercart[$j]['ContentPrice'] * $ordercart[$j]['ContentPercent'] / 10;		
			$arrcart[$ordercart[$j]['ID']] = $ordercart[$j];
		}
		$result['orderinfo']   = $orderinfo;
		$result['ordercart']   = $arrcart;
		$result['return']      = $orderinfo['return'];

		//$db->debug();
		return $result;
		unset($result);
	}	

	//插入退货数据
	function insertintoreturncart($in,$orderc,$returnarr,$db)
	{
		$totalp = 0;
		$rssn    = $db->get_row("select ReturnID,ReturnSN from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." order by ReturnID desc limit 0,1");
		if(empty($rssn['ReturnSN']))
		{
			$ReturnSN = "R".date("Ymd")."-1";
		}else{
			$nextid	  = intval(substr($rssn['ReturnSN'],strpos($rssn['ReturnSN'], '-')+1))+1;
			$ReturnSN = "R".date("Ymd")."-".$nextid;
		}
		$isinr = $db->query("insert into ".DATATABLE."_order_returninfo(ReturnSN,ReturnOrder,ReturnCompany,ReturnClient,ReturnSendType,ReturnSendAbout,ReturnProductW,ReturnProductB,ReturnAbout,ReturnDate,ReturnType) values('".$ReturnSN."','".$orderc['orderinfo']['OrderSN']."',".$_SESSION['uinfo']['ucompany'].",".$orderc['orderinfo']['OrderUserID'].",'".$in['ReturnSendType']."','".$in['ReturnSendAbout']."','".$in['ReturnProductW']."','".$in['ReturnProductB']."','".$in['ReturnAbout']."',".time().",'M')");
		
		if($isinr)
		{
			$rid = mysql_insert_id();
			$cartd = $orderc['ordercart'];
			foreach($returnarr as $rkey=>$rvar)
			{
				if(!empty($rvar))
				{
					$db->query("insert into ".DATATABLE."_order_cart_return(ReturnID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber) values(".$rid.",".$_SESSION['uinfo']['ucompany'].",".$orderc['orderinfo']['OrderUserID'].",".$cartd[$rkey]['ContentID'].",'".$cartd[$rkey]['ContentName']."','".$cartd[$rkey]['ContentColor']."','".$cartd[$rkey]['ContentSpecification']."','".$cartd[$rkey]['Price_End']."',".$rvar.")");
					$totalp = $totalp + $cartd[$rkey]['Price_End'] * $rvar;
				}
			}
		}else{
			return 'error';
		}
		$db->query("update ".DATATABLE."_order_returninfo set ReturnTotal='".$totalp."' where ReturnID=".$rid." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." limit 1");	
		return 'ok';
	}



//**************************************************
	//订单详细
	function getcartproduct_product($sn,$db)
	{

		$sql_o = "select OrderID,OrderSN,OrderUserID,OrderSendStatus,OrderPayStatus,OrderTotal,OrderStatus,OrderDate,OrderType from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderSN='".$sn."' limit 0,1";
		$orderinfo    = $db->get_row($sql_o);

		if($orderinfo['OrderStatus'] > 2 && $orderinfo['OrderStatus'] < 8 && $orderinfo['OrderSendStatus'] > 2)
		{
			$orderinfo['return'] = 'ok';
		}else{
			$orderinfo['return'] = '';
			return $orderinfo;
		}
		$sql_c = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent,ContentSend from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$orderinfo['OrderID']." order by ID asc";
		$ordercart	= $db->get_results($sql_c);
		
			$sql_cr = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID in (select ReturnID from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." and ReturnOrder='".$orderinfo['OrderSN']."') order by ID asc";
			$returncart	= $db->get_results($sql_cr);
			$returnarr = null;
			if(!empty($returncart))
			{
				foreach($returncart as $rc)
				{
					$kid = make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
					if(empty($returnarr[$kid]))
					{
						$returnarr[$kid] = $rc['ContentNumber'];
					}else{
						$returnarr[$kid] = $returnarr[$kid]+$rc['ContentNumber'];
					}
				}
			}

		for($j=0;$j<count($ordercart);$j++)
		{
			$kid = make_kid($ordercart[$j]['ContentID'], $ordercart[$j]['ContentColor'], $ordercart[$j]['ContentSpecification']);
			if(!empty($returnarr[$kid]))
			{
				$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentSend'] - $returnarr[$kid];
			}else{
				$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentSend'];
			}
			$ordercart[$j]['Price_End']    = $ordercart[$j]['ContentPrice'] * $ordercart[$j]['ContentPercent'] / 10;		
			$arrcart[$ordercart[$j]['ID']] = $ordercart[$j];
		}
		$result['orderinfo']   = $orderinfo;
		$result['ordercart']   = $arrcart;
		$result['return']      = $orderinfo['return'];

		//$db->debug();
		return $result;
		unset($result);
	}



	//插入退货数据
	function insertintoreturncart_product($in,$returnarr,$db)
	{
		$totalp = 0;
		$rssn    = $db->get_row("select ReturnID,ReturnSN from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." order by ReturnID desc limit 0,1");
		if(empty($rssn['ReturnSN']))
		{
			$ReturnSN = "R".date("Ymd")."-1";
		}else{
			$nextid	  = intval(substr($rssn['ReturnSN'],strpos($rssn['ReturnSN'], '-')+1))+1;
			$ReturnSN = "R".date("Ymd")."-".$nextid;
		}
		$in['cid'] = intval($in['cid']);
		$isinr = $db->query("insert into ".DATATABLE."_order_returninfo(ReturnSN,ReturnOrder,ReturnCompany,ReturnClient,ReturnSendType,ReturnSendAbout,ReturnProductW,ReturnProductB,ReturnAbout,ReturnDate,ReturnType) values('".$ReturnSN."','',".$_SESSION['uinfo']['ucompany'].",".$in['cid'].",'".$in['ReturnSendType']."','".$in['ReturnSendAbout']."','".$in['ReturnProductW']."','".$in['ReturnProductB']."','".$in['ReturnAbout']."',".time().",'M')");
		
		if($isinr)
		{
			$rid = mysql_insert_id();
			foreach($returnarr as $rkey=>$rvar)
			{
				if(!empty($rvar['number']) && !empty($rvar['ContentID']))
				{
					$db->query("insert into ".DATATABLE."_order_cart_return(ReturnID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber) values(".$rid.",".$_SESSION['uinfo']['ucompany'].",".$in['cid'].",".$rvar['ContentID'].",'".$rvar['ContentName']."','".$rvar['ContentColor']."','".$rvar['ContentSpecification']."','".$rvar['Price_End']."',".$rvar['number'].")");
					$totalp = $totalp + $rvar['Price_End'] * $rvar['number'];
				}
			}
		}else{
			return 'error';
		}
		$db->query("update ".DATATABLE."_order_returninfo set ReturnTotal='".$totalp."' where ReturnID=".$rid." and ReturnCompany=".$_SESSION['uinfo']['ucompany']." limit 1");	
		return 'ok';
	}


/*******************************************************/
if($in['m']=="add_to_select_product")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$outidmsg = "";
	if(!empty($in['cartkid']))
	{
		if(!empty($in['selectid']))
		{
			$in_selectarr = explode(",", $in['selectid']);
		}else{
			$in_selectarr = null;
			$in['selectid'] = '';
		}

		for($i=0;$i<count($in['cartkid']);$i++)
		{
			if(!@in_array($in['cartkid'][$i], $in_selectarr))
			{
				$outidmsg .= $in['cartkid'][$i].",";
				$dataarr = unserialize(urldecode($in['cartdata_'.$in['cartkid'][$i]]));
				$dmsg .= '<tr id=\"line_'.$in['cartkid'][$i].'\"><td height=26>'.$dataarr['ContentID'].'<input type=\"hidden\" value=\"'.$in['cartkid'][$i].'\" name=\"cartkid[]\"  /><input type=\"hidden\" value=\"'.$in['cartdata_'.$in['cartkid'][$i]].'\" name=\"cartdata[]\"  /></td><td><a href=product_content.php?ID='.$dataarr['ContentID'].' target=_blank>'.$dataarr['ContentName'].'</a></td><td>'.$dataarr['ContentColor'].'&nbsp;</td><td>'.$dataarr['ContentSpecification'].'&nbsp;</td><td align=right>'.$dataarr['rnumber'].'</td><td align=right><input name=\"cart_num[]\" id=\"cart_num_'.$in['cartkid'][$i].'\" type=\"text\" size=\"6\" maxlength=\"6\"  onKeypress=\"if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;\" onfocus=\"this.select();\" style=\"text-align:right; width:50px;\" value=\"0\"  /></td><td align=right>¥ '.$dataarr['Price_End'].'</td><td align=right><a href=javascript:void(0) onclick=\"del_line_select_product(\'line_'.$in['cartkid'][$i].'\')\" >移除</a></td></tr>';
			}
		}

		$outidmsg = $outidmsg.$in['selectid'];
		$omsg .= '{"backtype":"ok", "htmldata":"'.$dmsg.'", "selectiddata":"'.$outidmsg.'"}';
	}else{
		$omsg .= '{"backtype":"empty!"}';
	}
	echo $omsg;
	exit();
}


exit('非法操作!');
?>