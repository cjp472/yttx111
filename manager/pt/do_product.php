<?php
$menu_flag = "product";
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
include_once ("../class/letter.class.php");

if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');


/***********save_sort**************/
if($in['m']=="save_sort")
{
	
	if(!empty($in['SiteName']))
	{
		if(!is_numeric($in['SiteOrder'])) $in['SiteOrder'] = 0;

	    $letter  = new letter();
        $pinyima = $letter->C($in['SiteName']);

		$upsql = "insert into ".DATATABLE."_order_classify(siteID,name,pinyin,weight,type,ParentID) values('".$in['ParentID']."','".$in['SiteName']."','".$pinyima."','".$in['SiteOrder']."','".$in['types']."','".$in['ParentID']."')";
		if($db->query($upsql))
		{
			$insiteid = mysql_insert_id();
			if(!empty($in['ParentID']))
			{
				$pinfo = $db->get_row("select siteID FROM ".DATATABLE."_order_classify where id=".$in['ParentID']." limit 0,1");
				if(!empty($pinfo['siteID'])) $sno = $pinfo['siteID'].$insiteid.",";
			}else{
				$sno = "0,".$insiteid.",";
			}
			$db->query("update ".DATATABLE."_order_classify set siteID='".$sno."' where id=".$insiteid." limit 1");
			exit("ok");
		}else{
			exit("保存不成功!");
		}
	}
}

if($in['m']=="save_edit_sort")
{
	
	if(!empty($in['SiteID']))
	{
		if($in['SiteID'] == $in['ParentID']) exit('您不能选上级分类为本分类的子类！');
		if(!is_numeric($in['SiteOrder'])) $in['SiteOrder'] = 0;

	    $letter  = new letter();
        $pinyima = $letter->C($in['SiteName']);

		$pinfo = $db->get_row("select * FROM ".DATATABLE."_order_classify where ID=".$in['ParentID']." limit 0,1");
		$sinfo = $db->get_row("select * FROM ".DATATABLE."_order_classify where ID=".$in['SiteID']." limit 0,1");

		if(strpos($pinfo['siteID'],$sinfo['siteID']) !== false) exit('您不能选择上级分类作他的下级分类的子分类！');
		if(empty($pinfo['siteID'])) $pinfo['siteID'] = '0,';
		$sno = $pinfo['siteID'].$sinfo['id'].",";

		$upsql = "update ".DATATABLE."_order_classify set ParentID=".$in['ParentID'].",siteID='".$sno."', weight=".$in['SiteOrder'].", name='".$in['SiteName']."', pinyin='".$pinyima."' where ID=".$sinfo['id']." limit 1";
	
		$soninfo = $db->get_results("select * FROM ".DATATABLE."_order_classify where siteID like '".$sinfo['siteID']."%' ");

		if($db->query($upsql))
		{
			if(!empty($soninfo))
			{
				foreach($soninfo as $var)
				{
					if($sinfo['id'] == $var['id']) continue;
					$sonno = str_replace($sinfo['SiteID'],$sno,$var['SiteID']);
					$db->query("update ".DATATABLE."_order_classify set SiteID='".$sonno."' where ID=".$var['id']." limit 1");
				}
			}
			exit("ok");
		}else{
			exit("无变化，没修改任何内容!");
		}
	}
}

if($in['m']=="delete_sort")
{
	$in['SiteID'] = intval(trim($in['SiteID']));
	
	if(!empty($in['SiteID']))
	{
		$sortcount = $db->get_row("SELECT count(*) as countsite FROM ".DATATABLE."_order_classify where ParentID=".$in['SiteID']." limit 0,1");
		if(!empty($sortcount['countsite'])) exit("请先删除下级分类!(逐级删除)");

		$procount = $db->get_row("SELECT count(*) as countpro FROM ".DATATABLE."_order_content_index where SiteID=".$in['SiteID']." limit 0,1");
		if(!empty($procount['countpro'])) exit("该分类已在使用，请先删除该分类下的商品!(包含已下架的商品)");

		$upsql = "delete from ".DATATABLE."_order_classify where  id=".$in['SiteID']." limit 1";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("删除不成功!");
		}
	}else{
		exit('请指定你要删除的分类!');
	}
}

if($in['m']=="add_notice_type")
{
	
	if(!empty($in['name']))
	{
		$upsql = "insert into ".DATATABLE."_pay_notice_type(name,view_type,add_time) values('".$in['name']."','".$in['view_type']."','".time()."')";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("添加不成功!");
		}
	}else{
		exit('请输入分类名称!');
	}
}

if($in['m']=="edit_notice_type")
{
	if(empty($in['id'])) exit("获取不到ID!");
	if(!empty($in['name']))
	{
		$upsql = "update ".DATATABLE."_pay_notice_type set name='".$in['name']."',view_type='".$in['view_type']."' where ID=".$in['id']." limit 1";
		if($db->query($upsql))
		{
			exit("ok");
		}else{
			exit("修改不成功!");
		}
	}else{
		exit('请输入分类名称!');
	}
}

if($in['m']=="delete_notice_type")
{
	
	if(empty($in['id'])) exit("获取不到ID!");
	$count = $db->get_row("SELECT count(*) as countsite FROM ".DATATABLE."_pay_notice where type=".$in['id']." limit 0,1");
	if(!empty($count['countsite'])) exit("该分类已在使用，请先删除该分类下的信息!");

	$upsql = "delete from ".DATATABLE."_pay_notice_type where  id=".$in['id']." limit 1";
	if($db->query($upsql))
	{
		exit("ok");
	}else{
		exit("删除不成功!");
	}
	
}

exit('非法操作');
?>