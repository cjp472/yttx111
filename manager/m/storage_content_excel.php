<?php
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

if(!intval($in['ID']))
{
	Error::AlertJs('参数错误!');
}else{	 
	$info = $db->get_row("SELECT StorageID,StorageSN,StorageProduct,StorageAttn,StorageAbout,StorageUser,StorageDate FROM ".DATATABLE."_order_storage where CompanyID = ".$_SESSION['uinfo']['ucompany']." and StorageID=".intval($in['ID'])." ORDER BY StorageID ASC limit 0,1");

		$sql = "select s.ContentNumber,i.ID,i.Name,i.Coding,i.Barcode,i.Casing,i.Units,i.Color,i.Specification from ".DATATABLE."_order_storage_number s left join ".DATATABLE."_order_content_index i on s.ContentID=i.ID where s.StorageID=".$info['StorageID']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." ";
		$list_data_total = $db->get_results($sql);
		
		$sqlrow = "select ContentID from ".DATATABLE."_order_storage_number_cs where StorageID = ".$info['StorageID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
		$listrow = $db->get_col($sqlrow);
		if(!empty($listrow))
		{			
			$sqlcs = "select s.ContentColor,s.ContentSpec,s.ContentNumber,i.ID,i.Name,i.Coding,i.Barcode,i.Casing,i.Units,i.Color,i.Specification from ".DATATABLE."_order_storage_number_cs s left join ".DATATABLE."_order_content_index i on s.ContentID=i.ID where s.StorageID=".$info['StorageID']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			$cartdata = $db->get_results($sqlcs);
			foreach($list_data_total as $v)
			{
				if(!in_array($v['ID'],$listrow)) $cartdata[] = $v;
			}
		}else{
			$cartdata = $list_data_total;
			unset($list_data_total);
		}
}

	$valuearr  = null;
	$setinfo   = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
	$valuearr1 = unserialize($setinfo['SetValue']);

	if(empty($valuearr1['library'])) $valuearr = unserialize('a:10:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:4:"Name";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:11:"ContentSpec";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:0:"";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:16:"CompanyInfoPrint";s:1:"2";}'); else $valuearr = $valuearr1['library'];


	unset($valuearr['CompanyInfoPrint']);
	$kk = 'A';
	foreach($valuearr as $k=>$v)
	{
		if($v['show']!="1") continue;
		$narr[$kk] = $k;
		$endc = $kk;
		if($k=="ContentNumber") $nkey = $kk;
		$kk++;
	}

$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-详细订单数据")
							 ->setSubject("医统天下-详细订单数据")
							 ->setDescription("医统天下-详细订单数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("订单详细");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('订单'.$oinfo['OrderSN']);
//$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' - 入库单');
$objActSheet->mergeCells('A1:'.$endc.'1');
//$objActSheet->getColumnDimension('A' )->setAutoSize(true);


foreach($narr as $key=>$val)
{
	$w = $valuearr[$val]['width'] * 2;
	if(empty($w) && $val=="Name") $w = 36;
	if(!empty($w)) $objActSheet->getColumnDimension($key)->setWidth($w);
}

$objActSheet->getRowDimension('1')->setRowHeight(32);

$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);     
//$objFontA5 ->getColor()->setARGB('FF999999' );

//设置对齐方式
$objAlignA5 = $objStyleA5 ->getAlignment();   
$objAlignA5->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objAlignA5->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_TOP);

$objActSheet->setCellValue('A2', '单号：'.$info['StorageSN']);
$objActSheet->mergeCells('A2:B2');

$objActSheet->setCellValue('C2', '经办人：'.$info['StorageAttn']);
//$objActSheet->mergeCells('C2:C2');

$objActSheet->setCellValue('D2', '入库时间：'.date("Y-m-d",$info['StorageDate']));  
$objActSheet->mergeCells('D2:'.$endc.'2');
$objActSheet->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objActSheet->getStyle('A2:'.$endc.'2')->getFont()->setBold(true);

foreach($narr as $key=>$val)
{
	$objActSheet->setCellValue($key.'3', $valuearr[$val]['name']);
}

$objActSheet->getStyle('A3:'.$endc.'3')->getFont()->setBold(true);
$objActSheet->getStyle('C3:'.$endc.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$alltotal = 0;
	$allnumber = 0;
	$baseRow   = 3;
	$n=0;
	foreach($cartdata as $ckey=>$cvar)
	{
		$baseRow++;
		$n++;
		$cvar['Name'] = html_entity_decode($cvar['Name'], ENT_QUOTES,'UTF-8');
		$allnumber = $allnumber + $cvar['ContentNumber'];
		
		if(empty($listrow))
		{
			$cvar['ContentColor'] = '';
			$cvar['ContentSpec']  = '';
		}else{
			$cvar['ContentColor'] = base64_decode(str_replace($rp,$fp,$cvar['ContentColor']));
			if($cvar['ContentColor'] == '统一') $cvar['ContentColor'] = '';

			$cvar['ContentSpec']  = base64_decode(str_replace($rp,$fp,$cvar['ContentSpec']));
			if($cvar['ContentSpec'] == '统一') $cvar['ContentSpec'] = '';
		}

		foreach($narr as $key=>$val)
		{
			if($val=="NO")
			{
				$vv = $n;
			}else{
				$vv = $cvar[$val];
			}
			if($key=="A" || $key=="B")
			{
				$objActSheet->setCellValueExplicit($key.$baseRow , $vv,PHPExcel_Cell_DataType::TYPE_STRING); 
			}else{
				$objActSheet->setCellValue($key.$baseRow, $vv);
			}
		}			
	}
$alltotal = sprintf("%01.2f", round($alltotal,2));
$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '合计：');
$objActSheet->setCellValue($nkey.$baseRow, $allnumber);
$objActSheet->mergeCells('B'.$baseRow.':C'.$baseRow);
$objActSheet->getStyle('A'.$baseRow.':'.$endc.$baseRow)->getFont()->setBold(true);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '备注说明：'.$oinfo['OrderRemark']);
$objActSheet->mergeCells('A'.$baseRow.':'.$endc.$baseRow);
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
$objPHPExcel->getActiveSheet()->getStyle('A3:'.$endc.$baseRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('C3:'.$endc.$baseRow)->getAlignment()->setWrapText(true);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '经办人：'.$cinfo['ConsignmentMan']);
$objActSheet->mergeCells('A'.$baseRow.':B'.$baseRow);
$objActSheet->setCellValue('C'.$baseRow, '导出时间：'.date("Y-m-d H:i"));  
$objActSheet->mergeCells('C'.$baseRow.':'.$endc.$baseRow);
$objStyleBottom = $objActSheet->getStyle('C'.$baseRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objActSheet->getStyle('A2:'.$endc.$baseRow)->getFont()->setSize(10);


$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '入库单_'.$info['StorageSN'].'.xls';
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