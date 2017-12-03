<?php 
include_once ("header.php");
include_once ("../class/letter.class.php");
$menu_flag = "manager";

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");

	if(empty($cinfo)) exit('您访问的数据不存在!');
	$uinfo = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$cinfo['CompanyID']." and UserFlag='9' order by UserID asc limit 0,1");

	//$clientinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$cinfo['CompanyID']." and ClientFlag=0 order by ClientID asc limit 0,1");
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
<script src="js/manager.js?v=33<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="#">帐号管理</a> &#8250;&#8250; <a href="#">发送帐号</a></div>
   	        </div>         
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset  class="fieldsetstyle">		
			<legend>开通正式帐号资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">       <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">用户数：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['CS_Number'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(限制客户经销商的个数)</td>
                </tr>  
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">开通时间：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_BeginDate'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(客户交费开通日期)</td>
                </tr> 
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">到期时间：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_EndDate'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>
                </tr> 
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">最近更新时间：</div></td>
                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_UpDate'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(更新续费日期)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">短信条数：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['CS_SmsNumber'];?></td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">管理端帐号：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="us" id="us" value="<? echo $uinfo['UserName'];?>" maxlength="18" style="width:280px;" />  <font color=red>*</font></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">管理端密码：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="pw" id="pw" value="" maxlength="18" style="width:280px;" />  <font color=red>*</font></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
            </table>
           </fieldset>  

		   <div style="width:90%; margin:4px auto; height:30px; text-align:right;">
		   <input name="resetemailbutton" type="button" class="redbtn"  id="resetemailbutton" value="开通帐号邮件" onclick="sendtoemail(<? echo $cinfo['CompanyID']?>);" />&nbsp;&nbsp;&nbsp;&nbsp;
		   <input name="sendemailbutton" type="button" class="bluebtn" id="sendemailbutton" value="重置帐号邮件" onclick="resetpasstoemail(<? echo $cinfo['CompanyID']?>);" />
		   </div>
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>客户资料</legend>
             <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">   
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">公司全称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $cinfo['CompanyName'];?>
					</label></td>
                  <td width="29%" bgcolor="#FFFFFF">公司名或店名</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanySigned'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPrefix'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyCity'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyContact'];?></td>
                  <td bgcolor="#FFFFFF">可以写多个</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyMobile'];?>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPhone'];?>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyFax'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyAddress'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyEmail'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyWeb'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyUrl'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><? echo nl2br($cinfo['CompanyRemark']);?>&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>