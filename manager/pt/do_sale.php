<?php
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ('../class/PHPExcel.php');

if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

//*****sale start************/
/**
 * 删除销售员信息
 */
if($in['m']=="delete")
{
	if(!intval($in['ID'])) exit('非法操作!');

	$sql = "SELECT count(*) as allrow FROM ".DATABASEU.DATATABLE."_order_cs cs WHERE CS_SaleUID = ".$in['ID'];
    $info = $db->get_row($sql);
    if($info['allrow'] > 0) exit('此数据已使用不能删除！');

	$delsql =  "delete from ".DATABASEU.DATATABLE."_order_sale where ID = ".$in['ID'];

	if($db->query($delsql))
	{
		exit('ok');
	}
	else
	{
		exit('删除不成功,请重试!');
	}
}

/**
 * 保存销售员信息
 */
if($in['m']=="set_sale_info")
{
	if(empty($in['SaleName']))
	{
	    exit('销售员姓名不能为空');
	}
	if(empty($in['SaleDepartment']))
	{
	    exit('销售员部门不能为空');
	}
	if(empty($sale_depart[$in['SaleDepartment']]))
	{
	    exit('销售员部门超出范围');
	}

	//允许多个电话号，对每个电话号进行验证，不区分固话或移动电话
	if(!empty($in['SalePhone']))
	{
	    $Phones = array_unique(array_filter(explode(",",$in['SalePhone'])));
	    $PhoneSucc = array();
	    foreach($Phones as $phone)
	    {
	        if(is_telephone($phone))
	        {
	            $PhoneSucc[] = trim($phone);
	        }
	    }
	    if(count($PhoneSucc) > 3)
	    {
	        exit('最多输入三个电话号码');
	    }

	    $in['SalePhone'] = implode(",",$PhoneSucc);
	}

    if(!empty($in['ID']))
    {
        $sqlex = "update ".DATABASEU.DATATABLE."_order_sale set SaleName='".trim($in['SaleName'])."',SaleDepartment='".trim($in['SaleDepartment'])."',SalePhone='".trim($in['SalePhone'])."',SaleFlag='{$in['SaleFlag']}',Remark='".trim($in['Remark'])."',UpdateDate=".time().",UpdateUID=".$_SESSION['uinfo']['userid']." where ID=".$in["ID"];
    }
    else
    {
        $sqlex = "insert into ".DATABASEU.DATATABLE."_order_sale(SaleName,SaleDepartment,SalePhone,SaleFlag,Remark,CreateDate,CreateUID,UpdateDate,UpdateUID) values('".$in['SaleName']."', '".trim($in['SaleDepartment'])."', '".trim($in['SalePhone'])."','{$in['SaleFlag']}','".trim($in['Remark'])."',".time().",".$_SESSION['uinfo']['userid'].",".time().",".$_SESSION['uinfo']['userid'].")";
    }

    $rs = $db->query($sqlex);

    if($rs)
    {
        exit("ok");
    }
    else
    {
        exit("操作失败,请重试");
    }
}

/**
 * 获取销售员客户
 */
if($in['m'] == 'showClient')
{
    if(!intval($in['ID'])) exit('非法操作!');

    $sql = "SELECT c.* FROM ".DATABASEU.DATATABLE."_order_company c LEFT JOIN ".DATABASEU.DATATABLE."_order_cs cs ON c.companyid = cs.CS_Company WHERE cs.CS_SaleUID = ".$in['ID'];
    $info = $db->get_results($sql);
    echo json_encode($info);

    exit;
}

/**
 * 导出销售统计数据
 */
if($in['m']=="export")
{
    $ids = explode(',',$in['idNums']);
    $idsNum = array();
    foreach($ids as $j){
        $arr = explode(':',$j);
        $idsNum[$arr[0]] = $arr[1];
    }
    $saleList = $_SESSION['SaleCount']['salelist'];
    foreach($saleList as &$sale){
        foreach($idsNum as $k=>$v){
            if($k == $sale['ID']){
                $sale['saleNum'] = $v;
            }
        }
    }
    $freeCNT  = $_SESSION['SaleCount']['freeCNT'];
    $payCNT   = $_SESSION['SaleCount']['payCNT'];
    $total    = $_SESSION['SaleCount']['total'];

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("订货宝 订货管理系统 DHB.HK")
    							 ->setLastModifiedBy("DingHuoBao")
    							 ->setTitle("订货宝-订单数据")
    							 ->setSubject("订货宝-订单数据")
    							 ->setDescription("订货宝-订单数据")
    							 ->setKeywords("订货宝 订货管理系统")
    							 ->setCategory("订单数据");

    $objPHPExcel->setActiveSheetIndex(0);
    $objActSheet = $objPHPExcel->getActiveSheet();
    $objActSheet->setTitle('销售统计');
    $objActSheet->getDefaultRowDimension()->setRowHeight(20);

    $objActSheet->setCellValue('A1', '销售统计数据');
    $objActSheet->mergeCells('A1:F1');
    $objStyleA5 = $objActSheet->getStyle('A1');
    //设置对齐方式
    $objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //设置字体
    $objFontA5 = $objStyleA5->getFont();
    $objFontA5->setName('黑体' );
    $objFontA5->setSize(18);
    $objFontA5->setBold(true);

    $objActSheet->getColumnDimension('B' )->setWidth(20);
    $objActSheet->getColumnDimension('C' )->setWidth(15);
    $objActSheet->getColumnDimension('D' )->setWidth(15);
    $objActSheet->getColumnDimension('E' )->setWidth(15);
    $objActSheet->getColumnDimension('F' )->setWidth(15);
    $objActSheet->getRowDimension('1')->setRowHeight(32);
    $objActSheet->freezePane('A2');

    if(!empty($saleList))
    {
    	$k = 2;
		$objActSheet->setCellValue('A'.$k, '操作人：'.$_SESSION['uinfo']['usertruename']);
		$objActSheet->mergeCells('A'.$k.':B'.$k);

		$objActSheet->setCellValue('C'.$k, '操作时间：'.date("Y-m-d H:i"));
		$objActSheet->mergeCells('C'.$k.':F'.$k);
		$objActSheet->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objActSheet->getStyle('A'.$k.':F'.$k)->getFont()->setBold(true);

		$k++;
		$objActSheet->setCellValue('A'.$k, '序号');
		$objActSheet->setCellValue('B'.$k, '销售员');
		$objActSheet->setCellValue('C'.$k, '（总）免费单数');
		$objActSheet->setCellValue('D'.$k, '（总）付费单数');
		$objActSheet->setCellValue('E'.$k, '金额');
		$objActSheet->setCellValue('F'.$k, '分享次数');

		$objActSheet->getStyle('A'.$k.':F'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('F'.$k.':F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		$freeSum  = 0;
		$paySum   = 0;
		$totalSum = 0;
        $totalNum = 0;
		$baseRow  = $k;
		$n = 0;
		foreach($saleList as $ckey=>$cvar)
		{
			$baseRow++;
			$n++;

			$freeSum  = $freeSum + $freeCNT[$cvar['ID']];
			$paySum   = $paySum + $payCNT[$cvar['ID']];
			$totalSum = $totalSum + $total[$cvar['ID']];
            $totalNum = $totalNum + $cvar['saleNum'];

			$objActSheet->setCellValueExplicit('A'.$baseRow , $n,PHPExcel_Cell_DataType::TYPE_STRING);
			$objActSheet->setCellValue('B'.$baseRow, $cvar['SaleName']);

			$objActSheet->setCellValue('C'.$baseRow,empty($freeCNT[$cvar['ID']]) ? '0' : $freeCNT[$cvar['ID']]);
			$objActSheet->setCellValue('D'.$baseRow, empty($payCNT[$cvar['ID']]) ? '0' : $payCNT[$cvar['ID']]);
			$objActSheet->setCellValue('E'.$baseRow, empty($total[$cvar['ID']]) ? '0' : $total[$cvar['ID']]);
			$objActSheet->setCellValue('F'.$baseRow, empty($cvar['SaleName']) ? '0' : $cvar['saleNum']);
		}

		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '合计：');

		$objActSheet->setCellValue('C'.$baseRow, $freeSum);
		$objActSheet->setCellValue('D'.$baseRow, $paySum);
		$objActSheet->setCellValue('E'.$baseRow, $totalSum);
		$objActSheet->setCellValue('F'.$baseRow, $totalNum);

        $objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($totalSum));

		$objActSheet->getStyle('A'.$baseRow.':F'.$baseRow)->getFont()->setBold(true);

		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':F'.$baseRow)->applyFromArray($styleThinBlackBorderOutline);

		$baseRow++;
		$objActSheet->mergeCells('A'.$baseRow.':F'.$baseRow);
		$objActSheet->getRowDimension($baseRow)->setRowHeight(30);

		$k = $baseRow;

    	$objActSheet->getStyle('A2:F'.$k)->getFont()->setSize(10);
    }

    $ua = $_SERVER["HTTP_USER_AGENT"];
    $filename = '销售统计_'.date("Ymd").'_'.rand(1,999).'.xls';
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
    $outputFileName = 'dhb_order_'.date("Ymd").'_'.rand(1,999).'.xls';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
    header('Cache-Control: max-age=0');
    **/
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}
//*****sale end************/

exit('非法操作!');
?>