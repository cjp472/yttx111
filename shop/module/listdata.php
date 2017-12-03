<?php
class listdata
{
	//获取所有分类信息
	function listallsitedata()
	{
		$db = dbconnect::dataconnect()->getdb();

		$result = $db->get_results("SELECT SiteID,ParentID,SiteNo,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." ORDER BY SiteOrder DESC,SiteID ASC ");

		return $result;
		unset($result);
	}

	//分类信息
	function getsiteinfo($s='',$num=1)
	{	
		$db = dbconnect::dataconnect()->getdb();
		$s   = intval($s);

		$sqlnoid = '';
		if(!empty($_SESSION['cc']['csetshield'])) $sqlnoid = " and SiteID NOT IN (".$_SESSION['cc']['csetshield'].")";
		if($num == 1)
		{
			$sql_l  = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where SiteID=".$s." and CompanyID=".$_SESSION['cc']['ccompany']."  limit 0,1";
			$result	= $db->get_row($sql_l);
		}else{
			$sql_l  = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where ParentID=".$s." and CompanyID=".$_SESSION['cc']['ccompany']." ".$sqlnoid." order by SiteOrder desc,SiteID asc limit 0,".$num;
			$result	= $db->get_results($sql_l);
		}
		//$db->debug();
		return $result;
		unset($result);
	}

	//当前位置信息
	function getlocationinfo($si='')
	{	
		$db = dbconnect::dataconnect()->getdb();

		$sql_l   = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where SiteID IN (".str_replace(".",",",trim($si['SiteNo'], ".")).") and CompanyID=".$_SESSION['cc']['ccompany']." Order by SiteNo asc";
		$result = $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}	

	//分类信息
	function listsite($p=0,$s="",$level=2)
	{	
		$db = dbconnect::dataconnect()->getdb();
		$s  = intval($s);

		$sqlnoid = '';
		if(!empty($_SESSION['cc']['csetshield'])) $sqlnoid = " and SiteID NOT IN (".$_SESSION['cc']['csetshield'].")";
		$sql_2   = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where ParentID=".$s." and CompanyID=".$_SESSION['cc']['ccompany']." order by SiteOrder desc,SiteID asc ";
		$result2 = $db->get_results($sql_2);

		if(empty($result2) && $p!="0")
		{
			$parr = $db->get_row("select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where SiteID=".$p." and CompanyID=".$_SESSION['cc']['ccompany']." order by SiteOrder desc,SiteID asc");

			$sql_l   = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where ParentID=".$parr['ParentID']." and CompanyID=".$_SESSION['cc']['ccompany']." ".$sqlnoid." order by SiteOrder desc,SiteID asc ";
			$result	 = $db->get_results($sql_l);
			
			$sql_2   = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where ParentID=".$p." and CompanyID=".$_SESSION['cc']['ccompany']." order by SiteOrder desc,SiteID asc ";
			$result2 = $db->get_results($sql_2);
			$s = $p;
		}else{
			$sql_l   = "select SiteID,ParentID,SiteName from ".DATATABLE."_order_site where ParentID=".$p." and CompanyID=".$_SESSION['cc']['ccompany']." ".$sqlnoid." order by SiteOrder desc,SiteID asc ";
			$result	 = $db->get_results($sql_l);
		}

		for($i=0;$i<count($result);$i++)
		{
			if($result[$i]['SiteID'] == $s) $result[$i]['son'] =  $result2;
		}

		//$db->debug();
		return $result;
		unset($result);
	}

	//两级分类信息
	function listallsite($p=0,$s="",$level=2)
	{	
		$db = dbconnect::dataconnect()->getdb();

		$sqlnoid = '';
		if(!empty($_SESSION['cc']['csetshield'])) $sqlnoid = " and SiteID NOT IN (".$_SESSION['cc']['csetshield'].")";
		$sql_2   = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." ".$sqlnoid." order by  ParentID asc, SiteOrder desc, SiteID asc ";
		$result2 = $db->get_results($sql_2);

		if(!empty($p))
		{
			$sql_1   = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and ParentID=".$s." order by  ParentID asc, SiteOrder desc, SiteID asc limit 0,1 ";
			$result1 = $db->get_row($sql_1);
		
			if(empty($result1))
			{
				$sql_0   = "select SiteID,ParentID,SiteNo,SiteName from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and SiteID=".$p." order by  ParentID asc, SiteOrder desc, SiteID asc ";
				$result0 = $db->get_row($sql_0);
				$p = $result0['ParentID'];
				$s = $result0['SiteID'];
			}
		}
		foreach($result2 as $var)
		{
			if($var['ParentID']==$p)
			{
				$result[$var['SiteID']] = $var;
			}
		}

		foreach($result2 as $var)
		{
			if($var['ParentID']!="0")
			{
				if(!empty($result[$var['ParentID']])) $result[$var['ParentID']]['son'][$var['SiteID']] = $var;
			}
		}

		//$db->debug();
		return $result;
		unset($result);
	}

	//三级分类信息
	function listallsite_3($p=0,$s="",$level=2)
	{	
		$db = dbconnect::dataconnect()->getdb();

		$sqlnoid = '';
		if(!empty($_SESSION['cc']['csetshield'])) $sqlnoid = " and SiteID NOT IN (".$_SESSION['cc']['csetshield'].")";
		$sql_2   = "select SiteID,ParentID,SiteName from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." ".$sqlnoid." order by  ParentID asc, SiteOrder desc, SiteID asc ";
		$result2 = $db->get_results($sql_2);
		//var_dump($result2);exit;

		foreach($result2 as $var)
		{
			if($var['ParentID']=="0")
			{
				$result[$var['SiteID']] = $var;
			}
		}

		foreach($result2 as $var)
		{
			if($var['ParentID']!="0")
			{
				if(!empty($result[$var['ParentID']])) $result[$var['ParentID']]['son'][$var['SiteID']] = $var;
			}
		}
		
		foreach($result as $key=>$val)
		{
			
			if(!empty($val['son']))
			{
				foreach($val['son'] as $k=>$v){
					foreach($result2 as $kk=>$vv){
						if($v['SiteID']==$vv['ParentID']){
							  $result[$key]['son'][$v['SiteID']]['son'][$vv['SiteID']] = $vv;
							}
					}
				
				}
			}
		}

		//$db->debug();
		return $result;
		unset($result);
	}

	
	//商品列表
	function listgoods($s=0,$b=0,$o=0,$t='imglist',$ps=18,$ishow='off',$lurl='list.php',$stock=0)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		if(empty($ps)) $ps = 18;

		if(empty($o) || $o=="0")
		{
			$orderbymsg = " order by i.OrderID DESC, i.ID DESC";
		}elseif($o=="1"){
			$orderbymsg = " order by i.Price2 DESC";
		}elseif($o=="2"){
			$orderbymsg = " order by i.Price2 ASC";
		}elseif($o=="3"){
			$orderbymsg = " order by i.ID ASC";
		}elseif($o=="4"){
			$orderbymsg = " order by i.Count DESC, i.ID DESC";
		}
		
		$sidsqlmsg = self::getshieldsite($s); //屏蔽分类、商品
		if($sidsqlmsg == "empty") return ''; else $smsg .= $sidsqlmsg;
		$slf = self::showproductfield(); //自定义字段显示
		if(!empty($b)) $smsg .= " and i.BrandID = ".intval($b)." ";
		if(!empty($stock)) $smsg .= " and n.OrderNumber > 0 ";
		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID LEFT JOIN ".DATATABLE."_order_number AS n ON i.ID = n.ContentID AND i.CompanyID = n.CompanyID where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg;
		$sql_l  = "select i.ID,i.CommendID,i.Count,i.Name,i.Coding,i.Barcode,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,i.Model,i.Nearvalid,i.Conversion,i.Casing,i.Farvalid,i.Appnumber,c.FieldContent,n.OrderNumber,b.BrandName,b.BrandID,(SELECT 1 FROM ".DATATABLE."_order_fav AS f WHERE f.FavCompany=i.CompanyID AND i.ID=f.FavContent AND f.FavClient=".$_SESSION['cc']['cid'].") AS fav from ".DATATABLE."_order_content_index  i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID left join ".DATATABLE."_order_brand as b on i.BrandID=b.BrandID LEFT JOIN ".DATATABLE."_order_number AS n ON i.ID = n.ContentID AND i.CompanyID = n.CompanyID  where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg." ".$orderbymsg;
		//echo $sql_l;exit;
		//$r=$db->get_results($sql_l);
		//$rr=count($r);
		//var_dump($rr);exit;
		//$result['sql'] = $sql_l;
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize			= $ps;
        $page->Total		    = $rs['allrow'];
        $page->LinkAry			= array("s"=>$s,"b"=>$b,"t"=>$t,"o"=>$o,"ps"=>$ps,"stock"=>$stock);
        
        $result['total']		= $rs['allrow'];
        $result['totalpage']	= ceil($page->Total / $page->PageSize);
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;
		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink($lurl);
		
		$result['prepage']		= $page->PrePage($lurl);
		$result['nextpage']		= $page->NextPage($lurl);
		//$result['sql'] = $sql_l;
		$stock_arr=array();
		for($i=0;$i<count($result['list']);$i++)
		{
			if(!empty($result['list'][$i]['Picture']) && $_SESSION['cc']['ccompany'] == "1" && substr($result['list'][$i]['Picture'],0,1)!="1") $result['list'][$i]['Picture'] = "1/".$result['list'][$i]['Picture'];
			//$idarr[] = $result['list'][$i]['ID'];
			//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
// 			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']];
			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;
			$result['list'][$i]['Price'] = sprintf('%.2f', $result['list'][$i]['Price']);
			$price3 = commondata::setprice3($result['list'][$i]['Price3']);
			if(!empty($price3)) $result['list'][$i]['Price'] = sprintf('%.2f', $price3);
			//计算毛利率
			$result['list'][$i]['maoli'] = (($result['list'][$i]['Price2']-$result['list'][$i]['Price'])/$result['list'][$i]['Price2'])*100;
			$result['list'][$i]['maoli'] = sprintf('%.2f', $result['list'][$i]['maoli']);
			
			if(!empty($result['list'][$i]['Color']) || !empty($result['list'][$i]['Specification']))
			{
				$result['list'][$i]['cs'] = "Y";
			}else{
				$result['list'][$i]['cs'] = "N";
			}
			if(!empty($slf) && !empty($result['list'][$i]['FieldContent']))
			{
				$cvarr = unserialize($result['list'][$i]['FieldContent']);
				if(!empty($cvarr[$slf[0]['field']]))
				{
					$result['list'][$i]['ShowField'][0]['name']  =  $slf[0]['name'];
					$result['list'][$i]['ShowField'][0]['value']   =  $cvarr[$slf[0]['field']];
				}
				if(!empty($cvarr[$slf[1]['field']]))
				{
					$result['list'][$i]['ShowField'][1]['name']  =  $slf[1]['name'];
					$result['list'][$i]['ShowField'][1]['value']   =  $cvarr[$slf[1]['field']];
				}
			}
			
			$stock_arr[$result['list'][$i]['ID']]=($result['list'][$i]['OrderNumber'] <= 0)? 0 : $result['list'][$i]['OrderNumber'];
			
		}
		//if(!empty($idarr)) $idmsg = implode(",",$idarr);		
		if(!empty($stock_arr) && $ishow=="on") $result['number'] = $stock_arr; //库存
		//var_dump($result['number']);exit;
		//$db->debug();
		return $result;
		unset($result);
	}	


	//特价商品列表
	function listsgoods($o=0,$t='imglist',$ty='2',$ps=18,$ishow='off',$lurl='list.php',$b=0)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		if(empty($ty)) $ty = 2;
		$smsg  = " and i.CommendID = ".$ty." ";
		if(empty($ps)) $ps = 18;

		if(empty($o) || $o=="0")
		{
			$orderbymsg = " order by i.OrderID DESC, i.ID DESC";
		}elseif($o=="1"){
			$orderbymsg = " order by i.Price2 DESC";
		}elseif($o=="2"){
			$orderbymsg = " order by i.Price2 ASC";
		}elseif($o=="3"){
			$orderbymsg = " order by i.ID ASC";
		}elseif($o=="4"){
			$orderbymsg = " order by i.Count DESC, i.ID DESC";
		}

		$sidsqlmsg = self::getshieldsite(0); //屏蔽分类、商品
		if($sidsqlmsg == "empty") return ''; else $smsg .= $sidsqlmsg;
		$slf = self::showproductfield(); //自定义字段显示
		if(!empty($b)) $smsg .= " and i.BrandID = ".intval($b)." ";

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg;
		$sql_l  = "select i.ID,i.CommendID,i.Count,i.Name,i.Coding,i.Barcode,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,i.Model,i.Nearvalid,i.Conversion,i.Casing,i.Farvalid,i.Appnumber,c.FieldContent,b.BrandName from ".DATATABLE."_order_content_index  i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID left join ".DATATABLE."_order_brand as b on i.BrandID=b.BrandID where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg." ".$orderbymsg;
		
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("m"=>"spc","ty"=>$ty, "t"=>$t,"o"=>$o,"ps"=>$ps);
        
        $result['total']		= $rs['allrow'];
        $result['totalpage']	= ceil($page->Total / $page->PageSize);
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;
		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink($lurl);
		
		$result['prepage']		= $page->PrePage($lurl);
		$result['nextpage']		= $page->NextPage($lurl);

		for($i=0;$i<count($result['list']);$i++)
		{
			$idarr[] = $result['list'][$i]['ID'];
			//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
// 			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']];
			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;
			$price3 = commondata::setprice3($result['list'][$i]['Price3']);
			if(!empty($price3)) $result['list'][$i]['Price'] = $price3;
			if(!empty($result['list'][$i]['Color']) || !empty($result['list'][$i]['Specification']))
			{
				$result['list'][$i]['cs'] = "Y";
			}else{
				$result['list'][$i]['cs'] = "N";
			}
			if(!empty($slf) && !empty($result['list'][$i]['FieldContent']))
			{
				$cvarr = unserialize($result['list'][$i]['FieldContent']);
				if(!empty($cvarr[$slf[0]['field']]))
				{
					$result['list'][$i]['ShowField'][0]['name']  =  $slf[0]['name'];
					$result['list'][$i]['ShowField'][0]['value']   =  $cvarr[$slf[0]['field']];
				}
				if(!empty($cvarr[$slf[1]['field']]))
				{
					$result['list'][$i]['ShowField'][1]['name']  =  $slf[1]['name'];
					$result['list'][$i]['ShowField'][1]['value']   =  $cvarr[$slf[1]['field']];
				}
			}
		}
		$idmsg = implode(",",$idarr);
		if(!empty($idarr) && $ishow=="on") $result['number'] = self::getordernumber($idmsg); //库存

		//$db->debug();
		return $result;
		unset($result);
	}

	//记数
	function upcount($id)
	{
		$db	    = dbconnect::dataconnect()->getdb();

		$sql_l  = "update ".DATATABLE."_order_content_index set Count=Count+1 where ID={$id}";
		$db->query($sql_l);
		
		return true;
	}


	//商品详细
	function listcontent($id='')
	{
		$db   = dbconnect::dataconnect()->getdb();
		$id   = intval($id);
		$sortidarr = null;

		//屏蔽ID
		$shielddata = $db->get_row("select count(*) as crow from ".DATATABLE."_order_shield where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." and ContentID=".$id);
		if(!empty($shielddata['crow']))
		{
			return null;
		}

		//屏蔽分类
		$sidsqlmsg = '';
		if(!empty($_SESSION['cc']['csetshield']))
		{
			$nsmsg = '';
			$nomsg = '';
			$sitepdata = $db->get_col("select SiteNO from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and (SiteID IN (".$_SESSION['cc']['csetshield'].") )");
			if(!empty($sitepdata))
			{
				foreach($sitepdata as $var)
				{
					if(empty($nomsg)) $nomsg = " SiteNO LIKE '".$var."%' "; else $nomsg .= " or SiteNO LIKE '".$var."%' ";
				}
				$sitesdata2 = $db->get_col("select SiteID from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and (".$nomsg.")");
				if(!empty($sitesdata2))  $sortidarr = $sitesdata2;
			}
		}

		$sql_l   = "select i.*,b.BrandName from ".DATATABLE."_order_content_index as i left join ".DATATABLE."_order_brand as b on i.BrandID=b.BrandID where i.ID=".$id." and i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 limit 0,1";
		$result['index']	 = $db->get_row($sql_l);

		if(empty($result['index']) || @in_array($result['index']['SiteID'],$sortidarr)) return '';

		$sql_c   = "select * from ".DATATABLE."_order_content_1 where ContentIndexID = ".$result['index']['ID']." and CompanyID=".$_SESSION['cc']['ccompany']." limit 0,1";
		$result['content'] = $db->get_row($sql_c);
		
		//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
		// 			$result['index']['Price'] = $result['index'][$_SESSION['cc']['csetprice']];
 		$result['index']['Price'] = $result['index'][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;

		$price3 = commondata::setprice3($result['index']['Price3']);
		if(!empty($price3)) $result['index']['Price'] = $price3;
		
		//计算毛利率
		$result['index']['maoli'] = (($result['index']['Price2']-$result['index']['Price'])/$result['index']['Price2'])*100;
		$result['index']['maoli'] = sprintf('%.2f', $result['index']['maoli']);

		if(!empty($result['index']['Picture']))
		{
			$result['index']['PictureBig'] = str_replace("thumb_","img_",$result['index']['Picture']);
			list($width, $height) = @getimagesize(RESOURCE_PATH.$result['index']['PictureBig']);
			$result['index']['PictureWH'] = '';
			if($width > 400 || $height > 360)
			{
				if($width/$height > 400/360)
				{
					$result['index']['PictureWH'] = 'width="400"';
				}else{
					$result['index']['PictureWH'] = 'height="360"';
				}
			}
		}

		$sql_r   = "select Name,Path from ".DATATABLE."_order_resource where CompanyID=".$_SESSION['cc']['ccompany']." and IndexID = ".$result['index']['ID']." order by OrderID asc";
		$pdata = $db->get_results($sql_r);
		if(!empty($pdata))
		{
			$result['content']['PicArray'] = $pdata;
			$result['index']['PictureBig']  = $pdata[0]['Path']."img_".$pdata[0]['Name'];
		}

		if(!empty($result['index']['ContentTags']))
		{
			$result['index']['TagArray']   =  @explode(" ", $result['index']['ContentTags']);
		}
		if(!empty($result['index']['Color']))
		{
			$result['index']['ColorArray']  =  @explode(",", $result['index']['Color']);
		}		
		if(!empty($result['index']['Specification']))
		{
			$result['index']['SpecificationArray']  =  @explode(",", $result['index']['Specification']);
		}

		$setarr = commondata::getproductset();
		$result['library']['pn']  = $setarr['product_number'];
		$result['library']['png']  = $setarr['product_negative'];
		$result['library']['pns'] = $setarr['product_number_show'];
		if(empty($result['library']['pn'])) $result['library']['pn']  = "off";
		if(empty($result['library']['png'])) $result['library']['png']  = "off";
		if(empty($result['library']['pns']))  $result['library']['pns'] = "off";

		if($result['library']['pn'] == "on" || $result['library']['pns'] == "on")
		{
			$sqlnumber = "select ContentID,OrderNumber,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID = ".$id."";
			$numberarr	= $db->get_row($sqlnumber);
			if(empty($numberarr['OrderNumber']) || $numberarr['OrderNumber'] <0 ) $numberarr['OrderNumber'] = 0;
			$result['number'] = $numberarr;
		}


		return $result;
		unset($result);
	}

	function listsitelink($id, $site=0, $Num=8 )
	{
		$db	  = dbconnect::dataconnect()->getdb();
		if(empty($Num))	$Num = 8;
		$shielddata = null;
		$shielddata = $db->get_col("select ContentID from ".DATATABLE."_order_shield where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']);
		if(!empty($shielddata))
		{
			$shieldmsg = implode(",",$shielddata);
			$smsg1  = " and ID NOT IN ( ".$shieldmsg." ) ";
		}
		$sql_l   = "SELECT ID,Name,Price1,Price2,Price3,Picture FROM ".DATATABLE."_order_content_index  WHERE SiteID = ".$site." and CompanyID=".$_SESSION['cc']['ccompany']." and FlagID=0 ORDER BY OrderID DESC, ID DESC LIMIT 0,".$Num;
		$resulti = $db->get_results($sql_l);

		for($i=0;$i<count($resulti);$i++)
		{
			$resulti[$i]['Price'] = $resulti[$i][$_SESSION['cc']['csetprice']];
			$price3 = commondata::setprice3($resulti[$i]['Price3']);
			if(!empty($price3)) $resulti[$i]['Price'] = $price3;
		}		
		
		return($resulti);
		unset($resulti);		
	}

	function listlink($id, $TagMsg='',$site=0, $Num=8 )
	{		
		$db	  = dbconnect::dataconnect()->getdb();
		$id   = intval($id);
		$smsg = "";
		$smsg1 = "";
		if(empty($Num))	$Num = 8;
		$resultl = null;	
		$shielddata = null;
		$shielddata = $db->get_col("select ContentID from ".DATATABLE."_order_shield where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']);
		if(!empty($shielddata))
		{
			$shieldmsg = implode(",",$shielddata);
			$smsg1  = " and ID NOT IN ( ".$shieldmsg." ) ";
		}

		//屏蔽分类
		$sidsqlmsg = '';
		if(!empty($_SESSION['cc']['csetshield']))
		{
			$nsmsg = '';
			$nomsg = '';
			$sitepdata = $db->get_col("select SiteNO from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and (SiteID IN (".$_SESSION['cc']['csetshield'].") )");
			if(!empty($sitepdata))
			{
				foreach($sitepdata as $var)
				{
					if(empty($nomsg)) $nomsg = " SiteNO LIKE '".$var."%' "; else $nomsg .= " or SiteNO LIKE '".$var."%' ";
				}
				$sitesdata2 = $db->get_col("select SiteID from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and (".$nomsg.")");
				if(!empty($sitesdata2))  $nsmsg = implode(",",$sitesdata2);
			}
			if(!empty($nsmsg)) $smsg = " and SiteID NOT IN (".$nsmsg.")";
		}

		if(!empty($TagMsg))
		{
			$st_msg = "";		
			
			$CTag = explode(" ", $TagMsg);
			if(@is_array($CTag))
			{
				$tc = 0;
				foreach ($CTag as $tagname_s)
				{
					if($tc == 0){
						$st_msg  = " Name LIKE '%".$tagname_s."%' ";
					}else{
						$st_msg .= " OR Name LIKE '%".$tagname_s."%' ";
					}
					$tc++;
					if($tc>3) break;
				}
				if(!empty($st_msg)) $st_msg = " AND ( ".$st_msg." ) ";
				$st_msg .= " and ID!=".$id." ";
			}

			$sql_l   = "SELECT ID,Name,Price1,Price2,Price3,Picture FROM ".DATATABLE."_order_content_index  WHERE  CompanyID=".$_SESSION['cc']['ccompany']."  ".$st_msg." ".$smsg1." ".$smsg." and FlagID=0 ORDER BY OrderID DESC, ID DESC LIMIT 0,".$Num;
			$resulti = $db->get_results($sql_l);

		}else{				
			if(@in_array($site,$sitepdata)) return "";
			$isql = "SELECT ID,Name,Price1,Price2,Price3,Picture FROM ".DATATABLE."_order_content_index WHERE SiteID = ".$site."  and CompanyID=".$_SESSION['cc']['ccompany']." and ID!=".$id." ".$smsg1." and FlagID=0 ORDER BY OrderID DESC, ID DESC LIMIT 0,".$Num;
			$resulti = $db->get_results($isql);
		}

		for($i=0;$i<count($resulti);$i++)
		{
			$resulti[$i]['Price'] = $resulti[$i][$_SESSION['cc']['csetprice']];
			$price3 = commondata::setprice3($resulti[$i]['Price3']);
			if(!empty($price3)) $resulti[$i]['Price'] = $price3;
		}

		return($resulti);
		unset($resulti);	
	}


	//商品搜索
	function listsearch($kw='',$o='',$t='imglist',$ps=18,$ishow='off',$action="")
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";	

		$kw = urldecode($kw);
		$kw = str_replace("%", "", $kw);
		
		if(empty($kw))  return null;

		if(empty($ps)) $ps = 18;

		if(empty($o) || $o=="0")
		{
			$orderbymsg = " order by i.OrderID DESC, i.ID DESC";
		}elseif($o=="1"){
			$orderbymsg = " order by i.Price2 DESC";
		}elseif($o=="2"){
			$orderbymsg = " order by i.Price2 ASC";
		}elseif($o=="3"){
			$orderbymsg = " order by i.ID ASC";
		}elseif($o=="4"){
			$orderbymsg = " order by i.Count DESC, i.ID DESC";
		}
		$kwn = str_replace(' ','%',$kw);
		
		if($action == 'vague'){
			$smsg  .= "  and (i.Name = '".$kwn."') ";
		}else{
			if(strpos($kwn,'%'))
			{
				$temsql = array();
				$kwnarr = explode('%',$kwn);
				foreach($kwnarr as $v)
				{
					$temsql[] = " i.Name like '%".$v."%' "; 
				}
				$smsg  .= " AND ((".implode(" AND ",$temsql).") OR (b.BrandName like '%".$kwn."%' OR c.ContentKeywords like '%".$kwn."%' OR i.Pinyi like '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%'))";
			}else{
				$smsg  .= " and (i.Name like '%".$kwn."%' OR b.BrandName like '%".$kwn."%' OR c.ContentKeywords like '%".$kwn."%' OR i.Pinyi like  '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%') ";
			}
		}

		$sidsqlmsg = self::getshieldsite(0); //屏蔽分类、商品
		if($sidsqlmsg == "empty") return ''; else $smsg .= $sidsqlmsg;
		$slf = self::showproductfield(); //自定义字段显示

		$sql_c = "select count(*) as allrow from 
		    ".DATATABLE."_order_content_index i 
		        INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID
		             LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID 
		             LEFT JOIN ".DATATABLE."_order_brand b ON i.BrandID=b.BrandID 
		                 where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg;
		$sql_l  = "select i.ID,i.CommendID,i.Count,i.Name,i.Coding,i.Barcode,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,i.Model,i.Conversion,i.Casing,i.Nearvalid,i.Farvalid,i.Appnumber,c.FieldContent,b.BrandName 
		          from ".DATATABLE."_order_content_index  i 
		          INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID 
	              LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID
	              LEFT JOIN ".DATATABLE."_order_brand b ON i.BrandID=b.BrandID 
		          where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg." ".$orderbymsg;

		$rs       = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("kw"=>urlencode($kw),"o"=>$o,"ps"=>$ps,"t"=>$t);
        
        $result['total']	    = $rs['allrow'];
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;

		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink("search.php");
		
		for($i=0;$i<count($result['list']);$i++)
		{
			$idarr[] = $result['list'][$i]['ID'];
			//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
// 			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']];
			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;
			$price3 = commondata::setprice3($result['list'][$i]['Price3']);
			if(!empty($price3)) $result['list'][$i]['Price'] = $price3;

			if(!empty($result['list'][$i]['Color']) || !empty($result['list'][$i]['Specification']))
			{
				$result['list'][$i]['cs'] = "Y";
			}else{
				$result['list'][$i]['cs'] = "N";
			}
			if(!empty($slf) && !empty($result['list'][$i]['FieldContent']))
			{
				$cvarr = unserialize($result['list'][$i]['FieldContent']);
				if(!empty($cvarr[$slf[0]['field']]))
				{
					$result['list'][$i]['ShowField'][0]['name']  =  $slf[0]['name'];
					$result['list'][$i]['ShowField'][0]['value']   =  $cvarr[$slf[0]['field']];
				}
				if(!empty($cvarr[$slf[1]['field']]))
				{
					$result['list'][$i]['ShowField'][1]['name']  =  $slf[1]['name'];
					$result['list'][$i]['ShowField'][1]['value']   =  $cvarr[$slf[1]['field']];
				}
			}
		}
		if(!empty($idarr)) $idmsg = implode(",",$idarr);
		if(!empty($idarr) && $ishow=="on") $result['number'] = self::getordernumber($idmsg); //库存
		//$db->debug();
		return $result;
		unset($result);
	}
	
	//商品搜索
	function list_vague_search($kw='',$o='',$t='imglist',$ps=18,$ishow='off')
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";	

		$kw = urldecode($kw);
		$kw = str_replace("%", "", $kw);
		
		if(empty($kw))  return null;

		if(empty($ps)) $ps = 18;

		if(empty($o) || $o=="0")
		{
			$orderbymsg = " order by i.OrderID DESC, i.ID DESC";
		}elseif($o=="1"){
			$orderbymsg = " order by i.Price2 DESC";
		}elseif($o=="2"){
			$orderbymsg = " order by i.Price2 ASC";
		}elseif($o=="3"){
			$orderbymsg = " order by i.ID ASC";
		}elseif($o=="4"){
			$orderbymsg = " order by i.Count DESC, i.ID DESC";
		}
		$kwn = str_replace(' ','%',$kw);
		if(strpos($kwn,'%'))
		{
			$temsql = array();
			$kwnarr = explode('%',$kwn);
			foreach($kwnarr as $v)
			{
				$temsql[] = " i.Name like '%".$v."%' "; 
			}
			$smsg  .= " AND ((".implode(" AND ",$temsql).") OR (b.BrandName like '%".$kwn."%' OR c.ContentKeywords like '%".$kwn."%' OR i.Pinyi like '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%'))";
		}else{
			$smsg  .= " and (i.Name like '%".$kwn."%' OR b.BrandName like '%".$kwn."%' OR c.ContentKeywords like '%".$kwn."%' OR i.Pinyi like  '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%') ";
		}

		$sidsqlmsg = self::getshieldsite(0); //屏蔽分类、商品
		if($sidsqlmsg == "empty") return ''; else $smsg .= $sidsqlmsg;
		$slf = self::showproductfield(); //自定义字段显示

		$sql_c = "select count(*) as allrow from 
		    ".DATATABLE."_order_content_index i 
		        INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID
		             LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID 
		             LEFT JOIN ".DATATABLE."_order_brand b ON i.BrandID=b.BrandID 
		                 where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 group by i.Name ".$smsg;
		$sql_l  = "select i.ID,i.CommendID,i.Count,i.Name,count(i.Name) as c_num,i.Coding,i.Barcode,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,i.Model,i.Nearvalid,i.Farvalid,i.Appnumber,i.Conversion,i.Casing,c.FieldContent,b.BrandName 
		          from ".DATATABLE."_order_content_index  i 
		          INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID 
	              LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID
	              LEFT JOIN ".DATATABLE."_order_brand b ON i.BrandID=b.BrandID 
		          where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg." group by i.Name  ".$orderbymsg;

		$rs       = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("kw"=>urlencode($kw),"o"=>$o,"ps"=>$ps,"t"=>$t);
        
        $result['total']	    = $rs['allrow'];
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;

		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink("search.php");
		
		for($i=0;$i<count($result['list']);$i++)
		{
			$idarr[] = $result['list'][$i]['ID'];
			//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
// 			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']];
			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;
			$price3 = commondata::setprice3($result['list'][$i]['Price3']);
			if(!empty($price3)) $result['list'][$i]['Price'] = $price3;

			if(!empty($result['list'][$i]['Color']) || !empty($result['list'][$i]['Specification']))
			{
				$result['list'][$i]['cs'] = "Y";
			}else{
				$result['list'][$i]['cs'] = "N";
			}
			if(!empty($slf) && !empty($result['list'][$i]['FieldContent']))
			{
				$cvarr = unserialize($result['list'][$i]['FieldContent']);
				if(!empty($cvarr[$slf[0]['field']]))
				{
					$result['list'][$i]['ShowField'][0]['name']  =  $slf[0]['name'];
					$result['list'][$i]['ShowField'][0]['value']   =  $cvarr[$slf[0]['field']];
				}
				if(!empty($cvarr[$slf[1]['field']]))
				{
					$result['list'][$i]['ShowField'][1]['name']  =  $slf[1]['name'];
					$result['list'][$i]['ShowField'][1]['value']   =  $cvarr[$slf[1]['field']];
				}
			}
		}
		if(!empty($idarr)) $idmsg = implode(",",$idarr);
		if(!empty($idarr) && $ishow=="on") $result['number'] = self::getordernumber($idmsg); //库存
		//$db->debug();
		return $result;
		unset($result);
	}
	
	//获取是否已收藏
	function getfav($gid){
		
		$db	   = dbconnect::dataconnect()->getdb();
		
		$sql = "select count(*) as total from ".DATATABLE."_order_fav where FavCompany=".$_SESSION['cc']['ccompany']." and FavClient=".$_SESSION['cc']['cid']." and FavContent=".(int)$gid;
		return $db->get_var($sql);
	}


	//商品列表-快速下单
	function listsearchgoods($s=0,$kw,$ps=18,$ishow='off',$lurl='list.php',$m='select')
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		if(empty($kw)) $kw = '';
		$kw = urldecode($kw);
		$kw = str_replace(" ", "%", $kw);
		if(empty($ps)) $ps = 18;
		$orderbymsg = " order by i.OrderID DESC, i.ID DESC";

		$sidsqlmsg = self::getshieldsite($s); //屏蔽分类、商品
		if($sidsqlmsg == "empty") return ''; else $smsg .= $sidsqlmsg;
		$slf = self::showproductfield(); //自定义字段显示

		// 2015/11/06 小牛New 移植过来
		$kwn = str_replace(' ','%',$kw);
		if(strpos($kwn,'%'))
		{
			$temsql = array();
			$kwnarr = explode('%',$kwn);
			foreach($kwnarr as $v)
			{
				$temsql[] = " i.Name like '%".$v."%' "; 
			}
			$smsg  .= " AND ((".implode(" AND ",$temsql).") OR (c.ContentKeywords like '%".$kwn."%' OR i.Pinyi like '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%'))";
		}else{
			$smsg  .= " and (i.Name like '%".$kwn."%' OR c.ContentKeywords like '%".$kwn."%' OR i.Pinyi like  '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%') ";
		}

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_content_index i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg;
		$sql_l  = "select i.ID,i.CommendID,i.BrandID,i.Count,i.Name,i.Coding,i.Barcode,i.Model,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,i.Nearvalid,i.Farvalid,i.Appnumber,b.BrandName from ".DATATABLE."_order_content_index  i INNER JOIN ".DATATABLE."_order_site s ON i.SiteID=s.SiteID LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID left join ".DATATABLE."_order_brand as b on i.BrandID=b.BrandID where i.CompanyID=".$_SESSION['cc']['ccompany']." and i.FlagID=0 ".$smsg." ".$orderbymsg;
		
		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("s"=>$s,"m"=>$m,"kw"=>$kw,"ps"=>$ps);
        
        $result['total']		= $rs['allrow'];
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;
		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink($lurl);

		//获取品牌
		$brandarr = commondata::getbrandinfo(0,10000);
		foreach ($brandarr as $val){
		    $branddata[$val['BrandID']] = $val;
		}
		
		for($i=0;$i<count($result['list']);$i++)
		{
			$idarr[] = $result['list'][$i]['ID'];
			$result['list'][$i]['NO'] = $i+$result['pagestart'];
			//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
// 			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']];
			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;
			$price3 = commondata::setprice3($result['list'][$i]['Price3']);
			if(!empty($price3)) $result['list'][$i]['Price'] = $price3;

			if(!empty($result['list'][$i]['Color']) || !empty($result['list'][$i]['Specification']))
			{
				$result['list'][$i]['cs'] = "Y";
			}else{
				$result['list'][$i]['cs'] = "N";
			}
			
			//匹配品牌名
			$result['list'][$i]['BrandName'] = $branddata[$result['list'][$i]['BrandID']]['BrandName'];
		}
		if(!empty($idarr)) $idmsg = implode(",",$idarr);
		if(!empty($idarr) && $ishow=="on") $result['number'] = self::getordernumber($idmsg); //库存

		//$db->debug();
		return $result;
		unset($result);
	}

	//添加到收藏
	function addtowishlist($pid)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$pid  = intval($pid);

		$sql_l = "insert into ".DATATABLE."_order_fav(FavCompany,FavClient,FavContent) values(".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",".$pid.")";
		if($db->query($sql_l))
		{
			return true;
		}
		return false;
	}

	//移除收藏
	function removetowishlist($pid)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$pid    = intval($pid);

		$sql_l  = "delete from ".DATATABLE."_order_fav where FavContent = ".$pid." and FavClient = ".$_SESSION['cc']['cid']." and FavCompany=".$_SESSION['cc']['ccompany']."";

		if($db->query($sql_l))
		{
			return true;
		}
		return false;
	}

	//收藏列表
	function wishlist($o='',$t='imglist',$ps=18,$ishow='off')
	{
		$db	    = dbconnect::dataconnect()->getdb();
		
		if(empty($ps)) $ps = 18;
		if(empty($o))
		{
			$orderbymsg = " order by f.FavID DESC";
		}elseif($o=="1"){
			$orderbymsg = " order by i.Price2 DESC";
		}elseif($o=="2"){
			$orderbymsg = "order by i.Price2 ASC";
		}elseif($o=="3"){
			$orderbymsg = " order by i.ID DESC";
		}elseif($o=="4"){
			$orderbymsg = " order by f.FavID ASC";
		}
		$slf = self::showproductfield(); //自定义字段显示

		$sql_c  = "select count(*) as allrow from ".DATATABLE."_order_fav where FavCompany=".$_SESSION['cc']['ccompany']." and FavClient=".$_SESSION['cc']['cid'];
		$sql_l  = "select f.*,i.ID,i.Name,i.Coding,i.Barcode,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,i.Model,c.FieldContent,b.BrandName from ".DATATABLE."_order_fav f LEFT JOIN ".DATATABLE."_order_content_index i ON f.FavContent=i.ID LEFT JOIN ".DATATABLE."_order_content_1 c ON i.ID=c.ContentIndexID left join ".DATATABLE."_order_brand as b on i.BrandID=b.BrandID where f.FavClient = ".$_SESSION['cc']['cid']." and i.CompanyID=".$_SESSION['cc']['ccompany']." ".$orderbymsg." ";

		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $rs['allrow'];
        $page->LinkAry		= array("t"=>$t,"o"=>$o, "ps"=>$ps);
        
        $result['total']	= $rs['allrow'];
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;

		$result['list']			    = $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink("wishlist.php");

		for($i=0;$i<count($result['list']);$i++)
		{
			$idarr[] = $result['list'][$i]['ID'];
			//toding  by  @author zhoujunbo  @date: 20161114 首页价格显示 折扣后的价格
// 			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']];
			$result['list'][$i]['Price'] = $result['list'][$i][$_SESSION['cc']['csetprice']]*$_SESSION['cc']['csetpercent']/10;
			$price3 = commondata::setprice3($result['list'][$i]['Price3']);
			if(!empty($price3)) $result['list'][$i]['Price'] = $price3;

			if(!empty($result['list'][$i]['Color']) || !empty($result['list'][$i]['Specification']))
			{
				$result['list'][$i]['cs'] = "Y";
			}else{
				$result['list'][$i]['cs'] = "N";
			}
			if(!empty($slf) && !empty($result['list'][$i]['FieldContent']))
			{
				$cvarr = unserialize($result['list'][$i]['FieldContent']);
				if(!empty($cvarr[$slf[0]['field']]))
				{
					$result['list'][$i]['ShowField'][0]['name']  =  $slf[0]['name'];
					$result['list'][$i]['ShowField'][0]['value']   =  $cvarr[$slf[0]['field']];
				}
				if(!empty($cvarr[$slf[1]['field']]))
				{
					$result['list'][$i]['ShowField'][1]['name']  =  $slf[1]['name'];
					$result['list'][$i]['ShowField'][1]['value']   =  $cvarr[$slf[1]['field']];
				}
			}
		}
		$idmsg = implode(",",$idarr);
		if(!empty($idarr) && $ishow=="on") $result['number'] = self::getordernumber($idmsg); //库存

		//$db->debug();
		return $result;
	}

	//自定义字段显示
	function showproductfield()
	{
		$slf = null;
		$setfieldarr   = commondata::getproductset('field');
		if(!empty($setfieldarr))
		{
			$m = 0;
			foreach($setfieldarr as $k=>$v)
			{
				if($v['check']=="1")
				{
					if($m>1) break;
					$slf[$m]['name'] = $v['name']; 
					$slf[$m]['field']   = $k;
					$m++;
				}
			}
		}
		return $slf;
	}

	//屏蔽分类
	function getshieldsite($siteid)
	{
		$db = dbconnect::dataconnect()->getdb();
		$smsg = '';
		$sidsqlmsg = '';

		if(!empty($_SESSION['cc']['csetshield']))
		{
			$nsmsg = '';
			$nomsg = '';
			$sitepdata = $db->get_col("select SiteNO from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and (SiteID IN (".$_SESSION['cc']['csetshield'].") )");
			if(!empty($sitepdata))
			{
				foreach($sitepdata as $var)
				{
					if(empty($nomsg)) $nomsg = " SiteNO LIKE '".$var."%' "; else $nomsg .= " or SiteNO LIKE '".$var."%' ";
				}
				$sitesdata2 = $db->get_col("select SiteID from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and (".$nomsg.")");
				if(!empty($sitesdata2))  $nsmsg = ",".implode(",",$sitesdata2).",";
			}
			if(!empty($nsmsg)) $sidsqlmsg = " and instr('".$nsmsg."', CONCAT(',', i.SiteID, ',') ) = 0 ";  //$sidsqlmsg = " and i.SiteID NOT IN (".$nsmsg.")"; 
		}

		if(!empty($siteid))
		{
			if(!empty($nsmsg))
			{
				$notinsarr = explode(",",$nsmsg);
				if(in_array($siteid,$notinsarr))
				{
					return 'empty';
				}
			}
			$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." and SiteID=".$siteid." limit 0,1");
			//$smsg  = " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";	
			$smsg  = " and instr(s.SiteNO,'".$sortinfo['SiteNO']."') > 0 ";
		}else{
			$smsg = $sidsqlmsg;
		}

		//屏蔽商品
		$shielddata = $db->get_col("select ContentID from ".DATATABLE."_order_shield where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']);
		if(!empty($shielddata))
		{
			$shieldmsg = ",".implode(",",$shielddata).",";
			//$smsg  .= " and i.ID NOT IN ( select ContentID from ".DATATABLE."_order_shield where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." ) ";
			$smsg .= " and instr('".$shieldmsg."', CONCAT(',', i.ID, ',') ) = 0";
		}
		return $smsg;
	}

	//库存
	function getordernumber($idmsg)
	{
		$db = dbconnect::dataconnect()->getdb();
		$narr  = null;
		$sqlnumber = "select ContentID,OrderNumber,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID in (".$idmsg.")";
		$numberarr	= $db->get_results($sqlnumber);
		if(!empty($numberarr))
		{
			foreach($numberarr as $nvar)
			{
				$narr[$nvar['ContentID']] = ($nvar['OrderNumber'] < 0) ? 0 : $nvar['OrderNumber'] ;
			}
		}
		return $narr;
	}


	//分类select
	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠";
		$selectmsg = "";
		
		if($var['ParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-1);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." > ".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= $this->ShowTreeMenu($resultdata,$var['SiteID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}

	//关联商品
	function getrelation($id)
	{
		$db = dbconnect::dataconnect()->getdb();
		$narr  = null;
		$sql = "select ID,Name from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['cc']['ccompany']." AND FlagID=0  AND ID IN(select FID FROM ".DATATABLE."_order_relation where CompanyID=".$_SESSION['cc']['ccompany']." and SID=".$id." union select SID FROM ".DATATABLE."_order_relation where CompanyID=".$_SESSION['cc']['ccompany']." and FID=".$id.")";
		$narr	= $db->get_results($sql);

		return $narr;
	}	
	
//END
}
?>