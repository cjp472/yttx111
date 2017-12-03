<?php

/**
 * @name 系统短信模板 @2014/08/19
 */

$smsTpl = array(
		"YJFOpenApiReapet"		=> "开户回推信息异常：已开户，但再次收到开户回推。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
		"YJFOpenApiLessZero"	=> "开户回推信息异常：修正供应商ID后，小于0。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
		"YJFOpenApiSuccess"		=> "写入开户数据成功。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
		"YJFOpenApiFailed"		=> "写入开户数据失败。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
);

/**
 * @name 获取对应短信模板
 * @param string $tplName
 * @return string
 */
function getSmsTpl($tplName = ''){
	global $smsTpl;
	
	if(isset($smsTpl[$tplName])){
		return $smsTpl[$tplName] . "退订回复 TD";
	}else{
		//抛出异常
	}
}

?>