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
	define("GLOBAL_LOG_PATH", WEB_SITE_PATH."/global/logs/");
	
	//全局 CLASS 路径配置
	define("GLOBAL_CLASS_PATH", WEB_SITE_PATH."/global/class/");
	
	//全局 MODULE 路径配置
	define("GLOBAL_MODULE_PATH", WEB_SITE_PATH."/global/module/");
	
	//全局 PLUGIN 路径配置
	define("GLOBAL_PLUGIN_PATH", WEB_SITE_PATH."/global/plugin/");
	
	//短信签名档
	define("COMPANY_SIGNED_NAME", '【医统天下】');
	
	//密码混淆码
	define("PASSWORD_MIX_UP", 'yitongtianxia111.com%*&*#$^');
	
	//全局表单序列号
	define("GLOBAL_SAFETY_SERIAL", md5(PASSWORD_MIX_UP . time()));
	
	//全局调试开关
	define("OPEN_DEBUG", true);
	
	//文件访问授权
	define("SYSTEM_ACCESS", 'ALLOW');
	
	/*************** 银行签约账户类型 **********/
	$getway_account_type = array(
			'personal' => '个人账户',
			'company' => '企业账户'
	);
	
	//全局 ID 偏移量(不可更改)
	define("GLOBAL_NUMERIC_FIXED", 0);
	
	/******************************************************** 医统账期 START **********************************************************/
	define("YTTX_CREDIT_RATE", 0.18);	//账期年化利息18%

	/******************************************************** 易极付配置 START **********************************************************/
	
	//报文记录配置
	define("YOPENAPI_MESSAGE_PATH", GLOBAL_LOG_PATH."yopenapireturn/");
	
	//是否记录易支付日志
	define("YOPENAPI_LOG_IS_TRUE", true);
	
	//易支付日志记录
	define("YOPENAPI_LOG_PATH", GLOBAL_LOG_PATH.'yopenapi/');
	
	//易支付供应商开户回推日志记录
	define("YOPENAPI_MERCHANT_LOG_PATH", GLOBAL_LOG_PATH.'merchant/');
	
	//联调URL
 	define("YAPI_SUBMIT_URL", 'https://api.yiji.com');			//生产环境
//	define("YAPI_SUBMIT_URL", 'https://openapi.yijifu.net/gateway.html');		//测试环境
	
	//返回通知URL(异步，经销商和手机端)
	define("YAPI_FRONT_NOTIFY_URL", WEB_ROOT_URL.'/shop/async.php');
	//define("YAPI_FRONT_NOTIFY_URL", 'http://139.224.208.83/shop/async.php');

	//返回通知URL(页面，经销商和手机端)
	define("YAPI_FRONT_RETURN_URL", WEB_ROOT_URL.'/shop/online.php');
	//define("YAPI_FRONT_RETURN_URL", 'http://139.224.208.83/shop/online.php');
	
	//返回通知URL(异步，供应商)
	define("YAPI_BACKEND_NOTIFY_URL", WEB_ROOT_URL.'/manager/m/async.php');
	//define("YAPI_BACKEND_NOTIFY_URL", 'http://139.224.208.83/manager/m/async.php');

	//返回通知URL(页面，供应商)
	define("YAPI_BACKEND_RETURN_URL", WEB_ROOT_URL.'/manager/m/online.php');
	//define("YAPI_BACKEND_RETURN_URL", 'http://139.224.208.83/manager/m/online.php');
	
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
	define("YAPI_ORDER_PREFIX_BACKEND", 'etong');  //生产环境
	//define("YAPI_ORDER_PREFIX_BACKEND", 'dhb0tm');	//测试环境
	
	//DHB商户号
 	define("YAPI_PARTNERID", '20161212020012194276');	//生产环境
//	define("YAPI_PARTNERID", '20160612020000748352');	//测试环境
	
	//DHB安全码
 	define("YAPI_SECURITYKEY", '6b87bb349cb807e43da2df76a6e0bac1');		//生产环境
//	define("YAPI_SECURITYKEY", 'fbeb22c3ac9b1928ff175ab6dce70220');		//测试环境
	
	//开户信息通知号码，多个,分隔
// 	define("YAPI_RECEIVE_PHONE", '13088070799,13981715406');
	define("YAPI_RECEIVE_PHONE", '13348889870');
	
	/************************* 以下是特有配置，禁止修改 ******************************/
	//医统专用收款账户编号
	define("YTTX_CREDIT_SIGNNO", '20161212020012194276');
	//医统专用收款账户名称
	define("YTTX_CREDIT_SIGNACCOUNT", '20161226010012231124');
	define("YAPI_CREDIT_NOTIFY_URL", WEB_ROOT_URL.'/shop/credit_async.php');
	define("YAPI_CREDIT_RETURN_URL", WEB_ROOT_URL.'/shop/credit_online.php');
	/************************* 以上是特有配置，禁止修改 ******************************/
	
	/******************************************************** 易极付配置 END **********************************************************/
	
	/**********************************************************短信配置***********************************************************************/

	//旧版本
	//网关地址
	define("YM_SMS_ADDR", "http://hprpt2.eucp.b2m.cn:8080/sdk/SDKService?wsdl");
	
	//序列号,请通过亿美销售人员获取
	define("YM_SMS_APPID", "EUCP-EMY-SMS1-02RIH");
	
	//密码,请通过亿美销售人员获取
	define("YM_SMS_AESPWD", "968162");
	
	//登录后所持有的SESSION KEY，即可通过login方法时创建
	define("YM_SMS_SESSION_KEY", "968162");
	
	//连接超时时间，单位为秒
	define("YM_SMS_CONNECT_TIMEOUT", 2);
	
	//远程信息读取超时时间，单位为秒
	define("YM_SMS_READ_TIMEOUT", 10);
	
	/**
	 $proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器
	 $proxyport		可选，代理服务器端口，默认为 false
	 $proxyusername	可选，代理服务器用户名，默认为 false
	 $proxypassword	可选，代理服务器密码，默认为 false
	 */
	define("YM_SMS_PROXYHOST", false);
	define("YM_SMS_PROXYPORT", false);
	define("YM_SMS_PROXYUSERNAME", false);
	define("YM_SMS_PROXYPASSWORD", false);
	
	//个别ERP对单个订单的商品明细数量有要求，需要限制[注意线上与本地开发的companyid可能不一致]
	$erp_limit_order_nums = array(
			'523' => 90,		//新乡爱森-时空，ERP限制数量99
			'543' => 90,		//新乡德尔康-时空，ERP限制数量99
	)

?>