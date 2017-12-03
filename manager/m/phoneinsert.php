<?php 
$menu_flag = "sms";
$pope	   = "pope_view";
include_once ("header.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<script type="text/javascript">
function sub_insertphone() 
{
	var opval = document.getElementById('InsertPhone').value;
	var returnValue = '';
	if(opval.length > 11)
	{
		opvalarr	  = opval.split("\n");
		var alength = opvalarr.length;
		if(alength > 1)
		{
				for(i=0;i<alength;i++)
				{
						if(returnValue=='') {
							returnValue = opvalarr[i];
						} else {
							returnValue = returnValue + ',' + opvalarr[i];
						}
				}
		}else{
			returnValue = opval;
		}
	}else{
		returnValue = opval;
	}
	parent.insertphone(returnValue);
	document.getElementById('InsertPhone').value = "";
	document.getElementById('InsertPhone').focus();
}
</script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
thead tr td{font-weight:bold;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#f0f0f0; background:url(./img/f1s.jpg); }

.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
-->
</style>
</head>

<body>
	<div style="width:100%; height:340px; overflow:auto;">
          <form id="MainForm" name="MainForm" method="post" action="" target="" >
        	  <table width="98%" border="0" cellpadding="4" cellspacing="0"  align="center">               
     		  <tr>
       				 <td height="30"  bgcolor="#FFFFFF">					 
						<strong>请录入号码：</strong><br />
						<font color=red>注：</font>一行输入一个号码，小灵通请在前面加区号
					 </td>
   			  </tr>
     		  <tr>
       				 <td height="30"  bgcolor="#FFFFFF">					 
					 <textarea name="InsertPhone" id="InsertPhone" cols="" rows="" style="border:#CCCCCC solid 1px; width:100%; height:280px; clear:both; overflow:auto;" ></textarea>
					 </td>
   			  </tr>

 				</tbody>                
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
          <table width="96%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td  align="right" height="30"><input name="insertsubmit" id="insertsubmit" type="button" onclick="sub_insertphone();" class="redbtn" value=" 提 交 " />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="cancelsubmit" id="cancelsubmit" type="button" onclick="parent.closewindowui();" class="bluebtn" value=" 关 闭 " /></td>
     			 </tr>
          </table>
       
</body>
</html>