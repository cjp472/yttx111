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
$objPHPExcel = $objReader->load(RESOURCE_PATH."/example/gjb.xls");
$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();

$clientdata = $db->get_results("select ClientID,ClientNO,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']."  order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar;
}
$k = 1;

$sql = "select c.Coding,c.Units,c.ID,c.OrderID,c.ContentID,c.ContentName,c.ContentPrice,c.ContentNumber,c.ContentPercent,o.OrderSN,o.OrderUserID,o.OrderDate,o.OrderRemark from ".DATATABLE."_view_index_cart c inner join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.OrderID in (".$idmsg.") order by o.OrderID desc,c.ID asc";
$odata = $db->get_results($sql);
$baseRow   = $k;
$firstline = '';
if(!empty($odata))
{	
	foreach($odata as $oinfo)
	{
			
		$baseRow++;
		$oinfo['ContentName'] = html_entity_decode($oinfo['ContentName'], ENT_QUOTES,'UTF-8');
		$oinfo['Percent'] = $oinfo['ContentPercent'] / 10;
		if(empty($oinfo['Percent'])) $oinfo['Percent'] = 1;
		$oinfo['PercentPrice'] = $oinfo['ContentPrice'] * $oinfo['ContentPercent'] / 10;
		$oinfo['Total'] = $oinfo['ContentNumber'] * $oinfo['ContentPrice'] * $oinfo['ContentPercent'] / 10;
		$oinfo['OrderRemark'] = str_replace('-','/',$oinfo['OrderRemark']);
		$oinfo['OrderRemark'] = str_replace('.','/',$oinfo['OrderRemark']);

			$kk = 1;
			$objActSheet->setCellValue('A'.$baseRow, $oinfo['OrderSN']);
			$objActSheet->setCellValue('B'.$baseRow, date("Y-m-d",$oinfo['OrderDate']));
			$objActSheet->setCellValue('C'.$baseRow, $oinfo['OrderRemark']);
			$objActSheet->setCellValue('D'.$baseRow, $clientarr[$oinfo['OrderUserID']]['ClientNO']);
			$objActSheet->setCellValue('E'.$baseRow, $clientarr[$oinfo['OrderUserID']]['ClientCompanyName']);
			$objActSheet->setCellValueExplicit('F'.$baseRow , $oinfo['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objActSheet->setCellValue('G'.$baseRow, $oinfo['ContentName']);
			$objActSheet->setCellValue('H'.$baseRow, $oinfo['Units']);
			$objActSheet->setCellValue('I'.$baseRow, $oinfo['ContentPrice']);
			$objActSheet->setCellValue('J'.$baseRow, $oinfo['ContentNumber']);
			$objActSheet->setCellValue('K'.$baseRow, $oinfo['Percent']);				
			$objActSheet->setCellValue('L'.$baseRow, $oinfo['Total']);

	}
}

$outputFileName = 'dhb_order_togjp_'.date("Ymd").'_'.rand(10,99).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>