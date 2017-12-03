<?php 
$menu_flag = "return";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!empty($in['ID']))
{	 
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." and ReturnID=".intval($in['ID'])." limit 0,1");
}else{
	exit('错误参数!');
}
if($oinfo['ReturnStatus'] > 2) exit('非法操作！');
$binfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_return_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
if(empty($binfo['allrow']))
{
	$cartdataarrmsg = $db->get_results("select ID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber from ".DATATABLE."_order_cart_return where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID=".$oinfo['ReturnID']." order by ID asc");
	$cartdatamsg = serialize($cartdataarrmsg);
	$db->query("insert into ".DATATABLE."_order_return_cartbak(CompanyID,OrderID,Content) values(".$_SESSION['uinfo']['ucompany'].",".$oinfo['ReturnID'].",'".$cartdatamsg."')");
}

$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['ReturnClient']." limit 0,1");

$cartdata = $db->get_results("select c.*,i.Coding,i.Units from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ReturnID=".$oinfo['ReturnID']." order by ID asc");
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
<script type="text/javascript">
function checknumber(inputid,packagenum)
{
	var inum  = $("#"+inputid).val();
	inumu = parseInt(inum);
	if(inumu > packagenum )
	{
		alert('退货数量只能改少，不能增大！');	
		$("#"+inputid).val(packagenum);
	}
}
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
				<div style="margin-top:4px; margin-left:12px;"><input type="button" name="newbutton" id="newbutton" value=" 退货申请 " class="redbtn" onclick="javascript:window.location.href='return_add.php'" /> </div> 
   	        </div>            
			<div class="location"><strong>当前位置：</strong><a href="return.php">退货单管理</a> </div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	
			<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">单号：<? echo $oinfo['ReturnSN'];?> <? if($oinfo['ReturnType']=="M") echo "(管理端代下单)";?>&nbsp;&nbsp;&nbsp;&nbsp;   状态：<? echo $return_status_arr[$oinfo['ReturnStatus']];?></div>
					<div class="rightdiv">申请时间：<? echo date("Y-m-d H:i",$oinfo['ReturnDate']);?></div>
					<input name="return_id" id="return_id" type="hidden" value="<? echo $oinfo['ReturnID'];?>"  />
				</div>

					<br class="clearfloat" />
					<div class="border_line">
					<div class="line bgw">
						<div class="line22 font12">商品清单</div>
						<div class="line22">
						
  <table width="98%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr>
    <td width="5%" height="28">&nbsp;行号</td>
    <td>&nbsp;商品名称</td>
	<td width="10%">&nbsp;编号</td>
    <td width="10%">&nbsp;颜色</td>
    <td width="10%">&nbsp;规格</td>
    <td width="8%" align="right">数量</td>
	<td width="5%" align="right">单位</td>
    <td width="12%" align="right">单价</td>
    <td width="14%" align="right">价格(元)&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata as $ckey=>$cvar)
	{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?><input name="cart_id[]" id="cart_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ID'];?>"  /><input name="cart_content_id[]" id="cart_content_id_<? echo $cvar['ID'];?>" type="hidden" value="<? echo $cvar['ContentID'];?>"  /></td>
    <td><div  title="<? echo $cvar['ContentName'];?>"><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></div></td>
	<td>&nbsp;<? echo $cvar['Coding'];?> </td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> </td>
	<td>&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" >
	<input name="cart_num[]" id="cart_num_<? echo $cvar['ID'];?>" type="text" value="<? echo $cvar['ContentNumber'];?>" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1" onBlur="checknumber('cart_num_<? echo $cvar['ID'];?>','<? echo $cvar['ContentNumber'];?>');"   />
	</td>
	<td align="right" ><? echo $cvar['Units'];?>	</td>
	<td align="right">
	¥ <input type="text" name="cart_price[]" id="cart_price_<? echo $cvar['ID'];?>" value="<? echo $cvar['ContentPrice'];?>" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" class="numberinput2" onfocus="this.select();"  />
	</td>
    <td class="font12" align="right">¥ <? 
		echo $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice'];
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>&nbsp;</td>
  </tr>
   <? }?> 
  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font14">合计：</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
    <td class="font12" align="right"><? echo $allnumber;?></td>
    <td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
    <td class="font12" align="right">¥ <? echo $alltotal = sprintf("%01.2f", round($alltotal,2));?>&nbsp;</td>
  </tr>
   </tbody>
</table>
			<div class="line22" align="right">			
			<input type="button" value="保存修改" class="redbtn" name="confirmbtn" id="confirmbtn" onclick="do_save_return_product('reload',0)" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="完成，返回退单管理" class="greenbtn" name="confirmbtn" id="confirmbtn" onclick="do_save_return_product('back',<? echo $oinfo['ReturnID'];?>)" />
			</div>
				          </div>
						</div>
					</div>				
					</form>
				</div>

        <br style="clear:both;" />
    </div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>