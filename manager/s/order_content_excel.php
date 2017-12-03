<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

if(!intval($in['ID']))
{
	Error::AlertJs('参数错误!');
}else{	 
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
}
$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
if (!in_array($oinfo['OrderUserID'], $sclientidarr )) exit('错误参数!');
$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");
$cominfo = $db->get_row("SELECT CompanyName,CompanyContact,CompanyPhone,CompanyFax,CompanyAddress FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']."  limit 0,1");

$cartdata = $db->get_results("select * from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by ID asc");
$cartdata_gifts = $db->get_results("select * from ".DATATABLE."_view_index_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by ID asc");
$valuearr = null;
$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
$valuearr1 = unserialize($setinfo['SetValue']);
if(empty($valuearr1['order'])) $valuearr = unserialize('a:14:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"折后价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";}'); else $valuearr = $valuearr1['order'];
$rightarr = array('ContentNumber','Price1','Price2','ContentPrice','ContentPercent','PercentPrice','LineTotal');

	unset($valuearr['CompanyInfoPrint']);
	$kk = 'A';
	foreach($valuearr as $k=>$v)
	{
		if($v['show']!="1") continue;
		$narr[$kk] = $k;
		$endc = $kk;
		if($k=="ContentNumber") $nkey = $kk;
		if($k=="Price1") $n1key = $kk;
		if($k=="Price2") $n2key = $kk;
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

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 在线订单');
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

$objActSheet->setCellValue('C2', '客户：'.$cinfo['ClientCompanyName']);
$objActSheet->mergeCells('C2:F2');

$objActSheet->setCellValue('F2', '订购时间：'.date("Y-m-d H:i",$oinfo['OrderDate']));  
$objActSheet->mergeCells('F2:'.$endc.'2');
$objActSheet->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objActSheet->getStyle('A2:'.$endc.'2')->getFont()->setBold(true);

$baseRow = 2;
if(!empty($cartdata)) {
    foreach($narr as $key=>$val)
    {
	    $objActSheet->setCellValue($key.'3', $valuearr[$val]['name']);
    }
    $objActSheet->getStyle('A3:'.$endc.'3')->getFont()->setBold(true);
    $objActSheet->getStyle('F2:'.$endc.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    
    $baseRow   = 3;
}
	$alltotal   = 0;
	$alltotal1 = $alltotal2 = 0;
	$allnumber = 0;
// 	$baseRow   = 3;
	$n = 0;
	foreach($cartdata as $ckey=>$cvar)
	{
		$baseRow++;
		$n++;
		$cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');
		if($_SESSION['uinfo']['ucompany']=="115") $cvar['Coding'] = totiaoma($cvar['Coding'],$cvar['ContentColor'],$cvar['ContentSpecification']);
		
		$alltotal1  = $alltotal1 + $cvar['Price1'] * $cvar['ContentNumber'];
		$alltotal2  = $alltotal2 + $cvar['Price2'] * $cvar['ContentNumber'];
		$linetotal = $cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
		$alltotal   = $alltotal + $linetotal;
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
			}else if($val=="ContentName" && $cvar['CommendID'] == '2'){
			    $vv = "【特】".$cvar[$val];
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
$totalVal = $alltotal;
if($oinfo['OrderSpecial']  == 'T')
{
    $totalVal = "原价 ￥".$alltotal."\n";
    $totalVal .= "特价 ￥".$oinfo['OrderTotal'];

    $alltotal = $oinfo['OrderTotal'];
}

if(!empty($cartdata)) {
	$baseRow++;
	$objActSheet->setCellValue('A'.$baseRow, '合计：');
	$objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($alltotal));
	$objActSheet->setCellValue($nkey.$baseRow, $allnumber);
	if(!empty($n1key)) $objActSheet->setCellValue($n1key.$baseRow, $alltotal1);
	if(!empty($n2key)) $objActSheet->setCellValue($n2key.$baseRow, $alltotal2);
	
	$objActSheet->getStyle($endc.$baseRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objActSheet->setCellValue($endc.$baseRow, $totalVal);
	
	$objActSheet->mergeCells('B'.$baseRow.':C'.$baseRow);
	$objActSheet->getStyle('A'.$baseRow.':'.$endc.$baseRow)->getFont()->setBold(true);
}

$cartdata_gifts = $db->get_results("select  c.*,i.Coding,i.Price1,i.Price2,i.Units,i.Casing from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by ID asc");
if(!empty($cartdata_gifts))
{
	$baseRow++;
	$objActSheet->setCellValue('A'.$baseRow, ' 赠品清单：');
	$objActSheet->mergeCells('A'.$baseRow.':'.$endc.$baseRow);
	$objActSheet->getStyle('A'.$baseRow.':'.$endc.$baseRow)->getFont()->setBold(true);
	
	$baseRow++;
	foreach($narr as $key=>$val)
	{
		$objActSheet->setCellValue($key.$baseRow, $valuearr[$val]['name']);
	}
	$objActSheet->getStyle('A'.$baseRow.':'.$endc.$baseRow)->getFont()->setBold(true);
	$objActSheet->getStyle('F'.$baseRow.':'.$endc.$baseRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata_gifts as $ckey=>$cvar)
	{
		$baseRow++;
		$n++;
		$cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');
		$cvar['ContentPercent'] = 10;
		$linetotal = $cvar['ContentNumber']*$cvar['ContentPrice'];
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];

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

if(empty($oinfo['DeliveryDate']) || $oinfo['DeliveryDate'] == '0000-00-00')
    $oinfo['DeliveryDate'] = '';

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '收货人：'.$oinfo['OrderReceiveCompany'].' / '.$oinfo['OrderReceiveName']);
$objActSheet->mergeCells('A'.$baseRow.':B'.$baseRow);
$objActSheet->setCellValue('C'.$baseRow, '联系电话：'.$oinfo['OrderReceivePhone']);
$objActSheet->mergeCells('C'.$baseRow.':D'.$baseRow);
$objActSheet->setCellValue('E'.$baseRow, '交货日期：'.$oinfo['DeliveryDate']);
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
$objPHPExcel->getActiveSheet()->getStyle('A3:'.$endc.$baseRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('C4:'.$endc.$baseRow)->getAlignment()->setWrapText(true);

$baseRow++;
$objActSheet->setCellValue('A'.$baseRow, '操作员：'.$_SESSION['uinfo']['usertruename']);
$objActSheet->mergeCells('A'.$baseRow.':B'.$baseRow);
$objActSheet->setCellValue('F'.$baseRow, '导出时间：'.date("Y-m-d H:i"));  
$objActSheet->mergeCells('F'.$baseRow.':'.$endc.$baseRow);
$objStyleBottom = $objActSheet->getStyle('F'.$baseRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objActSheet->getStyle('A2:'.$endc.$baseRow)->getFont()->setSize(10);


$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '订单_'.$oinfo['OrderSN'].'.xls';
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


function totiaoma($pno,$pcolor,$pspec)
{
		$color_arr = array(
		'01'			=>  '红色',
		'02'			=>  '棕红色',
		'03'			=>  '咖啡色',
		'04'			=>  '米白',
		'05'			=>  '黑色',
		'06'			=>  '军绿',
		'07'			=>  '墨绿',
		'08'			=>  '杏色',
		'09'			=>  '黄色',
		'10'			=>  '卡其色',
		'11'			=>  '深啡',
		'12'			=>  '浅啡',
		'13'			=>  '白色',
		'14'			=>  '其它',
		'15'			=>  '橙色',
		'16'			=>  '粉色',
		'17'			=>  '湖蓝',
		'18'			=>  '深蓝',
		'19'			=>  '灰色',
		'20'			=>  '紫色',
		'21'			=>  '土黄色',
		'22'			=>  '宝蓝',
		'23'			=>  '驼色',
		'24'			=>  '玫红色',
		'25'			=>  '绿色',
		'26'			=>  '酒红色'
 	 );
	 //$color_data_arr = array_flip($color_arr);
	 $rmsg = $pno;
	 if(!empty($pcolor)) $rmsg =  $rmsg.array_search($pcolor,$color_arr);
	 if(!empty($pspec)) $rmsg =  $rmsg.$pspec;
	 return $rmsg;
}
?>