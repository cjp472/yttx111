<?php
class orderdata
{	 
	//订单列表
	function listorder($in,$ps=12,$lurl='myorder.php')
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		if(empty($ps)) $ps = 12;

		if(isset($in['status']) && $in['status'] != "")
		{
			$smsg = " and a.OrderStatus=".$in['status']." ";
			$in['pid'] = '';
			$in['fid'] = '';
		}elseif(isset($in['pid']) && $in['pid'] != ""){
			$smsg = " and a.OrderPayStatus=".$in['pid']." ";
			$in['status'] = '';
			$in['fid'] = '';
		}elseif(isset($in['fid']) && $in['fid'] != ""){
			$smsg = " and a.OrderSendStatus=".$in['fid']." ";
			$in['pid'] = '';
			$in['status'] = '';
		}
        if(!empty($in['kw'])){
            switch($in['stype']){
                case 'ordersn':
                    $smsg .= "and a.OrderSN='".$in['kw']."'";
                    break;
                case 'productname':
                    $smsg .= " and a.OrderID IN (SELECT OrderID FROM ".DATATABLE."_view_index_cart WHERE CompanyID = ".$_SESSION['cc']['ccompany']." AND CONCAT(Name,Pinyi,Coding,Barcode) LIKE '%".$in['kw']."%') ";
                    break;
                case 'giftsname':
                    $smsg .= " and a.OrderID IN (SELECT OrderID FROM  ".DATATABLE."_view_index_gifts WHERE CompanyID = ".$_SESSION['cc']['ccompany']." AND CONCAT(Name, Pinyi, Coding, Barcode) LIKE '%".$in['kw']."%') ";
                    break;
                case 'receiveName': // 收货人
                    $smsg .= " AND a.OrderReceiveName like '%".$in['kw']."%'";
                    break;
                case 'receiveAdd': // 收货地址
                    $smsg .= " AND a.OrderReceiveAdd like '%".$in['kw']."%'";
                    break;
                default:
                    break;
            }
        }
        if(!empty($in['sdate'])){
            $smsg .= " and a.OrderDate>=".strtotime(date("Y-m-d 00:00:00",strtotime($in['sdate'])));
        }
        if(!empty($in['edate'])){
            $smsg .= " and a.OrderDate<=".strtotime(date("Y-m-d 23:59:59",strtotime($in['edate'])));
        }
        if(!empty($in['collect'])){
            $smsg .= " AND a.OrderCollect='".$in['collect']."'";
        }
		$orderbymsg = " ORDER BY OrderID DESC";

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']."  ".$smsg." ";
		$sql_l = "select OrderID,OrderSN,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,DeliveryDate,OrderRemark,OrderTotal,OrderStatus,OrderDate,OrderType,OrderCollect,OrderSpecial,ReturnID from ".DATATABLE."_order_orderinfo a left join ".DATATABLE."_order_returninfo b on a.ordersn= b.returnorder where a.OrderCompany=".$_SESSION['cc']['ccompany']." and a.OrderUserID=".$_SESSION['cc']['cid']." ".$smsg." ".$orderbymsg;
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize			= $ps;
        $page->Total			= $rs['allrow'];
        $page->LinkAry		    = array(
            "status"=>$in['status'],
            "ps"=>$ps,
            "pid"=>$in['pid'],
            "fid"=>$in['fid'],
            "kw"=>$in["kw"],
            "stype"=>$in["stype"],
            "sdate"=>$in['sdate'],
            "edate"=>$in['edate'],
            "collect"=>$in['collect'],
        );
        
		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink($lurl);

		for($j=0;$j<count($result['list']);$j++)
		{
			if($result['list'][$j]['OrderStatus'] > 2 && $result['list'][$j]['OrderStatus'] < 8 && $result['list'][$j]['OrderSendStatus'] > 2)
			{
				$result['list'][$j]['return'] = 'ok';
			}else{
				$result['list'][$j]['return'] = '';
			}
			$result['list'][$j]['goods'] = $db->get_results("select ContentID,ContentName from ".DATATABLE."_order_cart where OrderID=".$result['list'][$j]['OrderID']." order by ID asc limit 0,3");
		}

		//$db->debug();
		return $result;
		unset($result);
	}

	//订单详细
	function getorderinfo($sn)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		if(strpos($sn,'-')) $sqlmsg = " OrderSN = '".$sn."' "; else $sqlmsg = " OrderID = ".intval($sn)." ";
		$sql_o = "select * from ".DATATABLE."_order_orderinfo where ".$sqlmsg." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." limit 0,1";
		$orderinfo    = $db->get_row($sql_o);

		return $orderinfo;
		unset($orderinfo);
	}

	//付款记录
	function getpaylist($oinfo)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sqlmsg	= " and (FinanceOrderID = ".$oinfo['OrderID']." OR FinanceOrder like '%".$oinfo['OrderSN']."%') ";
		$datasql   = "SELECT * FROM ".DATATABLE."_order_finance where FinanceClient=".$oinfo['OrderUserID']." and FinanceCompany = ".$_SESSION['cc']['ccompany']." ".$sqlmsg." Order by FinanceID ASC";
		$list_data = $db->get_results($datasql);

		return $list_data;
		unset($list_data);
	}


	//订单详细
	function showorder($id)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$smsg   = "";
		$id     = intval($id);

		$sql_o = "select * from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderID=".$id." limit 0,1";
		$orderinfo    = $db->get_row($sql_o);

		$sql_c = "select CommendID,BrandID,Model,Coding,Picture,Units,ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$orderinfo['OrderID']." order by SiteID asc,ID asc";
		$ordercart	= $db->get_results($sql_c);
		//获取去厂家  by zjb 20160623
		$brandsql   = "SELECT * FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['cc']['ccompany']." ORDER BY BrandPinYin ASC";
		$brandsql_data = $db->get_results($brandsql);
		foreach ($brandsql_data as $val){
		    $brandsqlarr[$val['BrandID']] = $val;
		}

		$TotalPrice  = 0;
		$TotalNumber = 0;
		for($j=0;$j<count($ordercart);$j++)
		{
			$ordercart[$j]['Price_End'] = $ordercart[$j]['ContentPrice'] * $ordercart[$j]['ContentPercent'] / 10;
			$ordercart[$j]['notetotal'] = $ordercart[$j]['ContentNumber'] * $ordercart[$j]['Price_End'];
			$TotalPrice  = $TotalPrice + $ordercart[$j]['notetotal'];
			$TotalNumber = $TotalNumber + $ordercart[$j]['ContentNumber'];
			
			//匹配 厂家
			$ordercart[$j]['BrandName'] = $brandsqlarr[$ordercart[$j]['BrandID']]['BrandName'];
		}

		
		
        $totalPure = sprintf("%01.2f" , round($TotalPrice,2));//商品总金额

        $stair_after = sprintf("%01.2f",$orderinfo['OrderTotal'] / (1+ $orderinfo['InvoiceTax'] / 100));

        $stair_count = $TotalPrice - $stair_after;

		if($orderinfo['InvoiceType'] == 'P' || $orderinfo['InvoiceType'] == 'Z'){
			$sql_i = "select InvoiceType,AccountName,BankName,BankAccount,InvoiceHeader,InvocieContent,TaxpayerNumber,InvoiceDate,InvoiceFlag,InvoiceSendDate from ".DATATABLE."_order_invoice where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$orderinfo['OrderID']." order by InvoiceID desc";
			$result['invoice']	= $db->get_row($sql_i);
		}

		$TotalPrice = sprintf("%01.2f", round($TotalPrice,2));
		$result['orderinfo']   = $orderinfo;
		$result['ordercart']   = $ordercart;
		$result['totalprice']  = $TotalPrice;
		$result['totalnumber'] = $TotalNumber;
        $result['stair_count'] = $stair_count;//满省金额
        $result['totalpure'] = $totalPure;

		$result['totaltax']  = $stair_after * $orderinfo['InvoiceTax'] / 100;
		$result['totaltax']  = 	sprintf("%01.2f", round($result['totaltax'],2));	
		//$db->debug();
		return $result;
		unset($result);
	}

	//赠品详细
	function showordergifts($id)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		$id    = intval($id);

		$sql_c = "select CommendID,Coding,Picture,Units,ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_view_index_gifts where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$id." order by SiteID asc,ID asc";
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
		$oid    = intval($oid);

		$sql_l  = "select AdminUser,Name,Date,Status,Content from ".DATATABLE."_order_ordersubmit where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$oid." order by ID DESC";       
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}	

	//取状态
	function statusarr($ty="sendtype", $needAbout = true)
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sql_l  = "select TypeID,TypeName,TypeAbout from ".DATABASEU.DATATABLE."_order_".$ty." order by TypeID asc limit 0,50";
		$result	= $db->get_results($sql_l);

		for($j=0;$j<count($result);$j++)
		{
			if($needAbout){
				$resultarr[$result[$j]['TypeID']] = $result[$j]['TypeName']." (".$result[$j]['TypeAbout'].")";
			}else{
				$resultarr[$result[$j]['TypeID']] = $result[$j]['TypeName'];
			}
		}

		//$db->debug();
		return $resultarr;
		unset($result);
		unset($resultarr);
	}

	//取消订单
	function cancelorder($oid,$content)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$oid   = intval($oid);

		$sql_l  = "update ".DATATABLE."_order_orderinfo set OrderStatus=8 where OrderID=".$oid." and OrderCompany = ".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderStatus=0 ";
		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['cc']['ccompany'].", ".$oid.", '".$_SESSION['cc']['cusername']."', '".$_SESSION['cc']['ctruename']."',".time().", '客户取消订单', '".$content."')";
		
		
		//读取订单金额,如果是账期支付[12]，则退款
		$tSql = "select OrderSN,OrderPayType,OrderIntegral from ".DATATABLE."_order_orderinfo where OrderID=".$oid." and OrderCompany = ".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderStatus=0 ";
		$tresult = $db->get_row($tSql);
		
		if($tresult['OrderPayType'] == 12){//账期支付，退款
			client::MainUpdate('', -$tresult['OrderIntegral']);
			$credit = new Credit();
			$credit->reFundLog($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid'], $tresult['OrderIntegral'], $oid, true);
		}
		
		$resultstatus	= $db->query($sql_l);
		if($resultstatus)
		{
			$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['cc']['ccompany']." and SetName='product' limit 0,1");
			if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);

			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$sql     = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".DATATABLE."_order_cart where OrderID=".$oid." and CompanyID=".$_SESSION['cc']['ccompany']." and ClientID = ".$_SESSION['cc']['cid']." ";
				$data_c = $db->get_results($sql);
				
				$fp = array('+','/','=','_');
				$rp = array('-','|','DHB',' ');

				$tykey = str_replace($fp,$rp,base64_encode("统一"));
				foreach($data_c as $dvar)
				{
					$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$dvar['ContentID']." limit 1");
					
					if(!empty($dvar['ContentColor']) || !empty($dvar['ContentSpecification']))
					{
						if(empty($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
						if(empty($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
						$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
					}
					$dnumber = intval("-".$dvar['ContentNumber']);
					$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['cc']['ccompany']},{$dvar['ContentID']},{$oid},{$dnumber},'cancel')");
				}

				$sql     = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".DATATABLE."_order_cart_gifts where OrderID=".$oid." and CompanyID=".$_SESSION['cc']['ccompany']." and ClientID = ".$_SESSION['cc']['cid']." ";
				$data_g = $db->get_results($sql);
				if(!empty($data_g))
				{
					$tykey = str_replace($fp,$rp,base64_encode("统一"));
					foreach($data_g as $dvar)
					{
						$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$dvar['ContentID']." limit 1");
						
						if(!empty($dvar['ContentColor']) || !empty($dvar['ContentSpecification']))
						{
							if(empty($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
							if(empty($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
							$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
						}
						$dnumber = intval("-".$dvar['ContentNumber']);
						$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['cc']['ccompany']},{$dvar['ContentID']},{$oid},{$dnumber},'cancel')");
					}
				}
			}
			$db->query($sqlin);
			if($tresult['OrderPayType'] == 12){//账期支付，退款
				$sql_tui = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['cc']['ccompany'].", ".$oid.", '".$_SESSION['cc']['cusername']."', '".$_SESSION['cc']['ctruename']."',".time().", '账期退款', '退款金额：".$tresult['OrderIntegral']."')";
				$db->query($sql_tui);
				
				//发送短信
				$sms = new SmsApp();
				$sms->getSmsTpl('YTZQTUIKUAN')
					->bulidContent(array('{CLIENTNAME}', '{ORDERSN}', '{ORDERTOTAL}'), array($_SESSION['cc']['ccompanyname'], $tresult['OrderSN'], $tresult['OrderIntegral']))
					->SendSMS(YAPI_RECEIVE_PHONE)
					->logStatus($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);
			}
			$status = true;
		}else{
			$status = false;
		}
		return $status;
	}


	//订过的商品
	function orderproduct($o='',$t='imglist',$ps=18)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		
		if(empty($ps)) $ps = 18;
		if(empty($o))
		{
			$orderbymsg = " order by c.ID DESC";
		}elseif($o=="1"){
			$orderbymsg = " order by i.Price2 DESC";
		}elseif($o=="2"){
			$orderbymsg = " order by i.Price2 ASC";
		}elseif($o=="3"){
			$orderbymsg = " order by i.ID DESC";
		}elseif($o=="4"){
			$orderbymsg = " order by c.ID ASC";
		}

		$sql_c  = "select count(*) as allrow from ".DATATABLE."_order_content_index i inner join (select ContentID from ".DATATABLE."_order_cart where ClientID=".$_SESSION['cc']['cid']." and CompanyID=".$_SESSION['cc']['ccompany']." group by ContentID) as c ON i.ID=c.ContentID where i.CompanyID=".$_SESSION['cc']['ccompany']."" ;

		//$sql_l  = "select distinct i.ID,i.Name,i.Coding,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID where c.ClientID = ".$_SESSION['cc']['cid']." and i.CompanyID=".$_SESSION['cc']['ccompany']." ".$orderbymsg." ";
		$sql_l  = "select i.ID,i.Name,i.Coding,i.Model,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,b.BrandName from ".DATATABLE."_order_content_index i inner join (select ID,ContentID from ".DATATABLE."_order_cart where ClientID=".$_SESSION['cc']['cid']." and CompanyID=".$_SESSION['cc']['ccompany']." group by ContentID) as c ON i.ID=c.ContentID left join ".DATATABLE."_order_brand as b on i.BrandID=b.BrandID where i.CompanyID=".$_SESSION['cc']['ccompany']." ".$orderbymsg." ";
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("m"=>"product", "t"=>$t, "o"=>$o, "ps"=>$ps);
        
        $result['total']	= $rs['allrow'];
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;

		$result['list']			    = $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink("myorder.php");

		for($i=0;$i<count($result['list']);$i++)
		{
						//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
// 			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']];
			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;
			$price3 = commondata::setprice3($result['list'][$i]['Price3']);
			if(!empty($price3)) $result['list'][$i]['Price'] = $price3;

			if(!empty($result['list'][$i]['Color']) || !empty($result['list'][$i]['Specification']))
			{
				$result['list'][$i]['cs'] = "Y";
			}else{
				$result['list'][$i]['cs'] = "N";
			}
		}
		if(!empty($idarr) && $ishow=="on")
		{
			$idmsg = implode(",",$idarr);
			$sqlnumber = "select ContentID,OrderNumber,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID in (".$idmsg.")";
			$numberarr	= $db->get_results($sqlnumber);
			if(!empty($numberarr))
			{
				foreach($numberarr as $nvar)
				{
					$narr[$nvar['ContentID']] = $nvar['OrderNumber'];
				}
			}
			$result['number'] = $narr;
		}

		//$db->debug();
		return $result;
	}

	//操作菜单
	function OrderStatus($ostatus,$oid)
	{
		 $ext = "";
		 switch($ostatus)
		 {
			case 0:
			{
				$ext = '<a href="myorder.php?m=cancel&oid='.$oid.'" onClick="return confirm(\'确定要取消该订单吗?\')" >&#8250; 取消订单</a>';
				break;
			}
			case 1:
			{
				$ext = '';
				break;
			}
			case 2:
			{
				$ext = '<a href="javascript:void(0)" title="货物已发出一段时间，如果您已收到货物可以点击确认按钮进行确认" onclick="do_order_status('.$oid.')">&#8250; 确认收货</a>';
				break;
			}
			case 8:
			{
				$ext = '<a href="myorder.php?m=uncancel&oid='.$oid.'" onClick="return confirm(\'确定要恢复该订单吗?\')" >&#8250; 恢复订单</a>';
				break;
			}
			default: 
				$ext = "";
				break;
		}
		return $ext;
	}


	/**************** consignment  *************************/
	//发货单列表
	function listconsignment($status,$ps=12,$lurl='consignment.php')
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		$smsgc = "";
		if(empty($ps)) $ps = 12;

		if($status != "")
		{
			$status = intval($status);
			$smsg   = " and c.ConsignmentFlag = ".$status." ";
			$smsgc  = " and ConsignmentFlag = ".$status." ";
		}
		$orderbymsg = " ORDER BY c.ConsignmentID DESC";

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['cc']['ccompany']." and ConsignmentClient=".$_SESSION['cc']['cid']."  ".$smsgc." ";
		$sql_l = "select c.*,l.LogisticsName,l.LogisticsPhone from ".DATATABLE."_order_consignment c LEFT JOIN ".DATATABLE."_order_logistics l ON c.ConsignmentLogistics=l.LogisticsID where c.ConsignmentCompany=".$_SESSION['cc']['ccompany']." and c.ConsignmentClient=".$_SESSION['cc']['cid']." ".$smsg." ".$orderbymsg;
		
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("status"=>$status,"ps"=>$ps);
        
		$result['list']		= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink($lurl);

		//$db->debug();
		return $result;
		unset($result);
	}

	//确认到货
	function confirmincept($cid)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$cid     = intval($cid);

		$sql_l   = "update ".DATATABLE."_order_consignment set ConsignmentFlag=1 where ConsignmentID=".$cid." and ConsignmentCompany=".$_SESSION['cc']['ccompany']." and ConsignmentClient=".$_SESSION['cc']['cid']." ";

		$resultstatus	 = $db->query($sql_l);
		if($resultstatus)
		{
			$cinfo = $db->get_row("SELECT ConsignmentID,ConsignmentOrder FROM ".DATATABLE."_order_consignment where ConsignmentCompany = ".$_SESSION['cc']['ccompany']." and ConsignmentID=".$cid." limit 0,1");
			if(!empty($cinfo['ConsignmentOrder']))
			{
				$upinfo  = $db->get_row("SELECT OrderID,OrderSN,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderSN = '".$cinfo['ConsignmentOrder']."' and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." limit 0,1");

				$sendline = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where ContentSend < ContentNumber and CompanyID = ".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." and OrderID=".$upinfo['OrderID']."");
				if(!empty($sendline['allrow']) && $sendline['allrow'] > 0)
				{
					$upsql =  "update ".DATATABLE."_order_orderinfo set OrderSendStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']."";
				}else{
					$upsql =  "update ".DATATABLE."_order_orderinfo set OrderSendStatus=4 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']."";
				}
				
				$upsql2 =  "update ".DATATABLE."_order_orderinfo set OrderStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderStatus < 3";
				$db->query($upsql2);
				if($db->query($upsql))
				{	
					$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Date,Status,Content) values(".$_SESSION['cc']['ccompany'].", ".$upinfo['OrderID'].", '".$_SESSION['cc']['cusername']."',".time().", '客户确认收货', '...')";
					$db->query($sqlin);

					$message = "【".$_SESSION['ucc']['CompanySigned']."】经销商:".$_SESSION['cc']['ctruename']."-".$_SESSION['cc']['ccompanyname']."已于".date("Y-m-d")."收到订单号为:".$cinfo['ConsignmentOrder']."的货物。退订回复TD";
					sms::get_setsms("6",$message);
				}
			}
			return true;
		}else{
			return false;
		}
	}

	//发货单详细
	function showconsignment($id='',$sn='')
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$smsg  = "";
		if(!empty($id))
		{
			$id       = intval($id);
			$sql_o = "select * from ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['cc']['ccompany']." and ConsignmentClient=".$_SESSION['cc']['cid']."  and ConsignmentID=".$id." limit 0,1";
		}elseif(!empty($sn)){
			$sql_o = "select * from ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['cc']['ccompany']." and ConsignmentClient=".$_SESSION['cc']['cid']."  and ConsignmentOrder='".$sn."' limit 0,1";
		}		
		$coninfo    = $db->get_row($sql_o);
		$id = $coninfo['ConsignmentID'];

		$sql_c = "select * from ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['cc']['ccompany']." and LogisticsID=".$coninfo['ConsignmentLogistics']." order by LogisticsID asc limit 0,1";
		$loginfo	= $db->get_row($sql_c);

		$sql_e = "select * from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderSN='".$coninfo['ConsignmentOrder']."' and OrderUserID=".$_SESSION['cc']['cid']." limit 0,1";
		$orderinfo	= $db->get_row($sql_e);
		
		$sql_cart = "select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,l.ContentNumber,i.Coding,i.Casing,i.Units from ".DATATABLE."_order_cart c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID where c.CompanyID=".$_SESSION['cc']['ccompany']." and l.ConsignmentID=".$id." and l.ConType='c' order by i.SiteID asc,c.ID asc";
		$result['cartinfo']	   = $db->get_results($sql_cart);

		$sql_cart_g = "select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,l.ContentNumber,i.Coding,i.Casing,i.Units from ".DATATABLE."_order_cart_gifts c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID where c.CompanyID=".$_SESSION['cc']['ccompany']." and l.ConsignmentID=".$id." and l.ConType='g' order by i.SiteID asc,c.ID asc";
		$result['cartinfog']   = $db->get_results($sql_cart_g);

		$result['orderinfo']   = $orderinfo;
		$result['loginfo']     = $loginfo;
		$result['coninfo']     = $coninfo;

		//$db->debug();
		return $result;
		unset($result);
	}


	//地址
	function listaddress()
	{
		$db	   = dbconnect::dataconnect()->getdb();
		
		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_address where CompanyID = ".$_SESSION['cc']['ccompany']." and AddressClient=".$_SESSION['cc']['cid']." order by AddressID asc ";
		$rs    = $db->get_row($sql_c);
		
		$sql_l  = "select * from ".DATATABLE."_order_address where CompanyID = ".$_SESSION['cc']['ccompany']." and AddressClient=".$_SESSION['cc']['cid']." order by AddressID desc ";  //limit 0,20  
		
		$page  = new ShowPage;
		$page->PageSize			= 20;
		$page->Total		    = $rs['allrow'];
		$result['total']		= $rs['allrow'];
		$page->LinkAry			= array("m"=>'address');
		
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;
		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink('consignment.php');

		//$db->debug();
		return $result;
		unset($result);
	}

	//删除地址
	function deladdress($kid)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$kid   = intval($kid);

		$sql_l  = "delete from ".DATATABLE."_order_address where AddressID=".$kid." and CompanyID=".$_SESSION['cc']['ccompany']." and AddressClient=".$_SESSION['cc']['cid']." ";

		$resultstatus	= $db->query($sql_l);
		if($resultstatus)
		{
			return true;
		}else{
			return false;
		}
	}

	//设置默认值
	function setaddress($kid)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$kid    = intval($kid);

		$sql_l  = "update ".DATATABLE."_order_address set AddressFlag=1 where AddressID=".$kid." and CompanyID=".$_SESSION['cc']['ccompany']." and AddressClient=".$_SESSION['cc']['cid']." ";
		$db->query("update ".DATATABLE."_order_address set AddressFlag=0 where CompanyID=".$_SESSION['cc']['ccompany']." and AddressClient=".$_SESSION['cc']['cid']." ");

		$resultstatus	 = $db->query($sql_l);
		if($resultstatus)
		{			
			return true;
		}else{
			return false;
		}
	}

	//保存地址
	function saveaddress($in)
	{
		$db	  = dbconnect::dataconnect()->getdb();
		
		//取消订货端收货地址数量限制  addby lxc 2016-02-16
// 		$row  = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_address where CompanyID=".$_SESSION['cc']['ccompany']." and AddressClient=".$_SESSION['cc']['cid']." ");
// 		if($row['allrow'] >= 20) return "常用地址最多只能 20 个，请删除一些不常用的地址再添加！";

		$addressid = array('status' => 'error','msg' => '提交失败，请与供应商联系!');
		if(empty($in['data_AddressID']))
		{
			$row  = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_address where AddressClient=".$_SESSION['cc']['cid']." and AddressAddress = '".$in['data_AddressAddress']."' ");
			if($row['allrow'] >= 1) return json_encode(array('status' => 'error','msg' => '此地址已存在，不要重复添加！'));
			
			$sql_l  = "insert into ".DATATABLE."_order_address(CompanyID,AddressClient,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressDate) values(".$_SESSION['cc']['ccompany'].", ".$_SESSION['cc']['cid'].", '".$in['data_AddressCompany']."', '".$in['data_AddressContact']."', '".$in['data_AddressPhone']."', '".$in['data_AddressAddress']."', ".time().")";
			$status	= $db->query($sql_l);
			
			$addressid['msg'] = $db->insert_id;
		}else{
			$sql_l  = "update ".DATATABLE."_order_address set AddressCompany='".$in['data_AddressCompany']."',  AddressContact='".$in['data_AddressContact']."', AddressPhone='".$in['data_AddressPhone']."', AddressAddress='".$in['data_AddressAddress']."' where CompanyID=".$_SESSION['cc']['ccompany']." and AddressClient=".$_SESSION['cc']['cid']." and AddressID=".$in['data_AddressID']." ";
			$status	= $db->query($sql_l);
			
			$addressid['msg'] = $in['data_AddressID'];
		}

		if($status) 
			$addressid['status'] = 'ok';
		
		return json_encode($addressid); 
		//$db->debug();
	}


	/**************** finance  *************************/
	//发货单列表
	function listfinance($status,$ps=12,$lurl='finance.php')
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		$smsgc = "";
		if(empty($ps)) $ps = 12;

		if($status != "")
		{
			$status  = intval($status);
			$smsg    = " and f.FinanceFlag = ".$status." ";
			$smsgc   = " and FinanceFlag = ".$status." ";
		}
		$orderbymsg  = " ORDER BY f.FinanceID DESC";

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['cc']['ccompany']." and FinanceClient=".$_SESSION['cc']['cid']."   ".$smsgc." ";
		$sql_l = "select f.*,a.AccountsID,a.AccountsBank,a.AccountsNO,a.AccountsName,a.AccountsType from ".DATATABLE."_order_finance f LEFT JOIN ".DATATABLE."_order_accounts a ON f.FinanceAccounts=a.AccountsID where f.FinanceCompany=".$_SESSION['cc']['ccompany']." and f.FinanceClient=".$_SESSION['cc']['cid']." ".$smsg." ".$orderbymsg;
		
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("status"=>$status,"ps"=>$ps);        
		$result['list']		= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink($lurl);
		
		for($i=0;$i<count($result['list']);$i++)
		{
			if(empty($result['list'][$i]['FinanceOrder']))
			{
				$result['list'][$i]['FinanceOrderList'][] = '预付款';
			}else{
				$foarr = explode(",",$result['list'][$i]['FinanceOrder']);
				foreach($foarr as $fvar)
				{
					$result['list'][$i]['FinanceOrderList'][] = $fvar;
				}
			}
		}

		//$db->debug();
		return $result;
		unset($result);
	}

	//内容
	function showfinance($ID)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$ID    = intval($ID);

		$sql_c = "select * from ".DATATABLE."_order_finance where FinanceID=".$ID." and  FinanceCompany=".$_SESSION['cc']['ccompany']." and FinanceClient=".$_SESSION['cc']['cid']." limit 0,1";
		$financecontent = $db->get_row($sql_c);
		$orderarr = null;
		$sqlarr   = '';

		if(!empty($financecontent['FinanceOrder']))
		{
			$orderarr = explode(",", $financecontent['FinanceOrder']);
			if(is_array($orderarr))
			{
				foreach($orderarr as $ovar)
				{
					if(empty($sqlarr)) $sqlarr .= " OrderSN = '".$ovar."' "; else $sqlarr .= " or OrderSN = '".$ovar."' ";
				}
			}
		}
		if(!empty($sqlarr))
		{
			$sqlarr = " and (".$sqlarr.") ";
			$sql_l  = "select OrderID,OrderSN,OrderTotal,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." ".$sqlarr." order by OrderID asc ";
			$olist  =  $db->get_results($sql_l);
		}

		$sql_a = "select AccountsID,AccountsBank,AccountsNO,AccountsName,AccountsType from ".DATATABLE."_order_accounts where AccountsID=".$financecontent['FinanceAccounts'];
		$accountscontent = $db->get_row($sql_a);

		if($financecontent['FinanceFrom'] == 'alipay')
		{
			$sql_a = "select * from ".DATABASEU.DATATABLE."_order_alipay where PayCompany=".$_SESSION['cc']['ccompany']." and PaySN='".$financecontent['FinancePaysn']."' limit 0,1";
			$alipaycontent = $db->get_row($sql_a);
			$result['pay']	= $alipaycontent;
		}elseif($financecontent['FinanceFrom'] == 'allinpay'){
			$sql_a = "select * from ".DATABASEU.DATATABLE."_order_netpay where CompanyID=".$_SESSION['cc']['ccompany']." and OrderNO='".$financecontent['FinancePaysn']."' limit 0,1";
			$alipaycontent = $db->get_row($sql_a);
			$result['pay']	= $alipaycontent;
		}elseif($financecontent['FinanceFrom'] == 'yijifu'){
			$sql_a = "select * from ".DATABASEU.DATATABLE."_order_netpay where CompanyID=".$_SESSION['cc']['ccompany']." and OrderNO='".$financecontent['FinancePaysn']."' limit 0,1";
			$alipaycontent = $db->get_row($sql_a);
			$result['pay']	= $alipaycontent;
		}

		$result['content']   = $financecontent;
		$result['orderarr']  = $olist;
		$result['accounts']  = $accountscontent;
		
		//$db->debug();
		return $result;
		unset($result);
	}

	//删除
	function delfinance($kid)
	{
		$db	  = dbconnect::dataconnect()->getdb();
		$kid   = intval($kid);
		
		$rs    = $db->get_row("select * from ".DATATABLE."_order_finance where FinanceID=".$kid." and FinanceCompany=".$_SESSION['cc']['ccompany']." and FinanceClient=".$_SESSION['cc']['cid']."");
		if($rs['FinanceFlag'] == "2") return false;

		$sql_l  = "delete from ".DATATABLE."_order_finance where FinanceID=".$kid." and FinanceCompany=".$_SESSION['cc']['ccompany']." and FinanceClient=".$_SESSION['cc']['cid']." and FinanceFlag=0 ";

		$resultstatus	= $db->query($sql_l);
		if($resultstatus)
		{
			if(!empty($rs['FinanceOrder']))
			{
				if(strpos($rs['FinanceOrder'],","))
				{
					$ordersn_arr = explode(",", $rs['FinanceOrder']);
					foreach($ordersn_arr as $osv)
					{
						if(!empty($osv))
						{
							$upsql =  "update ".DATATABLE."_order_orderinfo set OrderPayStatus=0 where OrderSN = '".$osv."' and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']."  and (OrderPayStatus=1 or OrderPayStatus=4)";
							$isup = $db->query($upsql);
						}
					}
				}else{
					$db->query("update ".DATATABLE."_order_orderinfo set OrderPayStatus=0 where OrderSN = '".$rs['FinanceOrder']."' and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']."  and (OrderPayStatus=1 or OrderPayStatus=4) ");
				}				
			}			
			return true;
		}else{
			return false;
		}
	}

	//帐号
	function listaccounts()
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select AccountsID,AccountsBank,AccountsNO,AccountsName,PayType,PayPartnerID,PayKey from ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['cc']['ccompany']." order by AccountsID asc";       
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}
        //是否开通医统账期
        function CompanyCredit(){
            $db = dbconnect::dataconnect()->getdb();
            $CreditSql = "select CompanyCredit from  ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['cc']['ccompany']."";
            $CreditSel = $db->get_row($CreditSql);
            return $CreditSel['CompanyCredit'];
            
        }
        //是否提交企业资质
        public function BottomZizhi(){
            $db = dbconnect::dataconnect()->getdb();
            $client_id = $_SESSION['cc']['cid'];
            $company_id = $_SESSION['cc']['ccompany'];
            $cs_flag = $db->get_var("SELECT C_Flag FROM ".DATATABLE."_order_client WHERE ClientCompany={$company_id} and ClientID={$client_id} LIMIT 1");
            return $cs_flag;
        }
	//帐号
	function showaccounts()
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select * from ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['cc']['ccompany']." and PayType='alipay'  order by AccountsID asc limit 0,1";       
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}

	//在线支付帐号
	function show_getway($getway='allinpay')
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select MerchantNO,SignMsgKey,SignMsg,B2B,Fee,SignNO from ".DATABASEU.DATATABLE."_order_getway where CompanyID = ".$_SESSION['cc']['ccompany']." and Status='T' and GetWay='".$getway."' order by GetWayID asc limit 0,1";       
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}


	//订单
	function listordersn()
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sql_l  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and (OrderPayStatus=0 or OrderPayStatus=1 or OrderPayStatus=3) and OrderStatus < 5 order by OrderID desc limit 0,100";       
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}
	
	//保存转帐记录
	function subaccounts($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$financeordermsg = '';
		if(!empty($in['FinanceYufu']))
		{
			$financeordermsg = '0';
		}else{
			if(!empty($in['data_FinanceOrder']))
			{
				$financeordermsg = implode(",", $in['data_FinanceOrder']);
			}
		}

		if((empty($financeordermsg) || $financeordermsg == '0') && $in['finance_type'] == 'Y') return false; //余额支付不能为预付款。
		if(empty($in['finance_type'])) $in['finance_type'] = 'Z';
		$in['data_FinanceOrderID'] = intval($in['data_FinanceOrderID']);
		$in['data_FinanceTotal']   = abs(floatval($in['data_FinanceTotal']));
		$sql_l  = "insert into ".DATATABLE."_order_finance(FinanceCompany,FinanceClient,FinanceOrderID,FinanceOrder,FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,FinanceToDate,FinanceDate,FinanceUser,FinanceType) values(".$_SESSION['cc']['ccompany'].", ".$_SESSION['cc']['cid'].", ".$in['data_FinanceOrderID'].",'".$financeordermsg."', ".$in['data_FinanceAccounts'].", '".$in['data_FinanceTotal']."', '".$in['data_FinancePicture']."', '".$in['data_FinanceAbout']."', '".$in['data_FinanceToDate']."', ".time().",'".$_SESSION['cc']['cusername']."','".$in['finance_type']."')";   
		
		$status	= $db->query($sql_l);
		$insert_id = mysql_insert_id();
		if(!empty($in['data_FinanceOrder']) && empty($in['FinanceYufu']))
		{
			
			if(!empty($in['data_FinanceOrderID'])) $db->query("update ".DATATABLE."_order_orderinfo set OrderPayStatus=1 where OrderID = ".$in['data_FinanceOrderID']." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderPayStatus=0 ");
			$ins['ID'] = $insert_id;
			$ins['finance_type'] = $in['finance_type'];
			if($in['finance_type'] == 'Y' || $in['finance_type'] == 'C') self::set_validate($ins,$db);	//余额或账期支付直接确认到账
		}

		if($status){
			
			//发送验证码短信
// 			$sms = new SmsApp();
// 			$sms->getSmsTpl('YTZQPAYSUCCESS')
// 				->bulidContent(array('{PAYTIME}', '{ORDERSN}', '{ORDERTOTAL}'), array(date('Y-m-d H:i'), $financeordermsg, $in['data_FinanceTotal']))
// 				->SendSMS($in['mobile'])
// 				->logStatus($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);
			
			return true;
		}else{
			return false;
		}
		//$db->debug();
	}


	//保存收货状态
	function confirmclientincept($id,$content)
	{
		$db	 = dbconnect::dataconnect()->getdb();
		$id    = intval($id);

		$upinfo  = $db->get_row("SELECT OrderID,OrderSN,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderID = ".$id." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." limit 0,1");

		$sendline = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where ContentSend < ContentNumber and CompanyID = ".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." and OrderID=".$upinfo['OrderID']."");
		if(!empty($sendline['allrow']) && $sendline['allrow'] > 0)
		{
			$upsql1 =  "update ".DATATABLE."_order_orderinfo set OrderSendStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']."";
		}else{
			$upsql1 =  "update ".DATATABLE."_order_orderinfo set OrderSendStatus=4 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']."";
		}

		$upsql2 =  $db->query("update ".DATATABLE."_order_orderinfo set OrderStatus=3 where OrderID = ".$id." and OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderStatus<3");	
		if($db->query($upsql1))
		{
			if(!empty($upinfo['OrderSN'])) $db->query("update ".DATATABLE."_order_consignment set ConsignmentFlag=1 where ConsignmentCompany=".$_SESSION['cc']['ccompany']." and ConsignmentClient=".$_SESSION['cc']['cid']." and ConsignmentOrder='".$upinfo['OrderSN']."' ");	
		
			$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['cc']['ccompany'].", ".$id.", '".$_SESSION['cc']['cusername']."', '".$_SESSION['cc']['ctruename']."',".time().", '客户确认已收货', '".$content."')";
			$db->query($sqlin);
			$status = true;
		}else{
			$status = false;
		}
		return $status;
	}


	//保存留言
	function save_guestbook($id, $content, $action = '客户留言')
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$id    = intval($id);

		$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['cc']['ccompany'].", ".$id.", '".$_SESSION['cc']['cusername']."', '".$_SESSION['cc']['ctruename']."',".time().", '".$action."', '".$content."')";
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

		$binfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_cartbak where CompanyID = ".$_SESSION['cc']['ccompany']." and OrderID=".$oid." limit 0,1");

		//$db->debug();
		return $binfo['allrow'];
		unset($binfo);
	}

	function show_cartproduct($oid)
	{
		$db    = dbconnect::dataconnect()->getdb();		

		$result = $db->get_row("SELECT ID,Content FROM ".DATATABLE."_order_cartbak where CompanyID = ".$_SESSION['cc']['ccompany']." and OrderID=".$oid." limit 0,1");
		if(!empty($result['Content']))
		{
			$cartdata = $db->get_results("select * from ".DATATABLE."_order_cart  where CompanyID=".$_SESSION['cc']['ccompany']." and OrderID=".$oid." order by ID asc");
			foreach($cartdata as $var)
			{
				$cartarr[$var['ID']] = $var;
			}
			
			$redata = unserialize($result['Content']);
			foreach($redata as $rv)
			{
				$oldidarr[] = $rv['ID'];
				$resultdata['cart'][$rv['ID']] = $rv;
				$resultdata['cart'][$rv['ID']]['NewNumber']		= $cartarr[$rv['ID']]['ContentNumber'];
				$resultdata['cart'][$rv['ID']]['NewPrice']		= $cartarr[$rv['ID']]['ContentPrice'];
				$resultdata['cart'][$rv['ID']]['NewPercent']	= $cartarr[$rv['ID']]['ContentPercent'];
			}
			foreach($cartdata as $cvar)
			{
				if(!in_array($cvar['ID'],$oldidarr))
				{
					$resultdata['ncart'][$cvar['ID']] = $cvar;
				}
			}
		}	

		//$db->debug();
		return $resultdata;
		unset($resultdata);
	}

	//费用
	function listexpense($ps=12,$lurl='finance.php')
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$smsg   = " and e.FlagID = '2' ";
		$smsgc	= " and FlagID = '2' ";
		if(empty($ps)) $ps = 12;

		$orderbymsg = " ORDER BY e.ExpenseID DESC";

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']."  ".$smsgc." ";
		$sql_l = "select e.ExpenseID,e.BillID,e.ExpenseTotal,e.ExpenseDate,e.ExpenseTime,b.BillName from ".DATATABLE."_order_expense e LEFT JOIN ".DATATABLE."_order_expense_bill b ON e.BillID=b.BillID where e.CompanyID=".$_SESSION['cc']['ccompany']." and e.ClientID=".$_SESSION['cc']['cid']." ".$smsg." ".$orderbymsg;
		
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("m"=>'expense',"ps"=>$ps);
        
		$result['list']		= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink($lurl);
		
		//$db->debug();
		return $result;
		unset($result);
	}

	function get_client_money()
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$cid       =  $_SESSION['cc']['cid'];
		//收款单
		$sqlunion  = " and FinanceClient = ".$cid." "; 		
		$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
		$statdata2 = $db->get_row($statsql2);
		/***
		//已经使用的余额
		$sqlunion  = " and f.FinanceClient = ".$cid." "; 
		$statsql3  = "SELECT sum(f.FinanceTotal) as Ftotal from ".DATATABLE."_order_finance f inner join ".DATATABLE."_order_orderinfo o ON f.FinanceOrderID=o.OrderID where o.OrderStatus=0 and (o.OrderPayStatus=2 or o.OrderPayStatus=3) ".$sqlunion." and f.FinanceCompany=".$_SESSION['cc']['ccompany']." and f.FinanceFlag=2 and f.FinanceType='Y' ";
		$statdata3 = $db->get_row($statsql3);
		***/
		//其他款项
		$sqlunion  = " and ClientID = ".$cid." "; 		
		$statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['cc']['ccompany']." ".$sqlunion." and FlagID = '2' ";
		$statdata4 = $db->get_row($statsql4);
		
		//没取消的订单金额
		$sqlunion  = " and OrderUserID   = ".$cid." "; 
		$statsqlt  = "SELECT sum(OrderIntegral) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and (OrderStatus!=8 and OrderStatus!=9) ";
		$statdatat = $db->get_row($statsqlt);
		
		 //没取消但使用账期支付的订单金额
		$sqlunion  = " and OrderUserID   = ".$cid." ";
		$statsqltzq  = "SELECT sum(OrderIntegral) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and (OrderStatus!=8 and OrderStatus!=9 and OrderPayType=12)";
		$statdatatzq = $db->get_row($statsqltzq);
		
		//退货金额
		$sqlunion   = " and ReturnClient  = ".$cid." ";
		$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and (ReturnStatus=3 or ReturnStatus=5) ";
		$statdata1  = $db->get_row($statsqlt1);
		
		$begintotal = $statdata2['Ftotal'] - ($statdatat['Ftotal'] - $statdatatzq['Ftotal']) + $statdata4['Ftotal'] + $statdata1['Ftotal'];
		return $begintotal;
	}
	
	//更改订单状态
	function set_validate($in,$db){
		$upsql =  "update ".DATATABLE."_order_finance set FinanceUpDate=".time().",FinanceAdmin='".$_SESSION['cc']['cusername']."',FinanceFlag=2 where FinanceID = ".$in['ID']." and FinanceCompany=".$_SESSION['cc']['ccompany']." ";	
		
		if($status = $db->query($upsql))
		{
			$cinfo = $db->get_row("SELECT FinanceID,FinanceClient,FinanceOrder,FinanceTotal,FinanceToDate FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['cc']['ccompany']." and FinanceID=".$in['ID']." limit 0,1");
			if(!empty($cinfo['FinanceOrder']))
			{
				$ordersn_arr = explode(",", $cinfo['FinanceOrder']);
				$smmsg = " '".str_replace(",","','",$cinfo['FinanceOrder'])."' ";
				$sqlarr = " and OrderSN IN (".$smmsg.") ";
				$sql_l  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." ".$sqlarr." order by OrderID asc ";
				$olist  =  $db->get_results($sql_l);

				if(!empty($olist))
				{
					
					//修改最新的支付方式todo，订单在提交时，不应该选择支付方式
					$payTypeConfig = array(
							'Y' => 7,  	//预存款(先付)
							'C' => 12, 	//医统账期
					);
					$patType = $payTypeConfig[$in['finance_type']];

					$chatotal = $cinfo['FinanceTotal'];
					foreach($olist as $osv)
					{
						if(!empty($osv['OrderTotal']))
						{
							$chatotal = $chatotal - $osv['OrderTotal'] + $osv['OrderIntegral'];				
							if($chatotal >= 0)
							{
								$changePay = '';
								if($in['finance_type'] == 'Y' || $in['finance_type'] == 'C'){
									$changePay = ',OrderPayType='.$patType;
								}
								$upsql = "update ".DATATABLE."_order_orderinfo set OrderPayStatus=2, OrderIntegral='".$osv['OrderTotal']."'".$changePay." where OrderID = '".$osv['OrderID']."' and OrderCompany=".$_SESSION['cc']['ccompany']." limit 1";
								$isup  = $db->query($upsql);
							}else{
								$uptotal = $chatotal + $osv['OrderTotal'];
								$upsql = "update ".DATATABLE."_order_orderinfo set OrderPayStatus=3, OrderIntegral='".$uptotal."' where OrderID = '".$osv['OrderID']."' and OrderCompany=".$_SESSION['cc']['ccompany']." limit 1";
								$isup  = $db->query($upsql);
								break;
							}
						}
						$lastosv = $osv['OrderSN'];
					}				
				}
			}
			return $status;
		}
	}

	///读取购物车详情，易极付专用
	function getCartDetail($sn = ''){
		if(!$sn) return array();
		
		$psn	= explode(",", $sn);
		$snTmp	= array();
		foreach($psn as $v){
			if(empty($v)) continue;
			$snTmp[] = "'".$v."'";
		}
		$condition = implode(",", $snTmp);
		
		$db	  = dbconnect::dataconnect()->getdb();
		$csql = "select 
				  c.ContentName as title 
				from 
				  ".DATATABLE."_order_cart AS c 
				  LEFT JOIN ".DATATABLE."_order_orderinfo AS o 
				  ON c.OrderID=o.OrderID 
				where o.OrderSN in (".$condition.") limit 30";
		
		$info = $db->get_results($csql);
		
		$length = count($info);
		$detail = array();
		for($i = 0; $i < $length; $i++){
			$title = trim($info[$i]['title']);
			$detail[$i]['title'] = urlencode($title);
		}
		unset($info);
				
		return $detail;
	}
	
	//获取订单总金额，易极付专用
	function getTotalForYJF($sn = '', $comanyid = 0, $clientid = 0){
		
		if(empty($sn)) return false;
		$db	  = dbconnect::dataconnect()->getdb();
		$psn	= explode(",", $sn);
		$snTmp	= array();
		foreach($psn as $v){
			if(empty($v)) continue;
			$snTmp[] = "'".$v."'";
		}
		$condition = implode(",", $snTmp);
		
		$sql = "select sum(OrderTotal) total from ".DATATABLE."_order_orderinfo where OrderCompany=".$comanyid." and OrderUserID=".$clientid." and OrderSN in (".$condition.")";
		
		return $db->get_row($sql);
	}

	//保存网银支付信息
	function save_netpay($inv,$acc, $getway = 'allinpay')
	{
		$db	   = dbconnect::dataconnect()->getdb();

		$sqlin = "insert into ".DATABASEU.DATATABLE."_order_netpay(CompanyID,ClientID,GetWay,MerchantNO,OrderNO,OrderMoney,PayMoney,Remark,OrderDateTime,PayResult,ErrorCode,Bank,PayType,VerifyMsg,PayTradeNO,MerchantAccount) values(".$_SESSION['cc']['ccompany'].", ".$_SESSION['cc']['cid'].", '".$getway."', '".$acc['MerchantNO']."', '".$inv['orderNo']."','".$inv['orderMoney']."','".$inv['orderAmount']."', '".$inv['ext2']."', '".$inv['orderDatetime']."', '0', '".$inv['errorCode']."', '".$inv['payType']."', '".$inv['issuerId']."', '".$inv['verifyMsg']."', '', '".$inv['acType']."')";

		$status = $db->query($sqlin);
		if($status)
		{
			return true;
		}else{
			$pinfo = $db->get_row("select count(*) as row from ".DATABASEU.DATATABLE."_order_netpay where MerchantNO='".$acc['MerchantNO']."' and OrderNO='".$inv['orderNo']."' order by PayID desc limit 0,1");
			if($pinfo['row'] > 0) return true; else return false;			
		}
	}

//END
}

	$pay_status_arr = array(
		'0'			=>  '未付款',
		'1'			=>  '付款中',
		'2'			=>  '已付款',
		'3'			=>  '预付款',
		'4'			=>  '款项确认中'
 	 );
	
	$send_status_arr = array(
		'0'			=>  '待审核',
		'1'			=>  '备货中',
		'2'			=>  '已发货',
		'3'			=>  '未发完',
		'4'			=>  '已收货'
 	 );

	$order_status_arr = array(
		'0'			=>  '待审核',
		'1'			=>  '备货中',
		'2'			=>  '已开票出库',
		'3'			=>  '已收货',
		'5'         =>  '已付款',
		'7'         =>  '已完成',
		'8'         =>  '客户取消',
		'9'         =>  '管理员取消'
	 );

	$pay_send_arr = array(
		'1'			=>  '已付',
		'2'			=>  '到付'
 	 );

	$incept_arr = array(
		'0'			=>  '在途',
		'1'			=>  '确认收货',
		'2'			=>  '管理员确认'
 	 );

	$finance_arr = array(
		'0'			=>  '在途',
		'2'			=>  '确认到帐'
 	 );
?>