<?php 
$menu_flag = "order";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('错误参数!');
}else{	 
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderStatus,OrderTotal FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." and OrderStatus < 2 limit 0,1");
}
if(empty($oinfo['OrderID'])) exit('已发货不能操作!');

$cartdata = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentNumber,ContentPrice from ".DATATABLE."_order_cart_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by ID asc");

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
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>
<div class="bodyline" style="height:32px;"><div class="leftdiv" style=" margin-top:8px; padding-left:12px;"><span><h4><?php echo $_SESSION['uc']['CompanyName'];?></h4></span><span valign="bottom">&nbsp;&nbsp;<? echo $_SESSION['uinfo']['usertruename']."(".$_SESSION['uinfo']['username'].")";?> 欢迎您！</span>&nbsp;&nbsp;<span>[<a href="change_pass.php">修改密码</a>]</span>&nbsp;&nbsp;<span>[<a href="do_login.php?m=logout">退出系统</a>]</span></div>
        <div class="rightdiv">
       	  <span class="leftdiv"><img src="img/menu2_left.jpg" /></span>
            <span id="menu2">
            	<ul>
                  	<li class="current2"><a href="order.php">订单管理</a></li>			
                </ul>
          </span>
            <span><img src="img/menu2_right.jpg" /></span>
        </div>
</div>    
    
    
    	<div class="bodyline" style="height:70px; background-image:url(img/bodyline_bg.jpg);">
   		  <div class="leftdiv"><img src="img/blue_left.jpg" /></div>
                <div class="leftdiv"><h1><? echo $menu_arr[$menu_flag];?></h1></div>
                <div class="rightdiv" style="color:#ffffff; padding-right:20px; padding-top:40px;">此栏目针对订单管理，主要包括订单处理。</div>
        </div>
        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">

   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a> &#8250;&#8250; <a href="#">修改订单赠品</a></div> 
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
						<div class="font12">&nbsp;赠品清单</div>
						<div >
						
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="6%" >&nbsp;行号</td>
    <td>&nbsp;商品名称</td>
    <td width="10%">颜色&nbsp;</td>
	<td width="10%">规格&nbsp;</td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td width="10%" >库存</td>
	<? }?>
    <td width="10%" >数量</td>
    <td width="14%" >&nbsp;&nbsp;&nbsp;单价</td>	
    <td width="6%" align="center">删除</td> 
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
		$alltotal  = $alltotal + $cvar['ContentNumber'] * $cvar['ContentPrice'];
		$kid = make_kid($cvar['ContentID'],$cvar['ContentColor'],$cvar['ContentSpecification']);
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?><input name="cart_id[]" id="cart_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ID'];?>"  /><input name="cart_content_id[]" id="cart_content_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ContentID'];?>"  /></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<? if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> </td>
	<td>&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?></td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td width="8%" ><? if(empty($libarr[$kid])) echo '0'; else echo $libarr[$kid];?></td> 
	<? }?>
	<td ><input name="cart_num[]" id="cart_num_<? echo $cvar['ID'];?>" type="text" value="<? echo $cvar['ContentNumber'];?>" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1"  /></td>
	<td >¥ <input type="text" name="cart_price[]" id="cart_price_<? echo $cvar['ID'];?>" value="<? echo $cvar['ContentPrice'];?>" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" class="numberinput2" onfocus="this.select();"  /></td>
    <td align="center"> <input type="checkbox" value="del" name="cart_del_<? echo $cvar['ID'];?>" id="cart_del_<? echo $cvar['ID'];?>" /></td>
  </tr>
   <? }?> 
  <tr><td height="28">&nbsp;</td><td class="font12">&nbsp;合计：</td><td>&nbsp;</td><td  >&nbsp;</td>
	<? if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){ ?>
	<td  >&nbsp;</td>
	<? }?>
<td class="font12">&nbsp;<? echo $allnumber;?></td><td class="font12">¥<? echo $alltotal2;?></td><td>&nbsp;</td></tr>
   </tbody>
</table>
			<div class="line22" align="right"><input type="button" value="继续添加赠品" class="bluebtn" name="confirmbtn" id="confirmbtn" onclick="javascript:window.location.href='order_add_gifts_product.php?oid=<? echo $oinfo['OrderID'];?>'" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="保存修改" class="redbtn" name="confirmbtn" id="confirmbtn" onclick="do_save_gifts_product('reload','<? echo $oinfo['OrderID'];?>')" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="完成，返回订单管理" class="greenbtn" name="confirmbtn" id="confirmbtn" onclick="do_save_gifts_product('back','<? echo $oinfo['OrderID'];?>')" /></div>

				          </div>
						</div>
					</div>
					</form>
				</div>
</div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
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