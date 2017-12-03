<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的订单!');
	exit;
}else{
	$idmsg = implode(",",$in['selectedID']);
}

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(RESOURCE_PATH."import_397.xls");
$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();

$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']."  order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
}
$k = 1;

$sql = "select c.Coding,c.Units,c.Model,c.ID,c.OrderID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentNumber,c.ContentPercent,c.ContentSend,o.* from ".DATATABLE."_view_index_cart c inner join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.OrderID in (".$idmsg.") order by o.OrderID desc,c.SiteID asc,c.ID asc";
$odata = $db->get_results($sql);
$baseRow   = $k;
$oldsn = '';
if(!empty($odata))
{	
	foreach($odata as $oinfo)
	{
		$oinfo['OrderRemark'] = html_entity_decode($oinfo['OrderRemark'], ENT_QUOTES,'UTF-8');	

		$baseRow++;
		$oinfo['ContentName'] = html_entity_decode($oinfo['ContentName'], ENT_QUOTES,'UTF-8');
		$oinfo['Percent'] = (10 - $oinfo['ContentPercent']) * 10;
		$oinfo['ContentPrice'] = $oinfo['ContentPrice'] * $oinfo['ContentPercent'] / 10;
		$oinfo['Total'] = $oinfo['ContentPrice']*$oinfo['ContentNumber'];
		
		if($oldsn == $oinfo['OrderSN']){
			$objActSheet->setCellValue('A'.$baseRow, '');
			$objActSheet->setCellValue('B'.$baseRow, '');
			$objActSheet->setCellValue('C'.$baseRow, '');
			$objActSheet->setCellValue('D'.$baseRow, '');
			$objActSheet->setCellValue('E'.$baseRow, '');
			$objActSheet->setCellValue('F'.$baseRow, '');
			$objActSheet->setCellValue('G'.$baseRow, $oinfo['ContentName']);
			$objActSheet->setCellValue('H'.$baseRow, $oinfo['Model']);
			$objActSheet->setCellValue('I'.$baseRow, $oinfo['Units']);
			$objActSheet->setCellValue('J'.$baseRow, $oinfo['ContentNumber']);				
			$objActSheet->setCellValue('K'.$baseRow, $oinfo['ContentPrice']);

			$objActSheet->setCellValue('L'.$baseRow, $oinfo['Total']);
			$objActSheet->setCellValue('M'.$baseRow, '');
			$objActSheet->setCellValue('N'.$baseRow, $oinfo['Coding']);
			$objActSheet->setCellValue('O'.$baseRow, '');
			$objActSheet->setCellValue('P'.$baseRow, '');
		}else{
			$objActSheet->setCellValue('A'.$baseRow, 'Y');
			$objActSheet->setCellValue('B'.$baseRow, 'Y');
			$objActSheet->setCellValue('C'.$baseRow, date("Y-m-d",$oinfo['OrderDate']));
			$objActSheet->setCellValue('D'.$baseRow, $clientarr[$oinfo['OrderUserID']]);
			$objActSheet->setCellValue('E'.$baseRow, $oinfo['OrderSN']);
			$objActSheet->setCellValue('F'.$baseRow, '');
			$objActSheet->setCellValue('G'.$baseRow, $oinfo['ContentName']);
			$objActSheet->setCellValue('H'.$baseRow, $oinfo['Model']);
			$objActSheet->setCellValue('I'.$baseRow, $oinfo['Units']);
			$objActSheet->setCellValue('J'.$baseRow, $oinfo['ContentNumber']);				
			$objActSheet->setCellValue('K'.$baseRow, $oinfo['ContentPrice']);

			$objActSheet->setCellValue('L'.$baseRow, $oinfo['Total']);
			$objActSheet->setCellValue('M'.$baseRow, $oinfo['OrderRemark']);
			$objActSheet->setCellValue('N'.$baseRow, $oinfo['Coding']);
			$objActSheet->setCellValue('O'.$baseRow, '');
			$objActSheet->setCellValue('P'.$baseRow, '');
		}
		$oldsn = $oinfo['OrderSN'];
		$k = $baseRow;
	}

}

$outputFileName = 'dhb_order_tok3_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>