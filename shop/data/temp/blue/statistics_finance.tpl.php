<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="<?=CONF_PATH_IMG?>css/showpage.css" rel="stylesheet" type="text/css">

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>

<script type="text/javascript" src="./plugin/Highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="./plugin/Highcharts/js/modules/exporting.js"></script>

<link type="text/css" href="plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">

$(function(){
$("#begindate").datepicker();
$("#enddate").datepicker();
});

function show_stat_data()
{
document.MainForm.action = 'statistics.php?m=finance';
document.MainForm.target = '_self';
document.MainForm.submit();
}

function closewindowui()
{
$.unblockUI();
}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="javascript:;">款项统计</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   数据统计</div>
  <div class="news_info">
  <ul>
                <li><a href="statistics.php" ><span class="ali-small-circle iconfont icon-next-s"></span>订单统计</a>
<dd><a href="statistics.php?m=year" >年订单统计</a></dd>
<dd><a href="statistics.php?m=month" >  月订单统计</a></dd>
<dd><a href="statistics.php?m=day" > 日订单统计</a></dd>
</li>

                <li><a href="statistics.php?m=return" ><span class="ali-small-circle iconfont icon-next-s"></span>退货单统计</a>	</li>
<li><a href="statistics.php?m=finance" ><span class="ali-small-circle iconfont icon-next-s"></span>款项统计</a></li>
                <li><a href="reconciliation.php" ><span class="ali-small-circle iconfont icon-next-s"></span>往来对账</a>	</li>
<li><a href="statistics.php?m=product" ><span class="ali-small-circle iconfont icon-next-s"></span>订购商品统计</a></li>

  </ul>

  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">

<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   款项统计</div>
</div>

<div class="right_product_main">
<div class="list_line">

<form id="MainForm" name="MainForm" method="post" action="statistics.php"  >
             <table width="98%" align="center" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;<input name="begindate" style="border: 1px solid #ABADB3;height: 20px;"type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="   <?=$in['begindate']?>"   />&nbsp;到&nbsp;<input name="enddate" style="border: 1px solid #ABADB3;height: 20px;"type="text" id="enddate" maxlength="12" onfocus="this.select();" value="   <?=$in['enddate']?>"   />&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查 看 " class="button_6" onclick="show_stat_data()"/>&nbsp;&nbsp;</td>
     			 </tr>

 
<? if(!empty($statdata)) { ?>
 
<? if($statdata['w'] > 0) { ?>
     			 <tr>
       				 <td >
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
text: ''
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
name: 'Browser share',
data: [
<? if(is_array($finactpencent)) { foreach($finactpencent as $skey => $osvar) { if($skey=="1") { ?>
{
name: "<?=$osvar['n']?>",    
y: <?=$osvar['p']?>,
sliced: true,
selected: true
},
<? } else { ?>
['<?=$osvar['n']?>',   <?=$osvar['p']?>],
<? } } } ?>
]
}]
});
});	
</script>		
<div id="container" style="width: 700px; height: 400px; margin: 0 auto"></div>
</td>
     			 </tr>
<? } ?>
     			 <tr>
       				 <td >

<table width="100%" border="0" align="center" cellpadding="4" cellspacing="0">
  <tr>
    <td align="right">订单总金额：</td>
    <td align="right" style="width: 200px;height: 30px"><p style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$statdata['a']?> 元</p></td>
  </tr>
  <tr>
    <td align="right">&nbsp;退货金额：</td>
    <td align="right"><p style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$statdata['r']?> 元</p></td>
  </tr>
  <tr>
    <td align="right">&nbsp;应付款金额：</td>
    <td align="right" class="font12" style="height: 35px;"><p style="float: left;margin-top: 5px">&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$statdata['yin']?> 元</p></td>
  </tr>
  <tr>
    <td align="right">&nbsp;1、已确认到账：</td>
    <td align="right"><p style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$statdata['y']?> 元</p></td>
  </tr>
  <tr>
    <td align="right">&nbsp;2、未付款金额：</td>
    <td align="right"><p style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$statdata['w']?> 元</p></td>
  </tr>
  <tr>
    <td align="right">&nbsp; 3、在途付款金额：</td>
    <td align="right"><p style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$statdata['t']?> 元</p></td>
  </tr>
</table>

 </td>
     			 </tr>
     			 <tr>
       				 <td height="30" bgcolor="#ffffff" align="right" class="font12">注：&nbsp;未审核的订单没有计入款项统计!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
     			 </tr>
<? } else { ?>
     			 <tr>
       				 <td height="130" bgcolor="#ffffff" align="center">&nbsp; 暂无符合条件的数据!</td>
     			 </tr>
<? } ?>
              </table>
  </form>
<br />&nbsp;

</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
