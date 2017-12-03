<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");


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
<!--时间插件-->
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>


<script type="text/javascript">
	$(function(){
		$("#begindate").datepicker();
		$("#enddate").datepicker();
	});
</script>

<link rel="stylesheet" type="text/css" href="css/credit.css?v={VERID}"/>
<link rel="stylesheet" type="text/css" href="css/icon.css?v={VERID}"/>

</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="">
        		<tr>
					<td align="center" width="80">起止时间：</td>
					<td height="30" width="350">&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;<input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;<input name="enddate" type="text" id="enddate"   maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   />&nbsp;&nbsp;
					</td>
					<td width="70">
						<select id="ptype" name="ptype" >
							<option value="" >⊙ 所有类型</option>
							<option value="unrefund" <?php if($_GET['ptype'] == 'unrefund') echo 'selected="selected"'?> >┠-未还款</option>
							<option value="refunded" <?php if($_GET['ptype'] == 'refunded') echo 'selected="selected"'?>>┠-已还款</option>
							<option value="normal" <?php if($_GET['ptype'] == 'normal') echo 'selected="selected"'?>>┠-未出账</option>
						</select>
					</td>
					<td align="center" width="250" style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">
						<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:85%;" >
							<option value="" >⊙ 所有药店</option>
				<?php 
					$n = 0;
					$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
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
                    <td width="60" align="right"><input name="searchbutton" type="submit" class="mainbtn"  value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="statistics_credit.php">账期对账</a></div></td>
				</tr>
   	          </form>
			 </table>      
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
     
		<div id="credits">
                
          <form id="MainForm" name="MainForm" method="post" action="finance_excel.php" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                	<td width="6%" class="bottomlinebold">序号</td>
                	<td width="28%" class="bottomlinebold">&nbsp;药店名称</td>
                	<td width="10%" class="bottomlinebold">&nbsp;订单时间</td>
                	<td width="9%" class="bottomlinebold">记账日</td>
                  	<td width="12%" class="bottomlinebold">&nbsp;订单号</td>
					<td width="12%" class="bottomlinebold">&nbsp;金额</td>
					<td width="8%" class="bottomlinebold bankIn except">利息<i class="iconfont icon-wenhao wenhao1"></i><i class="iconfont icon-xiala-copy hide iconf1"></i></td>
                  	<td width="8%" class="bottomlinebold lateFee except">滞纳金<i class="iconfont icon-wenhao wenhao2"></i><i class="iconfont icon-xiala-copy hide iconf2"></i></td>
                  	<td width="10%" class="bottomlinebold">还款状态</td>
                </tr>
     		 </thead>
                 <?php  
                 $begindate = $_GET['begindate'];
                 $enddate = $_GET['enddate'];
                 $cid = $_GET['cid'];
                 $ptype = $_GET['ptype'];
                 if(!empty($cid)&& isset($cid)){
                     $c = " and d.ClientID =".$cid;
                 }else{
                     $c = "";
                 }
                 if(!empty($begindate)&& isset($begindate) && !empty($enddate)&& isset($enddate)){
                     $dateSel = " and left(RecordDate,10) >='".$begindate."' and left(RecordDate,10)<='".$enddate."'";              
                 }else{
                     $dateSel="";
                 }
                 if(!empty($ptype)&& isset($ptype)){
                     $CreditStatus = " and d.CreditStatus = '".$ptype."'";
                 }else{
                     $CreditStatus="";
                 }
                //                 获取对应订单
                $CompanyID = $_SESSION['uc']['CompanyID'];
                $creditDetailSql = "select d.*,c.ClientCompanyName,o.OrderSN from ".DATABASEU.DATATABLE."_credit_detail as d left join ".DATATABLE."_order_client  as c on d.ClientID = c.ClientID left join ".DATATABLE."_order_orderinfo AS o ON d.OrderID = o.OrderID where CompanyID=".$CompanyID."".$c.$dateSel.$CreditStatus." order by d.ID desc";
                $creditDetailSum = $db->query($creditDetailSql);
                $page = new ShowPage;
                $page->PageSize = 15;
                $page->Total = $creditDetailSum;
                 $page->LinkAry = array("cid"=>$in['cid'],"begindate"=>$begindate,"enddate"=>$enddate,"ptype"=>$ptype);
                $list_data = $db->get_results($creditDetailSql." ".$page->OffSet());
                 ?>
      		<tbody>
                    <?php foreach ($list_data as $k => $v) { ?>           
      			<tr class="bottomline">
      				<td class="orders">1</td>
      				<td><?php echo $v['ClientCompanyName']?></td>
      				<!--<td><?php echo date("Y-m-d",strtotime($v['RecordDate']))?></td>-->
      				<td><?php echo  date("Y-m-d",strtotime($v['PayDate']))?></td>
      				<!--<td><?php echo $v['ClientCompanyName']?></td>-->
      				<td><?php echo date("Y-m-d",strtotime($v['RecordDate']))?></td>
      				<td><?php echo $v['OrderSN']?></td>
      				<td>￥<?php echo round(MoneyFormat::MoneyOfFenToYuan($v['OrderTotal']),2); ?></td>
                                <td>￥<?php echo round(MoneyFormat::MoneyOfFenToYuan($v['Interest']),2)?></td>
                                <td>￥<?php echo round(MoneyFormat::MoneyOfFenToYuan($v['OverdueFine']),2)?></td>
      				<td><?php if($v['CreditStatus'] =='refunded' ){
                                        echo '已还款';
                                }elseif($v['CreditStatus'] =='unrefund'){
                                        echo '未还款';
                                }elseif($v['CreditStatus'] =='normal'){
                                        echo '未出账';
                                }else{ 
                                        echo '未出账';
                                }?></td>
      			</tr>
                    <?php } ?>
 		</tbody>              
              </table>

			<!--分页-->
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   
				   <td  align="right">&nbsp;</td>
     			 </tr>
     			 <tr>
       			   <td width="4%" align="center"  height="30" >&nbsp;</td>
   			       <td width="8%" >&nbsp;</td>
   			       <td class="sublink">&nbsp;</td>
				   <td  align="right"><? echo $page->ShowLink('statistics_credit.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
              
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
		    </div>
       	  </div>
        </div>
        <br style="clear:both;" />
        
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  

<script type="text/javascript">
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
    	$('.wenhao1,.wenhao2').mouseleave(function(){
    		$('.wenhao1,.wenhao2').removeClass('hover');
    		$('.sameFee').addClass('hide');
			$('.icon-xiala-copy').addClass('hide');
    	});
</script>
</body>
</html>