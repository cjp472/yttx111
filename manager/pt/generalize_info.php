<?php
$menu_flag = "open";
include_once ("header.php");
include_once ("../class/ip2location.class.php");
$erp_version = include_once("inc/erp_version.php");

setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name='robots' content='noindex,nofollow' />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>

    <div id="searchline">
        <div class="leftdiv">
            <?php
            if(!isset($in['startTime']) && !isset($in['endTime'])){
                $in['startTime'] = date('Y-m-01',time());
                $in['endTime'] = date('Y-m-t',time());
            }
            ?>
            <form id="FormSearch" name="FormSearch" method="get" action="generalize_info.php">
                <label>&nbsp;&nbsp;统计时间：
                    <input type="text" name="startTime" id="startTime" class="inputline" style="width:80px;" value="<? if(!empty($in['startTime'])) echo $in['startTime'];?>" /> -
                </label>
                <label>&nbsp;&nbsp;
                    <input type="text" name="endTime" id="endTime" class="inputline" style="width:80px;" value="<? if(!empty($in['endTime'])) echo $in['endTime'];?>" />
                </label>
                <label>
                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
                </label>
            </form>
        </div>
    </div>

    <div class="line2"></div>
        <div class="bline" >
            <br style="clear:both;" />
                <?php
                $gsql = "select generalizeName,count(*) as num from ".DATABASEU.DATATABLE."_order_generalize where generalizeType <> 'seller' group by generalizeName";
                $ginfo = $db->get_results($gsql);
                $totalorder = 0;
                foreach($ginfo as $gvar)
                {
                    $totalorder = $totalorder + $gvar['num'];
                    $generalizeNumber[$gvar['generalizeName']] = $gvar['num'];
                }
                foreach($ginfo as $gvar)
                {
                    $generalizePencent[$gvar['generalizeName']] = round($generalizeNumber[$gvar['generalizeName']]/$totalorder*100,2);
                }
                ?>
            <fieldset class="fieldsetstyle">
                <?php
                $gNameSql = "select * from ".DATABASEU.DATATABLE."_order_generalize where generalizeType <> 'seller' order by generalizeName";
                $gNameList = $db->get_results($gNameSql);
                $nameIdsArr=array();
                foreach($gNameList as $v){
                    $nameIdsArr[$v['generalizeType']][]  = $v['generalizeNo'];
                }
                $nameIdsStr = '';
                foreach($nameIdsArr as $na=>$id){
                    $idStr = implode(',',$id);
                    $nameIdsStr = $nameIdsStr.$na.'='.$idStr.';';
                }
                ?>
                <legend>推广类型统计</legend>
                <? if(!empty($ginfo)){?>
                    <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                        <tr>
                            <td>

                                <script type="text/javascript">
                                    var chart1;
                                    $(document).ready(function() {
                                        chart1 = new Highcharts.Chart({
                                            chart: {
                                                renderTo: 'container_total',
                                                plotBackgroundColor: null,
                                                plotBorderWidth: null,
                                                plotShadow: false
                                            },
                                            title: {
                                                text: '推广方式使用统计'
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
                                                            return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
                                                        }
                                                    }
                                                }
                                            },
                                            series: [{
                                                type: 'pie',
                                                colorByPoint: true,
                                                name: 'generalize log',
                                                data: [
                                                    <?php
                                                    for($i = 0; $i<count($ginfo);$i++){
                                                    echo '10,';
                                                    }
                                                    ?>
                                                ]
                                            }]
                                        });
                                    });
                                </script>
                                <div id="container_total" style="width: 700px; height: 400px; margin: 0 auto"></div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <table width="96%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF" align="center">
                                    <tr>
                                        <td class="bold ajax-bold" height="25" >
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                <? }?>
            </fieldset>



            <br style="clear:both;" />
        </div>
    <br style="clear:both;" />
</div>



</div>

<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>

</body>
</html>
<script src="js/function.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){

        $("#startTime").datepicker({changeMonth: true,	changeYear: true});
        $("#endTime").datepicker({changeMonth: true,	changeYear: true});
        var str = "<?php echo $nameIdsStr;?>";
        $.post(
            "do_generalize.php",
            {
                m:"generalize_select_generalize", //请求方法
                url:"<?php echo DHB_HK_URL;?>/?f=generalizeNum", //请求第三方数据源地址
                p:Math.random(),
                s:str,
                startTime:"<?php echo $in['startTime'];?>",
                endTime:"<?php echo $in['endTime'];?>"
            },
            function(result){
                $.ajaxSettings.async = false;
                var data1 = [];
                if (result['p'].length == 0)
                {
                    $("#container_total").html("<div style='text-align:center;margin-top: 100px;'>暂无数据</div>");
                } else{
                    for(var key in result['p']){
                        if(result['p'].hasOwnProperty(key)) {
                            data1.push([key, result['p'][key]]);
                        }
                    };
                    chart1.series[0].setData(data1);
                }
                var ntext = '';
                var i = 1;
                if(result['n'] != 0){
                    for(var j in result['n']) {
                        ntext += i+'、'+j+'('+result['n'][j]+')'+"&nbsp;&nbsp;&nbsp;-";
                        i++;
                    }
                }
                ntext = trim(ntext, '-');
                $(".ajax-bold").html(ntext);
            },'json');
    });
</script>
