<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
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
							 ->setTitle("医统天下-详细退货单数据")
							 ->setSubject("医统天下-详细退货单数据")
							 ->setDescription("医统天下-详细退货单数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("退货单详细");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('退货单');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 退货单');
$objActSheet->mergeCells('A1:H1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
$objActSheet->getColumnDimension('B' )->setWidth(36);
$objActSheet->getRowDimension('1')->setRowHeight(32);
$objActSheet->freezePane('A2');

$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
}

$odata = $db->get_results("SELECT ReturnID,ReturnSN,ReturnClient,ReturnOrder,ReturnSendType,ReturnProductW,ReturnProductB,ReturnAbout,ReturnDate FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." and ReturnID in (".$idmsg.") limit 0,20");
if(!empty($odata))
{
	$k = 1;
	foreach($odata as $oinfo)
	{
		$cartdata = $db->get_results("select c.*,i.Coding,i.Units from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ReturnID=".$oinfo['ReturnID']." order by c.ID asc");

		$k++;
		$objActSheet->setCellValue('A'.$k, '退单号：'.$oinfo['ReturnSN']);
		$objActSheet->mergeCells('A'.$k.':C'.$k);

		$objActSheet->setCellValue('D'.$k, '客户：'.$clientarr[$oinfo['ReturnClient']]);
		$objActSheet->mergeCells('D'.$k.':H'.$k);
		$objActSheet->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objActSheet->getStyle('A'.$k.':H'.$k)->getFont()->setBold(true);
		
		$k++;
		$objActSheet->setCellValue('A'.$k, '序号');
		$objActSheet->setCellValue('B'.$k, '商品名称');
		$objActSheet->setCellValue('C'.$k, '编号/货号');
		$objActSheet->setCellValue('D'.$k, '颜色/规格');
		$objActSheet->setCellValue('E'.$k, '单位');
		$objActSheet->setCellValue('F'.$k, '数量');
		$objActSheet->setCellValue('G'.$k, '单价');
		$objActSheet->setCellValue('H'.$k, '小计(元)');

		$objActSheet->getStyle('A'.$k.':H'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('F'.$k.':H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
		$alltotal  = 0;
		$allnumber = 0;
		$baseRow   = $k;
		$n=0;
		if(!empty($cartdata))
		{
		foreach($cartdata as $ckey=>$cvar)
		{
			$baseRow++;
			$n++;
			$linetotal = $cvar['ContentNumber'] * $cvar['ContentPrice'];
			$alltotal  = $alltotal + $linetotal;
			$allnumber = $allnumber + $cvar['ContentNumber'];
			$cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');

			$objActSheet->setCellValueExplicit('A'.$baseRow , $n,PHPExcel_Cell_DataType::TYPE_STRING);  
			$objActSheet->setCellValue('B'.$baseRow, $cvar['ContentName']);
			$objActSheet->setCellValue('C'.$baseRow, $cvar['Coding']);
			$objActSheet->setCellValue('D'.$baseRow, $cvar['ContentColor'].'/'.$cvar['ContentSpecification']);
			$objActSheet->setCellValue('E'.$baseRow, $cvar['Units']);
			$objActSheet->setCellValue('F'.$baseRow, $cvar['ContentNumber']);
			$objActSheet->setCellValue('G'.$baseRow, $cvar['ContentPrice']);
			$objActSheet->setCellValue('H'.$baseRow, $linetotal);			
		}
		}
		$alltotal = sprintf("%01.2f", round($alltotal,2));
		
		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '合计：');
		$objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($alltotal));
		$objActSheet->setCellValue('F'.$baseRow, $allnumber);
		$objActSheet->setCellValue('H'.$baseRow, $alltotal);
		$objActSheet->mergeCells('B'.$baseRow.':D'.$baseRow);
		$objActSheet->getStyle('A'.$baseRow.':H'.$baseRow)->getFont()->setBold(true);

		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '联 系 人：'.$cinfo['ClientTrueName']);
		$objActSheet->mergeCells('A'.$baseRow.':C'.$baseRow);
		$objActSheet->setCellValue('D'.$baseRow, '联系电话：'.$oinfo['ClientPhone']);
		$objActSheet->mergeCells('D'.$baseRow.':H'.$baseRow);
		
		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '货运方式：'.$oinfo['ReturnSendType']);
		$objActSheet->mergeCells('A'.$baseRow.':C'.$baseRow);
		$objActSheet->setCellValue('D'.$baseRow, '包装外观：'.$oinfo['ReturnProductW']."/".$oinfo['ReturnProductB']);
		$objActSheet->mergeCells('D'.$baseRow.':H'.$baseRow);

		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '原因说明：'.$oinfo['ReturnAbout']);
		$objActSheet->mergeCells('A'.$baseRow.':H'.$baseRow);
		$objActSheet->getStyle('A'.$baseRow)->getAlignment()->setWrapText(true);

		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':H'.$baseRow)->applyFromArray($styleThinBlackBorderOutline);

		$baseRow++;
		$objActSheet->mergeCells('A'.$baseRow.':H'.$baseRow);
		$objActSheet->getRowDimension($baseRow)->setRowHeight(30);
		
		$k = $baseRow;
	}
	$objActSheet->getStyle('A2:H'.$k)->getFont()->setSize(10);
}

$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '退单统计报表_'.date("Ymd").'_'.rand(1,999).'.xls';
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
$outputFileName = 'dhb_return_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
***/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>