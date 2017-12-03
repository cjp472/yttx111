<?php 
include_once ("header.php");
$menu_flag = "home";
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

<script src="js/login.js?v=4<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>

<? include_once ("top.php");?>
    
    <div id="bodycontent">
    	<div class="lineblank"></div>         	
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>&nbsp;&nbsp;当前位置：</strong><a href="home.php">系统首页</a> &#8250;&#8250; <a href="#">修改密码</a></div>
   	        </div>                       
        </div>

    	
        <div class="line2"></div>
		<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
        <div class="bline" >

			<fieldset title="“*”为必填项！" class="fieldsetstyle">            
			
			<legend>修改密码</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登陆帐号：</div></td>
                  <td width="55%"><label>
                   <? echo $_SESSION['uinfo']['username'];?>
                    </label></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">原密码：</div></td>
                  <td><input name="data_OldPass" type="password" id="data_OldPass" value=""  maxlength="18" />
                  <span class="red">*</span></td>
                  <td>可以是数字、字母、下划线(3-18位)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">新密码：</div></td>
                  <td><input name="data_NewPass" type="password" id="data_NewPass" value=""  maxlength="18" />
                  <span class="red">*</span></td>
                  <td>可以是数字、字母、下划线(3-18位)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">确认密码：</div></td>
                  <td><input name="data_ConfirmPass" type="password" id="data_ConfirmPass" value=""  maxlength="18" />
                  <span class="red">*</span></td>
                  <td>可以是数字、字母、下划线(3-18位)</td>
                </tr>
            </table>
           </fieldset>            

			<br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="do_save_change();" >保 存</a></li><li><a href="home.php">返 回</a></li></ul></div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
<?php
	$datasql = "SELECT UserID,OpenID,QQ FROM ".DATABASEU.DATATABLE."_order_qq where UserID=".$_SESSION['uinfo']['userid']." and UserType='M' limit 0,10";
	$ulinfo  = $db->get_results($datasql);
	if(!empty($ulinfo)){
?>
    	
        <div class="line2"></div>
		<form id="MainForm2" name="MainForm2" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<div class="bline" >

			<fieldset class="fieldsetstyle">
			<legend>QQ 同步登录帐号</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
               <?php
				foreach($ulinfo as $v){
			   ?>                 
                <tr>
                  <td width="30%" height="32">订货宝帐号：<?php echo $_SESSION['uinfo']['username'];?></td>
                  <td width="30%">绑定的QQ：<?php echo $v['QQ'];?></td>
                  <td ><input type="button" name="cancelbutton" id="cancelbutton" value="解除绑定" class="button_2" onclick="do_cancel_qq('<?php echo $v['OpenID'];?>');" /></td>
                </tr>
				<?php }?>
            </table>
           </fieldset>
        	</div>
              </form>
        <br style="clear:both;" />
<?php }?>
    </div>
    

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>