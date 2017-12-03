<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");

$productinfo = $db->get_row("SELECT ID,Name,Color,Specification FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID=".intval($in['pid'])." limit 0,1");

$snarr = null;
if(!empty($in['amsg']))
{
	$snarr  = unserialize(urldecode($in['amsg']));
}
$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.tc thead tr td{font-weight:bold; background: #efefef; height:25px; padding:2px;}
.tc tbody tr td{ background: #ffffff; font-weight:bold; height:25px; padding:2px;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#ffffff; background:url(./img/f1s.jpg); }
.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
input{font-weight:bold; font-size:12px;font-family: Verdana, Arial, Helvetica, sans-serif; color:#333333;}
-->
</style>
</head>

<body>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#ffffff"> 
<tr><td align="center" height="38"><strong><? echo $productinfo['Name'];?></strong></td></tr>
</table>

<div style="width:100%; height:260px; overflow:auto;">
          <form id="MainForm" name="MainForm" method="post" action="" target="" >
			  <input type="hidden" name="inputpid" id="inputpid" value="<? if(!empty($in['pid'])) echo $in['pid'];?>" />
        	  <table width="96%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="tc">               
                <thead>
				<tr>
				  <td width="60" >规格/颜色</td>
				  <?
					$tdmsg = '';
					if(empty($productinfo['Color']))
					{
						$tdmsg = '<td width="60"><strong>统一</strong></td>';
						$tdnumber = 1;
						$carr[]   = str_replace($fp,$rp,base64_encode("统一")); 
					}else{
						if(strpos($productinfo['Color'], ","))
						{
							$in_color_arr = explode(",", $productinfo['Color']);
							foreach($in_color_arr as $cvar)
							{
								$tdmsg  .=  '<td width="60">'.$cvar.'</td>';
								$carr[]  = str_replace($fp,$rp,base64_encode($cvar)); 
							}
							$tdnumber = count($in_color_arr);
						}else{
							$tdmsg = '<td width="60">'.$productinfo['Color'].'</td>';
							$tdnumber = 1;
							$carr[]   = str_replace($fp,$rp,base64_encode($productinfo['Color'])); 
						}						
					}
					$tdmsg .= '<td width="60"><strong>合计</strong></td>';
					echo $tdmsg;
				  ?>
                </tr>
				</thead>
				<tbody>
				  <?
					$trmsg = '';
					$slinet = 0;
					$llinet = null;
					if(empty($productinfo['Specification']))
					{
						$trmsg .= '<tr><td width="60"><strong>统一</strong></td>';
						$basecode = str_replace($fp,$rp,base64_encode("统一"));

						for($i=0;$i<$tdnumber;$i++)
						{
							$akey = 'inputn_'.$basecode.'_'.$carr[$i];
							if(empty($snarr[$akey])) $sv = 0; else $sv = $snarr[$akey];
							$slinet = $slinet + $sv;
							$llinet[$i] = $llinet[$i] + $sv;
							$trmsg .= '<td width="60"><input name="storage_number[]" onfocus="this.select();" type="text" id="'.$akey.'" maxlength="6" size="6" value="'.$sv.'" onBlur="changeupnumber(\''.$basecode.'\',\''.$carr[$i].'\');" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" /><input name="storage_number_id[]" type="hidden" value="'.$akey.'" /></td>';
						}
						$trmsg .= '<td width="60"><input name="storage_number2[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_'.$basecode.'_hj" size="6" value="'.$slinet.'" /></td></tr>';
					}else{
						if(strpos($productinfo['Specification'], ","))
						{
							$in_spec_arr = explode(",", $productinfo['Specification']);
							foreach($in_spec_arr as $svar)
							{
								$trmsg .= '<tr><td width="60">'.$svar.'</td>';
								$basecode =str_replace($fp,$rp, base64_encode($svar));
								$slinet = 0;
								for($i=0;$i<$tdnumber;$i++)
								{
									$akey = 'inputn_'.$basecode.'_'.$carr[$i];
									if(empty($snarr[$akey])) $sv = 0; else $sv = $snarr[$akey];
									$slinet = $slinet + $sv;
									$llinet[$i] = $llinet[$i] + $sv;
									$trmsg .= '<td width="60"><input name="storage_number[]" onfocus="this.select();" type="text" id="'.$akey.'" maxlength="6" size="6" value="'.$sv.'" onBlur="changeupnumber(\''.$basecode.'\',\''.$carr[$i].'\');" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" /> <input name="storage_number_id[]" type="hidden" value="'.$akey.'" /></td>';
								}
								$trmsg .= '<td width="60"><input name="storage_number2[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_'.$basecode.'_hj" size="6" value="'.$slinet.'" /></td></tr>';
							}
						}else{
							$trmsg .= '<tr><td width="60">'.$productinfo['Specification'].'</td>';
							$basecode = str_replace($fp,$rp,base64_encode($productinfo['Specification']));
							for($i=0;$i<$tdnumber;$i++)
							{
								$akey = 'inputn_'.$basecode.'_'.$carr[$i];
								if(empty($snarr[$akey])) $sv = 0; else $sv = $snarr[$akey];
								$slinet = $slinet + $sv;
								$llinet[$i] = $llinet[$i] + $sv;
								$trmsg .= '<td width="60"><input name="storage_number[]" onfocus="this.select();" type="text" id="'.$akey.'" maxlength="6" size="6" value="'.$sv.'" onBlur="changeupnumber(\''.$basecode.'\',\''.$carr[$i].'\');" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" /><input name="storage_number_id[]" type="hidden" value="'.$akey.'" /></td>';
							}
							$trmsg .= '<td width="60"><input name="storage_number2[]" disabled="disabled" onfocus="this.select();" type="text" id="inputn_'.$basecode.'_hj" size="6" value="'.$slinet.'" /></td></tr>';
						}
					}
					echo $trmsg;
				  ?>
				   <tr>
					<td width="60"><strong>合计：</strong></td>
				<?
				for($i=0;$i<$tdnumber;$i++)
				{
					echo '<td width="60"><input name="storage_number2[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_'.$carr[$i].'_sj" size="6" value="'.$llinet[$i].'" /></td>';
				}
				$totalnum = @array_sum($snarr); if(empty($totalnum)) $totalnum = 0;
				echo '<td width="60"><input name="storage_number_total[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_total" size="6" value="'.$totalnum.'" /></td></tr>';
				?>
				   </tr>
				   </tbody>
              </table>			
              </form>
       	  </div>


          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
                 	<td >&nbsp;</td>
					 <td height="35" width="180"><label>
                 	   <input type="button" name="button" id="button" value=" 提交 " class="redbtn" onclick="add_input_number();" />
                 	 </label>                 	   
               	     &nbsp;&nbsp;
               	     <label>
               	     <input type="button" name="button2" id="button2" value=" 取消 " class="bluebtn"  onclick="parent.closewindowui()" />
               	     </label></td>
       			     
     			 </tr>
          </table>
       
</body>
</html>