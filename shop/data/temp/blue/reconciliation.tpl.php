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

<link type="text/css" href="plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">

$(function(){
$("#begindate").datepicker();
$("#enddate").datepicker();
});
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="statistics.php">数据统计</a> / <a href="reconciliation.php">往来对账</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   数据统计</div>
  <div class="news_info">
  <ul>
                <li><a href="statistics.php" ><span class="ali-small-circle iconfont icon-next-s"></span>订单统计</a>
<dd><a href="statistics.php?m=year" > 年订单统计</a></dd>
<dd><a href="statistics.php?m=month" > 月订单统计</a></dd>
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
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   往来对账</div>
</div>

<div class="right_product_main">
<div class="list_line">
<form name="changetypeform" id="changetypeform" action="reconciliation.php" method="post">
             <table width="98%" align="center" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;<input name="begindate" type="text" style="border: 1px solid #ABADB3;height: 20px;" id="begindate"   maxlength="12" onfocus="this.select();" value="   <?=$in['begindate']?>"   />&nbsp;到&nbsp;<input name="enddate" style="border: 1px solid #ABADB3;height: 20px;" type="text" id="enddate" maxlength="12" onfocus="this.select();" value="   <?=$in['enddate']?>"   />&nbsp;<input type="submit" name="newbutton" id="newbutton" value=" 查 看 " class="button_6" /></td>
     			 </tr>
 </table>
 </form>
<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" class="ordertd">
                <thead>
<tr>
  <td height="28" width="5%" class="bottomlinebold">行号</td>
                  <td width="12%" class="bottomlinebold">日期</td>
                  <td width="18%" class="bottomlinebold">编号</td>
  <td class="bottomlinebold">科目名称</td>
                  <td width="14%" class="bottomlinebold" align="right">应付款项&nbsp;</td>
  <td width="14%" class="bottomlinebold" align="right">已付款项&nbsp;</td>
  <td width="16%" class="bottomlinebold" align="right">期末应付(元)&nbsp;</td>
</tr>
</thead>
<tbody>
<tr onmouseover="inStyle(this)"  onmouseout="outStyle(this)" id="line_0">
                  <td height="28">1</td>
                  <td ><?=$in['begindate']?></td>
  <td >&nbsp;</td>
                  <td ><strong>期初应付</strong></td>
  <td align="right"> &nbsp;</td>
  <td align="right"> &nbsp;</td>
                  <td align="right"> <? echo sprintf("%01.2f", round($btotal,2)); ?>&nbsp;</td>
</tr>
<? if(is_array($redata)) { foreach($redata as $gkey => $var) { if(!empty($var['Total'])) { ?>
<tr onmouseover="inStyle(this)"  onmouseout="outStyle(this)" id="line_<? echo $n++; ?>">
                  <td height="28"><? echo $n; ?></td>
                  <td ><?=$var['Date']?></td>
  <td ><a href="<?=$var['LinkUrl']?>" target="blank"><?=$var['SN']?></a>&nbsp;</td>
                  <td ><?=$var['atype']?></td>
  <td align="right"> 
<? if($var['TotalType']=="+") { ?>
<?=$var['Total']?>
<? } ?>
&nbsp;</td>
  <td align="right"> 
<? if($var['TotalType']=="-") { ?>
<?=$var['Total']?>
<? } ?>
&nbsp;</td>
                  <td align="right"> <? echo sprintf("%01.2f", round($var['linetotal'],2)); ?>&nbsp;</td>
</tr>
<? } } } ?>
 
                <tr id="line_0" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td >&nbsp;</td>
                  <td >&nbsp; <strong>合计：</strong></td>
  <td >&nbsp;</td>
                  <td >&nbsp;</td>
  <td align="right"> <strong><? echo sprintf("%01.2f", round($tjia,2)); ?>&nbsp;</strong></td>
  <td align="right"> <strong><? echo sprintf("%01.2f", round($tjian,2)); ?>&nbsp;</strong></td>
                  <td align="right"> <strong><? echo sprintf("%01.2f", round($tall,2)); ?>&nbsp;</strong></td>
                </tr>
</tbody>
</table>
<div class="list_showpage">&nbsp;</div><br />&nbsp;
</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
