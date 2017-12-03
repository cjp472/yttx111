<?php
$menu_flag = "system";
include_once ("header.php");
require_once("../alipay/alipay_config.php");
//$uinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");
$paysn = date("Ymd").microtime_float();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
<SCRIPT language=JavaScript>
<!-- 
  //校验输入框 -->
function CheckForm()
{
	if (document.alipayment.aliorder.value.length == 0) {
		alert("请输入商品名称.");
		document.alipayment.aliorder.focus();
		return false;
	}
	if (document.alipayment.alimoney.value.length < 2) {
		alert("请输入正确的付款金额.");
		document.alipayment.alimoney.focus();
		return false;
	}
	var reg	= new RegExp(/^\d*\.?\d{0,2}$/);
	if (! reg.test(document.alipayment.alimoney.value))
	{
        alert("请正确输入付款金额");
		document.alipayment.alimoney.focus();
		return false;
	}
	if (Number(document.alipayment.alimoney.value) < 100) {
		alert("您付款金额必需大于 100元.");
		document.alipayment.alimoney.focus();
		return false;
	}
}  

function set_vlaue(valmsg,namemsg)
{
	$("#alimoney").val(valmsg);
	$("#alibody").val(namemsg);
}
</SCRIPT>

</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">短信充值</a></div>
   	        </div>     
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong>财务管理</strong></div>
<ul>
	<li> <a href="pay_log.php">交费记录</a></li>
	<li> <a href="sys_pay.php" >系统续费</a></li>
	<li> <a href="sys_pay.php?pt=num">增加用户</a></li>
	<li> <a href="sms_pay.php" class="locationli">短信充值</a></li>
</ul>
<br style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">
			<div id="oldinfo" class="line">

			<fieldset  class="fieldsetstyle">		
			<legend>银行转帐汇款帐号：</legend>
            <table width="98%" border="0" cellpadding="8" cellspacing="1" bgcolor="#cccccc"  >                
                <thead>
				<tr>
                  <td width="8%" bgcolor="#F0F0F0" align="center">行号</td>
                  <td bgcolor="#F0F0F0" width="30%">开户行</td>
				  <td bgcolor="#F0F0F0" width="28%">帐号</td>
                  <td bgcolor="#F0F0F0" >开户名称（收款人）</td>
                </tr>
				</thead>
                <tr>
                  <td width="8%" bgcolor="#ffffff" align="center">1</td>
                  <td bgcolor="#ffffff" >招商银行成都龙腾东路支行</td>
				  <td bgcolor="#ffffff" >283580516210001</td>
                  <td bgcolor="#ffffff" >成都阿商信息技术有限公司</td>
                </tr>
                <tr>
                  <td width="8%" bgcolor="#ffffff" align="center">2</td>
                  <td bgcolor="#ffffff" >支付宝</td>
				  <td bgcolor="#ffffff" >dhbpay@rsung.com</td>
                  <td bgcolor="#ffffff" >成都阿商信息技术有限公司</td>
                </tr>
                <tr>
                  <td width="8%" bgcolor="#ffffff" align="center">3</td>
                  <td bgcolor="#ffffff" >建设银行成都翡翠城支行</td>
				  <td bgcolor="#ffffff" >6217 0038 1002 9023 440</td>
                  <td bgcolor="#ffffff" >侯晓晖  </td>
                </tr>
			</table>
			</fieldset>

			<br style="clear:both;" />
			<fieldset  class="fieldsetstyle">		
			<legend>支付宝手机支付 - 系统续费：</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  >                
                <tr>
                  <td  bgcolor="#FFFFFF" ><img src="./img/wifi0s04860459781393815632627.jpg" alt="用支付宝扫一扫 手机支付" title="用支付宝扫一扫 手机支付" width="150" /></td>
                </tr>
			</table>
			</fieldset>
              
			<br style="clear:both;" />
			<FORM name="alipayment" onSubmit="return CheckForm();" action="../alipaydirect/alipayapi.php" method="post" target="_blank">
			<INPUT  name="paytype" id="paytype"  type="hidden" value="sms"/>
			<INPUT  name="paysn" id="paysn"  type="hidden" value="<? echo $paysn; ?>"/>
			<fieldset  class="fieldsetstyle">		
			<legend>网上付款 - 短信充值</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  >                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">收款方：</div></td>
                  <td ><?php echo $mainname; ?></td>
                  <td width="35%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">标 题：</div></td>
                  <td><INPUT size=40 name="aliorder" maxlength="200" value="医统天下 短信充值" readonly="readonly" /></td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">充值数量：</div></td>
                  <td>
				<select name="_StorageAttn_default" onchange="set_vlaue(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text);">
				<option value="100">1000条 / 100元</option>
				<option value="200">2000条 / 200元 </option>
				<option value="300">3000条 / 300元 </option>
				<option value="400">4000条 / 400元 </option>
				<option value="500">5000条 / 500元 </option>
				<option value="600">6000条 / 600元 </option>
				<option value="800">8000条 / 800元 </option>
				<option value="1000">10000条 / 1000元 </option>
				<option value="1500">15000条 / 1500元 </option>
				<option value="2000">20000条 / 2000元 </option>
				</select>
				</td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">付款金额：</div></td>
                  <td bgcolor="#FFFFFF"><INPUT maxLength=10 size=40 name="alimoney" id="alimoney" onfocus="if(Number(this.value)==0){this.value='';}" value="100.00"/> 元</td>
                  <td bgcolor="#FFFFFF">最少充值 100 元，短信单价为 0.1元/条</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备注说明：</div></td>
                  <td bgcolor="#FFFFFF"><TEXTAREA name="alibody" id="alibody" rows=4 cols=40 wrap="physical"></TEXTAREA></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">支付方式：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2">              
				  <table class="noborder">
                 <tr>
                   <td ><input type="radio" name="pay_bank" value="directPay" checked ><img src="../alipay/images/alipay_1.gif" border="0"/></td>
                 </tr>
                 <tr>
                   <td><input type="radio" name="pay_bank" value="ICBCB2C"/><img src="../alipay/images/ICBC_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="CMB"/><img src="../alipay/images/CMB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="CCB"/><img src="../alipay/images/CCB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="BOCB2C"><img src="../alipay/images/BOC_OUT.gif" border="0"/></td>
                 </tr>
                 <tr>
                   <td><input type="radio" name="pay_bank" value="ABC"/><img src="../alipay/images/ABC_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="COMM"/><img src="../alipay/images/COMM_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="SPDB"/><img src="../alipay/images/SPDB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="GDB"><img src="../alipay/images/GDB_OUT.gif" border="0"/></td>
                 </tr>
                 <tr>
                   <td><input type="radio" name="pay_bank" value="CITIC"/><img src="../alipay/images/CITIC_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="CEBBANK"/><img src="../alipay/images/CEB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="CIB"/><img src="../alipay/images/CIB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="SDB"><img src="../alipay/images/SDB_OUT.gif" border="0"/></td>
                 </tr>
                 <tr>
                   <td><input type="radio" name="pay_bank" value="CMBC"/><img src="../alipay/images/CMBC_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="HZCBB2C"/><img src="../alipay/images/HZCB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="SHBANK"/><img src="../alipay/images/SHBANK_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="NBBANK "><img src="../alipay/images/NBBANK_OUT.gif" border="0"/></td>
                 </tr>
                 <tr>
                   <td><input type="radio" name="pay_bank" value="SPABANK"/><img src="../alipay/images/SPABANK_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="BJRCB"/><img src="../alipay/images/BJRCB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="ICBCBTB"/><img src="../alipay/images/ENV_ICBC_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="CCBBTB"/><img src="../alipay/images/ENV_CCB_OUT.gif" border="0"/></td>
                 </tr>
                 <tr>
                   <td><input type="radio" name="pay_bank" value="SPDBB2B"/><img src="../alipay/images/ENV_SPDB_OUT.gif" border="0"/></td>
                   <td><input type="radio" name="pay_bank" value="ABCBTB"/><img src="../alipay/images/ENV_ABC_OUT.gif" border="0"/></td>
				   <td><input type="radio" name="pay_bank" value="fdb101"/><img src="../alipay/images/FDB_OUT.gif" border="0"/></td>
				   <td><input type="radio" name="pay_bank" value="PSBC-DEBIT"/><img src="../alipay/images/PSBC_OUT.gif" border="0"/></td>
                 </tr>
               </table></td>

                </tr>
            </table>
           </fieldset> 
  
			<div class="line" align="right"><input type="submit" name="subinfo" id="subinfo" value=" 下一步，支付 " class="redbtn"  />&nbsp;&nbsp;<input type="button" name="subinfocancel" id="subinfocancel" value=" 返 回 " class="bluebtn" onclick="javascript:window.location.href='system.php'" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>

				<INPUT TYPE="hidden" name="referer" value ="" >
				</form>
				</div>

            </div>
        	</div>
              
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>