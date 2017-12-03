<?
class listinfo
{
	
	//分类信息
	function showsortinfo($sid)
	{	
		$db = dbconnect::dataconnect()->getdb();		
		if(empty($sid))
		{
			$sql_l  = "SELECT SortID,SortName FROM ".DATATABLE."_order_sort where SortCompany=".$_SESSION['cc']['ccompany']." order by SortOrder DESC,SortID ASC limit 0,1";
		}else{
			$sid    = intval($sid);
			$sql_l  = "SELECT SortID,SortName FROM ".DATATABLE."_order_sort where SortID=".$sid." and  SortCompany=".$_SESSION['cc']['ccompany']." order by SortOrder DESC,SortID ASC limit 0,1";
		}
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
		unset($result);
	}
	
	//
	function showinfo($sid='',$id='')
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$sid    = intval($sid);

		if(empty($id))
		{
			$sql_l  = "select * from ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleSort = {$sid} and ArticleFlag=0  order by ArticleOrder desc,ArticleID DESC limit 0,1";
		}else{
			$id    = intval($id);
			$sql_l  = "select * from ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleSort = {$sid} and ArticleID={$id}  and ArticleFlag=0  order by ArticleOrder desc,ArticleID DESC limit 0,1";
		}
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
	}
	
	//平台信息内容
	function platformshowinfo($id='')
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$id    = intval($id);
		
		$sql_l  = "select title,content from ".DATATABLE."_pay_notice where id='".$id."' order by addtime desc limit 0,1";
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result;
	}
	

	function showinfotitle($sid='',$num=10)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$sid    = intval($sid);

		$sql_l  = "select ArticleID,ArticleSort,ArticleTitle,ArticleDate from ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleSort = {$sid} and ArticleFlag=0 order by ArticleOrder desc,ArticleID DESC limit 0,".$num;
		$result	= $db->get_results($sql_l);

		//$db->debug();
		return $result;
	}

	function showinfotitlenumber($sid='')
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$sid    = intval($sid);

		$sql_l  = "select count(*) as articlerow from ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleSort = {$sid} and ArticleFlag=0  limit 0,1";
		$result	= $db->get_row($sql_l);

		//$db->debug();
		return $result['articlerow'];
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
	
	function upcount($id)
	{
		$db	    = dbconnect::dataconnect()->getdb();
		$id    = intval($id);

		$sql_l  = "update ".DATATABLE."_order_article set ArticleCount=ArticleCount+1 where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleID={$id}";
		$db->query($sql_l);
		
		return true;
	}

/******************************/

	//
	function listinfomation($sid,$totalnumber,$ps=18)
	{

		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		if(empty($ps)) $ps = 18;
		$sid    = intval($sid);

		$sql_l = "select ArticleID,ArticleSort,ArticleTitle,ArticleDate from ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['cc']['ccompany']." and ArticleSort=".$sid." and ArticleFlag=0 Order By ArticleOrder DESC, ArticleID DESC ";

		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $totalnumber;
        $page->LinkAry		= array("sid"=>$sid);
        
        $result['total']	= $totalnumber;
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;

		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink("infomation.php");

		//$db->debug();

		return $result;
		unset($result);
	}
	
	//平台公告列表
	function platformlistinfomation($ps=18)
	{

		$db	   = dbconnect::dataconnect()->getdb();
		$smsg  = "";
		if(empty($ps)) $ps = 18;

		$sql_l = "select id as ArticleID,title as ArticleTitle,addtime ArticleDate from ".DATATABLE."_pay_notice where type=2 Order By addtime DESC ";
		
		$sql_l2="select count(*) totle from ".DATATABLE."_pay_notice where type=2";
		$totle=$db->get_var($sql_l2);
		$totalnumber=$totle;
		
		$page  = new ShowPage;
        $page->PageSize		= $ps;
        $page->Total		= $totalnumber;
        $page->LinkAry		= array("ty"=>"platfrom_list");
        
        $result['total']	= $totalnumber;
		$result['pagestart']    = ($page->PageNum()-1)*$page->PageSize+1;
		$result['pageend']		= $page->PageNum()*$page->PageSize;

		$result['list']			= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']		= $page->ShowLink("infomation.php");

		//$db->debug();

		return $result;
		unset($result);
	}

/*********** help *********************/

	function showhelptitle($sid=8,$num=50,$db2)
	{
		$sid    = intval($sid);

		$sql_l  = "select ArticleID,ArticleSort,ArticleTitle from ".DATATABLE."_order_article where ArticleCompany=1 and ArticleSort = {$sid} and ArticleFlag=0 order by ArticleOrder desc,ArticleID DESC limit 0,".$num;
		$result	= $db2->get_results($sql_l);

		//$db->debug();
		return $result;
	}

	function showhelpinfo($sid=8,$id='',$db2)
	{
		$sid    = intval($sid);
		if(empty($id))
		{
			$sql_l  = "select * from ".DATATABLE."_order_article where ArticleCompany=1 and ArticleSort = {$sid} and ArticleFlag=0  order by ArticleOrder desc,ArticleID DESC limit 0,1";
		}else{
			$id    = intval($id);
			$sql_l  = "select * from ".DATATABLE."_order_article where ArticleCompany=1 and ArticleSort = {$sid} and ArticleID={$id}  and ArticleFlag=0  order by ArticleOrder desc,ArticleID DESC limit 0,1";
		}
		$result	= $db2->get_row($sql_l);

		//$db->debug();
		return $result;
	}

}
?>