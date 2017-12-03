<?
class statdata
{	 
	//订单统计
	function statorder($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$statsql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=8 and OrderStatus!=9 group by left(OrderSN,8)";
		$result['order'] = $db->get_results($statsql);

		$statsql0  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus=0 group by left(OrderSN,8)";
		$result['order0'] = $db->get_results($statsql0);

		//$db->debug();
		return $result;
		unset($result);
	}

	//年订单
	function statorder_y($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$statsql  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus!=8 and OrderStatus!=9 group by left(OrderSN,6)";
		$result['order'] = $db->get_results($statsql);

		$statsql0  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus=0 group by left(OrderSN,6)";
		$result['order0'] = $db->get_results($statsql0);

		//$db->debug();
		return $result;
		unset($result);
	}

	//月订单
	function statorder_m($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$statsql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['mon']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus!=8 and OrderStatus!=9 group by left(OrderSN,8)";
		$result['order'] = $db->get_results($statsql);

		$statsql0  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['mon']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus=0 group by left(OrderSN,8)";
		$result['order0'] = $db->get_results($statsql0);

		//$db->debug();
		return $result;
		unset($result);
	}

	//日订单列表
	function statorder_d($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$datemsg = str_replace("-","",$in['cordate']);

		$statsql  = "SELECT OrderID,OrderSN,OrderTotal,OrderDate,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and left(OrderSN,8)='".$datemsg."' and OrderStatus!=8 and OrderStatus!=9 order by OrderID asc limit 0,1000";
		$result = $db->get_results($statsql);

		//$db->debug();
		return $result;
		unset($result);
	}


	//款项
	function statorder_finance($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$sqlunion = "";
		

		if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FinanceToDate between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
		$statsql0  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['cc']['ccompany']."  and FinanceClient=".$_SESSION['cc']['cid']." ".$sqlunion." and FinanceFlag=0 and (FinanceType='Z' OR FinanceType='O') ";
		$statdata0 = $db->get_row($statsql0);

		if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FROM_UNIXTIME(FinanceUpDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
		$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['cc']['ccompany']." and FinanceClient=".$_SESSION['cc']['cid']." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
		$statdata2 = $db->get_row($statsql2);

		if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FROM_UNIXTIME(OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
		$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." ".$sqlunion."  and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 ";
		$statdatat = $db->get_row($statsqlt);

		if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FROM_UNIXTIME(ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
		$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']." ".$sqlunion." and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) ";
		$statdata1 = $db->get_row($statsqlt1);

		if(!empty($statdatat))
		{
			if(empty($statdatat['Ftotal']))  $statdatat['Ftotal']  = 0;
			if(empty($statdatat0['Ftotal'])) $statdatat0['Ftotal'] = 0;
			if(empty($statdatat1['Ftotal'])) $statdatat1['Ftotal'] = 0;
			if(empty($statdatat2['Ftotal'])) $statdatat2['Ftotal'] = 0;
			
			$statdata['w']   = $statdatat['Ftotal'] - $statdata2['Ftotal'] - $statdata0['Ftotal'] - $statdata1['Ftotal'];
			$statdata['yin'] = $statdatat['Ftotal'] - $statdata1['Ftotal'];
			$statdata['y'] = $statdata2['Ftotal'];
			$statdata['t'] = $statdata0['Ftotal'];
			$statdata['a'] = $statdatat['Ftotal'];
			$statdata['r'] = $statdata1['Ftotal'];

			if(empty($statdata['w'])) $statdata['w'] = 0;
			if(empty($statdata['yin'])) $statdata['yin'] = 0;
			if(empty($statdata['y'])) $statdata['y'] = 0;
			if(empty($statdata['t'])) $statdata['t'] = 0;
			if(empty($statdata['a'])) $statdata['a'] = 0;
			if(empty($statdata['r'])) $statdata['r'] = 0;
		}

		//$db->debug();
		return $statdata;
		unset($statdata);
	}

	//往来对帐
	function showreconciliation($in)
	{
		$db	= dbconnect::dataconnect()->getdb();
		$sqlunion = "";
		
		$billdata = $db->get_results("select BillID,BillName from ".DATATABLE."_order_expense_bill where CompanyID=".$_SESSION['cc']['ccompany']." ");
		foreach($billdata as $var)
		{
			$billarr[$var['BillID']] = $var['BillName'];
		}

		if(!empty($in['begindate']))
		{
			   $sqlunion = " and FROM_UNIXTIME(FinanceUpDate) < '".$in['begindate']." 00:00:00' "; 		
				$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['cc']['ccompany']." and FinanceClient = ".$_SESSION['cc']['cid']." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
				$statdata2 = $db->get_row($statsql2);

			    $sqlunion = " and ExpenseDate < '".$in['begindate']."' "; 		
				$statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID = ".$_SESSION['cc']['cid']." ".$sqlunion." and FlagID = '2' ";
				$statdata4 = $db->get_row($statsql4);

				$sqlunion = " and FROM_UNIXTIME(OrderDate) < '".$in['begindate']." 00:00:00' "; 
				$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID   = ".$_SESSION['cc']['cid']." ".$sqlunion." and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 ";
				$statdatat = $db->get_row($statsqlt);
				
				$sqlunion = " and FROM_UNIXTIME(ReturnDate) < '".$in['begindate']." 00:00:00' ";
				$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient  = ".$_SESSION['cc']['cid']." ".$sqlunion." and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) ";
				$statdata1 = $db->get_row($statsqlt1);
				
				$begintotal = $statdatat['Ftotal'] - $statdata2['Ftotal'] - $statdata1['Ftotal'] - $statdata4['Ftotal'];
		}else{
				$begintotal = 0;
		}
		$darr['begin'] = $begintotal;

		$financesql   = "SELECT FinanceID,FinanceOrder,FinanceTotal,FinanceUpDate FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['cc']['ccompany']." and FinanceClient = ".$_SESSION['cc']['cid']." and FROM_UNIXTIME(FinanceUpDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') Order by FinanceID ASC";
		$finance_data = $db->get_results($financesql);

		$expensesql   = "SELECT ExpenseID,ClientID,BillID,ExpenseTotal,ExpenseDate,ExpenseTime FROM ".DATATABLE."_order_expense where CompanyID = ".$_SESSION['cc']['ccompany']." and ClientID = ".$_SESSION['cc']['cid']." and ExpenseDate between '".$in['begindate']."' and '".$in['enddate']."' and FlagID='2' Order by ExpenseID ASC";
		$expense_data = $db->get_results($expensesql);

		$ordersql   = "SELECT OrderID,OrderSN,OrderTotal,OrderIntegral,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['cc']['ccompany']." and OrderUserID   = ".$_SESSION['cc']['cid']." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 Order by OrderID ASC";
		$order_data = $db->get_results($ordersql);

		$returnsql   = "SELECT ReturnID,ReturnSN,ReturnOrder,ReturnTotal,ReturnDate FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['cc']['ccompany']." and ReturnClient  = ".$_SESSION['cc']['cid']." and FROM_UNIXTIME(ReturnDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) Order by ReturnID ASC";
		$return_data = $db->get_results($returnsql);

		if(empty($finance_data)) $finance_data[0]['FinanceID'] = 0;
		if(empty($order_data))   $order_data[0]['OrderID'] = 0;
		if(empty($return_data))  $return_data[0]['ReturnID'] = 0;
		if(empty($expense_data))  $expense_data[0]['ExpenseID'] = 0;

		if(empty($in['tid']))
		{
			$dataarr = array_merge($finance_data, $order_data, $return_data, $expense_data);
		}else{
			if($in['tid'] == "O") $dataarr = $order_data;
			if($in['tid'] == "F") $dataarr = $finance_data;
			if($in['tid'] == "E") $dataarr = $expense_data;
			if($in['tid'] == "R") $dataarr = $return_data;
		}
		if(!empty($dataarr))
		{
			foreach($dataarr as $dv)
			{
				if(!empty($dv['FinanceID']))
				{
					$key = $dv['FinanceUpDate'];
					$larr['atype']  = "付款单";
					$larr['SN']     = "F".date("Ymd",$dv['FinanceUpDate'])."-".$dv['FinanceID'];
					$larr['Date']   = date("Y-m-d",$dv['FinanceUpDate']);
					$larr['Total']  = $dv['FinanceTotal'];
					$larr['TotalType'] = "-";
					$larr['LinkUrl'] = "finance.php?m=content&ID=".$dv['FinanceID'];

				}elseif(!empty($dv['ExpenseID'])){

					$key = strtotime($dv['ExpenseDate']." ".date("H:i:s",$dv['ExpenseTime']));
					$larr['atype'] = "其他款项 - ".$billarr[$dv['BillID']];
					$larr['SN']    = "E".date("Ymd",$dv['ExpenseTime'])."-".$dv['ExpenseID'];
					$larr['Date']  = $dv['ExpenseDate'];
					$larr['Total'] = $dv['ExpenseTotal'];
					$larr['TotalType'] = "-";
					$larr['LinkUrl'] = "finance.php?m=expense";

				}elseif(!empty($dv['OrderID'])){

					$key = $dv['OrderDate'];
					$larr['atype']   = "订单";
					$larr['SN']      = $dv['OrderSN'];
					$larr['Date']    = date("Y-m-d",$dv['OrderDate']);
					$larr['Total']   = $dv['OrderTotal'];
					$larr['TotalType'] = "+";
					$larr['LinkUrl']   = "myorder.php?m=showorder&id=".$dv['OrderID'];

				}else{
					$key = $dv['ReturnDate'];
					$larr['atype'] = "退货单";
					$larr['SN']    = $dv['ReturnSN'];
					$larr['Date']  = date("Y-m-d",$dv['ReturnDate']);
					$larr['Total'] = $dv['ReturnTotal'];
					$larr['TotalType'] = "-";
					$larr['LinkUrl']   = "return.php?m=showreturn&id=".$dv['ReturnID'];

				}
				$darr[$key] = $larr;
			}
			ksort($darr);
		}
		//$db->debug();
		return $darr;
		unset($darr);
	}

	//商品
	function statorder_product($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$statsql  = "SELECT sum(ContentNumber) as cnum,c.ContentID,c.ContentName,sum(c.ContentNumber * c.ContentPrice * c.ContentPercent / 10) as amount from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.OrderUserID=".$_SESSION['cc']['cid']." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ContentID order by cnum desc";
		$result['order'] = $db->get_results($statsql);

		$statsqlr  = "SELECT sum(ContentNumber) as cnum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.ReturnClient=".$_SESSION['cc']['cid']." and FROM_UNIXTIME(o.ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus=5) group by c.ContentID order by cnum desc";
		$result['return'] = $db->get_results($statsqlr);

		//$db->debug();
		return $result;
		unset($result);
	}


	//颜色规格明细
	function statorder_product_cs($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$productinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['cc']['ccompany']." and ID=".intval($in['ID'])." limit 0,1");
		if($in['stype']=="color")
		{
			$statsql  = "SELECT sum(c.ContentNumber) as cnum,c.ContentID,c.ContentName,c.ContentColor as ContentCS from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.OrderUserID=".$_SESSION['cc']['cid']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59'  and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ContentColor ";

			$statsqlr  = "SELECT sum(c.ContentNumber) as rnum,c.ContentID,c.ContentName,c.ContentColor as ContentCS from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.ReturnClient=".$_SESSION['cc']['cid']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus=5) group by c.ContentColor";

		}else{
			$statsql  = "SELECT sum(c.ContentNumber) as cnum,c.ContentID,c.ContentName,c.ContentSpecification as ContentCS from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.OrderUserID=".$_SESSION['cc']['cid']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ContentSpecification ";

			$statsqlr  = "SELECT sum(c.ContentNumber) as rnum,c.ContentID,c.ContentName,c.ContentSpecification as ContentCS from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.ReturnClient=".$_SESSION['cc']['cid']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus=5) group by c.ContentSpecification";
		}

		$result['order']  = $db->get_results($statsql);
		$result['return'] = $db->get_results($statsqlr);
		$result['pinfo']  = $productinfo;

		//$db->debug();
		return $result;
		unset($result);
	}

	//订单列表
	function statorder_product_all($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$productinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['cc']['ccompany']." and ID=".intval($in['ID'])." limit 0,1");

		$statsql  = "SELECT c.ContentNumber,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.OrderUserID=".$_SESSION['cc']['cid']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9";

		$statsqlr  = "SELECT c.ContentNumber,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.CompanyID=".$_SESSION['cc']['ccompany']." and o.ReturnClient=".$_SESSION['cc']['cid']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus=5)";

		$result['order']  = $db->get_results($statsql);
		$result['return'] = $db->get_results($statsqlr);
		$result['pinfo']  = $productinfo;

		//$db->debug();
		return $result;
		unset($result);
	}

	//退货单列表
	function statreturn($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$statsql  = "SELECT left(ReturnSN,9) as ODate,sum(ReturnTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient=".$_SESSION['cc']['cid']." and FROM_UNIXTIME(ReturnDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) group by left(ReturnSN,9)";
		$result = $db->get_results($statsql);

		//$db->debug();
		return $result;
		unset($result);
	}


//END
}
?>