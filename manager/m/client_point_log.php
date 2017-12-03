<?php 
$menu_flag = "client";
$pope	       = "pope_view";
include_once ("header.php");

$ucid = $_SESSION['uinfo']['ucompany'];
if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$clientinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_client  where ClientCompany=".$ucid." and ClientID=".intval($in['ID'])." limit 0,1");
	$in['aid']  = $clientinfo['ClientArea'];
}
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
<script src="js/client.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>        
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<input name="data_ClientID" type="hidden" id="data_ClientID" value="<? echo $clientinfo['ClientID'];?>"  />
		
		<?php include_once ("inc/menu_client.php");?>

        <div class="bline" >
			<fieldset  class="fieldsetstyle" style="min-height:400px;">		
			<legend>积分记录</legend>
        	  <table width="96%" border="0" cellspacing="0" cellpadding="0" align="center">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">行号</td>
                  <td width="20%" class="bottomlinebold">日期</td>
				   <td width="10%" class="bottomlinebold" align="right">记分&nbsp;</td>
				   <td width="10%" class="bottomlinebold">&nbsp;</td>
				  <td width="20%" class="bottomlinebold">摘要</td>
					<td  class="bottomlinebold">说明</td>
				 
                </tr>
     		 </thead>       		
      		<tbody>
<?
	$n=1;
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_point where PointCompany = ".$_SESSION['uinfo']['ucompany']." and PointClient=".$in['ID']." ");
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $InfoDataNum['allrow']; 
	$page->LinkAry = array("ID"=>$in['ID']);

	$datasql   = "SELECT PointID,PointDate,PointOrder,PointValue,PointTitle FROM ".DATATABLE."_order_point where PointCompany = ".$_SESSION['uinfo']['ucompany']." and PointClient=".$in['ID']."  ORDER BY PointID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

	if(!empty($list_data))
	{
     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['ClientID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
                  <td ><? echo date("Y-m-d H:i",$lsv['PointDate']);?></td>
				  <td class="font12" align="right">&nbsp;<? echo $lsv['PointValue'];?></td>
				  <td>&nbsp;</td>
				  <td ><? if(empty($lsv['PointOrder'])) echo '管理员改变积分'; else echo '订单：<a href="order_manager.php?SN='.$lsv['PointOrder'].'" target="_blank">'.$lsv['PointOrder'].'</a>';?></td>
				  <td >&nbsp;<? echo $lsv['PointTitle'];?></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
			</tbody>
			<table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>

       			     <td  align="right"><? echo $page->ShowLink('client_point_log.php');?></td>
     			 </tr>
              </table>
           </fieldset>              

            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close();">关闭</a></li></ul></div>
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
            </form>
        <br style="clear:both;" />		
    </div>
    
<? include_once ("bottom_content.php");?>
</body>
</html>