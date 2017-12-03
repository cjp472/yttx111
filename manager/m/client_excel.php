<?php 
$menu_flag = "client";
$pope	   = "pope_audit";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();
if($in['ty'] == "all"){
	$sqlmsg = '';
}
else{
	if(empty($in['selectedID']))
	{
		Error::AlertJs('请选择您要导出的药店!');
		exit;
	}else{
		$idmsg  = implode(",",$in['selectedID']);
		$sqlmsg = " and c.ClientID IN (".$idmsg.") ";
	}
}

$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-药店数据")
							 ->setSubject("医统天下-药店数据")
							 ->setDescription("医统天下-药店数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("药店数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('药店数据');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 药店列表');
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
$objActSheet->getColumnDimension('B' )->setWidth(18);
$objActSheet->getColumnDimension('C' )->setWidth(36);
$objActSheet->getColumnDimension('D' )->setWidth(18);
$objActSheet->getColumnDimension('E' )->setWidth(18);
$objActSheet->getColumnDimension('F' )->setWidth(18);
$objActSheet->getColumnDimension('G' )->setWidth(18);
$objActSheet->getColumnDimension('H' )->setWidth(18);
$objActSheet->getColumnDimension('I' )->setWidth(24);
$objActSheet->getColumnDimension('J' )->setWidth(24);
$objActSheet->getColumnDimension('K' )->setWidth(24);
$objActSheet->getColumnDimension('L' )->setWidth(36);
$objActSheet->getColumnDimension('M' )->setWidth(36);

$objActSheet->getRowDimension('1')->setRowHeight(32);


$company_id = $_SESSION['uinfo']['ucompany'];
$api_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_api_serial WHERE CompanyID=" . $company_id);
$show_guid = $api_info['Status'] == 'T';

$sitedata = $db->get_results("SELECT AreaID,AreaParentID,AreaName FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
foreach($sitedata as $cvar)
{
	$areaarr[$cvar['AreaID']] = $cvar['AreaName'];
}

$saler = $db->get_results("SELECT UserID,UserName,UserTrueName FROM ".DATABASEU.DATATABLE."_order_user WHERE UserFlag!='1' AND UserType='S' AND UserCompany=" . $company_id);
foreach($saler as $svar)
{
    $salerarr[$svar['UserID']] = $svar['UserTrueName'];
}

	$odata = $db->get_results("SELECT c.*,s.SalerID FROM ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where  c.ClientCompany = ".$_SESSION['uinfo']['ucompany']."  and c.ClientFlag IN(0,8,9) ".$sqlmsg." ORDER BY ClientID ASC limit 0,5000");
		$k = 1;
		$k++;
        $ei = 0;
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, 'ID');
        if($show_guid) {
            $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '药店内码');
        }
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '药店编码');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '药店名称');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '登陆帐号');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '账号状态');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '所在地区');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '负责客情官');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '联系人');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, 'E-mail');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '联系电话');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '传真');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '手机');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '地址');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '备注');
		$objActSheet->getStyle('A'.$k.':'.PHPExcel_Cell::stringFromColumnIndex($ei-1).$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':'.PHPExcel_Cell::stringFromColumnIndex($ei-1).$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3');

        if(!empty($odata))
        {
            foreach($odata as $var)
            {
                if(empty($var['ClientName'])) continue;
                $k++;
                $pmsg = '';
                $var['ClientCompanyName'] = html_entity_decode($var['ClientCompanyName'], ENT_QUOTES,'UTF-8');
                $var['ClientAdd'] = html_entity_decode($var['ClientAdd'], ENT_QUOTES,'UTF-8');
                $var['ClientAbout'] = html_entity_decode($var['ClientAbout'], ENT_QUOTES,'UTF-8');
                $eiv = 0;
                $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k , $var['ClientID'],PHPExcel_Cell_DataType::TYPE_STRING);
                if($show_guid) {
                    $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k , $var['ClientGUID'],PHPExcel_Cell_DataType::TYPE_STRING);
                }

                $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k ,$var['ClientNO'],PHPExcel_Cell_DataType::TYPE_STRING);

                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientCompanyName']);
                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientName']);
                $statusStr = "已审";
                if($var['ClientFlag'] == 8) {
                    $statusStr = '只读';
                } else if($var['ClientFlag'] == 9) {
                    $statusStr = '待审';
                }
                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $statusStr);
                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $areaarr[$var['ClientArea']]);
                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $salerarr[$var['SalerID']]);
                $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientTrueName'],PHPExcel_Cell_DataType::TYPE_STRING);
                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientEmail']);

                $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientPhone'],PHPExcel_Cell_DataType::TYPE_STRING);
                $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientFax'],PHPExcel_Cell_DataType::TYPE_STRING);
                $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientMobile'],PHPExcel_Cell_DataType::TYPE_STRING);
                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientAdd']);
                $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['ClientAbout']);
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
	$objPHPExcel->getActiveSheet()->getStyle('A2:' . PHPExcel_Cell::stringFromColumnIndex($ei-1).$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:'.PHPExcel_Cell::stringFromColumnIndex($ei-1).$k)->getFont()->setSize(10);


$outputFileName = '药店_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>