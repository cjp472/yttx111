    <div class="bodyline" style="background-image:url(img/bottom_bg.jpg); height:12px;">
        <div class="leftdiv"><img src="img/bottom_left.jpg" /></div>    	
        <div class="rightdiv"><img src="img/bottom_right.jpg" /></div>
	</div>
    
    <div id="copyright"><span class="leftdiv">[<a href="help.php" target="_blank" title="操作指南">帮助?</a>]&nbsp;&nbsp;&nbsp;&nbsp;
	<? if($_SESSION['uinfo']['userid'] === "1" || $_SESSION['uinfo']['userid'] === "2" || $_SESSION['uinfo']['userid'] === "3") echo '[<a href="../r/manager_all.php">管理</a>]';?>
	</span><span class="rightdiv">Powered By Rsung DingHuoBao (<a href="http://www.dhb.hk" target="_blank">WWW.DHB.HK</a>) System © 2005 - <?php echo date("Y");?> <a href="http://www.rsung.com" target="_blank">Rsung</a> Ltd.</span></div>

<script language="JavaScript" type="text/javascript"> 
<!--
if(typeof(jQuery) == "undefined") document.write('<script src="../scripts/jquery.min.js" type="text/javascript"></script>');
document.write('<script src="../scripts/jquery.messager.js" type="text/javascript"></script>');
function refresh_message()
{
	$.post("do_message.php?rid=<? echo rand(1000,9999);?>",
		{m:"refresh"},
			function(data){
				if(data=="isouttime")
				{
					alert('登陆超时或您的帐号在别的地方登陆了，请重新登陆！');
					top.window.location.href='/index.html';
				}		
		}		
	);
}
window.setInterval("refresh_message()", 300000);
-->
</script>
