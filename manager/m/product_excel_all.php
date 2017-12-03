<?php 
$menu_flag = "product";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();


$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-商品数据")
							 ->setSubject("医统天下-商品数据")
							 ->setDescription("医统天下-商品数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("商品数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('商品数据');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 商品列表');
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
$objActSheet->getColumnDimension('B' )->setWidth(12);
$objActSheet->getColumnDimension('C' )->setWidth(36);
$objActSheet->getColumnDimension('M' )->setWidth(36);
$objActSheet->getRowDimension('1')->setRowHeight(32);

$sitedata = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." order by SiteID asc");
foreach($sitedata as $cvar)
{
	$sitearr[$cvar['SiteID']] = $cvar['SiteName'];
}

	$odata = $db->get_results("SELECT * FROM ".DATATABLE."_order_content_index where  CompanyID = ".$_SESSION['uinfo']['ucompany']."  and FlagID=0 ORDER BY OrderID DESC, ID DESC limit 0,5000");
		
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, 'ID');
		$objActSheet->setCellValue('B'.$k, '商品分类');
		$objActSheet->setCellValue('C'.$k, '商品名称');
		$objActSheet->setCellValue('D'.$k, '拼音码');
		$objActSheet->setCellValue('E'.$k, '编号');
		$objActSheet->setCellValue('F'.$k, '价格1');
		$objActSheet->setCellValue('G'.$k, '价格2');
		$objActSheet->setCellValue('H'.$k, '药店价');
		$objActSheet->setCellValue('I'.$k, '单位');
		$objActSheet->setCellValue('J'.$k, '包装');
		$objActSheet->setCellValue('K'.$k, '颜色');
		$objActSheet->setCellValue('L'.$k, '规格');
		$objActSheet->setCellValue('M'.$k, '图片');
		$objActSheet->setCellValue('N'.$k, '人气');
		$objActSheet->getStyle('A'.$k.':N'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':N'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3');

if(!empty($odata))
{
	foreach($odata as $var)
	{
		if(empty($var['Name'])) continue;
		$k++;
		$pmsg = '';
		if(!empty($var['Price3']))
		{
			$parr = unserialize(urldecode($var['Price3']));
			if(empty($parr['typeid'])) $parr['typeid'] = 'A';
			foreach($parr as $pkey=>$pvar)
			{
				if($pkey=="typeid") continue;
				$pmsg .= $parr['typeid']."_".$pkey.':'.$pvar.',';
			}
		}
		$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');

		$objActSheet->setCellValueExplicit('A'.$k , $var['ID'],PHPExcel_Cell_DataType::TYPE_STRING);  
		$objActSheet->setCellValue('B'.$k, $sitearr[$var['SiteID']]);
		$objActSheet->setCellValue('C'.$k, $var['Name']);
		$objActSheet->setCellValue('D'.$k, $var['Pinyi']);
		$objActSheet->setCellValueExplicit('E'.$k, $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('F'.$k, $var['Price1']);
		$objActSheet->setCellValue('G'.$k, $var['Price2']);
		$objActSheet->setCellValue('H'.$k, $pmsg);
		$objActSheet->setCellValue('I'.$k, $var['Units']);
		$objActSheet->setCellValue('J'.$k, $var['Casing']);
		$objActSheet->setCellValue('K'.$k, $var['Color']);			
		$objActSheet->setCellValue('L'.$k, $var['Specification']);
		$objActSheet->setCellValue('M'.$k, $var['Picture']);
		$objActSheet->setCellValue('N'.$k, $var['Count']);
	
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
	$objPHPExcel->getActiveSheet()->getStyle('A2:N'.$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:N'.$k)->getFont()->setSize(10);


$outputFileName = 'dhb_product_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>