<?php
/* *
 * 功能：支付宝手机网站支付接口(alipay.trade.wap.pay)接口调试入口页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 请确保项目文件有可写权限，不然打印不了日志。
 */

header("Content-type: text/html; charset=utf-8");
include_once ("../common.php");

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'AlipayApiController.class.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';

$db		= dbconnect::dataconnect()->getdb();
$db->cache_dir  = CONF_PATH_CACHE;

$input		=	new Input;
$in			=	$input->parse_incoming();
$param		=   json_decode($in['v'],true);

if (empty ( $param['sKey'] )||empty ( $param['AliMoney'] ))
	{
		$rdata['rStatus'] = 110;
		$rdata['error']   = '参数错误';
	}
	else{
		$cidarr = AlipayApiController::getCompanyInfo($param['sKey']); //取公司ID,Database
		
		if($cidarr['rStatus'] != 100){
    			$rdata = $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$clientid	= $cidarr['ClientID'];
    			$sdatabase  = $cidarr['Database'];
    			
    			$payinfo = AlipayApiController::show_alipay($cidarr,$param['AliPayID']);
    			
    			//重组支付宝配置参数
    			$config['app_id'] = $payinfo['APPID'];
    			
    			if($payinfo['rStatus'] != 100){
    				$rdata = $payinfo;
	    		}else{
	    			/**************************请求参数**************************/
			        //合作身份者id，以2088开头的16位纯数字
// 			        $alipay_config['partner']               = $payinfo['PayPartnerID'];
			
			        //安全检验码，以数字和字母组成的32位字符
// 			        $alipay_config['key']                   = $payinfo['PayKey'];
			
			        //支付类型
// 			        $payment_type = "1";
			        //必填，不能修改
			        //服务器异步通知页面路径
// 			        $notify_url = $config['notify_url'];
			
			        //卖家支付宝帐户
// 			        $seller_id = $payinfo['AccountsNO'];
			        //必填
			
			        //商户订单号
			        $paysn = date("Ymd").microtime_float();
			        $paysn = str_replace(".","-",$paysn);
			        $out_trade_no = $paysn;
			        //商户网站订单系统中唯一订单号，必填
			
			        //订单名称
			        $subject = $param['orderNO'];
			        if(empty($subject)) $subject = '医统天下BMB平台预付款';
			        //必填
			
			        //付款金额
			        $total_fee = abs(floatval($param['AliMoney']));
			        //必填
			
			        //订单描述
			        $body = $param['AliBody'];
			        if(empty($body) || $body == '无') $body = $cidarr['ClientInfo']['ClientCompanyName'];
			        $body = "【".$cidarr['CompanySigned']."】" . $body;
			        
			        /************************************************************/

			        //超时时间
			        $timeout_express="1m";
			        
			        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
			        $payRequestBuilder->setBody($body);
			        $payRequestBuilder->setSubject($subject);
			        $payRequestBuilder->setOutTradeNo($out_trade_no);
			        $payRequestBuilder->setTotalAmount($total_fee);
			        $payRequestBuilder->setTimeExpress($timeout_express);
			        
			        $payResponse = new AlipayTradeService($config);
			        $result=$payResponse->wapPay($payRequestBuilder, $config['return_url'], $config['notify_url']);
					
					if(!empty($result)){
						$rdata['rStatus'] = 100;
						$rdata['para'] = $result;
						
						$param['out_trade_no'] = $out_trade_no;
						$status = AlipayApiController::save_alipay($param, $cidarr);	
						
						//↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓文本记录↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓//begin tubo 增加日志接收记录 
						$kLog = KLogger::instance(AliPay_LOG_PATH);
						$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
						
						//转码
						$logMsg = '发送请求：' . $result;
						$kLog->logInfo($logMsg);
						//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑文本记录↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑//
					}else{
						$rdata['rStatus'] = 110;
						$rdata['error'] = '生成支付数据异常';
					}
	    		}
    		}
	}

$rdatamsg = json_encode($rdata);
$rdatamsg = str_replace("\n","",$rdatamsg);
$rdatamsg = str_replace("\t","",$rdatamsg);
$rdatamsg = str_replace('"rData":null','"rData":[]',$rdatamsg);
$rdatamsg = str_replace('null','""',$rdatamsg);
echo $rdatamsg = str_replace("\r","",$rdatamsg);


?>
