    <div class="bodyline" style="background-image:url(img/bottom_bg.jpg); height:12px;">
        <div class="leftdiv"><img src="img/bottom_left.jpg" /></div>    	
        <div class="rightdiv"><img src="img/bottom_right.jpg" /></div>
	</div>
    
    <div id="copyright"><span style="" class="leftdiv"><!-- [<a href="<?php echo HELP_URL;?>" target="_blank" title="操作指南">帮助?</a>]&nbsp;&nbsp;&nbsp;&nbsp; -->
	<? if(in_array($_SESSION['uinfo']['userid'],array(1))) echo '[<a href="../pt/feedback.php">管理</a>]';?>
	<? if(DHB_RUNTIME_MODE === 'experience' && $_SESSION['uinfo']['userid']==1) echo '[<a href="../r/experience.php">体验</a>]';?>
	</span></div>

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
				}else if(data != "" && data !=0 && data != "undefined" && data != undefined){

					$.messager.anim('fade', 2000);
					$.messager.show('','<font color=red>您有新订单！</font><br />  共有 <strong>'+data+'</strong> 个订单待审核!<br /><a href=order.php?sid=0 >点击查看 &nbsp;&nbsp;</a><br /><bgsound src="./img/message.mp3" loop="1" delay="2" />',0);
			}				
		}		
	);
}
window.setInterval("refresh_message()", 300000);
//$.messager.show('','<font color=red>系统又升级了</font><br />  <br /><a href=changelog.php >点击查看详细 &nbsp;&nbsp;</a>',0);
-->
</script>
<?php
 $file_php_name = basename($_SERVER['SCRIPT_FILENAME']);
 if($file_php_name == 'product_add.php' || $file_php_name == 'product_edit.php' ){
?>
    <link rel="stylesheet" href="css/select2.css" type="text/css" />
    <script src="../scripts/select2.min.js" type="text/javascript"></script>
	<script src="../scripts/select2_locale_zh-CN.js" type="text/javascript"></script>
<?php }else{?>
    <link rel="stylesheet" href="../scripts/select2/select2.min.css" type="text/css" />
    <script src="../scripts/select2/select2.min.js" type="text/javascript"></script>
	<script src="../scripts/select2/zh-CN.js" type="text/javascript"></script>
<?php }?>
    <script>
        $(function(){
            if($(".select2").length >0){
                $(".select2").select2();
            }
        });
    </script>

<?php if(DHB_RUNTIME_MODE === 'experience'):?>
<div style="display:none;">
<script language="javascript" src="http://count36.51yes.com/click.aspx?id=361587363&logo=12" charset="gb2312"></script>
</div>
<?php endif;?>
