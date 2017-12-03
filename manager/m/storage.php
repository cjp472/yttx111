<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="storage.php">
        	    <label>
        	     &nbsp;&nbsp;单号： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>            
			<div class="location"><strong>当前位置：</strong><a href="storage.php">入库单</a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="18%" class="bottomlinebold">&nbsp;单号</td>
                  <td width="15%" class="bottomlinebold">&nbsp;经办人</td>                  
				  <td width="12%" class="bottomlinebold" >&nbsp;操作员</td>				  
                  <td width="18%" class="bottomlinebold" >时间</td>
				  <td class="bottomlinebold">备注</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	if(!empty($in['kw']))  $sqlmsg .= " and StorageSN like binary '%%".$in['kw']."%%' ";

  //yangmm 2017-11-28 代理商只能看到自己商品的信息
  $userid=$_SESSION['uinfo']['userid'];
  $type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$userid."");
  if($type['UserType']=='M' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$userid." ";
  if($type['UserType']=='S' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$type['UpperID']." ";  
  
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_storage where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total   = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT StorageID,StorageSN,StorageProduct,StorageAttn,StorageAbout,StorageUser,StorageDate FROM ".DATATABLE."_order_storage where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ORDER BY StorageID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['StorageID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td >&nbsp;<a href="storage_content.php?ID=<? echo $lsv['StorageID'];?>" target="_blank" ><? echo $lsv['StorageSN'];?></a></td>
                  <td >&nbsp;<? echo $lsv['StorageAttn'];?></td>
                  <td >&nbsp;<? echo $lsv['StorageUser'];?></td>
                  <td ><? echo date("Y-m-d H:i",$lsv['StorageDate']);?></td>                 
				  <td ><? echo $lsv['StorageAbout'];?>&nbsp;</td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无数据!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td height="30" align="right"><? echo $page->ShowLink('storage.php');?></td>
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