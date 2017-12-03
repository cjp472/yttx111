<div class="content_wrap" id="pay-password" style="display:none;">
<div class="content_title">请输入您的医统账期独立支付密码</div>
<i class="iconfont icon-weibiaoti101 close"></i>
<p class="pay_key">
<i class="iconfont icon-zhifumima sameIcon"></i>
<input type="password" placeholder="请输入支付密码" id="pay_pwd" name="pay_pwd" class="pay_password1"/>
<input type="text" placeholder="请输入支付密码" name="pay_pwd" id="pay_pwd" class="pay_text1 hide"/>
<i class="iconfont icon-wodezhangdan close1"></i>
<i class="iconfont icon-yanjing open1 hide"></i>
</p>
<a href="/shop/my.php?m=CreditPass" class="forgrt_pay">忘记密码？</a>
<p class="notice passPop" id="set_notice"></p>
<p class="button_sure">支付</p>
</div>

<script type="text/javascript">

$('input').focus(function() {
$('.sameIcon').css({
'color' : '#cdcdcd'
})
$(this).parent().find('i.sameIcon').css({
"color" : "#04A057"
})
})
$('input').blur(function() {
$('.icon').css({
'color' : '#cdcdcd'
})
})
/*眼睛*/
$('.close1').click(function(){
check('close1','open1','pay_password1','pay_text1')
});
$('.open1').click(function(){
check('open1','close1','pay_text1','pay_password1')
});
function check(nodeBtn1,nodeBtn2,node1,node2){
$("."+nodeBtn1).hide();
$("."+nodeBtn2).show();
$('.'+node1).addClass('hide');
$('.'+node2).removeClass('hide').val($('.'+node1).val());
}

$(".button_sure").click(function() {
var _this = $(this);

if(_this.attr('disabled') == 'disabled') return;

var pay_pwd_input = $('#pay_pwd'), set_notice = $('#set_notice'), forgrt_pay = $('.forgrt_pay');
var pwd = pay_pwd_input.val();

if (isNaN(pwd) || pwd.length != 6) {
set_notice.html('请输入6位数字密码');
return;
}

_this.attr("disabled", true);
$.post('/shop/my.php', {
m : 'PaypwdSel',
PayPassword : pwd,
orderID : <?=$oinfo['OrderID']?>,
}, function(response) {
if (response['status'] == 'error') {
_this.removeAttr("disabled");
} else if (response['status'] == 'success' || response['status'] == 'notice') {
setTimeout(function(){ window.location.href = 'myorder.php' }, 1000);
}

set_notice.html(response['message']);
if(response['message'] == '为保障资金安全平台已冻结您的账期或请重置密码' ){
forgrt_pay.html("重置密码");
}
}, 'json');

});
$(".close").click(function(){
layer.closeAll();
})
</script>
