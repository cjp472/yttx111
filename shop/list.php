<?php
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
$urlmsg		=   "";
$location	=   null;
setcookie("backurl", $_SERVER['REQUEST_URI']);
$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '[推荐]',
		'2'			=>  '[特价]',
		'3'			=>  '[新款]',
		'4'			=>  '[热销]',
		'9'			=>  '[缺货]'
 	 );

if(empty($in['s']))
{
	$SiteID   = 0;
	$in['s']  = 0;
	$SiteInfo['ParentID'] = 0;
}else{
	$SiteInfo = listdata::getsiteinfo($in['s']);
	$SiteID   = $SiteInfo['SiteID'];
	$urlmsg  .= "&s=".$SiteID;
	$location = listdata::getlocationinfo($SiteInfo);
}

$setarr   = commondata::getproductset();
if(empty($in['t']))
{
	if(!empty($_SESSION['showtype']))
	{
		$in['t']  = $_SESSION['showtype'];
	}
	elseif(!empty($setarr['producttype']))
	{
		$in['t']  = $setarr['producttype'];
	}else{
		$in['t']  = 'imglist';
	}
}else{
	$urlmsg  .= "&t=".$in['t'];
}
$_SESSION['showtype'] = $in['t'];

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

if(empty($in['o']))
{
	$in['o']  = "";
}else{
	$urlmsg  .= "&o=".$in['o'];
}

if(empty($in['stock']))
{
	$in['stock']  = 0;
}else{
	$urlmsg  .= "&stock=".$in['stock'];
}

if($sv=="default")
{
	$listsite     = listdata::listsite($SiteInfo['ParentID'],$SiteID);
}else{
	//$listallsite  = listdata::listallsite($SiteInfo['ParentID'],$SiteID);
	$listallsite  = listdata::listallsite_3($SiteInfo['ParentID'],$SiteID);
}

if(empty($in['m']))
{
	$goodslist  = listdata::listgoods($SiteID,$in['b'],$in['o'],$in['t'],$in['ps'],$iss,'list.php',$in['stock']);
// 	debug($goodslist);
// 	$brandlist  = commondata::getbrandinfo($SiteID);
// 	include template("list");
	include template("unicom+list");
	
}elseif($in['m']=="spc"){
	$goodslist  = listdata::listsgoods($in['o'],$in['t'], $in['ty'],$in['ps'],$iss,'list.php',$in['b']);
	
	if(!empty($in['ty'])) $titlemsg = $producttypearr[$in['ty']]."商品"; else $titlemsg = "特价商品"	;
	$titlemsg = str_replace("[","",$titlemsg);
	$titlemsg = str_replace("]","",$titlemsg);
// 	include template("list_spc");
	include template("unicom+list_spc");
}
elseif($in['m']=="select")
{
	$ls		=	new listdata;
	if(empty($in['s'])) $in['sid'] = 0;
	$goodslist   = $ls->listsearchgoods($in['s'],$in['kw'],10,$iss);

	$sortarr       = $ls->listallsitedata();
	$menumsg  = $ls->ShowTreeMenu($sortarr,0,$in['s'],1);

	include template("select_list");
}
?>