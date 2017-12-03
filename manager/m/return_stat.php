<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate'] = date("Y-m-d");

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

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#begindate").datepicker();
		$("#enddate").datepicker();
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
			<div class="location"><strong>当前位置：</strong><a href="return_stat.php">退货统计</a>  </div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 


<div ><strong><a href="return_stat.php">所有药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="<? echo 'return_stat.php?begindate='.$in['begindate'].'&enddate='.$in['enddate'].'';?>" method="post">
				<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:85%;" >
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
 </div>

<!-- tree -->   
       	  </div>
        	<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="return_stat.php"  >
		  <input name="clientid" type="hidden" id="clientid"   maxlength="12" value="<? if(!empty($in['cid'])) echo $in['cid'];?>"   />
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>退单统计</legend>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从<input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;<input name="enddate" type="text" id="enddate"   maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   />&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick="show_stat_return_data()"/>&nbsp;&nbsp;<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_excel('between_return');" /></td>
     			 </tr>
				 <?
				if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
				{
					echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
				}else{
					
					if(!empty($in['cid'])) $sqll = " and ReturnClient=".$in['cid']." ";
					$statsql  = "SELECT left(ReturnSN,9) as ODate,sum(ReturnTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." and FROM_UNIXTIME(ReturnDate) between  '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) group by left(ReturnSN,9)";
					$statdata = $db->get_results($statsql);
					
					if(!empty($statdata))
					{
						foreach($statdata as $var)
						{
							if(empty($snmsg))
							{
								$snmsg  = "'".$var['ODate']."'";
								$tmsg    = $var['OTotal'];
							}else{
								$snmsg .= ","."'".$var['ODate']."'";
								$tmsg   .= ",".$var['OTotal'];
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
						text: '<? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 退货单数据 <? if(!empty($in['cid'])) echo '('.$clientarr[$in['cid']].')';?>'
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
							text: '退单'
						}
					},
					legend: {
						enabled: false
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								 '退单金额: '+ Highcharts.numberFormat(this.y, 1) +
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
       				 <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong><? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 退货单数据 <? if(!empty($in['cid'])) echo '('.$clientarr[$in['cid']].')';?></strong></td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="2%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="35%" class="bottomlinebold">日期</td>
				  <td width="35%" class="bottomlinebold">退单金额</td>
                  <td  class="bottomlinebold">退货单数</td>
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
					$var['ODate'] = substr($var['ODate'],1);
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
				  <td ><a href="javascript:void(0);" onclick="show_return_list('day','<? echo $var['ODate'];?>','<? if(!empty($in['cid'])) echo $in['cid'];?>')"><? echo $var['ODate'];?></a></td>
				  <td >¥ <? echo $var['OTotal'];?></td>
                  <td ><? echo $var['totalnumber'];?></td>
			 </tr>
			 <? 
				 }			 
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
				  <td ><strong>合计：</strong></td>
				  <td ><strong>¥ <? echo $totalm;?> 元</strong></td>
                  <td ><strong> <? echo $totaln;?> 个</strong></td>
			 </tr>
			 </tbody>
			</table>

					 </td>
     			 </tr>
				 <? }else{?>
     			 <tr>
       				 <td height="130" bgcolor="#ffffff" align="center">&nbsp; 暂无符合条件的数据!</td>
     			 </tr>
				<? }}?>
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
			<h3 id="windowtitle">退单列表：</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContentList" >
        数据载入中...       
        </div>
	</div>
</body>
</html>