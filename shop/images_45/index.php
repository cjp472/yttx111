<?php
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
{
	header('Location: http://3g.dhb.net.cn/');
	exit;
}

include_once ('common.php');
if(!empty($_SESSION['cc']['cid']) && !empty($_SESSION['ucc']['CompanyID'])) header("Location: /home.php");

$input		= new Input;
$in			= $input->parse_incoming();
$in			= $input->_htmlentities($in);

if(!empty($in['c']))
{
	$companyflag = $in['c'];
}else{
	$urlstar = substr($_SERVER['SERVER_NAME'],0,strpos($_SERVER['SERVER_NAME'],'.'));
	if(!empty($urlstar) && $urlstar != 'c' && $urlstar != 'tc'){
		$companyflag = $urlstar;
	}else{
		$companyflag = '';
	}
}

if(!empty($companyflag))
{
	$db	= dbconnect::dataconnect()->getdb();
	if(!is_filename($companyflag)) exit('输入网址有误！<a href="http://tc.dhb.net.cn">返回客户端</a>');
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
		exit('输入网址有误！<a href="http://tc.dhb.net.cn">返回客户端</a>');
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
<link href="/images/main.css" rel="stylesheet" type="text/css" />
<script src="/template/js/jquery.js" type="text/javascript"></script>
<script src="/template/js/login.js?v=<?php echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript"> 
var mycookie = document.cookie;
function readcookie(name) 
{ 
	var start1 = mycookie.indexOf(name + "="); 
	if (start1== -1){ 
		value = '<?php if(!empty($companyflag)) echo $companyflag."-";?>';
		document.getElementById('user_login').value = value;
		//document.getElementById('user_pass').focus();
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

</script>

</head>

<body >
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
				echo '<a href="http://www.dhb.hk" target="_blank" title="订货宝 网上订货系统"><img src="/images/logo.jpg" alt="订货宝 - 网上订货系统" width="256" height="90" /></a>';
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
					echo '<img src="/images/banner1.jpg" alt="用了订货宝，订货管理没烦恼!" width="505" height="330" />';
				}
				?>
				</div>  
                <!--登录开始-->
    		    <div class="login">
            	<div class="login_info">
					<form name="loginform" id="loginform"  method="post" onsubmit="return loginin();" >
                	<div class="login_title">
                    	<h1><?php if(empty($ucinfo['CompanyName'])) echo '<span>订货宝</span> - 网上订货系统（订货入口）'; else echo $ucinfo['CompanyName'].' - 网上订货系统'; ?></h1>
                    </div>
                    <div class="login_info_text UI_textbox1" title="请输入帐号">
                    	<input name="log" tabindex="1" class="UI_text1" id="user_login" type="text" maxlength="30" placeholder="请输入帐号"/>
                    </div>
                     <div class="login_info_text UI_textbox2" title="请输入密码">
                    	<input name="pwd" tabindex="2" class="UI_text1" id="user_pass" type="password" maxlength="30"  placeholder="请输入密码"/>
                    </div>
                     <div class="login_info_text UI_textbox3" title="请输入验证码">
                    	<input name="vc" tabindex="3" class="UI_text2" id="user_vc" type="text" maxlength="5" autocomplete="off" placeholder="请输入验证码"/>
                    </div>
                    <div class="yzm"><a tabindex="-1" style="border-style: none" href="#" title="点击 刷新验证码" onclick="document.getElementById('siimage').src = '/plugin/securimage/checkcodeimg.php?sid=' + Math.random(); return false"><img id="siimage" align="left" src="/plugin/securimage/checkcodeimg.php?sid=<script languange=javascript>Math.random();</script>" title="点击 刷新验证码" border="0" onclick="this.blur()"   /></a></div>
                     <div class="UI_btn">
                    	<input tabindex="4" class="ui-button" name="wp-submit" id="wp-submit" type="submit" value="登 录" seed="authcenter-submit-index" />
                    </div>
                    <div class="zhanghao">
                   		<a href="/qq/qq_login.php" title="QQ 同步登录"><div class="QQbtn"></div></a>
						<?php if(!empty($companyflag) && $reginfoarr['regiester_type'] == "on"){?>
						<a href="/client_regiester.php?PSID=<?php echo session_id();?>&c=<?php echo passport_encrypt($companyflag,ENCODE_KEY);?>" title="我还没有帐号，马上注册" class="zhzc" >注册帐号</a>
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
                <div class="ewm" title="订货宝 手机微信端"></div>
            </div>            
        </div>
        <div class="bott_info2">
		<?php
		if(empty($buttoninfo['SetValue']))
		{
		?>
		<p align="center">Powered By Rsung DingHuoBao (<a href="http://www.dhb.hk" target="_blank" title="订货宝官网">WWW.DHB.HK </a>) System © 2006 - <?php echo date('Y');?> <a href="http://www.rsung.com" target="_blank">Rsung</a> Ltd.   <br />建议您使用 IE8(及以上),Chrome,Fixfox 浏览器 , 备用网址：http://c.dhb.net.cn/<?php if(!empty($companyflag)) echo $companyflag.'/';?></p>
		<?php }else{ echo html_entity_decode($buttoninfo['SetValue'], ENT_QUOTES,'UTF-8'); }?>
        </div>
        <!--底部结束-->
	</div>
</div>
</body>
</html>