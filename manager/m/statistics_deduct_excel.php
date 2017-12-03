<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');

	$objPHPExcel = new PHPExcel();

	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate'] = date("Y-m-d");

	$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
								 ->setLastModifiedBy("DingHuoBao")
								 ->setTitle("医统天下-客情官提成")
								 ->setSubject("医统天下-客情官提成")
								 ->setDescription("医统天下-客情官提成")
								 ->setKeywords("医统天下 订货管理系统")
								 ->setCategory("客情官提成");

	$objPHPExcel->setActiveSheetIndex(0);
	$objActSheet = $objPHPExcel->getActiveSheet();
	$objActSheet->setTitle('客情官提成');
	$objActSheet->getDefaultRowDimension()->setRowHeight(20);
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 客情官提成 ";

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
	$objActSheet->setCellValue('A'.$k, '客情官');
	$objActSheet->setCellValue('B'.$k, '已发放笔数');
	$objActSheet->setCellValue('C'.$k, '已发放的提成');
	$objActSheet->setCellValue('D'.$k, '未放发的提成');
	$objActSheet->setCellValue('E'.$k, '总提成笔数');
	$objActSheet->setCellValue('F'.$k, '总的提成金额');

	$objActSheet->getStyle('A'.$k.':F'.$k)->getFont()->setBold(true);
	$objActSheet->getStyle('A'.$k.':F'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
	$objActSheet->getStyle('E'.$k.':F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				
	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
	{
		if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 years'));
		if(empty($in['enddate']))   $in['enddate'] = date("Y-m-d");
	}
	$sqll = '';
	if(!empty($in['sid'])) $sqll = " and DeductUser = ".$in['sid']." ";
	$statsql  = "SELECT DeductUser,sum(DeductTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_deduct where CompanyID=".$_SESSION['uinfo']['ucompany']."  ".$sqll." and FROM_UNIXTIME(DeductDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59'  group by DeductUser";
	$statdata = $db->get_results($statsql);

	$statsql0  = "SELECT DeductUser,sum(DeductTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_deduct where CompanyID=".$_SESSION['uinfo']['ucompany']." and DeductStatus='T' ".$sqll." and FROM_UNIXTIME(DeductDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59'  group by DeductUser";
	$rdata = $db->get_results($statsql0);
	
	foreach($rdata as $rvar)
	{
		$stdata[$rvar['DeductUser']] = $rvar;
	}
	$totalm = 0;
	$totaln = 0;
	$allttotal = $allftotal  = 0;
	$n=1;

	if(!empty($statdata))
	{
		$salerarr = null;
		$clientdata = $db->get_results("select UserID,UserName,UserTrueName from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserType='S'  order by UserID asc");
		foreach($clientdata as $areavar)
		{
			$salerarr[$areavar['UserID']] = $areavar['UserTrueName'];
		}

			foreach($statdata as $var)
			{
				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];

				if(empty($stdata[$var['DeductUser']]['totalnumber'])) $tnumber = 0; else $tnumber =$stdata[$var['DeductUser']]['totalnumber'];
				$alltnumber = $alltnumber + $tnumber;
				
				if(empty($stdata[$var['DeductUser']]['OTotal'])) $ttotal = 0; else $ttotal =$stdata[$var['DeductUser']]['OTotal'];
				$allttotal = $allttotal + $ttotal; 
				$ftotal = $var['OTotal'] - $ttotal;
				$allftotal = $allftotal + $ftotal; 

				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , $salerarr[$var['DeductUser']],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $tnumber);
				$objActSheet->setCellValue('C'.$k, $ttotal);
				$objActSheet->setCellValue('D'.$k, $ftotal);
				$objActSheet->setCellValue('E'.$k, $var['totalnumber']);
				$objActSheet->setCellValue('F'.$k, $var['OTotal']);
			}

			$k++;
			$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, $alltnumber);
			$objActSheet->setCellValue('C'.$k, $allttotal);
			$objActSheet->setCellValue('D'.$k, $allftotal);
			$objActSheet->setCellValue('E'.$k, $totaln);
			$objActSheet->setCellValue('F'.$k, $totalm);
			$objActSheet->getStyle('A'.$k.':F'.$k)->getFont()->setBold(true);
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
$filename = '提成统计报表_'.$in['begindate'].'_'.$in['enddate'].'.xls';
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