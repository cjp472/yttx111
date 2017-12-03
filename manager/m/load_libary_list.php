<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
?>
<div class="line bgw">
						
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="5%" >&nbsp;行号</td>
	<td width="12%">&nbsp;编号</td>
    <td>&nbsp;商品名称</td>
	<td width="25%">&nbsp;品牌</td>
	<td width="12%">&nbsp;规格</td>
    <td width="8%" align="right">订购数量</td>
    <td width="8%" align="right">实际库存</td>  
    <td width="5%" align="center">单位</td>  
  </tr>
   </thead>
   <tbody>
	<?php
    //wangd 2017-11-28 判断是否为代理商，代理商只能看到自己所管辖商品
    $user_flag = trim($_SESSION['uinfo']['userflag']);
    if ($user_flag == '2')
    {
        $sql1 = "select c.*,b.BrandName from ".DATATABLE."_view_index_cart as c left join ".DATATABLE."_order_brand as b on c.BrandID=b.BrandID and c.CompanyID=b.CompanyID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".intval($in['oid'])." and c.AgentID=".$_SESSION['uinfo']['userid']." order by c.SiteID asc,c.ID asc";
    }
    else //管理员和商业公司可以看到所有订单
    {
		$sql1 = "select c.*,b.BrandName from ".DATATABLE."_view_index_cart as c left join ".DATATABLE."_order_brand as b on c.BrandID=b.BrandID and c.CompanyID=b.CompanyID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".intval($in['oid'])." order by c.SiteID asc,c.ID asc";
    }	

	$cartdata = $db->get_results($sql1);
	$cartdata_gifts = $db->get_results("select * from ".DATATABLE."_view_index_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['oid'])." order by SiteID asc,ID asc");
	foreach($cartdata as $ckey=>$cvar)
	{
		$conidarr[] = $cvar['ContentID'];
	}
	foreach($cartdata_gifts as $ckey=>$cvar)
	{
		$conidarr[] = $cvar['ContentID'];
	}

	$n=1;
	$conidmsg = implode(",",$conidarr);
	$data_all    = $db->get_results("select ContentID,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$conidmsg.")");
	$data_cs    = $db->get_results("select ContentID,ContentColor,ContentSpec,ContentNumber from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$conidmsg.")");

	foreach($data_all  as $dv)
	{
		$libarr[$dv['ContentID']] = $dv['ContentNumber'];
	}
	if(!empty($data_cs))
	{
		foreach($data_cs  as $dv)
		{
			$kid = make_kid2($dv['ContentID'],$dv['ContentColor'],$dv['ContentSpec']);
			$libarr[$kid] = $dv['ContentNumber'];
		}
	}
	foreach($cartdata as $ckey=>$cvar)
	{
		$kkid = make_kid($cvar['ContentID'],$cvar['ContentColor'],$cvar['ContentSpecification']);
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($n,2)!=0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;<? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td><?php echo $cvar['BrandName'];?></td>
    <td>
        &nbsp;<?if(strlen($cvar['Model']) > 0) echo $cvar['Model'];?>
    </td>
	<td align="right"><? echo $cvar['ContentNumber'];?> </td>
	<td align="right" >
        <? echo intval($libarr[$kkid]);?>
    </td>
    <td align="center"><? echo $cvar['Units'];?></td>
  </tr>
   <?php 
		}
	if(!empty($cartdata_gifts))
	{
		foreach($cartdata_gifts as $ckey=>$cvar)
		{
			$kkid = make_kid($cvar['ContentID'],$cvar['ContentColor'],$cvar['ContentSpecification']);
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($n,2)!=0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;<? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(strlen($cvar['ContentSpecification']) > 0) echo $cvar['ContentSpecification'];?> </td>
	<td align="right"><? echo $cvar['ContentNumber'];?> </td>
	<td align="right" ><? echo intval($libarr[$kkid]);?></td> 
    <td align="center"><? echo $cvar['Units'];?></td>
  </tr>
	<? }}?>
   </tbody>
</table>
		</div>	
<?php
function make_kid($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(strlen($product_color)==0 && strlen($product_spec) == 0) return $product_id;

		if(strlen($product_color) == 0) $product_color  = '统一';
		if(strlen($product_spec) == 0) $product_spec    = '统一';

		if(strlen($product_color) > 0 )
		{
		   $kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
		}
		if(strlen($product_spec) > 0)
		{
		   $kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
		}
		return $kid;
	}

	function make_kid2($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(strlen($product_color) > 0)
		{
		   $kid .= "_p_".$product_color;
		}
		if(strlen($product_spec) > 0)
		{
		   $kid .= "_s_".$product_spec;
		}
		return $kid;
	}
	
?>