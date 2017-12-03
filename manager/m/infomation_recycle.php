<?php 
$menu_flag = "infomation";
$pope	   = "pope_form";
include_once ("header.php"); 

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
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="infomation.php">信息管理</a>  &#8250;&#8250; <a href="infomation_recycle.php">信息回收站</a></div>
   	        </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div ><br />
<strong><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;<a href="infomation_recycle.php" title="所有信息">信息栏目</a></strong></div>
<ul>
	<li>1、<a href="infomation_recycle.php?sid=0">公告信息</a></li>
	<?
	$sinfo = $db->get_results("SELECT SortID,SortName FROM ".DATATABLE."_order_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." order by SortOrder DESC,SortID ASC");
	$sort_arr[0] = '公告信息';
	for($i=0;$i<count($sinfo);$i++)
	{
		$sort_arr[$sinfo[$i]['SortID']] = $sinfo[$i]['SortName'];

		if($in['sid'] == $sinfo[$i]['SortID']) $smsg = 'class="locationli"'; else $smsg ="";
		echo '<li>'.($i+2).'、<a href="infomation_recycle.php?sid='.$sinfo[$i]['SortID'].'" '.$smsg.' >'.$sinfo[$i]['SortName'].'</a></li>';
	}
	?>
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
                  <td width="4%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="8%" class="bottomlinebold">ID</td>
				  <td width="6%" class="bottomlinebold">排序</td>
                  <td  class="bottomlinebold">标题</td>
                  <td width="12%" class="bottomlinebold">栏目</td>
                  <td width="14%" class="bottomlinebold" >发布时间</td>
                  <td width="8%" class="bottomlinebold" >管理员</td>
                  <td width="14%" class="bottomlinebold" >管理</td>
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

	if(!empty($in['kw']))  $sqlmsg .= " and (ArticleTitle like binary '%%".$in['kw']."%%' ) ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_article where ArticleCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and ArticleFlag=1 ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid']);        
	
	$datasql   = "SELECT ArticleID,ArticleOrder,ArticleUser,ArticleTitle,ArticleSort,ArticleDate FROM ".DATATABLE."_order_article where ArticleCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and ArticleFlag=1 ORDER BY ArticleOrder DESC, ArticleID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ArticleID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ArticleID'];?>" value="<? echo $lsv['ArticleID'];?>" /></td>
                  <td ><? echo $n++;?></td>
				   <td ><input type="text" class="numberinput" name="ArticleOrder[]" id="order_<? echo $lsv['ArticleID'];?>" value="<? echo $lsv['ArticleOrder'];?>" onBlur="do_change_order('<? echo $lsv['ArticleOrder'];?>','<? echo $lsv['ArticleID']; ?>');" /></td>
                  <td ><a href="infomation_content.php?ID=<? echo $lsv['ArticleID'];?>" target="_blank"><? echo $lsv['ArticleTitle'];?></a></td>
                  <td ><? echo $sort_arr[$lsv['ArticleSort']];?>&nbsp;</td>

                  <td ><? echo date("Y-m-d",$lsv['ArticleDate']);?></td>
                  
                  <td > <? echo $lsv['ArticleUser'];?></td>
                  <td ><a href="javascript:void(0);" onclick="do_restore('<? echo $lsv['ArticleID'];?>');" >恢复</a>&nbsp;｜&nbsp;<a href="javascript:void(0);" onclick="do_quite_delete('<? echo $lsv['ArticleID'];?>');" >彻底删除</a></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合条件的信息!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td   class="sublink"><ul><li><a href="javascript:void(0);" onclick="going('restore','<? echo $in['sid'];?>')" >批量还原</a></li><li><a href="javascript:void(0);" onclick="going('quite_delete','<? echo $in['sid'];?>')" >批量删除</a></li></ul></td>       			     
     			 </tr>
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('infomation_recycle.php');?></td>
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