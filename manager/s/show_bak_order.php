<? include_once ("header.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? echo SITE_NAME;?> - 管理平台</title>

<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p{color:#333333; font-size:12px; line-height:150%;}
thead td{font-weight:bold; height:24px;}
tbody td{border-bottom:#efefef solid 1px;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.font12{font-weight:bold;}
-->
</style>
</head>

<body >
<div >
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="6%" >&nbsp;行号</td>

    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色/规格</td>
    <td width="8%" align="right">数量</td>
    <td width="10%" align="right">单价</td> 
    <td width="5%" align="right">折扣</td> 
    <td width="10%" align="right">折后价</td> 
    <td width="12%" align="right">价格(元)&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	$cartbakinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID='".$in['oid']."' limit 0,1");
	if(!empty($cartbakinfo))
	{
		$cartbakarr = unserialize($cartbakinfo['Content']);

		foreach($cartbakarr as $ckey=>$cvar)
		{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> / <?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>	</td>
	<td align="right">¥ <? echo $cvar['ContentPrice'];?> </td>
	<td align="right"><? echo $cvar['ContentPercent'];?> </td>
	<td align="right">¥ <? echo $cvar['ContentPrice']*$cvar['ContentPercent']/10;?> </td>
    <td class="font12" align="right">¥ <? 
		echo $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>&nbsp;</td>

  </tr>
   <? }}?> 
  <tr>
    <td>&nbsp;</td>

    <td height="28" class="font12">合计：</td>
	<td>&nbsp;</td>
    <td class="font12" align="right"><? echo $allnumber;?></td>
    <td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
    <td class="font12" align="right">¥ <? echo $alltotal = sprintf("%01.2f", round($alltotal,2));?>&nbsp;</td>
  </tr>
   </tbody>
</table>
		

</div>
</body>
</html>