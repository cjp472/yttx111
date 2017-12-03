<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('错误参数!');
}

$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".intval($in['ID'])." limit 0,1");
$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$cinfo['ConsignmentOrder']."' limit 0,1");
$clientinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");
$cominfo = $db->get_row("SELECT CompanyName,CompanyContact,CompanyPhone,CompanyFax,CompanyAddress FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']."  limit 0,1");

$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,c.ContentPercent,l.ContentNumber,i.Coding,i.Casing,i.Units,i.Barcode,i.Price1,i.Price2,i.Model,i.CommendID from ".DATATABLE."_order_cart c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and l.ConsignmentID=".$cinfo['ConsignmentID']." and l.ConType='c' order by i.SiteID asc, i.BrandID asc,c.ID asc");

$cartdatag = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentPrice,l.ContentNumber,i.Coding,i.Casing,i.Units,i.Price1,i.Price2,i.Model from ".DATATABLE."_order_cart_gifts c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']."  and l.ConsignmentID=".$cinfo['ConsignmentID']." and l.ConType='g' order by i.SiteID asc,c.ID asc");

$valuearr  = null;
$setinfo   = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
$valuearr1 = unserialize($setinfo['SetValue']);

if(empty($valuearr1['send'])) $valuearr = unserialize('a:14:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"折后价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";}'); else $valuearr = $valuearr1['send'];

if(empty($valuearr1['paper']['PrintWidth'])) $paper_width = 2100; else $paper_width = $valuearr1['paper']['PrintWidth']*10;
if(empty($valuearr1['paper']['PrintHeight'])) $paper_height = 29700; else $paper_height = $valuearr1['paper']['PrintHeight']*10;
$paper_name = 'LodopCustomPage';

$rightarr = array('ContentNumber','Price1','Price2','ContentPrice','ContentPercent','PercentPrice','LineTotal');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link rel="stylesheet" href="css/printf.css" rel='stylesheet' />
<script language="javascript" src="../plugin/printf/LodopFuncs_11.js"></script>
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
			printlog('send','<?php echo $cinfo['ConsignmentID'];?>');
		}
	}

	function Preprint()
	{
		setMytable();
		//LODOP.SELECT_PRINTER();
		var isprint = LODOP.PRINTA();
		if(isprint == 1)
		{
			printlog('send','<?php echo $cinfo['ConsignmentID'];?>');
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
	  $printlogdata = $db->get_results("select * from ".DATATABLE."_order_print_log where  LogContent=".$cinfo['ConsignmentID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." and LogType ='send' order by LogID asc limit 0,100");
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
if(@file_exists("./print/consignment_".$_SESSION['uinfo']['ucompany'].".php")){
	include_once ("print/consignment_".$_SESSION['uinfo']['ucompany'].".php"); 
}else{
	include_once ("print/consignment.php"); 
}
?>

<form id="MainForm" name="MainForm" method="post" action="consignment_content_excel<?php if(@file_exists('./consignment_content_excel_'.$_SESSION['uinfo']['ucompany'].".php")) echo '_'.$_SESSION['uinfo']['ucompany'] ?>.php" target="exe_iframe" >
<input type="hidden" name="handle" id="handle" value="excel" />
<input type="hidden" name="ID" id="ID" value="<? echo $cinfo['ConsignmentID'];?>" />
</form>
<iframe name="exe_iframe" id="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>