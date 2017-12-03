<?php
$menu_flag = "return";
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
<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>

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
        	  <form id="FormSearch" name="FormSearch" method="get" action="return_product.php">
        		<tr>
					<td width="80" align="center"><strong>退单商品：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
					<td width="180" align="center"><select id="cid" name="cid" style="width:160px;" class="select2">
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
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="order_product.php">商品明细</a>  </div> </td>
					</tr>
   	          </form>
			 </table>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
		<fieldset  class="fieldsetstyle">
			<legend>退单商品明细数据</legend>
            <form id="MainForm" name="MainForm" method="post" action="order_excel.php" target="exe_iframe" >

<table width="100%" border="0" cellspacing="2" cellpadding="0" >
  <thead>
  <tr>
    <td width="5%" >&nbsp;行号</td>
	<td width="12%">编号</td>
    <td>&nbsp;商品名称</td>
    <td width="18%">&nbsp;颜色/规格</td>
    <td width="6%" align="right">退货数</td> 
	<td width="5%" align="center">单位</td>
	<td width="8%" align="right">价格</td>
    <td width="12%" align="right">退单&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<?php 
			$sqlmsg = $sqlmsg1 = '';
			
			if(!empty($in['cid']))   $sqlmsg1 .= " and o.ReturnClient = ".$in['cid']." ";
			if(!empty($in['bdate'])) $sqlmsg  .= ' and o.ReturnDate > '.strtotime($in['bdate'].'00:00:00').' ';
			if(!empty($in['edate'])) $sqlmsg  .= ' and o.ReturnDate < '.strtotime($in['edate'].'23:59:59').' ';
			
			if(!empty($in['kw'])) $sqlmsg1 .= " AND (i.Name LIKE '%".$in['kw']."%' OR CONCAT(i.Pinyi,i.Coding,i.Barcode) LIKE '%".$in['kw']."%') ";
			$rowsql = "SELECT count(*) AS allrow from ".DATATABLE."_order_returninfo o inner join ".DATATABLE."_order_cart_return c ON o.ReturnID=c.ReturnID left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID where o.ReturnCompany=".$_SESSION['uinfo']['ucompany']." and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus = 5) ".$sqlmsg1." ".$sqlmsg." ";
			
			$datasql = "select i.Coding,i.Units,o.ReturnID,o.ReturnSN,c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentNumber from ".DATATABLE."_order_returninfo o inner join ".DATATABLE."_order_cart_return c ON o.ReturnID=c.ReturnID left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID  where o.ReturnCompany=".$_SESSION['uinfo']['ucompany']." and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus = 5) ".$sqlmsg1." ".$sqlmsg." order by c.ID desc ";
			$InfoDataNum = $db->get_row($rowsql);
			$page = new ShowPage;
			$page->PageSize = 30;
			$page->Total    = $InfoDataNum['allrow'];
			$page->LinkAry  = array("kw"=>$in['kw'],"cid"=>$in['cid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);  
	
			$list_data = $db->get_results($datasql." ".$page->OffSet());
			$n = 1;
			$totalnumber = $totalmoney = 0;
			if(!empty($list_data))
			{
				if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
				foreach($list_data as $ckey=>$cvar)
				{
					$totalnumber = $totalnumber + $cvar['ContentNumber'];
					$totalmoney = $totalmoney + $cvar['ContentNumber'] * $cvar['ContentPrice'];

	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td ><? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> / <?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>	</td>
	<td align="center"><? echo $cvar['Units'];?></td> 
	<td align="right">¥ <? 
		echo $pricepencent = $cvar['ContentPrice'];
	?> </td>
    <td  align="right"><a href="return_manager.php?ID=<?php echo $cvar['ReturnID'];?>" target="_blank"><?php echo $cvar['ReturnSN'];?></a>&nbsp;</td>
  </tr>
   <? }?> 
    <tr id="linegoods_"  >
    <td height="30">&nbsp;</td>
	<td class="font12">本页小计：</td>
    <td ></td>
    <td>&nbsp; </td>
	<td class="font12" align="right"><? echo $totalnumber;?> </td>
	<td align="center"></td> 
    <td align="right" class="font12">¥  <? echo $totalmoney;?></td>
	
	<td align="right"></td> 
    <td  align="right">&nbsp;</td>
  </tr>
   <? }?>
   </tbody>
</table>


              <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
				   <td  align="right"><? echo $page->ShowLink('return_product.php');?></td>
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