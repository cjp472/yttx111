<?
/**
 * List
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/listdata.php");

	$input		=	new Input;
	$in			=	$input->parse_incoming();

	if(!intval($in['id']))
	{
		exit('<p><p>您访问的商品不存在，点此 <a href="./home.php">返回首页 Back</a></p>');
	}

	$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '[推荐]',
		'2'			=>  '[特价]',
		'3'			=>  '[新款]',
		'4'			=>  '[热销]',
		'9'			=>  '[缺货]'
 	 );

	listdata::upcount($in['id']);
	$goods  = listdata::listcontent($in['id']);
	if(empty($goods['index']['Name']))
	{
		exit('<p><p>您访问的商品不存在，点此 <a href="./home.php">返回首页 Back</a></p>');
	}
	if(!empty($goods['index']['Color']) || !empty($goods['index']['Specification']))
	{
		$goods['index']['cs'] = "Y";
	}else{
		$goods['index']['cs'] = "N";
	}
	$goods['content']['Content'] = html_entity_decode($goods['content']['Content'], ENT_QUOTES,'UTF-8');
		
	$goods['content']['Content'] = str_replace("http://resource.dhb.hk/",RESOURCE_PATH,$goods['content']['Content']);

	if(!empty($goods['content']['FieldContent']))
	{
		$farr = unserialize($goods['content']['FieldContent']);
		$setfield = commondata::getproductset('field');

		if(!empty($setfield))
		{
			foreach($setfield as $key=>$var)
			{
				if(!empty($farr[$key]))
				{
					$goods['field'][$key]['value'] = $farr[$key];
					$goods['field'][$key]['name']  = $var['name'];
				}
			}
		}
	}
	$setpoint = commondata::getproductset('point');
	if(empty($setpoint['pointtype']) || $setpoint['pointtype']!="3")  $goods['content']['ContentPoint'] = '';

	$SiteInfo = listdata::getsiteinfo($goods['index']['SiteID']);
	$SiteID   = $SiteInfo['SiteID'];
	$location = listdata::getlocationinfo($SiteInfo);

	if($sv=="default")
	{
		$listsite     = listdata::listsite($SiteInfo['ParentID'],$SiteID);
	}else{
		$listallsite  = listdata::listallsite($SiteInfo['ParentID'],$SiteID);
	}
	
	$goodsrelation = listdata::getrelation($goods['index']['ID']);
	$goodslink    = listdata::listsitelink($goods['index']['ID'], $goods['index']['SiteID'],8);
    $setarr   = commondata::getproductset();
    
    //收藏
    $goods['fav'] = listdata::getfav($goods['index']['ID']);
    
	if(!empty($in['tpl']) && $in['tpl']=="img")
	{
// 		include template("content_img");
		include template("unicom+content_img");
	}else{
// 		include template("content");
		include template("unicom+content");
	}
?>