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
function deladdress(kid){
if(confirm("您确定要删除该地址吗?"))
{

$.post("consignment.php",
{m:"deladdress", kid: kid},
function(data){
data = $.trim(data);
if(data == "ok"){
$.growlUI('删除成功！');
$("#line_"+kid).animate({opacity: 'hide'}, 'slow');	
}else{
$.growlUI(data);
}
}			
);
}
}

function setdefault(kid){
if(confirm("您确定要设置此地址为默认收货地址吗?"))
{
$.post("consignment.php",
{m:"setaddress", kid: kid},
function(data){
if(data == "ok"){
$.growlUI('设置成功！');
window.location.reload();
}else{
$.growlUI(data);
}
}			
);
}
}

function addressadd()
{
if($("#data_AddressContact").val()=="" || $("#data_AddressPhone").val()=="" || $("#data_AddressAddress").val()=="")
{
$.growlUI('联系人 / 联系电话 / 详细地址 不能为空!');
}else{
$.post("consignment.php?m=saveaddress",$("#formorder").serialize(),
function(data){		
if(data.status == "ok"){					
$.growlUI('提交成功，正在载入页面...');
var jumpurl = 'consignment.php?m=address';
document.location = jumpurl;
}else{
$.growlUI(data.msg);
}
}, 'json'			
);
}
}

function set_edit_value(AddressID,AddressCompany,AddressContact,AddressPhone,AddressAddress)
{
if(AddressID != "")
{
$('#settitle').html('修改地址');
$('#data_AddressID').val(AddressID);
$('#data_AddressCompany').val(AddressCompany);
$('#data_AddressContact').val(AddressContact);
$('#data_AddressPhone').val(AddressPhone);
$('#data_AddressAddress').val(AddressAddress);
}		 
}

function set_title_value()
{
$('#settitle').html('新增地址');
$('#data_AddressID').val("");
$('#data_AddressCompany').val("");
$('#data_AddressContact').val("");
$('#data_AddressPhone').val("");
$('#data_AddressAddress').val("");
}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置：<a href="home.php">首页</a> / <a href="consignment.php">我的发货单</a> / <a href="consignment.php?m=address">常用收货地址</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   发货单管理</div>
  <div class="news_info">
  <ul>
                <li><a href="consignment.php" ><span class="ali-small-circle iconfont icon-next-s"></span>发货单查询</a>
<? if(is_array($incept_arr)) { foreach($incept_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="consignment.php?status=<?=$skey?>" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span><?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="consignment.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>

                <li><a href="consignment.php?m=address" ><span class="ali-small-circle iconfont icon-next-s"></span>收货地址管理</a>	</li>
<li><a href="consignment.php?m=address#editname" ><span class="ali-small-circle iconfont icon-next-s"></span>新增地址</a></li>
  </ul>

  </div>
<div class="fenlei_bottom"><img src="<?=CONF_PATH_IMG?>images/info_bottom.jpg" /></div>

</div>

<div class="main_right">

<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   常用收货地址</div>
</div>

<div class="right_product_main">
<div class="list_line">

<div class="line">
   <table width="96%" border="0" cellspacing="0" cellpadding="0" align="center" class="ordertd">
  <thead>
  <tr>
    <td width="14%" height="28">ID</td>
    <td>单位</td>
    <td width="24%">联系方式</td>
    <td width="32%">详细地址</td> 
    <td width="8%">操作</td>
  </tr>
   </thead>
   <tbody>
<? if(is_array($addresslist['list'])) { foreach($addresslist['list'] as $gkey => $gvar) { ?>
  <tr  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" id="line_<?=$gvar['AddressID']?>">
    <td height="48">
<span class="numberbg"><? echo $gkey+1; ?></span><br />
<? if(empty($gvar['AddressFlag'])) { ?>
<span ><a href="javascript:void(0)" title="设为默认地址" onClick="setdefault(<?=$gvar['AddressID']?>);">&#8250; 设为默认值</a></span><br />
<? } else { ?>
<span class="title_green_w" title="默认收货地址" >√</span>
<? } ?>
</td>
    <td>
<span ><?=$gvar['AddressCompany']?></span>
</td>
    <td>
<span title="联系人"><?=$gvar['AddressContact']?></span><br />
<span title="联系电话"><?=$gvar['AddressPhone']?></span>
</td>
    <td>
<span ><?=$gvar['AddressAddress']?></span>
</td>
    <td>
<span ><a href="javascript:void(0)" onclick="deladdress(<?=$gvar['AddressID']?>);">&#8250; 删除</a></span><br />
<span ><a href="#editname" onclick="set_edit_value('<?=$gvar['AddressID']?>','<?=$gvar['AddressCompany']?>','<?=$gvar['AddressContact']?>','<?=$gvar['AddressPhone']?>','<?=$gvar['AddressAddress']?>')">&#8250; 修改</a></span><br />

</td>
  </tr>
   
<? } } ?>
 
   </tbody>
</table>
</div>
<br class="clear" /><a name="#editname"></a>
<div class="border_line" >
<div class="line2 font12h" id="settitle">新增地址：</div>
<div class="line2">
<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
             <form id="formorder" name="formorder" method="post" action="" >
<input type="hidden" name="data_AddressID" id="data_AddressID" value="" />
                <tr>
                  <td width="20%" bgcolor="#F0F0F0"><div align="right">单位/公司名称 ：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_AddressCompany" id="data_AddressCompany" value="" class="input1" />
                  </label></td>
                  <td width="15%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联 系 人：</div></td>
                  <td bgcolor="#FFFFFF">
<input type="text" name="data_AddressContact" id="data_AddressContact" value="" class="input1" />
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;<span class="test_1">*</span></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_AddressPhone" id="data_AddressPhone" value="" class="input1" />
                  </label>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;<span class="test_1">*</span></td>
                </tr>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_AddressAddress" id="data_AddressAddress" value="" class="input1" />
                  </label></td>
                  <td bgcolor="#FFFFFF">&nbsp;<span class="test_1">*</span></td>
                </tr>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0">&nbsp;</td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="button" name="subfinance" id="subfinance" value="  保 存  " class="button_3" onclick="addressadd();" />
                  </label>&nbsp;&nbsp;&nbsp;&nbsp;
  <label>
                    <input type="reset" name="resetfinance" id="resetfinance" value=" 重 置 " class="button_2" />
                  </label>
  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
</form>
               </table>
</div>
</div>
<br />&nbsp;

</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
