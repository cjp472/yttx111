<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/orderdata.php");
include_once (SITE_ROOT_PATH."/module/cart.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();

	$pay_arr = array(
		'0'			=>  '未付款',
		'1'			=>  '付款中',
		'2'			=>  '已付款',
		'3'			=>  '预付款',
 	 );
	
	$send_arr = array(
		'1'			=>  '备货中',
		'2'			=>  '已发货',
		'3'			=>  '未发完',
		'4'			=>  '已收货'
 	 );

	$order_arr = array(
		'0'			=>  '待审核',
		'7'         =>  '已完成',
		'8'         =>  '客户取消',
		'9'         =>  '管理员取消'
	 );


if($in['m']=="product")
{
	setcookie("backurl", $_SERVER['REQUEST_URI']);
	$urlmsg = "";
	if(empty($in['t']))
	{
		$in['t']  = "textlist";
	}else{
		$urlmsg  .= "&t=".$in['t'];
	}
	if(empty($in['o']))
	{
		$in['o']  = "";
	}else{
		$urlmsg  .= "&o=".$in['o'];
	}
	$goodslist = orderdata::orderproduct($in['o'],$in['t'],$in['ps']);
    $setarr   = commondata::getproductset();
    
	include template("orderproduct");

}elseif($in['m']=="showorder"){
	
	$urlmsg = "";
	if(!empty($in['sn']))
	{
		$oinfo    = orderdata::getorderinfo($in['sn']);
		$in['id']   = $oinfo['OrderID'];
	}
	if(empty($in['id']))
	{
		exit('参数错误！');
	}else{
		$in['id'] = intval($in['id']);
		$urlmsg  .= "&id=".$in['id'];
	}
	$order          = orderdata::showorder($in['id']);
	$ordergifts     = orderdata::showordergifts($in['id']);
	$ordersubmit    = orderdata::listsubmit($in['id']);

	$sendtypearr  = orderdata::statusarr("sendtype");
	$paytypearr   = orderdata::statusarr("paytype");

	$isbak        =   orderdata::get_row_cartbak($in['id']);

	include template("showorder");

}elseif($in['m']=="showoldorder"){
		
		$in['id'] = intval($in['id']);
		$n=1;
		$olddata    =   orderdata::show_cartproduct($in['id']);
		include template("showoldorder");

}elseif($in['m']=="cancel"){
	
	if(strlen($in['Content']) > 200 ) exit('说明内容过长，请用简短的语言描述!');
	$status = orderdata::cancelorder($in['ID'],$in['Content']);
	if($status)
	{
		exit('ok');
	}else{
		exit('操作失败，请与供货商联系!');
	}

}elseif($in['m']=="confirmincept"){
		
	if(strlen($in['Content']) > 200 ) exit('说明/留言内容过长，请用简短的语言描述!');
	$status = orderdata::confirmclientincept($in['ID'],$in['Content']);
	if($status)
	{
		exit('ok');
	}else{
		exit('操作失败，请与供货商联系!');
	}
}elseif($in['m']=="sub_guestbook"){
	
	if(strlen($in['Content']) > 200 ) exit('留言内容过长，请用简短的语言描述!');
	$status = orderdata::save_guestbook($in['ID'],$in['Content']);
	if($status)
	{
		exit('ok');
	}else{
		exit('操作失败，请与供货商联系!');
	}

}elseif($in['m']=='collect'){
    $db = dbconnect::dataconnect()->getdb();
    $collect = $in['join']==1 ? 1 : 0;
    $coll_sql = "UPDATE ".DATATABLE."_order_orderinfo SET OrderCollect='".$collect."' WHERE OrderID=".$in['id'];
    $rst = $db->query($coll_sql);
    echo json_encode(array(
        'status'=>1,
        'msg'=>'ok'
    ));
    exit();
}elseif($in['m']=="copyorder"){
	//复制订单

	$in['id'] = intval($in['oid']);
	if(empty($in['id'])) exit('参数错误!');

	$shcart   = new ShoppingCart();
	$shcart->clear_items();
	$order    = orderdata::showorder($in['id']);

	if(!empty($order['ordercart']))
	{
		foreach($order['ordercart'] as $ov)
		{
			if($ov['CommendID'] == "9") continue;
			$shcart->add_items($ov['ContentID'], $ov['ContentColor'], $ov['ContentSpecification'],$ov['ContentNumber']);
			$cmsg = $shcart->show_cart();
		}
	}
	header("Location: ./cart.php");

}else{
	$orderlist    = orderdata::listorder($in,12,'myorder.php');
	$sendtypearr  = orderdata::statusarr("sendtype");
	$paytypearr   = orderdata::statusarr("paytype", false);
	$producttype  = get_set_arr('product');

    //exit;
	include template("myorder");	
//END
}
?>