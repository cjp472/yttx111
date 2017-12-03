<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => file_get_contents(SITE_ROOT_PATH.'/alipaytrade/RSA/rsa2_private_key.txt'),
		
		//异步通知地址
		'notify_url' => "http://139.224.208.83:8080/mobileApi/alipaytrade/notify_url.php",
// 		'notify_url' => WEB_ROOT_URL.'/mobileApi/alipaytrade/notify_url.php',
		
		//同步跳转
// 		'return_url' => "http://mitsein.com/alipay.trade.wap.pay-PHP-UTF-8/return_url.php",
		'return_url' => WEB_ROOT_URL.'/mobileApi/alipaytrade/return_url.php',

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApCqGDRox0xD5ZYt4oz9rhFAYmdiB50zt/Y8WNHwmzqMbfNmR4qVANdfRquu/YPnOP4Cgbohg9iB4HrWTWYnW0J5cGXaVftMo00NQzUG8ycrAcWtGy2FQSbvpUs8IgZDtMXwvtbiG5D0oUfnpDY+eCa5c6LDrZFQndx8GFXsQV+APG1+VxJSRtTcPqxbgqKY+RaRBtTF9ONZD/uUrksZV2PkCqi5VuNtdT3bnCF6QuBD4NFnkE6PFyF7xhiqRKH0clBXhepeNXxgBP3fB19R9to/L6LTdrCzlJlCl5P909RAfilJhB1LhKP0KzBky+MSWVMDI7qwWQ/ZaRpZ01O4SAQIDAQAB",
		
	
);