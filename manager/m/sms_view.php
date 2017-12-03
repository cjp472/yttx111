<?php 
$menu_flag = "sms";
$pope	       = "pope_view";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>

<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
thead tr td{font-weight:bold;}
.bluebtn {
    background: #3366CC; color: #FFF; font-weight: bold; font-size: 12px;  padding: .2em .3em !important; padding: .1em .2em; cursor: pointer; height:24px;
}
.darkbtn {
    background: #666666; color: #FFF;font-weight: bold; font-size: 12px; padding: .2em .3em !important; padding: .1em .2em; height:24px; cursor: pointer;
}
.title_green_w {color: #009933;	font-weight: bold; font-family:Verdana, "Times New Roman", Arial, Helvetica, sans-serif; }
-->
</style>
</head>

<body>
	<div style="width:100%; height:410px; overflow:auto; margin-top:10px;">
        	  <table width="96%" border="0" cellpadding="4" cellspacing="1"  align="center" bgcolor="#cccccc">               
     		  <?
		if(!intval($in['ID']))
		{
			exit('非法操作!');
		}else{	 
			$smsinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_sms_send  where PostCompany=".$_SESSION['uinfo']['ucompany']." and PostID=".intval($in['ID'])." limit 0,1");
		}
		if(empty($smsinfo['PostID'])) exit('此信息不存在，或已经删除!');
			  ?>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">发送时间：</td>
					 <td height="30"  bgcolor="#ffffff">	<? echo date("Y-m-d H:i",$smsinfo['PostDate']);?></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">接收号码：</td>
					 <td height="30"  bgcolor="#ffffff"><div style="word-wrap: break-word; word-break: normal; width:360px; height:120px; overflow:auto;"><? echo $smsinfo['PostPhone'];?></div></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">接收号码数：</td>
					 <td height="30"  bgcolor="#ffffff">	<? echo $smsinfo['PostNumber'];?></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">错误号码：</td>
					 <td height="30"  bgcolor="#ffffff">	<? echo $smsinfo['PostErrorPhone'];?></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">错误号码数：</td>
					 <td height="30"  bgcolor="#ffffff">	<? echo $smsinfo['PostErrorNumber'];?></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">发送短信数：</td>
					 <td height="30"  bgcolor="#ffffff">	<? echo $smsinfo['PostSmsCount'];?></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">短信内容：</td>
					 <td height="30"  bgcolor="#ffffff">	<? echo  str_replace("[".$_SESSION['uc']['CompanySigned']."]","",$smsinfo['PostContent']);?></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">发送状态：</td>
					 <td height="30"  bgcolor="#ffffff">	<? if($smsinfo['PostFlag']=="0") echo'<span class="title_green_w" title="发送成功" >√</span>'; else echo $smsinfo['PostFlag'];?></td>
   			  </tr>
			  <tr>
       				 <td height="30"  bgcolor="#efefef" width="25%" align="right">操作员：</td>
					 <td height="30"  bgcolor="#ffffff">	
					 <? 
					 $userinfo = $db->get_row("SELECT UserName,UserTrueName FROM ".DATABASEU.DATATABLE."_order_user  where UserID=".$smsinfo['PostUser']."  limit 0,1");
					 echo $userinfo['UserTrueName']." (".$userinfo['UserName'].")";
					 ?></td>
   			  </tr>
 				</tbody>                
              </table>
              </form>
       	  </div>       
</body>
</html>