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
<script src="template/js/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js" type="text/javascript"></script>
<script src="template/js/my.js?v=<?=VERID?>" type="text/javascript"></script>
<script type="text/javascript" src="plugin/layer/layer.js"></script>


<link rel="stylesheet" type="text/css" href="template/css/icon.css?v=<?=VERID?>"/>
<link rel="stylesheet" type="text/css" href="template/css/pop.css?v=<?=VERID?>"/>
<link rel="stylesheet" type="text/css" href="template/css/credit.css?v=<?=VERID?>"/>
<style type="text/css">
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
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>医统账期/密码管理</div>
</div>
<div class="right_product_main">
<div class="list_line">		
<div class="reset">
<p class="reset_title">重置医统账期支付密码</p>
<ul class="reset_con">
<li class="reset_tel">
<p>输入手机号：</p>
<input type="text" placeholder="请输入安全手机号 <?=$mobileReapet?>" id="credit_mobile" name="credit_mobile" />
</li>
<li class="reset_text">
<p>短信验证码：</p>
<input type="text" placeholder="请输入4位短信验证码" class="textPass" id="valicode" />
<input type="button" name="getBtn" value="获取验证码" id="getBtn">
<p id="valid-status"></p>
<i class="iconfont icon-duigou"></i>
</li>
<li class="reset_pass">
<p>输入新密码：</p>
<input type="password" placeholder="设置6位数字支付密码" name="pay_pwd" id="pay_pwd" class="pay_password1"/>
<input type="text" placeholder="设置6位数字支付密码" name="pay_pwd" id="pay_pwd" class="pay_text1 hide"/>
<i class="iconfont icon-wodezhangdan close1"></i>
<i class="iconfont icon-yanjing open1"></i>
</li>
<li class="reset_new">
<p>确认新密码：</p>
<input type="password" placeholder="请再次输入支付密码" name="pay_pwd1" id="pay_pwd1" class="pay_password2"/>
<input type="text" placeholder="请再次输入支付密码" name="pay_pwd1" id="pay_pwd1" class="pay_text2 hide"/>
<i class="iconfont icon-wodezhangdan close2"></i>
<i class="iconfont icon-yanjing open2"></i>
</li>
</ul>

<br class="clear" />
<p class="set_notice" id="set_notice"></p>
<input type="button" value="修改" class="reset_sure">


</div>
<br class="clear" />
</div>
<br />&nbsp;
</div>
</div>
</div>
<!--</div>-->
<? include template('bottom'); include template('popReset'); ?>
</body>

<script type="text/javascript">

/*眼睛*/
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
};

/*判断是否有缓存*/	
//		if($.cookie("tel") && $.cookie("seconds")){
//			$('#credit_mobile').val($.cookie("tel")).attr("disabled", true).addClass('global-gray');
//		}
//		if($.cookie("seconds")){  
//          var seconds = $.cookie("seconds");  
//          var btn = $('#getBtn');  
//          btn.val(seconds+'秒后可重新获取').attr('disabled',true).addClass('global-gray');  
//          var resend = setInterval(function(){  
//              seconds--;  
//              if (seconds > 0){  
//                  btn.val(seconds+'秒后可重新获取').attr('disabled',true);  
//                  $.cookie("seconds", seconds, {path: '/', expires: (1/86400)*seconds});  
//              }else {  
//                  clearInterval(resend);  
//                  btn.val("获取验证码").removeClass('global-gray').removeAttr('disabled');  
//              }  
//          }, 1000);  
//      };
//获取验证码
$("#getBtn").click(function(){
getSecurityCode('credit_mobile', 'getBtn', 'set_notice');
setTimeout(function(){$('#credit_mobile').removeClass('global-gray').removeAttr('disabled');},60000)
});

$('.reset_sure').click(function(){
 var noticeBox	= $('#set_notice'),
 	 eleMobile 	= $("#credit_mobile"),
 	 eleValiCode = $("#valicode"),
 	 elePayPwd	= $("#pay_pwd"),
 	 elePayPwd1 = $("#pay_pwd1");

 layer.closeAll('dialog'); //关闭信息框
 
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
     $.post('my.php', {m:'PaypwdUpdate', mobile : mobile, valicode: valicode, pay_pwd:pay_pwd, pay_pwd1:pay_pwd1}, function(response){

    	 if(response['status'] == 'success'){//成功
    		noticeBox.html(response['message']);
//		    		layer.closeAll('dialog'); //关闭信息框
    			/*重置密码成功弹窗*/

layer.open({
        	type: 1,
        	closeBtn: 0,
            title: "",
content: $('#success'),
            area: ["320px","210px"],
            shade:[0.6, '#000'],
            shadeClose: false,
            move:false
        })

    		setTimeout(function(){ window.location.href = 'my.php?m=credit' }, 1000);
    	}else{
    		noticeBox.html(response['message']);
    		eleMobile.removeAttr("disabled").removeClass('global-gray');
    	$(this).removeAttr("disabled").removeClass('dianji');
    	}
     }, 'json');
});


//
/*手机号为空时倒计时*/
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

</script>
</html>
