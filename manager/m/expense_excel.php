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
$objActSheet->setTitle('费用');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 其他款项');
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
$objActSheet->getColumnDimension('F' )->setWidth(32);
$objActSheet->getRowDimension('1')->setRowHeight(32);

	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $areavar)
	{
		$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
	}

	$billdata = $db->get_results("select BillID,BillName from ".DATATABLE."_order_expense_bill where CompanyID=".$_SESSION['uinfo']['ucompany']." ");
	foreach($billdata as $var)
	{
		$billarr[$var['BillID']] = $var['BillName'];
	}

	$odata = $db->get_results("SELECT * FROM ".DATATABLE."_order_expense where ExpenseID in (".$idmsg.") and CompanyID = ".$_SESSION['uinfo']['ucompany']." Order by ExpenseID Desc");
		
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, 'ID');
		$objActSheet->setCellValue('B'.$k, '药店');
		$objActSheet->setCellValue('C'.$k, '类型');
		$objActSheet->setCellValue('D'.$k, '金额');
		$objActSheet->setCellValue('E'.$k, '日期');
		$objActSheet->setCellValue('F'.$k, '备注说明');
		$objActSheet->setCellValue('G'.$k, '审核');
		$objActSheet->setCellValue('H'.$k, '操作员');

		$objActSheet->getStyle('A'.$k.':H'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':H'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3'); 
		
if(!empty($odata))
{
	foreach($odata as $var)
	{
		if(empty($var['ExpenseID'])) continue;
		$k++;
		$pmmsg = '';
		if($var['FlagID'] == "2") $flagmsg = '是'; else $flagmsg = '否';

		$objActSheet->setCellValueExplicit('A'.$k, $var['ExpenseID'],PHPExcel_Cell_DataType::TYPE_STRING);  
		$objActSheet->setCellValue('B'.$k, $clientarr[$var['ClientID']]);
		$objActSheet->setCellValue('C'.$k, $billarr[$var['BillID']]);
		$objActSheet->setCellValue('D'.$k, $var['ExpenseTotal']);
		$objActSheet->setCellValue('E'.$k, $var['ExpenseDate']);
		$objActSheet->setCellValue('F'.$k, $var['FinanceRemark']);
		$objActSheet->setCellValue('G'.$k, $flagmsg);
		$objActSheet->setCellValue('H'.$k, $var['ExpenseUser']);

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
	$objPHPExcel->getActiveSheet()->getStyle('A2:H'.$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:H'.$k)->getFont()->setSize(10);


$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '其他款项_'.date("Ymd").'_'.rand(1,999).'.xls';
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