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
* 易支付 供应商端 数据处理程序，各服务代码如下：
* 
* 1.账户开户			service服务代码[cowpayUserRegister]
* 2.提现记录查询	service服务代码[即:对账记录查询？代码：reconciliationQuery]
* 
*/

class YopenApiBackend extends YOpenApi{
	
	/**
	 * @name 初始化系统
	 * @author wanjun
	 */
	public function __construct($companyID = 0, $adminUserID = 0){

//		if(!$companyID && OPEN_DEBUG) throw new Exception('您没有此项的操作权限...');
		
		parent::__construct($companyID, 'backend');
	}
	
	/**
	 * @name 商户提现
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function getMoney(){
	
		//本模块需要添加的数据
		$post = array(
 				"userId"		=> $this->apiSignNO,
				"requestTime" 	=> date('YmdHis'),
				"type"			=> 'ordinary',
				"loginId"		=> $this->signAccount,
				"outOrderNo"	=> $this->orderNo,
		);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);
	
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
	}//getMoney
	
	/**
	 * @name 提现记录查询
	 * @author wanjun
	 * @param $sear array 查询条件
	 * @since 2015/04/15
	 */
	public function getSear($sear = array()){
	
		//添加本模块服务代码
		$this->bulidSet(
							array_merge(
									$this->predefine[__FUNCTION__],
									array(
										'userId'	=> $this->apiSignNO,
										'currPage'	=> $sear['currPage'],
										'pageSize'	=> $sear['pageSize'],
										'startTime'	=> $sear['startTime'],
										'endTime'	=> $sear['endTime']
									)
							)
						);
		
		//Curl 方式提交
		return $this->submitExecute->setCurlHandle($this->commonPost);
	}//getSear
	
	/**
	 * @name 账户开户 
	 * @author wanjun
	 * @since 2015/04/15
	 * @property
	 * 	externalUserId DHB用户ID
	 * 	outOrderNo	开户业务号
	 */
	public function setAccount(){
		
		$post = array(
					"externalUserId" => $this->getCompanyID(),
					"outOrderNo" 	 => $this->orderNo,
				);
				
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);

		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}//setAccount
	
	/**
	 * @name 修改支付密码
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function changePWD($cinfo = array()){
		
		$accountInfo = $this->getAccountInfo(strval($cinfo['actype']));
		
		//本模块需要添加的数据
		$post = array(
					"passwordType" 	=> 'payPwd',
					"requestTime" 	=> date('YmdHis'),
					"userId"		=> trim($accountInfo['SignNO']),
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);
	
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}//END changePWD
	
	/**
	 * @name 找回支付密码
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function getPWD($cinfo){
		
		$accountInfo = $this->getAccountInfo(strval($cinfo['actype']));
		//本模块需要添加的数据
		$post = array(
					"system" 	=> 'qft',
					"userName"	=> trim($accountInfo['SignAccount']),
				);
		
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}//END getPWD
	
	/**
	 * @name 修改绑定手机
	 * @author wanjun
	 * @since 2015/04/27
	 */
	public function changeMobile($cinfo = array()){
		
		$accountInfo = $this->getAccountInfo(strval($cinfo['actype']));
		//本模块需要添加的数据
		$post = array(
				"system" 	=> 'qft',
				"userName"	=> trim($accountInfo['SignAccount']),
		);
	
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}//END changeMobile
	
	/**
	 * @name 修改邮箱
	 * @author wanjun
	 * @since 2015/04/15
	 */
	public function changeEmail($cinfo = array()){
		
		$accountInfo = $this->getAccountInfo(strval($cinfo['actype']));
		//本模块需要添加的数据
		$post = array(
				"system" 	=> 'qft',
				"userName"	=> trim($accountInfo['SignAccount']),
		);
	
		//添加本模块服务代码
		$to = array_merge($this->predefine[__FUNCTION__], $post);
		$this->bulidSet($to);
		
		//记录映射关系
		$this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);
		
		//HTML方式提交
		$this->submitExecute->createHtml($this->commonPost);
		
	}//ENDchangeEmail
	
	/**
	 * 供应商开通快捷支付自助提交资料
	 * @version 1.0
	 * @author wanjun
	 * @since 2016/03/15
	 */
	public function openPaymentAccount($in = array()){

	    $merchant = new MerchantInfo();
	    $merchantInfo = $merchant->getMerchantInfo($_SESSION['uc']['CompanyID']);
	    
	    //本模块需要添加的数据
	    $post = array(
	    	"userTerminalType" => "PC",						//终端类型
	    	"registerUserType" => $in['user'],				//个人：PERSONAL 企业：ENTERPRISE 
	    	"outUserId" 	   => $this->getCompanyID(),	//外部用户id
	    	"merchOrderNo" 	   => $this->orderNo,	//外部用户id
// 	    	"userId" 	   	   => '',					    //易极付用户id
	    	"title" 		   => 1							//是否需要展示标题
	    );
	    
	    //添加本模块服务代码
	    $to = array_merge($this->predefine[__FUNCTION__], $post);
	    $this->bulidSet($to);
	    
	    //记录映射关系
	    $this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);

	    
// 	    $aa = 'merchOrderNo=20161223172221134400&notifyUrl=http://test.yitong111.com/manager/m/async.php&orderNo=20161223172221134400&outUserId=etong0000510&partnerId=20160612020000748352&protocol=httpPost&registerUserType=PERSONAL&returnUrl=http://test.yitong111.com/manager/m/online.php&service=openPaymentAccount&signType=MD5&title=1&userId=&userTerminalType=PC&version=1.0&sign=f0a524b6c6a560d0c687615da1ea380c';
	    
// 	    parse_str($aa, $aaaaa);
// 	    debug($aaaaa);
	    
// 	    debug($this->commonPost, 1);
	    //HTML方式提交
	    $this->submitExecute->createHtml($this->commonPost);
	    
	}
	
	/**
	 * @name 易极付跳转登录
	 * @author wanjun
	 * @since 2016/04/15
	 * @property
	 * 	partnerShopId 易极付平台商用户ID
	 * 	clientType	客户端类型
	 */
	public function wallet(){
		
	    $post = array(
	        "userId" 		 => $this->apiSignNO,
	    );
	    
	     //添加本模块服务代码
	    $to = array_merge($this->predefine[__FUNCTION__], $post);
	    $this->bulidSet($to);
	
	    //记录映射关系
	    $this->yOpenApiSet->SetMap($this->orderNo, '', '', $this->commonPost['sign'], 0, $this->predefine[__FUNCTION__]['service'], $this->companyID, 0);
	
	    //HTML方式提交
	    $this->submitExecute->createHtml($this->commonPost);
	}
	
	/**
	 * 为易极付准备 DHB 供应商ID
	 *
	 * @author wanjun
	 * @return string 组合后可用于易极付的ID
	 */
	private function getCompanyID(){
		//加上了偏移量
		return $this->myPrefix().str_pad(($this->companyID + GLOBAL_NUMERIC_FIXED), 7, '0', STR_PAD_LEFT);
		
	}//END getCompanyID
	
	/**
	 * 获取易极付账户信息
	 * @author wanjun
	 * @param 
	 * 	$actype string 账户ID
	 * @return 
	 * 	$accountInfo array 账户信息
	 */
	private function getAccountInfo($actype = ''){
		$NetGetWay		= new NetGetWay();
		$accountInfo	= $NetGetWay->showGetway('yijifu', $_SESSION['uc']['CompanyID'], $actype);
		return $accountInfo;
	}
	
	
}//EOC YopenApiBackend