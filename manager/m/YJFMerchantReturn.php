<?php 

/**
 * program for action merchant api
*
* PHP version 5
*
* @category  PHP
* @author    WanJun <316174705@qq.com>
* @copyright 2015 Rsung
* @version   1.0
* @date	  2015/07/02
*
*/

include_once ("../common.php");

$inv = new Input();
$in  = $inv->parse_incoming();
$db  = dbconnect::dataconnect()->getdb();

//parse_str('userType=B&userName=cs123@t.com&externalUserId=0001125&userId=20150608020000071808&notifyTime=2015-07-02 18:54:36&request_method=post', $in);
//parse_str('userType=B&userName=331@q.com&externalUserId=dhb0m0000819&userId=20150610020000071990&notifyTime=2015-07-07 18:19:11', $in);

unset($in['request_method']);
							
$Merchant = new Merchant($in);
$Merchant->writeMerchantInDHB();

//只要收到信息就通知已收到回推信息
echo 'success';

?>