<?php
$menu_flag = "system";
include_once ("header.php");

$uinfo  = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");
$csinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs where CS_Company=".$_SESSION['uinfo']['ucompany']." limit 0,1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
a.noneunderline :link, :hover, :visited{text-decoration:none !important}
</style>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">公司信息</a></div>
   	        </div>     
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong>系统设置</strong></div>
<!-- 系统设置菜单开始 -->
<?php include_once("inc/system_set_left_bar.php")  ;?>
<!-- 系统设置菜单结束 -->
<br style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">
			<div id="oldinfo" class="line">
			<fieldset  class="fieldsetstyle">		
			<legend>基本资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">平台全称：</div></td>
                  <td width="55%"><? echo $uinfo['CompanyName'];?></td>
                  <td width="29%">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司简称：</div></td>
                  <td><? echo $uinfo['CompanySigned'];?></td>
                  <td>&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">开通时间：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $csinfo['CS_BeginDate'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">更新时间：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $csinfo['CS_UpDate'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">许可用户数：</div></td>
                  <td bgcolor="#FFFFFF"><? if($csinfo['CS_Number'] == 10000 ) echo "<font color=red>不限用户</font>"; else echo "<strong>".$csinfo['CS_Number']."</strong> 用户";?> </td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">短信余额：</div></td>
                  <td bgcolor="#FFFFFF"><? if($csinfo['CS_SmsNumber'] < 0 ){ echo "<font color=red>0</font>";}elseif($csinfo['CS_SmsNumber'] < 50 ) echo "<font color=red>".$csinfo['CS_SmsNumber']."</font>"; else echo $csinfo['CS_SmsNumber'];?> 条</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

            </table>
           </fieldset> 

            <br style="clear:both;" />
            <fieldset class="fieldsetstyle">
			<legend>其他信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">             
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><? echo $uinfo['CompanyContact'];?> </td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['CompanyMobile'];?> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
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
                  <td bgcolor="#FFFFFF"><? echo nl2br($uinfo['CompanyRemark']);?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>
			<div class="line" align="right"><input type="button" name="editbuttoninfo" id="editbuttoninfo" value=" 修改资料 " class="button_2" onclick="showeditinfo();" />&nbsp;&nbsp;&nbsp;&nbsp;</div>
			</div>
		<br style="clear:both;" />

			<div id="editinfo" class="line" style="display:none;">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="set_filename" id="set_filename" value="" />


            <br style="clear:both;" />
            <fieldset class="fieldsetstyle">
		<legend>联系信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">            
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><input type="text" name="data_CompanyContact" id="data_CompanyContact" value="<? echo $uinfo['CompanyContact'];?>" />
                  <span class="red">*</span> </td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyMobile" id="data_CompanyMobile" maxlength="11" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" value="<? echo $uinfo['CompanyMobile'];?>"  /> 
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;用于短信通知,等操作</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyPhone" id="data_CompanyPhone" value="<? echo $uinfo['CompanyPhone'];?>" />
                  <span class="red">*</span> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司传真：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyFax" id="data_CompanyFax" value="<? echo $uinfo['CompanyFax'];?>" /> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司地址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyAddress" id="data_CompanyAddress" value="<? echo $uinfo['CompanyAddress'];?>" /> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyEmail" id="data_CompanyEmail" value="<? echo $uinfo['CompanyEmail'];?>" /> </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_CompanyRemark" rows="5"  id="data_CompanyRemark"><? echo $uinfo['CompanyRemark'];?></textarea></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>


			<div class="line" align="right"><input type="button" name="subinfo" id="subinfo" value="保存资料" class="button_2" onclick="subeditinfo();" />&nbsp;&nbsp;<input type="button" name="subinfocancel" id="subinfocancel" value="返 回" class="button_3" onclick="canceleditinfo();" />&nbsp;&nbsp;&nbsp;&nbsp;</div>

				<INPUT TYPE="hidden" name="referer" value ="" >
				</form>
				</div>

            </div>
        	</div>
              
        <br style="clear:both;" />
    </div>
    

    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">上传图片</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">

        </div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>