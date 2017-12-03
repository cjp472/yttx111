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
.red{color:red; font-weight:bold;}
.tdbg{background-color: #fffceb;}
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
    <td width="10%">&nbsp;颜色</td>
    <td width="10%">&nbsp;规格</td>
    <td width="8%" align="right">原数量</td>
    <td width="8%" align="right" >现数量</td>
    <td width="10%" align="right">原单价</td> 
    <td width="10%" align="right" >现单价</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	$cartbakinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_return_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID='".$in['oid']."' limit 0,1");

	if(!empty($cartbakinfo))
	{
		$cartdata = $db->get_results("select * from ".DATATABLE."_order_cart_return  where CompanyID=".$_SESSION['uinfo']['ucompany']." and ReturnID=".$in['oid']." order by ID asc");
		foreach($cartdata as $var)
		{
			$cartarr[$var['ID']] = $var;
		}
		
		$cartbakarr = unserialize($cartbakinfo['Content']);
		foreach($cartbakarr as $ckey=>$cvar)
		{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td ><?
	if(empty($cartarr[$cvar['ID']])) echo '<font color=red>[已删除]</font> ';
	?><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> </td>
    <td>&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>	&nbsp;</td>
    <td align="right" class="tdbg"><? if($cvar['ContentNumber'] != $cartarr[$cvar['ID']]['ContentNumber']) echo '<font class="red">'.$cartarr[$cvar['ID']]['ContentNumber'].'</font>'; else echo $cartarr[$cvar['ID']]['ContentNumber'];?>&nbsp;	</td>
	<td align="right">¥ <?php echo $cvar['ContentPrice'];?>&nbsp; </td>
    <td  align="right" class="tdbg">¥ <?php if($cvar['ContentPrice'] != $cartarr[$cvar['ID']]['ContentPrice']) echo '<font class="red">'.$cartarr[$cvar['ID']]['ContentPrice'].'</font>'; else echo $cartarr[$cvar['ID']]['ContentPrice'];?>&nbsp;</td>

  </tr>
   <? }}?> 

   </tbody>
</table>
		

</div>
</body>
</html>