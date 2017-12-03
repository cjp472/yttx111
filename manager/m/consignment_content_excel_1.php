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
	$clientinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");
	$cominfo = $db->get_row("SELECT CompanyName,CompanyContact,CompanyPhone,CompanyFax,CompanyAddress FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']."  limit 0,1");
	$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentPercent,l.ContentNumber,i.Coding,i.Casing,i.Price1,i.Price2,i.Units from ".DATATABLE."_order_cart c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and l.ConsignmentID=".$cinfo['ConsignmentID']." and l.ConType='c' order by i.SiteID asc,c.ID asc");

	$cartdatag = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,l.ContentNumber,i.Coding,i.Casing,i.Price1,i.Price2,i.Units from ".DATATABLE."_order_cart_gifts c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and l.ConsignmentID=".$cinfo['ConsignmentID']." and l.ConType='g' order by i.SiteID asc,c.ID asc");
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

	if(empty($valuearr1['send'])) $valuearr = unserialize('a:14:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"折后价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";}'); else $valuearr = $valuearr1['send'];

	if(!empty($valuearr['CompanyInfoPrint'])) unset($valuearr['CompanyInfoPrint']);
	$valuearr['Jian'] = Array(
	        'name'  => '备注',
	        'width' => '10%',
	        'show'  => '1'
	);
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

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].'配送清单');
$objActSheet->mergeCells('A1:'.$endc.'1');


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
$objFontA5->setSize(12);   
$objFontA5->setBold(true);     
//$objFontA5 ->getColor()->setARGB('FF999999' );

//设置对齐方式
$objAlignA5 = $objStyleA5 ->getAlignment();   
$objAlignA5->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objAlignA5->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_TOP);

$objActSheet->setCellValue('A2', '编号号：'.$oinfo['OrderSN']);
$objActSheet->mergeCells('A2:B2');

$objActSheet->setCellValue('C2', '流水号：'.$cinfo['ConsignmentNO']);
$objActSheet->mergeCells('C2:F2');

$objActSheet->setCellValue('G2', "欢迎登陆本公司网上购销平台订货\n公司网站：http://xccnfs.dhb.hk");  
$objActSheet->mergeCells('G2:'.$endc.'2');
$objActSheet->getStyle('G2')->getAlignment()->setWrapText(true);
$objActSheet->getColumnDimension('G' )->setWidth(36);
$objActSheet->getRowDimension('2')->setRowHeight(30);
$objActSheet->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objActSheet->setCellValue('A3', '客户名称：'.$clientinfo['ClientCompanyName']);
$objActSheet->mergeCells('A3:B3');

$objActSheet->setCellValue('C3', '电话：'.$cominfo['CompanyPhone']);
$objActSheet->mergeCells('C3:F3');

$objActSheet->setCellValue('G3', '第1页/共1页');  
$objActSheet->mergeCells('G3:'.$endc.'3');
$objActSheet->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// $objActSheet->getStyle('A3:'.$endc.'3')->getFont()->setBold(true);

$objActSheet->setCellValue('A4', '送货地址：'.$oinfo['OrderReceiveAdd']);
$objActSheet->mergeCells('A4:B4');

$objActSheet->setCellValue('C4', '联系人：'.$oinfo['OrderReceiveName']);
$objActSheet->mergeCells('C4:F4');

$objActSheet->setCellValue('G4', '日期：'.date("Y-m-d H:i"));
$objActSheet->mergeCells('G4:'.$endc.'4');
$objActSheet->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// $objActSheet->getStyle('A4:'.$endc.'4')->getFont()->setBold(true);

foreach($narr as $key=>$val)
{
	$objActSheet->setCellValue($key.'5', $valuearr[$val]['name']);
}

// $objActSheet->getStyle('A5:'.$endc.'5')->getFont()->setBold(true);
$objActSheet->getStyle('F5:'.$endc.'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$alltotal = 0;
	$allnumber = 0;
	$baseRow   = 5;
	$n=0;
	foreach($cartdata as $ckey=>$cvar)
	{
		$baseRow++;
		$n++;
		$cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');

		$linetotal = $cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];

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
	
	//赠品
	foreach($cartdatag as $ckey=>$cvar)
	{
	    $baseRow++;
	    $n++;
	    $cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');
	    $cvar['ContentPercent'] = 10;
	    $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice'];
// 	    $alltotal  = $alltotal + $linetotal;
// 	    $allnumber = $allnumber + $cvar['ContentNumber'];
	
	    foreach($narr as $key=>$val)
	    {
	        if($val=="NO")
	        {
	            $vv = $n;
	        }else if($val=="ContentName"){
	            $vv = '【赠】'.$cvar['ContentName'];
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
$objActSheet->setCellValue('A'.$baseRow, '全单合计金额：'.toCNcap($alltotal));
$objActSheet->mergeCells('A'.$baseRow.':C'.$baseRow);
$objActSheet->setCellValue('D'.$baseRow, '本页小计：'.$alltotal);
$objActSheet->mergeCells('D'.$baseRow.':E'.$baseRow);
$objActSheet->setCellValue('F'.$baseRow, '合计：'.$alltotal);
$objActSheet->mergeCells('F'.$baseRow.':'.$endc.$baseRow);
// $objActSheet->getStyle('A'.$baseRow.':'.$endc.$baseRow)->getFont()->setBold(true);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '公司地址：新蔡县驻新路与农校路交叉口东20米紫光网吧院内    报货电话：0396-3511811 售后：15836655560 质量状况：合格');
$objActSheet->mergeCells('A'.$baseRow.':'.$endc.$baseRow);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '货物与单不符请于3天内致电本公司，非质量问题退货不得超过30天，质量问题退货不得超过60天，多谢合作！');
$objActSheet->mergeCells('A'.$baseRow.':'.$endc.$baseRow);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '制单员：小薛        检货员：         客情官：        收货人：');
$objActSheet->mergeCells('A'.$baseRow.':'.$endc.$baseRow);
$objActSheet->getStyle('A'.$baseRow)->getAlignment()->setWrapText(true);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '单据说明：本单一式4联，（白色：存根，蓝色：仓库，红色：客户，黄色：财务）    备注：');
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
$objPHPExcel->getActiveSheet()->getStyle('A5:'.$endc.$baseRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('C5:'.$endc.$baseRow)->getAlignment()->setWrapText(true);

// $baseRow++;
// $objActSheet->setCellValue('A'.$baseRow, '经办人：'.$cinfo['ConsignmentMan']);
// $objActSheet->mergeCells('A'.$baseRow.':B'.$baseRow);
// $objActSheet->setCellValue('E'.$baseRow, '导出时间：'.date("Y-m-d H:i"));  
// $objActSheet->mergeCells('E'.$baseRow.':'.$endc.$baseRow);
// $objStyleBottom = $objActSheet->getStyle('E'.$baseRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// $objActSheet->getStyle('A2:'.$endc.$baseRow)->getFont()->setSize(10);

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