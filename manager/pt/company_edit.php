<?php 
include_once ("header.php");
$menu_flag = "manager";
if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyID=".intval($in['ID'])." limit 0,1");
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

<script src="js/manager.js?v=<? echo VERID;?>3" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#CS_BeginDate").datepicker();
		$("#CS_EndDate").datepicker();
		$("#CS_UpDate").datepicker();
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
        	 <div class="locationl"><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="company_add.php">修改客户</a></div>
   	        </div>
            
            <div class="rightdiv sublink" style="padding-right:20px;">			
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_edit_company();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
            
			
			<legend>属性资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所属地区：</div></td>
                  <td width="55%"><label>
                  <select name="data_CompanyArea" id="data_CompanyArea" class="select2">
                    <option value="0">⊙ 请选择客户所在地区</option>
                    <? 
					$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city ORDER BY AreaParent ASC, AreaID ASC");
					echo ShowTreeMenu($sortarr,0,$cinfo['CompanyArea'],1);
					?>
                  </select>
                    <span class="red">*</span></label></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                  <td>
                  <select name="data_CompanyIndustry" id="data_CompanyIndustry" class="select2">
                    <option value="0">⊙ 请选择客户所属行业</option>
                    <? 
					$industryarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");
					foreach($industryarr as $ivar)
					{
						if($cinfo['CompanyIndustry'] == $ivar['IndustryID'])
						{
							echo '<option value="'.$ivar['IndustryID'].'" selected="selected">┠-'.$ivar['IndustryName'].'</option>';
						}else{
							echo '<option value="'.$ivar['IndustryID'].'">┠-'.$ivar['IndustryName'].'</option>';
						}
					}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所属代理商：</div></td>
                  <td>
                  <select name="data_CompanyAgent" id="data_CompanyAgent" class="select2">
                    <option value="0">⊙ 请选择客户所属代理商</option>
                    <? 
					$industryarr = $db->get_results("SELECT AgentID,AgentName,AgentContact FROM ".DATABASEU.DATATABLE."_order_agent ORDER BY AgentID ASC ");
					foreach($industryarr as $ivar)
					{
						if($cinfo['CompanyAgent'] == $ivar['AgentID'])
						{
							echo '<option value="'.$ivar['AgentID'].'" selected="selected">┠-'.$ivar['AgentName'].'('.$ivar['AgentContact'].')</option>';
						}else{
							echo '<option value="'.$ivar['AgentID'].'">┠-'.$ivar['AgentName'].'('.$ivar['AgentContact'].')</option>';
						}
					}
					?>
                  </select>
                  </td>
                  <td></td>
                </tr>
            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
		<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
              	<tr>
              		<td width="16%" bgcolor="#F0F0F0"><div align="right">医统账期：</div></td>
                        <td>
                            是<input type="radio" value="1" name ="CreditCheck" style="width: 5% !important;height: 15px;vertical-align: top;" 
                            <?php if($cinfo['CompanyCredit']==1){ echo "checked='checked'"; }?>>
                            否<input type="radio" value="0" name ="CreditCheck" style="width: 5% !important;height: 15px;vertical-align: top;" 
                            <?php if($cinfo['CompanyCredit']==0){ echo "checked='checked'"; }?>>
                        </td>
              	</tr>
                <tr>
                  	<td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                  	<td width="55%" bgcolor="#FFFFFF"><label>
                    	<input type="text" name="data_BusinessLicense" id="data_BusinessLicense" value="<? if(!empty($cinfo['BusinessLicense'])) echo $cinfo['BusinessLicense']; ?>" />
                    	<span class="red">*</span></label>
                    </td>
                  	<td width="29%" bgcolor="#FFFFFF">公司营业执照上的名称</td>
                </tr>
                <tr>
                  	<td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号：</div></td>
                  	<td width="55%" bgcolor="#FFFFFF"><label>
                    	<input type="text" name="data_IdentificationNumber" id="data_IdentificationNumber" value="<? if(!empty($cinfo['IdentificationNumber'])) echo $cinfo['IdentificationNumber']; ?>" />
                    	<span class="red">*</span></label>
                    </td>
                  	<td width="29%" bgcolor="#FFFFFF">营业执照号</td>
                </tr> 
                <tr>
	                  <td width="16%" bgcolor="#F0F0F0"><div align="right">订货系统名称：</div></td>
	                  <td width="55%" bgcolor="#FFFFFF"><label>
	                    <input type="text" name="data_CompanyName" id="data_CompanyName" value="<? echo $cinfo['CompanyName'];?>" />
						           <input type="hidden" name="ID" id="ID" value="<? echo $cinfo['CompanyID'];?>" />
	                    <span class="red">*</span></label></td>
	                  <td width="29%" bgcolor="#FFFFFF">在订货系统中显示的名称</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanySigned" id="data_CompanySigned" value="<? echo $cinfo['CompanySigned'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyPrefix" id="data_CompanyPrefix" value="<? echo $cinfo['CompanyPrefix'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">经销商帐号前缀</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyCity" id="data_CompanyCity" value="<? echo $cinfo['CompanyCity'];?>"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyContact" id="data_CompanyContact" value="<? echo $cinfo['CompanyContact'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">可以写多个</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyMobile" id="data_CompanyMobile" maxlength="11" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" value="<? echo $cinfo['CompanyMobile'];?>"  />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">用于短信通知，等操作(13************)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyPhone" id="data_CompanyPhone" value="<? echo $cinfo['CompanyPhone'];?>" />
                  </td>
                  <td bgcolor="#FFFFFF">可以写多个</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyFax" id="data_CompanyFax" value="<? echo $cinfo['CompanyFax'];?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;        </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyAddress" id="data_CompanyAddress" value="<? echo $cinfo['CompanyAddress'];?>" /></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyEmail" id="data_CompanyEmail" value="<? echo $cinfo['CompanyEmail'];?>" />&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyWeb" id="data_CompanyWeb" value="<? echo $cinfo['CompanyWeb'];?>"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyUrl" id="data_CompanyUrl" value="<? echo $cinfo['CompanyUrl'];?>"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_CompanyRemark" rows="5"  id="data_CompanyRemark"><? echo $cinfo['CompanyRemark'];?></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

			<br style="clear:both;" />
    <fieldset  class="fieldsetstyle">
			<legend>设置</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">用户数：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><input name="CS_Number" type="text" id="CS_Number" value="<? echo $cinfo['CS_Number'];?>"  maxlength="5" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;"  />&nbsp;<span class="red">*</span></td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(限制客户经销商的个数)</td>
                </tr>  
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">开通时间：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><input name="CS_BeginDate" type="text" id="CS_BeginDate" value="<? echo $cinfo['CS_BeginDate'];?>"  maxlength="12" onfocus="this.select();"    />&nbsp;<span class="red">*</span></td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(客户交费开通日期)</td>
                </tr> 
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">到期时间：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><input name="CS_EndDate" type="text" id="CS_EndDate"  value="<? echo $cinfo['CS_EndDate'];?>" maxlength="12" onfocus="this.select();"   />&nbsp;<span class="red">*</span></td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>
                </tr> 
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">最近更新时间：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><input name="CS_UpDate" type="text" id="CS_UpDate" value="<? echo date("Y-m-d");?>"  maxlength="12" onfocus="this.select();"    />&nbsp;</td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(更新续费日期)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">短信条数：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><input name="CS_SmsNumber" type="text" id="CS_SmsNumber" value="<? echo $cinfo['CS_SmsNumber'];?>"  maxlength="12" onfocus="this.select();" />&nbsp;</td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>
                </tr>
          </table>
          </fieldset>  

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_edit_company();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    
<?php include_once ("bottom.php");?>
	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠-";
		$selectmsg = "";
		
		if($var['AreaParent']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-2);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>