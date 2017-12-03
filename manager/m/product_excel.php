<?php 
$menu_flag = "product";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();
$basenumber  = 500;

$valuearrf	= get_set_arr('field');
$productarr  = get_set_arr('product');
$price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
$price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";

$company_id = $_SESSION['uinfo']['ucompany'];

$api_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_api_serial WHERE CompanyID=" . $company_id);
$show_guid = $api_info['Status'] == 'T';
if($in['ty'] == "all")
{
	if(empty($in['b']))
	{
		$endsql = ' limit 0,'.$basenumber;
	}else{
		$baeginnumber = $basenumber * intval($in['b']);
		$endsql = ' limit '.$baeginnumber.','.$basenumber;
	}
	$datasql   = "SELECT * FROM ".DATATABLE."_view_index_content where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0 order by OrderID desc,ID asc ".$endsql;

}else{
	if(empty($in['selectedID']))
	{
		Error::AlertJs('请选择您要导出的商品!');
		exit;
	}else{
		$idmsg = implode(",",$in['selectedID']);
		$datasql   = "SELECT * FROM ".DATATABLE."_view_index_content where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0 and ID IN (".$idmsg.") order by OrderID desc,ID asc";
	}
}

$producttypearr = array(
		'0'			=>  '[默认]',		
		'1'			=>  '[推荐]',
		'2'			=>  '[特价]',
		'3'			=>  '[新款]',
		'4'			=>  '[热销]',
		'8'			=>  '[赠品]',
		'9'			=>  '[缺货]'
 	 );
	 
$levelarr = get_set_arr('clientlevel');

$branddata = $db->get_results("SELECT BrandID,BrandName FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." ");
if(!empty($branddata))
{
	foreach($branddata as $cvar)
	{
		$barr[$cvar['BrandID']] = $cvar['BrandName'];
	}
}
$sitedata = $db->get_results("SELECT SiteID,SiteName FROM ".DATATABLE."_order_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." ");
if(!empty($sitedata))
{
	foreach($sitedata as $cvar)
	{
		$sarr[$cvar['SiteID']] = $cvar['SiteName'];
	}
}

$odata = $db->get_results($datasql);

$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("医统天下-商品数据")
							 ->setSubject("医统天下-商品数据")
							 ->setDescription("医统天下-商品数据")
							 ->setKeywords("医统天下 订货管理系统")
							 ->setCategory("商品数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('商品数据');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 商品列表');
$objActSheet->mergeCells('A1:P1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

$objActSheet->getColumnDimension('A')->setWidth(15);
$objActSheet->getColumnDimension('B')->setWidth(12);
$objActSheet->getColumnDimension('C')->setWidth(36);
$objActSheet->getColumnDimension('D')->setWidth(16);
$objActSheet->getColumnDimension('K')->setWidth(18);
$objActSheet->getColumnDimension('H')->setWidth(38);
$objActSheet->getRowDimension('1')->setRowHeight(32);
	
		$k = 1;
		$k++;
        $ei = 0;
        if($show_guid){
            $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '内码');
        }

		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '条码');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '商品分类');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '商品名称');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '编号');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '品牌');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, $price1_name);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, $price2_name);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '等级价格');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '单位');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '包装');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '颜色');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '规格');
        $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '型号');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '排序');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '整包装');
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ei++).$k, '属性');
		if(!empty($valuearrf))
		{
			foreach($valuearrf as $key=>$var){
				if(!empty($var['name'])){
                    $th = PHPExcel_Cell::stringFromColumnIndex($ei++);
					$objActSheet->setCellValue($th.''.$k, $var['name']);
				}
			}
		}
		$objActSheet->getStyle('A'.$k.':'.PHPExcel_Cell::stringFromColumnIndex($ei - 1).''.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':'.PHPExcel_Cell::stringFromColumnIndex($ei - 1).''.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->freezePane('A3');

if(!empty($odata))
{
	foreach($odata as $var)
	{
		if(empty($var['Name'])) continue;
		$k++;
		$pmsg = '';
		if(!empty($var['Price3']))
		{
			$parr = unserialize(urldecode($var['Price3']));
			if(!empty($parr['typeid']))
			{
				$valuearr = $levelarr[$parr['typeid']];
			}
			foreach($valuearr as $key=>$var2)
			{
				if($key=="id" || $key=="name") continue;
				if(!empty($parr[$key])) $pmsg .= $var2.': ¥ '.$parr[$key].' , ';
			}

		}
		$var['Name'] = html_entity_decode($var['Name'], ENT_QUOTES,'UTF-8');
        $eiv = 0;
        if($show_guid) {
            $objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k , $var['GUID'],PHPExcel_Cell_DataType::TYPE_STRING);
        }

		$objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k , $var['Barcode'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $sarr[$var['SiteID']]);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Name']);
		$objActSheet->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Coding'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $barr[$var['BrandID']]);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Price1']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Price2']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $pmsg);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Units']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Casing']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Color']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Specification']);
        $objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Model']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['OrderID']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $var['Package']);
		$objActSheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($eiv++).$k, $producttypearr[$var['CommendID']]);
		$farr = unserialize($var['FieldContent']);
		if(!empty($valuearrf))
		{
			foreach($valuearrf as $key=>$var){
				if(!empty($var['name'])){
                    $th = PHPExcel_Cell::stringFromColumnIndex($eiv++);
					$objActSheet->setCellValue($th.''.$k, $farr[$key]);
				}
			}
		}
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
	$objPHPExcel->getActiveSheet()->getStyle('A2:'.PHPExcel_Cell::stringFromColumnIndex($eiv-1).''.$k)->applyFromArray($styleThinBlackBorderOutline);
	$objActSheet->getStyle('A2:'.PHPExcel_Cell::stringFromColumnIndex($eiv-1).''.$k)->getFont()->setSize(10);

	
$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '商品_'.date("Ymd").'_'.rand(1,999).'.xls';
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