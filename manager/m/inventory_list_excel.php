<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的数据!');
	exit;
}else{
	$idmsg = implode(",",$in['selectedID']);
}

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

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 库存明细');
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

		$odata = $db->get_results("SELECT ID,SiteID,Name,Coding,Units,Casing,Coding,Color,Specification FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID in (".$idmsg.") and FlagID=0 ORDER BY OrderID DESC, ID DESC");

		$sqllv = "SELECT ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber FROM ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$idmsg.") order by ContentID DESC";
		$numdata = $db->get_results($sqllv);
		$snarr  = null;
		if(!empty($numdata))
		{
			foreach($numdata as $nvar)
			{
				$snarr[$nvar['ContentID'].'_'.$nvar['ContentColor'].'_'.$nvar['ContentSpec']]['o'] = $nvar['OrderNumber'];
				$snarr[$nvar['ContentID'].'_'.$nvar['ContentColor'].'_'.$nvar['ContentSpec']]['c'] = $nvar['ContentNumber'];
			}
		}

		$sqlall = "SELECT ContentID,OrderNumber,ContentNumber FROM ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$idmsg.") order by ContentID DESC";
		$numalldata = $db->get_results($sqlall);
		$snallarr  = null;
		if(!empty($numalldata))
		{
			foreach($numalldata as $nallvar)
			{
				$snallarr[$nallvar['ContentID']]['o'] = $nallvar['OrderNumber'];
				$snallarr[$nallvar['ContentID']]['c'] = $nallvar['ContentNumber'];
			}
		}


$k = 1;
$k++;
$objActSheet->setCellValue('A'.$k, 'ID');
$objActSheet->setCellValue('B'.$k, '商品名称');
$objActSheet->setCellValue('C'.$k, '编号/货号');
$objActSheet->setCellValue('D'.$k, '包装');
$objActSheet->setCellValue('E'.$k, '颜色');
$objActSheet->setCellValue('F'.$k, '规格');
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
			if(empty($var['Color']) && empty($var['Specification']))
			{
				$k++;
				if(empty($snallarr[$var['ID']]['o'])) $snallarr[$var['ID']]['o']= 0;
				if(empty($snallarr[$var['ID']]['c'])) $snallarr[$var['ID']]['c'] = 0;
				$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');

				$objActSheet->setCellValueExplicit('A'.$k , $var['ID'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $var['Name']);
				$objActSheet->setCellValueExplicit('C'.$k , $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objActSheet->setCellValue('D'.$k, $var['Casing']);
				$objActSheet->setCellValue('E'.$k, '');
				$objActSheet->setCellValue('F'.$k, '');
				$objActSheet->setCellValue('G'.$k, $snallarr[$var['ID']]['o']);
				$objActSheet->setCellValue('H'.$k, $snallarr[$var['ID']]['c']);
				$objActSheet->setCellValue('I'.$k, $var['Units']);
			}else{
				if(empty($var['Color']))			  $var['Color']			  = "统一";
				if(empty($var['Specification'])) $var['Specification'] = "统一";
				$carr = explode(",",$var['Color']);
				$sarr = explode(",",$var['Specification']);
				foreach($carr as $c)
				{
					if(empty($c)) continue;
					$ccode = str_replace($fp,$rp,base64_encode($c));
					foreach($sarr as $s)
					{
						if(empty($s)) continue;
						$scode = str_replace($fp,$rp,base64_encode($s));
						$akey = $var['ID'].'_'.$ccode.'_'.$scode;

				$k++;
				if(empty($snarr[$akey]['o'])) $snarr[$akey]['o'] = 0;
				if(empty($snarr[$akey]['c'])) $snarr[$akey]['c']  = 0;
				$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');

				if($c=="统一") $c = "";
				if($s=="统一") $s = "";
				$objActSheet->setCellValueExplicit('A'.$k , $var['ID'],PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('B'.$k, $var['Name']);
				$objActSheet->setCellValueExplicit('C'.$k , $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objActSheet->setCellValue('D'.$k, $var['Casing']);
				$objActSheet->setCellValue('E'.$k, $c);
				$objActSheet->setCellValue('F'.$k, $s);
				$objActSheet->setCellValue('G'.$k, $snarr[$akey]['o']);
				$objActSheet->setCellValue('H'.$k, $snarr[$akey]['c']);
				$objActSheet->setCellValue('I'.$k, $var['Units']);

					}
				}
			}
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

$outputFileName = 'dhb_inventory_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>