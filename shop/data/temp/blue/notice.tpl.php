<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<script src="template/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" >
function sendnotice()
{
$.post("notice.php",
{m:"addnotice", ProductID: $("#goodsid").val(),Email: $("#Email_").val(),Mobile: $("#Mobile_").val()},
function(data){
if(data == "ok"){
alert('操作成功,到货我们会及时通知您!');
parent.closewindowui();
}else{
alert(data);
}
}			
);		
}
</script>

<style type="text/css">
<!--
body{font-size:12px;}
td,span,div,p,li{
font-size:12px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#3C3C3C; line-height:150%;
}
form{padding:0;margin:0;}
.line{margin:0 auto; width:96%; clear:both; padding:2px; line-height:28px; height:28px;}
.input{width:90%; color:#666666; height:28px; line-height:28px;}
.bluebtn:hover {
    background: blue;
}

.bluebtn {
    background: #3366CC;
    color: #FFF;
    font-weight: bold;
    font-size: 12px;
    padding: .4em .8em !important;
    padding: .2em .1em;
    cursor: pointer;
}
.close-form{background:#229955; color:#fff; width:18px; height:18px; margin:6px; text-align:center; line-height:18px; float:right; font-size:14px; font-weight:bold; cursor:pointer; clear:both; border:#eeeeee solid 1px;}

.button_3{ width:80px; height:26px; line-height:26px; border:0; margin:5px 0 0 5px; background-color:#ffb236;border-radius: 2px;cursor: pointer;margin-right:10px;}
.button_3:hover {background-color:#ffbe55;}
-->
</style>
</head>

<body>
<div style="width:100%;height:30px; clear:both; line-height:30px; background-color:#229955; border-bottom:2px soild #1c7e29; color:#ffffff; font-size:14px; font-weight:bold; ">
<div style="width:150px; height:30px; float:left; text-align:left; margin:0; line-height:32px; padding-left:4px; font-size:14px; color:#ffffff; overflow:hidden;">到货通知?</div>
<div style="float:right; height:32px; width:32px; line-height:32px;"><div class="close-form" onclick="parent.closewindowui()" title="关闭" >x</div></div>
</div>
<form id="PostNotice" name="PostNotice" method="post" action="" >
<input name="goodsid" id="goodsid" type="hidden" value="<?=$in['gid']?>"  />
<div class="line" style="display:none;">
<strong>E-mail:</strong>
</div>
<div class="line" style="display:none;">
<input name="Email_" id="Email_" type="text" value="
<? if(!empty($_SESSION['cc']['cemail'])) { ?>
<?=$_SESSION['cc']['cemail']?>
<? } ?>
" onfocus="this.select();" class="input" />
</div>
<div class="line">
<strong>短信通知:</strong>
</div>
<div class="line" >
<input name="Mobile_" id="Mobile_" style="padding-left: 5px;" type="text" value="
<? if(!empty($_SESSION['cc']['cmobile'])) { ?>
<?=$_SESSION['cc']['cmobile']?>
<? } else { ?>
手机号
<? } ?>
" onfocus="this.select();" class="input" />
</div>
<div class="line" style="margin-top:4px;">
<input name="sendnoticebutton" id="sendnoticebutton" value="到货通知我" type="button" class="button_3" onclick="sendnotice();" style="margin-left:0px;" />
</div>
</form>
</body>
</html>
