<?php
include_once ("header.php");

if(!in_array($_SESSION['uinfo']['userid'],array(1))) exit('非法路径!!!');

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if(!intval($in['cid'])) exit('非法操作!');
$cid = intval($in['cid']);
$csql   = "SELECT CompanyID,CompanyDatabase FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$cid." ORDER BY CompanyID ASC limit 0,1";
$cominfo = $db->get_row($csql);
if(!empty($cominfo['CompanyDatabase']))
{
	$sdbname = DB_DATABASE.'_'.$cominfo['CompanyDatabase'].".";
}else{
	$sdbname = DB_DATABASE.'.';
}

if($in['m']=="delete_order")
{
	if(!empty($in['did'])){
		$deleteidmsg = intval($in['did']);
	}elseif(!empty($in['selectedID'])){
		$deleteidmsg = implode(",", $in['selectedID']);
	}else{
		exit('请先选择您要删除的内容！');
	}
	$upsql =  "delete from ".$sdbname.DATATABLE."_order_orderinfo where OrderCompany = ".$cid." and OrderID IN (".$deleteidmsg.") ";
	if($db->query($upsql))
	{
		$db->query("delete from ".$sdbname.DATATABLE."_order_cart where CompanyID = ".$cid." and OrderID IN (".$deleteidmsg.") ");
		$db->query("delete from ".$sdbname.DATATABLE."_order_ordersubmit where CompanyID = ".$cid." and OrderID IN (".$deleteidmsg.") ");
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}
elseif($in['m']=="delete_consignment")
{

	if(!empty($in['did'])){
		$deleteidmsg = intval($in['did']);
	}elseif(!empty($in['selectedID'])){
		$deleteidmsg = implode(",", $in['selectedID']);
	}else{
		exit('请先选择您要删除的内容！');
	}
	//单头
	$upsql =  "delete from ".$sdbname.DATATABLE."_order_consignment where ConsignmentCompany = ".$cid." and ConsignmentID IN (".$deleteidmsg.") ";
	if($db->query($upsql))
	{
		$outdata = $db->get_results("select * from ".$sdbname.DATATABLE."_order_out_library where CompanyID = ".$cid." and ConsignmentID IN (".$deleteidmsg.") ");
		
		$orderidarr = null;
		foreach($outdata as $k=>$v){
			$outarr[$v['ConType']][$v['OrderID'].'_'.$v['CartID']] = $v['ContentNumber'];
			if(!in_array($v['OrderID'],$orderidarr)) $orderidarr[] = $v['OrderID'];
		}
		//正品
		foreach($outarr['c'] as $ck=>$cv){
			$idmsg = explode("_",$ck);
			$oid = $idmsg[0];
			$cartid = $idmsg[1];
			$cartarr[] = $cartid;
			//发货数量
			$db->query("update ".$sdbname.DATATABLE."_order_cart set ContentSend=ContentSend-".$cv." where ID=".$cartid." and CompanyID = ".$cid." and ContentSend >= ".$cv." ");
		}
		//赠品
		foreach($outarr['g'] as $ck=>$cv){
			$idmsg = explode("_",$ck);
			$oid = $idmsg[0];
			$cartid = $idmsg[1];
			$giftsarr[] = $cartid;
			$db->query("update ".$sdbname.DATATABLE."_order_cart_gifts set ContentSend=ContentSend-".$cv." where ID=".$cartid." and CompanyID = ".$cid." and ContentSend >= ".$cv." ");
		}

		//库存
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){
			$tykey  = str_replace($fp,$rp,base64_encode("统一"));	
			//正品
			if(!empty($cartarr)){
				$libdata = $db->get_results("select ID,OrderID,ContentID,ContentColor,ContentSpecification from ".$sdbname.DATATABLE."_order_cart where CompanyID = ".$cid." and ID IN (".implode(",",$cartarr).") ");
				foreach($libdata as $var){
					if(empty($outarr['c'][$var['OrderID'].'_'.$var['ID']])) continue;
					$db->query("update ".$sdbname.DATATABLE."_order_number set ContentNumber=ContentNumber+".$outarr['c'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." limit 1");
					if(!empty($var['ContentColor']) || !empty($var['ContentSpecification'])){
						if(empty($var['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($var['ContentColor']));
						if(empty($var['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($var['ContentSpecification']));
						$db->query("update ".$sdbname.DATATABLE."_order_inventory_number set ContentNumber=ContentNumber+".$outarr['c'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
					}
				}
			}
			//赠品
			if(!empty($giftsarr)){
				$libdata = $db->get_results("select ID,OrderID,ContentID,ContentColor,ContentSpecification from ".$sdbname.DATATABLE."_order_cart_gifts where CompanyID = ".$cid." and ID IN (".implode(",",$giftsarr).") ");
				foreach($libdata as $var){
					if(empty($outarr['g'][$var['OrderID'].'_'.$var['ID']])) continue;
					$db->query("update ".$sdbname.DATATABLE."_order_number set ContentNumber=ContentNumber+".$outarr['g'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." limit 1");
					if(!empty($var['ContentColor']) || !empty($var['ContentSpecification'])){
						if(empty($var['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($var['ContentColor']));
						if(empty($var['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($var['ContentSpecification']));
						$db->query("update ".$sdbname.DATATABLE."_order_inventory_number set ContentNumber=ContentNumber+".$outarr['g'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
					}
				}
			}
		}
		//发货明细
		$db->query("delete from ".$sdbname.DATATABLE."_order_out_library where CompanyID = ".$cid." and ConsignmentID IN (".$deleteidmsg.") ");
		
		//订单状态
		$db->query("update ".$sdbname.DATATABLE."_order_orderinfo set OrderSendStatus=1, OrderStatus=1 where OrderID IN  (".implode(",",$orderidarr).") and OrderCompany=".$cid."");

		exit('ok');
	}else{
		exit('删除不成功!');
	}
}
elseif($in['m']=="delete_finance")
{
	if(!empty($in['did'])){
		$deleteidmsg = intval($in['did']);
	}elseif(!empty($in['selectedID'])){
		$deleteidmsg = implode(",", $in['selectedID']);
	}else{
		exit('请先选择您要删除的内容！');
	}
	$fdata = $db->get_results("select * from ".$sdbname.DATATABLE."_order_finance where FinanceCompany = ".$cid." and FinanceID IN (".$deleteidmsg.") ");

	$upsql =  "delete from ".$sdbname.DATATABLE."_order_finance where FinanceCompany = ".$cid." and FinanceID IN (".$deleteidmsg.") ";
	if($db->query($upsql))
	{
		//更新订单状态
		foreach($fdata as $v)
		{
			if(!empty($v['FinanceOrder'])){
				if(strpos($v['FinanceOrder'],",")) $omsg = str_replace(",","','",$v['FinanceOrder']); else $omsg = $v['FinanceOrder'];
				$db->query("update ".$sdbname.DATATABLE."_order_orderinfo set OrderPayStatus=0 where OrderSN IN  ('".$omsg."') and OrderCompany=".$cid."");

			}
		}
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}
elseif($in['m']=="delete_expense")
{
	if(!empty($in['did'])){
		$deleteidmsg = intval($in['did']);
	}elseif(!empty($in['selectedID'])){
		$deleteidmsg = implode(",", $in['selectedID']);
	}else{
		exit('请先选择您要删除的内容！');
	}

	$upsql =  "delete from ".$sdbname.DATATABLE."_order_expense where CompanyID = ".$cid." and ExpenseID IN (".$deleteidmsg.") ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}
elseif($in['m']=="delete_library")
{
	if(!empty($in['did'])){
		$deleteidmsg = intval($in['did']);
	}elseif(!empty($in['selectedID'])){
		$deleteidmsg = implode(",", $in['selectedID']);
	}else{
		exit('请先选择您要删除的内容！');
	}

	$upsql =  "delete from ".$sdbname.DATATABLE."_order_storage where CompanyID = ".$cid." and StorageID IN (".$deleteidmsg.") ";
	if($db->query($upsql))
	{
		//库存
		$valuearr = get_set_arr('product');
		if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){
			//总库存
			$ldata = $db->get_results("select * from ".$sdbname.DATATABLE."_order_storage_number where CompanyID = ".$cid." and StorageID IN (".$deleteidmsg.") ");	
			foreach($ldata as $v){
				$isup = $db->query("update ".$sdbname.DATATABLE."_order_number set OrderNumber=OrderNumber-".$v['ContentNumber'].", ContentNumber=ContentNumber-".$v['ContentNumber']." where CompanyID=".$cid." and ContentID=".$v['ContentID']);
			}
			//子库存
			$ldatacs = $db->get_results("select * from ".$sdbname.DATATABLE."_order_storage_number_cs where CompanyID = ".$cid." and StorageID IN (".$deleteidmsg.") ");			
			foreach($ldatacs as $v){
				$iscsup = $db->query("update ".$sdbname.DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$v['ContentNumber'].", ContentNumber=ContentNumber-".$v['ContentNumber']." where CompanyID=".$cid." and ContentID=".$v['ContentID']." and ContentColor='".$v['ContentColor']."' and ContentSpec='".$v['ContentSpec']."'");
			}
		}

		//删除明细
		$db->query("delete from ".$sdbname.DATATABLE."_order_storage_number where CompanyID = ".$cid." and StorageID IN (".$deleteidmsg.") ");
		$db->query("delete from ".$sdbname.DATATABLE."_order_storage_number_cs where CompanyID = ".$cid." and StorageID IN (".$deleteidmsg.") ");		
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}
elseif($in['m']=="delete_return")
{
	if(!empty($in['did'])){
		$deleteidmsg = intval($in['did']);
	}elseif(!empty($in['selectedID'])){
		$deleteidmsg = implode(",", $in['selectedID']);
	}else{
		exit('请先选择您要删除的内容！');
	}

	$upsql =  "delete from ".$sdbname.DATATABLE."_order_returninfo where ReturnCompany = ".$cid." and ReturnID IN (".$deleteidmsg.") ";
	if($db->query($upsql))
	{
		//删除明细
		$db->query("delete from ".$sdbname.DATATABLE."_order_cart_return where CompanyID = ".$cid." and ReturnID IN (".$deleteidmsg.") ");
		$db->query("delete from ".$sdbname.DATATABLE."_order_returnsubmit where CompanyID = ".$cid." and OrderID IN (".$deleteidmsg.") ");		
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}
elseif($in['m']=="delete_product")
{
	if(!empty($in['did'])){
		$deleteidmsg = intval($in['did']);
	}elseif(!empty($in['selectedID'])){
		$deleteidmsg = implode(",", $in['selectedID']);
	}else{
		exit('请先选择您要删除的内容！');
	}

	$upsql =  "delete from ".$sdbname.DATATABLE."_order_content_index where CompanyID = ".$cid." and ID IN (".$deleteidmsg.") ";
	if($db->query($upsql))
	{
		//关联详细
		$db->query("delete from ".$sdbname.DATATABLE."_order_content_1 where CompanyID = ".$cid." and ContentIndexID IN (".$deleteidmsg.") ");
		$db->query("delete from ".$sdbname.DATATABLE."_order_resource where CompanyID = ".$cid." and IndexID IN (".$deleteidmsg.") ");		
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}
elseif($in['m']=="clear_library")
{
	$s1 = $db->query("update ".$sdbname.DATATABLE."_order_inventory_number set OrderNumber=0,ContentNumber=0 where CompanyID = ".$cid." ");
	$s2 = $db->query("update ".$sdbname.DATATABLE."_order_number set OrderNumber=0,ContentNumber=0 where CompanyID = ".$cid." ");

	if($s2){
		exit('ok');
	}else{
		exit('执行不成功!');
	}
}
else if($in['m'] == "del_orderinfo")
{
	$strDate = '';
	$dateArr = array(
			"1"		=> "OrderDate",
			"2"		=> "ReturnDate",
			"3"		=> "InputDate",//填写日期
			"4"		=> "FinanceDate",//填写日期
			"5"		=> "StorageDate"
	);
	if(empty($cid) || empty($in['OrderType']))
	    exit('请先选择您要删除内容的客户或数据类型！');
	
	if(!empty($in['SDate']))
	{
		$strDate .= ' AND '.$dateArr[$in['OrderType']].' >='.strtotime($in['SDate'].' 00:00:00');
	}
	if(!empty($in['EDate']))
	{
	    $strDate .= ' AND '.$dateArr[$in['OrderType']].' <='.strtotime($in['EDate'].' 23:59:59');
	}
	
	$upsql = '';

	if($in['OrderType'] == '1') /** 删除订单数据 **/
	{
	    $strSQL = "Select GROUP_CONCAT(OrderID SEPARATOR ',') AS OrderID FROM ".$sdbname.DATATABLE."_order_orderinfo where OrderCompany = ".$cid.$strDate.' limit 0,1';
		$orderid = $db->get_row($strSQL);
		$deleteidmsg = $orderid['OrderID'];
        
		if(!empty($deleteidmsg))
		{
		    $upsql =  "delete from ".$sdbname.DATATABLE."_order_orderinfo where OrderCompany = ".$cid.(empty($strDate) ? "" : " and OrderID IN (".$deleteidmsg.") ");
		    if($db->query($upsql))
		    {
		        //记录日志
		        $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除订单:{$strSQL}\")");
		        
		        $db->query("delete from ".$sdbname.DATATABLE."_order_cart where CompanyID = ".$cid.(empty($strDate) ? "" : " and OrderID IN (".$deleteidmsg.") "));
		        $db->query("delete from ".$sdbname.DATATABLE."_order_ordersubmit where CompanyID = ".$cid.(empty($strDate) ? "" : " and OrderID IN (".$deleteidmsg.") "));
		        exit('ok');
		    }
		    else
		    {
		        exit('未影响任何行!');
		    }
		}
		else
		{
		    exit('未找到满足条件的数据!');
		}
		
	}
	else if($in['OrderType'] == '2') /** 删除退单数据 **/
	{
	    $strSQL = "Select GROUP_CONCAT(ReturnID SEPARATOR ',') AS ReturnID FROM ".$sdbname.DATATABLE."_order_returninfo where ReturnCompany = ".$cid.$strDate.' limit 0,1';
	    $orderid = $db->get_row($strSQL);
	    $deleteidmsg = $orderid['ReturnID'];

	    if(!empty($deleteidmsg))
	    {
	        $upsql =  "delete from ".$sdbname.DATATABLE."_order_returninfo where ReturnCompany = ".$cid.(empty($strDate) ? "" : " and ReturnID IN (".$deleteidmsg.") ");
	        if($db->query($upsql))
	        {
	            //记录日志
	            $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除退单:{$strSQL}\")");
	            //删除明细
	            $db->query("delete from ".$sdbname.DATATABLE."_order_cart_return where CompanyID = ".$cid.(empty($strDate) ? "" : " and ReturnID IN (".$deleteidmsg.") "));
	            $db->query("delete from ".$sdbname.DATATABLE."_order_returnsubmit where CompanyID = ".$cid.(empty($strDate) ? "" : " and OrderID IN (".$deleteidmsg.") "));
	            exit('ok');
	        }
	        else
	        {
	            exit('未影响任何行!');
	        }
	    }
	    else
	    {
	        exit('未找到满足条件的数据!');
	    }
	}
	else if($in['OrderType'] == '3') /** 删除发货单数据 **/
	{
	    $strSQL = "Select GROUP_CONCAT(ConsignmentID SEPARATOR ',') AS ConsignmentID FROM ".$sdbname.DATATABLE."_order_consignment where ConsignmentCompany = ".$cid.$strDate.' limit 0,1';
	    $orderid = $db->get_row($strSQL);
	    $deleteidmsg = $orderid['ConsignmentID'];

	    if(!empty($deleteidmsg))
	    {
	        //单头
	        $upsql =  "delete from ".$sdbname.DATATABLE."_order_consignment where ConsignmentCompany = ".$cid.(empty($strDate) ? "" : " and ConsignmentID IN (".$deleteidmsg.") ");
	        if($db->query($upsql))
	        {
	            //记录日志
	            $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除发货单:{$strSQL}\")");
	            
	            $outdata = $db->get_results("select * from ".$sdbname.DATATABLE."_order_out_library where CompanyID = ".$cid.(empty($strDate) ? "" : " and ConsignmentID IN (".$deleteidmsg.") "));
	             
	            $orderidarr = null;
	            foreach($outdata as $k=>$v){
	                $outarr[$v['ConType']][$v['OrderID'].'_'.$v['CartID']] = $v['ContentNumber'];
	                if(!in_array($v['OrderID'],$orderidarr)) $orderidarr[] = $v['OrderID'];
	            }
	            //正品
	            foreach($outarr['c'] as $ck=>$cv){
	                $idmsg = explode("_",$ck);
	                $oid = $idmsg[0];
	                $cartid = $idmsg[1];
	                $cartarr[] = $cartid;
	                //发货数量
	                $db->query("update ".$sdbname.DATATABLE."_order_cart set ContentSend=ContentSend-".$cv." where ID=".$cartid." and CompanyID = ".$cid." and ContentSend >= ".$cv." ");
	            }
	            //赠品
	            foreach($outarr['g'] as $ck=>$cv){
	                $idmsg = explode("_",$ck);
	                $oid = $idmsg[0];
	                $cartid = $idmsg[1];
	                $giftsarr[] = $cartid;
	                $db->query("update ".$sdbname.DATATABLE."_order_cart_gifts set ContentSend=ContentSend-".$cv." where ID=".$cartid." and CompanyID = ".$cid." and ContentSend >= ".$cv." ");
	            }
	             
	            //库存
	            $valuearr = get_set_arr('product');
	            if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){
	                $tykey  = str_replace($fp,$rp,base64_encode("统一"));
	                //正品
	                if(!empty($cartarr)){
	                    $libdata = $db->get_results("select ID,OrderID,ContentID,ContentColor,ContentSpecification from ".$sdbname.DATATABLE."_order_cart where CompanyID = ".$cid." and ID IN (".implode(",",$cartarr).") ");
	                    foreach($libdata as $var){
	                        if(empty($outarr['c'][$var['OrderID'].'_'.$var['ID']])) continue;
	                        $db->query("update ".$sdbname.DATATABLE."_order_number set ContentNumber=ContentNumber+".$outarr['c'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." limit 1");
	                        if(!empty($var['ContentColor']) || !empty($var['ContentSpecification'])){
	                            if(empty($var['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($var['ContentColor']));
	                            if(empty($var['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($var['ContentSpecification']));
	                            $db->query("update ".$sdbname.DATATABLE."_order_inventory_number set ContentNumber=ContentNumber+".$outarr['c'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
	                        }
	                    }
	                }
	                //赠品
	                if(!empty($giftsarr)){
	                    $libdata = $db->get_results("select ID,OrderID,ContentID,ContentColor,ContentSpecification from ".$sdbname.DATATABLE."_order_cart_gifts where CompanyID = ".$cid." and ID IN (".implode(",",$giftsarr).") ");
	                    foreach($libdata as $var){
	                        if(empty($outarr['g'][$var['OrderID'].'_'.$var['ID']])) continue;
	                        $db->query("update ".$sdbname.DATATABLE."_order_number set ContentNumber=ContentNumber+".$outarr['g'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." limit 1");
	                        if(!empty($var['ContentColor']) || !empty($var['ContentSpecification'])){
	                            if(empty($var['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($fp,$rp,base64_encode($var['ContentColor']));
	                            if(empty($var['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($fp,$rp,base64_encode($var['ContentSpecification']));
	                            $db->query("update ".$sdbname.DATATABLE."_order_inventory_number set ContentNumber=ContentNumber+".$outarr['g'][$var['OrderID'].'_'.$var['ID']]." where CompanyID=".$cid." and ContentID=".$var['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
	                        }
	                    }
	                }
	            }
	            //发货明细
	            $db->query("delete from ".$sdbname.DATATABLE."_order_out_library where CompanyID = ".$cid.(empty($strDate) ? "" : " and ConsignmentID IN (".$deleteidmsg.") "));
	             
	            //订单状态
	            $db->query("update ".$sdbname.DATATABLE."_order_orderinfo set OrderSendStatus=1, OrderStatus=1 where OrderID IN  (".implode(",",$orderidarr).") and OrderCompany=".$cid."");
	             
	            exit('ok');
	        }else{
	            exit('未影响任何行!');
	        }
	    }
	    else
	    {
	        exit('未找到满足条件的数据!');
	    }
	}
	else if($in['OrderType'] == '4') /** 删除收款单数据 **/
	{
	    $strSQL = "SELECT GROUP_CONCAT(FinanceID SEPARATOR ',') AS FinanceID,GROUP_CONCAT(FinanceOrder SEPARATOR ';') AS FinanceOrder from ".$sdbname.DATATABLE."_order_finance where FinanceCompany = ".$cid.$strDate.' limit 0,1';
	    $fdata = $db->get_row($strSQL);
	    $deleteidmsg = $fdata['FinanceID'];

	    if(!empty($deleteidmsg))
	    {
	        $upsql =  "delete from ".$sdbname.DATATABLE."_order_finance where FinanceCompany = ".$cid.(empty($strDate) ? "" : " and FinanceID IN (".$deleteidmsg.") ");
	        if($db->query($upsql))
	        {
	            //记录日志
	            $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除收款单:{$strSQL}\")");
	            
	            $strorder = explode(';', $fdata['FinanceOrder']) ;
	            //更新订单状态
	            foreach($strorder as $v)
	            {
	                if(!empty($v)){
	                    if(strpos($v,",")) $omsg = str_replace(",","','",$v); else $omsg = $v;
	                    $db->query("update ".$sdbname.DATATABLE."_order_orderinfo set OrderPayStatus=0 where OrderSN IN  ('".$omsg."') and OrderCompany=".$cid."");
	                     
	                }
	            }
	            exit('ok');
	        }else{
	            exit('未影响任何行!');
	        }
	    }
	    else
	    {
	        exit('未找到满足条件的数据!');
	    }
	}
	else if($in['OrderType'] == '5') /** 删除入库单数据 **/
	{
	    $strSQL = "Select GROUP_CONCAT(StorageID SEPARATOR ',') AS StorageID FROM ".$sdbname.DATATABLE."_order_storage where CompanyID = ".$cid.$strDate.' limit 0,1';
	    $orderid = $db->get_row($strSQL);
	    $deleteidmsg = $orderid['StorageID'];

	    if(!empty($deleteidmsg))
	    {
	        $upsql =  "delete from ".$sdbname.DATATABLE."_order_storage where CompanyID = ".$cid.(empty($strDate) ? "" : " and StorageID IN (".$deleteidmsg.") ");
	        if($db->query($upsql))
	        {
	            //记录日志
	            $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除入库单:{$strSQL}\")");
	             
	            //库存
	            $valuearr = get_set_arr('product');
	            if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on"){
	                //总库存
	                $ldata = $db->get_results("select * from ".$sdbname.DATATABLE."_order_storage_number where CompanyID = ".$cid.(empty($strDate) ? "" : " and StorageID IN (".$deleteidmsg.") "));
	                foreach($ldata as $v){
	                    $isup = $db->query("update ".$sdbname.DATATABLE."_order_number set OrderNumber=OrderNumber-".$v['ContentNumber'].", ContentNumber=ContentNumber-".$v['ContentNumber']." where CompanyID=".$cid." and ContentID=".$v['ContentID']);
	                }
	                //子库存
	                $ldatacs = $db->get_results("select * from ".$sdbname.DATATABLE."_order_storage_number_cs where CompanyID = ".$cid.(empty($strDate) ? "" : " and StorageID IN (".$deleteidmsg.") "));
	                foreach($ldatacs as $v){
	                    $iscsup = $db->query("update ".$sdbname.DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$v['ContentNumber'].", ContentNumber=ContentNumber-".$v['ContentNumber']." where CompanyID=".$cid." and ContentID=".$v['ContentID']." and ContentColor='".$v['ContentColor']."' and ContentSpec='".$v['ContentSpec']."'");
	                }
	            }
	             
	            //删除明细
	            $db->query("delete from ".$sdbname.DATATABLE."_order_storage_number where CompanyID = ".$cid.(empty($strDate) ? "" : " and StorageID IN (".$deleteidmsg.") "));
	            $db->query("delete from ".$sdbname.DATATABLE."_order_storage_number_cs where CompanyID = ".$cid.(empty($strDate) ? "" : " and StorageID IN (".$deleteidmsg.") "));
	            exit('ok');
	        }else{
	            exit('未影响任何行!');
	        }
	    }
	    else
	    {
	        exit('未找到满足条件的数据!');
	    }
	}
}
else if($in['m'] == "del_storage")
{
    //库存
    $valuearr = get_set_arr('product');
    if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
    {
        //记录日志
        $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"清空库存:客户ID为{$cid}\")");
         
        //总库存
        $ldata = $db->query("delete from ".$sdbname.DATATABLE."_order_storage_number where CompanyID = ".$cid);
        if($ldata)
        {
            $db->query("update ".$sdbname.DATATABLE."_order_number set OrderNumber = 0 , ContentNumber = 0 where CompanyID=".$cid);
            $db->query("delete from ".$sdbname.DATATABLE."_order_storage_number_cs where CompanyID = ".$cid);
            $db->query("update ".$sdbname.DATATABLE."_order_inventory_number set OrderNumber=0, ContentNumber=0 where CompanyID=".$cid);
            
            exit('ok');
        }
        else 
        {
            exit('未影响任何行');
        }
    }
    else 
        exit('该客户未启用库存！');
    
}
else if($in['m'] == "del_product")
{
    if(empty($in['ptype']))
    {
        exit('请选择要删除的商品');
    }
    
    $strcondition= '';
    if($in['ptype'] == 'Parts')
    {
        $strcondition .= ' AND FlagID = 1';
    }
    
    $strSQL = "Select GROUP_CONCAT(ID SEPARATOR ',') AS ID FROM ".$sdbname.DATATABLE."_order_content_index where CompanyID = ".$cid.$strcondition.' limit 0,1';
    $orderid = $db->get_row($strSQL);
    $deleteidmsg = $orderid['ID'];

    if(!empty($deleteidmsg))
    {
        $upsql =  "delete from ".$sdbname.DATATABLE."_order_content_index where CompanyID = ".$cid.(empty($strcondition) ? "" : " and ID IN (".$deleteidmsg.") ");
        if($db->query($upsql))
        {
            //记录日志
            $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除商品:{$strSQL}\")");
             
            //关联详细
            $db->query("delete from ".$sdbname.DATATABLE."_order_content_1 where CompanyID = ".$cid.(empty($strcondition) ? "" : " and ContentIndexID IN (".$deleteidmsg.") "));
            $db->query("delete from ".$sdbname.DATATABLE."_order_resource where CompanyID = ".$cid.(empty($strcondition) ? "" : " and IndexID IN (".$deleteidmsg.") "));
            exit('ok');
        }else{
            exit('未影响任何行!');
        }
    }
    else 
        exit('未找到满足条件的数据!');
}
else if($in['m'] == "del_productsite")
{
    $upsql =  "DELETE FROM ".$sdbname.DATATABLE."_order_site WHERE CompanyID = ".$cid;
    if($db->query($upsql))
    {
        //记录日志
        $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除商品分类:{$upsql}\")");
         
        //重置商品的分类字段
        $db->query("UPDATE ".$sdbname.DATATABLE."_order_content_index SET SiteID = 0 WHERE CompanyID = ".$cid );
        exit('ok');
    }else{
        exit('未影响任何行!');
    }
}
else if($in['m'] == "del_Client")
{
    if(empty($in['ctype']))
    {
        exit('请选择要删除的经销商');
    }
    
    $strcondition= '';
    if($in['ctype'] == 'Recycle')
    {
        $strcondition .= ' AND ClientFlag = 1';
    }
    
    $upsql =  "delete from ".$sdbname.DATATABLE."_order_client where ClientCompany = ".$cid.$strcondition;
    if($db->query($upsql))
    {
        //记录日志
        $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除经销商:{$upsql}\")");
         
        exit('ok');
    }else
    {
        exit('未影响任何行!');
    }
}
else if($in['m'] == "del_clientarea")
{
    $upsql =  "DELETE FROM ".$sdbname.DATATABLE."_order_area WHERE AreaCompany = ".$cid;
    if($db->query($upsql))
    {
        //记录日志
        $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除经销商地区:{$upsql}\")");
         
        //重置经销商的地区字段
        $db->query("UPDATE ".$sdbname.DATATABLE."_order_client SET ClientArea = 0 WHERE ClientCompany = ".$cid );
        exit('ok');
    }else{
        exit('未影响任何行!');
    }
}
else if($in['m'] == "del_Info")
{
    if(empty($in['infotype']))
    {
        exit('请选择要删除的信息!');
    }

    $strcondition= '';
    if($in['infotype'] == 'Recycle')
    {
        $strcondition .= ' AND ArticleFlag = 1';
    }

    $upsql =  "delete from ".$sdbname.DATATABLE."_order_article where ArticleCompany1 = ".$cid.$strcondition;
    if($db->query($upsql))
    {
        //记录日志
        $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除信息:{$upsql}\")");
         
        exit('ok');
    }else
    {
        exit('未影响任何行!');
    }
}
else if($in['m'] == "del_InfoCategory")
{
    $upsql =  "DELETE FROM ".$sdbname.DATATABLE."_order_sort WHERE SortCompany = ".$cid;
    if($db->query($upsql))
    {
        //记录日志
        $db->query("insert into ".DATABASEU.DATATABLE."_order_company_log (CompanyID,CreateDate,CreateUser,Content) values ({$_SESSION['uinfo']['ucompany']},".time().",'{$_SESSION['uinfo']['username']}',\"删除信息分类:{$upsql}\")");
         
        //重置经销商的地区字段
        $db->query("UPDATE ".$sdbname.DATATABLE."_order_article SET ArticleSort = 0 WHERE ArticleCompany = ".$cid );
        exit('ok');
    }else{
        exit('未影响任何行!');
    }
}
else if($in['m'] == "delete_mul_data")
{
    if(empty($in['OrderType'])) exit('未指定数据类型');
    if(empty($in['SDate']) && empty($in['EDate'])){
    	$delall = true;
    }else{
    	$delall = false;
    }
    foreach($in['OrderType'] as $v){
    	$sqlmsg = '';
    	if($v == 'order'){
    		if(!$delall){
    			$msg = '';
    			if(!empty($in['SDate'])){
    				$msg = " and OrderDate >= ".strtotime($in['SDate']." 00:00:00")." ";
    				$MinOrderID = $db->get_var("select MIN(OrderID) from ".$sdbname."rsung_order_orderinfo where OrderCompany = ".$cid." ".$msg." ");
    				if(!empty($MinOrderID)) $sqlmsg .= " and OrderID >= ".$MinOrderID." ";
    			}
    			$msg = '';
    			if(!empty($in['EDate'])){
    				$msg = " and OrderDate <= ".strtotime($in['EDate']." 23:59:59")." ";
    				$MaxOrderID = $db->get_var("select MAX(OrderID) from ".$sdbname."rsung_order_orderinfo where OrderCompany = ".$cid." ".$msg." ");
    				if(!empty($MaxOrderID)) $sqlmsg .= " and OrderID <= ".$MaxOrderID." ";
    			}    			
    		}
    		
    		$delsql = 'DELETE FROM '.$sdbname.'rsung_order_cart WHERE `CompanyID`='.$cid.$sqlmsg.'' ;
    		$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_cartbak WHERE `CompanyID`='.$cid.$sqlmsg.'' ;
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_cart_gifts WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_orderinfo WHERE `OrderCompany`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_ordersubmit WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
    	
    	}elseif($v == 'return'){
    		if(!$delall){
    			$msg = '';
    			if(!empty($in['SDate'])){
    				$msg = " and ReturnDate >= ".strtotime($in['SDate']." 00:00:00")." ";
    				$MinOrderID = $db->get_var("select MIN(ReturnID) from ".$sdbname."rsung_order_returninfo where OrderCompany = ".$cid." ".$msg." ");
    				if(!empty($MinOrderID)) $sqlmsg .= " and ReturnID >= ".$MinOrderID." ";
    			}
    			$msg = '';
    			if(!empty($in['EDate'])){
    				$msg = " and ReturnDate <= ".strtotime($in['EDate']." 23:59:59")." ";
    				$MaxOrderID = $db->get_var("select MAX(ReturnID) from ".$sdbname."rsung_order_returninfo where ReturnCompany = ".$cid." ".$msg." ");
    				if(!empty($MaxOrderID)) $sqlmsg .= " and ReturnID <= ".$MaxOrderID." ";
    			}    			
    		}
    		$delsql = 'DELETE FROM '.$sdbname.'rsung_order_cart_return WHERE `CompanyID`='.$cid.$sqlmsg.'' ;
    		$isorder = $db->query($delsql);
    		$delsql = 'DELETE FROM '.$sdbname.'rsung_order_return_cartbak WHERE `CompanyID`='.$cid.$sqlmsg.'' ;
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_returninfo WHERE `ReturnCompany`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_returnsubmit WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);

		//
    	}elseif($v == 'consignment'){
    		if(!$delall){
    			$msg = '';
    			if(!empty($in['SDate'])){
    				$msg = " and InputDate >= ".strtotime($in['SDate']." 00:00:00")." ";
    				$MinOrderID = $db->get_var("select MIN(ConsignmentID) from ".$sdbname."rsung_order_consignment where ConsignmentCompany = ".$cid." ".$msg." ");
    				if(!empty($MinOrderID)) $sqlmsg .= " and ConsignmentID >= ".$MinOrderID." ";
    			}
    			$msg = '';
    			if(!empty($in['EDate'])){
    				$msg = " and InputDate <= ".strtotime($in['EDate']." 23:59:59")." ";
    				$MaxOrderID = $db->get_var("select MAX(ConsignmentID) from ".$sdbname."rsung_order_consignment where ConsignmentCompany = ".$cid." ".$msg." ");
    				if(!empty($MaxOrderID)) $sqlmsg .= " and ConsignmentID <= ".$MaxOrderID." ";
    			}    			
    		}
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_consignment WHERE `ConsignmentCompany`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_out_library WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);


    	}elseif($v == 'finance'){
    		if(!$delall){
    			$msg = '';
    			if(!empty($in['SDate'])){
    				$msg = " and FinanceDate >= ".strtotime($in['SDate']." 00:00:00")." ";
    				$MinOrderID = $db->get_var("select MIN(FinanceID) from ".$sdbname."rsung_order_finance where FinanceCompany = ".$cid." ".$msg." ");
    				if(!empty($MinOrderID)) $sqlmsg .= " and FinanceID >= ".$MinOrderID." ";
    			}
    			$msg = '';
    			if(!empty($in['EDate'])){
    				$msg = " and FinanceDate <= ".strtotime($in['EDate']." 23:59:59")." ";
    				$MaxOrderID = $db->get_var("select MAX(FinanceID) from ".$sdbname."rsung_order_finance where FinanceCompany = ".$cid." ".$msg." ");
    				if(!empty($MaxOrderID)) $sqlmsg .= " and FinanceID <= ".$MaxOrderID." ";
    			}    			
    		}
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_finance WHERE `FinanceCompany`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
    	}elseif($v == 'expense'){
    		if(!$delall){
    			$msg = '';
    			if(!empty($in['SDate'])){
    				$msg = " and ExpenseTime >= ".strtotime($in['SDate']." 00:00:00")." ";
    				$MinOrderID = $db->get_var("select MIN(ExpenseID) from ".$sdbname."rsung_order_expense where CompanyID = ".$cid." ".$msg." ");
    				if(!empty($MinOrderID)) $sqlmsg .= " and ExpenseID >= ".$MinOrderID." ";
    			}
    			$msg = '';
    			if(!empty($in['EDate'])){
    				$msg = " and ExpenseTime <= ".strtotime($in['EDate']." 23:59:59")." ";
    				$MaxOrderID = $db->get_var("select MAX(ExpenseID) from ".$sdbname."rsung_order_expense where CompanyID = ".$cid." ".$msg." ");
    				if(!empty($MaxOrderID)) $sqlmsg .= " and ExpenseID <= ".$MaxOrderID." ";
    			}    			
    		}
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_expense WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);


    	}elseif($v == 'invertory'){

			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_storage WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_storage_number WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
			$delsql = 'DELETE FROM '.$sdbname.'rsung_order_storage_number_cs WHERE `CompanyID`='.$cid.$sqlmsg.'';
			$isorder = $db->query($delsql);
    	}
    }

    exit('ok');

}elseif($in['m'] == 'delete_sigle_data'){
	if(empty($in['data_type'])) exit('未指定数据类型');
		
	if($in['data_type'] == 'library'){
		$delsql = 'update '.$sdbname.'rsung_order_inventory_number set OrderNumber=0,ContentNumber=0 WHERE `CompanyID`='.$cid.'';
		$isorder = $db->query($delsql);
		$delsql = 'update '.$sdbname.'rsung_order_number set OrderNumber=0,ContentNumber=0 WHERE `CompanyID`='.$cid.'';
		$isorder = $db->query($delsql);
	}elseif($in['data_type'] == 'goods'){
		$smsg = '';
		if($in['Products'] == 'Recycle'){
			$smsg = " and FlagID=1 ";
		}
		$delsql = 'DELETE FROM '.$sdbname.'rsung_order_content_index WHERE `CompanyID`='.$cid.$smsg.'';
		$isorder = $db->query($delsql);
		if($in['Products'] == 'All'){
			$delsql  = 'DELETE FROM '.$sdbname.'rsung_order_content_1 WHERE `CompanyID`='.$cid.'';
			$isorder = $db->query($delsql);
			$delsql  = 'DELETE FROM '.$sdbname.'rsung_order_resource WHERE `CompanyID`='.$cid.'';
			$isorder = $db->query($delsql);
		}
	}elseif($in['data_type'] == 'category'){
		$delsql  = 'DELETE FROM '.$sdbname.'rsung_order_site WHERE `CompanyID`='.$cid.'';
		$isorder = $db->query($delsql);
	}elseif($in['data_type'] == 'client'){
		if($in['Client'] == 'Recycle'){
			$smsg = " and ClientFlag=1 ";
		}
		$delsql  = 'DELETE FROM '.$sdbname.'rsung_order_client WHERE `ClientCompany`='.$cid.$smsg.'';
		$isorder = $db->query($delsql);
		$delsql  = 'delete from etong_db_live_user.rsung_order_dealers where ClientCompany='.$cid.$smsg.'';
		$isorder = $db->query($delsql);
	}elseif($in['data_type'] == 'area'){
		$delsql  = 'DELETE FROM '.$sdbname.'rsung_order_area WHERE `AreaCompany`='.$cid.'';
		$isorder = $db->query($delsql);


	}elseif($in['data_type'] == 'infomation'){
		$delsql  = 'DELETE FROM '.$sdbname.'rsung_order_article WHERE `ArticleCompany`='.$cid.'';
		$isorder = $db->query($delsql);

	}elseif($in['data_type'] == 'sort'){
		$delsql  = 'DELETE FROM '.$sdbname.'rsung_order_sort WHERE SortCompany='.$cid.'';
		$isorder = $db->query($delsql);
	}
exit('ok');

}

exit('非法操作!!!');
?>