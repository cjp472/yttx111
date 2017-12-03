<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
include_once ('../class/PHPExcel.php');

if(empty($in['action']))
{
	Error::AlertJs('参数错误!');
	exit;
}

$objPHPExcel = new PHPExcel();
$sqll = '';
if(!empty($in['clientid'])) $sqll = " and OrderUserID=".$in['clientid']." ";

	$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
								 ->setLastModifiedBy("DingHuoBao")
								 ->setTitle("医统天下-订单统计")
								 ->setSubject("医统天下-订单统计")
								 ->setDescription("医统天下-订单统计")
								 ->setKeywords("医统天下 订货管理系统")
								 ->setCategory("订单统计");

	$objPHPExcel->setActiveSheetIndex(0);
	$objActSheet = $objPHPExcel->getActiveSheet();
	$objActSheet->setTitle('订单统计');
	$objActSheet->getDefaultRowDimension()->setRowHeight(20);

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
	$objActSheet->getColumnDimension('B' )->setWidth(20);
	$objActSheet->getColumnDimension('C' )->setWidth(20);
	$objActSheet->getColumnDimension('D' )->setWidth(20);
	$objActSheet->getRowDimension('1')->setRowHeight(32);
	$objActSheet->freezePane('A3');

if($in['action']=="client_between")
{
	$objActSheet->getColumnDimension('A' )->setWidth(50);
	if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
	if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");

	$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $areavar)
	{
		$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
	}

	$statsql  = "SELECT OrderUserID,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=8 and OrderStatus!=9 group by OrderUserID";
	$statdata = $db->get_results($statsql);
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 订单数据 ";
	$objActSheet->setCellValue('A1', $titlemsg);

	$statsql0  = "SELECT OrderUserID,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus=0 group by OrderUserID";
	$rdata = $db->get_results($statsql0);
					
	$totalnumber0  = 0;
	$totalprice0   = 0;
	foreach($rdata as $rvar)
	{
		$rarr[$rvar['ODate']] = $rvar['totalnumber'];
		$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
		$totalprice0     = $totalprice0 + $rvar['OTotal'];
	}

		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '药店');
		$objActSheet->setCellValue('B'.$k, '订单金额');
		$objActSheet->setCellValue('C'.$k, '订单数');
		$objActSheet->setCellValue('D'.$k, '待审核订单');

		$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':D'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->getStyle('B'.$k.':D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		if(!empty($statdata))
		{
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];
				if(empty($rarr[$var['OrderUserID']])) $rarr[$var['OrderUserID']] = 0;
				$k++;
				$objActSheet->setCellValue('A'.$k, $clientarr[$var['OrderUserID']]);
				$objActSheet->setCellValue('B'.$k, $var['OTotal']);
				$objActSheet->setCellValue('C'.$k, $var['totalnumber']);
				$objActSheet->setCellValue('D'.$k, $rarr[$var['OrderUserID']]);
			}
				$k++;
				$objActSheet->setCellValue('A'.$k , '合计');  
				$objActSheet->setCellValue('B'.$k, $totalm);
				$objActSheet->setCellValue('C'.$k, $totaln);
				$objActSheet->setCellValue('D'.$k, $totalnumber0.'个 (¥ '.$totalprice0.')');
				$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:D'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:D'.$k)->applyFromArray($styleThinBlackBorderOutline);
		$filename = '药店订单统计报表_'.$in['begindate'].'_'.$in['enddate'].'.xls';
}
elseif($in['action']=="between")
{

	if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
	if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");
	
	$statsql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus!=8 and OrderStatus!=9 group by left(OrderSN,8)";
	$statdata = $db->get_results($statsql);
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 订单数据 ";
	$objActSheet->setCellValue('A1', $titlemsg);

	$statsql0  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(OrderDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and OrderStatus=0 group by left(OrderSN,8)";
	$rdata = $db->get_results($statsql0);
					
	$totalnumber0  = 0;
	$totalprice0   = 0;
	foreach($rdata as $rvar)
	{
		$rarr[$rvar['ODate']] = $rvar['totalnumber'];
		$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
		$totalprice0  = $totalprice0 + $rvar['OTotal'];
	}

		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '日期');
		$objActSheet->setCellValue('B'.$k, '订单金额');
		$objActSheet->setCellValue('C'.$k, '订单数');
		$objActSheet->setCellValue('D'.$k, '待审核订单');

		$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':D'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->getStyle('B'.$k.':D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		if(!empty($statdata))
		{
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];
				if(empty($rarr[$var['ODate']])) $rarr[$var['ODate']] = 0;
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $var['ODate'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $var['OTotal']);
				$objActSheet->setCellValue('C'.$k, $var['totalnumber']);
				$objActSheet->setCellValue('D'.$k, $rarr[$var['ODate']]);
			}
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $totalm);
				$objActSheet->setCellValue('C'.$k, $totaln);
				$objActSheet->setCellValue('D'.$k, $totalnumber0.'个 (¥ '.$totalprice0.')');
				$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:D'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:D'.$k)->applyFromArray($styleThinBlackBorderOutline);
		$filename = '订单统计报表_'.$in['begindate'].'_'.$in['enddate'].'.xls';

}elseif($in['action']=="month"){

	if(empty($in['y'])) $in['y'] = date("Y");
	if(empty($in['m'])) $in['m'] = date("m");
	$titlemsg = $in['y']." 年 ".$in['m']."月 订单数据";
	$objActSheet->setCellValue('A1', $titlemsg);

	$statsql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['m']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus!=8 and OrderStatus!=9 group by left(OrderSN,8)";
	$statdata = $db->get_results($statsql);

	$statsql0  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['m']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus=0 group by left(OrderSN,8)";
	$rdata = $db->get_results($statsql0);

	$totalnumber0  = 0;
	$totalprice0   = 0;
	foreach($rdata as $rvar)
	{
		$rarr[$rvar['ODate']] = $rvar['totalnumber'];
		$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
		$totalprice0 = $totalprice0 + $rvar['OTotal'];
	}
	
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '日期');
		$objActSheet->setCellValue('B'.$k, '订单金额');
		$objActSheet->setCellValue('C'.$k, '订单数');
		$objActSheet->setCellValue('D'.$k, '待审核订单');

		$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':D'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->getStyle('B'.$k.':D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		if(!empty($statdata))
		{
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];
				if(empty($rarr[$var['ODate']])) $rarr[$var['ODate']] = 0;
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $var['ODate'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $var['OTotal']);
				$objActSheet->setCellValue('C'.$k, $var['totalnumber']);
				$objActSheet->setCellValue('D'.$k, $rarr[$var['ODate']]);
			}
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $totalm);
				$objActSheet->setCellValue('C'.$k, $totaln);
				$objActSheet->setCellValue('D'.$k, $totalnumber0.'个 (¥ '.$totalprice0.')');
				$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:D'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:D'.$k)->applyFromArray($styleThinBlackBorderOutline);
		$filename = '月订单统计报表_'.$in['y'].'_'.$in['m'].'.xls';

}elseif($in['action']=="year"){

	if(empty($in['y'])) $in['y'] = date("Y");
	$titlemsg = $in['y']." 年 订单数据";
	$objActSheet->setCellValue('A1', $titlemsg);

		$statsql  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus!=8 and OrderStatus!=9 group by left(OrderSN,6)";
		$statdata = $db->get_results($statsql);

		$statsql0  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus=0 group by left(OrderSN,6)";
		$rdata = $db->get_results($statsql0);

		$totalnumber0  = 0;
		$totalprice0   = 0;
		foreach($rdata as $rvar)
		{
			$rarr[$rvar['ODate']] = $rvar['totalnumber'];
			$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
			$totalprice0  = $totalprice0 + $rvar['OTotal'];
		}
	
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '日期');
		$objActSheet->setCellValue('B'.$k, '订单金额');
		$objActSheet->setCellValue('C'.$k, '订单数');
		$objActSheet->setCellValue('D'.$k, '待审核订单');

		$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':D'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->getStyle('B'.$k.':D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		if(!empty($statdata))
		{
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];
				if(empty($rarr[$var['ODate']])) $rarr[$var['ODate']] = 0;
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $var['ODate'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $var['OTotal']);
				$objActSheet->setCellValue('C'.$k, $var['totalnumber']);
				$objActSheet->setCellValue('D'.$k, $rarr[$var['ODate']]);
			}
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $totalm);
				$objActSheet->setCellValue('C'.$k, $totaln);
				$objActSheet->setCellValue('D'.$k, $totalnumber0.'个 (¥ '.$totalprice0.')');
				$objActSheet->getStyle('A'.$k.':D'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:D'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:D'.$k)->applyFromArray($styleThinBlackBorderOutline);
		$filename = '年订单统计报表_'.$in['y'].'.xls';

}elseif($in['action']=="day"){

	if(empty($in['cordate'])) $in['cordate'] = date("Y-m-d");
	$datemsg = str_replace("-","",$in['cordate']);
	$titlemsg = $in['cordate']." 订单数据";
	$objActSheet->setCellValue('A1', $titlemsg);

	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $cvar)
	{
		$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
	}


		$statsql  = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderDate,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and left(OrderSN,8)='".$datemsg."' and OrderStatus!=8 and OrderStatus!=9 order by OrderID asc limit 0,1000";
		$statdata = $db->get_results($statsql);
	
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '订单号');
		$objActSheet->setCellValue('B'.$k, '药店');
		$objActSheet->setCellValue('C'.$k, '订单金额');
		$objActSheet->setCellValue('D'.$k, '下单时间');
		$objActSheet->setCellValue('E'.$k, '订单状态');

		$objActSheet->getStyle('A'.$k.':E'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':E'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');

		if(!empty($statdata))
		{
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				$totalm = $totalm + $var['OrderTotal'];
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $var['OrderSN'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $clientarr[$var['OrderUserID']]);
				$objActSheet->setCellValue('C'.$k, $var['OrderTotal']);
				$objActSheet->setCellValue('D'.$k, date("Y-m-d",$var['OrderDate']));
				$objActSheet->setCellValue('E'.$k, $order_status_arr[$var['OrderStatus']]);
				
			}
			$k++;
			$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('C'.$k, $totalm);
			$objActSheet->getStyle('A'.$k.':E'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:E'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:E'.$k)->applyFromArray($styleThinBlackBorderOutline);
		$filename = '日订单统计报表_'.$in['cordate'].'.xls';

}elseif($in['action']=="between_return"){

	if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
	if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");
	
	$statsql  = "SELECT left(ReturnSN,9) as ODate,sum(ReturnTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sql2." and FROM_UNIXTIME(ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) group by left(ReturnSN,9)";
	$statdata = $db->get_results($statsql);
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 退单数据 ";
	$objActSheet->setCellValue('A1', $titlemsg);


		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '日期');
		$objActSheet->setCellValue('B'.$k, '金额');
		$objActSheet->setCellValue('C'.$k, '退单数');

		$objActSheet->getStyle('A'.$k.':C'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':C'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->getStyle('B'.$k.':C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		if(!empty($statdata))
		{
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];
				if(empty($rarr[$var['ODate']])) $rarr[$var['ODate']] = 0;
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $var['ODate'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $var['OTotal']);
				$objActSheet->setCellValue('C'.$k, $var['totalnumber']);

			}
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $totalm);
				$objActSheet->setCellValue('C'.$k, $totaln);

				$objActSheet->getStyle('A'.$k.':C'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:C'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:C'.$k)->applyFromArray($styleThinBlackBorderOutline);
		$filename = '退单统计报表_'.$in['begindate'].'_'.$in['enddate'].'.xls';

}elseif($in['action']=='salers'){
    //客情官订单统计
    if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
    if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");
    $begin = strtotime(date('Y-m-d 00:00:00',strtotime($in['begindate'])));
    $end = strtotime(date('Y-m-d 23:59:59',strtotime($in['enddate'])));

    $datasql   = "SELECT UserID,UserName,UserTrueName,UserPhone,UserLogin,UserLoginIP,UserLoginDate,UserFlag FROM ".DATABASEU.DATATABLE."_order_user where UserCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and UserFlag!='1' and UserType='S' ORDER BY UserID DESC";
    $saler = $db->get_results($datasql);
    $calc_sql = "SELECT SUM(OrderTotal) AS total,COUNT(1) AS cnt,SalerID FROM ".DATATABLE."_order_orderinfo AS o
              LEFT JOIN ".DATATABLE."_order_salerclient AS s
              ON s.ClientID=o.OrderUserID
              WHERE o.OrderStatus < 8 AND s.CompanyID=".$_SESSION['uc']['CompanyID']." AND o.OrderDate between ".$begin." and ".$end."
              GROUP BY SalerID
              ORDER BY SalerID DESC";
    $calc_rst = $db->get_results($calc_sql);
    $calc = array();
    foreach($calc_rst as $val){
        $calc[$val['SalerID']] = $val;
    }
    foreach($saler as $key=>$val){
        $saler[$key]['cnts'] = $calc[$val['UserID']] ? $calc[$val['UserID']]['cnt'] : 0;
        $saler[$key]['total'] = $calc[$val['UserID']] ? $calc[$val['UserID']]['total'] : 0;
    }

    $k = 1;
    $objActSheet->setCellValue('A'.$k, '客情官从'.$in['begindate'].'到'.$in['enddate'].'订单统计');
    $k++;
    $objActSheet->setCellValue('A'.$k, '客情官');
    $objActSheet->setCellValue('B'.$k, '真实姓名');
    $objActSheet->setCellValue('C'.$k, '订单金额');
    $objActSheet->setCellValue('D'.$k, '订单数');

    $totals = 0;
    $cnts = 0;
    foreach($saler as $key=>$v){
        $k++;
        $objActSheet->setCellValue('A'.$k,$v['UserName'].' ');
        $objActSheet->setCellValue('B'.$k,$v['UserTrueName'].' ');
        $objActSheet->setCellValue('C'.$k,'¥'.$v['total']);
        $objActSheet->setCellValue('D'.$k,$v['cnts']);
        $totals += $v['total'];
        $cnts += $v['cnts'];
    }
    $k = $k+2;
    $objActSheet->setCellValue('A'.$k,'合计');
    $objActSheet->setCellValue('B'.$k,'');
    $objActSheet->setCellValue('C'.$k,'¥'.$totals);
    $objActSheet->setCellValue('D'.$k,$cnts);
    $filename = '客情官从'.$in['begindate'].'到'.$in['enddate'].'订单统计.xls';
} else if($in['action'] == 'deliver') {

    if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
    if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");

    $company = $_SESSION['uc']['CompanyID'];
    $datatable = DATATABLE;
    $appendMap = "";
    if($in['cid']) {
        $appendMap .= " AND c.ClientID=" . $in['cid'];
    }

    $sql = "SELECT s.ConsignmentDate,s.ConsignmentCompany,COUNT( DISTINCT c.OrderID) AS OrderCnt , COUNT( DISTINCT s.`ConsignmentID`) AS ConsignmentCnt, SUM(c.ContentSend * c.ContentPrice) AS Amount
                                FROM {$datatable}_order_consignment AS s
                                LEFT JOIN {$datatable}_order_orderinfo AS o
                                    ON o.OrderSN = s.ConsignmentOrder
                                INNER JOIN {$datatable}_order_out_library AS l
                                    ON l.ConsignmentID = s.ConsignmentID
                                LEFT JOIN {$datatable}_order_cart AS c
                                    ON c.ID = l.CartID
                                    WHERE s.ConsignmentCompany = {$company} {$appendMap} AND ConsignmentDate <> '0000-00-00' AND ConsignmentDate >= '{$in['begindate']} 00:00:00' AND ConsignmentDate <= '{$in['enddate']} 23:59:59'
                                GROUP BY s.ConsignmentDate ASC ";
    $list = $db->get_results($sql);

    $k = 1;
    $objActSheet->setCellValue('A'.$k, '从'.$in['begindate'].'到'.$in['enddate'].'发货统计');
    $k++;
    $objActSheet->setCellValue('A'.$k, '发货日期');
    $objActSheet->setCellValue('B'.$k, '发货金额');
    $objActSheet->setCellValue('C'.$k, '发货订单数');
    $objActSheet->setCellValue('D'.$k, '发货单数量');

    $amount = 0;
    $order = 0;
    $cons = 0;
    foreach($list as $key=>$v){
        $k++;
        $objActSheet->setCellValue('A'.$k,$v['ConsignmentDate'].' ');
        $objActSheet->setCellValue('B'.$k,'¥' . ($v['Amount'] ? $v['Amount'] : 0).' ');
        $objActSheet->setCellValue('C'.$k,$v['OrderCnt']);
        $objActSheet->setCellValue('D'.$k,$v['ConsignmentCnt']);
        $amount += $v['Amount'];
        $order += $v['OrderCnt'];
        $cons += $v['ConsignmentCnt'];
    }
    $k = $k+2;
    $objActSheet->setCellValue('A'.$k,'合计');
    $objActSheet->setCellValue('B'.$k,'¥' . $amount);
    $objActSheet->setCellValue('C'.$k,$order);
    $objActSheet->setCellValue('D'.$k,(int)$cons);
    $filename = '从'.$in['begindate'].'到'.$in['enddate'].'发货统计.xls';

}

$ua = $_SERVER["HTTP_USER_AGENT"];
//$filename = '商品统计报表_'.$in['begindate'].'_'.$in['enddate'].'.xls';
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

/**
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
***/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>