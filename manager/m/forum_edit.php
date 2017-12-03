<?php 
$menu_flag = "forum";
$pope	   = "pope_form";
include_once ("header.php"); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/forum.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="forum.php">客服</a> &#8250;&#8250; <a href="forum.php">留言管理</a></div>
   	        </div>       
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div ><br />
<strong><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;<a href="forum.php" title="所有留言">留言管理</a></strong></div>
<ul>
					<li><a href="forum.php?ty=replyed" > &#8250;&#8250; 已回复留言</a></li>
					<li><a href="forum.php?ty=noreply" > &#8250;&#8250; 未回复留言</a></li>
</ul>

<hr style="clear:both;" />
<div >
<strong><a href="forum.php">药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="forum.php" method="get">
				<select id="cname" name="cname" onchange="javascript:submit()" >
				<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
		$sortarr = $db->get_results("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0  ORDER BY ClientCompanyPinyi ASC ");
		foreach($sortarr as $areavar)
		{
			$n++;
			if($in['cname'] == $areavar['ClientName'])  $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
			//echo '<option value="'.$areavar['ClientName'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" >'.$n.' - '.$areavar['ClientCompanyName'].'</option>';
			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$areavar['ClientCompanyPinyi']).'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
		}
	?>
				</select>
				</form>
</ul>
</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
<?
if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$tinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_forum where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID=".intval($in['ID'])." limit 0,1");
}
?>
			 <div class="line_bottom">
				<br /><h4><? echo $tinfo['Title'];?></h4>
				<div class="line">
					<span class="leftdiv"><strong><? echo $tinfo['User']." - ".$tinfo['Name']." </strong>&nbsp;&nbsp; ".date("Y-m-d H:i",$tinfo['Date']);?></span>
					<span class="rightdiv">[<a href="#" onclick="do_delete_all(<? echo $tinfo['ID'];?>)"><strong>删除主题</strong></a>]</span>
				</div>
				<div class="line"><? echo nl2br($tinfo['Content']);?></div>
			</div>
			<?
			if(!empty($tinfo))
			{
				$sinfo = $db->get_results("SELECT * FROM ".DATATABLE."_order_forum where CompanyID=".$_SESSION['uinfo']['ucompany']." and PID=".$tinfo['ID']." limit 0,50");
				for($i=0;$i<count($sinfo);$i++)
				{
			?>
			<div class="line_right" id="reply_<? echo $sinfo[$i]['ID'];?>" <? if(!empty($sinfo[$i]['ID'])) echo 'title="管理员回复"';?> >
				<div class="line"><span class="leftdiv"><span class="numberbg"><? echo $i+1;?></span>&nbsp;&nbsp;<strong><? echo $sinfo[$i]['User']." - ".$sinfo[$i]['Name']." </strong>&nbsp;&nbsp; ".date("Y-m-d H:i",$sinfo[$i]['Date']);?></span>
				<span class="rightdiv">[<a href="#" onclick="do_delete(<? echo $sinfo[$i]['ID'];?>)">删除</a>]</span>
				</div>
				<div class="line" style="padding:4px;"><? echo nl2br($sinfo[$i]['Content']);?></div>
			</div>
			<? }}?>
			<div class="line_right" id="replyinput_" style="display:none;"></div>
				
			<div class="line_right">
				<div class="line" id="allertidtext"></div>
				<div class="line"><strong>回复：</strong><input name="replypid" id="replypid" type="hidden" value="<? echo $tinfo['ID'];?>" /></div>
				<div class="line"><textarea id="replycontent" name="replycontent" cols="50" rows="4" style="width:100%;"></textarea></div>
				<div class="line" style="margin: 4px;"><span class="spanleft"><strong>回复人：</strong>
					<input name="replyname" id="replyname" type="text" value="<? echo $_SESSION['uinfo']['usertruename'];?>" onfocus="this.select();" />&nbsp;&nbsp;</span></div>
				<div class="line" style="padding:4px;"><input name="replybuttom" id="replybuttom" value="发表回应" type="button" class="button_2" onclick="SubmitReply()" /></div>
			</div>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
<!-- rightbody end -->
        </div>              
          </div>              
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>