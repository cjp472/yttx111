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
$("#cordate").datepicker();
});

function show_stat_data()
{
document.MainForm.action = 'statistics.php?m=day';
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
<div id="location">当前位置：<a href="home.php">首页</a> / <a href="statistics.php">订单统计</a> / <a href="javascript:;">日订单统计</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   数据统计</div>
  <div class="news_info">
  <ul>
                <li><a href="statistics.php" ><span class="ali-small-circle iconfont icon-next-s"></span>订单统计</a>
<dd><a href="statistics.php?m=year" >年订单统计</a></dd>
<dd><a href="statistics.php?m=month" >月订单统计</a></dd>
<dd><a href="statistics.php?m=day" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span>日订单统计</strong></a></dd>
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
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   订单统计</div>
</div>

<div class="right_product_main">
<div class="list_line">

<form id="MainForm" name="MainForm" method="post" action="statistics.php"  >
             <table width="98%" align="center" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;<strong>日期： </strong>&nbsp;<input name="cordate" style="border: 1px solid #ABADB3;height: 20px;"type="text" id="cordate"   maxlength="12" onfocus="this.select();" value="&nbsp;&nbsp;<?=$in['cordate']?>"   />&nbsp;
<input type="button" name="newbutton" id="newbutton" value=" 查看 " class="button_6" onclick="show_stat_data();"/>&nbsp;&nbsp;</td>
     			 </tr>

 
<? if(!empty($statdata)) { ?>
     			 <tr>
       				 <td >
<script type="text/javascript">

var chart;
$(document).ready(function() {
chart = new Highcharts.Chart({
chart: {
renderTo: 'container',
defaultSeriesType: 'column',
margin: [ 50, 50, 100, 80]
},
title: {
text: '<?=$in["cordate"]?>  订单数据'
},
xAxis: {
categories: [
<?=$snmsg?>
],
labels: {
rotation: -45,
align: 'right',
style: {
 font: 'normal 13px Verdana, sans-serif'
}
}
},
yAxis: {
min: 0,
title: {
text: '订单金额 (元)'
}
},
legend: {
enabled: false
},
tooltip: {
formatter: function() {
return '<b>'+ this.x +'</b><br/>'+
 '订单金额: '+ Highcharts.numberFormat(this.y, 1) +
 ' 元';
}
},
        series: [{
name: 'Population',
data: [<?=$tmsg?>],
dataLabels: {
enabled: true,
rotation: -90,
color: '#FFFFFF',
align: 'right',
x: -3,
y: 10,
formatter: function() {
return this.y;
},
style: {
font: 'normal 13px Verdana, sans-serif'
}
}			
}]
});
});
</script>
 <div id="container" style="width: 700px; height: 400px; margin: 0 auto"></div>
</td>
     			 </tr>

     			 <tr>
       				 <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong><?=$in['cordate']?> 订单数据</strong></td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="2%" class="bottomlinebold"><label> &nbsp;</label></td>
                  <td width="20%" class="bottomlinebold">订单号</td>
                  <td width="18%" class="bottomlinebold">订单金额</td>
  <td width="20%" class="bottomlinebold">下单时间</td>
  <td width="10%" class="bottomlinebold">状态</td>
                </tr>
     		 </thead>
 
 <tbody>
 
<? if(is_array($statdata)) { foreach($statdata as $skey => $var) { ?>
 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
  <td ><a href="myorder.php?m=showorder&id=<?=$var['OrderID']?>" target="_blank"><?=$var['OrderSN']?></a></td>
                  <td >¥ <?=$var['OrderTotal']?></td>
                  <td ><? echo date("Y-m-d H:i",$var['OrderDate']); ?></td>
  <td ><? echo $order_status_arr[$var['OrderStatus']]; ?></td>
 </tr>
 
<? } } ?>
 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
  <td >&nbsp;</td>
  <td ><strong>合计：</strong><strong>&nbsp;<? echo count($statdata); ?>个</strong></td>
                  <td ><strong>¥ <?=$totalm?> 元</strong></td>
                  <td >&nbsp;</td>
  <td >&nbsp;</td>
 </tr>
 </tbody>
</table>

 </td>
     			 </tr>
<? } else { ?>
     			 <tr>
       				 <td bgcolor="#ffffff" align="center" style="border:none;text-align:center;height:40px;line-height:40px">&nbsp; 暂无符合条件的数据!</td>
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
