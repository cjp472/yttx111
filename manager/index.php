<?php include_once ("header.inc.php");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="renderer" content="webkit">
<title>医统天下 管理平台登录</title>

<script src="scripts/jquery.min.js" type="text/javascript"></script>
<script src="m/js/login.js?20160908" type="text/javascript"></script>

<script src="scripts/jquery-1.8.3.min.js" type="text/javascript"></script>
<link href="m/css/me.css" rel="stylesheet" type="text/css" />
<link href="images/main.css?20160908" rel="stylesheet" type="text/css" />


<style type="text/css">
   *{margin: 0;padding: 0;  }
	html,body{width:100%;height:100%;}
	input::input-placeholder{color:  #bdbdbd ;}
	::-webkit-input-placeholder { /* WebKit browsers */ 
		color: #d4d4d4 !important;
		font-size: 15px; 
	} 
	:-moz-placeholder { /* Mozilla Firefox 4 to 18 */ 
		color: #d4d4d4 !important; 
		font-size: 15px; 
	} 
	::-moz-placeholder { /* Mozilla Firefox 19+ */ 
		color: #d4d4d4 !important;
		font-size: 15px;  
	} 
	:-ms-input-placeholder { /* Internet Explorer 10+ */ 
		color: #d4d4d4 !important; 
		font-size: 15px; 
	} 

    .forget_password>div { color: #ffffff;}
    .forget_password:hover>div { color : #FF0000;}
	
	@keyframes changeBG{
		0 {background-position: 100% 100%;}
		20% {background-position: 90% 100%;}
		40% {background-position: 80% 100%;}
		60% {background-position: 70% 100%;}
		80% {background-position: 60% 100%;}
		100% {background-position: 50% 100%;}
	}
	@-webkit-keyframes changeBG{
		0 {background-position: 100% 100%;}
		20% {background-position: 90% 100%;}
		40% {background-position: 80% 100%;}
		60% {background-position: 70% 100%;}
		80% {background-position: 60% 100%;}
		100% {background-position: 50% 100%;}
	}
	@keyframes road{
		0 {background-position: 100% 100%;}
		20% {background-position: 80% 100%;}
		40% {background-position: 60% 100%;}
		60% {background-position: 40% 100%;}
		80% {background-position: 20% 100%;}
		100% {background-position: 0% 100%;}
	}
	@-webkit-keyframes road{
		0 {background-position: 100% 100%;}
		20% {background-position: 80% 100%;}
		40% {background-position: 60% 100%;}
		60% {background-position: 40% 100%;}
		80% {background-position: 20% 100%;}
		100% {background-position: 0% 100%;}
	}
@font-face {font-family: "iconfont";
  src: url('//at.alicdn.com/t/font_423685_icqz9k3f9ulbx1or.eot?t=1508294538156'); /* IE9*/
  src: url('//at.alicdn.com/t/font_423685_icqz9k3f9ulbx1or.eot?t=1508294538156#iefix') format('embedded-opentype'), /* IE6-IE8 */
  url('data:application/x-font-woff;charset=utf-8;base64,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') format('woff'),
  url('//at.alicdn.com/t/font_423685_icqz9k3f9ulbx1or.ttf?t=1508294538156') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/
  url('//at.alicdn.com/t/font_423685_icqz9k3f9ulbx1or.svg?t=1508294538156#iconfont') format('svg'); /* iOS 4.1- */
}

.iconfont {
  font-family:"iconfont" !important;
  font-size:16px;
  font-style:normal;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}


.icon-suo:before { content: "\e616"; }

.icon-zhanghao:before { content: "\e602"; }
 body,input{font:menu}

</style>
<!--[if lt IE 9]>

   <style type="text/css">

   .login_info{
      	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#7fffffff,endColorstr=#7fffffff);
    }

    </style>

<![endif]-->
<script src="scripts/app_mian.js" type="text/javascript"></script>
<script type="text/javascript">
//判断浏览器是否支持 placeholder属性
function isPlaceholder(){
    var input = document.createElement('input');
    return 'placeholder' in input;
}

if (!isPlaceholder()) {//不支持placeholder 用jquery来完成
    $(document).ready(function() {
        if(!isPlaceholder()){
            $("input").not("input[type='password']").each(//把input绑定事件 排除password框
                function(){
                    if($(this).val()=="" && $(this).attr("placeholder")!=""){
                        $(this).val($(this).attr("placeholder"));
                        $(this).focus(function(){
                            if($(this).val()==$(this).attr("placeholder")) $(this).val("");
                        });
                        $(this).blur(function(){
                            if($(this).val()=="") $(this).val($(this).attr("placeholder"));
                        });
                    }
            });
            //对password框的特殊处理1.创建一个text框 2获取焦点和失去焦点的时候切换
            var pwdField    = $("input[type=password]");
            var pwdVal      = pwdField.attr('placeholder');
            pwdField.after('<input id="pwdPlaceholder" tabindex="2" class="UI_text1" type="text" value='+pwdVal+' autocomplete="off" style="*padding-bottom:4px;" />');
            var pwdPlaceholder = $('#pwdPlaceholder');
            pwdPlaceholder.show();
            pwdField.hide();

            pwdPlaceholder.focus(function(){
                pwdPlaceholder.hide();
                pwdField.show();
                pwdField.focus();
            });

            pwdField.blur(function(){
                if(pwdField.val() == '') {
                    pwdPlaceholder.show();
                    pwdField.hide();
                }
            });

        }
    });
}
</script>
<!--[if IE 6]>
<script type="text/javascript" src="images/DD_belatedPNG.js" ></script>

<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->
</head>

<body>
	<div class="login">
		<img src="images/login_logo.png" class="login_logo"/>
		<img src="images/login_intro.png" class="login_intro"/>
		<div class="road">
			<img src="images/login_car.gif" class="car"/>
		</div>
        <div class="login_info" >
           <!-- <div class="warning" id="warning">正在执行，请稍候...</div>-->
    		<form name="loginform" id="loginform" method="post" onsubmit="return loginto();">

                <div class="UI_textbox1" title="请输入帐号" >
                	<span class="iconfont icon-zhanghao"></span>
                    <input name="user_login" tabindex="1" class="UI_text1" id="user_login" type="text" autocomplete="off" placeholder="请输入帐号"/>     
                </div>

				<div class="login_reg all_center_box">
			      	<span class="iconfont icon-suo"></span>
			     	<input name="" type="password" tabindex="2" id="user_pass" class="input_text_password mima_dd " placeholder="请输入密码"">
				</div>
                <div class="UI_textbox3" title="请输入验证码" style="">
                	<input name="user_vc" tabindex="3" class="UI_text2" id="user_vc" type="text" autocomplete="off" placeholder="请输入验证码"  style="font-size:14px;/>
					<span>
						<a tabindex="-1" style="border-style: none" href="#" title="点击 刷新验证码"> <!--onclick="this.blur()"-->
							<img id="siimage" align="left" style="width: 37.8906%;height: 100%;float:right;border: 0" src="./plugin/securimage/checkcodeimg.php?sid=<script type=text/javascript>Math.random();</script>" title="点击 刷新验证码" border="0" onclick="document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random(); return false"/>
						</a>
					</span>
                </div>
                <div class="warning" id="warning">正在执行，请稍候...</div>
                <div class="UI_btn" style="">
                	<input class="ui-button" id="J-login-btn" type="submit" value="登 录" seed="authcenter-submit-index" />
					<input type="hidden" name="redirect_to" value="http://m.dhb.hk" />
				</div>
    		</form>
		</div>
    </div>



<script type="text/javascript">
try{
	document.getElementById('user_login').focus();
	document.getElementById('user_login').values = '';
	document.getElementById('user_vc').values = '';
}catch(e){}
</script>
</body>
</html>
