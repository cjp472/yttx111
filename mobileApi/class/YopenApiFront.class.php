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
* 1.账户开户			service服务代码[cowpayUserRegister]
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
	
	public function __construct($userID = 0,$companyID = 0){
		
		$this->dhbUserID = $userID;
		
		parent::__construct($companyID);
		
		$this->signInfo = $this->yOpenApiSet->getSignInfo($this->dhbUserID);
	}
	
	 /**
	 * @name 账户开户后激活账户
	 * @author tubo
	 * @since 2015/08/12
	 * @property
	 * 	userId	易极付用户ID
	 *  externalUserId 外部软件商useId
	 *  entrance 设备 0：手机，1：PC
	 */
	public function actAccount($userInfo = array()){
		
		$post = array(
					"userId" => $userInfo['userId'],
					"externalUserId" => $this->getFrontUserID(),
					"entrance" => '0',
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
									$this->companyID, $this->dhbUserID,
									$userInfo['VisitType']
								);
		
		//页面方式提交
	    $this->submitExecute->createHtml($this->commonPost);
	}//actAccount
	
	/**
	 * @name 用户信息查询
	 * @author tubo
	 * @since 2015/09/06
	 * @property
	 * 	userId	易极付用户id
	 */
	public function userInfo($userInfo = array()){
		
		$post = array(
					"userId" => $userInfo['userId'],
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

		//接口方式提交
		return $this->submitExecute->setCurlHandle($this->commonPost);
	}//userInfo
	
		/**
	 * @name 账户开户查询是否有效 
	 * @author tubo
	 * @since 2015/08/7
	 * @property
	 * 	userName	易极付用户名
	 */
	public function checkUserName($userInfo = array()){
		
		$post = array(
					"userName" => $userInfo['username'],
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
		
		//接口方式提交
		return $this->submitExecute->setCurlHandle($this->commonPost);
	}//checkUserName
	
	/**
	 * @name 账户开户 
	 * @author tubo
	 * @since 2015/04/15
	 * @property
	 * 	externalUserId DHB用户ID
	 * 	outOrderNo	开户业务号
	 */
	public function qftSetAccount($userInfo = array()){
		$post = array(
					"partnerShopId" => $this->getFrontUserID(),
					"partnerShopName" => $this->orderNo,
					"clientType" 	 => "MOBILE",
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
									$this->companyID, $this->dhbUserID,
									$userInfo['VisitType']
								);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}
	
	public function setAccount($userInfo = array()){
		
		$post = array(
					"userName"   		 => $userInfo['username'],
					"realName" 	 		 => $userInfo['realName'],
					"gender" 	 		 => substr($userInfo['certNo'], (strlen($userInfo['certNo'])==15 ? -2 : -1), 1) % 2 ? 'MALE' : 'FEMALE',
					"profession" 		 => '职员',
					"regAddress" 		 => $userInfo['regAddress'],
					"certVaildTime" 	 => '2020-10-10',
					"mobile" 	 		 => $userInfo['mobile'],
					"certNo" 	 		 => $userInfo['certNo'],
					"externalUserId" 	 => $this->getFrontUserID(),
				);
		if(!empty($userInfo['email'])){
			$email = array("email"   	 => $userInfo['email'],);
			$post  = array_merge($post,$email);
		}
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
		
		//接口方式提交
		return $this->submitExecute->setCurlHandle($this->commonPost);
	}//setAccount
	
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
		
		if(empty($this->signInfo['YapiUserId'])) throw new Exception('买方ID不能为空');
		
		if(empty($this->apiSignNO)) throw new Exception('卖方ID不能为空');
	
		//本模块需要添加的数据
		 $tradeName = '医统天下BMB平台创建订单';
		 
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
								$this->dhbUserID,
								$orderInfo['VisitType']
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
				"merchOrderNo" => $this->orderNo,		//买家(易极付)用户id
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

// 		print_r($this->commonPost);
// 		exit;
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}
	
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
	
// 	/**
// 	 * @name 订单支付
// 	 * @author wanjun
// 	 * @since 2015/04/28
// 	 */
// 	public function payOrder($orderInfo = array()){
		
// 		if(empty($orderInfo) && OPEN_DEBUG) throw new Exception('订单数据不能为空');
	
// 		//本模块需要添加的数据
// 		$ifYuFu = array(
// 					array(
// 							"title" => $this->orderNo
// 						)
// 				);
		
// 		 if(empty($orderInfo['cartdetail'])){
// 		 	$goodsClauses = $ifYuFu;
// 		 	$tradeName = '订货宝预付款';
// 		 }else{
// 		 	$goodsClauses = $orderInfo['cartdetail'];
// 		 	$tradeName = '订货宝订货款项';
// 		 }
		 
// 		$post = array(
// 				"outTradeTitle"	=> $tradeName,
// 				"tradeChannel"  => 'CASHIER_MOBILE',
// 				"payeeUserId"	=> $this->apiSignNO,				//卖方ID，签约ID,$this->_apiMerchantID
// 				"payerUserId"	=> $this->signInfo['YapiUserId'],	//买方ID ,$this->_signInfo['YapiUserId']
// 				"outPayerShopId"=> $this->getFrontUserID(),			//买方外部会员ID 
// 				"outPayeeShopId"=> $this->getCompanyID(),//$this->signAccount			//卖方外部会员ID 
// 				"money"			=> $orderInfo['orderAmount'],
// 				"moneyReal"		=> $orderInfo['orderAmount'],
// 				"origin"		=> 'MOBILE',
// 				"outOrderNo"	=> $this->orderNo,
// 				"goodList"		=> html_entity_decode(urldecode(json_encode($goodsClauses)), ENT_NOQUOTES, "utf-8")
// 		);
		
// 		//添加本模块服务代码
// 		$to = array_merge($this->predefine[__FUNCTION__], $post);
// 		$this->bulidSet($to);

// 		//记录映射关系
// 		$this->yOpenApiSet->SetMap(
// 								$this->orderNo,
// 								$orderInfo['osn'], 
// 								$orderInfo['orderNo'], 
// 								$this->commonPost['sign'],
// 								$orderInfo['total'],
// 								$this->predefine[__FUNCTION__]['service'],
// 								$this->companyID,
// 								$this->dhbUserID,
// 								$orderInfo['VisitType']
// 							);
		
// 		//HTML方式提交
// 		$this->submitExecute->createHtml($this->commonPost);
// 	}
	
	
	public function goLogin($userInfo = array()){
// 		$post = array(
// 					"partnerShopId" => $this->getFrontUserID(),
// 					"clientType" 	 => "MOBILE",
// 				);
		
		$post = array(
				"userId" => $this->signInfo['YapiUserId'],
				"termnalType" => 'MOBILE'
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
									$this->companyID, $this->dhbUserID,
									$userInfo['VisitType']
								);
		
// 		print_r($this->commonPost);
// 		exit;
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}
	
	/**
	 * 用户信息查询
	 * @author tubo
	 * @param string $userId
	 * @param string $userName
	 * @since 2016-01-28
	 * @return 
	 */
	public function certifyStatus(){

	    $post = array(
	        "userName"	=> $this->signInfo['YapiuserName'],
	    );
	    
	    //添加本模块服务代码
	    $to = array_merge($this->predefine[__FUNCTION__], $post);
	    
	    $this->bulidSet($to);
	    
	    //同步方式提交
	    return $this->submitExecute->setCurlHandle($this->commonPost);
	    
	}//END certifyStatus
	
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