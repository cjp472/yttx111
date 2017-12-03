<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate'] = date("Y-m-d");

$clientdata = $db->get_results("select UserID,UserName,UserTrueName from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserType='S'  order by UserID asc");
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
<script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#begindate").datepicker();
		$("#enddate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">

   	        </div>            
			<div class="location"><strong>当前位置：</strong> <a href="statistics_deduct.php">客情官提成统计</a></div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

        	<div id="">
          <form id="MainForm" name="MainForm" method="post" action="statistics_deduct.php"  >
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>客情官提成统计</legend>
                 <table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
     			 <tr>
       				 <td height="30" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;<input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;<input name="enddate" type="text" id="enddate"   maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   />&nbsp;&nbsp;
					 <select id="sid" name="sid" onchange="javascript:submit()" style="width:160px !important; width:145px;" class="select2">
							<option value="" >⊙ 所有客情官</option>
							<?php 
							foreach($clientdata as $areavar)
							{
								$salerarr[$areavar['UserID']] = $areavar['UserTrueName'];
								if($in['sid'] == $areavar['UserID']) $smsg = 'selected="selected"'; else $smsg ="";
								echo '<option value="'.$areavar['UserID'].'" '.$smsg.' title="'.$areavar['UserTrueName'].'"  > '.$areavar['UserName'].' - '.$areavar['UserTrueName'].'</option>';
							}
						?>
						</select>
					 &nbsp;&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick="show_stat_return_data()"/>&nbsp;&nbsp;<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_deduct_excel();" /></td>
     			 </tr>
				 <? 
				if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
				{
					echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
				}else{
					$sqll = '';
					if(!empty($in['sid'])) $sqll = " and DeductUser = ".$in['sid']." ";
					$statsql  = "SELECT DeductUser,sum(DeductTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_deduct where CompanyID=".$_SESSION['uinfo']['ucompany']."  ".$sqll." and FROM_UNIXTIME(DeductDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59'  group by DeductUser";
					$statdata = $db->get_results($statsql);

					$statsql0  = "SELECT DeductUser,sum(DeductTotal) as OTotal,count(*) as totalnumber from ".DATATABLE."_order_deduct where CompanyID=".$_SESSION['uinfo']['ucompany']." and DeductStatus='T' ".$sqll." and FROM_UNIXTIME(DeductDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59'  group by DeductUser";
					$rdata = $db->get_results($statsql0);
					if(!empty($rdata))
					{
						foreach($rdata as $rvar)
						{
							$stdata[$rvar['DeductUser']] = $rvar;
						}
					}

					if(!empty($statdata))
					{
						foreach($statdata as $var)
						{
							if(empty($stdata[$var['DeductUser']]['OTotal'])) $ttotal = 0; else $ttotal = $stdata[$var['DeductUser']]['OTotal'];
							$ftotal = $var['OTotal'] - $ttotal;
							$ftotal = sprintf("%01.2f", round($ftotal,2));
							$ttotal = sprintf("%01.2f", round($ttotal,2));
							if(empty($cmsg))
							{
								$cmsg = "'".$salerarr[$var['DeductUser']]."'";
								$pmsg = $ttotal;
								$rmsg = $ftotal;
							}else{
								$cmsg .= ",'".$salerarr[$var['DeductUser']]."'";
								$pmsg .= ",".$ttotal;
								$rmsg  .= ",".$ftotal;
							}
						}
				 ?>
     			 <tr>
       				 <td bgcolor="#ffffff" >
		<script type="text/javascript">		
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						defaultSeriesType: 'column'
					},
					title: {
						text: '客情官提成统计'
					},
					xAxis: {
						categories: [<? echo $cmsg;?>]
					},
					yAxis: {
						min: 0,
						title: {
							text: '提成金额'
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
								 '共: '+ this.point.stackTotal.toFixed(2);
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
						name: '未发放提成',
						data: [<? echo $rmsg;?>]
					}, {
						name: '已发放提成',
						data: [<? echo $pmsg;?>]
					}]
				});	
			});
		</script>
					 <div id="container" ></div>
					 </td>
     			 </tr>
     			 <tr>
       				 <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong><? echo "从 ".$in['begindate']." 到 ".$in['enddate']."  ";?> 客情官提成数据 </strong></td>
     			 </tr>
     			 <tr>
       				 <td >

        	  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">               
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold"><label>  行号</label></td>
                  <td  class="bottomlinebold">客情官</td>
				  <td align="right" width="10%" class="bottomlinebold">已发放笔数</td>
                  <td align="right" width="16%"  class="bottomlinebold">已发放的提成</td>
				  <td align="right" width="16%" class="bottomlinebold">未放发的提成</td>
				  <td align="right" width="10%" class="bottomlinebold">总提成笔数</td>
				  <td align="right" width="18%" class="bottomlinebold">总的提成金额</td>
				  <td align="center" width="8%" class="bottomlinebold">明细</td>
                </tr>
     		 </thead>			 
			 <tbody>
			 <?
				$totalm = 0;
				$totaln = 0;
				$allttotal = $allftotal  = 0;
				$n=1;
				foreach($statdata as $var)
				{
					$totalm = $totalm + $var['OTotal'];
					$totaln = $totaln + $var['totalnumber'];

					if(empty($stdata[$var['DeductUser']]['totalnumber'])) $tnumber = 0; else $tnumber =$stdata[$var['DeductUser']]['totalnumber'];
					$alltnumber = $alltnumber + $tnumber;
					
					if(empty($stdata[$var['DeductUser']]['OTotal'])) $ttotal = 0; else $ttotal =$stdata[$var['DeductUser']]['OTotal'];
					$allttotal = $allttotal + $ttotal; 
					$ftotal = $var['OTotal'] - $ttotal;
					$allftotal = $allftotal + $ftotal; 
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;<? echo $n++;?></td>
				  <td ><a href="javascript:void(0);" ><? echo $salerarr[$var['DeductUser']];?></a></td>				  
                  <td align="right"><? echo $tnumber;?></td>
				  <td align="right">¥ <? echo $ttotal;?></td>
				  <td align="right">¥ <? echo $ftotal;?></td>
				  <td align="right" ><? echo $var['totalnumber'];?></td>
				  <td align="right" style="color:#f45c0d">¥ <? echo $var['OTotal'];?></td>
				  <td align="center"><a href="javascript:show_deduct_list('<? echo $var['DeductUser'];?>','<? echo $in['begindate'];?>','<? echo $in['enddate'];?>');">明细</a></td>
			 </tr>
			 <? 
				 }			 
			 ?>
			 <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td>&nbsp;</td>
				  <td ><strong>合计：</strong></td>
				  <td align="right"><strong><? echo $alltnumber;?> 笔</strong></td>
                  <td align="right"><strong>¥  <? echo sprintf("%01.2f", round($allttotal,2));?></strong></td>
				  <td align="right"><strong>¥ <? echo sprintf("%01.2f", round($allftotal,2));?></strong></td>
 				  <td align="right"><strong><? echo $totaln;?> 笔</strong></td>
				  <td align="right"><strong class="font12h">¥  <? echo sprintf("%01.2f", round($totalm,2));?></strong></td>
				  <td>&nbsp;</td>
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
    <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">提成明细：</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent" >
        数据载入中...       
        </div>
	</div>
</body>
</html>