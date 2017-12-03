<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

if(empty($in['cid'])) $in['cid'] = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/order.js?v=4<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#bdate").datepicker();
		$("#edate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="order_product_gifts.php">
        		<tr>
					<td width="80" align="center"><strong>订单商品：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
					<td width="180"><select id="cid" name="cid" style="width:240px;" class="select2">
				<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
		foreach($clientdata as $areavar)
		{
			$n++;
			if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'"  >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
		}
	?>
				</select></td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="order_product_gifts.php">赠品明细</a> </div></td>
				</tr>
   	          </form>
			 </table>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
		<fieldset  class="fieldsetstyle">
			<legend>订单赠品明细数据</legend>
            <form id="MainForm" name="MainForm" method="post" action="order_excel.php" target="exe_iframe" >

<table width="100%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr class="bottomlinebold">
	<td width="3%" >&nbsp;</td>
    <td width="4%" >行号</td>
	<td width="14%" >药店</td>
	<td width="10%">编号</td>
    <td>商品名称</td>
    <td width="8%">颜色</td>
    <td width="8%">规格</td>
    <td width="5%" align="right">赠送数</td>    
    <td width="5%" align="right">发货数</td>
	<td width="4%" align="right">单位</td>
	<td width="8%" align="right">订购价</td>
    <td width="10%" align="right">订单&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<?php 
			$sqlmsg = $sqlmsg1 = '';
			if(!empty($in['cid']))  $sqlmsg .= " and OrderUserID = ".$in['cid']." ";

			if(!empty($in['kw']))  $sqlmsg1 .= " AND CONCAT(c.Name,c.Pinyi,c.Coding,c.Barcode) LIKE '%".$in['kw']."%' ";

			if(!empty($in['bdate'])) $sqlmsg .= ' and OrderDate > '.strtotime($in['bdate'].'00:00:00').' ';
			if(!empty($in['edate'])) $sqlmsg .= ' and OrderDate < '.strtotime($in['edate'].'23:59:59').' ';
			
			$sqlmsg = str_replace("ClientID","OrderUserID",$sqlmsg);
			$rowsql = "select count(*) as allrow FROM ".DATATABLE."_view_index_gifts c 
			inner join (SELECT OrderID FROM rsung_order_orderinfo
  WHERE OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." AND OrderStatus != 8 AND OrderStatus != 9) AS o ON c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg1."  ";
			
			$InfoDataNum = $db->get_row($rowsql);
			
			$datasql = "select c.Coding,c.Units,c.ID,c.OrderID,c.ClientID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentNumber,c.ContentSend,o.OrderSN 
			FROM ".DATATABLE."_view_index_gifts c 
			inner join (SELECT OrderID,OrderSN FROM rsung_order_orderinfo
  WHERE OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." AND OrderStatus != 8 AND OrderStatus != 9) AS o ON c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg1." ORDER BY c.ID DESC ";

			$page = new ShowPage;
			$page->PageSize = 50;
			$page->Total   = $InfoDataNum['allrow'];
			$page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);  

			$list_data = $db->get_results($datasql." ".$page->OffSet());
			$n = 1;
			$totalnumber = $totalnumbers = $totalmoney = 0;
			if(!empty($list_data))
			{
				if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
				foreach($list_data as $ckey=>$cvar)
				{
					$totalnumber = $totalnumber + $cvar['ContentNumber'];
					$totalnumbers = $totalnumbers + $cvar['ContentSend'];
					$totalmoney = $totalmoney + $cvar['ContentNumber'] * $cvar['ContentPrice'];
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
	<td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $cvar['ID'];?>" value="<? echo $cvar['ID'];?>"  /></td>
    <td height="30"><? echo $n++;?></td>
	<td ><a href="client_content.php?ID=<? echo $cvar['ClientID'];?>" target="_blank"><? echo $clientarr[$cvar['ClientID']];?></a></td>
	<td ><? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td><?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor']; else echo '&nbsp;';?>  </td>
    <td><?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification']; else echo '&nbsp;';?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>	</td>
    <td align="right"><? echo $cvar['ContentSend'];	?></td>
	<td align="right"><? echo $cvar['Units'];?>&nbsp;</td> 
	<td align="right">¥ <? 
		echo $pricepencent = $cvar['ContentPrice'];
	?> </td>
    <td  align="right"><a href="order_manager.php?ID=<? echo $cvar['OrderID'];?>" target="_blank"><? echo $cvar['OrderSN'];?></a>&nbsp;</td>
  </tr>
   <?php } ?>
    <tr id="linegoods_"  >
    <td height="30" class="selectinput" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
	<td >全选</td>
	<td colspan="2" class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_order_product_gifts_excel();"  >批量导出</a></li></ul></td>
    <td colspan="3" class="font12">本页小计： </td>
	<td class="font12" align="right"><? echo $totalnumber;?> </td>
	<td class="font12" align="right"><? echo $totalnumbers;?></td> 
	<td align="right"></td>
    <td align="right" class="font12">¥  <? echo $totalmoney;?></td> 
    <td  align="right">&nbsp;</td>
  </tr>
  <?php }?> 
   </tbody>
</table>


              <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
				   <td  align="right"><? echo $page->ShowLink('order_product_gifts.php');?></td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
			  </fieldset>
       	  </div>
        <br style="clear:both;" />
    </div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>