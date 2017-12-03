<?php
require_once("../class/API/qqConnectAPI.php");
$qc = new QC();
$accesstoken = $qc->qq_callback();
$openid = $qc->get_openid();
$_SESSION['QC_userData']['access_token'] = $accesstoken;
$_SESSION['QC_userData']['openid'] = $openid;

$qc2 = new QC();
$rdata = $qc2->get_user_info();

if($rdata['ret'] != "0" || !empty($rdata['ret'])) exit('不正确，请关闭重新打开！');
include_once ("../common.php");
$db	 = dbconnect::dataconnect()->getdb();

$datasql   = "SELECT count(*) as lrow FROM ".DATABASEU.DATATABLE."_order_qq where OpenID='".$openid."' and UserType='C' limit 0,1";
$uinfo = $db->get_row($datasql);
if(!empty($uinfo['lrow'])){
	header("location: ../login.php?m=login&openid=".$openid);
	exit;
}else{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>医统天下BMB系统 QQ帐号登录</title>
<meta name="keywords" content="医统天下BMB系统 DHB.HK 订货管理系统 订单管理系统 在线订单管理系统 在线订货系统  网上订货系统 网络订货系统 网上订货平台 在线订货平台 订货软件 分销系统 在线软件 商品批发 " />
<meta name="Contact" content="support@rsung.com" />
<script src="/template/js/jquery.js" type="text/javascript"></script>
<script src="/template/js/login.js?v=140115" type="text/javascript"></script>
<style>
body{background-color:#fefefe;}
div,td,input{font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; font-size:14px;}
#loginbody{width:468px; height:auto; margin:120px auto; border:#277DB7 solid 1px; background-color:#ffffff;}

#titlediv{background-color:#ffffff; width:100%; height:50px; line-height:50px; text-align:center; font-size:20px; font-weight:bold; border-top:#277DB7 solid 4px; }
.inputline{width:98%; margin:4px;line-height:200%; padding:8px 2px; clear:both;}

.inputline_dt{float:left; width:25%; height:34px; line-height:34px;}
.inputline_dd{float:left; width:70%; height:34px; line-height:34px;}
.inputline_dd input{width:98%}
.inputs{width:150px; padding:4px; line-height:28px; }
.dl{float:left; width:120px; height:28px; padding:2px; text-align:center; background-color:#277DB7; color:#fff; font-weight:bold;  border:0; cursor: pointer; margin:0 12px;}
.dl2{float:left; width:80px; height:28px; padding:2px; text-align:center; background-color:#666; color:#fff; font-weight:bold;  border:0; cursor: pointer; margin:0 12px;}
.tiptext{font-size:12px; color:#666; line-height:160%; padding:8px;}
.headerimg{width:110px; height:100px; float:left;}
.headertext{width:270px; height:100px; float:left;}
.warning{text-align:center;display:none;background:#FFFDE3; color:#CF682F; border:#CF682F solid 1px; margin:1px auto; padding:4px; border-left:none; border-right:none; width:80%; font-size:12px; }
.warning a{color:#CF682F; text-decoration:underline;}
</style>
</head>

<body>
	
	<div id="loginbody">
		<form name="bform" id="bform"  method="post" onsubmit="return false;" >
		<input type="hidden" value="<?php echo $openid;?>" name="openid" id="openid" />
		<input type="hidden" value="<?php echo $accesstoken;?>" name="accesstoken" id="accesstoken" />
		<input type="hidden" value="<?php echo $rdata['nickname'];?>" name="nickname" id="nickname" />
		<div class="inputline" style="margin:8px; border:#CF682F solid 1px; width:96%; height:110px;">
			<div class="headerimg"><img src="<?php echo $rdata['figureurl_2']; ?>" width="100" height="100" /></div>
			<div class="headertext">来自QQ的 <font color=red><?php echo $rdata['nickname']?></font>，你好!<br />
立即输入医统天下BMB系统帐号密码，便可与QQ帐号绑定，以后就可以直接登录医统天下BMB系统了！</div>
		</div>

		<div class="inputline">
			<div class="inputline_dt">帐号：</div><div class="inputline_dd"><label><input type="text" value="" name="bname" id="bname" class="inputs" /></label></div>
		</div>
		<div class="inputline">
			<div class="inputline_dt">密码：</div><div class="inputline_dd"><label><input type="password" value="" name="bpass" id="bpass" class="inputs" /></label></div>
		</div>
		<div class="inputline">
			<div class="inputline_dt">验 证 码：</div><div class="inputline_dd"><label><input type="text" value="" name="bvc" id="bvc" class="inputs" onKeyDown="javascript:checkCode(event);" style="width:50%;" /></label>
			<a tabindex="-1" style="border-style: none" href="#" title="点击 刷新验证码" onclick="document.getElementById('siimage').src = '../plugin/securimage/checkcodeimg.php?sid=' + Math.random(); return false">
		<img id="siimage" align="center" style="border: 0" src="../plugin/securimage/checkcodeimg.php?sid=<script languange=javascript>Math.random();</script>" title="点击 刷新验证码" border="0" onclick="this.blur()"   /></a>
			</div>
		</div>

		<div class="inputline" align="center" style="height:38px;">
			<input name="wp-submit" id="wp-submit" class="dl" type="button" onclick="binto();" value=" 绑 定 " style="width:200px;"  />
			<input name="wp-back" id="wp-back" class="dl2" type="button" onclick="window.location.href='/'" value=" 返 回 " style="width:120px;"  />
		</div>
		<div class="inputline tiptext" >
			<font color=red>提示：</font><br />将医统天下BMB系统帐号与QQ帐号绑定之后，便可通医统天下BMB系统录QQ直接点击进入，无需再登录！
		</div>
		<div class="inputline"> <div class="warning" id="warning">正在执行，请稍候...</div> </div>
		</form>
	</div>

</body>
<html>
<?php
}
?>

