<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/cart.class.php");
include_once (SITE_ROOT_PATH."/class/sms.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();

$isnotshowloadcart = true;

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

$shcart  = new ShoppingCart();

if($in['m']=="guestadd" && $_SERVER['REQUEST_METHOD']=="POST")
{
	if(empty($_SESSION['cartitems']))
	{
		$omsg = '{"backtype":"error", "sn":"提示：您的购物物车中没有任何商品，请先添加您要购买的商品!"}';
		exit;
	}
	
	if(empty($in['AddressAddress']) || empty($in['AddressContact']) || empty($in['AddressPhone']))
	{
		$omsg = '{"backtype":"error", "cartnum":"为保证物品能及时快捷的到达，请填写完整您的收货信息!"}';
	}else{
		//是否允许提交订单
	    $cResult = check_ordertime();
	    
	    $cinfo = client::clientinfo();
	    $time = time();
	    if(!empty($cinfo['GsmpValidity']) && $cinfo['GsmpValidity'] != '1970-01-01'){
	    	$gsmpValidity    = strtotime($cinfo['GsmpValidity'].' 23:59:59');
	    	if($time > $gsmpValidity){//资质效期失效
	    		echo $omsg = '{"backtype":"error", "sn":"您的GSP证书已过期，不能进行采购"}';
	    		exit;
	    	}
	    }
	    
	    if(!empty($cinfo['LicenceValidity']) && $cinfo['LicenceValidity'] != '1970-01-01'){
	    	$licenceValidity = strtotime($cinfo['LicenceValidity'].' 23:59:59');
	    	if($time > $licenceValidity){//资质效期失效
	    		echo $omsg = '{"backtype":"error", "sn":"您的许可证已过期，不能进行采购"}';
	    		exit;
	    	}
	    }

	    //该经销商状态
	    $cinfo = clientinfo();
	    if(!$cResult['status']) 
	        $omsg = '{"backtype":"error", "sn":"'.$cResult['rmsg'].'"}';
	    else if(empty($cinfo) || $cinfo['ClientFlag'] == "1" || $cinfo['ClientFlag'] == "8" || $cinfo['ClientFlag'] == "9"){
	    	
	    	if($cinfo['ClientFlag'] == "8" || $cinfo['ClientFlag'] == "9"){
				 $omsg = '{"backtype":"error", "sn":"您的帐号还处于待审核状态，暂不能下单，请联系供应商审核!"}';
			}else{
				$omsg = '{"backtype":"error", "sn":"您的帐号已禁用，暂不能下单，请联系供应商审核!"}';
			}
	    }
	    else{	 		
    		$in1 = $input->_htmlentities($in);
    		$oid = $shcart->insertcart($in1);
    		if($oid=="empty"){ 
				$omsg = '{"backtype":"empty", "sn":"'.$oid.'"}'; 
			}else{ 
				$shcart->add_order_tracking($oid);
				$omsg = '{"backtype":"ok", "sn":"'.$oid.'"}';
			}
	    }
	}
	echo $omsg;
	exit();

}elseif($in['m']=="setaccount"){
	//当前经销商在易极付是否已开户
	$myYJF = array();
	$YOpenApiSet = new YOpenApiSet();
	$myYJF = $YOpenApiSet->getSignInfo(intval($_SESSION['cc']['cid']));	

	if(empty($myYJF)){
		exit('error');
	}
	else{
		exit('ok');
	}
	//end	
}
else{
if(empty($in['id']))
{
	if(empty($_SESSION['cartitems'])) header("Location: cart.php");
	$pagesize = 50;

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
					$cartproduct[$i]['spec']	    = base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
				}
			}else if(!empty($pos_spec)){
				$cartproduct[$i]['pid']		= substr($key, 0, $pos_spec);
				$cartproduct[$i]['spec']	    = base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
			}
			$cartproduct[$i]['kid']		= $key;
			$cartproduct[$i]['number']	= $var;

			$idmsg .= ",".$cartproduct[$i]['pid'];
			$i++;
		}
		$cartarray = $shcart->listcartgoods($idmsg,$iss);

		for($j=0;$j<count($cartarray['all']);$j++)
		{
			$carttemp[$cartarray['all'][$j]['ID']] =  $cartarray['all'][$j];
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
        $stair_count = 0;//商品可优惠金额
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
			$carttempproduct[$k]['price']   = $carttemp[$cartproduct[$k]['pid']]['Price'];
			$carttempproduct[$k]['pencent'] = $carttemp[$cartproduct[$k]['pid']]['Pencent'];
			$carttempproduct[$k]['price_end'] = $carttempproduct[$k]['price'] * $carttempproduct[$k]['pencent'] / 10;
			$carttempproduct[$k]['notetotal'] = $cartproduct[$k]['number'] * $carttempproduct[$k]['price'] * ($carttempproduct[$k]['pencent']/10);
			//$carttempproduct[$k]['notetotal'] = sprintf("%01.2f", $carttempproduct[$k]['notetotal']);
			$producttotal = $producttotal + $carttempproduct[$k]['notetotal'];
			$productnum   = $productnum + $carttempproduct[$k]['number'];

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
		if($isempty) echo '<script language="javascript" type="text/javascript">alert("库存不够，请修改订购商品数量!"); window.location.href="cart.php"</script>';
		$producttotal = sprintf("%01.2f", round($producttotal,2));

        $stair_count = get_stair($producttotal);
        $stair_amount = get_stair($producttotal,'amount');

	}else{
		$cartproduct  = null;
	}
	$addressdata      = null;
	$defaultaddress   = null;

	$addressdata = $shcart->listaddress();
	if(!empty($addressdata))
	{
		foreach($addressdata as $key=>$var)
		{
			if($key==0)
			{
				$defaultaddress = $var;
			}elseif($var['AddressFlag']==1){
				$defaultaddress = $var;
				break;
			}
		}
	}else{
		$defaultaddress['AddressCompany'] = $_SESSION['cc']['ccompanyname'];
		$defaultaddress['AddressContact'] = $_SESSION['cc']['ctruename'];
		$defaultaddress['AddressPhone']	  = $_SESSION['cc']['cphone'];
		$defaultaddress['AddressAddress'] = $_SESSION['cc']['cadd'];
	}
	
	$clientdata    = $shcart->showclient();

	$sendtypedata  = $shcart->listsendtype();
	$paytypedata   = $shcart->listpaytype();
	
	//begin 增加取易极付，网银，支付宝开通信息 2015-9-17 by tubo
	//易极付网关信息
	$NetGetWay    = new NetGetWay();
	$yijifuInfo   = $NetGetWay->showGetway('yijifu', $_SESSION['ucc']['CompanyID'], '', true);
	$allinpayInfo = $NetGetWay->showGetway('allinpay', $_SESSION['ucc']['CompanyID'], '', true);
	$alipayInfo	= null;
	$acclist	= orderdata::listaccounts();
	foreach($acclist as $v){
		if($v['PayType']=='alipay'){
			if(!empty($v['AccountsNO']) && !empty($v['PayPartnerID']) && !empty($v['PayKey']))
				$alipayInfo = $v;
		}
	}
	//$alipayInfo   = $shcart->showaccounts;

		
	//当前经销商在易极付是否已开户
	$myYJF = array();
	$YOpenApiSet = new YOpenApiSet();
	$myYJF = $YOpenApiSet->getSignInfo(intval($_SESSION['cc']['cid']));
	//end 

        //当前商业公司是否开通医统账期
        $CompanyCredit = orderdata::CompanyCredit();
        //end
        
        //判断经销商是否上传企业资质
        $BottomZizhi = orderdata::BottomZizhi();
	include template("order");
	
	}else{	
		
		$accinfo = $shcart->showaccounts();
		if(empty($accinfo['AccountsNO']) || empty($accinfo['PayPartnerID']) || empty($accinfo['PayKey'])) $ispay = "no"; else $ispay = 'pay';

		$getway = $shcart->show_getway('allinpay');
		$YJFgetway = orderdata::show_getway('yijifu');
		
		$bankarr = $shcart->listbank();
		
		//易极付网关信息
		$NetGetWay = new NetGetWay();
		$netInfo = $NetGetWay->showGetway('yijifu', $_SESSION['ucc']['CompanyID'], '', true);
		$allinpayInfo = $NetGetWay->showGetway('allinpay', $_SESSION['ucc']['CompanyID'], '', true);
		$alipayInfo	= null;
		$acclist	= orderdata::listaccounts();
		foreach($acclist as $v){
			if($v['PayType']=='alipay'){
				if(!empty($v['AccountsNO']) && !empty($v['PayPartnerID']) && !empty($v['PayKey']))
					$alipayInfo = $v;
			}
		}
		//$alipayInfo   = $shcart->showaccounts;
		
		//当前经销商在易极付是否已开户
		$myYJF = array();
		$YOpenApiSet = new YOpenApiSet();
		$myYJF = $YOpenApiSet->getSignInfo(intval($_SESSION['cc']['cid']));
		
		$paysn = date("Ymd").microtime_float();
		
		$paytypearr = array (
		  '1' => '现金(先付)',
		  '2' => '转帐(先付)',
		  '3' => '支付宝在线支付',
		  '4' => '转帐(后付)',
		  '5' => '代收款',
		  '6' => '月结',
		  '7' => '预存款(先付)',
		  '8' => '货到付款',
		  '9' => '快捷支付',
		  '10' => '网银支付',
		  '11' => '支付宝支付',
                  '12' => '医统账期',
		);
		
		$paytypedata   = $shcart->listpaytype();
		$payInfo = array('9' => '快捷支付', '11' => '支付宝','12'=>'医统账期');
		foreach($paytypedata as $val){
			$payInfo[$val['TypeID']] = $val['TypeName'];
		}
		
		//订单信息
		$oinfo = orderdata::getorderinfo($in['id']); 
		if($oinfo['OrderPayStatus'] == 2 || $oinfo['OrderPayStatus'] == 4){//该订单已支付完毕
			header("location:./myorder.php");
			exit;
		}

		if(!empty($in['type'])){
			$oinfo['OrderPayType']=$in['type'];
		}
	
		if(($oinfo['OrderPayType']==9)||($oinfo['OrderPayType']==10)||($oinfo['OrderPayType']==11)){
// 			include template("order2");
// 			include template("order1");
		}elseif($oinfo['OrderPayType']==7){
			$payTotal = 0;
			$ytotal = orderdata::get_client_money();
			if($payTotal > $ytotal) $payTotal = $ytotal;
			if($ytotal <= 0) $payTotal = 0;
			$ytotal = round($ytotal,2);
		
// 			include template("order1");
		}elseif($oinfo['OrderPayType']==12){
            //医统账期支付
            $PayPaw = client::getPayPwdMobile();
            
//             $PayPaw = array();
                    
        }else{
// 			include template("order3");
// 			include template("order2");
		}
		
		include template("order1");
	}
//END
}
?>