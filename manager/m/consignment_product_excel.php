<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');

	$objPHPExcel = new PHPExcel();
	$sqll = $sql2 = '';
	if(!empty($in['clientid']))
	{
		$sqll = " and o.OrderUserID=".$in['clientid']." ";
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
	$titlemsg = "从 ".$in['begindate']." 到 ".$in['enddate']." 待发货商品明细 ";

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


					//订购商品
					$statsql  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=0 and o.OrderStatus!=8 and o.OrderStatus!=9 and c.ContentNumber <> c.ContentSend group by c.ContentID order by cnum desc";
					$statdata = $db->get_results($statsql);

					//赠品
					$statsqlg  = "SELECT sum(ContentNumber) as cnum,sum(ContentSend) as snum,c.ContentID,c.ContentName from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=0 and o.OrderStatus!=8 and o.OrderStatus!=9 and c.ContentNumber <> c.ContentSend group by c.ContentID order by cnum desc";
					$statdatag = $db->get_results($statsqlg);

					$totals = 0;
					$totalm = 0;
					$totalq = 0;
					$gdata = null;
					if(!empty($statdatag))
					{
						foreach($statdatag as $rvar)
						{
							$gdata[$rvar['ContentID']] = $rvar['cnum'];
							$gsdata[$rvar['ContentID']] = $rvar['snum'];
							$gdataarr[$rvar['ContentID']] = $rvar;
						}
					}
	
					if(!empty($statdata))
					{
						$sqlmsg = '';
						if(!empty($in['sid']))
						{
							$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
							if(!empty($in['sid'])) $sqlmsg .= " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";
						}						
						$pcoding = $db->get_results("SELECT i.ID,i.Coding,i.Units FROM ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID  where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
						foreach($pcoding as $v)
						{
							if(empty($v['Coding'])) $v['Coding'] = '&nbsp;';
							$codingarr[$v['ID']]['Coding'] = $v['Coding'];
							$codingarr[$v['ID']]['Units']  = $v['Units'];
						}
					}

		$k = 1;
		$k++;
		$objActSheet->setCellValue('A'.$k, '行号');
		$objActSheet->setCellValue('B'.$k, '编号');
		$objActSheet->setCellValue('C'.$k, '商品名称');
		$objActSheet->setCellValue('D'.$k, '订购数量');
		$objActSheet->setCellValue('E'.$k, '赠送数量');
		$objActSheet->setCellValue('F'.$k, '已发数量');
		$objActSheet->setCellValue('G'.$k, '待发货数');	
		$objActSheet->setCellValue('H'.$k, '单位');			

		$objActSheet->getStyle('A'.$k.':H'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('A'.$k.':H'.$k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
		$objActSheet->getStyle('C'.$k.':H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		if(!empty($statdata))
		{
			$pcoding = $db->get_results("SELECT ID,Coding,Units FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']."  ORDER BY ID ASC");
			foreach($pcoding as $v)
			{
				$codingarr[$v['ID']]['Coding'] = $v['Coding'];
				$codingarr[$v['ID']]['Units']  = $v['Units'];				
			}
			$n=0;
			foreach($statdata as $var)
			{
					if(empty($codingarr[$var['ContentID']])) continue;
					$var['onum'] = $var['cnum'];
					if(!empty($gdata[$var['ContentID']])) $var['gnum'] = $gdata[$var['ContentID']]; else $var['gnum'] = 0;
					if(!empty($gsdata[$var['ContentID']])) $var['gsnum'] = $gsdata[$var['ContentID']]; else $var['gsnum'] = 0;					
					$var['cnum'] = $var['onum'] + $var['gnum'] - $var['snum']- $var['gsnum'];

					$totalm = $totalm + $var['onum'];
					$totalg = $totalg + $var['gnum'];
					$totals = $totals + $var['snum'] + $var['gsnum'];
			
				$var['ContentName']= html_entity_decode($var['ContentName'], ENT_QUOTES,'UTF-8');
				$k++;
				$n++;
				$objActSheet->setCellValueExplicit('A'.$k , $n,PHPExcel_Cell_DataType::TYPE_STRING);
				$objActSheet->setCellValueExplicit('B'.$k , $codingarr[$var['ContentID']]['Coding'],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objActSheet->setCellValueExplicit('C'.$k , $var['ContentName'],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objActSheet->setCellValue('D'.$k, $var['onum']);
				$objActSheet->setCellValue('E'.$k, $var['gnum']);
				$objActSheet->setCellValue('F'.$k, $var['snum']+$var['gsnum']);
				$objActSheet->setCellValue('G'.$k, $var['cnum']);
				$objActSheet->setCellValue('H'.$k, $codingarr[$var['ContentID']]['Units']);	
				unset($gdataarr[$var['ContentID']]);
			}
			
			if(!empty($gdataarr))
			{
				foreach($gdataarr as $var)
				{
					$totalg = $totalg + $var['cnum'];
					$totals = $totals + $var['snum'];
					
					$var['ContentName']= html_entity_decode($var['ContentName'], ENT_QUOTES,'UTF-8');
					$k++;
					$n++;
					$objActSheet->setCellValueExplicit('A'.$k , $n,PHPExcel_Cell_DataType::TYPE_STRING);
					$objActSheet->setCellValueExplicit('B'.$k , $codingarr[$var['ContentID']]['Coding'],PHPExcel_Cell_DataType::TYPE_STRING); 
					$objActSheet->setCellValueExplicit('C'.$k , $var['ContentName'],PHPExcel_Cell_DataType::TYPE_STRING); 
					$objActSheet->setCellValue('D'.$k, 0);
					$objActSheet->setCellValue('E'.$k, $var['cnum']);
					$objActSheet->setCellValue('F'.$k, $var['snum']);
					$objActSheet->setCellValue('G'.$k, $var['cnum']-$var['snum']);
					$objActSheet->setCellValue('H'.$k, $codingarr[$var['ContentID']]['Units']);				
					
				}
			}		
			
			
				$total = $totalm + $totalg - $totals;
				$k++;
				$objActSheet->setCellValueExplicit('A'.$k , '合计',PHPExcel_Cell_DataType::TYPE_STRING);  
				$objActSheet->setCellValue('C'.$k, count($statdata));
				$objActSheet->setCellValue('D'.$k, $totalm);
				$objActSheet->setCellValue('E'.$k, $totalg);
				$objActSheet->setCellValue('F'.$k, $totals);
				$objActSheet->setCellValue('G'.$k, $total);
			
				$objActSheet->getStyle('A'.$k.':H'.$k)->getFont()->setBold(true);
		}
		$objActSheet->getStyle('A2:H'.$k)->getFont()->setSize(10);
		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:H'.$k)->applyFromArray($styleThinBlackBorderOutline);


$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = '待发货商品明细_'.date("Ymd").'_'.rand(1,999).'.xls';
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
/***
$outputFileName = 'consignment_product_between_'.$in['begindate'].'_'.$in['enddate'].'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
***/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>