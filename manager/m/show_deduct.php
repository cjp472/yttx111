<? 
$menu_flag = "saler";
$pope		   = "pope_view";
include_once ("header.php");
if(!intval($in['ID']))
{
	exit('参数错误!');
}else{	 
	$dinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_deduct where CompanyID = ".$_SESSION['uinfo']['ucompany']." and DeductID=".intval($in['ID'])." limit 0,1");

	$listinfo = $db->get_results("SELECT c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,d.ProductDeduct,d.ProductTotal,d.DeductTotal FROM ".DATATABLE."_order_deduct_cart d left join ".DATATABLE."_order_cart c ON d.CartID=c.ID where d.CompanyID=".$_SESSION['uinfo']['ucompany']." and d.OrderID=".$dinfo['OrderID']." and c.OrderID=".$dinfo['OrderID']." order by c.ID asc ");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? echo SITE_NAME;?> - 管理平台</title>

<style type="text/css">
<!--
td,div,p,span,select{color:#333333; font-size:12px; line-height:180%; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; }
a{text-decoration:none; color:#33a676; }


td,div,p{color:#333333; font-size:12px; line-height:150%;}
thead td{font-weight:bold; height:24px;}
tbody td{border-bottom:#efefef solid 1px;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.font12{font-weight:bold;}
-->
</style>
</head>

<body >
<div style="overflow:auto; width:100%; height:380px;" >
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="6%" >&nbsp;行号</td>
    <td>&nbsp;商品名称</td>
    <td width="18%">&nbsp;颜色/规格</td>
    <td width="14%" align="right">订购金额</td>
    <td width="10%" align="right">提成比例</td> 
    <td width="14%" align="right">提成金额(元)&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$allproduct = 0;
	$n=1;

	if(!empty($listinfo))
	{
		foreach($listinfo as $ckey=>$cvar)
		{
	?>
    <tr  <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td><?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> / <?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
	<td align="right">¥ <? 
		echo sprintf("%01.2f", round($cvar['ProductTotal'],2));
		$allproduct = $allproduct + $cvar['ProductTotal'];
	?> </td>
	<td align="right"><? echo $cvar['ProductDeduct'];?> % </td>
    <td class="font12" align="right">¥ <? 
		$linetotal = $cvar['DeductTotal'];
		echo sprintf("%01.2f", round($linetotal,2));
		$alltotal  = $alltotal + $linetotal;
	?>&nbsp;</td>
  </tr>
   <? }}?> 
  <tr>
    <td>&nbsp;</td>

    <td height="28" class="font12">合计：</td>

	<td class="font12" colspan="2" align="right">¥ <? echo sprintf("%01.2f", round($allproduct,2));?>&nbsp;</td>
	<td class="font12">&nbsp;</td>
    <td class="font12" colspan="2" align="right">¥ <? echo sprintf("%01.2f", round($alltotal,2));?>&nbsp;</td>
  </tr>
   </tbody>
</table>
</div>
</body>
</html>