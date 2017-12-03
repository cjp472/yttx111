<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/cart.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();
$in			=  $input->_htmlentities($in);

$isnotshowloadcart = true;

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');
$page2 =  (isset( $_GET['page'])!="") ? $_GET['page'] :  $page2 = 1;
$page2 = intval($page2);

//close_order(); //关闭订货

if($in['m']=="addtocart" && $_SERVER['REQUEST_METHOD']=="POST")
{
	$shcart  = new ShoppingCart();

	if(empty($in['pid'])) exit('提示：请选择你要预订的商品!');
	$in['pid']	  = intval($in['pid']);
	$in['pnum']   = intval($in['pnum']);
	if(empty($in['pnum'])) $in['pnum'] = 1;

	if($in['pcolor'] == "null")  $in['pcolor'] = "";
	if($in['pspec']  == "null")  $in['pspec'] = "";

	$db	= dbconnect::dataconnect()->getdb();
	$productcontent = $db->get_row("SELECT ContentIndexID,Package FROM ".DATATABLE."_order_content_1 where CompanyID=".$_SESSION['cc']['ccompany']." and ContentIndexID=".intval($in['pid'])." limit 0,1");
	if(empty($productcontent['Package']) || fmod($in['pnum'],$productcontent['Package'])==0 )
	{
		$tocn = $in['pnum'];
	}else{
        exit(json_encode(array(
            'backtype' => 'error',
            'cartnum' => '订购数量必需为'.$productcontent['Package'].'的倍数',
        )));
		//$tocn = $in['pnum']+($productcontent['Package']-fmod($in['pnum'],$productcontent['Package']));
	}

	$shcart->add_items($in['pid'], $in['pcolor'], $in['pspec'],$tocn);
	$cmsg = $shcart->show_cart();
	$numc = count($cmsg);
	if($numc > 0) $omsg = '{"backtype":"ok", "cartnum":"'.$numc.'"}'; else $omsg = '{"backtype":"error", "cartnum":"提成不成功!"}';
	echo $omsg;
	exit();

}elseif($in['m']=="updatecart" && $_SERVER['REQUEST_METHOD']=="POST"){

	if(empty($in['kid'])) exit('提示：您还没有预订任何商品');
	$shcart  = new ShoppingCart();
	for($i=0;$i<count($in['kid']);$i++)
	{
		if(!empty($in['kid'][$i]))
		{	
			$delid = 'kiddel_'.$in['kid'][$i];
			$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));

			if(empty($in['cart_num'][$i]) || !empty($in[$delid]))
			{
				$shcart->remove_item($in['kid'][$i]);
			}elseif($in['cart_num'][$i] > 10000000){

			}else{
				$shcart->update_items($in['kid'][$i], $in['cart_num'][$i]);
			}
		}
	}
	$cmsg = $shcart->show_cart();
                
	//重新设置session值（价格）
	commondata::set_session_price();
	//购物车明细
	$shcart  = new ShoppingCart();

	$page  = new ShowPage;
	$page->PageSize			= $pagesize = 100;	
	$page->Total		    = count($_SESSION['cartitems']);
	$splitp['total']		= count($_SESSION['cartitems']);
	if(count($_SESSION['cartitems']) <= $pagesize) $splitp['page']  = 1;  else $splitp['page']	= $page->PageNum();
	$splitp['pagestart']    = ($splitp['page']-1)*$page->PageSize+1;
	$splitp['pageend']		= $splitp['page']*$page->PageSize;
	$splitp['showpage']		= $page->ShowLink('cart.php');

	if(!empty($_SESSION['cartitems']))
	{
		$i = 0;
		$idmsg = "0";
		$setarr = commondata::getproductset('product');
		if(!empty($setarr['product_number']))
		{
			$pn  = $setarr['product_number'];
		}else{
			$pn  = 'off';
		}
		if(!empty($setarr['product_negative']))
		{
			$png  = $setarr['product_negative'];
		}else{
			$png  = 'off';
		}
		if(!empty($setarr['product_number_show']))
		{
			$pns  = $setarr['product_number_show'];
		}else{
			$pns  = 'off';
		}
		if($pn=="off" && $pns == "off") $iss = "off"; else $iss = "on";
		if($pn=="off" || $png == "on") $isred = "off"; else $isred = "on";
		$pp = 0;
		foreach($_SESSION['cartitems'] as $key=>$var)
		{		
			$pos_color = strpos($key, "_p_");
			$pos_spec  = strpos($key, "_s_");

			$cartproduct[$i]['color']	= '';
			$cartproduct[$i]['spec']	= '';

			if(empty($pos_color) && empty($pos_spec))
			{
				$cartproduct[$i]['pid']		= $key;
			}else if(!empty($pos_color)){
				$cartproduct[$i]['pid']		= substr($key, 0, $pos_color);
				if(empty($pos_spec))
				{
					$cartproduct[$i]['color']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_color+3)));
				}else{
					$cartproduct[$i]['color']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_color+3,$pos_spec-$pos_color-3)));
					$cartproduct[$i]['spec']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
				}
			}else if(!empty($pos_spec)){
				$cartproduct[$i]['pid']		= substr($key, 0, $pos_spec);
				$cartproduct[$i]['spec']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
			}
			$cartproduct[$i]['kid']		= $key;
			$cartproduct[$i]['number']	= abs(intval($var));
			
			$idmsg .= ",".$cartproduct[$i]['pid'];
			$i++;
		}

		$cartarray = $shcart->listcartgoods($idmsg,$iss);
		for($j=0;$j<count($cartarray['all']);$j++)
		{
			$carttemp[$cartarray['all'][$j]['ID']]  =  $cartarray['all'][$j];
			$carttemp[$cartarray['all'][$j]['ID']]['package']  = $cartarray['package'][$cartarray['all'][$j]['ID']];
		}

		$tykey = str_replace($fp,$rp,base64_encode("统一"));
		$cospnumarr = null;
		if(!empty($cartarray['cosp']) && !empty($cartarray['ison']))
		{
			foreach($cartarray['cosp'] as $cospvar)
			{	
				$cospkey = $cospvar['ContentID'];
				if(!empty($cospvar['ContentColor']) && $cospvar['ContentColor']!=$tykey)
				{
					$cospkey .= "_p_".$cospvar['ContentColor'];
				}
				if(!empty($cospvar['ContentSpec']) && $cospvar['ContentSpec']!=$tykey)
				{
					$cospkey .= "_s_".$cospvar['ContentSpec'];
				}
				$cospnumarr[$cospkey] = $cospvar['OrderNumber'];
			}
		}

		$productnum = 0;
		$producttotal = 0;
		$productnump = 0;
		$producttotalp = 0;
		$isempty = false;

		for($k=0;$k<count($cartproduct);$k++)
		{		
			$carttempproduct[$k]			= $carttemp[$cartproduct[$k]['pid']];
			$carttempproduct[$k]['No']		= $k+1;
			$carttempproduct[$k]['kid']		= $cartproduct[$k]['kid'];
			$carttempproduct[$k]['id']		= $cartproduct[$k]['pid'];
			$carttempproduct[$k]['color']	= $cartproduct[$k]['color'];
			$carttempproduct[$k]['spec']	= $cartproduct[$k]['spec'];
			$carttempproduct[$k]['number']  = $cartproduct[$k]['number'];
			$carttempproduct[$k]['price']     = $carttemp[$cartproduct[$k]['pid']]['Price'];
			$carttempproduct[$k]['pencent']   = $carttemp[$cartproduct[$k]['pid']]['Pencent'];
			$carttempproduct[$k]['price_end'] = $carttempproduct[$k]['price'] * $carttempproduct[$k]['pencent'] / 10;
			$carttempproduct[$k]['notetotal'] = $cartproduct[$k]['number'] * $carttempproduct[$k]['price'] * ($carttempproduct[$k]['pencent']/10);

			$producttotal   = $producttotal + $carttempproduct[$k]['notetotal'];
			$productnum   = $productnum + $carttempproduct[$k]['number'];

			if($carttempproduct[$k]['No'] >= $splitp['pagestart'] && $carttempproduct[$k]['No'] <=$splitp['pageend'])
			{
				$producttotal2   = $producttotal2 + $carttempproduct[$k]['notetotal'];
				$productnum2   = $productnum2 + $carttempproduct[$k]['number'];
			}

			if(empty($carttemp[$cartproduct[$k]['pid']]['ID']))
			{
				$carttempproduct[$k]['Name'] = '此商品已下架!';
				$shcart->remove_item($cartproduct[$k]['kid']);
				$shcart->show_cart();
			}
			if(empty($carttempproduct[$k]['OrderNumber'])) $carttempproduct[$k]['OrderNumber'] = 0;

			if(empty($cartarray['ison']))
			{
				$carttempproduct[$k]['onumber'] = 9999999999;
			}else{
				if(strlen($carttempproduct[$k]['color']) == 0 && strlen($carttempproduct[$k]['spec']) == 0)
				{
					$carttempproduct[$k]['onumber'] = $carttempproduct[$k]['OrderNumber'];
				}else{
					$carttempproduct[$k]['onumber'] = $cospnumarr[$carttempproduct[$k]['kid']];
				}
			}
			if(empty($carttempproduct[$k]['onumber'])) $carttempproduct[$k]['onumber'] = 0;
			
			if(($carttempproduct[$k]['number'] > $carttempproduct[$k]['onumber']) && $pn=="on" && $png=="off")
			{
				$carttempproduct[$k]['library'] = "empty";
				$isempty = true;
			}else{
				$carttempproduct[$k]['library'] = "ok";
			}
		}
		$producttotal = sprintf("%01.2f", round($producttotal,2));
        $stair_count = get_stair($producttotal);
        $stair_amount = get_stair($producttotal,'amount');
        $omsg = '{"backtype":"ok", "producttotal":"'.$producttotal.'", "productnum":"'.$productnum.'","stair_count":"'.$stair_count.'","stair_amount":"'.$stair_amount.'","producttotal2":"'.$producttotal2.'","productnum2":"'.$productnum2.'","isempty":"'.$isempty.'","pns":"'.$pns.'","isred":"'.$isred.'"}';
        echo $omsg;
	}else{
		$cartproduct = null;
		$omsg = '{"backtype":"ok"}';
		echo $omsg;
	}
	//end tubo 
	exit();

}elseif($in['m']=="updatecartsubmit"){

	
	if(empty($in['kid'])) exit('提示：您还没有预订任何商品');
	
	$cResult = check_ordertime();
	if(!$cResult['status']) exit($cResult['rmsg']);

	$shcart  = new ShoppingCart();
	$order_amount=$shcart->check_order_amount();
	$OrderAmount=(float)$in['OrderAmount'];
	//var_dump($order_amount,$OrderAmount);exit;
	if(is_numeric($order_amount)){
		$order_amount=(float) $order_amount;
		if( $OrderAmount < $order_amount){
			exit("提示：没有达到订单的最小金额<span style='color:#FF8E32;'> ¥ ".$order_amount."</span>，请继续采购！");
		}
	}else{
		exit("提示：无法获取到最小金额！");
	}
	
	for($i=0;$i<count($in['kid']);$i++)
	{
		if(!empty($in['kid'][$i]))
		{	
			$delid = 'kiddel_'.$in['kid'][$i];
			$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));

			if(empty($in['cart_num'][$i]) || !empty($in[$delid]))
			{
				$shcart->remove_item($in['kid'][$i]);
			}else{
				$shcart->update_items($in['kid'][$i], $in['cart_num'][$i]);
			}
		}
	}
	$cmsg = $shcart->show_cart();
	exit('ok');

}elseif($in['m']=="removecart"){
	
	if(empty($in['kid']))
	{
		exit('请指定要移除商品!');
	}else{
		$shcart  = new ShoppingCart();
		$shcart->remove_item($in['kid']);
		$cmsg = $shcart->show_cart();
		echo $numc = count($cmsg);
		exit();
	}

}elseif($in['m'] == "update_load_cart"){

	if(empty($in['kid'])) exit('您还没有订购任何商品！');
	$shcart  = new ShoppingCart();
	for($i=0;$i<count($in['kid']);$i++)
	{
		if(!empty($in['kid'][$i]))
		{	
			$in['cart_num'][$i] = abs(intval($in['cart_num'][$i]));

			if(empty($in['cart_num'][$i]))
			{
				$shcart->remove_item($in['kid'][$i]);
			}else{
				$shcart->update_items($in['kid'][$i], $in['cart_num'][$i]);
			}
		}
	}
	$cmsg = $shcart->show_cart();
	echo $numc = count($cmsg);
	exit();

}elseif($in['m']=="removecart"){
	
	if(empty($in['kid']))
	{
		exit('请指定要移除商品!');
	}else{
		$shcart  = new ShoppingCart();
		$shcart->remove_item($in['kid']);
		$cmsg = $shcart->show_cart();
		echo $numc = count($cmsg);
		exit();
	}

}elseif($in['m']=="clearcart"){
	$shcart  = new ShoppingCart();

	$shcart->clear_items();
	header("location: cart.php");

}elseif($in['m']=="change_input_number"){

	$snarr = null;
	$totalnumber = 0;
	$stotal = 0;
	$ctotal = 0;
	if(!empty($in['cart_number_id']))
	{
		for($i=0;$i<count($in['cart_number_id']);$i++)
		{
			$snarr[$in['cart_number_id'][$i]] = abs(intval($in['cart_number'][$i]));
			$keyarr = explode("_",$in['cart_number_id'][$i]);
			$sarr[] = $keyarr[1];
			$carr[] = $keyarr[2];
			$totalnumber = $totalnumber + abs(intval($in['cart_number'][$i]));
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

}elseif($in['m']=="add_input_number_save"){

	$shcart  = new ShoppingCart();
	$snarr   = null;
	$totalnumber = 0;
	if(empty($in['inputpid'])) exit('参数错误!'); else $pid = intval($in['inputpid']);
	if(!empty($in['cart_number_id']))
	{
		for($i=0;$i<count($in['cart_number_id']);$i++)
		{
			if(!empty($in['cart_number'][$i])) $snarr[$in['cart_number_id'][$i]] = abs(intval($in['cart_number'][$i]));
		}
		if(!empty($snarr))
		{
			$shcart->add_items_arr($pid,$snarr);
			$cmsg = $shcart->show_cart();
			exit('ok');
		}
	}
	exit('您还没有订购任何商品！');

}elseif($in['m']=="get_cart_product_number"){

	echo $numc = count($_SESSION['cartitems']);
	exit();

}else{
	//重新设置session值（价格）
	commondata::set_session_price();
	//购物车明细
	$shcart  = new ShoppingCart();
	//$cmsg = $shcart->show_cart();

	$page  = new ShowPage;
	$page->PageSize			= $pagesize = 100;	
	$page->Total		    = count($_SESSION['cartitems']);
	$splitp['total']		= count($_SESSION['cartitems']);
	if(count($_SESSION['cartitems']) <= $pagesize) $splitp['page']  = 1;  else $splitp['page']	= $page->PageNum();
	$splitp['pagestart']    = ($splitp['page']-1)*$page->PageSize+1;
	$splitp['pageend']		= $splitp['page']*$page->PageSize;
	$splitp['showpage']		= $page->ShowLink('cart.php');
	
	if(!empty($_SESSION['cartitems']))
	{
		$i = 0;
		$idmsg = "0";
		$setarr = commondata::getproductset('product');
		if(!empty($setarr['product_number']))
		{
			$pn  = $setarr['product_number'];
		}else{
			$pn  = 'off';
		}
		if(!empty($setarr['product_negative']))
		{
			$png  = $setarr['product_negative'];
		}else{
			$png  = 'off';
		}
		if(!empty($setarr['product_number_show']))
		{
			$pns  = $setarr['product_number_show'];
		}else{
			$pns  = 'off';
		}
		if($pn=="off" && $pns == "off") $iss = "off"; else $iss = "on";

		$pp = 0;
		foreach($_SESSION['cartitems'] as $key=>$var)
		{		
			$pos_color = strpos($key, "_p_");
			$pos_spec  = strpos($key, "_s_");

			$cartproduct[$i]['color']	= '';
			$cartproduct[$i]['spec']	= '';

			if(empty($pos_color) && empty($pos_spec))
			{
				$cartproduct[$i]['pid']		= $key;
			}else if(!empty($pos_color)){
				$cartproduct[$i]['pid']		= substr($key, 0, $pos_color);
				if(empty($pos_spec))
				{
					$cartproduct[$i]['color']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_color+3)));
				}else{
					$cartproduct[$i]['color']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_color+3,$pos_spec-$pos_color-3)));
					$cartproduct[$i]['spec']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
				}
			}else if(!empty($pos_spec)){
				$cartproduct[$i]['pid']		= substr($key, 0, $pos_spec);
				$cartproduct[$i]['spec']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
			}
			$cartproduct[$i]['kid']		= $key;
			$cartproduct[$i]['number']	= abs(intval($var));
			
			$idmsg .= ",".$cartproduct[$i]['pid'];
			$i++;
		}

		$cartarray = $shcart->listcartgoods($idmsg,$iss);
		for($j=0;$j<count($cartarray['all']);$j++)
		{
			$carttemp[$cartarray['all'][$j]['ID']]  =  $cartarray['all'][$j];
			$carttemp[$cartarray['all'][$j]['ID']]['package']  = $cartarray['package'][$cartarray['all'][$j]['ID']];
		}

		$tykey = str_replace($fp,$rp,base64_encode("统一"));
		$cospnumarr = null;
		if(!empty($cartarray['cosp']) && !empty($cartarray['ison']))
		{
			foreach($cartarray['cosp'] as $cospvar)
			{	
				$cospkey = $cospvar['ContentID'];
				if(!empty($cospvar['ContentColor']) && $cospvar['ContentColor']!=$tykey)
				{
					$cospkey .= "_p_".$cospvar['ContentColor'];
				}
				if(!empty($cospvar['ContentSpec']) && $cospvar['ContentSpec']!=$tykey)
				{
					$cospkey .= "_s_".$cospvar['ContentSpec'];
				}
				$cospnumarr[$cospkey] = $cospvar['OrderNumber'];
			}
		}

		$productnum = 0;
		$producttotal = 0;
		$productnump = 0;
		$producttotalp = 0;
		$isempty = false;

		for($k=0;$k<count($cartproduct);$k++)
		{		
			$carttempproduct[$k]			= $carttemp[$cartproduct[$k]['pid']];
			$carttempproduct[$k]['No']		= $k+1;
			$carttempproduct[$k]['kid']		= $cartproduct[$k]['kid'];
			$carttempproduct[$k]['id']		= $cartproduct[$k]['pid'];
			$carttempproduct[$k]['color']	= $cartproduct[$k]['color'];
			$carttempproduct[$k]['spec']	= $cartproduct[$k]['spec'];
			$carttempproduct[$k]['number']  = $cartproduct[$k]['number'];
			$carttempproduct[$k]['price']     = $carttemp[$cartproduct[$k]['pid']]['Price'];
			$carttempproduct[$k]['pencent']   = $carttemp[$cartproduct[$k]['pid']]['Pencent'];
			$carttempproduct[$k]['price_end'] = $carttempproduct[$k]['price'] * $carttempproduct[$k]['pencent'] / 10;
			$carttempproduct[$k]['notetotal'] = $cartproduct[$k]['number'] * $carttempproduct[$k]['price'] * ($carttempproduct[$k]['pencent']/10);
			//$carttempproduct[$k]['notetotal'] = sprintf("%01.2f", $carttempproduct[$k]['notetotal']);
			$producttotal   = $producttotal + $carttempproduct[$k]['notetotal'];
			$productnum   = $productnum + $carttempproduct[$k]['number'];

			if($carttempproduct[$k]['No'] >= $splitp['pagestart'] && $carttempproduct[$k]['No'] <=$splitp['pageend'])
			{
				$producttotal2   = $producttotal2 + $carttempproduct[$k]['notetotal'];
				$productnum2   = $productnum2 + $carttempproduct[$k]['number'];
			}

			if(empty($carttemp[$cartproduct[$k]['pid']]['ID']))
			{
				$carttempproduct[$k]['Name'] = '此商品已下架!';
				$shcart->remove_item($cartproduct[$k]['kid']);
				$shcart->show_cart();
			}
			if(empty($carttempproduct[$k]['OrderNumber'])) $carttempproduct[$k]['OrderNumber'] = 0;

			if(empty($cartarray['ison']))
			{
				$carttempproduct[$k]['onumber'] = 9999999999;
			}else{
				if(strlen($carttempproduct[$k]['color']) == 0 && strlen($carttempproduct[$k]['spec']) == 0)
				{
					$carttempproduct[$k]['onumber'] = $carttempproduct[$k]['OrderNumber'];
				}else{
					$carttempproduct[$k]['onumber'] = $cospnumarr[$carttempproduct[$k]['kid']];
				}
			}
			if(empty($carttempproduct[$k]['onumber'])) $carttempproduct[$k]['onumber'] = 0;
			
			if(($carttempproduct[$k]['number'] > $carttempproduct[$k]['onumber']) && $pn=="on" && $png=="off")
			{
				$carttempproduct[$k]['library'] = "empty";
				$isempty = true;
			}else{
				$carttempproduct[$k]['library'] = "ok";
			}
		}
		$producttotal = sprintf("%01.2f", round($producttotal,2));
        $stair_count = get_stair($producttotal);
        $stair_amount = get_stair($producttotal,'amount');
	}else{
		$cartproduct = null;
	}

	if(empty($_COOKIE["backurl"])) $backu = 'list.php'; else $backu = $_COOKIE["backurl"];

	$cartTotal = $page->Total;
	if(!empty($in['mt'])){
		include template("load_cart");
	}else{
		include template("cart");	
	}
	
}


function close_order(){
	$zero1 = strtotime (date("Y-m-d h:i:s")); //当前时间
	$zero2 = strtotime ("2015-02-13 00:00:01");  //开始时间
	$zero3 = strtotime ("2015-02-25 23:59:59");  //结束时间
	if($_SESSION['cc']['ccompany'] == "191" && $zero1 > $zero2 && $zero1 < $zero3){
		echo $omsg = '{"backtype":"error", "cartnum":"各位客户朋友：2月13日--2月25日因春节期间放假，本系统暂停订货，2月25日恢复正常！"}';
		exit;
	}

}
?>