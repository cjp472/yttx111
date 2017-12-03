    <div class="bodyline" style="background-image:url(img/bottom_bg.jpg); height:12px;">
        <div class="leftdiv"><img src="img/bottom_left.jpg" /></div>    	
        <div class="rightdiv"><img src="img/bottom_right.jpg" /></div>
	</div>
    
    <div id="copyright"><span style="" class="leftdiv"><!-- [<a href="<?php echo HELP_URL;?>" target="_blank" title="操作指南">帮助?</a>]&nbsp;&nbsp;&nbsp;&nbsp; -->
	<?php 
	if(in_array($_SESSION['uinfo']['userid'],array(1))) echo '[<a href="../pt/feedback.php">管理</a>]';
	?>
	<? if(DHB_RUNTIME_MODE === 'experience' && $_SESSION['uinfo']['userid']==1) echo '[<a href="../r/experience.php">体验</a>]';?>
	</span></div>

<script language="JavaScript" type="text/javascript"> 
<!--
if(typeof(jQuery) == "undefined") document.write('<script src="../scripts/jquery.min.js" type="text/javascript"></script>');
document.write('<script src="../scripts/jquery.messager.js" type="text/javascript"></script>');

<?php
$orhomeinfo  = $db->get_row("SELECT count(*) as orow FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=0 limit 0,1");
if(!empty($orhomeinfo['orow']))
{
?>
function refresh_message()
{
		$.messager.anim('fade', 5000);
		$.messager.show('',' 您有 <strong><? echo $orhomeinfo[orow];?></strong> 个新订单待审核!<br /><a href=order.php?sid=0 >点击查看 &#8250;&#8250;</a><object data="./img/message.mp3" type="application/x-mplayer2" width="0" height="0"><param name="src" value="./img/message.mp3"><param name="autostart" value="1"><param name="playcount" value="infinite"></object>',0);
		
		<?php if(DHB_RUNTIME_MODE === 'experience'):?>
		$('#message').css('right','45px');
		<?php endif;?>
}
window.setTimeout("refresh_message()",1000);
<? }?>

function refresh_all()
{
	window.location.reload();//刷新当前页面.
}
window.setInterval("refresh_all()",300000);
//$.messager.show('','<font color=red>系统又升级了</font><br />  <br /><a href=changelog.php >点击查看详细 &nbsp;&nbsp;</a>',0);
-->
</script>

<?php if(DHB_RUNTIME_MODE === 'experience'):?>
<div style="display:none;">
<script language="javascript" src="http://count36.51yes.com/click.aspx?id=361587363&logo=12" charset="gb2312"></script>
</div>
<?php endif;?>