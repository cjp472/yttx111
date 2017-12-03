<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();
$basenumber = 500;

if(empty($in['b']))
{
	$endsql = ' limit 0,'.$basenumber;
	$in['b'] = 0;
}else{
	$baeginnumber = $basenumber * intval($in['b']);
	$endsql = ' limit '.$baeginnumber.','.$basenumber;
}
$endsql = '';
$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-库存数据")
							 ->setSubject("医统天下-库存数据")
							 ->setDescription("医统天下-库存数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("库存数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('库存');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 库存状况'.'('.$baeginnumber.')');
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

$sitedata = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." order by SiteID asc");
foreach($sitedata as $cvar)
{
	$sitearr[$cvar['SiteID']] = $cvar['SiteName'];
}

$odata = $db->get_results("SELECT i.ID,i.SiteID,i.Name,i.Coding,i.Units,i.Casing,i.Color,i.Specification,i.Price1,n.OrderNumber,n.ContentNumber FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_number n on i.ID=n.ContentID where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." and i.FlagID=0 ORDER BY i.OrderID DESC, i.ID DESC ".$endsql);

$k = 1;
$k++;
$objActSheet->setCellValue('A'.$k, 'ID');
$objActSheet->setCellValue('B'.$k, '商品名称');
$objActSheet->setCellValue('C'.$k, '编号/货号');
$objActSheet->setCellValue('D'.$k, '价格');
$objActSheet->setCellValue('E'.$k, '可选颜色');
$objActSheet->setCellValue('F'.$k, '可选规格');
$objActSheet->setCellValue('G'.$k, '可用库存');
$objActSheet->setCellValue('H'.$k, '实际库存');
$objActSheet->setCellValue('I'.$k, '单位');

$objActSheet->getStyle('A'.$k.':I'.$k)->getFont()->setBold(true);
$objActSheet->getStyle('A'.$k.':I'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
$objActSheet->freezePane('A3');

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
			$objActSheet->setCellValue('D'.$k, $var['Price1']);
			$objActSheet->setCellValue('E'.$k, $var['Color']);
			$objActSheet->setCellValue('F'.$k, $var['Specification']);
			$objActSheet->setCellValue('G'.$k, $var['OrderNumber']);
			$objActSheet->setCellValue('H'.$k, $var['ContentNumber']);
			$objActSheet->setCellValue('I'.$k, $var['Units']);
		}
		$objActSheet->getStyle('A2:I'.$k)->getFont()->setSize(10);
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
$filename = '库存状况_'.$in['b'].'_'.date("Ymd").'_'.rand(1,999).'.xls';
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