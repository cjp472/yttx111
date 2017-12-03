<?php 
$menu_flag = "credit";
include_once ("header.php");

setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<?php 
$ClientID = $in['id'];
$CompanyID = $in['cpid'];
if(empty($ClientID) || empty($CompanyID)){
    exit('非法请求');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name='robots' content='noindex,nofollow' />

<title><? echo SITE_NAME;?> - 管理平台</title>

<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="css/jquery.treeview.css" />

<link rel="stylesheet" href="css/showpage.css" />

<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>

<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>

<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>

<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">

		$(function() {
            $("body").on('click','.blockOverlay',function(){
                $.unblockUI();
            });
			$("#tree").treeview({

				collapsed: true,

				animated: "medium",

				control:"#sidetreecontrol",

				persist: "location"

			});
			
			//$(document).delegate("body", "click", function(e) {  
				//closewindowui();
           // });

            $("#bdate").datepicker({changeMonth: true,	changeYear: true});
            $("#edate").datepicker({changeMonth: true,	changeYear: true});

		});
</script>

<link rel="stylesheet" type="text/css" href="css/credit.css"/>
</head>



<body>


     <?php include_once ("top.php");?>

	<?php include_once ("inc/son_menu_bar.php");?>   

    <div id="bodycontent">
        <!--采购频率-->
        <?php 
            $pinlvSql = "select * from ".DATABASEU.DATATABLE."_credit_apply where CompanyID=".$CompanyID." and ClientID=".$ClientID."";
            $pinlvSel = $db->get_row($pinlvSql);
        ?>
       <div class="credit_data">
       	
      
			<div class="data_rete">
				<p class="same_name">采购频率：</p>
				<div class="rate_choice">
					<div>
	                    <p class="rates odd"><span><i class ="<?php if($pinlvSel['Level'] == 1) echo 'active'; ?>"></i></span><em>2周3次以内</em></p>
						<p class="rates"><span><i class ="<?php if($pinlvSel['Level'] == 2) echo 'active'; ?>"></i></span><em>2周3次以上</em></p>
						<p class="rates odd"><span><i class ="<?php if($pinlvSel['Level'] == 3) echo 'active'; ?>"></i></span><em>1月6次以内</em></p>
						<p class="rates"><span><i class ="<?php if($pinlvSel['Level'] == 4) echo 'active'; ?>"></i></span><em>1月6次以上</em></p>
					</div>
	                            <div class="clear">其他：<?php echo $pinlvSel['LevelOther'] ?></div>
				</div>
				<div class="rate_number">
					<p>
						<span>每月采购额：</span>
	                                        <?php echo $pinlvSel['PurchaseAmount']?>
					</p>
					<p>
						<span>期望授信额：</span>
						<?php echo $pinlvSel['Amount']?>
					</p>
				</div>
			</div>
        <!--法人资料-->
        <?php 
            $MerchantSql = "select * from ".DATABASEU.DATATABLE."_three_sides_merchant where CompanyID=".$CompanyID." and MerchantID = ".$ClientID."";
            $MerchantSel = $db->get_row($MerchantSql);
            $IDCardImg = explode(',',$MerchantSel['IDCardImg']);
        ?>
			<form action="" class="data_form">
				<table class="person_data">
					<tr>
						<td rowspan="10" valign="top" class="same_name" align="center" width="10%">法人资料：</td>
						<td bgcolor="#f0f0f0" align="right" width="15%">公司名称：</td>
						<td> <?php echo $MerchantSel['BusinessName']?></td>
					</tr>
					<tr>
						
						<td bgcolor="#f0f0f0" align="right">姓名：</td>
						<td><?php echo $MerchantSel['TureUserName']?></td>
					</tr>
					<tr>
						
						<td bgcolor="#f0f0f0" align="right">手机号：</td>
						<td><?php echo $MerchantSel['UserPhone']?></td>
					</tr>
					<tr>
						
						<td bgcolor="#f0f0f0" align="right">身份证号：</td>
						<td><?php echo $MerchantSel['IDCard']?></td>
					</tr>
					<tr class="idcard">
						<td bgcolor="#f0f0f0" align="right">身份证：</td>
	                                <td>
	                           <?php foreach ($IDCardImg as $key => $value) { ?>
	                            <img src="<?php echo RESOURCE_URL.$IDCardImg[$key]?>"/>
	                            <?php }?>
	                                </td>
					</tr>
				</table>
				<table class="person_data">
					<tr>
						<td rowspan="10" valign="top" class="same_name" align="center" width="10%">认证信息：</td>
						<td bgcolor="#f0f0f0" align="right" width="15%">营业执照号码：</td>
						<td><?php echo $MerchantSel['BusinessCard']?></td>
					</tr>
	                            	<tr class="idcard">
						
						<td bgcolor="#f0f0f0" align="right">营业执照：</td>
						<td><img src="<?php echo RESOURCE_URL.$MerchantSel['BusinessCardImg']?>"/></td>
					</tr>
					<tr>
						
						<td bgcolor="#f0f0f0" align="right">药品经营许可证号码：</td>
						<td><?php echo $MerchantSel['IDLicenceCard']?></td>
					</tr>
	                            	<tr class="idcard">	
						<td bgcolor="#f0f0f0" align="right">药品经营许可证：</td>
						<td><img src="<?php echo RESOURCE_URL.$MerchantSel['IDLicenceImg']?>"/></td>
					</tr>
				</table>
			</form>
	        <!--交易流水 orderinfo表查询-->
	        <?php 
	         $bdata=isset($_GET['bdata'])?$_GET['bdata']:'';
	         $edata=isset($_GET['edata'])?$_GET['edata']:'';
	         $month=isset($_GET['month'])?$_GET['month']:'';
	         if(!empty($bdata)&&empty($edata))
	            {
	                $OrderinfoSqlnew=" and left(FROM_UNIXTIME(OrderDate),10)>='".$bdata;
	            }elseif(empty($bdata)&&!empty($edata)){
	                $OrderinfoSqlnew=" and left(FROM_UNIXTIME(OrderDate),10)<='".$edata."'";
	            }elseif(!empty($bdata)&&!empty($edata))
	            {
	                 $OrderinfoSqlnew=" and left(FROM_UNIXTIME(OrderDate),10)>='".$bdata."' and left(FROM_UNIXTIME(OrderDate),10)<='".$edata."'";
	            }
	            if($month=='month')
	            {
	                //本月
					$monthPre = date('Y-m-d');
					$monthSub = date('Y-m').'-01';	//包含当天
	                $OrderinfoSqlnew=" and left(FROM_UNIXTIME(OrderDate),10)>='".$monthSub."' and left(FROM_UNIXTIME(OrderDate),10)<='".$monthPre."'";
	            }
	         //$id=isset($_GET['id'])?$_GET['id']:'';
	            $OrderinfoSql = "select OrderID,left(FROM_UNIXTIME(OrderDate),10) as OrderDate,OrderSN,OrderTotal,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$CompanyID." and OrderUserID=".$ClientID.$OrderinfoSqlnew;
	            $OrderinfoSqlSum = "select (OrderID),left(FROM_UNIXTIME(OrderDate),10) from ".DATATABLE."_order_orderinfo where OrderCompany=".$CompanyID." and OrderUserID=".$ClientID.$OrderinfoSqlnew;
	            $Ordercount = $db->query($OrderinfoSqlSum);
	            $page = new ShowPage;
	            $page->PageSize = 20;
	            $page->Total = $Ordercount;
	            $page->LinkAry = array("kw"=>$in['kw'],"id"=>$ClientID,"cpid"=>$CompanyID);
	            $OrderinfoSel = $db->get_results($OrderinfoSql." ".$page->OffSet());
	            //定购总额
	            $OrderinfoSqlToal ="select sum(OrderTotal) AS Total,left(FROM_UNIXTIME(OrderDate),10) from ".DATATABLE."_order_orderinfo where OrderCompany=".$CompanyID." and OrderUserID=".$ClientID.$OrderinfoSqlnew;
	            $OrderinfoSelToal = $db->get_row($OrderinfoSqlToal);
	//            $OrderinfoSel = $db->get_results($OrderinfoSql);
	            $OrderinfoDate = $db->get_row($OrderinfoSql);
	        ?>
			<div class="trade_detail">
				<p class="same_name">交易流水：</p>
				<div class="details">
	                <p>首笔订单时间：<?php echo $OrderinfoDate['OrderDate']?></p>
					<p class="total">
						<span>总采购额：￥<?php echo $OrderinfoSelToal['Total']?></span>
						<span>订单：<?php echo $Ordercount?> 笔</span>
						<span>单均价：￥<?php echo round($OrderinfoSelToal['Total']/$Ordercount,2) ?></span>
					</p>				
					<div class="search_line">
	                    <p>开始时间：<input type="text" name="bdate" id="bdate" class="inputline" 
	                                   value="<?php if($_GET['month']==month){
	                                                echo $monthSub;
	                                            }
	                                    ?>"/>
	                    </p>
						<p>结束时间：<input type="text" name="edate" id="edate" class="inputline" 
	                                                       value="<?php if($_GET['month']==month){
	                                                                echo $monthPre;
	                                                                }
	                                                    ?>"/>
	                    </p>
						<a onclick='Selectmonth()'>本月</a>
	                    <input type='hidden' id='creditid' value='<?php echo $_GET['id']?>'>    
						<a onclick='Selectdata()' class="serch_button">查询</a>
					</div>
					<table border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<td width="5%">&nbsp;</td>
								<td width="25%">订单日</td>
								<td width="25%">订单号</td>
								<td width="25%">金额</td>
								<td width="15%">订单状态</td>
							</tr>
						</thead>
						<tbody>
	                                            <?php foreach ($OrderinfoSel as $k => $v) { ?>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo $v['OrderDate']?></td>
								<td><?php echo $v['OrderSN'] ?></td>
								<td><?php echo $v['OrderTotal'] ?></td>
								<td><?php
	                                                            if($v['OrderStatus']=0){
	                                                                echo'待审核';
	                                                            }elseif($v['OrderStatus']=1){
	                                                                echo'备货中';
	                                                            }elseif($v['OrderStatus']=2){
	                                                                echo'已出库';
	                                                            }elseif($v['OrderStatus']=3){
	                                                                echo'已收货';
	                                                            }elseif($v['OrderStatus']=5){
	                                                                echo'已收款';
	                                                            }elseif($v['OrderStatus']=7){
	                                                                echo'已完成';
	                                                            }elseif($v['OrderStatus']=8){
	                                                                echo'客户取消';
	                                                            }elseif($v['OrderStatus']=9){
	                                                                echo'管理员取消';
	                                                            }
	                                                            ?>
	                                                        </td>
							</tr>
	                                            <?php }?>
	                        <tr class="lasttd">
	                        	<td>&nbsp;</td>
	                            <td></td>
	                            <td></td>
	                            <td colspan="2"><?php echo $page->ShowLink('credit_data.php');?></td>
	                        </tr>
						</tbody>
					</table>
					
				</div>
			 </div>	
		</div>
    </div>
 
<?php include_once ("bottom.php");?>

<!--<iframe style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>-->
<!--<div id="windowForm" style="width:800px; height:100%; position: fixed; top: 0%; left: 25%; background-color:#fff;">
    <div class="windowHeader">
        <h3 id="windowtitle">查看图片</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent" >
		<img id="showimg" name="showimg" src="" />
    </div>
</div>-->
<script>
function Selectdata(){
 var bdata=$('#bdate').val();
 var edata=$('#edate').val();
 var creditid=$('#creditid').val();
  url=window.location.href;
  newurl=url.substr(0,url.indexOf('?'));
  //alert(url+"&bdata="+bdata+"&edata="+edata);
 window.location.href=newurl+"?bdata="+bdata+"&edata="+edata+"&id="+creditid+"&cpid="+<?php echo $in['cpid']?>;
//$.get(url+"&bdata="+bdata+"&edata="+edata,function(result){
//               // console.log(result);
//                alert(result);
//            });
}
function Selectmonth(){
     url=window.location.href;
     newurl=url.substr(0,url.indexOf('?'));
      var creditid=$('#creditid').val();
  //alert(url+"&bdata="+bdata+"&edata="+edata);
  window.location.href=newurl+"?month=month&id="+creditid+"&cpid="+<?php echo $in['cpid']?>;
}
</script>
</body>
</html>
