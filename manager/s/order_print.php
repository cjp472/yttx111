<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('错误参数!');
}else{	 
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
}
$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
if (!in_array($oinfo['OrderUserID'], $sclientidarr )) exit('错误参数!');

$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

$cominfo = $db->get_row("SELECT CompanyName,CompanyContact,CompanyPhone,CompanyFax,CompanyAddress FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']."  limit 0,1");

$cartdata = $db->get_results("select c.*,i.Coding,i.Price1,i.Price2,i.Units from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by ID asc");

	$valuearr = null;
	$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='printf' limit 0,1");
	if(empty($setinfo['SetValue'])) $setinfo['SetValue']='a:14:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"折后价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";} ';
	$valuearr = unserialize($setinfo['SetValue']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<style type="text/css">
<!--
body { margin:0; padding:0; font-size:12px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#333;}
h4{ font-size:14px;font-weight:bold; margin:0; padding:0; float:left; height:auto;}
h1{color:#000000; font-size:18px; line-height:24px; padding-top:20px; font-weight:bold; font-family:"黑体",Verdana, Arial, Helvetica, sans-serif;}

a{text-decoration:none; color:#277DB7; font-family:Verdana, Arial, Helvetica, sans-serif; }
a:hover{text-decoration:underline; color:#cc0000; font-family:Verdana, Arial, Helvetica, sans-serif;}

a.buttonb{text-decoration:none; color:#277DB7;  border:#277DB7 solid 1px; padding:2px; }
a.buttonb:hover{text-decoration:none; color:#cc0000;  border:#cc0000 solid 1px; padding:2px;}

td,div,p{color:#333333; font-size:12px; line-height:180%; font-family:Verdana, Arial, Helvetica, sans-serif;}
.td_line{border:solid .5pt #666666; padding:0cm 2pt 0cm 2pt; background-color:#ffffff; border-right:none;}
.tdr_line{border:solid .5pt #666666; padding:2pt; background-color:#ffffff; }
.tl_line{border-top:none; border-left:solid .5pt #666666; border-bottom:solid .5pt #666666; border-right:none; padding:0cm 2pt 0cm 2pt;background-color:#FFFFFF }
.tr_line{border-top:none; border-left:solid .5pt #666666; border-bottom:solid .5pt #666666; border-right:solid .5pt #666666; padding:2pt; background-color:#FFFFFF }
-->
</style>
<script type="text/javascript">
function control(obj, sType) 
{
	var oDiv = document.getElementById(obj);
	if (sType == 'show') { oDiv.style.display = 'block';}
	if (sType == 'hide') { oDiv.style.display = 'none';}
}
</script>

</head>

<body <? if(!empty($valuearr['CompanyInfoPrint']) && $valuearr['CompanyInfoPrint']=="1") echo 'onload="window.parent.printHeader.frm_hiddeninfo();"';?>> 
 <h1 align="center"><?php echo $_SESSION['uc']['CompanyName'];?> 在线订单</h1>
	
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="0">
    <tr>
	<td height="18" width="10%"><strong>商品清单</strong></td>
	<td width="28%"><strong>订单号：<? echo $oinfo['OrderSN'];?></strong></td>
    <td width="28%"><strong>客户：<? echo $cinfo['ClientCompanyName'];?></strong></td>
	<td  align="right"><strong>订购时间：<? echo date("Y-m-d H:i",$oinfo['OrderDate']);?></strong></td>
  </tr>
</table>

  <table width="98%" border="0" align="center" cellpadding="2" cellspacing="0" bgcolor="#FFFFFF" >
  <thead>
  <tr>
    <td width="<? if(!empty($valuearr['NO']['width'])) echo $valuearr['NO']['width']; else echo '6%';?>" height="32" class="td_line"><strong>&nbsp;<? if(!empty($valuearr['NO']['width'])) echo $valuearr['NO']['name']; else echo '行号';?></strong></td>
	<td  width="<? if(!empty($valuearr['Coding']['width'])) echo $valuearr['Coding']['width']; else echo '10%';?>" nowrap="nowrap" class="td_line"><strong>&nbsp;<? if(!empty($valuearr['Coding']['width'])) echo $valuearr['Coding']['name']; else echo '编号/货号';?></strong></td>
    <td  <? if(!empty($valuearr['ContentName']['width'])) echo 'width="'.$valuearr['ContentName']['width'].'"';?> class="td_line"><strong>&nbsp;<? if(!empty($valuearr['ContentName']['name'])) echo $valuearr['ContentName']['name']; else echo '商品名称';?></strong></td>	
	<? 
	$td1=0;
	$td2=0;
	if(!empty($valuearr['ContentColor']['show']) && $valuearr['ContentColor']['show']=="1")
	{
		if(empty($valuearr['ContentColor']['name'])) $valuearr['ContentColor']['name'] = '颜色';
		if(empty($valuearr['ContentColor']['width'])) $valuearr['ContentColor']['width'] = '8%';
		echo '<td width="'.$valuearr['ContentColor']['width'].'" class="td_line"><strong>&nbsp;'.$valuearr['ContentColor']['name'].'</strong></td>';
		$td1++;
	}
	if(!empty($valuearr['ContentSpecification']['show']) && $valuearr['ContentSpecification']['show']=="1")
	{
		if(empty($valuearr['ContentSpecification']['name'])) $valuearr['ContentSpecification']['name'] = '规格';
		if(empty($valuearr['ContentSpecification']['width'])) $valuearr['ContentSpecification']['width'] = '8%';
		echo '<td width="'.$valuearr['ContentSpecification']['width'].'" class="td_line"><strong>&nbsp;'.$valuearr['ContentSpecification']['name'].'</strong></td>';
		$td1++;
	}
	?> 
	
	<td  width="<? if(!empty($valuearr['ContentNumber']['width'])) echo $valuearr['ContentNumber']['width']; else echo '6%';?>" align="right" nowrap="nowrap" class="td_line"><strong>&nbsp;<? if(!empty($valuearr['ContentNumber']['width'])) echo $valuearr['ContentNumber']['name']; else echo '数量';?></strong></td>
	<td  width="<? if(!empty($valuearr['Units']['width'])) echo $valuearr['Units']['width']; else echo '5%';?>" nowrap="nowrap" align="center" class="td_line"><strong>&nbsp;<? if(!empty($valuearr['Units']['width'])) echo $valuearr['Units']['name']; else echo '单位';?></strong></td>

	<? 
	if(!empty($valuearr['Price1']['show']) && $valuearr['Price1']['show']=="1")
	{
		if(empty($valuearr['Price1']['name'])) $valuearr['Price1']['name'] = '价格1';
		if(empty($valuearr['Price1']['width'])) $valuearr['Price1']['width'] = '10%';
		echo '<td width="'.$valuearr['Price1']['width'].'" align="right" class="td_line"><strong>&nbsp;'.$valuearr['Price1']['name'].'</strong></td>';
		//$td2++;
	}

	if(!empty($valuearr['Price2']['show']) && $valuearr['Price2']['show']=="1")
	{
		if(empty($valuearr['Price2']['name'])) $valuearr['Price2']['name'] = '价格2';
		if(empty($valuearr['Price2']['width'])) $valuearr['Price2']['width'] = '10%';
		echo '<td width="'.$valuearr['Price2']['width'].'" align="right" class="td_line"><strong>&nbsp;'.$valuearr['Price2']['name'].'</strong></td>';
		//$td2++;
	}

	if(!empty($valuearr['ContentPrice']['show']) && $valuearr['ContentPrice']['show']=="1")
	{
		if(empty($valuearr['ContentPrice']['name'])) $valuearr['ContentPrice']['name'] = '单价';
		if(empty($valuearr['ContentPrice']['width'])) $valuearr['ContentPrice']['width'] = '10%';
		echo '<td width="'.$valuearr['ContentPrice']['width'].'" align="right" class="td_line"><strong>&nbsp;'.$valuearr['ContentPrice']['name'].'</strong></td>';
		$td2++;
	}
	if(!empty($valuearr['ContentPercent']['show']) && $valuearr['ContentPercent']['show']=="1")
	{
		if(empty($valuearr['ContentPercent']['name'])) $valuearr['ContentPercent']['name'] = '折扣';
		if(empty($valuearr['ContentPercent']['width'])) $valuearr['ContentPercent']['width'] = '6%';
		echo '<td width="'.$valuearr['ContentPercent']['width'].'" align="right" class="td_line"><strong>&nbsp;'.$valuearr['ContentPercent']['name'].'</strong></td>';
		$td2++;
	}
	?> 
	<td  width="<? if(!empty($valuearr['PercentPrice']['width'])) echo $valuearr['PercentPrice']['width']; else echo '10%';?>" align="right" class="td_line"><strong>&nbsp;<? if(!empty($valuearr['PercentPrice']['width'])) echo $valuearr['PercentPrice']['name']; else echo '折后价';?></strong></td>
	<td  width="<? if(!empty($valuearr['LineTotal']['width'])) echo $valuearr['LineTotal']['width']; else echo '10%';?>" align="right"  class="tdr_line"><strong>&nbsp;<? if(!empty($valuearr['LineTotal']['width'])) echo $valuearr['LineTotal']['name']; else echo '金额';?>(元)</strong></td>
  </tr>
   </thead>
   <tbody >
	<? 
	$alltotal1=$alltotal2=0;
	$alltotal = 0;
	$allnumber = 0;
	$n=0;
	foreach($cartdata as $ckey=>$cvar)
	{
		$n++;
	?>
    <tr id="linegoods_<? echo $cvar['ID'];?>"   >
    <td height="24" class="tl_line">&nbsp;<? echo $n;?></td>
	<td  class="tl_line">&nbsp;<? echo $cvar['Coding'];?>	</td>
    <td class="tl_line"><div  title="<? echo $cvar['ContentName'];?>"><? echo $cvar['ContentName'];?></div></td>
	<?
		if(!empty($valuearr['ContentColor']['show']) && $valuearr['ContentColor']['show']=="1")
		{
			if(empty($cvar['ContentColor'])) $cvar['ContentColor']='&nbsp;'; 
			echo '<td class="tl_line">'.$cvar['ContentColor'].'</td>';
		}
		if(!empty($valuearr['ContentSpecification']['show']) && $valuearr['ContentSpecification']['show']=="1")
		{
			if(empty($cvar['ContentSpecification'])) $cvar['ContentSpecification']='&nbsp;'; 
			echo '<td class="tl_line">'.$cvar['ContentSpecification'].'</td>';
		}
	?>
	
	<td align="right" class="tl_line"><? echo $cvar['ContentNumber'];?>	</td>
	<td align="center" class="tl_line"><? echo $cvar['Units'];?>	</td>
	<?
		if(!empty($valuearr['Price1']['show']) && $valuearr['Price1']['show']=="1")
		{
			if(empty($cvar['Price1'])) $cvar['Price1']='&nbsp;'; 
			echo '<td align="right" class="tl_line">¥ '.$cvar['Price1'].'</td>';
			$alltotal1  = $alltotal1 + $cvar['Price1'] * $cvar['ContentNumber'];
		}
		if(!empty($valuearr['Price2']['show']) && $valuearr['Price2']['show']=="1")
		{
			if(empty($cvar['Price2'])) $cvar['Price2']='&nbsp;'; 
			echo '<td align="right" class="tl_line">¥ '.$cvar['Price2'].'</td>';
			$alltotal2  = $alltotal2 + $cvar['Price2'] * $cvar['ContentNumber'];
		}
		if(!empty($valuearr['ContentPrice']['show']) && $valuearr['ContentPrice']['show']=="1")
		{
			if(empty($cvar['ContentPrice'])) $cvar['ContentPrice']='&nbsp;'; 
			echo '<td align="right" class="tl_line">¥ '.$cvar['ContentPrice'].'</td>';
		}
		if(!empty($valuearr['ContentPercent']['show']) && $valuearr['ContentPercent']['show']=="1")
		{
			if(empty($cvar['ContentPercent'])) $cvar['ContentPercent']='&nbsp;'; 
			echo '<td align="right" class="tl_line"> '.$cvar['ContentPercent'].'</td>';
		}
	?>

    <td align="right" class="tl_line">¥ <? 
		echo $pricepencent = $cvar['ContentPrice']*$cvar['ContentPercent']/10;
	?></td>
    <td align="right" class="tr_line" >¥ <? 
		echo $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>&nbsp;</td>
  </tr>
   <? 
		}
	$alltotal = sprintf("%01.2f", round($alltotal,2));
	$alltotal1 = sprintf("%01.2f", round($alltotal1,2));
	$alltotal2 = sprintf("%01.2f", round($alltotal2,2));
	?> 
  <tr>
    <td height="28" class="tl_line"><strong>合计：</strong></td>
	<td class="tl_line"  colspan="<? echo $td1+2;?>"><strong>大写：</strong><? echo toCNcap($alltotal);?></td>
	<td align="right" class="tl_line" ><strong><? echo $allnumber;?></strong></td>
	<td class="tl_line" >&nbsp;</td>
	<?
	if(!empty($valuearr['Price1']['show']) && $valuearr['Price1']['show']=="1")
	{
		echo '<td align="right" class="tl_line" ><strong>¥ '.$alltotal1.'&nbsp;</strong></td>';
	}
	if(!empty($valuearr['Price2']['show']) && $valuearr['Price2']['show']=="1")
	{
		echo '<td align="right" class="tl_line" ><strong>¥ '.$alltotal2.'&nbsp;</strong></td>';
	}
	?>
	<td class="tl_line" colspan="<? echo $td2;?>">&nbsp;</td>
    <td align="right" class="tr_line" colspan="2"><strong>¥ <? echo $alltotal;?></strong>&nbsp;</td>
  </tr>
<?
	$cartdata_gifts = $db->get_results("select c.*,i.Coding,i.Price1,i.Price2,i.Units from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by ID asc");
	if(!empty($cartdata_gifts))
	{
?>
  <tr>
    <td class="tl_line" height="24" colspan="<? echo 6+$td1+$td2;?>" ><strong>赠品清单</strong></td>
	<?
	if(!empty($valuearr['Price1']['show']) && $valuearr['Price1']['show']=="1")
	{
		echo '<td class="tl_line" >&nbsp;</td>';
	}
	if(!empty($valuearr['Price2']['show']) && $valuearr['Price2']['show']=="1")
	{
		echo '<td class="tl_line" >&nbsp;</td>';
	}
	?>
	<td class="tr_line" >&nbsp;</td>
  </tr>
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata_gifts as $ckey=>$cvar)
	{
	?>
    <tr    >
    <td height="24" class="tl_line">&nbsp;<? echo $n++;?></td>
	<td  class="tl_line">&nbsp;<? echo $cvar['Coding'];?>	</td>
    <td class="tl_line"><div  title="<? echo $cvar['ContentName'];?>"><? echo $cvar['ContentName'];?></div></td>	
	<?
		if(!empty($valuearr['ContentColor']['show']) && $valuearr['ContentColor']['show']=="1")
		{
			if(empty($cvar['ContentColor'])) $cvar['ContentColor']='&nbsp;'; 
			echo '<td class="tl_line">'.$cvar['ContentColor'].'</td>';
		}
		if(!empty($valuearr['ContentSpecification']['show']) && $valuearr['ContentSpecification']['show']=="1")
		{
			if(empty($cvar['ContentSpecification'])) $cvar['ContentSpecification']='&nbsp;'; 
			echo '<td class="tl_line">'.$cvar['ContentSpecification'].'</td>';
		}
	?>
	<td align="right" class="tl_line"><? echo $cvar['ContentNumber'];?>	</td>   
	<td align="center" class="tl_line"><? echo $cvar['Units'];?>	</td>
	<?
		if(!empty($valuearr['Price1']['show']) && $valuearr['Price1']['show']=="1")
		{
			if(empty($cvar['Price1'])) $cvar['Price1']='&nbsp;'; 
			echo '<td align="right" class="tl_line">¥ '.$cvar['Price1'].'</td>';
		}
		if(!empty($valuearr['Price2']['show']) && $valuearr['Price2']['show']=="1")
		{
			if(empty($cvar['Price2'])) $cvar['Price2']='&nbsp;'; 
			echo '<td align="right" class="tl_line">¥ '.$cvar['Price2'].'</td>';
		}
		if(!empty($valuearr['ContentPrice']['show']) && $valuearr['ContentPrice']['show']=="1")
		{
			if(empty($cvar['ContentPrice'])) $cvar['ContentPrice']='&nbsp;'; 
			echo '<td align="right" class="tl_line">¥ '.$cvar['ContentPrice'].'</td>';
		}
		if(!empty($valuearr['ContentPercent']['show']) && $valuearr['ContentPercent']['show']=="1")
		{
			if(empty($cvar['ContentPercent'])) $cvar['ContentPercent']='&nbsp;'; 
			echo '<td align="right" class="tl_line"> '.$cvar['ContentPercent'].'</td>';
		}
	?>

    <td align="right" class="tl_line">¥ <? 
		echo $pricepencent = $cvar['ContentPrice'];
	?></td>
    <td align="right" class="tr_line" >¥ <? 
		echo $linetotal = $cvar['ContentNumber']*$cvar['ContentPrice'];
		$alltotal  = $alltotal + $linetotal;
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>&nbsp;</td>
  </tr>
  <? }?>
  <tr>
    <td height="28" class="tl_line"><strong>合计：</strong></td>
	<td class="tl_line"  colspan="<? echo $td1+2;?>">&nbsp;<strong>大写：</strong><? echo toCNcap($alltotal);?></td>
	<td align="right" class="tl_line" ><strong><? echo $allnumber;?></strong></td>
	<td class="tl_line" >&nbsp;</td>
	<?
	if(!empty($valuearr['Price1']['show']) && $valuearr['Price1']['show']=="1")
	{
		echo '<td class="tl_line" >&nbsp;</td>';
	}
	if(!empty($valuearr['Price2']['show']) && $valuearr['Price2']['show']=="1")
	{
		echo '<td class="tl_line" >&nbsp;</td>';
	}
	?>
	<td class="tl_line" colspan="<? echo $td2;?>">&nbsp;</td>
    <td align="right" class="tr_line" colspan="2"><strong>¥ <? echo $alltotal;?></strong>&nbsp;</td>
  </tr>
<?
	}
?>
</tbody>
</table>

<table width="98%" border="0" align="center" cellpadding="2" cellspacing="0" bgcolor="#FFFFFF" >
  <tr>
    <td  height="28"  class="tl_line" width="20%" >&nbsp;<strong>收货人：</strong><? echo $oinfo['OrderReceiveCompany'].' / '.$oinfo['OrderReceiveName'];?></td>
    <td class="tl_line" width="30%">&nbsp;<strong>联系电话：</strong><? echo $oinfo['OrderReceivePhone'];?>&nbsp;</td>
	<td class="tr_line" >&nbsp;<strong>收货地址：</strong><? echo $oinfo['OrderReceiveAdd'];?>&nbsp;</td>
  </tr>
  <tr>
    <td class="tl_line" >&nbsp;<strong>备 注：</strong></td>
    <td height="28" class="tr_line" colspan="2"><? echo $oinfo['OrderRemark'];?>&nbsp;</td>
  </tr>
</table>

<table width="98%" border="0" align="center" cellpadding="2" cellspacing="0" bgcolor="#FFFFFF"  id="showcontactid" <? if(!empty($valuearr['CompanyInfoPrint']) && $valuearr['CompanyInfoPrint']=="1") echo 'style="display:none;"';?>>
   <tr>
    <td  height="28"  class="tl_line" colspan="3" >&nbsp;<strong>公司联系方式和收款帐号：</strong></td>
	<td class="tr_line" >&nbsp;&nbsp;</td>
  </tr>
  <tr>
    <td class="tl_line" height="24" width="20%"><strong>&nbsp;联系人：</strong><? echo $cominfo['CompanyContact'];?></td>
	<td  width="25%" class="tl_line" ><strong>&nbsp;电话：</strong><? echo $cominfo['CompanyPhone'];?></td>
	<td  class="tl_line"  width="25%"><strong>&nbsp;传真：</strong><? echo $cominfo['CompanyFax'];?></td>
    <td  class="tr_line" ><strong>&nbsp;地址：</strong><? echo $cominfo['CompanyAddress'];?></td>
  </tr>
	<?
	$datasql   = "SELECT AccountsBank,AccountsName,AccountsNO FROM ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['uinfo']['ucompany']."  Order by AccountsID ASC";
	$list_data = $db->get_results($datasql);
	$nn = 1;
	foreach($list_data as $lv)
	{	
	?>
  <tr  >
    <td class="tl_line" height="24" ><strong>&nbsp;收款帐号：</strong></td>
	<td   class="tl_line" ><strong>&nbsp;开户行：</strong><? echo $lv['AccountsBank'];?></td>
	<td  class="tl_line"  ><strong>&nbsp;帐号：</strong><? echo $lv['AccountsNO'];?></td>
    <td  class="tr_line" ><strong>&nbsp;收款人：</strong><? echo $lv['AccountsName'];?></td>
  </tr>
  <?}?>
  </table>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="0" bgcolor="#FFFFFF" >
  <tr>
	<td  height="30" width="50%" ><strong>操作员：</strong><? echo $_SESSION['uinfo']['usertruename'];?></td>    
	<td align="right"  ><strong>打印日期：</strong><? echo date("Y-m-d H:i");?>	</td>
  </tr>
</table>

<form id="MainForm" name="MainForm" method="post" action="order_content_excel.php" target="exe_iframe" >
<input type="hidden" name="handle" id="handle" value="excel" />
<input type="hidden" name="ID" id="ID" value="<? echo $oinfo['OrderID'];?>" />
</form>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?
 function toCNcap($data){
   $capnum=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
   $capdigit=array("","拾","佰","仟");
   $subdata=explode(".",$data);
   $yuan=$subdata[0];
   $j=0; $nonzero=0;
   for($i=0;$i<strlen($subdata[0]);$i++){
      if(0==$i){ //确定个位 
         if($subdata[1]){ 
            $cncap=(substr($subdata[0],-1,1)!=0)?"圆":"圆零";
         }else{
            $cncap="圆";
         }
      }   
      if(4==$i){ $j=0;  $nonzero=0; $cncap="万".$cncap; } //确定万位
      if(8==$i){ $j=0;  $nonzero=0; $cncap="亿".$cncap; } //确定亿位
      $numb=substr($yuan,-1,1); //截取尾数
      $cncap=($numb)?$capnum[$numb].$capdigit[$j].$cncap:(($nonzero)?"零".$cncap:$cncap);
      $nonzero=($numb)?1:$nonzero;
      $yuan=substr($yuan,0,strlen($yuan)-1); //截去尾数	  
      $j++;
   }

   if($subdata[1]){
     $chiao=(substr($subdata[1],0,1))?$capnum[substr($subdata[1],0,1)]."角":"零";
     $cent=(substr($subdata[1],1,1))?$capnum[substr($subdata[1],1,1)]."分":"零分";
   }
   $cncap .= $chiao.$cent."整";
   $cncap=preg_replace("/(零)+/","\\1",$cncap); //合并连续“零”
   return $cncap;
 }
?>