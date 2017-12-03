<?php 
$menu_flag = "return";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

$valuearr = get_set_arr('product');
if(!empty($valuearr['return_type']) && $valuearr['return_type']=="product")
{
	header("Location: return_product_add.php");
}

if(!empty($in['ID']))
{	 
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderSendStatus,OrderStatus,OrderTotal,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." and (OrderSendStatus=3 or OrderSendStatus=4) limit 0,1");
}elseif(!empty($in['sn'])){
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderSendStatus,OrderStatus,OrderTotal,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$in['sn']."'  and (OrderSendStatus=3 or OrderSendStatus=4) limit 0,1");
}

if(!empty($oinfo))
{
	$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

	$cartdata = $db->get_results("select c.*,i.Units from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by ID asc");

	$sql_cr = "select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID in (select ReturnID from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." and ReturnOrder='".$oinfo['OrderSN']."' and ReturnStatus!=1 and ReturnStatus!=8 and ReturnStatus!=9 ) order by ID asc";
	$returncart	= $db->get_results($sql_cr);
	$returnarr  = null;
	if(!empty($returncart))
	{
		foreach($returncart as $rc)
		{
			$kid = make_kid($rc['ContentID'], $rc['ContentColor'], $rc['ContentSpecification']);
			if(empty($returnarr[$kid]))
			{
				$returnarr[$kid] = $rc['ContentNumber'];
			}else{
				$returnarr[$kid] = $returnarr[$kid]+$rc['ContentNumber'];
			}
		}
	}
			
	for($j=0;$j<count($cartdata);$j++)
	{
		$kid = make_kid($cartdata[$j]['ContentID'], $cartdata[$j]['ContentColor'], $cartdata[$j]['ContentSpecification']);
		if(!empty($returnarr[$kid]))
		{
			$cartdata[$j]['rnumber'] = $cartdata[$j]['ContentSend'] - $returnarr[$kid];
		}else{
			$cartdata[$j]['rnumber'] = $cartdata[$j]['ContentSend'];
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
			<div class="location"><strong>当前位置：</strong><a href="return.php">退单管理</a> &#8250;&#8250; <a href="#">退单申请</a></div>
		</div>
    	
        <div class="line2"></div>
        <div class="bline">

				<br class="clearfloat" />
				<div class="border_line">
					<form id="search_return_1" name="search_return_1" method="post" action="return_add.php">
					<div class="line"><span class="font14">&nbsp;订单号：</span>&nbsp;<input name="sn" id="sn" type="text" class="inputsearch" onfocus="this.select();" value="<? if(!empty($in['sn'])) echo $in['sn'];?>" />&nbsp;<input name="searchbutton1" value="查询" type="submit" class="redbtn" /> &nbsp;&nbsp; <font color=red>（根据订单退货， 请输入订单号点击查询,选择您要退货的商品.）</font> </div>
					</form>
				</div>

			<form id="MainForm" name="MainForm" method="post" action="">
			<input name="orderid" id="orderid" type="hidden" value="<? if(!empty($in['sn'])) echo $in['sn'];?>"  />
<? if(!empty($oinfo)){?>
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">订单号：<? echo $oinfo['OrderSN'];?> <? if($oinfo['OrderType']=="M") echo "(管理员下单)";?>&nbsp;&nbsp;&nbsp;&nbsp;   状态：<? echo $order_status_arr[$oinfo['OrderStatus']];?></div>
					<div class="rightdiv">下单时间：<? echo date("Y-m-d H:i",$oinfo['OrderDate']);?></div>
				</div>
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">订单信息：</div>
					<div class="line bgw">
						<div class="line22 font12">客户信息</div>
<div class="line22"><strong>经 销 商：</strong><a href="client_content.php?ID=<? echo $cinfo['ClientID'];?>" target="_blank"><? echo $cinfo['ClientCompanyName'];?>（<? echo $cinfo['ClientName'];?>）</a></div>
<div class="line45"><strong>联 系 人：</strong><? echo $cinfo['ClientTrueName'];?></div>
<div class="line45"><strong>联系电话：</strong><? echo $cinfo['ClientPhone'].','.$cinfo['ClientMobile'];?></div>					
					</div>
					<br class="clearfloat" />

					<div class="line bgw">
						<div class="line22 font12">商品清单</div>
						<div class="line22">
						
  <table width="98%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr>
    <td width="8%" height="28">&nbsp;ID</td>
    <td>&nbsp;商品名称</td>
    <td width="10%">颜色</td>
	<td width="10%">规格</td>
	<td width="6%" align="right">单位</td>
    <td width="8%" align="right">可退数</td>
	<td width="10%" align="right">退货数</td>
    <td width="12%" align="right">订购价</td>
  </tr>
   </thead>
   <tbody>
	<? 
	$n=1;
	foreach($cartdata as $ckey=>$cvar)
	{
		if(!empty($cvar['rnumber']))
		{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30"><input type="hidden" value="<? echo $cvar['ID'];?>" name="cartid[]" id="cartid_<? echo $cvar['ID'];?>" />&nbsp;<? echo $n++;?></td>
    <td><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> </td>
	<td>&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
	<td align="right"><? echo $cvar['Units'];?>&nbsp;</td>
    <td align="right" ><? echo $cvar['rnumber'];?>&nbsp;</td>
	<td align="right" ><input name="cart_num[]" id="cart_num_<? echo $cvar['ID'];?>" type="text" size="6" maxlength="6"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" style="text-align:right; width:50px;" value="0"  /></td>
    <td align="right">¥ <? echo $pricepencent = $cvar['ContentPrice']*$cvar['ContentPercent']*0.1;?>&nbsp;</td>
  </tr>
   <? }}?> 
   </tbody>
</table>
			</div>
		</div>
	</div>
<? }?>
	
	<br class="clear" />
	<div class="border_line">
		<div class="line font14">退货信息</div>
			<div class="line bgw">
					
  <table width="98%" border="0" cellspacing="2" cellpadding="2" align="center">
  <tr>
    <td width="12%">&nbsp;<strong>货运方式:</strong></td>
	<td>	
	<span id="rblQuery"><span ><input id="ReturnSendType1" type="radio" name="ReturnSendType" value="送货" style="border:0;"  /><label for="rblQuery_0">送货 （直接到公司退货）</label></span><br />
	<span ><input id="ReturnSendType2" type="radio" name="ReturnSendType" value="发货" checked="checked" style="border:0;"  /><label for="rblQuery_1">发货 （通过快递，货运把商品寄公司库房）</label></span><br />
	</td>
  </tr>
  <tr>
    <td >&nbsp;<strong>货运说明:</strong></td>
	<td><textarea name="ReturnSendAbout" rows="3" id="ReturnSendAbout" style="width:80%; height:48px;"></textarea></td>
  </tr>
  <tr>
    <td >&nbsp;<strong>外观包装:</strong></td>
	<td>产品外观：<select name="ReturnProductW" id="ReturnProductW" style="width:100px;">
	<option value="">---请选择---</option>
	<option value="良好">良好</option>
	<option value="有划痕">有划痕</option>
	<option value="外观有破损">外观有破损</option> 
</select>&nbsp;<font color=red>*</font>
	&nbsp;&nbsp; 包装情况：<select name="ReturnProductB" id="ReturnProductB" style="width:100px;">
	<option value="">---请选择---</option>
	<option value="无包装">无包装</option>
	<option value="包装破损">包装破损</option>
	<option value="包装完整">包装完整</option> 
</select>&nbsp;<font color=red>*</font>
</td>
  </tr>
  <tr>
    <td >&nbsp;<strong>退货原因:</strong></td>
	<td><textarea name="ReturnAbout" rows="5"  id="ReturnAbout" style="width:80%;"></textarea>&nbsp;<font color=red>*</font></td>
  </tr>
</table>					
					</div>
				</div>
<? 
	if(!empty($oinfo))
	{
		if($oinfo['OrderStatus'] > 2 && $oinfo['OrderStatus'] < 8 && $oinfo['OrderSendStatus'] > 2)
		{
	?>
			<br class="clear" />
			<div class="border_line">					
				<div class="line22 bgw" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="  提交退单  " class="bluebtn" name="returnsubbutton" id="returnsubbutton" onclick="do_save_new_return();"  /></div>				
			</div>
	<?	
		}else{
	?>
	<br class="clear" />
	<div class="border_line">					
		<div class="line22 bgw" align="right"><font color=red>注：订单只能在已收货的状态下才能退货!</font></div>				
	</div>
	<?
		}
	}
	?>

				<br class="clearfloat" />
					</form>				
		</div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>
<?
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