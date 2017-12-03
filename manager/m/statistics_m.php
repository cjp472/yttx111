<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(empty($in['y'])) $in['y']   = date("Y");
if(empty($in['m'])) $in['m'] = date("m");
$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="js/statistics.js?v=<? echo VERID;?>" type="text/javascript"></script>

<script src="../scripts/jquery.min.js?v=1230" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
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
            
			<div class="location"><strong>当前位置：</strong> <a href="statistics_m.php">月订单统计</a> <? if(!empty($in['cid'])) echo '&#8250;&#8250; <a href="#">'.$clientarr[$in['cid']].'</a>';?></div>           
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
	<li>- <a href="statistics_m.php" class="locationli">月订单统计</a></li>
	<li>- <a href="statistics_d.php" >日订单统计</a></li>
</ul>

<hr style="clear:both;" />
<div ><strong><a href="statistics_m.php?y=<? echo $in['y'];?>&m=<? echo $in['m']; ?>">所有药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="statistics_m.php?y=<? echo $in['y'];?>&m=<? echo $in['m']; ?>" method="post">
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
<?php //include_once ("inc/search_client.php");?>
<div>&nbsp;</div>
<hr style="clear:both;" />
<div ><strong><a href="statistics_client.php">药店订单统计</a></strong></div>
 </div>

<!-- tree -->   
       	  </div>
        	<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="statistics_m.php"  >
		  <input name="clientid" type="hidden" id="clientid"   maxlength="12" value="<? if(!empty($in['cid'])) echo $in['cid'];?>"   />
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>月订单统计</legend>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;日期:&nbsp;<select name="y" id="y" >
						<? 
						for($y=2010;$y<2025;$y++)
						{
							if($in['y']==$y)
							{
								echo '<option value="'.$y.'" selected="selected">'.$y.'</option>';
							}else{
								echo '<option value="'.$y.'" >'.$y.'</option>';
							}
						}
						?>
					</select>&nbsp;年 &nbsp;<select name="m" id="m" >
						<? 
						for($m=1;$m<13;$m++)
						{
							if($in['m']==$m)
							{
								echo '<option value="'.$m.'" selected="selected">'.$m.'</option>';
							}else{
								echo '<option value="'.$m.'" >'.$m.'</option>';
							}
						}
						?>
					</select>&nbsp;月&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick="show_stat_data('month')"/>&nbsp;&nbsp;<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_excel('month');" /></td>
     			 </tr>
				 <? 
				 	if(!empty($in['cid'])) $sqll = " and OrderUserID=".$in['cid']." ";
					$statsql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['m']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus!=8 and OrderStatus!=9 group by left(OrderSN,8)";
					$statdata = $db->get_results($statsql);
					foreach($statdata as  $rvar)
					{
						$carr[] = "'".$rvar['ODate']."'";
						$tarr[] = $rvar['OTotal'];
						$narr[] = $rvar['totalnumber'];
					}
					$statsql0  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and MONTH(FROM_UNIXTIME(OrderDate))=".$in['m']." and YEAR(FROM_UNIXTIME(OrderDate))=".$in['y']." and OrderStatus=0 group by left(OrderSN,8)";
					$rdata = $db->get_results($statsql0);

					$totalnumber0  = 0;
					$totalprice0   = 0;
					foreach($rdata as $rvar)
					{
						$rarr[$rvar['ODate']] = $rvar['totalnumber'];
						$totalnumber0 = $totalnumber0 + $rvar['totalnumber'];
						$totalprice0 = $totalprice0 + $rvar['OTotal'];
					}

					if(!empty($statdata))
					{
						$carrmsg = implode(",",$carr);
						$tarrmsg  = implode(",",$tarr);
						$narrmsg = implode(",",$narr);
				 ?>
     			 <tr>
       				 <td >
		<script type="text/javascript">
		
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						zoomType: 'xy'
					},
					title: {
						text: "<? echo $in['y']." 年 ".$in['m']." 月 ";?> <? if(!empty($in['cid'])) echo $clientarr[$in['cid']];?> 订单数据"
					},
					subtitle: {
						text: ''
					},
					xAxis: [{
						categories: [<? echo $carrmsg;?>]
					}],
					yAxis: [{ // Primary yAxis
						labels: {
							formatter: function() {
								return this.value +'个';
							},
							style: {
								color: '#89A54E'
							}
						},
						title: {
							text: '订单数',
							style: {
								color: '#89A54E'
							}
						}
					}, { // Secondary yAxis
						title: {
							text: '订单金额',
							style: {
								color: '#4572A7'
							}
						},
						labels: {
							formatter: function() {
								return this.value +' 元';
							},
							style: {
								color: '#4572A7'
							}
						},
						opposite: true
					}],
					tooltip: {
						formatter: function() {
							return ''+
								this.x +': '+ this.y +
								(this.series.name == '订单金额' ? ' 元' : ' 个');
						}
					},
					legend: {
						layout: 'vertical',
						align: 'left',
						x: 120,
						verticalAlign: 'top',
						y: 100,
						floating: true,
						backgroundColor: '#FFFFFF'
					},
					series: [{
						name: '订单金额',
						color: '#4572A7',
						type: 'spline',
						yAxis: 1,
						data: [<? echo $tarrmsg;?>]
					}, {
						name: '订单数',
						color: '#89A54E',
						type: 'column',
						data: [<? echo $narrmsg;?>]
					}]
				});		
			});				
		</script>
					 <div id="container" ></div>

					</td>
     			 </tr>

     			 <tr>
       				 <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong><? echo $in['y']." 年 ".$in['m']." 月 ";?> <? if(!empty($in['cid'])) echo $clientarr[$in['cid']];?> 订单数据</strong></td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">               
               <thead>
                <tr>
                  <td width="2%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="25%" class="bottomlinebold">日期</td>
				  <td width="25%" class="bottomlinebold">订单金额</td>
                  <td  class="bottomlinebold">总订单数</td>
				  <td width="25%" class="bottomlinebold">未审核订单</td>
                </tr>
     		 </thead>
			 
			 <tbody>
			 <?
				$totalm = 0;
				$totaln = 0;
				foreach($statdata as $var)
				{
					$totalm = $totalm + $var['OTotal'];
					$totaln = $totaln + $var['totalnumber'];
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
				  <td ><a href="javascript:void(0);" onclick="show_order_list('day','<? echo $var['ODate'];?>','<? if(!empty($in['cid'])) echo $in['cid'];?>')"><? echo $var['ODate'];?></a></td>
				  <td >¥ <? echo $var['OTotal'];?></td>
                  <td ><? echo $var['totalnumber'];?></td>
				  <td ><? if(empty($rarr[$var['ODate']])) echo '0'; else echo $rarr[$var['ODate']];?></td>
			 </tr>
			 <? 
				 }			 
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
				  <td ><strong>合计：</strong></td>
				  <td ><strong>¥ <? echo number_format($totalm,2,'.',',');?> 元</strong></td>
                  <td ><strong> <? echo $totaln;?> 个</strong></td>
				  <td ><strong><? echo $totalnumber0.'个 (¥ '.number_format($totalprice0,2,'.',',').')';?></strong></td>
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
    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">订单列表：</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContentList" >
        数据载入中...       
        </div>
	</div>
</body>
</html>