<?php 

class ShoppingCart { 

    var $items;
	var $File = '';

	 function ShoppingCart()
	 {
		if(!empty($_SESSION['cartitems']))
		{
			$this->items = $_SESSION['cartitems'];
		}else{
			$tempcart = unserialize($this->read_file('r'));
			if(!empty($tempcart)) $this->items = $_SESSION['cartitems'] = $tempcart;
		}
	 }
	
	 //清空购物车
	 function clear_items()
	 {
		$_SESSION['cartitems'] = null;
		unset($_SESSION['cartitems']);
		$this->write_file('','w');
		$this->items = null;
		unset($this->items);
	 }

	 //添加到购物车
     function add_items($product_id, $product_color, $product_spec, $qty=1) 
     { 
        if(empty($qty)) $qty = 1;
		$kid = commondata::make_kid($product_id, $product_color, $product_spec);        

	    if(@array_key_exists($kid, $this->items)) 
        { 
			$this->items[$kid]  =  $this->items[$kid]+$qty;
	    }else{
			$this->items[$kid]  =  $qty; 	   
	    }
     }
	 
	 //添加到购物车多规格，颜色
     function add_items_arr($p_id,$cartarr) 
     { 
		 $fp = array('+','/','=','_');
		 $rp = array('-','|','DHB',' ');
		 $qty = 1;

		 $cy   = str_replace($fp,$rp,base64_encode("统一"));
		 if(!empty($cartarr))
		 {
			 foreach($cartarr as $key=>$var)
			 {
				if(!empty($var)) $qty = $var;
				$karr = explode("_",$key);
				if($karr[1]==$cy) $pspec  = ''; else $pspec  = $karr[1];
				if($karr[2]==$cy) $pcolor = ''; else $pcolor = $karr[2];
				$kid = $p_id;
				if(!empty($pcolor))
				{
				   $kid .= "_p_".$pcolor;
				}
				if(!empty($pspec))
				{
				   $kid .= "_s_".$pspec;
				}
				if(@array_key_exists($kid, $this->items)) 
				{ 
					$this->items[$kid]  =   $this->items[$kid]+$qty;
				}else{
					$this->items[$kid]  =   $qty; 	   
				}
			 }
		 }
     } 

	//更新购物车
     function update_items($kid, $qty) 
     { 
        if(@array_key_exists($kid, $this->items)) 
        { 
			if($this->items[$kid]>$qty) 
			{ 
				$this->items[$kid]-=($this->items[$kid]-$qty); 
			} 
			if($this->items[$kid]<$qty) 
			{ 
				$this->items[$kid]+=abs($this->items[$kid]-$qty); 
			} 
			if($qty==0) 
			{ 
				unset($this->items[$kid]); 
			} 
		} 
    } 

	//从购物车移除
    function remove_item($kid) 
    { 
		if(@array_key_exists($kid, $this->items)) 
		{ 
			unset($this->items[$kid]);
		} 
    } 

	//显示
    function show_cart() 
    { 
       $_SESSION['cartitems'] = $this->items;
	   $cookiemsg = serialize($this->items);
	   $this->write_file($cookiemsg,'w');
	   return $this->items; 
    }

	//读文件
	function read_file($Mode = "r")
	{
		$cartcontent = '';
		if(empty($this->File))
		{
			$filename	= md5($_SESSION['cc']['cid']).".txt";
			$path		= CART_PATH.$_SESSION['cc']['ccompany']."/";
			if(!file_exists($path)) mkdir($path, 0777);		
			$this->File = $path.$filename;
		}
		$cartcontent = @file_get_contents($this->File); 

		return $cartcontent;
	}

	//写文件
	function write_file($wcontent,$Mode = "w")
	{
		$isw = false;
		if(empty($this->File))
		{
			$filename	= md5($_SESSION['cc']['cid']).".txt";
			$path		= CART_PATH.$_SESSION['cc']['ccompany']."/";
			if(!file_exists($path)) mkdir($path, 0777);		
			$this->File = $path.$filename;
		}

		if ($Fp = fopen($this->File, $Mode))
		{
			$isw = fwrite($Fp, $wcontent);
			@fclose($Fp);
		}
		return $isw;
	}
	

	//列出购物车商品信息
	function listcartgoods($idmsg,$ison)
	{
		$db	     = dbconnect::dataconnect()->getdb();
		$data_cs = null;

		if($ison=="on")
		{
			$result['ison'] = 'on';
			$sql_l = "SELECT i.ID,i.BrandID,i.CommendID,i.Name,i.Coding,i.Model,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,(CASE WHEN n.OrderNumber < 0 THEN 0 ELSE n.OrderNumber END) as OrderNumber FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_number n on i.ID=n.ContentID where i.ID in ( ".$idmsg." ) and i.CompanyID = ".$_SESSION['cc']['ccompany']." and i.FlagID=0 ";

			$sql     = "select ContentID,ContentColor,ContentSpec,OrderNumber from ".DATATABLE."_order_inventory_number where  CompanyID=".$_SESSION['cc']['ccompany']." and ContentID in ( ".$idmsg." )";
			$data_cs = $db->get_results($sql);
		}else{
			$result['ison'] = '';
			$sql_l  = "select ID,BrandID,CommendID,Name,Coding,Model,Price1,Price2,Price3,Units,Casing from ".DATATABLE."_order_content_index where ID in ( ".$idmsg." ) and CompanyID=".$_SESSION['cc']['ccompany']." and FlagID=0 ";
		}
		$datat = $db->get_results($sql_l);

		//整包装出货
		$sql_c = "select ContentIndexID,Package from ".DATATABLE."_order_content_1 where ContentIndexID in ( ".$idmsg." ) and CompanyID=".$_SESSION['cc']['ccompany']." ";
		$datac = $db->get_results($sql_c);
		if(!empty($datac))
		{
			foreach($datac  as $cvar)
			{
				if(empty($cvar['Package'])) $cvar['Package'] = 0;
				$result['package'][$cvar['ContentIndexID']]  =  $cvar['Package'];
			}
		}
		
		//获取品牌
		$brandarr = commondata::getbrandinfo(0,10000);
		foreach ($brandarr as $val){
		    $branddata[$val['BrandID']] = $val;
		}
		
		//获取订购价格
		for($i=0;$i<count($datat);$i++)
		{
			$datat[$i]['Price']      = $datat[$i][$_SESSION['cc']['csetprice']];
			if($datat[$i]['CommendID'] == "2")
			{
				$datat[$i]['Pencent'] = '10.0';
			}else{				
				if(!empty($datat[$i]['BrandID']) && !empty($_SESSION['cc']['cbrandpercent'][$datat[$i]['BrandID']]))
				{
					$datat[$i]['Pencent'] = $_SESSION['cc']['cbrandpercent'][$datat[$i]['BrandID']];
				}else{
					$datat[$i]['Pencent'] = $_SESSION['cc']['csetpercent'];
				}
			}
			$price3 = commondata::setprice3($datat[$i]['Price3']);
			if(!empty($price3))
			{
				$datat[$i]['Price']   = $price3;
				$datat[$i]['Pencent'] = '10.0';
			}
			//匹配品牌名
			$datat[$i]['BrandName'] = $branddata[$datat[$i]['BrandID']]['BrandName'];
		}
	
		$result['all']  = $datat;
		$result['cosp'] = $data_cs;

		return $result;
		unset($result);
	}

	function listordercartgoods($idmsg)
	{
		$db	    = dbconnect::dataconnect()->getdb();

		$sql_l  = "select ID,Name,Price1,Price2,Units,Casing from ".DATATABLE."_order_content_index where ID in ( ".$idmsg." ) and CompanyID=".$_SESSION['cc']['ccompany']." and FlagID=0 ";
		$datat  = $db->get_results($sql_l);
		$result = $datat;

		//$db->debug();
		return $result;
		unset($result);
	}

	//取收货地址
	function listaddress()
	{
		$db	    = dbconnect::dataconnect()->getdb();

		$sql_l  = "select AddressID,AddressClientName,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag from ".DATATABLE."_order_address where AddressClient = ".$_SESSION['cc']['cid']." order by AddressFlag desc,AddressID desc limit 0,100";
		$result	= $db->get_results($sql_l);
		
		//检查是否有默认地址
		$length = count($result);
		$myDefault = false;
		for($i = 0; $i < $length; $i++){
			if($result[$i]['AddressFlag']){
				$myDefault = true;
				break;
			}
		}
		//设置默认值
		if(count($result) && !$myDefault) $result[0]['AddressFlag'] = 1;

		//$db->debug();
		return $result;
	}

	//取收款帐号
	function listbank()
	{
		$db	      = dbconnect::dataconnect()->getdb();

		$bankdata = $db->get_results("SELECT AccountsBank,AccountsNO,AccountsName,AccountsType FROM ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['cc']['ccompany']." limit 0,50");

		//$db->debug();
		return $bankdata;
	}

	//送货方试
	function listsendtype()
	{
		$db	     = dbconnect::dataconnect()->getdb();

		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['cc']['ccompany']." and SetName='send' limit 0,1");
		if(!empty($setinfo['SetValue']))
		{	
			$valuearr = unserialize($setinfo['SetValue']);
			$valuemsg = implode(",", $valuearr);
			$sql_l  = "select TypeID,TypeName,TypeAbout from ".DATABASEU.DATATABLE."_order_sendtype where TypeID in (".$valuemsg.") order by TypeID asc limit 0,10";
			$result	= $db->get_results($sql_l);
		}
		if(empty($result))
		{
			$sql_l  = "select TypeID,TypeName,TypeAbout from ".DATABASEU.DATATABLE."_order_sendtype order by TypeID asc limit 0,10";
			$result	= $db->get_results($sql_l);
		}

		$result[0]['TypeFlag'] = 1;
		//$db->debug();
		return $result;
	}

	//付款方式
	function listpaytype()
	{
		$db	    = dbconnect::dataconnect()->getdb();

		if(!empty($_SESSION['cc']['cclientpay']) && $_SESSION['cc']['cclientpay']<=8)
		{
			$valuemsg = $_SESSION['cc']['cclientpay'];
		}else{
			$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['cc']['ccompany']." and SetName='pay' limit 0,1");
			if(!empty($setinfo['SetValue']))
			{	
				$valuearr   = unserialize($setinfo['SetValue']);
				$valuemsg = implode(",", $valuearr);
			}else{
				$valuemsg = '';
			}
		}
		if(!empty($valuemsg))
		{	
			$sql_l  = "select TypeID,TypeName,TypeAbout from ".DATABASEU.DATATABLE."_order_paytype where TypeClose=0 and TypeID in (".$valuemsg.") order by TypeID DESC limit 0,20";
			$result	= $db->get_results($sql_l);
		}

		if(empty($result))
		{
			$sql_l  = "select TypeID,TypeName,TypeAbout from ".DATABASEU.DATATABLE."_order_paytype where TypeClose=0 order by TypeID DESC limit 0,20";
			$result	= $db->get_results($sql_l);
		}
		
		$result[0]['TypeFlag'] = 1;
		//$db->debug();
		return $result;
	}

	//插入订单
	function insertcart($in)
	{
		$db  = dbconnect::dataconnect()->getdb();
		$fp  = array('+','/','=','_');
		$rp  = array('-','|','DHB',' ');

		if(!empty($_SESSION['cartitems']))
		{
			$i = 0;
			$idmsg = "0";
			$tykey = str_replace($fp,$rp,base64_encode("统一"));

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
				$cartproduct[$i]['encolor'] = '';
				$cartproduct[$i]['enspec']  = '';
			}
			else if(!empty($pos_color))
			{
				$cartproduct[$i]['pid']		    = substr($key, 0, $pos_color);
				if(empty($pos_spec))
				{
					$cartproduct[$i]['color']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_color+3)));
					$cartproduct[$i]['encolor'] = substr($key, $pos_color+3);
					$cartproduct[$i]['enspec']  = $tykey;
				}else{
					$cartproduct[$i]['color']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_color+3,$pos_spec-$pos_color-3)));
					$cartproduct[$i]['spec']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
					$cartproduct[$i]['encolor'] = substr($key, $pos_color+3,$pos_spec-$pos_color-3);
					$cartproduct[$i]['enspec']  = substr($key, $pos_spec+3);
				}
			}else if(!empty($pos_spec)){
				$cartproduct[$i]['pid']		= substr($key, 0, $pos_spec);
				$cartproduct[$i]['spec']	= base64_decode(str_replace($rp,$fp,substr($key, $pos_spec+3)));
				$cartproduct[$i]['encolor'] = $tykey;
				$cartproduct[$i]['enspec']  = substr($key, $pos_spec+3);
			}
			$cartproduct[$i]['kid']		    = $key;
			$cartproduct[$i]['number']		= $var;

			$idmsg .= ",".$cartproduct[$i]['pid'];
			$i++;
		}
		$cartarray = $this->listcartgoods($idmsg,$iss);

		for($j=0;$j<count($cartarray['all']);$j++)
		{
			$carttemp[$cartarray['all'][$j]['ID']] =  $cartarray['all'][$j];
		}

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
		$isempty = false;

		for($k=0;$k<count($cartproduct);$k++)
		{
			$carttempproduct[$k]			  = $carttemp[$cartproduct[$k]['pid']];
			$carttempproduct[$k]['notetotal'] = $cartproduct[$k]['number'] * $carttempproduct[$k]['Price'] * ($carttempproduct[$k]['Pencent']/10);
			$producttotal = $producttotal + $carttempproduct[$k]['notetotal'];
			$productnum   = $productnum + $cartproduct[$k]['number'];			

			if(empty($carttempproduct[$k]['OrderNumber'])) $carttempproduct[$k]['OrderNumber'] = 0;
			if(empty($cartarray['ison']))
			{
				$carttempproduct[$k]['onumber'] = 9999999999;
			}else{
				if(empty($cartproduct[$k]['color']) && empty($cartproduct[$k]['spec']))
				{
					$carttempproduct[$k]['onumber'] = $carttempproduct[$k]['OrderNumber'];
				}else{
					$carttempproduct[$k]['onumber'] = $cospnumarr[$cartproduct[$k]['kid']];
				}
			}
			if(empty($carttempproduct[$k]['onumber'])) $carttempproduct[$k]['onumber'] = 0;

			if(($cartproduct[$k]['number'] > $carttempproduct[$k]['onumber']) && $pn=="on" && $png=="off")
			{
				$carttempproduct[$k]['library'] = "empty";
				$isempty = true;
			}else{
				$carttempproduct[$k]['library'] = "ok";
			}
		}
		$producttotal = sprintf("%01.2f", round($producttotal,2));

		if($isempty)
		{
			return 'empty';
		}else{
			$osn = $db->get_row("SELECT OrderID,OrderSN from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['cc']['ccompany']." and OrderSN!='' order by OrderID desc limit 0,1");
			
			
			if(empty($osn['OrderSN']))
			{
				$OrderSN = date("Ymd")."-".mt_rand(1999,5999);
			}else{
				
				$today   = date("Ymd");
				$nowDate = substr($osn['OrderSN'], 0, 8);
				$nextid	 = intval(substr($osn['OrderSN'],strpos($osn['OrderSN'], '-')+1))+1;
				$OrderSN = $nowDate == $today ? (date("Ymd")."-".$nextid) : (date("Ymd")."-".mt_rand(1999,5999));
			}
			
			if(empty($in['paytype'])) $in['paytype'] = 0;
			if(empty($in['sendtype'])) $in['sendtype'] = 0;
			if(!empty($setarr['audit_type']) && $setarr['audit_type']=="on") $autidstatus = 'F'; else $autidstatus = 'T';
			
			//税点
			if(empty($in['invoicetype'])){ $in['invoicetype'] = 'N';}
			if($in['invoicetype'] == 'P'){
				$invoicetax = $setarr['invoice_p_tax'];
			}elseif($in['invoicetype'] == 'Z'){
				$invoicetax = $setarr['invoice_z_tax'];
			}else{
				$invoicetax = 0;
			}

            $productPure = $producttotal;//纯商品总价 用来计算优惠
            $stair_count = get_stair($productPure);

            $producttotal = $producttotal - $stair_count;
			$producttotal = $producttotal + ($producttotal * $invoicetax / 100); //含税总价
			$producttotal = sprintf("%01.2f", round($producttotal,2));

            $orderSpecial = 'F';
            if($stair_count > 0) {
                $orderSpecial = 'T';
            }

			$sql_l  = "insert into ".DATATABLE."_order_orderinfo(OrderSN,OrderCompany,OrderUserID,OrderSendType,OrderPayType,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,OrderReceiveAdd,InvoiceType,InvoiceTax,DeliveryDate,OrderRemark,OrderTotal,OrderDate,OrderSaler,OrderFrom,OrderSpecial) values('".$OrderSN."',".$_SESSION['cc']['ccompany'].", '".$_SESSION['cc']['cid']."', ".$in['sendtype'].",".$in['paytype'].",'".$in['AddressCompany']."','".$in['AddressContact']."','".$in['AddressPhone']."','".$in['AddressAddress']."','".$in['invoicetype']."','".$invoicetax."','".$in['DeliveryDate']."','".$in['OrderRemark']."','".$producttotal."',".time().",'".$autidstatus."','Compute','{$orderSpecial}')";
			$db->query($sql_l);

			$osnid = $db->get_row("SELECT OrderID,OrderSN from ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderSN='".$OrderSN."' order by OrderID desc limit 0,1");

			if(!empty($osnid['OrderID']))
			{
				$oid = $osnid['OrderID'];
				if(!empty($in['invoicetype']) && $in['invoicetype'] != 'N'){
					if(!empty($in['InvoiceHeader'])){
						$db->query("insert into ".DATATABLE."_order_invoice(OrderID,CompanyID,ClientID,InvoiceType,AccountName,BankName,BankAccount,InvoiceHeader,InvocieContent,TaxpayerNumber,InvoiceDate) values(".$oid.",".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",'".$in['invoicetype']."','".$in['AccountName']."','".$in['BankName']."','".$in['BankAccount']."','".$in['InvoiceHeader']."','".$in['InvocieContent']."','".$in['TaxpayerNumber']."',".time().")");

					}
				}

				for($k=0;$k<count($cartproduct);$k++)
				{
					if(!empty($carttempproduct[$k]['ID']))
					{
						$addInset[] = "(
   							    ".$oid.",
   							    ".$_SESSION['cc']['ccompany'].",
   							    ".$_SESSION['cc']['cid'].",
   							    ".$carttempproduct[$k]['ID'].",
								'".$carttempproduct[$k]['Name']."', 
								'".$cartproduct[$k]['color']."', 
								'".$cartproduct[$k]['spec']."',
								'".$carttempproduct[$k]['Price']."',
								".$cartproduct[$k]['number'].",
								'".$carttempproduct[$k]['Pencent']."'
								)";
					}
				}
				
				//tubo begin 增加 保证cart里面加入的金额和订单金额一致  2016-04-06
				$sql = "insert into ".$sdatabase.DATATABLE."_order_cart(OrderID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent) values ".implode(",", $addInset);
				$isIncart = $db->query($sql);
			
				if($isIncart){
					for($k=0;$k<count($cartproduct);$k++)
					{
						if(!empty($carttempproduct[$k]['ID']))
						{
							if($pn=="on")
							{	
								if(!empty($cartproduct[$k]['encolor']) && !empty($cartproduct[$k]['enspec']))
								{
									$db->query("update ".DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$cartproduct[$k]['number']." where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$carttempproduct[$k]['ID']." and ContentColor='".$cartproduct[$k]['encolor']."' and ContentSpec='".$cartproduct[$k]['enspec']."' limit 1");
									
									$db->query("update ".DATATABLE."_order_number set OrderNumber=(select sum(OrderNumber) from ".DATATABLE."_order_inventory_number where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$carttempproduct[$k]['ID']." ) where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$carttempproduct[$k]['ID']." limit 1");
								}else{
									$db->query("update ".DATATABLE."_order_number set OrderNumber=OrderNumber-".$cartproduct[$k]['number']." where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID=".$carttempproduct[$k]['ID']." limit 1");
								}
								$db->query("insert into ".DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$_SESSION['cc']['ccompany']},{$carttempproduct[$k]['ID']},{$oid},{$cartproduct[$k]['number']},'order')");
							}
						}
					}
					
	                 if($stair_count > 0) {
	                 	$stair_amount = get_stair($productPure,'amount');
	                	$db->query("INSERT INTO ".DATATABLE."_order_ordersubmit (CompanyID,OrderID,AdminUser,Name,Date,Status,Content) VALUES (".$_SESSION['cc']['ccompany'].",".$osnid['OrderID'].",'client','经销商',".time().",'订单满省','订单满 ¥{$stair_amount} 省 ¥{$stair_count} ，金额： ¥{$producttotal}')");
	            	}
	           	    
					// end 
					
					unset($_SESSION['cartitems']);
					$this->write_file('','w');
			
					$message = "【".$_SESSION['ucc']['CompanySigned']."】您有一个新订单:NO.".$OrderSN.",来自:".$_SESSION['cc']['ccompanyname']."(".$_SESSION['cc']['cusername'].")金额为:".$producttotal." 元,请尽快登录医统天下BMB系统处理。退订回复TD";
					sms::get_setsms("1",$message);
					sms::get_setsms('9',$message);
			
					return $OrderSN;
				}else{
					$db->query("delete from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['cc']['ccompany']." and OrderUserID=".$_SESSION['cc']['cid']." and OrderID=".$osnid['OrderID']);
					return 'error';
				}
			}else{
				return 'error';
			}
		}
		}
		return 'error';
	}

	//帐号
	function showaccounts()
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select * from ".DATATABLE."_order_accounts where AccountsCompany = ".$_SESSION['cc']['ccompany']." and PayType='alipay'  order by AccountsID asc limit 0,1";       
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}

	//客户资料
	function showclient()
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select * from ".DATATABLE."_order_client where ClientID=".$_SESSION['cc']['cid']." and ClientCompany = ".$_SESSION['cc']['ccompany']." limit 0,1";       
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}

	//判断是否符合最低金额
	function check_order_amount()
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select OrderAmount from ".DATATABLE."_order_client where ClientID=".$_SESSION['cc']['cid']." and ClientCompany = ".$_SESSION['cc']['ccompany']." limit 0,1";       
		$result	= $db->get_var($sql_l);
		
		if(empty($result)){
			$sql_l  = "select OrderAmount from ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['cc']['ccompany']." limit 0,1";       
			$result	= $db->get_var($sql_l);
		}
		//$db->debug();
		return $result;
		unset($result);
	}
	
	//在线支付帐号
	function show_getway($getway='allinpay')
	{
		$db	= dbconnect::dataconnect()->getdb();

		$sql_l  = "select MerchantNO,SignMsgKey,SignMsg from ".DATABASEU.DATATABLE."_order_getway where CompanyID = ".$_SESSION['cc']['ccompany']." and Status='T' and GetWay='".$getway."' order by GetWayID asc limit 0,1";       
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}
	
	//跟踪订单信息 
	//params order_sn 订单号
	//params type 1.提交订单跟踪信息 2.订单付款跟踪信息
	//add by @ltc  2017-06-10 
	function add_order_tracking($order_sn,$type=1){
		//echo 23234;exit;
		$db	= dbconnect::dataconnect()->getdb();
		$time=time();
		$OrderID=0;
		$result	=false;
		if(!empty($order_sn)){
			$sql_l  = "select OrderID from ".DATATABLE."_order_orderinfo where OrderSN='".$order_sn."' and OrderCompany ='".$_SESSION['cc']['ccompany']."'";   
			$OrderID=$db->get_var($sql_l);
		}else{
			return false;
		}
		
		if(!empty($OrderID)){
			if($type==1){
				$msg="('".$_SESSION['cc']['ccompany']."','".$OrderID."','提交订单','".$_SESSION['cc']['cusername']."','".$time."','订单已提交','订单已提交，单号：".$order_sn."')";
			}else{
				$msg="('".$_SESSION['cc']['ccompany']."','".$OrderID."','订单付款','".$_SESSION['cc']['cusername']."','".$time."','订单已付款','订单已付款，单号：".$order_sn."')";
			}
			
			$sql_l  = "insert into ".DATATABLE."_order_ordersubmit  (CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values ".$msg;       
			$result	= $db->query($sql_l);
		}
		return (bool)$result;
		unset($result);
	}
	

//END
}
?>