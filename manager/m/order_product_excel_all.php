<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();
set_time_limit(480);
if(!empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的商品!');
	exit;
}else{

	if(empty($in['page'])){
		$in['page'] = 1;
	}
	$limitmsg = " limit ".(($in['page']-1)*10000).", 10000";

	$datasql   = "SELECT Name,Coding,Casing,Barcode,Units,OrderID,ClientID,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent,ContentSend FROM ".DATATABLE."_view_index_cart where CompanyID = ".$_SESSION['uinfo']['ucompany']."  order by ID desc ".$limitmsg;

	$datasql = "select i.Name,i.Coding,i.Casing,i.Barcode,i.Units,o.OrderSN,c.OrderID,c.ClientID,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentNumber,c.ContentPercent,c.ContentSend from rsung_order_orderinfo o inner join rsung_order_cart c ON o.OrderID=c.OrderID inner join rsung_order_content_index i ON c.ContentID=i.ID where o.OrderCompany = ".$_SESSION['uinfo']['ucompany']." 
	 and OrderStatus <> 8 and OrderStatus <>9 ";

	$list_data = $db->get_results($datasql);

	foreach($list_data as $v){
		$cidarr[] = $v['ClientID'];
		$oidarr[] = $v['OrderID'];
	}
	$cidmsg = implode(',',$cidarr);
	$oidarr = implode(',',$oidarr);
	$csql = "select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ";
	$cdata = $db->get_results($csql);
	foreach($cdata as $v){
		$cdataarr[$v['ClientID']] = $v['ClientCompanyName'];
	}

}

$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-商品数据")
							 ->setSubject("医统天下-商品数据")
							 ->setDescription("医统天下-商品数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("商品数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('订单商品明细');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 订单商品明细');
$objActSheet->mergeCells('A1:J1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

$objActSheet->getColumnDimension('A')->setWidth(25);
$objActSheet->getColumnDimension('B')->setWidth(16);
$objActSheet->getColumnDimension('C')->setWidth(36);
$objActSheet->getColumnDimension('J')->setWidth(25);
$objActSheet->getRowDimension('1')->setRowHeight(32);
	
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '药店');
		$objActSheet->setCellValue('B'.$k, '商品编号');
		$objActSheet->setCellValue('C'.$k, '商品名称');
		$objActSheet->setCellValue('D'.$k, '颜色');
		$objActSheet->setCellValue('E'.$k, '规格');
		$objActSheet->setCellValue('F'.$k, '订购数');
		$objActSheet->setCellValue('G'.$k, '发货数');
		$objActSheet->setCellValue('H'.$k, '单位');
		$objActSheet->setCellValue('I'.$k, '订购价');
		$objActSheet->setCellValue('J'.$k, '订单号');		
		$objActSheet->getStyle('A'.$k.':J'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':J'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3');

if(!empty($list_data))
{
	$dnum = $snum = $total = 0;
	foreach($list_data as $var)
	{
		if(empty($var['Name'])) continue;
		$k++;
		$pmsg = '';
		$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');
		$var['Price'] = $var['ContentPrice'] * $var['ContentPercent'] / 10;
		
		$dnum  = $dnum + $var['ContentNumber'];
		$snum  = $snum + $var['ContentSend'];
		$total = $total + $var['Price'] * $var['ContentNumber'];

		$objActSheet->setCellValue('A'.$k, $cdataarr[$var['ClientID']]); 
		$objActSheet->setCellValueExplicit('B'.$k, $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('C'.$k, $var['Name']);		
		$objActSheet->setCellValue('D'.$k, $var['ContentColor']);	
		$objActSheet->setCellValueExplicit('E'.$k, $var['ContentSpecification'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('F'.$k, $var['ContentNumber']);
		$objActSheet->setCellValue('G'.$k, $var['ContentSend']);
		$objActSheet->setCellValue('H'.$k, $var['Units']);
		$objActSheet->setCellValue('I'.$k, $var['Price']);
		$objActSheet->setCellValue('J'.$k, $var['OrderSN']);
	}
	
		$k++;
		$objActSheet->setCellValue('A'.$k, '合计:'); 
		$objActSheet->setCellValueExplicit('B'.$k, '');
		$objActSheet->setCellValue('C'.$k, '');		
		$objActSheet->setCellValue('D'.$k, '');	
		$objActSheet->setCellValueExplicit('E'.$k, '');
		$objActSheet->setCellValue('F'.$k, $dnum);
		$objActSheet->setCellValue('G'.$k, $snum);
		$objActSheet->setCellValue('H'.$k, '');
		$objActSheet->setCellValue('I'.$k, $total);
		$objActSheet->setCellValue('J'.$k, '');
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
	$objPHPExcel->getActiveSheet()->getStyle('A2:J'.$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:J'.$k)->getFont()->setSize(10);

	
$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '订单商品明细_'.date("Ymd").'_'.rand(1,999).'.xls';
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