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

$example_name = RESOURCE_PATH."example/kis2.xls";

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load($example_name);
$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();

$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']."  order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
}
$k = 1;

$sql = "select c.Coding,c.Units,c.ID,c.OrderID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentNumber,c.ContentPercent,c.ContentSend,o.* from ".DATATABLE."_view_index_cart c inner join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.OrderID in (".$idmsg.") order by o.OrderID desc,c.SiteID asc,c.ID asc";
$odata = $db->get_results($sql);
$baseRow   = $k;
if(!empty($odata))
{	
	foreach($odata as $oinfo)
	{
		$oinfo['OrderRemark'] = html_entity_decode($oinfo['OrderRemark'], ENT_QUOTES,'UTF-8');	
		$taxprice  = $oinfo['ContentPrice'] + $oinfo['ContentPrice'] * $oinfo['InvoiceTax']/100;
		$linetotal = $oinfo['ContentNumber'] * $oinfo['ContentPrice']*$oinfo['ContentPercent']/10;
		$taxtotal  = $oinfo['ContentPrice'] * $oinfo['ContentPercent'] / 10 * (1+$oinfo['InvoiceTax']/100);

		$baseRow++;
		$oinfo['ContentName'] = html_entity_decode($oinfo['ContentName'], ENT_QUOTES,'UTF-8');
		$oinfo['Percent'] = (10 - $oinfo['ContentPercent']) * 10;
		$oinfo['PercentPrice'] = $oinfo['ContentPrice'] * $oinfo['Percent'] / 100;


			$objActSheet->setCellValue('A'.$baseRow, date("Y-m-d",$oinfo['OrderDate']));
			$objActSheet->setCellValue('B'.$baseRow, $clientarr[$oinfo['OrderUserID']]);
			$objActSheet->setCellValue('C'.$baseRow, $oinfo['OrderSN']);
			$objActSheet->setCellValue('D'.$baseRow, '人民币');
			$objActSheet->setCellValue('E'.$baseRow, '终端事业部');
			$objActSheet->setCellValue('F'.$baseRow, '');
			$objActSheet->setCellValue('G'.$baseRow, '1.0');
			$objActSheet->setCellValue('H'.$baseRow, '购销');
			$objActSheet->setCellValue('I'.$baseRow, $oinfo['Coding']);
			$objActSheet->setCellValue('J'.$baseRow, $oinfo['Units']);				
			$objActSheet->setCellValue('K'.$baseRow, $oinfo['ContentNumber']);

			$objActSheet->setCellValue('L'.$baseRow, $oinfo['DeliveryDate']);
			$objActSheet->setCellValue('M'.$baseRow, $oinfo['DeliveryDate']);

		$k = $baseRow;
	}

}

$outputFileName = 'dhb_order_tokis2_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>