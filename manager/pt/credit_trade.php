<?php 
$menu_flag = "credit_client";
include_once ("header.php");
$erp_version = include_once("inc/erp_version.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);
$in['status'] = $in['status'] ? $in['status']: 'A';

$currentCompanyID = intval($in['id']);


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
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="css/credit.css"/>
<link rel="stylesheet" type="text/css" href="css/icon.css"/>
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
<style type="text/css">
		.icon-xiala-copy{
			width: 10px;
			height: 8px;
			text-align: center;
			line-height: 10px;
			font-weight: 400;
			font-size: 10px;
			color: #b7b7b7;
			
			position: absolute;
			top: 69%;
			left: 34%;
			
			background: #f3f3f3;
			border: 0;
			z-index: 1000;
		}
</style>
</head>
<body>

<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
<?php 
$id = $_GET['id'];
if(empty($id)){
    exit('非法访问');
}
?>
    <!--账期账单-->    
<?php 
if($_GET['type'] == Upmonth){
    $last_month = date('Y-m', strtotime('last month'));
    $last['first'] = $last_month . '-01';
    $last['end'] = date('Y-m-d', strtotime("$last_month +1 month -1 day +23 hours +59 minutes +59 seconds"));
    $CompanyID = $in['cpid'];
    $ClientID = $in['id'];
    $CreditDetSqlSum = "select count(*) as sum from ".DATABASEU.DATATABLE."_credit_detail where  CompanyID=".$CompanyID."  and ClientID = ".$ClientID." and Type = 'out' and  left(RecordDate,10)>='". $last['first']."' and left(RecordDate,10)<='".$last['end']."'";
    $CreditDetSql = "select a.*,b.OrderSN from ".DATABASEU.DATATABLE."_credit_detail as a left join ".DATATABLE."_order_orderinfo as b on a.OrderID = b.OrderID where  CompanyID=".$CompanyID."  and ClientID = ".$ClientID." and Type = 'out' and  left(RecordDate,10)>='". $last['first']."' and left(RecordDate,10)<='".$last['end']."'";
    $CreditDetSelSum = $db->get_row($CreditDetSqlSum);
    $CreditDetSqlaa = "select sum(OrderTotal) as sumTotal,sum(Interest) as sumOverdueFine,sum(OrderTotal+Interest+OverdueFine) as sumJin from ".DATABASEU.DATATABLE."_credit_detail where  CompanyID=".$CompanyID."  and ClientID = ".$ClientID." and Type = 'out' and  left(RecordDate,10)>='". $last['first']."' and left(RecordDate,10)<='".$last['end']."'";
    $CreditDetSelaa = $db->get_row($CreditDetSqlaa);
    $page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $CreditDetSelSum['sum'];
    $page->LinkAry = array("id"=>$ClientID,"cpid"=>$CompanyID);
    $CreditinfoSel = $db->get_results($CreditDetSql." ".$page->OffSet());
}else{
$bdata=isset($in['bdata'])?$in['bdata']:'';
$edata=isset($in['edata'])?$in['edata']:'';
$billtype=isset($in['billtype'])?$in['billtype']:'';
$month=isset($in['month'])?$in['month']:'';
if(!empty($bdata)&&empty($edata))
    {
        $OrderinfoSqlnew=" and left(RecordDate,10)>='".$bdata;
    }elseif(empty($bdata)&&!empty($edata)){
        $OrderinfoSqlnew=" and left(RecordDate,10)<='".$edata."'";
    }elseif(!empty($bdata)&&!empty($edata))
    {
         $OrderinfoSqlnew=" and left(RecordDate,10)>='".$bdata."' and left(RecordDate,10)<='".$edata."'";
    }
    if($month=='month')
        {
            //本月
            $monthPre = date('Y-m-d');
            $monthSub = date('Y-m').'-01';	//包含当天
            $OrderinfoSqlnew=" and left(RecordDate,10)>='".$monthSub."' and left(RecordDate,10)<='".$monthPre."'";
        }elseif($month=='seven')
        {
            $sevenPre = date('Y-m-d'); 
	    $sevenSub = date('Y-m-d', time() - (6 * 24 * 3600));	//包含当天
            $OrderinfoSqlnew=" and left(RecordDate,10)>='".$sevenSub."' and left(RecordDate,10)<='".$sevenPre."'";
            
        }
        if(!empty($billtype)){
            $billtypenew=" and Type='".$billtype."'";
        }
        $CompanyID = $in['cpid'];
        $ClientID = $in['id'];
        $CreditDetSqlSun = "select count(*) as sum,RecordDate from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$CompanyID." and ClientID = ".$ClientID.$OrderinfoSqlnew.$billtypenew;
        $CreditDetSel = $db->get_row($CreditDetSqlSun);
        $CreditDetSql = "select a.*, b.OrderSN from ".DATABASEU.DATATABLE."_credit_detail as a left join ".DATATABLE."_order_orderinfo as b on a.OrderID = b.OrderID where CompanyID=".$CompanyID." and ClientID = ".$ClientID.$OrderinfoSqlnew.$billtypenew;
        $CreditDetSqlaa = "select sum(OrderTotal) as sumTotal,sum(Interest) as sumOverdueFine,sum(OrderTotal+Interest+OverdueFine) as sumJin,sum(OverdueFine) as sumInterest  from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$CompanyID." and ClientID = ".$ClientID.$OrderinfoSqlnew.$billtypenew." And Type='out'";
        $CreditDetSqlbb = "select sum(OrderTotal) as sumTotal,sum(Interest) as sumOverdueFine,sum(OrderTotal+Interest+OverdueFine) as sumJin,sum(OverdueFine) as sumInterest  from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$CompanyID." and ClientID = ".$ClientID.$OrderinfoSqlnew.$billtypenew." And Type='int'";
        $CreditDetSelaa = $db->get_row($CreditDetSqlaa);
        $CreditDetSelbb = $db->get_row($CreditDetSqlbb);
        $page = new ShowPage;
        $page->PageSize = 50;
        $page->Total = $CreditDetSel['sum'];
        $page->LinkAry = array("id"=>$ClientID,"cpid"=>$CompanyID);
        $CreditinfoSel = $db->get_results($CreditDetSql." ".$page->OffSet());
}
?>
    <div id="bodycontent">
     
    	<div class="lineblank"></div>
    	<div class="credit_trade">
			<div class="data_search">
	                    <input type="hidden" id="creditid" value="<?php echo $_GET['id']?>" />
				<div class="data_search_time">
	                                    <p>开始时间：<input type="text" name="bdate" id="bdate" class="inputline"
	                                                   value="<?php if($_GET['month']==month){
	                                                            echo $monthSub;
	                                                        }elseif ($_GET['month'] ==seven) {
	                                                            echo $sevenSub;
	                                                        }elseif($_GET['type'] == Upmonth){
	                                                            echo $last['first'];
	                                                        }else{
	                                                            echo $_GET['bdata'];
	                                                        } ?>
	                                                   "/></p>
	                                    <p>结束时间：<input type="text" name="edate" id="edate" class="inputline"
	                                                    value="<?php if($_GET['month']==month){
	                                                            echo $monthPre;
	                                                        }elseif ($_GET['month'] ==seven) {
	                                                            echo $sevenPre;
	                                                        }elseif($_GET['type'] == Upmonth){
	                                                            echo $last['end'];
	                                                        }else{
	                                                            echo $_GET['edata'];
	                                                        } ?>
	                                                   "/></p>
				</div>
				<div class="data_search_con">
	                            <span><a id="seven-days" onclick="Selectseven()">7天</a></span>
	                            <span><a id="current-month" onclick="Selectmonth()">本月</a></span>
					<div>
	                                    <select name="billtype" id="billtype">
	                                                    <option value="">类型</option>
	                                                    <option value="out" <?php if($in['billtype'] == out) { echo "selected=selected";}?>>支出</option>
	                                                    <option value="in"<?php if($in['billtype'] == in) { echo "selected=selected";}?>>退款</option>
	                                     </select>
					</div>
	                            <span class="search"><a onclick="Selecttrade()">查询</a></span>
				</div>
			</div>
			<!--分页位置-->
	      	
			<table class="data_table">
				<thead>
					<tr>
						<td width="5%">序号</td>
						<td width="13%">记账日</td>
						<td width="13%">订单号</td>
						<td width="13%">金额(元)</td>
						<td width="13%" class="bankIn">利息<i class="iconfont icon-wenhao wenhao1"></i><i class="iconfont icon-xiala-copy iconf1 hide"></td>
	                    <td width="13%" class="lateFee">滞纳金<i class="iconfont icon-wenhao wenhao2"></i><i class="iconfont icon-xiala-copy iconf2 hide"></td>
						<td width="13%" class="accouts">结算金额<i class="iconfont icon-wenhao wenhao3"></i><i class="iconfont icon-xiala-copy iconf3 hide"></td>
	                    <td width="14%">交易描述</td>
					</tr>
				</thead>
				<tbody>
	                            <?php foreach ($CreditinfoSel as $k => $v) { ?>
					<tr>
						<td class="orders">1</td>
						<td><?php 
	                                        echo $CUR_DATE = date("Y-m-d",strtotime($v['RecordDate'] )); 
	                                        ?></td>
						<td><?php echo $v['OrderSN'] ?></td>
	                                        <td><?php 
	                                        if($v['Type']=='int') {
	                                            echo '-';
	                                        }  
	                                        echo  MoneyFormat::MoneyOfFenToYuan($v['OrderTotal'])
	                                        ?></td>
	                                        <td><?php echo MoneyFormat::MoneyOfFenToYuan(round($v['Interest'],2)) ?></td>
	                                        <td><?php echo MoneyFormat::MoneyOfFenToYuan(round($v['OverdueFine'],2)) ?></td>
	                                        <td><?php echo MoneyFormat::MoneyOfFenToYuan(round($v['OrderTotal']+ $v['Interest']+$v['OverdueFine'],2))?></td>
	                                        <td style="text-align: left;"><?php echo $v['DescribeContent'] ?></td>
	
					</tr>
	                            <?php } ?>
	                            <tr class="lasttd">
	                                   
	                                    <td style="text-align: right" colspan="3">小计</td>
	                                    <td><?php echo MoneyFormat::MoneyOfFenToYuan($CreditDetSelaa['sumTotal'])-MoneyFormat::MoneyOfFenToYuan($CreditDetSelbb['sumTotal'])?></td>
	                                    <td><?php echo MoneyFormat::MoneyOfFenToYuan(round($CreditDetSelaa['sumOverdueFine'],2))?></td>
	                                    <td><?php echo MoneyFormat::MoneyOfFenToYuan(round($CreditDetSelaa['sumInterest'],2))?></td>
	                                    <td><?php echo MoneyFormat::MoneyOfFenToYuan(round($CreditDetSelaa['sumJin'])-MoneyFormat::MoneyOfFenToYuan($CreditDetSelbb['sumJin']),2)?></td>
	                            </tr>
				</tbody>
	                    <tr class="lasttd">
	                    <td colspan="10"><?php echo $page->ShowLink('credit_trade.php');?></td>
	                </tr>
			</table>
	
			<!--利息说明位置-->
			<div class="popBank sameFee hide">
				<p>1.医统账期为您提供安全、方便的信用支付，首月免息。为您的药品采购提供资金保障，年化利息仅<b>18%</b>；</p>
				<p>2.计算公式：<b>本金*(18%/12)*N(N：代表还款自然月)</b>；</p>
				<p>3.具体解释权归医统天下公司所有；</p>
				<p>4.如有疑问请联系医统天下公司；</p>
			</div>
			<div class="popFee sameFee hide">
				<p>1.若您在三个自然月内未还清款项，医统按日息<b>5‱ </b>收取滞纳金(复利)，建议您及时还款；</p>
				<p>2.计算公式：<b>本金*(18%/12)*3(收取滞纳金前的应还款月数)+滞纳金*逾期天数</b>；</p>
				<p>3.如有疑问请联系医统天下公司；</p>
			</div>
			<div class="popAccount sameFee hide">
				<p>1.计算公式：<b>本金+利息+滞纳金</b>；</p>
				<p>2.如有疑问请联系医统天下公司；</p>
			</div>
		</div>
	</div>
 
<?php include_once ("bottom.php");?>
<script>
	//序号
	for(var i = 0;i<$('.orders').length;i++){
		$('.orders:eq('+i+')').html(i+1);		
	}
	//点击出现利息、滞纳金说明
	function intro(node1,node2,node3){
		var eleNode1 = $('.'+node1);
		var eleNode2 = $('.'+node2);
		var eleNode3 = $('.'+node3);
		
		$('.sameFee').addClass('hide');
		$('.icon-xiala-copy').addClass('hide');
		eleNode3.addClass('hover');
		if(eleNode1.hasClass('hide')){
			eleNode1.removeClass('hide');
			eleNode2.removeClass('hide');
		}else{
			eleNode1.addClass('hide');
			eleNode2.removeClass('hide');
		};
	};
	$('.wenhao1').mouseenter(function(){
		intro('popBank','iconf1','wenhao1');
	});
	$('.wenhao2').mouseenter(function(){
		intro('popFee','iconf2','wenhao2');
	});
	$('.wenhao3').mouseenter(function(){
		intro('popAccount','iconf3','wenhao3');
	});
	$('.wenhao1,.wenhao2,.wenhao3').mouseleave(function(){
		$('.wenhao1,.wenhao2,.wenhao3').removeClass('hover');
		$('.sameFee').addClass('hide');
		$('.icon-xiala-copy').addClass('hide');
	});
  	function Selecttrade(){
		var aa=$('#bdate').val();
		var bdata = $.trim(aa);
		var bb = $('#edate').val();
		var edata=$.trim(bb);
		var creditid=$('#creditid').val();
		var billtype=$('#billtype').val();
		url=window.location.href;
		newurl=url.substr(0,url.indexOf('?'));
		//alert(url+"&bdata="+bdata+"&edata="+edata);
		 window.location.href=newurl+"?bdata="+bdata+"&edata="+edata+"&id="+creditid+"&billtype="+billtype+"&cpid="+<?php echo $in['cpid']?>;
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

function Selectseven(){
     url=window.location.href;
     newurl=url.substr(0,url.indexOf('?'));
      var creditid=$('#creditid').val();
  //alert(url+"&bdata="+bdata+"&edata="+edata);
  window.location.href=newurl+"?month=seven&id="+creditid+"&cpid="+<?php echo $in['cpid']?>;
}
</script>
</body>
</html>
