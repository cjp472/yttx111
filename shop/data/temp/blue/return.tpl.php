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
<script type="text/javascript">

function do_order_status(oid)
{	
if(confirm('你确认已收到货物了吗? 此操作不可逆!'))
{
$.post("myorder.php",
{m:"confirmincept", ID: oid, Content: $('#data_OrderContent').val()},
function(data){		
if(data == "ok"){						
$.growlUI("提交成功，正在载入页面...");
window.location.reload();
}else{
$("#tip").html(data);
}
}			
);
window.setTimeout("hideshow('tip')",20000);
}	
}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="return.php">退货管理</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   退货管理</div>
  <div class="news_info">
  <ul>
                <li><a href="return.php?m=returnadd" ><span class="ali-small-circle iconfont icon-next-s"></span>退货申请</a>
<li><a href="return.php" ><span class="ali-small-circle iconfont icon-next-s"></span>退货单查询</a>
<? if(is_array($return_status_arr)) { foreach($return_status_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="return.php?status=<?=$skey?>" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span><?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="return.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>

                <li><a href="myorder.php?m=product" ><span class="ali-small-circle iconfont icon-next-s"></span>我订过的商品</a>	</li>
  </ul>

  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">

<div class="right_product_tit1">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   我的退货单</div>
<!-- <span class="notic_e"><input type="button" name="linkaddfinace" id="linkaddfinace" value="退货申请" class="button_3" onclick="javascript:window.location.href='return.php?m=returnadd'" /></span> -->
</div>

<div class="right_product_main">
<div class="list_line">

 <table width="99%" border="0" cellspacing="0" cellpadding="0" align="center" class="ordertd">
            <thead>
  <tr>
    <td width="22%" height="28">退单号</td>
    <td>退货商品</td>
    <td width="25%">配送/外观/包装</td>
    <td width="25%">原因说明</td>
  </tr>
   </thead>
   <tbody>
   
<? if(empty($orderlist['list'])) { ?>
   <tr>
    <td colspan="4" style="border:none;text-align:center;height:40px;line-height:40px">
暂无符合条件的数据!
</td>
  </tr>
   
<? } else { if(is_array($orderlist['list'])) { foreach($orderlist['list'] as $gkey => $gvar) { ?>
  <tr  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
    <td height="98">
<? if($gvar['ReturnType']=="C") { ?>
<span title="客户端下单" class="font12h">&nbsp;C</span><br />
<? } else { ?>
<span title="管理端代下单" class="font12h">&nbsp;M</span><br />
<? } ?>
<span><a href="return.php?m=showreturn&id=<?=$gvar['ReturnID']?>" >&nbsp;<?=$gvar['ReturnSN']?></a></span><br />		
<span class="font12" title="订单金额">&nbsp;¥ <?=$gvar['ReturnTotal']?></span><br />
<span title="订单状态" class="font12h">&nbsp;<?=$return_status_arr[$gvar['ReturnStatus']]?></span><br />
</td>
    <td>
<? if(is_array($gvar['goods'])) { foreach($gvar['goods'] as $lkey => $lvar) { ?>
<div style="width:200px; height:20px; line-height:20px; overflow:hidden;"><a href="content.php?id=<?=$lvar['ContentID']?>" target="_blank"><?=$lvar['ContentName']?></a></div>
<? } } ?>
<div><a href="return.php?m=showreturn&id=<?=$gvar['ReturnID']?>" >......</a></div>
</td>
    <td>
<div class="font12" title="<?=$gvar['ReturnSendAbout']?>" ><?=$gvar['ReturnSendType']?></div>
<span title="产品外观"> <?=$gvar['ReturnProductW']?></span><br />
<span title="包装情况"><?=$gvar['ReturnProductB']?></span><br />
</td>
    <td>
<span > <?=$gvar['ReturnAbout']?></span>
</td>

  </tr>
   
<? } } ?>
 
   
<? } ?>
 
   </tbody>
</table>

<div class="list_showpage"><?=$orderlist['showpage']?></div><br />&nbsp;


</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
