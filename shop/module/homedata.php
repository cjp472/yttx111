<?
class homedata
{
	
	//分类信息
	function listsite($p=0,$s="",$level=2)
	{	
		$db = dbconnect::dataconnect()->getdb();
		
		$sqlnoid = '';
		if(!empty($_SESSION['cc']['csetshield'])) $sqlnoid = " and SiteID NOT IN (".$_SESSION['cc']['csetshield'].")";
		$sql_2   = "select SiteID,ParentID,SiteName from ".DATATABLE."_order_site where ParentID=".$s." and CompanyID=".$_SESSION['cc']['ccompany']."  ".$sqlnoid."  order by SiteOrder desc,SiteID asc ";
		$result = $db->get_results($sql_2);

		//$db->debug();
		return $result;
		unset($result);
	}

	//分类信息
	function listallsite($p=0,$s="",$level=2)
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

	//特价商品列表
	function listsgoods($ty='3',$num=16,$ishow='on')
	{
		$db	     = dbconnect::dataconnect()->getdb();
		$smsgk = '';
		if(empty($ty)) $ty = 3;
		$smsg  = " and CommendID = ".$ty." ";
		if(empty($num)) $num = 16;

		$shielddata = $db->get_col("select ContentID from ".DATATABLE."_order_shield where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']);
		if(!empty($shielddata))
		{
			$shieldmsg = implode(",",$shielddata);
			$smsg  .= " and ID NOT IN ( ".$shieldmsg." ) ";
			$smsgk .= " and ID NOT IN ( ".$shieldmsg." ) ";
		}

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
			if(!empty($nsmsg))
			{
				$smsg .= " and SiteID NOT IN (".$nsmsg.")";
				$smsgk .= " and SiteID NOT IN (".$nsmsg.")";
			}
		}

		$sql_l = "select ID,BrandID,CommendID,Count,Name,Coding,Price1,Price2,Price3,Units,Casing,Picture,Color,Specification from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['cc']['ccompany']." and FlagID=0 ".$smsg." order by OrderID desc,ID desc limit 0,".$num;
		$result['list']	= $db->get_results($sql_l);

		if(empty($result['list']) && $ty == 3)
		{
			$sql_l = "select ID,BrandID,CommendID,Count,Name,Coding,Price1,Price2,Price3,Units,Casing,Picture,Color,Specification from ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['cc']['ccompany']." and Picture!='' and FlagID=0 ".$smsgk." order by OrderID desc,ID desc limit 0,".$num;		
			$result['list']	= $db->get_results($sql_l);
		}

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
		}
		if(!empty($idarr) && $ishow=="on")
		{
			$idmsg = implode(",",$idarr);
			$sqlnumber = "select ContentID,OrderNumber,ContentNumber from ".DATATABLE."_order_number where CompanyID=".$_SESSION['cc']['ccompany']." and ContentID in (".$idmsg.")";
			$numberarr	= $db->get_results($sqlnumber);
			foreach($numberarr as $nvar)
			{
				$narr[$nvar['ContentID']] = $nvar['OrderNumber'];
			}
			$result['number'] = $narr;
		}
		//$db->debug();
		return $result;
		unset($result);
	}

	//公告信息
	function getgginfo($num=10)
	{	
		$db = dbconnect::dataconnect()->getdb();		

		$sql_l  = "SELECT ArticleID,ArticleSort,ArticleTitle,ArticleColor,ArticleAuthor,ArticleDate FROM ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleSort=0 and ArticleFlag=0 order by ArticleOrder DESC, ArticleID DESC limit 0,".$num;
		$result	= $db->get_results($sql_l);
		$sql_l2  = "SELECT id,title,type FROM ".DATATABLE."_pay_notice where type=2  order by addtime DESC limit 0,".$num;
		$result2 = $db->get_results($sql_l2);
		$res=array_merge($result,$result2);
		//$db->debug();
		return $res;
		unset($res);
	}

	//广告信息
	function getxdinfo($num=5)
	{	
		$db = dbconnect::dataconnect()->getdb();		

		$sql_l  = "SELECT ArticleID,ArticleName,ArticleLink,ArticlePicture FROM ".DATATABLE."_order_xd where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleSort=1  order by ArticleOrder DESC, ArticleID DESC limit 0,".$num;
		$result	= $db->get_results($sql_l);
		//$db->debug();
		return $result;
		unset($result);
	}

//END
}
?>
