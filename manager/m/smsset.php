<?php 
$menu_flag = "system";
include_once ("header.php"); 
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
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="companyinfo_edit.php">公司信息</a></div>
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
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<fieldset  class="fieldsetstyle">		
			<legend>短信通知设置(勾选以下您使用的短信通知项目)</legend>
            <br style="clear:both;" />
			<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >                
                <?
				$valuearr = array("0");
				$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='sms' limit 0,1");
				if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);

				$uinfo = $db->get_row("SELECT CompanyID,CompanyMobile FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$_SESSION['uinfo']['ucompany']." limit 0,1");

				$typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_smstype ORDER BY TypeID asc Limit 0,50");
				foreach($typedata as $tvar)
				{
				    if($tvar['TypeID'] == '1')
				    {
				?>
    				<tr>
                      <td height="30" width="20%" bgcolor="#F0F0F0"><input type="checkbox" name="smstypeID[]" id="select_<? echo $tvar['TypeID'];?>" value="<? echo $tvar['TypeID'];?>" <? if (in_array($tvar['TypeID'], $valuearr)) echo 'checked="checked"';?> /><strong>&nbsp;<? echo $tvar['TypeName'];?>：</strong></td>
                      <td ><? echo $tvar['TypeAbout'];?></td>
                    </tr>
                    <tr>
                      <td height="30" width="20%" bgcolor="#F0F0F0" style="text-align: right;"><input type="checkbox" name="smstypeID[]" id="select_9" value="9" <? if (in_array('9', $valuearr)) echo 'checked="checked"';?> /><strong>&nbsp;<? echo $typedata[count($typedata)-1]['TypeName'];?>：</strong></td>
                      <td ><? echo $typedata[count($typedata)-1]['TypeAbout'];?></td>
                    </tr>
				<?
				    }
				    else if($tvar['TypeID'] == '9') continue;
				    else 
				    {?>
				        <tr>
                          <td height="30" width="20%" bgcolor="#F0F0F0"><input type="checkbox" name="smstypeID[]" id="select_<? echo $tvar['TypeID'];?>" value="<? echo $tvar['TypeID'];?>" <? if (in_array($tvar['TypeID'], $valuearr)) echo 'checked="checked"';?> /><strong>&nbsp;<? echo $tvar['TypeName'];?>：</strong></td>
                          <td ><? echo $tvar['TypeAbout'];?></td>
                        </tr>
				    <?}
				}	
				?>
                <tr>
                  <td >&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><strong>管理员手机号：</strong></td>
                  <td><input type="text" name="MainPhone" id="MainPhone" style="width:54%;" value="<?
				  if(empty($valuearr['Mobile']['MainPhone']))
				  {
					  if(!empty($uinfo['CompanyMobile'])) echo $uinfo['CompanyMobile'];
				  }else{
					 echo  $valuearr['Mobile']['MainPhone'];
				  }
				  ?>" />&nbsp;&nbsp;主要信息接收号码,多个号码之间以英文半角逗号(,)隔开</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><strong>财务手机号：</strong></td>
                  <td><input type="text" name="FinancePhone" id="FinancePhone" style="width:54%;" value="<? if(!empty($valuearr['Mobile']['FinancePhone'])) echo $valuearr['Mobile']['FinancePhone']; ?>" />&nbsp;&nbsp;收款信息接收号码,多个号码之间以英文半角逗号(,)隔开</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><strong>库管手机号：</strong></td>
                  <td><input type="text" name="LibaryPhone" id="LibaryPhone" style="width:54%;" value="<? if(!empty($valuearr['Mobile']['LibaryPhone'])) echo $valuearr['Mobile']['LibaryPhone']; ?>" />&nbsp;&nbsp;发货信息接收号码,多个号码之间以英文半角逗号(,)隔开</td>
                </tr>

                <tr>
                  <td ><div align="right"></div></td>
                  <td><input type="button" name="sendbuttoninfo" id="sendbuttoninfo" value="保存短信通知" class="button_2" onclick="savesettype('sms');" /></td>
                </tr>
            </table><br style="clear:both;" />
           </fieldset>  
            
			</form>
			<br style="clear:both;" />
            </div>
        	</div>              
        <br style="clear:both;" />
     </div>
	 </div>
    

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>