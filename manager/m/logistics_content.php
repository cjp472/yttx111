<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID=".intval($in['ID'])." limit 0,1");
}
if(empty($cinfo['LogisticsID'])) exit('此记录不存在，或已经删除!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
</head>

<body>


<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
         <input type="hidden" name="set_filename" id="set_filename" value="" />
         <input type="hidden" name="update_id" id="update_id" value="<? echo $productinfo['ID'];?>" />
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="#">物流公司</a> &#8250;&#8250; <a href="#">查看信息</a> </div>
   	        </div>            
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px;"><ul></ul></div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
		<legend>货运公司信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                     <? echo $cinfo['LogisticsName'];?></label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label><? echo $cinfo['LogisticsContact'];?></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['LogisticsPhone'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['LogisticsFax'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['LogisticsMobile'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地 址：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['LogisticsAddress'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">网 站：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['LogisticsUrl'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">经营线路：<br />(简介)</div></td>
                  <td bgcolor="#FFFFFF"><? echo nl2br($cinfo['LogisticsAbout']);?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
		</fieldset>
			<br style="clear:both;" />
            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close(true);">关 闭 </a></li></ul></div>
          </div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />

    </div>
    
<? include_once ("bottom_content.php");?>

</body>
</html>