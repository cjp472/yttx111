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

<script language="javascript" type="text/javascript">
function del_finance(kid){
if(confirm("您确定要删除该转账记录吗?"))
{
$.growlUI('正在执行，请稍候...');
$.post("finance.php",
{m:"delfinance", kid: kid},
function(data){
if(data == "ok"){
$.growlUI('删除成功！');		
window.location = 'finance.php';
}else{
$.growlUI(data);
}
}			
);
}
}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div class="main_left" style="margin-top: 33px">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   付款单管理</div>
  <div class="news_info">
  <ul>
                <li><a href="finance.php" ><span class="ali-small-circle iconfont icon-next-s"></span>付款单查询</a>
<? if(is_array($finance_arr)) { foreach($finance_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="finance.php?status=<?=$skey?>" ><strong> &#8250;&#8250; <?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="finance.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>
                <li><a href="finance.php?m=new" ><span class="ali-small-circle iconfont icon-next-s"></span>新增付款单</a>	</li>
<li><a href="reconciliation.php" ><span class="ali-small-circle iconfont icon-next-s"></span>往来对账</a></li>
  </ul>

  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">
<div id="location" style="margin-left: -250px">当前位置： <a href="home.php">首页</a> / <a href="finance.php">我的付款单</a> 
<? if(($in['status']!="")) { ?>
/ <a href="finance.php?status=<?=$in['status']?>"><?=$incept_arr[$in['status']]?> - 付款单</a>
<? } ?>
</div>
<div class="right_product_tit">
<div class="xs_0">我的付款单</div>
</div>

<div class="right_product_main">
<div class="list_line">


<div class="line">
<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
<form id="formorder" name="formorder" method="post" action="" >
<input type="hidden" name="set_filename" id="set_filename" value="" />
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">付款单状态 ：</div></td>
                  <td bgcolor="#FFFFFF">				  
<strong>[<?=$finance_arr[$finance['content']['FinanceFlag']]?>]</strong>
                  </td>
                  </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">付款订单 ：</div></td>
                  <td  bgcolor="#FFFFFF">
  
<? if(empty($finance['orderarr'])) { ?>
预付款
  
<? } else { if(is_array($finance['orderarr'])) { foreach($finance['orderarr'] as $okey => $ovar) { ?>
- <a href="myorder.php?m=showorder&sn=<?=$ovar['OrderSN']?>" target="_blank"><?=$ovar['OrderSN']?>&nbsp;&nbsp;(¥ <?=$ovar['OrderTotal']?>)</a><br />
<? } } } ?>
                  </td>
                  </tr>

<tr>
                  <td width="16%" bgcolor="#F0F0F0" valign="top"><div align="right">日期 ：</div></td>
                  <td bgcolor="#FFFFFF">
  <label>转账日期：<?=$finance['content']['FinanceToDate']?></label><br />
  <label>到账日期：
<? if(!empty($finance['content']['FinanceUpDate'])) { echo date("Y-m-d",$finance['content']['FinanceUpDate']); } ?>
</label><br />
  <label>填写日期：<? echo date("Y-m-d H:i",$finance['content']['FinanceDate']); ?></label><br />
  </td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收款账户：</div></td>
                  <td bgcolor="#FFFFFF">
  
<? if($finance['content']['FinanceType']=="Y") { ?>
   <strong>余额支付</strong>
  
<? } elseif($finance['content']['FinanceFrom']=="allinpay") { ?>
<strong>网银支付</strong>
  
<? } elseif($finance['content']['FinanceFrom']=="yijifu") { ?>
<strong>快捷支付</strong>
  
<? } else { ?>
<strong>开户行：</strong><?=$finance['accounts']['AccountsBank']?><br />
<strong>收款人：</strong><?=$finance['accounts']['AccountsName']?><br />
<strong>帐&nbsp;&nbsp; 号：</strong><?=$finance['accounts']['AccountsNO']?>
  
<? } ?>
                </td>

                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">支付金额：</div></td>
                  <td bgcolor="#FFFFFF">
<label class="font12">¥ <?=$finance['content']['FinanceTotal']?> &nbsp;元</label>
  </td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">支付凭证：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    
<? if(!empty($finance['content']['FinancePicture'])) { ?>
<a href="<?=RESOURCE_PATH?><?=$finance['content']['FinancePicture']?>" target="_blank"><img src="<?=RESOURCE_PATH?><?=$finance['content']['FinancePicture']?>" border="0" onload="javascript:if(this.width>400)this.style.width=400;"  ></a>
<? } ?>
                  </label> </td>
                </tr>

                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                   <? echo nl2br($finance['content']['FinanceAbout']); ?>                  </label></td>
                </tr>
<? if($finance['content']['FinanceFrom']=="alipay") { ?>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">支付宝支付信息：</div></td>
                  <td bgcolor="#FFFFFF">
<strong>支付宝交易号：</strong><?=$finance['pay']['PayTradeNO']?><br />
<strong>支付账号：</strong><?=$finance['pay']['PayBuyer']?><br />
<strong>交易状态：</strong><font color="red">
<? if($finance['pay']['PayStatus']=="TRADE_SUCCESS" || $finance['pay']['PayStatus']=="TRADE_FINISHED") { ?>
支付成功
<? } else { ?>
交易未完成
<? } ?>
</font><br />
<strong>支付时间：</strong><? echo date("Y-m-d H:i:s",$finance['pay']['PayDate']); ?><br />
  </td>
                </tr>
<? } if($finance['content']['FinanceFrom']=="allinpay") { ?>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">网银支付信息：</div></td>
                  <td bgcolor="#FFFFFF">
<strong>交易号：</strong><?=$finance['pay']['OrderNO']?><br />
<strong>交易状态：</strong><font color="red">
<? if($finance['pay']['PayResult']=="1" ) { ?>
支付成功
<? } else { ?>
交易未完成
<? } ?>
</font><br />
<strong>支付时间：</strong><?=$finance['pay']['PayDateTime']?><br />
  </td>
                </tr>
<? } if($finance['content']['FinanceFrom']=="yijifu") { ?>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">快捷支付信息：</div></td>
                  <td bgcolor="#FFFFFF">
<strong>交易号：</strong><?=$finance['pay']['OrderNO']?><br />
<strong>交易状态：</strong><font color="red">
<? if($finance['pay']['PayResult']=="1" ) { ?>
支付成功
<? } else { ?>
交易未完成
<? } ?>
</font><br />
<strong>支付时间：</strong><?=$finance['pay']['PayDateTime']?><br />
  </td>
                </tr>
<? } ?>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0">&nbsp;</td>
                  <td bgcolor="#FFFFFF">
  
  
<? if(empty($finance['content']['FinanceFlag'])) { ?>
<input type="button" name="delfinance" id="delfinance" value=" 删除 " class="button_7" onclick="del_finance('<?=$finance['content']['FinanceID']?>');" />
&nbsp;&nbsp;&nbsp;&nbsp;
  
<? } ?>
                    <input type="button" name="resetfinance" id="resetfinance" value=" 返回 " class="button_6" onclick="javascript:history.back(-1)" />
                  </td>
                  <!--<td bgcolor="#FFFFFF">&nbsp;</td>-->
                </tr>
</form>
               </table>
              </div>	
<br />&nbsp;


</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
