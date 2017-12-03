<?php 
$menu_flag = "consignment";
$pope	   = "pope_form";
include_once ("header.php");
include_once ("arr_data.php");

if(!empty($in['ID']))
{
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
	if($oinfo['OrderPayStatus'] < 2 && in_array($oinfo['OrderPayType'],$paytypeidarr)) exit('此订单还不能发货，<a href="javascript:;" onclick="history.go(-1)">点此返回</a>');
	$scid = $oinfo['OrderUserID'];
}
$valuearr = get_set_arr('product');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/consignment.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#data_ConsignmentDate").datepicker();
	});
</script>
<style type="text/css">
.inputstyle input{
	width: 88%;
}

</style>
</head>

<body>
<?php include_once ("top.php");?>

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="consignment.php">发货</a> &#8250;&#8250; <a href="consignment_add.php">新增发货单</a></div>
   	        </div>
			
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_consignment();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='consignment.php'" />
			</div>      
        </div>

        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>发货信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
			<?php
			if(!empty($in['ID'])){
			?>
			<tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店：</div></td>
                  <td width="55%" bgcolor="#FFFFFF">
				  <input type="hidden" name="data_ConsignmentClient" id="data_ConsignmentClient" value="<?php echo $oinfo['OrderUserID'];?>" /> 
				  <input type="hidden" name="data_ConsignmentOrder" id="data_ConsignmentOrder" value="<?php echo $oinfo['OrderSN'];?>" /> 
				  <label>
				  <?php
					$clientinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientTrueName FROM ".DATATABLE."_order_client where ClientID=".$oinfo['OrderUserID']." and ClientCompany=".$_SESSION['uinfo']['ucompany']." ");
					echo $clientinfo['ClientCompanyName']." (".$clientinfo['ClientName'].")";
				  ?></label></td>
				  <td></td>
			</tr>
			<tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">订单信息：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><strong>
				  <?php
					echo $oinfo['OrderSN']." (<span class=font12h>".$order_status_arr[$oinfo['OrderStatus']]."</span>)";
				  ?></strong></td>
				  <td></td>
			</tr>
			<?php }else{?>
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户（药店）：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <select name="data_ConsignmentClient" id="data_ConsignmentClient" class="select2" style="width:554px;" onChange="javascript: get_orderlist(this.options[this.selectedIndex].value);"  >
                    <option value="0">⊙ 请选择客户（药店）</option>
                    <? 
					$orderintoarr = $db->get_results("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0  ORDER BY ClientCompanyPinyi ASC ");
					if(!empty($oinfo['OrderUserID'])) $scid = $oinfo['OrderUserID']; else $scid = $orderintoarr[0]['ClientID'];
					foreach($orderintoarr as $orderinfovar)
					{
						if($scid == $orderinfovar['ClientID']) $smsg = 'selected="selected"'; else $smsg='';
						echo '<option value="'.$orderinfovar['ClientID'].'" title="'.$orderinfovar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$orderinfovar['ClientCompanyPinyi']).'" '.$smsg.' >'.substr($orderinfovar['ClientCompanyPinyi'],0,1).' - '.$orderinfovar['ClientCompanyName'].'</option>';
					}

					?>
                  </select>
                 <span class="red"> *</span></label></td>
                  <td  bgcolor="#FFFFFF" >&nbsp;可输入名称首字母快速匹配</td>
                </tr> 
				
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">客户订单：</div></td>
                  <td  colspan="2">
				  
				  <div style="width:60%; height:180px; overflow:scroll;" id="showuserorder">
				    <table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0">
                        <td width="8%">&nbsp;</td>
                        <td width="30%"><strong>&nbsp;订单号</strong></td>
                        <td width="25%"><strong>&nbsp;订单金额</strong></td>
                        <td ><strong>&nbsp;发货状态</strong></td>
                      </tr>
					  <?
					  
					  $orderlistuser = $db->get_results("SELECT OrderID,OrderSN,OrderTotal,OrderSendStatus,OrderStatus,OrderPayType,OrderPayStatus FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$scid." and (OrderSendStatus=1 or OrderSendStatus=3) and (OrderStatus=1 or OrderStatus=2 or OrderStatus=3 or OrderStatus=5) order by OrderSendStatus asc,OrderID desc limit 0,100");
					  if(!empty($orderlistuser))
					  {
					  foreach($orderlistuser as $olvar)
					  {
							if(!empty($oinfo['OrderSN']) && ($oinfo['OrderSN'] == $olvar['OrderSN'])){ 
								$chmsg = 'checked="checked"'; 
								$bgmsg = 'bgcolor="#efefef"'; 
							}else{
								$chmsg = ''; 
								$bgmsg = 'bgcolor="#ffffff"';
							}
							if($olvar['OrderPayStatus'] < 2 && in_array($olvar['OrderPayType'],$paytypeidarr)) continue;
					  ?>
                      <tr height="28" id="selected_line_<? echo $olvar['OrderID'];?>" <? echo $bgmsg;?> >
                        <td ><input id="data_ConsignmentOrder_<? echo $olvar['OrderID'];?>" name="data_ConsignmentOrder" type="radio" onfocus="selectorderline('<? echo $olvar['OrderID'];?>')" value="<? echo $olvar['OrderSN'];?>" <? echo $chmsg;?> /></td>
                        <td onclick="selectorderline('<? echo $olvar['OrderID'];?>')" >&nbsp;<? echo $olvar['OrderSN'];?></td>
                        <td onclick="selectorderline('<? echo $olvar['OrderID'];?>')">&nbsp;¥ <? echo $olvar['OrderTotal'];?></td>
                        <td onclick="selectorderline('<? echo $olvar['OrderID'];?>')">&nbsp;<? echo $send_status_arr[$olvar['OrderSendStatus']];?></td>
                      </tr>
                      <? }}else{?>
						<tr><td height="32" align="center" colspan="5">
							该用户，无待发货订单(新订单需审核后才能发货)
						</td></tr>
					  <? }?>
                  </table>
                  <div>				  
				  </td>
                </tr>
				<?php 
				}
				$clinfo = $db->get_row("SELECT ClientID,ClientConsignment FROM ".DATATABLE."_order_client  where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".$scid." limit 0,1");
				
				$last_info = $db->get_row("SELECT ConsignmentLogistics FROM ".DATATABLE."_order_consignment  where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentClient=".$scid." and ConsignmentLogistics<>0 order by ConsignmentID desc limit 0,1");

				if(empty($last_info['ConsignmentLogistics'])) $slid = 0; else $slid = $last_info['ConsignmentLogistics'];

				if(!empty($clinfo['ClientConsignment']))
				{
					  $loginfo = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsPinyi,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID in (".$clinfo['ClientConsignment'].") ORDER BY LogisticsPinyi asc, LogisticsID ASC Limit 0,500");
				}
				if(empty($loginfo))
				{
					$stymsg = '';
				?>
                <tr id="logistics_nomal" style="display:none;">
                  <td bgcolor="#F0F0F0"><div align="right">常用物流公司：</div></td>
                  <td id="show_logistics_nomal">
                  <select name="ConsignmentLogistics_nomal" id="ConsignmentLogistics_nomal" class="select2" style="width:554px;" >
                    <option value=""> ⊙ 请选择物流货运公司</option>
					<option value="0"> ┠- 上门自提</option>
                  </select>
                  <span class="red">*</span></td>
                  <td>如不在常用物流公司里，可选择[<a href="javascript:void(0);" onclick="show_logisticslistall()">所有物流公司</a>]</td>
                </tr>
				<? 
				}else{
					$stymsg = 'style="display:none;"';
				?>
                <tr id="logistics_nomal" >
                  <td bgcolor="#F0F0F0"><div align="right">常用物流公司：</div></td>
                  <td id="show_logistics_nomal">
                  <select name="ConsignmentLogistics_nomal" id="ConsignmentLogistics_nomal" class="select2" style="width:554px;" >
                    <option value=""> ⊙ 请选择物流货运公司</option>
					<option value="0"> ┠- 上门自提</option>
					<?php 
						foreach($loginfo as $lvar)
						{
							if($slid == $lvar['LogisticsID']) $selectedmsg = 'selected="selected"'; else $selectedmsg = '';
							echo '<option value="'.$lvar['LogisticsID'].'" '.$selectedmsg.' >'.$lvar['LogisticsPinyi'].' -- '.$lvar['LogisticsName'].' </option>';
						}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td>如不在常用物流公司里，可选择[<a href="javascript:void(0);" onclick="show_logisticslistall()">所有物流公司</a>]</td>
                </tr>
				<? }?>
                <tr id="logistics_all" <? echo $stymsg;?> >
                  <td bgcolor="#F0F0F0"><div align="right">物流公司：</div></td>
                  <td>
                  <select name="data_ConsignmentLogistics" id="data_ConsignmentLogistics" class="select2" style="width:554px;">
                    <option value=""> ⊙ 请选择物流货运公司</option>
					<option value="0"> ┠- 上门自提</option>
                    <? 
					$logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsPinyi,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY LogisticsPinyi asc, LogisticsID ASC Limit 0,500");
					foreach($logisticsarr as $logisticsvar)
					{
						$selectedmsg = '';//tubo 修改此处，之前此处逻辑判断容易引起误会
						if(empty($loginfo)){
							if($slid == $logisticsvar['LogisticsID']) $selectedmsg = 'selected="selected"'; else $selectedmsg = '';
						}
						echo '<option value="'.$logisticsvar['LogisticsID'].'" '.$selectedmsg.' >'.$logisticsvar['LogisticsPinyi'].' -- '.$logisticsvar['LogisticsName'].' </option>';
					}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td>物流、托运、快递公司 [<a href="logistics_add.php">新增物流公司</a>]</td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">运单号：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_ConsignmentNO" id="data_ConsignmentNO" />
                    </label></td>
                  <td  bgcolor="#FFFFFF">发货单号，物流跟踪依据</td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">发货经办人：</div></td>
                  <td bgcolor="#FFFFFF">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="inputstyle">
				  <tr>
					<td width="50%">
                    <input type="text" name="data_ConsignmentMan" id="data_ConsignmentMan" style="width:200px;" value="<?php if(!empty($_SESSION['uinfo']['usertruename'])) echo $_SESSION['uinfo']['usertruename'];?>" />
                  </td><td style="padding-left: 37px;"> 
		<?php
			$userdata = $db->get_results("select UserID,UserName,UserTrueName,UserPhone from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserFlag='0' and UserType='M' order by UserID asc limit 0,50");
			if(!empty($userdata))
			{
				echo '<select name="_StorageAttn_default" onchange="this.form.data_ConsignmentMan.value = this.options[this.selectedIndex].value" style="width:200px;">
				<option value="">--请选择--</option>';
				foreach($userdata as $var)
				{
					echo '<option value="'.$var['UserTrueName'].'"> '.$var['UserTrueName'].' </option>';
				}
				echo '</select>';
			}
		?>	  
			</td>
			</tr></table>
				  </td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>  
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">发货时间：</div></td>
                  <td bgcolor="#FFFFFF"><label><input type="text" name="data_ConsignmentDate" id="data_ConsignmentDate" value="<?php echo date("Y-m-d");?>" />
                    <span class="red">*</span></label></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr> 
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">运费付款方式：</div></td>
                  <td bgcolor="#FFFFFF">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="inputstyle">
						  <tr>
							<td width="31%">
							<select name="data_ConsignmentMoneyType" id="data_ConsignmentMoneyType">
							<?										
								foreach($pay_send_arr as $pay_send_key => $pay_send_var)
								{
									echo '<option value="'.$pay_send_key.'">┠- '.$pay_send_var.'</option>';
								}
							?>
							</select>&nbsp;</td>
							<td ><div align="right">运费金额：</div></td>
							<td width="47%"><input type="text" style="width:256px;" name="data_ConsignmentMoney" id="data_ConsignmentMoney"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10"/>&nbsp;元</td>
							<td width="6%">&nbsp;</td>
						  </tr>
						</table>
                 </td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>  
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">备注/说明：</div></td>
                  <td bgcolor="#FFFFFF"><label>
                    <textarea name="data_ConsignmentRemark" rows="2" style="width:550px;"  id="data_ConsignmentRemark"></textarea>
                  </label></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
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
	<td width="12%">&nbsp;药品规格</td>
	<td width="14%">&nbsp;单位</td>
	<?
	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
	{
		echo '<td width="6%" title="实际库存">&nbsp;库存</td>';
	}
	?>
    <td width="8%" >数量</td>
    <td width="4%" align="center" >移除</td>
  </tr>
   </thead>
   <tbody id="listcartdataid">
   <?php
	if(!empty($oinfo['OrderID']))
	{
		$bodymsg = '';
		$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentNumber,c.ContentSend,i.Coding,i.Units,i.Casing,i.Model from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by i.SiteID asc,c.ID asc");
		if(!empty($cartdata))
		{
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				foreach($cartdata as $ck=>$cv)
				{
					$conidarr[] = $cv['ContentID'];
				}
				$conidmsg = implode(",",$conidarr);
				$data_all    = $db->get_results("select ContentID,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$conidmsg.")");
				$data_cs    = $db->get_results("select ContentID,ContentColor,ContentSpec,ContentNumber from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$conidmsg.")");
				foreach($data_all  as $dv)
				{
					$libarr[$dv['ContentID']] = $dv['ContentNumber'];
				}

				if(!empty($data_cs))
				{
					foreach($data_cs  as $dvs)
					{
						$kid = make_kid2($dvs['ContentID'],$dvs['ContentColor'],$dvs['ContentSpec']);
						$libarr[$kid] = $dvs['ContentNumber'];
					}
				}
			}

			$n=0;
			foreach($cartdata as $ckey=>$cvar)
			{				
				$lnumber = $cvar['ContentNumber'] - $cvar['ContentSend'];
				if($lnumber < 1) continue;
				$n++;
				$checnumber = $lnumber;
				if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
				{
					$kkid = make_kid($cvar['ContentID'],$cvar['ContentColor'],$cvar['ContentSpecification']);
					if(empty($libarr[$kkid])) $libarr[$kkid] = 0;
					$lmsg = '<td>&nbsp;'.$libarr[$kkid].'</td>';
					if($checnumber > $libarr[$kkid]) $checnumber = $libarr[$kkid];
				}else{
					$lmsg = '';
				}

				$bodymsg .= '<tr class="bottomline" id="linegoods_c_'.$cvar['ID'].'"   >
		<td height="30">&nbsp;'.$n.' <input type="hidden" name="cart_id_c[]" id="cart_id_c_'.$cvar['ID'].'" value="'.$cvar['ID'].'" /><input type="hidden" name="cart_cid_c[]" id="cart_cid_c_'.$cvar['ID'].'" value="'.$cvar['ContentID'].'" /></td>
		<td >&nbsp;'.$cvar['Coding'].'</td>
		<td><a href="product_content.php?ID='.$cvar['ContentID'].'" target="_blank">'.$cvar['ContentName'].'</a></td>
		<td>&nbsp;'.$cvar['Model'].'</td>
		<td>&nbsp;'.$cvar['Units'].'</td>
		'.$lmsg.'
		<td ><input name="cart_num_c[]" id="cart_num_c_'.$cvar['ID'].'" type="text" value="'.$lnumber .'" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1"  onBlur="checknumber(\'cart_num_c_'.$cvar['ID'].'\',\''.$checnumber.'\');"  />&nbsp;'.$cvar['Units'].'</td>
		<td  align="center">[<a href="javascript:void(0)" onclick="remove_library_line(\'c_'.$cvar['ID'].'\')">移除</a>]</td>
	  </tr>';
			}
		}
		//赠品
		$giftdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentNumber,c.ContentSend,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by i.SiteID asc,c.ID asc");

		if(!empty($giftdata))
		{
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$libarr = null;
				$conidarr = null;
				foreach($giftdata as $ck=>$cv)
				{
					$conidarr[] = $cv['ContentID'];
				}
				$conidmsg = implode(",",$conidarr);
				$data_all    = $db->get_results("select ContentID,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$conidmsg.")");
				$data_cs    = $db->get_results("select ContentID,ContentColor,ContentSpec,ContentNumber from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$conidmsg.")");
				foreach($data_all  as $dv)
				{
					$libarr[$dv['ContentID']] = $dv['ContentNumber'];
				}

				if(!empty($data_cs))
				{
					foreach($data_cs  as $dvs)
					{
						$kid = make_kid2($dvs['ContentID'],$dvs['ContentColor'],$dvs['ContentSpec']);
						$libarr[$kid] = $dvs['ContentNumber'];
					}
				}
			}
			
			foreach($giftdata as $ckey=>$cvar)
			{
				$gnumber = $cvar['ContentNumber'] - $cvar['ContentSend'];
				if($gnumber < 1) continue;
				$n++;
				$checnumber = $gnumber;

				if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
				{
					$kkid = make_kid($cvar['ContentID'],$cvar['ContentColor'],$cvar['ContentSpecification']);
					if(empty($libarr[$kkid])) $libarr[$kkid] = 0;
					$lmsg = '<td>&nbsp;'.$libarr[$kkid].'</td>';
					if($checnumber > $libarr[$kkid]) $checnumber = $libarr[$kkid];
				}else{
					$lmsg = '';
				}

				$bodymsg .= '<tr class="bottomline" id="linegoods_g_'.$cvar['ID'].'"  bgcolor="#efefef" title="赠品" >
		<td height="30">&nbsp;'.$n.' <input type="hidden" name="cart_id_g[]" id="cart_id_g_'.$cvar['ID'].'" value="'.$cvar['ID'].'" /><input type="hidden" name="cart_cid_g[]" id="cart_cid_g_'.$cvar['ID'].'" value="'.$cvar['ContentID'].'" /></td>
		<td >&nbsp;'.$cvar['Coding'].'</td>
		<td><a href="product_content.php?ID='.$cvar['ContentID'].'" target="_blank">'.$cvar['ContentName'].'</a></td>
		<td>&nbsp;'.$cvar['Model'].'</td>
		<td>&nbsp;'.$cvar['Units'].'</td>
		'.$lmsg.'
		<td ><input name="cart_num_g[]" id="cart_num_g_'.$cvar['ID'].'" type="text" value="'.$gnumber .'" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1" onBlur="checknumber(\'cart_num_g_'.$cvar['ID'].'\',\''.$checnumber.'\');"  />&nbsp;'.$cvar['Units'].'</td>
		<td  align="center">[<a href="javascript:void(0)" onclick="remove_library_line(\'g_'.$cvar['ID'].'\')">移除</a>]</td>
	  </tr>';
			}
		}
		echo $bodymsg;
	}
	?>
   </tbody>
</table>
		  </fieldset>
		 <br style="clear:both;" />

            <fieldset  class="fieldsetstyle">
			<legend>收货信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">收货人：</div></td>
                  <td  bgcolor="#FFFFFF"><label><input type="text" name="data_InceptMan" id="data_InceptMan" value="<? if(!empty($oinfo['OrderUserID'])) echo $oinfo['OrderReceiveName'];?>"  /><span class="red"> *</span></label></td>
                  <td  bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>               
                <tr style="display:none;">
                  <td  bgcolor="#F0F0F0"><div align="right">到达城市：</div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_InceptArea" id="data_InceptArea" value=""  />
                    <span class="red">*</span></label></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收货人地址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_InceptAddress" id="data_InceptAddress" value="<? if(!empty($oinfo['OrderUserID'])) echo $oinfo['OrderReceiveAdd'];?>"   />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">收货人公司：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_InceptCompany" id="data_InceptCompany" value="<? if(!empty($oinfo['OrderUserID'])) echo $oinfo['OrderReceiveCompany'];?>"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_InceptPhone" id="data_InceptPhone" value="<? if(!empty($oinfo['OrderUserID'])) echo $oinfo['OrderReceivePhone'];?>"   />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">可以写多个</td>
                </tr>
              </table>
		  </fieldset>
		 <br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveproductid" type="button" class="button_1" id="saveproductid" value="保 存" onclick="do_save_consignment();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="javascript:window.location.href='consignment.php'" />
		</div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?
	function make_kid($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(strlen($product_color) == 0 && strlen($product_spec) == 0) return $product_id;

		if(strlen($product_color) == 0) $product_color  = '统一';
		if(strlen($product_spec) == 0) $product_spec    = '统一';

		if(strlen($product_color) > 0)
		{
		   $kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
		}
		if(strlen($product_spec) > 0)
		{
		   $kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
		}
		return $kid;
	}

	function make_kid2($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(strlen($product_color) > 0)
		{
		   $kid .= "_p_".$product_color;
		}
		if(strlen($product_spec) > 0)
		{
		   $kid .= "_s_".$product_spec;
		}
		return $kid;
	}
?>