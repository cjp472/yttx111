<?php

//载入SMS短信配置文件

/***
 * 亿美新短信接口API
 * 主要端口包括：1、发送短信 2、批量发送 3、查询余额
 * 
 * @author wanjun<wan.jun@hotmail.com>
 * @since  2017-09-21
 * 
 * @todo 逐步取代平台现有的亿美老短信接口
 */

class SmsApp{
	
	public $tpl = '',			//短信模板
		   $mobiles = '',		//接收短信手机号码
		   $msgContent = '',	//短信内容
		   $sendReturn = '';	//短信发送回报报文
	
	private $_smsClient = null;	//发送短信对象
	
	public function __construct(){
		
		//初始化短信发送接口对象
		require_once(GLOBAL_PLUGIN_PATH.'SmsWebService/nusoaplib/nusoap.php');
		$this->_smsClient = new SmsClient ( YM_SMS_ADDR, 
											YM_SMS_APPID, 
											YM_SMS_AESPWD, 
											YM_SMS_SESSION_KEY, 
											YM_SMS_PROXYHOST, 
											YM_SMS_PROXYPORT, 
											YM_SMS_PROXYUSERNAME, 
											YM_SMS_PROXYPASSWORD, 
											YM_SMS_CONNECT_TIMEOUT, 
											YM_SMS_READ_TIMEOUT );
		$this->_smsClient->setOutgoingEncoding("UTF-8");
	}
	
	/**
	 * 获取毫秒数
	 */
	private function _getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	
	/**
	 * 获取短信模板
	 * @param string $tplName
	 */
	public function getSmsTpl($tplName = ''){
		
		$smsTpl = new SmsTpl();
		$this->tpl = $smsTpl->getSmsTpl($tplName);
		
		return $this;
	}
	
	/**
	 * 创建短信内容
	 * @param array $search
	 * @param array $replace
	 */
	public function bulidContent($search = array(), $replace = array()){
		
		
		$this->msgContent = str_replace($search, $replace, $this->tpl);
		return $this;
	}
	
	/**
	 * 发送短信
	 * @param unknown $mobile 手机号码
	 * @return string 短信发送状态
	 */
	public function SendSMS($mobile){
		
// 		$mobile = array('13348889870', '18519095045', '18701424736');
		$this->mobiles = is_array($mobile) ? $mobile : array($mobile);		//时数组时，批量发送

		$this->sendReturn = $this->_smsClient->sendSMS($this->mobiles, $this->msgContent);
		return $this;
	}
	
	/**
	 * 记录短信发送情况
	 */
	public function logStatus($companyid = 0, $clientid = 0){
		
		//记录短信日志
		$smsLog = new SmsLog($companyid, $clientid);
		$smsLog->logSmsMsg(implode(',', $this->mobiles), $this->msgContent, $this->sendReturn);
	}
	

	/**
	 * 获取版本号
	 * @return string 当前版本
	 */
	public function getVersion(){
	
		return $this->_smsClient->getVersion();
	}
	
	/**
	 * 余额查询（金额）
	 */
	public function getBalance(){
	
		$balance = $this->_smsClient->getBalance();
		return $balance;
	}
	
	/**
	 * 查询每条短信条数
	 */
	public function getEachFee()
	{
		$fee = $this->_smsClient->getEachFee();
		return $fee;
	}
	
	/**
	 * 短信剩余条数
	 * @return numerical 剩余数量
	 */
	public function getBalanceNumbers(){
		
		return $this->getBalance() / $this->getEachFee();
	}
}