<?php
/** * 网关地址 */	
$gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl';
/** * 序列号,订货宝验证码 */
$serialNumber = '3SDK-EMY-0130-KCULP';
/** * 密码, */
$password = '064347';
/** * 登录后所持有的SESSION KEY，即可通过login方法时创建 */
$sessionKey = '489181';
/** * 连接超时时间，单位为秒 */
$connectTimeOut = 2;
/** * 远程信息读取超时时间，单位为秒 */ 
$readTimeOut = 10;
/**	$proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器	$proxyport		可选，代理服务器端口，默认为 false	$proxyusername	可选，代理服务器用户名，默认为 false	$proxypassword	可选，代理服务器密码，默认为 false*/		
$proxyhost = false;	
$proxyport = false;	
$proxyusername = false;	
$proxypassword = false; 

$sms_config = array(
		'sms_validate' => '【订货宝】 {CODE}（订货宝短信校验码，请勿泄漏），需要您进行身份校验。如非本人操作，请忽略本短信',
		'sms_notify' => '【订货宝】订货系统已经开通，您的账号：{ACCOUNT} ，密码：{PASSWORD} ；欢迎您使用最好的互联网订货系统，电脑登录： m.dhb.hk 下载APP: dhb.hk/app 。 您也可以和您的客户关注我们的微信【订货宝手机客户端】直接进行移动管理与订货。我们将在24小时内进行回访，为您提供专业细致的服务。欢迎致电订货宝客服中心 4006311682。',
);
?>