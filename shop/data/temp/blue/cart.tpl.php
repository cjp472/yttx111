<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="<?=CONF_PATH_IMG?>css/showpage.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<style  type="text/css">
.main_car{
width:1000px;
}
a{
    text-decoration:none;
}

</style>
</head>

<body>
<? include template('header'); ?>
<div id="main">

<div id="location" style="float:left;width:250px;">当前位置： <a href="home.php">首页</a> / <a href="cart.php">我的购物车 </a></div>

<!-- 
<div style="width:400px;float:right;">
<form id="excel_forms"  method="POST" style="width:380px;">
导入excel:<input name="excel_data" type="file" id="file" />
<input type="button"  onclick="do_excel();" style="background:green;width:50px;height:25px;color:yellow;" value="提交"/>
</form>
</div>
 -->
 
<div style="clear:both;"></div>

<script type="text/javascript">
function do_excel(){

if($("#file").val() == ""){
alert("请上传要导入的excel！");
return false;
}
//var formdata=new FormData($("#excel_forms").[0]);//获取文件法一
var formdata=new FormData( ); 
formdata.append("file" , $("#file")[0].files[0]);//获取文件法二
   $.ajax({
   type : 'post',
   url : './do_cart_excel.php',
   data : formdata,
   cache : false,
   dataType:'json',
   processData : false, // 不处理发送的数据，因为data值是Formdata对象，不需要对数据做处理
   contentType : false, // 不设置Content-type请求头
   success : function(data){
if(data.status ==1){
alert(data.msg);
window.location.reload();
}else{
alert(data.msg);
}
   }
   });
}


</script>

<div class="car_tit"><span class="iconfont icon-gouwuche" style="margin-left:10px;font-size:18px;color: #ffb236"></span>订购商品列表
<!-- 
<a style="float:right;width:200px;color:#ffffff; line-height:35px;" href="./resource/购物清单导入.xls">下载excel导入模版</a>
 -->
</div>

<div class="main_car">

<form id="formcart" name="formcart" method="post" action="cart.php?m=updatecart">
<input id="page" type="hidden" value="<?=$page2?>" />
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr style="background-color:#FFFF99;">
<td  height="30" width="80" align="center">
<strong>快速订购： </strong></td>
<td width="220"><input name="inputsp" placeholder="商品名称|拼音简码"  class="searchqinput" style="height:24px;padding-left:5px;" type="text" id="inputsp" size="40" onKeyDown="javascript: if(window.event.keyCode == 13) select_product();" />
     </td>
 <td >
                  <input name="buttonsp" type="button" class="button_6" id="buttonsp" style="color:#615f5f;"value=" 搜索 "  onClick="select_product();"  />
                  (可通录入商品的名称、编号、拼音码查询)
</td>
<td align="right">[<a href="wishlist.php" class="test_2">我的收藏</a>]&nbsp;&nbsp;&nbsp;&nbsp; [<a href="myorder.php?m=product" class="test_2">我订过的商品</a>]&nbsp;&nbsp;</td>
</tr>
</table>
<? if(empty($cartproduct)) { ?>
<br />&nbsp;
<br />&nbsp;
<div class="line font12" ><p align="center"><a href="list.php"><img src="./template/red/images/carnull.png" style="width: 280px;height: 280px;" title="您还没有预订任何商品！,返回订购" /></a><br /><br /><button style="width: 400px;height: 50px;background-color: #bcbcbb;border-radius: 8px;">
                        <a href="list.php" style="color: white;font-size:22px;">不！马上起来逛逛</a></button></a></p></div>			<br />&nbsp;

    <br />&nbsp;
<? } else { ?>
   <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
      <thead>
      <tr>
        <td width="5%" height="28">&nbsp;行号</td>
        <td width="12">&nbsp;商品名称</td>
<!--        <td width="24">&nbsp;生产厂家</td> -->
<!--        <td width="10%">&nbsp;规格</td> -->
        <td width="24">&nbsp;品牌</td>
        <td width="10%">&nbsp;规格</td>
        <td width="10%" style="display:none;">&nbsp;颜色 / 规格</td>
        
<? if($pns=="on") { ?>
        <td width="5%" align="right">库存</td>
        
<? } ?>
        <td width="10%" align="right">数量 / 单位</td>
        <td width="10%" align="right">单价</td>
<!--         <td width="4%" align="right">折扣</td>
        <td width="10%" align="right">折后价</td> -->
        <td width="10%" align="right">小计(元)</td>
        <td width="4%" align="center">删除</td>   
      </tr>
   </thead>
   <tbody>
    
<? if(is_array($carttempproduct)) { foreach($carttempproduct as $cartkey => $cartvar) { ?>
    
<? if(($cartvar['No'] >= $splitp['pagestart'] && $cartvar['No'] <=$splitp['pageend'])) { ?>
    
<? if($cartvar['library']=="empty") { ?>
    <tr id="linegoods_<?=$cartvar['kid']?>" style="background-color:#FFFF99;"  >
    
<? } else { ?>
    <tr id="linegoods_<?=$cartvar['kid']?>" 
<? if(fmod($cartkey,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    
<? } ?>
    <td height="35">&nbsp;<input name="kid[]" type="hidden" value="<?=$cartvar['kid']?>" /><input id="cart_oldnum_<?=$cartvar['kid']?>" type="hidden" value="<?=$cartvar['number']?>" /><?=$cartvar['No']?></td>
    <td 
<? if(!empty($cartvar['Casing'])) { ?>
title="（包装：<?=$cartvar['Casing']?>）"
<? } ?>
 >
    
<? if($cartvar['CommendID']=="2") { ?>
<span class="test_1" title="特价">[特]</span>
<? } ?>
    <a href="content.php?id=<?=$cartvar['id']?>" target="_blank"><?=$cartvar['Name']?></a></td>
    <td >&nbsp;<?=$cartvar['BrandName']?></td>
    <td >&nbsp;
<? if(strlen($cartvar['Model'])>0) { ?>
<?=$cartvar['Model']?>
<? } ?>
</td>
    <td style="display:none;" 
<? if(!empty($cartvar['Casing'])) { ?>
title="（包装：<?=$cartvar['Casing']?>）"
<? } ?>
 >
        
<? if(strlen($cartvar['color'])>0) { ?>
<?=$cartvar['color']?>
<? } ?>
        /
        
<? if(strlen($cartvar['spec'])>0) { ?>
<?=$cartvar['spec']?>
<? } ?>
    </td>
    <input id="line_num_<?=$cartvar['kid']?>" type="hidden" value="<?=$cartvar['onumber']?>" />
    <input id="line_color_<?=$cartvar['kid']?>" type="hidden" 
<? if(fmod($cartkey,2)==0) { ?>
 value="#f9f9f9"
<? } else { ?>
value="#ffffff"
<? } ?>
 />
    
<? if($pns=="on") { ?>
    <td class="font12h" align="right" >
<? if($cartvar['onumber']==9999999999) { ?>
充足
<? } else { ?>
<?=$cartvar['onumber']?>&nbsp;
<? } ?>
</td>
    
<? } ?>
    <td align="right" 
<? if(!empty($cartvar['package'])) { ?>
title="（订购数必需为 <?=$cartvar['package']?> 的倍数!）"
<? } ?>
 ><input name="cart_num[]" id="cart_num_<?=$cartvar['kid']?>" type="text" size="6" maxlength="6" value="   <?=$cartvar['number']?>" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();"  style="width:40px;border:1px solid #ABADB3" onBlur="checkcartnumber('<?=$cartvar['kid']?>','<?=$cartvar['package']?>','<?=$cartvar['price_end']?>','cart');"   />&nbsp;/&nbsp;<?=$cartvar['Units']?></td>
<!--     <td align="right" >¥ <?=$cartvar['price']?> </td>
    <td align="right" ><?=$cartvar['pencent']?></td>     -->
    <td align="right">¥ <?=$cartvar['price_end']?></td>
    <td class="font12" align="right" style="" id="line_total_<?=$cartvar['kid']?>">¥ <?=$cartvar['notetotal']?>&nbsp;</td>
    <td align="center"><a href="javascript:void(0);" onclick="delete_cart('<?=$cartvar['kid']?>')"><img src="template/img/cross.png"/></a> <input type="hidden" value="" id="kiddel_<?=$cartvar['kid']?>" name="kiddel_<?=$cartvar['kid']?>" /></td>
  </tr>
  
<? } ?>
   
<? } } ?>
 
    
<? if($splitp['total'] > $pagesize) { ?>
  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font12h">本页小计：</td>
    <td>&nbsp;</td>
    
<? if($pns=="on") { ?>
    <td >&nbsp;</td>
    
<? } ?>
        <td>&nbsp;</td>
    <td class="font12h" id="all_number_total_page" align="center"><?=$productnum2?> </td>
<!--     <td>&nbsp;</td>
    <td class="font14">&nbsp;</td> -->
    <td colspan="2" class="font12h" align="right" id="all_price_total_page">¥ <?=$producttotal2?>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    
<? } ?>
    
<? if($stair_count > 0) { ?>
    <tr id="stairtr" >
    
<? } else { ?>
    <tr id="stairtr" style="display:none">
    
<? } ?>
        <td align="right" style="padding-right:30px;" colspan="
<? if($pns == 'on') { ?>
11
<? } else { ?>
10
<? } ?>
">
            订单满 <span style="color:red;font-size:16px;font-weight:bold;" id='stair_amount'>¥ <?=$stair_amount?></span> 省 <span style="color:red;font-size:16px;font-weight:bold;" id='stair_count'>¥ <?=$stair_count?></span>
        </td>
    </tr>
    
  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font14">订单总计：</td>
    <td>&nbsp;</td>
    
<? if($pns=="on") { ?>
    <td >&nbsp;</td>
    
<? } ?>
<!--         <td>&nbsp;</td> --> 
     <td>&nbsp;</td>
   <td class="font14" id="all_number_total" align="center"><?=$productnum?> </td>
    <td colspan="2" class="font14" align="right" data-total="<? echo $producttotal - $stair_count; ?>" id="all_price_total">
        ¥ <? echo $producttotal - $stair_count; ?>&nbsp;

    </td>

    <td>&nbsp;<input type="hidden" id="price_all_" name="OrderAmount" value="<? echo $producttotal - $stair_count; ?>" /></td>
  </tr>
   </tbody>
</table>
<? if($splitp['showpage']) { ?>
<div class="list_showpage" ><?=$splitp['showpage']?></div>
<? } ?>
<div class="line"  style="margin-top:6px;">
<? if($isempty) { ?>
<font  color="red">&nbsp;&nbsp;注：黄色部份为：订货量超出可用库存数，请更新该商品的订购数量，再提交订单!</font>
<? } else { if(array_key_exists($_SESSION['cc']['ccompany'], $erp_limit_order_nums)) { if($cartTotal <= $erp_limit_order_nums[$_SESSION['cc']['ccompany']]) { if($_SESSION['cc']['cflag']==8) { ?>
<span class="notic_e"><input type="button" name="nextordercart" class="button_5" id="nextordercart" value="下一步，填写订单信息" onclick="alert('您的账号为待审核状态,不能提交订单,请联系供货商!');return false;"  /></span>
<? } else { ?>
<span class="notic_e"><input type="button" name="nextordercart" class="button_5" id="nextordercart" value="下一步，填写订单信息" onclick="updatecartsub();"  /></span>
<? } } else { ?>
&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red;font-size:16px;text-decoration:underline;">您好，单次采购明细请控制在 <?=$erp_limit_order_nums[$_SESSION['cc']['ccompany']]?>个 以内才能提交订单！</span>
<? } } else { if($_SESSION['cc']['cflag']==8) { ?>
<span class="notic_e"><input type="button" name="nextordercart" class="button_5" id="nextordercart" value="下一步，填写订单信息" onclick="alert('您的账号为待审核状态,不能提交订单,请联系供货商!');return false;"  /></span>
<? } else { ?>
<span class="notic_e"><input type="button" name="nextordercart" class="button_5" id="nextordercart" value="下一步，填写订单信息" onclick="updatecartsub();"  /></span>
<? } } } ?>
<span class="notic_e"><input type="hidden" name="updatecart" class="button_2" id="updatecart" value="更新购物车" /></span>
<span class="notic_e"><input type="button" name="goorder" class="button_3" id="goorder" value="继续购物" onclick="javascript:window.location.href='list.php'" /></span>
<span class="notic_e" style="margin-top: 6px"><a href="cart.php?m=clearcart" id="delectcar" style="margin-top: -10px" onClick="return confirm('确认要清空购物车吗?')"><img src="template/img/delete.gif" border="0" class="img" />&nbsp;清空购物车</a></span>
</div>
<? } ?>
</form>
<br />
&nbsp;
</div>

</div>
<? include template('bottom'); ?>
    <div id="windowForm6" style="width:100%;">
<div class="windowHeader">
<h3 id="windowtitle" style="color:#ffffff">请选择订购商品</h3>
<div class="windowClose"><div class="close-form" onclick="backtocart()" title="关闭" >x</div></div>
</div>
<div id="windowContent">
        正在载入数据...       
        </div>
</div>
</body>
</html>
