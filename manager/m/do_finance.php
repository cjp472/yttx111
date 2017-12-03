<?php
$menu_flag = "finance";
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
include_once ("arr_data.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

/***********save_finance**************/
if($in['m']=="finance_add_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	//if(empty($in['data_FinanceAccounts'])) exit('请选择收款帐号!');
	$financeordermsg = '';
	if(!empty($in['FinanceYufu'])){
		$financeordermsg = '0';
	}else{
		if(!empty($in['FinanceOrder'])){
			$financeordermsg = implode(",", $in['FinanceOrder']);
		}
	}
	if(empty($in['finance_type'])) $in['finance_type'] = 'Z';
	$in['data_FinanceOrderID'] = intval($in['data_FinanceOrderID']);
	$in['data_FinanceTotal']   = abs(floatval($in['data_FinanceTotal']));
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('FinanceType', $in['finance_type']);
	$data_->addData('FinanceOrder', $financeordermsg);
	$data_->addData('FinanceCompany', $_SESSION['uinfo']['ucompany']);
	$data_->addData('FinanceDate', time());
	$data_->addData('FinanceUser', $_SESSION['uinfo']['username']);

	$insert_id = $data_->dataInsert ("_order_finance");
	if(!empty($insert_id))
	{
		if(!empty($in['FinanceOrder']) && empty($in['FinanceYufu']))
		{
			foreach($in['FinanceOrder'] as $ovar)
			{
				if(!empty($ovar))
				{
					$db->query("update ".DATATABLE."_order_orderinfo set OrderPayStatus=1 where OrderSN = '".$ovar."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderPayStatus=0 ");
				}
			}
		}
		$ins['ID'] = $insert_id;
		if($in['finance_type'] == 'Y') set_validate($ins,$db);
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}


if($in['m']=="finance_edit_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['data_FinanceAccounts'])) exit('请选择收款帐号!');

	$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_finance where FinanceID = ".$in['FinanceID']." and FinanceCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$infodatamsg = serialize($InfoData);
	$in['data_FinanceTotal'] = abs(floatval($in['data_FinanceTotal']));

	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('FinanceUser', $_SESSION['uinfo']['username']);
	$wheremsg =" where FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceID=".$in['FinanceID'];

	$update = $data_->dataUpdate("_order_finance",$wheremsg);
	if(!empty($update))
	{		
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_finance.php?m=finance_edit_save&FinanceID=".$in['FinanceID']."','修改收款单(".$in['FinanceID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);		
		exit("ok");
	}else{
		exit("无变化!");
	}
}

if($in['m']=="delete_finance")
{
	if(!intval($in['ID'])) exit('错误的参数');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('对不起，您没有此项操作权限！');

	$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_finance where  FinanceID = ".$in['ID']." and FinanceCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$infodatamsg = serialize($InfoData);

	$upsql =  "delete from ".DATATABLE."_order_finance where FinanceID = ".$in['ID']." and FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceFlag=0 limit 1";	
	if($db->query($upsql))
	{
		if(!empty($InfoData['FinanceOrder']))
		{
			if(strpos($InfoData['FinanceOrder'],","))
			{
				$ordersn_arr = explode(",", $InfoData['FinanceOrder']);
				foreach($ordersn_arr as $osv)
				{
					$upsql =  "update ".DATATABLE."_order_orderinfo set OrderPayStatus=0 where OrderSN = '".$osv."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." and (OrderPayStatus=1 or OrderPayStatus=4)";//tubo修改付款状态，增加4的时候修改回来 2015-11-24
					$isup = $db->query($upsql);
				}
			}else{
				$upsql =  "update ".DATATABLE."_order_orderinfo set OrderPayStatus=0 where OrderSN = '".$InfoData['FinanceOrder']."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." and (OrderPayStatus=1 or OrderPayStatus=4)";//tubo修改付款状态，增加4的时候修改回来 2015-11-24
				$isup = $db->query($upsql);
			}
		}		
		
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_finance.php?m=delete_finance&ID=".$in['ID']."','删除收款单(".$in['ID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);	
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}


if($in['m']=="validate_finance")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('nopope');
	
	$sta = set_validate($in,$db);
	if($sta){
		$cinfo = $db->get_row("SELECT FinanceID,FinanceClient,FinanceOrder,FinanceTotal,FinanceToDate FROM ".DATATABLE."_order_finance where FinanceID=".$in['ID']." and FinanceCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
		$message = "【".$_SESSION['uc']['CompanySigned']."】您于".$cinfo['FinanceToDate']."支付款项:".$cinfo['FinanceTotal']."已确认到帐。退订回复TD";

		sms::get_setsms("4",$cinfo['FinanceClient'],$message);
		echo '<font color="blue">'.date("Y-m-d H:i").'</font>';
		exit();
	}else{
		exit('error!');
	}
}

if($in['m']=="loadorderlist")
{
	if(!intval($in['ID'])) exit('error');

	$orderlistuser = $db->get_results("SELECT OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus FROM ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." and OrderUserID=".$in['ID']." and (OrderPayStatus=0 or OrderPayStatus=1 or OrderPayStatus=3) and OrderStatus < 5 order by OrderID desc limit 0,100");
	$bodymsg = '';
	$headermsg = '<table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0">
                        <td width="8%">&nbsp;</td>
                        <td width="30%"><strong>&nbsp;订单号</strong></td>
                        <td width="25%"><strong>&nbsp;订单金额</strong></td>
						<td width="20%"><strong>&nbsp;已收金额</strong></td>
                        <td ><strong>&nbsp;状态</strong></td>
                      </tr>				  
					  ';
	foreach($orderlistuser as $olvar)
	{
		$counttotla = $olvar['OrderTotal']-$olvar['OrderIntegral'];
		$bodymsg .= '<tr height="28" id="selected_line_'.$olvar['OrderID'].'">
                        <td class="selectinput">&nbsp;<input  id="data_FinanceOrder_'.$olvar['OrderID'].'" name="FinanceOrder[]" type="checkbox" onclick="selectorderlinefocus(\''.$olvar['OrderID'].'\')" value="'.$olvar['OrderSN'].'" /></td>
                        <td onclick="selectorderline(\''.$olvar['OrderID'].'\')" >&nbsp;'.$olvar['OrderSN'].'</td>
                        <td onclick="selectorderline(\''.$olvar['OrderID'].'\')">&nbsp;¥ '.$olvar['OrderTotal'].'<input type="hidden" name="ordertotal[]" id="order_total_'.$olvar['OrderID'].'" value="'.$counttotla.'" /></td>
						<td onclick="selectorderline(\''.$olvar['OrderID'].'\')">&nbsp;¥ '.$olvar['OrderIntegral'].'</td>
                        <td onclick="selectorderline(\''.$olvar['OrderID'].'\')">&nbsp;'.$order_status_arr[$olvar['OrderStatus']].'</td>
                      </tr>';
	}

	$endmsg = '</table>';
	//if(empty($orderlistuser)) exit('该用户，无待付款订单');
	echo $headermsg.$bodymsg.$endmsg;
	exit();
}

/***********save_expense**************/
if($in['m']=="expense_add_save")
{
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');
	if(empty($in['data_ClientID'])) exit('请选择客户!');
	$financeordermsg = '';

	$in['data_ExpenseTotal'] = floatval($in['data_ExpenseTotal']);
	$data_ = new idata();
	$data_->flushData();
	$data_->filterData($in);
	$data_->addData('CompanyID', $_SESSION['uinfo']['ucompany']);
	$data_->addData('ExpenseTime', time());
	$data_->addData('ExpenseUser', $_SESSION['uinfo']['username']);

	$insert_id = $data_->dataInsert ("_order_expense");
	if(!empty($insert_id))
	{		
		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="validate_expense")
{
	if(!intval($in['ID'])) exit('error');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y') exit('nopope');

	$upsql =  "update ".DATATABLE."_order_expense set ExpenseTime=".time().",ExpenseUser='".$_SESSION['uinfo']['username']."',FlagID='2' where ExpenseID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";	
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('审核不成功!');
	}
}

if($in['m']=="delete_expense")
{
	if(!intval($in['ID'])) exit('错误的参数');
	if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_form'] != 'Y') exit('对不起，您没有此项操作权限！');

	$InfoData = $db->get_row("SELECT * FROM ".DATATABLE."_order_expense where ExpenseID = ".$in['ID']." and CompanyID = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
	$infodatamsg = serialize($InfoData);

	$upsql =  "delete from ".DATATABLE."_order_expense where ExpenseID = ".$in['ID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." and FlagID='1' limit 1";	
	if($db->query($upsql))
	{
		$sqlex = "insert into ".DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$_SESSION['uinfo']['ucompany'].", '".$_SESSION['uinfo']['username']."', 'do_finance.php?m=delete_expense&ID=".$in['ID']."','删除费用(".$in['ID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);	
		exit('ok');
	}else{
		exit('删除不成功!');
	}
}elseif($in['m']=="getSearInfo"){//易极付提现
	
	if(empty($in['sdate'])) $in['sdate'] = date('Y-m-d', strtotime('-30 days'));
	if(empty($in['edate'])) $in['edate'] = date('Y-m-d');
	
	$in['currPage'] = intval($in['currPage']);
	$in['pageSize'] = intval($in['pageSize']);
	$in['acType']	= strval($in['acType']);
	$currPage = $in['currPage'] + 1;
	$pageSize = $in['pageSize'] ? $in['pageSize'] : 5;
	
	$search = array(
			'accountType'	=> $in['acType'],
			'currPage'		=> $currPage,
			'pageSize'		=> $pageSize,
			'startTime'		=> $in['sdate']." 00:00:00",
			'endTime'		=> $in['edate']." 23:59:59"
	);
	
	$openApi = new YopenApiBackend($_SESSION['uc']['CompanyID']);
	$searInfo = $openApi->setGetway($in['acType'])->getSear($search);
	
	echo json_encode($searInfo);
	exit;
}elseif($in['m']=="setMyDefault"){
    
    $in['myDefault'] = strval($in['myDefault']);
    $in['dName']     = strval($in['dName']);
    
    
    if(empty($in['myDefault']) || empty($in['dName']) || empty($_SESSION['uc']['CompanyID'])) exit('error');
    
    //重置全部为非默认
    $upAllSql = "update ".DATABASEU.DATATABLE."_order_getway set IsDefault='N' where CompanyID=".$_SESSION['uc']['CompanyID']." ";
    $db->query($upAllSql);
    
    //设置指定账户为默认
    $upDefaultSql = "update ".DATABASEU.DATATABLE."_order_getway set IsDefault='Y' where CompanyID=".$_SESSION['uc']['CompanyID']." and SignNO='".$in['dName']."'";
    $db->query($upDefaultSql);
    
    exit;
}




//确认到帐
function set_validate($in,$db)
{
	$upsql =  "update ".DATATABLE."_order_finance set FinanceUpDate=".time().",FinanceAdmin='".$_SESSION['uinfo']['username']."',FinanceFlag=2 where FinanceID = ".$in['ID']." and FinanceCompany=".$_SESSION['uinfo']['ucompany']." and FinanceFlag=0 ";	
	if($status = $db->query($upsql))
	{
		$cinfo = $db->get_row("SELECT FinanceID,FinanceClient,FinanceOrder,FinanceTotal,FinanceToDate FROM ".DATATABLE."_order_finance where FinanceID=".$in['ID']." and FinanceCompany = ".$_SESSION['uinfo']['ucompany']." limit 0,1");
		if(!empty($cinfo['FinanceOrder']))
		{
			$ordersn_arr = explode(",", $cinfo['FinanceOrder']);
			$smmsg  = " '".str_replace(",","','",$cinfo['FinanceOrder'])."' ";
			$sqlarr = " and OrderSN IN (".$smmsg.") ";
			$sql_l  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlarr." order by OrderID asc ";
			$olist  =  $db->get_results($sql_l);

			if(!empty($olist))
			{
				$chatotal = $cinfo['FinanceTotal'];
				foreach($olist as $osv)
				{
					if(!empty($osv['OrderTotal']))
					{
						$chatotal = round($chatotal - $osv['OrderTotal'] + $osv['OrderIntegral'],2);				
						if($chatotal >= 0)
						{
							$upsql = "update ".DATATABLE."_order_orderinfo set OrderPayStatus=2, OrderIntegral='".$osv['OrderTotal']."' where OrderID = '".$osv['OrderID']."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 1";
							$isup  = $db->query($upsql);
						}else{
							$uptotal = $chatotal + $osv['OrderTotal'];
							$upsql = "update ".DATATABLE."_order_orderinfo set OrderPayStatus=3, OrderIntegral='".$uptotal."' where OrderID = '".$osv['OrderID']."' and OrderCompany=".$_SESSION['uinfo']['ucompany']." limit 1";
							$isup  = $db->query($upsql);
							break;
						}
					}
					$lastosv = $osv['OrderSN'];
				}				
			}
		}
		return $status;
	}
}

exit('非法操作!');
?>