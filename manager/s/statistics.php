<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$clientdata = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");

$sdmsg = '';
$locationmsg = '';
$valuearr = get_set_arr('product');
setcookie("backurl", $_SERVER['REQUEST_URI']);
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
}else{
    $clientIds = array();
    foreach($clientdata as $val){
        $clientIds[] = $val['ClientID'];
    }
    $where .= " AND OrderUserID IN(".implode(',',$clientIds).")";
    //所有药店
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
$axis = array(); // 订单日期
$totals = array();//订单金额
$cnts = array();//订单数量
foreach($stataData as $val){
    $axis[] = $val['ODate'];
    $totals[] = $val['OTotal'];
    $cnts[] = $val['totalnumber'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/showpage.css" />
    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    <script src="../scripts/jquery.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>

    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
    <script type="text/javascript">
        $(function(){
            $("#beginDate,#endDate").datepicker({changeMonth: true,	changeYear: true});
            $("#newbutton").click(function(){
                //查看统计
                $("#MainForm").attr('method','get').attr('action','statistics.php').attr('target','_self').get(0).submit();

            });

            $("#exceltable").click(function(){
                //导出报表
                $("#MainForm").attr('method','post').attr('action','statistics_excel.php?action=order').attr('target','exe_iframe').get(0).submit();
            });
        });
    </script>
</head>

<body>
<? include_once ("top.php");?>

<div class="bodyline" style="height:25px;"></div>


<div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
            <form id="FormSearch" name="FormSearch" method="post" action="order.php">
                <tr>
                    <!--<td width="80" align="center"><strong>订单搜索：</strong></td>
                    <td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<?/* if(!empty($in['kw'])) echo $in['kw'];*/?>"  onfocus="this.select();" /></td>
                    <td align="center" width="80">起止时间：</td>
                    <td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<?/* if(!empty($in['bdate'])) echo $in['bdate'];*/?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<?/* if(!empty($in['edate'])) echo $in['edate'];*/?>" /></td>
                    <td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>-->
                    <td aling="right"><div class="location"><strong>当前位置：</strong><a href="statistics.php">销售统计</a> &#8250;&#8250; 订单统计</div></td>
                </tr>
            </form>
        </table>
    </div>

    <div class="line2"></div>
    <div class="bline">
        <div id="sortleft">

            <!-- tree -->
            <div class="leftlist">

                <!--<strong><a href="statistics.php">时间段订单统计</a></strong></div>
                <ul style="padding: 2px 0 10px 0;">
                    <li>- <a href="statistics_y.php" >年订单统计</a></li>
                    <li>- <a href="statistics_m.php">月订单统计</a></li>
                    <li>- <a href="statistics_d.php" >日订单统计</a></li>
                </ul>-->

            <hr style="clear:both;" />
                <div ><strong><a href="statistics.php">药店</a></strong></div>
                <ul style="padding: 2px 0 10px 0;">
                    <?php
                        foreach($clientdata as $client){
                    ?>
                            <li>
                                <a href="statistics.php?<?php echo http_build_query(array('cid'=>$client['ClientID'],'beginDate'=>$in['beginDate'],'endDate'=>$in['endDate'])); ?>"><?php echo substr($client['ClientCompanyPinyi'],0,1) . '-'.$client['ClientCompanyName']; ?></a>
                            </li>
                    <?php } ?>
                    <form name="changetypeform" id="changetypeform" action="statistics.php" method="get">
                        <select id="cid" style="display:none;" name="cid" onchange="javascript:submit()" style="width:160px !important; width:145px;">
                            <option value="" >⊙ 所有药店</option>
                            <?php
                            $n = 0;
                            foreach($clientdata as $areavar)
                            {
                                $n++;
                                if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
                                $clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
                                echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'"  >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
                            }
                            ?>
                        </select>
                    </form>
                </ul>

            </div>
            <!-- tree -->
        </div>
        <div id="sortright">
            <form id="MainForm" name="MainForm" method="post" action="order_excel.php" target="exe_iframe" >
                <input type="hidden" name="cid" value="<?php echo $in['cid']; ?>" />
                <div class="line">
                    <fieldset class="fieldsetstyle">
                        <legend>订单统计</legend>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td height="30" >
                                    &nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;
                                    <input name="beginDate" type="text" id="beginDate"   maxlength="12" onfocus="this.select();" value="<?php echo $in['beginDate']; ?>"   />&nbsp;到&nbsp;
                                    <input name="endDate" type="text" id="endDate"   maxlength="12" onfocus="this.select();" value="<?php echo $in['endDate']; ?>"   />&nbsp;
                                    <input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" />&nbsp;&nbsp;
                                    <input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" />
                                </td>
                            </tr>
                            <?php
                                if(empty($stataData)) {
                                    ?>
                                <tr>
                                    <td align="center">暂无相关数据</td>
                                </tr>
                                <?php
                                }else {
                                    ?>
                                    <tr>
                                        <td>
                                            <script type="text/javascript">

                                                var chart;
                                                $(document).ready(function() {
                                                    chart = new Highcharts.Chart({
                                                        chart: {
                                                            renderTo: 'container',
                                                            zoomType: 'xy'
                                                        },
                                                        title: {
                                                            text: '从 <?php echo $in['beginDate']; ?> 到 <?php echo $in['endDate']; ?>   订单数据'
                                                        },
                                                        subtitle: {
                                                            text: ''
                                                        },
                                                        xAxis: [{
                                                            categories: [<?php echo implode(',',$axis); ?>]
                                                        }],
                                                        yAxis: [{ // Primary yAxis
                                                            labels: {
                                                                formatter: function() {
                                                                    return this.value +'个';
                                                                },
                                                                style: {
                                                                    color: '#89A54E'
                                                                }
                                                            },
                                                            title: {
                                                                text: '订单数',
                                                                style: {
                                                                    color: '#89A54E'
                                                                }
                                                            }
                                                        }, { // Secondary yAxis
                                                            title: {
                                                                text: '订单金额',
                                                                style: {
                                                                    color: '#4572A7'
                                                                }
                                                            },
                                                            labels: {
                                                                formatter: function() {
                                                                    return this.value +' 元';
                                                                },
                                                                style: {
                                                                    color: '#4572A7'
                                                                }
                                                            },
                                                            opposite: true
                                                        }],
                                                        tooltip: {
                                                            formatter: function() {
                                                                return ''+
                                                                this.x +': '+ this.y +
                                                                (this.series.name == '订单金额' ? ' 元' : ' 个');
                                                            }
                                                        },
                                                        legend: {
                                                            layout: 'vertical',
                                                            align: 'left',
                                                            x: 120,
                                                            verticalAlign: 'top',
                                                            y: 100,
                                                            floating: true,
                                                            backgroundColor: '#FFFFFF'
                                                        },
                                                        series: [{
                                                            name: '订单金额',
                                                            color: '#4572A7',
                                                            type: 'spline',
                                                            yAxis: 1,
                                                            data: [<?php echo implode(',',$totals); ?>]
                                                        }, {
                                                            name: '订单数',
                                                            color: '#89A54E',
                                                            type: 'column',
                                                            data: [<?php echo implode(',',$cnts); ?>]
                                                        }]
                                                    });
                                                });
                                            </script>
                                            <div id="container"></div>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong>从 <?php echo $in['beginDate']; ?> 到 <?php echo $in['endDate']; ?>  订单数据 </strong></td>
                                    </tr>
                                    <tr>
                                        <td >

                                            <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
                                                <thead>
                                                <tr>
                                                    <td width="2%" class="bottomlinebold"><label> &nbsp;</label></td>
                                                    <td width="25%" class="bottomlinebold">日期</td>
                                                    <td width="25%" class="bottomlinebold">订单金额</td>
                                                    <td  class="bottomlinebold">总订单数</td>
                                                    <td width="25%" class="bottomlinebold">待审核订单</td>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php
                                                $cnt = 0;
                                                $total = 0;
                                                foreach($stataData as $val) {
                                                    $cnt += $val['totalnumber'];
                                                    $total += $val['OTotal'];
                                                    ?>
                                                    <tr class="bottomline" onmouseover="inStyle(this);" onmouseout="outStyle(this);">
                                                        <td>&nbsp;</td>
                                                        <td><?php echo $val['ODate']; ?></td>
                                                        <td><?php echo $val['OTotal']; ?></td>
                                                        <td><?php echo $val['totalnumber']; ?></td>
                                                        <td><?php echo $rarr[$val['ODate']] ? $rarr[$val['ODate']] : 0; ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                                <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                                    <td>&nbsp;</td>
                                                    <td ><strong>合计：</strong></td>
                                                    <td ><strong>¥ <?php echo $total; ?> 元</strong></td>
                                                    <td ><strong> <?php echo $cnt; ?> 个</strong></td>
                                                    <td ><strong><?php echo $cnt0; ?>个 (¥ <?php echo $total0; ?>)</strong></td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </td>
                                    </tr>
                                <?php
                                }
                            ?>

                        </table>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
    <br style="clear:both;" />
</div>

<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>