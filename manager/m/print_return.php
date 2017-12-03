<?php 
$menu_flag = "return";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('错误参数!');
}else{	 
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_returninfo where ReturnCompany = ".$_SESSION['uinfo']['ucompany']." and ReturnID=".intval($in['ID'])." limit 0,1");
}
$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['ReturnClient']." limit 0,1");

$cominfo = $db->get_row("SELECT CompanyName,CompanyContact,CompanyPhone,CompanyFax,CompanyAddress FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']."  limit 0,1");

$cartdata = $db->get_results("select c.*,i.Coding,i.Barcode,i.Units from ".DATATABLE."_order_cart_return c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ReturnID=".$oinfo['ReturnID']." order by ID asc");


$valuearr = null;
$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
$valuearr1 = unserialize($setinfo['SetValue']);

if(empty($valuearr1['return'])) $valuearr = unserialize('a:12:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:2:"5%";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:3:"24%";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:3:"12%";s:4:"show";s:1:"1";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";}'); else $valuearr = $valuearr1['return'];

if(empty($valuearr1['paper']['PrintWidth'])) $paper_width = 2100; else $paper_width = $valuearr1['paper']['PrintWidth']*10;
if(empty($valuearr1['paper']['PrintHeight'])) $paper_height = 29700; else $paper_height = $valuearr1['paper']['PrintHeight']*10;
$paper_name = 'LodopCustomPage';

$rightarr = array('ContentNumber','ContentPrice','ContentPercent','PercentPrice','LineTotal');
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
			printlog('return','<?php echo $oinfo['ReturnID'];?>');
		}
	}

	function Preprint()
	{
		setMytable();
		//LODOP.SELECT_PRINTER();
		var isprint = LODOP.PRINTA();
		if(isprint == 1)
		{
			printlog('return','<?php echo $oinfo['ReturnID'];?>');
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

	function setMytable()
	{
		LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));  
		LODOP.PRINT_INIT("在线退单打印");
		LODOP.SET_PRINT_PAGESIZE(0,<?php echo $paper_width;?>,<?php echo $paper_height;?>,"<?php echo $paper_name;?>");
		var strStyleCSS="<link href='css/printf.css' type='text/css' rel='stylesheet'>";
		var strFormHtml=strStyleCSS+"<body>"+document.getElementById("div_content").innerHTML+"</body>";
		
		LODOP.ADD_PRINT_TABLE(88,"2%","96%","BottomMargin:10px",strFormHtml);
		LODOP.SET_PRINT_STYLEA(0,"Vorient",3);		
		LODOP.ADD_PRINT_HTM(15,"2%","96%",88,strStyleCSS+"<body>"+document.getElementById("div_title").innerHTML+"</body>");
		LODOP.SET_PRINT_STYLEA(0,"ItemType",1);
		LODOP.SET_PRINT_STYLEA(0,"LinkedItem",1);	

		LODOP.ADD_PRINT_TEXT(3,60,150,20,"总页号：第#页/共&页");
		LODOP.SET_PRINT_STYLEA(0,"ItemType",2);
		LODOP.SET_PRINT_STYLEA(0,"Horient",1);			
	
	};	
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
	  $printlogdata = $db->get_results("select * from ".DATATABLE."_order_print_log where  LogContent=".$oinfo['ReturnID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." and LogType ='return' order by LogID asc limit 0,100");
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


<div id="div_title">
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="noborder">
	   <tr><td colspan="3" style="font-size:24px; font-weight:bold; text-align:center;"><?php echo $_SESSION['uc']['CompanyName'];?> 退单</td></tr>
	   <tr>
		<td width="28%"><strong>退单号：</strong><? echo $oinfo['ReturnSN'];?></td>
		<td ><strong>经 销 商：</strong><? echo $cinfo['ClientCompanyName'];?></td>
		<td width="28%" align="right"><strong>退货时间：</strong><? echo date("Y-m-d H:i",$oinfo['ReturnDate']);?></td>		
	  </tr>
	</table>
</div>

<div id="div_content">
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" >
  <thead>
  <tr>
	<?php
	$tdmsg = '';	
	foreach($valuearr as $kk=>$v)
	{
		if($kk == 'CompanyInfoPrint') continue;
		if(empty($v['width'])) $v['width'] = 'wid_th';
		if($v['show'] == "1")
		{
			if(@in_array($kk,$rightarr)) $alignmsg = ' align="right" '; elseif($kk=='Units') $alignmsg = ' align="center" '; else $alignmsg = '';
			if($kk == 'ContentName') $tdmsg .= '<td >'.$v['name'].'</td>'; else 
			$tdmsg .= '<td width="'.$v['width'].'" '.$alignmsg.' >'.$v['name'].'</td>';
			$tdfield[] = $kk;
		}
	}
	$dwidth = 100/count($tdfield).'%';
	$tdmsg = str_replace('wid_th',$dwidth,$tdmsg);
	echo $tdmsg;
	?>
  </tr>
   </thead>
   <tbody >
   <?php 
	$alltotal1 = 0;
	$alltotal  = 0;
	$allnumber = 0;
	$n=0;
	foreach($cartdata as $ckey=>$cvar)
	{
		$n++;
		$cvar['PercentPrice'] = $cvar['ContentPrice'];
		$cvar['LineTotal'] = $cvar['ContentNumber'] * $cvar['PercentPrice'];
		$allnumber = $allnumber + $cvar['ContentNumber'];
		$alltotal1 = $alltotal1 + $cvar['ContentNumber'] * $cvar['ContentPrice'];
		$alltotal  = $alltotal + $cvar['LineTotal'];		

		$tdmsg = '';
		foreach($tdfield as $kv)
		{
			if($kv == 'NO') $cvar[$kv] = $n;
			if(@in_array($kv,$rightarr)) $alignmsg = ' align="right" '; elseif($kv=='Units') $alignmsg = ' align="center" '; else $alignmsg = '';
			$tdmsg .= '<td '.$alignmsg.' >'.$cvar[$kv].'</td>';			
		}
		echo '<tr>'.$tdmsg.'</tr>';
	}
	echo '</tbody>';
	$alltotal  = number_format($alltotal,2,'.',',');
	$alltotal1 = number_format($alltotal1,2,'.',',');

	$totallinemsg = '';
	$totallinepage = '';
	foreach($tdfield as $kk=>$kv)
	{
		if(count($cartdata)/$paper_height > 6/1000)
		{
			//页小计
			if($kk==0) $totallinepage .= '<td><strong>页小计：</strong></td>';
			elseif($kv=="ContentNumber") $totallinepage .= '<td align="right" tdata="subSum" format="#.##"><strong> ######</strong></td>';
			elseif($kv=="ContentPrice") $totallinepage .= '<td align="right" tdata="subSum" format="#,##0.00"><strong>###</strong></td>';
			elseif($kv=="LineTotal") $totallinepage .= '<td align="right" tdata="subSum" format="#,##0.00"><strong>###</strong></td>';
			else $totallinepage .= '<td>&nbsp;</td>';
		}

		//合计
		if($kk==0) $totallinemsg .= '<td><strong>合计：</strong></td>';
		elseif($kv=="ContentName") $totallinemsg .= '<td ><strong>大写：</strong>'.toCNcap(str_replace(",","",$alltotal)).'</td>';
		elseif($kv=="ContentNumber") $totallinemsg .= '<td align="right"><strong> '.$allnumber.'</strong></td>';
		elseif($kv=="ContentPrice") $totallinemsg .= '<td align="right"><strong> '.$alltotal1.'</strong></td>';
		elseif($kv=="LineTotal") $totallinemsg .= '<td align="right"><strong> '.$alltotal.'</strong></td>';
		else $totallinemsg .= '<td>&nbsp;</td>';
	}

	$bottommsg = '  <tr>
	<td colspan="3" ><strong>联系人：</strong>'.$cinfo['ClientTrueName'].'</td>    
	<td colspan="'.(count($tdfield)-3).'"><strong>联系电话：</strong>'.$oinfo['ClientPhone'].'</td>
  </tr>
  <tr>
	<td colspan="3" ><strong>货运方式：</strong>'.$oinfo['ReturnSendType'].'</td>    
	<td colspan="'.(count($tdfield)-3).'" ><strong>包装外观：</strong>'.$oinfo['ReturnProductW'].' / '.$oinfo['ReturnProductB'].'</td>
  </tr>';

  if(!empty($valuearr['CompanyInfoPrint']) && $valuearr['CompanyInfoPrint']=="1")
  {
	 $bottommsg .= '<tr >
	<td  colspan="'.(count($tdfield)).'"><strong>联系人：</strong>'.$cominfo['CompanyContact'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>电话：</strong>'.$cominfo['CompanyPhone'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>传真：</strong>'.$cominfo['CompanyFax'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>地址：</strong>'.$cominfo['CompanyAddress'].'</td>
  </tr>';
  }

	$bottommsg .= '<tr class="noborder">
		<td  colspan="3" ><strong>操作员：</strong>'. $_SESSION['uinfo']['usertruename'].'</td>    
		<td align="right" colspan="'.(count($tdfield)-3).'" ><strong>打印日期：</strong>'.date("Y-m-d H:i").'</td>
	  </tr>';
	echo '<tfoot><tr>'.$totallinepage.'</tr><tr>'.$totallinemsg.'</tr>'.$bottommsg.'</tfoot>';
?>
</table>
</div>

<form id="MainForm" name="MainForm" method="post" action="return_content_excel.php" target="exe_iframe" >
<input type="hidden" name="handle" id="handle" value="excel" />
<input type="hidden" name="ID" id="ID" value="<? echo $oinfo['ReturnID'];?>" />
</form>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>