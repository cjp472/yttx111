<?php
$menu_flag = "open";
include_once ("header.php");
include_once ("../class/ip2location.class.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name='robots' content='noindex,nofollow' />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/showpage.css" />
    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

    <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
    <script type="text/javascript">
        $(function() {
	       $("#bdate").datepicker({changeMonth: true,	changeYear: true});
	       $("#edate").datepicker({changeMonth: true,	changeYear: true});
        });
    </script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <div class="leftdiv">
            <form id="FormSearch" name="FormSearch" method="get" action="open_statistics.php">
                &nbsp;&nbsp;<label>统计时间:&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />
                
                <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
                </label>
            </form>
        </div>

        <div class="location"><strong>当前位置：</strong><a href="open_statistics.php">统计来源</a> </div>
    </div>

    <div class="line2"></div>
    <div class="bline">

        <?php
            $begin_company_id = 1;//fixme 需要改为600
            $sqlmsg = "";
            
            if(!empty($in['bdate'])) $sqlmsg .= " and s.CS_BeginDate >= '".$in['bdate']."' ";
            if(!empty($in['edate'])) $sqlmsg .= " and s.CS_BeginDate <= '".$in['edate']."' ";
            
            $calc_data = $db->get_results("SELECT s.LoginFrom,COUNT(*) as Total FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company right join ".DATABASEU.DATATABLE."_buy_account as a ON a.company_id=c.CompanyID where c.CompanyFlag='0' AND c.CompanyID > {$begin_company_id} ".$sqlmsg."  GROUP BY s.LoginFrom");
            $calc_data = array_column($calc_data ? $calc_data : array(),'Total','LoginFrom');
            $total = array_sum($calc_data);
            $total = $total ? $total : 0;
            $dft_data = array_combine(array_keys($from_arr),array_pad(array(),count($from_arr),0));
            $calc_data = array_merge($dft_data,$calc_data);
            $calc_str_arr = array();
            foreach($calc_data as $key=> $val) {
                $calc_str_arr[] = array($from_arr[$key] ,(int)$val);
            }
        ?>
        <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="5%" class="bottomlinebold" colspan="3">
                        <script type="text/javascript">
                            var chart;
                            $(document).ready(function() {
                                chart = new Highcharts.Chart({
                                    chart: {
                                        renderTo: 'container',
                                        plotBackgroundColor: null,
                                        plotBorderWidth: null,
                                        plotShadow: false
                                    },
                                    title: {
                                        text: '注册来源统计'
                                    },
                                    tooltip: {
                                        formatter: function() {
                                            return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
                                        }
                                    },
                                    plotOptions: {
                                        pie: {
                                            allowPointSelect: true,
                                            cursor: 'pointer',
                                            dataLabels: {
                                                enabled: true,
                                                color: '#000000',
                                                connectorColor: '#000000',
                                                formatter: function() {
                                                    return '<b>'+ this.point.name +'</b>: '+ this.y +' 人';
                                                }
                                            }
                                        }
                                    },
                                    series: [{
                                        type: 'pie',
                                        name: 'Browser share',
                                        data: <?php echo json_encode($calc_str_arr); ?>
                                    }]
                                });
                            });
                        </script>
                        <div id="container" style="width: 700px; height: 400px; margin: 0 auto"></div>
                    </td>
                </tr>
                <tr class="bottomlinebold">
                    <td style="padding-left:20px;">
                        来源
                    </td>
                    <td>
                        数量
                    </td>
                    <td>
                        占比
                    </td>
                </tr>
                <?php foreach($calc_data as $key=>$val) { ?>
                <tr class="bottomline">
                    <td style="padding-left:20px;">
                        <?php echo $from_arr[$key]; ?>
                    </td>
                    <td>
                        <?php echo (int)$val; ?> 人
                    </td>
                    <td>
                        <?php echo sprintf("%02.2f%%",(int)$val / $total * 100); ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td style="padding-left:20px;">
                        合计：
                    </td>
                    <td><?php echo (int)$total; ?> 人</td>
                    <td></td>
                </tr>
            </table>
        </form>

    </div>
    <br style="clear:both;" />
</div>

<? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>