<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的数据!');
	exit;
}else{
	$idmsg = implode(",",$in['selectedID']);
}

$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-收款数据")
							 ->setSubject("医统天下-收款数据")
							 ->setDescription("医统天下-收款数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("收款数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('应收款');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 应收款');
$objActSheet->mergeCells('A1:E1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
$objActSheet->getColumnDimension('C' )->setWidth(25);
$objActSheet->getColumnDimension('D' )->setWidth(32);
$objActSheet->getColumnDimension('E' )->setWidth(20);
$objActSheet->getRowDimension('1')->setRowHeight(32);


		$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
		foreach($sortarr as $areavar)
		{
			$areaarr[$areavar['AreaID']]   = $areavar['AreaName'];
		}

		$statsql2  = "SELECT sum(FinanceTotal) as Ftotal,FinanceClient from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') group by FinanceClient  ";
		$statdata2 = $db->get_results($statsql2);
		foreach($statdata2 as $v)
		{
			$rdata['finance'][$v['FinanceClient']] = $v['Ftotal'];
		}

		$statsql4 = "SELECT sum(ExpenseTotal) as Ftotal,ClientID from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." and FlagID = '2' group by ClientID ";
		$statdata4 = $db->get_results($statsql4);
		foreach($statdata4 as $v)
		{
			$rdata['expense'][$v['ClientID']] = $v['Ftotal'];
		}

// 		$statsqlt  = "SELECT sum(OrderTotal) as Ftotal,OrderUserID from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']."  and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 group by OrderUserID ";
		$statsqlt  = "SELECT sum(OrderTotal) as Ftotal,OrderUserID from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']."  and OrderStatus!=8 and OrderStatus!=9 group by OrderUserID ";
		$statdatat = $db->get_results($statsqlt);

		foreach($statdatat as $v)
		{
			$rdata['order'][$v['OrderUserID']] = $v['Ftotal'];
		}

		$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal,ReturnClient from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']."  and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) group by ReturnClient";
		$statdata1 = $db->get_results($statsqlt1);
		foreach($statdata1 as $v)
		{
			$rdata['return'][$v['ReturnClient']] = $v['Ftotal'];
		}
		
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '行号');
		$objActSheet->setCellValue('B'.$k, '地区');
		$objActSheet->setCellValue('C'.$k, '药店帐号');
		$objActSheet->setCellValue('D'.$k, '药店名称');
		$objActSheet->setCellValue('E'.$k, '期末应收');

		$objActSheet->getStyle('A'.$k.':E'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':E'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3'); 
		
		$clientdata = $db->get_results("select ClientID,ClientArea,ClientName,ClientCompanyName from ".DATATABLE."_order_client where ClientID IN (".$idmsg.") and ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0  order by ClientID asc");
		$n=0;
		$alltotal = 0;

		foreach($clientdata as $var)
		{
			$k++;
			$tall = floatval($rdata['order'][$var['ClientID']]) - floatval($rdata['return'][$var['ClientID']]) - floatval($rdata['expense'][$var['ClientID']]) - floatval($rdata['finance'][$var['ClientID']]);
			$alltotal = $alltotal + $tall;
			$tallshow = number_format($tall,2,'.',',');			
			$n++;
			$objActSheet->setCellValueExplicit('A'.$k, $n,PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, $areaarr[$var['ClientArea']]);
			$objActSheet->setCellValue('C'.$k, $var['ClientName']);
			$objActSheet->setCellValue('D'.$k, $var['ClientCompanyName']);
			$objActSheet->setCellValueExplicit('E'.$k, $tallshow,PHPExcel_Cell_DataType::TYPE_STRING);

		}	
			$alltotal = number_format($alltotal,2,'.',',');	
			$k++;
			$objActSheet->setCellValueExplicit('A'.$k, '',PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, '合计：');
			$objActSheet->setCellValue('C'.$k, '');
			$objActSheet->setCellValue('D'.$k, '');
			$objActSheet->setCellValueExplicit('E'.$k, $alltotal,PHPExcel_Cell_DataType::TYPE_STRING);

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
	$objActSheet->getStyle('A2:E'.$k)->getFont()->setSize(10);


$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '应收款_'.date("Ymd").'_'.rand(1,999).'.xls';
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
$outputFileName = 'dhb_expense_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
**/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>