<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的商品!');
	exit;
}else{
	if($in['stype'] == "g"){
		$carttalbe = DATATABLE."_order_cart_gifts ";
	}else{
		$carttalbe = DATATABLE."_order_cart ";
	}

	$idmsg = implode(",",$in['selectedID']);
	$datasql = "SELECT 
    con.ConsignmentID,con.ConsignmentClient,con.ConsignmentOrder,con.ConsignmentNO,con.InputDate,l.ContentNumber,c.ContentID,c.ContentName,c.ID,c.OrderID,c.ContentColor,c.ContentSpecification,i.Name,i.Coding,i.Units,i.Price1,i.Price2,i.Casing 
  FROM
    ".DATATABLE."_order_consignment con 
    INNER JOIN ".DATATABLE."_order_out_library l 
      ON con.ConsignmentID = l.ConsignmentID 
      INNER JOIN ".$carttalbe." c ON c.ID=l.CartID
	  left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID
  WHERE i.CompanyID=".$_SESSION['uinfo']['ucompany']." and con.ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." and c.ID IN (".$idmsg.") order by con.ConsignmentID desc,c.ID desc limit 0,50";
	$list_data = $db->get_results($datasql);

	foreach($list_data as $v){
		$cidarr[] = $v['ConsignmentClient'];
	}
	
	//查询客户
	$cidmsg = implode(',',$cidarr);
	$csql = "select c.ClientID,c.ClientCompanyName,sc.SalerID from ".DATATABLE."_order_client c
			left join ".DATATABLE."_order_salerclient sc on c.ClientID = sc.ClientID AND sc.CompanyID = ".$_SESSION['uinfo']['ucompany']." 
			where c.ClientCompany = ".$_SESSION['uinfo']['ucompany']." and c.ClientID IN (".$cidmsg.")";
	$cdata = $db->get_results($csql);
// 	$cdata = array_column($cdata, null,'ClientID');
	
	//取客情官
	unset($arrOrderId,$csql);
	$arrOrderId = array_column($cdata, 'SalerID');
	$arrOrderId = array_filter(array_unique($arrOrderId));
	if($arrOrderId){
		$csql = 'SELECT UserID,UserTrueName FROM '.DATABASEU.DATATABLE.'_order_user WHERE UserID IN ('.implode(',', $arrOrderId).') ';
		$arrSale = $db->get_results($csql);
		$arrSale = array_column($arrSale, 'UserTrueName','UserID');
	}
	foreach($cdata as $v){
		$cdataarr[$v['ClientID']]['ClientName'] = $v['ClientCompanyName'];
		$cdataarr[$v['ClientID']]['SaleName'] = $arrSale[$v['SalerID']];
	}
	
	//查询订单信息
	$arrOrders = array();
	$arrOrderId = array_column($list_data, 'OrderID');
	$arrOrderId = array_filter(array_unique($arrOrderId));
	if($arrOrderId){
		$csql = 'SELECT OrderDate,OrderID,OrderRemark,OrderStatus FROM '.DATATABLE.'_order_orderinfo WHERE OrderID IN ('.implode(',', $arrOrderId).') ';
		$arrOrders = $db->get_results($csql);
		$arrOrders = array_column($arrOrders, null,'OrderID');
	}
	//取自定义字段数据
	unset($arrOrderId,$csql);
	$arrOrderId = array_column($list_data, 'ContentID');
	$arrOrderId = array_filter(array_unique($arrOrderId));
	if($arrOrderId){
		$csql = 'SELECT ContentIndexID,FieldContent FROM '.DATATABLE.'_order_content_1 WHERE ContentIndexID IN ('.implode(',', $arrOrderId).') ';
		$arrCunstomFiled = $db->get_results($csql);
		$arrCunstomFiled = array_column($arrCunstomFiled, 'FieldContent','ContentIndexID');
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
$objActSheet->mergeCells('A1:O1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

$objActSheet->getColumnDimension('A')->setWidth(36);
$objActSheet->getColumnDimension('B')->setWidth(16);
$objActSheet->getColumnDimension('C')->setWidth(16);
$objActSheet->getColumnDimension('E')->setWidth(20);
$objActSheet->getColumnDimension('F')->setWidth(20);
$objActSheet->getColumnDimension('H')->setWidth(16);
$objActSheet->getColumnDimension('I')->setWidth(25);
$objActSheet->getColumnDimension('K')->setWidth(20);
$objActSheet->getColumnDimension('L')->setWidth(15);
$objActSheet->getColumnDimension('M')->setWidth(20);
$objActSheet->getColumnDimension('N')->setWidth(20);
$objActSheet->getColumnDimension('O')->setWidth(50);
$objActSheet->getRowDimension('1')->setRowHeight(32);
	
		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '商品名称');
		$objActSheet->setCellValue('B'.$k, '供货价');
		$objActSheet->setCellValue('C'.$k, '金额');
		$objActSheet->setCellValue('D'.$k, '数量');
		$objActSheet->setCellValue('E'.$k, '包装规格');
		$objActSheet->setCellValue('F'.$k, '生产厂家');
		$objActSheet->setCellValue('G'.$k, '属性');
		$objActSheet->setCellValue('H'.$k, '进货价');
		$objActSheet->setCellValue('I'.$k, '客户');
		$objActSheet->setCellValue('J'.$k, '客情官');
		$objActSheet->setCellValue('K'.$k, '申请日期');
		$objActSheet->setCellValue('L'.$k, '状态');
		$objActSheet->setCellValue('M'.$k, '运单号');
		$objActSheet->setCellValue('N'.$k, '订单号');
		$objActSheet->setCellValue('O'.$k, '备注');
		$objActSheet->getStyle('A'.$k.':O'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':O'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
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
		
		$dnum  = $dnum + $var['ContentNumber'];
		
		$strStatus = $arrOrders[$var['OrderID']]['OrderStatus'];
		$strStatus = $order_status_arr[$strStatus];
		
		$strFiledVal = unserialize($arrCunstomFiled[$var['ContentID']]);

		$objActSheet->setCellValue('A'.$k, $var['Name']); 
		$objActSheet->setCellValue('B'.$k, $var['Price2']);
		$objActSheet->setCellValue('C'.$k, floatval($var['Price2']) * floatval($var['ContentNumber']));		
		$objActSheet->setCellValue('D'.$k, $var['ContentNumber']);	
		$objActSheet->setCellValueExplicit('E'.$k, $var['Casing'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue('F'.$k, $strFiledVal['FieldName_2']);
		$objActSheet->setCellValue('G'.$k, ($in['stype'] == 'g') ? '赠品':'销售');
		$objActSheet->setCellValue('H'.$k, $var['Price1']);
		$objActSheet->setCellValue('I'.$k, $cdataarr[$var['ConsignmentClient']]['ClientName']);
		$objActSheet->setCellValue('J'.$k, $cdataarr[$var['ConsignmentClient']]['SaleName']);
		$objActSheet->setCellValue('K'.$k, date("Y/m/d",$arrOrders[$var['OrderID']]['OrderDate']));
		$objActSheet->setCellValue('L'.$k, $strStatus);
		$objActSheet->setCellValueExplicit('M'.$k, $var['ConsignmentNO'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValueExplicit('N'.$k, $var['ConsignmentOrder'],PHPExcel_Cell_DataType::TYPE_STRING);
		
		$objActSheet->setCellValue('O'.$k, $arrOrders[$var['OrderID']]['OrderRemark']);
		$objActSheet->getStyle('O'.$k)->getAlignment()->setWrapText(true);
		
		unset($strStatus,$strFiledVal);
	}
	
// 		$k++;
// 		$objActSheet->setCellValue('A'.$k, '合计:'); 
// 		$objActSheet->setCellValueExplicit('B'.$k, '');
// 		$objActSheet->setCellValue('C'.$k, '');		
// 		$objActSheet->setCellValue('D'.$k, '');	
// 		$objActSheet->setCellValueExplicit('E'.$k, '');
// 		$objActSheet->setCellValue('F'.$k, $dnum);
// 		$objActSheet->setCellValue('G'.$k, '');
// 		$objActSheet->setCellValue('H'.$k, '');
// 		$objActSheet->setCellValue('I'.$k, '');

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
	$objPHPExcel->getActiveSheet()->getStyle('A2:O'.$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:O'.$k)->getFont()->setSize(10);

	
$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '发货明细_'.date("Ymd").'_'.rand(1,999).'.xls';
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