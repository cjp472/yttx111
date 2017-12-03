<?php 
$menu_flag = "consignment";
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

$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-订单数据")
							 ->setSubject("医统天下-订单数据")
							 ->setDescription("医统天下-订单数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("订单数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('配货单数据');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 配货单');
$objActSheet->mergeCells('A1:F1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
$objActSheet->getColumnDimension('A' )->setWidth(18);
$objActSheet->getColumnDimension('B' )->setWidth(34);
$objActSheet->getColumnDimension('C' )->setWidth(18);
$objActSheet->getColumnDimension('D' )->setWidth(20);
$objActSheet->getRowDimension('1')->setRowHeight(32);
$objActSheet->freezePane('A3');


	$odata = $db->get_results("SELECT sum(ContentNumber) as Num,ContentID,Coding,Name,Barcode,Casing,Units FROM ".DATATABLE."_view_index_cart where OrderID in (".$idmsg.") and CompanyID = ".$_SESSION['uinfo']['ucompany']." group by ContentID Order by SiteID,ContentID asc");


	$gdata = $db->get_results("SELECT sum(ContentNumber) as Num,ContentID,Coding,Name,Barcode,Casing,Units FROM ".DATATABLE."_view_index_gifts where OrderID in (".$idmsg.") and CompanyID = ".$_SESSION['uinfo']['ucompany']." group by ContentID Order by SiteID,ContentID asc");
	foreach($gdata as $v){
		$garr[$v['ContentID']] = $v['Num'];
	}

	$k = 1;
	$k++;
	$objActSheet->setCellValue('A'.$k, '编号');
	$objActSheet->setCellValue('B'.$k, '商品名称');
	$objActSheet->setCellValue('C'.$k, '条码');
	$objActSheet->setCellValue('D'.$k, '包装');
	$objActSheet->setCellValue('E'.$k, '订购数');
	$objActSheet->setCellValue('F'.$k, '单位');

	$objActSheet->getStyle('A'.$k.':F'.$k)->getFont()->setBold(true);
	$objActSheet->getStyle('A'.$k.':F'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
	$objActSheet->freezePane('A3');
	$indataarr = null;

	if(!empty($odata))
	{	
		foreach($odata as $var)
		{
			if(empty($var['Num'])) continue;
			$k++;
			$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');
			if(!empty($garr[$var['ContentID']])){
				$var['Num']  = $var['Num'] + $garr[$var['ContentID']];
				$indataarr[] = $var['ContentID'];
			}
			$objActSheet->setCellValueExplicit('A'.$k , $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, $var['Name']);
			$objActSheet->setCellValueExplicit('C'.$k , $var['Barcode'],PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('D'.$k, $var['Casing']);
			$objActSheet->setCellValue('E'.$k, $var['Num']);
			$objActSheet->setCellValue('F'.$k, $var['Units']);
		}
	}

	if(!empty($gdata))
	{
		foreach($gdata as $var)
		{
			if(empty($var['Num']) || in_array($var['ContentID'],$indataarr)) continue;
			$k++;
			$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');
			$objActSheet->setCellValueExplicit('A'.$k, $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, $var['Name']);
			$objActSheet->setCellValueExplicit('C'.$k, $var['Barcode'],PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('D'.$k, $var['Casing']);
			$objActSheet->setCellValue('E'.$k, $var['Num']);
			$objActSheet->setCellValue('F'.$k, $var['Units']);
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
		//$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setVisible(false);

		
$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '配货单_'.date("Ymd").'_'.rand(1,999).'.xls';
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