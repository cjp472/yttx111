<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />

<meta http-equiv="x-ua-compatible" content="IE=edge" />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="<?=CONF_PATH_IMG?>css/showpage.css" rel="stylesheet" type="text/css">

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js" type="text/javascript"></script>
<script src="template/js/my.js?v=<?=VERID?>" type="text/javascript"></script>


<script type="text/javascript" src="plugin/layer/layer.js"></script>
<script src="template/js/echarts.js" type="text/javascript" charset="utf-8"></script>

<link rel="stylesheet" type="text/css" href="template/css/pop.css?v=<?=VERID?>"/>
<link rel="stylesheet" type="text/css" href="template/css/credit.css?v=<?=VERID?>"/>
<style>
    /*关闭按钮设置*/
    .layui-layer-close{
        position: absolute;
        top: 0 !important;
        right: 0 !important;
    }
</style>
</head>
<body>
<? include template('header'); ?>
<div id="main">
    <div id="location">
        当前位置： <a href="home.php">首页</a> / <a href="my.php?m=profile">我的医药账户</a> / <a href="my.php?m=credit">账期预览</a>
    </div>
    <div class="main_left">
        <div class="fenlei_bg_tit"><span>我的医药账户</span></div>
          <div class="news_info">
            <!-- 载入菜单 -->
            
<? include template('my_profile_menu'); ?>
        
          </div>
        <div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>
    </div>
    <div class="main_right">
        <div class="right_product_tit">
            <div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span><!--修改密码-->医统账期/账期概览</div>
        </div>
        <div class="right_product_main">
            <div class="list_line">
                
                    <ul class="credit-detail">
                        <li class="total">
                            <i></i>
                            
<? if(empty($detailAdd)) { ?>
                            <div id="credit_total"></div>
                            
<? } else { ?>
                            <div id="credit_total" style="display: block;"></div>
                            
<? } ?>
                            <span>总额度</span>
                        </li>
                        <li class="used">
                            <i></i>
                            
<? if(empty($detailAdd)) { ?>
                            <div id="credit_used"></div>
                            
<? } else { ?>
                            <div id="credit_used"></div>
                            
<? } ?>
                            <span>已用</span>
                        </li>
                        <li class="unused">
                            <i></i>
                            
<? if(empty($detailAdd)) { ?>
                            <div id="credit_unused"></div>
                            
<? } else { ?>
                            <div id="credit_unused"></div>
                            
<? } ?>
                            <span>余额</span>
                        </li>
                    </ul>
                
<? if($BottomCommon == T) { ?>
                    
<? if($detailAdd['CreditStatus'] == oneunapprove) { ?>
                        <div class="credit_notice" style="color:red">
                                <?=$detailAdd['OneContent']?>
                        </div>
                    
<? } ?>
                    
<? if($detailAdd['CreditStatus'] == twounapprove) { ?>
                        <div class="credit_notice" style="color:red">
                                <?=$detailAdd['TwoContent']?>
                        </div>
                    
<? } ?>
                    
<? if($detailAdd['CreditStatus'] == closed) { ?>
                        <div class="credit_notice" style="color:red">
                                <?=$detailAdd['OneContent']?>
                        </div>
                    
<? } ?>
                
<? } ?>
                    
<? if(empty($detailAdd) && $detailApply['total']) { ?>
                    <p class="apply"><i class="same_button"><a href="javascript:;">请耐心等待审核</a></i></p>
                    <div class="credit_notice">
                                                医统账期为您提供安全、方便的信用支付，首月免息。为您的药品采购提供资金保障，年化利息仅18%。
                    </div>
                    
<? } elseif($detailAdd['CreditStatus'] != open) { ?>
                        
<? if($BottomCommon == T) { ?>
                            <p class="apply"><i class="same_button"><a href="my.php?m=creditApply">立即申请</a></i></p>
                        
<? } else { ?>
                        	<p class="apply"><i class="same_button"><a href="#" onclick="Bottom()">立即申请</a></i></p>
                        
<? } ?>
                        <div class="credit_notice">
                                                    医统账期为您提供安全、方便的信用支付，首月免息。为您的药品采购提供资金保障，年化利息仅18%。
                        </div>
                    
<? } else { ?>
                    <p class="credit_notice">
<? if(!$creditMoney['isAdvanced']&&$creditMoney['yuan']) { ?>
提前还款
<? } else { ?>
本期全部应还款额
<? } ?>
&nbsp;&nbsp;&nbsp;&nbsp;<span>¥&nbsp;<?=$creditMoney['yuanFormat']?></span><i class="same_button return"><a href="finance.php?m=refund">立即还款</a></i><i class="same_button"><a href="my.php?m=creditDetail&begindate=<?=$creditDate['start']?>&enddate=<?=$creditDate['end']?>&isup=true">对账</a></i></p>
                    
                    
                    <p style="width:90%;margin: 0 auto;"><br />
                    <span class="notice">医统账期为您提供安全、方便的信用支付，首月免息。为您的药品采购提供资金保障，年化利息仅18%。</span><br /><br />
                    	感谢您使用医统账期，以下是您  <span style="color:#f49400"><?=$creditDate['startofzh']?></span> 至  <span style="color:#f49400"><?=$creditDate['endofzh']?></span> 账期账户变动情况，请勿错过您的还款期限而产生利息。
                    </p>
                    
<? } ?>
                    
                <br class="clear" />
            </div>
            <br />&nbsp;
        </div>
    </div>
</div>
<? include template('bottom'); ?>
</body>

<!-- 载入图表模板 -->
<? include template('my_credit_pie'); ?>
<div class="mask" style="display: none;">
<div class="logan1">
<div class="logan-top"></div>
<div class="logan-content">
<p class="show_page" style="padding: 15px 0 8px 30px; color: #33a676">为保证账户安全，请确认手机号码！</p>
<span class="" style="margin: 15px 0 0 30px;"> <input
id='edit_phone'
style='border-bottom: 1px solid #cccccc; width: 200px; font-size: 20px;'
type='text' value="" />
</span>

<div style="float: right; padding: 50px 20px 0 0">

<button class="btn3" onclick=""
style="display: block; float: left; margin: 0 4px 0 0; width: 50px; height: 23px; background-color: #ffbe55; color: #fff">确定</button>

</div>
</div>
</div>
</div>

</html>
<script>
$(document).ready(function () {
//还款金额判断
if($('.credit_notice span').html() == '¥&nbsp;0.00'){
$('.return').attr('disabled','true').addClass('global-gray');
$('.return a').attr('disabled','true').attr('href','#').addClass('global-gray');
};
//易极付开户检查
var gopay = $('#pay-go-yijifu');
gopay.bind('click', function() {
$.ajax({
type : "post",
url : 'my.php',
data : {
'm' : 'bank_notice'
},
success : function(data) {
if (data.status == "success") {
//墨绿深蓝风
var html = "";
html += "<h2 align='center'>" + data.data.title
+ "</h2><br/>";
html += "<p>&nbsp;&nbsp;&nbsp;&nbsp;"
+ data.data.content + "</p>";
var notice = layer.alert(html, {
skin : 'layui-layer-molv', //样式类名
title : "快捷支付提示信息",
//anim: 1 //动画类型
btnAlign : 'c',
closeBtn : 0
}, function() {
layer.close(notice);
do_pays();
return false;
});
} else {
do_pays();
}
},
dataType : "json"
});
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
gopay.html('立即还款');
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
gopay.html('立即还款');
$('#error-message').html('（' + rsp['message'] + '）');
layer.close(index);
return false;
}


//前往收银台
window.location = 'finance.php?m=yijifu&OID=<?=$in['id']?>';
}, 'json');
}	

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
window.location = 'finance.php?m=refund';
}else{
var gopay = $('#pay-go-yijifu');
gopay.html('立即还款');
$(".mask").css("display","none");
$('#error-message').html('（' + data['message'] + '）');
layer.close(index);
return false;
}

},'json');
})
});
        function Bottom(){
                var validHtml = '<div class="layui-layer-content" style="height: 143px;line-height: 28px;font-size: 16px;text-align: center;margin: 0 15px;">';
                validHtml +='<img src="../manager/images/wenjian.jpg" style="width: 50px;display: block;margin: 0 auto;margin-top: 10px;">';
                
<? if($BottomCommon == 'W' || $BottomCommon ==""){  ?>
                validHtml += '为便于您更好的使用系统<br />请现在前往 ›› <a href="my.php?m=qualification" style="color:#33a676;font-size:16px">上传资质文件</a>';
                
<? }else if($BottomCommon == 'F'){  ?>
                validHtml = validHtml.replace('wenjian.jpg', 'cha.jpg');
                validHtml += '您的企业资料审核未通过<br />请现在前往 ›› <a href="my.php?m=qualification" style="color:#33a676;font-size:16px">更新企业资质文件</a>';
                
<? }else{  ?>
                validHtml += '您所提交的企业资质正在审核中，请耐心等待';
                
<? }  ?>
                validHtml += '</div>';

                layer.open({
                        type : 1,
                        title: '提示信息',
                        area: ['390px', '170px'],
                        content: validHtml,
                        cancel: function(){

                                
<? echo (strpos($_SERVER['SCRIPT_NAME'], 'cart.php') === false) ? '' : "window.location = 'home.php?isin';";  ?>
                        }
                });
        }
</script>