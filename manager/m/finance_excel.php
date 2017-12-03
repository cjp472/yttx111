<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();


	$finance_arr = array(
		'0'			=>  '在途',
		'2'			=>  '确认到帐'
 	 );

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的商品!');
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
$objActSheet->setTitle('收款单');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 收款单');
$objActSheet->mergeCells('A1:P1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
$objActSheet->getColumnDimension('B' )->setWidth(25);
$objActSheet->getColumnDimension('D' )->setWidth(32);
$objActSheet->getRowDimension('1')->setRowHeight(32);


	$accarr = $db->get_results("SELECT AccountsID,AccountsBank,AccountsNO,AccountsName FROM ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['uinfo']['ucompany']." ORDER BY AccountsID ASC ");
	foreach($accarr as $accvar)
	{
		$accancearr[$accvar['AccountsID']] = $accvar['AccountsBank'].'('.$accvar['AccountsNO'].')';
	}

	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $areavar)
	{
		$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
	}

	$odata = $db->get_results("SELECT * FROM ".DATATABLE."_order_finance where FinanceID in (".$idmsg.") and FinanceCompany = ".$_SESSION['uinfo']['ucompany']." Order by FinanceID Desc limit 0,50");
		
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, 'ID');
		$objActSheet->setCellValue('B'.$k, '药店');
		$objActSheet->setCellValue('C'.$k, '相关订单');
		$objActSheet->setCellValue('D'.$k, '收款帐号');
		$objActSheet->setCellValue('E'.$k, '收款金额');
		$objActSheet->setCellValue('F'.$k, '备注说明');
		$objActSheet->setCellValue('G'.$k, '付款时间');
		$objActSheet->setCellValue('H'.$k, '确认时间');
		$objActSheet->setCellValue('I'.$k, '填写时间');
		$objActSheet->setCellValue('J'.$k, '操作员');
		$objActSheet->setCellValue('K'.$k, '确认操作员');
		$objActSheet->setCellValue('L'.$k, '状态');
		$objActSheet->getStyle('A'.$k.':L'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':L'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3'); 
		
if(!empty($odata))
{
	foreach($odata as $var)
	{
		if(empty($var['FinanceID'])) continue;
		$k++;
		$pmmsg = '';
		if(empty($var['FinanceOrder'])) $var['FinanceOrder'] = '';

		$objActSheet->setCellValueExplicit('A'.$k, $var['FinanceID'],PHPExcel_Cell_DataType::TYPE_STRING);  
		$objActSheet->setCellValue('B'.$k, $clientarr[$var['FinanceClient']]);
		$objActSheet->setCellValueExplicit('C'.$k, $var['FinanceOrder'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('D'.$k, $accancearr[$var['FinanceAccounts']]);
		$objActSheet->setCellValue('E'.$k, $var['FinanceTotal']);
		$objActSheet->setCellValue('F'.$k, $var['FinanceAbout']);
		$objActSheet->setCellValue('G'.$k, $var['FinanceToDate']);
		$objActSheet->setCellValue('H'.$k, date("Y-m-d H:i",$var['FinanceUpDate']));
		$objActSheet->setCellValue('I'.$k, date("Y-m-d H:i",$var['FinanceDate']));
		$objActSheet->setCellValue('J'.$k, $var['FinanceUser']);
		$objActSheet->setCellValue('K'.$k, $var['FinanceAdmin']);		
		$objActSheet->setCellValue('L'.$k, $finance_arr[$var['FinanceFlag']]);	
	}	
}

	
	//设置边框
	$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
	$objPHPExcel->getActiveSheet()->getStyle('A2:L'.$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:L'.$k)->getFont()->setSize(10);

$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '付款单报表_'.date("Ymd").'_'.rand(1,999).'.xls';
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

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>