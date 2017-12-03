<?php

!defined('SYSTEM_ACCESS') && exit('Access deny!');
/**
 * class for merchant api return 
*
* PHP version 5
*
* @category  PHP
* @author    WanJun <316174705@qq.com>
* @copyright 2015 Rsung
* @version   1.0
* @date	  2015/04/16
*
*/

class Merchant{
	
	private $_merchantInfo	= array();	//回推商户数据
	private $_kLog			= '';		//日志对象
	
	public function __construct($merchantInfo = array()){
		
		$this->_merchantInfo = $merchantInfo;
		
		//初始化日志记录
		$this->_kLog = new KLogger(YOPENAPI_MERCHANT_LOG_PATH, KLogger::INFO);
		$this->_kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
		$this->_kLog->logInfo(urldecode(http_build_query($this->_merchantInfo)));
		
	}

	public function writeMerchantInDHB(){
		
		global $getway_account_type;
		
		//转换成DHB的账户类型[personal, company]
		$accountType = $this->_merchantInfo['userType'] == 'P'  ? 'personal' : 'company';
		
		$this->_merchantInfo['externalUserId'] = str_replace('dhb0m', '', $this->_merchantInfo['externalUserId']);
		
		$reviMerchantID = $this->revivification();
		
		if($reviMerchantID > 0){//供应商ID正常
			
			//验证该供应商是否已开户
			$NetGetWay = new NetGetWay();
			$netInfo = $NetGetWay->getInfoByAccount('yijifu', $reviMerchantID, trim($this->_merchantInfo['userId']), trim($this->_merchantInfo['userName']));
			
			//获取供应商信息
			$merchant = new MerchantInfo();
			$merchantInfo = $merchant->getMerchantInfo($reviMerchantID);
			
			if(count($netInfo)){//该供应商已开户
				$rp = array('{COMPANYSIGNED}', '{MERCHANTID}', '{USERTYPE}');
				$re = array($merchantInfo['CompanySigned'], $reviMerchantID, $getway_account_type[$accountType]);
				$message = str_replace($rp, $re, getSmsTpl('YJFOpenApiReapet'));

			}else{//数据正常，开始执行写入操作
				$getWayID = $NetGetWay->storeGetway(array(
													"CompanyID"			=> $reviMerchantID,
													"SignNO"			=> $this->_merchantInfo['userId'],
													"SignAccount"		=> $this->_merchantInfo['userName'],
													"AccountType"		=> $this->_merchantInfo['userType']
												));
				if($getWayID){
					$rp = array('{COMPANYSIGNED}', '{MERCHANTID}', '{USERTYPE}');
					$re = array($merchantInfo['CompanySigned'], $reviMerchantID, $getway_account_type[$accountType]);
					$message = str_replace($rp, $re, getSmsTpl('YJFOpenApiSuccess'));
				}else{
					$rp = array('{COMPANYSIGNED}', '{MERCHANTID}', '{USERTYPE}');
					$re = array($merchantInfo['CompanySigned'], $reviMerchantID, $getway_account_type[$accountType]);
					$message = str_replace($rp, $re, getSmsTpl('YJFOpenApiFailed'));
				}
												
			}
			
		}else{//供应商ID异常，并发送短信
			$rp = array('{COMPANYSIGNED}', '{MERCHANTID}', '{USERTYPE}');
			$re = array($merchantInfo['CompanySigned'], $reviMerchantID, $getway_account_type[$accountType]);
			$message = str_replace($rp, $re, getSmsTpl('YJFOpenApiLessZero'));
		}
		
		//短信接收人员
		$_SESSION['uinfo']['ucompany']	= 1;			//默认使用我们自己的ID
		$_SESSION['uinfo']['username']	= 'seekfor';	//默认使用seekfor
		
		//获取我们公司的简称
		$merchant = new MerchantInfo();
		$merchantInfo = $merchant->getMerchantInfo(1);
		$message = "【" . $merchantInfo['CompanySigned']."】" . $message;

		$mobiles = explode(",", YAPI_RECEIVE_PHONE);
		foreach ($mobiles as $mv){
			$mv && sms::send_sms($mv, $message, 0);
		}
		
	}// END writeMerchantInDHB
	
	/**
	 * 还原供应商ID，默认加上了一个数字：GLOBAL_NUMERIC_FIXED
	 *
	 * @author wanjun
	 * @since 2015/07/02
	 * @return int 还原后的供应商ID
	 */
	private function revivification(){
		
		return intval($this->_merchantInfo['externalUserId']) - GLOBAL_NUMERIC_FIXED;
	}// END revivification
	
	
	
	

}//END Merchant


?>