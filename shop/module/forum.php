<?php
class forum
{

	//取回复
	function listpost($pid='')
	{
		$db	    = dbconnect::dataconnect()->getdb();
		
		if(empty($pid))
		{
			return null;
		}else{
			$pid    = intval($pid);
			$sql_l  = "select ID,Name,Title,Content,Date from ".DATATABLE."_order_forum where PID = {$pid} and CompanyID=".$_SESSION['cc']['ccompany']." order by ID ASC limit 0,50";
			$result	= $db->get_results($sql_l);
		}	
		//$db->debug();
		return $result;
	}
	
	//取主题
	function listfirstpost($id='')
	{
		$db	    = dbconnect::dataconnect()->getdb();

		if(empty($id))
		{
			return null;
		}else{
			$id    = intval($id);
			$sql_l  = "select ID,Name,Title,Content,Date from ".DATATABLE."_order_forum where ID = {$id} and PID=0 and CompanyID=".$_SESSION['cc']['ccompany']." order by ID ASC limit 0,1";
			$result	= $db->get_row($sql_l);
		}	
		//$db->debug();
		return $result;
	}
	


	//列表
	function listforum($ty='')
	{
		$db	   = dbconnect::dataconnect()->getdb();
		
		$wsql = '';
		if($ty=='replyed')
		{
			$wsql = ' and Reply!=0 ';
		}elseif($ty=='noreply'){
			$wsql = ' and Reply=0 ';
		}

		$sql_c = "select count(*) as allrow from ".DATATABLE."_order_forum where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." and PID=0 ".$wsql.""; 
		$sql_l = "select ID,Name,Title,Content,Date,Reply,ReplyDate from ".DATATABLE."_order_forum where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." and PID=0 ".$wsql." Order by ReplyDate DESC,ID DESC ";

		$rs    = $db->get_row($sql_c);
		$page  = new ShowPage;
        $page->PageSize		= 8;
        $page->Total		= $rs['allrow'];     

		$result['list']		= $db->get_results($sql_l." ".$page->OffSet());
		$result['showpage']	= $page->ShowLink("forum.php");
		$idmsg = '';
		if(!empty($rs['allrow']))
		{
			foreach($result['list'] as $idvar)
			{
				if(empty($idmsg)) $idmsg = $idvar['ID']; else $idmsg = $idmsg.",".$idvar['ID'];			
			}
			$sql_son = "select ID,PID,Name,Title,Content,Date,Flag,Reply,ReplyDate from ".DATATABLE."_order_forum where CompanyID=".$_SESSION['cc']['ccompany']." and PID!=0 and PID in (".$idmsg.") Order by PID DESC, ID ASC ";
			$replydata	= $db->get_results($sql_son);
			if(!empty($replydata))
			{
				for($i=0;$i<count($result['list']);$i++)
				{
					foreach($replydata as $rvar)
					{
						if($result['list'][$i]['ID'] == $rvar['PID']) $result['list'][$i]['re'][] = $rvar;
					}
				}
			}
		}

		//$db->debug();
		return $result;
		unset($result);
	}

	//插入留言
	function insertforum($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		
		$sql = "insert into ".DATATABLE."_order_forum(CompanyID,ClientID,PID,User,Name,Title,Content,Date,IP,ReplyDate) values(".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",0,'".$_SESSION['cc']['cusername']."', '".$in['forumname']."', '".$in['forumtitle']."', '".$in['froumcontent']."', ".time().", '".RealIp()."', ".time().")";

		if($db->query($sql))
		{			
			$message = "【".$_SESSION['ucc']['CompanySigned']."】您有一条新留言来自:".$_SESSION['cc']['ctruename']."-".$_SESSION['cc']['ccompanyname']."(".$in['forumtitle'].")。退订回复TD";
			sms::get_setsms("7",$message);
						
			return true;
		}else{
			return false;
		}
	}
	

	//回复留言
	function replyforum($in)
	{
		$db	   = dbconnect::dataconnect()->getdb();
		$in['pid'] = intval($in['pid']);
		
		$sql = "insert into ".DATATABLE."_order_forum(CompanyID,ClientID,PID,User,Name,Content,Date,IP) values(".$_SESSION['cc']['ccompany'].",".$_SESSION['cc']['cid'].",".$in['pid'].", '".$_SESSION['cc']['cusername']."', '".$in['replyname']."', '".$in['replycontent']."', ".time().", '".RealIp()."')";

		$sqlu = "update ".DATATABLE."_order_forum set Reply=Reply+1, ReplyDate=".time()." where ID=".$in['pid'];
		if($db->query($sql))
		{
			$db->query($sqlu);
			return true;
		}else{
			return false;
		}
	}

//END
}
?>
