<?php
//$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
include_once ('../class/PHPExcel.php');

if(empty($in['action']))
{
    Error::AlertJs('参数错误!');
    exit;
}

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("订货宝 订货管理系统 DHB.HK")
    ->setLastModifiedBy("DingHuoBao")
    ->setTitle("订货宝-订单统计")
    ->setSubject("订货宝-订单统计")
    ->setDescription("订货宝-订单统计")
    ->setKeywords("订货宝 订货管理系统")
    ->setCategory("订单统计");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('订单统计');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->mergeCells('A1:E1');
$objStyleA5 = $objActSheet->getStyle('A1');
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置字体
$objFontA5 = $objStyleA5->getFont();
$objFontA5->setName('黑体' );
$objFontA5->setSize(14);
$objFontA5->setBold(true);

//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
$objActSheet->getColumnDimension('A' )->setWidth(20);
$objActSheet->getColumnDimension('B' )->setWidth(20);
$objActSheet->getColumnDimension('C' )->setWidth(20);
$objActSheet->getColumnDimension('D' )->setWidth(20);
$objActSheet->getRowDimension('1')->setRowHeight(32);
$objActSheet->freezePane('A3');
if($in['action']=='order'){
    //业务员订单统计导出
    if(empty($in['beginDate'])){
        $in['beginDate'] = date('Y-m-d',strtotime('-1 months'));
    }
    if(empty($in['endDate'])){
        $in['endDate'] = date('Y-m-d');
    }
    $where = "";
    if(!empty($in['cid'])){
        //指定药店
        $where .= " AND OrderUserID=".$in['cid'];
        $clientInfo = $db->get_row("SELECT ClientName,ClientCompanyName FROM ".DATATABLE."_order_client WHERE ClientID=".$in['cid']);
        $client = '-'. $clientInfo['ClientCompanyName'];
    }else{
        //所有药店
        $where .= " AND OrderUserID IN(".$_SESSION['uinfo']['clientidmsg'].")";
        $client = '';
    }
    $dataSql = "SELECT
                  LEFT(OrderSN, 8) AS ODate,
                  SUM(OrderTotal) AS OTotal,
                  COUNT(*) AS totalnumber
                FROM
                  ".DATATABLE."_order_orderinfo
                WHERE OrderCompany = ".$_SESSION['uinfo']['ucompany']."
                  AND FROM_UNIXTIME(OrderDate) BETWEEN '".$in['beginDate']." 00:00:00'
                  AND '".$in['endDate']." 23:59:59'
                  AND OrderStatus != 8
                  AND OrderStatus != 9" . $where ."
                  GROUP BY LEFT(OrderSN,8) DESC
                  ";

    //待审核
    $dataSql0 = "SELECT
              LEFT(OrderSN, 8) AS ODate,
              SUM(OrderTotal) AS OTotal,
              COUNT(*) AS totalnumber
            FROM
              ".DATATABLE."_order_orderinfo
            WHERE OrderCompany = ".$_SESSION['uinfo']['ucompany']."
              AND FROM_UNIXTIME(OrderDate) BETWEEN '".$in['beginDate']." 00:00:00'
              AND '".$in['endDate']." 23:59:59'
              AND OrderStatus = 0" . $where . "
              GROUP BY LEFT(OrderSN,8) DESC
              ";
    $rdata = $db->get_results($dataSql0);
    $rarr = array();

    $total0 = 0;//待审核订单金额
    $cnt0 = 0;//待审核订单数
    foreach($rdata as $val){
        $rarr[$val['ODate']] = $val['totalnumber'];
        $total0 += $val['OTotal'];
        $cnt0 += $val['totalnumber'];
    }
    $stataData = $db->get_results($dataSql);
    $objActSheet->setCellValue('A1','业务员'.$_SESSION['uinfo']['usertruename'].$client.'从'.$in['beginDate'].'到'.$in['endDate'].'订单统计');

    $k=1;
    $k++;
    $objActSheet->setCellValue('A'.$k,'日期');
    $objActSheet->setCellValue('B'.$k,'金额');
    $objActSheet->setCellValue('C'.$k,'订单数');
    $objActSheet->setCellValue('D'.$k,'待审核订单数');
    $total = 0;//订单总金额
    $cnt = 0;//订单总数
    foreach($stataData as $v){
        $k++;
        $total += $v['OTotal'];
        $cnt += $v['totalnumber'];
        $objActSheet->setCellValue('A'.$k,$v['ODate'].' ');
        $objActSheet->setCellValue('B'.$k,'¥'.$v['OTotal']);
        $objActSheet->setCellValue('C'.$k,$v['totalnumber'].' ');
        $objActSheet->setCellValue('D'.$k,$rarr[$v['ODate']].' ');
    }
    $k = $k+2;
    $objActSheet->setCellValue('A'.$k,'合计:');
    $objActSheet->setCellValue('B'.$k,'¥'.$total.'元');
    $objActSheet->setCellValue('C'.$k,$cnt.'个');
    $objActSheet->setCellValue('D'.$k,$cnt0.'个(¥'.$total0.')');
    $filename = '业务员'.$_SESSION['uinfo']['usertruename'].'-'.$client.'从'.$in['beginDate'].'到'.$in['endDate'].'订单统计.xls';
}elseif($in['action']=='product'){
    //业务员商品统计

    $where = "";
    $giftWhere = "";
    $returnWhere = "";
    if(!empty($in['sid'])){
        $where .= " AND SiteID=".$in['sid'];
    }

    if(!empty($in['cid'])){
        $where .= " AND OrderUserID=".$in['cid'];
        $giftWhere .= " AND OrderUserID=".$in['cid'];
        $returnWhere .= "AND ReturnClient=".$in['cid'];
        $clientInfo = $db->get_row("SELECT ClientName,ClientCompanyName FROM ".DATATABLE."_order_client WHERE ClientID=".$in['cid']);
        $client = '-'. $clientInfo['ClientCompanyName'];
    }else{
        $where .= " AND OrderUserID IN(".$_SESSION['uinfo']['clientidmsg'].")";
        $giftWhere .= " AND OrderUserID IN(".$_SESSION['uinfo']['clientidmsg'].")";
        $returnWhere .= " AND ReturnClient IN(".$_SESSION['uinfo']['clientidmsg'].")";
    }

    if(empty($in['beginDate'])){
        $in['beginDate'] = date('Y-m-d',strtotime('-1 months'));
    }
    if(empty($in['endDate'])){
        $in['endDate'] = date('Y-m-d');
    }

    $title = "业务员".$_SESSION['uinfo']['usertruename'].$client.'从'.$in['beginDate'].'到'.$in['endDate'].'商品统计';
    $where .= " AND FROM_UNIXTIME(o.OrderDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59' ";

//购买商品
    $data_sql = "SELECT c.ContentID,COUNT(1) AS cnt,i.Coding,i.Name,SUM(c.ContentNumber) AS ContentNumber,SUM(c.ContentSend) AS ContentSend,SUM(c.ContentPrice * c.ContentNumber * c.ContentPercent * 0.1) AS Total
              FROM ".DATATABLE."_order_cart AS c
              LEFT JOIN ".DATATABLE."_order_orderinfo AS o
                ON o.OrderID = c.OrderID
               LEFT JOIN ".DATATABLE."_order_content_index AS i
                ON c.ContentID = i.ID
              WHERE c.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$where."
              GROUP BY ContentID,i.Name";

    $statdata = $db->get_results($data_sql);

//赠送商品
    $gift_sql = "SELECT SUM(ContentNumber) as cnum,SUM(ContentSend) as snum,c.ContentID,c.ContentName
             FROM ".DATATABLE."_order_cart_gifts c
             LEFT JOIN ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID
             WHERE c.CompanyID=".$_SESSION['uinfo']['ucompany']."
             AND FROM_UNIXTIME(o.OrderDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59'
             AND o.OrderStatus!=8 AND o.OrderStatus!=9 ".$giftWhere."
             GROUP BY c.ContentID ORDER BY cnum DESC";
    $gift_source = $db->get_results($gift_sql);
    $gift_arr = array();
    foreach($gift_source as $key=>$val){
        $gift_arr[$val['ContentID']] = $val;
    }

    //退货商品
    $return_sql = "SELECT SUM(ContentNumber) as cnum,c.ContentID,c.ContentName
               FROM ".DATATABLE."_order_cart_return c
               LEFT JOIN ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID
               WHERE c.CompanyID=".$_SESSION['uinfo']['ucompany']."
               AND FROM_UNIXTIME(o.ReturnDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59'
               AND (o.ReturnStatus=2 OR o.ReturnStatus=3 OR o.ReturnStatus=5) ".$returnWhere."
               GROUP BY c.ContentID ORDER BY cnum DESC";
    $return_source = $db->get_results($return_sql);
    $return_arr = array();
    foreach($return_source as $key=>$val){
        $return_arr[$val['ContentID']] = $val;
    }

    $objActSheet->setCellValue('A1',$title);
    $k=1;
    $k++;
    $objActSheet->setCellValue('A'.$k,'编号');
    $objActSheet->setCellValue('B'.$k,'商品名称');
    $objActSheet->setCellValue('C'.$k,'订购数');
    $objActSheet->setCellValue('D'.$k,'订购金额');
    $objActSheet->setCellValue('E'.$k,'赠送数');
    $objActSheet->setCellValue('F'.$k,'退货数');
    $objActSheet->setCellValue('G'.$k,'实际数');
    $objActSheet->setCellValue('H'.$k,'发货数');
    $goodsCnt = count($statdata);//商品总数
    $contentNumber = 0 ;//订购数
    $contentTotal = 0;//订购金额
    $giftNumber = 0;//赠送数
    $returnNumber = 0;//退货数
    $trueNumber = 0;//实际数
    $sendNumber = 0;//发货数
    foreach($statdata as $key=>$val){
        $k++;
        $val['giftNumber'] = !empty($gift_arr[$val['ContentID']]) ? $gift_arr[$val['ContentID']]['cnum'] : 0;
        $val['giftSendNumber'] = !empty($gift_arr[$val['ContentID']]) ? $gift_arr[$val['ContentID']]['snum'] : 0;
        $val['returnNumber'] = !empty($return_arr[$val['ContentID']]) ? $return_arr[$val['ContentID']]['cnum'] : 0;
        $val['trueNumber'] = $val['ContentNumber'] + $val['giftNumber'] - $val['returnNumber'];
        $val['sendNumber'] = $val['ContentSend'] + $val['giftSendNumber'];

        $contentNumber += $val['ContentNumber'];
        $contentTotal += $val['Total'];
        $giftNumber += $val['giftNumber'];
        $returnNumber += $val['returnNumber'];
        $trueNumber += $val['trueNumber'];
        $sendNumber += $val['sendNumber'];

        $objActSheet->setCellValue('A'.$k,$val['Coding'].' ');
        $objActSheet->setCellValue('B'.$k,$val['Name']);
        $objActSheet->setCellValue('C'.$k,$val['ContentNumber']);
        $objActSheet->setCellValue('D'.$k,'¥'.$val['Total']);
        $objActSheet->setCellValue('E'.$k,$val['giftNumber']);
        $objActSheet->setCellValue('F'.$k,$val['returnNumber']);
        $objActSheet->setCellValue('G'.$k,$val['trueNumber']);
        $objActSheet->setCellValue('H'.$k,$val['sendNumber']);
    }

    $k = $k+2;
    $objActSheet->setCellValue('A'.$k,'合计:');
    $objActSheet->setCellValue('B'.$k,$goodsCnt.'种');
    $objActSheet->setCellValue('C'.$k,$contentNumber);
    $objActSheet->setCellValue('D'.$k,'¥'.$contentTotal);
    $objActSheet->setCellValue('E'.$k,$giftNumber);
    $objActSheet->setCellValue('F'.$k,$returnNumber);
    $objActSheet->setCellValue('G'.$k,$trueNumber);
    $objActSheet->setCellValue('H'.$k,$sendNumber);

    $filename = $title.'.xls';
}

$ua = $_SERVER["HTTP_USER_AGENT"];
//$filename = '商品统计报表_'.$in['begindate'].'_'.$in['enddate'].'.xls';
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
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');
 ***/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>