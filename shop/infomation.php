<?
/**
 * Infomation
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/listinfo.php");

$input		=	new Input;
$in			=	$input->parse_incoming();
$urlmsg     =   "";
$location   =   null;

if(empty($in['ID'])) $in['ID'] = '';
if(empty($in['sid']))
{
	$in['sid'] = 0;
	$sortinfo['SortID'] = 0;
	$sortinfo['SortName'] = '公告信息';
}else{
	$sortinfo		= listinfo::showsortinfo($in['sid']);
}


$sortdata_arr_left	= listinfo::getsortinfo('50');
if(count($sortdata_arr_left) > 15){
	foreach($sortdata_arr_left as $leftkey=>$leftvar)
	{
		if($leftkey > 15) break;
		$sortdata_arr[] = $leftvar;
	}
}else{
	$sortdata_arr = $sortdata_arr_left;
}

if(!empty($in['m']))
{
	
	if(!empty($in['ty']) && $in['ty'] == 'plat'){
		$infomation			= listinfo::platformshowinfo($in['ID']);
		$infomation['ArticleTitle']=$infomation['title'];
		$infomation['ArticleContent']=html_entity_decode($infomation['content'], ENT_QUOTES,'UTF-8');
	}else{
		$infomation			= listinfo::showinfo($sortinfo['SortID'],$in['ID']);
		$stype = "file";
		if(!empty($infomation['ArticlePicture']))
		{
			$sExtension = substr( $infomation['ArticlePicture'], ( strrpos($infomation['ArticlePicture'], '.') + 1 ) );
			if($sExtension=="jpg" || $sExtension=="png" || $sExtension=="gif") $stype = "img"; else $stype = "file";
		}
		$infomation['ArticleContent']     = html_entity_decode($infomation['ArticleContent'], ENT_QUOTES,'UTF-8');
		
		$infomation['ArticleContent'] = str_replace("http://resource.dhb.hk/",RESOURCE_PATH,$infomation['ArticleContent']);
	}
	include template("infomation_content");

}else{
	
	if(!empty($in['ty']) && $in['ty'] == 'platfrom_list'){
		$infomation			= listinfo::platformlistinfomation(18);
		include template("platform_infomation_list");
		
	}else{
		$infomationnumber	= listinfo::showinfotitlenumber($sortinfo['SortID']);
		if($infomationnumber <= 5)
		{
			$infomation			= listinfo::showinfo($sortinfo['SortID'],$in['ID']);
			$infomationtitle	= listinfo::showinfotitle($sortinfo['SortID']);
			$stype = "file";

			if(!empty($infomation['ArticlePicture']))
			{
				$sExtension = substr( $infomation['ArticlePicture'], ( strrpos($infomation['ArticlePicture'], '.') + 1 ) );
				if($sExtension=="jpg" || $sExtension=="png" || $sExtension=="gif") $stype = "img"; else $stype = "file";
			}
			$infomation['ArticleContent'] = html_entity_decode($infomation['ArticleContent'], ENT_QUOTES,'UTF-8');
			
			$infomation['ArticleContent'] = str_replace("http://resource.dhb.hk/",RESOURCE_PATH,$infomation['ArticleContent']);

			include template("infomation");
		}else{
			$infomation			= listinfo::listinfomation($sortinfo['SortID'],$infomationnumber,18);

			include template("infomation_list");
		}
	}	
	
}
?>