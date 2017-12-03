<?php 

include_once ("header.inc.php");
include_once (SITE_ROOT_PATH."/experience/common.php");

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

$sOldIndustry_experience = isset($_GET['industry']) ? trim($_GET['industry']) : '';
$sIndustry_experience = getSafeIndustry($sOldIndustry_experience);
if(empty($sIndustry_experience) || !in_array($sIndustry_experience,$EXPERIENCE_INDUSTRY)){
	echo '<script language="javascript">window.location.href="'.OFFICE_SITE.'";</script>';
	exit();
}

/**
 * 已经登录了，直接跳转到对应的
 */
if(!empty($_SESSION['uinfo']['userid']) && !empty($_SESSION['uc']['CompanyID']) && !empty($_SESSION['industry'])){
	
	// 判断行业是否相同
	$sOldIndustry2_experience = $_SESSION['industry'];
	$sIndustry2_experience = getSafeIndustry($sOldIndustry2_experience);

	if(!empty($sIndustry2_experience) && in_array($sIndustry2_experience,$EXPERIENCE_INDUSTRY) && $sIndustry2_experience == $sIndustry_experience){
		if($_SESSION['uinfo']['UserType']=="S")
		{
			echo '<script language="javascript">window.location.href="/s/order.php";</script>';
		}else{
			echo '<script language="javascript">window.location.href="/m/home.php";</script>';
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>订货宝 订货管理系统 微信订货 网上订货系统 在线订货系统 在线订单管理系统 网上订货平台第一品牌 管理平台登录</title>
<meta name="keywords" content="订货宝 微信订货 订货管理系统 订单管理系统 在线订单管理系统 在线订货系统  网上订货系统 网络订货系统 网上订货平台 在线订货平台 订货软件 分销系统 在线软件 商品批发 " />
<meta name="description" content="订货宝订货管理系统(免费咨询:400-6311-682)是为贸易或生产企业开发的网上订单管理系统,利用”云计算“技术,实现供货商与经销商之间实时订货,收货,发货,库存管理,收付款对帐管理,物流信息查询,安全的在线支付,订单短信通知,在线客服等全面高效的订货流程管理,提升企业管理竞争力.  " />
<meta name="Contact" content="support@rsung.com" />
<script src="scripts/jquery.min.js" type="text/javascript"></script>
<script src="m/js/login_experience.js?v=20140117" type="text/javascript"></script>
<link href="images/main.css?v=20141203" rel="stylesheet" type="text/css" />
<style type="text/css">
html,body{ height:100%; margin:0; padding:0; font-size:14px;}
.experience_mask{height:100%; width:100%; position:fixed; _position:absolute; top:0; z-index:1000; }
.experience_opacity{ opacity:0.5; filter: alpha(opacity=30); background-color:#000; }
.experience_content{height:170px; width:400px; overflow:auto; border:2px solid #ccc;border:none; background-color:translateparent; position:absolute; top:50%; left:50%; margin:-160px auto auto -300px; z-index:1001; word-wrap: break-word; padding:15px;}
.experience_content .login_time{background:#ebebee; background:none;font-size:20px;font-weight:bold;color:#0cf5ab;margin-top:15px;text-align:center;line-height:45px;height:35px;padding-bottom:20px; }
.experience_content #time_count{color:red;font-size:100px;position:relative;bottom:-6px;}
</style>

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
              
        };
    });
}

$(document).ready(function() {
    // 倒计时
    var timeNum = <?php echo MAX_WAIT_EXPERIENCE;?>;
    function changeTime(){
        if (timeNum ==0 ){ 
            // 到时间后的操作
            clearInterval(changeTime);
            $('#J-login-btn').val('正 在 登 录...');
            loginto();
            timeNum--;
        }else if(timeNum<0){

        }else{
        	timeNum--;
        	$('#time_count').text(timeNum);
       	}
    }
    setInterval(changeTime,1000);
});
</script>
<!--[if IE 6]>
<script type="text/javascript" src="images/DD_belatedPNG.js" ></script>

<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->
</head>

<body>

<div class="experience_mask experience_opacity"></div>
<div class="experience_content">
	<div class="login_time"><span id="time_count"><?php echo MAX_WAIT_EXPERIENCE;?></span> 秒后自动登录...</div>
</div>


	<div class="top">
    	<div class="top_info">
    		<div class="logo" ><a href="http://www.dhb.hk/" title="订货宝 - 订货管理系统" target="_blank"><img src="images/logo.jpg" /></a></div>
        	<div class="tel" title="服务热线：400 6311 682"></div>
        </div>
    </div>

    <div class="banner" style="background-image:url(images/banner_99.jpg?v=3);">
    	<div class="login0">
    		<div class="login">
            	<div class="login_info">
				<form name="loginform" id="loginform"  method="post">
                	<div class="login_title">
                    	<h1>订货宝 - 订货管理平台（管理入口）</h1>
                    </div>
                    <div class="UI_textbox1" title="请输入帐号" >
                    	<input name="user_login" tabindex="1" class="UI_text1" id="user_login" type="text" autocomplete="off" placeholder="请输入帐号" readonly="true" value="试用登录" style="background:#e8e7e7;*padding-bottom:4px;" />
                    </div>
                     <div class="UI_textbox2" title="请输入密码" >
                    	<input name="user_pass" tabindex="2" class="UI_text1" id="user_pass" type="password" autocomplete="off" placeholder="请输入密码" readonly="true" value="●●●" style="background:#e8e7e7;*padding-bottom:4px;" />
                    </div>
                     <div class="UI_textbox3" title="请输入验证码" >
                    	<input name="user_vc" tabindex="3" class="UI_text2" id="user_vc" type="text" autocomplete="off" placeholder="请输入验证码" readonly="true" value="" style="background:#e8e7e7;" />
						<span><a tabindex="-1" style="border-style: none" href="#" title="点击 刷新验证码" onclick="document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random(); return false">
						<img id="siimage" align="left" style="padding-right: 5px; border: 0" src="./plugin/securimage/checkcodeimg.php?sid=<script type=text/javascript>Math.random();</script>" title="点击 刷新验证码" border="0" onclick="this.blur()"  /></a>
						</span>
                    </div>
                     <div class="UI_btn">
                     	<input type="hidden" name="cp" id="cp" value="<?php echo isset($_GET['cp']) ? trim($_GET['cp']) : '';?>" />
						<input type="hidden" name="industry" id="industry" value="<?php echo $sOldIndustry_experience;?>" />
                    	<input tabindex="4" class="ui-button" id="J-login-btn" type="button" value="登  录" seed="authcenter-submit-index" />
						<input type="hidden" name="redirect_to" value="http://m.dhb.hk" />
					</div>
					<a href="qq/qq_login.php"><div class="QQbtn"></div></a>
					<div class="warning" id="warning">正在执行，请稍候...</div> 
                    </div>
				</form>
                </div>
            </div>
        </div>


    <div class="main">
    	<div class="main_info">
        	<div class="ssgx"><img src="images/ssgx.jpg" width="288" height="79" alt="实时高效" /></div>
            <div class="aqkk"><img src="images/aqkk.jpg" width="288" height="80" alt="安全可靠" /></div>
            <div class="bjgl"><img src="images/bjgl.jpg" width="305" height="80" alt="便捷管理" /></div>
        </div>
    </div>
    <div class="bottom">
    	<div class="bottom_info">
        	<div class=" bottom_info1">
            	<div class="message">
                	<h2>公告：</h2>
                    <div class="message_info">
						<script src="http://www.dhb.hk/genggao.php?m=show" type="text/javascript"></script>
                    </div>
                </div>
                <div class="hzhb">
                	<h2>合作伙伴：</h2>
                	<div class="hzhb_info">
                    	<a href="http://www.aliyun.com/" target="_blank" title="阿里云" ><div class="aly" ></div></a>
                      <a href="http://www.e-future.com.cn/"  target="_blank" title="富基融通" ><div class="fjrt"></div></a>
                        <a href="http://http://www.ibm.com/"  target="_blank" title="IBM" ><div class="ibm"></div></a>
                        <a href="http://weixin.qq.com/"  target="_blank" title="微信" ><div class="weixing"></div></a>
                        <a href="http://www.allinpay.com/"  target="_blank" title="通联支付" ><div class="sdhl"></div></a>
                    </div>
                </div>
                 <div class="ewm">
                	<h2>订货宝微信公众号</h2>
                	<div class="ewm_info"><img src="http://www.dhb.hk/qr-code/gzh.png" width="85" height="85" alt="订货宝微信公众号" /></div>
                </div>
            </div>
          <div class="bottom_info2">
           	<p>
                Powered By Rsung DingHuoBao (<a href="http://www.dhb.hk" title="订货宝 网上订货系统" target="_blank">WWW.DHB.HK</a>) System © 2005 - <?php echo date("Y");?> <a href="http://www.rsung.com"  >Rsung</a> Ltd.<br />
使用 <a href="http://www.google.cn/intl/zh-CN/chrome/browser/?installdataindex=chinabookmarkcontrol&brand=CHUN" target="_blank">Chrome</a> 或 <a href="http://www.firefox.com.cn/" target="_blank">Firefox</a> 浏览器访问本系统会获得最佳体验  <a href="desktop.php" >保存到桌面↓</a> </p>
			<div class="rz">
				<a href="http://www.anquan.org/s/www.dhb.hk?from=logo&tab=wzmp" title="安全联盟官网认证"  target="_blank"><div class="rz_1"></div></a>
                <a href="http://www.anquan.org/s/www.dhb.hk?from=logo&tab=wzmp" title="安全联盟实名认证" target="_blank"><div class="rz_2"></div></a>
				<a href="images/zzq.jpg" title="软件著作权" target="_blank"><div class="rz_7"></div></a>
                <a href="images/yjqy.jpg" target="_blank" title="双软认证" ><div class="rz_5"></div></a>
                <a href="http://www.cdnet110.com/" title="公安机关110报警求助举报服务平台" target="_blank"><div class="rz_3" ></div></a>
                <a href="http://www.cyberpolice.cn/wfjb/" title="网络违法犯罪举报" target="_blank"><div class="rz_4"></div></a>
                <a href="http://www.miibeian.gov.cn/publish/query/indexFirst.action" title="工业和信息化部ICP/IP地址/域名信息备案管理系统"  target="_blank"><div class="rz_6"></div></a>               
            </div>
            </div>
        </div>
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