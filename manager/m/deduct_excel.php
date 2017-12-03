<?php 
$menu_flag = "saler";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的记录!');
	exit;
}else{
	$idmsg = implode(",",$in['selectedID']);
}

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
								 ->setLastModifiedBy("DingHuoBao")
								 ->setTitle("医统天下-提成明细")
								 ->setSubject("医统天下-提成明细")
								 ->setDescription("医统天下-提成明细")
								 ->setKeywords("医统天下 订货管理系统")
								 ->setCategory("提成明细");

	$objPHPExcel->setActiveSheetIndex(0);
	$objActSheet = $objPHPExcel->getActiveSheet();
	$objActSheet->setTitle('提成明细');
	$objActSheet->getDefaultRowDimension()->setRowHeight(20);
	$titlemsg = "客情官提成明细 ";

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
	$objActSheet->getColumnDimension('D' )->setWidth(20);
	$objActSheet->getColumnDimension('E' )->setWidth(20);
	$objActSheet->getColumnDimension('F' )->setWidth(20);
	$objActSheet->getRowDimension('1')->setRowHeight(32);
	$objActSheet->freezePane('A3');

	$k = 1;
	$k++;
	$objActSheet->setCellValue('A'.$k, '药店');
	$objActSheet->setCellValue('B'.$k, '客情官');
	$objActSheet->setCellValue('C'.$k, '订单号');
	$objActSheet->setCellValue('D'.$k, '提成金额');
	$objActSheet->setCellValue('E'.$k, '发放状态');
	$objActSheet->setCellValue('F'.$k, '发放时间');

	$objActSheet->getStyle('A'.$k.':F'.$k)->getFont()->setBold(true);
	$objActSheet->getStyle('A'.$k.':F'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
	$objActSheet->getStyle('E'.$k.':F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$datasql   = "SELECT * FROM ".DATATABLE."_order_deduct where DeductID IN (".$idmsg.") and CompanyID = ".$_SESSION['uinfo']['ucompany']."  Order by DeductID Desc";
	$list_data = $db->get_results($datasql);
	
	if(!empty($list_data))
	{
		$cinfo = $db->get_results("select ClientID,ClientCompanyName FROM ".DATATABLE."_order_client  where ClientCompany=".$_SESSION['uinfo']['ucompany']."  limit 0,10000");
		foreach($cinfo as  $lv)
		{
			$clientarr[$lv['ClientID']] = $lv['ClientCompanyName'];
		}
		$uinfo = $db->get_results("SELECT UserID,UserName,UserTrueName FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']."  and UserType='S' limit 0,1000");
		foreach($uinfo as  $lv)
		{
			$salerarr[$lv['UserID']] = $lv['UserTrueName'];
		}
		foreach($list_data as $lsv)
		{
			if($lsv['DeductStatus']=="T") $smsg = '已发放'; else $smsg = '未发放';
			if(!empty($lsv['DeductToDate'])) $dmsg = date("Y-m-d",$lsv['DeductToDate']); else $dmsg = '';

				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $clientarr[$lsv['ClientID']],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $salerarr[$lsv['DeductUser']]);
				$objActSheet->setCellValue('C'.$k, $lsv['OrderSN']);
				$objActSheet->setCellValue('D'.$k, $lsv['DeductTotal']);
				$objActSheet->setCellValue('E'.$k, $smsg);
				$objActSheet->setCellValue('F'.$k, $dmsg);
			}
		}

		$objActSheet->getStyle('A2:F'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:F'.$k)->applyFromArray($styleThinBlackBorderOutline);

$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '提成_'.date("Ymd").'_'.rand(1,999).'.xls';
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
$outputFileName = 'deduct.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
**/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>