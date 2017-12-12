<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
$oinfo = array();
	if(!empty($in['ID']))
	{
		$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
	}elseif(!empty($in['SN'])){
		$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$in['SN']."' limit 0,1");
	}else{
		exit('错误参数!');
	}

    $cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd,ClientGUID FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

    //wangd 2017-11-28 判断是否为代理商，代理商只能看到自己所管辖商品
    $user_flag = trim($_SESSION['uinfo']['userflag']);
    if ($user_flag == '2')
    {
        $sql1 = "select * from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." and AgentID=".$_SESSION['uinfo']['userid']." order by SiteID asc, BrandID asc, ID asc";
    }
    else //管理员和商业公司可以看到所有订单
    {
        $sql1 = "select * from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by SiteID asc, BrandID asc, ID asc";
    }
	
	$cartdata = $db->get_results($sql1);

	$cidmsg = '';
	$valuearr = get_set_arr('product');
    $erparr = get_set_arr('erp');
    $erparr['erp_interface']    = $erparr['erp_interface'] ? $erparr['erp_interface'] : 'N';
    $erparr['erp_order_check']  = $erparr['erp_order_check'] ? $erparr['erp_order_check'] : 'N';
	setcookie("backurl", $_SERVER['REQUEST_URI']);

$allow_special = false;

if(is_allow_access($menu_flag,array('pope_view','pope_form','pope_audit')) && in_array($oinfo['OrderStatus'],array(0,1))) {
    //待审核&备货中　允许指定特价
    $allow_special = true;
}

$erp_is_run = erp_is_run($db,$_SESSION['uinfo']['ucompany']);
$erp_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_api_serial WHERE CompanyID=" . $_SESSION['uinfo']['ucompany']);

$hasNotErp = false; //是否包含未在ERP中的商品

//查询最近一次易极付交易流水
//todo 这个页面需要显示易极付交易流水号，目前只显示了一个，但是客户可能提交N个支付请求，但最后只有一个成功交易的
//所以这里的临时方案：先查询付款单里面是否存在，不存在则查找交易流水表
$yjfPayInfo = $db->get_row("SELECT FinancePaysn as PayNO FROM ".DATATABLE."_order_finance WHERE FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceClient=".$oinfo['OrderUserID']." and FinanceOrder='".$oinfo['OrderSN'].",' limit 1 ");
if(empty($yjfPayInfo)){
	$yjfPayInfo = $db->get_row("SELECT PayNO FROM ".DATABASEU.DATATABLE."_neypay_map WHERE CompanyID=" . $_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." and DHBOrderNO='".$oinfo['OrderSN'].",' order by Mid desc limit 1");
}

$intCNT = $db->get_row("select COUNT(*) AS CNT from ".DATATABLE."_order_print_log where  LogContent=".$oinfo['OrderID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." and LogType ='order' ");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="../../shop/plugin/layer/layer.js" type="text/javascript"></script>
<script src="js/order.js?v=2<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
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
            padding-top: 60px;
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
        .up-to{
            display: block;
            width: 180px;
            height: 42px;
            line-height: 40px;
            background:#FF4A00 ;
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            float: left;
            cursor:pointer;
        }
        .get-idea{
            display: block;
            width: 180px;
            height: 40px;
            line-height: 40px;
            border: 1px solid #C0C0C0;
            color: #959595;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            float: left;
            margin-left: 10px;
            cursor:pointer;
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

		
		
        .mask {
            background: rgba(0, 0, 0, .7);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
        }
        .logan {
            width: 600px;
            height: 400px;
            margin: 100px auto 200px;
            background-color: #fff;
        }
        li {list-style-type:none;}
        .piaochecked {
            width: 20px;
            height: 20px;
            float: left;
            margin-top: 2%;
            cursor: pointer;
            margin-left: 10px;
            text-align: center;
            background-image: url(./img/checkbox_01.png);
            background-repeat: no-repeat;
            background-position: 0 0;
        }

        .on_check {
            background-position: 0 -21px;
        }
        .radioclass {
            opacity: 0;
            cursor: pointer;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
            filter: alpha(opacity=0);
        }
    </style>
	
<script type="text/javascript">
	$(function(){
		$("#bdate").datepicker({changeMonth: true,	changeYear: true});
		$("#edate").datepicker({changeMonth: true,	changeYear: true});
		
		
		$(".piaochecked").on("click",function(){
			$(".piaochecked").removeClass("on_check");
			$(this).addClass("on_check");
			var val=$(this).find("span").html();
			//alert(val);
			if(val == '其他'){
				$("#replycontent").removeAttr("readonly");
			}else{
				$("#replycontent").val("");
				$("#replycontent").attr("readonly","readonly");
			}
		})
		
		$("#mask_back").click(function(){
			$(".mask").css("display","none");
		});

		
		
	});

    function setSendFlag(cid,el)
    {
        if(confirm('确定货物已签收吗?'))
        {
            $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
            $.post("do_consignment.php",
                {m:"setSendFlag", ConsignmentID: cid},
                function(data){
                    data = Jtrim(data);
                    if(data == "ok"){
                        $.blockUI({ message: "<p>设置成功!</p>" });
                        $(el).remove();
                        //$(el).parent().prev("td").html('<font color="green">已签收</a>');
                        $("#consignment_" + cid).html('<font color="green">已签收</font>');
                    }else{
                        $.blockUI({ message: "<p>"+data+"</p>" });
                    }
                }
            );
            window.setTimeout($.unblockUI, 1000);
        }else{
            return false;
        }
        window.setTimeout($.unblockUI, 1000);
    }

    //未支付完成不能审核订单的提示
    function unapprove_order(){

    	layer.open({
    		title : '信息提示', 
    		type: 0, 
    		content: '<p style="font-size:14px;">当前订单未支付完成，不能进行审核操作!</p>' //这里content是一个普通的String
    	});

    	return false;
    }

</script>
</head>

<body>

<?php include_once ("top.php");?>

<?php include_once ("inc/son_menu_bar.php");?>

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
					<div class="line font14">订单号：<span class="font14h"><? echo $oinfo['OrderSN'];?> <? if($oinfo['OrderType']=="M") echo "(管理员代下单)"; elseif($oinfo['OrderType']=="S") echo "(客情官代下单)";?></span>
					<? if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on"){?>&nbsp;&nbsp;&nbsp;&nbsp;   初审状态：<span class="red"><? if($oinfo['OrderSaler']=="T") echo '已初核'; else echo '未初审';?></span><? }?>
					&nbsp;&nbsp;&nbsp;&nbsp;   订单状态：<span class="font14h"><? echo $order_status_arr[$oinfo['OrderStatus']];?></span>
                        <?php if($oinfo['OrderSpecial'] == "T") { ?>
                            <span style="margin-left:50px;"></span>订单类型：<span class="font14h">特价订单</span>
                        <?php } ?>
                    </div>
                    <div>
                    <?php if($yjfPayInfo['PayNO']){?>
                    <span style="font-size:13px;line-height:25px;">快捷支付交易流水号：<?php echo $yjfPayInfo['PayNO'];?></span>
					<?php }?>
					<div class="rightdiv">下单时间：<? echo date("Y-m-d H:i",$oinfo['OrderDate']);?>   <span style="color:#666; padding-left:12px;">
					<?php
					$recentinfo = $db->get_row("SELECT OrderID,OrderDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$oinfo['OrderUserID']." and OrderID < ".$oinfo['OrderID']." order by OrderID desc limit 0,1");
					if(!empty($recentinfo['OrderDate'])){
						echo '(上次下单时间：'.date('Y-m-d H:i',$recentinfo['OrderDate']).')';
					}
					?>
					</span></div>
					</div>
				</div>

				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14">订单信息：</div>
					<div class="line bgw">
						<div class="line22 font12">客户信息</div>
						<div class="line22"><strong>经 销 商：</strong><a href="client_content.php?ID=<? echo $cinfo['ClientID'];?>" target="_blank"><? echo $cinfo['ClientCompanyName'];?>（<? echo $cinfo['ClientName'];?>）</a></div>
						<div class="line45"><strong>联 系 人：</strong><? echo $cinfo['ClientTrueName'];?></div>
						<div class="line45"><strong>联系电话：</strong><? echo $cinfo['ClientPhone'].','.$cinfo['ClientMobile'];?></div>

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
						<div class="line45"><strong>配送状态：</strong><span class="font12h"><? echo $send_status_arr[$oinfo['OrderSendStatus']];?></span>&nbsp;&nbsp;&nbsp;&nbsp; <?php
						if($oinfo['OrderSendStatus'] == 1 || $oinfo['OrderSendStatus']==3)
						{
							$paytypeidarr = array('1','2','3','7');

							if($oinfo['OrderPayStatus'] < 2 && in_array($oinfo['OrderPayType'],$paytypeidarr))
							{

							}else{
								echo '<a href="consignment_add.php?ID='.$oinfo['OrderID'].'" class="buttonb"> &#8250; 开票出库 </a>';
							}
						}
						?>

						</div>
						<div class="line45"><strong>支付方式：</strong><? echo $paytypearr[$oinfo['OrderPayType']];?></div>
						<div class="line45"><strong>支付状态：</strong><span class="font12h"><? echo $pay_status_arr[$oinfo['OrderPayStatus']];?> <? if($oinfo['OrderPayStatus']=="3") echo '&nbsp;&nbsp;¥ '.$oinfo['OrderIntegral'].'';?></span>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php
						if($oinfo['OrderStatus']!="8" && $oinfo['OrderStatus']!="9"){
							if($oinfo['OrderPayStatus']=="0" || $oinfo['OrderPayStatus']=="3") echo '<a href="finance_add.php?oid='.$oinfo['OrderID'].'" class="buttonb"> &#8250; 添加收款单 </a>';
						}
						?>
						</div>
					</div>
				<?php
					if($oinfo['InvoiceType'] == "P" || $oinfo['InvoiceType'] == "Z"){
						$sql_i = "select InvoiceID,InvoiceType,AccountName,BankName,BankAccount,InvoiceHeader,InvocieContent,TaxpayerNumber,InvoiceDate,InvoiceFlag,InvoiceSendDate from ".DATATABLE."_order_invoice where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by InvoiceID desc";
						$invoice	= $db->get_row($sql_i);
				?>
					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line22 font12">开票信息</div>
						<div class="line45">
							<strong>开票类型：</strong><span class="font12h"><? if($oinfo['InvoiceType'] == "Z") echo '增值税发票'; else echo '普通发票'; ?></span><br />
							<strong>开票抬头：</strong><?php echo $invoice['InvoiceHeader'];?><br />
							<strong>开票内容：</strong><?php echo $invoice['InvocieContent'];?><br />
							<strong>开票状态：</strong>
							<span id="show_invoice_div"><?php if($invoice['InvoiceFlag'] == 'T'){ echo '<font color="green">已开票 时间：'.date("Y-m-d",$invoice['InvoiceSendDate'])."</font>"; }else{ echo '<font color=red>未开票</font>';?>
							&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="已开票" class="bluebtn" name="invoicebtn" id="invoicebtn" onclick="set_invoice('<?php echo $invoice['InvoiceID'];?>');" />
							<?php }?>
							</span>
						</div>
						<div class="line45">
						    <?php
						    	if($oinfo['InvoiceType'] == "Z"){
						    ?>
							<strong>纳税人识别号：</strong><?php echo $invoice['TaxpayerNumber']; ?><br />
							<strong>开户名称：</strong><?php echo $invoice['AccountName']; ?><br />
							<strong>开户银行：</strong><?php echo $invoice['BankName']; ?><br />
							<strong>银行帐号：</strong><?php echo $invoice['BankAccount']; ?>
							<?php }?>
						</div>
					</div>
				<?php }?>
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
					<a id="list" name="list"></a>
                    <!--<input type="button" class="bluebtn" onclick="window.location.href='order_edit_base.php?ID=<?php echo $in['ID']; ?>';" value="修改基本信息" style="margin-left:1040px;margin-top:-40px;" />-->
				</div>

					<br class="clearfloat" />
					<div class="jf_menu">
						<ul id="jf_menu_id">
							<li ><a href="javascript:void(0)" class="jf_menu_hover" id="hover1" onclick="show_content('show_list','product_list','hover1','<? echo $oinfo['OrderID'];?>')" >商品清单</a></li>
							<?php
							if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){
							?>
							<li><a href="javascript:void(0)" id="hover2" onclick="show_content('show_list','libary_list','hover2','<? echo $oinfo['OrderID'];?>')">库存状况</a></li>
							<?php }?>
							<li><a href="javascript:void(0)" id="hover3" onclick="show_content('show_list','send_list','hover3','<? echo $oinfo['OrderID'];?>')">发货明细</a></li>
							<li><a href="javascript:void(0)" id="hover4" onclick="show_content('show_list','finance_list','hover4','<? echo $oinfo['OrderID'];?>')">收款记录</a></li>

							<li><a href="javascript:void(0)" id="hover5" onclick="show_content('show_list','old_list','hover5','<? echo $oinfo['OrderID'];?>')">原始订单</a></li>
						</ul>
					</div>

					<div class="border_line" id="show_list">
					<div class="line">
						<div class="line bgw">

  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="5%" >&nbsp;行号</td>
    <td>&nbsp;商品名称</td>
<!--     <td width="18%">&nbsp;生产厂家</td> -->
<!--     <td width="10%">&nbsp;规格</td> -->
    <td width="21%">&nbsp;品牌</td>
    <td width="10%">&nbsp;药品规格</td>
    <td width="10%" style="display: none">&nbsp;颜色/规格</td>
    <td width="6%" align="right">数量</td>
    <td width="4%" align="right">单位&nbsp;</td>
    <td width="7%" align="right">单价</td>
	<td width="5%" align="right">折扣</td>
    <td width="6%" align="right">折后价</td>
    <td width="10%" align="right">金额(元)&nbsp;</td>
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
	foreach($cartdata as $ckey=>$cvar)
	{
        if($cvar['ERP'] == 'F' || empty($cvar['ERP'])) {
            $hasNotErp = true;
        }
		$conidarr[] = $cvar['ContentID'];
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
    <td title="包装：<? echo $cvar['Casing'];?>"><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
	<td ><? echo $brandsqlarr[$cvar['BrandID']]['BrandName'];?></td>
	<td ><? echo $cvar['Model'];?></td>
    <td style="display: none">&nbsp;
        <? if(strlen($cvar['ContentColor']) > 0) echo $cvar['ContentColor'];?> 
        / 
        <? if(strlen($cvar['ContentSpecification']) > 0) echo $cvar['ContentSpecification'];?> 
    </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>	</td>
    <td align="right" ><? echo $cvar['Units'];?>&nbsp;</td>
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
$orderPure = $alltotal;//纯商品金额
if($oinfo['InvoiceType'] != 'N' && !empty($oinfo['InvoiceTax'])){
?>
  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font14">合计：</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
    <td class="font12" align="right"></td>
    <td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
    <td class="font12h" align="right">
        <!--¥ <?/* echo sprintf("%01.2f", round($alltotal,2));*/?>&nbsp;-->

        <?php
        if($oinfo['OrderSpecial'] == 'T') {

            $stair_after = sprintf("%01.2f",$oinfo['OrderTotal'] / (1+ $oinfo['InvoiceTax'] / 100));

            $stair_count = $alltotal - $stair_after;
            $alltotal = $stair_after;
            echo '<span>省 ¥ '.$stair_count.'</span>';
            echo '<br/> ¥ ' . sprintf("%01.2f",round($stair_after,2));
        } else {
            $alltotal_bottom = sprintf("%01.2f", round($alltotal,2));
            echo '¥ ' . $alltotal_bottom;
        }
        ?>
    </td>
  </tr>
  <tr style="background-color:#f9f9f9;">
    <td>&nbsp;</td>
    <td height="28" class="font14">税点：</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
    <td class="font12" align="right"></td>
    <td class="font14">&nbsp;</td>
	<td class="font12" colspan="3" align="right"><?php echo $alltotal.' * '.$oinfo['InvoiceTax'].'% = ';?>&nbsp;</td>
    <td class="font12" align="right">¥ <?
    $taxtotal = $alltotal*$oinfo['InvoiceTax']/100;
    $alltotal = $alltotal + $taxtotal;
    echo sprintf("%01.2f", round($taxtotal,2));
    ?>&nbsp;</td>
  </tr>
<?php }?>

  <tr>
    <td>&nbsp;</td>
    <td height="28" class="font14">总计：</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
    <td class="font12" align="right"><? echo $allnumber;?></td>
    <td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
	<td class="font14">&nbsp;</td>
    <td class="font12h" align="right">
        <?php
        if($oinfo['OrderSpecial'] == 'T') {

            $initTotal = $orderPure + $orderPure * $oinfo['InvoiceTax'] / 100;
            echo '<span style="text-decoration:line-through;">原价 ¥ '.$initTotal.'</span>';
            echo '<br/>特价 ¥ ' . sprintf("%01.2f",round($oinfo['OrderTotal'],2));
        } else {
            $alltotal_bottom = sprintf("%01.2f", round($alltotal,2));
            echo '¥ ' . $alltotal_bottom;
        }
        ?>
    </td>
  </tr>
   </tbody>
</table>
		</div>
	<?
	$cartdata_gifts = $db->get_results("select * from ".DATATABLE."_view_index_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by SiteID asc,ID asc");

	if(!empty($cartdata_gifts))
	{
	?>
<hr style="clear:both;" />
<div class="line font14">赠品清单：</div>
<div class="line bgw">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr>
    <td width="6%" >&nbsp;行号</td>
	<td width="12%">编号</td>
    <td>&nbsp;商品名称</td>
    <td width="16%">&nbsp;颜色/规格</td>
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
        if($cvar['ERP'] == 'F' || empty($cvar['ERP'])) {
            $hasNotErp = true;
        }
		$conidarr[] = $cvar['ContentID'];
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>" <? if(fmod($ckey,2)==0) echo 'style="background-color:#f9f9f9;"'; else echo 'style="background-color:#ffffff;"';?>  >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td ><? echo $cvar['Coding'];?></td>
    <td ><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<?if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?> / <?if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td align="right" ><? echo $cvar['ContentNumber'];?>	</td>
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
	<td>&nbsp;</td>
    <td height="28" class="font14">合计：</td>
	<td>&nbsp;</td>
    <td class="font12" align="right"><? echo $allnumber;?></td>
    <td class="font14">&nbsp;</td><td class="font14">&nbsp;</td>
    <td class="font12" align="right">
        ¥ <? echo $alltotal = sprintf("%01.2f", round($alltotal,2));?>&nbsp;
    </td>
  </tr>
   </tbody>
</table>
</div>
<? }?>

		<div class="line22" align="right">
            <?php if($allow_special) { ?>
            <input type="button" class="redbtn" onclick='setting_special_form(<?php echo json_encode($oinfo); ?>);' value="设置特价" style="float:left;"/>
            <?php } ?>

			<? if($valuearr['checkandapprove']=='on'){ ?>
				<? if($oinfo['OrderSendStatus']>1 && $oinfo['OrderStatus']<7 && ($erp_is_run && $oinfo['OrderApi']=='F' || !$erp_is_run)){ ?>
                    <!--
                    <?php if($oinfo['OrderSpecial'] == 'T') { ?>
					<input type="button" value="订单核准" class="redbtn" name="printbtn2" id="excel_confirmbtn2" onclick="if(confirm('系统提示：订单核准会将特价订单更新为普通订单!确定去操作吗?')) window.location.href='order_product_checkapprove.php?ID=<? echo $oinfo['OrderID'];?>'" />
                    <?php } else { ?>
                        <input type="button" value="订单核准" class="redbtn" name="printbtn2" id="excel_confirmbtn2" onclick="javascript:window.location.href='order_product_checkapprove.php?ID=<? echo $oinfo['OrderID'];?>'" />
                    <?php } ?>
                     -->
				<? }else{ ?>
					<!-- <input type="button" value="订单核准" class="darkbtn" name="printbtn2" id="excel_confirmbtn2" disabled="disabled"  />  -->
				<? }?>
			<? }?>
			<? 
			    //if(empty($oinfo['OrderStatus']) && ($erp_is_run && $oinfo['OrderApi']=='F' || !$erp_is_run)){ 
			    //zjb 订单审核 ->erp对接->调整订单（去掉没有库存的商品）->已发货（计算提成）
// 			    if(($oinfo['OrderStatus']<2 && ($erp_is_run && $oinfo['OrderApi']=='T' || !$erp_is_run)) || $oinfo['OrderStatus']<2) {
			    if(($erp_is_run && $oinfo['OrderSendStatus']!=2) || !$erp_is_run) {
			?>
			<input type="button" value="修改订单商品" class="redbtn" name="confirmbtn" id="confirmbtn" onclick="javascript:window.location.href='order_product_edit.php?ID=<? echo $oinfo['OrderID'];?>'" />
			<? }else{ ?>
			<input type="button" value="修改订单商品" class="darkbtn" name="confirmbtn" id="confirmbtn"  disabled="disabled" />
			<? }?>
		
			<!-- 
			<? if( ((empty($oinfo['OrderStatus']) &&  in_array($_SESSION['uinfo']['ucompany'],array(133,309))) || ($oinfo['OrderStatus'] < 2 && !in_array($_SESSION['uinfo']['ucompany'],array(133,309)))) && ($erparr['erp_interface']=='Y' && $oinfo['OrderApi']=='F' || $erparr['erp_interface']=='N')){ ?>
			&nbsp;&nbsp;
			<input type="button" value=" 赠品管理 " class="bluebtn" name="gifts_confirmbtn" id="gifts_confirmbtn" onclick="javascript:window.location.href='order_gifts_product.php?ID=<? echo $oinfo['OrderID'];?>'" />
			<? }else{?>
			<input type="button" value=" 赠品管理 " class="darkbtn" name="gifts_confirmbtn" id="gifts_confirmbtn"  disabled="disabled" />
			<? }?>
			&nbsp;&nbsp;
			 -->
			<input type="button" title="已打印 <?php echo intval($intCNT['CNT']);?> 次" value="打印订单<?php if(intval($intCNT['CNT']) > 0) echo "(".$intCNT['CNT'].")";?>" class="bluebtn" name="printbtn" id="print_confirmbtn" onclick="javascript:window.open( 'print.php?u=print_order&ID=<? echo $oinfo['OrderID'];?>','_blank');" />&nbsp;&nbsp;
			<input type="button" value="导出订单" class="bluebtn" name="excelprintbtn" id="excel_confirmbtn" onclick="javascript:window.open( 'order_content_excel.php?ID=<? echo $oinfo['OrderID'];?>','exe_iframe');" />

			<input type="button" value="原始顺序导出订单" class="bluebtn" name="excelprintbtn2" id="excel_confirmbtn2" onclick="javascript:window.open( 'order_content_excel.php?ty=order&ID=<? echo $oinfo['OrderID'];?>','exe_iframe');" />
		</div>
		</div>
	</div>
	
	<div style="display: none;">
	<?php 
// 		print_r($erp_is_run);
// 		echo '<br />';
// 		print_r($oinfo);
// 		echo '<br />';
	?>
	</div>

				<br class="clearfloat" />
				<div class="border_line">
					<div class="line font14"  style="background-color:#FFFEF5;">订单跟踪：</div>
					<div class="line bgw">

<table width="100%" border="0" cellspacing="1" cellpadding="4" >
  <thead>
  <tr>
    <td width="14%">&nbsp;时间</td>
    <td width="20%">&nbsp;用户</td>
    <td width="16%">&nbsp;动作</td>
    <td >说明</td>
	<td width="6%" align="center">操作</td>
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
	<td align="center"><?php if($cvar['Status']=='留言'){?> [<a href="javascript:void(0)" onclick="delete_guestbook('<?php echo $cvar['ID'];?>')">删除</a>] <? }?></td>
  </tr>
  <? }}?>
  </tbody>
  </table>
				</div>

					<br class="clearfloat" />
					<div class="line bgw">
						<div class="line font12">操作(说明/原因)</div>
						<div class="line">
						<textarea name="data_OrderContent" rows="5"  id="data_OrderContent" style="width:99%; height:100px;"></textarea>
          				</div>
						<div class="line" style="margin-top: 10px;">
						<?php
                            if($erp_is_run){
                                //启用了ERP接口
                                if($erp_info['TransferCheck'] == 'T' && $oinfo['OrderApi']=='F' && empty($oinfo['OrderStatus'])){
									
                                    if($hasNotErp) {
                                        $ext = '
					<input type="button" value="审核订单" class="greenbtn" name="confirmbtn1" id="confirmbtn1" onclick="$.blockUI({message : \'<p>当前订单中包含未同步的商品,请先在ERP中维护并同步!</p>\'});return false;" '.$disablemsg1.'  />&nbsp;&nbsp;';
                                    } else {
                                        $ext = '
					<input type="button" value="审核订单" class="greenbtn" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'Audit\',\''.$in['ID'].'\')" '.$disablemsg1.'  />&nbsp;&nbsp;';
                                    }
									
                                    //wangdan 2017-12-08 支付方式为货到付款或者已经支付时，可以审核订单
									if($oinfo['OrderPayStatus']!=2 && $oinfo['OrderPayType']!=8){
										$ext = '
					<input type="button" value="审核订单" class="greenbtn" name="confirmbtn1" id="confirmbtn1" onclick="unapprove_order()" '.$disablemsg1.'  />&nbsp;&nbsp;';
									}

                                    echo $ext;
                                }

                                if($oinfo['OrderApi'] == 'F' && $oinfo['OrderStatus'] == 0) {
                                    //没有传输到ERP并且是待审核状态 允许取消
                                    echo '<input type="button" value="取消订单" class="redbtn" name="confirmbtn0" id="confirmbtn0"  onclick="do_order_status(\'Cancel\',\''.$in['ID'].'\')" '.$disablemsg1.'  />';
                                }

                                if($oinfo['OrderApi'] == 'F' && in_array($oinfo['OrderStatus'],array(8,9))) {
                                    if($_SESSION['uinfo']['userflag']=="9")
                                    {
                                        echo '<input type="button" value="删除订单" class="redbtn" name="confirmbtn7" id="confirmbtn7" onclick="do_order_status(\'Delete\',\''.$in['ID'].'\')" />&nbsp;&nbsp;';
                                    }else{
                                        echo '<input type="button" value="删除订单" class="darkbtn" name="confirmbtn7" id="confirmbtn7" onclick="do_order_status(\'Delete\',\''.$in['ID'].'\')" disabled="disabled" />&nbsp;&nbsp;';
                                    }
                                }

                                //取消、删除
                            }else{
                                if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on")
                                {
                                    if($oinfo['OrderSaler']=="F" && empty($oinfo['OrderStatus']))
                                    {
                                        echo '<strong>待客情官初审订单</strong>';
                                        echo '&nbsp;&nbsp;<input type="button" value="取消订单" class="greenbtn" name="confirmbtn0" id="confirmbtn0"  onclick="do_order_status(\'Cancel\',\''.$oinfo['OrderID'].'\')" />';
                                    }else{
                                        echo OrderStatus($oinfo['OrderStatus'],$oinfo['OrderID'],$oinfo['OrderSendStatus'],$oinfo['OrderPayStatus']);
                                    }
                                }else{
                                    echo OrderStatus($oinfo['OrderStatus'],$oinfo['OrderID'],$oinfo['OrderSendStatus'],$oinfo['OrderPayStatus']);
                                }
                            }

						?>
						&nbsp;&nbsp;<input type="button" value=" 留 言 " class="bluebtn" name="confirmbtn9" id="confirmbtn9" onclick="do_order_status('Message','<? echo $oinfo['OrderID'];?>')" />
						</div>
						<div class="line font12" style="margin:8px;margin-left: 0px;">此单金额：
                            <?php if($oinfo['OrderSpecial'] == 'T') { ?>
                                <span class="font12h" style="text-decoration:line-through;">原价 ¥ <?php echo number_format($alltotal,2); ?></span>
                                <span class="font12h">¥ <? echo $alltotal_bottom =  number_format($oinfo['OrderTotal'],2);?>&nbsp;&nbsp;</span>
                            <?php } else { ?>
                                <span class="font12h">¥ <? echo $alltotal_bottom =  number_format($alltotal_bottom,2);?>&nbsp;&nbsp;</span>
                            <?php } ?>
                            ，期末应收：<span class="font12h">¥ <?php $ye = get_client_money($db,$oinfo['OrderUserID']);
						echo $ye = number_format($ye, 2);
						?></span></div>
					</div>
				</div>
					</form>
				</div>
				<br class="clearfloat" />
		<div class="line">&nbsp;</div>
		</div>
	</div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">查看未发货订单商品</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"> </div>
	</div>

    <div id="windowFormShort" style="width:500px;display:none;">
        <div class="windowHeader" style="width:500px;">
            <h3 class="windowtitle" style="width:300px;">订单子信息</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div class="windowContent" style="background:#fff;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <thead>
                <tr class="bottomline">
                    <th>单号</th>
                    <th>时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="dan_content">

                </tbody>
            </table>
        </div>
    </div>

<div id="windowFormSpecial" style="width:500px;display:none;">
    <div class="windowHeader" style="width:500px;">
        <h3 class="windowtitle" style="width:300px;">指定特价订单</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div class="windowContent" style="background:#fff;">
        <form id="special_form" action="do_order.php">
            <input name="m" type="hidden" value="Special"/>
            <input name="order_id" type="hidden" value="" />
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tbody id="dan_content">
                <tr style="height:40px;line-height:40px;">
                    <td align="right">订单号：</td>
                    <td data-order-no align="left"></td>
                </tr>
                <tr style="height:40px;line-height:40px;">
                    <td align="right">原单金额：</td>
                    <td data-order-total align="left"></td>
                </tr>
                <tr style="height:40px;line-height:40px;">
                    <td align="right">特价金额：</td>
                    <td align="left"><input type="text" name="amount" id=""/></td>
                </tr>
                <tr style="height:40px;line-height:40px;">
                    <td></td>
                    <td align="left">
                        <input type="button" class="redbtn" value=" 提 交 " onclick="setting_special();"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<div id="old_show_list"><script type="text/javascript">$("#old_show_list").html($("#show_list").html());</script></div>
<?php 
	$timsgu = (time()+60*60*24);
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
                <h4>感谢您体验医统天下系统！</h4>
                <p>您的<?php echo $strMsg;?>时间<span>已经到期</span>，升级至正式版，让您的企业立刻高效起来！医统天下已为<script src="http://m.dhb.hk/case.php?m=sjdhbcount" type="text/javascript"></script>客户解决渠道分销管理难题，加入他们的行列吧！</p>
                <span onclick="javascript:window.location.href = 'http://m.dhb.hk/pro/buy_product.php'" class="up-to">升级至正式版</span>
                <span onclick="$('.slide-feedback').click();" class="get-idea">给医统天下提点意见</span>
            </div>

        </div>
    </div>
<?php }?>  

<div class="mask">
    <div class="logan">
    <p style="font-size: 20px;padding-left: 20px;height: 50px;line-height:50px;background-color: #f8f8f8;">取消订单<span id="mask_back" style="float:right;font-size: 20px;margin-right: 10px;line-height: 50px;cursor: pointer;">X</span></p>
        <div class="logan_content" style="width: 100%; height: 60%;">
            <p style="padding-left: 33px;">请选择取消订单的原因，以便客户进行查看</p>
            <ul style="    height: 100%;padding-left: 5%;">
                <li>
                    <div class="piaochecked" style="width: 30%;">
                        <input name="need_inv" type="radio" style="" class="radioclass input" value="1">
                        <span style=" margin-left: -63%;">缺货</span>
                    </div>
                </li>
                <li>
                    <div class="piaochecked " style="width: 28%;">
                        <input name="need_inv" type="radio" style="" class="radioclass input" value="1">
                        <span style=" margin-left: -30%;">客户要求取消</span>
                    </div>
                </li>
                <li>
                    <div class="piaochecked" style="width: 33%;">
                        <input name="need_inv" type="radio" style="" class="radioclass input" value="1">
                        <span style=" margin-left: -15%;">预付款订单未完成付款</span>
                    </div>
                </li>
                <li>
                    <div class="piaochecked" style="width: 30%;">
                        <input name="need_inv" type="radio" style="" class="radioclass input" value="1">
                        <span style=" margin-left: -15%;">未达到最低起订数量</span>
                    </div>
                </li>
                <li>
                    <div class="piaochecked" style="width: 28%;">
                        <input name="need_inv" type="radio" style="" class="radioclass input" value="1">
                        <span style=" margin-left: -43%;">连锁专供</span>
                    </div>
                </li>
                <li>
                    <div class="piaochecked " style="width: 30%;">
                        <input name="need_inv" type="radio" style="" class="radioclass input" value="1">
                        <span style=" margin-left: -48%;">医院专供</span>
                    </div>
                </li>
				<li>
                    <div class="piaochecked " style="width: 30%;" >
                        <input name="need_inv" type="radio" style="" class="radioclass input" value="1">
                        <span style=" margin-left: -63%;">其他</span>
                    </div>
                </li>


                <div class="message">
                    <textarea id="replycontent" name="replycontent" style="width:530px;height:90px;margin-left: 0%;margin-top: 2%;border: 1px solid #239a56" placeholder="其他原因："></textarea>
                </div>
            </ul>

        </div>

	<div class="mask_bottom" style="float: right;margin-right: 6%;width: 25%;height: 20%;">
	<button style="border:1px solid #f18a38;background-color:#f4f4f4;width:38%;height:32%;border-radius:6px;margin-left:17%;cursor:pointer;float: right;" onclick="do_cancel_orders(<?php echo $oinfo['OrderID'];?>,'Cancel')">确认</button>


	</div>
    </div>
</div>
  
</body>
</html>
<?php
function OrderStatus($ostatus,$oid,$sstatus,$pstatus)
{
    global $oinfo;
	$ext = "";

	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['order']['pope_audit'] != 'Y')
	{
		$disablemsg1 = 'disabled="disabled"';
		$classmsg1	 = 'darkbtn';
	}else{
		$disablemsg1 = '';
		$classmsg1	 = 'redbtn';
	}

	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['consignment']['pope_audit'] != 'Y')
	{
		$disablemsg2 = 'disabled="disabled"';
		$disablemsg4 = 'disabled="disabled"';
		$classmsg2	 = 'darkbtn';
		$classmsg4	 = 'darkbtn';
	}else{
		if($sstatus == "2"){
			$disablemsg2 = 'disabled="disabled"';
			$disablemsg4 = '';
			$classmsg2    = 'darkbtn';
			$classmsg4    = 'greenbtn';
		}elseif($sstatus == "3"){
			$disablemsg2 = 'disabled="disabled"';
			$disablemsg4 = 'disabled="disabled"';
			$classmsg2    = 'darkbtn';
			$classmsg4    = 'darkbtn';
		}elseif($sstatus == "4"){
			$disablemsg2 = 'disabled="disabled"';
			$disablemsg4 = 'disabled="disabled"';
			$classmsg2    = 'darkbtn';
			$classmsg4    = 'darkbtn';
		}else{
			$disablemsg2 = '';
			$disablemsg4 = 'disabled="disabled"';
			$classmsg2    = 'greenbtn';
			$classmsg4    = 'darkbtn';
		}
	}

	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['finance']['pope_audit'] != 'Y')
	{
		$disablemsg3 = 'disabled="disabled"';
		$classmsg3	  = 'darkbtn';
	}else{
		if($pstatus == "2")
		{
			$disablemsg3 = 'disabled="disabled"';
			$classmsg3	 = 'darkbtn';
		}else{
			$classmsg3	 = 'bluebtn';
			$disablemsg3 = '';
		}
	}

    $allow_done = get_order_done($oid);
	if($allow_done && $_SESSION['uinfo']['userflag']=="9" || ($_SESSION['up']['order']['pope_audit'] == 'Y' && $_SESSION['up']['consignment']['pope_audit'] == 'Y' && $_SESSION['up']['finance']['pope_audit'] == 'Y'))
	{
			$classmsg6	  = 'redbtn';
			$disablemsg6 = '';
	}else{
			$classmsg6	  = 'darkbtn';
			$disablemsg6 = 'disabled="disabled"';
	}
	if($_SESSION['uinfo']['userflag']=="9")
	{
			$classmsg7	  = 'redbtn';
			$disablemsg7 = '';
	}else{
			$classmsg7	  = 'darkbtn';
			$disablemsg7 = 'disabled="disabled"';
	}

	 switch($ostatus)
	 {
		case 0:
		{
			
            //wangdan 2017-11-29 支付方式为货到付款或者已经支付时，可以审核订单
			if($pstatus==2 || $oinfo['OrderPayType']==8){
				$ext = '
					<input type="button" value="审核订单" class="'.$classmsg1.'" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'Audit\',\''.$oid.'\')" '.$disablemsg1.'  />&nbsp;&nbsp;
					<input type="button" value="取消订单" class="'.$classmsg1.'" name="confirmbtn0" id="confirmbtn0"  onclick="do_order_status(\'Cancel\',\''.$oid.'\')" '.$disablemsg1.'  />';
			}else{
				$ext = '
					<input type="button" value="审核订单" class="'.$classmsg1.'" name="confirmbtn1" id="confirmbtn1" onclick="unapprove_order()" '.$disablemsg1.'  />&nbsp;&nbsp;
					<input type="button" value="取消订单" class="'.$classmsg1.'" name="confirmbtn0" id="confirmbtn0"  onclick="do_order_status(\'Cancel\',\''.$oid.'\')" '.$disablemsg1.'  />';
			}
			
			
			break;
		}
		case 1:
		{
			$ext = '
						<!-- <input type="button" value="反审核订单" class="'.$classmsg1.'" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'UnAudit\',\''.$oid.'\')" '.$disablemsg1.' />&nbsp;&nbsp;-->
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick=\'do_order_status("Send","'.$oid.','.json_encode($oinfo).'")\' '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick=\'do_order_status("Incept","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="'.$classmsg3.'" name="confirmbtn3" id="confirmbtn3"  onclick=\'do_order_status("Pay","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg3.' />&nbsp;&nbsp;
						';
            $ext = '
						<!-- <input type="button" value="反审核订单" class="'.$classmsg1.'" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'UnAudit\',\''.$oid.'\')" '.$disablemsg1.' />&nbsp;&nbsp; -->
						<input type="button" value="已到帐" class="'.$classmsg3.'" name="confirmbtn3" id="confirmbtn3"  onclick=\'do_order_status("Pay","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg3.' />&nbsp;&nbsp;
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick=\'do_order_status("Send","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick=\'do_order_status("Incept","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg4.' />&nbsp;&nbsp;

						';
			break;
		}
		case 2:
		{
			$ext = '
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick=\'do_order_status("Send","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick=\'do_order_status("Incept","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="'.$classmsg3.'" name="confirmbtn3" id="confirmbtn3" onclick=\'do_order_status("Pay","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg3.' />&nbsp;&nbsp;
						<input type="button" value="已完结" class="'.$classmsg6.'" name="confirmbtn6" id="confirmbtn6" onclick="do_order_status(\'Over\',\''.$oid.'\')" '.$disablemsg6.' />';
			break;
		}
		case 3:
		{
			$ext = '
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick=\'do_order_status("Send","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick=\'do_order_status("Incept","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="'.$classmsg3.'" name="confirmbtn3" id="confirmbtn3" onclick=\'do_order_status("Pay","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg3.' />&nbsp;&nbsp;
						<input type="button" value="已完结" class="'.$classmsg6.'" name="confirmbtn6" id="confirmbtn6" onclick="do_order_status(\'Over\',\''.$oid.'\')" '.$disablemsg6.' />';
			break;
		}
		case 5:
		{
			$ext = '
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick=\'do_order_status("Send","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick=\'do_order_status("Incept","'.$oid.'",'.json_encode($oinfo).')\' '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="darkbtn" name="confirmbtn3" id="confirmbtn3"  onclick=\'do_order_status("Pay","'.$oid.'",'.json_encode($oinfo).')\' disabled="disabled" />&nbsp;&nbsp;
						<input type="button" value="已完结" class="'.$classmsg6.'" name="confirmbtn6" id="confirmbtn6" onclick="do_order_status(\'Over\',\''.$oid.'\')" '.$disablemsg6.' />';
			break;
		}
		case 8:
		{
			$ext = '<input type="button" value="删除订单" class="'.$classmsg7.'" name="confirmbtn7" id="confirmbtn7" onclick="do_order_status(\'Delete\',\''.$oid.'\')" '.$disablemsg7.'  />&nbsp;&nbsp;
						';
			break;
		}
		case 9:
		{
			$ext = '<input type="button" value="删除订单" class="'.$classmsg7.'" name="confirmbtn7" id="confirmbtn7" onclick="do_order_status(\'Delete\',\''.$oid.'\')" '.$disablemsg7.'  />&nbsp;&nbsp;
						';
			break;
		}
		default:
			$ext = "&nbsp;&nbsp;";
			break;
	}
	return $ext;
}


    /**
     * 获取订单是否流程已走完 (已全款且已确认到账,已发完货且已确认收货)
     * @param $orderID
     * @return bool
     */
    function get_order_done($orderID) {
        global $db;
        $oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($orderID)." limit 0,1");
        $pay_one = $oinfo['OrderIntegral'] >= $oinfo['OrderTotal'] && $oinfo['OrderPayStatus'] == 2;
        $finance = $db->get_var("SELECT count(*) as Total FROM ".DATATABLE."_order_finance WHERE FinanceCompany=".$_SESSION['uinfo']['ucompany']." AND INSTR(FinanceOrder,'{$oinfo['OrderSN']}') AND FinanceFlag<>2");
        $pay_done = $pay_one && $finance == 0;//付款完成且都已到账
        $consignment = $db->get_var("SELECT count(*) as Total FROM ".DATATABLE."_order_consignment WHERE ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." AND ConsignmentOrder='{$oinfo['OrderSN']}' AND ConsignmentFlag=0");
        $cart = $db->get_var("SELECT COUNT(*) as total FROM ".DATATABLE."_order_cart WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." AND OrderID={$oinfo['OrderID']} AND ContentNumber > ContentSend");
        $sh_done = $consignment == 0 && $cart == 0;

        return $pay_done && $sh_done;
    }

	//应收款
	function get_client_money($db,$cid)
	{
		$cid       =  intval($cid);
		$sqlunion  = " and FinanceClient = ".$cid." ";
		$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
		$statdata2 = $db->get_row($statsql2);

		$sqlunion  = " and ClientID = ".$cid." ";
		$statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and FlagID = '2' ";
		$statdata4 = $db->get_row($statsql4);

		$sqlunion  = " and OrderUserID   = ".$cid." ";
// 		$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 ";
		$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and OrderStatus!=8 and OrderStatus!=9 ";
		$statdatat = $db->get_row($statsqlt);

		$sqlunion   = " and ReturnClient  = ".$cid." ";
		$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and (ReturnStatus=3 or ReturnStatus=5) ";
		$statdata1  = $db->get_row($statsqlt1);

		$begintotal = $statdatat['Ftotal'] - $statdata2['Ftotal'] - $statdata1['Ftotal'] - $statdata4['Ftotal'];

		return $begintotal;
	}
?>