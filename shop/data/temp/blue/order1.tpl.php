<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<!-- <script src="template/js/jquery-1.9.1.min.js" type="text/javascript"></script> -->
<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="plugin/layer/layer.js"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/my.js?v=<?=VERID?>" type="text/javascript"></script>
<script type="text/javascript" language="javascript" > 

function setinputval(companymsg,contactmsg,phonemsg,addressmsg)
{
$("#AddressCompany").val(companymsg);
$("#AddressContact").val(contactmsg);
$("#AddressPhone").val(phonemsg);
$("#AddressAddress").val(addressmsg);
}
</script>


<link rel="stylesheet" type="text/css" href="template/css/icon.css?v=<?=VERID?>"/>
<link rel="stylesheet" type="text/css" href="template/css/pop.css?v=<?=VERID?>"/>
</head>
<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="cart.php">我的购物车 </a> / <a href="#">提交订单</a></div>

<div class="car_tit"><span class="iconfont icon-gouwuche" style="margin-left: 10px;color:#ffb236;"></span>   您的订单</div>

<div class="thank" style="height: 370px;border: 1px solid #D6D6D6">
  <div class="thank_word" style="height:40px;">
  <p style="font-weight:bold;font-size: 18px;margin-top: 15px;text-align:center;">订单提交成功，请您尽快付款，以便库房及时出库！</p>
  </div>
<div class="thank_goods" style="height:204px;border-bottom:1px solid #dbdbdb;width:98%;margin:0 auto;border-top:1px solid #dbdbdb;">
<img src="template/red/images/duihao.png" style="margin:20px 0 0 194px" alt=""/>
<div class="goods_de">
             <p style="font-size: 16px;font-weight: bold;position: absolute;margin-left: 260px;margin-top: -50px">您的订单号是：<span style="color: #01A157"><? echo $in['id']; ?></span>&nbsp;&nbsp;&nbsp;&nbsp; 现在 <a href="myorder.php?m=showorder&sn=<? echo $in['id']; ?>" style="color: #01A157;font-size: 16px;"> 查看订单状态 </a>或者<a href="home.php" style="color: #01A157;font-size: 16px;"> 返回首页</a></p>
<p style="margin-left: 260px;font-size: 16px;font-weight: bold;margin-bottom: 30px">应付金额：<span style="font-weight: bold;color: red;font-size:20px;">¥ <?=$oinfo['OrderTotal']?></span></p>
<p style="font-weight: bold;font-size: 16px"><span style="font-size: 16px;font-weight: bold;margin-left: 260px;">支付方式：</span>您已选择 <span style="font-size: 18px;font-weight: bold;color: red;"><? echo $payInfo[$oinfo['OrderPayType']]; ?></span>，您可以尝试 <a href="finance.php?m=new&id=<?=$in['id']?>" style="font-size: 16px;color:#01A157;font-weight: bold">其他付款方式</a></p>

 <p id="error-message" style="text-align:center;color:red;margin:10px 0;font-size:18px;"></p>
                         <input type="text" id="order_id" value="<?=$oinfo['OrderID']?>" hidden="block">
                         <input type="text" id="OrderMonth" value="<?=$oinfo['OrderTotal']?>" hidden="block">
                         <input type="text" id="order_sn" value="<? echo $in['id']; ?>" hidden="block">
</div>
</div>
<? if($oinfo['OrderPayType']=='9') { ?>
<span class="pay-go" id="pay-go-yijifu">立即支付</span>
<!--<span class="pay-go">立即支付</span>-->

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
<? } elseif($oinfo['OrderPayType']=='11') { ?>
<span class="pay-go" onclick="window.location='finance.php?OID=<?=$oinfo['OrderID']?>&m=pay'">立即支付</span>
        
<? } elseif($oinfo['OrderPayType']=='12') { ?>
            
<? if(empty($PayPaw)) { ?>
                <span class="pay-go" id="pay-setPass">立即支付</span>
            
<? } else { ?>
                <span class="pay-go" id="pay-Pass">立即支付</span>
            
<? } } else { ?>
<span class="pay-go" onclick="window.location='finance.php?m=new&id=<?=$oinfo['OrderSN']?>&ty=Y'">立即支付</span>
<? } ?>
</div>

</div>

</div>
<? include template('bottom'); ?>
<!-- 遮罩层 -->
<? include template('mask_layer'); ?>
<!-- 载入首次设置密码或支付 -->
<? if($oinfo['OrderPayType']=='12') { if(empty($PayPaw)) { include template('popSetpass'); } else { include template('popPass'); } } ?>
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
if(data.status == 'success' ){
layer.close(index);
window.location = 'finance.php?m=yijifu&OID=<?=$in['id']?>';
}else{
var gopay = $('#pay-go-yijifu');
gopay.html('立即支付');
$(".mask").css("display","none");
$('#error-message').html('（' + data['message'] + '）');
layer.close(index);
return false;
}

},'json');
})
});
</script>
<script type="text/javascript">
$(document).ready(function(){
if('<?=$in['autopay']?>'){
layer.open({
        	type: 1,
            title: '',
            closeBtn: 0,
            content: $('#pay-password'),
            area: ["376px","270px"],
            shade:[0.6, '#000'],
            move:false,
            shadeClose: false
        })
}

var gopay = $('#pay-go-yijifu');
gopay.bind('click', function(){ 
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
//执行支付操作
function do_pays(){
gopay.html('处理中...');
//加载层
var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
$.post('my.php',
{
'm' : 'onlinepay',
'type' :'Check_onlinepay'
}, 
function(rsp){
if(rsp['status'] == 'errors1'){
var gopay = $('#pay-go-yijifu');
gopay.html('立即支付');
if(rsp.phone != ""){
$("#edit_phone").val(rsp.phone);
}
$('#error-message').html('（' + rsp['message'] + '）');
$("#edit_phone").focus();
$(".mask").css("display","block");
$("#edit_phone").focus();
layer.close(index);
return false;
}else if(rsp['status'] == 'error'){
var gopay = $('#pay-go-yijifu');
gopay.html('立即支付');
$('#error-message').html('（' + rsp['message'] + '）');
layer.close(index);
return false;
}


//前往收银台
window.location = 'finance.php?m=yijifu&OID=<?=$in['id']?>';
}, 'json');
}	

});

//点击设置密码
$('#pay-setPass').click(function(){
layer.open({
type: 1,
closeBtn: 0,
title: '',
content: $('#pay-password-set'),
area: ["376px","420px"],
shade:[0.6, '#000'],
shadeClose: false,
resize: false,
            move:false,
})
      /* 输入手机号弹窗layer.open({
        	type: 1,
        	closeBtn: 0,
            title: '',
            content: $('#telplayer'),
            area: ["376px","188px"],
            shade:[0.6, '#000'],
            shadeClose: false,
            move:false,
            success: function(layero, index){
            	$('#set_notice', layero).html('');
            }
        })*/

         
});
    //输入密码
    $('#pay-Pass').click(function(){
        layer.open({
        	type: 1,
        	closeBtn: 0,
            title: '',
            content: $('#pay-password'),
            area: ["376px","270px"],
            shade:[0.6, '#000'],
            shadeClose: false,
            move:false,
            success: function(layero, index){
            	$('#set_notice', layero).html('');
            }
        });
    });        
});
</script>

</body>
</html>
