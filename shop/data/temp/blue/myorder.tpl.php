<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />


<link href="<?=CONF_PATH_IMG?>css/showpage.css" rel="stylesheet" type="text/css">
<link href="plugin/jquery-ui/development-bundle/themes/base/ui.all.css" type="text/css" rel="stylesheet" />
<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="plugin/jquery-ui/development-bundle/ui/ui.datepicker.js" type="text/javascript"></script>
    <style type="text/css">
        .searchtable td{border:none;}
        .page_bar{float:right;}
        .page_bar a{padding:2px 5px;line-height: 20px;}
    </style>
<script type="text/javascript">

    $(function(){
        $("input[name='sdate'],input[name='edate']").datepicker();
        $(".collect_ajax").click(function(){
            $.get($(this).attr('href'),function(json){
                if(json.status==1){
                    $.growlUI('操作成功!');
                    window.location.reload();
                }else{
                    $.growlUI('操作失败,请重试!');
                }
            },'json');
            return false;
        });
    });

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
<div id="location">当前位置： 
    <a href="home.php">首页</a> / 
    <a href="myorder.php">我的订单</a>
    
<? if(($in['status']!="")) { ?>
 / 
    <a href="myorder.php?status=<?=$in['status']?>"><?=$order_status_arr[$in['status']]?> - 订单</a>
    
<? } ?>
</div>
<div class="main_left">
<div class="fenlei_bg_tit7"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>    订单管理</div>
  <div class="news_info">
  <ul>

                <li><a href="myorder.php"><span class="ali-small-circle iconfont icon-next-s"></span>订单查询</a>
<? if(is_array($order_arr)) { foreach($order_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="myorder.php?status=<?=$skey?>" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span><?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="myorder.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>
                <li><a href="myorder.php" ><span class="ali-small-circle iconfont icon-next-s"></span>付款状态</a>
<? if(is_array($pay_arr)) { foreach($pay_arr as $pkey => $pvar) { if($pkey==$in['pid'] && isset($in['pid'])) { ?>
<dd><a href="myorder.php?pid=<?=$pkey?>" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span><?=$pvar?></strong></a></dd>
<? } else { ?>
<dd><a href="myorder.php?pid=<?=$pkey?>" > <?=$pvar?></a></dd>
<? } } } ?>
</li>
                <li><a href="myorder.php" ><span class="ali-small-circle iconfont icon-next-s"></span>发货状态</a>
<? if(is_array($send_arr)) { foreach($send_arr as $dkey => $dvar) { if($dkey==$in['fid'] && isset($in['fid'])) { ?>
<dd><a href="myorder.php?pid=<?=$dkey?>" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span><?=$dvar?></strong></a></dd>
<? } else { ?>
<dd><a href="myorder.php?fid=<?=$dkey?>" >  <?=$dvar?></a></dd>
<? } } } ?>
</li>
<li><a href="myorder.php?m=product" ><span class="ali-small-circle iconfont icon-next-s"></span>我订过的商品</a></li>
                <li><a href="myorder.php?collect=1" ><span class="ali-small-circle iconfont icon-next-s"></span>我收藏的订单</a>
  </ul>

  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">

<div class="right_product_tit">

<div class="xs_0">  <span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   我的订单</div>
</div>


    <div style="clear:both;">

    </div>


<div class="right_product_main">
<div class="list_line">
   <div style="border-bottom:#CCCCCC solid 1px;">
       <form method="post" action="">
           <table class="searchtable">
               <tr>
                   <td>
                       <strong>订单搜索：</strong><input style="border: 1px solid #ABADB3;height: 22px;margin-top: -2px;" type="text" name="kw" value="  <?=$in['kw']?>" style="width:100px; "  />
                   </td>
                   <td>
                       <select name="stype" id="stype" class="selectline" style="width:88px;height:24px;margin-bottom:-2px;border:1px solid #ABADB3;margin-top:-2px;">
                           
<? if($in['stype']=='ordersn') { ?>
                           <option value="ordersn" selected="selected" >订单号</option>
                           
<? } else { ?>
                           <option value="ordersn">订单号</option>
                           
<? } ?>
                           
<? if($in['stype']=='productname') { ?>
                           <option value="productname" selected="selected">商品名称</option>
                           
<? } else { ?>
                           <option value="productname">商品名称</option>
                           
<? } ?>
                           
<? if($in['stype']=='giftsname') { ?>
                           <option value="giftsname" selected="selected">赠品名称</option>
                           
<? } else { ?>
                           <option value="giftsname">赠品名称</option>
                           
<? } ?>
                           
<? if($in['stype']=='receiveName') { ?>
                           <option value="receiveName" selected="selected">收货人</option>
                           
<? } else { ?>
                           <option value="receiveName">收货人</option>
                           
<? } ?>
                           
<? if($in['stype']=='receiveAdd') { ?>
                           <option value="receiveAdd" selected="selected">收货地址</option>
                           
<? } else { ?>
                           <option value="receiveAdd">收货地址</option>
                           
<? } ?>
                       </select>
                   </td>
                   <td>日期 从</td>
                   <td>
                       <input type="text" name="sdate" style="width:100px;border: 1px solid #ABADB3;height: 22px" value="  <?=$in['sdate']?>" />
                   </td>
                   <td>到</td>
                   <td>
                       <input type="text" name="edate" style="width:100px;border: 1px solid #ABADB3;height: 22px" value="  <?=$in['edate']?>" />
                   </td>
                   <td>
                       <input type="submit" class="button_3" style="margin-top:0px;" value="搜索" />
                   </td>
               </tr>
           </table>
       </form>
   </div>
  <table width="99%" border="0" cellspacing="0" cellpadding="0" align="center" class="ordertd">
  <thead>
  <tr>
    <td width="22%" height="28">订单号</td>
    <td>订单商品</td>
    <td width="25%">配送</td>
    <td width="25%">付款</td>
  </tr>
   </thead>
   <tbody>
   
<? if(count($orderlist['list'])==0) { ?>
   <tr>
       <td colspan="4" align="center" style="text-align:center;">暂无相关订单</td>
   </tr>
   
<? } if(is_array($orderlist['list'])) { foreach($orderlist['list'] as $gkey => $gvar) { ?>
  <tr  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
    <td height="98">
<span>
<a href="myorder.php?m=showorder&id=<?=$gvar['OrderID']?>" ><?=$gvar['OrderSN']?></a>
<? if($gvar['OrderType']=="C") { ?>
[<span title="客户自下单" class="font12h">C</span>]
<? } else { ?>
[<span title="管理端代下单" class="font12h">M</span>]
<? } ?>
</span>
        <br />
<span title="订单状态" class="font12h">&nbsp;<?=$order_status_arr[$gvar['OrderStatus']]?></span>
        
<? if($gvar['OrderSpecial'] == 'T') { ?>
        <span title="特价订单" style="color:#B61BD2;border:1px solid;margin-left:5px;">&nbsp;特价单</span>
        
<? } ?>
        <br />
        <span title="收藏状态">
            
<? if($gvar['OrderCollect']==1) { ?>
            <a class="collect_ajax" title="移除收藏" href="myorder.php?m=collect&id=<?=$gvar['OrderID']?>&join=0" style="font-size:12px;">&#8250;&#8250; 移除</a>
            
<? } else { ?>
            <a class="collect_ajax" title="收藏订单" href="myorder.php?m=collect&id=<?=$gvar['OrderID']?>&join=1" style="font-size:12px;">&#8250;&#8250; 收藏</a>
            
<? } ?>
        </span>
        <a class="onclick-to-go" href="myorder.php?m=copyorder&oid=<?=$gvar['OrderID']?>">一键订购</a>

</td>
    <td>
<? if(is_array($gvar['goods'])) { foreach($gvar['goods'] as $lkey => $lvar) { ?>
<div style="width:180px; height:20px; line-height:20px; overflow:hidden;margin-top:3px;"><a href="content.php?id=<?=$lvar['ContentID']?>" target="_blank" title="<?=$lvar['ContentName']?>"><?=$lvar['ContentName']?></a></div>
<? } } ?>
<div><a href="myorder.php?m=showorder&id=<?=$gvar['OrderID']?>" >......</a></div>
</td>
    <td>
<div style="width:140px; height:20px; line-height:20px; overflow:hidden;margin-top:3px;" title="配送方式: <?=$sendtypearr[$gvar['OrderSendType']]?>"><?=$sendtypearr[$gvar['OrderSendType']]?></div>
<? if(!empty($gvar['DeliveryDate']) && $gvar['DeliveryDate'] != "0000-00-00") { ?>
交货日期：<?=$gvar['DeliveryDate']?><br />
<? } ?>
<span title="状态" class="font12h"><?=$send_status_arr[$gvar['OrderSendStatus']]?></span><br />
<? if($gvar['OrderSendStatus']=="2") { ?>
<span ><a href="consignment.php?m=showcontent&sn=<?=$gvar['OrderSN']?>">物流跟踪</a></span>
<? } ?>
</td>
    <td>
<span class="font12" title="订单金额">¥ <?=$gvar['OrderTotal']?></span><br />
<div title="付款方式:<?=$paytypearr[$gvar['OrderPayType']]?>" style="width:140px; height:20px; line-height:20px; overflow:hidden;"><?=$paytypearr[$gvar['OrderPayType']]?></div>
<span title="付款状态" class="font12h"><?=$pay_status_arr[$gvar['OrderPayStatus']]?></span><br />
<? if($gvar['OrderPayStatus'] != "2" && $gvar['OrderPayStatus'] != "4" && (!in_array($gvar['OrderStatus'], array(8,9)))) { ?>
<div title="付款" style="width:45px; height:23px; text-align:left; font-weight:bold; padding:1px 2px;border-radius: 2px; background-color:#ffb236;"><a href="finance.php?m=new&id=<?=$gvar['OrderID']?>" >&#8250;&#8250; 付款</a></div>
<? } if($gvar['OrderStatus'] == "3" && $producttype['return_type'] == "order" && empty($gvar['ReturnID'])) { ?>
<div title="我要退货" style="width:70px; height:20px; text-align:left; font-weight:bold; padding:1px 2px;margin-top:2px; border:#cc0000 solid 1px; background-color:#fefefe;"><a href="return.php?m=returnadd&sn=<?=$gvar['OrderSN']?>" >&#8250;&#8250; 我要退货</a></div>
<? } ?>
</td>

  </tr>
   
<? } } ?>
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
