<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的订单!');
	exit;
}else{
	$idmsg = implode(",",$in['selectedID']);
}

$objPHPExcel->getProperties()->setCreator("订货宝 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("订货宝-订单数据")
							 ->setSubject("订货宝-订单数据")
							 ->setDescription("订货宝-订单数据")
							 ->setKeywords("订货宝 订货管理系统")
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
$objActSheet->getColumnDimension('C' )->setWidth(11);
$objActSheet->getColumnDimension('R' )->setWidth(11);
$objActSheet->getRowDimension('1')->setRowHeight(32);
$objActSheet->freezePane('A3');

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientNO from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
	$clientNo[$cvar['ClientID']] = $cvar['ClientNO'];
}

		$odata = $db->get_results("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID in (".$idmsg.") limit 0,20");

		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '订单号');
		$objActSheet->setCellValue('B'.$k, '药店');
		$objActSheet->setCellValue('C'.$k, '药店编号');
		$objActSheet->setCellValue('D'.$k, '配送方式');
		$objActSheet->setCellValue('E'.$k, '配送状态');
		$objActSheet->setCellValue('F'.$k, '付款方式');
		$objActSheet->setCellValue('G'.$k, '付款状态');
		$objActSheet->setCellValue('H'.$k, '收货单位');
		$objActSheet->setCellValue('I'.$k, '联系人');
		$objActSheet->setCellValue('J'.$k, '联系电话');
		$objActSheet->setCellValue('K'.$k, '收货地址');
		$objActSheet->setCellValue('L'.$k, '备注说明');
		$objActSheet->setCellValue('M'.$k, '原价金额');
		$objActSheet->setCellValue('N'.$k, '特价金额');
		$objActSheet->setCellValue('O'.$k, '订单状态');
		$objActSheet->setCellValue('P'.$k, '下单时间');
		$objActSheet->setCellValue('Q'.$k, '类型');
		$objActSheet->setCellValue('R'.$k, '交货日期');

		$objActSheet->getStyle('A'.$k.':R'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':R'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3');

	if(!empty($odata))
	{	
		foreach($odata as $var)
		{
			if(empty($var['OrderID'])) continue;
			$k++;
			$objActSheet->setCellValueExplicit('A'.$k , $var['OrderSN'],PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, $clientarr[$var['OrderUserID']]);
			$objActSheet->setCellValue('C'.$k, $clientNo[$var['OrderUserID']]);
			$objActSheet->setCellValue('D'.$k, $senttypearr[$var['OrderSendType']]);
			$objActSheet->setCellValue('E'.$k, $send_status_arr[$var['OrderSendStatus']]);
			$objActSheet->setCellValue('F'.$k, $paytypearr[$var['OrderPayType']]);
			$objActSheet->setCellValue('G'.$k, $pay_status_arr[$var['OrderPayStatus']]);
			$objActSheet->setCellValue('H'.$k, $var['OrderReceiveCompany']);
			$objActSheet->setCellValue('I'.$k, $var['OrderReceiveName']);
			$objActSheet->setCellValueExplicit('J'.$k, $var['OrderReceivePhone'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objActSheet->setCellValue('K'.$k, $var['OrderReceiveAdd']);
			$objActSheet->setCellValue('L'.$k, $var['OrderRemark']);
			if($var['OrderSpecial'] == 'T')
			{
			    $sql = "select SUM(ContentNumber * ContentPrice * ContentPercent / 10) as Total from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$var['OrderID'];
			    $cartTotal = $db->get_results($sql);

			    if(!empty($cartTotal))
			        $objActSheet->setCellValue('M'.$k, $cartTotal[0]['Total']);
			    else 
			        $objActSheet->setCellValue('M'.$k, $var['OrderTotal']);
			}
			else
			    $objActSheet->setCellValue('M'.$k, $var['OrderTotal']);
			$objActSheet->setCellValue('N'.$k, $var['OrderTotal']);
			$objActSheet->setCellValue('O'.$k, $order_status_arr[$var['OrderStatus']]);
			$objActSheet->setCellValue('P'.$k, date("Y-m-d H:i",$var['OrderDate']));
			$objActSheet->setCellValue('Q'.$k, $var['OrderType']);
			
			if(empty($var['DeliveryDate']) || $var['DeliveryDate'] == '0000-00-00')
			    $var['DeliveryDate'] = '';
		    $objActSheet->setCellValue('R'.$k, $var['DeliveryDate']);
		}
		$objActSheet->getStyle('A2:R'.$k)->getFont()->setSize(10);
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
		$objPHPExcel->getActiveSheet()->getStyle('A2:R'.$k)->applyFromArray($styleThinBlackBorderOutline);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setVisible(false);

$outputFileName = 'dhb_order_list_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>