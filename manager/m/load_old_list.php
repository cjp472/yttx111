<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

?>
<div class="bgw">
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p{color:#333333; font-size:12px; line-height:150%;}
thead td{font-weight:bold; height:24px;}
tbody td{border-bottom:#efefef solid 1px;}
.font12{font-weight:bold;}
.red{color:red; font-weight:bold;}
.tdbg{background-color: #fffceb;}
-->
</style>
<?php
$cartbakinfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID='".intval($in['oid'])."' limit 0,1");
if(!empty($cartbakinfo['allrow']))
{
?>
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr style="background-color:#f9f9f9;">
    <td width="6%" >&nbsp;行号</td>
    <td>&nbsp;商品名称</td>
    <!-- <td width="24%">&nbsp;生产厂家</td> -->
    <!-- <td width="12%">&nbsp;规格</td> -->
    <td width="24%">&nbsp;品牌</td>
    <td width="12%">&nbsp;型号</td>
    <td width="6%" align="right">原数量</td>
    <td width="6%" align="right" class="tdbg">现数量</td>
    <td width="6%" align="right">原单价</td> 
    <td width="6%" align="right" class="tdbg">现单价</td> 
    <td width="6%" align="right">原折扣</td> 
    <td width="6%" align="right" class="tdbg">现折扣</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	//获取去厂家  by zjb 20160623
	$brandsql   = "SELECT * FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." ORDER BY BrandPinYin ASC";
	$brandsql_data = $db->get_results($brandsql);
	foreach ($brandsql_data as $val){
	    $brandsqlarr[$val['BrandID']] = $val;
	}
	
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	$cartbakinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID='".$in['oid']."' limit 0,1");
	if(!empty($cartbakinfo))
	{
		$cartdata = $db->get_results("select * from ".DATATABLE."_order_cart  where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$in['oid']." order by ID asc");
		foreach($cartdata as $var)
		{
			$cartarr[$var['ID']] = $var;
		}		
		
		$cartbakarr = unserialize($cartbakinfo['Content']);
		foreach($cartbakarr as $ckey=>$cvar)
		{
			$oldidarr[] = $cvar['ID'];
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" style="background-color:#fff;"  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><?
	if(empty($cartarr[$cvar['ID']])) echo '<font color=red>[已删除]</font> ';
	?><? echo $cvar['ContentName'];?></a></td>
	<td ><? echo $brandsqlarr[$cvar['BrandID']]['BrandName'];?></td>
	<td ><? echo $cvar['Model'];?></td>
    <td style="display: none;">&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> / <?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>&nbsp;</td>
	  <td align="right" class="tdbg"><? if($cvar['ContentNumber'] != $cartarr[$cvar['ID']]['ContentNumber']) echo '<font class="red">'.$cartarr[$cvar['ID']]['ContentNumber'].'</font>'; else echo $cartarr[$cvar['ID']]['ContentNumber'];?>&nbsp;	</td>
	<td align="right">¥ <? echo $cvar['ContentPrice'];?> </td>
	<td  align="right" class="tdbg">¥ <?php if($cvar['ContentPrice'] != $cartarr[$cvar['ID']]['ContentPrice']) echo '<font class="red">'.$cartarr[$cvar['ID']]['ContentPrice'].'</font>'; else echo $cartarr[$cvar['ID']]['ContentPrice'];?>&nbsp;</td>
	<td align="right"><? echo $cvar['ContentPercent'];?> </td>
	<td  align="right" class="tdbg"><?php if($cvar['ContentPercent'] != $cartarr[$cvar['ID']]['ContentPercent']) echo '<font class="red">'.$cartarr[$cvar['ID']]['ContentPercent'].'</font>'; else echo $cartarr[$cvar['ID']]['ContentPercent'];?>&nbsp;</td>
  </tr>
   <? }}?>
  <?php
	foreach($cartdata as $cvar)
	{
		if(!in_array($cvar['ID'],$oldidarr))
		{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" style="background-color:#f9f9f9;"  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><?
	 echo '<font color=red>[新增]</font> ';
	?><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> / <?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" >&nbsp;</td>
	  <td align="right" class="tdbg"><? echo $cvar['ContentNumber'];?>&nbsp;	</td>
	<td align="right"> &nbsp;</td>
	<td  align="right" class="tdbg">¥ <? echo $cvar['ContentPrice'];?>&nbsp;</td>
	<td align="right"> &nbsp;</td>
	<td  align="right" class="tdbg"><? echo $cvar['ContentPercent'];?>&nbsp;</td>
  </tr>
	<? }}?> 
   </tbody>
</table>

<?php 
}else{
	echo '<br /><br /><p align="center">此订单无修改记录!</p><br /><br />';
}
?>
</div>