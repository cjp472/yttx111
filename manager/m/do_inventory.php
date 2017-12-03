<?php
$menu_flag = "inventory";
include_once ("header.php");
include_once ("../class/data.class.php");

if(empty($in['m']))
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

if($in['m']=="add_to_select_storage")
{
	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	if(!empty($in['selectedPID']))
	{
		if(!empty($in['selectid']))
		{
			$in_selectarr = explode(",", $in['selectid']);
			foreach($in['selectedPID'] as $svar)
			{
				if(!in_array($svar, $in_selectarr)) $subselectidarr[] = $svar;
			}
			$outidmsg = $in['selectid'].",";
		}else{
			$subselectidarr = $in['selectedPID'];
		}
		
		$comma_separated = implode(",", $subselectidarr);
		$outidmsg       .= $comma_separated;
		$cartdata = $db->get_results("select ID,Name,Coding,Units,Casing,Color,Specification from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']." and ID in (".$comma_separated.") order by ID asc");
		if(!empty($cartdata))
		{
			foreach($cartdata as $cvar)
			{
				$cvar['Name'] = preg_replace('/"([^"]*)"/', '“${1}”', $cvar['Name']);
				$cvar['Name'] = str_replace('"',"“",$cvar['Name']);
				$cvar['Name'] = str_replace(chr(32),"",$cvar['Name']);
				if(empty($cvar['Color']) && empty($cvar['Specification'])) $jsmsg = 'onfocus=\"this.select();\"'; else $jsmsg = 'onKeyDown=\"javascript: if(window.event.keyCode == 13) inputnumber(\''.$cvar['ID'].'\');\" onclick=\"inputnumber(\''.$cvar['ID'].'\');\" readonly=\"readonly\" ';

				$dmsg .= '<tr id=\"del_'.$cvar['ID'].'\"><td >&nbsp;'.$cvar['ID'].'<input name=\"storage_id[]\" type=\"hidden\" id=\"inputs_'.$cvar['ID'].'\" value=\"'.$cvar['ID'].'\" /></td><td >&nbsp;<a href=\"product_content.php?ID='.$cvar['ID'].'\" target=\"_blank\">'.$cvar['Name'].'</a></td><td >&nbsp;'.$cvar['Coding'].'</td><td >&nbsp;'.$cvar['Casing'].'</td><td align=\"center\">&nbsp;'.$cvar['Units'].'</td><td >&nbsp;<input name=\"storage_number[]\" type=\"text\" id=\"inputn_'.$cvar['ID'].'\" size=\"8\" maxlength=\"8\" '.$jsmsg.' value=\"0\" /> <input name=\"storage_number_arr[]\" type=\"hidden\" id=\"inputn_arr_'.$cvar['ID'].'\" value=\"\" /></td><td align=\"center\"><a href=\"javascript:void(0);\" onclick=\"del_sp_line(\''.$cvar['ID'].'\')\">移除</a></td></tr>';
			}
			$omsg .= '{"backtype":"ok", "htmldata":"'.$dmsg.'", "selectiddata":"'.$outidmsg.'"}';
		}else{
			$omsg .= '{"backtype":"empty"}';
		}
	}else{
		$omsg .= '{"backtype":"empty!"}';
	}
	echo $omsg;
	exit();
}

if($in['m']=="remove_line")
{
	if(!empty($in['selectid']))
	{
		$pos = strpos($in['selectid'], ",");
		if($pos)
		{
			$in_selectarr = explode(",", $in['selectid']);
			$kkey = array_search($in['ID'], $in_selectarr);
			if($kkey) unset($in_selectarr[$kkey]);
			$outmsg = implode(",",$in_selectarr);
			echo $outmsg;
		}else{
			echo '';
		}
	}else{
		echo '';
	}
	exit();
}


if($in['m']=="add_storage_save")
{
	if(!empty($in['selectid_storage']))
	{
		$osn = $db->get_row("SELECT StorageID,StorageSN from ".DATATABLE."_order_storage where CompanyID = ".$_SESSION['uinfo']['ucompany']." order by StorageID desc limit 0,1");
			
		if(empty($osn['StorageSN']))
		{
			$stsn	= date("Ymd")."-1";
		}else{
			$nextid	= intval(substr($osn['StorageSN'],strpos($osn['StorageSN'], '-')+1))+1;
			$stsn	= date("Ymd")."-".$nextid;
		}
		$sqlin = "insert into ".DATATABLE."_order_storage(StorageSN,CompanyID,StorageProduct,StorageAttn,StorageAbout,StorageUser,StorageDate) values('".$stsn."',".$_SESSION['uinfo']['ucompany'].", '".$in['selectid_storage']."', '".$in['StorageAttn']."',  '".$in['StorageAbout']."','".$_SESSION['uinfo']['username']."' ,".time().")";
		$isup = $db->query($sqlin);
		
		$totalnum = 0;
		if($isup)
		{
			$stoid = mysql_insert_id();
			for($i=0;$i<count($in['storage_id']);$i++)
			{
				$snum = abs(intval($in['storage_number'][$i]));
				if(empty($snum)) continue;
				$totalnum = $totalnum + $snum;
				$sqlin = "insert into ".DATATABLE."_order_storage_number(StorageID,CompanyID,ContentID,ContentNumber) values(".$stoid.",".$_SESSION['uinfo']['ucompany'].", ".$in['storage_id'][$i].", ".$snum.")";
				$db->query($sqlin);

				//更新库存
				$isup = $db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber+".$snum.", ContentNumber=ContentNumber+".$snum." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['storage_id'][$i]);
				if(!$isup) $db->query("insert into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$in['storage_id'][$i].",".$snum.",".$snum.")");

				if(!empty($in['storage_number_arr'][$i]))
				{
					$dataarr = unserialize(urldecode($in['storage_number_arr'][$i]));
					foreach($dataarr as $dkey=>$dvar)
					{						
						$scsnum = abs(intval($dvar));
						if(!empty($scsnum))
						{
							$dkeyarr = explode("_",$dkey);
							$sqlincs = "insert into ".DATATABLE."_order_storage_number_cs(StorageID,CompanyID,ContentID,ContentColor,ContentSpec,ContentNumber) values(".$stoid.",".$_SESSION['uinfo']['ucompany'].", ".$in['storage_id'][$i].",'".$dkeyarr[2]."','".$dkeyarr[1]."', ".$scsnum.")";
							$db->query($sqlincs);
							//更新库存
							$iscsup = $db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$scsnum.", ContentNumber=ContentNumber+".$scsnum." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['storage_id'][$i]." and ContentColor='".$dkeyarr[2]."' and ContentSpec='".$dkeyarr[1]."'");
							if(!$iscsup) $db->query("insert into ".DATATABLE."_order_inventory_number(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$in['storage_id'][$i].",'".$dkeyarr[2]."','".$dkeyarr[1]."',".$scsnum.",".$scsnum.")");
						}
					}
					$sqlsum = "select sum(OrderNumber) as ototal,sum(ContentNumber) as ctotal from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['storage_id'][$i]."";
					$totalrow = $db->get_row($sqlsum);
					if(!empty($totalrow))
					{
						$db->query("update ".DATATABLE."_order_number set OrderNumber=".$totalrow['ototal'].", ContentNumber=".$totalrow['ctotal']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['storage_id'][$i]);
					}
				}
			}
			if(empty($totalnum))
			{
				$db->query("delete from ".DATATABLE."_order_storage where CompanyID=".$_SESSION['uinfo']['ucompany']." and  StorageID=".$stoid." limit 1 ");
				exit('请先输入入库商品数量！');
			}else{
				exit('ok');
			}
		}else{
			exit("提交不成功!");
		}
	}
	exit("您还没有选择任何商品!");
}


if($in['m']=="add_input_number_save")
{
	$snarr = null;
	$totalnumber = 0;
	if(!empty($in['storage_number_id']))
	{
		for($i=0;$i<count($in['storage_number_id']);$i++)
		{
			$snarr[$in['storage_number_id'][$i]] = abs(intval($in['storage_number'][$i]));
			$totalnumber = $totalnumber + abs(intval($in['storage_number'][$i]));
		}
	}
	//echo "application/json;charset=UTF-8";
	$arrmsg = urlencode(serialize($snarr));

	$omsg = '{"backtype":"ok", "iddata":"'.$arrmsg.'", "tnumber":"'.$totalnumber.'", "pid":"'.$in['inputpid'].'"}';
	echo $omsg;
	exit();	
}

if($in['m']=="change_input_number")
{
	$snarr = null;
	$totalnumber = 0;
	$stotal = 0;
	$ctotal = 0;
	if(!empty($in['storage_number_id']))
	{
		for($i=0;$i<count($in['storage_number_id']);$i++)
		{
			$snarr[$in['storage_number_id'][$i]] = abs(intval($in['storage_number'][$i]));
			$keyarr = explode("_",$in['storage_number_id'][$i]);
			$sarr[] = $keyarr[1];
			$carr[] = $keyarr[2];
			$totalnumber = $totalnumber + abs(intval($in['storage_number'][$i]));
		}
		$sarr = array_unique($sarr);
		$carr = array_unique($carr);
		
		foreach($carr as $cvar)
		{
			$stotal = $stotal + $snarr['inputn_'.$in['spec'].'_'.$cvar];
		}
		foreach($sarr as $svar)
		{
			$ctotal = $ctotal + $snarr['inputn_'.$svar.'_'.$in['color']];
		}
	}

	//echo "application/json;charset=UTF-8";
	$arrmsg = urlencode(serialize($snarr));

	$omsg = '{"backtype":"ok", "hjvalue":"'.$stotal.'", "sjvalue":"'.$ctotal.'","totalvalue":"'.$totalnumber.'"}';
	echo $omsg;
	exit();
}

/********** 调整库存 ********************/
if($in['m']=="change_input_library_number")
{
	$snarr = null;
	$totalnumber = 0;
	$stotal = 0;
	$ctotal = 0;
	if(!empty($in['storage_number_id']))
	{
		for($i=0;$i<count($in['storage_number_id']);$i++)
		{
			$snarr[$in['storage_number_id'][$i]] = abs(intval($in['storage_number'][$i]));
			$keyarr = explode("_",$in['storage_number_id'][$i]);
			$sarr[] = $keyarr[1];
			$carr[] = $keyarr[2];
			$totalnumber = $totalnumber + abs(intval($in['storage_number'][$i]));
		}
		$sarr = array_unique($sarr);
		$carr = array_unique($carr);
		
		foreach($carr as $cvar)
		{
			$stotal = $stotal + $snarr['inputn_'.$in['spec'].'_'.$cvar];
		}
		foreach($sarr as $svar)
		{
			$ctotal = $ctotal + $snarr['inputn_'.$svar.'_'.$in['color']];
		}
	}

	//echo "application/json;charset=UTF-8";
	$arrmsg = urlencode(serialize($snarr));

	$omsg = '{"backtype":"ok", "hjvalue":"'.$stotal.'", "sjvalue":"'.$ctotal.'","totalvalue":"'.$totalnumber.'"}';
	echo $omsg;
	exit();
}

if($in['m']=="change_library_number_save")
{
	$snarr = null;
	$totalnumber = 0;
	$totalordernumber = 0;

	if(!empty($in['storage_number_id']))
	{
		for($i=0;$i<count($in['storage_number_id']);$i++)
		{
			$snarr[$in['storage_number_id'][$i]] = abs(intval($in['storage_number'][$i]));
		}

		$sql   = "select ContentID,ContentColor,ContentSpec,ContentNumber from ".DATATABLE."_order_inventory_number where  CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".intval($in['inputpid']);
		$list_data = $db->get_results($sql);
		if(!empty($list_data))
		{
			foreach($list_data as $lv)
			{
				$lkey = 'inputn_'.$lv['ContentSpec']."_".$lv['ContentColor'];
				$snarr_old[$lkey] = $lv['ContentNumber'];

				if(empty($snarr[$lkey]))  $n_var = 0; else $n_var = $snarr[$lkey];
				if($snarr_old[$lkey] > $n_var)
				{
					$db->query("insert into ".DATATABLE."_order_library_number(CompanyID,ContentID,ContentColor,ContentSpec,ContentNumber,ContentOldNumber,LibraryUser,LibraryDate) values(".$_SESSION['uinfo']['ucompany'].",".$in['inputpid'].",'".$lv['ContentColor']."','".$lv['ContentSpec']."',".$n_var.",".$lv['ContentNumber'].",'".$_SESSION['uinfo']['username']."',".time().")");
					$chazi = $snarr_old[$lkey]-$n_var;
					
					$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$chazi.", ContentNumber=".$n_var." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['inputpid']." and ContentColor='".$lv['ContentColor']."' and ContentSpec='".$lv['ContentSpec']."'");
					$totalnumber = $totalnumber + $n_var;
				}else{
					$totalnumber = $totalnumber + $snarr_old[$lkey];
				}
			}
			$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=ContentNumber where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['inputpid']." and OrderNumber > ContentNumber");
		}

		$numberline = $db->get_row("select ContentID,OrderNumber,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['inputpid']." limit 0,1");
		if(!empty($numberline) && ($totalnumber < $numberline['ContentNumber']))
		{
			$db->query("insert into ".DATATABLE."_order_library_number(CompanyID,ContentID,ContentColor,ContentSpec,ContentNumber,ContentOldNumber,LibraryUser,LibraryDate,LibraryFlag) values(".$_SESSION['uinfo']['ucompany'].",".$in['inputpid'].",'','',".$totalnumber.",".$numberline['ContentNumber'].",'".$_SESSION['uinfo']['username']."',".time().",'1')");

			$sqlsum = "select sum(OrderNumber) as ototal,sum(ContentNumber) as ctotal from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['inputpid']."";
			$totalrow = $db->get_row($sqlsum);
			if(!empty($totalrow))
			{
				$db->query("update ".DATATABLE."_order_number set OrderNumber=".$totalrow['ototal'].", ContentNumber=".$totalrow['ctotal']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['inputpid']);
			}
			
			//echo "application/json;charset=UTF-8";
			$omsg = '{"backtype":"ok", "tnumber":"'.$totalrow['ctotal'].'", "tordernumber":"'.$totalrow['ototal'].'", "pid":"'.$in['inputpid'].'"}';
			echo $omsg;
			exit();
		}
	}
	$omsg = '{"backtype":"error"}';
	echo $omsg;
	exit();
}


if($in['m']=="change_library_number_save_one")
{
	if(!empty($in['inputpid']))
	{
		$in['inputpid'] = intval($in['inputpid']);
		$totalnumber    = abs(intval($in['storage_number']));

		$numberline = $db->get_row("select ContentID,OrderNumber,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['inputpid']." limit 0,1");
		if(!empty($numberline) && ($numberline['ContentNumber'] > $totalnumber))
		{
			$db->query("insert into ".DATATABLE."_order_library_number(CompanyID,ContentID,ContentColor,ContentSpec,ContentNumber,ContentOldNumber,LibraryUser,LibraryDate,LibraryFlag) values(".$_SESSION['uinfo']['ucompany'].",".$in['inputpid'].",'','',".$totalnumber.",".$numberline['ContentNumber'].",'".$_SESSION['uinfo']['username']."',".time().",'1')");
			$totalordernumber = $numberline['OrderNumber'] - ($numberline['ContentNumber'] - $totalnumber);
			if($totalordernumber < 0) $totalordernumber = 0;

			$db->query("update ".DATATABLE."_order_number set OrderNumber=".$totalordernumber.", ContentNumber=".$totalnumber." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['inputpid']);
		
			$omsg = '{"backtype":"ok", "tnumber":"'.$totalnumber.'", "tordernumber":"'.$totalordernumber.'", "pid":"'.$in['inputpid'].'"}';
			echo $omsg;
			exit();	
		}
	}
	$omsg = '{"backtype":"error"}';
	echo $omsg;
	exit();
}

/************************/
if($in['m']=="change_library_mul_number_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
	
	$snarr = null;
	$snidmsg = '0';
	$totalnumber = 0;
	$totalordernumber = 0;

	if(!empty($in['storage_number_id']))
	{
		for($i=0;$i<count($in['storage_number_id']);$i++)
		{
			$num = abs(intval($in['storage_number'][$i]));
			if(strpos($in['storage_number_id'][$i],"_"))
			{
				$tagarr = explode("_",$in['storage_number_id'][$i]);
				if(empty($snarr[$tagarr[0]])) $snarr[$tagarr[0]] = $num; else $snarr[$tagarr[0]] = $snarr[$tagarr[0]] + $num;
				$snidmsg .= ",".$tagarr[0];
				$db->query("Replace into ".DATATABLE."_order_inventory_number(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$tagarr[0].",'".$tagarr[2]."','".$tagarr[1]."',".$num.",".$num.")");			
			}else{
				$snarr[$in['storage_number_id'][$i]] = $num;
				$snidmsg .= ",".$in['storage_number_id'][$i];
			}
		}
		if($snidmsg!='0')
		{
			$list_data = $db->get_results("select ContentID,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID in (".$snidmsg.")");
			foreach($list_data as $lv)
			{
				$larr[$lv['ContentID']] = $lv['ContentNumber'];
			}
		}

		foreach($snarr as $key=>$sv)
		{
			if(empty($larr[$key])) $larr[$key] = 0;
			if($larr[$key]!=$sv)
			{
				$db->query("Replace into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$key.",".$sv.",".$sv.")");
				$db->query("insert into ".DATATABLE."_order_library_change_log(CompanyID,ContentID,ChangeOld,ChangeNew,ChangeUser,ChangeDate) values(".$_SESSION['uinfo']['ucompany'].",".$key.",".$larr[$key].",".$sv.",'".$_SESSION['uinfo']['username']."',".time().")");
			}
		}
	}
	$updatetype = "批量调整库存";
	$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_inventory.php?m=change_library_mul_number_save','".$updatetype."','-',".time().")";
	$db->query($sqlex);

	exit('ok');
}

//调整库存-单个
if($in['m']=="change_lib_num_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');
	$valuearr = get_set_arr('product');

	if(!empty($in['ID']))
	{
		$in['ID'] = str_replace("DHK","|",$in['ID']);
		$outarr['action'] = "ok";
		$num = abs(intval($in['changenumber']));

		if(strpos($in['ID'],"_"))
		{
			$tagarr = explode("_",$in['ID']);
			if($tagarr[1] == str_replace($fp,$rp,base64_encode("统一")))
			{
				$specv = '';
			}else{
				$specv = base64_decode(str_replace($rp,$fp,$tagarr[1]));
			}
			if($tagarr[2] == str_replace($fp,$rp,base64_encode("统一")))
			{
				$colorv = '';
			}else{
				$colorv = base64_decode(str_replace($rp,$fp,$tagarr[2]));
			}
			$odata = $db->get_row("select ContentID,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$tagarr[0]."");

			$statsql  = "SELECT sum(ContentNumber-ContentSend) as cnum from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.ContentID=".$tagarr[0]." and c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ContentColor='".$colorv."' and c.ContentSpecification='".$specv."' and o.OrderStatus!=8 and o.OrderStatus!=9 ";
			$totalallrow = $db->get_row($statsql );
			$statsqlg  ="SELECT sum(ContentNumber-ContentSend) as cnum from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.ContentID=".$tagarr[0]." and c.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.ContentColor='".$colorv."' and c.ContentSpecification='".$specv."' and o.OrderStatus!=8 and o.OrderStatus!=9 ";
			$totalallrowg = $db->get_row($statsqlg);
			$cnum = intval($totalallrow['cnum']) + intval($totalallrowg['cnum']);

			if(!empty($cnum))
			{
				$onumber = $num - $cnum;
			}else{
				$onumber = $num;
			}
			if((empty($valuearr['product_negative']) || $valuearr['product_negative']!="on") && $onumber < 0) $onumber = 0;

			$db->query("Replace into ".DATATABLE."_order_inventory_number(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$tagarr[0].",'".$tagarr[2]."','".$tagarr[1]."',".$onumber.",".$num.")");

			$totalrow = $db->get_row("select sum(ContentNumber) as cnum,sum(OrderNumber) as onum from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$tagarr[0]."");
			if(empty($totalrow))
			{
				$totalrow['cnum'] = 0;
				$totalrow['onum'] = 0;
			}
			//$db->query("update ".DATATABLE."_order_number set ContentNumber=".$totalrow['cnum'].",OrderNumber=".$totalrow['onum']." where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$tagarr[0]." limit 1");
			$db->query("Replace into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$tagarr[0].",".$totalrow['onum'].",".$totalrow['cnum'].")");

			$db->query("insert into ".DATATABLE."_order_library_change_log(CompanyID,ContentID,ChangeOld,ChangeNew,ChangeUser,ChangeDate) values(".$_SESSION['uinfo']['ucompany'].",".$tagarr[0].",".$odata['ContentNumber'].",".$totalrow['cnum'].",'".$_SESSION['uinfo']['username']."',".time().")");
			$omsg = '{"backtype":"ok", "htmldataid":"total_'.$tagarr[0].'", "htmldata":"'.$totalrow['cnum'].'"}';
			
		}else{
			$odata = $db->get_row("select ContentID,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['uinfo']['ucompany']." and ContentID=".$in['ID']."");

			$statsql  = "SELECT sum(ContentNumber-ContentSend) as cnum from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.ContentID=".$in['ID']." and c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.OrderStatus!=8 and o.OrderStatus!=9 ";
			$totalallrow = $db->get_row($statsql);
			$statsqlg  = "SELECT sum(ContentNumber-ContentSend) as cnum from ".DATATABLE."_order_cart_gifts c left join ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID where c.ContentID=".$in['ID']." and c.CompanyID=".$_SESSION['uinfo']['ucompany']." and o.OrderStatus!=8 and o.OrderStatus!=9 ";
			$totalallrowg = $db->get_row($statsqlg);
			$cnum = intval($totalallrow['cnum']) + intval($totalallrowg['cnum']);

			if(!empty($cnum))
			{
				$onumber = $num - $cnum;
			}else{
				$onumber = $num;
			}
			if((empty($valuearr['product_negative']) || $valuearr['product_negative']!="on") && $onumber < 0) $onumber = 0;

			$db->query("Replace into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$in['ID'].",".$onumber.",".$num.")");

			$db->query("insert into ".DATATABLE."_order_library_change_log(CompanyID,ContentID,ChangeOld,ChangeNew,ChangeUser,ChangeDate) values(".$_SESSION['uinfo']['ucompany'].",".$in['ID'].",".$odata['ContentNumber'].",".$num.",'".$_SESSION['uinfo']['username']."',".time().")");

			$omsg = '{"backtype":"ok", "htmldataid":"", "htmldata":""}';
		}
	}else{
		$omsg = '{"backtype":"修改不成功!", "htmldataid":"", "htmldata":""}';
	}
	echo $omsg;
	exit();
}

//33
if($in['m']=="add_implode_storage_save")
{
	require_once '../class/PHPExcel/IOFactory.php';
	$isok = false;	
	$companyidmsg = $_SESSION['uinfo']['ucompany'];
	$resPath = setuppath($companyidmsg);

	$srcpath 	= RESOURCE_PATH.$companyidmsg."/".$resPath."/";	
	$backpath   = $companyidmsg."/".$resPath."/";

	$uploadDir 	= RESOURCE_PATH.$companyidmsg.'/';
	$sFileName  = basename($_FILES['import_storagge_file']['name']);
	$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) );
	$sExtension = strtolower( $sExtension );
	$currenttime = date("Ymd_His")."_".currentTimeMillis()."_".rand(100,999);
	$sFileName   = $currenttime.".".$sExtension;
	$uploadFile  = $srcpath.$sFileName;
	
	if(empty($_FILES['import_storagge_file']['name'])) Error::AlertJs('请先选择您要导入的数据文件!');
	if($sExtension!="xls") Error::AlertJs('只能导入EXCEL文件(扩展名：xls)');

	if ($_FILES['import_storagge_file']['name']) {
		if (move_uploaded_file ($_FILES['import_storagge_file']['tmp_name'], $uploadFile)) {
			@unlink($_FILES['import_storagge_file']["tmp_name"]);//删除临时文件
		}
	} else {
		if ($_FILES['import_storagge_file']['error']) {
		   Error::AlertJs($_FILES['import_storagge_file']['error']);
		   exit();
		}
	}

	if(file_exists($uploadFile))
	{
		$condata   = $db->get_results("select ID,Coding from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']."  order by ID asc");

		if(!empty($condata))
		{
			foreach($condata as $var)
			{
				$inarr[$var['Coding']] = $var['ID'];
			}
		}

		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($srcpath.$sFileName);
		$casingmsg = '';
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			foreach ($worksheet->getRowIterator() as $row)
			{
				if($row->getRowIndex() > 1)
				{			
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					$linearr = null;
					foreach ($cellIterator as $cell) {
						if (!is_null($cell)) {
							$firstchar = substr($cell->getCoordinate(),0,1);
							$fieldvar  = trim($cell->getCalculatedValue());
							$linearr[$firstchar] = $fieldvar;					
						}
					}
					if(empty($linearr['A'])) continue;
					if(empty($inarr[$linearr['A']])) continue;

					$indexid = $inarr[$linearr['A']];
					$sql = "replace into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$indexid.",".intval($linearr['B']).",".intval($linearr['B']).")";
					$db->query($sql);
					$isok = true;
				}
			}
		}

		if($isok)
		{
			$db->query("insert into ".DATATABLE."_order_import(ImportCompany,ImportExcel,ImportExcelFile,ImportAbout,ImportDate,ImportUser) values(".$_SESSION['uinfo']['ucompany'].",'".$_FILES['import_storagge_file']['name']."','".$backpath.$sFileName."','".$in['ImportAbout']."',".time().",'".$_SESSION['uinfo']['username']."')");
			Error::outAdmin('导入成功!','storage_import.php');
		}else{
			Error::outAdmin('数据格式不正确!','storage_import.php');	
		}
	}else{
		Error::outAdmin('导入不成功!','storage_import.php');
	}
	exit();
}


//51
if($in['m']=="add_implode_51_storage_save")
{
	require_once '../class/PHPExcel/IOFactory.php';
	$isok = false;	
	$companyidmsg = $_SESSION['uinfo']['ucompany'];
	$resPath = setuppath($companyidmsg);

	$srcpath 	= RESOURCE_PATH.$companyidmsg."/".$resPath."/";	
	$backpath   = $companyidmsg."/".$resPath."/";

	$uploadDir  = RESOURCE_PATH.$companyidmsg.'/';
	$sFileName  = basename($_FILES['import_storagge_file']['name']);
	$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) );
	$sExtension = strtolower( $sExtension );
	$currenttime = date("Ymd_His")."_".currentTimeMillis()."_".rand(100,999);
	$sFileName   = $currenttime.".".$sExtension;
	$uploadFile  = $srcpath.$sFileName;
	
	if(empty($_FILES['import_storagge_file']['name'])) Error::AlertJs('请先选择您要导入的数据文件!');
	if($sExtension!="xls") Error::AlertJs('只能导入EXCEL文件(扩展名：xls)');

	if ($_FILES['import_storagge_file']['name']) {
		if (move_uploaded_file ($_FILES['import_storagge_file']['tmp_name'], $uploadFile)) {
			@unlink($_FILES['import_storagge_file']["tmp_name"]);//删除临时文件
		}
	} else {
		if ($_FILES['import_storagge_file']['error']) {
		   Error::AlertJs($_FILES['import_storagge_file']['error']);
		   exit();
		}
	}

	if(file_exists($uploadFile))
	{
		$condata   = $db->get_results("select ID,Coding from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']."  order by ID asc");

		if(!empty($condata))
		{
			foreach($condata as $var)
			{
				$inarr[$var['Coding']] = $var['ID'];
			}
		}

		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($srcpath.$sFileName);
		$casingmsg = '';
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			foreach ($worksheet->getRowIterator() as $row)
			{
				if($row->getRowIndex() > 1)
				{			
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					$linearr = null;
					foreach ($cellIterator as $cell) {
						if (!is_null($cell)) {
							$firstchar = substr($cell->getCoordinate(),0,1);
							$fieldvar  = trim($cell->getCalculatedValue());
							$linearr[$firstchar] = $fieldvar;					
						}
					}
					if(empty($linearr['A'])) continue;
					if(empty($inarr[$linearr['A']])) continue;

					$indexid = $inarr[$linearr['A']];
					echo $sql = "replace into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$indexid.",".intval($linearr['I']).",".intval($linearr['I']).")";
					$db->query($sql);
					$isok = true;
				}
			}
		}

		if($isok)
		{
			$db->query("insert into ".DATATABLE."_order_import(ImportCompany,ImportExcel,ImportExcelFile,ImportAbout,ImportDate,ImportUser) values(".$_SESSION['uinfo']['ucompany'].",'".$_FILES['import_storagge_file']['name']."','".$backpath.$sFileName."','".$in['ImportAbout']."',".time().",'".$_SESSION['uinfo']['username']."')");
			Error::outAdmin('导入成功!','storage_import.php');
		}else{
			Error::outAdmin('数据格式不正确!','storage_import.php');
		}
	}else{
		Error::outAdmin('导入不成功!','storage_import.php');
	}
	exit();
}


//上传库存excel
if($in['m']=="add_implode_storage_all")
{	
	$isok = false;	
	$companyidmsg = $_SESSION['uinfo']['ucompany'];
	$resPath = setuppath($companyidmsg);

	$srcpath 	= RESOURCE_PATH.$companyidmsg."/".$resPath."/";	
	$backpath   = $companyidmsg."/".$resPath."/";

	$uploadDir  = RESOURCE_PATH.$companyidmsg.'/';
	$sFileName  = basename($_FILES['import_storagge_file']['name']);
	$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) );
	$sExtension = strtolower( $sExtension );
	$currenttime = date("Ymd_His")."_".currentTimeMillis()."_".rand(100,999);
	$sFileName   = $currenttime.".".$sExtension;
	$uploadFile  = $srcpath.$sFileName;
	
	if(empty($_FILES['import_storagge_file']['name'])) Error::AlertJs('请先选择您要导入的数据文件!');
	if($sExtension!="xls") Error::AlertJs('只能导入EXCEL文件(扩展名：xls)');

	if ($_FILES['import_storagge_file']['name']) {
		if (move_uploaded_file ($_FILES['import_storagge_file']['tmp_name'], $uploadFile)) {
			@unlink($_FILES['import_storagge_file']["tmp_name"]);//删除临时文件
		}
	} else {
		if ($_FILES['import_storagge_file']['error']) {
		   Error::AlertJs($_FILES['import_storagge_file']['error']);
		   exit();
		}
	}

	if(file_exists($uploadFile))
	{		
		$db->query("insert into ".DATATABLE."_order_import(ImportCompany,ImportExcel,ImportExcelFile,ImportAbout,ImportDate,ImportUser) values(".$_SESSION['uinfo']['ucompany'].",'".$_FILES['import_storagge_file']['name']."','".$backpath.$sFileName."','".$in['ImportAbout']."',".time().",'".$_SESSION['uinfo']['username']."')");
		$stoid = mysql_insert_id();
		header("location: do_inventory.php?m=import_number_all&ID=".$stoid);
		exit;
		//Error::Alert('上传成功!');
	}else{
		Error::outAdmin('上传不成功!','storage_import_51.php');
	}
	exit();
}

//导入库存
if($in['m']=="import_number_all")
{
	set_time_limit(300);
	require_once '../class/PHPExcel/IOFactory.php';

	$datasql   = "SELECT ImportID,ImportExcelFile FROM ".DATATABLE."_order_import where ImportID=".intval($in['ID'])." and ImportCompany = ".$_SESSION['uinfo']['ucompany']."  ORDER BY ImportID DESC limit 0,1";
	$InfoData = $db->get_row($datasql);
	if(!empty($InfoData)) $uploadFile = RESOURCE_PATH.$InfoData['ImportExcelFile'];

	if(file_exists($uploadFile))
	{
		$condata   = $db->get_results("select ID,Coding from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']."  order by ID asc");

		if(!empty($condata))
		{
			foreach($condata as $var)
			{
				$inarr[$var['Coding']] = $var['ID'];
			}
		}
		
		$sql1 = $sql2 = '';
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($uploadFile);
		$casingmsg = '';
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{
			foreach ($worksheet->getRowIterator() as $row)
			{
				if($row->getRowIndex() > 1)
				{			
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					$linearr = null;
					foreach ($cellIterator as $cell) {
						if (!is_null($cell)) {
							$firstchar = substr($cell->getCoordinate(),0,1);
							$fieldvar  = trim($cell->getCalculatedValue());
							$linearr[$firstchar] = $fieldvar;					
						}
					}
					if(empty($linearr['A'])) continue;
					if(empty($inarr[$linearr['A']])) continue;

					if(!empty($indexid))
					{
						if($indexid != $inarr[$linearr['A']] && $istotal) $sql1 = "replace into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$indexid.",".intval($ordern[$indexid]).",".intval($contentn[$indexid]).")";
						$db->query($sql1);
					}

					$indexid = $inarr[$linearr['A']];
					if(empty($linearr['B']) && empty($linearr['C']))
					{
						$sql1 = "replace into ".DATATABLE."_order_number(CompanyID,ContentID,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$indexid.",".intval($linearr['E']).",".intval($linearr['D']).")";
						$db->query($sql1);
						$istotal = false;
					}else{
						if(empty($linearr['B'])) $colormsg = str_replace($fp,$rp,base64_encode("统一")); else $colormsg = str_replace($fp,$rp,base64_encode($linearr['B']));
						if(empty($linearr['C'])) $specmsg = str_replace($fp,$rp,base64_encode("统一")); else $specmsg = str_replace($fp,$rp,base64_encode($linearr['C']));
						$sql2 = "replace into ".DATATABLE."_order_inventory_number(CompanyID,ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber) values(".$_SESSION['uinfo']['ucompany'].",".$indexid.", '".$colormsg."', '".$specmsg."',".intval($linearr['E']).",".intval($linearr['D']).")";
						$db->query($sql2);
						if(empty($ordern[$indexid])) $ordern[$indexid] = 0;
						$ordern[$indexid] = $ordern[$indexid] + intval($linearr['E']);

						if(empty($contentn[$indexid])) $contentn[$indexid] = 0;
						$contentn[$indexid] = $contentn[$indexid] + intval($linearr['D']);
						$istotal = true;
					}					
				}
			}
		}
		Error::outAdmin('导入成功!','storage_import_115.php');
	}else{
		Error::outAdmin('导入不成功!','storage_import_115.php');
	}
	exit();
} else if($in['m'] == 'export_excel') {
    //导出库存信息
    set_time_limit(300);
    require_once '../class/PHPExcel.php';
    $fp = array('+','/','=','_');
    $rp = array('-','|','DHB',' ');
    $company_id = $_SESSION['uc']['CompanyID'];
    $objPHPExcel = new PHPExcel();
    $objSheet = $objPHPExcel->getActiveSheet();
    $objSheet->getDefaultRowDimension()->setRowHeight(24);
    $objSheet->setTitle("库存信息");
    $sql = "SELECT i.ID,i.Coding,i.Name ,n.ContentNumber,n.OrderNumber,n.ContentColor,n.ContentSpec -- 取多规格商品库存数
            FROM rsung_order_content_index AS i
            LEFT JOIN rsung_order_inventory_number AS n
            ON n.ContentID = i.ID
            WHERE i.CompanyID = {$company_id} AND i.FlagID=0 AND n.ContentNumber IS NOT NULL   AND n.OrderNumber IS NOT NULL 
            UNION
            SELECT i.ID,i.Coding,i.Name,n.ContentNumber,n.OrderNumber,'' as ContentColor,'' as ContentSpec -- 取无规格商品库存数
            FROM rsung_order_content_index AS i
            LEFT JOIN rsung_order_number AS n
            ON n.ContentID = i.ID
            WHERE i.CompanyID={$company_id} AND i.FlagID=0 AND (i.Color = ''  OR i.color IS NULL )  AND (i.Specification='' OR i.Specification IS NULL)";//i.Color = ''  AND i.Specification=''
    $list = $db->get_results($sql);
    $objSheet->setCellValue("A1","商品编号(*)")->setCellValue("B1","商品名称")->setCellValue("C1","颜色")->setCellValue("D1","规格")->setCellValue("E1","实际库存")->setCellValue("F1" , "可用库存");
    //$objActSheet->getColumnDimension('A')->setWidth(25);
    foreach(range('A','F') as $code) {
        $objSheet->getColumnDimension($code)->setWidth(30);
    }
    $idx = 1;
    foreach($list as $item) {
        $idx++;
        $item['ContentSpec'] = $item['ContentSpec'] ? base64_decode(str_replace($rp,$fp,$item['ContentSpec'])) : '';
        $item['ContentColor'] = $item['ContentColor'] ? base64_decode(str_replace($rp,$fp,$item['ContentColor'])) : '';
        $item['ContentSpec'] = $item['ContentSpec'] == "统一" ? "" : $item['ContentSpec'];
        $item['ContentColor'] = $item['ContentColor'] == "统一" ? "" : $item['ContentColor'];
        $objSheet->setCellValue("A" . $idx , $item['Coding'])
            ->setCellValue("B" . $idx , $item['Name'])
            ->setCellValue("C" . $idx , $item['ContentColor'])
            ->setCellValue("D" . $idx , $item['ContentSpec'])
            ->setCellValue("E" . $idx , $item['ContentNumber'])
            ->setCellValue("F" . $idx , $item['OrderNumber']);
    }


    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,"Excel5");
    browser_export("Excel5",date('YmdHis').'库存信息.xls');
    $objWriter->save("php://output");
    //echo "XXX";

    exit;
} else if($in['m'] == 'import_excel') {

    echo "XXX";

    exit;
}

function browser_export($type,$filename){
    if($type=="Excel5"){
        header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
    }else{
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
    }
    header('Content-Disposition: attachment;filename="'.$filename.'"');//告诉浏览器将输出文件的名称
    header('Cache-Control: max-age=0');//禁止缓存
}

exit('非法操作');
?>