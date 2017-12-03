<?php
$menu_flag = "consignment";
include_once ("header.php");

include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
include_once ("../class/letter.class.php");
include_once ("arr_data.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}
$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

/*****************************/
if($in['m']=="getinceptvaue")
{
	$omsg = "application/json;charset=UTF-8";
	$bodymsg = '';
	$orderinfo = $db->get_row("SELECT OrderID,OrderReceiveName,OrderReceiveAdd,OrderReceiveCompany,OrderReceivePhone,OrderUserID FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID='".$in['OrderID']."' limit 0,1");
	if(!empty($orderinfo))
	{
		$omsg = '{"backtype":"ok", "InceptMan":"'.$orderinfo['OrderReceiveName'].'", "InceptAddress":"'.$orderinfo['OrderReceiveAdd'].'", "InceptCompany":"'.$orderinfo['OrderReceiveCompany'].'", "InceptPhone":"'.$orderinfo['OrderReceivePhone'].'", "InceptUser":"'.$orderinfo['OrderUserID'].'"}';
	}else{
		$omsg = '{"backtype":"error"}';
	}
	echo $omsg;
	exit();
}

if($in['m']=="loadcartlist")
{
	$bodymsg = '';
	$valuearr = get_set_arr('product');
	$orderinfo = $db->get_row("SELECT OrderID,OrderUserID FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderID='".$in['ID']."' limit 0,1");
	if(!empty($orderinfo))
	{
		$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentNumber,c.ContentSend,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$orderinfo['OrderID']." order by i.SiteID asc,c.ID asc");

		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
		{
			foreach($cartdata as $ck=>$cv)
			{
				$conidarr[] = $cv['ContentID'];
			}
			$conidmsg	 = implode(",",$conidarr);
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
    <td>&nbsp;'.$cvar['ContentColor'].'</td>
    <td>&nbsp;'.$cvar['ContentSpecification'].'</td>
    <td>&nbsp;'.$cvar['Casing'].'</td>
	'.$lmsg.'
    <td ><input name="cart_num_c[]" id="cart_num_c_'.$cvar['ID'].'" type="text" value="'.$lnumber .'" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1"  onBlur="checknumber(\'cart_num_c_'.$cvar['ID'].'\',\''.$checnumber.'\');"  />&nbsp;'.$cvar['Units'].'</td>
    <td  align="center">[<a href="javascript:void(0)" onclick="remove_library_line(\'c_'.$cvar['ID'].'\')">移除</a>]</td>
  </tr>';
		}

		//赠品
		$giftdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,c.ContentNumber,c.ContentSend,i.Coding,i.Units,i.Casing from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$orderinfo['OrderID']." order by i.SiteID asc,c.ID asc");
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
		<td>&nbsp;'.$cvar['ContentColor'].'</td>
		<td>&nbsp;'.$cvar['ContentSpecification'].'</td>
		<td>&nbsp;'.$cvar['Casing'].'</td>
		'.$lmsg.'
		<td ><input name="cart_num_g[]" id="cart_num_g_'.$cvar['ID'].'" type="text" value="'.$gnumber .'" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" class="numberinput1" onBlur="checknumber(\'cart_num_g_'.$cvar['ID'].'\',\''.$gnumber.'\');"  />&nbsp;'.$cvar['Units'].'</td>
		<td  align="center">[<a href="javascript:void(0)" onclick="remove_library_line(\'g_'.$cvar['ID'].'\')">移除</a>]</td>
	  </tr>';
			}
		}

		$omsg = $bodymsg;
	}else{
		$omsg = 'error';
	}
	echo $omsg;
	exit();
}

if($in['m']=="loadorderlist")
{
	if(!intval($in['ID'])) exit('请选择客户');

	$orderlistuser = $db->get_results("SELECT OrderID,OrderSN,OrderTotal,OrderSendStatus,OrderStatus,OrderPayType,OrderPayStatus FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$in['ID']." and (OrderSendStatus=1 or OrderSendStatus=3) and (OrderStatus=1 or OrderStatus=2 or OrderStatus=3 or OrderStatus=5) order by OrderSendStatus asc,OrderID desc limit 0,200");
	if(empty($orderlistuser)) exit('该用户，无待发货订单(新订单需审核后才能发货)');
	$bodymsg = '';
	$headermsg = '<table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0">
                        <td width="8%">&nbsp;</td>
                        <td width="30%"><strong>&nbsp;订单号</strong></td>
                        <td width="25%"><strong>&nbsp;订单金额</strong></td>
                        <td ><strong>&nbsp;发货状态</strong></td>
                      </tr>';
	foreach($orderlistuser as $olvar)
	{
		if($olvar['OrderPayStatus'] < 2 && in_array($olvar['OrderPayType'],$paytypeidarr)) continue;

		$bodymsg .= '<tr height="28" id="selected_line_'.$olvar['OrderID'].'">
                        <td ><input id="data_ConsignmentOrder_'.$olvar['OrderID'].'" name="data_ConsignmentOrder" type="radio" onfocus="selectorderline(\''.$olvar['OrderID'].'\')" value="'.$olvar['OrderSN'].'" /></td>
                        <td onclick="selectorderline(\''.$olvar['OrderID'].'\')" >&nbsp;'.$olvar['OrderSN'].'</td>
                        <td onclick="selectorderline(\''.$olvar['OrderID'].'\')">&nbsp;¥'.$olvar['OrderTotal'].'</td>
                        <td onclick="selectorderline(\''.$olvar['OrderID'].'\')">&nbsp;'.$send_status_arr[$olvar['OrderSendStatus']].'</td>
                      </tr>';
	}

	$endmsg = '</table>';	
	echo $headermsg.$bodymsg.$endmsg;
	exit();
}

if($in['m']=="logisticslist")
{
	if(!intval($in['ID'])) exit('error');

	$clientinfo = $db->get_row("SELECT ClientID,ClientName,ClientPay,ClientConsignment FROM ".DATATABLE."_order_client  where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['ID'])." limit 0,1");
	if(empty($clientinfo['ClientConsignment']) || $clientinfo['ClientConsignment']== "null")
	{
		exit('error');
	}else{

		$bodymsg = '';
		$headermsg = '<select name="ConsignmentLogistics_nomal" id="ConsignmentLogistics_nomal" >
                    <option value="0"> ⊙ 请选择物流货运公司</option>
					<option value="0"> ┠- 上门自提</option>';

		$logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsPinyi,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID in (".$clientinfo['ClientConsignment'].") ORDER BY LogisticsID ASC Limit 0,100");
		foreach($logisticsarr as $olvar)
		{
			$bodymsg .= '<option value="'.$olvar['LogisticsID'].'">  ┠- '.$olvar['LogisticsName'].' ( '.$olvar['LogisticsAddress'].' ) </option>';
		}

		$endmsg = '</select>';
		echo $headermsg.$bodymsg.$endmsg;
		exit();
	}
}

if($in['m']=="setSendFlag")
{
	if(!intval($in['ConsignmentID'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$upsql =  "update ".DATATABLE."_order_consignment set ConsignmentFlag=2 where ConsignmentID = ".$in['ConsignmentID']." and ConsignmentCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{		
		$loinfo = $db->get_row("SELECT ConsignmentID,ConsignmentOrder,ConsignmentFlag FROM ".DATATABLE."_order_consignment where ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".$in['ConsignmentID']." limit 0,1");
		if(!empty($loinfo['ConsignmentOrder']))
		{
			$upinfo  = $db->get_row("SELECT OrderID,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderSN = '".$loinfo['ConsignmentOrder']."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
			$sendline = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where ContentSend < ContentNumber and CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			$sendlineg = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart_gifts where ContentSend < ContentNumber and CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			if($sendline['allrow'] > 0 || $sendlineg['allrow'] > 0)
			{
				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
			}else{
				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=4 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
			}
			$db->query("update ".DATATABLE."_order_orderinfo set OrderStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=2 ");			
			
			$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$upinfo['OrderID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '收货并确认', '管理方确认收到货...')";
			$db->query($sqlin);		
		}
		exit('ok');
	}else{
		exit('设置无变化!');
	}
}

if($in['m']=="delete")
{
	if(!intval($in['ID'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentID = ".$in['ID']." and ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$sqll = "select CartID,ContentNumber,ConType from ".DATATABLE."_order_out_library where CompanyID=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".$InfoData['ConsignmentID']." ";
	$data_cart = $db->get_results($sqll);
	$backdata['coninfo'] = $InfoData;
	$backdata['library'] = $data_cart;
	$infodatamsg = serialize($backdata);
	unset($backdata);

	$upsql =  "delete from ".DATATABLE."_order_consignment where ConsignmentID = ".$in['ID']." and ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentFlag = 0";	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_consignment.php?m=delete&ID=".$in['ID']."','删除发货单(".$in['ID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		
		$upinfo  = $db->get_row("SELECT OrderID,OrderSN,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderSN = '".$InfoData['ConsignmentOrder']."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
		if(!empty($upinfo['OrderID']))
		{		
			$infoline = $db->get_row("SELECT count(*) as allrow FROM ".DATATABLE."_order_consignment where  ConsignmentOrder = '".$upinfo['OrderSN']."' and ConsignmentCompany = ".$_SESSION['uinfo']['ucompany']." ");
			if(empty($infoline['allrow']))
			{
				$db->query("update ".DATATABLE."_order_cart set ContentSend=0 where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
				$db->query("update ".DATATABLE."_order_cart_gifts set ContentSend=0 where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
				foreach($data_cart as $lv)
				{					
					$larr[$lv['ConType']][$lv['CartID']] = $lv['ContentNumber'];
				}				

				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=1 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
				$db->query("update ".DATATABLE."_order_orderinfo set OrderStatus=1 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=2 ");
			}else{

				foreach($data_cart as $lv)
				{
					if($lv['ConType'] == "c")
					{
						$db->query("update ".DATATABLE."_order_cart set ContentSend=ContentSend-".$lv['ContentNumber']." where ID=".$lv['CartID']." and CompanyID=".$_SESSION['uinfo']['ucompany']."");
						$larr['c'][$lv['CartID']] = $lv['ContentNumber'];
					}else{
						$db->query("update ".DATATABLE."_order_cart_gifts set ContentSend=ContentSend-".$lv['ContentNumber']." where ID=".$lv['CartID']." and CompanyID=".$_SESSION['uinfo']['ucompany']."");
						$larr['g'][$lv['CartID']] = $lv['ContentNumber'];
					}
				}
				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
			}

			$valuearr = get_set_arr('product');
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$sqlc   = "select ID,OrderID,ContentID,ContentColor,ContentSpecification from ".DATATABLE."_order_cart where OrderID = ".$upinfo['OrderID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
				$data_c = $db->get_results($sqlc);
			
				$tykey  = str_replace($fp,$rp,base64_encode("统一"));
				foreach($data_c as $dvar)
				{
					if(empty($larr['c'][$dvar['ID']])) continue;
					$db->query("update ".DATATABLE."_order_number set ContentNumber=ContentNumber+".$larr['c'][$dvar['ID']]." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");
			
					if(strlen($dvar['ContentColor']) || strlen($dvar['ContentSpecification']))
					{
						if(!strlen($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
						if(!strlen($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
						$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=ContentNumber+".$larr['c'][$dvar['ID']]." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
					}
					$num = -($larr['c'][$dvar['ID']]);
					$ac  = 'delete_con_'.$in['ID'];
					$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$dvar['OrderID']},{$num},'".$ac."')");
				}

				$sqlg   = "select ID,OrderID,ContentID,ContentColor,ContentSpecification from ".DATATABLE."_order_cart_gifts where OrderID = ".$upinfo['OrderID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
				$data_g = $db->get_results($sqlg);
				foreach($data_g as $dvar)
				{
					if(empty($larr['g'][$dvar['ID']])) continue;
					$db->query("update ".DATATABLE."_order_number set ContentNumber=ContentNumber+".$larr['g'][$dvar['ID']]." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");
			
					if(strlen($dvar['ContentColor']) || strlen($dvar['ContentSpecification']))
					{
						if(!strlen($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
						if(!strlen($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
						$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=ContentNumber+".$larr['g'][$dvar['ID']]." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
					}
					$num = -($larr['g'][$dvar['ID']]);
					$ac  = 'delete_con_'.$in['ID'];
					$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$dvar['OrderID']},{$num},'".$ac."')");
				}
			}
		}

		$db->query("delete from ".DATATABLE."_order_out_library where CompanyID=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".$InfoData['ConsignmentID']." ");
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


/***********save**************/
if($in['m']=="content_add_save")
{	
	if((empty($in['data_ConsignmentLogistics']))&&($in['data_ConsignmentLogistics']!='0')){$in['data_ConsignmentLogistics'] = $in['ConsignmentLogistics_nomal'];}

	//if((!empty($in['ConsignmentLogistics_nomal']))||($in['ConsignmentLogistics_nomal']==0)) $in['data_ConsignmentLogistics'] = $in['ConsignmentLogistics_nomal'];
	if(empty($in['cart_id_c']) && empty($in['cart_id_g'])) exit('请录入发货明细单!');
	$isdata = false;
	$iswwc  = false;

	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('ConsignmentCompany', $_SESSION['uinfo']['ucompany']);
	$data_->addData('InputDate', time());
	$data_->addData('ConsignmentUser', $_SESSION['uinfo']['username']);

	$insert_id = $data_->dataInsert ("_order_consignment");
	if(!empty($insert_id))
	{
		if(!empty($in['data_ConsignmentOrder']))
		{
			$valuearr = get_set_arr('product');
			$upinfo   = $db->get_row("SELECT OrderID,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderSN = '".$in['data_ConsignmentOrder']."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");

			$librarydata = $db->get_results("select ID,ContentID,ContentColor,ContentSpecification,ContentNumber,ContentSend from ".DATATABLE."_order_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."  order by ID asc");
			$librarydatag = $db->get_results("select ID,ContentID,ContentColor,ContentSpecification,ContentNumber,ContentSend from ".DATATABLE."_order_cart_gifts where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."  order by ID asc");

			if(!empty($librarydata))
			{
				foreach($librarydata as $lkey=>$lvar)
				{
					$larr[$lvar['ID']]   = $lvar['ContentNumber'] - $lvar['ContentSend'];
					$idarr[$lvar['ID']]  = $lvar['ContentID'];
					$kidarr[$lvar['ID']] = make_kid($lvar['ContentID'],$lvar['ContentColor'],$lvar['ContentSpecification']);
				}
			}else{
				$idarr = $kidarr = array();
			}
			//赠品
			if(!empty($librarydatag))
			{
				foreach($librarydatag as $lkey=>$lvar)
				{
					$larrg[$lvar['ID']]   = $lvar['ContentNumber'] - $lvar['ContentSend'];
					$idarrg[$lvar['ID']]  = $lvar['ContentID'];
					$kidarrg[$lvar['ID']] = make_kid($lvar['ContentID'],$lvar['ContentColor'],$lvar['ContentSpecification']);
				}
			}else{
				$idarrg = $kidarrg = array();
			}
			$idarrall  = $idarr + $idarrg;

			//库存检查
			if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
			{
				$conidmsg   = implode(",",$idarrall);
				$data_all   = $db->get_results("select ContentID,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$conidmsg.")");
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

			//正品
			if(!empty($librarydata))
			{
				if(!empty($in['cart_id_c']))
				{
					for($i=0;$i<count($in['cart_num_c']);$i++)
					{
						$in['cart_num_c'][$i] = intval($in['cart_num_c'][$i]);
						if(!empty($in['cart_num_c'][$i]))
						{
							if($in['cart_num_c'][$i] > $larr[$in['cart_id_c'][$i]]) $in['cart_num_c'][$i] = $larr[$in['cart_id_c'][$i]];
							if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
							{
								$skid = $kidarr[$in['cart_id_c'][$i]];
								if($in['cart_num_c'][$i] > $libarr[$skid])
								{
									$in['cart_num_c'][$i] = $libarr[$skid];
									$iswwc = true;									
								}
								$libarr[$skid] = $libarr[$skid] - $in['cart_num_c'][$i]; //当前库存数量
							}
							if($in['cart_num_c'][$i] > 0)
							{
								$db->query("insert into ".DATATABLE."_order_out_library(CompanyID,ConsignmentID,OrderID,CartID,ContentID,ContentNumber,ConType) values(".$_SESSION['uinfo']['ucompany'].",".$insert_id.",".$upinfo['OrderID'].",".$in['cart_id_c'][$i].",".intval($in['cart_cid_c'][$i]).",".$in['cart_num_c'][$i].",'c')");
								$db->query("update ".DATATABLE."_order_cart set ContentSend=ContentSend+".$in['cart_num_c'][$i]." where ID=".$in['cart_id_c'][$i]." and CompanyID=".$_SESSION['uinfo']['ucompany']." ");
								$isdata = true;
							}
						}
					}
				}
			}	
			
			//赠品
			if(!empty($librarydatag))
			{
				if(!empty($in['cart_id_g']))
				{
					for($i=0;$i<count($in['cart_num_g']);$i++)
					{
						$in['cart_num_g'][$i] = intval($in['cart_num_g'][$i]);
						if(!empty($in['cart_num_g'][$i]))
						{
							if($in['cart_num_g'][$i] > $larrg[$in['cart_id_g'][$i]]) $in['cart_num_g'][$i] = $larrg[$in['cart_id_g'][$i]];
							if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
							{								
								$skid = $kidarrg[$in['cart_id_g'][$i]];
								if($in['cart_num_g'][$i] > $libarr[$skid])
								{
									$in['cart_num_g'][$i] = $libarr[$skid];
									$iswwc = true;
								}
								$libarr[$skid] = $libarr[$skid] - $in['cart_num_g'][$i]; //当前库存数量
							}
							if($in['cart_num_g'][$i] > 0)
							{
								$db->query("insert into ".DATATABLE."_order_out_library(CompanyID,ConsignmentID,OrderID,CartID,ContentID,ContentNumber,ConType) values(".$_SESSION['uinfo']['ucompany'].",".$insert_id.",".$upinfo['OrderID'].",".$in['cart_id_g'][$i].",".intval($in['cart_cid_g'][$i]).",".$in['cart_num_g'][$i].",'g')");
								$db->query("update ".DATATABLE."_order_cart_gifts set ContentSend=ContentSend+".$in['cart_num_g'][$i]." where ID=".$in['cart_id_g'][$i]." and CompanyID=".$_SESSION['uinfo']['ucompany']." ");
								$isdata = true;
							}
						}
					}
				}
			}

			if($isdata == false)
			{
				$db->query("DELETE FROM ".DATATABLE."_order_consignment where ConsignmentID=".$insert_id." and ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." limit 1");
				exit('没有可发货的库存商品!');
			}
			$in['ID']  = $upinfo['OrderID'];
			$in['CID'] = $insert_id;
			chang_number($db,$in,'SendCon');

			if(!empty($upinfo['OrderID']))
			{			
				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=2 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
				$db->query("update ".DATATABLE."_order_orderinfo set OrderStatus=2 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderStatus=1 ");			
			    order_deduct($db,$upinfo['OrderID']);// zjb 已开票出库计算提成
			}
			$sendline = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart where ContentSend < ContentNumber and CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			$sendlineg = $db->get_row("select count(*) as allrow from ".DATATABLE."_order_cart_gifts where ContentSend < ContentNumber and CompanyID = ".$_SESSION['uinfo']['ucompany']." and OrderID=".$upinfo['OrderID']."");
			if($sendline['allrow'] > 0 || $sendlineg['allrow'] > 0)
			{
				$db->query("update ".DATATABLE."_order_orderinfo set OrderSendStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$_SESSION['uinfo']['ucompany']." ");
			}

			$sqlin = "insert into ".DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$_SESSION['uinfo']['ucompany'].", ".$upinfo['OrderID'].", '".$_SESSION['uinfo']['username']."', '".$_SESSION['uinfo']['usertruename']."',".time().", '已发货', '已添加发货单(NO.".$insert_id.")...')";
			$db->query($sqlin);

			if(!empty($in['data_ConsignmentLogistics']) && $in['data_ConsignmentLogistics'] != '0')
			{
				$loinfo = $db->get_row("SELECT LogisticsName,LogisticsPhone FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." and LogisticsID=".$in['data_ConsignmentLogistics']." limit 0,1");
				if(!empty($loinfo['LogisticsName'])) $linkmsg = "由".$loinfo['LogisticsName'].""; else $linkmsg= "";
				$message = "【".$_SESSION['uc']['CompanySigned']."】您订单号:".$in['data_ConsignmentOrder']."的货物已于".$in['data_ConsignmentDate']."日".$linkmsg."发出,请注意查收(运单号:".$in['data_ConsignmentNO']."),退订回复TD";
				sms::get_setsms("2",$in['data_ConsignmentClient'],$message);
			}
		}
		if($iswwc) exit('iswwf'); else exit('ok');
		
	}else{
		exit("保存不成功!");
	}
}

/***********editsave**************/
if($in['m']=="content_edit_save")
{
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('ConsignmentUser', $_SESSION['uinfo']['username']);
	$wheremsg =" where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".$in['ConsignmentID'];

	$update = $data_->dataUpdate("_order_consignment",$wheremsg);
	if(!empty($update))
	{
		exit("ok");
	}else{
		exit("无变化!");
	}
}

/***********save_logistics**************/
if($in['m']=="logistics_add_save")
{
	//$letter  = new letter();
    //$pinyima = strtolower($letter->C($in['data_LogisticsName']));
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	//$data_->addData('LogisticsPinyi', $pinyima);
	$data_->addData('LogisticsCompany', $_SESSION['uinfo']['ucompany']);
	$data_->addData('LogisticsDate', time());

	$insert_id = $data_->dataInsert ("_order_logistics");
	if(!empty($insert_id))
	{
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}


if($in['m']=="logistics_edit_save")
{
	//$letter  = new letter();
    //$pinyima = strtolower($letter->C($in['data_LogisticsName']));
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	//$data_->addData('LogisticsPinyi', $pinyima);
	$wheremsg = " where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID=".$in['LID'];

	$update = $data_->dataUpdate("_order_logistics",$wheremsg);
	if(!empty($update))
	{
		exit("ok");
	}else{
		exit("无变化!");
	}
}

if($in['m']=="delete_logistics")
{
	if(!intval($in['ID'])) exit('非法操作!');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$cinfo = $db->get_row("SELECT count(*) as lrow FROM ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentLogistics=".intval($in['ID'])." ");
	if(!empty($cinfo['lrow'])) exit('该物流公司已在使用，不能删除!');
	
	$upsql =  "delete from ".DATATABLE."_order_logistics where LogisticsID = ".$in['ID']." and LogisticsCompany=".$_SESSION['uinfo']['ucompany'];	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}

//补发短信
if($in['m'] == "send_to_message")
{
	if(empty($in['clientid']) || empty($in['sendcontent']) || empty($in['consignmentid'])) exit('参数错误！');
	if(empty($in['sendmobile'])) exit('该药店资料上没有填写手机号，请先补全！');
	if(!is_phone($in['sendmobile'])) exit('该药店资料上手机号码不正确！');

	if(!empty($in['sendcno'])){
	  $in['sendcontent'] = str_replace('{kuaidi_danhao}', $in['sendcno'], $in['sendcontent']);
	  $db->query("update ".DATATABLE."_order_consignment set ConsignmentNO='".$in['sendcno']."' where ConsignmentID = ".$in['consignmentid']." and ConsignmentCompany=".$_SESSION['uinfo']['ucompany']);
	}

	//$isok = sms::get_setsms("2",intval($in['clientid']),$in['sendcontent']);
	$isok = sms::send_sms($in['sendmobile'],$in['sendcontent'],$in['clientid']);
	if($isok !== false){
		exit('ok');
	}else{
		exit('发送不成功'.$isok);
	}

}

//自动签收
if($in['m'] == "do_Sign_time")
{
	$return=array();
	$return['status']=0;
	$return['message']="修改失败！";
	$res=false;
	if(!empty($in['out_day'])){
		$out_day=(int) $in['out_day'];
		if($out_day ==0) $out_day=15;
		$res=$db->query("update ".DATABASEU.DATATABLE."_order_company set ExpireDays='".$out_day."' where CompanyID = ".$_SESSION['uinfo']['ucompany']);
		if($res){
			$return['status']=1;
			$return['message']="修改成功！";
		}
	}
	
	exit(json_encode($return));

}

//自动取消 do_Cancel_time
if($in['m'] == "do_Cancel_time")
{
	//var_dump($in);exit;
	$return=array();
	$return['status']=0;
	$return['message']="修改失败！";
	$res=false;
	if(!empty($in['AutoCancelTime'])){
		$AutoCancelTime=(int) $in['AutoCancelTime'];
		if($AutoCancelTime ==0) $AutoCancelTime=1440;
		$AutoCancelTime=round(($AutoCancelTime/60),2);
		$res=$db->query("update ".DATABASEU.DATATABLE."_order_company set AutoCancelTime='".$AutoCancelTime."' where CompanyID = ".$_SESSION['uinfo']['ucompany']);
		if($res){
			$return['status']=1;
			$return['message']="修改成功！";
		}
	}
	
	exit(json_encode($return));

}

//管理端设置最小订单金额
if($in['m'] == "do_Order_Amount")
{	
	//var_dump($in);exit;
	$return=array();
	$return['status']=0;
	$return['message']="修改失败！";
	$res=false;
	$Order_Amount=(int) $in['OrderAmount'];
	
	$res=$db->query("update ".DATABASEU.DATATABLE."_order_company set OrderAmount='".$Order_Amount."' where CompanyID = ".$_SESSION['uinfo']['ucompany']);
	if($res){
		$return['status']=1;
		$return['message']="修改成功！";
	}
	
	exit(json_encode($return));
}

function chang_number($db,$in,$ac)
{
	$fp = array('+','/','=','_');
	$rp = array('-','|','DHB',' ');
		
	$valuearr = get_set_arr('product');
	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
	{
		$sqlc     = "select c.OrderID,c.ContentID,c.ContentColor,c.ContentSpecification,l.ContentNumber from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_out_library l on c.ID=l.CartID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".intval($in['ID'])." and l.OrderID=".intval($in['ID'])."  and l.ConsignmentID=".$in['CID']." and l.ConType='c'";
		$data_c = $db->get_results($sqlc);

		$sqlg     = "select c.OrderID,c.ContentID,c.ContentColor,c.ContentSpecification,l.ContentNumber from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_out_library l on c.ID=l.CartID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".intval($in['ID'])." and l.OrderID=".intval($in['ID'])." and l.ConsignmentID=".$in['CID']." and l.ConType='g'";
		$data_g = $db->get_results($sqlg);

		$tykey = str_replace($fp,$rp,base64_encode("统一"));
		if(!empty($in['CID'])) $ac = $ac."_".$in['CID'];
		if(!empty($data_c))
		{
			foreach($data_c as $dvar)
			{			
				if(strlen($dvar['ContentColor']) || strlen($dvar['ContentSpecification']))
				{
					if(!strlen($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
					if(!strlen($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec = str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
					$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=ContentNumber-".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
				}
				$db->query("update ".DATATABLE."_order_number set ContentNumber=ContentNumber-".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");

				$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$dvar['OrderID']},{$dvar['ContentNumber']},'".$ac."')");
			}
		}

		if(!empty($data_g))
		{
			foreach($data_g as $dvar)
			{			
				if(strlen($dvar['ContentColor']) || strlen($dvar['ContentSpecification']))
				{
					if(!strlen($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($dvar['ContentColor']));
					if(!strlen($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($dvar['ContentSpecification']));
					$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=ContentNumber-".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
				}
				$db->query("update ".DATATABLE."_order_number set ContentNumber=ContentNumber-".$dvar['ContentNumber']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$dvar['ContentID']." limit 1");

				$db->query("insert into ".DATATABLE."_order_number_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['uinfo']['ucompany']},{$dvar['ContentID']},{$dvar['OrderID']},{$dvar['ContentNumber']},'".$ac."')");
			}
		}

		$underdata = $db->get_col("select ContentID from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and (ContentNumber < 0 or ContentNumber < OrderNumber)");
		if(!empty($underdata))
		{
			$db->query("update ".DATATABLE."_order_inventory_number set ContentNumber=0 where CompanyID=".$_SESSION['uinfo']['ucompany']."  and ContentNumber < 0 ");
			$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']."  and ContentNumber < OrderNumber ");

			$conidarr = array_unique($underdata);
			foreach($conidarr as $v)
			{
				$allnumber = $db->get_row("select sum(OrderNumber) as onum,sum(ContentNumber) as cnum from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$v."  ");
				$db->query("update ".DATATABLE."_order_number set OrderNumber=".$allnumber['onum']." ,ContentNumber=".$allnumber['cnum']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$v." limit 1");
			}
		}
		$db->query("update ".DATATABLE."_order_number set ContentNumber=0 where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentNumber < 0");
		$db->query("update ".DATATABLE."_order_number set OrderNumber=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderNumber > ContentNumber");
	 }
}

//计算订单提成
function order_deduct($db,$oid){

    $upinfo  = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderStatus,OrderSendStatus FROM ".DATATABLE."_order_orderinfo where OrderID = ".$oid." and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 0,1");
    $productarr  = get_set_arr('product');
    if(!empty($productarr['deduct_type']) && $productarr['deduct_type']=="on")
    {
        $salerrow = $db->get_row("select SalerID from ".DATATABLE."_order_salerclient where CompanyID=".$_SESSION['uinfo']['ucompany']." and ClientID=".$upinfo['OrderUserID']." limit 0,1");
        if(!empty($salerrow['SalerID']))
        {
            $cartarr = $db->get_results("SELECT c.ID,c.ContentID,c.ContentPrice,c.ContentNumber,c.ContentPercent,i.Deduct FROM ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_1 i ON c.ContentID=i.ContentIndexID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$upinfo['OrderID']." ORDER BY c.ID ASC ");
            $alltotal = 0;
            if(!empty($cartarr))
            {
                foreach($cartarr as $cv)
                {
                    if(!empty($cv['Deduct']))
                    {
                        $ptotal  = $cv['ContentPrice'] * $cv['ContentNumber'] * ($cv['ContentPercent'] / 10);
                        $dtotal  = $ptotal * $cv['Deduct'] / 100;
                        if($dtotal > 0)
                        {
                            $alltotal = $alltotal + $dtotal;
                            $db->query("insert into ".DATATABLE."_order_deduct_cart(CartID,CompanyID,ClientID,OrderID,ProductDeduct,ProductTotal,DeductTotal) values(".$cv['ID'].",".$_SESSION['uinfo']['ucompany'].",".$upinfo['OrderUserID'].",".$upinfo['OrderID'].",'".$cv['Deduct']."','".$ptotal."','".$dtotal."')");
                        }
                    }
                }
                if($alltotal > 0)
                {
                    $db->query("insert into ".DATATABLE."_order_deduct(OrderID,OrderSN,CompanyID,ClientID,DeductUser,DeductTotal,DeductDate) values(".$upinfo['OrderID'].", '".$upinfo['OrderSN']."',".$_SESSION['uinfo']['ucompany'].",".$upinfo['OrderUserID'].",".$salerrow['SalerID'].",'".$alltotal."',".time().")");
                }
            }
        }
    }
}

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

exit('非法操作!');
?>