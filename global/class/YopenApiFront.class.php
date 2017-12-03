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
*
* 易支付 经销商端 数据处理程序，各服务代码如下：
* 
* 1.账户开户			service服务代码[qftDealerRegister]//[cowpayUserRegister]
* 2.实名认证			service服务代码[realNameAuthorize]
* 3.修改支付密码		service服务代码[findPayPassword]
* 4.找回支付密码		service服务代码[findPayPassword]
* 5.修改绑定手机		service服务代码
* 6.修改邮箱			service服务代码[即:跳转收银台付款？代码：commonTradePay]
* 7.账户修改			service服务代码[queryCowpayWithdraw]
* 8.绑定银行卡		service服务代码[signmanybank]
* 
* 易支付 移动端 数据处理程序，各服务代码如下：
* 1.创建订单			service服务代码[createTradeOrder]
* debug_backtrace
*/

class YopenApiFront extends YOpenApi{
	
	public function __construct($userID = 0,$isSDK = ''){

		$this->dhbUserID = $userID;
		
		if( !empty($_SESSION['uinfo']['ucompany']) ){
			$company_id = $_SESSION['uinfo']['ucompany'];
			$type = 'backend';
		}else{
			$company_id = $_SESSION['ucc']['CompanyID'];
			$type = 'front';
		}

		parent::__construct($company_id,$type,$isSDK);
		
		$this->signInfo = $this->yOpenApiSet->getSignInfo($this->dhbUserID);
	}
	
		/**
	 * @name 易极付跳转登录 
	 * @author tubo
	 * @since 2015/10/21
	 * @property
	 * 	partnerShopId 易极付平台商用户ID
	 * 	clientType	客户端类型
	 */
	public function wallet(){
		
		 $post = array(
	        "userId" => $this->signInfo['YapiUserId'],
	    );
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap(
									$this->orderNo, 
									'', 
									'', 
									$this->commonPost['sign'], 
									0, 
									$this->predefine[__FUNCTION__]['service'], 
									$this->companyID, $this->dhbUserID
								);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}
	
	
	
	/**
	 * @name 账户开户 
	 * @author wanjun
	 * @since 2015/04/15
	 * @property
	 * 	externalUserId DHB用户ID
	 * 	outOrderNo	开户业务号
	 */
	public function setAccount(){
		
		//验证是否已开户
		$YOpenApiSet = new YOpenApiSet();
		$myYJF = $YOpenApiSet->getSignInfo(intval($_SESSION['cc']['cid']));
		if(count($myYJF)){
			header("location:./my.php");
			exit;
		}
		
		$post = array(
					"partnerShopId" => $this->getFrontUserID(),
					"partnerShopName" 	 => $this->orderNo,
					"clientType" 	 => "PC",
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap(
									$this->orderNo, 
									'', 
									'', 
									$this->commonPost['sign'], 
									0, 
									$this->predefine[__FUNCTION__]['service'], 
									$this->companyID, $this->dhbUserID
								);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}
/*	public function setAccount(){
		
		//验证是否已开户
		$YOpenApiSet = new YOpenApiSet();
		$myYJF = $YOpenApiSet->getSignInfo(intval($_SESSION['cc']['cid']));
		if(count($myYJF)){
			header("location:./my.php");
			exit;
		}
		
		$post = array(
					"externalUserId" => $this->getFrontUserID(),
					"outOrderNo" 	 => $this->orderNo,
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap(
									$this->orderNo, 
									'', 
									'', 
									$this->commonPost['sign'], 
									0, 
									$this->predefine[__FUNCTION__]['service'], 
									$this->companyID, $this->dhbUserID
								);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}//setAccount */
	
	/**
	 * 绑定银行卡
	 *
	 * @author wanjun
	 * @since 2015/07/23
	 */
	public function setBind(){
		//本模块需要添加的数据
		$post = array(
					"purpose"	=> 'DEDUCT',	//DEDUCT：代扣；WITHDRAW：提现
					"userId"	=> $this->signInfo['YapiUserId'],
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);

		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, $this->dhbUserID);
	
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}
	
	public function managerBind(){
		
		//本模块需要添加的数据
		$post = array(
					"userId"	=> $this->signInfo['YapiUserId'],
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);

		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, $this->dhbUserID);
	
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}// END managerBind
	
	/**
	 * @name 实名认证
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function setFication(){
	
		//本模块需要添加的数据
		$post = array(
					"externalUserId" => $this->getFrontUserID(),
					"userId" 		 => $this->signInfo['YapiUserId'],
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, $this->dhbUserID);
	
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}//setFication
	
	/**
	 * @name 修改支付密码
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function changePWD(){
	
		//本模块需要添加的数据
		$post = array(
					"passwordType" 	=> 'payPwd',
					"requestTime" 	=> date('YmdHis'),
					"userId"		=> $this->signInfo['YapiUserId'],
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, $this->dhbUserID);
	
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}//changePWD
	
	/**
	 * @name 找回支付密码
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function getPWD(){
	
		//本模块需要添加的数据
		$post = array(
					"system" 	=> 'qft',
					"userName"	=> $this->signInfo['YapiuserName'],
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, $this->dhbUserID);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}//getPWD
	
	/**
	 * @name 修改绑定手机
	 * @author wanjun
	 * @since 2015/04/27
	 */
	public function changeMobile(){
	
		//本模块需要添加的数据
		$post = array(
				"system" 	=> 'qft',
				"userName"	=> $this->signInfo['YapiuserName'],
		);
	
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, $this->dhbUserID);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	
	}//changeMobile
	
	/**
	 * @name 修改邮箱
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function changeEmail(){
	
		//本模块需要添加的数据
		$post = array(
				"system" 	=> 'qft',
				"userName"	=> $this->signInfo['YapiuserName'],
		);
	
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, $this->dhbUserID);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	
	}//changeEmail
	
	/**
	 * @name 移动端订单支付
	 * @author tubo
	 * @since 2015/07/21
	 */
	public function createOrder($orderInfo = array()){

		if(empty($orderInfo) && OPEN_DEBUG) throw new Exception('订单数据不能为空');
	
		//本模块需要添加的数据
		 $tradeName = '医统天下创建订单';
		 
		$post = array(
				"tradeName"		=> $tradeName,
				"sellerUserId"	=> $this->apiSignNO,				//卖方ID，签约ID,$this->_apiMerchantID
				"buyerUserId" 	=> $this->signInfo['YapiUserId'],	//买方ID
				"tradeAmount"	=> $orderInfo['orderAmount'],
				"orderType"		=> '2',
				"currency"		=> 'CNY',
				"requestType"	=> '2',
				"kjTrade"       => '0'
				//"goodsClauses"	=> html_entity_decode(urldecode(json_encode($goodsClauses)), ENT_NOQUOTES, "utf-8")
		);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);

		//记录映射关系
		$this->yOpenApiSet->SetMap(
								$this->orderNo,
								$orderInfo['osn'], 
								$orderInfo['orderNo'], 
								$this->commonPost['sign'],
								$orderInfo['total'],
								$this->predefine[__FUNCTION__]['service'],
								$this->companyID,
								$this->dhbUserID
							);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}//createOrder
	
	/**
	 * @name 订单支付
	 * @author wanjun
	 * @since 2015/04/28
	 */
	public function payOrder($orderInfo = array()){
		
		if(empty($orderInfo) && OPEN_DEBUG) throw new Exception('订单数据不能为空');
	
		//本模块需要添加的数据
		$ifYuFu = array(
					array(
							"title" => $this->orderNo
						)
				);
		
		 if(empty($orderInfo['cartdetail'])){
		 	$goodsClauses = $ifYuFu;
		 	$tradeName = '医统天下BMB平台预付款';
		 }else{
		 	$goodsClauses = $orderInfo['cartdetail'];
		 	$tradeName = '医统天下BMB平台订货款项';
		 }
		 
		$post = array(
				"outUserId" => $this->getFrontUserID(),			//买家外部会员id 平台的
				"buyerUserId" => $this->signInfo['YapiUserId'],		//买家(易极付)用户id
				"merchOrderNo" => $this->orderNo,
				"commandPayOrders" => json_encode(array(array(
						"merchOrderNo"	=> $this->orderNo,
						"sellerUserId"	=> $this->apiSignNO,
						"tradeName"		=> $tradeName,
						"tradeAmount"	=> $orderInfo['orderAmount'],
						"goodsName"		=> $this->orderNo
						)), JSON_UNESCAPED_UNICODE)
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		
		$this->bulidSet($to);

		//记录映射关系
		$this->yOpenApiSet->SetMap(
								$this->orderNo,
								$orderInfo['osn'], 
								$orderInfo['orderNo'], 
								$this->commonPost['sign'],
								$orderInfo['orderAmount'],
								$this->predefine[__FUNCTION__]['service'],
								$this->companyID,
								$this->dhbUserID
							);
		
// 		debug($this->commonPost, 1);
// 		print_r($this->commonPost);
// 		exit;
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}
	
	
	//注释原因：以下接口是即时到账的程序段
// 	/**
// 	 * @name 订单支付
// 	 * @author wanjun
// 	 * @since 2015/04/28
// 	 */
// 	public function payOrder($orderInfo = array()){
	
// 		if(empty($orderInfo) && OPEN_DEBUG) throw new Exception('订单数据不能为空');
	
// 		//本模块需要添加的数据
// 		$ifYuFu = array(
// 				array(
// 						"title" => $this->orderNo
// 				)
// 		);
	
// 		if(empty($orderInfo['cartdetail'])){
// 			$goodsClauses = $ifYuFu;
// 			$tradeName = '医统天下BMB平台预付款';
// 		}else{
// 			$goodsClauses = $orderInfo['cartdetail'];
// 			$tradeName = '医统天下BMB平台订货款项';
// 		}
			
// 		$post = array(
// 				"outTradeTitle"	=> $tradeName,
// 				"tradeChannel"  => 'CASHIER_PC',
// 				"payeeUserId"	=> $this->apiSignNO,				//卖方ID，签约ID,$this->_apiMerchantID
// 				"payerUserId"	=> $this->signInfo['YapiUserId'],	//买方ID ,$this->_signInfo['YapiUserId']
// 				"outPayerShopId"=> $this->getFrontUserID(),			//买方外部会员ID
// 				"outPayeeShopId"=> $this->getCompanyID(),				//卖方外部会员ID   //signAccount,
// 				"money"			=> $orderInfo['orderAmount'],
// 				"moneyReal"		=> $orderInfo['orderAmount'],
// 				"outOrderNo"	=> $this->orderNo,
// 				"goodList"		=> html_entity_decode(urldecode(json_encode($goodsClauses)), ENT_NOQUOTES, "utf-8")
// 		);
	
// 		//添加本模块服务代码
// 		$to = array_merge($this->predefine[__FUNCTION__], $post);
// 		$this->bulidSet($to);
	
// 		//记录映射关系
// 		$this->yOpenApiSet->SetMap(
// 				$this->orderNo,
// 				$orderInfo['osn'],
// 				$orderInfo['orderNo'],
// 				$this->commonPost['sign'],
// 				$orderInfo['total'],
// 				$this->predefine[__FUNCTION__]['service'],
// 				$this->companyID,
// 				$this->dhbUserID
// 		);
	
// 		// 		debug($this->commonPost, 1);
// 		//HTML方式提交
// 		$this->submitExecute->createHtml($this->commonPost);
// 	}

	
	/**
	 * 担保交易确认打款(临时使用)
	 * @author wanjun
	 * @date 2016-12-23
	 */
	public function commandPayConfirm($tradeNo = ''){
		
		if(empty($tradeNo)) return false;
		
		$post = array(
				"tradeNo"	=> $tradeNo,
				"merchOrderNo"	=> $this->orderNo,
		);
		 
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		 
		$this->bulidSet($to);
		
		//同步方式提交
		return $this->submitExecute->setCurlHandle($this->commonPost);
		
	}
	
	
	/**
	 * @name 生成SDK流水号
	 * @author tubo
	 * @since 2015/12/28
	 */
	public function paySDK($orderInfo = array()){
		
		if(empty($orderInfo) && OPEN_DEBUG) throw new Exception('订单数据不能为空');
	
		//本模块需要添加的数据
		$ifYuFu = array(
					array(
							"title" => $this->orderNo
						)
				);
		
		 if(empty($orderInfo['cartdetail'])){
		 	$goodsClauses = $ifYuFu;
		 	$tradeName = '医统天下预付款';
		 }else{
		 	$goodsClauses = $orderInfo['cartdetail'];
		 	$tradeName = '医统天下订货款项';
		 }
		 
		$post = array(
				"outTradeTitle"	=> $tradeName,
				"tradeChannel"  => 'CASHIER_POS',
				"payeeUserId"	=> $this->apiSignNO,				//卖方ID，签约ID,$this->_apiMerchantID
				"payerUserId"	=> $this->signInfo['YapiUserId'],	//买方ID ,$this->_signInfo['YapiUserId']
				"outPayerShopId"=> $this->getFrontUserID(),			//买方外部会员ID 
				"outPayeeShopId"=> $this->getCompanyID(),				//卖方外部会员ID   //signAccount,
				"money"			=> $orderInfo['orderAmount'],
				"moneyReal"		=> $orderInfo['orderAmount'],
				"outOrderNo"	=> $this->orderNo,
				"terminalBelongs"=>'SUPPLIER',
				"goodList"		=> html_entity_decode(urldecode(json_encode($goodsClauses)), ENT_NOQUOTES, "utf-8")
		);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);

		//记录映射关系
		$this->yOpenApiSet->SetMap(
								$this->orderNo,
								$orderInfo['osn'], 
								$orderInfo['orderNo'], 
								$this->commonPost['sign'],
								$orderInfo['total'],
								$this->predefine[__FUNCTION__]['service'],
								$this->companyID,
								$this->dhbUserID
							);
		
		//接口方式提交setCurlHandle
		$result = $this->submitExecute->setCurlHandle($this->commonPost);
		//tubo 临时处理
		if( $result['resultCode'] == 'EXECUTE_SUCCESS' ){
			$apiInfo['ext1']				= $this->companyID.'_'.$this->dhbUserID;
			$apiInfo['ext2']				= $orderInfo['ext2']; 
			$apiInfo['tradeNo']             = $result['tradeNo'];
			//修改订单状态
			$NetPayToOrder = new NetPayToOrder();
			$NetPayToOrder->updateSDKOrderStatus($apiInfo);
		}
		//end
	}
	
	/** 
	 * 发送post请求 
	 * @param string $url 请求地址 
	 * @param array $post_data post键值对数据 
	 * @return string 
	 */ 
	/* 
	public function send_post($url, $post_data) {  
		  
	  $postdata = http_build_query($post_data);  
	  $options = array(  
	    'http' => array(  
	      'method' => 'POST',  
	      'header' => 'Content-type:application/x-www-form-urlencoded',  
	      'content' => $postdata,  
	      'timeout' => 2 * 60 // 超时时间（单位:s）  
	    )  
	  );  
	  $context = stream_context_create($options);  
	  $result = file_get_contents($url, false, $context);  
	 
	  return $result;  
	}*/
	
	/*public function payOrder($orderInfo = array()){
		
		if(empty($orderInfo) && OPEN_DEBUG) throw new Exception('订单数据不能为空');
	
		//本模块需要添加的数据
		$ifYuFu = array(
					array(
							"name" => $this->orderNo
						)
				);
		
		 if(empty($orderInfo['cartdetail'])){
		 	$goodsClauses = $ifYuFu;
		 	$tradeName = '医统天下预付款';
		 }else{
		 	$goodsClauses = $orderInfo['cartdetail'];
		 	$tradeName = '医统天下订货款项';
		 }
		 
		$post = array(
				"tradeName"		=> $tradeName,
				"sellerUserId"	=> $this->apiSignNO,				//卖方ID，签约ID,$this->_apiMerchantID
				"payerUserId"	=> $this->signInfo['YapiUserId'],	//买方ID ,$this->_signInfo['YapiUserId']
				"buyerUserId" 	=> $this->signInfo['YapiUserId'],	//买方ID
				"tradeAmount"	=> $orderInfo['orderAmount'],
				"outOrderNo"	=> $this->orderNo,
				"goodsClauses"	=> html_entity_decode(urldecode(json_encode($goodsClauses)), ENT_NOQUOTES, "utf-8")
		);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);

		//记录映射关系
		$this->yOpenApiSet->SetMap(
								$this->orderNo,
								$orderInfo['osn'], 
								$orderInfo['orderNo'], 
								$this->commonPost['sign'],
								$orderInfo['total'],
								$this->predefine[__FUNCTION__]['service'],
								$this->companyID,
								$this->dhbUserID
							);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}//payOrder */
	
	/**
	 * 用户信息查询
	 * @author wanjun
	 * @param string $userId
	 * @param string $userName
	 * @since 2016-01-22
	 * @return 
	 */
	public function certifyStatus($userName = ''){
	    
	    if(empty($userName)) return array();

	    $post = array(
	        "userName"	=> $userName,
	    );
	    
	    //添加本模块服务代码
	    $to = array_merge($this->predefine[__FUNCTION__], $post);
	    
	    $this->bulidSet($to);
	    
	    //同步方式提交
	    return $this->submitExecute->setCurlHandle($this->commonPost);
	    
	}//END certifyStatus
	
	public function ppmNewRuleRegisterUser(){
		
		$oClient = new ClientInfo();
		$cinfo = $oClient->getClientInfo($this->companyID, $this->dhbUserID);
		
		if(empty($cinfo['ClientMobile'])) return array('status' => 'error', 'message' => '请在用户中心填写真实手机号码');
		
		//这里是只有新乡爱森的进行处理
		$post = array(
				"userName"			=> $cinfo['ClientCompany'] == 523 ? 'et'.$cinfo['ClientMobile'] : $cinfo['ClientMobile'],
				"registerUserType"	=> 'PERSONAL',
				"mobile"			=> $cinfo['ClientMobile'],
				"merchOrderNo"		=> $this->orderNo
		);
		 
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		 
		$this->bulidSet($to);
		
		//同步方式提交
		$excute = $this->submitExecute->setCurlHandle($this->commonPost);
		if($excute['resultCode'] == 'EXECUTE_SUCCESS'){
			
			$params = array(
					'dhbUserid'		=> $this->dhbUserID,
					'YapiUserId'	=> $excute['userId'],
					'YapiuserName'	=> $post['userName'],
					'YapiUserType'	=> $post['registerUserType'],
			);
			$this->yOpenApiSet->UserRegister($params);
			
			$return = array('status' => 'success', 'message' => $excute['resultMessage']);
		}else{
			$return = array('status' => 'error', 'message' => $excute['resultMessage']);
		}
		
		return $return;
	}
	
	/**
	 * 为易极付准备 DHB 用户ID
	 *
	 * @author wanjun
	 * @return string 组合后可用于易极付的ID
	 */
	private function getFrontUserID(){
		return YAPI_ORDER_PREFIX.str_pad($this->dhbUserID, 8, '0', STR_PAD_LEFT);
	}//END getFrontUserID
	
	/**
	 * 为易极付准备 DHB 供应商ID
	 *
	 * @author wanjun
	 * @return string 组合后可用于易极付的ID
	 */
	private function getCompanyID(){
		//加上了偏移量
		$type = 'm';
		//return 'dhb01';
		return YAPI_ORDER_PREFIX_BACKEND.str_pad(($this->companyID + GLOBAL_NUMERIC_FIXED), 7, '0', STR_PAD_LEFT);
		
	}//END getCompanyID
	
	
}//EOC 