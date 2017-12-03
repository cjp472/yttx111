<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('错误参数!');
}else{	 
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
}

if($_SESSION['uinfo']['usertype']=="M")
{
	$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");
}else{
	$cinfo = $db->get_row("SELECT c.ClientID,c.ClientName,c.ClientCompanyName,c.ClientTrueName,c.ClientPhone,c.ClientMobile,c.ClientAdd FROM ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany = ".$_SESSION['uinfo']['ucompany']." and c.ClientID=".$oinfo['OrderUserID']."  and s.SalerID=".$_SESSION['uinfo']['userid']." limit 0,1");

	if(empty($cinfo))
	{
		echo '<p>&nbsp;</p><p>参数错误！<a href="javascript:history.back(-1)">点此返回</a></p>';
		exit;
	}
}

$cominfo = $db->get_row("SELECT CompanyName,CompanyContact,CompanyPhone,CompanyFax,CompanyAddress FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']."  limit 0,1");
$orderby = " order by SiteID asc,ID asc ";

$cartdata = $db->get_results("select * from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." ".$orderby." ");
$cartdata_gifts = $db->get_results("select * from ".DATATABLE."_view_index_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by SiteID asc,ID asc");
$valuearr = null;
$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
$valuearr1 = unserialize($setinfo['SetValue']);

// 导出配置 by zjb  20160623
if(empty($valuearr1['order'])) $valuearr = unserialize('a:13:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:3:"6 %";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:13:"商品名称 ";s:5:"width";s:0:"";s:4:"show";i:1;}s:9:"BrandName";a:3:{s:4:"name";s:12:"品牌";s:5:"width";s:4:"18 %";s:4:"show";i:1;}s:5:"Model";a:3:{s:4:"name";s:7:"型号 ";s:5:"width";s:3:"8 %";s:4:"show";i:1;}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:3:"8 %";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:3:"5 %";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:4:"10 %";s:4:"show";i:1;}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:3:"6 %";s:4:"show";i:1;}s:12:"PercentPrice";a:3:{s:4:"name";s:10:"折后价 ";s:5:"width";s:4:"10 %";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:7:"金额 ";s:5:"width";s:4:"12 %";s:4:"show";i:1;}s:16:"CompanyInfoPrint";i:2;}'); else $valuearr = $valuearr1['order'];
//if(empty($valuearr1['order'])) $valuearr = unserialize('a:13:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:3:"6 %";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:13:"商品名称 ";s:5:"width";s:0:"";s:4:"show";i:1;}s:9:"BrandName";a:3:{s:4:"name";s:12:"生产厂家";s:5:"width";s:4:"18 %";s:4:"show";i:1;}s:5:"Model";a:3:{s:4:"name";s:7:"规格 ";s:5:"width";s:3:"8 %";s:4:"show";i:1;}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:3:"8 %";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:3:"5 %";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:4:"10 %";s:4:"show";i:1;}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:3:"6 %";s:4:"show";i:1;}s:12:"PercentPrice";a:3:{s:4:"name";s:10:"折后价 ";s:5:"width";s:4:"10 %";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:7:"金额 ";s:5:"width";s:4:"12 %";s:4:"show";i:1;}s:16:"CompanyInfoPrint";i:2;}'); else $valuearr = $valuearr1['order'];
//if(empty($valuearr1['order'])) $valuearr = unserialize('a:14:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"折后价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";}'); else $valuearr = $valuearr1['order'];
if(empty($valuearr1['paper']['PrintWidth'])) $paper_width = 2100; else $paper_width = $valuearr1['paper']['PrintWidth']*10;
if(empty($valuearr1['paper']['PrintHeight'])) $paper_height = 29700; else $paper_height = $valuearr1['paper']['PrintHeight']*10;
$paper_name = 'LodopCustomPage';

$rightarr = array('ContentNumber','Price1','Price2','ContentPrice','ContentPercent','PercentPrice','LineTotal');
//获取去厂家  by zjb 20160623
$brandsql   = "SELECT * FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." ORDER BY BrandPinYin ASC";
$brandsql_data = $db->get_results($brandsql);
foreach ($brandsql_data as $val){
    $brandsqlarr[$val['BrandID']] = $val;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/printf.css" rel='stylesheet' />

<script language="javascript" src="../plugin/printf/LodopFuncs_11.js?v=2014d3e1203"></script>

<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="../plugin/printf/install_lodop32.exe"></embed>
</object>
</head>

<body style="width:800px; margin:0 auto;"> 
<script language="javascript" type="text/javascript"> 
	var LODOP; //声明为全局变量

	function Preview()
	{		
		setMytable();
		var isprint = LODOP.PREVIEW();
		if(isprint == 1)
		{
			printlog('order','<?php echo $oinfo['OrderID'];?>');
		}
	}

	function Preprint()
	{
		setMytable();
		//LODOP.SELECT_PRINTER();
		var isprint = LODOP.PRINTA();
		if(isprint == 1)
		{
			printlog('order','<?php echo $oinfo['OrderID'];?>');
		}
	}

	function printlog(logty,logid)
	{
		$.post("do_log.php",
			{m:"printlog", ty: logty, ID: logid},
			function(data){
				
			}		
		);
	}

	function frm_show_printlog()
	{
		document.getElementById('print_log').style.display = "block";
	}

	function frm_hide_printlog()
	{
		document.getElementById('print_log').style.display = "none";
	}

</script>

<div id="print_log" style="display:none;">
	<p align="right">[<a href="javascript:void(0);" onclick="frm_hide_printlog();">关闭</a>]&nbsp;&nbsp;</p>
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" >
	  <thead>
	   <tr>
	    <td width="20%">打印日志：</td>
		<td width="15%">行号</td>
		<td width="25%">打印时间</td>
		<td>用户</td>		
	  </tr>
	  </thead>

	 <tbody>
	  <?php
	  $n = 1;
	  $printlogdata = $db->get_results("select * from ".DATATABLE."_order_print_log where  LogContent=".$oinfo['OrderID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." and LogType ='order' order by LogID asc limit 0,100");
	  if(!empty($printlogdata)){
	  foreach($printlogdata as $v){
	  ?>
	   <tr>
	    <td >&nbsp;</td>
		<td ><?php echo $n++;?></td>
		<td ><?php echo date("Y-m-d H:i:s",$v['LogDate']);?></td>
		<td><?php echo $v['logUser'];?></td>		
	  </tr>
	  <?php 
	  }}else{ 
		echo '<tr>
	    <td colspan="4" align="center">&nbsp;暂无记录！</td>
	  </tr>'; 
	  }
	  ?>
	 </tbody>
	</table>
	<br />
</div>


<?php 
if(@file_exists("./print/order_".$_SESSION['uinfo']['ucompany'].".php")){
	include_once ("print/order_".$_SESSION['uinfo']['ucompany'].".php"); 
}else{
	include_once ("print/order.php"); 
}
?>


<form id="MainForm" name="MainForm" method="post" action="order_content_excel.php" target="exe_iframe" >
<input type="hidden" name="handle" id="handle" value="excel" />
<input type="hidden" name="ID" id="ID" value="<? echo $oinfo['OrderID'];?>" />
</form>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>