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
document.MainForm.action = 'statistics.php?m=return';
document.MainForm.target = '_self';
document.MainForm.submit();
}

function closewindowui()
{
$.unblockUI();
}

function show_order_list(showtype,did)
{
$('#windowContentList').html('数据载入中... ');
$.blockUI({ 
message: $('#windowForm'),
css:{ 
                width: '540px',height:'350px',top:'8%'
            }			
});

$.post("statistics.php",
{m:"show_list", stype: showtype, did: did},
function(data){
$('#windowContentList').html(data);				
}		
);

$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置：<a href="home.php">首页</a> / <a href="statistics.php?m=return">退货单统计</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   数据统计</div>
  <div class="news_info">
  <ul>
                <li><a href="statistics.php" ><span class="ali-small-circle iconfont icon-next-s"></span>订单统计</a>
<dd><a href="statistics.php?m=year" >年订单统计</a></dd>
<dd><a href="statistics.php?m=month" >月订单统计</a></dd>
<dd><a href="statistics.php?m=day" >日订单统计</a></dd>
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
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   退货单统计</div>
</div>

<div class="right_product_main">
<div class="list_line">

<form id="MainForm" name="MainForm" method="post" action="statistics.php"  >
             <table width="98%" align="center" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;<input name="begindate"style="border: 1px solid #ABADB3;height: 20px;" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="   <?=$in['begindate']?>"   />&nbsp;到&nbsp;<input name="enddate" style="border: 1px solid #ABADB3;height: 20px;"type="text" id="enddate" maxlength="12" onfocus="this.select();" value="   <?=$in['enddate']?>"   />&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查 看 " class="button_6" onclick="show_stat_data()"/>&nbsp;&nbsp;</td>
     			 </tr>
 
<? if(!empty($rmsg)) { ?>
<?=$rmsg?>
<? } else { ?>
 
<? if(!empty($statdata)) { ?>
     			 <tr>
       				 <td >

<script type="text/javascript">

var chart;
$(document).ready(function() {
chart = new Highcharts.Chart({
chart: {
renderTo: 'container',
zoomType: 'xy'
},
title: {
text: '从 <?=$in['begindate']?> 到 <?=$in['enddate']?> 退单数据'
},
subtitle: {
text: ''
},
xAxis: [{
categories: [<?=$snmsg?>]
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
text: '退单数',
style: {
color: '#89A54E'
}
}
}, { // Secondary yAxis
title: {
text: '退单金额',
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
(this.series.name == '退单金额' ? '元 ' : '个');
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
name: '退单金额',
color: '#4572A7',
type: 'spline',
yAxis: 1,
data: [<?=$tmsg?>]
}, {
name: '退单数',
color: '#89A54E',
type: 'column',
data: [<?=$nmsg?>]
}]
});		
});				
</script>
 <div id="container" style="width: 700px; height: 400px; margin: 0 auto"></div>

</td>
     			 </tr>

     			 <tr>
       				 <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong>从 <?=$in['begindate']?> 到 <?=$in['enddate']?> 订单数据</strong></td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="2%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="30%" class="bottomlinebold">日期</td>
  <td width="30%" class="bottomlinebold">退货单金额</td>
                  <td  class="bottomlinebold">退货单个数</td>
                </tr>
     		 </thead>
 
 <tbody>
<? if(is_array($statdata)) { foreach($statdata as $skey => $var) { ?>
 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
  <td ><a href="javascript:void(0);" onclick="show_order_list('rday','<?=$var['ODate']?>')"><?=$var['ODate']?></a></td>
  <td >¥ <?=$var['OTotal']?></td>
                  <td ><?=$var['totalnumber']?></td>
 </tr>
 
<? } } ?>
 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
  <td ><strong>合计：</strong></td>
  <td ><strong>¥ <?=$totalm?> 元</strong></td>
                  <td ><strong> <?=$totaln?> 个</strong></td>
 </tr>
 </tbody>
</table>
 </td>
     			 </tr>
<? } else { ?>
     			 <tr>
       				 <td height="130" bgcolor="#ffffff" align="center">&nbsp; 暂无符合条件的数据!</td>
     			 </tr>
<? } } ?>
              </table>
  </form>
<br />&nbsp;

</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
    <div id="windowForm">
<div class="windowHeader">
<h3 id="windowtitle">退货单列表：</h3>
<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
</div>
<div id="windowContentList" >
        数据载入中...       
        </div>
</div>
</body>
</html>
