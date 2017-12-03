<?php 
$menu_flag = "feedback";
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

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

 <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="feedback.php">
			  
        	    <label>
        	      &nbsp;&nbsp;姓名，内容： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="feedback.php">反馈-客户反馈</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or f.ClientName like '%".$in['kw']."%' or f.Content like '%".$in['kw']."%' ) ";


	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_common_feedback where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT f.*,c.CompanyName FROM ".DATABASEU.DATATABLE."_common_feedback f inner join ".DATABASEU.DATATABLE."_order_company c ON f.CompanyID=c.CompanyID where 1=1 ".$sqlmsg." ORDER BY f.FeedbackID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">行号</td>
                  <td width="18%" class="bottomlinebold">公司</td>
                  <td width="10%" class="bottomlinebold">姓名</td>
				  <td width="14%" class="bottomlinebold">联系方式</td>
				  <td width="10%" class="bottomlinebold">反馈类型</td>
				  <td width="12%" class="bottomlinebold">提交时间</td>
                  <td class="bottomlinebold">反馈内容</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n = 1;
if(!empty($list_data))
{

     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {

?>
               <tr id="line_<? echo $lsv['FeedbackID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td ><? echo $n++;?></td>
                  <td ><?php echo $lsv['CompanyName'];?></td>
                  <td ><? echo $lsv['ClientName'];?></td>
				  <td ><? echo $lsv['Contact'];?></td>
				  <td ><? echo $lsv['FeedbackType'];?></td>
				  <td class="TitleNUM2"><? echo date("y-m-d H:i",$lsv['CreateDate']);?></td>
                  <td class="TitleNUM2"><? echo $lsv['Content'];?></td>
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
       				 <td width="4%"  height="30" ></td>
   			         
       			     <td  align="right"><? echo $page->ShowLink('feedback.php');?></td>
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