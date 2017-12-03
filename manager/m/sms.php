<?php 
$menu_flag = "sms";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("../WebService/include/Client.php");
include_once ("../soap2.inc.php");

$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
$client->setOutgoingEncoding("UTF-8");
$statusCode = $client->login();
$phonearr = null;
setcookie("backurl", $_SERVER['REQUEST_URI']);
if(!empty($in['PID']))
{
	$MsgData = $db->get_row("SELECT PostPhone,PostContent FROM ".DATATABLE."_order_sms_send where PostCompany = ".$_SESSION['uinfo']['ucompany']." and PostID=".$in['PID']." limit 0,1");
	$Msg = str_replace("[".$_SESSION['uc']['CompanySigned']."]","",$MsgData['PostContent']);
	$Msg = str_replace("【".$_SESSION['uc']['CompanySigned']."】","",$MsgData['PostContent']);
	if(!empty($in['z']))
	{
		$phonearr = explode(";",$MsgData['PostPhone']);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/sms.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        		<tr>
					<td width="50%" align="left" height="28"><strong>当前位置：</strong><a href="#">发短信</a> </td>
        			<td width="50%" align="right">
					
					<? echo '您目前的短信余额为 <span class="font12" id="sms_number_id">'.$_SESSION['uc']['SmsNumber'].'</span> 条。';  ?></td>
				</tr>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >


		<form id="newsms" name="newsms" method="post" action="">
		<fieldset class="fieldsetstyle">
		<legend>短信发送 (包括单发和群发，可以向单个或多个手机号码发送相同信息)</legend>
	  
			 <div style="margin:4px auto; padding-left:4px; line-height:200%; width:95%; border-bottom:#cccccc solid 1px; text-align:left;">
			<strong>操作：</strong>&nbsp;&nbsp;1.在“输入手机码”中输入手机号码 ;
			&nbsp;&nbsp;2. 在短信栏填写短信内容。
			</div>

		<div style="border:#CCCCCC solid 0px; width:95%; height:230px; clear:both; margin:4px auto;">
    	<div style="border:#CCCCCC solid 0px; width:400px; height:220px; float:left; margin:2px; padding:2px;">
    	  <fieldset style="width:90%; padding:4px;" align="left">
      		<legend> 接收号码列表: (已添加 <span class="font12h" id="shownumberlength"><?
			if(!empty($phonearr)) echo count($phonearr); else echo '0';
			?></span> 个号码)</legend>
    	  <select name="postlistno" id="postlistno" size="15" style="width:360px; height:200px; overflow:hidden; border:0;">
			<? if(!empty($phoneinsert)) echo '<option value="'.$phoneinsert.'">'.$phoneinsert.'</option>';?>
			<? 
			if(!empty($phonearr))
			{
				foreach($phonearr as $phvar)
				{
					echo '<option value="'.$phvar.'">'.$phvar.'</option>';
				}
			}			
			?>
  	      </select>
    	  </fieldset>
    	</div> 
        
         <div style="border:#CCCCCC solid 0px; width:400px; height:220px; float:left; margin:4px;">
			<div style="float:left;margin:1px; width:380px; text-align:left">
    		<fieldset style="width:100%; padding:4px;" align="left">
      		<legend> 输入手机号码 </legend>
            <div style="margin:4px;line-height:160%;">
            	<div style="margin-bottom:8px;">号码输入：<input name="mobileno" id="mobileno" type="text" maxlength="11" size="18" style="width:160px;" onKeyDown="javascript: if(window.event.keyCode == 13) addphone();" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" title="请输入11位手机或小灵通号(小灵通请在前面加区号)" />&nbsp;&nbsp;<input type="button" class="redbtn" value="提交" onclick="addphone();" /></div>
            	<div><input type="button" class="bluebtn" value="批量号码录入" onclick="insertmulphone();" />
            	&nbsp;&nbsp;
           	      <input type="button" name="button3" id="button3" class="bluebtn" value="从通讯录中提取" onclick="insert_phonebook_phone();"  />
            	</div>
             </div>
		</fieldset>
		</div>
        
        	<div style="float:left; margin:4px; width:350px;  padding-top:10px; line-height:200%;">
			<div>
         		<div style="clear:both; height:34px;"><input type="button" name="button" id="button" class="bluebtn" value="删除选中的号码" onclick="delphone();" /></div>
         		<div style="clear:both; height:34px;"><input type="button" name="button" id="button" class="bluebtn" value="清除所有号码" onclick="clearphone();" /></div>
            	<div style="clear:both; height:34px;"><input type="button" name="button" id="button" class="bluebtn" value="过滤重复号码" onclick="clearrepeat();" /></div>
			</div>
        	</div>
         </div>
    </div>    


<div style="margin:24px auto; line-height:150%; width:95%; ">
    <div style="margin:4px; line-height:200%;">
       <div style="margin-top:10px; line-height:200%;"><font color="red">发送短信的内容：</font>&nbsp;&nbsp;您已写了<INPUT readonly VALUE="<? if(!empty($Msg)) echo countStrLength($Msg); else echo '0'; ?>" TYPE="text" NAME="chrLen" id="chrLen" size=2 maxlength=3/>个字数，共
								<INPUT readonly VALUE="0" TYPE="text" NAME="smsLen" size=1 maxlength=1 />条短信，当前通道允许<span id="MaxLen" style="color:#FF6600">60</span>字/条</div>
                                
       <div> <textarea name="Msg" id="Msg" cols="" rows="" style="border:#CCCCCC solid 1px; width:90%; height:60px; clear:both; margin-top:4px; overflow:auto;" onKeyUp="smsCount(this.form,2);" onChange="smsCount(this.form,2);"><? if(!empty($Msg)) echo $Msg; ?></textarea>
   		</div>
       

		<div class="warning">
			接移动运营商通知，近期国家相关部门正在进行垃圾短信全面整顿，短信群发功能暂时关闭，系统通知短信不受此影响！
		</div>
        <div style="margin:8px; clear:both;float:left; display:none;" ><input type="button" class="button_1" value=" 发 送 " name="postmsgbutton" id="postmsgbutton" onclick="sentmsg()" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="button2" type="button" class="button_3" id="button2" value="插入模板" onclick="insert_template_select();" />
        </div>       
    </div>

	      </fieldset >
		  </form>
          </div>
              
        <br style="clear:both;" />
    </div>
    

    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">录入号码</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"> <div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>