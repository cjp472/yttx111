<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
	$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

	$sqlmsg = '';
	$locationmsg = '';
	if(empty($in['cid']))
	{
		$in['cid'] = '';
		$sidmsg    = '';
	}else{
		$sqlmsg .=" and OrderUserID = ".intval($in['cid'])." ";
		$cidmsg  = '&cid='.$in['cid'];
		$pagearr['cid'] = $in['cid'];
	}
	if(!empty($in['areaid'])){
		$cidmsg  = '&areaid='.$in['areaid'];
	}

	if(isset($in['pid']) && $in['pid']!='')
	{
		$sqlmsg .= " and OrderPayStatus = ".intval($in['pid'])." and OrderStatus < 8 ";
		$sidmsg  = '&pid='.$in['pid'];
		$pagearr['pid'] = $in['pid'];
	}else{
		$in['pid'] = '';
		$sidmsg    = '';
	}

 	if(isset($in['fid']) && $in['fid']!='')
	{
		$sqlmsg .= " and OrderSendStatus = ".intval($in['fid'])." and OrderStatus < 8  ";
		$sidmsg  = '&fid='.$in['fid'];
		$pagearr['fid'] = $in['fid'];
	}else{
		$in['fid'] = '';
		$sidmsg    = '';
	}

 	if(isset($in['sid']) && $in['sid']!='')
	{
		$sqlmsg .= " and OrderStatus = ".intval($in['sid'])." ";
		$sidmsg  = '&sid='.$in['sid'];
		$locationmsg .= ' &#8250;&#8250; '.$order_status_arr[$in['sid']];
		$pagearr['sid'] = $in['sid'];
	}else{
		$in['sid'] = '';
		$sidmsg    = '';
	}
	$valuearr = get_set_arr('product');
	setcookie("backurl", $_SERVER['REQUEST_URI']);
	
	if(@file_exists("./order_excel_".$_SESSION['uinfo']['ucompany'].".php")){
		$strExcelUrl =  "order_excel_".$_SESSION['uinfo']['ucompany'].".php";
	}else{
		$strExcelUrl = "order_excel.php";
	}
	
	$customizedList = '';
	if(@file_exists("./order_list_excel_".$_SESSION['uinfo']['ucompany'].".php")){
		$customizedList =  $_SESSION['uinfo']['ucompany'];
	}
	
	/* start 判断是否过期 addby lxc 20160421 */
	$timsgu = (strtotime($_SESSION['uc']['EndDate'])+60*60*24);
	$starDate = (strtotime($_SESSION['uc']['BeginDate']." +1 month")+60*60*24);
	$strMsg = '使用';
	$booTimeOut = false;
	if(($timsgu - $starDate) <= 0)
		$strMsg = '试用';
	if(time() > $timsgu){
		$strMsg = '您的'.$strMsg.'时间已经到期，请升级至正式版!';
		$booTimeOut = true;
		
	}
	/* end */
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
<script src="js/order.js?v=<? echo VERID;?>4" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#bdate").datepicker({changeMonth: true,	changeYear: true});
		$("#edate").datepicker({changeMonth: true,	changeYear: true});
		$("#begindate").datepicker({changeMonth: true,	changeYear: true});
		$("#enddate").datepicker({changeMonth: true,	changeYear: true});
	});

	function AlertMsg(){
		var strMsg = '<?php echo $strMsg?>';
		alert(strMsg);
	}
</script>
    <style type="text/css">
        .from-tag { padding:0px 4px; margin-left:5px; background-color:#848484; color:#fff;font-family: "Microsoft YaHei","Source Sans Pro","Helvetica Neue",Helvetica,Arial,sans-serif;
            font-size: 12px;}

        .order-tag { padding: 0px 4px;margin-left:5px;background-color:#848484; color:#fff;font-family: "Microsoft YaHei","Source Sans Pro","Helvetica Neue",Helvetica,Arial,sans-serif;
            font-size: 12px;}
    </style>
</head>

<body>

<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="order.php">
        		<tr>
					<td width="80" align="center"><strong>订单搜索：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
        	        <td width="80">
        	        <select name="stype" id="stype" class="selectline">
						<option value="ordersn" <?php if($in['stype']=="ordersn") echo 'selected="selected"'; ?> >订单号</option>
						<option value="productname" <?php if($in['stype']=="productname") echo 'selected="selected"'; ?> > 商品名称 </option>
						<option value="giftsname" <?php if($in['stype']=="giftsname") echo 'selected="selected"'; ?>>赠品名称</option>
					</select>
					</td>
					<td align="center" width="100"><select name="dtype" id="dtype" class="selectline">
						<option value="order" <?php if($in['dtype']=="order") echo 'selected="selected"'; ?> >订单日期</option>
						<option value="delivery" <?php if($in['dtype']=="delivery") echo 'selected="selected"'; ?> > 交货日期 </option>
					</select></td>
					<td width="220" nowrap="nowrap">从<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> 到 <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /> </td>
					<td align="center" width="100"> 【<a href="javascript:void(0);" onclick="advsearch('show')">高级搜索</a>】 </td>
					<td align="right"><div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a>  <? echo $locationmsg;?></div></td>
				</tr>
   	          </form>
			 </table>           
        </div>
    	<div class="bline"  id="advseaerchdiv" style="display:none;">
		<fieldset class="fieldsetstyle">
			<legend>高级搜索：</legend>		
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="right" >
        	  <form id="AdvSearch" name="AdvSearch" method="get" action="order.php">
			  <input type="hidden" name="m" id="m" value="adv" />
        		<tr>
					<td align="right" width="8%">开始时间：</td>
					<td width="17%" ><input type="text" name="begindate" id="begindate" class="inputline"  value="<? if(!empty($in['begindate'])) echo $in['begindate'];?>" />  </td>
       				<td align="right" width="8%">结束时间：</td>
					<td width="17%"> <input type="text" name="enddate" id="enddate" class="inputline"  value="<? if(!empty($in['enddate'])) echo $in['enddate'];?>" /></td>	
       				<td align="right" width="8%">订 单 号：</td>
					<td width="17%"> <input type="text" name="adv_OrderSN" id="adv_OrderSN" class="inputline"  value="<? if(!empty($in['adv_OrderSN'])) echo $in['adv_OrderSN'];?>" /></td>
					<td align="right" width="8%">经 销 商：</td>
					<td width="17%"> 	
					<select id="adv_cid" name="cid" class="adv_inputline select2">
						<option value="" >⊙ 所有药店</option>
						<?php 
							$selectcidmsg = '';
							foreach($clientdata as $areavar)
							{
								$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
								if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
								$selectcidmsg .= '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'"  >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
							}
							echo $selectcidmsg;
						?>
					</select>
				</td>	
				</tr>
        		<tr>
					<td align="right" >收 货 人：</td>
					<td  ><input type="text" name="adv_OrderReceiveCompany" id="adv_OrderReceiveCompany" class="inputline"  value="<? if(!empty($in['OrderSN'])) echo $in['adv_OrderReceiveCompany'];?>" /></td>
					<td align="right" >联 系 人：</td>
					<td > <input type="text" name="adv_OrderReceiveName" id="adv_OrderReceiveName" class="inputline"  value="<? if(!empty($in['adv_OrderReceiveName'])) echo $in['adv_OrderReceiveName'];?>" /></td>
					<td align="right" >联系电话：</td>
					<td > <input type="text" name="adv_OrderReceivePhone" id="adv_OrderReceivePhone" class="inputline"  value="<? if(!empty($in['adv_OrderReceivePhone'])) echo $in['adv_OrderReceivePhone'];?>" /></td>	
       				<td align="right" >收货地址：</td>
					<td > <input type="text" name="adv_OrderReceiveAdd" id="adv_OrderReceiveAdd" class="inputline"  value="<? if(!empty($in['adv_OrderReceiveAdd'])) echo $in['adv_OrderReceiveAdd'];?>" /></td>	
				</tr>
        		<tr>
					<td align="right" >发货状态：</td>
					<td  >
						<select id="eq_OrderSendStatus" name="fid" class="adv_inputline">
						<option value="" >⊙ 所有状态</option>
						<?php 
							foreach($send_status_arr as $key=>$var)
							{
								if($key==0) continue;
								if($in['fid'] == $key) $smsg = 'selected="selected"'; else $smsg ="";								
								echo '<option value="'.$key.'" '.$smsg.' title="'.$var.'"  >┠-'.$var.'</option>';
							}
						?>
						</select>
					</td>
       				<td align="right" >发货方式：</td>
					<td > 
						<select id="eq_OrderSendType" name="eq_OrderSendType" class="adv_inputline">
						<option value="" >⊙ 所有方式</option>
						<?php 
							foreach($senttypearr as $key=>$var)
							{
								if($key==0) continue;
								if($in['eq_OrderSendType'] == $key) $smsg = 'selected="selected"'; else $smsg ="";								
								echo '<option value="'.$key.'" '.$smsg.' title="'.$var.'"  >┠-'.$var.'</option>';
							}
						?>
						</select>
					</td>	
       				<td align="right" >支付状态：</td>
					<td > 
						<select id="eq_OrderPayStatus" name="pid" class="adv_inputline">
						<option value="" >⊙ 所有状态</option>
						<?php 
							foreach($pay_status_arr as $key=>$var)
							{
								if($in['pid'] == $key  && $in['pid']!='') $smsg = 'selected="selected"'; else $smsg ="";								
								echo '<option value="'.$key.'" '.$smsg.' title="'.$var.'"  >┠-'.$var.'</option>';
							}
						?>
						</select>
					</td>
					<td align="right" >支付方式：</td>
					<td > 
						<select id="eq_OrderPayType" name="eq_OrderPayType" class="adv_inputline">
						<option value="" >⊙ 所有方式</option>
						<?php 
							foreach($paytypearr as $key=>$var)
							{
								if($in['eq_OrderPayType'] == $key) $smsg = 'selected="selected"'; else $smsg ="";								
								echo '<option value="'.$key.'" '.$smsg.' title="'.$var.'"  >┠-'.$var.'</option>';
							}
						?>
						</select>
					</td>	
       				<td align="right" ></td>	
				</tr>
        		<tr>
					<td align="right" >订单状态：</td>
					<td  >
						<select id="eq_OrderStatus" name="sid" class="adv_inputline">
						<option value="" >⊙ 所有状态</option>
						<?php 
							foreach($order_status_arr as $key=>$var)
							{
								if($key==1 || $key==2 || $key==3 || $key==5) continue;
								if($in['sid'] == $key && $in['sid']!='') $smsg = 'selected="selected"'; else $smsg ="";								
								echo '<option value="'.$key.'" '.$smsg.' title="'.$var.'"  >┠-'.$var.'</option>';
							}
						?>
						</select>
					</td>
       				<td align="right">特殊说明：</td>
					<td colspan="3"> <input type="text" name="adv_OrderRemark" id="adv_OrderRemark" class="inputline" style="width:88%;" value="<? if(!empty($in['adv_OrderRemark'])) echo $in['adv_OrderRemark'];?>" /></td>	
					<td colspan="3" align="center"> <input type="submit" name="advbutton" id="advbutton" value=" 搜 索 " class="bluebtn"  />
					<input type="reset" name="resetladvbutton" id="resetladvbutton" onclick="resetadvform();" value=" 重置 " class="redbtn"  />
					 <input type="button" name="canceladvbutton" id="canceladvbutton" value=" 关闭 " class="redbtn" onclick="advsearch('hide')" />
					</td>
				</tr>
   	          </form>
			 </table> 	

		</fieldset>
		</div>
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 
<div class="linebutton"><!--<input type="button" name="newbutton" id="newbutton" value=" 新增订单 " class="button_2" onclick="javascript:window.location.href='order_add.php'" />--> </div> 
<hr style="clear:both;" />
<div ><strong><a href="order.php">药店</a></strong></div>
<ul style="padding: 2px 0 10px 0;">
	<form name="changetypeform" id="changetypeform" action="order.php" method="get">
	<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:85%;">
	<option value="" >⊙ 所有药店</option>
	<?php 
		echo $selectcidmsg;
	?>
	</select>
	</form>
</ul>

<?php include_once ("inc/search_client.php");?>
<hr style="clear:both;"  />
<div ><strong><a href="order.php">所属地区</a></strong></div>
<ul style="padding: 2px 0 10px 0;">
	<form name="changeareaform" id="changeareaform" action="order.php" method="get">
	<select id="areaid" name="areaid" onchange="javascript:submit()"  class="select2" style="width:85%;">
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
					$repeatMsg = str_repeat(" -- ", $layer-2);
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
	</select>
	</form>
</ul>


<hr style="clear:both;"  />
<div ><strong><a href="order.php">所有订单</a></strong></div>
<ul>
<?php 
	foreach($order_status_arr as $skey=>$svar)
	{
		if($skey==1 || $skey==2 || $skey==3 || $skey==5) continue;
		if(isset($in['sid']) && $in['sid']!='')
		{
			if($in['sid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
		}else{
			$smsg ="";
		}
		echo '<li><a href="order.php?sid='.$skey.''.$cidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
	}
?>
</ul>
<hr style="clear:both;" />
<div >
<strong><a href="order.php">支付状态</a></strong></div>
<ul>
	<?php 
		foreach($pay_status_arr as $skey=>$svar)
		{
			if(isset($in['pid']) && $in['pid']!='')
			{
				if($in['pid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg = "";
			}
			echo '<li><a href="order.php?pid='.$skey.''.$cidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
		}
	?>
</ul>
<hr style="clear:both;" />
<div >
<strong><a href="order.php">发货状态</a></strong></div>
<ul>
	<?php 
		foreach($send_status_arr as $skey=>$svar)
		{
			if($skey==0) continue;
			if(isset($in['fid']) && $in['fid']!='')
			{
				if($in['fid'] == $skey) $smsg = 'class="locationli"'; else $smsg ="";
			}else{
				$smsg ="";
			}
			echo '<li><a href="order.php?fid='.$skey.''.$cidmsg.'" '.$smsg.' >'.$svar.'</a></li>';
		}
	?>
</ul>
 </div>
<!-- tree -->   
       	  </div>
        	<div id="sortright">
            <form id="MainForm" name="MainForm" method="post" action="<?php echo $strExcelUrl;?>" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="18%" class="bottomlinebold">&nbsp;订单号</td>
                  <td  class="bottomlinebold">收货信息</td>
                  <td width="20%" class="bottomlinebold" >配送</td>
				  <td width="18%" class="bottomlinebold" >款项</td>
                  <td width="12%" class="bottomlinebold" nowrap="nowrap">&nbsp;管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php
if(empty($in['stype']))
{
	$advsql = '';
	if(!empty($in['m']) && $in['m'] = 'adv')
	{
		$pagearr['m'] = $in['m'];
		foreach($in as $key=>$var)
		{
			if(substr($key,0,4) == "adv_" && !empty($var))
			{
				$advsql .= " and ".substr($key,4)." like '%".$var."%' ";
				$pagearr[$key] = $var;
			}
			if(substr($key,0,3) == "eq_" && !empty($var))
			{
				$advsql .= " and ".substr($key,3)." = '".intval($var)."' ";
				$pagearr[$key] = $var;
			}
		}

		if(!empty($in['begindate']))
		{
			$advsql .= ' and OrderDate > '.strtotime($in['begindate'].'00:00:00').' ';
			$pagearr['begindate'] = $in['begindate'];
		}
		if(!empty($in['enddate']))
		{
			$advsql .= ' and OrderDate < '.strtotime($in['enddate'].'23:59:59').' ';
			$pagearr['enddate'] = $in['enddate'];
		}
	}

	if(!empty($in['areaid']))
	{
		$pagearr['areaid'] = $in['areaid'];
		$in['areaid'] = intval($in['areaid']);
		$sql = "
		SELECT c.ClientID from ".DATATABLE."_order_client c 
		inner join ".DATATABLE."_order_area a ON c.ClientArea=a.AreaID
		where (a.AreaParentID = ".$in['areaid']." OR a.AreaID = ".$in['areaid'].") and a.AreaCompany=".$_SESSION['uinfo']['ucompany']." ";
		$area_client_arr = $db->get_col($sql);	
		$sqlmsg .=" and OrderUserID IN (".$sql.") ";
	}
	//wangd 2017-11-28 判断是否为代理商，代理商只能看到自己所管辖商品相关的订单
	$user_flag = trim($_SESSION['uinfo']['userflag']);
	if ($user_flag == '2')
	{		
		$sqlnum = "SELECT count(DISTINCT o.OrderID) AS allow FROM "
			.DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
			where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and c.AgentID= ".$_SESSION['uinfo']['userid']." "
			.$sqlmsg.$advsql." ";
		$subsql = "SELECT DISTINCT o.OrderID AS allow FROM "
			.DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
			where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and c.AgentID= ".$_SESSION['uinfo']['userid']." "
			.$sqlmsg.$advsql." ";
		$datasql = "SELECT OrderID,OrderSN,OrderUserID,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,DeliveryDate,OrderRemark,OrderTotal,OrderStatus,OrderDate,OrderType,OrderSaler,OrderFrom,OrderSpecial,SMSNotified FROM "
		.DATATABLE."_order_orderinfo 
		where OrderID in (".$subsql.")";
	}
	else //管理员和商业公司可以看到所有订单
	{
		$sqlnum = "SELECT count(*) AS allrow FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg.$advsql." ";
		$datasql   = "SELECT OrderID,OrderSN,OrderUserID,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,DeliveryDate,OrderRemark,OrderTotal,OrderStatus,OrderDate,OrderType,OrderSaler,OrderFrom,OrderSpecial,SMSNotified FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg.$advsql." Order by OrderID Desc";	
	}
	$InfoDataNum = $db->get_row($sqlnum);
	$page        = new ShowPage;
    $page->PageSize = 30;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = $pagearr;        
	
	$list_data = $db->get_results($datasql." ".$page->OffSet());
}else{
	$sdmsg = '';
	$sqlson = '';
	$in['kw'] = trim($in['kw']);
	if($in['dtype'] == 'delivery'){ //交货日期
		if(!empty($in['bdate'])) $sdmsg .= " and DeliveryDate >= '".$in['bdate']."' ";
		if(!empty($in['edate'])) $sdmsg .= " and DeliveryDate <= '".$in['edate']."' ";
	}else{
		if(!empty($in['bdate'])) $sdmsg .= ' and OrderDate > '.strtotime($in['bdate'].'00:00:00').' ';
		if(!empty($in['edate'])) $sdmsg .= ' and OrderDate < '.strtotime($in['edate'].'23:59:59').' ';		
	}

	if($in['stype']=="productname")
	{
		if(!empty($in['kw']))   $sqlson  = " and OrderID IN (SELECT OrderID FROM ".DATATABLE."_view_index_cart WHERE CompanyID = ".$_SESSION['uinfo']['ucompany']." AND CONCAT(Name,Pinyi,Coding,Barcode) LIKE '%".$in['kw']."%') ";
	}
	elseif($in['stype']=="giftsname")
	{
		if(!empty($in['kw']))   $sqlson  = " and OrderID IN (SELECT OrderID FROM  ".DATATABLE."_view_index_gifts WHERE CompanyID = ".$_SESSION['uinfo']['ucompany']." AND CONCAT(Name, Pinyi, Coding, Barcode) LIKE '%".$in['kw']."%') ";		
	}else{
		if(!empty($in['kw']))   $sqlson .= " and OrderSN like '%".$in['kw']."%' ";
	}
        //代理商查询订单判断
        $user_flag = trim($_SESSION['uinfo']['userflag']);
        if ($user_flag == '2')
	{	
		$sqlnum = "SELECT count(DISTINCT o.OrderID) AS allow FROM "
			.DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
			where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and c.AgentID= ".$_SESSION['uinfo']['userid']." "
			.$sqlson.$sdmsg." ";

		$subsql = "SELECT DISTINCT o.OrderID AS allow FROM "
			.DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
			where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and c.AgentID= ".$_SESSION['uinfo']['userid']." "
			.$sqlson.$sdmsg." ";
		$datasql = "SELECT OrderID,OrderSN,OrderUserID,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,DeliveryDate,OrderRemark,OrderTotal,OrderStatus,OrderDate,OrderType,OrderSaler,OrderFrom,OrderSpecial,SMSNotified FROM "
		.DATATABLE."_order_orderinfo 
		where OrderID in (".$subsql.")";
	}
	else //管理员和商业公司可以看到所有订单
	{
		$sqlnum = "SELECT count(*) AS allrow FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlson.$sdmsg." ";
		$datasql   = "SELECT OrderID,OrderSN,OrderUserID,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,DeliveryDate,OrderRemark,OrderTotal,OrderStatus,OrderDate,OrderType,OrderSaler,OrderFrom,OrderSpecial,SMSNotified FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlson.$sdmsg." Order by OrderID Desc";	
	}
        
	$InfoDataNum = $db->get_row($sqlnum);
	$page = new ShowPage;
	$page->PageSize = 30;
	$page->Total   = $InfoDataNum['allrow'];
	$page->LinkAry = array("kw"=>$in['kw'],"stype"=>$in['stype'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);  
	$list_data = $db->get_results($datasql." ".$page->OffSet());
} 
	if(!empty($list_data))
	{
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['OrderID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" title="<? echo $lsv['OrderRemark'];?>" >
                  <td height="48" -style="width:22%">
					<span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['OrderID'];?>" value="<? echo $lsv['OrderID'];?>" /></span>&nbsp;&nbsp;<span title="订单状态" class=red><? echo $order_status_arr[$lsv['OrderStatus']];?></span><br />
					<span title="订单号"><a href="order_manager.php?ID=<? echo $lsv['OrderID'];?>" class="no" style="display:inline;padding: 3px 6px;"><? echo $lsv['OrderSN'];?></a></span>
                      <?php if($lsv['OrderSpecial'] == 'T') {
                          ?>
                        <span style="color:#B61BD2;border:1px solid;margin-left:5px;"><b>特价单</b></span>
                      <?php
                      } ?>
                      <br />
                    <span title="订单时间" style="color:#999;"><? echo date("Y-m-d H:i",$lsv['OrderDate']);?></span>
                    <span  id="sms_<? echo $lsv['OrderID'];?>"><?php if($lsv['SMSNotified'] == "T") echo '<span title="已短信通知药店收到了订单" class="font12">[已通知]</span>';?></span>
				  </td>
                  <td >
						<a href="client_content.php?ID=<? echo $lsv['OrderUserID'];?>" target="_blank"><? echo $clientarr[$lsv['OrderUserID']];?></a><br />
						<span title="收货人"><? echo $lsv['OrderReceiveName'];?></span><br />
						<span title="联系方式"><? echo $lsv['OrderReceivePhone'];?></span><br />
				  </td>

				  <td >
					<span title="配送方式"><? echo $senttypearr[$lsv['OrderSendType']];?></span>&nbsp;&nbsp;
				    <?php
				    if($lsv['OrderStatus']=="8" || $lsv['OrderStatus']=="9"){
				    	echo '<span title="配送状态" class="red">[已取消]</span>';
				    }else{
				    ?>
				    <span title="配送状态" class="red">[<? echo $send_status_arr[$lsv['OrderSendStatus']];?>]</span><br />
				    <?php
				    if(!empty($lsv['DeliveryDate']) && $lsv['DeliveryDate'] != '0000-00-00') echo '<span title="交货日期" class="green">交货日期：'.$lsv['DeliveryDate'].'</span><br />';
				    ?>
					<span title="开票出库" > <?php 
					$ptarr = array(4,5,6,8);
					if(($lsv['OrderSendStatus'] == "1" || $lsv['OrderSendStatus'] == "3") && (($lsv['OrderPayStatus'] < 2 && in_array($lsv['OrderPayType'], $ptarr)) || $lsv['OrderPayStatus'] >= 2)) echo '<a href="consignment_add.php?ID='.$lsv['OrderID'].'" class="buttonb" >&nbsp;&nbsp;开票出库&nbsp;&nbsp;</a>';?></span>
					<?php }?>
				  </td>
                  <td >
				  <?php 
				  //2017-12-12 ymm 判断当前登录的人的身份如果是代理商或者是代理商的客情的话就查询出对应的订单信息
				   $user_flag = trim($_SESSION['uinfo']['userflag']);
				   if ($user_flag == '2'){
				        $sql1 = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$lsv['OrderID']." and AgentID=".$_SESSION['uinfo']['userid']." order by SiteID asc, BrandID asc, ID asc";
				    }
				    else //管理员和商业公司可以看到所有订单
				    {
				        $sql1 = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$lsv['OrderID']." order by SiteID asc, BrandID asc, ID asc";
				    }
				   $total=$db->get_results($sql1);
				   $alltotal=0;
				   //2017-12-12 算出负责的订单总金额
				  foreach ($total as $key => $cvar) {
				  	$alltotal+=$cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
				  }
				  echo "<span title='金额' class=font12>¥ ".$alltotal."</span><br />
				  <span title='付款方式'>".$paytypearr[$lsv['OrderPayType']]."</span>";
				  if($lsv['OrderStatus']=="8" || $lsv['OrderStatus']=="9"){
				    	echo '<br /><span title="付款状态" class="red">[已取消]</span>';
				  }
				  else{
				  	echo "<span title='付款状态' class=red>&nbsp;&nbsp;[".$pay_status_arr[$lsv['OrderPayStatus']]."]</span> <br />";
				  if($lsv['OrderPayStatus'] != '2' && $lsv['OrderPayStatus'] != '4' && $lsv['OrderStatus']!='8' && $lsv['OrderStatus']!='9' ) echo '<a href="finance_add.php?oid='.$lsv['OrderID'].'" class="buttonb">&nbsp;&nbsp; 添加收款单&nbsp;&nbsp;</a>';
				  }
				  ?>
					</td>
                  <td >
                      <?php
                        switch($lsv['OrderType']){
                            case 'M':
                                echo "<span class='order-tag' title='管理员代下单'>管理员</span>";
                                break;
                            case 'S':
                                echo "<span class='order-tag' title='客情官代下单'>客情官</span>";
                                break;
                            case 'C':
                                echo "<span class='order-tag' title='客户下单'>客户</span>";
                                break;
                            default:;
                        }

                        switch($lsv['OrderFrom']){
                            case 'WeiXin':
                                echo "<span class='from-tag' title='微信下单'>微信</span>";
                                break;
                            case 'Compute':
                                echo "<span class='from-tag' title='电脑下单'>电脑</span>";
                                break;
                            case 'Mobile':
                                echo "<span title='手机浏览器下单' class='from-tag'>手机</span>";
                                break;
                            case 'Android':
                                echo "<span title='安卓手机下单' class='from-tag'>安卓</span>";
                                break;
                            case 'Ios':
                                echo "<span title='苹果手机下单' class='from-tag'>苹果</span>";
                                break;
                            case 'Api':
                                echo "<span class='from-tag' title='Api接口下单'>接口</span>";
                                break;
                            default:;
                        }

                        echo "<br/>";
                      ?>
					<?
					if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on")
					{
						 if($lsv['OrderSaler']=="T") echo '<span title="初审状态" class=font12>已初审</span><br />'; else echo '<span title="初审状态" class=red>未初审</span><br />';
					}	  
				  ?>
					<a href="order_manager.php?ID=<? echo $lsv['OrderID'];?>" class="buttonb" style="margin:10px 0px;display:inline-block;padding:0 4px;" >&nbsp;&nbsp; 管理订单&nbsp;&nbsp;</a>
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
       			   <td width="4%" align="center"  height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink">
                       <ul>
                           <li><a href="javascript:void(0);" title="短信通知药店收到订单" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'notify_client(this);'; ?>">收到订单</a></li>
                           <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_order_excel();'; ?>" >导出详单</a></li>
                           <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_orderlist_excel(\''.$customizedList.'\');'; ?>" >导出数据</a></li>
                           <li><a href="javascript:void(0);" onclick=" out_all_orderlist_excel();" >全部导出</a></li>
				   <!--
				   <? if($_SESSION['uinfo']['userid']=="1" || $_SESSION['uinfo']['ucompany']=="51" || $_SESSION['uinfo']['ucompany']=="102"){?>
				   <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_order_kis_excel();'; ?>" >导出到KIS</a></li>
				   <? }?>
				   <? if($_SESSION['uinfo']['userid']=="1" || $_SESSION['uinfo']['ucompany']=="397" ){?>
				   <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_order_k3_excel();'; ?>" >导出到K3</a></li>
				   <? }?>
				   <? if($_SESSION['uinfo']['userid']=="1" || $_SESSION['uinfo']['ucompany']=="193"){?>
				   <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_order_sd_excel();'; ?>" >导出到速达</a></li>
				   <? }?>
				   <? if($_SESSION['uinfo']['userid']=="1" || $_SESSION['uinfo']['ucompany']=="261"){?>
				   <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_order_gjp_excel();'; ?>" >导出管家婆</a></li>
				   <? }?>
				   <? if($_SESSION['uinfo']['userid']=="1"){?>
				   <li><a href="javascript:void(0);" onclick="<?php if($booTimeOut) echo 'AlertMsg();'; else echo 'out_order_kis2_excel();'; ?>" >导出到KIS</a></li>
				   <? }?>
				   -->
				   </ul></td>
     			 </tr>
                  <tr height="40">
                      <td colspan="5"  align="right"><? echo $page->ShowLink('order.php');?></td>
                  </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>


<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
        <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">导出全部商品数据</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>
</body>
</html>