<?php

!defined('SYSTEM_ACCESS') && exit('Access deny!');

/**
 * class for pay of yi zhi fu(openApi)
*
* PHP version 5
*
* @category  PHP
* @author    WanJun <316174705@qq.com>
* @copyright 2015 Rsung
* @version   1.0
* @date	  2015/04/15
* debug_backtrace
*/

class YOpenApi{
	
	private 	$_apiSubmitUrl		= YAPI_SUBMIT_URL,		//数据 提交 地址
				$_apiProtocol		= YAPI_PROTOCOL,		//传输协议
				$_apiSignType		= YAPI_SIGN_TYPE,		//签名加密方式
				$_apiVersion		= YAPI_VERSION;			//接口版本号
	public 		$orderNo			= '';					//业务号
	
	protected 	$frontReturnUrl		= YAPI_FRONT_RETURN_URL,		//数据 返回 地址(页面,经销商)
				$frontNotifyUrl		= YAPI_FRONT_NOTIFY_URL,		//数据 返回 地址(异步,经销商)
				$backendReturnUrl	= YAPI_BACKEND_RETURN_URL,		//数据 返回 地址(页面,供应商)
				$backendNotifyUrl	= YAPI_BACKEND_NOTIFY_URL,		//数据 返回 地址(异步,供应商)
				$apiSecurityKey		= YAPI_SECURITYKEY;				//安全码
	
	protected	$dhbUserID			= '',							//待操作用户ID
				$companyID 			= null,							//企业在DHB中的ID
				$signInfo			= '',							//已在易支付签约用户信息
				$apiSignNO			= '',							//商户签约NO
				$signAccount		= '',							//商户签约用户名
				$apiPartnerId		= YAPI_PARTNERID;				//商户ID，类似merchantid
			
	protected 	$submitExecute 		= null,							//执行方式对象，
				$netGetWay 			= null,							//网关对象
			  	$yOpenApiSet		= null;							//关系映射对象
			  
	protected 	$backgroundType		= '';									//本次操作是经销商还是供应商[主要是通知地址]
	
	public 		$commonPost 		= array();								//公共数据提交
	protected 	$predefine			= array(								//每个模块对应的服务代码
										"setAccount"	=> array("service" => "cowpayUserRegisterV2"),
										"setFication"	=> array("service" => "realNameAuthorize"),
										"changePWD"		=> array("service" => "modifyPayAndLoginPwd"),
										"getPWD"		=> array("service" => "findPayPassword"),
										"changeAccount"	=> array("service" => "mcfmUserRegister"),
										"changeMobile"	=> array("service" => "modifyBindMobile"),
										"changeEmail"	=> array("service" => "modifyEmail"),
										"getMoney"		=> array("service" => "yjfWithdraw"),
										"getSear"		=> array("service" => "queryCowpayWithdraw"),
										"carGid"		=> array("service" => "mcfmUserRegister"),
										"payOrder"		=> array("service" => "commonTradePay"),
										"createOrder"	=> array("service" => "createTradeOrder"),
										"setBind"		=> array("service" => "signmanybank"),
										"managerBind"	=> array("service" => "signPactManager"),
										"checkUserName"	=> array("service" => "checkUserNameExistOrNot"),
										"actAccount"	=> array("service" => "modifyPw2Cowpay"),
										"userInfo"	    => array("service" => "userInfoQuery"),
// 										"goLogin"		=> array("service" => "qftGoLogin"),
										"goLogin"		=> array("service" => "wallet"),
// 										"payOrder"		=> array("service" => "qftIntegratedPayment"),
										"payOrder"		=> array("service" => "commandPayTradeCreatePay"),
										"qftSetAccount"	=> array("service" => "qftDealerRegister"),
										"certifyStatus"	=> array("service" => "userInfoQuery"),//wanjun 查询用户信息
										"ppmNewRuleRegisterUser"	=> array("service" => "ppmNewRuleRegisterUser"),//wangd 2017-12-09 与PC端同步：经销商在线开户
										"commandPayConfirm"	=> array("service" => "commandPayConfirm"),//wanjun 担保交易确认打款
									);
	
	/**
	 * @name 初始化模块数据
	 * @author wanjun
	 * @since 2015/04/15
	 * @param int $companyID 供应商在DHB中的ID
	 * @param string $backgroundType 本次操作是经销商还是供应商[主要是通知地址]
	 * @todo 需要增加参数错误时的异常处理
	 */
	public function __construct($companyID = 0, $backgroundType = 'front'){
		
		$this->companyID		= $companyID;
		$this->backgroundType	= $backgroundType;
		
		//初始化业务号
		$this->createID();
		
		//初始化已签约用户ID
		$this->yOpenApiSet		= new YOpenApiSet();
		
		//初始化数据提交对象
		$this->submitExecute 	= new SubmitExecute($this->_apiSubmitUrl);
		
	}
	
	///////////////////////////////////////////////////////// 内部处理 ////////////////////////////////////////////////////////
	
	/**
	 * @name 设置当前账户网关
	 * @author wanjun
	 */
	public function setGetway($getwayID = ''){
// 		if($getwayID == '' && OPEN_DEBUG) throw new Exception('您没有此项的操作权限...');

		//初始化网关对象
		$this->netGetWay		= new NetGetWay();
		//获取网关信息
		$accinfo = $this->netGetWay->showGetway('yijifu', $this->companyID, $getwayID);
		
		$this->apiSignNO		= trim($accinfo['SignNO']);
		$this->signAccount		= trim($accinfo['SignAccount']);
		
		return $this;
	}
	
	/**
	 * @name 数据合并按照ASCII排序
	 * @author wanjun
	 * @since 2015/04/15
	 * @param
	 * 	$set array 待合并的数据
	 * @return array 已合并的数据
	 */
	protected function bulidSet($set = array()){
		
		$toVersion2 = array("qftDealerRegister", "commandPayTradeCreatePay");
		if(in_array($set['service'], $toVersion2)){
			$version = '2.0';
		}else{
			$version = $this->_apiVersion;
		}

		$this->commonPost = array_merge(
							array(
									"protocol"	=> $this->_apiProtocol,
									"version"	=> $version,
									"partnerId"	=> $this->apiPartnerId,
									"signType"	=> $this->_apiSignType,
									"returnUrl" => $this->backgroundType == 'front' ? $this->frontReturnUrl : $this->backendReturnUrl,
									"notifyUrl" => $this->backgroundType == 'front' ? $this->frontNotifyUrl : $this->backendNotifyUrl,
									"orderNo" 	=> $this->orderNo,
							)
							, $set);
		
		ksort($this->commonPost);
		
		//生成签名字符串
		$this->sign($this->commonPost);
	}// bulidSet
	
	/**
	 * @name 支付对接业务号
	 * @author wanjun
	 * @return $id string 20位长度的字符串
	 */
	protected function createID(){
		
		list($usec, $sec) = explode(" ", microtime());
		
		$this->orderNo = YAPI_ORDER_PREFIX.date('YmdHis').substr($usec, 8);
		
		//在返回字段中只有这个是每次都存在的资料，SESSION保存后用于异步通知的检查
		$_SESSION['YJForderNo'] = $this->orderNo;
	}
	
	/**
	 * @name 签名
	 * @author wanjun
	 * @since 2015/04/15
	 * @param
	 * 	$set array 待合并的数据
	 * @return array 已合并的数据
	 */
	public function sign($signInfo = array()){

		//组合数据
		$preQuery = $this->_buildPairs($signInfo);

		//添加安全码
		$preQuery .= $this->apiSecurityKey;
		
		//生成加密字符串
		switch($this->_apiSignType){
			case 'MD5':
					$signString = md5($preQuery);
				break;
		}
		//添加签名字符串
		$this->commonPost['sign'] = $signString;
	}
	
	/**
	 * @name 将参数组装成post字符串
	 * @author wanjun
	 * @param 
	 * 	$param array 待转换的数据
	 * @return string
	 */
	public static function _buildPairs(array $param){
		$post = array();
		foreach ($param as $key => $value) {
			$value = trim($value);
			$post[] = $key . '=' . mb_convert_encoding($value, "utf-8", "auto");
		}

		return implode("&", $post);
	}
	
		/**
	 * @name 当前环境(正式环境还是体验环境)
	 * @author wanjun
	 * @uses 
	 * tc是体验系统的经销商端;tm是体验系统的供应商端
	 */
	protected function myPrefix(){
		
		$chost = explode(".", $_SERVER['HTTP_HOST']);
		$env = array_shift($chost);
		
		if($this->backgroundType == 'front'){//经销商端
			$prefix = $env == 'c' ? YAPI_ORDER_PREFIX : YAPI_ORDER_PREFIX_TEST;
		}else{//供应商端
			$prefix = $env == 'm' ? YAPI_ORDER_PREFIX_BACKEND : YAPI_ORDER_PREFIX_BACKEND_TEST;
		}
		
		return $prefix;
	}//END myPrefix
	
	
}// END YOpenApi




?>