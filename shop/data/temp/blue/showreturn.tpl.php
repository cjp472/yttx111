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
<style  type="text/css">
.border_line {
margin-left:20px !important;
}
</style>
<script type="text/javascript">

function do_order_status(oid,ty)
{	
var altmsg = "确定要取消该退货单吗?";

if(confirm(altmsg))
{
$.post("return.php",
{m:ty, ID: oid, Content: $('#data_OrderContent').val()},
function(data){
if(data == "ok"){						
$.growlUI("提交成功，正在载入页面...");
window.location.reload();
}else{
$.growlUI(data);
}
}			
);
}	
}


function do_order_guestbook(oid)
{
if($('#data_OrderContent').val() == "")
{
$.growlUI("请输入留言内容！");
}else{
$.post("return.php",
{m:"sub_guestbook", ID: oid, Content: $('#data_OrderContent').val()},
function(data){	
if(data == "ok"){						
$.growlUI("提交成功，正在载入页面...");
window.location.reload();
}else{
$.growlUI(data);
}
}			
);
}
}

function show_bak_order(oid,ty)
{	
if(ty=="show")
{
$('#showoldorder').css("width","100%");
if($('#showoldorder').html() == "")
{
$('#showoldorder').html('<iframe src="return.php?m=showoldreturn&id='+oid+'" width="100%" marginwidth="0" height="400" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
}
$("#showoldorder").animate({opacity: 'show'}, 'slow');
$("#show_oldbutton").html('<input type="button" value="隐藏原始退单数据" class="button_4" name="viewoldorder" id="viewoldorder"  onclick="show_bak_order(\''+oid+'\',\'hide\')" />');
}else{
$("#showoldorder").animate({opacity: 'hide'}, 'slow');
$("#show_oldbutton").html('<input type="button" value="查看原始退单数据" class="button_4" name="viewoldorder" id="viewoldorder"  onclick="show_bak_order(\''+oid+'\',\'show\')" />');
}

}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div class="main_left" style="margin-top: 33px">
<div class="fenlei_bg_tit">

<span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   退货管理</div>
  <div class="news_info">
  <ul>
                <li><a href="return.php?m=returnadd" > &#8250;&#8250; 退货申请</a>
<li><a href="return.php" > &#8250;&#8250; 退货单查询</a>
<? if(is_array($return_status_arr)) { foreach($return_status_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="return.php?status=<?=$skey?>" ><strong> &#8250;&#8250; <?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="return.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>

                <li><a href="myorder.php?m=product" > &#8250;&#8250; 我订过的商品</a>	</li>
  </ul>

  </div>
<div class="fenlei_bottom"><img src="<?=CONF_PATH_IMG?>images/info_bottom.jpg" /></div>

</div>

<div class="main_right">
<div id="location" style="margin-left: -250px"><strong>您的当前位置： </strong><a href="home.php">首页</a> &#8250;&#8250; <a href="return.php">退货管理</a> &#8250;&#8250; <a href="#">退货单详细</a></div>
<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   我的退货单</div>
</div>

<div class="right_product_main">
<div class="list_line">

<div class="border_line">
<div class="line font14">单号：<span class="font14h"><?=$order['orderinfo']['ReturnSN']?></span> 
<? if($order['orderinfo']['ReturnType']=="M") { ?>
&nbsp;&nbsp;(管理端代下单)
<? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;   状态：<span class="font14h"><?=$return_status_arr[$order['orderinfo']['ReturnStatus']]?></span></div>
</div>

<br class="clear" />
<div class="border_line">
<div class="line bgw">
<div class="line2 font12">退货单信息</div>
<div class="line2">
<? if(!empty($order['orderinfo']['ReturnOrder'])) { ?>
<strong>相关订单:</strong>&nbsp;&nbsp;<a href="myorder.php?m=showorder&sn=<?=$order['orderinfo']['ReturnOrder']?>"><?=$order['orderinfo']['ReturnOrder']?></a><br />
<? } ?>
<strong>货运方式:</strong>&nbsp;&nbsp;<?=$order['orderinfo']['ReturnSendType']?><br />
<strong>货运说明:</strong>&nbsp;&nbsp;<?=$order['orderinfo']['ReturnSendAbout']?><br />
<strong>产品外观:</strong>&nbsp;&nbsp;<?=$order['orderinfo']['ReturnProductW']?><br />
<strong>包装情况:</strong>&nbsp;&nbsp;<?=$order['orderinfo']['ReturnProductB']?><br />	
<strong>退货原因:</strong>&nbsp;&nbsp;<?=$order['orderinfo']['ReturnAbout']?><br />
<strong>退货金额:</strong>&nbsp;&nbsp;<span class="font12">¥ <?=$order['orderinfo']['ReturnTotal']?></span><br />
<strong>退货时间:</strong>&nbsp;&nbsp;<? echo date("Y-m-d H:i",$order['orderinfo']['ReturnDate']); ?><br />
<strong>退 货 端:</strong>&nbsp;&nbsp;<span class="font12h">
<? if($order['orderinfo']['ReturnType']=="M") { ?>
管理端
<? } else { ?>
客户端
<? } ?>
</span><br />
<? if(!empty($order['orderinfo']['ReturnPicture'])) { ?>
<div style="width:100%;height:auto; min-height:300px; overflow:hidden;">
<img src="<?=RESOURCE_PATH?><?=$order['orderinfo']['ReturnPicture']?>" />
</div>
<? } ?>
</div>
</div>

<br class="clear" />
<div class="line bgw">
<div class="line font12">&nbsp;&nbsp;商品清单</div>
<div class="line">

  <table width="99%" border="0" cellspacing="0" cellpadding="0" align="center">
  <thead>
  <tr>
    <td width="10%" height="28">&nbsp;序号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色/规格</td>
    <td width="8%" align="right">数量</td>
    <td width="14%" align="right">单价</td>
    <td width="16%" align="right">价格(元)&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
<? if(is_array($order['ordercart'])) { foreach($order['ordercart'] as $cartkey => $cartvar) { ?>
    <tr id="linegoods_<?=$cartvar['kid']?>" 
<? if(fmod($cartkey,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    <td height="30">&nbsp;<? echo $cartkey+1; ?></td>
    <td><div style="width:95%; overflow:hidden; line-height:20px; height:20px;" title="<?=$cartvar['ContentName']?>"><a href="content.php?id=<?=$cartvar['ContentID']?>" target="_blank"><?=$cartvar['ContentName']?></a></div></td>
    <td>&nbsp;
<? if(!empty($cartvar['ContentColor'])) { ?>
<?=$cartvar['ContentColor']?>
<? } ?>
 / 
<? if(!empty($cartvar['ContentSpecification'])) { ?>
<?=$cartvar['ContentSpecification']?>
<? } ?>
</td>
    <td align="right"><?=$cartvar['ContentNumber']?>	</td>
<td align="right">¥ <?=$cartvar['ContentPrice']?>&nbsp;</td>
    <td class="font12" align="right">¥ <?=$cartvar['notetotal']?>&nbsp;</td>
  </tr>
   
<? } } ?>
 
  <tr>
    <td>&nbsp;</td>

    <td height="28" class="font14">合计：</td>
<td>&nbsp;</td>
    <td class="font12" align="right"><?=$order['totalnumber']?></td>
    <td class="font14">&nbsp;</td>

    <td class="font12" align="right">¥ <?=$order['totalprice']?>&nbsp;</td>
  </tr>
   </tbody>
</table>					

</div>
</div>
</div>

<br class="clear" />
<div class="border_line">
<div class="line" style="text-align:right;">
<? if(!empty($isbak)) { ?>
<span id="show_oldbutton">
<input type="button" value="查看原始退单数据" class="button_4" name="viewoldorder" id="viewoldorder"  onclick="show_bak_order('<?=$order['orderinfo']['ReturnID']?>','show')" />
</span>
<? } ?>
</div>
<div class="line bgw" id="showoldorder"></div>
</div>


<br class="clear" />
<div class="border_line">
<div class="line font14">订单跟踪：<a name="showsubmitlocation"></a></div>
<div class="line bgw">
<div class="line2">
<table width="100%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr>
    <td width="20%" height="28">&nbsp;时间</td>
    <td width="24%">&nbsp;操作人</td>
    <td width="20%">&nbsp;动作</td>
    <td >说明</td>
  </tr>
   </thead>
   <tbody>
<? if(is_array($ordersubmit)) { foreach($ordersubmit as $skey => $svar) { ?>
  <tr id="linesub_<?=$svar['ID']?>" 
<? if(fmod($skey,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    <td height="28">&nbsp;<? echo date("Y-m-d H:i",$svar['Date']); ?></td>
<td><?=$svar['AdminUser']?> / <?=$svar['Name']?> </td>
    <td class="font12"><?=$svar['Status']?>	</td>
<td ><?=$svar['Content']?>&nbsp;</td>
  </tr>
  
<? } } ?>
  </tbody>
  </table>
</div></div>
<br class="clearfloat" />
<div class="line bgw">
<div class="line2 font12">操作(说明/原因/留言)</div>
<div class="line2">
<textarea name="data_OrderContent" rows="4"  id="data_OrderContent" style="width:80%; height:48px;"></textarea>
          				</div>
<div class="line2">
<input type="button" value=" 我要留言 " class="button_3" name="guestbookconfirmbtn" id="guestbookconfirmbtn"  onclick="do_order_guestbook('<?=$order['orderinfo']['ReturnID']?>')"/>
<? if(empty($order['orderinfo']['ReturnStatus'])) { ?>
&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取消退货单" class="button_2" name="confirmbtn1"  id="confirmbtn1"  onclick="do_order_status('<?=$order['orderinfo']['ReturnID']?>','cancel')"/>
<? } ?>
</div>
<br class="clearfloat" />&nbsp;
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
