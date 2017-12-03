<?php 
$menu_flag = "system";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$uinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserID=".intval($in['ID'])." limit 0,1");
}
if($in['ID']=="1" || $in['ID']=="3")
{
	if($_SESSION['uinfo']['userid'] != "1") exit('非法路径!');
}

$pope_info = $db->get_results("SELECT pope_module,pope_view,pope_form,pope_audit FROM ".DATABASEU.DATATABLE."_order_pope where pope_company=".$_SESSION['uinfo']['ucompany']." and pope_user=".$uinfo['UserID']." ");
$popearr = null;
if(!empty($pope_info))
{
	foreach($pope_info as $pvar)
	{
		$popearr[$pvar['pope_module']] = $pvar;
	}
}
$cinfo = $db->get_results("select ID,Name FROM ".DATATABLE."_order_content_index  where CompanyID=".$_SESSION['uinfo']['ucompany']." and AgentID=".$in['ID']."");
if(!empty($cinfo))
{
        foreach($cinfo as $cv)
	{
		$optionmsg .=  '<option value="'.$cv['ID'].'">'.$cv['Name'].'</option>';
		if(empty($cidmsg)) $cidmsg = $cv['ID']; else $cidmsg .= ",".$cv['ID'];
	}
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
<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
	      <input name="UserID" type="hidden" id="UserID" value="<? echo $uinfo['UserID'];?>"  />
		
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>&nbsp;&nbsp;当前位置：</strong><a href="user.php">帐号管理</a> &#8250;&#8250; <a href="#">修改帐号</a></div>
   	        </div>
            		
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_user();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
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
                    <input name="data_UserName" type="text" id="data_UserName" value="<?php echo $uinfo['UserName'];?>"  maxlength="18" />
                    <span class="red">*</span></label></td>
                  <td width="29%">可以是数字、字母、下划线(3-18位)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">登陆密码：</div></td>
                  <td><input name="data_UserPass" type="text" id="data_UserPass" value=""  maxlength="18" />
                  <span class="red">*</span></td>
                  <td><font color=red>不修改密码时请不要填写！</font></td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">账号类型：</div></td>
                  <td width="55%">
                      <label>
                              <select id="CompanyType" name="UserFlag"  class="select2" >
                                  <?php  if($uinfo['UserFlag'] == 2){ ;?>
                                  <option value="2" >代理商</option>
                                  <?php }else{?>
                                  <option value="0" >商业公司</option>
                                  <?php }?>
                              </select>
                    <span class="red">*</span></label>
                  </td>
                  <td width="29%">可以是数字、字母、下划线(3-18位)</td>
                </tr>
            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset class="fieldsetstyle">
		<legend>基本资料</legend>
                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">姓名：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_UserTrueName" id="data_UserTrueName" value="<? echo $uinfo['UserTrueName'];?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF">
                    <label>
                         <input type="text" name="data_UserMobile" id="data_UserMobile" value="<? echo $uinfo['UserMobile'];?>"  />
                         <span class="red">*</span>
                     </label>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">部门职位：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_UserPhone" id="data_UserPhone" value="<? echo $uinfo['UserPhone'];?>"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>              

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_UserRemark" rows="5"  id="data_UserRemark"><? echo $uinfo['UserRemark'];?></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>
	<?php if($uinfo['UserFlag']!="9"){ ?>
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>权限</legend>
                        <table width="66%" border="0" cellpadding="4" cellspacing="1" bgcolor="#cccccc" style="float: left;">             
               <thead>
		<tr>
                  <td bgcolor="#F0F0F0" align="center">模块名称</td>
                  <td width="20%" bgcolor="#F0F0F0" align="center">查看 <input type="checkbox" onclick="CheckAll(this.form,'view_')" name="<? echo "view_";?>" id="<? echo "view_";?>" value="Y" style="border:0;" /></td>
                  <td width="20%" bgcolor="#F0F0F0" align="center">编辑 <input type="checkbox" onclick="CheckAll(this.form,'form_')" name="<? echo "form_";?>" id="<? echo "form_";?>" value="Y" style="border:0;" /></td>
                  <td width="20%" bgcolor="#F0F0F0" align="center">审核 <input type="checkbox" onclick="CheckAll(this.form,'audi_')" name="<? echo "audi_";?>" id="<? echo "audi_";?>" value="Y" style="border:0;" /></td>
                </tr>
			   </thead>
			   <tbody>
                            <?php if($uinfo['UserFlag']=="2"){   ?>
                                    <?php 
                                    foreach($menu_arr11 as $pkey=>$pvar)
                                    {
                                            if($pkey == 'system') continue;
                                    ?>				
                                    <tr>
                                      <td align="center" bgcolor="#FFFFFF"><strong><? echo $pvar;?></strong></td>
                                      <td  bgcolor="#FFFFFF" align="center"><input type="checkbox" name="<? echo "view_".$pkey;?>" id="<? echo "view_".$pkey;?>" value="Y" style="border:0;" <? if($popearr[$pkey]['pope_view']=="Y") echo 'checked="checked"';?> /></td>
                                      <td  bgcolor="#FFFFFF" align="center"><input type="checkbox" name="<? echo "form_".$pkey;?>" id="<? echo "form_".$pkey;?>" value="Y" style="border:0;" <? if($popearr[$pkey]['pope_form']=="Y") echo 'checked="checked"';?> /></td>
                                    <td bgcolor="#FFFFFF" align="center"><input type="checkbox" name="<? echo "audi_".$pkey;?>" id="<? echo "audi_".$pkey;?>" value="Y" style="border:0;" <? if($popearr[$pkey]['pope_audit']=="Y") echo 'checked="checked"';?> /></td>
                                    </tr>				
                                    <?php }?>
                            <?php }else{?>
                                    <?php 
                                    foreach($pope_arr as $pkey=>$pvar)
                                    {
                                            if($pkey == 'system') continue;
                                    ?>				
                                    <tr>
                                      <td align="center" bgcolor="#FFFFFF"><strong><? echo $pvar;?></strong></td>
                                      <td  bgcolor="#FFFFFF" align="center"><input type="checkbox" name="<? echo "view_".$pkey;?>" id="<? echo "view_".$pkey;?>" value="Y" style="border:0;" <? if($popearr[$pkey]['pope_view']=="Y") echo 'checked="checked"';?> /></td>
                                      <td  bgcolor="#FFFFFF" align="center"><input type="checkbox" name="<? echo "form_".$pkey;?>" id="<? echo "form_".$pkey;?>" value="Y" style="border:0;" <? if($popearr[$pkey]['pope_form']=="Y") echo 'checked="checked"';?> /></td>
                                    <td bgcolor="#FFFFFF" align="center"><input type="checkbox" name="<? echo "audi_".$pkey;?>" id="<? echo "audi_".$pkey;?>" value="Y" style="border:0;" <? if($popearr[$pkey]['pope_audit']=="Y") echo 'checked="checked"';?> /></td>
                                    </tr>				
                                    <?php }?>
                            <?php }?>                                
                    <?php }?>        
				</tbody>
              </table>
                        <?php if($uinfo['UserFlag'] == "2"){ ?>
                        <table border="0" cellpadding="4" cellspacing="0" id="ComTable" width="30%" style="float: right;border: 1px solid #cbcbcd;display: block">
                            <tr>
                                <td colspan="2" style="border-bottom: 1px solid #cbcbcd" bgcolor="#F0F0F0">管辖产品</td>  
                            </tr>
                            <tr>
                                <td width="80%"><label>
                                  <input name="Shield" type="hidden" id="Shield" value=""   />
                                                      <div style="height:300px;float:left;width: 100%;padding: 0 0 4px 0;">
                                                      <select name="selectshield"  id="selectshield" size="8" style="height:300px;float:left;width: 100%;" >
                                                        <?
                                                            echo $optionmsg;
                                                        ?>
                                                      </select>
                                                      </div>
                                </td>  
                                <td width="20%">
                                                      <div style="height:100px; width:80px; padding:4px; float:left; ">
                                                              <div ><p>&nbsp;</p><input name="b1" id="shield_b1" type="button"  value="删除" onClick="del_client();" class="bluebtn" title="删除选中的药店"  style="width:80px; height:24px; font-size:12px;" /></div>
                                                              <div style="margin-top:8px;"><input name="b2"  id="shield_b2" type="button"  value="清空" onClick="clear_client();" class="bluebtn" title="清除所有药店" style="width:80px; height:24px; font-size:12px;"  /></div>
                                                              <div style="margin-top:8px;"><input name="b4"  id="shield_b4" type="button" value="选择" onClick="set_shield_client();" class="bluebtn" title="选择您要管辖的药店" style="width:80px; height:24px; font-size:12px;"  /></div>
                                                      </div>
                               </label></td>
<!--                                <td>&nbsp;</td>-->
                          </tr>
                        </table>
                        <?php }?>
                        </fieldset>
			<br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_user();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">选择药店</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">

        </div>
	</div>
</body>
</html>