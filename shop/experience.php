<?php
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
{
	header('Location: http://sj.dhb.hk/');
	exit;
}

include_once ('common.php');

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

$sOldIndustry_experience = isset($_GET['industry']) ? trim($_GET['industry']) : '';
$sIndustry_experience = getSafeIndustry($sOldIndustry_experience);
if(empty($sIndustry_experience) || !in_array($sIndustry_experience,$EXPERIENCE_INDUSTRY)){
	echo '<script language="javascript">window.location.href="'.OFFICE_SITE.'";</script>';
	exit();
}

if(!empty($_SESSION['cc']['cid']) && !empty($_SESSION['ucc']['CompanyID']) && !empty($_SESSION['industry'])){
	$db	= dbconnect::dataconnect()->getdb();
	$upsql =  "select ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientMobile,ClientAdd,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientPay,ClientFlag from ".DATATABLE."_order_client where ClientID=".$_SESSION['cc']['cid']." and ClientCompany = ".$_SESSION['ucc']['CompanyID']." and ClientFlag=0 limit 0,1";	
	$cinfo = $db->get_row($upsql);

	$_SESSION['cc']['clevel']		= $cinfo['ClientLevel'];
	$_SESSION['cc']['csetshield']	= $cinfo['ClientShield'];
	$_SESSION['cc']['csetprice']	= $cinfo['ClientSetPrice'];
	if(empty($_SESSION['cc']['csetprice'])) $_SESSION['cc']['csetprice'] = "Price2";
	$_SESSION['cc']['csetpercent']	= $cinfo['ClientPercent'];
	if(empty($_SESSION['cc']['csetpercent'])) $_SESSION['cc']['csetpercent'] = '10.0';
	if(!empty($cinfo['ClientBrandPercent'])) $_SESSION['cc']['cbrandpercent'] = unserialize($cinfo['ClientBrandPercent']);
	
	$_SESSION['cc']['cclientpay']	= $cinfo['ClientPay'];
	$_SESSION['cc']['cflag']		= $cinfo['ClientFlag'];

	if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
	{
		$_SESSION['cc']['clevel'] = "A_".$cinfo['ClientLevel'];
	}

	// 判断行业是否相同
	$sOldIndustry2_experience = $_SESSION['industry'];
	$sIndustry2_experience = getSafeIndustry($sOldIndustry2_experience);

	if(!empty($sIndustry2_experience) && in_array($sIndustry2_experience,$EXPERIENCE_INDUSTRY) && $sIndustry2_experience == $sIndustry_experience){
		header("Location: /home.php");
	}
}
$input		= new Input;
$in			= $input->parse_incoming();
$in			= $input->_htmlentities($in);

if(!empty($in['c']))
{
	$companyflag = $in['c'];
}else{
	$urlstar = substr($_SERVER['SERVER_NAME'],0,strpos($_SERVER['SERVER_NAME'],'.'));
	if(!empty($urlstar) && $urlstar != 'c' && $urlstar != 'tc'  && $urlstar != 'ty' && $urlstar != 'tdh'){
		$companyflag = $urlstar;
	}else{
		$companyflag = '';
	}
}

if(DHB_DEVELOPMENT_MODE === 'development'){
	$companyflag = 'rsung';
}

if(!empty($companyflag))
{
	$db	= dbconnect::dataconnect()->getdb();
	if(!is_filename($companyflag)) exit('输入网址有误！<a href="http://tdh.dhb.net.cn">返回客户端</a>');
	$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyLogo,CompanyLogin,CompanyFlag from ".DATABASEU.DATATABLE."_order_company where CompanyPrefix = '".$companyflag."' limit 0,1");
	if(!empty($ucinfo['CompanyID']))
	{		
		$buttoninfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$ucinfo['CompanyID']." and SetName='template' limit 0,1");

		$reginfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$ucinfo['CompanyID']." and SetName='product' limit 0,1");
		if(!empty($reginfo['SetValue'])) $reginfoarr = unserialize($reginfo['SetValue']);

		 $settmsg = @file_get_contents(RESOURCE_PATH.$ucinfo['CompanyID']."/config.txt?r=".rand(1,100));	
		 if(!empty($settmsg)) $setarr = unserialize($settmsg);
		 if(!empty($setarr['template'])) $sv = $setarr['template']; else $sv = 'blue';
	}else{
		exit('输入网址有误！<a href="http://tdh.dhb.net.cn">返回客户端</a>');
	}
}
?>
<!doctype html>
<html>
<head>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php if(empty($ucinfo['CompanyName'])) echo '订货宝 '; else echo $ucinfo['CompanyName']; ?>  网上订货系统 在线订货系统 在线订单管理系统 网上订货平台第一品牌 经销商登录</title>
<meta name="keywords" content="订货宝 商超配送 微信订货系统 订货管理系统 订单管理系统 在线订单管理系统 在线订货系统  网上订货系统 网络订货系统 网上订货平台 在线订货平台 订货软件 分销系统 " />
<meta name="description" content="订货宝订货管理系统(免费咨询:400-6311-682)是为贸易或生产企业开发的网上订单管理系统,利用”云计算“技术,实现供货商与经销商之间实时订货,收货,发货,库存管理,收付款对帐管理,物流信息查询,安全的在线支付,订单短信通知,在线客服等全面高效的订货流程管理,提升企业管理竞争力." />
<meta name="Contact" content="support@rsung.com" />
<link href="./images/main.css?v=83<?php echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="./template/js/jquery.js" type="text/javascript"></script>
<script src="./template/js/login_experience.js?v=55<?php echo VERID;?>" type="text/javascript"></script>

<style type="text/css">
html,body{ height:100%; margin:0; padding:0; font-size:14px;}
.experience_mask{height:100%; width:100%; position:fixed; _position:absolute; top:0; z-index:1000; }
.experience_opacity{ opacity:0.5; filter: alpha(opacity=30); background-color:#000; }
.experience_content{height:170px; width:400px; overflow:auto; border:2px solid #ccc;border:none; background-color:translateparent; position:absolute; top:50%; left:50%; margin:-160px auto auto -300px; z-index:1001; word-wrap: break-word; padding:15px;}
.experience_content .login_time{background:#ebebee; background:none;font-size:20px;font-weight:bold;color:#0cf5ab;margin-top:15px;text-align:center;line-height:45px;height:35px;padding-bottom:20px; }
.experience_content #time_count{color:red;font-size:100px;position:relative;bottom:-6px;}
</style>

<script type="text/javascript"> 
var mycookie = document.cookie;
function readcookie(name) 
{ 
	var start1 = mycookie.indexOf(name + "="); 
	if (start1== -1){ 
		value = '<?php if(!empty($companyflag)) echo $companyflag."-";?>体验用户';
		document.getElementById('user_login').value = value;
		return value;
	}
	else 
	{ 
		start=mycookie.indexOf("=",start1)+1;  
		var end = mycookie.indexOf(";",start); 
		if (end==-1) 
		{ 
			end = mycookie.length;
		} 
		var value=unescape(mycookie.substring(start,end)); 
		if (value==null) 
		{
			value = '<?php if(!empty($companyflag)) echo $companyflag."-";?>';
		}

		document.getElementById('user_login').value = value;
		//document.getElementById('user_login').focus();
		return value;
	} 
}

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
            pwdField.after('<input id="pwdPlaceholder" tabindex="2" class="UI_text1" type="text" value='+pwdVal+' autocomplete="off" />');
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

$(document).ready(function() {
    // 倒计时
    var timeNum = <?php echo MAX_WAIT_EXPERIENCE;?>;
    function changeTime(){
        if (timeNum ==0 ){ 
            // 到时间后的操作
            clearInterval(changeTime);
            $('#wp-submit').val('正在登录...');
            loginin();
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
</head>

<body onload="readcookie('CompanyPrefix');">
<div class="experience_mask experience_opacity"></div>
<div class="experience_content">
	<div class="login_time"><span id="time_count"><?php echo MAX_WAIT_EXPERIENCE;?></span> 秒后自动登录...</div>
</div>

<!--[if IE 6]>
<script type="text/javascript" src="images/DD_belatedPNG.js" ></script>

<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->

<div class="location">
<!--内容开始-->
  <div class="content">
  <!--logo开始-->
  	<div class="top">
  		<div class="logo">
    		<div class="logo_info">
			<?php 
			if(!empty($ucinfo['CompanyLogo']))
			{
				echo '<img src="'.RESOURCE_PATH.$ucinfo['CompanyLogo'].'" alt="'.$ucinfo['CompanyName'].' - 网上订货系统"  />';
			}else{
				echo '<a href="http://www.dhb.hk" target="_blank" title="订货宝 网上订货系统"><img src="./template/industry/logo_'.$sIndustry_experience.'.jpg" alt="订货宝 - 网上订货系统" width="300" height="73" /></a>';
			}
			?>
			</div>
        </div>
    </div>
	<!--logo结束-->
  		<div class="main_bg_0">
  		<div class="main_bg<?php if($sv != 'default') echo ' main_bg_'.$sv;?>">
        	<div class="main">
            	<div class="banner">
				<?php
				if(!empty($ucinfo['CompanyLogin']))
				{
					echo '<img src="'.RESOURCE_PATH.$ucinfo['CompanyLogin'].'" alt="用了订货宝，订货管理没烦恼!" />';
				}else{
					echo '<img src="./template/industry/banner_'.$sIndustry_experience.'.jpg" alt="用了订货宝，订货管理没烦恼!" width="505" height="330" />';
				}
				?>
				</div>  
                <!--登录开始-->
    		    <div class="login">
            	<div class="login_info">
					<form name="loginform" id="loginform"  method="post">
                	<div class="login_title">
                    	<h1><?php if(empty($ucinfo['CompanyName'])) echo '<span>订货宝</span> - 网上订货系统（订货入口）'; else echo $ucinfo['CompanyName'].' - 网上订货系统'; ?></h1>
                    </div>
                    <div class="login_info_text UI_textbox1" title="请输入帐号" >
                    	<input name="log" tabindex="1" class="UI_text1" id="user_login" type="text" maxlength="30" autocomplete="off" placeholder="请输入账号" readonly="true" value="xx" style="background:#e8e7e7;" />
                    </div>
                     <div class="login_info_text UI_textbox2" title="请输入密码" >
                    	<input name="pwd" tabindex="2" class="UI_text1" id="user_pass" type="password" maxlength="30" autocomplete="off" placeholder="请输入密码" value="●●●" readonly="true" style="background:#e8e7e7;" />
                    </div>
                     <div class="login_info_text UI_textbox3" title="请输入验证码" >
                    	<input name="vc" tabindex="3" class="UI_text2" id="user_vc" type="text" maxlength="5" autocomplete="off" placeholder="请输入验证码" value="" readonly="true" style="background:#e8e7e7;" />
                    </div>
                    <div class="yzm"><a tabindex="-1" style="border-style: none" href="#" title="点击 刷新验证码" onclick="document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random(); return false"><img id="siimage" align="left" src="./plugin/securimage/checkcodeimg.php?sid=<script languange=javascript>Math.random();</script>" title="点击 刷新验证码" border="0" onclick="this.blur()"   /></a></div>
                     <div class="UI_btn">
                    	<input tabindex="4" class="ui-button" name="wp-submit" id="wp-submit" type="button" value="登 录" seed="authcenter-submit-index" />
                    </div>
                    <div class="zhanghao"> 
                    	<input type="hidden" name="cp" id="cp" value="<?php echo isset($_GET['cp']) ? trim($_GET['cp']) : '';?>" />
                    	<input type="hidden" name="industry" id="industry" value="<?php echo $sOldIndustry_experience;?>" />
                   		<a href="/qq/qq_login.php" title="QQ 同步登录"><div class="QQbtn"></div></a>
						<?php if(!empty($companyflag) && $reginfoarr['regiester_type'] == "on"){?>
						<a href="/client_regiester.php?c=<?php echo passport_encrypt($companyflag,ENCODE_KEY);?>" title="我还没有帐号，马上注册" class="zhzc" >注册帐号</a>
						<?php }else{?>
						<a href="javascript:alert('请与您的供货商联系，重置密码！')" title="找回密码" class="zhzc" >忘记密码？</a>
						<?php }?>
	                </div>
					<div class="warning" id="warning">正在执行，请稍候...</div>
					</form>
                </div>           
            </div>
           	<!--登录结束-->
                
        </div>
        </div>
        </div>
          <!--底部开始-->
        <div class="bott">
        	<div class="bott_info1">
            	<div class="dhlc"></div>
                <div class="ewmW"><?php
				if(file_exists("./resource/".$ucinfo['CompanyID']."/wx.jpg")){
					echo '<img src="./resource/'.$ucinfo['CompanyID'].'/wx.jpg" width="86" height="86" />微信端';
				}else{
					echo '<img src="http://www.dhb.hk/qr-code/wxd.png" width="86" height="86" />订货宝微信端';
				}
				?></div>
                <div class="ewmC">				
				<img src="http://www.dhb.hk/qr-code/app.png" width="86" height="86" />订货宝 APP</div>
            </div>            
        </div>
        <div class="bott_info2">
		<?php
		if(empty($buttoninfo['SetValue']))
		{
		?>
		<p align="center">Powered By Rsung DingHuoBao (<a href="http://www.dhb.hk" target="_blank" title="订货宝官网">WWW.DHB.HK </a>) System © 2005 - <?php echo date('Y');?> <a href="http://www.rsung.com" target="_blank">Rsung</a> Ltd.   <br />使用 <a href="http://rj.baidu.com/soft/detail/14744.html?ald" target="_blank">Chrome</a> 或 <a href="http://www.firefox.com.cn/" target="_blank">Firefox</a> 浏览器访问本系统会获得最佳体验 , 备用网址：http://c.dhb.net.cn/<?php if(!empty($companyflag)) echo $companyflag.'/';?></p>
		<?php }else{ echo html_entity_decode($buttoninfo['SetValue'], ENT_QUOTES,'UTF-8'); }?>
		<div id="desk" align="center"><a href="/desktop.php" >保存到桌面↓</a></div>
        </div>
        <!--底部结束-->
	</div>
</div>
<!--<div style="display:none;"><script language="javascript" src="http://count8.51yes.com/click.aspx?id=89359585&logo=12" charset="gb2312"></script></div>-->
</body>
</html>