<?php 
$menu_flag = "inventory";
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

$objPHPExcel->getProperties()->setCreator("订货宝 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("订货宝-库存预警")
							 ->setSubject("订货宝-库存预警")
							 ->setDescription("订货宝-库存预警")
							 ->setKeywords("订货宝 订货管理系统")
							 ->setCategory("库存预警");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('库存');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 库存预警');
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
$objActSheet->getColumnDimension('B' )->setWidth(38);
$objActSheet->getColumnDimension('C' )->setWidth(15);
$objActSheet->getRowDimension('1')->setRowHeight(32);
$objActSheet->freezePane('A3');

	if($in['ty'] == 'up'){
		$fieldsql = " i.LibraryDown,i.LibraryUp as Library, ";
	}else{
		$fieldsql = " i.LibraryDown as Library,i.LibraryUp, ";
	}

$odata = $db->get_results("SELECT i.ID,i.SiteID,i.Name,i.Coding,i.Barcode,i.Units,i.Casing,i.Color,i.Specification,".$fieldsql." n.OrderNumber,n.ContentNumber FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_number n on i.ID=n.ContentID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." and i.ID in (".$idmsg.") and i.FlagID=0 ORDER BY i.OrderID DESC, i.ID DESC limit 0,100");

$k = 1;
$k++;
$objActSheet->setCellValue('A'.$k, 'ID');
$objActSheet->setCellValue('B'.$k, '商品名称');
$objActSheet->setCellValue('C'.$k, '编号');
$objActSheet->setCellValue('D'.$k, '条码');
$objActSheet->setCellValue('E'.$k, '包装');
$objActSheet->setCellValue('F'.$k, '可用库存');
$objActSheet->setCellValue('G'.$k, '实际库存');
$objActSheet->setCellValue('H'.$k, '报警值');
$objActSheet->setCellValue('I'.$k, '单位');


$objActSheet->getStyle('A'.$k.':I'.$k)->getFont()->setBold(true);
$objActSheet->getStyle('A'.$k.':I'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
$objActSheet->freezePane('A3');
$total1 = $total2 = 0;
	if(!empty($odata))
	{	
		foreach($odata as $var)
		{
			if(empty($var['ID'])) continue;
			$k++;
			if(empty($var['OrderNumber'])) $var['OrderNumber'] = 0;
			if(empty($var['ContentNumber'])) $var['ContentNumber'] = 0;
			$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');

			$objActSheet->setCellValueExplicit('A'.$k , $var['ID'],PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$k, $var['Name']);
			$objActSheet->setCellValueExplicit('C'.$k , $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objActSheet->setCellValueExplicit('D'.$k , $var['Barcode'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objActSheet->setCellValue('E'.$k, $var['Casing']);
			$objActSheet->setCellValue('F'.$k, $var['OrderNumber']);
			$objActSheet->setCellValue('G'.$k, $var['ContentNumber']);
			$objActSheet->setCellValue('H'.$k, $var['Library']);
			$objActSheet->setCellValue('I'.$k, $var['Units']);
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
		$objPHPExcel->getActiveSheet()->getStyle('A2:I'.$k)->applyFromArray($styleThinBlackBorderOutline);

$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '库存预警_'.date("Ymd").'_'.rand(1,999).'.xls';
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