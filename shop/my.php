<?php

/**
 * my 我的订货宝
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */

include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/client.php");
$input		=	new Input;
$in			=	$input->parse_incoming();

if(empty($in['m'])) $in['m'] = "profile";

if(empty($in['m']))
{
	include template("my");
}
elseif($in['m']=="profile")
{
	$setarr = commondata::getproductset('product');
	$myinfo  = client::clientinfo();
	$wxinfo  = client::weixininfo();
	$qqinfo  = client::listqqinfo();

	include template("my_profile");
}
elseif($in['m']=="remove_weixin")
{
	$in['ID'] = intval($in['ID']);

	if(!empty($in['ID'])){
		$myinfo  = client::removeweixin($in['ID']);
	}else{
		exit('操作不成功！');
	}
}
elseif($in['m']=="remove_qq")
{
	if(!empty($in['OpenID'])){
		$myinfo  = client::removeqq($in['OpenID']);
	}else{
		exit('操作不成功！');
	}

}
elseif($in['m']=="point")
{


	
	$pdata  = client::get_point();
	
	if(empty($pdata['pv']['pv'])) $pdata['pv']['pv'] = 0;
	include template("my_point");

}
elseif($in['m']=="editprofile")
{
	if(!empty($in['ClientMobile']) && !is_phone($in['ClientMobile'])) exit('请输入正确的手机号!');
	$in = $input->_htmlentities($in);
	$tr = client::editprofile($in);
	
	if($tr)
	{
		exit('ok');
	}else{
		exit('您的资料无变化...');
	}
}
elseif($in['m']=="password")
{
	$myinfo  = client::clientinfo();

	include template("my_password");
}
elseif($in['m']=="editpass")
{
	$opass = strtolower(trim($in['OldPass']));
	$npass = strtolower(trim($in['ConfirmPass']));
	if(!is_filename($opass)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($opass) < 3 || strlen($opass) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(!is_filename($npass)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($npass) < 3 || strlen($npass) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');

	$in = $input->_htmlentities($in);
	$tr = client::editpass($opass,$npass);

	if($tr)
	{	unset($_SESSION['Initial_pass']);
		exit('ok');
	}else{
		exit('原密码不正确...');
	}	
}
elseif($in['m']=="address")
{

	$addlist = client::listaddress();	

	include template("myaddress");
}
elseif($in['m']=="addaddress")
{
	if(empty($in['AddressContact']) || empty($in['AddressPhone'])) exit('联系人 / 联系电话 / 地址 不能为空!');

	$in = $input->_htmlentities($in);
	$tr = client::addaddress($in);

	if($tr)
	{
		exit('ok');
	}else{
		exit('添加失败...');
	}
}
elseif($in['m']=="editaddress")
{
	if(empty($in['AddressID'])) exit('参数错误!');
	if(empty($in['AddressContact']) || empty($in['AddressPhone'])) exit('联系人 / 联系电话 / 地址 不能为空!');

	$in = $input->_htmlentities($in);
	$tr = client::editaddress($in);

	if($tr)
	{
		exit('ok');
	}else{
		exit('添加失败...');
	}
}
elseif($in['m']=="deladdress")
{
	if(empty($in['ID'])) exit('请指定要删除的送货地址!');	

	$tr = client::deladdress($in['ID']);
	if($tr)
	{
		exit('ok');
	}else{
		exit('删除失败...');
	}
}
elseif($in['m']=="getSear"){
	
	$startDate	= date('Y-m-d', strtotime('-30 days'));
	$endtDate	= date('Y-m-d');

	include template("myGetSear");
}
elseif($in['m']=="execute"){
	
	//$_SESSION['YJForderNo'] 来自于 YOpenApi 接口
	$fileName = YOPENAPI_MESSAGE_PATH.md5($_SESSION['YJForderNo']);
	if(file_exists($fileName)){
		parse_str(file_get_contents($fileName), $parse);
		$_SESSION['YJFSynInfo'] = '';
		$_SESSION['YJFSynInfo'] = $parse;
		unlink($fileName);
	}
	
	echo $in['jp']."('".(isset($_SESSION['YJFSynInfo']) && !empty($_SESSION['YJFSynInfo']))."')";
	exit;
}elseif($in['m']=="getSearInfo"){//易极付提现
	
	if(empty($in['sdate'])) date('Y-m-d', strtotime('-30 days'));
	if(empty($in['edate'])) date('Y-m-d');
	
	$in['currPage'] = intval($in['currPage']);
	$in['pageSize'] = intval($in['pageSize']);
	$currPage = $in['currPage'] ? $in['currPage'] : 1;
	$pageSize = $in['pageSize'] ? $in['pageSize'] : 5;
	
	$search = array(
			'currPage'	=> $currPage,
			'pageSize'	=> $pageSize,
			'startTime'	=> $in['sdate']." 00:00:00",
			'endTime'	=> $in['edate']." 23:59:59"
	);
	
	$YOpenApi = new YOpenApi($_SESSION['cc']['cid']);
	$searInfo = $YOpenApi->getSear($search);
	
	echo json_encode($searInfo);
	exit;
}elseif($in['m'] == 'certifyStatus'){
    
    //获取易极付账号
    $myinfo = client::clientinfo();
    
    //同步获取开户信息
    $yopen  = new YopenApiFront();
    $msg    = $yopen->certifyStatus($myinfo['YapiuserName']);
    
    $status = array(
                    'UNAUTHERIZED'  => '未认证',
                    'AUTHORIZED'    => '已认证',
                    'INAUTHORIZED'  => '认证中',
                    'REJECT'        => '被驳回',
                    'UNOPEN'        => '未开通快捷支付'
    );
    
    $msg = $msg ? $msg : array_merge($msg, array('certifyStatus' => 'UNOPEN'));

    echo json_encode(array('status' => $msg['certifyStatus'], 'msg' => $status[$msg['certifyStatus']]));
    exit;
}else if($in['m'] == 'onlinepay'){
	
	$NetGetWay = new NetGetWay();
	$accinfo = $NetGetWay->showGetway('yijifu', $_SESSION['cc']['ccompany']);
	
	//供应商是否开户
	if(empty($accinfo)){
		
		die(json_encode(array('status' => 'error', 'message' => '请在商业完成开户后进行支付')));
	}
	
	//药店/诊所是否开户
	$ySet = new YOpenApiSet();
	$myinfo = $ySet->getSignInfo($_SESSION['cc']['cid']);
	//$myinfo="";
	//验证是否显示弹窗
	$client= new client();
	$client_info=$client->clientinfo();
	if(!empty($in['type']) && $in['type']=="Check_onlinepay" && empty($myinfo)){
		
		$phone=$client_info['ClientMobile'];

		die(json_encode(array('status' => 'errors1', 'message' => '药店/诊所未开户','phone'=>$phone)));
	} 

	//修改用户的手机号码
	if(!empty($in['type']) && $in['type']=="edit_phone" && empty($myinfo)){
		if($client_info['ClientMobile'] != $in['phone'] && !empty($in['phone'])){
		$client_info['ClientMobile']=$in['phone'];
		
		$data=array(
			'ClientMobile'=>$client_info['ClientMobile']
			);
		
		$res=$client->edit_user_phone($data);
		if(!$res){
			echo json_encode(array('status' => 'errors2', 'message' => '药店/诊所手机号码修改失败！'));exit;
		}
		
		}
	}	

	if(empty($myinfo)){//还没开户，执行开户操作
		
		$front = new YopenApiFront($_SESSION['cc']['cid']);
		$aynResponse = $front->ppmNewRuleRegisterUser();
		if($aynResponse['status'] == 'error') die(json_encode($aynResponse));

		$myinfo = $ySet->getSignInfo($_SESSION['cc']['cid']);
	}
	//处理完毕，前往收银台
	echo json_encode(array('status' => 'success', 'message' => '系统即将前往支付'));	
	exit;
}else if($in['m'] == 'bank_notice'){
	$client= new client();
	$bank_notice=$client->notice_info();
	if($bank_notice){
		echo json_encode(array('status' => 'success', 'message' => '存在银行提示信息!','data'=>$bank_notice));	
		exit;
	}else{
		echo json_encode(array('status' => 'error', 'message' => '没有提示信息!','data'=>''));	
		exit;
	}
	//var_dump($bank_notice);
	
}else if($in['m'] == 'qualification'){ 
	$result  = client::get_Merchant();
        $result['CompanyName'] = $_SESSION['cc']['ccompanyname'];
        if(empty($result['data'])){
            $result['data'] ='';
        }
        $CatdImg = $result['data']['IDCardImg'];
        $IDCatdImg = explode(',',$CatdImg);
	include template("qualification");
}else if($in['m'] == 'addQualification'){
    $result  = client::add_Merchant($in);
    if($result) exit('ok'); else exit('操作不成功!');
}else if($in['m'] == 'PaypwdAdd'){
    //查看是否激活医统账期
    $MainCreditStatus = client::CreditMain($in);
    
    //查询是否已设置密码
    $alreadyPwd = client::getPayPwdMobile();
    
	//安全验证  账期情况 
    if(empty($MainCreditStatus)){
    	echo JsonForAPI::formatMsg('error', '您还未开通医统账期，不能设置密码');
    	exit;
    }elseif($MainCreditStatus['CreditStatus'] == 'closed'){
    	echo JsonForAPI::formatMsg('error', '您的医统账期已被冻结，不能设置密码');
        exit;
    }else if(!empty($alreadyPwd)){
    	echo JsonForAPI::formatMsg('error', '您已设置医统账期支付密码');
    	exit;
    }
    
    //设置医统账期支付密码 方法   
    //增加
    $in['mobile']		= trim($in['mobile']);
    $in['valiCode']		= trim($in['valicode']);
    $in['payPwd']		= trim($in['pay_pwd']);
    $in['payPwdQue']	= trim($in['pay_pwd1']);
    
    if(empty($in['valiCode']) || empty($_SESSION['paymobilecode']) || $in['valiCode'] != $_SESSION['paymobilecode']){//判断验证码
      echo JsonForAPI::formatMsg('error', '验证码错误或请刷新页面重新获取');
      exit;
    }elseif((time() - $_SESSION['paymobiletime']) > (5 * 60)){//5分钟有效期
      echo JsonForAPI::formatMsg('error', '验证码已过5分钟有效期，请刷新页面重新获取');
      exit;
    }elseif(empty($in['mobile']) || strlen($in['mobile']) != 11 || !is_numeric($in['mobile'])){//验证手机号
      echo JsonForAPI::formatMsg('error', '为保障您的资金安全，请输入正确的安全手机号码');
      exit;
    }else if(empty($in['payPwd']) || empty($in['payPwdQue']) || !is_numeric($in['payPwd']) || strlen($in['payPwd']) != 6){//判断支付密码不能空且必须6位数字
      echo JsonForAPI::formatMsg('error', '请设置6位数字支付密码');
      exit;
    }else  if($in['payPwd'] != $in['payPwdQue']){//两次密码
      echo JsonForAPI::formatMsg('error', '两次输入密码输入不一致');
      exit;
    }
	
     //保存密码和手机号
     $result  = client::PaypwdAdd($in, $type='add');
     if($result){//设置成功
     	
     	//发送验证码短信
     	$lastnum = substr($in['mobile'], 7);
     	$sms = new SmsApp();
     	$sms->getSmsTpl('YTZQSETSUCCESS')
     		->bulidContent(array('{SECURITYCODE}', '{LASTNUM}'), array($code, $lastnum))
     		->SendSMS($in['mobile'])
     		->logStatus($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);
     	
     	//清空验证码存储
     	unset($_SESSION['paymobilecode'], $_SESSION['paymobiletime']);
        echo JsonForAPI::formatMsg('success', '医统账期独立支付密码设置成功');
        exit;
     }
                 
    }else if($in['m'] == 'PaypwdUpdate'){//修改账期支付密码
    	//@todo 业务逻辑重复度极高，下来需要重写逻辑
    	
    	//查看是否激活医统账期
    	$MainCreditStatus = client::CreditMain($in);
    	
    	//查询是否已设置密码
    	$alreadyPwd = client::getPayPwdMobile();
    	
    	//安全验证  账期情况
    	if(empty($MainCreditStatus)){
    		echo JsonForAPI::formatMsg('error', '您还未开通医统账期，不能设置密码');
    		exit;
    	}elseif($MainCreditStatus['CreditStatus'] == 'closed'){
    		echo JsonForAPI::formatMsg('error', '您的医统账期已被冻结，不能设置密码');
    		exit;
    	}else if(empty($alreadyPwd)){
    		echo JsonForAPI::formatMsg('error', '您还没设置医统账期支付密码，请先开通或设置');
    		exit;
    	}
    	
    	//设置医统账期支付密码 方法
    	//增加
    	$in['mobile']		= trim($in['mobile']);
    	$in['valiCode']		= trim($in['valicode']);
    	$in['payPwd']		= trim($in['pay_pwd']);
    	$in['payPwdQue']	= trim($in['pay_pwd1']);
    	
    	if(empty($in['valiCode']) || empty($_SESSION['paymobilecode']) || $in['valiCode'] != $_SESSION['paymobilecode']){//判断验证码
    		echo JsonForAPI::formatMsg('error', '验证码错误或请刷新页面重新获取');
    		exit;
    	}elseif((time() - $_SESSION['paymobiletime']) > (5 * 60)){//5分钟有效期
    		echo JsonForAPI::formatMsg('error', '验证码已过5分钟有效期，请刷新页面重新获取');
    		exit;
    	}elseif(empty($in['mobile']) || strlen($in['mobile']) != 11 || !is_numeric($in['mobile'])){//验证手机号
    		echo JsonForAPI::formatMsg('error', '为保障您的资金安全，请输入正确的安全手机号码');
    		exit;
    	}else if(empty($in['payPwd']) || empty($in['payPwdQue']) || !is_numeric($in['payPwd']) || strlen($in['payPwd']) != 6){//判断支付密码不能空且必须6位数字
    		echo JsonForAPI::formatMsg('error', '请设置6位数字支付密码');
    		exit;
    	}else  if($in['payPwd'] != $in['payPwdQue']){//两次密码
    		echo JsonForAPI::formatMsg('error', '两次输入密码不一致');
    		exit;
    	}
    	
    		//保存密码和手机号
    		$result  = client::PaypwdAdd($in, $type='update');
    		if($result){//设置成功
    	
    			//发送验证码短信
    			$lastnum = substr($in['mobile'], 7);
    			$sms = new SmsApp();
    			$sms->getSmsTpl('YTZQPAYPWDRETSET')
    				->bulidContent(array('{SECURITYCODE}', '{LASTNUM}'), array($code, $lastnum))
    				->SendSMS($in['mobile'])
    				->logStatus($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);
    	
    			//清空验证码存储
    			unset($_SESSION['paymobilecode'], $_SESSION['paymobiletime']);
    			echo JsonForAPI::formatMsg('success', '医统账期独立支付密码已修改成功');
    			exit;
    		}
    }else if($in['m'] == 'PaypwdSel'){
			$PayPassword = trim ( $in ['PayPassword'] );
			
			//必须是6位数字的密码
			if (empty ( $PayPassword ) || ! is_numeric ( $PayPassword ) || strlen ( $PayPassword ) != 6) {
				echo JsonForAPI::formatMsg ( 'error', '请输入正确的支付密码' );
				exit ();
			}
			
			// 密码匹配
			$result = client::PaypwdSel ( $in );
			if (! empty ( $result ) && $result['ErrorTimes'] <= 3) {//每天三次错误
				// 账户余额
				$ResultMain = client::CreditMain ( $in );
				$order_id = intval($in['orderID']);
				
				// 根据传入订单ID查询订单信息
				$orderInfo = orderdata::getorderinfo ( $order_id );
				
				if (empty ( $order_id ) || empty ( $orderInfo )) {
					echo JsonForAPI::formatMsg ( 'error', '支付订单与实际支付订单信息不一致' );
					exit ();
				}elseif($orderInfo['OrderPayStatus'] == 2){
					echo JsonForAPI::formatMsg ( 'notice', '订单 '.$orderInfo['OrderSN'].' 已完成支付' );
					exit ();
				}
				
				if ($ResultMain ['ResidueAmount'] < $orderInfo ['OrderTotal']) {
					echo JsonForAPI::formatMsg ( 'error', '您的医统账期余额' . $ResultMain ['ResidueAmount'] . '元不足支付，建议还款恢复额度' ); // 亦可引导去还款
					exit ();
				} else {
					// 增加账期交易记录
					$in ['orderSn'] = $orderInfo ['OrderSN'];
					$in ['orderID'] = $orderInfo ['OrderID'];
					$in ['OrderTotal'] = $orderInfo ['OrderTotal'];
					$detailAdd = client::detailAdd ( $in );
					
					if ($detailAdd) {
						// 修改账期余额
						$MainUpdate = client::MainUpdate ( $in, $orderInfo ['OrderTotal'] );						
						if ($MainUpdate) {
							// 增加付款单
							$order ['finance_type'] = 'C';
							$order ['mobile'] = $result ['Mobile'];
							$order ['data_FinanceOrder'] = array (
									$orderInfo ['OrderSN'] 
							);
							$order ['data_FinanceOrderID'] = $orderInfo ['OrderID'];
							$order ['data_FinanceTotal'] = $orderInfo ['OrderTotal'];
							$order ['data_FinanceToDate'] = date ( 'Y-m-d' );
							$order ['data_FinanceAccounts'] = 0;
							$order ['data_FinanceAbout'] = '医统账期支付';
							orderdata::subaccounts ( $order ); // 付款单记录
							orderdata::save_guestbook($orderInfo['OrderID'], '使用医统账期支付订单', '支付订单');
							echo JsonForAPI::formatMsg ( 'success', '订单支付成功' );
						} else {
							echo JsonForAPI::formatMsg ( 'error', '订单支付失败' );
						}
						exit ();
					} else {
						echo JsonForAPI::formatMsg ( 'error', '订单支付失败' );
						exit ();
					}
				}
			} else {
				//每天三次输入错误
				$errorInfo = client::updateErrorTimes();
				$diff = 3 - ($errorInfo['ErrorTimes']);
				$message = ($diff <= 0) ? '为保障资金安全平台已冻结您的账期或请重置密码' : '密码错误，今日还有'.$diff.'次输入次数';
				echo JsonForAPI::formatMsg ( 'error', $message );
				exit ();
			}
    }elseif($in['m'] == 'credit'){
        //账期概要
        $detailAdd  	= client::MyCredit($in);
        $detailApply  	= client::MyCreditApplyStatus($in);
        $UpMonth 		= client::UpMonth($in); //上月账期欠款
        $Amount 		= round($detailAdd['Amount'],2);											//总额度
        $ResidueAmount  = round($detailAdd['ResidueAmount'],2);										//剩余额度
        $usedAmount 	= round(($detailAdd['Amount'] - $detailAdd['ResidueAmount']), 2);			//已用额度
        $Residuelu 		= floatval(($ResidueAmount / $Amount) * 100);								//剩余占比
        $usedLu 		= floatval(($usedAmount / $Amount) * 100);									//已用占比
        
        $credit = new Credit();
        $creditDate  = $credit->getCreditDate($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);
        $creditMoney = $credit->getCreditMoney($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);	//上个账期还款额
        
        $BottomCommon = $credit->BottomCommon();
        include template("my_credit");
    }elseif($in['m'] == 'creditDetail'){
    	
	    	//todo 条件交换
    		$in['isunset'] = false;
	    	if($in['billtype'] == 'isup'){
	    		$in['isup'] = 'true';
	    		$in['isunset'] = true;
	    		$credit = new Credit();
	    		$creditDate  = $credit->getCreditDate($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);
	    		$in['enddate'] = $creditDate['end'];
	    		$in['begindate'] = $creditDate['start'];
	    		unset($in['billtype']);
	    	}

            //账期详情页
            $detailAdd  = client::creditDetail($in);
            
            if($in['isunset'] == true) $in['billtype'] = 'isup';
            
            //还款额 
            $totaoFen = ($detailAdd['sql_Sel']['TotalSum']+$detailAdd['sql_Sel']['InterestSum']+$detailAdd['sql_Sel']['FineSum']);
            $TotalSumMoneyFormat = MoneyFormat::MoneyOfFenToYuan($totaoFen, true);
            
            //每列计算
            $suMToal = $suMInterest = $sumOverdueFine = $sumTotalf = $suMToalJian = $sumTotalfJian = 0; //各列金额初始化
            foreach ($detailAdd['list'] as $k => $v){
                if($v['Type'] == 'out'){//支付
                	$v['OrderTotal'] = (+$v['OrderTotal']);
                }elseif($v['Type'] == 'in'){//退款
                	$v['OrderTotal'] = (-$v['OrderTotal']);
                	$v['sumTotalf']  = (-$v['sumTotalf']);
                }
                
                $suMToal = $suMToal + $v['OrderTotal'];					//订单金额
                $suMInterest = $suMInterest+$v['Interest'];				//利息
                $sumOverdueFine = $sumOverdueFine+$v['OverdueFine'];	//滞纳金
                $sumTotalf = $sumTotalf+$v['sumTotalf'];				//结算金额
                
                $v['OrderTotal']  = MoneyFormat::MoneyOfFenToYuan($v['OrderTotal'], true);
                $v['Interest']    = MoneyFormat::MoneyOfFenToYuan($v['Interest'], true);
                $v['OverdueFine'] = MoneyFormat::MoneyOfFenToYuan($v['OverdueFine'], true);
                $v['sumTotalf']   = MoneyFormat::MoneyOfFenToYuan($v['sumTotalf'], true);
                
                $detailAdd['list'][$k] = $v;
            }
            
            //格式化金额
            $suMToal = MoneyFormat::MoneyOfFenToYuan($suMToal, true);
            $suMInterest = MoneyFormat::MoneyOfFenToYuan($suMInterest, true);
            $sumOverdueFine = MoneyFormat::MoneyOfFenToYuan($sumOverdueFine, true);
            $sumTotalf = MoneyFormat::MoneyOfFenToYuan($sumTotalf, true);
            
            //7天
            $sevenPre = date('Y-m-d'); 
            $sevenSub = date('Y-m-d', time() - (6 * 24 * 3600));	//包含当天
            $sevenStatus = ($in['enddate'] && $in['begindate']) ? (strtotime($in['enddate']) - strtotime($in['begindate']))/(24*3600) : '';
            
             //本月
            $monthSub = date('Y-m').'-01';	//包含当天
            $monthPre = date('Y-m-d');
            $monthStatus = ($in['begindate'] == $monthSub && $in['enddate'] == $monthPre) ? true : false;
            
        include template("my_credit_detail");  
      
    }elseif($in['m'] == 'creditApply'){
    	//账期概要
    	$detailAdd  	= client::MyCredit($in);
    	if($detailAdd['CreditStatus'] == 'open') {//已开通的不能再申请开通
    		header('location:' . WEB_ROOT_URL);
    		exit;
    	}
    	
        //申请账期 页面
        $creditApply = client::creditApply();
        
        include template("my_credit_apply");
    }elseif($in['m'] == 'AddcreditApply'){
    	
        $Level = $in['Level'];
        $LevelOther = $in['LevelOther'];
        $PurchaseAmount = $in['PurchaseAmount'];
        $Amount = $in['Amount'];

        $creditApply = client::AddcreditApply($in);
        if($creditApply){
        	//发送短信
        	$ccompanyname = trim($_SESSION['cc']['ccompanyname']);
            $sms = new SmsApp();
            $sms->getSmsTpl('YTZQRAPPLY')
	            ->bulidContent(array('{CLIENTNAME}'), array($ccompanyname))
	            ->SendSMS(YAPI_RECEIVE_PHONE)
            	->logStatus($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);

            	echo'ok';
        }else{
            echo'操作失败';
        }
    }elseif($in['m']=='SelQu'){
        //查询是否开通
        $selQu= client::SelQu($in);
        echo $selQu['CreditStatus'];die;
    }elseif($in['m']=='CreditPass'){
    	$storeMobile = client::getPayPwdMobile();
    	
    	//隐藏部分手机号码
    	$mobileReapet = $storeMobile['Mobile'] ? substr($storeMobile['Mobile'], 0, 3).'****'.substr($storeMobile['Mobile'], 7) : '';
    	
    	$tpl = $storeMobile ? "my_credit_resetpass" : "my_credit_setpass";
    	
    	include template($tpl);
    }elseif($in['m'] == 'getSecurityCode'){
    	
    	$in['mobile'] = trim($in['mobile']);
	
    	//是否合法
    	if(strlen($in['mobile']) != 11 || !is_numeric($in['mobile'])){
    		
    		echo json_encode(array('status' => 'error', 'message' => '请输入正确的手机号码'), JSON_UNESCAPED_UNICODE);
    		exit;
    	}
    	
    	//验证手机号是否与初次设置时一致
		$storeMobile = client::getPayPwdMobile();
		if(!empty($storeMobile) && $in['mobile'] != $storeMobile['Mobile']){
			
			echo json_encode(array('status' => 'error', 'message' => '当前手机号码与设置号码不一致'), JSON_UNESCAPED_UNICODE);
			exit;
		}
		
		//60秒内只能获取一次
		$passeTime 	 = time() - (int)$_SESSION['paymobiletime'];
		$residueTime = 60 - $passeTime;
		if(isset($_SESSION['paymobiletime']) && $residueTime > 0 && $residueTime < 60){
			echo json_encode(array('status' => 'unreach', 'message' => '60秒内请勿重复获取，验证码有效期5分钟'.$_SESSION['paymobilecode'], 'residuetime' => $residueTime), JSON_UNESCAPED_UNICODE);
			exit;
		}else{
			//发送验证码短信
	    	$code = rand(1034, 9999);
	    	$_SESSION['paymobilecode'] = $code;
	    	$_SESSION['paymobiletime'] = time();
	    	$lastnum = substr($in['mobile'], 7);
	    	$sms = new SmsApp();
	    	$sms->getSmsTpl('YTZQSecurityCode')
	    		->bulidContent(array('{SECURITYCODE}','{LASTNUM}'), array($code, $lastnum))
	    		->SendSMS($in['mobile'])
	    		->logStatus($_SESSION['cc']['ccompany'], $_SESSION['cc']['cid']);

// 	    	echo json_encode(array('status' => 'success', 'message' => $code), JSON_UNESCAPED_UNICODE);
	    	echo json_encode(array('status' => 'success', 'message' => '请填写您收到的4位数字验证码'), JSON_UNESCAPED_UNICODE);
                
	    	exit;
		}
    }elseif($in['m'] == 'validatePayCode'){//验证授信验证码
    	
    	$vcCode = trim($in['code']);
    	if($vcCode == $_SESSION['paymobilecode']){
    		echo json_encode(array('status' => 'success', 'message' => '短信验证码正确'), JSON_UNESCAPED_UNICODE);
    	}else{
    		echo json_encode(array('status' => 'error', 'message' => '请输入正确的短信验证码'), JSON_UNESCAPED_UNICODE);
    	}
    	exit;
    }


//END
?>