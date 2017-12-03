<?php 
$menu_flag = "manager";
include_once ("header.php");
include_once ("../class/ip2location.class.php");
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

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

 <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="experience_contact.php">
			  
        	    <label>
        	      &nbsp;&nbsp;姓名，电话： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong> <a href="experience.php">体验入口</a>&#8250;&#8250;<a href="experience_contact.php">体验信息</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and (o.ContactName like '%".$in['kw']."%' or o.Phone like '%".$in['kw']."%' ) ";


	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_experience_contact o where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT o.* FROM ".DATABASEU.DATATABLE."_experience_contact o where 1=1 ".$sqlmsg." ORDER BY o.id DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="5%" class="bottomlinebold">行号</td>
                  <td width="10%" class="bottomlinebold">联系人</td>
                  <td width="13%" class="bottomlinebold">联系方式</td>
				  <td width="13%" class="bottomlinebold">添加时间</td>
				  <td class="bottomlinebold">备注</td>
				  <td width="25%" class="bottomlinebold">IP地址</td>
				  <td width="10%" class="bottomlinebold">状态</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n = 1;
if(!empty($list_data))
{

     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
     $IPAddress = new IPAddress();
	 foreach($list_data as $lsv)
	 {
	 	$iparr = explode(",",$lsv['IP']);
		$IPAddress->qqwry($iparr[0]);
		$localArea = $IPAddress->replaceArea();

?>
               <tr id="line_<? echo $lsv['id'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['ContactName'];?></td>
                  <td ><? echo $lsv['Phone'];?></td>
                  <td ><? echo date("y-m-d H:i",$lsv['Date']);?></td>
                  <td ><? echo $lsv['Remark'];?></td>
                  <td ><? echo $localArea." (".$iparr[0].")";?></td>
                  <td ><? echo $contact_arr[$lsv['Status']];?></td>

                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>
   			         
       			     <td  align="right"><? echo $page->ShowLink('experience_contact.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>

        </div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
 
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    
</body>
</html>