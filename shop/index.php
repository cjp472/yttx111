<?php
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
{
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/mobile/login.html');
	exit;
}

include_once ('common.php');
if(!empty($_SESSION['cc']['cid']) && !empty($_SESSION['ucc']['CompanyID'])){
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

	header("Location: ./home.php");
}
$input		= new Input;
$in			= $input->parse_incoming();
$in			= $input->_htmlentities($in);

if(ip2long($_SERVER['HTTP_HOST']) === false){
	if(!empty($in['c']))
	{
		$companyflag = $in['c'];
	}else{
	// 	$urlstar = substr($_SERVER['SERVER_NAME'],0,strpos($_SERVER['SERVER_NAME'],'.'));
		$urlstar = substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],'.'));
		if(!empty($urlstar) && $urlstar != 'yy' && $urlstar != 'www'){
			$companyflag = $urlstar == 'test' ? 'etong' : $urlstar;
		}else{
	// 		exit('<p align="center" style="color:#01a157;line-height:50px;font-size:20px;"><br /><img src="http://www.yitong111.com/front/images/logo.png" /><br />系统即将发布，敬请期待...</p>');
			$companyflag = '';
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
    <title><?php if(empty($ucinfo['CompanyName'])) echo '医统天下BMB集采平台 '; else echo $ucinfo['CompanyName']; ?> </title>
    <link rel="stylesheet" href="css/index.css"/>
    <link href="./images/index.css?v=<?php echo VERID;?>" rel="stylesheet" type="text/css" />
    <link href="./images/styles.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="template/red/css/iconfont.css">
    <script src="./template/js/jquery.js" type="text/javascript"></script>
    <script src="./template/js/jquery-2.1.1.min.js" type="text/javascript"></script>


	<script src="./template/js/login.js?v=<?php echo VERID;?>" type="text/javascript"></script>
	<link rel="shortcut icon" href="/favicon.ico"/>
	<link rel="bookmark" href="/favicon.ico"/>
    <style>
html{
    width: 100%;
    height: 100%;
}

        .whole{
            height: 100%;
            width: 100%;
            overflow-x:hidden;
            overflow-y:hidden;
            -moz-background-size:cover;
            -webkit-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            position: relative;
        }
        .login7{
            width: 25.5%;
            height: 40%;
            background-color: #fff;
            opacity: 0.9;
            position: absolute;
            left: 65.72%;
            top: 41.76%;
            -moz-box-shadow:10px 10px 20px rgba(0, 0, 0, 0.25);
            -webkit-box-shadow:10px 10px 20px rgba(0, 0, 0, 0.25);
            box-shadow:10px 10px 20px rgba(0, 0, 0, 0.25);
        }


input::-webkit-input-placeholder, textarea::-webkit-input-placeholder {
    color: #777777;
}
input:-moz-placeholder, textarea:-moz-placeholder {
    color: #777777;
}
input::-moz-placeholder, textarea::-moz-placeholder {
    color: #777777;
}
input:-ms-input-placeholder, textarea:-ms-input-placeholder {
    color: #777;
}

@media (min-width: 1366px) {
    p{font-size: 16px !important;}

}
@media (min-width: 1440px) {p{font-size: 17px !important;}}
@media (min-width: 1680px) {p{font-size: 18px !important;}}
@media (min-width: 1920px) {p{font-size: 16px !important;}}



.login{margin-left: 58%;}
.login_info{ width:100%; height:36%;background-color:#fff; padding:4% 20px 16px 26px;border-radius:10px;}
.login_info_text{ height:35px;*height:35px;background-color:#fff;  margin-top:12px; padding-left:25px;  background-repeat:no-repeat; border:#e6e6e6 2px solid;}
.UI_textbox1{ width:97%; background-position:0px 0px;position:relative;margin-bottom:10px;border-radius:8px; }
.UI_text1{color:#666; width:100%; height:31px; font-size:16px; border:none; padding:5px 10px;  *padding:0px 10px;*height:33px; *border:0; *line-height:33px;}
.UI_textbox2{ width:97%; background-position:-26px 0px;margin-bottom:10px;border-radius:8px;position:relative;}
.UI_textbox3{ width:55%;  background-position:-52px 0px;border-radius:8px;position:relative;}
.UI_text2{color:#666; width:100%; height:31px; font-size:16px; border:none; padding:5px 10px; *padding:0px 10px;*height:33px; *border:0; *line-height:33px; }
.yzm{ width:100px; height:35px; margin-top:-35px; margin-left:57%;}
.UI_btn{ width:95%; height:35px;margin-top:10px;position:relative; }
.ui-button {
    display: block;
    text-align: center;
    cursor: pointer;
    width: 300px;
    padding: 0 20px;
    font-size: 16px;
    line-height: 35px;
    height: 35px;
    color: #fff;
    background-color: #40a175;
    border: 0;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
}

.a {
    width: 84px;
    height: 110px;
    float: right;
    overflow: hidden;
    text-align: center;
    text-decoration:none;
}

.bott{      width: 100%;
    height: 19.5%;;margin-top:30px;}
.bott_info1{ height:100px; width:880px; margin:0px auto;}
.dhlc{ width:475px; height:85px;background-image:url(img2.png); background-position:-95px -36px; overflow:hidden; float:left;margin-top:1.8%;background-color:#FFF}
.ewm{width:85px; height:100px;background-image:url(img2.png); background-position:-10px -37px; float:right; overflow:hidden;}
.bott_info2{width:100%;
    height: 15.5%; line-height: 15.5%;text-align: center;
    background-color: #E2E2E2; margin:0 auto;padding-top:12px; line-height:18px;}
.bott_info2 div{  height:15px;}
.ewmW{width:84px; height:110px;float:right; overflow:hidden; text-align:center;}
.ewmC{width:84px; height:110px;float:right; overflow:hidden; margin-right:10px; text-align:center;}

.warning{text-align:center;display:none;background:#FFFDE3; color:#CF682F; border:#CF682F solid 1px; margin: 15px auto; border-left:none; border-right:none; width:80%; font-size:14px; height:25px; line-height:25px; }
.warning a{color:#CF682F;display:block;margin-left:-30px;text-decoration:underline;}
.conwe{ color:#40a175;font-size: 12px}
.conw{position:relative;}
.er{position:absolute;display:none;width:200px;height: 100px;top:23px;left: -10px;}
.conw:hover .er{
    display: block;

}
        .whole>ul>li{
            float: left;
            margin-left: 30px;
            line-height:20px;
        }
    </style>
</head>
<body style="">
<div class="whole" style="position: relative">

    <?php 
		if(!empty($ucinfo['CompanyLogo']))
		{
			echo '<img src="'.RESOURCE_PATH.$ucinfo['CompanyLogo'].'" alt="医统天下集采通" style="margin: 28px 20px 0 60px;" />';
		}else{
			echo '<img src="images/77.png" alt="医统天下集采通" style="margin: 28px 20px 0 60px;" />';
		}
	?>
			
    <span style="font-size:24px;font-weight:bold;position:absolute;top:55px;color:#009054">
    	<?php if(empty($ucinfo['CompanyName'])) echo '欢迎您登录医统天下BMB - 集采平台'; else echo $ucinfo['CompanyName'].' - 集采平台'; ?>
    </span>

    <ul style="float: right;margin-top: 25px;">
    <li class="conw" style="position: relative;margin-top: 1px;"><a href="" class="conwe">关注我们</a><img src="images/1.png" alt="" class="er" style="z-index:9999"/></li>
    <li><a href="/manager/index.php" style="color:#01A157; float:right;font-size:12px;margin-top: 2px;margin-left: 7px; cursor:pointer">管理端登录</a></li>
    <li><div style="width:100px;height:1.8rem; float:right;">
        <a href="resource/MedicalClickTracks-PlatformOperationGuide.pptx" target="_self"
           title="下载操作指南" style="color:#01A157;display:block;cursor: pointer;vertical-align: middle;width:87%;height:20px;line-height:20px;text-align:center;font-size:12px;">&nbsp;下载操作指南 &nbsp;</a>
    </div></li>
    </ul>

<div class="line" style="width:100%;height:1px;background-color:#ccc;margin-top:14px"> </div>



  <div class="wrapper">
  	<ul class="bg-bubbles">
  		<li></li>
  		<li></li>
  		<li></li>
  		<li></li>
  		<li></li>
  		<li></li>
  		<li></li>
  		<li></li>
  		<li></li>
  		<li></li>
  	</ul>
 <!--登录开始-->
    <div class="login" style="position: absolute;top:8%;">

        <div class="login_info">

            <form name="loginform" id="loginform"  method="post" onsubmit="return loginin();" >
               <p style="color:#666">用户名:</p>
                <div class="login_info_text UI_textbox1" title="请输入帐号" >
                                   <i class="iconfont icon-yonghuming" style="font-size:26px;color:#e6e6e6;position:absolute;top:0;left:0"></i>
                                   <input name="log" tabindex="1" class="UI_text1" id="user_login" type="text" maxlength="30" autocomplete="off" placeholder="请输入用户名" />
                </div>

                <p style="color:#666">密码:</p>
                               <div class="login_info_text UI_textbox2" title="请输入密码" >
                                  <i class="iconfont icon-mima" style="font-size:20px;color:#e6e6e6;position:absolute;top:2px;left:2px"></i>
                                   <input name="pwd" tabindex="2" class="UI_text1" id="user_pass" type="password" maxlength="30" autocomplete="off" placeholder="请输入密码" />
                               </div>

                <p style="color:#666">验证码:</p>
                               <div class="login_info_text UI_textbox3" title="请输入验证码" >
                               <i class="iconfont icon-yanzhengma" style="font-size:22px;color:#e6e6e6;position:absolute;top:2px;left:2px"></i>
                                   <input name="vc" tabindex="3" class="UI_text2" id="user_vc" type="text" maxlength="5" autocomplete="off" placeholder="请输入验证码" />
                               </div>
                               <div class="yzm"><a tabindex="-1" style="border-style: none" href="#" title="点击 刷新验证码" onclick="document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random(); return false"><img id="siimage" align="left" src="./plugin/securimage/checkcodeimg.php?sid=<script languange=javascript>Math.random();</script>" title="点击 刷新验证码" border="0" onclick="this.blur()"   /></a></div>


                               <div class="UI_btn" style="font-size: 14px;text-align:center;padding-left:-20%;">
                                   <font color="#CF682F" id="warning"></font>
                               </div>

                               <div class="UI_btn" style="margin-top:-9px;margin-bottom: 0.8rem">
                                   <input tabindex="4" class="ui-button" name="wp-submit" id="wp-submit" type="submit" value="登 录" seed="authcenter-submit-index" />
                               </div>
                               <div class="zhanghao" style="text-align: right;">
                                   <?php if(!empty($companyflag) && $reginfoarr['regiester_type'] == "on"){?>
                                   <a href="client_regiester.php?c=<?php echo passport_encrypt($companyflag,ENCODE_KEY);?>" title="我还没有帐号，马上注册" class="zhzc" style="margin-top:-0.8rem;margin-left:75%;">注册帐号</a>
                                   <?php }else{?>
                                   <a href="javascript:alert('请与您的供货商联系，重置密码！')" title="找回密码" class="zhzc" style="margin-top:-1.3rem;margin-left:45%;color:#666;">忘记密码？</a>
                                   <?php }?>

                               </div>

            </form>
        </div>
    </div>
    <!--登录结束-->

  <script type="text/javascript">
  $('#login-button').click(function(event){
  	event.preventDefault();
  	$('form').fadeOut(500);
  	$('.wrapper').addClass('form-success');
  });
  </script>

  <script>
     $(document).ready(function(){
  $("#user_login").focus(function(){
    $(".icon-yonghuming").css("color","#40a175");
  });
    $("#user_login").mouseleave(function(){
      $(".icon-yonghuming").css("color","#e6e6e6");
    });
     });
  </script>
  <script>
          $(document).ready(function(){
       $("#user_pass").focus(function(){
         $(".icon-mima").css("color","#40a175");
       });
         $("#user_pass").mouseleave(function(){
           $(".icon-mima").css("color","#e6e6e6");
         });
          });
</script>

  <script>
          $(document).ready(function(){
       $("#user_vc").focus(function(){
         $(".icon-yanzhengma").css("color","#40a175");
       });
         $("#user_vc").mouseleave(function(){
           $(".icon-yanzhengma").css("color","#e6e6e6");
         });
          });
</script>

</div>
      <div class="whole-bottom" style="width:100%;height:22%;position:fixed;left:0;bottom:0;">
      <div class="bottom-content" style="width:78%;margin:6% auto;">
      <p style="display:block;height:22px;line-height:22px;text-align:center;margin-bottom:10px;color:#666;font-size:16px">Copyright © 2014 - 2017 医统天下（北京）网络科技有限公司</p>
      <p style="color:#666;height:22px;line-height:22px;text-align:center;font-size:16px;">互联网药品交易服务资格证书国A20150005号，京ICP备14037820号，京公网安备11010102001371号 电话 ：400-855-9111 邮箱：info@yitong111.com</p>
      </div>
    </div>
</div>
</body>
</html>