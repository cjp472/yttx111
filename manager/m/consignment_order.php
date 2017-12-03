<?php
$menu_flag = "consignment";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

	$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

	$valuearr = get_set_arr('product');
	setcookie("backurl", $_SERVER['REQUEST_URI']);
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
		$("#bdate").datepicker({changeMonth: true,	changeYear: true});
		$("#edate").datepicker({changeMonth: true,	changeYear: true});
	});
</script>
</head>

<style>
.addf{background-color:#33a676;display:block;height:24px;line-height:24px;text-align:center;width:71px;}
.addf>a{color:#fff !important;}
</style>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="consignment_order.php">
        		<tr>
					<td width="80" align="center"><strong>订单搜索：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
        	        <td width="100">	
					<select id="areaid" name="areaid" class="select2" style="width:135px;" >
	<option value="" >⊙ 所有地区</option>
	<?php 
		$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
		echo ShowTreeMenu($sortarr,0,$in['areaid']);

		function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
		{
			$frontMsg  = "";
			$frontTitleMsg = "";
			$selectmsg = "";
			
			if($var['AreaParentID']=="0") $layer = 1; else $layer++;
						
			foreach($resultdata as $key => $var)
			{
				if($var['AreaParentID'] == $p_id)
				{
					$repeatMsg = str_repeat(" -- ", $layer-1);
					if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
					
					$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	

					$frontMsg2  = "";
					$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
					$frontMsg  .= $frontMsg2;
				}
			}		
			return $frontMsg;
		}
	?>	
	</select></td>
					<td width="170">
				<select id="cid" name="cid"  style="width:160px !important; width:145px;margin-left:3px;" class="select2">
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
				</select>
					</td>
					<td align="center" width="100"><select name="dtype" id="dtype" class="selectline">
						<option value="order" <?php if($in['dtype']=="order") echo 'selected="selected"'; ?> >订单日期</option>
						<option value="delivery" <?php if($in['dtype']=="delivery") echo 'selected="selected"'; ?> > 交货日期 </option>
					</select></td>
					<td width="220" nowrap="nowrap">从<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> 到 <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="consignment_order.php">待发货订单</a> </div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
            <form id="MainForm" name="MainForm" method="post" action="order_excel.php" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="14%" class="bottomlinebold">&nbsp;订单号</td>
                  <td width="22%" class="bottomlinebold">收货信息</td>
                  <td width="14%" class="bottomlinebold" >配送</td>
				  <td width="14%" class="bottomlinebold" >款项</td>
				  <td  class="bottomlinebold" >备注</td>
                  <td width="8%" class="bottomlinebold" nowrap="nowrap">&nbsp;&nbsp;发货</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php
		$sdmsg = ' and (OrderSendStatus=1 or OrderSendStatus=3) ';
		if(!empty($in['cid']))   $sdmsg .=" and OrderUserID = ".intval($in['cid'])." ";
		if(!empty($in['kw']))   $sdmsg .= " and OrderSN like '%".trim($in['kw'])."%' ";

		if($in['dtype'] == 'delivery'){ //交货日期
			if(!empty($in['bdate'])) $sdmsg .= " and DeliveryDate >= '".$in['bdate']."' ";
			if(!empty($in['edate'])) $sdmsg .= " and DeliveryDate <= '".$in['edate']."' ";
		}else{
			if(!empty($in['bdate'])) $sdmsg .= ' and OrderDate > '.strtotime($in['bdate'].'00:00:00').' ';
			if(!empty($in['edate'])) $sdmsg .= ' and OrderDate < '.strtotime($in['edate'].'23:59:59').' ';		
		}
		if(!empty($in['areaid']))
		{
			$in['areaid'] = intval($in['areaid']);
			$sql = "
			SELECT c.ClientID from ".DATATABLE."_order_client c 
			inner join ".DATATABLE."_order_area a ON c.ClientArea=a.AreaID
			where (a.AreaParentID = ".$in['areaid']." OR a.AreaID = ".$in['areaid'].") and a.AreaCompany=".$_SESSION['uinfo']['ucompany']." ";
			$area_client_arr = $db->get_col($sql);	
			$sdmsg .=" and OrderUserID IN (".$sql.") ";
		}

		$sdmsg .= " and ((OrderPayStatus < 2 and OrderPayType IN (4,5,6,8)) or OrderPayStatus >= 2)";
		$sqlnum = "select count(*) as allrow from ".DATATABLE."_order_orderinfo where  OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sdmsg." ";
		$datasql = "SELECT OrderID,OrderSN,OrderUserID,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,DeliveryDate,OrderRemark,OrderTotal,OrderStatus,OrderDate,OrderSaler FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']."  ".$sdmsg." Order by OrderID Desc";

		$InfoDataNum = $db->get_row($sqlnum);
		$page = new ShowPage;
		$page->PageSize = 30;
		$page->Total = $InfoDataNum['allrow'];
		$page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid'],"bdate"=>$in['bdate'],"edate"=>$in['edate'],"areaid"=>$in['areaid']);  
		$list_data = $db->get_results($datasql." ".$page->OffSet());


	if(!empty($list_data))
	{
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['OrderID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" title="<? echo $lsv['OrderRemark'];?>" >
                  <td height="48" >
					<span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['OrderID'];?>" value="<? echo $lsv['OrderID'];?>" /></span>&nbsp;&nbsp;
					<span title="订单状态" class=red><? echo $order_status_arr[$lsv['OrderStatus']];?></span><br />
					<span title="订单号"><a href="order_manager.php?ID=<? echo $lsv['OrderID'];?>" class="no1" ><? echo $lsv['OrderSN'];?></a></span>
				  </td>
                  <td >
						<a href="client_content.php?ID=<? echo $lsv['OrderUserID'];?>" target="_blank"><? echo $clientarr[$lsv['OrderUserID']];?></a><br />
						<span title="收货人"><? echo $lsv['OrderReceiveName'];?></span> , 
						<span title="联系方式"><? echo $lsv['OrderReceivePhone'];?></span><br />
				  </td>

				  <td >
					<span title="配送方式"><? echo $senttypearr[$lsv['OrderSendType']];?></span><br />
				    <span title="配送状态" class="red"><? echo $send_status_arr[$lsv['OrderSendStatus']];?></span><br />				
				  </td>
                  <td >
					<? echo "<span title='金额' class=font12>¥ ".$lsv['OrderTotal']."</span><br /><span title='付款方式'>".$paytypearr[$lsv['OrderPayType']]."</span>, <span title='付款状态' class=red>".$pay_status_arr[$lsv['OrderPayStatus']]."</span><br />";?>
					</td>
				  <td>
				  <?php 
				  if(!empty($lsv['DeliveryDate']) && $lsv['DeliveryDate'] != '0000-00-00'){ echo '<span title="交货日期" class="green">交货日期：'.$lsv['DeliveryDate'].'</span><br />';}
				  echo $lsv['OrderRemark'];
				  ?></td>
                  <td >
					<span title="添加发货单" class="addf"> <?php echo '<a href="consignment_add.php?ID='.$lsv['OrderID'].'" >&nbsp;添加发货单&nbsp;</a>';?></span>
				  </td>
                </tr>
	<? } }else{?>
     			 <tr>
       				 <td colspan="5" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
	<? }?>
 				</tbody>                
              </table>
			   <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="2%" align="left"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="out_order_excel();" >导出详单</a></li><li><a href="javascript:void(0);" onclick="out_orderph_excel();" >导出配货单</a></li>
				   </ul></td>
				   <td  align="right"><? echo $page->ShowLink('consignment_order.php');?></td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        </div>
        <br style="clear:both;" />
    </div>


<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>