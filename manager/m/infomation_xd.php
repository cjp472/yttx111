<?php 
$menu_flag = "infomation";
$pope	   = "pope_view";
include_once ("header.php"); 
setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/infomation.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="infomation_xd.php">广告管理</a> </div>
   	        </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree -->
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增广告 " class="button_2" onclick="javascript:window.location.href='infomation_xd_add.php'" /></div>
<hr style="clear:both;" />

<div class="leftlist"> 
<div >
<strong><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;<a href="infomation_xd.php" title="所有信息">信息栏目</a></strong></div>
<ul>
	<li>1、<a href="infomation_xd.php?sid=1">首页多图广告</a></li>
</ul>
<br style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="5%" class="bottomlinebold">行号</td>
                  <td width="220"  class="bottomlinebold">名称</td>
                  <td width="220" class="bottomlinebold">链接</td>
                  <td width="10%" class="bottomlinebold" >发布时间</td>
                  <td width="8%" class="bottomlinebold" >管理员</td>
                  <td width="6%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';

	if(!empty($in['sid']) || $in['sid']=="0")
	{
		$sqlmsg  .= " and ArticleSort = ".$in['sid']." ";
	}else{
		$in['sid'] ='';
	}

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_xd where ArticleCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and ArticleFlag=0 ");
	$page = new ShowPage;
    $page->PageSize = 10;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("sid"=>$in['sid']);        
	
	$datasql   = "SELECT ArticleID,ArticleOrder,ArticleUser,ArticleName,ArticlePicture,ArticleLink,ArticleSort,ArticleDate FROM ".DATATABLE."_order_xd where ArticleCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and ArticleFlag=0 ORDER BY ArticleOrder DESC, ArticleID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ArticleID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['ArticleName'];?></td>
                  <td ><? if(!empty($lsv['ArticleLink'])) echo '<a href="'.$lsv['ArticleLink'].'" target="_blank">'.$lsv['ArticleLink']."</a>";?>&nbsp;</td>
                  <td ><? echo date("Y-m-d",$lsv['ArticleDate']);?></td>                  
                  <td > <? if($lsv['ArticleUser']=="seekfor" || $lsv['ArticleUser']=="knight") echo "user"; else echo $lsv['ArticleUser'];?></td>
                  <td align="center"><a href="infomation_xd_edit.php?ID=<? echo $lsv['ArticleID'];?>" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_xd_delete('<? echo $lsv['ArticleID'];?>');" >
                  <span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a></td>
                </tr>
                <tr id="line_<? echo $lsv['ArticleID'];?>_1" class="bottomline" >
                    <td  colspan="6" ><div align="right" style="width:720px; height:255px; overflow:hidden; float:right;"><a href="<? echo RESOURCE_URL.$lsv['ArticlePicture'];?>" target="_blank"><img src="<? echo RESOURCE_URL.$lsv['ArticlePicture'];?>" alt="<? echo $lsv['ArticleContent'];?>"  /></a></div></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="6" height="30" align="center">暂无符合条件的信息!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('infomation_xd.php');?></td>
     			 </tr>
              </table>
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