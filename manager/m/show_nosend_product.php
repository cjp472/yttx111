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
thead td{font-weight:bold; background-color:#efefef; height:24px;}
tbody td{border-bottom:#efefef solid 1px;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.font12{font-weight:bold;}
-->
</style>
</head>

<body bgcolor=#efefef STYLE="margin:3pt;padding:0pt;border: 1px buttonhighlight;" >
<div style="width:100%; height:440px; overflow:auto; bgcolor:#ffffff; text-align:left;">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr >
    <td width="6%" >&nbsp;行号</td>
	<td width="12%">编号/货号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色</td>
	<td width="12%">&nbsp;规格</td>
	<td width="12%">&nbsp;包装</td>
    <td width="10%" >&nbsp;数量</td>
    <td width="4%" align="center" >单位</td>
  </tr>
   </thead>
   <tbody>
   <?
	if(!empty($in['oid']))
	{
		$bodymsg = '';
		$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentNumber,c.ContentSend,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$in['oid']." order by c.ID asc");

		$n=0;
		foreach($cartdata as $ckey=>$cvar)
		{			
			$lnumber = $cvar['ContentNumber'] - $cvar['ContentSend'];
			if($lnumber < 1) continue;
			$n++;
			$bodymsg .= '<tr class="bottomline" id="linegoods_c_'.$cvar['ID'].'"   >
    <td height="30">&nbsp;'.$n.' <input type="hidden" name="cart_id_c[]" id="cart_id_c_'.$cvar['ID'].'" value="'.$cvar['ID'].'" /></td>
	<td >&nbsp;'.$cvar['Coding'].'</td>
    <td><a href="product_content.php?ID='.$cvar['ContentID'].'" target="_blank">'.$cvar['ContentName'].'</a></td>
    <td>&nbsp;'.$cvar['ContentColor'].'</td>
    <td>&nbsp;'.$cvar['ContentSpecification'].'</td>
    <td>&nbsp;'.$cvar['Casing'].'</td>
    <td >'.$lnumber .'</td>
    <td  align="center">'.$cvar['Units'].'</td>
  </tr>';
		}
		//赠品
		$giftdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentNumber,c.ContentSend,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$in['oid']." order by c.ID asc");

		if(!empty($giftdata))
		{
			foreach($giftdata as $ckey=>$cvar)
			{
				$gnumber = $cvar['ContentNumber'] - $cvar['ContentSend'];
				if($gnumber < 1) continue;
				$n++;
				$bodymsg .= '<tr class="bottomline" id="linegoods_g_'.$cvar['ID'].'"  bgcolor="#efefef" title="赠品" >
		<td height="30">&nbsp;'.$n.' <input type="hidden" name="cart_id_g[]" id="cart_id_g_'.$cvar['ID'].'" value="'.$cvar['ID'].'" /></td>
		<td >&nbsp;'.$cvar['Coding'].'</td>
		<td><a href="product_content.php?ID='.$cvar['ContentID'].'" target="_blank">'.$cvar['ContentName'].'</a></td>
		<td>&nbsp;'.$cvar['ContentColor'].'</td>
		<td>&nbsp;'.$cvar['ContentSpecification'].'</td>
		<td>&nbsp;'.$cvar['Casing'].'</td>
		<td >'.$gnumber .'</td>
		<td  align="center">'.$cvar['Units'].'</td>
	  </tr>';
			}
		}
		echo $bodymsg;
	}
	?>
   </tbody>
</table>
		

</div>
</body>
</html>