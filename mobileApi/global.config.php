<?php
/**
 * ++++++++++++++++++++++++++++++++++++++++++
 * 
 * 全局配置文件
 * 2015/01/14 @hugh
 * 
 *+++++++++++++++++++++++++++++++++++++++++++
 **/
	//全局日志路径配置
	define("GLOBAL_LOG_PATH", SITE_ROOT_PATH."/logs/");
	
	//全局 CLASS 路径配置
	define("GLOBAL_CLASS_PATH", SITE_ROOT_PATH."/class/");
	
	/*************** 银行签约账户类型 **********/
	$getway_account_type = array(
			'personal' => '个人账户',
			'company' => '企业账户'
	);
	
	//全局 ID 偏移量(不可更改)
	define("GLOBAL_NUMERIC_FIXED", 0);

	/******************************************************** 易极付配置 START **********************************************************/
	
	//define root url
	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
	define("WEB_ROOT_URL", rtrim(trim($protocol.$_SERVER['HTTP_HOST'], '/'),'.'));
	
	//报文记录配置
	define("YOPENAPI_MESSAGE_PATH", GLOBAL_LOG_PATH."yopenapireturn/");
	
	//是否记录易支付日志
	define("YOPENAPI_LOG_IS_TRUE", true);
	
	//文件访问授权
	define("SYSTEM_ACCESS", 'ALLOW');
	
	//易支付日志记录
	define("YOPENAPI_LOG_PATH", GLOBAL_LOG_PATH.'yopenapi/');
	
	//易支付供应商开户回推日志记录
	define("YOPENAPI_MERCHANT_LOG_PATH", GLOBAL_LOG_PATH.'merchant/');
	
	//返回通知URL(异步，经销商和手机端)
	define("YAPI_FRONT_NOTIFY_URL", WEB_ROOT_URL.'/mobileApi/async.php');
	//define("YAPI_FRONT_NOTIFY_URL", 'http://139.224.208.83/mobileApi/async.php');	

	//返回通知URL(页面，经销商和手机端)
	define("YAPI_FRONT_RETURN_URL", WEB_ROOT_URL.'/mobileApi/online.php');

	//返回通知URL(异步，供应商) PC
	define("YAPI_BACKEND_NOTIFY_URL", WEB_ROOT_URL.'/manager/m/async.php');
	
	//返回通知URL(页面，供应商) PC
	define("YAPI_BACKEND_RETURN_URL", WEB_ROOT_URL.'/manager/m/online.php');	
	
	//api协议
	define("YAPI_PROTOCOL", 'httpPost');
	
	//api版本
	define("YAPI_VERSION", '1.0');
	
	/////////////////////////  正式环境使用 //////////////////////////////////////
	//易支付业务订单号前缀(正式环境经销商)
	define("YAPI_ORDER_PREFIX", '');
	
	//易支付业务订单号前缀(正式环境供应商)
	//define("YAPI_ORDER_PREFIX_BACKEND", 'dhb0m');
	
	///////////////////////// 测试环境使用 //////////////////////////////////////
	//易支付业务订单号前缀(测试环境经销商)
	define("YAPI_ORDER_PREFIX_TEST", 'dhb0tc');
	
	//易支付业务订单号前缀(测试环境供应商)
	//define("YAPI_ORDER_PREFIX_BACKEND_TEST", 'dhb0tm');
	
	//签名方式：MD5,Sha1hex,Sha256Hex,HmacSHA1Hex
	define("YAPI_SIGN_TYPE", 'MD5');
	
	//DHB供应商前缀
	define("YAPI_ORDER_PREFIX_BACKEND", 'etong');  //新生产环境
	//define("YAPI_ORDER_PREFIX_BACKEND", 'dhb0tm');	//测试环境
	
		//联调URL
 	define("YAPI_SUBMIT_URL", 'https://api.yiji.com');		//生产环境
//	define("YAPI_SUBMIT_URL", 'https://openapi.yijifu.net/gateway.html');		//测试环境
	
	//DHB商户号
	define("YAPI_PARTNERID", '20161212020012194276');	//生产环境
//	define("YAPI_PARTNERID", '20140411020055684571');	//测试环境
	
	//DHB安全码
	define("YAPI_SECURITYKEY", '6b87bb349cb807e43da2df76a6e0bac1');		//生产环境
//	define("YAPI_SECURITYKEY", 'c9cef22553af973d4b04a012f9cb8ea8');		//测试环境

	
	//执行完跳转回网页地址
	define("YAPI_RETURN_URL", 'http://'.$_SERVER['HTTP_HOST'].'/mobile/html/index.html#/order-payment/0');		//订货宝生产环境
//	define("YAPI_RETURN_URL", 'http://www.wyb.hk/dhb/html/index.html#/order-payment/0');		//订货宝测试环境
	
	//开户信息通知号码，多个,分隔
// 	define("YAPI_RECEIVE_PHONE", '13088070799,13981715406');
	define("YAPI_RECEIVE_PHONE", '');

	include_once (SITE_ROOT_PATH."/class/YOpenApi.class.php");
	include_once (SITE_ROOT_PATH."/class/SubmitExecute.class.php");
	include_once (SITE_ROOT_PATH."/class/Common.class.php");
	include_once (SITE_ROOT_PATH."/class/NetGetWay.module.php");
	include_once (SITE_ROOT_PATH."/class/NetPaySeria.module.php");
	include_once (SITE_ROOT_PATH."/class/NetPayToOrder.module.php");
	include_once (SITE_ROOT_PATH."/class/YOpenApiSet.module.php");
	include_once (SITE_ROOT_PATH."/class/YopenApiFront.class.php");
	include_once (SITE_ROOT_PATH."/class/YOpenApiController.class.php");
	include_once (SITE_ROOT_PATH."/class/YOpenApiDo.class.php");
	
	/******************************************************** 易极付配置 END **********************************************************/
	
?>