<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/returndata.php");

$input	=	new Input;
$in		=	$input->parse_incoming();

if($in['m']=="returnadd")
{	
	$urlmsg = "";
	$in['id'] = '';
	$in['pid'] = '';

	$rtype = returndata::showreturntype();
	if($rtype=="order")
	{
		if(!empty($in['sn']))
		{
			$order = returndata::showorder($in['sn']);
			if(!empty($order['return']))
			{
				$in['id'] = $order['orderinfo']['OrderID'];
			}
		}
		include template("returnadd");
	}else{	
		$n = 1;
		include template("returnadd_product");
	}

}elseif($in['m']=="select_return_product"){
		
	$in['pid'] = '';
	if(empty($in['selectid'])) $in['selectid'] = '';
	if(!empty($in['kw']))
	{
		$pinfo = returndata::showproduct($in);
		$in['pid'] = count($pinfo);
	}	
	include template("select_return_product");


}elseif($in['m']=="add_to_select_product"){

	//$omsg = "application/json;charset=UTF-8";
	$omsg = "";
	$dmsg = "";
	$outidmsg = '';
	if(!empty($in['cartkid']))
	{
		if(!empty($in['selectid']))
		{
			$in_selectarr = explode(",", $in['selectid']);
		}else{
			$in_selectarr = null;
			$in['selectid'] = '';
		}

		for($i=0;$i<count($in['cartkid']);$i++)
		{
			if(!@in_array($in['cartkid'][$i], $in_selectarr))
			{
				$outidmsg .= $in['cartkid'][$i].",";
				$dataarr = unserialize(urldecode($in['cartdata_'.$in['cartkid'][$i]]));
				$dmsg .= '<tr id=\"line_'.$in['cartkid'][$i].'\"><td height=24>'.$dataarr['ContentID'].'<input type=\"hidden\" value=\"'.$in['cartkid'][$i].'\" name=\"cartid[]\"  /><input type=\"hidden\" value=\"'.$in['cartdata_'.$in['cartkid'][$i]].'\" name=\"cartdata[]\" id=\"cartdata_'.$in['cartkid'][$i].'\"  /></td><td><a href=content.php?id='.$dataarr['ContentID'].' target=_blank>'.$dataarr['ContentName'].'</a></td><td>'.$dataarr['ContentColor'].'&nbsp;</td><td>'.$dataarr['ContentSpecification'].'&nbsp;</td><td align=right>'.$dataarr['rnumber'].'</td><td align=right><input name=\"cart_num[]\" id=\"cart_num_'.$in['cartkid'][$i].'\" type=\"text\" size=\"6\" maxlength=\"6\"  onKeypress=\"if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;\" onfocus=\"this.select();\" style=\"text-align:right; width:50px;\" value=\"0\"  /></td><td align=right>¥ '.$dataarr['Price_End'].'</td><td align=right><a href=javascript:void(0) onclick=\"del_line_select_product(\'line_'.$in['cartkid'][$i].'\')\" >移除</a></td></tr>';
			}
		}

		$outidmsg = $outidmsg.$in['selectid'];
		$omsg .= '{"backtype":"ok", "htmldata":"'.$dmsg.'", "selectiddata":"'.$outidmsg.'"}';
	}else{
		$omsg .= '{"backtype":"empty!"}';
	}
	echo $omsg;
	exit();


}elseif($in['m']=="sub_returnadd"){

	if(empty($in['cartid'])) exit('提示：您还没有输入任何退货商品');
	$cartidmsg = "";

	for($i=0;$i<count($in['cartid']);$i++)
	{
		if(!empty($in['cartid'][$i]))
		{	
			$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
			if(!empty($in['cart_num'][$i]))
			{
				$returnarr[$in['cartid'][$i]] = $in['cart_num'][$i];
				$cartidmsg .= ",".$in['cartid'][$i];
			}
		}
	}
	if(empty($cartidmsg))
	{
		exit('提示：您还没有输入任何退货商品!');
	}elseif(!empty($in['orderid'])){
		$order = returndata::getcartproduct($in['orderid']);
		if(!empty($order['return']))
		{
			foreach($returnarr as $rkey=>$rvar)
			{
				if($rvar > $order['ordercart'][$rkey]['rnumber'])
				{
					echo $msg = '['.$order['ordercart'][$rkey]['ContentName'].'] 退货数量不能大于订单可退数!';
					exit();
				}
			}
			$orderin = returndata::insertintoreturncart($in,$order,$returnarr);
			if($orderin=="ok")
			{
				exit('ok');
			}else{
				exit('提交不成功!');
			}
		}
	}
	exit('提交不成功!');

}elseif($in['m']=="sub_returnadd_product"){

	if(empty($in['cartid'])) exit('提示：您还没有输入任何退货商品');
	$cartidmsg = "";

	for($i=0;$i<count($in['cartid']);$i++)
	{
		if(!empty($in['cartid'][$i]))
		{	
			$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));
			if(!empty($in['cart_num'][$i]))
			{
				$returnarr[$in['cartid'][$i]] = unserialize(urldecode($in['cartdata'][$i]));
				$returnarr[$in['cartid'][$i]]['number'] = $in['cart_num'][$i];
				$cartidmsg .= ",".$in['cartid'][$i];
			}
		}
	}
	if(empty($cartidmsg))
	{
		exit('提示：您还没有输入任何退货商品!');
	}else{

		if(!empty($returnarr))
		{
			foreach($returnarr as $rkey=>$rvar)
			{
				if($rvar['number'] > $rvar['rnumber'])
				{
					echo $msg = '['.$rvar['ContentName'].'] 退货数量不能大于商品可退数!';
					exit();
				}
			}
			$orderin = returndata::insertintoreturncart_product($in,$returnarr);
			if($orderin=="ok")
			{
				exit('ok');
			}else{
				exit('提交不成功!');
			}
		}
	}
	exit('提交不成功!');


}elseif($in['m']=="cancel"){
	
	if(strlen($in['Content']) > 200 ) exit('说明内容过长，请用简短的语言描述!');
	$status = returndata::cancelorder($in['ID'],$in['Content']);
	if($status)
	{
		exit('ok');
	}else{
		exit('操作失败，请与供货商联系!');
	}

}elseif($in['m']=="sub_guestbook"){
	
	if(strlen($in['Content']) > 200 ) exit('留言内容过长，请用简短的语言描述!');
	$status = returndata::save_guestbook($in['ID'],$in['Content']);
	if($status)
	{
		exit('ok');
	}else{
		exit('操作失败，请与供货商联系!');
	}

}elseif($in['m']=="showreturn"){
	
	$order   = returndata::showreturn($in['id']);
	$ordersubmit = returndata::listsubmit($in['id']);

	$isbak    =   returndata::get_row_cartbak($in['id']);

	include template("showreturn");

}elseif($in['m']=="showoldreturn"){
		
		$in['id'] = intval($in['id']);
		$n=1;
		$olddata    =   returndata::show_cartproduct($in['id']);

		include template("showoldreturn");

}else{

	$orderlist   = returndata::listreturn($in['status'],12,'return.php');

	include template("return");
}
//END
?>