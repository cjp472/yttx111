<?php
class commondata
{

	function make_kid($product_id, $product_color='', $product_spec='')
	{
	    $kid = $product_id;
		$fp = array('+','/','=','_');
		$rp = array('-','|','DHB',' ');

		if(!empty($product_color))
		{
		   $kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
		}
		if(!empty($product_spec))
		{
		   $kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
		}

		return $kid;
	}

	//获取执行价格
	function setprice3($p3)
	{
		$rp3 = '';
		$lkey = '';
		if(!empty($p3))
		{
			$pricearr = unserialize(urldecode($p3));
			//单个指定
			if(!empty($pricearr['clientprice'][$_SESSION['cc']['cid']]))
			{
				$rp3 = $pricearr['clientprice'][$_SESSION['cc']['cid']];
			}else{
				if(empty($pricearr['typeid'])) $pricearr['typeid'] = 'A';
				if(!empty($_SESSION['cc']['clevel']))
				{
					$clientlevelarr = explode(",", $_SESSION['cc']['clevel']);
					if(substr($clientlevelarr[0],0,1)==="l")
					{
						if($pricearr['typeid']=="A") $lkey = $clientlevelarr[0];
					}else{
						foreach($clientlevelarr as $cvar)
						{
							if($pricearr['typeid']==substr($cvar,0,1))
							{
								$lkey = substr($cvar,2);
								break;
							}
						}
					}				
				}
				if(!empty($pricearr[$lkey])) $rp3 = $pricearr[$lkey];
			}
		}
		return $rp3;
	}

	//产品设置
	function getproductset($ty='product')
	{	
		$db = dbconnect::dataconnect()->getdb();	

		$sql_l  = "SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['cc']['ccompany']." and SetName='".$ty."' limit 0,1";
		$result	= $db->get_row($sql_l);
		if(!empty($result['SetValue'])) $valuearr = unserialize($result['SetValue']);
		if(!empty($valuearr)) $typemsg = $valuearr; else $typemsg = null;

		//$db->debug();
		return $typemsg;
		unset($result);
	}

	//分类信息
	function getsortinfo($num=15)
	{	
		$db = dbconnect::dataconnect()->getdb();		

		$sql_l  = "SELECT SortID,SortName FROM ".DATATABLE."_order_sort where SortCompany=".$_SESSION['cc']['ccompany']." order by SortOrder DESC,SortID ASC limit 0,".$num;
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}

	//在线交流工具信息
	function gettoolinfo($num=5)
	{	
		$db = dbconnect::dataconnect()->getdb();		

		$sql_l  = "SELECT ToolID,ToolType,ToolName,ToolNO,ToolCode FROM ".DATATABLE."_order_tool where ToolCompany=".$_SESSION['cc']['ccompany']." order by ToolType ASC, ToolID ASC limit 0,".$num;
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}


	//联系方式
	function getcontactinfo($num=5)
	{	
		$db = dbconnect::dataconnect()->getdb();		

		$sql_l  = "SELECT ContactName,ContactValue FROM ".DATATABLE."_order_contact where ContactCompany=".$_SESSION['cc']['ccompany']." order by ContactID ASC limit 0,".$num;
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}

	//底部信息
	function getbuttoninfo()
	{	
		$db = dbconnect::dataconnect()->getdb();		

		$result = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['cc']['ccompany']." and SetName='template' limit 0,1");

		if(empty($result['SetValue']))
		{
			$rbuttonmsg = '<div class="foot"><p align="right">Powered By Rsung DingHuoBao (<a href="http://www.dhb.hk" target="_blank">WWW.DHB.HK </a>) System © 2006 - 2013 <a href="http://www.rsung.com" target="_blank">Rsung</a> Ltd.   <br />建议您使用 IE8,IE9 浏览器</p></div>';
		}else{
			$rbuttonmsg = html_entity_decode($result['SetValue'], ENT_QUOTES,'UTF-8');
		}

		//$db->debug();
		return $rbuttonmsg;
		unset($result);
	}


	//品牌
	function getbrandinfo($s = 0,$num = 10000, $index = false)
	{	
		$db = dbconnect::dataconnect()->getdb();		
		if($index && !$s)
		{
			$sql_l = "SELECT b.BrandID,b.BrandNO,b.BrandName,b.BrandPinYin,b.Logo,(SELECT COUNT(*) FROM ".DATATABLE."_order_content_index AS c WHERE c.BrandID=b.BrandID) AS total FROM ".DATATABLE."_order_brand AS b WHERE b.CompanyID=".$_SESSION['cc']['ccompany']." ORDER BY b.IsIndex DESC, b.BrandID ASC LIMIT 0,".$num;
		}elseif(empty($s)){
			$sql_l  = "SELECT BrandID,BrandNO,BrandName,BrandPinYin FROM ".DATATABLE."_order_brand where CompanyID=".$_SESSION['cc']['ccompany']." order by BrandID ASC limit 0,".$num;
		}else{
			$sortinfo = $db->get_row("SELECT SiteNO FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and SiteID=".intval($s)." limit 0,1");
			
			$sql_l  = "SELECT DISTINCT b.BrandID,b.BrandNO,b.BrandName,b.BrandPinYin,b.Logo FROM ".DATATABLE."_order_brand b left join ".DATATABLE."_view_index_site i ON b.BrandID=i.BrandID where b.CompanyID=".$_SESSION['cc']['ccompany']." and i.SiteNO like '".$sortinfo['SiteNO']."%' order by b.BrandID ASC limit 0,".$num;
		}

		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}
	
	//品牌街
	function getbrandlist($ps = 18, $lurl='brand.php' ,$kw='',$action="")
	{
		$db = dbconnect::dataconnect()->getdb();
		$search="";
		$search2="";
		if(!empty($kw)){
			$search=" and (BrandName like '%".$kw."%' or BrandPinYin like '%".$kw."%') ";
			$search2=" and (b.BrandName like '%".$kw."%' or b.BrandPinYin like '%".$kw."%') ";
			if($action=='vague'){
				$search=" and (BrandName like '".$kw."' or BrandPinYin like '".$kw."') ";
				$search2=" and (b.BrandName like '".$kw."' or b.BrandPinYin like '".$kw."') ";
			}
			
		}
		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_brand where CompanyID=".$_SESSION['cc']['ccompany']." ".$search;
		
		$sql_l = "SELECT b.BrandID,b.BrandNO,b.BrandName,b.BrandPinYin,b.Logo,(SELECT COUNT(*) FROM ".DATATABLE."_order_content_index AS c WHERE c.BrandID=b.BrandID AND c.FlagID=0) AS total FROM ".DATATABLE."_order_brand AS b WHERE b.CompanyID=".$_SESSION['cc']['ccompany']." ".$search2." group by b.BrandName   ORDER BY b.IsIndex DESC, total desc ";
	
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize			= $ps;
        $page->Total		    = $rs['allrow'];
        $page->LinkAry			= array("m"=>'brand', "ps"=>$ps , "kw"=>$kw);
        
        $result['total']		= $rs['allrow'];
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;
		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink($lurl);
		
		//$db->debug();
		return $result;
		unset($result);
	}

	//获取底部公共内容
	function getcommonclass()
	{
		$bdata['sort']      = self::getsortinfo();
		$bdata['binfo']		= self::getbuttoninfo();
		$bdata['tools']		= self::gettoolinfo();
		$bdata['contact']	= self::getcontactinfo();
		return $bdata;
	}

    //获取是否显示余额
    function get_is_show() {
        $db	    = dbconnect::dataconnect()->getdb();
        $cid       =  $_SESSION['cc']['ccompany'];
        $valuearr = array();
        $setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cid." and SetName='product' limit 0,1");
        if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);
        return $valuearr['show_money'] != 'off' && !in_array($cid,array(133,309));
    }

	//获取余额
    function get_amount()
    {
        $db	    = dbconnect::dataconnect()->getdb();
        $cid       =  $_SESSION['cc']['cid'];
        //收款单
        $sqlunion  = " and FinanceClient = ".$cid." ";
        $statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
        $statdata2 = $db->get_row($statsql2);
        /***
        //已经使用的余额
        $sqlunion  = " and f.FinanceClient = ".$cid." ";
        $statsql3  = "SELECT sum(f.FinanceTotal) as Ftotal from ".DATATABLE."_order_finance f inner join ".DATATABLE."_order_orderinfo o ON f.FinanceOrderID=o.OrderID where o.OrderStatus=0 and (o.OrderPayStatus=2 or o.OrderPayStatus=3) ".$sqlunion." and f.FinanceCompany=".$_SESSION['cc']['ccompany']." and f.FinanceFlag=2 and f.FinanceType='Y' ";
        $statdata3 = $db->get_row($statsql3);
         ***/
        //其他款项
        $sqlunion  = " and ClientID = ".$cid." ";
        $statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['cc']['ccompany']." ".$sqlunion." and FlagID = '2' ";
        $statdata4 = $db->get_row($statsql4);

        //没取消的订单金额
        $sqlunion  = " and OrderUserID   = ".$cid." ";
        $statsqlt  = "SELECT sum(OrderIntegral) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and OrderStatus!=8 and OrderStatus!=9 ";
        $statdatat = $db->get_row($statsqlt);
        
        //没取消但使用账期支付的订单金额
        $sqlunion  = " and OrderUserID   = ".$cid." ";
        $statsqltzq  = "SELECT sum(OrderIntegral) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and (OrderStatus!=8 and OrderStatus!=9 and OrderPayType=12) ";
        $statdatatzq = $db->get_row($statsqltzq);
        //退货金额
        $sqlunion   = " and ReturnClient  = ".$cid." ";
        $statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." ".$sqlunion." and (ReturnStatus=3 or ReturnStatus=5) ";
        $statdata1  = $db->get_row($statsqlt1);

        $begintotal = $statdata2['Ftotal'] - ($statdatat['Ftotal'] - $statdatatzq['Ftotal']) + $statdata4['Ftotal'] + $statdata1['Ftotal'];

        $begintotal = floatval($begintotal);
        $begintotal = sprintf("%.2f",round($begintotal,2));
        return $begintotal;
    }


	//设置session
	function set_session_price(){
		$db	= dbconnect::dataconnect()->getdb();
		//$upsql =  "select ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientMobile,ClientAdd,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientPay,ClientFlag from ".DATATABLE."_order_client where ClientID=".$_SESSION['cc']['cid']." and ClientCompany = ".$_SESSION['ucc']['CompanyID']." and ClientFlag=0 limit 0,1";
		$upsql =  "select ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientTrueName,ClientEmail,ClientPhone,ClientMobile,ClientAdd,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientPay,ClientFlag from ".DATATABLE."_order_client where ClientID=".$_SESSION['cc']['cid']." and ClientCompany = ".$_SESSION['ucc']['CompanyID']." limit 0,1";
		$cinfo = $db->get_row($upsql);

		$_SESSION['cc']['clevel']		= $cinfo['ClientLevel'];
		$_SESSION['cc']['csetshield']	= $cinfo['ClientShield'];
		$_SESSION['cc']['csetprice']	= $cinfo['ClientSetPrice'];
		if(empty($_SESSION['cc']['csetprice'])) $_SESSION['cc']['csetprice'] = "Price2";
		$_SESSION['cc']['csetpercent']	= $cinfo['ClientPercent'];
		if(empty($_SESSION['cc']['csetpercent'])) $_SESSION['cc']['csetpercent'] = '10.0';
		if(empty($cinfo['ClientBrandPercent'])) $_SESSION['cc']['cbrandpercent'] = '';
		if(!empty($cinfo['ClientBrandPercent'])) $_SESSION['cc']['cbrandpercent'] = unserialize($cinfo['ClientBrandPercent']);
		
		$_SESSION['cc']['cclientpay']	= $cinfo['ClientPay'];
		$_SESSION['cc']['cflag']		= $cinfo['ClientFlag'];

		if(!strpos($cinfo['ClientLevel'],",") && substr($cinfo['ClientLevel'],0,1)==="l")
		{
			$_SESSION['cc']['clevel'] = "A_".$cinfo['ClientLevel'];
		}
	}

//END
}

?>
