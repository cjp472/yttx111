<?php 
$menu_flag = "smscode";
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
        	  <form id="FormSearch" name="FormSearch" method="post" action="smscode.php">
			  
        	    <label>
        	      &nbsp;&nbsp;手机号： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="manager_client_log.php">短信校验码</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and mobile = '".$in['kw']."' ";


	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_buy_sms where code <> 0 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_buy_sms  where code <> 0 ".$sqlmsg." ORDER BY id DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
                  <td width="12%" class="bottomlinebold">手机</td>
                  <td width="12%" class="bottomlinebold">校验码</td>
				  <td width="16%" class="bottomlinebold">时间</td>
				  <td width="16%" class="bottomlinebold">IP</td>
				  <td width="18%" class="bottomlinebold">地区</td>
				  <td width="12%" class="bottomlinebold">发送状态</td>
                  <td align="center" class="bottomlinebold">是否使用</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n = 1;
if(!empty($list_data))
{
	$IPAddress = new IPAddress();
     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {
		$IPAddress->qqwry($lsv['ip']);
		$localArea = $IPAddress->replaceArea();
?>
               <tr id="line_<? echo $lsv['FeedbackID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td ><? echo $n++;?></td>
                  <td ><?php echo $lsv['mobile'];?></td>
                  <td ><? echo $lsv['code'];?></td>
				  <td ><? echo date("Y-m-d H:i:s",$lsv['time']);?></td>
				  <td ><? echo $lsv['ip'];?></td>
				  <td ><? echo $localArea;?></td>
				  <td class="TitleNUM2"><? if($lsv['status'] == 0) echo '<span class="title_green_w" title="发送成功" >√</span>'; else echo $lsv['status']; ?></td>
                  <td align="center">&nbsp;<? if($lsv['is_next']=="T") echo '<span class="title_green_w" title="已使用" >√</span>';?></td>
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
   			         
       			     <td  align="right"><? echo $page->ShowLink('smscode.php');?></td>
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