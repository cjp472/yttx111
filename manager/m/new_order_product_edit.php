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

$cartdata = $db->get_results("select c.*,i.BrandID,i.Model,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by ID asc");
foreach($cartdata as $var)
{
	$idarr[] = $var['ContentID'];
}
$idmsg = implode(",", $idarr);
$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');
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
	foreach($data_all as $avar)
	{
		$libarr[$avar['ContentID']] = $avar['OrderNumber'];
	}
}

//获取去厂家  by zjb 20160623 
$brandsql   = "SELECT * FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." ORDER BY BrandPinYin ASC";
$brandsql_data = $db->get_results($brandsql);
foreach ($brandsql_data as $val){
    $brandsqlarr[$val['BrandID']] = $val;
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
            
			<div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a> &#8250;&#8250; <a href="#">订单商品管理</a></div> 
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

        	<div id="line">
			<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">订单号：<? echo $oinfo['OrderSN'];?> <input name="order_id" id="order_id" type="hidden" value="<? echo $oinfo['OrderID'];?>"  />&nbsp;&nbsp;&nbsp;&nbsp;   状态：<? echo $order_status_arr[$oinfo['OrderStatus']];?></div>
				</div>				

					<br class="clearfloat" />
					<div class="border_line">
					<div class="line bgw">
						<div >		
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr style="background-color:#efefef; background-image:url(./img/line_bg.jpg);  ">
<td  height="30" width="70" align="center" style="border-bottom:#cccccc solid 1px;">
	<strong>快速订购： </strong></td>
	<td width="220"  style="border-bottom:#cccccc solid 1px;"><input name="inputsp"  type="text" id="inputsp" size="40" onKeyDown="javascript: if(window.event.keyCode == 13) select_product('<? echo $oinfo['OrderID'];?>');" /> 
     </td>
	 <td  style="border-bottom:#cccccc solid 1px;">
                  <input name="buttonsp" type="button" class="bluebtn" id="buttonsp" value="......"  onClick="select_product('<? echo $oinfo['OrderID'];?>');"  />
                  (可通录入商品的ID、名称、编号、拼音码查询)
	</td></tr>
</table>	
  <table width="100%" border="0" cellspacing="2" cellpadding="0" >
  <thead>
  <tr>
    <td width="6%" height="28">&nbsp;行号</td>
    <td>&nbsp;商品名称</td>
	<!-- <td width="16%" >&nbsp;生产厂家</td> -->
	<!-- <td width="10%" >&nbsp;规格</td> -->
	<td width="16%" >&nbsp;品牌</td>
	<td width="10%" >&nbsp;型号</td>
    <td width="8%" style="display:none;">颜色&nbsp;</td>
	<td width="8%" style="display:none;">规格&nbsp;</td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td width="6%" >库存</td>
	<? }?>
    <td width="6%" >数量</td>
    <td width="5%" align="center">单位</td>
    <td width="8%" align="right">单价&nbsp;</td>	
    <td width="5%" align="right">折扣</td>
	<td width="8%" align="right">折后价&nbsp;</td>
    <td width="5%" align="center">删除</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$alltotal2 = 0;
	$allnumber = 0;
	$n = 1;
	foreach($cartdata as $ckey=>$cvar)
	{
		$allnumber = $allnumber + $cvar['ContentNumber'];
		$alltotal2 = $alltotal2 + $cvar['ContentNumber'] * $cvar['ContentPrice'];
		$alltotal  = $alltotal + $cvar['ContentNumber'] * $cvar['ContentPrice'] * $cvar['ContentPercent'] * 0.1;
		$kid = make_kid($cvar['ContentID'],$cvar['ContentColor'],$cvar['ContentSpecification']);
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="28">&nbsp;<? echo $n++;?><input name="cart_id[]" id="cart_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ID'];?>"  /><input name="cart_content_id[]" id="cart_content_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ContentID'];?>"  /></td>
    <td title="包装：<? echo $cvar['Casing'];?>"><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
	<td >&nbsp;<? echo $brandsqlarr[$cvar['BrandID']]['BrandName'];?></td>
	<td >&nbsp;<? echo $cvar['Model'];?></td>
    <td style="display:none;">&nbsp;<? if(strlen($cvar['ContentColor'])) echo $cvar['ContentColor'];?> </td>
	<td style="display:none;">&nbsp;<?if(strlen($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?></td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td  ><? echo ($libarr[$kid]+$cvar['ContentNumber']);?></td> 
	<? }?>
	<td title="包装：<? echo $cvar['Casing'];?>"><input name="cart_num[]" id="cart_num_<? echo $cvar['ID'];?>" type="text" value="<? echo $cvar['ContentNumber'];?>" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1"  /></td>
	<td align="center"><? echo $cvar['Units'];?></td>
	<td align="right">¥ <? echo $cvar['ContentPrice'];?>&nbsp;</td>
    <td align="right"><? echo $cvar['ContentPercent'];?>&nbsp;</td>
	<td align="right">¥ <? echo $cvar['ContentPrice']*$cvar['ContentPercent']*0.1;?></td>
    <td align="center"> <input type="checkbox" value="del" name="cart_del_<? echo $cvar['ID'];?>" id="cart_del_<? echo $cvar['ID'];?>" /></td>
  </tr>
   <? }?> 
  <tr><td height="28">&nbsp;</td><td class="font12">&nbsp;合计：</td>
  	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td  >&nbsp;</td> 
	<? }?>
  <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class="font12">&nbsp;<? echo $allnumber;?></td><td  >&nbsp;</td><td class="font12" align="right">¥ <? echo $alltotal2;?></td><td>&nbsp;</td><td class="font12" align="right">¥ <? echo $alltotal;?></td><td>&nbsp;</td></tr>
   </tbody>
</table>
			<div class="line22" align="right"><input type="button" value="继续订购商品" class="bluebtn" name="confirmbtn" id="confirmbtn" onclick="javascript:window.location.href='new_order_add_product.php?oid=<? echo $oinfo['OrderID'];?>'" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="保存修改" class="redbtn" name="confirmbtn" id="confirmbtn" onclick="do_save_order_product_new('reload',0)" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="完成，返回订单管理" class="greenbtn" name="confirmbtn" id="confirmbtn" onclick="do_save_order_product_new('back',<? echo $oinfo['OrderID'];?>)" /></div>

				          </div>
						</div>
					</div>
					</form>
				</div>
		</div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  

     <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">快速订购</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui2()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"></div>
	</div> 
</body>
</html>
<?
	function make_kid($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(strlen($product_color))
		{
		   $kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
		}
		if(strlen($product_spec))
		{
		   $kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
		}
		return $kid;
	}
?>