<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

	$pay_send_arr = array(
		'1'			=>  '已付',
		'2'			=>  '到付'
 	 );
	$incept_arr = array(
		'0'			=>  '在途',
		'1'			=>  '确认到货',
		'2'			=>  '管理员确认'
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
							 ->setTitle("医统天下-发货数据")
							 ->setSubject("医统天下-发货数据")
							 ->setDescription("医统天下-发货数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("发货数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('发货单');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 发货单');
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
$objActSheet->getRowDimension('1')->setRowHeight(32);


	$datasql   = "SELECT LogisticsID,LogisticsName,LogisticsContact,LogisticsPhone,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." Order by LogisticsID ASC";
	$lvdata = $db->get_results($datasql);
	foreach($lvdata as  $lv)
	{
		$logarr[$lv['LogisticsID']] = $lv['LogisticsName'];
	}

	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $areavar)
	{
		$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
	}

	$odata = $db->get_results("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentID in (".$idmsg.") and ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." Order by ConsignmentID Desc limit 0,50");
		
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, 'ID');
		$objActSheet->setCellValue('B'.$k, '药店');
		$objActSheet->setCellValue('C'.$k, '相关订单');
		$objActSheet->setCellValue('D'.$k, '物流公司');
		$objActSheet->setCellValue('E'.$k, '运单号');
		$objActSheet->setCellValue('F'.$k, '经办人');
		$objActSheet->setCellValue('G'.$k, '发货日期');
		$objActSheet->setCellValue('H'.$k, '备注/说明');
		$objActSheet->setCellValue('I'.$k, '运费');
		$objActSheet->setCellValue('J'.$k, '收货人');
		$objActSheet->setCellValue('K'.$k, '收货公司');
		$objActSheet->setCellValue('L'.$k, '收货地址');
		$objActSheet->setCellValue('M'.$k, '联系电话');
		$objActSheet->setCellValue('N'.$k, '填写时间');
		$objActSheet->setCellValue('O'.$k, '操作人');
		$objActSheet->setCellValue('P'.$k, '状态');
		$objActSheet->getStyle('A'.$k.':P'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':P'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3');

if(!empty($odata))
{
	foreach($odata as $var)
	{
		if(empty($var['ConsignmentID'])) continue;
		$k++;
		$pmmsg = '';
		if(!empty($var['ConsignmentMoneyType']))
		{
			$pmmsg = $pay_send_arr[$var['ConsignmentMoneyType']].':'.$var['ConsignmentMoney'];
		}

		$objActSheet->setCellValueExplicit('A'.$k, $var['ConsignmentID'],PHPExcel_Cell_DataType::TYPE_STRING);  
		$objActSheet->setCellValue('B'.$k, $clientarr[$var['ConsignmentClient']]);
		$objActSheet->setCellValueExplicit('C'.$k, $var['ConsignmentOrder'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('D'.$k, $logarr[$var['ConsignmentLogistics']]);
		$objActSheet->setCellValueExplicit('E'.$k, $var['ConsignmentNO'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('F'.$k, $var['ConsignmentMan']);
		$objActSheet->setCellValue('G'.$k, $var['ConsignmentDate']);
		$objActSheet->setCellValue('H'.$k, $var['ConsignmentRemark']);
		$objActSheet->setCellValue('I'.$k, $pmmsg);
		$objActSheet->setCellValueExplicit('J'.$k, $var['InceptMan'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('K'.$k, $var['InceptCompany']);
		$objActSheet->setCellValue('L'.$k, $var['InceptAddress']);			
		$objActSheet->setCellValueExplicit('M'.$k, $var['InceptPhone'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('N'.$k, date("Y-m-d",$var['InputDate']));
		$objActSheet->setCellValue('O'.$k, $var['ConsignmentUser']);
		$objActSheet->setCellValue('P'.$k, $incept_arr[$var['ConsignmentFlag']]);	
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
	$objPHPExcel->getActiveSheet()->getStyle('A2:P'.$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:P'.$k)->getFont()->setSize(10);

$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '发货单_'.date("Ymd").'_'.rand(1,999).'.xls';
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