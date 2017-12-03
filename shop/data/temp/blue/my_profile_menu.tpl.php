<script src="template/js/jquery.execute.hidden.js?v=<?=VERID?>" type="text/javascript"></script>
<script type="text/javascript">
function checkStatusForYJF(){

backendAction();
}

//显示限额
function showSignBankLimit(){
var xftxLayer = $("#xftx_content");

var W = $(window).width(), 
H = $(window).height();

var layerW = xftxLayer.width(),
layerH = xftxLayer.height();

var scroll = $(window).scrollTop();

//显示
xftxLayer.css({
"top" : (H - layerH) / 2 + 'px',
"left" : (W - layerW) / 2 + 'px'
}).show();

$('body').append('<div id="mask" class="xftx" style="height:100%;width:100%;position:fixed;background:#000;top:0px;z-index:9999999;display:none;"></div>');

$("#mask").css('opacity', 0.6).show();
}
</script>
<?php
//载入经销商的易极付开户信息
include('openapi_check.php');

?>
 <ul>
<li><a href="my.php?m=profile" ><span class="ali-small-circle iconfont icon-next-s"></span>我的资料</a></li>
        <li><a href="my.php?m=qualification" ><span class="ali-small-circle iconfont icon-next-s"></span>企业资质</a></li>
<li><a href="my.php?m=point" ><span class="ali-small-circle iconfont icon-next-s"></span>我的积分</a></li>
<li><a href="my.php?m=password" ><span class="ali-small-circle iconfont icon-next-s"></span>修改密码</a></li>
<li><a href="wishlist.php" ><span class="ali-small-circle iconfont icon-next-s"></span>我的收藏的商品</a></li>
    <li><a href="myorder.php?collect=1" ><span class="ali-small-circle iconfont icon-next-s"></span>我的收藏的订单</a></li>
    
<? if($companyCredit==1) { ?>
<li style="border: none;"><a href="my.php?m=credit" ><span class="ali-small-circle iconfont icon-next-s"></span>医统账期</a>
<dd><a href="my.php?m=credit" 
<? if($in['m']=='credit') { ?>
style="font-weight:bold;"
<? } ?>
><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span>账期概览</a></dd>
<dd><a href="my.php?m=creditDetail" 
<? if($in['m']=='creditDetail') { ?>
style="font-weight:bold;"
<? } ?>
><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span>账期对账</a></dd>
                
<? if($PwdSetSel=='open') { ?>
<dd><a href="my.php?m=CreditPass" 
<? if($in['m']=='CreditPass') { ?>
style="font-weight:bold;"
<? } ?>
><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span>密码管理</a></dd>
                
<? } ?>
 </li>
 
<? } ?>
    
<? if(!empty($myYJF)) { ?>
<li>快捷支付</li>
<li style="border:none;line-height:20px;margin-top:5px;">
<a href="openpay.php?type=wallet" target="_blank"><span class="ali-small-circle iconfont icon-next-s"></span> 进入快捷支付管理中心</a>
</li>
<? } ?>
</ul>

<!-- 开户限额提醒 -->

<div style="width:490px;height:;background:rgb(255, 255, 255) none repeat scroll 0% 0%;border-radius:3px;z-index:999999990;position:fixed;top:0px;display:none;" id="xftx_content" class="xftx">
    <div style="border-bottom:1px solid #ccc;background:rgb(106,158,218);height:45px;line-height:45px;margin-bottom:0px;">
        <h2 style="color:#fff;text-align:center;font-weight:150;margin:0px;">限额提醒
        	<span style="float:right;margin-right:5px;font-size:26px;font-weight:normal;color:#e6e6e6;cursor:pointer;" onclick="$('#mask').remove();$('#xftx_content').hide();">X</span>
        </h2>
    </div>
    <div style="height:auto;">
    <table width="94%" cellspacing="0" cellpadding="0" border="0" align="center">
    	<tr>
    		<td>银行类型</td>
    		<td>单笔限额(元)</td>
    		<td>每日限额(元)</td>
    	</tr>
    	<tr>
    		<td>农业银行</td>
    		<td>10万</td>
    		<td>50万</td>
    	</tr>
    	<tr>
    		<td>交通银行</td>
    		<td>50万</td>
    		<td>500万</td>
    	</tr>
    	<tr>
    		<td>中国银行</td>
    		<td>100万</td>
    		<td>500万</td>
    	</tr>
    	<tr>
    		<td>建设银行</td>
    		<td>100万</td>
    		<td>500万</td>
    	</tr>
    	<tr>
    		<td>光大银行</td>
    		<td>100万</td>
    		<td>500万</td>
    	</tr>
    	<tr>
    		<td>兴业银行</td>
    		<td>50万</td>
    		<td>500万</td>
    	</tr>
    	<tr>
    		<td>工商银行</td>
    		<td>5万</td>
    		<td>5万</td>
    	</tr>
    	<tr>
    		<td>平安银行</td>
    		<td>150万</td>
    		<td>500万</td>
    	</tr>
    	<tr>
    		<td>浦发银行</td>
    		<td>5万</td>
    		<td>5万</td>
    	</tr>
    	<tr>
    		<td>中信银行</td>
    		<td>1050万</td>
    		<td>500万</td>
    	</tr>
    	<tr>
    		<td>民生银行</td>
    		<td>100万</td>
    		<td>500万</td>
    	</tr>
    
    </table>
        
    
    
    
    </div>
    <div style="text-align:center;margin-top:25px;margin-bottom:15px;">
        <button style="font-family:微软雅黑;cursor:pointer;width:140px;height:40px;background:rgb(106,158,218);border:none;border-radius: 3px;color:#fff;font-size:14px;margin-right:10px;" onclick="window.location.reload();">前往开户</button>
    </div>
</div>