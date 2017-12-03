<?php
$menu_flag = "order";
$pope	   = "pope_form";
include_once ("header.php");

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

$productinfo = $db->get_row("SELECT ID,Name,Color,Specification,Units FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID=".intval($in['ID'])." limit 0,1");

$productcontent = $db->get_row("SELECT ContentIndexID,Package FROM ".DATATABLE."_order_content_1 where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentIndexID=".intval($in['ID'])." limit 0,1");
if(empty($productinfo)) exit('此商品不存在!');
if(empty($productcontent['Package'])) $productcontent['Package'] = 0;

//库存
$snarr = null;
$sql_l  = "SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='product' limit 0,1";
$result	= $db->get_row($sql_l);
if(!empty($result['SetValue'])) $setarr = unserialize($result['SetValue']);
if(!empty($setarr['product_number']) && $setarr['product_number']=="on" && $setarr['product_negative']!="on") $pn = "on"; else $pn = "off";
if(!empty($setarr['product_number_show']) && $setarr['product_number_show']=="on") $ison = "on"; else $ison   = "off";

if($setarr['product_number']=="on" || $setarr['product_number_show']=="on")
{
	$sql   = "select ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber from ".DATATABLE."_order_inventory_number where  CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".intval($in['ID']);
	$list_data = $db->get_results($sql);
	foreach($list_data as $lv)
	{
		$lkey = 'inputn_'.$lv['ContentSpec']."_".$lv['ContentColor'];
		$snarr[$lkey] = $lv['OrderNumber'];
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>医统天下 网上订货系统</title>
<meta name='robots' content='noindex,nofollow' />
<meta name="Author" content="rsung seekfor" />
<link rel="shortcut icon" href="/favicon.ico" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/order.js?ver=33<?php echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,span,div,p,li{
	font-size:12px; font-family: "微软雅黑", Arial, Helvetica, sans-serif, "宋体"; color:#3C3C3C; line-height:150%;
}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.tc thead tr td{font-weight:bold; background: #efefef; height:25px; padding:2px;}
.tc tbody tr td{ background: #ffffff; font-weight:100; height:25px; padding:2px;}
.tdthader{font-weight:bold; background: #efefef; height:25px; padding:2px;}
.tc tbody tr td span{color:#999; font-size:11px;}
.bluebtn {
    background: #3366CC; color: #FFF; font-weight: bold; font-size: 12px;  padding: .2em .4em !important; padding: .1em .2em; cursor: pointer; height:24px;
}
.darkbtn {
    background: #666666; color: #FFF;font-weight: bold; font-size: 12px; padding: .2em .4em !important; padding: .1em .2em; height:24px; cursor: pointer;
}
input{border:#cbcbcd solid 1px; height:20px; font-size:12px; line-height:150%; color:#333333; VERTICAL-ALIGN: middle; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; }
.growlUI {  }
.growlUI h1, div.growlUI h2{
	color: white; padding: 5px 5px 5px 15px; text-align: left; font-size:14px;
}
.close-form{background:#003366; color:#fff; width:18px; height:18px; margin:6px; text-align:center; line-height:18px; float:right; font-size:14px; font-weight:bold; cursor:pointer; clear:both; border:#eeeeee solid 1px;}
.font14{
	color:red;	font-weight:bold;	font-size:14px;
}

.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#ffffff; background:url(./img/f1s.jpg); }


.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
-->
</style>
</head>

<body>
<div style="width:100%;height:30px; clear:both; line-height:30px; background-color:#003366; border-bottom:2px soild #1c7e29; color:#d94318; font-size:14px; font-weight:bold; ">
<div style="width:450px; height:30px; float:left; text-align:left; margin:0; line-height:32px; padding-left:4px; font-size:14px; color:#ffffff; overflow:hidden;">订购：<? echo $productinfo['Name'];?></div>
<div style="float:right; height:32px; width:32px; line-height:32px;"><div class="close-form" onclick="parent.closewindowui()" title="关闭" >x</div></div>
</div>

<div style="width:100%;  height:276px; overflow:auto;">
          <form id="MainFormNumber" name="MainFormNumber" method="post" action="" target="" >
			  <input type="hidden" name="inputpid" id="inputpid" value="<? echo $productinfo['ID'];?>" />
			  <input type="hidden" name="orderid" id="orderid" value="<? echo $in['oid'];?>" />
        	  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="tc">               
                <thead>
				<tr>
				  <td width="68" >规格/颜色</td>
				  <?php
					$tdmsg = '';
					if(!strlen($productinfo['Color']))
					{
						$tdmsg = '<td ><strong>统一</strong></td>';
						$tdnumber = 1;
						$carr[]   = str_replace($fp,$rp,base64_encode("统一")); 
					}else{
						if(strpos($productinfo['Color'], ","))
						{
							$in_color_arr = explode(",", $productinfo['Color']);
							foreach($in_color_arr as $cvar)
							{
								$tdmsg  .=  '<td >'.$cvar.'</td>';
								$carr[]  = str_replace($fp,$rp,base64_encode($cvar)); 
							}
							$tdnumber = count($in_color_arr);
						}else{
							$tdmsg = '<td >'.$productinfo['Color'].'</td>';
							$tdnumber = 1;
							$carr[]   = str_replace($fp,$rp,base64_encode($productinfo['Color'])); 
						}						
					}
					$tdmsg .= '<td ><strong>合计</strong></td>';
					echo $tdmsg;
				  ?>
                </tr>
				</thead>
				<tbody>
				  <?php
					$trmsg = '';
					$slinet = 0;
					$llinet = null;
					if(!strlen($productinfo['Specification']))
					{
						$trmsg .= '<tr><td style="background: #efefef;"><strong>统一</strong></td>';
						$basecode = str_replace($fp,$rp,base64_encode("统一"));

						for($i=0;$i<$tdnumber;$i++)
						{
							$akey = 'inputn_'.$basecode.'_'.$carr[$i];
							if(empty($in['allnumber'])) $sv = 0; else $sv = $in['allnumber'];
							$slinet = $slinet + $sv;
							$llinet[$i] = $llinet[$i] + $sv;
							if($ison=="on") $ordrenum = "<span><br />库存：".intval($snarr[$akey])."</span>"; else $ordrenum = "";
							if($pn=="on" && intval($snarr[$akey]) < 1) $dmsg = ' title="无库存" disabled="disabled" '; else $dmsg = '';

							$trmsg .= '<td ><input name="cart_number[]" onfocus="this.select();" type="text" id="'.$akey.'" maxlength="6" size="6" value="'.$sv.'" onBlur="changeupnumber(\''.$basecode.'\',\''.$carr[$i].'\',\''.$productcontent['Package'].'\');" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" '.$dmsg.' /><input name="cart_number_id[]" type="hidden" value="'.$akey.'" '.$dmsg.' />'.$ordrenum.'</td>';
						}
						$trmsg .= '<td ><input name="cart_number2[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_'.$basecode.'_hj" size="6" value="'.$slinet.'" /></td></tr>';
					}else{
						if(strpos($productinfo['Specification'], ","))
						{
							$in_spec_arr = explode(",", $productinfo['Specification']);
							foreach($in_spec_arr as $svar)
							{
								$trmsg .= '<tr><td style="background: #efefef;">'.$svar.'</td>';
								$basecode = str_replace($fp,$rp, base64_encode($svar));
								$slinet = 0;
								for($i=0;$i<$tdnumber;$i++)
								{
									$akey = 'inputn_'.$basecode.'_'.$carr[$i];
									if(empty($in['allnumber'])) $sv = 0; else $sv = $in['allnumber'];
									$slinet = $slinet + $sv;
									$llinet[$i] = $llinet[$i] + $sv;
									if($ison=="on") $ordrenum = "<span><br />库存：".intval($snarr[$akey])."</span>"; else $ordrenum = "";
									if($pn=="on" && intval($snarr[$akey]) < 1) $dmsg = ' title="无库存" disabled="disabled" '; else $dmsg = '';
									$trmsg .= '<td ><input name="cart_number[]" onfocus="this.select();" type="text" id="'.$akey.'" maxlength="6" size="6" value="'.$sv.'" onBlur="changeupnumber(\''.$basecode.'\',\''.$carr[$i].'\',\''.$productcontent['Package'].'\');" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"  '.$dmsg.'  /> <input name="cart_number_id[]" type="hidden" value="'.$akey.'" '.$dmsg.' />'.$ordrenum.'</td>';
								}
								$trmsg .= '<td ><input name="cart_number2[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_'.$basecode.'_hj" size="6" value="'.$slinet.'" /></td></tr>';
							}
						}else{
							$trmsg .= '<tr><td  style="background: #efefef;">'.$productinfo['Specification'].'</td>';
							$basecode = str_replace($fp,$rp,base64_encode($productinfo['Specification']));
							for($i=0;$i<$tdnumber;$i++)
							{
								$akey = 'inputn_'.$basecode.'_'.$carr[$i];
								if(empty($in['allnumber'])) $sv = 0; else $sv = $in['allnumber'];
								$slinet = $slinet + $sv;
								$llinet[$i] = $llinet[$i] + $sv;
								if($ison=="on") $ordrenum = "<span><br />库存：".intval($snarr[$akey])."</span>"; else $ordrenum = "";
								if($pn=="on" && intval($snarr[$akey]) < 1) $dmsg = ' title="无库存" disabled="disabled" '; else $dmsg = '';
								$trmsg .= '<td ><input name="cart_number[]" onfocus="this.select();" type="text" id="'.$akey.'" maxlength="6" size="6" value="'.$sv.'" onBlur="changeupnumber(\''.$basecode.'\',\''.$carr[$i].'\',\''.$productcontent['Package'].'\');" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"  '.$dmsg.'  /><input name="cart_number_id[]" type="hidden" value="'.$akey.'" '.$dmsg.' />'.$ordrenum.'</td>';
							}
							$trmsg .= '<td ><input name="cart_number2[]" disabled="disabled" onfocus="this.select();" type="text" id="inputn_'.$basecode.'_hj" size="6" value="'.$slinet.'" /></td></tr>';
						}
					}
					echo $trmsg;
				  ?>
				   <tr>
					<td style="background: #efefef;"><strong>合计：</strong></td>
				<?php
				for($i=0;$i<$tdnumber;$i++)
				{
					echo '<td ><input name="cart_number2[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_'.$carr[$i].'_sj" size="6" value="'.$llinet[$i].'" /></td>';
				}
				$totalnum = @array_sum($llinet); if(empty($totalnum)) $totalnum = 0;
				echo '<td ><input name="cart_number_total[]" onfocus="this.select();" disabled="disabled" type="text" id="inputn_total" size="6" value="'.$totalnum.'" /></td></tr>';
				?>
				   </tr>
				   </tbody>
              </table>			
              </form>
       	  </div>

          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
					 <td width="380"  height="38" ><strong>全规格和颜色：</strong><input title="一次输入全部规格和颜色的订购数量" name="inputall_cart_number" onfocus="this.select();"  type="text" id="inputall_cart_number" size="6" value="<? if(!empty($in['allnumber'])) echo $in['allnumber'];?>" onBlur="do_change_cart_number('<? echo $in['ID'];?>','<? echo $productcontent['Package'];?>','<? echo $in['oid'];?>');" />&nbsp;<input type="button" name="addtocart" id="addtocart" value="确 定"  onclick="do_change_cart_number('<? echo $in['ID'];?>','<? echo $productcontent['Package'];?>','<? echo $in['oid'];?>');" class="bluebtn"  />
					 <? if(!empty($productcontent['Package'])) echo ' &nbsp;&nbsp; <span title="整包装订购数量">订购数量必需为 <span class="font12">'.$productcontent['Package'].'</span> 的倍数</span>';?></td>
					 <td align="right" width="135">
                 	   <input type="button" name="addtocart" id="addtocart" value="提 交" class="redbtn" onclick="add_input_number();"  />       	   
                 	   <input type="button" name="button2" id="button2" value="取 消" class="bluebtn"  onclick="parent.closewindowui()"  />
               	     </td>       			     
     			 </tr>
          </table>

</body>
</html>