<?php 

!defined('SYSTEM_ACCESS') && exit('Access deny!');

//易支付返回信息处理

class YOpenApiDo extends YOpenApi {
	
	private $_apiModule = null;		//数据模型处理
	private $_apiInfo	= array();	//接收数据
	
	//各项业务提示信息
	private $_message = array(
							'cowpayUserRegister'	=> '恭喜您，开户成功',				//开户
							'qftDealerRegister'		=> '恭喜您，开户成功',				//开户
							'commonTradePay'		=> '恭喜您，支付成功',				//支付
							'qftIntegratedPayment'	=> '恭喜您，支付成功',				//支付
							'commonTradePay_Sign'	=> '恭喜您，签约成功',				//签约银行卡
							'commonTradePay_Wait'	=> '已完成支付，系统确认中...',				//款项确认中
							'yjfWithdraw'			=> '恭喜您，提现成功',				//提现
							'findPayPassword'		=> '恭喜您，成功找回支付密码',		//找回支付密码
							'modifyBindMobile'		=> '恭喜您，成功绑定手机',			//修改绑定手机
							'modifyEmail'			=> '恭喜您，绑定邮箱操作成功，请根据邮件提示进行下一步操作',			//修改绑定邮箱
							'pay'					=> '恭喜您，支付成功',				//手机支付 对应提交的createTradeOrder
							'pay_Fail'				=> '很抱歉，支付失败',				//手机签约银行卡 对应提交的createTradeOrder
							'pay_Wait' 				=> '已完成支付，系统确认中...',		//手机支付款项确认中 对应提交的createTradeOrder
							'qftSDKPayment'			=> '恭喜您，生成成功',				//SDK对应POS支付 新接口
							'commandPayTradeCreatePay'	=> '付款成功，请等待系统处理交易数据...',			//担保交易支付延迟提示信息
						);
	
	
	
	public function __construct($apiInfo = array(), $companyID = 0, $isonline = false){
		
		//初始换父类
		parent::__construct($companyID);
		
		$this->_apiInfo = $apiInfo;
		$this->isonline = $isonline;

		//转换经销商ID
//		$this->_apiInfo['ClientID'] = (int)(str_replace($this->myPrefix(), '', $this->_apiInfo['externalUserId']));
		
		$this->_apiModule = new YOpenApiSet();
	}
	
	//处理 开户
	public function qftDealerRegister(){
		
		$params = array(
					'dhbUserid'		=> (int)(str_replace(YAPI_ORDER_PREFIX, '', $this->_apiInfo['partnerShopId'])),
					'YapiUserId'	=> $this->_apiInfo['userId'],
					'YapiuserName'	=> $this->_apiInfo['userName'],
					'YapiUserType'	=> $this->_apiInfo['userType'],
				);
		$this->_apiModule->UserRegister($params);
				
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
	
	public function cowpayUserRegister(){
		
		$params = array(
					'dhbUserid'		=> (int)(str_replace(YAPI_ORDER_PREFIX, '', $this->_apiInfo['externalUserId'])),
					'YapiUserId'	=> $this->_apiInfo['userId'],
					'YapiuserName'	=> $this->_apiInfo['userName'],
					'YapiUserType'	=> $this->_apiInfo['userType']
				);
		$this->_apiModule->UserRegister($params);
				
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
		//$total		= orderdata::getTotalForYJF($dhbOrder['DHBOrderNO'], $dhbOrder['CompanyID'], $dhbOrder['ClientID']);
		//$orderTotal = $total['total'] ? $total['total'] : $dhbOrder['Total'];
		$orderTotal = $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = $orderTotal * 100;
		
		//修改订单状态
		$NetPayToOrder = new NetPayToOrder();
		
		//验证是否来自于页面通知
		if(isset($this->_apiInfo['resultCode']) && $this->_apiInfo['resultCode'] == 'PAY_SUCCESS'){//PAY_SUCCESS表示支付成功
			$this->_apiInfo['PayResult'] = 1;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
			echo 'success';
			exit;
		}elseif(isset($this->_apiInfo['resultCode']) && $this->_apiInfo['resultCode'] == 'EXECUTE_PROCESSING'){//EXECUTE_PROCESSING表示处理中
			///显示支付结果
			$switchType = __FUNCTION__.'_Wait';
			$this->_apiInfo['PayResult'] = 0;
		}else{//包含PAY_FAIL在类的支付失败，暂时不会进到这一步，因为front里面已经检查过一次状态
			
			//显示支付结果
			$switchType = __FUNCTION__.'_Fail';
			$this->_apiInfo['PayResult'] = 0;
		}
		$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
	
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
					'payDatetime' 	=> $this->_apiInfo['PayDateTime'] ? $this->_apiInfo['PayDateTime'] : date('Y-m-d H:i')
			);

		$this->goToUrl($status);
	}
	
	
	//处理 订单支付
	public function commandPayTradeCreatePay(){
		
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
		//获取流水号
		$NetPaySeria	= new NetPaySeria();
		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);
		
		//修改订单状态
		$NetPayToOrder = new NetPayToOrder();
		if($this->_apiInfo['resultCode'] != 'DHB_WAIT'){  //异步就先锁定避免出错 tubo修改 2016-1-19
			$NetPayToOrder->insertPaynoCheck($this->_apiInfo);
		}
		
		//解析支付数据
		$this->_apiInfo['creatTradeResult'] = json_decode($this->_apiInfo['creatTradeResult'], 1);

		//预处理
		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];
		
		$orderTotal 	= $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = isset($this->_apiInfo['creatTradeResult']) ? $this->_apiInfo['creatTradeResult'][0]['tradeAmount'] : $this->_apiInfo['tradeAmount'];
		
		//判断是不是为支付服务service为qftIntegratedPayment tubo 2016-1-19 是的话同步进去锁单
		if(isset($this->_apiInfo['creatTradeResult']) && (in_array($this->_apiInfo['creatTradeResult'][0]['creatResult'], array('CONFIRM_PAY', 'FINISHED', 'PAY_PROCESSING')))){  //同步通知锁定订单
			$NetPayToOrder->lockOrderStatus($this->_apiInfo);
		}elseif(isset($this->_apiInfo['commandPayStatus']) && ($this->_apiInfo['commandPayStatus'] == 'CONFIRM_PAY' || $this->_apiInfo['commandPayStatus'] == 'FINISHED')){//异步通知的trade_finished状态是支付成功的
			$this->_apiInfo['PayResult'] = 1;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
			$this->payConfirm($dhbOrder);
			echo 'success';
			exit;
		}else{//异步通知解锁订单
			$NetPayToOrder->unLockOrderStatus($this->_apiInfo);
			echo 'success';
			exit;
		}
		//end 2016-1-19
	
		$switchType = $this->_apiInfo['service'];
		$status = array(
				'service'		=> $switchType,
				'verify_Result' => '验签成功',
				'dhbPayOrder' 	=> $dhbOrder['DHBOrderNO'] ? trim($dhbOrder['DHBOrderNO'], ",") : '预付款',
				'showInfo' 		=> $this->_message[$switchType],
				'merchantId' 	=> $this->_apiInfo['partnerId'],
				'orderNo' 		=> $this->_apiInfo['orderNo'],
				'orderDatetime' => $this->_apiInfo['notifyTime'],
				'payAmount' 	=> number_format($this->_apiInfo['payAmount'], 2, '.', ''),
				'returnStatus'	=> true,	//入口已验证签名
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
// 	public function qftIntegratedPayment(){
// 		//获取映射关系
// 		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
// 		//获取流水号
// 		$NetPaySeria	= new NetPaySeria();
// 		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);
		
// 		//修改订单状态
// 		$NetPayToOrder = new NetPayToOrder();
// 		if($this->_apiInfo['resultCode'] != 'DHB_WAIT'){  //异步就先锁定避免出错 tubo修改 2016-1-19
// 			$NetPayToOrder->insertPaynoCheck($this->_apiInfo);
// 		}												  //end 2016-1-19
		
// 		//预处理
// 		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
// 		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
// 		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
// 		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
// 		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
// 		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];
		
// 		$orderTotal 	= $dhbOrder['Total'];
// 		$this->_apiInfo['payAmount'] = $orderTotal * 100;
		
// 		//判断是不是为支付服务service为qftIntegratedPayment tubo 2016-1-19 是的话同步进去锁单
// 		if($this->_apiInfo['resultCode'] == 'DHB_WAIT'){  //同步通知锁定订单
// 			$NetPayToOrder->lockOrderStatus($this->_apiInfo);
// 		}elseif(isset($this->_apiInfo['tradeStatus']) && $this->_apiInfo['tradeStatus'] == 'trade_finished'){//异步通知的trade_finished状态是支付成功的
// 			$this->_apiInfo['PayResult'] = 1;
// 			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
// 			echo 'success';
// 			exit;
// 		}else{//异步通知解锁订单
// 			$NetPayToOrder->unLockOrderStatus($this->_apiInfo);
// 			echo 'success';
// 			exit;
// 		}
// 		//end 2016-1-19
		
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
	
	//处理 SDK付款确认
	public function qftSDKPayment(){
		//获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
		//获取流水号
		$NetPaySeria	= new NetPaySeria();
		$pinfo			= $NetPaySeria->getSeria($this->_apiInfo['partnerId'], $this->_apiInfo['orderNo']);
		
		$NetPayToOrder = new NetPayToOrder();
		
		//预处理
		$this->_apiInfo['PayTradeNO']		= $this->_apiInfo['tradeNo'];
		$this->_apiInfo['PayDateTime']		= $this->_apiInfo['notifyTime'];
		$this->_apiInfo['ErrorCode']		= $this->_apiInfo['resultCode'];
		$this->_apiInfo['ReturnDateTime']	= date('YmdHis');
		$this->_apiInfo['ext1']				= $dhbOrder['CompanyID'].'_'.$dhbOrder['ClientID'];
		$this->_apiInfo['ext2']				= $dhbOrder['DHBOrderNO'];
		
		$orderTotal 	= $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = $orderTotal * 100;
		
		if(isset($this->_apiInfo['tradeStatus']) && ($this->_apiInfo['tradeStatus'] == 'trade_payed' || $this->_apiInfo['tradeStatus'] == 'trade_finished') ){//异步通知的trade_payed状态是支付成功的
			$this->_apiInfo['PayResult'] = 1;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
			echo 'success';
			exit;
		}else{//异步其他通知不处理
			echo 'success';
			exit;
		}
		//end 2016-1-19
	}
	
	/*
	public function commonTradePay(){
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
		//$total		= orderdata::getTotalForYJF($dhbOrder['DHBOrderNO'], $dhbOrder['CompanyID'], $dhbOrder['ClientID']);
		//$orderTotal = $total['total'] ? $total['total'] : $dhbOrder['Total'];
		$orderTotal 	= $dhbOrder['Total'];
		$this->_apiInfo['payAmount'] = $orderTotal * 100;
		
		//修改订单状态
		$NetPayToOrder = new NetPayToOrder();
		
		//验证是否来自于页面通知
		if(empty($this->_apiInfo['tradeStatus'])){//异步通知一定是支付成功的，
			
			$this->_apiInfo['PayResult'] = 1;
			$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
			echo 'success';
			exit;
		}elseif(isset($this->_apiInfo['tradeStatus']) && $this->_apiInfo['tradeStatus'] == 'trade_finished'){//页面通知的trade_finished状态是支付成功的
			//显示支付结果类型
			$switchType = $this->_apiInfo['bankCode'] ? __FUNCTION__.'_Sign' : __FUNCTION__;
			$this->_apiInfo['PayResult'] = 1;
		}else{//可能还未扣款，wait_buyer_pay。是否还有其他情况
			
			//显示支付结果
			$switchType = __FUNCTION__.'_Wait';
			$this->_apiInfo['PayResult'] = 0;
		}
		$NetPayToOrder->updateOrderStatus($this->_apiInfo, $pinfo, 'yijifu');
	
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
					'payDatetime' 	=> $this->_apiInfo['PayDateTime'] ? $this->_apiInfo['PayDateTime'] : date('Y-m-d H:i')
			);

		$this->goToUrl($status);
	}*/
	
	//显示支付状态
	public function showStatus($status){
		
		extract($status);
		include template("yjf_netpay");
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
		
		//同步跳转[这里应该在完善下]
		if(strpos($_SERVER['SCRIPT_NAME'], "async.php")){
			Functions::putMsg(YOPENAPI_MESSAGE_PATH, md5($this->_apiInfo['orderNo']), http_build_query($this->_apiInfo));
//			$_SESSION['YJFSynInfo'] = $this->_apiInfo;
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
	
	//供应商申请开户
	public function openPaymentAccount(){
		
		//同步通知，直接跳转		
		if($this->isonline){
		 	header('location:paytype.php?type='.$this->_apiInfo['registerUserType'].'&message='.$this->_apiInfo['message']);
		 	exit;
		}
	    
	    //获取映射关系
		$dhbOrder = $this->_apiModule->getMap($this->_apiInfo['orderNo']);
		
		//写入开户资料
		$netGetWay = new NetGetWay();
		$return = $netGetWay->saveGetWay(
		                          $dhbOrder['CompanyID'],
		                          '',//MerchantNO
		                          $this->_apiInfo['userId'],
		                          $this->_apiInfo['partnerShopName'], //开户名称
		                          $this->_apiInfo['registerUserType'],
		                          $this->_apiInfo['userName'],
		                          $this->_apiInfo['orderNo']
		                      );
	}//END qftSupplierApply
	
}




?>