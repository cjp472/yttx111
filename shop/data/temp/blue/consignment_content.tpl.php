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
<script language="javascript" type="text/javascript">
function do_confirm_consignment(cid)
{
if(confirm("您确认已经收到货物了吗?"))
{
$.growlUI('正在执行，请稍候...');
$.post("consignment.php",
{m:"confirm", cid: cid},
function(data){
if(data == "ok"){
$.growlUI('操作成功！');		
$("#show_status_").html('&nbsp;状态：确认到货');
$("#button_line_").animate({opacity: 'hide'}, 'slow');	
}else{
$.growlUI(data);
}
}			
);
}
}

function show_kuaidi(com,nu)
{
$.post("consignment.php",
{m:"showkuaidi", Com: com, Nu:nu},
function(data){
$("#showkuaidi").html(data);
}			
);
}

</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div class="main_left" style="margin-top: 33px">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   发货单管理</div>
  <div class="news_info">
  <ul>
                <li><a href="consignment.php" > <span class="ali-small-circle iconfont icon-next-s"></span>发货单查询</a>
<? if(is_array($incept_arr)) { foreach($incept_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="consignment.php?status=<?=$skey?>" ><strong> &#8250;&#8250; <?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="consignment.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>

                <li><a href="consignment.php?m=address" ><span class="ali-small-circle iconfont icon-next-s"></span>收货地址管理</a>	</li>
<li><a href="consignment.php?m=address#editname" ><span class="ali-small-circle iconfont icon-next-s"></span>新增地址</a></li>

<li><a href="kuaidi_search.php" target="_blank"  title="快递查询"><img src="template/img/c1.jpg" alt="快递查询" /></a></li>
<li><a href="wuliu_search.php" target="_blank"  title="物流查询"><img src="template/img/c2.jpg" alt="物流查询" /></a></li>
  </ul>

  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">
<div id="location" style="margin-left: -250px"><strong>您的当前位置： </strong><a href="home.php">首页</a> &#8250;&#8250; <a href="consignment.php">我的发货单</a></div>
<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   我的发货单</div>
</div>

<div class="right_product_main">
<div class="list_line">

<div class="border_line">					
<div class="line font14"><span class="numberbg"><?=$content['coninfo']['ConsignmentID']?></span> &nbsp;&nbsp;运单号：<span class="font14h"><?=$content['coninfo']['ConsignmentNO']?></span> &nbsp;&nbsp;&nbsp;&nbsp;   <span id="show_status_" class="font14">状态：<span class="font14h"><?=$incept_arr[$content['coninfo']['ConsignmentFlag']]?></span></span>  &nbsp;&nbsp;&nbsp;&nbsp; 
<? if(empty($content['coninfo']['ConsignmentFlag'])) { ?>
<span id="button_line_"><a style="background:#FFB135;border-radius: 2px;color: #fff;padding: 2px 4px 2px 4px " href="javascript:void(0)" onclick="do_confirm_consignment('<?=$content['coninfo']['ConsignmentID']?>')" title="收到货后，请在此确认" >&nbsp;确认收货&nbsp;</a></span><br />
<? } ?>
</div>

<div class="line bgw">
<div class="line2 font12">发货信息</div>
<div class="line2">
<span class="bold">发 货 人：</span><?=$content['coninfo']['ConsignmentMan']?><br />
<span class="bold">发货时间：</span><?=$content['coninfo']['ConsignmentDate']?><br />					
    <span class="bold">备注信息：</span><?=$content['coninfo']['ConsignmentRemark']?><br />
</div>
</div>

<br class="clear" />
<div class="line bgw">

<div class="line2 font12">收货信息</div>
<div class="line2">
<span class="bold">收 货 人：</span><?=$content['coninfo']['InceptMan']?><br />
<span class="bold">收货单位：</span><?=$content['coninfo']['InceptCompany']?><br />					
    <span class="bold">联系电话：</span><?=$content['coninfo']['InceptPhone']?><br />
<span class="bold">收货地址：</span><?=$content['coninfo']['InceptAddress']?><br />					
</div>
</div>
</div>
<? if(!empty($content['loginfo']['LogisticsPinyi']) && !empty($content['coninfo']['ConsignmentNO'])) { ?>
<br class="clear" />
<div class="border_line">
<div class="line font14">&nbsp;&nbsp;物流跟踪</div>
<div class="line bgw">						
<div class="line2" id="showkuaidi"><script language="javascript" type="text/javascript">show_kuaidi("<?=$content['loginfo']['LogisticsPinyi']?>","<?=$content['coninfo']['ConsignmentNO']?>");</script>
<p align="center"><img src="template/img/loading.gif" /></p>
</div>
</div>
</div>
<? } ?>
<br class="clear" />
<div class="border_line">
<div class="line font14">&nbsp;&nbsp;订单信息</div>
<div class="line bgw">						
<div class="line" style="margin:4px; padding:4px;"><span class="font12">&nbsp;&nbsp;订单号：</span><a href="myorder.php?m=showorder&sn=<?=$content['orderinfo']['OrderSN']?>"><?=$content['orderinfo']['OrderSN']?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font12">订单金额：</span><span class="bold">¥ <?=$content['orderinfo']['OrderTotal']?></span></div>
</div>
<br class="clear" />
<div class="line bgw">						
<div class="line font12">&nbsp;&nbsp;发货明细单：</div>
<div class="line">
<table width="99%" border="0" cellspacing="1" cellpadding="2" align="center">
  <thead>
  <tr class="bottomline">
    <td width="6%" >&nbsp;行号</td>
<td width="14%" >编号/货号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色</td>
<td width="12%">&nbsp;规格</td>
    <td width="10%" >数量</td>
    <td width="5%" >单位</td>
  </tr>
   </thead>
   <tbody id="listcartdataid">
<? if(is_array($content['cartinfo'])) { foreach($content['cartinfo'] as $ckey => $cvar) { ?>
    <tr>
<td  height="30">&nbsp;<? echo $n=$ckey+1; ?></td>
<td >&nbsp;<?=$cvar['Coding']?></td>
    <td>&nbsp;<a href="content.php?id=<?=$cvar['ContentID']?>" target="_blank"><?=$cvar['ContentName']?></a></td>
    <td >&nbsp;<?=$cvar['ContentColor']?></td>
<td >&nbsp;<?=$cvar['ContentSpecification']?></td>
    <td ><?=$cvar['ContentNumber']?></td>
<td >&nbsp;<?=$cvar['Units']?></td>
</tr>
<? } } if(is_array($content['cartinfog'])) { foreach($content['cartinfog'] as $ckey => $cvar) { ?>
<tr bgcolor="#efefef" title="赠品">
    <td  height="30">&nbsp;<? echo $n+1; ?></td>
<td >&nbsp;<?=$cvar['Coding']?></td>
    <td>&nbsp;<a href="content.php?id=<?=$cvar['ContentID']?>" target="_blank"><?=$cvar['ContentName']?></a></td>
    <td >&nbsp;<?=$cvar['ContentColor']?></td>
<td >&nbsp;<?=$cvar['ContentSpecification']?></td>
    <td ><?=$cvar['ContentNumber']?></td>
<td >&nbsp;<?=$cvar['Units']?></td>
</tr>
<? } } ?>
    </tbody>
</table>
</div>
</div>
</div>

<br class="clear" />
<div class="border_line">
<div class="line font14">&nbsp;&nbsp;货运信息</div>

<div class="line bgw">
<? if(!empty($content['loginfo']['LogisticsName'])) { ?>
<div class="line2 font12">货运公司信息</div>
<div class="line2">
<span class="bold">货运公司：</span><?=$content['loginfo']['LogisticsName']?><br />
<span class="bold">联 系 人：</span><?=$content['loginfo']['LogisticsContact']?><br />					
    <span class="bold">联系电话：</span><?=$content['loginfo']['LogisticsPhone']?><br />
<span class="bold">公司传真：</span><?=$content['loginfo']['LogisticsFax']?><br />
<span class="bold">移动电话：</span><?=$content['loginfo']['LogisticsMobile']?><br />
<span class="bold">公司地址：</span><?=$content['loginfo']['LogisticsAddress']?><br />					
    <span class="bold">公司网站：</span>
<? if(!empty($content['loginfo']['LogisticsUrl'])) { ?>
<a href="<?=$content['loginfo']['LogisticsUrl']?>" target="_blank"><?=$content['loginfo']['LogisticsUrl']?></a>
<? } ?>
<br />
<span class="bold">公司简介：</span><br /><? echo nl2br($content['loginfo']['LogisticsAbout']); ?><br />
</div>
<? } else { ?>
<div class="line2 font12">上门自提</div>
<? } ?>
</div>
<br class="clear" />
<div class="line bgw">

<div class="line2 font12">运费及付款方式</div>
<div class="line2">
<span class="bold">付款方式：</span><?=$pay_send_arr[$content['coninfo']['ConsignmentMoneyType']]?><br />
<span class="bold">运    费：</span>¥ <?=$content['coninfo']['ConsignmentMoney']?><br />					
</div>
</div>
</div>

</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
