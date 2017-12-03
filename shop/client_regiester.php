<?php
include_once ('common.php');
$input		= new Input;
$in			= $input->parse_incoming();
$in			= $input->_htmlentities($in);
$db	        = dbconnect::dataconnect()->getdb();

if(!empty($in['c']))
{
	$companyflag = passport_decrypt($in['c'],ENCODE_KEY);
}else{
	exit('错误的路径');
}

if(!empty($companyflag))
{
	$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanyPrefix,CompanySigned,CompanyLogo,CompanyLogin,CompanyFlag,CompanyDatabase from ".DATABASEU.DATATABLE."_order_company where CompanyPrefix = '".$companyflag."' limit 0,1");
	if(empty($ucinfo)) 	exit('错误的路径');
	$buttoninfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$ucinfo['CompanyID']." and SetName='template' limit 0,1");

	if(!empty($ucinfo['CompanyDatabase'])) $setdbname = DB_DATABASE."_".$ucinfo['CompanyDatabase']."."; else $setdbname = '';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><? if(empty($ucinfo['CompanyName'])) echo '医统天下集采平台 '; else echo $ucinfo['CompanyName']; ?></title>
<script src="./template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="./template/js/login.js?v=<?php echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
<!--
/* CSS Document */
body{ padding:0; margin:0; 

    font-family: "Microsoft YaHei" ! important;

	font-family: "宋体",Arial, Helvetica, sans-serif;
font-size:14px; background:#fff;}

a{text-decoration:none; color:#009155; 	font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important;
	font-family: "宋体",Arial, Helvetica, sans-serif; }
a:hover{text-decoration:none; color:#009155; }
p{margin-top:0px;}
img{ border:none}
.img{	position: relative; top:5px; margin-left:2px; border:0; }
#content{ width:100%; height:auto; background:#ffffff;}
.main{ width:100%;background:#ffffff;position:relative}
.head{ width:100%; height:500px; overflow:hidden;background-image: url(./template/img/777.png);background-size:contain;background-repeat: no-repeat;}
.nav_right{ width:440px; height:48px; line-height:48px; float:right; font-weight:bold; font-size:18px; color:#009155;}
.clear{ clear:both}

.fogert{ width:100px;height:28px; margin-top:20px; line-height:28px; float:left;}
#main_a{ width:440px; height:654px; margin:auto;position:absolute;background-color:#fff;top:90%;left:0; right:0;bottom:0;}
.main_left{ width:503px; height:347px; margin-top:38px; float:left; overflow:hidden}
.main_right{ width:440px; height:auto;}
.main .main_table{ width:440px; padding-top:10px;}
.main_table{height:auto;}
.main .main_down{ width:440px; height:65px; margin-top:8px;}
.main .bottom{ width:960px; height:38px; line-height:38px; margin:5px auto 0; }
.bottom ul{ padding:0; margin:0;}
.bottom ul li{ width:140px; display:block; float:left; list-style-type:none}
.bottom ul li.click{
	background-image: url(./template/img/soild.jpg);
	background-repeat: no-repeat;
	background-position: 5px 15px;
	padding-left:15px;
	color:#999
}

li{list-style:none;float:left;margin-right:15px;}
#footer{ width:100%; text-align:center; color:#666;  line-height:25px; height:68px; background:#f3f3f3; padding-top:15px; font-size:12px;}
#footer a{color:#666; text-decoration:none;}
.foot{ width:960px; min-height:100px; height:auto; margin:0 auto}
#desk a{color:#277DB7; padding-left:10px;}
.input {
	font-size: 18px;
	width: 95%;
	padding: 2px;
	border: 1px solid #999999;
	background: #fbfbfb;
	font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important;
	font-family: "宋体",Arial, Helvetica, sans-serif;
}

dt{width:80px; height:34px; float:left; clear:left; margin:0px 0 2px 10px; text-align:right;font-size:16px; color:#565656}
dd{width:290px; height:34px; float:left; clear:right; margin:2px;}
.pline{clear:both; width:100%;height: 36px;
                                  line-height: 36px;margin-bottom:10px;}
.blankline{clear:both; width:100%; height:20px; background:#ffffff;}
.yellowbtn {
	width:97%;
	height:30px;
    cursor: pointer;
	border:0;
	background-color:#009155;
	color:#fff;
}

.warning{text-align:center; display:none; background:#FFFDE3; color:#CF682F; border:#CF682F solid 1px; margin:5px auto; padding:5px; border-left:none; border-right:none; width:82%; font-size:12px; clear:both;}
.warning a{color:#CF682F; text-decoration:underline;}

.blockUI p{margin:4px; padding:8px 20px; font-size:14px; font-weight:bold}
.growlUI {  }
.growlUI h1, div.growlUI h2 {
	color: white; padding: 5px 5px 5px 15px; text-align: left; font-size:18px;
}
-->
</style>

</head>
<body style="position:relative;">
<div id="content">
<div class="main">
<div class="head">


<img src="./template/img/7.png" style="z-index=99999;width:256px;height:78px;position:absolute;top:16px;left:35px;;">
</div>

<div id="main_a">

<div class="main_right">
<div class="main_table" style="margin:0 auto;;">
 <div class="nav_right"><img src="./template/img/botton.jpg" class="img" style="margin-left:40px;" />&nbsp;&nbsp;<? if(empty($ucinfo['CompanyName'])) echo '医统天下BMB集采平台'; else echo $ucinfo['CompanyName']; ?> - （注册）</div>
<div class="clear"></div>
<form name="regform" id="regform"  method="post" onsubmit="regin(); return false;" >
<input type="hidden" name="RegCompanyFlag" id="RegCompanyFlag"  value="<?php echo $in['c'];?>"  />
	<div class="pline">
		<dt>帐 号：</dt>
		<dd>
		<div style="padding: 3px; height:26px; width:95%; border: 1px solid #999999; background: #fbfbfb; float:left;" title="3-18位数字、字母和下划线" >
                    <font style="font-size:18px; padding-left:2px; width:auto; float:left;margin-top:-5px;"><? echo $ucinfo['CompanyPrefix']."-"; ?></font><input name="RegUserName" type="text" id="RegUserName" value=""  maxlength="12" style="width:50%; height:25px; float:left; font-size:20px; border:0; background: #fbfbfb;" /></div></dd><div style="margin-top:-5px;"><font color=red>*</font></div>

	</div>
	<div class="pline">
		<dt>密 码：</dt>
		<dd><input type="text" name="RegPassword" id="RegPassword" class="input" value="" size="20" maxlength="18" tabindex="20" /></dd><font color=red>*</font>
	</div>
	<div class="pline">
		<dt>手 机：</dt>
		<dd><input type="text" name="RegMobile" id="RegMobile" class="input" value="" size="20" maxlength="11" tabindex="30" /></dd><font color=red>*</font>
	</div>
	<div class="pline">
		<dt>名 称：</dt>
		<dd><input type="text" name="RegName" id="RegName" class="input" value="" size="20" maxlength="100" tabindex="40" /></dd><font color=red>*</font>
	</div>
	<div class="pline">
		<dt>地 区：</dt>
		<dd>                  <select name="RegArea" id="RegArea" class="input" tabindex="50" style="width:97%">
                    <option value="0">⊙ 请选择所在地区</option>
                    <? 
					$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaPinyi FROM ".DATATABLE."_order_area where AreaCompany=".$ucinfo['CompanyID']." ORDER BY AreaID ASC ");
					
					echo ShowTreeMenu($sortarr,0,0,1);
					?>
                  </select></dd><font color=red>*</font>
	</div>
	<div class="pline">
		<dt>联系人：</dt>
		<dd><input type="text" name="RegContact" id="RegContact" class="input" value="" size="20" maxlength="30" tabindex="60" /></dd><font color=red>*</font>
	</div>
	<div class="pline">
		<dt>传 真：</dt>
		<dd><input type="text" name="RegFax" id="RegFax" class="input" value="" size="20" maxlength="30" tabindex="80" /></dd>
	</div>
	<div class="pline">
		<dt>邮 箱：</dt>
		<dd><input type="text" name="RegEmail" id="RegEmail" class="input" value="" size="20" maxlength="30" tabindex="90" /></dd>
	</div>
	<div class="pline">
		<dt>地 址：</dt>
		<dd><input type="text" name="RegAddress" id="RegAddress" class="input" value="" size="20" maxlength="100" tabindex="100" /></dd>
	</div>

	<div class="pline">
		<dt>验证码：</dt>
		<dd><input type="text" name="vc" id="user_vc" class="input" value="" size="20" maxlength="5" tabindex="120"  style="width:100px; padding-right:20px; float:left;" /><a tabindex="-1" style="border-style: none" href="#" title="点击 刷新验证码" onclick="document.getElementById('siimage').src = './plugin/securimage/checkcodeimg.php?sid=' + Math.random(); return false">
		<img id="siimage" align="left" style="padding: 0 5px 0 20px;  border: 0; float:left;" src="./plugin/securimage/checkcodeimg.php?sid=<script languange=javascript>Math.random();</script>" title="点击 刷新验证码" border="0" onclick="this.blur()" /></a><font color=red>*</font></dd>
	</div>
	<div class="pline" style="border:none;">
	<dt>&nbsp;</dt>
	<dd style="margin-top:12px;"><input id="wp-submit" class="yellowbtn" type="submit" tabindex="100" value="注 册" style="font-weight: bold;font-size:18px;" name="wp-submit"><a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/shop" id="urlback" title="登录" style="display:block;width:155px;height:20px;margin:10px auto;">已有帐号，前往登录</a></dd>
	</div>
	<div class="warning" id="warning">正在执行，请稍候...</div>
</form>
<div class="main_down"></div>
</div>


</div>

</div>

</div>
</div>

<div id="footer" style="position:absolute;top:890px;">
<div class="message" style="background-color:#F3F3F3">
		<p class="p1">Copyright&nbsp©&nbsp2014 - <?=date('Y')?>&nbsp医统天下（北京）网络科技有限公司</p>
		<p class="p2" style="margin-top:-12px;">互联网药品交易服务资格证书国A20150005号，京ICP备14037820号，京公网安备11010102001371号 电话 ：400-855-9111 邮箱：info@yitong111.com</p>
		<ul class="po" style="width:600px !important;height:50px;margin:0 auto;">
			<li><a href="http://qyxy.baic.gov.cn/" target="_blank"><img src="./template/img/1.png" /></a></li>
			<li><a href="http://www.szfw.org/" target="_blank"><img src="./template/img/2.png" /></a></li>
			<li><a href="http://www.miitbeian.gov.cn/state/outPortal/loginPortal.action;jsessionid=cDGTW7JfLhXMLn8QWW130Kyhf1TFTpSWHHNCgv2QkvscR2nBLfYH!1794355333" target="_blank"><img src="./template/img/3.png" /></a></li>
			<li><a href="http://app1.sfda.gov.cn/datasearch/face3/base.jsp?tableId=28&tableName=TABLE28&title=%BB%A5%C1%AA%CD%F8%D2%A9%C6%B7%D0%C5%CF%A2%B7%FE%CE%F1&bcId=118715637133379308522963029631" target="_blank"><img src="./template/img/4.png" /></a></li>
			<li><a href="http://app1.sfda.gov.cn/datasearch/face3/base.jsp?tableId=28&tableName=TABLE28&title=%BB%A5%C1%AA%CD%F8%D2%A9%C6%B7%D0%C5%CF%A2%B7%FE%CE%F1&bcId=118715637133379308522963029631" target="_blank"><img src="./template/img/5.png" /></a></li>
		</ul>
		<div class="clear"></div>
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
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";
		
		if($var['AreaParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $layer-1);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>