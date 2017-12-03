<?php 
include_once ("header.php");
include_once ("../class/letter.class.php");
$menu_flag = "manager";

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$rinfo = $db->get_row("SELECT r.*,c.CompanySigned FROM ".DATABASEU.DATATABLE."_order_request r left join ".DATABASEU.DATATABLE."_order_company c on r.CompanyID=c.CompanyID where r.ID=".$in['ID']."  limit 0,1");
	if(empty($rinfo)) exit('您访问的数据不存在!');
	$uinfo = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$rinfo['CompanyID']." and UserFlag='9' order by UserID asc limit 0,1");

	$cinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$rinfo['CompanyID']." and ClientFlag=0 order by ClientID asc limit 0,1");
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
<script src="js/request.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="request.php">试用管理</a> &#8250;&#8250; <a href="#">查看</a></div>
   	        </div>         
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset  class="fieldsetstyle">		
			<legend>开通帐号资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">试用帐号：</div></td>
                  <td width="55%"><label><? echo $rinfo['CompanySigned'];?></label></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">开通时间：</div></td>
                  <td>  <? echo date("Y-m-d H:i",$rinfo['RequestDate']);?> </td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">到期时间：</div></td>
                  <td> <? echo $rinfo['EndDate'];?>  </td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">管理端帐号：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $uinfo['UserName'];?></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户端帐号：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['ClientName'];?> <?
				  if(!empty($cinfo['ClientMobile'])) echo " 或者 ".$cinfo['ClientMobile'];
				  ?></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">登录密码：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $rinfo['Password'];?></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
            </table>
           </fieldset>  

		   <div style="width:90%; margin:4px auto; height:30px; text-align:right;">
		   <? if($rinfo['SendFlag']=="T"){ ?>
			<input name="sendemailbutton1" type="button" class="button_4" disabled="disabled" id="sendemailbutton1" value="邮件已发送"  />
			 
		   <? }else{ ?>
		   <input name="sendemailbutton" type="button" class="button_2" id="sendemailbutton" value="发送到邮箱" onclick="sendtoemail(<? echo $rinfo['ID']?>);" />
		   <? }?>
		   </div>
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>客户资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    &nbsp;<? echo $rinfo['CompanyName'];?></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $rinfo['Contact'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">QQ：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $rinfo['QQ'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">手机：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $rinfo['Mobile'];?>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">电话：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $rinfo['Phone'];?>
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">邮箱：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $rinfo['Email'];?></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">主营产品/说明：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo nl2br($rinfo['Remark']);?>
                  &nbsp;</td>
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