<?php 
$menu_flag = "system";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$info = $db->get_row("SELECT * FROM ".DATATABLE."_order_accounts  where AccountsCompany=".$_SESSION['uinfo']['ucompany']." and AccountsID=".intval($in['ID'])." limit 0,1");
}
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
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input name="AccountsID" type="hidden" id="AccountsID" value="<? echo $info['AccountsID'];?>"  />
		
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="accounts.php">收款账户</a>  &#8250;&#8250; <a href="#">修改账户</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_accounts();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >


       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong>系统设置</strong></div>
<ul>
	<li> <a href="system.php">公司信息</a></li>
	<li > <a href="producttype_set.php">模式设置</a></li>
	<li > <a href="productfield_set.php">商品字段设置</a></li>
	<li > <a href="templateset.php">模板风格设置</a></li>
	<li  > <a href="pointset.php">积分设置</a></li>
	<li> <a href="smsset.php">短信通知设置</a></li>
	<li> <a href="typeset.php">发货方式设置</a></li>
	<li> <a href="typeset.php#paylocation">收款方式设置</a></li>
	<li> <a href="client_area.php">地区设置</a></li>
	<li> <a href="client_level.php">药店级别设置</a></li>
	<li > <a href="accounts.php" class="locationli">收款账号设置</a></li>
</ul>
<br style="clear:both;" />
</div>
<!-- tree -->  
       	  </div>

		<div id="sortright">
            <fieldset title="“*” 为必填项"class="fieldsetstyle">
			<legend>收款账户信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">开户行：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                     <input type="text" name="data_AccountsBank" id="data_AccountsBank" value="<? echo $info['AccountsBank'];?>"  />
                 <span class="red"> *</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" >指开户所在银行（写具体支行）</td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">账 号 ：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_AccountsNO" id="data_AccountsNO"  value="<? echo $info['AccountsNO'];?>"   />
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">收款账号(卡号、存折号)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">开户名称(收款人)：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_AccountsName" id="data_AccountsName" value="<? echo $info['AccountsName'];?>"  />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">账户类型：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <select name="data_AccountsType" id="data_AccountsType">
                      <option value="公司账户" <? if($info['AccountsType'] == "公司账户") echo "selected"; ?> >公司账户</option>
                      <option value="个人账户" <? if($info['AccountsType'] == "个人账户") echo "selected"; ?> >个人账户</option>
                    </select>
                  </label>
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">支付类型：</div></td>
                  <td bgcolor="#FFFFFF">
				  <? if(!empty($info['PayType']) && $info['PayType']=="alipay"){ ?>
				    <label>
                    <input type="radio" name="data_PayType" id="data_PayType1" value="transfer"   style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;" onclick="selectpayclick();"  /><strong>&nbsp;客户转账</strong>
                  </label>
				  <label>
                    <input type="radio" name="data_PayType" id="data_PayType2" value="alipay" checked="checked"  style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;" onclick="selectpayclick2();"  /><strong>&nbsp;支付宝在线支付（请先与支付宝签约有手续费）</strong>
                  </label>
				  <? }else{ ?>
				    <label>
                    <input type="radio" name="data_PayType" id="data_PayType1" value="transfer" checked="checked"  style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;" onclick="selectpayclick();"  /><strong>&nbsp;客户转账</strong>
                  </label>
				  <label>
                    <input type="radio" name="data_PayType" id="data_PayType2" value="alipay"  style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;" onclick="selectpayclick2();"  /><strong>&nbsp;支付宝在线支付（请先与支付宝签约有手续费）</strong>
                  </label>
				  <? }?>
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
				
                <tr id="show_alipay_id" <? if(empty($info['PayType']) || $info['PayType']!="alipay") echo 'style="display:none;"';?>>
                  <td bgcolor="#F0F0F0"><div align="right">支付宝签约信息：</div></td>
                  <td bgcolor="#FFFFFF">
                  <div style="margin:4px 0;"><strong>是否开通移动支付: </strong><br /><span  ><input type="radio" name="data_AliPhone" id="data_AliPhone1"  <? if(empty($info['AliPhone']) || $info['AliPhone']=="F") echo 'checked="checked"';?> value="F"   style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;"   />&nbsp;未开通</span>&nbsp;&nbsp;<span >
					<input type="radio" name="data_AliPhone" id="data_AliPhone2" value="T"  <? if($info['AliPhone']=="T") echo 'checked="checked"';?>   style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;"  />&nbsp;开通（请先与支付宝开通移动支付服务）</span></div>
					<div style="margin:4px 0;"><strong>签约类型: </strong><br /><span  title="买家付款直接到账，帮助我快速回笼资金。" ><input type="radio" name="data_AliPayType" id="data_AliPayType1"  <? if(empty($info['AliPayType']) || $info['AliPayType']=="jsdc") echo 'checked="checked"';?> value="jsdc"   style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;"   />&nbsp;即时到账</span>&nbsp;&nbsp;<span  title="同时提供担保、即时两种支付方式供买家选择。" style="visibility:hidden;">
					<input type="radio" name="data_AliPayType" id="data_AliPayType2" value="sgl"  <? if($info['AliPayType']=="sgl") echo 'checked="checked"';?>   style="border:0; width:15px; height:15px; VERTICAL-ALIGN: middle;"  />&nbsp;双功能收款</span></div>
					<div style="margin:4px 0;"><strong>合作身份者(Partner ID):</strong><input type="text" name="data_PayPartnerID" id="data_PayPartnerID" maxlength="16" value="<? echo $info['PayPartnerID'];?>"  /></div>
					<div style="margin:4px 0;"><strong>开放平台ID(APP ID):</strong><input type="text" name="data_AppID" id="data_AppID" maxlength="16" value="<? echo $info['AppID'];?>"  /></div>
					<div style="margin:4px 0;"><strong>安全校验码(Key):</strong><input type="text" name="data_PayKey" id="data_PayKey" maxlength="32" value="<? echo $info['PayKey'];?>"  /></div>
					<div style="margin:4px 0;word-break:break-all"><font color=red>提示：如何获取安全校验码和合作身份者ID</font><br />
1.访问支付宝商户服务中心(<a href="http://b.alipay.com" target="_blank">b.alipay.com</a>)，用您的签约支付宝账号登陆.<br />
2.在“自助集成帮助”中，点击“合作者身份(Partner ID)查询”、“安全校验码(Key)查询”<br />
3.开通移动支付的用户，请在“我的商家服务”->“查询PID、Key”->“合作伙伴密钥管理”下，
点击“RSA加密”后的“添加密钥”，把下面这串密钥复制到弹出框内（请不要复制其它密钥！）&nbsp;<a href="http://www.dhb.hk/dhbpay/file/DHB-ALIPAY.pdf" target="_blank">操作指南</a><br />
<div style="border:1px #dbdbdb dashed;padding: 4px 8px;">
 MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC/MpIwrzVBOaAWBWo827NM6q+1vJuJWm2SBWzGGYW3vPHfY+lCTx/W8a+mM2jnJ5nD8NSNFOwXs0yP1MyhJ3xMJkiucFieZa9Uu1dmHWKhpbOaz3GPWfzOA3VaUzXuTpyrsGr7ij9P/XEJr8i3nh1cO9Yhiuz7iub6zFqkwMNJvQIDAQAB
 </div>
 </div>

                  </td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>


               </table>
		  </fieldset>

			<br style="clear:both;" />
          <div class="rightdiv sublink" style="padding-right:20px;">		  
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_accounts();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />		  
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
		</div>
		<br style="clear:both;" />
		</div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>