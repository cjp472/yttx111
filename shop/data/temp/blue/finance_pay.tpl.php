<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />
<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>

<script type="text/javascript">
<!-- 
  //校验输入框 -->
function CheckForm()
{
if (document.alipayment.aliorder.value.length == 0) {
alert("请输入商品名称.");
document.alipayment.aliorder.focus();
return false;
}

var reg	= new RegExp(/^\d*\.?\d{0,2}$/);
if (! reg.test(document.alipayment.alimoney.value))
{
        alert("请正确输入付款金额");
document.alipayment.alimoney.focus();
return false;
}

}  
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   付款单管理</div>
  <div class="news_info">
  <ul>
                <li><a href="finance.php" ><span class="ali-small-circle iconfont icon-next-s"></span>付款单查询</a></li>
                <li><a href="finance.php?m=new" ><span class="ali-small-circle iconfont icon-next-s"></span>新增付款单</a></li>
<li><a href="reconciliation.php" ><span class="ali-small-circle iconfont icon-next-s"></span>往来对账</a></li>
  </ul>

  </div>
    <div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">
<div id="location"><strong>您的当前位置： </strong><a href="home.php">首页</a> &#8250;&#8250; <a href="finance.php">我的付款单</a> &#8250;&#8250; <a href="finance.php?m=new">新增付款单</a></div>
<div class="right_product_tit">
<div class="xs_0">支付宝在线支付</div>
</div>

<div class="right_product_main">
<div class="list_line">

<div class="line">
<? if($ispay=="pay") { ?>
<table width="98%" border="0" cellpadding="8" cellspacing="1" bgcolor="#FFFFFF" >

<FORM name="alipayment" onSubmit="return CheckForm();" action=
<? if($accinfo['AliPayType']=="sgl") { ?>
"./alipay_sgl/alipayapi.php"
<? } else { ?>
"./alipaydirect/alipayapi.php"
<? } ?>
 method="post" target="_blank" >

<INPUT type="hidden" name="aliorder" value="<?=$oinfo['OrderSN']?>" readonly="readonly" />		
<INPUT  name="paysn" id="paysn"  type="hidden" value="<?=$paysn?>" />
        <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right"><span class="test_1">*</span>订单号 ：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    &nbsp;<span class="font12">
<? if(empty($oinfo['OrderSN'])) { ?>
预付款
<? } else { ?>
<?=$oinfo['OrderSN']?>
<? } ?>
</span>
                  </label></td>
                  <td width="20%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right"><span class="test_1">* </span>订单金额：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="alimoney" id="alimoney"  maxlength="12" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" class="input1" style="width:150px;" value="<?=$oinfo['OrderTotal']?>" />&nbsp;元
                  </label> &nbsp</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right"><span class="test_1">*</span>收款账户：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2">&nbsp;&nbsp;	帐号：<strong><?=$accinfo['AccountsNO']?></strong><br />&nbsp;&nbsp;	名称：<strong><?=$accinfo['AccountsName']?></strong>	  </td>                  
                </tr>

                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="alibody" rows="4" id="alibody" class="input1"><?=$_SESSION['cc']['ccompanyname']?></textarea>
                  </label></td>
                  <td bgcolor="#FFFFFF">可注明转款原因，转款人等信息</td>
                </tr>

                <tr>
                  <td valign="top" bgcolor="#F0F0F0">&nbsp;</td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="submit" name="subfinance" id="subfinance" value="  下一步，支付  " class="button_4" />
                  </label>&nbsp;&nbsp;&nbsp;&nbsp;
  <label>
<input type="button" name="backfinance" id="backfinance" value="  返 回  " class="button_2" onclick="javascript:history.back(-1)" />
                  </label>				  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
</form>
               </table>
   <br />
<? } else { ?>
<br /><br />
<p align="center"><font color=red>支付宝集成信息有误，暂不能在线支付，请使用其他支付方式！</font></p>
<? } ?>
              </div>	
<br />&nbsp;
</div>

</div>
</div>
</div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
<? include template('bottom'); ?>
</body>
</html>
