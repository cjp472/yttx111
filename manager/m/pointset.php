<?php 
$menu_flag = "system";
include_once ("header.php");
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
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">积分设置</a></div>
   	        </div>     
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong>系统设置</strong></div>
<!-- 系统设置菜单开始 -->
<?php include_once("inc/system_set_left_bar.php")  ;?>
<!-- 系统设置菜单结束 -->
<br style="clear:both;" />

</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">
			<div id="oldinfo" class="line">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<fieldset  class="fieldsetstyle">		
			<legend>积分设置</legend>
            <br style="clear:both;" />
			<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >                
                <?
				$valuearr = null;
				$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='point' limit 0,1");
				if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);

				?>
				<tr>
                  <td height="100" width="28%" bgcolor="#F0F0F0" align="right"><strong>积分计算方式：</strong></td>
                  <td >
						<div style="height:32px; clear:both;"><input type="radio" name="pointtype" id="pointtype_1" value="1" <? if($valuearr['pointtype'] == "1" || empty($valuearr)) echo 'checked="checked"';?> style="border:0;" onclick="selectpointclick();" /><strong>&nbsp;不使用积分</strong></div>
						<div style="height:32px; clear:both;"><input type="radio" name="pointtype" id="pointtype_2" value="2" <? if($valuearr['pointtype'] == "2") echo 'checked="checked"';?> style="border:0;" onclick="selectpointclick2();"  /><strong>&nbsp;按订单商品总价格计算积分 </strong></div>
						<div style="height:32px; clear:both;"><input type="radio" name="pointtype" id="pointtype_3" value="3" <? if($valuearr['pointtype'] == "3") echo 'checked="checked"';?> style="border:0;"  onclick="selectpointclick();"  /><strong>&nbsp;为商品单独设置积分 </strong>(在添加商品的时候，输入积分。)</div>	
				  </td>
                </tr>

				<tr id="showpencentid" <? if($valuearr['pointtype'] != "2") echo 'style="display:none;"'; ?> >
				<td height="40"  bgcolor="#F0F0F0" align="right"><strong>积分换算比率：</strong></td>
				<td><input type="text" name="pointpencent" id="pointpencent" maxlength="5" size="5" value="<? if(!empty($valuearr['pointpencent'])) echo $valuearr['pointpencent']; ?>" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" /> &nbsp;&nbsp;<br />(订单所得积分 = 订单商品总价格 X 积分换算比率。示例：2元钱积1分比率为 ”0.5“)</td>
				</tr>

                <tr>
                  <td ><div align="right"></div></td>
                  <td><input type="button" name="sendbuttoninfo" id="sendbuttoninfo" value="保存设置" class="button_2" onclick="savesettype('point');" /></td>
                </tr>
            </table>
			<br style="clear:both;" />
           </fieldset>  
            
			</form>
			<br style="clear:both;" />
            </div>
        	</div>              
        <br style="clear:both;" />
     </div>
	 </div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>