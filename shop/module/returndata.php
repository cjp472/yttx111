<?
class returndata
{
	//退单列表
	function showreturntype()
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sql_c	= "SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['cc']['ccompany']." and SetName='product' limit 0,1";
		$rstype = $db->get_row($sql_c);
		if(!empty($rstype['SetValue'])) $valuearr = unserialize($rstype['SetValue']);
		if(!empty($valuearr['return_type']) && $valuearr['return_type']=="order"){
			return 'order';
		}else{
			return 'product';
		}		
	}

	//退单列表
	function listreturn($status,$ps=12,$lurl='myorder.php')
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$smsg  = "";
		if(empty($ps)) $ps = 12;

		if($status != "")
		{
			$status = intval($status);
			$smsg = " and ReturnStatus=".$status." ";
		}
		$orderbymsg = " ORDER BY ReturnID DESC";

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']."  ".$smsg." ";
		$sql_l = "select ReturnID,ReturnSN,ReturnOrder,ReturnSendType,ReturnProductW,ReturnProductB,ReturnAbout,ReturnTotal,ReturnStatus,ReturnDate,ReturnType from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']." ".$smsg." ".$orderbymsg;
		
		$rs      = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		    = $rs['allrow'];
        $page->LinkAry		= array("status"=>$status,"ps"=>$ps);
        
		$result['list']			    = $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink($lurl);

		for($j=0;$j<count($result['list']);$j++)
		{
			$result['list'][$j]['goods'] = $db->get_results("select ContentID,ContentName from ".DATATABLE."_order_cart_return where ReturnID=".$result['list'][$j]['ReturnID']." order by ID asc limit 0,3");
		}

		//$db->debug();
		return $result;
		unset($result);
	}

	//退单详细
	function showreturn($id)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		$id = intval($id);

		$sql_o = "select * from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']." and ReturnID='".$id."' limit 0,1";
		$orderinfo    = $db->get_row($sql_o);

		$sql_c = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where ReturnID=".$orderinfo['ReturnID']." and CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." order by ID asc";
		$ordercart	= $db->get_results($sql_c);
		
		$TotalPrice = 0;
		$TotalNumber = 0;
		for($j=0;$j<count($ordercart);$j++)
		{
			$ordercart[$j]['notetotal'] = $ordercart[$j]['ContentNumber'] * $ordercart[$j]['ContentPrice'];
			$TotalPrice  = $TotalPrice + $ordercart[$j]['notetotal'];
			$TotalNumber = $TotalNumber + $ordercart[$j]['ContentNumber'];
		}

		$TotalPrice = sprintf("%01.2f", round($TotalPrice,2));
		$result['orderinfo']   = $orderinfo;
		$result['ordercart']   = $ordercart;
		$result['totalprice']  = $TotalPrice;
		$result['totalnumber'] = $TotalNumber;

		//$db->debug();
		return $result;
		unset($result);
	}

	//取操作记录
	function listsubmit($oid)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$oid   = intval($oid);

		$sql_l  = "select AdminUser,Name,Date,Status,Content from ".DATATABLE."_order_returnsubmit where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$oid." order by ID DESC";       
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}

	//退单详细
	function getcartproduct($sn)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$ordercart = null;

		$sql_o = "select OrderID,OrderSN,OrderSendStatus,OrderPayStatus,OrderTotal,OrderStatus,OrderDate,OrderType from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderSN='".$sn."' limit 0,1";
		$orderinfo    = $db->get_row($sql_o);

		if($orderinfo['OrderStatus'] > 2 && $orderinfo['OrderStatus'] < 8 && $orderinfo['OrderSendStatus'] > 2)
		{
			$orderinfo['return'] = 'ok';
		}else{
			$orderinfo['return'] = '';
			return $orderinfo;
		}

		if(!empty($orderinfo['OrderID']))
		{
			$sql_c = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$orderinfo['OrderID']." order by ID asc";
			$ordercart	= $db->get_results($sql_c);
		
			$sql_cr = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['cc']['ccompany']." and ReturnID in (select ReturnID from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']." and ReturnOrder='".$orderinfo['OrderSN']."' and ReturnStatus!=1 and ReturnStatus!=8 and ReturnStatus!=9) order by ID asc";
			$returncart	= $db->get_results($sql_cr);
			$returnarr = null;
			if(!empty($returncart))
			{
				foreach($returncart as $rc)
				{
					$kid = commondata::make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
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
				$kid = commondata::make_kid($ordercart[$j]['ContentID'], $ordercart[$j]['ContentColor'], $ordercart[$j]['ContentSpecification']);
				if(!empty($returnarr[$kid]))
				{
					$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentNumber'] - $returnarr[$kid];
				}else{
					$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentNumber'];
				}
				$ordercart[$j]['Price_End']    = $ordercart[$j]['ContentPrice'] * $ordercart[$j]['ContentPercent'] / 10;			
				$arrcart[$ordercart[$j]['ID']] = $ordercart[$j];
			}
		}

		$result['orderinfo']   = $orderinfo;
		$result['ordercart']   = $arrcart;
		$result['return']      = $orderinfo['return'];

		//$db->debug();
		return $result;
		unset($result);
	}	

	//退单详细
	function showorder($id)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		$ordercart = null;

		$sql_o = "select OrderID,OrderSN,OrderSendStatus,OrderPayStatus,OrderTotal,OrderStatus,OrderDate,OrderType from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderSN='".$id."' limit 0,1";
		$orderinfo    = $db->get_row($sql_o);

		if($orderinfo['OrderStatus'] > 2 && $orderinfo['OrderStatus'] < 8 && $orderinfo['OrderSendStatus'] > 2)
		{
			$orderinfo['return'] = 'ok';
		}else{
			$orderinfo['return'] = '';
		}

		if(!empty($orderinfo['OrderID']))
		{
			$sql_c = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$orderinfo['OrderID']." order by ID asc";
			$ordercart	= $db->get_results($sql_c);

			$sql_cr = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['cc']['ccompany']." and ReturnID in (select ReturnID from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']." and ReturnOrder='".$orderinfo['OrderSN']."' and ReturnStatus!=1 and ReturnStatus!=8 and ReturnStatus!=9) order by ID asc";
			$returncart	= $db->get_results($sql_cr);
			$returnarr = null;
			if(!empty($returncart))
			{
				foreach($returncart as $rc)
				{
					$kid = commondata::make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
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
				$kid = commondata::make_kid($ordercart[$j]['ContentID'], $ordercart[$j]['ContentColor'], $ordercart[$j]['ContentSpecification']);
				if(!empty($returnarr[$kid]))
				{
					$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentNumber'] - $returnarr[$kid];
				}else{
					$ordercart[$j]['rnumber'] = $ordercart[$j]['ContentNumber'];
				}
				$ordercart[$j]['Price_End'] = $ordercart[$j]['ContentPrice'] * $ordercart[$j]['ContentPercent'] / 10;
				$ordercart[$j]['notetotal'] = $ordercart[$j]['ContentNumber'] * $ordercart[$j]['Price_End'];
			}
		}

		$result['orderinfo']   = $orderinfo;
		$result['ordercart']   = $ordercart;
		$result['return']      = $orderinfo['return'];
		//$db->debug();
		return $result;
		unset($result);
	}	

	//插入退货数据
	function showproduct($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		
		$oinfo = $db->get_results("SELECT c.ContentID,c.ContentColor,c.ContentSpecification,c.ContentName,c.ContentNumber,c.ContentPercent,c.ContentPrice,i.Coding FROM ".DATATABLE."_order_orderinfo o inner join ".DATATABLE."_order_cart c on o.OrderID=c.OrderID inner join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID where o.OrderCompany = ".$_SESSION['cc']['ccompany']." and o.OrderUserID=".$_SESSION['cc']['cid']." and o.OrderSendStatus=4 and (c.ContentName like '%".$in['kw']."%' OR CONCAT(i.Coding,i.Barcode,i.Pinyi) like '%".$in['kw']."%') order by c.ID asc limit 0,2000");

		if(!empty($oinfo))
		{
			$idarr = null;
			foreach($oinfo as $ovar)
			{
				if(!@in_array($ovar['ContentID'],$idarr))
				{
					$idarr[] = $ovar['ContentID'];
				}
				$kid = commondata::make_kid($ovar['ContentID'], $ovar['ContentColor'], $ovar['ContentSpecification']);
				
				if(empty($cartarr[$kid]))
				{
					$cartarr[$kid] = $ovar;
					$cartarr[$kid]['onumber'] = $ovar['ContentNumber'];
				}else{
					$cartarr[$kid]['onumber'] = $cartarr[$kid]['onumber']+$ovar['ContentNumber'];
				}
			}
			$cidmsg = implode(",", $idarr);
			if(strpos($cidmsg, ",")) $insqlmsg = " and r.ContentID in (".$cidmsg.") "; else $insqlmsg = " and r.ContentID = ".intval($cidmsg)." ";

			$sql_cr = "select r.ContentID,r.ContentName,r.ContentColor,r.ContentSpecification,r.ContentNumber from ".DATATABLE."_order_cart_return r left join ".DATATABLE."_order_returninfo i on r.ReturnID=i.ReturnID where i.ReturnCompany=".$_SESSION['cc']['ccompany']." and i.ReturnClient=".$_SESSION['cc']['cid']." and i.ReturnStatus!=1 and i.ReturnStatus!=8 and i.ReturnStatus!=9 ".$insqlmsg." order by r.ID desc";
			$returncart	= $db->get_results($sql_cr);
			$returnarr  = null;
			if(!empty($returncart))
			{
				foreach($returncart as $rc)
				{
					$kid = commondata::make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
					if(empty($returnarr[$kid]))
					{
						$returnarr[$kid] = $rc['ContentNumber'];
					}else{
						$returnarr[$kid] = $returnarr[$kid]+$rc['ContentNumber'];
					}
				}
			}
					
			foreach($cartarr as $ckey=>$cvar)
			{
				if(!empty($returnarr[$ckey]))
				{
					$cartarr[$ckey]['rnumber'] = $cartarr[$ckey]['onumber'] - $returnarr[$ckey];
				}else{
					$cartarr[$ckey]['rnumber'] = $cartarr[$ckey]['onumber'];
				}
				$cartarr[$ckey]['Price_End'] = $cartarr[$ckey]['ContentPrice']*$cartarr[$ckey]['ContentPercent']*0.1;
			}
			return $cartarr;
		}

	return '';
	}


	//插入退货数据
	function insertintoreturncart($in,$orderc,$returnarr)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$totalp = 0;
		$rssn   = $db->get_row("select ReturnID,ReturnSN from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." order by ReturnID desc limit 0,1");
		if(empty($rssn['ReturnSN']))
		{
			$ReturnSN = "R".date("Ymd")."-1";
		}else{
			$nextid	  = intval(substr($rssn['ReturnSN'],strpos($rssn['ReturnSN'], '-')+1))+1;
			$ReturnSN = "R".date("Ymd")."-".$nextid;
		}
		$isinr = $db->query("insert into ".DATATABLE."_order_returninfo(ReturnSN,ReturnOrder,ReturnCompany,ReturnClient,ReturnSendType,ReturnSendAbout,ReturnProductW,ReturnProductB,ReturnPicture,ReturnAbout,ReturnDate) values('".$ReturnSN."','".$orderc['orderinfo']['OrderSN']."',".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",'".$in['ReturnSendType']."','".$in['ReturnSendAbout']."','".$in['ReturnProductW']."','".$in['ReturnProductB']."','".$in['ReturnPicture']."','".$in['ReturnAbout']."',".time().")");
		
		if($isinr)
		{
			$rid = mysql_insert_id();
			$cartd = $orderc['ordercart'];
			foreach($returnarr as $rkey=>$rvar)
			{
				if(!empty($rvar))
				{
					$db->query("insert into ".DATATABLE."_order_cart_return(ReturnID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber) values(".$rid.",".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",".$cartd[$rkey]['ContentID'].",'".$cartd[$rkey]['ContentName']."','".$cartd[$rkey]['ContentColor']."','".$cartd[$rkey]['ContentSpecification']."','".$cartd[$rkey]['Price_End']."',".$rvar.")");
					$totalp = $totalp + $cartd[$rkey]['Price_End']*$rvar;
				}
			}
		}else{
			return 'error';
		}
		$db->query("update ".DATATABLE."_order_returninfo set ReturnTotal='".$totalp."' where ReturnID=".$rid." and ReturnCompany=".$_SESSION['cc']['ccompany']." limit 1");	
		return 'ok';
	}

	//插入退货数据-产品
	function insertintoreturncart_product($in,$returnarr)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$totalp = 0;
		$rssn   = $db->get_row("select ReturnID,ReturnSN from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." order by ReturnID desc limit 0,1");
		if(empty($rssn['ReturnSN']))
		{
			$ReturnSN = "R".date("Ymd")."-1";
		}else{
			$nextid	  = intval(substr($rssn['ReturnSN'],strpos($rssn['ReturnSN'], '-')+1))+1;
			$ReturnSN = "R".date("Ymd")."-".$nextid;
		}
		$isinr = $db->query("insert into ".DATATABLE."_order_returninfo(ReturnSN,ReturnOrder,ReturnCompany,ReturnClient,ReturnSendType,ReturnSendAbout,ReturnProductW,ReturnProductB,ReturnPicture,ReturnAbout,ReturnDate) values('".$ReturnSN."','',".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",'".$in['ReturnSendType']."','".$in['ReturnSendAbout']."','".$in['ReturnProductW']."','".$in['ReturnProductB']."','".$in['ReturnPicture']."','".$in['ReturnAbout']."',".time().")");
		
		if($isinr)
		{
			$rid = mysql_insert_id();
			$cartd = $orderc['ordercart'];
			foreach($returnarr as $rkey=>$rvar)
			{
				if(!empty($rvar))
				{
					$db->query("insert into ".DATATABLE."_order_cart_return(ReturnID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber) values(".$rid.",".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",".$rvar['ContentID'].",'".$rvar['ContentName']."','".$rvar['ContentColor']."','".$rvar['ContentSpecification']."','".$rvar['Price_End']."',".$rvar['number'].")");
					$totalp = $totalp + $rvar['Price_End']*$rvar['number'];
				}
			}
		}else{
			return 'error';
		}
		$db->query("update ".DATATABLE."_order_returninfo set ReturnTotal='".$totalp."' where ReturnID=".$rid." and ReturnCompany=".$_SESSION['cc']['ccompany']." limit 1");	
		return 'ok';
	}


	//取消退单
	function cancelorder($oid,$content)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sql_l  = "update ".DATATABLE."_order_returninfo set ReturnStatus=8 where ReturnID=".$oid." and ReturnCompany = ".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']." and ReturnStatus=0 ";
		
		$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['cc']['ccompany'].", ".$oid.", '".$_SESSION['cc']['cusername']."', '".$_SESSION['cc']['ctruename']."',".time().", '客户取消退单', '".$content."')";

		$resultstatus	= $db->query($sql_l);
		if($resultstatus)
		{
			$db->query($sqlin);
			$status = true;
		}else{
			$status = false;
		}
		return $status;
	}

	//保存留言
	function save_guestbook($id,$content)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sqlin = "insert into ".DATATABLE."_order_returnsubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['cc']['ccompany'].", ".$id.", '".$_SESSION['cc']['cusername']."', '".$_SESSION['cc']['ctruename']."',".time().", '客户留言', '".$content."')";
		$status = $db->query($sqlin);
		if($status)
		{
			return true;
		}else{
			return false;
		}
	}

	function get_row_cartbak($oid)
	{	
		$db    = dbconnect::dataconnect()->getdb();		

		$binfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_return_cartbak where CompanyID = ".$_SESSION['cc']['ccompany']." and OrderID=".$oid." limit 0,1");

		//$db->debug();
		return $binfo['allrow'];
		unset($binfo);
	}

	function show_cartproduct($oid)
	{
		$db    = dbconnect::dataconnect()->getdb();		

		$result = $db->get_row("SELECT ID,Content FROM ".DATATABLE."_order_return_cartbak where CompanyID = ".$_SESSION['cc']['ccompany']." and OrderID=".$oid." limit 0,1");
		if(!empty($result['Content']))
		{
			$cartdata = $db->get_results("select * from ".DATATABLE."_order_cart_return  where CompanyID=".$_SESSION['cc']['ccompany']." and ReturnID=".$oid." order by ID asc");
			foreach($cartdata as $var)
			{
				$cartarr[$var['ID']] = $var;
			}
			
			$redata = unserialize($result['Content']);
			foreach($redata as $rv)
			{
				$resultdata['cart'][$rv['ID']] = $rv;
				$resultdata['cart'][$rv['ID']]['NewNumber'] = $cartarr[$rv['ID']]['ContentNumber'];
				$resultdata['cart'][$rv['ID']]['NewPrice']		 = $cartarr[$rv['ID']]['ContentPrice'];
			}
		}	

		//$db->debug();
		return $resultdata;
		unset($resultdata);
	}

//END
}

$return_status_arr = array(
		'0'			=>  '待审核',
		'1'			=>  '未通过',
		'2'			=>  '已审核',
		'3'			=>  '已收货',
		'5'         =>  '已完成',
		'8'         =>  '客户取消',
		'9'         =>  '管理员取消',
);
?>