<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".intval($in['ID'])." limit 0,1");

	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$cinfo['ConsignmentOrder']."' limit 0,1");

	$clientinfo = $db->get_row("SELECT c.ClientID,c.ClientName,c.ClientCompanyName,c.ClientTrueName,c.ClientPhone,c.ClientMobile,c.ClientAdd FROM ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany = ".$_SESSION['uinfo']['ucompany']." and c.ClientID=".$oinfo['OrderUserID']."  and s.SalerID=".$_SESSION['uinfo']['userid']." limit 0,1");

	if(empty($clientinfo))
	{
		echo '<p>&nbsp;</p><p>参数错误！<a href="javascript:history.back(-1)">点此返回</a></p>';
		exit;
	}

	$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,i.ContentNumber from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_out_library i on c.ID=i.CartID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.ConsignmentID=".$cinfo['ConsignmentID']." and i.ConType='c' order by c.ID asc");
	foreach($cartdata as $cv)
	{
		$cidarr[] = $cv['ContentID'];
	}
	$cartdatag = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,i.ContentNumber from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_out_library i on c.ID=i.CartID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.ConsignmentID=".$cinfo['ConsignmentID']." and i.ConType='g' order by c.ID asc");
	if(!empty($cartdatag))
	{
		foreach($cartdatag as $cv)
		{
			$cidarr[] = $cv['ContentID'];
		}
	}
	$cidmsg  = implode(",", $cidarr);
	$cartinfo = $db->get_results("SELECT ID,Coding,Casing,Units FROM ".DATATABLE."_order_content_index where CompanyID = ".$_SESSION['uinfo']['ucompany']." and ID in (".$cidmsg.")  ORDER BY ID DESC");
	foreach($cartinfo as $ci)
	{
		$pinfo[$ci['ID']] = $ci;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
</head>

<body>        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			 <input type="hidden" name="ConsignmentID" id="ConsignmentID" value="<? echo $cinfo['ConsignmentID'];?>" />
		   
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="consignment.php">发货</a> &#8250;&#8250; <a href="#">发货单详细</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px;"><ul><li ><a href="javascript:void(0);" onclick="javascript:window.close();">关 闭</a></li></ul></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset class="fieldsetstyle">			
			<legend>发货信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  >               
                <tr>
                  <td width="12%" bgcolor="#F0F0F0"><div align="right">客户订单：</div></td>
                  <td width="38%"><label>&nbsp; <a href="order_manager.php?SN=<? echo $cinfo['ConsignmentOrder'];?>">
                    <? 	echo $cinfo['ConsignmentOrder'];?>
					</a></label></td>
                  <td width="12%" bgcolor="#F0F0F0"><div align="right">物流公司：</div></td>
                  <td width="38%">&nbsp; 
                    <? 
					if(empty($cinfo['ConsignmentLogistics']) || $cinfo['ConsignmentLogistics']=="0")
					{
						echo '上门自提';
					}else{
						$logisticsarr = $db->get_row("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsPinyi,LogisticsContact,LogisticsPhone FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID=".$cinfo['ConsignmentLogistics']." ORDER BY LogisticsID DESC Limit 0,1");
						echo $logisticsarr['LogisticsName']." (".$logisticsarr['LogisticsPhone'].")";
					}
					?>
                  </td>
                </tr>

                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">运单号：</div></td>
                  <td bgcolor="#FFFFFF"><label>&nbsp;<? echo $cinfo['ConsignmentNO'];?></label></td>
                  <td  bgcolor="#F0F0F0"><div align="right">发货经办人：</div></td>
                  <td bgcolor="#FFFFFF"><label>&nbsp;  <? echo $cinfo['ConsignmentMan'];?></label></td>
                </tr>  
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">发货时间：</div></td>
                  <td bgcolor="#FFFFFF"><label>&nbsp; <? echo $cinfo['ConsignmentDate'];?> </label></td>
                  <td  bgcolor="#F0F0F0"><div align="right">运费/付款方式：</div></td>
                  <td bgcolor="#FFFFFF"> &nbsp; <?					
						if($cinfo['ConsignmentMoneyType'] == "1") echo '已付'; else echo '到付';
					?>
                &nbsp;¥ <? echo $cinfo['ConsignmentMoney'];?> 元</td>
                 </td>
                </tr>  
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">备注/说明：</div></td>
                  <td bgcolor="#FFFFFF" colspan="3"><label><? echo nl2br($cinfo['ConsignmentRemark']);?> </label></td>
                </tr> 
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">操作员：</div></td>
                  <td bgcolor="#FFFFFF" ><label>&nbsp; <? echo $cinfo['ConsignmentUser'];?></label></td>
				  <td align="center" colspan="2"></td>
                </tr> 
            </table>
          </fieldset> 
		  

			<br style="clear:both;" />
			<fieldset class="fieldsetstyle">			
			<legend>物流跟踪</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  >                         
                <tr>
                  <td >
<?php
if(!empty($logisticsarr['LogisticsPinyi']) && !empty($cinfo['ConsignmentNO']))
{
	 include("../m/arr_kuaidi.php");
	$typeCom = $logisticsarr['LogisticsPinyi'];//快递公司
	$typeNu = $cinfo['ConsignmentNO'];  //快递单号
	$AppKey = KUDAIDIAPPKEY;

	if (in_array($logisticsarr['LogisticsPinyi'], $arr_html_kuaidi))
	{
		$url  = 'http://www.kuaidi100.com/applyurl?key='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'';
	}else{
		$url = 'http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';
	}
	
	//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
	$powered = '<div style="display:none;">查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 </div>';

	//优先使用curl模式发送数据
	if (function_exists('curl_init') == 1){
	  $curl = curl_init();
	  curl_setopt ($curl, CURLOPT_URL, $url);
	  curl_setopt ($curl, CURLOPT_HEADER,0);
	  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	  curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	  $get_content = curl_exec($curl);
	  curl_close ($curl);
	}else{
	  include("../class/snoopy.php");
	  $snoopy = new snoopy();
	  $snoopy->referer = 'http://www.google.com/';//伪装来源
	  $snoopy->fetch($url);
	  $get_content = $snoopy->results;
	}
	if (in_array($logisticsarr['LogisticsPinyi'], $arr_html_kuaidi))
	{
		echo '<iframe name="kudiaurl"  frameborder="0" scrolling="no" width="70%" height="350" scrolling="auto" src="'.$get_content.'" ></iframe> ';
	}else{
		print_r($get_content . '<br/>' . $powered);
	}
}else{
	echo '<p ><font color=red>无法获取物流状态! 请确认物流公司编号和运单号正确! </font></p><p ><a href="kuaidi_search.php" target="_blank"  title="快递查询"><img src="img/c1.png" alt="快递查询" /></a>&nbsp; &nbsp; &nbsp; &nbsp; <a href="wuliu_search.php" target="_blank"  title="物流查询"><img src="img/c2.png" alt="物流查询" /></a></p>';
}
?>
				  
				  </td>
                </tr> 
            </table>
          </fieldset>  
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>发货明细单</legend>
<table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr class="bottomline">
    <td width="6%" >&nbsp;行号</td>
	<td width="12%">编号/货号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色</td>
	<td width="12%">&nbsp;规格</td>
	<td width="12%">&nbsp;包装</td>
    <td width="6%" >数量</td>
    <td width="5%" >单位</td>
  </tr>
   </thead>
   <tbody id="listcartdataid">
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata as $ckey=>$cvar)
	{
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>
   <tr class="bottomline" id="linegoods_<? echo $cvar['ID'];?>"   >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;	<? echo $pinfo[$cvar['ContentID']]['Coding'];?></td>
    <td><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<? if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?>  </td>
    <td>&nbsp;<? if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td>&nbsp;<? if(!empty($pinfo[$cvar['ContentID']]['Casing'])) echo $pinfo[$cvar['ContentID']]['Casing'];?> </td>
    <td >&nbsp;<? echo $cvar['ContentNumber'];?></td>
    <td >&nbsp;<? echo $pinfo[$cvar['ContentID']]['Units'];?>	</td>
  </tr>
   <? 
		}
	if(!empty($cartdatag))
	{
			foreach($cartdatag as $ckey=>$cvar)
			{
	?>
   <tr class="bottomline" id="linegoods_g_<? echo $cvar['ID'];?>" bgcolor="#efefef" title="赠品" >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;	<? echo $pinfo[$cvar['ContentID']]['Coding'];?></td>
    <td><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<? if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?>  </td>
    <td>&nbsp;<? if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td>&nbsp;<? if(!empty($pinfo[$cvar['ContentID']]['Casing'])) echo $pinfo[$cvar['ContentID']]['Casing'];?> </td>
    <td >&nbsp;<? echo $cvar['ContentNumber'];?></td>
    <td >&nbsp;<? echo $pinfo[$cvar['ContentID']]['Units'];?>	</td>
  </tr>
	<?
			}
	}
	?>

   <tr class="bottomline" id="linegoods_"   >
    <td height="30">合计：</td>
	<td >&nbsp;	</td>
    <td>&nbsp;<strong><? echo $n-1;?>&nbsp;种</strong></td>
    <td>&nbsp;  </td>
    <td>&nbsp; </td>
    <td>&nbsp; </td>
    <td >&nbsp;<strong><? echo $allnumber;?></strong></td>
    <td >&nbsp;</td>
  </tr>
   </tbody>
</table>
		  </fieldset>
		 <br style="clear:both;" />


            <fieldset  class="fieldsetstyle">
		<legend>收货信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
                 <tr>
                  <td width="12%" bgcolor="#F0F0F0"><div align="right">收货人：</div></td>
                  <td width="38%" bgcolor="#FFFFFF"><label> &nbsp;<? echo $cinfo['InceptMan'];?></label></td>
				  <td width="12%" bgcolor="#F0F0F0"><div align="right">收货公司：</div></td>
                  <td width="38%" bgcolor="#FFFFFF" >&nbsp;<? echo $cinfo['InceptCompany'];?></td>
                </tr>               

                <tr>
				  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $cinfo['InceptPhone'];?></td>
				  <td bgcolor="#F0F0F0"><div align="right">收货人地址：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $cinfo['InceptAddress'];?></td>
                </tr>
              </table>
		  </fieldset>

			<br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="#" onclick="javascript:window.location.href= 'print.php?u=print_consignment&ID=<? echo $cinfo['ConsignmentID'];?>'">打 印</a></li><li><a href="#" onclick="window.close();">关 闭</a></li></ul></div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom_content.php");?>
</body>
</html>
