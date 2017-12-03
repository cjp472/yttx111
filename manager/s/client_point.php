<?php 
$menu_flag = "client";
$pope	       = "pope_view";
include_once ("header.php");

if(!empty($in['cid']) && ($_SESSION['uinfo']['userid'] == "1" || $_SESSION['uinfo']['userid'] == "3"))
{
	$ucid = $in['cid'];
}else{
	$ucid = $_SESSION['uinfo']['ucompany'];
}

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
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<input name="data_ClientID" type="hidden" id="data_ClientID" value="<? echo $clientinfo['ClientID'];?>"  />
		
		<div id="searchline" style="width:92%;">
        	   <span id="menu2" style=" height:32px; float:right;">
            	<ul>
                  	<li ><a href="client_content.php?ID=<? echo $in['ID'];?>">基本资料</a></li>					
					<li class="current2"><a href="client_point.php?ID=<? echo $in['ID'];?>">积分</a></li>
					<li ><a href="client_point_log.php?ID=<? echo $in['ID'];?>">积分记录</a></li>
                </ul>
            </span>                       
        </div>

        <div class="bline" >
			<fieldset  class="fieldsetstyle" style="height:400px;">		
			<legend>积分</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                              
                <tr>
                  <td width="18%" bgcolor="#F0F0F0"><div align="right">当前积分：</div></td>
                  <td width="55%"><label>
                    <?
					$cvalue = $db->get_row("select sum(PointValue) as pv from ".DATATABLE."_order_point where PointCompany=".$ucid." and PointClient=".$clientinfo['ClientID']." ");

					echo '&nbsp;<strong>'.$cvalue['pv'].'</strong>';
					?>
                    </label></td>
                  <td ></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">调整积分（增/减）：</div></td>
                  <td><INPUT TYPE="text" name="point" id="point" value ="" style="width:150px;" /> &nbsp; 输入负值即可减少积分；                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备注说明：</div></td>
                  <td><INPUT TYPE="text" name="title" id="title" value ="" style="width:350px;" />                </td>
                  <td></td>
                </tr>
            </table>
			<div align="center" style="width:480px; height:40px; line-height:40px;"><input name="saveclientpointid" type="button" class="button_1" id="saveclientpointid" value="保 存"  onclick="do_save_client_point();" />  </div>
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