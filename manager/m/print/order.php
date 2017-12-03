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
?>
<script language="javascript" type="text/javascript"> 
	function setMytable(){
		LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));  
		LODOP.PRINT_INIT("在线订单打印");
		LODOP.SET_PRINT_PAGESIZE(0,<?php echo $paper_width;?>,<?php echo $paper_height;?>,"<?php echo $paper_name;?>");
		var strStyleCSS="<link href='css/printf.css' type='text/css' rel='stylesheet'>";

	<?php if(!empty($cartdata)){?>
		var strFormHtml=strStyleCSS+"<body>"+document.getElementById("div_content").innerHTML+"</body>";
		
		LODOP.ADD_PRINT_TABLE(88,"2%","96%","BottomMargin:20px",strFormHtml);
		LODOP.SET_PRINT_STYLEA(0,"Vorient",3);		
		LODOP.ADD_PRINT_HTM(15,"2%","96%",88,strStyleCSS+"<body>"+document.getElementById("div_title").innerHTML+"</body>");
		LODOP.SET_PRINT_STYLEA(0,"ItemType",1);
		LODOP.SET_PRINT_STYLEA(0,"LinkedItem",1);	
	<?php }?>
	
	<?php if(!empty($cartdata_gifts)){?>
		LODOP.NewPageA();
		var strFormHtml2=strStyleCSS+"<body>"+document.getElementById("div_gifts").innerHTML+"</body>";
		LODOP.ADD_PRINT_TABLE(88,"2%","96%","BottomMargin:20px",strFormHtml2);
		LODOP.SET_PRINT_STYLEA(0,"Vorient",3);
		LODOP.ADD_PRINT_HTM(15,"2%","96%",112,strStyleCSS+"<body>"+document.getElementById("div_gifts_title").innerHTML+"</body>");
		LODOP.SET_PRINT_STYLEA(0,"ItemType",1);
		LODOP.SET_PRINT_STYLEA(0,"LinkedItem",4);
	<?php }?>

		LODOP.ADD_PRINT_TEXT(3,60,150,20,"总页号：第#页/共&页");
		LODOP.SET_PRINT_STYLEA(0,"ItemType",2);
		LODOP.SET_PRINT_STYLEA(0,"Horient",1);	
		
	}
</script>
	
<?php
	if(!empty($cartdata))
	{
?>
<div id="div_title">
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="noborder">
	   <tr><td colspan="3" style="font-size:24px; font-weight:bold; text-align:center;"><?php echo $_SESSION['uc']['CompanyName'];?> 在线订单</td></tr>
	   <tr>
		<td width="28%"><strong>订单号：</strong><? echo $oinfo['OrderSN'];?></td>
		<td ><strong>经 销 商：</strong><? echo $cinfo['ClientCompanyName'];?></td>
		<td width="28%" align="right"><strong>订购时间：</strong><? echo date("Y-m-d H:i",$oinfo['OrderDate']);?></td>		
	  </tr>
	</table>
</div>

<div id="div_content">
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" >
  <thead>
  <tr>
	<?php
		echo $tdmsg;
	?>
  </tr>
   </thead>
   <tbody>
   <?php 
	$alltotal1 = 0;
	$alltotal  = 0;
	$allnumber = 0;
	$n=0;
	foreach($cartdata as $ckey=>$cvar)
	{
		$n++;
		$cvar['BrandName'] = $brandsqlarr[$cvar['BrandID']]['BrandName'];
		$cvar['PercentPrice'] = $cvar['ContentPrice'] * $cvar['ContentPercent'] / 10;
		$cvar['LineTotal'] = $cvar['ContentNumber'] * $cvar['PercentPrice'];
		$allnumber = $allnumber + $cvar['ContentNumber'];
		$alltotal1 = $alltotal1 + $cvar['ContentNumber'] * $cvar['ContentPrice'];
		$alltotal  = $alltotal + $cvar['LineTotal'];		

		$tdmsg = '';
		foreach($tdfield as $kv)
		{
			if($kv == 'NO') $cvar[$kv] = $n;
			if(@in_array($kv,$rightarr)) $alignmsg = ' align="right" '; elseif($kv=='Units') $alignmsg = ' align="center" '; else $alignmsg = '';
			if($kv == 'ContentName' && $cvar['CommendID'] == '2')
		        $tdmsg .= '<td '.$alignmsg.' >【特】'.$cvar[$kv].'</td>';	
			else 
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
		$totalVal = '<td align="right"><strong> '.$alltotal.'</strong></td>';
		$totalNum = $alltotal;
		if($oinfo['OrderSpecial'] == 'T'){
		    $totalVal = '<td align="right"><span style="text-decoration:line-through;">原价 ￥'.$alltotal.'</span><br/><strong> 特价 ￥'.$oinfo['OrderTotal'].'</strong></td>';
		    $totalNum = $oinfo['OrderTotal'];
		}
		if($kk==0) $totallinemsg .= '<td><strong>合计：</strong></td>';
		elseif($kv=="ContentName")  $totallinemsg .= '<td ><strong>大写：</strong>'.toCNcap(str_replace(",","",$totalNum)).'</td>';
		elseif($kv=="ContentNumber") $totallinemsg .= '<td align="right"><strong> '.$allnumber.'</strong></td>';
		elseif($kv=="ContentPrice") $totallinemsg .= '<td align="right"><strong> '.$alltotal1.'</strong></td>';
		elseif($kv=="LineTotal") $totallinemsg .= $totalVal;
		else $totallinemsg .= '<td>&nbsp;</td>';
	}
	
	if(empty($oinfo['DeliveryDate']) || $oinfo['DeliveryDate'] == '0000-00-00')
	    $oinfo['DeliveryDate'] = '';
	
	$bottommsg = '  <tr>
    <td colspan="'.(count($tdfield)).'" ><strong>收货公司：</strong>'.$oinfo['OrderReceiveCompany'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>联系人：</strong>'.$oinfo['OrderReceiveName'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>电话：</strong>'.$oinfo['OrderReceivePhone'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>交货日期：</strong>'.$oinfo['DeliveryDate'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>地址：</strong>'.$oinfo['OrderReceiveAdd'].'</td>
  </tr>';

    if(!empty($oinfo['OrderRemark'])) $bottommsg .= '<tr>
    <td  colspan="'.(count($tdfield)).'"><strong>备注：</strong>'.$oinfo['OrderRemark'].'</td>
  </tr>';
  
  if(!empty($valuearr['CompanyInfoPrint']) && $valuearr['CompanyInfoPrint']=="1")
  {
	 $bottommsg .= '<tr >
    <td  colspan="'.(count($tdfield)).'"><strong>联系人：</strong>'.$cominfo['CompanyContact'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>电话：</strong>'.$cominfo['CompanyPhone'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>传真：</strong>'.$cominfo['CompanyFax'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>地址：</strong>'.$cominfo['CompanyAddress'].'</td>
  </tr>';
  }
	$bottommsg .= '<tr class="noborder">
		<td colspan="'.(count($tdfield)/2).'" ><strong>操作员：</strong>'. $_SESSION['uinfo']['usertruename'].'</td>    
		<td align="right" colspan="'.ceil(count($tdfield)/2).'" ><strong>打印日期：</strong>'.date("Y-m-d H:i").'</td>
	  </tr>';
	echo '<tfoot><tr>'.$totallinepage.'</tr><tr>'.$totallinemsg.'</tr>'.$bottommsg.'</tfoot>';
?>
</table>
</div>
<?php
	}
?>

<?php
	//赠品
	if(!empty($cartdata_gifts))
	{
?>
<br />
<div id="div_gifts_title">
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="noborder">
	   <tr><td colspan="3" style="font-size:24px; font-weight:bold; text-align:center;"><?php echo $_SESSION['uc']['CompanyName'];?> 在线订单 - 赠品</td></tr>
	   <tr>
		<td width="28%"><strong>订单号：</strong><? echo $oinfo['OrderSN'];?></td>
		<td ><strong>经 销 商：</strong><? echo $cinfo['ClientCompanyName'];?></td>
		<td width="28%" align="right"><strong>订购时间：</strong><? echo date("Y-m-d H:i",$oinfo['OrderDate']);?></td>		
	  </tr>
</table>
</div>

<div id="div_gifts">
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
			//$tdfield[] = $kk;
		}
	}
	$dwidth = 100/count($tdfield).'%';
	$tdmsg  = str_replace('wid_th',$dwidth,$tdmsg);
	echo $tdmsg;
	?>
  </tr>
   </thead>
   <tbody >
	<?php 
		$alltotal = 0;
		$allnumber = 0;
		$n = 0;
		foreach($cartdata_gifts as $ckey=>$cvar)
		{
			$n++;
			$cvar['PercentPrice'] = $cvar['ContentPrice'];
			$cvar['LineTotal'] = $cvar['ContentNumber'] * $cvar['PercentPrice'];
			$allnumber = $allnumber + $cvar['ContentNumber'];
			$alltotal  = $alltotal + $cvar['ContentNumber'] * $cvar['ContentPrice'];

			$tdmsg = '';
			foreach($tdfield as $kv)
			{
				if($kv == 'NO') $cvar[$kv] = $n;
				if(@in_array($kv,$rightarr)) $alignmsg = ' align="right" '; elseif($kv == 'Units') $alignmsg = ' align="center" '; else $alignmsg = '';
				$tdmsg .= '<td '.$alignmsg.' >'.$cvar[$kv].'</td>';			
			}
			echo '<tr>'.$tdmsg.'</tr>';
		}
		echo '</tbody>';

		$alltotal  = number_format($alltotal,2,'.',',');
		$totallinemsg = $totallinepage = '';
		foreach($tdfield as $kk=>$kv)
		{
			if(count($cartdata_gifts)/$paper_height > 6/1000)
			{
				//页小计
				if($kk==0) $totallinepage .= '<td><strong>页小计：</strong></td>';
				elseif($kv=="ContentNumber") $totallinepage .= '<td align="right" tdata="subSum" format="#.##"><strong> ######</strong></td>';
				elseif($kv=="ContentPrice") $totallinepage .= '<td align="right" tdata="subSum" format="#,##0.00"><strong>###</strong></td>';
				elseif($kv=="LineTotal") $totallinepage .= '<td align="right" tdata="subSum" format="#,##0.00"><strong>###</strong></td>';
				else $totallinepage .= '<td>&nbsp;</td>';
			}
			
			if($kk==0) $totallinemsg .= '<td><strong>合计：</strong></td>';
			elseif($kv=="ContentNumber") $totallinemsg .= '<td align="right"><strong> '.$allnumber.'</strong></td>';
			elseif($kv=="LineTotal") $totallinemsg .= '<td align="right"><strong> '.$alltotal.'</strong></td>';
			else $totallinemsg .= '<td></td>';
		}
		
		$bottommsg = '  <tr>
    <td colspan="'.(count($tdfield)).'" ><strong>收货公司：</strong>'.$oinfo['OrderReceiveCompany'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>联系人：</strong>'.$oinfo['OrderReceiveName'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>电话：</strong>'.$oinfo['OrderReceivePhone'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>交货日期：</strong>'.$oinfo['DeliveryDate'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>地址：</strong>'.$oinfo['OrderReceiveAdd'].'</td>
  </tr>';
		
		if(!empty($oinfo['OrderRemark'])) $bottommsg .= '<tr>
    <td  colspan="'.(count($tdfield)).'"><strong>备注：</strong>'.$oinfo['OrderRemark'].'</td>
  </tr>';
		
		if(!empty($valuearr['CompanyInfoPrint']) && $valuearr['CompanyInfoPrint']=="1")
		{
			$bottommsg .= '<tr >
    <td  colspan="'.(count($tdfield)).'"><strong>联系人：</strong>'.$cominfo['CompanyContact'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>电话：</strong>'.$cominfo['CompanyPhone'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>传真：</strong>'.$cominfo['CompanyFax'].'&nbsp;&nbsp;&nbsp;&nbsp;<strong>地址：</strong>'.$cominfo['CompanyAddress'].'</td>
  </tr>';
		}
		$bottommsg .= '<tr class="noborder">
		<td colspan="'.(count($tdfield)/2).'" ><strong>操作员：</strong>'. $_SESSION['uinfo']['usertruename'].'</td>
		<td align="right" colspan="'.ceil(count($tdfield)/2).'" ><strong>打印日期：</strong>'.date("Y-m-d H:i").'</td>
	  </tr>';
		
		echo '<tfoot><tr>'.$totallinepage.'</tr><tr>'.$totallinemsg.'</tr>'.$bottommsg.'</tfoot>';
?>
</table>
</div>
<?php }?>