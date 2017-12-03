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
$objPHPExcel = $objReader->load(RESOURCE_PATH."import_193.xls");
$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();

$clientdata = $db->get_results("select ClientID,ClientNO,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']."  order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar;
}
$k = 1;

$sql = "select c.Coding,c.Units,c.ID,c.OrderID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentNumber,c.ContentPercent,c.ContentSend,o.* from ".DATATABLE."_view_index_cart c inner join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.OrderID in (".$idmsg.") order by o.OrderID desc,c.ID asc";
$odata = $db->get_results($sql);
$baseRow   = $k;
$firstline = '';
if(!empty($odata))
{	
	foreach($odata as $oinfo)
	{
		
		$nextid	   = intval(substr($oinfo['OrderSN'],strpos($oinfo['OrderSN'], '-')+1));
		$nextid    = str_pad($nextid, 4, "0", STR_PAD_LEFT);
		$oinfo['SN'] = 'XD-000-'.date("Y-m-d").'-'.$nextid;
		
		$baseRow++;
		$oinfo['ContentName'] = html_entity_decode($oinfo['ContentName'], ENT_QUOTES,'UTF-8');
		$oinfo['Percent'] = (10 - $oinfo['ContentPercent']) * 10;
		if(empty($oinfo['Percent'])) $oinfo['Percent'] = 100;
		$oinfo['PercentPrice'] = $oinfo['ContentPrice'] * $oinfo['ContentPercent'] / 10;
		$oinfo['Total'] = $oinfo['ContentNumber'] * $oinfo['ContentPrice'] * $oinfo['ContentPercent'] / 10;

		if($firstline == $oinfo['SN']){
			$kk++;
			$objActSheet->setCellValue('A'.$baseRow, '');
			$objActSheet->setCellValue('B'.$baseRow, '');
			$objActSheet->setCellValue('C'.$baseRow, '');
			$objActSheet->setCellValue('D'.$baseRow, '');
			$objActSheet->setCellValue('E'.$baseRow, '');
			$objActSheet->setCellValue('F'.$baseRow, '');
			$objActSheet->setCellValue('G'.$baseRow, '');
			$objActSheet->setCellValue('H'.$baseRow, '');
			$objActSheet->setCellValue('I'.$baseRow, '');
			$objActSheet->setCellValue('J'.$baseRow, '');				
			$objActSheet->setCellValue('K'.$baseRow, '');

			$objActSheet->setCellValue('L'.$baseRow, '');
			$objActSheet->setCellValue('M'.$baseRow, '');
			$objActSheet->setCellValue('N'.$baseRow, '');
			$objActSheet->setCellValue('O'.$baseRow, '');
			$objActSheet->setCellValue('P'.$baseRow, '');
			$objActSheet->setCellValue('Q'.$baseRow, '');
			$objActSheet->setCellValue('R'.$baseRow, '');
			$objActSheet->setCellValue('S'.$baseRow, '');
			$objActSheet->setCellValue('T'.$baseRow, '');
			$objActSheet->setCellValue('U'.$baseRow, '');
			$objActSheet->setCellValue('V'.$baseRow, '');

			$objActSheet->setCellValue('W'.$baseRow, '');
			$objActSheet->setCellValue('X'.$baseRow, '');
			$objActSheet->setCellValue('Y'.$baseRow, '');
			$objActSheet->setCellValue('Z'.$baseRow, '');

		}else{
			$kk = 1;
			$objActSheet->setCellValue('A'.$baseRow, '销售订单');
			$objActSheet->setCellValue('B'.$baseRow, $oinfo['SN']);
			$objActSheet->setCellValue('C'.$baseRow, date("Y-m-d",$oinfo['OrderDate']));
			$objActSheet->setCellValue('D'.$baseRow, $clientarr[$oinfo['OrderUserID']]['ClientNO']);
			$objActSheet->setCellValue('E'.$baseRow, $clientarr[$oinfo['OrderUserID']]['ClientCompanyName']);
			$objActSheet->setCellValue('F'.$baseRow, '收据');
			$objActSheet->setCellValue('G'.$baseRow, '');
			$objActSheet->setCellValue('H'.$baseRow, $oinfo['OrderTotal']);
			$objActSheet->setCellValue('I'.$baseRow, '');
			$objActSheet->setCellValue('J'.$baseRow, '');				
			$objActSheet->setCellValue('K'.$baseRow, '');

			$objActSheet->setCellValue('L'.$baseRow, '');
			$objActSheet->setCellValue('M'.$baseRow, '');
			$objActSheet->setCellValue('N'.$baseRow, '');
			$objActSheet->setCellValue('O'.$baseRow, '');
			$objActSheet->setCellValue('P'.$baseRow, $oinfo['OrderReceiveName']);
			$objActSheet->setCellValue('Q'.$baseRow, $oinfo['OrderReceivePhone']);
			$objActSheet->setCellValue('R'.$baseRow, '');
			$objActSheet->setCellValue('S'.$baseRow, $oinfo['OrderReceiveAdd']);
			$objActSheet->setCellValue('T'.$baseRow, '销售部');
			$objActSheet->setCellValue('U'.$baseRow, '');
			$objActSheet->setCellValue('V'.$baseRow, '');

			$objActSheet->setCellValue('W'.$baseRow, '');
			$objActSheet->setCellValue('X'.$baseRow, '');
			$objActSheet->setCellValue('Y'.$baseRow, '');
			$objActSheet->setCellValue('Z'.$baseRow, date("Y-m-d",$oinfo['OrderDate']));
		}
		$objActSheet->setCellValue('AJ'.$baseRow, $kk);
		$objActSheet->setCellValue('AK'.$baseRow, '');
		$objActSheet->setCellValue('AL'.$baseRow, '');
		$objActSheet->setCellValue('AM'.$baseRow, $oinfo['Coding']);
		$objActSheet->setCellValue('AN'.$baseRow, $oinfo['ContentName']);
		$objActSheet->setCellValue('AO'.$baseRow, $oinfo['ContentSpecification']);
		$objActSheet->setCellValue('AP'.$baseRow, $oinfo['Units']);
		$objActSheet->setCellValue('AQ'.$baseRow, $oinfo['ContentNumber']);
		$objActSheet->setCellValue('AR'.$baseRow, 10);
		$objActSheet->setCellValue('AS'.$baseRow, $oinfo['PercentPrice']);
		$objActSheet->setCellValue('AT'.$baseRow, 0);
		$objActSheet->setCellValue('AU'.$baseRow, 0);
		$objActSheet->setCellValue('AV'.$baseRow, $oinfo['Total']);
		$objActSheet->setCellValue('AW'.$baseRow, $oinfo['Total']);

		$k = $baseRow;
		$firstline = $oinfo['SN'];
	}
}

$outputFileName = 'dhb_order_tosd_'.date("Ymd").'_'.rand(10,99).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>