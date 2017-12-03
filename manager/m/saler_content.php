<?php 
$menu_flag = "saler";
$pope	   = "pope_view";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$uinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserID=".intval($in['ID'])." and UserType='S' limit 0,1");
}
$optionmsg = '';
if(!empty($uinfo))
{
	$cinfo = $db->get_results("select c.ClientID,c.ClientCompanyName FROM ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$uinfo['UserID']."");
	if(!empty($cinfo))
	{
		foreach($cinfo as $cv)
		{
			$optionmsg .=  '<option value="'.$cv['ClientID'].'">'.$cv['ClientCompanyName'].'</option>';
			if(empty($cidmsg)) $cidmsg = $cv['ClientID']; else $cidmsg .= ",".$cv['ClientID'];
		}
	}
	$pope_info = $db->get_results("SELECT pope_module,pope_view,pope_form,pope_audit FROM ".DATABASEU.DATATABLE."_order_pope where pope_company=".$_SESSION['uinfo']['ucompany']." and pope_user=".$uinfo['UserID']." ");
	$popearr = null;
	if(!empty($pope_info))
	{
		foreach($pope_info as $v)
		{
			$popearr[$v['pope_module']] = $v;
		}
	}

}else{
	exit('参数错误!');
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
<script src="js/saler.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<input name="UserID" type="hidden" id="UserID" value="<? echo $uinfo['UserID'];?>"  />
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>&nbsp;&nbsp;当前位置：</strong><a href="saler.php">客情官</a> &#8250;&#8250; <a href="#">查看客情官帐号</a></div>
   	        </div>            
  
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">   		
			<legend>登陆信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">          
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登陆帐号：</div></td>
                  <td width="55%"><label>
                    <? echo $uinfo['UserName'];?>
                    </label></td>
                  <td width="29%">可以是数字、字母、下划线(3-18位)</td>
                </tr>

            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">             
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">姓名：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $uinfo['UserTrueName'];?>
                    </label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">管辖区域：</div></td>
                  <td bgcolor="#FFFFFF"> 
                    <? 
					$sortarr = $db->get_row("SELECT AreaID,AreaParentID,AreaName,AreaPinyi,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and AreaID=".$uinfo['UserPhone']." ORDER BY AreaID ASC limit 0,1");
					echo $sortarr['AreaName'];
					?>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['UserRemark'];?>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>


            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>管理权限</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" >
                <tr>
                  <td  bgcolor="#F0F0F0" width="16%"><div align="right">管辖药店：</div></td>
                  <td ><label>
                    <input name="Shield" type="hidden" id="Shield" value="<? if(!empty($cidmsg)) echo $cidmsg; ?>"   />
					<div style="height:300px; width:450px; padding:4px; float:left; ">
					<select name="selectshield"  id="selectshield" size="8" style="height:300px; width:440px; padding:4px; float:left; " >
					<?
						echo $optionmsg;
					?>
					</select>
					</div>

                 </label></td>
                  <td width="16%">&nbsp;</td>
                </tr>
              <tr>
                  <td bgcolor="#F0F0F0"><div align="right">订单管理权限：</div></td>
                  <td bgcolor="#FFFFFF">                  
				<table width="82%" border="0" cellpadding="1" cellspacing="2" bgcolor="#FFFFFF"  >
				<tr>
				  <td  bgcolor="#cccccc" align="center"><strong>订单</strong></td>
                  <td width="20%" bgcolor="#efefef" align="center">新增 <input type="checkbox" style="width:18px; height:18px; border:0;"  name="view_order" id="view_order" value="Y" <? if(!empty($popearr['order']['pope_view']) && $popearr['order']['pope_view']=="Y") echo 'checked="checked"'; ?> disabled="disabled"  /></td>
                  <td width="20%" bgcolor="#efefef" align="center">编辑 <input type="checkbox"  style="width:18px; height:18px; border:0;" name="form_order" id="form_order" value="Y"  <? if(!empty($popearr['order']['pope_form']) && $popearr['order']['pope_form']=="Y") echo 'checked="checked"'; ?> disabled="disabled" /></td>
				  <td width="20%"  bgcolor="#efefef" align="center">审核 <input type="checkbox" style="width:18px; height:18px; border:0;"  name="audi_order" id="audi_order" value="Y" <? if(!empty($popearr['order']['pope_audit']) && $popearr['order']['pope_audit']=="Y") echo 'checked="checked"'; ?> disabled="disabled" /></td>
				  <td>&nbsp;</td>
                </tr>

				<tr>
				  <td  bgcolor="#cccccc" align="center"><strong>发 货</strong></td>
                  <td  bgcolor="#efefef" align="center">查看 <input type="checkbox" style="width:18px; height:18px; border:0;"  name="view_consignment" id="view_consignment" value="Y"  <? if(!empty($popearr['consignment']['pope_view']) && $popearr['consignment']['pope_view']=="Y") echo 'checked="checked"'; ?> disabled="disabled" /></td>
                  <td  bgcolor="#efefef" align="center">&nbsp;</td>
				  <td   bgcolor="#efefef" align="center">审核 <input type="checkbox" style="width:18px; height:18px; border:0;"  name="audi_consignment" id="audi_consignment" value="Y" <? if(!empty($popearr['consignment']['pope_audit']) && $popearr['consignment']['pope_audit']=="Y") echo 'checked="checked"'; ?> disabled="disabled" /></td>
				  <td>&nbsp;</td>
                </tr>
				<tr>
				  <td  bgcolor="#cccccc" align="center"><strong>库存</strong></td>
                  <td  bgcolor="#efefef" align="center">查看 <input type="checkbox" style="width:18px; height:18px; border:0;"  name="view_inventory" id="view_inventory" value="Y" <? if(!empty($popearr['inventory']['pope_view']) && $popearr['inventory']['pope_view']=="Y") echo 'checked="checked"'; ?> disabled="disabled"  /></td>
                  <td  bgcolor="#efefef" align="center">&nbsp;</td>
				  <td   bgcolor="#efefef" align="center"></td>
				  <td>&nbsp;</td>
                </tr>
				<tr>
				  <td  bgcolor="#cccccc" align="center"><strong>药店</strong></td>
                  <td  bgcolor="#efefef" align="center">查看 <input type="checkbox" style="width:18px; height:18px; border:0;"  name="view_client" id="view_client" value="Y" <? if(!empty($popearr['client']['pope_view']) && $popearr['client']['pope_view']=="Y") echo 'checked="checked"'; ?>  disabled="disabled" /></td>
                  <td  bgcolor="#efefef" align="center">编辑 <input type="checkbox"  style="width:18px; height:18px; border:0;" name="form_client" id="form_client" value="Y" <? if(!empty($popearr['client']['pope_form']) && $popearr['client']['pope_form']=="Y") echo 'checked="checked"'; ?> disabled="disabled" /></td>
				  <td   bgcolor="#efefef" align="center">&nbsp;</td>
				  <td>&nbsp;</td>
                </tr>
              </table>
			  </td>
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