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
    <link rel="stylesheet" href="css/jquery.treeview.css" />
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

    <script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>
    <script type="text/javascript">
        $(function(){
            $("#begindate").datepicker();
            $("#enddate").datepicker();
            $("#tree").treeview({
                collapsed: true,
                animated: "medium",
                control:"#sidetreecontrol",
                persist: "location"
            });

            $("#newbutton").click(function(){
                document.MainForm.action = 'statistics_area.php';
                document.MainForm.target = '_self';
                document.MainForm.submit();
            });

            $("#exceltable").click(function(){
                alert('yy');
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
    <div class="location"><strong>当前位置：</strong> <a href="statistics.php">订单统计</a></div>
</div>

<div class="line2"></div>
<div class="bline">
<div id="sortleft">

    <!-- tree -->
    <div class="leftlist">

        <div >
            <strong><a href="statistics.php">时间段订单统计</a></strong></div>
        <ul>
            <li>- <a href="statistics_y.php" >年订单统计</a></li>
            <li>- <a href="statistics_m.php">月订单统计</a></li>
            <li>- <a href="statistics_d.php" >日订单统计</a></li>
        </ul>
        <hr style="clear:both;" />
        <div ><strong><a href="statistics_client.php">药店订单统计</a></strong></div>
        <hr style="clear:both;"/>
        <!-- tree -->
        <div id="sidetree">
            <div class="treeheader">
                <strong><a href="statistics_area.php?begindate=<?php echo $in['begindate']; ?>&enddate=<?php echo $in['enddate']; ?>">地区订单统计</a></strong></div>
            <ul id="tree">
                <?php
                $sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
                echo ShowTreeMenu($sortarr,0,$in);

                foreach($sortarr as $areavar)
                {
                    $areaarr[$areavar['AreaID']]   = $areavar['AreaName'];
                    $areaarr_p[$areavar['AreaID']] = $areavar['AreaParentID'];
                }
                ?>
            </ul>
        </div>
        <!-- tree -->
    </div>
    <!-- tree -->
</div>
<div id="sortright">
    <form id="MainForm" name="MainForm" method="post" action="statistics_area.php"  >
        <input name="clientid" type="hidden" id="clientid"   maxlength="12" value="<? if(!empty($in['cid'])) echo $in['cid'];?>"   />
        <div class="line" >
            <fieldset class="fieldsetstyle">
                <legend>订单统计</legend>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="30" >
                            <input type="hidden" name="aid" value="<?php echo $in['aid']; ?>" />
                            &nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;
                            <input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />
                            &nbsp;到&nbsp;
                            <input name="enddate" type="text" id="enddate"   maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   />
                            &nbsp;
                            <input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" />
                            &nbsp;&nbsp;
                            <!--<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" />-->
                        </td>
                    </tr>
                    <?php
                    if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
                    {
                        echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
                    }else{
                        $aid = intval($in['aid']);
                        $companyid = $_SESSION['uinfo']['ucompany'];
                        $alist = $db->get_results("SELECT * FROM rsung_order_area WHERE AreaParentID={$aid} AND AreaCompany=".$companyid);
                        $last = "";
                        if(empty($alist)){
                        	$last = " OR AreaID={$aid} ";
                        	$alist = $db->get_results("SELECT * FROM rsung_order_area WHERE AreaParentID={$aid} OR AreaID={$aid} AND AreaCompany=".$companyid);
                        }
                        $wt = "";
                        foreach($alist as $k=>$v){
                            $wt .= " WHEN AreaID=".$v['AreaID']." THEN '".implode(',',getSon($v['AreaID']))."'";
                        }

                        $sql = "SELECT
                                  cli.AreaID,cli.AreaName,cli.IDS,SUM(o.OrderTotal) AS Total,SUM(1) AS CNT,SUM(IF(o.OrderStatus=0,o.OrderTotal,0)) AS CKTOTAL,SUM(IF(o.OrderStatus=0,1,0)) AS CKCNT
                                FROM ".DATATABLE."_order_orderinfo AS o
                                INNER JOIN (
                                     SELECT ClientID ,ClientName,ClientArea
                                    ,A.IDS,A.AreaID,A.AreaName
                                    FROM ".DATATABLE."_order_client AS c
                                    LEFT JOIN (
                                        SELECT AreaID,AreaName,
                                            CASE
                                                {$wt}
                                                ELSE ''
                                            END AS IDS
                                        FROM ".DATATABLE."_order_area
                                        WHERE AreaCompany={$companyid} AND AreaID IN(
                                            SELECT AreaID FROM ".DATATABLE."_order_area WHERE AreaParentID = {$aid}{$last} AND AreaCompany={$companyid}
                                        )
                                    ) AS A
                                    ON A.IDS REGEXP CONCAT('^',c.ClientArea,'$|^',c.ClientArea,',|,',c.ClientArea,',|,',c.ClientArea,'$')
                                    WHERE c.ClientCompany = {$companyid}
                                    AND A.IDS <> ''
                                ) AS cli ON o.OrderUserID = cli.ClientID
                                WHERE o.OrderCompany = {$companyid} AND o.OrderStatus <>9 AND o.OrderStatus <> 8 AND o.OrderDate BETWEEN ".strtotime($in['begindate'].' 00:00:00')." AND ".strtotime($in['enddate'].' 23:59:59')."
                                GROUP BY cli.AreaName ORDER BY Total DESC";
                        $list = $db->get_results($sql);
                        $list = $list ? $list : array();
                        $areaList = $db->get_results("SELECT AreaID,AreaName,'0' as Total,'0' as CNT,'0' as CKTOTAL,'0' as CKCNT, CASE  {$wt} ELSE '' END AS IDS FROM ".DATATABLE."_order_area WHERE AreaCompany={$companyid} AND AreaID IN( SELECT AreaID FROM ".DATATABLE."_order_area WHERE AreaParentID = {$aid} AND AreaCompany={$companyid} )
");

                        $areaList = $areaList ? $areaList : array();
                        $listAsoc = array();
                        foreach($list as $key=>$val){
                            $listAsoc[$val['AreaID']] = $val;
                        }
                        foreach($areaList as $val){
                            if(empty($listAsoc[$val['AreaID']])){
                                $listAsoc[$val['AreaID']] = $val;
                            }
                        }
                        $list = $listAsoc;
                        if(!empty($list))
                        {
                            $chart_area = array();
                            $chart_amount = array();
                            $chart_count = array();

                            foreach($list as $val){
                                $chart_area[] = "'".$val['AreaName']."'";
                                $chart_amount[] = $val['Total'];
                                $chart_count[] = $val['CNT'];
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
                                                    text: '<? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 订单数据'
                                                },
                                                subtitle: {
                                                    text: ''
                                                },
                                                xAxis: [{
                                                    categories: [<? echo implode(',',$chart_area);?>]
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
                                                    data: [<? echo implode(',',$chart_amount);?>]
                                                }, {
                                                    name: '订单数',
                                                    color: '#89A54E',
                                                    type: 'column',
                                                    data: [<? echo implode(',',$chart_count);?>]
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
                                            <td width="25%" class="bottomlinebold">地区</td>
                                            <td width="25%" class="bottomlinebold">订单金额</td>
                                            <td  class="bottomlinebold">总订单数</td>
                                            <td width="25%" class="bottomlinebold">待审核订单</td>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                            $tTotal = 0;
                                            $tCnt = 0;
                                            $tCkCnt = 0;
                                            $tCkTotal = 0;
                                            foreach($list as $val){
                                                $tTotal += $val['Total'];
                                                $tCnt += $val['CNT'];
                                                $tCkCnt += $val['CKCNT'];
                                                $tCkTotal += $val['CKTOTAL'];
                                        ?>
                                                <tr class="bottomline" onmouseover="inStyle(this);" onmouseout="outStyle(this);">
                                                    <td>&nbsp;</td>
                                                    <td>
                                                        <?php
                                                            if($val['AreaID']==$val['IDS']){
                                                        ?>
                                                                <?php echo $val['AreaName']; ?>
                                                                <?php }else{ ?>
                                                                <a href="statistics_area.php?aid=<?php echo $val['AreaID'] ?>&begindate=<?php echo $in['begindate']; ?>&enddate=<?php echo $in['enddate']; ?>"><?php echo $val['AreaName']; ?></a>
                                                                <?php } ?>
                                                    </td>
                                                    <td>¥ <?php echo $val['Total']; ?></td>
                                                    <td><?php echo $val['CNT']; ?></td>
                                                    <td><?php echo $val['CKCNT'] ?></td>
                                                </tr>
                                        <?php
                                            }
                                        ?>
                                        <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                            <td>&nbsp;</td>
                                            <td ><strong>合计：</strong></td>
                                            <td ><strong>¥ <? echo number_format($tTotal,2,'.',',');?> 元</strong></td>
                                            <td ><strong> <? echo $tCnt;?> 个</strong></td>
                                            <td ><strong><? echo $tCkCnt.'个 (¥ '.number_format($tCkTotal,2,'.',',').')';?></strong></td>
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
<?php
function ShowTreeMenu($resultdata,$p_id,$in=array())
{
    $frontMsg  = "";
    foreach($resultdata as $key => $var)
    {
        if($var['AreaParentID'] == $p_id)
        {
            if($var['AreaParentID']=="0")
            {
                $frontMsg  .= '<li><a href="statistics_area.php?aid='.$var['AreaID'].'&begindate='.$in['begindate'].'&enddate='.$in['enddate'].'"><strong>'.$var['AreaName'].'</strong></a>';
            }
            else
            {
                $frontMsg  .= '<li><a href="statistics_area.php?aid='.$var['AreaID'].'&begindate='.$in['begindate'].'&enddate='.$in['enddate'].'">'.$var['AreaName'].'</a>';
            }

            $frontMsg2 = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$in);
            if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
            $frontMsg .= '</li>';
        }
    }
    return $frontMsg;
}

function getSon($pid){
    global $db;
    $son = array();
    $son[] = $pid;
    $list = $db->get_results("SELECT * FROM rsung_order_area WHERE AreaParentID = {$pid} AND AreaCompany=".$_SESSION['uinfo']['ucompany']);
    foreach($list as $val){
        $son = array_merge($son,getSon($val['AreaID']));
    }
    return $son;
}

?>