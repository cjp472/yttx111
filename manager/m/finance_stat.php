<?php 
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("../class/xmldata.class.php");

if(empty($in['begindate'])) $in['begindate'] = '';
if(empty($in['enddate']))   $in['enddate']   = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/finance.js?v=<? echo VERID;?>" type="text/javascript"></script>

<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>

<script language="JavaScript" type="text/javascript"> 
<!--
$(function(){
	$("#begindate").datepicker();
	$("#enddate").datepicker();
});

function show_stat_data()
{
	document.MainForm.action = 'finance_stat.php?cid=<? echo $in[cid];?>';
	document.MainForm.target = '_self';
	document.MainForm.submit();
}
-->
</script>
</head>

<body>
<?php include_once ("top.php");?>
<div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">

   	        </div>
            
			<div class="location"><strong>当前位置：</strong> <a href="#">款项统计</a> </div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 

<div >
<strong><a href="finance_stat.php">所有药店</a></strong></div>

<ul>
				<form name="changetypeform" id="changetypeform" action="<? echo 'finance_stat.php?begindate='.$in['begindate'].'&enddate='.$in['enddate'].'';?>" method="post">
				<select id="cid" name="cid" onchange="javascript:submit()" class="select2" style="width:85%;">
				<option value="" >⊙ 所有药店</option>
	<?php 
		$clientdata = $db->get_results("SELECT ClientID,ClientCompanyName,ClientCompanyPinyi,ClientFlag FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0  ORDER BY ClientCompanyPinyi ASC ");
		$n = 0;
		foreach($clientdata as $areavar)
		{
			$n++;
			if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
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
          <form id="MainForm" name="MainForm" method="post" action="finance_stat.php"  >
		  <input name="clientid" type="hidden" id="clientid"   maxlength="12" value="<? if(!empty($in['cid'])) echo $in['cid'];?>"   />
			<div class="line" >
			<fieldset class="fieldsetstyle">
			<legend>款项统计</legend>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td height="30" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从<input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;<input name="enddate" type="text" id="enddate" maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>"   />&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查 看 " class="mainbtn" onclick="show_stat_data()"/>&nbsp;&nbsp;</td>
     			 </tr>
				 <?					
					$sqll = "";
					$sql2 = "";
					$sql3 = "";
					if(!empty($in['cid'])){
						$sqll = " and FinanceClient=".$in['cid']." ";
						$sql2 = " and OrderUserID=".$in['cid']." ";
						$sql3 = " and ReturnClient=".$in['cid']." ";
						$sql5 = " and ClientID=".$in['cid']." ";
					}
					if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FinanceToDate between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
					$statsql0  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." ".$sqlunion." and FinanceFlag=0 and (FinanceType='Z' OR FinanceType='O') ";
					$statdata0 = $db->get_row($statsql0);

					if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FROM_UNIXTIME(FinanceUpDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
					$statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqll." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
					$statdata2 = $db->get_row($statsql2);

					if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FROM_UNIXTIME(OrderDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
// 					$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sql2." ".$sqlunion." and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 ";
					$statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sql2." ".$sqlunion." and OrderStatus!=8 and OrderStatus!=9 ";
					$statdatat = $db->get_row($statsqlt);

					if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and FROM_UNIXTIME(ReturnDate) between '".$in['begindate']." 00:00:00' and '".$in['enddate']." 23:59:59' "; else $sqlunion = "";
					$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sql3." ".$sqlunion." and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) ";
					$statdata1 = $db->get_row($statsqlt1);
					
					if(!empty($in['begindate']) && !empty($in['enddate'])) $sqlunion = " and ExpenseDate between '".$in['begindate']."' and '".$in['enddate']."' "; else $sqlunion = "";
					$statsqlt5  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sql5." ".$sqlunion." and FlagID='2' ";
					$statdata5 = $db->get_row($statsqlt5);

					if(!empty($statdatat))
					{
						if(empty($statdatat['Ftotal']))  $statdatat['Ftotal']  = 0;
						if(empty($statdatat0['Ftotal'])) $statdatat0['Ftotal'] = 0;
						if(empty($statdatat1['Ftotal'])) $statdatat1['Ftotal'] = 0;
						if(empty($statdatat2['Ftotal'])) $statdatat2['Ftotal'] = 0;
						if(empty($statdatat5['Ftotal'])) $statdatat5['Ftotal'] = 0;						
						
						$statdata['w']   = $statdatat['Ftotal'] - $statdata2['Ftotal'] - $statdata0['Ftotal'] - $statdata1['Ftotal'];
						$statdata['yin'] = $statdatat['Ftotal'] - $statdata1['Ftotal'] - $statdata5['Ftotal'];
						$statdata['y'] = $statdata2['Ftotal'];
						$statdata['t'] = $statdata0['Ftotal'];
						$statdata['a'] = $statdatat['Ftotal'];
						$statdata['r'] = $statdata1['Ftotal'];
						$statdata['o'] = $statdata5['Ftotal'];

						if(empty($statdata['w'])) $statdata['w'] = 0;
						if(empty($statdata['yin'])) $statdata['yin'] = 0;
						if(empty($statdata['y'])) $statdata['y'] = 0;
						if(empty($statdata['t'])) $statdata['t'] = 0;
						if(empty($statdata['a'])) $statdata['a'] = 0;
						if(empty($statdata['r'])) $statdata['r'] = 0;

						$finactpencent[1]['p'] = round($statdata['y']/$statdata['yin']*100,2);
						$finactpencent[1]['n'] = '已确认到账';

						$finactpencent[2]['p'] = round($statdata['w']/$statdata['yin']*100,2);
						$finactpencent[2]['n'] = '未付款金额';

						$finactpencent[3]['p'] = round($statdata['t']/$statdata['yin']*100,2);
						$finactpencent[3]['n'] = '在途付款金额';

						if(!empty($statdata['a']))
						{
				 ?>
     			 <tr>
       				 <td  >
		<?php 
		if($statdata['w'] > 0)
		{
		?>			
		<script type="text/javascript">
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: ''
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								color: '#000000',
								connectorColor: '#000000',
								formatter: function() {
									return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
								}
							}
						}
					},
				    series: [{
						type: 'pie',
						name: 'Browser share',
						data: [
						<?
						foreach($finactpencent as $oskey=>$osvar)	
						{
							if($oskey=="1")
							{
								echo '
								{
								name: "'.$osvar[n].'",    
								y: '.$osvar[p].',
								sliced: true,
								selected: true
								},
								';
							}else{
								echo "['".$osvar[n]."',   ".$osvar[p]."],";
							}
						}
						?>
						]
					}]
				});
			});	
		</script>	
		
		<div id="container" ></div>
		<?php }?>
					</td>
     			 </tr>
				
     			 <tr>
       				 <td height="30" class="font12" >

<table width="90%" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td align="right">&nbsp;订单总金额：</td>
    <td align="right">&nbsp;¥ <? echo number_format($statdata['a'],2,'.',',');?> 元</td>
  </tr>
  <tr>
    <td align="right">&nbsp;退货金额：</td>
    <td align="right">&nbsp;¥ <? echo number_format($statdata['r'],2,'.',',');?> 元</td>
  </tr>
  <tr>
    <td align="right">&nbsp;其他款项：</td>
    <td align="right">&nbsp;¥ <? echo number_format($statdata['o'],2,'.',',');?> 元</td>
  </tr>
  <tr>
    <td align="right">&nbsp;应付款金额：</td>
    <td align="right">&nbsp;¥ <? echo number_format($statdata['yin'],2,'.',',');?> 元</td>
  </tr>
  <tr>
    <td align="right">&nbsp;1、已确认到账：</td>
    <td align="right">&nbsp;¥ <? echo number_format($statdata['y'],2,'.',',');?> 元</td>
  </tr>
  <tr>
    <td align="right">&nbsp;2、未付款金额：</td>
    <td align="right">&nbsp;¥ <? echo number_format($statdata['w'],2,'.',',');?> 元</td>
  </tr>
  <tr>
    <td align="right">&nbsp; 3、在途付款金额：</td>
    <td align="right">&nbsp;¥ <? echo number_format($statdata['t'],2,'.',',');?> 元</td>
  </tr>
</table>
					 </td>
     			 </tr>
     			 <tr>
       				 <td height="30" bgcolor="#ffffff" align="right">注：&nbsp;未审核的订单没有计入款项统计!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
     			 </tr>
				 <? }}else{?>
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