<?php 
$menu_flag = "manager";
include_once ("header.php");


setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name='robots' content='noindex,nofollow' />

<title><? echo SITE_NAME;?> - 管理平台</title>

<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>

<style type="text/css">
 .analysis{}
 .analysis td{font-size:14px;}

 .sfont18{font-size:18px; color:#cc0000; font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
  .sfont20{font-size:20px; color:#FF6600; font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
  .sfont24{font-size:24px; color:#009933; font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
</style>

</head>



<body>

<?php include_once ("top.php");?>

    

<?php include_once ("inc/son_menu_bar.php");?>

        

    <div id="bodycontent">

    	<div class="lineblank"></div>

    	<div id="searchline">

        	<div class="leftdiv">

        	  

   	        </div>

            

			<div class="location"></div>

        </div>

    	

        <div class="line2"></div>

        <div class="bline">






	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 

<div >
<strong><a href="#">数据分析</a></strong></div>
<ul>
	<li>- <a href="analysis_y.php" class="locationli">年交易数据</a></li>	
	<li>- <a href="analysis_m.php">月交易数据</a></li>
</ul>


<hr style="clear:both;" />

</div>

<!-- tree -->   
       	  </div>
        	<div id="sortright">

          
		  <div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>年交易数据分析</legend>

<?php
	$sqlmsg = '';

	$year = date("Y");
	foreach($databasearr as $v)
	{
		if(empty($v)) $dname = DB_DATABASE."."; else $dname = DB_DATABASE."_".$v.".";
		
		$yearsql  = "SELECT left(OrderSN,4) as ODate,sum(OrderTotal) as totalmoney,count(*) as totalnumber from ".$dname.DATATABLE."_order_orderinfo where OrderCompany > 20   AND OrderStatus != 8 
  AND OrderStatus != 9 and OrderTotal < 1000000  group by ODate ";
		$data[$dname] = $db->get_results($yearsql);
		foreach($data[$dname] as $kk=>$vv){
			$ydata[$vv['ODate']]['number'] += $vv['totalnumber'];
			$ydata[$vv['ODate']]['money']  += $vv['totalmoney'];
		}
	}

?>


             <table width="100%" border="0" cellspacing="0" cellpadding="0" >
     			 <tr>
       				 
					 <td >

					 <div id="container" ></div>
					 
					 </td>
					
     			 </tr>
			</table>



			<table width="90%" border="0" cellspacing="0" cellpadding="0" class="analysis">  
			<?php 
			foreach($ydata as $k=>$v){
				$carr[] = "'".$k."'";
				$tarr[] = $v['number'];
				$narr[] = $v['money'];
			?>
			 <tr  >
			 <tr  >
                  <td align="center" height="50" width="20%" class="sfont18"><?php echo $k; ?> 年</td>
				  <td align="right" width="10%">订单量：</td>
				  <td width="10%" class="sfont20" align="right"><? echo intval($v['number']);?></td>
				  <td align="right" width="10%">交易量：</td>
				  <td class="sfont24" width="18%" align="right"> ¥ <? echo number_format($v['money'],2,'.',',');?></td>

			 </tr>
			 </tr>
			 <?php 
			 }
			$carrmsg = implode(",",$carr);
			$tarrmsg = implode(",",$tarr);
			$narrmsg = implode(",",$narr);			 
			 ?>

			 </table>
			<script type="text/javascript">
		
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						zoomType: 'xy'
					},
					title: {
						text: "年交易数据分析"
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
								return this.value +'元';
							},
							style: {
								color: '#89A54E'
							}
						},
						title: {
							text: '交易额',
							style: {
								color: '#89A54E'
							}
						}
					}, { // Secondary yAxis
						title: {
							text: '订单量',
							style: {
								color: '#4572A7'
							}
						},
						labels: {
							formatter: function() {
								return this.value +' 个';
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
								(this.series.name == '订单量' ? ' 个' : '元');
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
						name: '订单量',
						color: '#4572A7',
						type: 'spline',
						yAxis: 1,
						data: [<? echo $tarrmsg;?>]
					}, {
						name: '交易额',
						color: '#89A54E',
						type: 'column',
						data: [<? echo $narrmsg;?>]
					}]
				});		
			});				
		</script>

			</fieldset>  
			 </div>
		  </div>
		  

       	  </div>

        <br style="clear:both;" />
    </div>
 
<?php include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>