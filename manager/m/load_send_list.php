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
    <td>&nbsp;商品名称</td>
	<!-- <td width="24%">&nbsp;生产厂家</td> -->
	<!-- <td width="12%">&nbsp;规格</td> -->     
	<td width="24%">&nbsp;品牌</td>
	<td width="12%">&nbsp;型号</td>     
    <td width="8%" align="right">订购数</td>
    <td width="8%" align="right">已发数</td> 
	<td width="8%" align="right">&nbsp;未发数</td> 
    <td width="5%" align="center">单位</td>  
  </tr>
   </thead>
   <tbody>
	<?php
    //wangd 2017-11-28 判断是否为代理商，代理商只能看到自己所管辖商品
    $user_flag = trim($_SESSION['uinfo']['userflag']);
    if ($user_flag == '2')
    {
    	$sql1 = "select * from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['oid'])." and AgentID=".$_SESSION['uinfo']['userid']." order by SiteID asc,ID asc";
    }
    else //管理员和商业公司可以看到所有订单
    {
		$sql1 = "select * from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['oid'])." order by SiteID asc,ID asc";
    }

	$cartdata = $db->get_results($sql1);
	$cartdata_gifts = $db->get_results("select * from ".DATATABLE."_view_index_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['oid'])." order by SiteID asc,ID asc");
	$n=1;

	//获取去厂家  by zjb 20160623
	$brandsql   = "SELECT * FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." ORDER BY BrandPinYin ASC";
	$brandsql_data = $db->get_results($brandsql);
	foreach ($brandsql_data as $val){
	    $brandsqlarr[$val['BrandID']] = $val;
	}
	foreach($cartdata as $ckey=>$cvar)
	{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($n,2)!=0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
	<td >&nbsp;<? echo $brandsqlarr[$cvar['BrandID']]['BrandName'];?></td>
	<td >&nbsp;<? echo $cvar['Model'];?></td>
    <td style="display:none;">&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?>  </td>
    <td style="display:none;">&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right"><? echo $cvar['ContentNumber'];?>	</td>
	<td align="right"><? echo $cvar['ContentSend'];?> </td>
	<td align="right" ><? $wnum = $cvar['ContentNumber']-$cvar['ContentSend'];
	if($wnum > 0) echo '<span class="font12h">'.$wnum.'</span>'; else $wnum;
	?></td> 
    <td align="center"><? echo $cvar['Units'];?></td>
  </tr>
   <?php 
		}
	if(!empty($cartdata_gifts))
	{
		foreach($cartdata_gifts as $ckey=>$cvar)
		{
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($n,2)!=0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;<? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?>  </td>
    <td>&nbsp;<?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right"><? echo $cvar['ContentNumber'];?>	</td>
	<td align="right"><? echo $cvar['ContentSend'];?> </td>
	<td align="right" ><? $wnum = $cvar['ContentNumber']-$cvar['ContentSend'];
	if($wnum > 0) echo '<span class="font12h">'.$wnum.'</span>'; else $wnum;?></td> 
    <td align="center"><? echo $cvar['Units'];?></td>
  </tr>
	<? }}?>
   </tbody>
</table>
<div class="bgw font14">发货单：</div>
<?php
	$oinfo = $db->get_row("SELECT OrderID,OrderSN,OrderTotal FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['oid'])." limit 0,1");

	$datasql   = "SELECT ConsignmentID,ConsignmentOrder,ConsignmentLogistics,ConsignmentMan,ConsignmentDate,ConsignmentFlag FROM ".DATATABLE."_order_consignment where ConsignmentOrder='".$oinfo['OrderSN']."' and ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." Order by ConsignmentID Desc";
	$list_data = $db->get_results($datasql);
	if(!empty($list_data)){
		$datasql   = "SELECT LogisticsID,LogisticsName FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." Order by LogisticsID ASC";
		$lvdata = $db->get_results($datasql);
		foreach($lvdata as  $lv)
		{
			$logarr[$lv['LogisticsID']] = $lv['LogisticsName'];
		}
		$logarr[0] = '上门自提';	
?>

  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="5%" >&nbsp;行号</td>
	<td width="14%">&nbsp;编号</td>
    <td>&nbsp;货运公司</td>
    <td width="12%">&nbsp;发货人</td>
	<td width="12%">&nbsp;发货时间</td>     
    <td width="8%" align="right">状态</td>
    <td width="14%" align="right">操作</td>
  </tr>
   </thead>
   <tbody>
  <?php
    $n=1;
	foreach($list_data as $cvar){
		if(empty($cvar['ConsignmentLogistics'])) $cvar['ConsignmentLogistics'] = 0;
?>
    <tr id="linegoods_<? echo $cvar['ConsignmentID'];?>" <? if(fmod($n,2)!=0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>   >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;<? echo $cvar['ConsignmentID'];?></td>
    <td ><?php echo $logarr[$cvar['ConsignmentLogistics']];?></td>
    <td>&nbsp;<? echo $cvar['ConsignmentMan'];?>  </td>
    <td>&nbsp;<? echo $cvar['ConsignmentDate'];?> </td>
    <td align="right" id="consignment_<?php echo $cvar['ConsignmentID']; ?>"><? if(empty($cvar['ConsignmentFlag'])) echo '未签收'; else echo '<font color="green">已签收</a>';?>	</td>
	<td align="right">
        <a href="consignment_content.php?ID=<? echo $cvar['ConsignmentID'];?>" target="_blank">[查看]</a>
        <?php if(empty($cvar['ConsignmentFlag'])) { ?>
        <a href="javascript:;" onclick="setSendFlag(<?php echo $cvar['ConsignmentID']; ?>,this);">[确认收货]</a>
        <?php } ?>
    </td>
  </tr>
<?php
		}
	}else{
		echo '<br /><br /><p align="center">暂无发货单!</p><br /><br />';
	}
  ?>
</div>