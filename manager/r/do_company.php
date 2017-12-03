<?php
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
include_once ("../pro/function.inc.php");
if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="do_companyorder_pay")
{
	if(!intval($in['ID'])) exit('非法操作!');
	
	$selsql = "select * from ".DATABASEU.DATATABLE."_buy_order where id = ".$in['ID']." ";
	$orderdata = $db->get_row($selsql);
	
	if(empty($orderdata)){
		exit('订单信息不存在!');
	}
	$stream_no = build_order_no('stream');
	$insersql = "insert into ".DATABASEU.DATATABLE."_buy_stream (stream_no,order_no,company_id,pay_away,amount,trade_no,time,to_time,status,username) values('".$stream_no."','".$orderdata['order_no']."',".$orderdata['company_id'].",'line',".$orderdata['total'].",'".$in['account']."',".time().",".time().",'T','".$_SESSION['uinfo']['username']."')";
	if($db->query($insersql)){
		$upsql =  "update ".DATABASEU.DATATABLE."_buy_order set pay_status=1,integral=total where id = ".$in['ID']." ";
		if($db->query($upsql))
		{
			exit('ok');
		}else{
			exit('确认到账不成功!');
		}
	}
	else{
		exit('确认到账不成功!');
	}
	
}

if($in['m']=="do_companyorder_status")
{
	if(!intval($in['ID'])) exit('非法操作!');
	
	$selsql = "select * from ".DATABASEU.DATATABLE."_buy_order where id = ".$in['ID']." ";
	$orderdata = $db->get_row($selsql);
	
	if(empty($orderdata)){
		exit('订单信息不存在!');
	}
	$result = finish_order($db,$orderdata['order_no']);
	if($result){
		$upsql =  "update ".DATABASEU.DATATABLE."_buy_order set status=1 where id = ".$in['ID']." ";
		if($db->query($upsql))
		{
			exit('ok');
		}else{
			exit('订单支付开通失败!');
		}
	}
	else{
		exit('订单支付开通失败！');
	}
}

if($in['m']=="do_companyinvoice_edit")
{
	if(!intval($in['ID'])) exit('非法操作!');
	
	$upsql =  "update ".DATABASEU.DATATABLE."_buy_invoice set status='T',invoice_no='".$in['account']."',to_time='".$in['to_time']."' where id = ".$in['ID']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('订单支付开通失败!');
	}
	
}


exit('非法操作!');
?>