<?php 
$menu_flag = "order";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('错误参数!');
}else{	 
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderStatus,OrderTotal FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
}
if(empty($oinfo['OrderID'])) exit('错误路径!');
	
$binfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
if(empty($binfo['allrow']))
{
	$cartdataarrmsg = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by ID asc");
	$cartdatamsg = serialize($cartdataarrmsg);
	$db->query("insert into ".DATATABLE."_order_cartbak(CompanyID,OrderID,Content) values(".$_SESSION['uinfo']['ucompany'].",".$oinfo['OrderID'].",'".$cartdatamsg."')");
}

$cartdata = $db->get_results("select c.*,i.Coding,i.Units,i.Casing,i.Model from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by i.SiteID asc,c.ID asc");
foreach($cartdata as $var)
{
	$idarr[] = $var['ContentID'];
}
$idmsg = implode(",", $idarr);
$fp    = array('+','/','=','_');
$rp    = array('-','|','DHB',' ');
$libarr = null;

$valuearr = get_set_arr('product');
if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
{
	$data_all = $db->get_results("SELECT ContentID,OrderNumber FROM ".DATATABLE."_order_number where ContentID in (".$idmsg.") and CompanyID = ".$_SESSION['uinfo']['ucompany']." ");

	$data_cs = $db->get_results("SELECT ContentID,ContentColor,ContentSpec,OrderNumber FROM ".DATATABLE."_order_inventory_number where ContentID in (".$idmsg.") and CompanyID = ".$_SESSION['uinfo']['ucompany']." ");

	$tykey = str_replace($fp,$rp,base64_encode("统一"));
	if(!empty($data_cs))
	{
		foreach($data_cs as $svar)
		{	
			$cospkey = $svar['ContentID'];
			if(!empty($svar['ContentColor']) && $svar['ContentColor']!=$tykey)
			{
				$cospkey .= "_p_".$svar['ContentColor'];
			}
			if(!empty($svar['ContentSpec']) && $svar['ContentSpec']!=$tykey)
			{
				$cospkey .= "_s_".$svar['ContentSpec'];
			}
			$libarr[$cospkey] = $svar['OrderNumber'];
		}
	}
	if(!empty($data_all))
	{
		foreach($data_all as $avar)
		{
			$libarr[$avar['ContentID']] = $avar['OrderNumber'];
		}
	}
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
<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">

   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a> &#8250;&#8250; <a href="#">修改订单商品</a></div> 
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

        	<div id="line">
			<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">
					订单号：<? echo $oinfo['OrderSN'];?> <input name="order_id" id="order_id" type="hidden" value="<? echo $oinfo['OrderID'];?>"  />
					&nbsp;&nbsp;&nbsp;&nbsp;   状态：<? echo $order_status_arr[$oinfo['OrderStatus']];?>
					&nbsp;&nbsp;&nbsp;&nbsp;   操作：核准订单
					</div>
					
				</div>				

					<br class="clearfloat" />
					<div class="border_line">
					<div class="line bgw">
						<div >			
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td height="28" width="6%" >&nbsp;行号</td>
	<td width="10%">编号/货号</td>
    <td>&nbsp;商品名称</td>
	<td width="10%">药品规格&nbsp;</td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td width="8%" >库存</td>
	<? }?>
    <td width="6%" >订购数</td>
    <td width="6%" >已发数</td> 
	<td width="6%" >未发数</td> 
    <td width="5%" align="center">单位</td>
    <td width="6%" >&nbsp;单价</td>	
    <td width="5%" >折扣</td>
	<td width="5%" >&nbsp;折后价</td>
    <td width="4%" align="center">删除</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal   = 0;
	$alltotal2 = 0;
	$allnumber = 0;
	$n = 1;
	foreach($cartdata as $ckey=>$cvar)
	{
		$allnumber = $allnumber + $cvar['ContentNumber'];
		$alltotal2 = $alltotal2 + $cvar['ContentNumber'] * $cvar['ContentPrice'];
		$alltotal   = $alltotal + $cvar['ContentNumber'] * $cvar['ContentPrice'] * $cvar['ContentPercent'] * 0.1;
		$kid = make_kid($cvar['ContentID'],$cvar['ContentColor'],$cvar['ContentSpecification']);
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?><input name="cart_id[]" id="cart_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ID'];?>"  /><input name="cart_content_id[]" id="cart_content_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ContentID'];?>"  /></td>
	<td title="包装：<? echo $cvar['Casing'];?>"><? echo $cvar['Coding'];?></td>
    <td title="包装：<? echo $cvar['Casing'];?>"><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
	<td>&nbsp;<? echo $cvar['Model'];?></td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td width="8%" >
		<input name="product_number[]" type="hidden" value="<? if(empty($libarr[$kid])) echo '0'; else echo $libarr[$kid];?>" />
		<? if(empty($libarr[$kid])) echo '0'; else echo $libarr[$kid];?>
	</td> 
	<? }?>
	<td title="包装：<? echo $cvar['Casing'];?>">
		<input name="cart_name[]" type="hidden" value="<? echo $cvar['ContentName'].'[ 颜色：'.$cvar['ContentColor'].' / 规格：'.$cvar['ContentSpecification' ].']';?>" />
		<input name="send_num[]" type="hidden" value="<? echo $cvar['ContentSend'];?>" />
		<input name="old_cart_num[]" type="hidden" value="<? echo $cvar['ContentNumber'];?>" />
		<input name="cart_num[]" id="cart_num_<? echo $cvar['ID'];?>" type="text" value="<? echo $cvar['ContentNumber'];?>" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1"  />
	</td>
	<td title="包装：<? echo $cvar['Casing'];?>"><? echo $cvar['ContentSend'];?></td>
	<td title="包装：<? echo $cvar['Casing'];?>"><? echo ($cvar['ContentNumber']-$cvar['ContentSend']);?></td>
	<td align="center"><? echo $cvar['Units'];?></td>
	<td >¥ <input type="text" name="cart_price[]" id="cart_price_<? echo $cvar['ID'];?>" value="<? echo $cvar['ContentPrice'];?>" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" class="numberinput1" onfocus="this.select();" /></td>
    <td ><input type="text" name="cart_percent[]" id="cart_percent_<? echo $cvar['ID'];?>" value="<? echo $cvar['ContentPercent'];?>" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" class="numberinput1" onfocus="this.select();" /></td>
	<td >¥ <? echo $cvar['ContentPrice']*$cvar['ContentPercent']*0.1;?></td>
    <td> 
	<?php 
	if($cvar['ContentSend'] > 0) $dismsg = 'disabled="disabled"'; else $dismsg = '';
	?>
	<input type="checkbox" value="del" name="cart_del_<? echo $cvar['ID'];?>" id="cart_del_<? echo $cvar['ID'];?>" <?php echo $dismsg;?> />
	
	</td>
  </tr>
   <? }?> 
  <tr><td height="28">&nbsp;</td><td class="font12">&nbsp;合计：</td><td>&nbsp;</td><td>&nbsp;</td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td  >&nbsp;</td>
	<? }?>
<td>&nbsp; </td><td class="font12">&nbsp;<? echo $allnumber;?></td><td  >&nbsp;</td><td class="font12" colspan="2" align="right">¥ <? echo $alltotal2;?></td><td class="font12" colspan="2" align="right">¥ <? echo $alltotal;?></td></tr>
   </tbody>
</table>
			<div class="line22" align="right">
			<input type="button" value="保存修改" class="redbtn" name="confirmbtn" id="confirmbtn" onclick="do_checkoff_order_product('reload',0)" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="完成，返回订单管理" class="greenbtn" name="confirmbtn" id="confirmbtn" onclick="do_checkoff_order_product('back',<? echo $oinfo['OrderID'];?>)" />
			</div>

				          </div>
						</div>
					</div>
					</form>
				</div>
</div>
        <br style="clear:both;" />
    </div>

</body>
</html>
<?php
	function make_kid($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(!empty($product_color))
		{
		   $kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
		}
		if(!empty($product_spec))
		{
		   $kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
		}

		return $kid;
	}
?>