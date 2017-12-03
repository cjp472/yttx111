<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');


$objPHPExcel = new PHPExcel();

	if(empty($in['cid']))
	{
		$in['cid'] = '';
		$sidmsg    = '';
		$sidmsg2    = '';
		$sidmsg3    = '';
	}else{
		$sqlmsg  = " and FinanceClient = ".intval($in['cid'])." ";
		$sqlmsg2 = " and OrderUserID   = ".intval($in['cid'])." ";
		$sqlmsg3 = " and ReturnClient  = ".intval($in['cid'])." ";
		$sqlmsg4 = " and ClientID	   = ".intval($in['cid'])." ";
		$sidmsg   = '&cid='.$in['cid'];
	}
	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate'] = date("Y-m-d");

	$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
								 ->setLastModifiedBy("DingHuoBao")
								 ->setTitle("医统天下-往来对帐")
								 ->setSubject("医统天下-往来对帐")
								 ->setDescription("医统天下-往来对帐")
								 ->setKeywords("医统天下 订货管理系统")
								 ->setCategory("往来对帐");

	$objPHPExcel->setActiveSheetIndex(0);
	$objActSheet = $objPHPExcel->getActiveSheet();
	$objActSheet->setTitle('往来对帐');
	$objActSheet->getDefaultRowDimension()->setRowHeight(20);
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 往来对帐 ";

	$objActSheet->setCellValue('A1', $titlemsg);
	$objActSheet->mergeCells('A1:E1');
	$objStyleA5 = $objActSheet->getStyle('A1'); 
	//设置对齐方式
	$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	//设置字体    
	$objFontA5 = $objStyleA5->getFont();   
	$objFontA5->setName('黑体' );   
	$objFontA5->setSize(14);   
	$objFontA5->setBold(true);

	//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
	$objActSheet->getColumnDimension('A' )->setWidth(20);
	$objActSheet->getColumnDimension('B' )->setWidth(30);
	$objActSheet->getColumnDimension('C' )->setWidth(20);
	$objActSheet->getRowDimension('1')->setRowHeight(32);
	$objActSheet->freezePane('A3');

	$k = 1;
	$k++;
	$objActSheet->setCellValue('A'.$k, '日期');
	$objActSheet->setCellValue('B'.$k, '药店');
	$objActSheet->setCellValue('C'.$k, '单据号');
	$objActSheet->setCellValue('D'.$k, '科目名称');
	$objActSheet->setCellValue('E'.$k, '应收增加');
	$objActSheet->setCellValue('F'.$k, '应收减少');
	$objActSheet->setCellValue('G'.$k, '期末应收');

	$objActSheet->getStyle('A'.$k.':G'.$k)->getFont()->setBold(true);
	$objActSheet->getStyle('A'.$k.':G'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
	$objActSheet->getStyle('E'.$k.':G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $cvar)
	{
		$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
	}

		if(!empty($in['begindate']))
		{
			   $sqlunion = " and FROM_UNIXTIME(FinanceUpDate) < '".$in['begindate']." 00:00:00' "; 		
				$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O')";
				$statdata2 = $db->get_row($statsql2);

				$sqlunion = " and FROM_UNIXTIME(OrderDate) < '".$in['begindate']." 00:00:00' "; 
// 				$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." ".$sqlunion." and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 ";
				$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." ".$sqlunion." OrderStatus!=8 and OrderStatus!=9 ";
				$statdatat = $db->get_row($statsqlt);
				
				$sqlunion = " and FROM_UNIXTIME(ReturnDate) < '".$in['begindate']." 00:00:00' ";
				$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg3." ".$sqlunion." and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) ";
				$statdata1 = $db->get_row($statsqlt1);
				
			    $sqlunion = " and ExpenseDate < '".$in['begindate']."' "; 		
				$statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg4." ".$sqlunion." and FlagID = '2' ";
				$statdata4 = $db->get_row($statsql4);
				
				$begintotal = $statdatat['Ftotal'] - $statdata4['Ftotal'] - $statdata2['Ftotal'] - $statdata1['Ftotal'];
		}else{
				$begintotal = 0;
		}

				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $in['begindate'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $clientarr[$in['cid']]);
				//$objActSheet->setCellValue('C'.$k, "");
				$objActSheet->setCellValue('D'.$k, "期初应收");
				//$objActSheet->setCellValue('E'.$k, 0);
				//$objActSheet->setCellValue('F'.$k, 0);
				$objActSheet->setCellValue('G'.$k, $begintotal);
				
$billdata = $db->get_results("select BillID,BillName from ".DATATABLE."_order_expense_bill where CompanyID=".$_SESSION['uinfo']['ucompany']." ");
foreach($billdata as $var)
{
	$billarr[$var['BillID']] = $var['BillName'];
}

$financesql   = "SELECT FinanceID,FinanceClient,FinanceOrder,FinanceTotal,FinanceUpDate FROM ".DATATABLE."_order_finance where FinanceCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and FROM_UNIXTIME(FinanceUpDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O')  Order by FinanceID ASC";
$finance_data = $db->get_results($financesql);

$expensesql   = "SELECT ExpenseID,ClientID,BillID,ExpenseTotal,ExpenseDate,ExpenseTime FROM ".DATATABLE."_order_expense where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg4." and ExpenseDate between '".$in['begindate']."' and '".$in['enddate']."' and FlagID='2' Order by ExpenseID ASC";
$expense_data = $db->get_results($expensesql);

// $ordersql   = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderIntegral,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 Order by OrderID ASC";
$ordersql   = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderIntegral,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=8 and OrderStatus!=9 Order by OrderID ASC";
$order_data = $db->get_results($ordersql);

$returnsql   = "SELECT ReturnID,ReturnSN,ReturnOrder,ReturnClient,ReturnTotal,ReturnDate FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg3." and FROM_UNIXTIME(ReturnDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) Order by ReturnID ASC";
$return_data = $db->get_results($returnsql);

if(empty($finance_data)) $finance_data[0]['FinanceID'] = 0;
if(empty($order_data))   $order_data[0]['OrderID'] = 0;
if(empty($return_data))  $return_data[0]['ReturnID'] = 0;
if(empty($expense_data))  $expense_data[0]['ExpenseID'] = 0;

$dataarr = array_merge($finance_data, $expense_data, $order_data, $return_data);
if(!empty($dataarr))
{
	foreach($dataarr as $dv)
	{
		$rsid = rand(10,99);
		if(!empty($dv['FinanceID']))
		{
			$key = $dv['FinanceUpDate'].$dv['FinanceClient'].$rsid;//这组合健有可能会重复,导至结果相同键只有最后一条数据
			$larr['atype'] = "收款单";
			$larr['SN']    = "F".date("Ymd",$dv['FinanceUpDate'])."-".$dv['FinanceID'];
			$larr['Date']  = date("Y-m-d",$dv['FinanceUpDate']);
			$larr['Total'] = $dv['FinanceTotal'];
			$larr['TotalType'] = "-";
			$larr['LinkUrl'] = "finance_content.php?ID=".$dv['FinanceID'];
			$larr['Client']  = $clientarr[$dv['FinanceClient']];
		
		}elseif(!empty($dv['ExpenseID'])){

			$key = strtotime($dv['ExpenseDate']." ".date("H:i:s",$dv['ExpenseTime'])).$dv['ClientID'].$rsid;
			$larr['atype'] = "其他款项 - ".$billarr[$dv['BillID']];
			$larr['SN']    = "E".date("Ymd",$dv['ExpenseTime'])."-".$dv['ExpenseID'];
			$larr['Date']  = $dv['ExpenseDate'];
			$larr['Total'] = $dv['ExpenseTotal'];
			$larr['TotalType'] = "-";
			$larr['LinkUrl'] = "expense_content.php?ID=".$dv['ExpenseID'];
			$larr['Client']  = $clientarr[$dv['ClientID']];

		}elseif(!empty($dv['OrderID'])){

			$key = $dv['OrderDate'].$dv['OrderUserID'].$rsid;
			$larr['atype'] = "订单";
			$larr['SN']    = $dv['OrderSN'];
			$larr['Date']  = date("Y-m-d",$dv['OrderDate']);
			$larr['Total'] = $dv['OrderTotal'];
			$larr['TotalType'] = "+";
			$larr['LinkUrl'] = "order_manager.php?ID=".$dv['OrderID'];
			$larr['Client']  = $clientarr[$dv['OrderUserID']];

		}elseif(!empty($dv['ReturnID'])){

			$key = $dv['ReturnDate'].$dv['ReturnClient'].$rsid;
			$larr['atype'] = "退货单";
			$larr['SN']    = $dv['ReturnSN'];
			$larr['Date']  = date("Y-m-d",$dv['ReturnDate']);
			$larr['Total'] = $dv['ReturnTotal'];
			$larr['TotalType'] = "-";
			$larr['LinkUrl'] = "return_manager.php?ID=".$dv['ReturnID'];
			$larr['Client']  = $clientarr[$dv['ReturnClient']];
		}
		$darr[$key] = $larr;
	}
	ksort($darr);

	$tall = 0;
	$tjian = 0;
	$tjia  = 0;
	$n=1;

		if(!empty($darr))
		{
			foreach($darr as $var)
			{
				if(empty($var['Total'])) continue;
				$n++;
				if($var['TotalType']=="-")
				{
					$tall = $tall - $var['Total'];
					$tjian = $tjian + $var['Total'];
					$t2 = $var['Total'];
					$t1 = '';
				}else{
					$tall = $tall + $var['Total'];
					$tjia = $tjia + $var['Total'];
					$t1 = $var['Total'];
					$t2 = '';
				}

				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $var['Date'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $var['Client']);
				$objActSheet->setCellValue('C'.$k, $var['SN']);
				$objActSheet->setCellValue('D'.$k, $var['atype']);
				$objActSheet->setCellValue('E'.$k, $t1);
				$objActSheet->setCellValue('F'.$k, $t2);
				$objActSheet->setCellValue('G'.$k, $tall+$begintotal);
			}

			$k++;
			$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('C'.$k, $n.'个');
			$objActSheet->setCellValue('E'.$k, $tjia);
			$objActSheet->setCellValue('F'.$k, $tjian);
			$objActSheet->setCellValue('G'.$k, $tall+$begintotal);

			$objActSheet->getStyle('A'.$k.':G'.$k)->getFont()->setBold(true);
		}
}
		$objActSheet->getStyle('A2:G'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:G'.$k)->applyFromArray($styleThinBlackBorderOutline);

$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '往来对帐_'.$in['begindate'].'_'.$in['enddate'].'.xls';
$encoded_filename = urlencode($filename);
$encoded_filename = str_replace("+", "%20", $encoded_filename);
header('Content-Type: application/vnd.ms-excel');
if (preg_match("/MSIE/", $ua)) {
	header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
} else if (preg_match("/Firefox/", $ua)) {
	header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
} else {
	header('Content-Disposition: attachment; filename="' . $filename . '"');
}
header('Cache-Control: max-age=0');	

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>