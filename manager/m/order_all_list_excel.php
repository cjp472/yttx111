<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();


$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-订单数据")
							 ->setSubject("医统天下-订单数据")
							 ->setDescription("医统天下-订单数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("订单数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('订单数据');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 订单');
$objActSheet->mergeCells('A1:J1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
$objActSheet->getColumnDimension('A' )->setWidth(20);
$objActSheet->getColumnDimension('B' )->setWidth(28);
$objActSheet->getRowDimension('1')->setRowHeight(32);
$objActSheet->freezePane('A3');

$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
}

		$odata = $db->get_results("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." order by OrderID desc limit 0,10000");

		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '订单号');
		$objActSheet->setCellValue('B'.$k, '药店');
		$objActSheet->setCellValue('C'.$k, '配送方式');
		$objActSheet->setCellValue('D'.$k, '配送状态');
		$objActSheet->setCellValue('E'.$k, '付款方式');
		$objActSheet->setCellValue('F'.$k, '付款状态');
		$objActSheet->setCellValue('G'.$k, '收货单位');
		$objActSheet->setCellValue('H'.$k, '联系人');
		$objActSheet->setCellValue('I'.$k, '联系电话');
		$objActSheet->setCellValue('J'.$k, '收货地址');
		$objActSheet->setCellValue('K'.$k, '备注说明');
		$objActSheet->setCellValue('L'.$k, '总金额');
		//$objActSheet->setCellValue('M'.$k, '已付款金额');
		$objActSheet->setCellValue('N'.$k, '订单状态');
		$objActSheet->setCellValue('O'.$k, '下单时间');
		$objActSheet->setCellValue('P'.$k, '类型');

		$objActSheet->getStyle('A'.$k.':P'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':P'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3');

	if(!empty($odata))
	{	
		foreach($odata as $var)
		{
			if(empty($var['OrderID'])) continue;
			$k++;
			$objActSheet->setCellValueExplicit('A'.$k , $var['OrderSN'],PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, $clientarr[$var['OrderUserID']]);
			$objActSheet->setCellValue('C'.$k, $senttypearr[$var['OrderSendType']]);
			$objActSheet->setCellValue('D'.$k, $send_status_arr[$var['OrderSendStatus']]);
			$objActSheet->setCellValue('E'.$k, $paytypearr[$var['OrderPayType']]);
			$objActSheet->setCellValue('F'.$k, $pay_status_arr[$var['OrderPayStatus']]);
			$objActSheet->setCellValue('G'.$k, $var['OrderReceiveCompany']);
			$objActSheet->setCellValue('H'.$k, $var['OrderReceiveName']);
			$objActSheet->setCellValueExplicit('I'.$k, $var['OrderReceivePhone'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objActSheet->setCellValue('J'.$k, $var['OrderReceiveAdd']);
			$objActSheet->setCellValue('K'.$k, $var['OrderRemark']);
			$objActSheet->setCellValue('L'.$k, $var['OrderTotal']);
			//$objActSheet->setCellValue('M'.$k, $var['OrderIntegral']);
			$objActSheet->setCellValue('N'.$k, $order_status_arr[$var['OrderStatus']]);
			$objActSheet->setCellValue('O'.$k, date("Y-m-d H:i",$var['OrderDate']));
			$objActSheet->setCellValue('P'.$k, $var['OrderType']);
		}
		$objActSheet->getStyle('A2:P'.$k)->getFont()->setSize(10);
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setVisible(false);

		
$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '订单列表_'.date("Ymd").'_'.rand(1,999).'.xls';
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
/***		
$outputFileName = 'dhb_order_list_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
**/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>