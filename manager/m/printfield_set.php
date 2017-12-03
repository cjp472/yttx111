<?php 
$menu_flag = "system";
include_once ("header.php"); 
include_once ("arr_data.php");

if(empty($in['m']) || !in_array($in['m'],array('order','return','send','library','paper'))) $in['m'] = 'paper';

$setname = array(
	'order'		=> '订单',
	'return'	=> '退单',
	'send'		=> '发货单',
	'library'	=> '入库单',
	'paper'		=> '纸张',
);

$valuearr = null;
$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");

if(!empty($setinfo['SetValue']))
{
	$setarr = unserialize($setinfo['SetValue']);
}
$valuearr = $setarr[$in['m']];

$productarr  = get_set_arr('product');
$price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
$price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";


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
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">打印设置</a></div>
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
			<div class="line sublink" style="margin:8px; height:32px;">
					<input type="button" name="newbutton" id="newbutton" value="订单设置" class="<?php if($in['m']=='order') echo 'button_3'; else echo 'button_2';?>" onclick="javascript:window.location.href='printfield_set.php?m=order'" />
					<input type="button" name="newbutton" id="newbutton" value="退单设置" class="<?php if($in['m']=='return') echo 'button_3'; else echo 'button_2';?>" onclick="javascript:window.location.href='printfield_set.php?m=return'" />
					<input type="button" name="newbutton" id="newbutton" value="发货单设置" class="<?php if($in['m']=='send') echo 'button_3'; else echo 'button_2';?>" onclick="javascript:window.location.href='printfield_set.php?m=send'" />
					<input type="button" name="newbutton" id="newbutton" value="入库单设置" class="<?php if($in['m']=='library') echo 'button_3'; else echo 'button_2';?>" onclick="javascript:window.location.href='printfield_set.php?m=library'" />
					<input type="button" name="newbutton" id="newbutton" value="纸张设置" class="<?php if($in['m']=='paper') echo 'button_3'; else echo 'button_2';?>" onclick="javascript:window.location.href='printfield_set.php?m=paper'" />
			</div>
			<div id="oldinfo" class="line">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="ptype" id="ptype" value="<?php echo $in['m'];?>" />
			<fieldset  class="fieldsetstyle">		
			<legend><?php echo $setname[$in['m']];?>打印设置</legend>
            <br style="clear:both;" />	
		<?php if($in['m'] == 'paper'){?>
			<table width="600" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="center">
				<tr>
					<td><strong>自定义纸张大小：</strong></td>
				</tr>
				<tr>
					<td>宽度：<input type="text" size="5" value="<?php if(!empty($valuearr['PrintWidth'])) echo $valuearr['PrintWidth']; else echo '210';?>" name="PrintWidth" id="PrintWidth" style="width:200px;" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="5" />(MM)</td>
				</tr>
				<tr>
					<td>高度：<input type="text" size="5" value="<?php if(!empty($valuearr['PrintHeight'])) echo $valuearr['PrintHeight']; else echo '297';?>" name="PrintHeight" id="PrintHeight" style="width:200px;" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" maxlength="5" />(MM)</td>
				</tr>

				<tr>
					<td height="34"><input type="button" name="newbutton" id="newbutton" value="保存设置" class="button_2" onclick="savesettype('printf');" style="margin-left:120px; width:88px; height:25px;" /></td>
				</tr>
				<tr>
					<td>默认为 A4 纸 宽度：210 MM , 高度：297 MM</td>
				</tr>
			</table>

		<?php }else{?>
              <table width="600" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="center" id="print_field">
				 <tr>
       			     <td  bgcolor="#fffef5" width="8%" class="font14" align="center">序号</td>
					 <td  bgcolor="#fffef5" width="35%" class="font14" align="center">字段名称</td>
					 <td  bgcolor="#fffef5" width="25%" class="font14" align="center">显示名称</td>
					 <td  bgcolor="#fffef5" width="20%" class="font14" align="center">表格宽度</td>
					 <td  bgcolor="#fffef5" width="20%" class="font14" align="center">打印显示</td>
     			 </tr>
			  <?php
				$i=0;
				foreach($setfieldarr[$in['m']] as $k=>$v)
				{
					$i++;
			 ?>
                <tr>
				  <td  bgcolor="#F0F0F0"><div align="center" class="TitleNUM4"><? echo $i;?></div></td>
                  <td  bgcolor="#FFFFFF" align="center"><label>
                   <strong> <? echo $v;?></strong>
                    </label></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="FieldShowName_<? echo $k;?>" id="FieldShowName_<? echo $k;?>" value="<? if(!empty($valuearr[$k]['name'])) echo $valuearr[$k]['name']; else echo $v; ?>" />
                    </label></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="FieldShowWidth_<? echo $k;?>" id="FieldShowWidth_<? echo $k;?>" value="<? if(!empty($valuearr[$k]['width'])) echo $valuearr[$k]['width']; ?>" />
                    </label></td>
					 <td  bgcolor="#FFFFFF"><label> <input type="checkbox" name="FieldShowPrint_<? echo $k;?>" id="FieldShowPrint_<? echo $k;?>" value="1"  <?php
			 if(in_array($k,$disarr[$in['m']])) echo ' disabled="disabled" checked="checked" '; else if($valuearr[$k]['show'] == "1") echo ' checked="checked" ';?> /></label></td>
                </tr>
				<?php }?>
                <tr>
				  <td  bgcolor="#F0F0F0"><div align="center" class="TitleNUM4"><? echo $i+1;?></div></td>
                  <td  bgcolor="#FFFFFF" align="center" colspan="3"><label>
                   <strong> 打印公司联系方式</strong>
                    </label></td>
					 <td  bgcolor="#FFFFFF"><label> <input type="checkbox" name="CompanyInfoPrint" id="CompanyInfoPrint" value="1" <? if(!empty($valuearr['CompanyInfoPrint']) && $valuearr['CompanyInfoPrint']=="1")  echo 'checked="checked"';?> /></label></td>
                </tr>
              </table>
              <table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
     			 <tr>
       			     <td  align="right" height="38" ><input type="button" name="newbutton" id="newbutton" value="保存设置" class="button_2" onclick="savesettype('printf');" style="margin-right:20px;" /></td>
     			 </tr>
				 <tr><td colspan="2" align="right" height="32">(注：打勾的字段才会显示在打印表格中,灰色的为必选项。)&nbsp;</td></tr>
              </table>
		<?php }?>
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