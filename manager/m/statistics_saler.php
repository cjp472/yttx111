<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");


if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate']	 = date("Y-m-d");
$begin = strtotime(date('Y-m-d 00:00:00',strtotime($in['begindate'])));
$end = strtotime(date('Y-m-d 23:59:59',strtotime($in['enddate'])));

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

$datasql   = "SELECT UserID,UserName,UserTrueName,UserPhone,UserLogin,UserLoginIP,UserLoginDate,UserFlag FROM ".DATABASEU.DATATABLE."_order_user where UserCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and UserFlag!='1' and UserType='S' ORDER BY UserID DESC";
$saler = $db->get_results($datasql);
$axis = array();//横坐标
$cnts = array();//订单数
$totals = array();//订单金额

$calc_sql = "SELECT SUM(OrderTotal) AS total,COUNT(1) AS cnt,SalerID FROM ".DATATABLE."_order_orderinfo AS o
              LEFT JOIN ".DATATABLE."_order_salerclient AS s
              ON s.ClientID=o.OrderUserID
              WHERE o.OrderStatus < 8 AND s.CompanyID=".$_SESSION['uc']['CompanyID']." AND o.OrderDate BETWEEN ".$begin." AND ".$end."
              GROUP BY SalerID
              ORDER BY SalerID DESC";
$calc_rst = $db->get_results($calc_sql);
$calc = array();
foreach($calc_rst as $val){
    $calc[$val['SalerID']] = $val;
}
foreach($saler as $key=>$val){
    $axis[$key] = "'".$val['UserTrueName']."'";
    $cnts[$key] = $calc[$val['UserID']] ? $calc[$val['UserID']]['cnt'] : 0;
    $totals[$key] = $calc[$val['UserID']] ? $calc[$val['UserID']]['total'] : 0;
    $saler[$key]['cnts'] = $cnts[$key];
    $saler[$key]['total'] = $totals[$key];
}
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
            $("#newbutton").click(function(){
                document.MainForm.action = 'statistics_saler.php';
                document.MainForm.method = 'get';
                document.MainForm.target = '_self';
                document.MainForm.submit();
            });
            $("#exceltable").click(function(){
                document.MainForm.action = 'statistics_excel.php?action=salers';
                document.MainForm.method = 'post';
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
    <div class="location"><strong>当前位置：</strong> <a href="statistics_saler.php">客情官订单统计</a></div>
</div>

<div class="line2"></div>
<div class="bline">
<div id="sortright" style="width:100%;">
    <form id="MainForm" name="MainForm" method="get" action="statistics_saler.php"  >
        <div class="line" >
            <fieldset class="fieldsetstyle">
                <legend>客情官订单统计</legend>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="30" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;
                            <input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;<input name="enddate" type="text" id="enddate"   maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   />&nbsp;
                            <input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick_old="show_stat_data('between')"/>&nbsp;&nbsp;
                            <input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick_old="output_excel('between');" /></td>
                    </tr>
                    <?php
                    if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
                    {
                        echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
                    }else{

                        if(!empty($saler))
                        {
                            $axis = implode(",",$axis);
                            $cnts = implode(",",$cnts);
                            $totals = implode(",",$totals);
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
                                                    text: '<? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 订单数据'
                                                },
                                                subtitle: {
                                                    text: ''
                                                },
                                                xAxis: [{
                                                    categories: [<? echo $axis;?>]
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
                                                    data: [<? echo $totals;?>]
                                                }, {
                                                    name: '订单数',
                                                    color: '#89A54E',
                                                    type: 'column',
                                                    data: [<? echo $cnts;?>]
                                                }]
                                            });
                                        });
                                    </script>
                                    <div id="container"></div>

                                </td>
                            </tr>

                            <tr>
                                <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong><? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 订单数据 <? if(!empty($in['cid'])) echo '('.$clientarr[$in['cid']].')';?></strong></td>
                            </tr>
                            <tr>
                                <td >

                                    <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
                                        <thead>
                                        <tr>
                                            <td width="2%" class="bottomlinebold"><label> &nbsp;</label></td>
                                            <td width="25%" class="bottomlinebold">客情官</td>
                                            <td width="25%" class="bottomlinebold">真实姓名</td>
                                            <td width="25%" class="bottomlinebold">订单金额</td>
                                            <td  class="bottomlinebold">总订单数</td>
                                            <td class="bottomlinebold">查看明细</td>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?
                                        $totalm = 0;
                                        $totaln = 0;
                                        foreach($saler as $var)
                                        {
                                            $totalm = $totalm + $var['total'];
                                            $totaln = $totaln + $var['cnts'];
                                            ?>
                                            <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                                <td>&nbsp;</td>
                                                <td ><?php echo $var['UserName']; ?></td>
                                                <td><?php echo $var['UserTrueName']; ?></td>
                                                <td >¥ <? echo $var['total'];?></td>
                                                <td ><? echo $var['cnts'];?></td>
                                                <td><a target="_blank" href="statistics_saler_detail.php?<?php echo http_build_query(array('uid'=>$var['UserID'],'begindate'=>$in['begindate'],'enddate'=>$in['enddate'])); ?>">[明细]</a></td>
                                            </tr>
                                        <?
                                        }
                                        ?>
                                        <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                            <td>&nbsp;</td>
                                            <td ><strong>合计：</strong></td>
                                            <td >&nbsp;</td>
                                            <td ><strong> ¥ <? echo $totalm;?> </strong></td>
                                            <td ><strong><? echo $totaln.'个';?></strong></td>
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