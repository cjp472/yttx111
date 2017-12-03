<?php
define('READ_EXP',true);
$menu_flag = "ty";
include_once ("header.php");

$sqlmsg = "";
if(!empty($in['bdate'])) $sqlmsg .= " and a.LoginDate >= '".strtotime($in['bdate'])."' ";
if(!empty($in['edate'])) $sqlmsg .= " and a.LoginDate <= '".strtotime($in['edate'])."' ";

// 读取所有的行业
$industrys = $db->get_results( " select IndustryID,IndustryName from ".DATABASEU.DATATABLE."_order_industry " );
$industrys = array_column($industrys ? $industrys : array() , 'IndustryName','IndustryID');

$no_use_sql = "select count(*) as allrow,CompanyIndustry from ".DATABASEU.DATATABLE."_order_company where IsSystem='0' and CompanyFlag = '0' AND IsUse<>1 group by CompanyIndustry";
$no_use = $db->get_results($no_use_sql);
$no_use = array_column($no_use ? $no_use : array(), 'allrow','CompanyIndustry');


$use_ty_sql = " select count(*) as allrow,b.CompanyIndustry,b.IsUse from ".DATABASEU.DATATABLE."_order_login_user_log a
left join ".DATABASEU.DATATABLE."_order_company b on a.LoginCompany = b.CompanyID
where b.IsSystem='0' and b.CompanyFlag = '0' {$sqlmsg} group by b.CompanyIndustry,b.IsUse ";
$use_ty = $db->get_results($use_ty_sql);
$use_ty = array_column($use_ty ? $use_ty : array() , 'allrow','CompanyIndustry');

$sdata = array();


foreach($industrys as $k=>$v) {
    $sdata[$k]['use'] = (int)$use_ty[$k];
    $sdata[$k]['unUse'] = (int)$no_use[$k];
}

$total_calc = array();
$use_calc = array();
$scale_calc = array();//比例
foreach($industrys as $k=>$v) {
    $total_calc[$k] = (int)array_sum($sdata[$k]);
    $use_calc[$k] = (int)$sdata[$k]['use'];
    $scale_calc[$k] = $use_calc[$k] / $total_calc[$k];
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/jquery.treeview.css" />

    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>
    <script type="text/javascript">
        $(function(){
            $("#bdate").datepicker();
            $("#edate").datepicker();
        });
    </script>

</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>


<div id="bodycontent">
    <div class="lineblank"></div>

    <div id="searchline">
        <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
            <form id="FormSearch" name="FormSearch" method="post" action="ty_statistics.php">
                <tr>
                    <td align="center" width="80">起止时间：</td>
                    <td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<?php echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<?php echo $in['edate'];?>" />
                    </td>
                    <td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
                    <td aling="right"><div class="location"><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="ty_statistics.php">体验数据统计</a></div></td>
                </tr>
            </form>
        </table>
    </div>


    <div class="line2"></div>

    <div class="bline" >

        <div >
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td colspan="8">
                        <script type="text/javascript">

                            var chart;
                            $(document).ready(function() {
                                chart = new Highcharts.Chart({
                                    chart: {
                                        renderTo: 'container',
                                        zoomType: 'xy'
                                    },
                                    title: {
                                        text: '体验数据统计'
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: [{
                                        categories: [<?php echo "'" .implode("','",array_values($industrys)) ."'"; ?>]
                                        //categories: ['20150826','20150827','20150831','20150902','20150907','20150908','20150909','20150914','20150916','20150918','20150921','20150922','20150923','20150924']
                                    }],
                                    yAxis: [{ // Primary yAxis
                                        labels: {
                                            formatter: function() {
                                                return this.value * 100 +'%';
                                            },
                                            style: {
                                                color: '#89A54E'
                                            }
                                        },
                                        title: {
                                            text: '使用占比',
                                            style: {
                                                color: '#89A54E'
                                            }
                                        }
                                    }, { // Secondary yAxis
                                        title: {
                                            text: '数量',
                                            style: {
                                                color: '#4572A7'
                                            }
                                        },
                                        labels: {
                                            formatter: function() {
                                                return this.value +' 个';
                                            },
                                            style: {
                                                color: '#4572A7'
                                            }
                                        },
                                        opposite: true
                                    }],
                                    tooltip: {
                                        formatter: function() {
                                            switch(this.series.name) {
                                                case '总数量':
                                                    return this.x + ' - 共 ' + this.y + '个';
                                                    break;
                                                case '已用数量':
                                                    return this.x + ' - 已用 ' + this.y + '个';
                                                    break;
                                                case '使用占比':
                                                    return this.x + ' - 使用比例' + (this.y * 100).toFixed(2) + '%';
                                                    break;
                                                default:
                                                    break;
                                            }
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
                                        name: '总数量',
                                        color: '#4572A7',
                                        type: 'spline',
                                        yAxis: 1,
                                        data: [<?php echo implode(',',$total_calc) ?>]
                                    }, {
                                        name: '已用数量',
                                        color: '#000000',
                                        type: 'spline',
                                        yAxis:1,
                                        data:[<?php echo implode(',',$use_calc); ?>]
                                    },{
                                        name: '使用占比',
                                        color: '#89A54E',
                                        type: 'column',
                                        yAxis:0,
                                        data: [<?php echo implode(',',$scale_calc); ?>]
                                    }]
                                    //spline,column
                                });
                            });
                        </script>
                        <div id="container"></div>

                    </td>
                </tr>
                <tr class="bottomlinebold">
                    <td class="bottomlinebold" style="padding-left:20px;">行业</td>
                    <td class="bottomlinebold">总量</td>
                    <td class="bottomlinebold">已用</td>
                    <td class="bottomlinebold">比例</td>
                </tr>
                <?php $a_total = $a_use = 0; ?>
                <?php foreach($industrys as $key => $val) { ?>
                    <?php
                        $i_cnt = (int)array_sum($sdata[$key]);
                        $a_total += $i_cnt;
                        $a_use += (int)$sdata[$key]['use'];
                    ?>
                <tr class="bottomline">
                    <td style="padding-left:20px;"><?php echo $val; ?></td>
                    <td><?php echo $i_cnt; ?></td>
                    <td><?php echo (int)$sdata[$key]['use']; ?></td>
                    <td><?php echo sprintf("%02.2f%%",(int)$sdata[$key]['use'] / $i_cnt * 100); ?></td>
                </tr>

                <?php } ?>
                <tr class="bottomline">
                    <td style="padding-left:20px;">
                        合计：
                    </td>
                    <td><?php echo $a_total; ?></td>
                    <td><?php echo $a_use; ?></td>
                    <td></td>
                </tr>
            </table>
        </div>

    </div>
    <br style="clear:both;" />

</div>

<? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>