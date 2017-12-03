<?php 
include_once ("header.php");
$menu_flag = "infomation";

$basedatabasename = '';

if(!intval($in['ID']))
{
	$info = $db->get_row("SELECT * FROM ".$basedatabasename.DATATABLE."_order_article where ArticleCompany=1 and ArticleSort=22 and ArticleFlag=0 order by ArticleOrder desc, ArticleID asc limit 0,1");
}else{	 
	$info = $db->get_row("SELECT * FROM ".$basedatabasename.DATATABLE."_order_article where ArticleCompany=1 and ArticleSort=22 and ArticleFlag=0 and ArticleID=".intval($in['ID'])." limit 0,1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
</head>

<body>  
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="set_filename" id="set_filename" value="" />
			<input type="hidden" name="update_id" id="update_id" value="<? echo $info['ArticleID'];?>" />
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="home.php">首页</a> &#8250;&#8250; <a href="#.php">系统公告</a>  </div>
   	        </div>
			</div>
    	
        <div class="line2"></div>
        <div class="bline" >        

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong><img src="css/images/home.gif" alt="栏目"  />&nbsp;&nbsp;<a href="infomation.php" title="所有信息">系统公告</a></strong></div>
<ul>

	<?php
	$sinfo = $db->get_results("SELECT ArticleID,ArticleTitle FROM ".$basedatabasename.DATATABLE."_order_article where ArticleCompany=1 and ArticleSort=22 and ArticleFlag=0 order by ArticleOrder desc, ArticleID asc limit 0,50");

	for($i=0;$i<count($sinfo);$i++)
	{

		if($in['ID'] == $sinfo[$i]['ArticleID']) $smsg = 'class="locationli"'; else $smsg ="";
		echo '<li>'.($i+1).'、<a href="changelog.php?ID='.$sinfo[$i]['ArticleID'].'" '.$smsg.' >'.$sinfo[$i]['ArticleTitle'].'</a></li>';
	}
	?>
</ul>
<br style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>


		<div id="sortright">

		<fieldset title="" class="fieldsetstyle" style="padding:12px;">
		<legend>系统公告</legend>
			<h3><? echo $info['ArticleTitle'];?></h3>
			<p>
			<?php
				echo $stringContent = html_entity_decode($info['ArticleContent'], ENT_QUOTES,'UTF-8');
			?>
			</p>
			
            </fieldset>
<br style="clear:both;" />&nbsp;

		</div>

<br style="clear:both;" />&nbsp;

		 </div>
    </div>
    


</body>
</html>