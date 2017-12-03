<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */
include_once ("../common.php");
include_once ("../class/sms.class.php");
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

$inv = new Input();
$in  = $inv->parse_incoming();
$db  = dbconnect::dataconnect()->getdb();
$ip  = $_SERVER["REMOTE_ADDR"];
$in  = $inv->_htmlentities($in);
//$log   = KLogger::instance(LOG_PATH, KLogger::INFO);
	//商户订单号
	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号
	$trade_no    = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];
	$total_fee    = $_GET['total_fee'];	    //获取总价格

		$cinfo = $db->get_row("select PayID,PayCompany,PaySN,PayStatus from ".DATABASEU.DATATABLE."_order_pay where PaySN = '".$out_trade_no."' order by PayID desc limit 0,1");
		$companyid = $cinfo['PayCompany'];
		$clientid  = $cinfo['PayClient'];

?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	if(empty($_GET['subject']) || $_GET['subject']=="预付款" )  $_GET['subject'] = "0";

    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
	    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
		//如果有做过处理，不执行商户的业务程序
		$db->query("update ".DATABASEU.DATATABLE."_order_pay set PayMoney='".$total_fee."',PayTradeNO='".$_GET['trade_no']."',PayBuyer='".$_GET['buyer_email']."',PayStatus='".$_GET['trade_status']."' where PayID = ".$cinfo['PayID']." limit 1");

		$message = '【订货宝】支付宝'.$_GET['buyer_email'].' 充值: '.$total_fee.'元 单号:'.$dingdan.'状态:'.$_GET['trade_status'].'';
		$mobilearr[]  = '13981715406';
		$statusCode2  = $client->login();
		$statusCode   = $client->sendSMS($mobilearr,$message);
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
		
	//echo "验证成功<br />";

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "验证失败";
}
?>
        <title>支付宝即时到账交易接口</title>
        <style type="text/css">
            .font_content{
                font-family:"宋体";
                font-size:14px;
                color:#FF6600;
            }
            .font_title{
                font-family:"宋体";
                font-size:16px;
                color:#FF0000;
                font-weight:bold;
            }
            table{
                border: 2px solid #CCCCCC;
            }
			table td{
				border: 1px solid #eeeeee; font-size:14px;
				font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif;
			}
			h3{
				font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif;
				font-size:20px;
			}
        </style></head>
    <body>

        <table align="center" width="650" cellpadding="5" cellspacing="0" style="margin:30px auto;">
            <tr>
                <td align="center" class="font_title" colspan="2"><h3>支付宝支付返回通知</h3></td>
            </tr>
            <tr>
                <td class="font_content" align="right">收款帐号：</td>
                <td class="font_content" align="left"><?php echo $_GET['buyer_email']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">支付宝交易号：</td>
                <td class="font_content" align="left"><?php echo $_GET['trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">订单交易号：</td>
                <td class="font_content" align="left"><?php echo $_GET['out_trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">付款总金额：</td>
                <td class="font_content" align="left">¥ <?php echo $_GET['total_fee']; ?> 元</td>
            </tr>
            <tr>
                <td class="font_content" align="right">支付订单：</td>
                <td class="font_content" align="left"><?php if(empty($_GET['subject']) || $_GET['subject']=="0") echo '预付款'; else echo $_GET['subject']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">备注说明：</td>
                <td class="font_content" align="left"><?php echo $_GET['body']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">支付账号：</td>
                <td class="font_content" align="left"><?php echo $_GET['buyer_email']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">交易状态：</td>
                <td class="font_content" align="left"><?php if($_GET['trade_status']=="TRADE_SUCCESS" ||  $_GET['trade_status']=="TRADE_FINISHED") echo '支付成功'; else '支付未完成' ?></td>
            </tr>
        </table>
    </body>
</html>