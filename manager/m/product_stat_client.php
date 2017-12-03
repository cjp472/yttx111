<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$productinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID=".intval($in['ID'])." limit 0,1");
}
if(empty($productinfo['ID'])) exit('此商品不存在，或已经删除!');

$sortarr = $db->get_results("SELECT ClientID,ClientCompanyName FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ORDER BY ClientID ASC ");
$n = 0;
foreach($sortarr as $areavar)
{
	$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>
</head>

<body>      
    <div id="bodycontent">
    	
        <div class="line2"></div>
        <div class="bline" >   
			<fieldset  class="fieldsetstyle">
			<legend>商品统计：</legend>
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;</td>
     			 </tr>
				 <? 
					$statsql  = "SELECT sum(c.ContentNumber) as cnum,sum(ContentSend) as snum,c.ClientID from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ClientID Order by cnum desc";
					$statdata = $db->get_results($statsql);

					$statsqlg  = "SELECT sum(c.ContentNumber) as cnum,sum(ContentSend) as snum,c.ClientID from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and o.OrderStatus!=8 and o.OrderStatus!=9 group by c.ClientID Order by cnum desc";
					$statdatag = $db->get_results($statsqlg);

					$statsqlr  = "SELECT sum(c.ContentNumber) as rnum,c.ClientID from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ContentID=".$in['ID']." and FROM_UNIXTIME(o.ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (o.ReturnStatus=2 or o.ReturnStatus=3 or o.ReturnStatus=5) group by c.ClientID Order by rnum desc";
					$rdata = $db->get_results($statsqlr);

					if(!empty($statdatag))
					{
						foreach($statdatag as $rvar)
						{
							$garr[$rvar['ClientID']] = $rvar['cnum'];
							$garrs[$rvar['ClientID']] = $rvar['snum'];
						}
					}

					if(!empty($rdata))
					{
						foreach($rdata as $rvar)
						{
							$rarr[$rvar['ClientID']] = $rvar['rnum'];
						}
					}

					if(!empty($statdata) || !empty($statdatag))
					{
						if(!empty($statdata))
						{
							foreach($statdata as $var)
							{
								if(empty($rarr[$var['ClientID']])) $rarr[$var['ClientID']] = 0;
								if(empty($garr[$var['ClientID']])) $garr[$var['ClientID']] = 0;
								$var['cha'] = $var['cnum'] + $garr[$var['ClientID']] - $rarr[$var['ClientID']];
								if(empty($cmsg))
								{
									$cmsg = "'".$clientarr[$var['ClientID']]."'";
									$pmsg = $var['cha'];
									$rmsg = $rarr[$var['ClientID']];
								}else{
									$cmsg .= ",'".$clientarr[$var['ClientID']]."'";
									$pmsg .= ",".$var['cha'];
									$rmsg  .= ",".$rarr[$var['ClientID']];
								}
							}
						}else{
							foreach($statdatag as $var)
							{
								$var['cnum'] = 0;
								if(empty($rarr[$var['ClientID']])) $rarr[$var['ClientID']] = 0;
								if(empty($garr[$var['ClientID']])) $garr[$var['ClientID']] = 0;
								$var['cha'] = $var['cnum'] + $garr[$var['ClientID']] - $rarr[$var['ClientID']];
								if(empty($cmsg))
								{
									$cmsg = "'".$clientarr[$var['ClientID']]."'";
									$pmsg = $var['cha'];
									$rmsg = $rarr[$var['ClientID']];
								}else{
									$cmsg .= ",'".$clientarr[$var['ClientID']]."'";
									$pmsg .= ",".$var['cha'];
									$rmsg  .= ",".$rarr[$var['ClientID']];
								}
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
						defaultSeriesType: 'column'
					},
					title: {
						text: '（<? echo $productinfo['Name'];?>） 订购数据 - 药店'
					},
					xAxis: {
						categories: [<? echo $cmsg;?>]
					},
					yAxis: {
						min: 0,
						title: {
							text: '订单个数'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
							}
						}
					},
					legend: {
						align: 'right',
						x: -100,
						verticalAlign: 'top',
						y: 20,
						floating: true,
						backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
						borderColor: '#CCC',
						borderWidth: 1,
						shadow: false
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								 this.series.name +': '+ this.y +'<br/>'+
								 '共: '+ this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							dataLabels: {
								enabled: true,
								color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
							}
						}
					},
				    series: [{
						name: '订购赠送数',
						data: [<? echo $pmsg;?>]
					}, {
						name: '退货数',
						data: [<? echo $rmsg;?>]
					}]
				});	
			});				
		</script>
					<div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>
					</td>
     			 </tr>

     			 <tr>
       				 <td height="30" bgcolor="#efefef" > <strong>（<? echo $productinfo['Name'];?>） 订购数据 - 药店</strong></td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">               
               <thead>
                <tr>
                  <td width="10%" class="bottomlinebold">序号</td>
				  <td  class="bottomlinebold">药店</td>
				  <td width="30%" class="bottomlinebold">商品</td>
                  <td width="10%" class="bottomlinebold">订购数</td>
				  <td width="10%" class="bottomlinebold">赠送数</td>
				  <td width="10%" class="bottomlinebold">退货数</td>
				  <td width="10%" class="bottomlinebold">实际数</td>
				  <td width="10%" class="bottomlinebold">发货数</td>
                </tr>
     		 </thead>
			 
			 <tbody>
			 <?php
				$totalm = $totalr = $totalg = 0;
				$n = 1;
				if(!empty($statdata))
				{
					foreach($statdata as $var)
					{
						if(empty($rarr[$var['ClientID']])) $rarr[$var['ClientID']] = 0;
						if(empty($garr[$var['ClientID']])) $garr[$var['ClientID']] = 0;
						if(empty($garrs[$var['ClientID']])) $garrs[$var['ClientID']] = 0;
						$var['snum'] = $var['snum'] + $garrs[$var['ClientID']];
						$totalm = $totalm + $var['cnum'];
						$totalg = $totalg + $garr[$var['ClientID']];
						$totalr = $totalr + $rarr[$var['ClientID']];
						$totals = $totals + $var['snum'];

			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				   <td>&nbsp;<? echo $n++;?></td>
				  <td >&nbsp;<? echo $clientarr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $productinfo['Name'];?></td>
                  <td >&nbsp;<? echo $var['cnum'];?></td>
				  <td >&nbsp;<? echo $garr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $rarr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $var['cnum']+$garr[$var['ClientID']]-$rarr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $var['snum'];?></td>
			 </tr>
			 <?php 
					}
				}else{
					foreach($statdatag as $var)
					{
						$var['cnum'] = 0;
						if(empty($rarr[$var['ClientID']])) $rarr[$var['ClientID']] = 0;
						if(empty($garr[$var['ClientID']])) $garr[$var['ClientID']] = 0;
						$totalm = $totalm + $var['cnum'];
						$totalg = $totalg + $garr[$var['ClientID']];
						$totalr = $totalr + $rarr[$var['ClientID']];
			?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				   <td>&nbsp;<? echo $n++;?></td>
				  <td >&nbsp;<? echo $clientarr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $productinfo['Name'];?></td>
                  <td >&nbsp;<? echo $var['cnum'];?></td>
				  <td >&nbsp;<? echo $garr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $rarr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $var['cnum']+$garr[$var['ClientID']]-$rarr[$var['ClientID']];?></td>
				  <td >&nbsp;<? echo $var['snum'];?></td>
			 </tr>
			<?php
					}
				}
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><strong>合计：</strong></td>
				  <td>&nbsp;</td>
				  <td ><strong>&nbsp;</strong></td>
                  <td ><strong> <? echo $totalm;?> 件</strong></td>
				  <td ><strong> <? echo $totalg;?> 件</strong></td>
				  <td ><strong> <? echo $totalr;?> 件</strong></td>
				  <td ><strong> <? echo $totalm+$totalg-$totalr;?> 件</strong></td>
				  <td ><strong> <? echo $totals;?> 件</strong></td>
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
			<br style="clear:both;" />
            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close(true);">关 闭 </a></li></ul></div>

        </div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />

    </div>
    
<? include_once ("bottom_content.php");?>
</body>
</html>