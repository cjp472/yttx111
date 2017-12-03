<?
class listcatalog
{
	//分类信息
	function getallsite()
	{	
		$db = dbconnect::dataconnect()->getdb();		

		$sql_l  = "select SiteID,ParentID,SiteOrder,SiteName,Content from ".DATATABLE."_order_site where CompanyID=".$_SESSION['cc']['ccompany']." order by ParentID asc, SiteOrder desc,SiteID asc limit 0,500";
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}	

//END
}
?>
