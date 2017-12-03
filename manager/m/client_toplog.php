<?php 
$menu_flag = "client";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("../class/ip2location.class.php");
$ucid = $_SESSION['uinfo']['ucompany'];

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$clientinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_client  where ClientCompany=".$ucid." and ClientID=".intval($in['ID'])." limit 0,1");
	$in['aid']     = $clientinfo['ClientArea'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
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
			<legend>最近登录日志</legend>
           
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
				  <td width="15%" class="bottomlinebold">帐号</td>
				  <td width="22%" class="bottomlinebold">登陆地址/IP</td>
				  <td width="14%" class="bottomlinebold">登陆时间</td>
                  <td class="bottomlinebold">登陆地址</td>
                </tr>
     		 </thead>      		
      		<tbody>


<?php
$n = 1;
$datasql   = "SELECT LoginID,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl FROM ".DATABASEU.DATATABLE."_order_login_client_log where LoginClient = ".$in['ID']." and LoginCompany = ".$_SESSION['uinfo']['ucompany']." ORDER BY LoginID DESC limit 0,50";
$list_data = $db->get_results($datasql);
if(!empty($list_data))
{
   $IPAddress = new IPAddress();
   foreach($list_data as $lsv)
   {
		$iparr = explode(",",$lsv['LoginIP']);
		$IPAddress->qqwry($iparr[0]);
		$localArea = $IPAddress->replaceArea();
?>
               <tr id="line_<? echo $lsv['LoginID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
				  <td ><? echo $lsv['LoginName'];?></td>
				  <td ><? echo $localArea." (".$iparr[0].")";?></td>
				  <td class="TitleNUM2"><? echo date("Y-m-d H:i",$lsv['LoginDate']);?></td>
                  <td class="TitleNUM2"><? if(strrpos($lsv['LoginUrl'],'?')) echo substr($lsv['LoginUrl'],0,strrpos($lsv['LoginUrl'],'?')); else echo $lsv['LoginUrl']; ?></td>
                </tr>
<? } }else{ ?>
     			 <tr>
       				 <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>

 				</tbody>                
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