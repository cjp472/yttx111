<?php
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

if(empty($in['cid'])) $in['cid'] = '';
if(empty($in['kw'])) $in['kw'] = trim($in['kw']);

$customizedExport = '';
if(@file_exists("./consignment_send_excel_".$_SESSION['uinfo']['ucompany'].".php")){
	$customizedExport =  $_SESSION['uinfo']['ucompany'];
}
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
<script src="js/consignment.js?v=4<? echo VERID;?>" type="text/javascript"></script>

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
        	  <form id="FormSearch" name="FormSearch" method="get" action="consignment_send.php">
        		<tr>
					<td width="80" align="center"><strong>商品：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
					<td align="center" width="80"><select name="stype" id="stype" class="selectline">
						<option value="c" <?php if($in['stype']=="c") echo 'selected="selected"'; ?> >订购商品</option>
						<option value="g" <?php if($in['stype']=="g") echo 'selected="selected"'; ?> > 赠品 </option>
					</select></td>
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
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="consignment_send.php">发货明细</a>  </div> </td>
					</tr>
   	          </form>
			 </table>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
		<fieldset  class="fieldsetstyle">
			<legend>发货商品明细数据</legend>
            <form id="MainForm" name="MainForm" method="post" action="consignment_send_excel.php" target="exe_iframe" >

<table width="100%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr class="bottomlinebold">
	<td width="3%" >&nbsp;</td>
    <td width="3%" >行号</td>
	<td width="20%" >药店</td>
	<td width="6%" >&nbsp;编号</td>
    <td >&nbsp;商品名称</td>
    <td width="8%" >&nbsp;规格</td>	
    <td width="6%" align="right">发货数</td>	
	<td width="4%" align="right">单位</td>
    <td width="8%" align="right">发货时间&nbsp;</td> 
    <td width="10%" align="right">相关订单&nbsp;</td> 
    <td width="4%" align="right">发货单</td> 
  </tr>
   </thead>
   <tbody>
	<?php 
			$sqlmsg = $sqlmsg1 = '';			
			if(!empty($in['cid']))   $sqlmsg  .= " and con.ConsignmentClient = ".$in['cid']." ";
			if(!empty($in['bdate'])) $sqlmsg  .= ' and con.InputDate > '.strtotime($in['bdate'].'00:00:00').' ';
			if(!empty($in['edate'])) $sqlmsg  .= ' and con.InputDate < '.strtotime($in['edate'].'23:59:59').' ';
			if(!empty($in['stype'])){
				$sqlmsg  .= " and l.ConType = '".$in['stype']."' ";
			}
			if($in['stype'] == "g"){
				$carttalbe = DATATABLE."_order_cart_gifts ";
			}else{
				$carttalbe = DATATABLE."_order_cart ";
			}

			if(!empty($in['kw']))	 $sqlmsg  .= " AND (i.Name LIKE '%".$in['kw']."%' OR CONCAT(i.Pinyi,i.Coding,i.Barcode) LIKE '%".$in['kw']."%') ";

			$rowsql = "SELECT count(*) AS allrow  FROM
    ".DATATABLE."_order_consignment con 
    INNER JOIN ".DATATABLE."_order_out_library l 
      ON con.ConsignmentID = l.ConsignmentID 
      INNER JOIN ".$carttalbe." c ON c.ID=l.CartID
	  left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID
  WHERE i.CompanyID=".$_SESSION['uinfo']['ucompany']." and  con.ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ";

			$InfoDataNum = $db->get_row($rowsql);
			$datasql = "SELECT 
    con.ConsignmentID,con.ConsignmentClient,con.ConsignmentOrder,con.InputDate,l.ContentNumber,c.ContentID,c.ContentName,c.ID,c.ContentColor,c.ContentSpecification,i.Name,i.Coding,i.Units,i.Model
  FROM
    ".DATATABLE."_order_consignment con 
    INNER JOIN ".DATATABLE."_order_out_library l 
      ON con.ConsignmentID = l.ConsignmentID 
      INNER JOIN ".$carttalbe." c ON c.ID=l.CartID
	  left join ".DATATABLE."_order_content_index i ON c.ContentID=i.ID
  WHERE i.CompanyID=".$_SESSION['uinfo']['ucompany']." and con.ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']."  ".$sqlmsg." order by con.ConsignmentID desc,c.ID desc ";
			
			$page = new ShowPage;
			$page->PageSize = 50;
			$page->Total    = $InfoDataNum['allrow'];
			$page->LinkAry  = array("kw"=>$in['kw'],"cid"=>$in['cid'],"stype"=>$in['stype'],"kw"=>$in['kw'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);  
			$totalnumber = 0;
			$list_data = $db->get_results($datasql." ".$page->OffSet());
			$n = 1;
			if(!empty($list_data))
			{				
				if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
				foreach($list_data as $ckey=>$cvar)
				{
					$totalnumber = $totalnumber + $cvar['ContentNumber'];
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
	<td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $cvar['ID'];?>" value="<? echo $cvar['ID'];?>"  /></td>
    <td ><? echo $n++;?></td>
	<td ><a href="client_content.php?ID=<? echo $cvar['ConsignmentClient'];?>" target="_blank"><? echo $clientarr[$cvar['ConsignmentClient']];?></a></td>
	<td ><? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
	<td>&nbsp;<? echo $cvar['Model'];?> </td>
    <td align="right" title="<? echo $cvar['Units'];?>"><? echo $cvar['ContentNumber'];?>	</td>
	<td align="right"><? echo $cvar['Units'];?>&nbsp;</td> 
	<td align="right"><? echo date("Y-m-d",$cvar['InputDate']);?></td> 
    <td  align="right"><a href="order_manager.php?SN=<? echo $cvar['ConsignmentOrder'];?>" target="_blank"><? echo $cvar['ConsignmentOrder'];?></a>&nbsp;</td>
    <td  align="right">[<a href="consignment_content.php?ID=<? echo $cvar['ConsignmentID'];?>" target="_blank">查看</a>]&nbsp;</td>
  </tr>
   <? }?> 
    <tr id="linegoods_"  >
    <td height="30" class="selectinput" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
	<td >全选</td>
	<td colspan="2" class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_consignment_send_excel('<?php echo $customizedExport;?>','<?php if($in['stype']=="g") echo 'g'; else echo 'c'; ?>');"  >批量导出</a></li></ul></td>

	<td class="font12">本页小计：</td>
    <td>&nbsp; </td>
	<td>&nbsp; </td>
    <td align="right" class="font12"><? echo $totalnumber;?>	</td>
	<td align="center"></td> 
	<td align="right"></td> 
    <td  align="right">&nbsp;</td>
    <td  align="right">&nbsp;</td>
  </tr>
   <? }?>
   </tbody>
</table>

			  <br />
              <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
				   <td  align="right"><? echo $page->ShowLink('consignment_send.php');?></td>
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