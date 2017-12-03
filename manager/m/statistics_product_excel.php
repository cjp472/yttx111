<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');

if(empty($in['action']))
{
	Error::AlertJs('参数错误!');
	exit;
}

	$objPHPExcel = new PHPExcel();
	$sqll = '';
    $clientInfo = array();
	if(!empty($in['clientid']))
	{
        $clientInfo = $db->get_row("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 AND ClientID={$in['clientid']} LIMIT 0,1");
		$sqll  = " and o.OrderUserID=".$in['clientid']." ";
		$sql2 = " and o.ReturnClient=".$in['clientid']." ";
	}
	if(empty($in['begindate'])) $in['begindate'] = date("Y-m-d",mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
	if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");

	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 ) 
	{
		Error::AlertJs('注意：时间跨度不能超过一年!');
		exit;
	}	
	
	$objPHPExcel->getProperties()->setCreator("医统天下 订货管理系统 DHB.HK")
								 ->setLastModifiedBy("DingHuoBao")
								 ->setTitle("医统天下-商品统计")
								 ->setSubject("医统天下-商品统计")
								 ->setDescription("医统天下-商品统计")
								 ->setKeywords("医统天下 订货管理系统")
								 ->setCategory("商品统计");

	$objPHPExcel->setActiveSheetIndex(0);
	$objActSheet = $objPHPExcel->getActiveSheet();
	$objActSheet->setTitle('商品统计');
	$objActSheet->getDefaultRowDimension()->setRowHeight(20);
	$titlemsg = $clientInfo['ClientCompanyName']."从 ".$in['begindate']." 到 ".$in['enddate']." 商品订购数据 ";

	$objActSheet->setCellValue('A1', $titlemsg);
	$objActSheet->mergeCells('A1:F1');
	$objStyleA5 = $objActSheet->getStyle('A1'); 
	//设置对齐方式
	$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	//设置字体    
	$objFontA5 = $objStyleA5->getFont();   
	$objFontA5->setName('黑体' );   
	$objFontA5->setSize(14);   
	$objFontA5->setBold(true);

	//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
	$objActSheet->getColumnDimension('A' )->setWidth(10);
	$objActSheet->getColumnDimension('B' )->setWidth(18);
	$objActSheet->getColumnDimension('C' )->setWidth(44);
	$objActSheet->getColumnDimension('D' )->setWidth(16);
	$objActSheet->getColumnDimension('E' )->setWidth(16);
	$objActSheet->getColumnDimension('F' )->setWidth(16);
	$objActSheet->getRowDimension('1')->setRowHeight(32);
	$objActSheet->freezePane('A3');

	
	$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
	foreach($clientdata as $cvar)
	{
		$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
	}

	$statsql  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,sum(ContentPrice*ContentNumber*ContentPercent/10) as ctotal,c.ContentID,c.ContentName from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ContentID order by cnum desc";
	$statdata = $db->get_results($statsql);
	
	//赠品
	$statsqlg  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ContentID order by cnum desc";
	$statdatag = $db->get_results($statsqlg);

	$statsqlr  = "SELECT sum(ContentNumber) as cnum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sql2." and FROM_UNIXTIME(o.ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus=5) group by c.ContentID order by cnum desc";
	$rdata = $db->get_results($statsqlr);

	$sqlmsg = '';
	if(!empty($in['siteid']))
	{
		$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['siteid'])." limit 0,1");
		if(!empty($in['siteid'])) $sqlmsg .= " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";
	}						
	$pcoding = $db->get_results("SELECT i.ID,i.Coding FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID  where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	foreach($pcoding as $v)
	{
		if(empty($v['Coding'])) $v['Coding'] = '&nbsp;';
		$codingarr[$v['ID']] = $v['Coding'];
	}


		$totalr = 0;
		$totalm = 0;
		$totalq = 0;
		$gdata = null;
		if(!empty($statdatag))
		{
			foreach($statdatag as $rvar)
			{
				$gdata[$rvar['ContentID']]    = $rvar['cnum'];
				$gdatas[$rvar['ContentID']]   = $rvar['snum'];
				$gdataarr[$rvar['ContentID']] = $rvar;
			}
		}
		$returndata = null;
		if(!empty($rdata))
		{
			foreach($rdata as $rvar)
			{
				$returndata[$rvar['ContentID']] = $rvar['cnum'];
			}
		}
		for($i=0;$i<count($statdata);$i++)
		{
			if(empty($codingarr[$statdata[$i]['ContentID']])) continue;

			$statdata[$i]['onum'] = $statdata[$i]['cnum'];
			$totalm = $totalm + $statdata[$i]['onum'];
			if(!empty($gdata[$statdata[$i]['ContentID']]))
			{
				$statdata[$i]['gnum'] = $gdata[$statdata[$i]['ContentID']];
				$totalg = $totalg + $statdata[$i]['gnum'];
			}
			if(!empty($gdatas[$statdata[$i]['ContentID']]))
			{
				$statdata[$i]['gsnum'] = $gdatas[$statdata[$i]['ContentID']];
			}
			if(!empty($returndata[$statdata[$i]['ContentID']]))
			{
				$statdata[$i]['rnum'] = $returndata[$statdata[$i]['ContentID']];	
				$totalr = $totalr + $statdata[$i]['rnum'];
			}
			$statdata[$i]['cnum'] = $statdata[$i]['onum'] + $statdata[$i]['gnum'] - $statdata[$i]['rnum'];
			if(empty($statdata[$i]['rnum'])) $statdata[$i]['rnum'] = 0;
			if(empty($statdata[$i]['cnum'])) $statdata[$i]['cnum'] = 0;
			if(empty($statdata[$i]['gnum'])) $statdata[$i]['gnum'] = 0;
			//发货数量
			$statdata[$i]['snum'] = $statdata[$i]['snum'] + $statdata[$i]['gsnum'];
		}
		$total = $totalm - $totalr;

		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '行号');
		$objActSheet->setCellValue('B'.$k, '编号');
		$objActSheet->setCellValue('C'.$k, '商品名称');
		$objActSheet->setCellValue('D'.$k, '订购数量');
		$objActSheet->setCellValue('E'.$k, '赠送数量');
		$objActSheet->setCellValue('F'.$k, '退货数量');
		$objActSheet->setCellValue('G'.$k, '实际数量');		
		$objActSheet->setCellValue('H'.$k, '发货数量');
		$objActSheet->setCellValue('I'.$k, '订购金额');
		$objActSheet->getStyle('A'.$k.':I'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':I'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->getStyle('C'.$k.':I'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		if(!empty($statdata))
		{

			$n=0;
			foreach($statdata as $var)
			{
				if(empty($codingarr[$var['ContentID']])) continue;
				$var['ContentName']= html_entity_decode($var['ContentName'], ENT_QUOTES,'UTF-8');
				$k++;
				$n++;
				$objActSheet->setCellValueExplicit('A'.$k , $n,PHPExcel_Cell_DataType::TYPE_STRING);
				$objActSheet->setCellValueExplicit('B'.$k , $codingarr[$var['ContentID']],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objActSheet->setCellValueExplicit('C'.$k , $var['ContentName'],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objActSheet->setCellValue('D'.$k, $var['onum']);
				$objActSheet->setCellValue('E'.$k, $var['gnum']);
				$objActSheet->setCellValue('F'.$k, $var['rnum']);
				$objActSheet->setCellValue('G'.$k, $var['cnum']);
				$objActSheet->setCellValue('H'.$k, $var['snum']);
				$objActSheet->setCellValue('I'.$k, round($var['ctotal'],2));
				unset($gdataarr[$var['ContentID']]);
				$totals = $totals + $var['snum'];
				$totalc = $totalc + $var['ctotal'];
			}
			foreach($gdataarr as $var)
			{
				if(empty($codingarr[$var['ContentID']])) continue;
				$totalg = $totalg + $var['cnum'];
				$var['ContentName']= html_entity_decode($var['ContentName'], ENT_QUOTES,'UTF-8');
				$k++;
				$n++;
				$objActSheet->setCellValueExplicit('A'.$k , $n,PHPExcel_Cell_DataType::TYPE_STRING);
				$objActSheet->setCellValueExplicit('B'.$k , $codingarr[$var['ContentID']],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objActSheet->setCellValueExplicit('C'.$k , $var['ContentName'],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objActSheet->setCellValue('D'.$k, 0);
				$objActSheet->setCellValue('E'.$k, $var['cnum']);
				$objActSheet->setCellValue('F'.$k, 0);
				$objActSheet->setCellValue('G'.$k, $var['cnum']);
				$objActSheet->setCellValue('H'.$k, $var['snum']);
				$objActSheet->setCellValue('I'.$k, 0);
				$totals = $totals + $var['snum'];
					
			}
			
			$total = $totalm + $totalg - $totalr;
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  

				$objActSheet->setCellValue('D'.$k, $totalm);
				$objActSheet->setCellValue('E'.$k, $totalg);
				$objActSheet->setCellValue('F'.$k, $totalr);
				$objActSheet->setCellValue('G'.$k, $total);
				$objActSheet->setCellValue('H'.$k, $totals);	
				$objActSheet->setCellValue('I'.$k, round($totalc,2));				
				$objActSheet->getStyle('A'.$k.':I'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:I'.$k)->getFont()->setSize(10);
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
		
		
$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = $clientInfo['ClientCompanyName'].'商品统计报表_'.$in['begindate'].'_'.$in['enddate'].'.xls';
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
/**	
$outputFileName = 'stat_between_'.$in['begindate'].'_'.$in['enddate'].'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
**/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>