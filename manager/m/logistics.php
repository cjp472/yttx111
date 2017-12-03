<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
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

<script src="js/consignment.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="logistics.php">
        		<tr>
					<td width="100" align="center"><strong>公司名/介绍：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="consignment.php">发货信息</a> &#8250;&#8250; <a href="logistics.php">物流公司</a></div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">物流公司</td>
				  <td width="14%" class="bottomlinebold">联系人</td>
                  <td width="16%" class="bottomlinebold" >联系电话</td>
                  <td width="28%" class="bottomlinebold" >地址</td>
                  <td width="8%" class="bottomlinebold" align="left">管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and (LogisticsName like binary '%%".$in['kw']."%%' or LogisticsAbout like binary '%%".$in['kw']."%%') ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total	= $InfoDataNum['allrow'];
    $page->LinkAry  = array("kw"=>$in['kw']);    
	$n=0;
	$datasql   = "SELECT LogisticsID,LogisticsName,LogisticsContact,LogisticsPhone,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by LogisticsID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = $in['page']*$page->PageSize-1;
		foreach($list_data as $lsv)
		{
			$n++;
?>
                <tr id="line_<? echo $lsv['LogisticsID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td ><? echo $n;?></td>
                  <td ><a href="logistics_content.php?ID=<? echo $lsv['LogisticsID'];?>" target="_blank" ><? echo $lsv['LogisticsName'];?></a></td>
				  <td ><? echo $lsv['LogisticsContact'];?>&nbsp;</td>
                  <td ><? echo $lsv['LogisticsPhone'];?>&nbsp;</td>
                  <td ><? echo $lsv['LogisticsAddress'];?>&nbsp;</td>  		
                  <td align="left">
					<a href="logistics_edit.php?ID=<? echo $lsv['LogisticsID'];?>" >修改</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="do_logistics_delete('<? echo $lsv['LogisticsID'];?>');">删除</a>
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" >&nbsp;</td>
   			       <td width="8%" >&nbsp;</td>
   			       <td  class="sublink"></td>
       			     <td width="50%" align="right"><? echo $page->ShowLink('logistics.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
       	  </div>

        <br style="clear:both;" />
    </div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>