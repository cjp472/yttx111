<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <script src="js/statistics.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

    <script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>
    <script type="text/javascript">
        $(function(){
            $("#begindate").datepicker();
            $("#enddate").datepicker();

            //搜索查看
            $("#showStatistics").click(function(){
                document.MainForm.action = 'statistics_deliver.php';
                document.MainForm.target = '_self';
                document.MainForm.submit();
            });

            //导出报表
            $("#buildExcel").click(function(){
                document.MainForm.action = 'statistics_excel.php?action=deliver';
                document.MainForm.target = 'exe_iframe';
                document.MainForm.submit();
            });

        });
    </script>
</head>

<body>
<?php include_once ("top.php");?>
<div id="bodycontent">
<div class="lineblank"></div>
<div id="searchline">
    <div class="leftdiv">

    </div>
    <div class="location"><strong>当前位置：</strong> <a href="statistics_deliver.php">发货统计</a></div>
</div>

<div class="line2"></div>
<div class="bline">
<div id="sortright" style="width:100%;">
    <form id="MainForm" name="MainForm" method="post" action="statistics_deliver.php"  >
        <input name="clientid" type="hidden" id="clientid"   maxlength="12" value="<? if(!empty($in['cid'])) echo $in['cid'];?>"   />
        <div class="line" >
            <fieldset class="fieldsetstyle">
                <legend>发货统计</legend>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="30" >
                            <select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:218px;" >
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
                            &nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;
                            <input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />
                            &nbsp;到&nbsp;
                            <input name="enddate" type="text" id="enddate"   maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   />&nbsp;
                            <input type="button" name="newbutton" value=" 查看 " class="mainbtn" id="showStatistics" />&nbsp;&nbsp;
                            <input type="button" name="exceltable" value=" 导出报表 " class="mainbtn" id="buildExcel" onclick="output_excel('between');" />
                        </td>
                    </tr>
                    <?php
                    if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
                    {
                        echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
                    }else{
                        $company = $_SESSION['uc']['CompanyID'];
                        $datatable = DATATABLE;
                        $appendMap = "";
                        if($in['cid']) {
                            $appendMap .= " AND c.ClientID=" . $in['cid'];
                        }

                        $sql = "SELECT s.ConsignmentDate,s.ConsignmentCompany,COUNT( DISTINCT c.OrderID) AS OrderCnt , COUNT( DISTINCT s.`ConsignmentID`) AS ConsignmentCnt, SUM(l.ContentNumber * c.ContentPrice) AS Amount
                                FROM {$datatable}_order_consignment AS s
                                LEFT JOIN {$datatable}_order_orderinfo AS o
                                    ON o.OrderSN = s.ConsignmentOrder  -- 不同公司生成的OrderSN可能相同，关联时必须加companyid条件过滤
                                INNER JOIN {$datatable}_order_out_library AS l
                                    ON l.ConsignmentID = s.ConsignmentID
                                LEFT JOIN {$datatable}_order_cart AS c
                                    ON c.ID = l.CartID
                                    WHERE o.OrderCompany = {$company} AND s.ConsignmentCompany = {$company} {$appendMap} AND ConsignmentDate <> '0000-00-00' AND ConsignmentDate >= '{$in['begindate']} 00:00:00' AND ConsignmentDate <= '{$in['enddate']} 23:59:59'
                                GROUP BY s.ConsignmentDate ASC ";
                        $list = $db->get_results($sql);
                        if(!empty($list))
                        {
                            $x = array();//横坐标日期
                            $y1 = array();//金额
                            $y2 = array();//发货订单数
                            $y3 = array();//发货单数量
                            foreach($list as $val) {
                                $x[] = "'" . $val['ConsignmentDate'] . "'";
                                $y1[] = $val['Amount'];
                                $y2[] = $val['OrderCnt'];
                                $y3[] = $val['ConsignmentCnt'];
                            }
                            ?>
                            <tr>
                                <td height="30" >

                                    <script type="text/javascript">

                                        var chart;
                                        $(document).ready(function() {
                                            chart = new Highcharts.Chart({
                                                chart: {
                                                    renderTo: 'container',
                                                    zoomType: 'xy'
                                                },
                                                title: {
                                                    text: '<? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 发货数据'
                                                },
                                                subtitle: {
                                                    text: ''
                                                },
                                                xAxis: [{
                                                    categories: [<? echo implode(',',$x);?>]
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
                                                        text: '发货金额',
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
                                                        (this.series.name == '发货金额' ? ' 元' : ' 个');
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
                                                    name: '发货金额',
                                                    color: '#4572A7',
                                                    type: 'spline',
                                                    yAxis: 1,
                                                    data: [<? echo implode(',',$y1);?>]
                                                }, {
                                                    name: '发货订单数',
                                                    color: '#89A54E',
                                                    type: 'column',
                                                    data: [<? echo implode(',',$y2);?>]
                                                },{
                                                    name: "发货单数量",
                                                    color:"#434348",
                                                    type:"spline",
                                                    data: [<?php echo implode(',',$y3); ?>]
                                                }]
                                            });
                                        });
                                    </script>
                                    <div id="container"></div>

                                </td>
                            </tr>

                            <tr>
                                <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong><? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 发货数据 <? if(!empty($in['cid'])) echo '('.$clientarr[$in['cid']].')';?></strong></td>
                            </tr>
                            <tr>
                                <td >

                                    <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
                                        <thead>
                                        <tr>
                                            <td width="2%" class="bottomlinebold"><label> &nbsp;</label></td>
                                            <td width="25%" class="bottomlinebold">发货日期</td>
                                            <td width="25%" class="bottomlinebold">发货金额</td>
                                            <td  class="bottomlinebold">发货订单数</td>
                                            <td width="25%" class="bottomlinebold">发货单数</td>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        $totalAmount = 0;//总发货金额
                                        $totalOrder = 0;//总订货数
                                        $totalConsignment = 0; //总发货单数
                                        foreach($list as $var)
                                        {
                                            $totalAmount += $var['Amount'];
                                            $totalOrder += $var['OrderCnt'];
                                            $totalConsignment += $var['ConsignmentCnt'];
                                            ?>
                                            <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                                <td>&nbsp;</td>
                                                <td ><? echo $var['ConsignmentDate'];?></td>
                                                <td >¥ <? echo $var['Amount'] ? $var['Amount'] : 0;?></td>
                                                <td ><? echo $var['OrderCnt'];?></td>
                                                <td ><?php echo (int)$var['ConsignmentCnt'];?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                            <td>&nbsp;</td>
                                            <td ><strong>合计：</strong></td>
                                            <td ><strong>¥ <? echo number_format($totalAmount,2,'.',',');?> 元</strong></td>
                                            <td ><strong> <? echo $totalOrder;?> 个</strong></td>
                                            <td ><strong><? echo $totalConsignment;?></strong></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </td>
                            </tr>
                        <? }else{?>
                            <tr>
                                <td height="130" bgcolor="#ffffff" align="center">&nbsp; 暂无符合条件的数据!</td>
                            </tr>
                        <? }}?>
                </table>
            </fieldset>
        </div>
        <br style="clear:both;" />
        <INPUT TYPE="hidden" name="referer" value ="" >
    </form>
</div>
</div>
<br style="clear:both;" />
</div>


<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<div id="windowForm">
    <div class="windowHeader">
        <h3 id="windowtitle">订单列表：</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContentList" >
        数据载入中...
    </div>
</div>
</body>
</html>