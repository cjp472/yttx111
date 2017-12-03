<?php
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

if(!intval($in['ID']))
{
	Error::AlertJs('参数错误!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".intval($in['ID'])." limit 0,1");
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$cinfo['ConsignmentOrder']."' limit 0,1");

	$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
	if (!in_array($oinfo['OrderUserID'], $sclientidarr )) exit('错误参数!');

	$clientinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

	$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentPercent,i.ContentNumber from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_out_library i on c.ID=i.CartID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.ConsignmentID=".$cinfo['ConsignmentID']." and i.ConType='c' order by c.ID asc");
	foreach($cartdata as $cv)
	{
		$cidarr[] = $cv['ContentID'];
	}
	$cartdatag = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,i.ContentNumber from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_out_library i on c.ID=i.CartID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.ConsignmentID=".$cinfo['ConsignmentID']." and i.ConType='g' order by c.ID asc");
	if(!empty($cartdatag))
	{
		foreach($cartdatag as $cv)
		{
			$cidarr[] = $cv['ContentID'];
		}
	}
	$cidmsg  = implode(",", $cidarr);
	$cartinfo = $db->get_results("SELECT ID,Coding,Price1,Price2,Casing,Units FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID in (".$cidmsg.")  ORDER BY ID DESC");
	foreach($cartinfo as $ci)
	{
		$pinfo[$ci['ID']] = $ci;
	}
	if(empty($cinfo['ConsignmentLogistics']) || $cinfo['ConsignmentLogistics']=="0")
	{
		$logname = '上门自提';
	}else{
		$logisticsarr = $db->get_row("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsContact,LogisticsPhone FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID=".$cinfo['ConsignmentLogistics']." ORDER BY LogisticsID DESC Limit 0,1");
		$logname = $logisticsarr['LogisticsName'];
	}
}

	$valuearr  = null;
	$setinfo   = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
	$valuearr1 = unserialize($setinfo['SetValue']);

	if(empty($valuearr1['send'])) $valuearr = 'a:14:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"折后价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";} '; else $valuearr = $valuearr1['send'];

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


$objPHPExcel->getProperties()->setCreator("订货宝 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("订货宝-详细订单数据")
							 ->setSubject("订货宝-详细订单数据")
							 ->setDescription("订货宝-详细订单数据")
							 ->setKeywords("订货宝 订货管理系统")
							 ->setCategory("订单详细");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('订单'.$oinfo['OrderSN']);
//$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' - 发货单');
$objActSheet->mergeCells('A1:'.$endc.'1');
//$objActSheet->getColumnDimension('A' )->setAutoSize(true);

foreach($narr as $key=>$val)
{
	$w = $valuearr[$val]['width'] * 1.4;
	if(empty($w) && $val=="ContentName") $w = 36;
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

$objActSheet->setCellValue('A2', '订单号：'.$oinfo['OrderSN']);
$objActSheet->mergeCells('A2:B2');

$objActSheet->setCellValue('C2', '客户名称：'.$clientinfo['ClientCompanyName']);
$objActSheet->mergeCells('C2:F2');

$objActSheet->setCellValue('G2', '订购时间：'.date("Y-m-d",$oinfo['OrderDate']));  
$objActSheet->mergeCells('G2:'.$endc.'2');
$objActSheet->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objActSheet->getStyle('A2:'.$endc.'2')->getFont()->setBold(true);

$objActSheet->setCellValue('A3', '运单号：'.$cinfo['ConsignmentNO']);
$objActSheet->mergeCells('A3:B3');

$objActSheet->setCellValue('C3', '货运公司：'.$logname);
$objActSheet->mergeCells('C3:F3');

$objActSheet->setCellValue('G3', '发货时间：'.$cinfo['ConsignmentDate']);  
$objActSheet->mergeCells('G3:'.$endc.'3');
$objActSheet->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objActSheet->getStyle('A3:'.$endc.'3')->getFont()->setBold(true);

foreach($narr as $key=>$val)
{
	$objActSheet->setCellValue($key.'4', $valuearr[$val]['name']);
}

$objActSheet->getStyle('A4:'.$endc.'4')->getFont()->setBold(true);
$objActSheet->getStyle('F4:'.$endc.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$alltotal = 0;
	$allnumber = 0;
	$baseRow   = 4;
	$n=0;
	foreach($cartdata as $ckey=>$cvar)
	{
		$baseRow++;
		$n++;
		$cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');

		$linetotal = $cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];

		$cvar['Coding'] = $pinfo[$cvar['ContentID']]['Coding'];
		$cvar['Price1'] = $pinfo[$cvar['ContentID']]['Price1'];
		$cvar['Price2'] = $pinfo[$cvar['ContentID']]['Price2'];
		$cvar['Units'] = $pinfo[$cvar['ContentID']]['Units'];

		foreach($narr as $key=>$val)
		{
			if($val=="NO")
			{
				$vv = $n;
			}else if($val=="PercentPrice"){
				$vv = $cvar['ContentPrice']*$cvar['ContentPercent']/10;
			}else if($val=="LineTotal"){
				$vv = $linetotal;
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
$objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($alltotal));
$objActSheet->setCellValue($nkey.$baseRow, $allnumber);
$objActSheet->setCellValue($endc.$baseRow, $alltotal);
$objActSheet->mergeCells('B'.$baseRow.':C'.$baseRow);
$objActSheet->getStyle('A'.$baseRow.':'.$endc.$baseRow)->getFont()->setBold(true);


if(!empty($cartdatag))
{
	$baseRow++;
	$objActSheet->setCellValue('A'.$baseRow, ' 赠品清单：');
	$objActSheet->mergeCells('A'.$baseRow.':J'.$baseRow);
	$objActSheet->getStyle('A'.$baseRow.':J'.$baseRow)->getFont()->setBold(true);
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdatag as $ckey=>$cvar)
	{
		$baseRow++;
		$n++;
		$cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');
		$cvar['ContentPercent'] = 10;
		$linetotal = $cvar['ContentNumber']*$cvar['ContentPrice'];
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];

		$cvar['Coding'] = $pinfo[$cvar['ContentID']]['Coding'];
		$cvar['Price1']  = $pinfo[$cvar['ContentID']]['Price1'];
		$cvar['Price2']  = $pinfo[$cvar['ContentID']]['Price2'];
		$cvar['Units']    = $pinfo[$cvar['ContentID']]['Units'];

		foreach($narr as $key=>$val)
		{
			if($val=="NO")
			{
				$vv = $n;
			}else if($val=="PercentPrice"){
				$vv = $cvar['ContentPrice'];
			}else if($val=="LineTotal"){
				$vv = $linetotal;
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
	$objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($alltotal));
	$objActSheet->setCellValue($nkey.$baseRow, $allnumber);
	$objActSheet->setCellValue($endc.$baseRow, $alltotal);
	$objActSheet->mergeCells('B'.$baseRow.':C'.$baseRow);
	$objActSheet->getStyle('A'.$baseRow.':'.$endc.$baseRow)->getFont()->setBold(true);
}

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '收货人：'.$oinfo['OrderReceiveCompany'].' / '.$oinfo['OrderReceiveName']);
$objActSheet->mergeCells('A'.$baseRow.':D'.$baseRow);
$objActSheet->setCellValue('E'.$baseRow, '联系电话：'.$oinfo['OrderReceivePhone']);
$objActSheet->mergeCells('E'.$baseRow.':'.$endc.$baseRow);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '收货地址：'.$oinfo['OrderReceiveAdd']);
$objActSheet->mergeCells('A'.$baseRow.':'.$endc.$baseRow);

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
$objPHPExcel->getActiveSheet()->getStyle('A4:'.$endc.$baseRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('C4:'.$endc.$baseRow)->getAlignment()->setWrapText(true);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '经办人：'.$cinfo['ConsignmentMan']);
$objActSheet->mergeCells('A'.$baseRow.':B'.$baseRow);
$objActSheet->setCellValue('F'.$baseRow, '导出时间：'.date("Y-m-d H:i"));  
$objActSheet->mergeCells('F'.$baseRow.':'.$endc.$baseRow);
$objStyleBottom = $objActSheet->getStyle('F'.$baseRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objActSheet->getStyle('A2:'.$endc.$baseRow)->getFont()->setSize(10);

$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '发货单_'.$cinfo['ConsignmentID'].'.xls';
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