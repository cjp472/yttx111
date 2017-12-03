<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="<?=CONF_PATH_IMG?>css/showpage.css" rel="stylesheet" type="text/css" />

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div class="main_left" style="margin-top: 28px">
<div class="fenlei_bg_tit"><span>付款单管理</span></div>
  <div class="news_info">
  <ul>
                <li><a href="finance.php" > &#8250;&#8250; 付款单查询</a>
<? if(is_array($finance_arr)) { foreach($finance_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="finance.php?status=<?=$skey?>" ><strong> &#8250;&#8250; <?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="finance.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>

                <li><a href="finance.php?m=new" > &#8250;&#8250; 新增付款单</a></li>

<li><a href="finance.php?m=expense" > &#8250;&#8250; 其他款项</a></li>
<li><a href="reconciliation.php" > &#8250;&#8250; 往来对账</a></li>
<? if($ispay=="pay") { ?>
<li> &#8250;&#8250; 在线支付</li>
<li><a href="finance.php?m=pay" target="_blank"><img src="template/img/alipay.jpg" border="0"/></a></li>
<? } ?>
  </ul>

  </div>
<div class="fenlei_bottom"><img src="template/blue/images/info_bottom.jpg" /></div>

</div>

<div class="main_right">
<div id="location6" style="margin-left: -250px;margin-bottom: 4px"><strong>您的当前位置： </strong><a href="home.php">首页</a> &#8250;&#8250; <a href="#">其他款项</a> </div>
<div class="right_product_tit">
<div class="xs_0">其他款项</div>
<span class="notic_b"></span>
</div>

<div class="right_product_main">
<div class="list_line">


<table width="99%" border="0" cellspacing="0" cellpadding="0" align="center" class="ordertd">
  <thead>
  <tr>
    <td width="10%" height="28">序号</td>
    <td width="32%">其他款项类型</td>
    <td width="24%">金额</td>
    <td>日期</td> 
  </tr>
   </thead>
   <tbody>
<? if(is_array($expense['list'])) { foreach($expense['list'] as $gkey => $gvar) { ?>
  <tr  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" id="line_<?=$gvar['FinanceID']?>">
    <td height="28">
<span class="numberbg"><?=$gvar['ExpenseID']?></span>	
</td>
    <td>
<span ><?=$gvar['BillName']?></span>
</td>
    <td>
<span class="font12">¥ <?=$gvar['ExpenseTotal']?></span>
</td>

    <td>
<span ><?=$gvar['ExpenseDate']?></span>
</td>
  </tr>
   
<? } } ?>
 
   </tbody>
</table>

<div class="list_showpage"><?=$expense['showpage']?></div><br />&nbsp;


</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>