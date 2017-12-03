<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(empty($in['cordate'])) $in['cordate'] = date("Y-m-d");
$datemsg = str_replace("-","",$in['cordate']);

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="js/statistics.js?v=<? echo VERID;?>" type="text/javascript"></script>
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js?v=1230" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function() {
		$("#cordate").datepicker();
	});
</script>

<script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">

   	        </div>            
			<div class="location"><strong>当前位置：</strong><a href="statistics_d.php">日订单统计</a> </div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong><a href="statistics.php">时间段订单统计</a></strong></div>
<ul>
	<li>- <a href="statistics_y.php">年订单统计</a></li>	
	<li>- <a href="statistics_m.php">月订单统计</a></li>
	<li>- <a href="statistics_d.php" class="locationli">日订单统计</a></li>
</ul>

<hr style="clear:both;" />
<div ><strong><a href="statistics_d.php?cordate=<? echo $in['cordate'];?>">所有药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="statistics_d.php?cordate=<? echo $in['cordate'];?>" method="post">
				<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:218px;" >
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
				</form>
</ul>
<?php include_once ("inc/search_client.php");?>
<div>&nbsp;</div>
<hr style="clear:both;" />
<div ><strong><a href="statistics_client.php">药店订单统计</a></strong></div>
 </div>

<!-- tree -->   
       	  </div>
        	<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="statistics_d.php"  >
		  <input name="clientid" type="hidden" id="clientid"   maxlength="12" value="<? if(!empty($in['cid'])) echo $in['cid'];?>"   />
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>日订单统计</legend>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;日期:&nbsp;<input name="cordate" type="text" id="cordate"   maxlength="12" onfocus="this.select();" value="<? echo $in['cordate'];?>"   />&nbsp;
					<input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick="show_stat_data('day');"/>&nbsp;&nbsp;<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_excel('day');" /></td>
     			 </tr>
				 <? 
				 	if(!empty($in['cid'])) $sqll = " and OrderUserID=".$in['cid']." ";
					$statsql  = "SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderDate,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and left(OrderSN,8)='".$datemsg."' and OrderStatus!=8 and OrderStatus!=9 order by OrderID asc limit 0,1000";
					$statdata = $db->get_results($statsql);
					if(!empty($statdata))
					{
						foreach($statdata as $var)
						{
							if(empty($snmsg))
							{
								$snmsg = "'".$var['OrderSN']."'";
								$tmsg    = $var['OrderTotal'];
							}else{
								$snmsg .= ","."'".$var['OrderSN']."'";
								$tmsg   .= ",".$var['OrderTotal'];
							}
						}
				 ?>
     			 <tr>
       				 <td >
		<script type="text/javascript">
		
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						defaultSeriesType: 'column',
						margin: [ 50, 50, 100, 80]
					},
					title: {
						text: '<? echo $in["cordate"];?> <? if(!empty($in["cid"])) echo $clientarr[$in["cid"]];?> 订单数据'
					},
					xAxis: {
						categories: [
							<? echo $snmsg;?>
						],
						labels: {
							rotation: -45,
							align: 'right',
							style: {
								 font: 'normal 13px Verdana, sans-serif'
							}
						}
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Population (millions)'
						}
					},
					legend: {
						enabled: false
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								 '订单金额: '+ Highcharts.numberFormat(this.y, 1) +
								 ' 元';
						}
					},
				        series: [{
						name: 'Population',
						data: [<? echo $tmsg;?>],
						dataLabels: {
							enabled: true,
							rotation: -90,
							color: '#FFFFFF',
							align: 'right',
							x: -3,
							y: 10,
							formatter: function() {
								return this.y;
							},
							style: {
								font: 'normal 13px Verdana, sans-serif'
							}
						}			
					}]
				});
				
				
			});
				
		</script>
					 <div id="container" ></div>

					</td>
     			 </tr>
     			 <tr>
       				 <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong><? echo $in['cordate'];?> <? if(!empty($in['cid'])) echo $clientarr[$in['cid']];?> 订单数据</strong></td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">               
               <thead>
                <tr>

                  <td width="18%" class="bottomlinebold">订单号</td>
				  <td  class="bottomlinebold">药店</td>
                  <td width="18%" class="bottomlinebold">订单金额</td>
				  <td width="18%" class="bottomlinebold">下单时间</td>
				  <td width="12%" align="center" class="bottomlinebold">订单状态</td>
                </tr>
     		 </thead>
			 
			 <tbody>
			 <?
			$totalm = 0;	
			foreach($statdata as $var)
			{
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">  
				  <td ><a href="order_manager.php?ID=<? echo $var['OrderID'];?>" target="_blank"><? echo $var['OrderSN'];?></a></td>
				  <td ><? echo $clientarr[$var['OrderUserID']];?></td>
                  <td >¥ <? echo $var['OrderTotal'];?></td>
                  <td ><? echo date("Y-m-d H:i",$var['OrderDate']);?></td>
				  <td align="center"><? echo $order_status_arr[$var['OrderStatus']];?></td>
			 </tr>
			 <? 
				$totalm = $totalm + $var['OrderTotal'];
			 }			 
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
     
				  <td ><strong>合计：</strong></td>
				  <td ><strong>&nbsp;<? echo count($statdata);?>个</strong></td>
                  <td ><strong>¥ <? echo number_format($totalm,2,'.',',');?> 元</strong></td>
                  <td >&nbsp;</td>
				  <td >&nbsp;</td>
			 </tr>
			 </tbody>
			</table>

					 </td>
     			 </tr>
				 <? }else{?>
     			 <tr>
       				 <td height="130" bgcolor="#ffffff" align="center">&nbsp; 暂无符合条件的数据!</td>
     			 </tr>
				<? }?>
              </table>
		    </fieldset>  
			 </div>
            <br style="clear:both;" />
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>