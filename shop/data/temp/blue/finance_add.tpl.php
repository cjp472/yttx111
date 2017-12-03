<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link type="text/css" href="plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="template/js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="plugin/layer/layer.js"></script>
<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>

<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<style>
 #yijifu-box-line td{
border-bottom:0px;
 }
 
.layui-layer-molv .layui-layer-title{background:#01a157;}
.layui-layer-molv .layui-layer-btn a{background:#ff8e32;}
.layui-layer-btn .layui-layer-btn0{border-color:#ff8e32;}

</style>
<script type="text/javascript">
$(function() {
$("#data_FinanceToDate").datepicker();
});

$(document).ready(function(){
'<?=$in['t']?>' == 'y' && $('#FinanceYufu').click();
});

function accountadd()
{	
var ftotal = parseFloat($('#data_FinanceTotal').val());
var ytotal = parseFloat($('#ytotal').val());
$('#subfinance').attr("disabled","disabled");
if($("#data_FinanceToDate").val()=="" || $("#data_FinanceTotal").val()=="")
{
$.growlUI('转账日期和金额不能为空!');	
$('#subfinance').attr("disabled","");
}else if($('#finance_type').val()=="Y" && ftotal > ytotal){
$.growlUI('付款金额不能大于可支付余额!');
$("#data_FinanceTotal")[0].focus();
$('#subfinance').attr("disabled","");
}else if($("#data-ty").val()=="Z" && $('input[name="data_FinanceAccounts"]:checked').val()==null){
$.growlUI('收款账号不能为空!');	
$('#subfinance').attr("disabled","");
}else{
$.growlUI('正在提交，请稍后...');		
$.post("finance.php?m=guestadd",$("#formorder").serialize(),
function(data){		
if($.trim(data) == "ok"){						
$.growlUI('提交成功，正在载入页面...');		
var jumpurl = 'finance.php';
document.location = jumpurl;
}else{
$.growlUI(data);
$('#subfinance').attr("disabled","");
}
}			
);
}
}

function upload_file(fildname)
{
$('#windowContent').html('<iframe src="plugin/jqUploader/uploadfile.php" width="500" marginwidth="0" height="250" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
$.blockUI({ 
message: $('#windowForm'),
css:{ 
                width: '540px',height:'280px',top:'15%'
            }			
});
    $('#set_filename').val(fildname);
$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function closewindowui()
{
$.unblockUI();
}

function setinputfile(fpn)
{

var filevalue = $('#set_filename').val();
if(fpn!='' && fpn!=null)
{
$("#"+filevalue).val(fpn);
$("#"+filevalue+"_text").html('[<a href="<?=RESOURCE_PATH?>'+fpn+'" target="_blank">预览图片</a>]');
}

$.unblockUI();
}

var selectordertotal = 0;

function selectyufuclick()
{
if($("#FinanceYufu").attr('checked')=="checked")
{

$("#FinanceYufu").attr("checked","checked");
$("#finaceorderselectid").hide();
$("#show_order_total").html('0');
$("#yijifu_pay_total").removeAttr("readonly");
$("#alipay_pay_total").removeAttr("readonly");
}else{
$("#FinanceYufu").attr("checked",false);
$("#finaceorderselectid").show();
$("#yijifu_pay_total").attr("readonly", "true");
$("#alipay_pay_total").attr("readonly", "true");
}
}

function changeTotal(){
selectordertotal = 0;

$("#showuserorder tr").each(function(index, element) {

        if(index>0){
        	
var price = $(element).find("input[type=hidden]").val();
var box = $(element).find("input[type=checkbox]").attr("checked");

if(box == 'checked' && price != ""){
price = parseFloat(price);
selectordertotal += price;
}
}
    });
selectordertotal = Math.round(selectordertotal*100)/100;
if($("#show_order_total").length>0)  $("#show_order_total").html(selectordertotal);
if($("#alipay_pay_total").length>0)  $("#alipay_pay_total").val(selectordertotal);
if($("#netpay_pay_total").length>0)  $("#netpay_pay_total").val(selectordertotal);
if($("#yijifu_pay_total").length>0)  $("#yijifu_pay_total").val(selectordertotal);
}

function selectorderline(foid)
{
if($("#data_FinanceOrder_"+foid).attr('checked')==true)
{
$("#data_FinanceOrder_"+foid).attr("checked",false);
$("#selected_line_"+foid).css("background-color","#ffffff");
}else{
$("#data_FinanceOrder_"+foid).attr("checked",true);
$("#selected_line_"+foid).css("background-color","#efefef");
}
changeTotal();
}

function selectorderlinefocus(foid)
{
if($("#data_FinanceOrder_"+foid).attr('checked')==true)
{
$("#selected_line_"+foid).css("background-color","#efefef");
}else{
$("#selected_line_"+foid).css("background-color","#ffffff");
}
changeTotal();
}


function payto(ptype)
{
var jurl = "finance.php?OID=<?=$oinfo['OrderID']?>";
var chk_value = '';    
var obj = document.getElementsByName("data_FinanceOrder[]");//选择所有name="interest"的对象，返回数组    
    for(var i=0;i<obj.length;i++){
        if(obj[i].checked) //取到对象数组后，我们来循环检测它是不是被选中
        chk_value += obj[i].value+',';   //如果选中，将value添加到变量s中    
    }

if(ptype == "alipay"){
if($("#alipay_pay_total").val() <= 0){
alert('请输入您要支付的金额！');
return false;
}
jurl = jurl + '&m=pay&total='+$("#alipay_pay_total").val() + '&osn=' + chk_value;
window.location = jurl;
}

if(ptype == "allinpay"){
if($("#netpay_pay_total").val() <= 0){
alert('请输入您要支付的金额！');
return false;
}
jurl = jurl + '&m=netpay&total='+$("#netpay_pay_total").val() + '&osn=' + chk_value;
window.location = jurl;
}

if(ptype == "yijifu"){

if($("#yijifu_pay_total").val() <= 0){
alert('请输入您要支付的金额！');
return false;
}
var gopay = $('#subnetpay');
$.ajax({  
 type : "post",  
  url : 'my.php',  
  data : {'m' : 'bank_notice'},  
  success : function(data){  
if(data.status == "success"){
//墨绿深蓝风
var html="";
html+="<h2 align='center'>"+data.data.title+"</h2><br/>";
html+="<p>&nbsp;&nbsp;&nbsp;&nbsp;"+data.data.content+"</p>";
var notice = layer.alert(html, {
  skin: 'layui-layer-molv', //样式类名
  title:"快捷支付提示信息",
  offset: [ //为了演示，随机坐标
160
  ],
  //anim: 1 //动画类型
  btnAlign: 'c',
  closeBtn: 0 
}, function(){
layer.close(notice);
do_pays();
return false;
});
}else{
do_pays();
}


  },
  dataType:"json"
}); 
function do_pays(){
gopay.val('处理中...');
var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
$.post('my.php',
{
'm' : 'onlinepay',
'type' :'Check_onlinepay'
}, 
function(rsp){

if(rsp['status'] == 'errors1'){
gopay.val('立即支付');
layer.close(index);
if(rsp.phone != ""){
$("#edit_phone").val(rsp.phone);

}

$(".mask").css("display","block");
$("#edit_phone").focus();
$('#error-message').html('（' + rsp['message'] + '）');

return false;
}

jurl = jurl + '&m=yijifu&total='+$("#yijifu_pay_total").val() + '&osn=' + chk_value;
window.location = jurl;


}, 'json');
}	

}



}

function selectaccline(accid)
{
$("#data_FinanceAccounts_"+accid).attr("checked",true);
}
</script>

<script>
$(document).ready(function () {

$(".btn3").click(function(){
var phone= $("#edit_phone").val();
if(phone =='') {alert('手机号码不能为空！');return false;}
if(!(/^1[34578]\d{9}$/.test(phone))){ 
alert("请输入正确格式的手机号码!");return false;
}
var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
$.post("my.php",{'m' : 'onlinepay','type' :'edit_phone','phone':phone},function(data){
//alert(data);
if(data.status == 'success' ){
layer.close(index);
var jurl = "finance.php?OID=<?=$oinfo['OrderID']?>";
var chk_value = '';    
var obj = document.getElementsByName("data_FinanceOrder[]");//选择所有name="interest"的对象，返回数组    
for(var i=0;i<obj.length;i++){
if(obj[i].checked) //取到对象数组后，我们来循环检测它是不是被选中
chk_value += obj[i].value+',';   //如果选中，将value添加到变量s中    
}
window.location = jurl + '&m=yijifu&total='+$("#yijifu_pay_total").val() + '&osn=' + chk_value;;
}else{
layer.close(index);
var gopay = $('#subnetpay');
gopay.val('立即支付');
$(".mask").css("display","none");
$('#error-message').html('（' + data['message'] + '）');

return false;
}
},'json');
})


});


</script>


<style type="text/css">
<!--
.select_finance li{ padding:0; float:left; list-style-type:none; list-style:none; width:100px; text-align:center; height:38px; line-height:38px; font-weight:bold; border:#ccc solid 1px; margin:0 12px 0 0; cursor:pointer; color:#333; background:#ffffff;}
.select_finance li a{display:block; line-height:38px;}
.selected_finance a{background:#01A157; color:#fff; display:block; line-height:38px;}
.selected_finance a:hover{color:#ffffff}
-->
</style>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="finance.php">我的付款单</a> / <a href="finance.php?m=new">新增付款单</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   付款单管理</div>
  <div class="news_info">
  <ul>
                <li><a href="finance.php" ><span class="ali-small-circle iconfont icon-next-s"></span>付款单查询</a>
<? if(is_array($finance_arr)) { foreach($finance_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="finance.php?status=<?=$skey?>" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span><?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="finance.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>
                <li><a href="finance.php?m=new" ><span class="ali-small-circle iconfont icon-next-s"></span>新增付款单</a>	</li>
<li><a href="reconciliation.php" ><span class="ali-small-circle iconfont icon-next-s"></span>往来对账</a></li>
  </ul>

  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">

<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   新增付款单 </div>
</div>

<div class="right_product_main">
<div class="list_line">

<div class="line">
<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
<form id="formorder" name="formorder" method="post" action="" >
<input type="hidden" name="set_filename" id="set_filename" value="" />	
<input type="hidden" name="finance_type" id="finance_type" value="<?=$in['ty']?>" />
<? if(!empty($oinfo)) { ?>
<tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">订单信息：</div></td>
                  <td bgcolor="#FFFFFF" >订单号：<span title='订单号' class=font12><?=$oinfo['OrderSN']?></span>&nbsp;&nbsp;订单金额：<span title='金额' class=font12>¥  <?=$oinfo['OrderTotal']?></span>
  <input type="hidden" name="data_FinanceOrderID" id="data_FinanceOrderID" value="<?=$oinfo['OrderID']?>" />
  <input type="hidden" name="data_FinanceOrder[]" id="data_FinanceOrder" value="<?=$oinfo['OrderSN']?>" />
  </td>
                  <td width="20%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
<tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">付款记录：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2" >
<? if(!empty($pinfo)) { ?>
<table cellspacing="0" rules="all" border="1"  style="width:100%;border-collapse:collapse;">
<tr>
<th scope="col">付款日期</th>
<th scope="col">付款金额</th>
<th scope="col">收款账号</th>
<th scope="col">状态</th>
<th scope="col">订单</th>
</tr>
<? if(is_array($pinfo)) { foreach($pinfo as $pkey => $pvar) { ?>
    <tr>
<td><?=$pvar['FinanceToDate']?></td>
<td>¥ <?=$pvar['FinanceTotal']?></td>
<td>
<? if($pvar['FinanceFrom']=="allinpay") { ?>
网银支付
<? } elseif($pvar['FinanceType']=="Y") { ?>
余额支付
<? } else { ?>
<?=$accarr[$pvar['FinanceAccounts']]?>
<? } ?>
</td>
<td><?=$finance_arr[$pvar['FinanceFlag']]?></td>
<td><? echo $FinanceOrder = str_replace(',','<br/>',$pvar['FinanceOrder']); ?></td>
</tr>
<? } } ?>
</table>
<? } else { ?>
暂无付款记录!
<? } ?>
  
  </td>
                  
                </tr>
<? } else { if($in['ty'] != "Y") { ?>
<tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">预付款：</div></td>
                  <td bgcolor="#FFFFFF" onclick="selectyufuclick()">
                      <div align="left">&nbsp;&nbsp;<input id="FinanceYufu" name="FinanceYufu" type="checkbox" onclick="selectyufuclick()" value="yufu" style="border:0; width:16px; height:16px;" /></div>
                  </td>
                  <td width="20%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>	
<? } ?>
<tr id="finaceorderselectid">
                  <td  bgcolor="#F0F0F0"><div align="right">付款订单 ：</div></td>
                  <td colspan="2" bgcolor="#FFFFFF">				  
  <div style="width:98%; height:250px; overflow:auto;" id="showuserorder">
    <table width="98%" border="0" cellspacing="1" cellpadding="0">
                      <tr bgcolor="#F0F0F0">
                        <td width="8%">&nbsp;</td>
                        <td width="30%"><strong>&nbsp;订单号</strong></td>
                        <td width="25%"><strong>&nbsp;订单金额</strong></td>
                        <td ><strong>&nbsp;已付金额</strong></td>
                        <td ><strong>&nbsp;状态</strong></td>
                      </tr>

                      
<? if(is_array($ordlist)) { foreach($ordlist as $okey => $ovar) { ?>
                      <tr id="selected_line_<?=$ovar['OrderID']?>">
                        <td >&nbsp;
<? if($in['osn'] == $ovar['OrderSN']) { ?>
<input id="data_FinanceOrder_<?=$ovar['OrderID']?>" name="data_FinanceOrder[]" type="checkbox" value="<?=$ovar['OrderSN']?>"  checked="checked"  />
<? } else { ?>
<input  id="data_FinanceOrder_<?=$ovar['OrderID']?>" name="data_FinanceOrder[]" type="checkbox" onclick="selectorderlinefocus('<?=$ovar['OrderID']?>')" value="<?=$ovar['OrderSN']?>" />
<? } ?>
</td>
                        <td onclick="selectorderline('<?=$ovar['OrderID']?>')" >&nbsp;<?=$ovar['OrderSN']?></td>
                        <td onclick="selectorderline('<?=$ovar['OrderID']?>')">&nbsp;¥ <?=$ovar['OrderTotal']?><input type="hidden" name="ordertotal[]" id="order_total_<?=$ovar['OrderID']?>" value="<? echo $ovar['OrderTotal']-$ovar['OrderIntegral']; ?>" /></td>
<td onclick="selectorderline('<?=$ovar['OrderID']?>')">&nbsp;¥ <?=$ovar['OrderIntegral']?></td>
                        <td onclick="selectorderline('<?=$ovar['OrderID']?>')">&nbsp;<?=$order_status_arr[$ovar['OrderStatus']]?></td>
                      </tr>
                      
<? } } ?>
                  </table>
                  <div>
                  </td>
                </tr>
<? } ?>
<tr>
                  <td  bgcolor="#F0F0F0"><div align="right"><span class="test_1">*</span>支付日期 ：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_FinanceToDate" id="data_FinanceToDate" value="<? echo date('Y-m-d H:i'); ?>" class="input1 global-border"  style="width:208px;height:25px;line-height:25px;padding-left:5px;" />
                  </label></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
<tr>
                  <td height="40" bgcolor="#F0F0F0" valign="bottom"><div align="right"><strong>付款方式 ：</strong></div></td>
                  <td  bgcolor="#F0F0F0" class="select_finance" colspan="2">
<!-- <li id="pay_z" 
<? if($in['ty']=='Z') { ?>
 class="selected_finance" 
<? } ?>
 ><a href="finance.php?m=new&id=<?=$in['id']?>&ty=Z&t=<?=$in['t']?>">银行转账</a></li> -->
<li id="pay_y" 
<? if($in['ty']=='Y') { ?>
 class="selected_finance" 
<? } ?>
 ><a href="finance.php?m=new&id=<?=$in['id']?>&ty=Y&t=<?=$in['t']?>">余额支付</a></li>
<li id="pay_o" 
<? if($in['ty']=='O') { ?>
 class="selected_finance" 
<? } ?>
 ><a href="finance.php?m=new&id=<?=$in['id']?>&ty=O&t=<?=$in['t']?>">在线支付</a></li>
  </td>
                </tr>
<? if($in['ty'] == "Y") { ?>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">可支付余额：</div></td>
                  <td bgcolor="#FFFFFF"><label>&nbsp;¥ <span class="font12" ><?=$ytotal?></span>
 <input name="data_FinanceAccounts" id="data_FinanceAccounts" value="0" type="hidden"  />
 <input name="ytotal" id="ytotal" value="<?=$ytotal?>" type="hidden"  />
 </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
<? } if($in['ty'] != "O") { ?>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right"><span class="test_1">* </span>支付金额：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2">
                  
<? if($ytotal>=$payTotal) { ?>
                  <label>
                    <input type="text" name="data_FinanceTotal" id="data_FinanceTotal"  maxlength="12" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" class="input1 global-border" readonly="true" style="width:150px;height:25px;line-height: 25px;padding-left:5px;" value="
<? if(!empty($payTotal)) { ?>
<?=$payTotal?>
<? } ?>
" />&nbsp;元
                    </label> &nbsp;(您选择的订单金额为：¥ <span class="font12" id="show_order_total"><?=$payTotal?></span>，余额可完成支付)
                    
<? } else { ?>
                    <label>
                    <input type="text" name="data_FinanceTotal" id="data_FinanceTotal"  maxlength="12" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" class="input1 global-border" style="width:150px;height:25px;line-height: 25px;padding-left:5px;" value="
<? if(!empty($payTotal)) { ?>
<?=$payTotal?>
<? } ?>
" />&nbsp;元
                    </label> &nbsp;(您选择的订单金额为：¥ <span class="font12" id="show_order_total"><?=$payTotal?></span>)
                    
<? } ?>
                  
                  
                  </td>
                </tr>
<? } if($in['ty'] == "Z") { ?>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right"><span class="test_1">*</span>收款账户：</div></td>
                  <td bgcolor="#FFFFFF" colspan="2">
    <table width="99%" border="0" cellspacing="1" cellpadding="0">
                      <tr bgcolor="#F0F0F0">
                        <td width="6%">&nbsp;</td>
                        <td width="30%"><strong>&nbsp;开户行</strong></td>
                        <td width="30%"><strong>&nbsp;收款人</strong></td>
                        <td ><strong>&nbsp;帐&nbsp;号</strong></td>
                      </tr>
                      
<? if(!empty($acclist)) { ?>
                      
<? if(is_array($acclist)) { foreach($acclist as $akey => $avar) { ?>
  <tr >
                        <td align="center"><input name="data_FinanceAccounts" id="data_FinanceAccounts_<?=$avar['AccountsID']?>" type="radio" value="<?=$avar['AccountsID']?>" 
<? if(empty($akey)) { ?>
checked="checked"
<? } ?>
 /></td>
                        <td onclick="selectaccline('<?=$avar['AccountsID']?>')"><?=$avar['AccountsBank']?></td>
                        <td onclick="selectaccline('<?=$avar['AccountsID']?>')"><?=$avar['AccountsName']?></td>
                        <td onclick="selectaccline('<?=$avar['AccountsID']?>')"><?=$avar['AccountsNO']?></td>
                      </tr>
  
<? } } ?>
 
<? } else { ?>
  <tr>
                        <td colspan="4" align="center"><b>供应商暂未添加收款账号，请选择其他支付方式或联系供应商添加账号</b></td>
                      </tr>
     
<? } ?>
 </table>
    </td>                  
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">上传付款凭证：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_FinancePicture" id="data_FinancePicture"  class="input1" style="width:80%;" />
&nbsp;<input name="bt_Picture" type="button" class="button"  onClick="upload_file('data_FinancePicture');" value="..." title="上传" style="width:32px; font-size:12px;">
                  </label></td>
                  <td bgcolor="#FFFFFF" id="data_FinancePicture_text">&nbsp;</td>
                </tr>
<? } if($in['ty']=="O") { ?>
<tr>
<td colspan="3" style="border:none;">
<table width="99%" border="0" cellspacing="1" cellpadding="0">
 <!-- 支付宝支付 -->			
  
<? if(!empty($alipayarr['AccountsNO']) && !empty($alipayarr['PayPartnerID']) && !empty($alipayarr['PayKey'])) { ?>
			  
                          <tr bgcolor="#ffffff">
<td height="70" width="20%"><img src="template/img/alipay_to.jpg" alt="支付宝" width="124" /></td>
<td width="13%">&nbsp;</td>
<td width="20%"><input type="text" name="alipay_pay_total" id="alipay_pay_total"  maxlength="12" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" class="input1" style="width:100px;border: 1px solid #dbdbdb;height: 30px;line-height: 30px;padding-left: 5px;" value="
<? if(!empty($payTotal)) { ?>
<?=$payTotal?>
<? } ?>
" />&nbsp;元</td>
<td >
<input type="button" name="subpay" id="subpay" value="去支付宝支付" class="button_4" onclick="payto('alipay');" />					
</td>
  </tr>
  
<? } ?>
                          <!-- 支付宝 - 结束 -->
                          
                          <!-- 快捷支付(若供应商已开户则始终显示) -->
  
<? if(count($netInfo)) { ?>
  <tr bgcolor="#ffffff" id="yijifu-box-line">
<td height="70" width="20%"><img src="template/img/yijifu.gif" alt="快捷支付" style="padding:8px;"  /></td>
<td width="13%">&nbsp;</td>
<td width="20%">
<input type="text" name="yijifu_pay_total" id="yijifu_pay_total"  maxlength="12" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" class="input1" readonly="true" style="width:100px;border: 1px solid #dbdbdb;height: 30px;line-height: 30px;padding-left: 5px;" value="
<? if(!empty($payTotal)) { ?>
<?=$payTotal?>
<? } ?>
" />&nbsp;元
</td>
<td> 
<input type="button" name="subnetpay" id="subnetpay" value="立即支付" class="button_5" onclick="payto('yijifu');" />

<p id="error-message" style="color:red;font-size:12px;"></p>
</td>
  </tr>
  
<? } ?>
<!-- 易极付总控结束 -->

<!-- 未开通任何在线支付 -->
<? if(empty($alipayarr['AccountsNO']) &&empty($alipayarr['PayPartnerID']) && empty($alipayarr['PayKey']) && empty($getway['MerchantNO']) && empty($netInfo)) { ?>
<tr bgcolor="#ffffff">
<td align="center" style="border-bottom:0px;"><b>抱歉，请使用其它方式进行支付</b></td>
  </tr>
  	
<? } ?>
</table>
</td>
</tr>
<? } else { ?>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0"><div align="right">说 明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="data_FinanceAbout" rows="4" id="data_FinanceAbout" class="input1 global-border"><?=$_SESSION['cc']['ccompanyname']?></textarea>
                  </label></td>
                  <td bgcolor="#FFFFFF">可注明支付原因，支付人等信息</td>
                </tr>
                <tr>
                  <td valign="top" bgcolor="#F0F0F0">&nbsp;</td>
                  <td bgcolor="#FFFFFF"><label>
                  	<input type="hidden" name="data-ty" id="data-ty" value="<? echo $in['ty']; ?>"/>
                    <input type="button" name="subfinance" id="subfinance" value=" 保 存 " class="button_3" onclick="accountadd();" />
                  </label>&nbsp;&nbsp;&nbsp;&nbsp;
  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
<? } ?>
</form>
               </table>
<!--易极付弹窗开户-->

<div class="mask" style="display:none;">
<div class="logan1">
<div class="logan-top"></div>
<div class="logan-content">
<p class="show_page" style="padding:15px 0 8px 30px;color: #33a676">为保证账户安全，请确认手机号码！</p>
<span class="" style="margin:15px 0 0 30px;">
<input id='edit_phone'  style='border-bottom:1px solid #cccccc;width: 200px;font-size: 20px;' type='text' value=""/>
</span>

<div style="float: right;padding:50px 20px 0 0">

<button class="btn3" onclick="" style="display:block;float:left;margin:0 4px 0 0; width: 50px;height: 23px;background-color: #ffbe55;color: #fff">确定</button>

</div>
</div>
</div>
</div>
<!--end-->
<br />


              </div>	
<br />&nbsp;
</div>

</div>
</div>
</div>

    <div id="windowForm">
<div class="windowHeader">
<h3 id="windowtitle">上传图片</h3>
<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
</div>
<div id="windowContent">
        <iframe src="plugin/jqUploader/uploadfile.php" width="480" marginwidth="0" height="280" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>        
        </div>
</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
<? include template('bottom'); ?>
<script type="text/javascript">
    var t = "<?=$in['t']?>";
    if(t=='y'){
        selectyufuclick();
    }
</script>
</body>
</html>
