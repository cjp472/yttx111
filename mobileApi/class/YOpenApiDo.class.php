<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//易支付返回信息处理

class YOpenApiDo extends YOpenApi {
	
	private $_apiModule = null;		//数据模型处理
	private $_apiInfo	= array();	//接收数据
	
	//各项业务提示信息
	private $_message = array(
							'cowpayUserRegisterV2'	=> '恭喜您，开户成功',				//开户
							'commonTradePay'		=> '恭喜您，支付成功',				//支付
							'commonTradePay_Sign'	=> '恭喜您，签约成功',				//签约银行卡
							'commonTradePay_Wait'	=> '已完成支付，系统确认中...',				//款项确认中
							'yjfWithdraw'			=> '恭喜您，提现成功',				//提现
							'findPayPassword'		=> '恭喜您，成功找回支付密码',		//找回支付密码
							'modifyBindMobile'		=> '恭喜您，成功绑定手机',			//修改绑定手机
							'modifyEmail'			=> '恭喜您，绑定邮箱操作成功，请根据邮件提示进行下一步操作',			//修改绑定邮箱
							'pay'					=> '恭喜您，支付成功',				//手机支付 对应提交的createTradeOrder
							'pay_Fail'				=> '很抱歉，支付失败',				//手机签约银行卡 对应提交的createTradeOrder
							'pay_Wait' 				=> '已完成支付，系统确认中...',		//手机支付款项确认中 对应提交的createTradeOrder
							'modifyPw2Cowpay'		=> '恭喜你，设置支付密码成功',		//手机跳转设置支付密码页面
							'userInfoQuery'		    => '激活帐户成功',		        //手机激活帐户
							'qftDealerRegister'		=> '恭喜您，开户成功',				//开户  新接口
							'qftIntegratedPayment'	=> '恭喜您，支付成功',				//支付  新接口
							'qftGoLogin'			=> '恭喜您，跳转成功',				//跳转  新接口
						);
	
	
	
	public function __construct($apiInfo = array(), $companyID = 0){
		
		//初始换父类
		parent::__construct($companyID);
		
		$this->_apiInfo = $apiInfo;

		//转换经销商ID
//		$this->_apiInfo['ClientID'] = (int)(str_replace($this->myPrefix(), '', $this->_apiInfo['externalUserId']));
		
		$this->_apiModule = new YOpenApiSet();
	}
	
	//处理 支付手机设置支付密码
	public function modifyPw2Cowpay($returnAcc = array()){
		
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);

		$params = array(
					'dhbUserid'		=> $dhbOrder['ClientID'],
					'ClientCompany'	=> $dhbOrder['CompanyID'],
					'YapiUserId'	=> $this->_apiInfo['userId'],
					'YapiuserName'	=> $this->_apiInfo['userName'],
				);
	
		$result = $this->_apiModule->UserUpdate($params);
		
		if($result){
			$status = array(
					'service'		=> 'pay',
					'visittype'		=> $dhbOrder['VisitType'],
					);
		}else{
			$status = array(
					'service'		=> 'pay_Fail',
					'visittype'		=> $dhbOrder['VisitType'],
					);
		}
		
		$this->goToUrl($status);
	}
	
		//处理 激活
	public function userInfoQuery($returnAcc = array()){

		$params = array(
					'dhbUserid'		=> $returnAcc['dhbUserid'],
					'ClientCompany'	=> $returnAcc['ClientCompany'],
					'YapiUserId'	=> $this->_apiInfo['userId']
				);
	
		$this->_apiModule->UserUpdate($params);
				
	}
		//处理 开户
	public function qftDealerRegister(){
		
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
		$params = array(
					'dhbUserid'		=> (int)(str_replace(YAPI_ORDER_PREFIX, '', $this->_apiInfo['partnerShopId'])),
					'YapiUserId'	=> $this->_apiInfo['userId'],
					'YapiuserName'	=> $this->_apiInfo['userName'],
					'YapiUserType'	=> $this->_apiInfo['userType']
				);
		$this->_apiModule->UserRegister($params,1);
				
		//显示支付结果
		$status = array(
					'service'		=> 'pay',
					'visittype'		=> $dhbOrder['VisitType'],
					'verify_Result' => '验签成功',
					'showInfo' 		=> $this->_message[__FUNCTION__],
					'merchantId' 	=> $this->_apiInfo['partnerId'],
					'orderNo' 		=> $this->_apiInfo['orderNo'],
					'orderDatetime' => $this->_apiInfo['notifyTime'],
					'returnStatus'	=> true,	//入口已验证签名
					'userId' 		=> $this->_apiInfo['userId'],
					'userName' 		=> $this->_apiInfo['userName']
			);

		$this->goToUrl($status);
	}
	
	//处理 开户返回
	public function qftGoLogin(){
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
				
		//显示支付结果
		$status = array(
					'service'		=> 'pay',
					'visittype'		=> $dhbOrder['VisitType'],
					'verify_Result' => '验签成功',
					'showInfo' 		=> $this->_message[__FUNCTION__],
					'merchantId' 	=> $this->_apiInfo['partnerId'],
					'orderNo' 		=> $this->_apiInfo['orderNo'],
					'orderDatetime' => $this->_apiInfo['notifyTime'],
					'returnStatus'	=> true,	//入口已验证签名
					'userId' 		=> $this->_apiInfo['userId'],
					'userName' 		=> $this->_apiInfo['userName']
			);

		$this->goToUrl($status);
	}
	
	//处理 开户
	public function cowpayUserRegisterV2($returnAcc = array()){

		$params = array(
					'dhbUserid'		=> (int)($returnAcc['dhbUserid']),
					'YapiUserId'	=> $this->_apiInfo['userId'],
					'YapiuserName'	=> $this->_apiInfo['userName'],
					'YapiUserType'	=> 'P'
				);
	
		$this->_apiModule->UserRegister($params);
				
	}
	
	//处理支付失败跳转
	public function returnrul(){
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
		$status = array(
					'service'		=> 'pay_Fail',
					'visittype'		=> $dhbOrder['VisitType']
						);

		$this->showStatus($status);
	}
	
	//异步支付失败的时候调此方法
	public function createTradeOrder(){
		/******************************** 处理支付结果 ********************************/
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);

		//获取流水号
		$NetPaySeria	= new NetPaySeria();
		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);

		//预处理
		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];
		
		//修改订单状态
		$orderTotal 	=  $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = $orderTotal * 100;
		$NetPayToOrder = new NetPayToOrder();
		
		if(isset($this->_apiInfo['resultCode']) && $this->_apiInfo['resultCode'] == 'DEDUCT_FAIL'){//PAY_SUCCESS表示支付成功
			$this->_apiInfo['PayResult'] = 0;
			$switchType = __FUNCTION__;
			$NetPayToOrder->updatefinanceStatus($this->_apiInfo, $pinfo, 'yijifu');
		}
		
	}
	
		//处理 手机端创建订单
	public function qftIntegratedPayment(){
/******************************** 处理支付结果 ********************************/
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);

		//获取流水号
		$NetPaySeria	= new NetPaySeria();
		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);
		
		//修改订单状态
		$NetPayToOrder = new NetPayToOrder();
		if($this->_apiInfo['resultCode'] != 'DHB_WAIT'){  //异步就先锁定避免出错 tubo修改 2016-1-19
			$NetPayToOrder->insertPaynoCheck($this->_apiInfo);
		}												  //end 2016-1-19

		//预处理
		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];

		//添加数据
		//$total		= YOpenApiController::getTotalForYJF($dhbOrder['DHBOrderNO'], $dhbOrder['CompanyID'], $dhbOrder['ClientID']);
		//$orderTotal = $total['total'] ? $total['total'] : $dhbOrder['Total'];
		$orderTotal 	=  $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = $orderTotal * 100;

		//判断是不是为支付服务service为qftIntegratedPayment tubo 2016-1-19 是的话同步进去锁单
		if($this->_apiInfo['resultCode'] == 'DHB_WAIT'){  //同步通知锁定订单
			$NetPayToOrder->lockOrderStatus($this->_apiInfo);
		}elseif(isset($this->_apiInfo['tradeStatus']) && $this->_apiInfo['tradeStatus'] == 'trade_finished'){//异步通知的trade_finished状态是支付成功的
			$this->_apiInfo['PayResult'] = 1;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu',$dhbOrder['VisitType']);
			echo 'success';
			exit;
		}else{//异步通知解锁订单
			$NetPayToOrder->unLockOrderStatus($this->_apiInfo);
			echo 'success';
			exit;
		}
		$switchType = 'pay';
		//end; tubo 2016-1-19
		/*
		//验证是否来自于页面通知
		if((empty($this->_apiInfo['tradeStatus']) && ($this->_apiInfo['resultCode'] == 'PAY_SUCCESS'))||(isset($this->_apiInfo['tradeStatus']) && $this->_apiInfo['tradeStatus'] == 'trade_finished')){//PAY_SUCCESS表示支付成功
			$this->_apiInfo['PayResult'] = 1;
			$switchType = 'pay';
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu',$dhbOrder['VisitType']);
			echo 'success';
		}elseif((empty($this->_apiInfo['tradeStatus']) && $this->_apiInfo['resultCode'] == 'EXECUTE_PROCESSING')||(isset($this->_apiInfo['tradeStatus']) && $this->_apiInfo['tradeStatus'] == 'wait_buyer_pay')){//EXECUTE_PROCESSING表示处理中
			///显示支付结果
			$switchType = 'pay'.'_Wait';
			$this->_apiInfo['PayResult'] = 0;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu',$dhbOrder['VisitType']);
		}else{//包含PAY_FAIL在类的支付失败，暂时不会进到这一步，因为front里面已经检查过一次状态
			//显示支付结果
			$switchType = 'pay'.'_Fail';
			$this->_apiInfo['PayResult'] = 0;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu',$dhbOrder['VisitType']);
		} 
		*/

		$status = array(
					'service'		=> $switchType,
					'visittype'		=> $dhbOrder['VisitType'],
					'verify_Result' => '验签成功',
					'dhbPayOrder' 	=> $dhbOrder['DHBOrderNO'] ? trim($dhbOrder['DHBOrderNO'], ",") : '预付款',
					'showInfo' 		=> $this->_message[$switchType],
					'merchantId' 	=> $this->_apiInfo['partnerId'],
					'orderNo' 		=> $this->_apiInfo['bankCode'] ? $this->_apiInfo['orderNo'] : $this->_apiInfo['tradeNo'],
					'orderDatetime' => $this->_apiInfo['notifyTime'],
					'payAmount' 	=> number_format($this->_apiInfo['payAmount'] / 100, 2, '.', ''),
					'returnStatus'	=> true,	//入口已验证签名
					'payDatetime' 	=> $this->_apiInfo['PayDateTime'] ? $this->_apiInfo['PayDateTime'] : date('Y-m-d H:i')
			);

		$this->goToUrl($status);
	}
	
	//处理 手机端创建订单
	public function pay(){
/******************************** 处理支付结果 ********************************/
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);

		//获取流水号
		$NetPaySeria	= new NetPaySeria();
		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);

		//预处理
		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];

		//添加数据
		//$total		= YOpenApiController::getTotalForYJF($dhbOrder['DHBOrderNO'], $dhbOrder['CompanyID'], $dhbOrder['ClientID']);
		//$orderTotal = $total['total'] ? $total['total'] : $dhbOrder['Total'];
		$orderTotal 	=  $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = $orderTotal * 100;
		//修改订单状态
		$NetPayToOrder = new NetPayToOrder();

		//验证是否来自于页面通知
		if(($this->_apiInfo['resultCode'] == 'PAY_SUCCESS') ||($this->_apiInfo['executeStatus'] == 'true')){//PAY_SUCCESS表示支付成功
			$this->_apiInfo['PayResult'] = 1;
			$switchType = __FUNCTION__;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu',$dhbOrder['VisitType']);
			echo 'success';
		}elseif(isset($this->_apiInfo['resultCode']) && $this->_apiInfo['resultCode'] == 'EXECUTE_PROCESSING'){//EXECUTE_PROCESSING表示处理中
			///显示支付结果
			$switchType = __FUNCTION__.'_Wait';
			$this->_apiInfo['PayResult'] = 0;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu',$dhbOrder['VisitType']);
		}else{//包含PAY_FAIL在类的支付失败，暂时不会进到这一步，因为front里面已经检查过一次状态
			//显示支付结果
			$switchType = __FUNCTION__.'_Fail';
			$this->_apiInfo['PayResult'] = 0;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu',$dhbOrder['VisitType']);
		}

		$status = array(
					'service'		=> $switchType,
					'visittype'		=> $dhbOrder['VisitType'],
					'verify_Result' => '验签成功',
					'dhbPayOrder' 	=> $dhbOrder['DHBOrderNO'] ? trim($dhbOrder['DHBOrderNO'], ",") : '预付款',
					'showInfo' 		=> $this->_message[$switchType],
					'merchantId' 	=> $this->_apiInfo['partnerId'],
					'orderNo' 		=> $this->_apiInfo['bankCode'] ? $this->_apiInfo['orderNo'] : $this->_apiInfo['tradeNo'],
					'orderDatetime' => $this->_apiInfo['notifyTime'],
					'payAmount' 	=> number_format($this->_apiInfo['payAmount'] / 100, 2, '.', ''),
					'returnStatus'	=> true,	//入口已验证签名
					'payDatetime' 	=> $this->_apiInfo['PayDateTime'] ? $this->_apiInfo['PayDateTime'] : date('Y-m-d H:i')
			);

		$this->goToUrl($status);
	}
	
	
	//处理 订单支付
	public function commandPayTradeCreatePay(){
		
		/******************************** 处理支付结果 ********************************/
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
	
		//获取流水号
		$NetPaySeria	= new NetPaySeria();
		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);
	
		//修改订单状态 TODO
// 		$NetPayToOrder = new NetPayToOrder();
// 		if($this->_apiInfo['resultCode'] != 'DHB_WAIT'){  //异步就先锁定避免出错 tubo修改 2016-1-19
// 			$NetPayToOrder->insertPaynoCheck($this->_apiInfo);
// 		}
		
		//解析支付数据
		$this->_apiInfo['creatTradeResult'] = json_decode($this->_apiInfo['creatTradeResult'], 1);
	
		//预处理
		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];
	
		//添加数据
		$orderTotal = $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = $orderTotal * 100;
	
		//修改订单状态
		$NetPayToOrder = new NetPayToOrder();
		
		//判断是不是为支付服务service为qftIntegratedPayment tubo 2016-1-19 是的话同步进去锁单
		if(isset($this->_apiInfo['creatTradeResult']) && (in_array($this->_apiInfo['creatTradeResult'][0]['creatResult'], array('CONFIRM_PAY', 'FINISHED', 'PAY_PROCESSING')))){  //同步通知锁定订单
			$NetPayToOrder->lockOrderStatus($this->_apiInfo);
			$switchType = 'commonTradePay_Wait';
		}elseif(isset($this->_apiInfo['commandPayStatus']) && ($this->_apiInfo['commandPayStatus'] == 'CONFIRM_PAY' || $this->_apiInfo['commandPayStatus'] == 'FINISHED')){//异步通知的trade_finished状态是支付成功的
			$this->_apiInfo['PayResult'] = 1;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
			$this->payConfirm($dhbOrder);
			echo 'success';
			exit;
		}
// 		else{//异步通知解锁订单
// 			$NetPayToOrder->unLockOrderStatus($this->_apiInfo);
// 			echo 'success';
// 			exit;
// 		}
		
		$status = array(
				'service'		=> $switchType,
				'verify_Result' => '验签成功',
				'dhbPayOrder' 	=> $dhbOrder['DHBOrderNO'] ? trim($dhbOrder['DHBOrderNO'], ",") : '预付款',
				'showInfo' 		=> $this->_message[$switchType],
				'merchantId' 	=> $this->_apiInfo['partnerId'],
				'orderNo' 		=> $this->_apiInfo['bankCode'] ? $this->_apiInfo['orderNo'] : $this->_apiInfo['tradeNo'],
				'orderDatetime' => $this->_apiInfo['notifyTime'],
				'payAmount' 	=> number_format($this->_apiInfo['payAmount'] / 100, 2, '.', ''),
				'returnStatus'	=> true,	//入口已验证签名
				'visittype'		=> $dhbOrder['VisitType'],	//入口已验证签名
				'payDatetime' 	=> $this->_apiInfo['PayDateTime'] ? $this->_apiInfo['PayDateTime'] : date('Y-m-d H:i')
		);
	
		$this->goToUrl($status);
	}
	
	//确认打款[临时]
	public function payConfirm($dhbOrder = array()){
		$YopenApiFront = new YopenApiFront($dhbOrder['ClientID']);
		$YopenApiFront->commandPayConfirm($this->_apiInfo['tradeNo']);
	}
	
// 	//处理 订单支付
// 	public function commonTradePay(){
		
// 		/******************************** 处理支付结果 ********************************/
// 		//获取映射关系
// 		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
// 		//获取流水号
// 		$NetPaySeria	= new NetPaySeria();
// 		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);
		
		
		
// 		debug($pinfo, 1);
		
// 		//预处理
// 		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
// 		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
// 		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
// 		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
// 		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
// 		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];
		
// 		//添加数据
// 		//$total		= orderdata::getTotalForYJF($dhbOrder['DHBOrderNO'], $dhbOrder['CompanyID'], $dhbOrder['ClientID']);
// 		//$orderTotal = $total['total'] ? $total['total'] : $dhbOrder['Total'];
// 		$orderTotal = $dhbOrder['Total'];
// 		$this->_apiInfo['payAmount'] = $orderTotal * 100;
		
// 		//修改订单状态
// 		$NetPayToOrder = new NetPayToOrder();
		
// 		//验证是否来自于页面通知
// 		if(empty($this->_apiInfo['tradeStatus'])){//异步通知一定是支付成功的，
			
// 			$this->_apiInfo['PayResult'] = 1;
// 			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
// 			echo 'success';
// 			exit;
// 		}elseif(isset($this->_apiInfo['tradeStatus']) && $this->_apiInfo['tradeStatus'] == 'trade_finished'){//页面通知的trade_finished状态是支付成功的
// 			//显示支付结果类型
// 			$switchType = $this->_apiInfo['bankCode'] ? __FUNCTION__.'_Sign' : __FUNCTION__;
// 			$this->_apiInfo['PayResult'] = 1;
// 		}else{//可能还未扣款，wait_buyer_pay。是否还有其他情况
			
// 			//显示支付结果
// 			$switchType = __FUNCTION__.'_Wait';
// 			$this->_apiInfo['PayResult'] = 0;
// 		}
// 		$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
	
// 		$status = array(
// 					'service'		=> $switchType,
// 					'verify_Result' => '验签成功',
// 					'dhbPayOrder' 	=> $dhbOrder['DHBOrderNO'] ? trim($dhbOrder['DHBOrderNO'], ",") : '预付款',
// 					'showInfo' 		=> $this->_message[$switchType],
// 					'merchantId' 	=> $this->_apiInfo['partnerId'],
// 					'orderNo' 		=> $this->_apiInfo['bankCode'] ? $this->_apiInfo['orderNo'] : $this->_apiInfo['tradeNo'],
// 					'orderDatetime' => $this->_apiInfo['notifyTime'],
// 					'payAmount' 	=> number_format($this->_apiInfo['payAmount'] / 100, 2, '.', ''),
// 					'returnStatus'	=> true,	//入口已验证签名
// 					'payDatetime' 	=> $this->_apiInfo['PayDateTime'] ? $this->_apiInfo['PayDateTime'] : date('Y-m-d H:i')
// 			);

// 		$this->goToUrl($status);
// 	}
	
	//显示支付状态
	public function showStatus($status){
		extract($status);
		if($visittype == 'APP'){
			if($service=='pay'){
				$url = WEB_ROOT_URL.'/mobileApi/template/yjf_netpay.html';
				header("location: ".$url);
			}
			elseif($service=='pay_Fail'){
				$url = WEB_ROOT_URL.'/mobileApi/template/yjf_netpay_fail.html';
				header("location: ".$url);
			}
			elseif($service=='pay_Wait'){
				$url = WEB_ROOT_URL.'/mobileApi/template/yjf_netpay_wait.html';
				header("location: ".$url);
			}
		}elseif ($visittype == 'WEB'){
			//echo" <script language='javascript'>";
			//echo" window.open('".YAPI_RETURN_URL."')";
			//echo "</script>";
			$url = WEB_ROOT_URL.'/mobileApi/template/yjf_web.html';
			header("location: ".$url);
			//header("location: ".YAPI_RETURN_URL);
		}
		//include template("/template/yjf_netpay.html");
		//include template("yjf_netpay");
	}
	
	//提现记录
	public function yjfWithdraw(){

		$this->_apiModule->logTx($this->_apiInfo);
		
		//显示提现结果
		$status = array(
						'service'		=> __FUNCTION__,
						'verify_Result' => '验签成功',
						'showInfo' 		=> $this->_message[__FUNCTION__],
						'merchantId' 	=> $this->_apiInfo['partnerId'],
						'orderNo' 		=> $this->_apiInfo['payNo'],
						'orderDatetime' => $this->_apiInfo['notifyTime'],
						'payAmount' 	=> $this->_apiInfo['amount'],
						'amountIn' 		=> $this->_apiInfo['amountIn'],
						'payDatetime' 	=> $this->_apiInfo['notifyTime']
				);
				
		$this->goToUrl($status);
	}
	
	
	/**
	 * @name 使用SESSION记录
	 * @author wanjun
	 * @param
	 * 	$msg string 需要设置的信息
	 * 	$status string 需要设置的状态，默认success
	 * @since 2015.04.26
	 */
	public function setMsg($msg = '', $status = 'success'){
		if(empty($status) || empty($msg)) return false;
		
		//信息状态：error,success
		$_SESSION['oci']['status']	= $status;
		//提示信息
		$_SESSION['oci']['message'] = $msg;
	}
	
	//重定向
	public function goToUrl($status = array()){
		
		$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
		$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
		$logMsg = "【Service：".$status['service']."】：RUN END!";
		$kLog->logInfo($logMsg);
		
		//同步跳转[这里应该在完善下]
		if(strpos($_SERVER['SCRIPT_NAME'], "async.php")){
			YOpenApiController::putMsg(YOPENAPI_MESSAGE_PATH, md5($this->_apiInfo['orderNo']), http_build_query($this->_apiInfo));
			//$_SESSION['YJFSynInfo'] = $this->_apiInfo;
		}else{
			$this->showStatus($status);
		}
	}
	
	//修改支付密码
	public function findPayPassword(){
		
		//显示操作结果
		$status = array(
				'service'		=> __FUNCTION__,
				'verify_Result' => '验签成功',
				'showInfo' 		=> $this->_message[__FUNCTION__],
				'merchantId' 	=> $this->_apiInfo['partnerId'],
				'orderNo' 		=> $this->_apiInfo['orderNo'],
				'orderDatetime' => date('Y-m-d H:i:s'),
				'payDatetime' 	=> date('Y-m-d H:i:s'),
				'returnStatus'	=> true,	//入口已验证签名
		);
			
		$this->goToUrl($status);
		
	}
	
	//修改支付密码
	public function modifyBindMobile(){
	
		//显示操作结果
		$status = array(
				'service'		=> __FUNCTION__,
				'verify_Result' => '验签成功',
				'showInfo' 		=> $this->_message[__FUNCTION__],
				'merchantId' 	=> $this->_apiInfo['partnerId'],
				'orderNo' 		=> $this->_apiInfo['orderNo'],
				'orderDatetime' => date('Y-m-d H:i:s'),
				'payDatetime' 	=> date('Y-m-d H:i:s'),
				'returnStatus'	=> true,	//入口已验证签名
		);
			
		$this->goToUrl($status);
	
	}
	
	//修改支付密码
	public function modifyEmail(){
	
		//显示操作结果
		$status = array(
				'service'		=> __FUNCTION__,
				'verify_Result' => '验签成功',
				'showInfo' 		=> $this->_message[__FUNCTION__],
				'merchantId' 	=> $this->_apiInfo['partnerId'],
				'orderNo' 		=> $this->_apiInfo['orderNo'],
				'orderDatetime' => date('Y-m-d H:i:s'),
				'payDatetime' 	=> date('Y-m-d H:i:s'),
				'returnStatus'	=> true,	//入口已验证签名
		);
			
		$this->goToUrl($status);
	}
	
	/**
	 * 处理绑卡数据
	 *
	 * @author wanjun
	 * @since 2015/07/23
	 */
	public function signmanybank(){
		
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
		//保存绑卡记录
		$Signbank = new Signbank();
		$Signbank->saveSignMsg($dhbOrder['CompanyID'], $dhbOrder['ClientID'], $this->_apiInfo);
		
		//显示支付结果
		$status = array(
					'service'		=> __FUNCTION__,
					'verify_Result' => '验签成功',
					'showInfo' 		=> $this->_message[__FUNCTION__],
					'merchantId' 	=> $this->_apiInfo['partnerId'],
					'orderNo' 		=> $this->_apiInfo['orderNo'],
					'orderDatetime' => $this->_apiInfo['notifyTime'],
					'returnStatus'	=> true,	//入口已验证签名
					'userId' 		=> $this->_apiInfo['userId'],
					'userName' 		=> $this->_apiInfo['userName']
			);

		$this->goToUrl($status);
		
	}
	
}




?>