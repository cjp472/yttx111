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
if($in['m'] == 'is_reg_check') {
    $sql = "SELECT CompanyID,CompanyMobile FROM ".DATABASEU.DATATABLE."_order_company WHERE INSTR(',{$in['phones']},',CONCAT(',' , CompanyMobile,','))";
    $list = $db->get_results($sql);
    echo json_encode(array_column($list ? $list : array(),'CompanyMobile',null));

} else if($in['m']=="do_companyorder_pay")
{
	if(!intval($in['ID'])) exit('非法操作!');
	
	$selsql = "select * from ".DATABASEU.DATATABLE."_buy_order where id = ".$in['ID']." ";
	$orderdata = $db->get_row($selsql);
	
	if(empty($orderdata)){
		exit('订单信息不存在!');
	}
    $account = $in['account'];

	$stream_no = build_order_no('stream');
	$insersql = "insert into ".DATABASEU.DATATABLE."_buy_stream (stream_no,order_no,company_id,pay_away,amount,trade_no,time,to_time,status,username,remark) values('".$stream_no."','".$orderdata['order_no']."',".$orderdata['company_id'].",'line',".$orderdata['total'].",'".$account."',".time().",0,'F','".$_SESSION['uinfo']['username']."','{$in['remark']}')";
	if($db->query($insersql)){
        exit('ok');
	}
	else{
		exit('付款信息添加失败!');
	}
	
} else if($in['m'] == 'get_stream_info') {
    $order_id = $in['id'];
    $order_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_buy_order WHERE id=" . $order_id);
    $stream_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_buy_stream WHERE order_no='{$order_info['order_no']}' AND company_id=" . $order_info['company_id'] . " ORDER BY id DESC");
    if($stream_info) {
        $stream_info['time'] = date('Y/m/d H:i:s',$stream_info['time']);
        $stream_info['to_time'] = empty($stream_info['to_time']) ? '未到账' : date('Y/m/d H:i:s',$stream_info['to_time']);
        $stream_info['pay_status'] = $order_info['pay_status'];
    }

    echo json_encode($stream_info);
    exit;
} else if($in['m'] == 'get_order_info') {
    $order_id = $in['order_id'];
    $order_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_buy_order WHERE id=" . $order_id);
    $stream_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_buy_stream WHERE order_no='{$order_info['order_no']}' AND company_id=" . $order_info['company_id'] . " ORDER BY id DESC");
    $company_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company AS c LEFT JOIN ".DATABASEU.DATATABLE."_order_cs AS s ON c.CompanyID = s.CS_Company WHERE CompanyID=" . $order_info['company_id']);
    $order_info['data'] = json_decode($order_info['data'],true);

    $calc_begin = strtotime(date('Y-m-d',time()));
    $buy = $order_info['data']['buy_time'];
    $gift = $order_info['data']['gift_time'];
    if($company_info['CS_Number'] == 10000) {
        //续费无限用户版
        $calc_begin = strtotime($company_info['CS_EndDate']);
    }
    $company_info['result_end_date'] = date('Y-m-d',strtotime("+{$buy} years {$gift} months",$calc_begin));
    if($order_info['buy_count'] == -1) {
        $company_info['result_end_date'] = "";
    }
    echo json_encode(array(
        'company_info' => $company_info,
        'order_info' => $order_info,
        'stream_info' => $stream_info
    ));
    exit;
} else if ($in['m'] == 'sure_stream') {
    $id = (int) $in['id'];
    if(empty($id)) {
        exit("支付单信息不存在!");
    }
    $result_str = "ok";
    $company_id = $_SESSION['uinfo']['ucompany'];
    $stream_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_buy_stream WHERE id={$id} LIMIT 1");
    $upsql = "UPDATE ".DATABASEU.DATATABLE."_buy_stream SET status='T',username='".$_SESSION['uinfo']['username']."',to_time=".time()." WHERE id={$id} LIMIT 1";
    if(false !==$db->query($upsql)) {
        $modify_order_result = $db->query("UPDATE ".DATABASEU.DATATABLE."_buy_order SET pay_status=1,integral=total WHERE order_no='{$stream_info['order_no']}' AND company_id=".$stream_info['company_id']);
        if($modify_order_result === false) {
            $result_str = "确认到账发生错误,请稍后再试!";
        }
    } else {
        $result_str = "确认到账发生错误,请稍后再试!";
    }
    exit($result_str);
} else if ($in['m'] == 'del_stream') {
    $id = (int)$in['id'];
    $result_str = "ok";
    $company_id = $_SESSION['uinfo']['ucompany'];
    $status = $db->get_row("SELECT status FROM ".DATABASEU.DATATABLE."_buy_stream WHERE id={$id}");
    if($status == 'T') {
        $result_str = '已到账的支付信息禁止删除!';
    } else {
        $del_result = $db->query("DELETE FROM ".DATABASEU.DATATABLE."_buy_stream WHERE id={$id}");
        if(!$del_result) {
            $result_str = '支付信息删除失败,请重试!';
        }
    }
    exit ($result_str);
} else if($in['m'] == 'order_sure_and_open') {
    //订单确认支付并立即开通
    $in['gift_sms'] = (int) $in['gift_sms'];
    $order_id = $in['order_id'];
    $order_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_buy_order WHERE id=" . $order_id);
    $order_info['data'] = json_decode($order_info['data'],true);
    $company_id = $order_info['company_id'];

    $company_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company AS c LEFT JOIN ".DATABASEU.DATATABLE."_order_cs AS s ON c.CompanyID = s.CS_Company WHERE CompanyID=" . $order_info['company_id']);

    $stream_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_buy_stream WHERE order_no='{$order_info['order_no']}' AND company_id=" . $order_info['company_id'] . " ORDER BY id DESC");

    if(empty($stream_info)) {
        exit("当前订单没有支付信息,请先添加支付信息!");
    }

    //step1 stream确认到账,order付款状态

    $db->query("UPDATE ".DATABASEU.DATATABLE."_buy_stream SET status='T',username='".$_SESSION['uinfo']['username']."',to_time=".time()." WHERE id=" . $stream_info['id']);
    $db->query("UPDATE ".DATABASEU.DATATABLE."_buy_order SET pay_status=1,integral=integral+".$stream_info['amount']." WHERE id=" . $order_info['id']);

    //step2 开通处理
    $hasFinishOrder = false; //需要将订单置为已完成状态
    $result = true; //执行状态
    switch($order_info['type']) {
        case 'renewals':
            SaveCompanyLog($company_id,"订单" . $order_info['order_no'] . "为系统续费成功.");
        case 'product':
            //记录差异日志 (赠送的短信、及续费后的到期时间变动)
            $calc_begin = strtotime(date('Y-m-d'));
            $buy = $order_info['data']['buy_time'];
            $gift = $order_info['data']['gift_time'];
            if($company_info['CS_Number'] == 10000) {
                //续费无限用户版
                $calc_begin = strtotime($company_info['CS_EndDate']);
                if($order_info['type'] == 'product') {
                    $db->query("UPDATE ".DATABASEU.DATATABLE."_buy_order SET type='renewals' WHERE id=".$order_id." AND company_id=" . $company_id);
                }

            }
            $normal_end_date = date('Y-m-d',strtotime("+{$buy} years {$gift} months",$calc_begin));
            $now_end_date = date('Y-m-d',strtotime($in['end_date']));
            if($normal_end_date != $now_end_date || $in['gift_sms'] != $order_info['data']['gift_sms']) {
                //短信或时间有改动 记录日志
                apply_log($db,array(
                    'company_id' => $company_id,
                    'order_no' => $order_info['order_no'],
                    'title' => '项目短信数从' . $order_info['data']['gift_sms']. '到' . $in['gift_sms'] . ',时间从:' . $normal_end_date . '到' . $now_end_date,
                ));
            }
            $result = $db->query("UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_UpdateTime=".time().", CS_Number=10000, CS_EndDate='{$now_end_date}',CS_SmsNumber=CS_SmsNumber+".$in['gift_sms'].",IsCharge='T' WHERE CS_Company=" . $company_id);

            //更新开通时间
            $result = $db->query("UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_OpenDate='".date('Y-m-d')."' WHERE CS_Company=" . $company_id." and CS_OpenDate is null");

            SaveCompanyLog($company_id,"订单" . $order_info['order_no'] . "修改了到期时间从" . $company_info['CS_EndDate'] . "到" . $now_end_date.',<br/>短信数从:' . $company_info['CS_SmsNumber'] . '到' . ($company_info['CS_SmsNumber']+$in['gift_sms']));
            break;
        case 'sms':
            $in['sms'] = (int) $in['sms'];
            if($order_info['data']['buy_sms'] != $in['sms']) {
                apply_log($db,array(
                    'company_id' => $company_id,
                    'order_no' => $order_info['order_no'],
                    'title' => '购买短信从' . $order_info['data']['buy_sms'] . '修改为' . $in['sms']
                ));
            }
            $result = $db->query("UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_UpdateTime=".time().", CS_SmsNumber=CS_SmsNumber+" . $in['sms'] . " WHERE CS_Company=" . $company_id);
            SaveCompanyLog($company_id,"订单" . $order_info['order_no'] . "修改短信数从" . $company_info['CS_SmsNumber'] . '到' . ($company_info['CS_SmsNumber'] + $in['sms']));
            break;
        case 'weixin':
            //TODO::待处理
            SaveCompanyLog($company_id,"订单" . $order_info['order_no'] . "购买了独立微信端服务.");
            $result = true;
            break;
        case 'erp':
            //TODO::待处理
            SaveCompanyLog($company_id,"订单" . $order_info['order_no'] . "购买了ERP接口服务.");
            $result = true;
            break;
        default:
            break;
    }
    if(false !== $result) {
        //修改订单为已完成状态
        $db->query("UPDATE ".DATABASEU.DATATABLE."_buy_order SET status=1 WHERE id=".$order_id." AND company_id=" . $company_id);
        echo 'ok';
    } else {
        echo '确认开通发生错误,请重试!';
    }
    exit;
} else if($in['m'] == 'save_company_order') {
    $order_no = build_order_no('order');
    $stream_no = build_order_no('stream');
    $buy_type = $in['buy_type'];
    $company_id = $in['company_id'];
    $oData = array(
        'order_no' => $order_no,
        'company_id' => $company_id,
        'title' => '',
        'type' => $buy_type,
        'pay_status' => 0,
        'data' => '',
        'buy_count' => 1,
        'remark' => $in['order_remark'],
        'amount' => 0,
        'total' => 0,
        'integral' => 0,
        'status' => 0,
        'time' => time(),
    );
    if($in['buy_info'] == -1) {
        //确认开通时需要自定义数据
        $oData['data'] = json_encode(array());
        $oData['total'] = (int)$in['total'];
        $oData['buy_count'] = $in['buy_info'];
    }
    $company_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company AS c LEFT JOIN ".DATABASEU.DATATABLE."_order_cs AS s ON c.CompanyID = s.CS_Company WHERE CompanyID=" . $company_id);
    switch($buy_type) { //填充 title,data,buy_count,amount,total,integral
        case 'product':
            $product_info = get_buy_conf($db,'product');
            $product_info = $product_info[0];
            $dData = array_column($product_info['data'],null,'buy_time');
            $oData['title'] = $product_info['title'];
            $oData['amount'] = $product_info['amount'];
            $oData['buy_count'] = $in['buy_info'];
            if($in['buy_info'] != -1) {
                $oData['data'] = json_encode($dData[$in['buy_info']]);
                $oData['total'] = $dData[$in['buy_info']]['total'];
            }
            if($company_info['CS_Number'] == 10000) {
                $buy_type = 'renewals';
            }

            break;
        case 'sms':
            $sms_info = get_buy_conf($db,'sms');
            $sms_info = array_column($sms_info,null,'id');
            $sms_info = $sms_info[$in['buy_info']];
            if($in['buy_info'] == -1) {
                $oData['title'] = '购买标准短信!';
                $oData['amount'] = 100;
            } else {
                $oData['title'] = $sms_info['title'];
                $oData['data'] = json_encode($sms_info['data'][0]);
                $oData['amount'] = $sms_info['amount'];
                $oData['total'] = $sms_info['data'][0]['total'];
                $oData['buy_count'] = $sms_info['data'][0]['buy_sms'];
            }

            break;
        case 'erp':
            $erp_info = get_buy_conf($db,'erp');
            $erp_info = $erp_info[0];
            $dData = array_column($erp_info['data'],null,'buy_time');
            $oData['title'] = $erp_info['title'];

            $oData['amount'] = $erp_info['amount'];
            if($in['buy_info'] != -1) {
                $oData['data'] = json_encode($dData[$in['buy_info']]);
                $oData['total'] = $dData[$in['buy_info']]['total'];
            }

            $oData['buy_count'] = $in['buy_info'];
            break;
        case 'weixin':
            $wx_info = get_buy_conf($db,'weixin');
            $wx_info = $wx_info[0];
            $dData = array_column($wx_info['data'],null,'buy_time');
            $oData['title'] = $wx_info['title'];
            $oData['data'] = json_encode($dData[$in['buy_info']]);
            $oData['amount'] = $wx_info['amount'];
            $oData['total'] = $dData[$in['buy_info']]['total'];
            $oData['buy_count'] = $in['buy_info'];
            break;
        default:
            exit("请先选择订单类型!");
            break;
    }

    $adminUser = $_SESSION['uinfo']['username'];
    $amount = $oData['total'];

    $buy_time = strtotime($in['time']);
    $order_sql = "INSERT INTO ".DATABASEU.DATATABLE."_buy_order (order_no,company_id,title,type,pay_status,data,buy_count,remark,amount,total,integral,status,time) VALUES ('{$order_no}',{$company_id},'{$oData['title']}','{$buy_type}',0,'{$oData['data']}',{$oData['buy_count']},'{$oData['remark']}',{$oData['amount']},{$oData['total']},0,0,".$buy_time.")";

    $stream_sql = "INSERT INTO ".DATABASEU.DATATABLE."_buy_stream (stream_no,order_no,company_id,pay_away,amount,trade_no,time,status,username,remark) VALUES ('{$stream_no}','{$order_no}',{$company_id},'line',{$amount},'{$in['trade_no']}',".$buy_time.",'F','{$adminUser}','{$in['stream_remark']}')";

    if(false !== $db->query($order_sql)) {
        $db->query($stream_sql);
        exit('ok');
    } else {
        exit('订单信息生成失败,请重试!');
    }
    exit;

} else if($in['m'] == 'del_company') {
    //审核列表中　删除供应商
    $company_id = $in['company_id'];

    if(!intval($company_id)) exit('非法操作!');

    $upsql =  "update ".DATABASEU.DATATABLE."_order_company set CompanyFlag='1' where CompanyID = " . $company_id;
    if($db->query($upsql))
    {
        exit('ok');
    }else{
        exit('删除不成功!');
    }



    /*
    $company_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company WHERE CompanyID=" . $company_id);
    if(empty($company_info['CompanyDatabase'])) {
        $sdatabase = DB_DATABASE.'.';
    } else {
        $sdatabase = DB_DATABASE."_".$company_info['CompanyDatabase'].'.';
    }
    $list = array();
    $list['company'] = "DELETE FROM {__DBU__}_order_company WHERE CompanyID=" . $company_id;
    $list['user'] = "DELETE FROM {__DBU__}_order_user WHERE UserCompany=" . $company_id;
    $list['account'] = "DELETE FROM {__DBU__}_buy_account WHERE company_id=" . $company_id;
    $list['dealer'] = "DELETE FROM {__DBU__}_order_dealers WHERE ClientCompany=" . $company_id;
    $list['client'] = "DELETE FROM {__DB__}_order_client WHERE ClientCompany=" . $company_id;
    $list['content_index'] = "DELETE FROM {__DB__}_order_content_index WHERE CompanyID=" . $company_id;
    $list['content_1'] = "DELETE FROM {__DB__}_order_content_1 WHERE CompanyID=" . $company_id;

    $err = array();
    foreach($list as $sql) {
        $sql = str_replace(array("{__DBU__}","{__DB__}"),array(DATABASEU . DATATABLE,$sdatabase.DATATABLE),$sql);
        $result = $db->query($sql);
        if($result === false) {
            $err[] = $sql;
        }
    }

    if(count($err) > 0) {
        exit("删除失败!");
    } else {
        exit('ok');
    }
    */

} else if($in['m']=="do_companyorder_status")
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
} else if($in['m']=="do_companyinvoice_edit")
{
	if(!intval($in['ID'])) exit('非法操作!');
	
	$upsql =  "update ".DATABASEU.DATATABLE."_buy_invoice set status='T',invoice_no='".$in['account']."',to_time='".$in['to_time']."' where id = ".$in['ID']." ";
	if($db->query($upsql))
	{
		exit('ok');
	}else{
		exit('订单支付开通失败!');
	}
	
}else if($in['m'] == 'do_company_upload') {
    if(!intval($in['ID'])) exit('非法操作!');
    if(!intval($in['sale'])) exit('非法操作!');

    $cdata = $db->get_row('SELECT * FROM '.DATABASEU.DATATABLE."_order_company_data WHERE CompanyID = {$in['ID']} LIMIT 0,1");
    
    if(!empty($cdata))
    {
        $data_sql = "UPDATE ".DATABASEU.DATATABLE."_order_company_data SET BusinessCard='{$in['data_BusinessCard']}',BusinessCardImg='{$in['data_BusinessCardImg']}',IDCard='{$in['data_IDCard']}',IDCardImg='{$in['data_IDCardImg']}',BusinessName='{$in['data_BusinessName']}' WHERE CompanyID = {$in['ID']}";
    }
    else 
    {
        $data_sql = "INSERT INTO ".DATABASEU.DATATABLE."_order_company_data (CompanyID,BusinessCard,BusinessCardImg,IDCard,IDCardImg,BusinessName) VALUES (".$in['ID'].",'".$in['data_BusinessCard']."','".$in['data_BusinessCardImg']."','".$in['data_IDCard']."','".$in['data_IDCardImg']."','".$in['data_BusinessName']."')";
    }
    
    $cs_sql = "UPDATE ".DATABASEU.DATATABLE."_order_cs SET CS_SaleUID = ".$in['sale']." WHERE CS_Company = ".$in['ID'];
    
    $result = $db->query($data_sql);
    $result_cs = $db->query($cs_sql);
    
    if($result !== false && $result_cs !== false) {
		$infodatamsg = serialize($in);
		$sqlex = "insert into ".DATABASEU.DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(1, '".$_SESSION['uinfo']['username']."', 'do_company.php?m=do_company_upload&ID=".$in['ID']."','修改客户资料(".$in['ID'].")','".$infodatamsg."',".time().")";
		$db->query($sqlex);
		
        exit('ok');
    } else {
        exit('资料保存失败,请重试!');
    }
}

 else {
    exit('非法操作!');
}


function SaveCompanyLog($companyid,$content)
{
    global $db;

    $upsql = "insert into ".DATABASEU.DATATABLE."_order_company_log(CompanyID,CreateDate,CreateUser,Content) values('".intval($companyid)."',".time().",'".$_SESSION['uinfo']['username']."','".$content."')";
    return $db->query($upsql);
}

?>