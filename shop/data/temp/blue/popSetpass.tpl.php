<style type="text/css">
.layer_number>p{

position: relative;
}
.icon-duigou{
margin-top: 8px;
font-size: 26px;
color: #01a157;
position: absolute;
top: 0;
right: 2px;

display: none;
}
</style>
<div class="layer" id="pay-password-set" style="display: none;">
<i class="iconfont  icon-weibiaoti101 close"></i>
<p class="layer_title">
请设置您的医统账期独立支付密码
</p>
<p class="layer_tel">
<i class="iconfont icon-shouji sameIcon"></i>
<input type="text" placeholder="请输入您的手机号" id="credit_mobile" name="credit_mobile" maxlength="11"/>

</p>
<div class="layer_number">
<p>
<i class="iconfont icon-yanzhengma sameIcon"></i>
<input type="text" placeholder="请输入4位验证码" class="textPass" id="valicode" />
<i class="iconfont icon-duigou"></i>
</p>
<input type="button" name="getNum" value="获取验证码" class="getNum" id="getNum">
</div>
<p class="set_key">
<i class="iconfont icon-zhifumima sameIcon"></i>
<input type="password" placeholder="设置6位数字支付密码" name="pay_pwd" id="pay_pwd" class="pay_password1"/>
<input type="text" placeholder="设置6位数字支付密码" name="pay_pwd" id="pay_pwd" class="pay_text1 hide"/>
<i class="iconfont icon-wodezhangdan close1"></i>
<i class="iconfont icon-yanjing open1"></i>
</p>
<p class="set">
<i class="iconfont icon-zhifumima sameIcon"></i>
<input type="password" placeholder="请再次输入支付密码" name="pay_pwd1" id="pay_pwd1" class="pay_password2"/>
<input type="text" placeholder="请再次输入支付密码" name="pay_pwd1" id="pay_pwd1" class="pay_text2 hide"/>
<i class="iconfont icon-wodezhangdan close2"></i>
<i class="iconfont icon-yanjing open2"></i>
</p>
<p class="notice setpassPop" id="set_notice"></p>
<input type="button" value="确认" class="reset_sure button_sure">
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
$('.close1').click(function(){
check('close1','open1','pay_password1','pay_text1');
});
$('.open1').click(function(){
check('open1','close1','pay_text1','pay_password1');
});
$('.close2').click(function(){
check('close2','open2','pay_password2','pay_text2');
});
$('.open2').click(function(){
check('open2','close2','pay_text2','pay_password2');
});
function check(nodeBtn1,nodeBtn2,node1,node2){
$("."+nodeBtn1).hide();
$("."+nodeBtn2).show();
$('.'+node1).addClass('hide');

$('.'+node2).removeClass('hide').val($('.'+node1).val());
console.log($('.'+node2).val());
console.log($('.'+node1).val());
}
/*		$('.reset_sure').click(function(){
 var noticeBox	= $('#set_notice'),
 	 eleMobile 	= $("#credit_mobile"),
 	 eleValiCode = $("#valicode"),
 	 elePayPwd	= $("#pay_pwd"),
 	 elePayPwd1 = $("#pay_pwd1");

     var mobile		= eleMobile.val(),
     	 valicode	= eleValiCode.val(),
     	 pay_pwd	= elePayPwd.val(),
     	 pay_pwd1	= elePayPwd1.val();
     
     noticeBox.html('');
     if(!mobile){
    	 noticeBox.html('为保障您的资金安全，请验证安全手机号码');
    	 return;
     }else if(!valicode){
    	 noticeBox.html('请填写您收到的短信验证码');
    	 return;
     }else if(!pay_pwd || pay_pwd.length != 6 || isNaN(pay_pwd)){
    	 noticeBox.html('请设置6位数字支付密码');
    	 return;
     }else 
    	 if(pay_pwd != pay_pwd1){
    	 noticeBox.html('两次输入密码不一致');
    	 return;
     }else if(pay_pwd == '123456' || pay_pwd == '654321' || pay_pwd == '111111'){//todo
    	 noticeBox.html('不能使用连续数字，如：123456或654321或111111');
    	 return;
     }
     
     $(this).attr("disabled", true).addClass('dianji');
     $.post('my.php', {m:'PaypwdAdd', mobile : mobile, valicode: valicode, pay_pwd:pay_pwd, pay_pwd1:pay_pwd1}, function(response){

    	 if(response['status'] == 'success'){//成功
    		noticeBox.html(response['message']);
    		setTimeout(function(){ window.location.href = 'my.php?m=credit' }, 1000);
    	}else{
    		noticeBox.html(response['message']);
    		eleMobile.removeAttr("disabled").removeClass('global-gray');
    	$(this).removeAttr("disabled").removeClass('dianji');
    	}
     }, 'json');
});*/
if($.cookie("seconds")){  
            var seconds = $.cookie("seconds");  
            var btn = $('#getNum');  
            btn.val(seconds+'秒后可重新获取').attr('disabled',true).addClass('global-gray');  
            var resend = setInterval(function(){  
                seconds--;  
                if (seconds > 0){  
                    btn.val(seconds+'秒后可重新获取').attr('disabled',true);  
                    $.cookie("seconds", seconds, {path: '/', expires: (1/86400)*seconds});  
                }else {  
                    clearInterval(resend);  
                    btn.val("获取验证码").removeClass('disabled').removeAttr('disabled');  
                }  
            }, 1000);  
        }  

//获取验证码
$("#getNum").click(function(){
getSecurityCode('credit_mobile', 'getNum', 'set_notice');
});

$('.reset_sure').click(function(){
$('#set_notice').css({
"display":"block"
})
 setTimeout(function(){
     	$('#set_notice').css({
"display":"none"
})
     },5000)
 var noticeBox	= $('#set_notice'),
 	 eleMobile 	= $("#credit_mobile"),
 	 eleValiCode = $("#valicode"),
 	 elePayPwd	= $("#pay_pwd"),
 	 elePayPwd1 = $("#pay_pwd1");

     var mobile		= eleMobile.val(),
     	 valicode	= eleValiCode.val(),
     	 pay_pwd	= elePayPwd.val(),
     	 pay_pwd1	= elePayPwd1.val();
     
     noticeBox.html('');
     if(!mobile){
    	 noticeBox.html('为保障您的资金安全，请验证安全手机号码');
    	 return;
     }else if(!valicode){
    	 noticeBox.html('请填写您收到的短信验证码');
    	 return;
     }else if(!pay_pwd || pay_pwd.length != 6 || isNaN(pay_pwd)){
    	 noticeBox.html('请设置6位数字支付密码');
    	 return;
     }else if(pay_pwd != pay_pwd1){
    	 noticeBox.html('两次输入密码不一致');
    	 return;
     }else if(pay_pwd == '123456' || pay_pwd == '654321' || pay_pwd == '111111'){//todo
    	 noticeBox.html('不能使用连续数字，如：123456或654321或111111');
    	 return;
     }
     
     $(this).attr("disabled", true).addClass('dianji');
     $.post('my.php', {m:'PaypwdAdd', mobile : mobile, valicode: valicode, pay_pwd:pay_pwd, pay_pwd1:pay_pwd1}, function(response){

    	 if(response['status'] == 'success'){//成功
    		noticeBox.html(response['message']);
    		setTimeout(function(){ window.location.href='order.php?id=<?=$oinfo['OrderSN']?>&autopay=true'}, 1000);
    	}else{
    		noticeBox.html(response['message']);
    		eleMobile.removeAttr("disabled").removeClass('global-gray');
    	$(this).removeAttr("disabled").removeClass('dianji');
    	}
     }, 'json');
    
});

/*验证码*/
$('#valicode').blur(function(){
var _this = $(this);
var code = _this.val();

$('#set_notice').html('');
if(code.length == 4 && !isNaN(code)){//4位数字才验证
$.get('my.php', {m: 'validatePayCode', code: code}, function(response){

if(response['status'] == 'success'){
//_this.attr("disabled", true).addClass('global-gray');
//						$("#valid-status").html('正确');
$(".icon-duigou").show();
}else{
$('#set_notice').html(response['message']);
}
}, 'json')
}else if(code != ''){
$('#set_notice').html('请输入正确的短信验证码');
}
})

$(".close").click(function(){
layer.closeAll();
})

</script>
