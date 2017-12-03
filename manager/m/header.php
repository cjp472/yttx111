<?php
include_once ("../common.php");
include_once ("../class/login.class.php");

$inv = new Input();
$in  = $inv->parse_incoming();
$db  = dbconnect::dataconnect()->getdb();

$menu_arr = array(
		'order'			    =>  '订单',
		'consignment'		=>  '发货',
		'finance'  		    =>  '款项',
		'return'  		    =>  '退单',
		'product'		    =>  '商品',
		'inventory'			=>  '库存',
		'client'		    =>  '药店',
		'saler'		        =>  '客情官',
		'infomation'	    =>  '信息',
		'forum'			    =>  '客服',
		//'sms'	            =>  '短信',
		'statistics'	    =>  '统计',
		'system'    	    =>  '系统'
 	 );
$menu_arr11 = array(
		'order'			    =>  '订单',
		'product'		    =>  '商品',
		'inventory'			=>  '库存',
		'client'		    =>  '药店',
		'saler'		        =>  '客情官',
		'infomation'	    =>  '信息',
		'forum'			    =>  '客服',
		//'sms'	            =>  '短信',
		'statistics'	    =>  '统计',
		'system'    	    =>  '系统'
 	 );
$pope_arr = $menu_arr;

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');

// $skey = think_encrypt('m,'.$_SESSION['uinfo']['userid'].','.$_SESSION['uinfo']['username'],ENCODE_KEY);
// define("HELP_URL",'http://help.dhb.net.cn/manager.php?skey='.$skey);
?>