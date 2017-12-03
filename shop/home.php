<?php
/**
 * List
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/homedata.php");
include_once (SITE_ROOT_PATH."/module/cart.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();
$urlmsg     =   "";
$location   =   null;

$shcart  = new ShoppingCart();

setcookie("backurl", $_SERVER['REQUEST_URI']);
$xd_info     = homedata::getxdinfo(8);

if($in['m']=="rollimg")
{
	$xmlmsg = '<?xml version="1.0" encoding="utf-8"?>
	<bcaster autoPlayTime="3">
	';
	foreach($xd_info as $xvar)
	{
		$xmlmsg .= '<item item_url="'.RESOURCE_PATH.$xvar['ArticlePicture'].'" link="'.$xvar['ArticleLink'].'" itemtitle="">
		</item>';
	}
	$xmlmsg  .= '
	</bcaster>';
	echo $xmlmsg;
	exit();
}

$setarr   = commondata::getproductset();
if(empty($in['t']))
{	
	$in['t']  = $setarr['producttype'];
}
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

$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '[推荐]',
		'2'			=>  '[特价]',
		'3'			=>  '[新款]',
		'4'			=>  '[热销]',
		'9'			=>  '[缺货]'
 	 );
if($sv=="default")
{
	$listsite       = homedata::listsite(0,0);
}else{
	$listallsite    = homedata::listallsite(0,0);
}

//获取客服信息
//$customer_service= homedata::get_customer_service(7);

// $goodslist1  = homedata::listsgoods('1',16,$pn);
// $goodslist2  = homedata::listsgoods('2',16,$pn);
// $goodslist3  = homedata::listsgoods('3',16,$pn);
$goodslist4  = homedata::listsgoods('4',8,$pn);

$gg_info     = homedata::getgginfo();
$brand_info  = commondata::getbrandinfo(0, 8, true);
// include template("home");
include template("unicom+home");
?>