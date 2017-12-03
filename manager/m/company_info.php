<?php 
include_once ("header.php");
$menu_flag = "system";
	 
$uinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<div id="searchline">
        	<div class="leftdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统管理</a> &#8250;&#8250; <a href="#">公司信息</a></div>
   	        </div>
            
			
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px; display:none;"><ul><li><a href="company_edit.php?ID=<? echo $uinfo['CompanyID'];?>">修 改</a></li></ul></div>
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title=" " class="fieldsetstyle">
            
			
			<legend>基本资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">公司全称：</div></td>
                  <td width="55%"><? echo $uinfo['CompanyName'];?></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司简称：</div></td>
                  <td><? echo $uinfo['CompanySigned'];?></td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                  <td><? echo $uinfo['CompanyCity'];?></td>
                  <td></td>
                </tr>
            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset title="content" class="fieldsetstyle">
		<legend>联系信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
             
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><? echo $uinfo['CompanyContact'];?> </td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司电话：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['CompanyPhone'];?> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司传真：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['CompanyFax'];?> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司地址：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['CompanyAddress'];?> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['CompanyEmail'];?> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['CompanyRemark'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">注册日期：</div></td>
                  <td bgcolor="#FFFFFF"><? echo date("Y-m-d",$uinfo['CompanyDate']);?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

			<br style="clear:both;" />


            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>