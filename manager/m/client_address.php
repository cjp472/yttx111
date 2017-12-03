<?php 
$menu_flag = "client";
$pope	   = "pope_view";
include_once ("header.php");

$ucid = $_SESSION['uinfo']['ucompany'];

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$clientinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_client where ClientCompany=".$ucid." and ClientID=".intval($in['ID'])." limit 0,1");
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
			<legend>收货地址</legend>
           
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
               <thead>
                <tr>
				  <td width="6%" class="bottomlinebold">行号</td>
                  <td width="18%" class="bottomlinebold">单位名称</td>
				  <td width="10%" class="bottomlinebold">联系人</td>
                  <td width="12%" class="bottomlinebold">&nbsp;联系电话</td>
				  <td  class="bottomlinebold">&nbsp;收货地址</td>
                  <td width="10%" class="bottomlinebold" >时间&nbsp;</td>
				  <td width="5%" class="bottomlinebold" align="center">默认地址&nbsp;</td>
				  <td width="6%" class="bottomlinebold" align="center">操作&nbsp;</td>
                </tr>
     		 </thead>      		
      		<tbody>


<?php
$n = 0;
$adata = $db->get_results("select * from ".DATATABLE."_order_address where AddressClient=".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,50");
if(!empty($adata))
{
	foreach($adata as $var)
	{
		$n++;
?>
                <tr id="line_<? echo $var['AddressID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td height="28"><? echo $n;?></td>
                  <td ><? echo $var['AddressCompany'];?></td>
				  <td ><? echo $var['AddressContact'];?>&nbsp;</td>
				  <td ><? echo $var['AddressPhone'];?>&nbsp;</td>
                  <td ><? echo $var['AddressAddress'];?></td>
				  <td ><? echo date("Y-m-d",$var['AddressDate']);?>&nbsp;</td>
				  <td align="center"><? if(!empty($var['AddressFlag'])) echo '<span class="title_green_w" title="默认收货地址" >√</span>'; else echo '<span class="font12"  >X</span>';?>&nbsp;</td>
                  <td align="center">[<a href="javascript:void(0)" onclick="delete_address('<?php echo $var['AddressID'];?>')">删除</a>]</td>
                </tr>
<? }?>

<? }else{ ?>
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