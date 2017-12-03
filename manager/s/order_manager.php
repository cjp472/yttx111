<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

	if(!empty($in['ID']))
	{	 
		$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
	}elseif(!empty($in['SN'])){
		$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$in['SN']."' limit 0,1");
	}else{
		exit('错误参数!');
	}

	$cinfo = $db->get_row("SELECT c.ClientID,c.ClientName,c.ClientCompanyName,c.ClientTrueName,c.ClientPhone,c.ClientMobile,c.ClientAdd,c.lastOrderAt FROM ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany = ".$_SESSION['uinfo']['ucompany']." and c.ClientID=".$oinfo['OrderUserID']."  and s.SalerID=".$_SESSION['uinfo']['userid']." limit 0,1");

	if(empty($cinfo))
	{
		echo '<p>&nbsp;</p><p>参数错误！<a href="javascript:history.back(-1)">点此返回</a></p>';
		exit;
	}

    //wangd 2017-11-29 判断是否为代理商，代理商只能看到自己所管辖商品
    $user_flag = trim($_SESSION['uinfo']['userflag']);
    if ($user_flag == '2')
    {
        $type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$_SESSION['uinfo']['userid']."");

        $sqlcart = "select c.*,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart c 
            left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID 
            where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany'].
            " and c.OrderID=".$oinfo['OrderID']." and i.AgentID=".$type["UpperID"]." order by c.ID asc";
    }
    else //管理员和商业公司可以看到所有订单商品
    {
        $sqlcart = "select c.*,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by c.ID asc";
    }
    
    $cartdata = $db->get_results($sqlcart);

	$cidmsg = '';
	setcookie("backurl", $_SERVER['REQUEST_URI']);
	$valuearr = get_set_arr('product');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style>
		body{
            font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important;
            padding: 0;
            margin: 0;
            position:relative;
        }
        .message-dialog{
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.2);
            *background: #000;
            background: #000 \0;
            *filter:Alpha(opacity=20);
            filter:Alpha(opacity=20) \0;
            *zoom:1;
            zoom:1 \0;
        }
        .message-box{
            width: 700px;
            height: 350px;
            background: url("img/order-bg.jpg") no-repeat center;
            margin: auto auto auto auto;
            position: relative;
            top: 200px;
        }
        .message-info{
            width: 430px;
            margin-left: 30px;
            padding-top: 40px;
        }
        .message-info  h4{
            font-size: 24px;
            font-weight: lighter;
            color: #000;
            margin-top: 0;
            margin-bottom: 0;
        }
        .message-info  p{
            font-size: 14px;
            color: #525252;
            line-height: 24px;
            margin-top: 45px;
            margin-bottom: 60px;
        }
        .message-info  p span{
            color: #FF4A00;
            font-size:14px !important;
        }
        .get-idea{
            display: block;
            width: 380px;
            height: 40px;
            line-height: 40px;
            color: #ff4a00;
            text-decoration: none;
            font-size: 20px;
            float: left;
        }
        .close-dialog{
            font-size: 20px;
            font-weight: bold;
            color: #fff;
            font-style: normal;
            position: absolute;
            top: 0px;
            right: 10px;
            cursor:pointer;
        }
</style>
</head>

<body>
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>

   

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="order.php">
        		<tr>
					<td width="80" align="center"><strong>订单搜索：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>

					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a>  <? echo $locationmsg;?></div></td>
				</tr>
   	          </form>
			 </table>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
		<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">订单号：<span class="font14h"><? echo $oinfo['OrderSN'];?> <? if($oinfo['OrderType']=="M") echo "(管理员代下单)"; elseif($oinfo['OrderType']=="S") echo "(业务员代下单)";?></span>
					<? if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on"){?>&nbsp;&nbsp;&nbsp;&nbsp;   初审状态：<span class="red"><? if($oinfo['OrderSaler']=="T") echo '已初核'; else echo '未初审';?></span>
					<? }?>
					&nbsp;&nbsp;&nbsp;&nbsp;   订单状态：<span class="font14h"><? echo $order_status_arr[$oinfo['OrderStatus']];?></span>
                        <?php if($oinfo['OrderSpecial'] == 'T') { ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;   订单类型：<span class="font14h">特价订单</span>
                        <?php } ?>
                    </div>
					<div class="rightdiv">下单时间：<? echo date("Y-m-d H:i",$oinfo['OrderDate']);?></div>
				</div>

				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">订单信息：</div>
					<div class="line bgw">
						<div class="line22 font12">客户信息</div>
						<div class="line22"><strong>药 店：</strong><a href="client_content.php?ID=<? echo $cinfo['ClientID'];?>" target="_blank"><? echo $cinfo['ClientCompanyName'];?>（<? echo $cinfo['ClientName'];?>）</a></div>
						<div class="line45"><strong>联 系 人：</strong><? echo $cinfo['ClientTrueName'];?></div>
						<div class="line45"><strong>联系电话：</strong><? echo $cinfo['ClientPhone'].','.$cinfo['ClientMobile'];?></div>						
						<div class="line45"><strong>最近一次下单时间：</strong><? if(!empty($cinfo['lastOrderAt'])) echo date('Y-m-d H:i',$cinfo['lastOrderAt']);?></div>
					</div>
					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line22 font12">收货信息</div>
						<div class="line45"><strong>收货人/公司：</strong><? echo $oinfo['OrderReceiveCompany'];?></div>
						<div class="line45"><strong>联 系 人：</strong><? echo $oinfo['OrderReceiveName'];?></div>
						<div class="line45"><strong>联系电话：</strong><? echo $oinfo['OrderReceivePhone'];?></div>
						<div class="line45"><strong>收货地址：</strong><? echo $oinfo['OrderReceiveAdd'];?></div>
					</div>
					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line22 font12">支付及配送方式</div>						
						<div class="line45"><strong>配送方式：</strong><? echo $senttypearr[$oinfo['OrderSendType']];?></div>
						<div class="line45"><strong>配送状态：</strong><span class="font12h"><? echo $send_status_arr[$oinfo['OrderSendStatus']];?></span>&nbsp;&nbsp;&nbsp;&nbsp; <? 
						if($oinfo['OrderSendStatus']==3) echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="show_nosend_orderproduct(\''.$oinfo['OrderID'].'\');" class="buttonb"> &#8250; 查看未发货商品 </a>';
						?></div>
						<div class="line45"><strong>支付方式：</strong><? echo $paytypearr[$oinfo['OrderPayType']];?></div>
						<div class="line45"><strong>支付状态：</strong><span class="font12h"><? echo $pay_status_arr[$oinfo['OrderPayStatus']];?> <? if($oinfo['OrderPayStatus']=="3") echo '&nbsp;&nbsp;¥ '.$oinfo['OrderIntegral'].'';?></span></div>						
					</div>
                    <?php
					   if(!empty($oinfo['DeliveryDate']) && $oinfo['DeliveryDate'] != '0000-00-00'){
    				?>
    					<br class="clearfloat" />
    					<div class="line bgw">
    						<div class="line22 font12" style="height:40px; line-height:40px;">交货时间：<?php echo $oinfo['DeliveryDate'];?></div>
    					</div>
    				<?php }?>
					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line22 font12">特殊要求说明：</div>
						<div class="line22">
							<? echo nl2br($oinfo['OrderRemark']);?>						
						</div>
					</div>
				</div>

					<br class="clearfloat" />
					<div class="border_line">
					<div class="line bgw">
						<div class="line font14">商品清单：</div>
						<div class="line">
						
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="6%" >&nbsp;行号</td>
	<td width="12%">编号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色/规格</td>
    <td width="6%" align="right">数量</td>
    <td width="5%" align="center">单位</td> 
    <td width="10%" align="right">单价</td>  
	<td width="5%" align="right">折扣</td>
    <td width="10%" align="right">折后价</td>
    <td width="12%" align="right">金额(元)&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata as $ckey=>$cvar)
	{
		$conidarr[] = $cvar['ContentID'];
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td title="包装：<? echo $cvar['Casing'];?>"><? echo $cvar['Coding'];?></td>
    <td title="包装：<? echo $cvar['Casing'];?>"><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(strlen($cvar['ContentColor']) > 0) echo $cvar['ContentColor'];?> / <?if(strlen($cvar['ContentSpecification']) > 0) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?></td>
    <td align="center" ><? echo $cvar['Units'];?></td>
	<td align="right">¥ <? echo $cvar['ContentPrice'];?> </td>
	<td align="right"><? echo $cvar['ContentPercent'];?></td> 
    <td align="right">¥ <? 
		echo $pricepencent = $cvar['ContentPrice']*$cvar['ContentPercent']/10;
	?></td>
    <td class="font12" align="right">¥ <? 
		echo $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>&nbsp;</td>
  </tr>
   <? }?>

    <?php
    $orderPure = $alltotal;//纯订单商品金额
    if($oinfo['InvoiceType'] != 'N' && !empty($oinfo['InvoiceTax'])){ ?>
  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font14">合计：</td>
	<td>&nbsp;</td>	<td>&nbsp;</td>
    <td class="font12" align="right"><? echo $allnumber;?></td>
    <td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td> 
    <td class="font14" colspan="2" align="right">
        <?php if($oinfo['OrderSpecial'] == 'T') {
            //$stair_after = sprintf("%01.2f",round($oinfo['OrderTotal'],2) / (1+ $oinfo['InvoiceTax'] / 100));
            $stair_after = sprintf("%01.2f",$oinfo['OrderTotal'] / (1+ $oinfo['InvoiceTax'] / 100));

            $stair_count = $alltotal - $stair_after;

            $alltotal = $stair_after;
            ?>
            <span> 省 ¥ <? echo $stair_count;?>&nbsp;</span><br/>
       特价 ¥ <? echo $alltotal = sprintf("%01.2f", $alltotal);?>&nbsp;

        <?php } else { ?>
            ¥ <? echo $alltotal = sprintf("%01.2f", round($alltotal,2));?>&nbsp;
        <?php } ?>
    </td>
  </tr>
        <tr>
            <td>&nbsp;</td>
            <td height="28" class="font14">税点：</td>
            <td>&nbsp;</td>	<td>&nbsp;</td>
            <td class="font12" align="right"><? echo $allnumber;?></td>
            <td class="font14">&nbsp;</td>
            <td class="font14">&nbsp;</td>
            <td class="font14" colspan="2"><?php echo $alltotal.' * '.$oinfo['InvoiceTax'].'% = ';?>&nbsp;</td>
            <td class="font14" colspan="2" align="right">
                <?php echo $alltotal * $oinfo['InvoiceTax'] / 100; ?>
            </td>
        </tr>

    <?php } ?>

    <tr>
        <td>&nbsp;</td>
        <td height="28" class="font14">合计：</td>
        <td>&nbsp;</td>	<td>&nbsp;</td>
        <td class="font12" align="right"><? echo $allnumber;?></td>
        <td class="font14">&nbsp;</td>
        <td class="font14">&nbsp;</td>
        <td class="font14">&nbsp;</td>
        <td class="font14" colspan="2" align="right">
            <?php if($oinfo['OrderSpecial'] == 'T') { ?>
                <span style="text-decoration:line-through;"> 原价 ¥ <? echo $alltotal = sprintf("%01.2f", $orderPure + $orderPure * $oinfo['InvoiceTax'] / 100);?>&nbsp;</span><br/>
                特价 ¥ <? echo $alltotal = sprintf("%01.2f", round($oinfo['OrderTotal'],2));?>&nbsp;

            <?php } else { ?>
                ¥ <? echo $alltotal = sprintf("%01.2f", round($oinfo['OrderTotal'],2));?>&nbsp;
            <?php } ?>
        </td>
    </tr>

   </tbody>
</table>
		</div>
	<?
	$cartdata_gifts = $db->get_results("select c.*,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by ID asc");
	if(!empty($cartdata_gifts))
	{
	?>
						<hr style="clear:both;" />
						<div class="line font14">赠品清单：</div>
						<div class="line">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="6%" >&nbsp;行号</td>
	<td width="12%">编号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色/规格</td>
    <td width="6%" align="right">数量</td>
	<td width="5%" align="center">单位</td>
    <td width="10%" align="right">单价</td>  
    <td width="12%" align="right">价格(元)&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata_gifts as $ckey=>$cvar)
	{
		$conidarr[] = $cvar['ContentID'];
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td ><? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(strlen($cvar['ContentColor']) > 0) echo $cvar['ContentColor'];?> / <?if(strlen($cvar['ContentSpecification']) > 0) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?></td>
	<td align="center" ><? echo $cvar['Units'];?>	</td>
	<td align="right">¥ <? echo $cvar['ContentPrice'];?> </td>
    <td class="font12" align="right">¥ <? 
		echo $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice'];
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>&nbsp;</td>
  </tr>
   <? }?> 
  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font14">合计：</td>
	<td>&nbsp;</td>	<td>&nbsp;</td>
    <td class="font12" align="right"><? echo $allnumber;?></td>
    <td class="font14">&nbsp;</td> <td class="font14">&nbsp;</td>
    <td class="font12" align="right">¥ <? echo $alltotal = sprintf("%01.2f", round($alltotal,2));?>&nbsp;</td>
  </tr>
   </tbody>
</table>
				</div>
<? }?>	
			
			<div class="line22" align="right">			
			<? 
			if(!empty($_SESSION['up']['order']['pope_form']) && $_SESSION['up']['order']['pope_form']=="Y")
			{
				if((!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on" && $oinfo['OrderSaler']=="F") || ((empty($valuearr['audit_type']) || $valuearr['audit_type']=="off") && empty($oinfo['OrderStatus'])))
				{
			?>
			<input type="button" value="修改订单商品" class="redbtn" name="confirmbtn" id="confirmbtn" onclick="javascript:window.location.href='order_product_edit.php?ID=<? echo $oinfo['OrderID'];?>'" />
			<? }else{ ?>
			<input type="button" value="修改订单商品" class="darkbtn" name="confirmbtn" id="confirmbtn"  disabled="disabled" />
			<? }}?>
			&nbsp;&nbsp;
			<input type="button" value="打印订单" class="bluebtn" name="printbtn" id="print_confirmbtn" onclick="javascript:window.open( 'print.php?u=print_order&ID=<? echo $oinfo['OrderID'];?>','_blank');" />&nbsp;&nbsp;
			<input type="button" value="导出订单" class="bluebtn" name="excelprintbtn" id="excel_confirmbtn" onclick="javascript:window.open( 'order_content_excel.php?ID=<? echo $oinfo['OrderID'];?>','exe_iframe');" />
			</div>	
						</div>
					</div>

			<?
			$cartbakinfo = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_cartbak where CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID='".$oinfo['OrderID']."' limit 0,1");
			if(!empty($cartbakinfo['allrow']))
			{
			?>
			<br class="clearfloat" />
			<div class="border_line">
			<div class="line bgw">
				<div class="line"><div class="leftdiv font14">原始订单：</div><div class="leftdiv" id="show_order_img" style="padding-left:24px; padding-top:2px; color:#277DB7; cursor: pointer;" ><img src="img/jia.gif" border="0" class="img" onclick="contral_list_order('show','<? echo $oinfo['OrderID']; ?>');" /><span onclick="contral_list_order('show','<? echo $oinfo['OrderID']; ?>');"> 展开 </span></div></div>
				<div class="line" style="display:none;" id="show_old_order_list"></div>				          
				</div>
			</div>
			<? }?>

<?
if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on" && $_SESSION['up']['inventory']['pope_view']=="Y")
{
?>
			<br class="clearfloat" />
			<div class="border_line">
			<div class="line bgw">
				<div class="line"><div class="leftdiv font14">库存状况：</div><div class="leftdiv" id="show_library" style="padding-left:24px; padding-top:2px; color:#277DB7; cursor: pointer;" ><img src="img/jia.gif" border="0" class="img" onclick="contral_list('show');" /><span onclick="contral_list('show');"> 展开 </span></div></div>
				<div class="line" style="display:none;" id="show_library_list">
						
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="5%" >&nbsp;行号</td>
	<td width="12%">&nbsp;编号/货号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色</td>
	<td width="12%">&nbsp;规格</td>
    <td width="12%" >&nbsp;包装</td>  
    <td width="8%" align="right">订购数量</td>
    <td width="8%" align="right">实际库存</td>  
    <td width="5%" align="center">单位</td>  
  </tr>
   </thead>
   <tbody>
	<? 
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
    <td>&nbsp;<?if(strlen($cvar['ContentColor']) > 0) echo $cvar['ContentColor'];?>  </td>
    <td>&nbsp;<?if(strlen($cvar['ContentSpecification']) > 0) echo $cvar['ContentSpecification'];?> </td>
    <td >&nbsp;<? echo $cvar['Casing'];?>	</td>
	<td align="right"><? echo $cvar['ContentNumber'];?> </td>
	<td align="right"><? echo intval($libarr[$kkid]);?></td> 
    <td align="center"><? echo $cvar['Units'];?></td>
  </tr>
   <? 
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
    <td >&nbsp;<? echo $cvar['Casing'];?>	</td>
	<td align="right"><? echo $cvar['ContentNumber'];?> </td>
	<td align="right"><? echo intval($libarr[$kkid]);?></td> 
    <td align="center"><? echo $cvar['Units'];?></td>
  </tr>
	<? }}?>
   </tbody>
</table>
		</div>		          
						</div>
					</div>
<? }?>
				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">订单跟踪：</div>
					<div class="line bgw">

<table width="100%" border="0" cellspacing="1" cellpadding="4" >
  <thead>
  <tr>
    <td width="16%">&nbsp;时间</td>
    <td width="24%">&nbsp;用户</td>
    <td width="20%">&nbsp;动作</td>
    <td >说明</td>
  </tr>
   </thead>
   <tbody>
	<?
		$submitdata = $db->get_results("select ID,AdminUser,Name,Date,Status,Content from ".DATATABLE."_order_ordersubmit where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by ID DESC");
		if($submitdata){
		foreach($submitdata as $ckey=>$cvar)
		{
	?>	
  <tr id="linesub_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td >&nbsp;<? echo date("Y-m-d H:i",$cvar['Date']);?> </td>
	<td>&nbsp;<? echo $cvar['AdminUser']." / ".$cvar['Name'];?></td>
    <td class="font12"><? echo $cvar['Status'];?>	</td>
	<td> <? echo $cvar['Content'];?> </td>
  </tr>
  <? }}?>
  </tbody>
  </table>
				</div>

					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line font12">操作(说明/原因)</div>
						<div class="line">
						<textarea name="data_OrderContent" rows="5"  id="data_OrderContent" style="width:80%; height:48px;"></textarea>
          				</div>
						<div class="line">
						<?php
						if(!empty($_SESSION['up']['order']['pope_audit']) && $_SESSION['up']['order']['pope_audit']=="Y")
						{
							if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on")
							{
								if($oinfo['OrderSaler']=="F" && empty($oinfo['OrderStatus']))
								{
									echo '<input type="button" value="审核订单" class="redbtn" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'Audit1\',\''.$oinfo['OrderID'].'\')"  />&nbsp;&nbsp;';
								}
							}elseif(empty($oinfo['OrderStatus'])){
								echo '<input type="button" value="审核订单" class="redbtn" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'Audit2\',\''.$oinfo['OrderID'].'\')"  />&nbsp;&nbsp;';
							}
						}
						 ?>
						&nbsp;&nbsp;<input type="button" value=" 留 言 " class="bluebtn" name="confirmbtn9" id="confirmbtn9" onclick="do_order_status('Message','<? echo $oinfo['OrderID'];?>')" />
						</div>
					</div>
			</div>
					</form>
				</div>
						<br class="clearfloat" />
				<div class="line">&nbsp;</div>
		</div>
    </div>


<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
    <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">商品库存详细</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div> 
	
<?php 
	$timsgu = (strtotime($_SESSION['uc']['EndDate'])+60*60*24);
	$starDate = (strtotime($_SESSION['uc']['BeginDate']." +1 month")+60*60*24);
	$strMsg = '使用';
	if(($timsgu - $starDate) <= 0)
		$strMsg = '试用';
	
	if(time() > $timsgu){
?>
<div style="position: absolute;top:0;bottom:0;left:0;right:0;z-index:190">
        <div class="message-dialog">
        </div>
        <div class="message-box">
            <i class="close-dialog" onclick="javascript:window.location.href='order.php'">x</i>
            <div class="message-info">
                <h4>感谢您体验订货宝系统！</h4>
                <p>您的<?php echo $strMsg;?>时间<span>已经到期</span>，升级至正式版，让您的企业立刻高效起来！订货宝已为<script src="http://m.dhb.hk/case.php?m=sjdhbcount" type="text/javascript"></script>客户解决渠道分销管理难题，加入他们的行列吧！</p>
                
                <span class="get-idea">请联系管理员升级续费</span>
            </div>

        </div>
    </div>
<?php }?>    
</body>
</html>
<?
	function make_kid($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(!strlen($product_color) && !strlen($product_spec)) return $product_id;

		if(!strlen($product_color)) $product_color  = '统一';
		if(!strlen($product_spec)) $product_spec    = '统一';

		if(strlen($product_color))
		{
		   $kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
		}
		if(strlen($product_spec))
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