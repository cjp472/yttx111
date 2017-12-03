<?php
include_once ("../common.php");
include_once ("../class/login.class.php");

if(!in_array($_SESSION['uinfo']['userid'],array(1))) exit('非法路径!');

$kfarr = array(
	'all' => '全部操作员',
	'kfadm' => '尹欣',
	'kfzy'  => '张洋',
	'rsung-jx' => '舒加鑫',
);

$inv = new Input();
$in  = $inv->parse_incoming();
$db  = dbconnect::dataconnect()->getdb();

$menu_arr = array(
		'order'			    =>  '订单',
		'consignment'		=>  '发货',
		'finance'  		    =>  '收款',
		'return'  		    =>  '退单',
		'product'		    =>  '商品',
		'inventory'			=>  '库存',
		'client'		    =>  '经销商',
		'saler'		        =>  '业务员',
		'infomation'	    =>  '信息',
		'forum'			    =>  '客服',
		'sms'	            =>  '短信',
		'statistics'	    =>  '统计',
		'system'    	    =>  '系统'
 	 );
 	 
 $pay_arr = array(
     	'alipay'			=>  '支付宝',
		'allinpay'			=>  '通联支付',
		'line'  		    =>  '线下'
  	 );
  	 
  $contact_arr = array(
     	'0'					=>  '未查看',
		'1'					=>  '正常',
		'2'  		    	=>  '已回访',
		'9'  		    	=>  '已删除'
  	 );	 
 	 
$pope_arr = $menu_arr;
?>