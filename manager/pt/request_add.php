<?php
$menu_flag = "manager";
include_once ("header.php");
include_once ("../class/letter.class.php");
$rinfo = null;
if(intval($in['ID']))
{
	$rinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_request where ID=".$in['ID']."  limit 0,1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/request.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#data_EndDate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="request.php">试用管理</a> &#8250;&#8250; <a href="request_add.php">开通试用</a></div>
   	        </div>
            
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_request();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">		
			<legend>属性资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">试用帐号：</div></td>
                  <td width="55%"><label>
                  <select name="data_CompanyID" id="data_CompanyID"  style="line-height:30px;">
                    <option value="0">⊙ 请选择试用帐号</option>
                    <?
					$letter  = new letter();					
					$datasql   = "SELECT c.CompanyID,c.CompanySigned,s.CS_EndDate FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID!=1 and c.CompanyFlag='0' ".$sqlmsg."  ORDER BY c.CompanyID ASC";
					$list_data = $db->get_results($datasql);
					foreach($list_data as $var)
					{
						if($var['CompanyID'] == "1") continue;
						$pinyima = $letter->C($var['CompanySigned']);
						if($rinfo['CompanyID']==$var['CompanyID']) $smsg = 'selected="selected"'; else $smsg = '';
						echo '<option value="'.$var['CompanyID'].'" '.$smsg.'>'.substr($pinyima,0,4).' - '.$var['CompanySigned'].' - '.$var['CS_EndDate'].'</option>';
					}
					?>
                  </select>
                    <span class="red">*</span></label></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">到期时间：</div></td>
                  <td>
                  <input name="data_EndDate" type="text" id="data_EndDate" maxlength="12" onfocus="this.select();" value="<? echo date('Y-m-d',strtotime('+3 days'));?>"   />&nbsp;
                  <span class="red">*</span></td>
                  <td></td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">登录密码：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_Password" id="data_Password" maxlength="18" value="<? if(!empty($rinfo['Password'])) echo $rinfo['Password'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>客户资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_CompanyName" id="data_CompanyName" value="<? if(!empty($rinfo['CompanyName'])) echo $rinfo['CompanyName'];?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;【<a href="javascript:void(0);" onclick="format_content()">贴粘邮件内容</a>】</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_Contact" id="data_Contact" value="<? if(!empty($rinfo['Contact'])) echo $rinfo['Contact'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">QQ：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_QQ" id="data_QQ" value="<? if(!empty($rinfo['QQ'])) echo $rinfo['QQ'];?>"  />&nbsp;<span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">手机：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_Mobile" id="data_Mobile" maxlength="11" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;"  value="<? if(!empty($rinfo['Mobile'])) echo $rinfo['Mobile'];?>" />
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_Phone" id="data_Phone" value="<? if(!empty($rinfo['Phone'])) echo $rinfo['Phone'];?>" />
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">邮箱：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_Email" id="data_Email" value="<? if(!empty($rinfo['Email'])) echo $rinfo['Email'];?>" />&nbsp;<span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">QQ和邮箱必需输入一个</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">主营产品/说明：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_Remark" rows="5"  id="data_Remark"><? if(!empty($rinfo['Remark'])) echo $rinfo['Remark'];?></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_request();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
	    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">复制邮件中的客户资料：</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
				<p><textarea name="emailcontent" rows="5"  id="emailcontent" style="width:100%; height:250px; margin:8px;"></textarea></p>
				<p align="right">
				<input name="formartcontent" type="button" class="button_1" id="formartcontent" value="提 交" onclick="do_formart();" /></p>
        </div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>