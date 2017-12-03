<?php
include_once ("../common.php");
include_once ("../class/login.class.php");

$topAdminArr = array(1);	//超级管理账户
$adminArr    = array();				//管理员
$kfArr       = array();  //客服权限
$sqArr		 = array();		//销售

$allAdminArr = array_merge($adminArr, $topAdminArr);
$allArr1 = array_merge($allAdminArr,$kfArr);
$allArr = array_merge($allArr1,$sqArr);
if(!in_array($_SESSION['uinfo']['userid'],$allArr)) exit('非法路径!');

$inv = new Input();
$in  = $inv->parse_incoming();
$db  = dbconnect::dataconnect()->getdb();
if(in_array($_SESSION['uinfo']['userid'],$kfArr)){
	$menu_arr	 = array(
					"feedback"			=> "反馈",					
					"common_count"		=> "使用",
					"ty"				=> "体验"
			);
}elseif(in_array($_SESSION['uinfo']['userid'],$sqArr)){
	$menu_arr	 = array(
					"sale_count"		=> "销售",
					"open"				=> "注册",
					"smscode"			=> "校验码",
					"feedback"			=> "反馈",
					"ty"				=> "体验"
			);
}elseif(in_array($_SESSION['uinfo']['userid'],$adminArr)){
	$menu_arr	 = array(
// 					"sale_count"		=> "销售",
					"manager"			=> "客户",
// 					"company_order"		=> "订单",
// 					"open"				=> "注册",
// 					"smscode"			=> "校验码",
// 					"ty"				=> "体验",
// 					"feedback"			=> "反馈",					
					"common_count"		=> "使用",	
					"analysis"			=> "分析",
// 					"agent"				=> "代理商",
// 					"finance_log"		=> "财务",
					"company_log"		=> "日志",
					"credit_client"			=> "医统账期",
			);
}elseif(in_array($_SESSION['uinfo']['userid'],$topAdminArr)){
	$menu_arr	 = array(
// 	                "sale_count"		=> "销售",
					"manager"			=> "客户",
// 					"company_order"		=> "订单",
// 					"open"				=> "注册",
// 					"smscode"			=> "校验码",
// 					"ty"				=> "体验",
// 					"feedback"			=> "反馈",					
					"analysis"			=> "分析",
					"common_count"		=> "使用",
					"company_log"		=> "日志",									
//					"agent"				=> "代理商",
					"credit_client"			=> "医统账期",
// 					"finance_log"		=> "财务",				
					"notice_list"		=> "平台维护"				
			);
}


$son_menu	 = array(
                "sale_count"			=> array(
                    "sale_count"		=> "销售统计",
                    "sale"		        => "销售人员",
                ),
				"open"			=> array(
					"open"			         => "注册客户",
                    "generalize"			 => "推广关系",
                    "generalize_info"		 => "推广统计",
                    "company_verify_check2"  => "审核日志",
                    'open_statistics'        => '统计来源',
				),
				"common_count"			=> array(
				    "pay_count"		    => "支付数据",
					"common_count"		=> "客户数据",		
				    "visit"			    => "回访记录",
				    "contacts"			=> "联系人",
				),
				"manager"			=> array(
					"manager"				=> "客户管理",
                    "company_erp"           => "客户ERP",
                    "company_pay"           => "客户支付",
					"company_verify"		=> "客户审核",
                    "company_verify_check"  => "审核日志",
					"regiester"				=> "原注册客户"

				),
				"company_log"			=> array(
					"company_log"			=> "跟踪日志",					
					"manager_user_log"		=> "登陆日志",
					"error_log"				=> "错误日志",					
					"execution_admin_log"	=> "操作日志",
				),
				"company_order"			=> array(
					"company_order"			=> "客户订单",			
					"company_stream"		=> "支付信息",
					"company_invoice"		=> "开票申请",
				),
                "ty"                    => array(
                    'ty'                    => '体验',
                    'ty_analyze'            => '分析',
                    'ty_statistics'         => '统计',
                ),
                "feedback"              => array(
                    'feedback'              => '客户反馈',
                    'ty_feedback'           => '体验反馈',
                ),
				"notice_list"              => array(
					'notice_list'           => '平台信息',
					'notice_type'           => '信息分类',
					'base_classify'       => '基础分类',
					'special_classify'       => '特定分类',
                ),
				"credit_client"              => array(
						'credit_client'      => '客户列表',
//						'credit'      => '账期详情',
						'sms_send' 	  => '短信列表',
                                                'credit_info' =>'账期详情'
				),

			);

if($_SESSION['uinfo']['userid'] == 12403){
	unset($son_menu['common_count']);
	$son_menu['common_count']['visit'] = "回访记录";
}


 $pay_arr = array(
     	'alipay'			=>  '支付宝',
		'allinpay'			=>  '通联支付',
		'line'  		    =>  '线下'
  	 );
  	 
  $contact_arr = array(
     	'0'					=>  '未查看',
		'1'					=>  '正常',
		'2'  		    	=>  '已回访',
		'9'  		    	=>  '已删除'
  	 );
	 
  $audit_arr = array(
     	'T'					=>  '审核通过',
		'F'					=>  '审核不过',
		'D'  		    	=>  '待审核',
		'W'  		    	=>  '未上传'
  	 );

  $from_arr = array(
     	'Compute'			=>  '电脑',
		'WeiXin'			=>  '微信',
		'Android'  		    =>  '安卓',
		'Ios'  		    	=>  '苹果',
		'Mobile'			=>  '手机'
  	 );
  
  /** 销售部门  **/
  $sale_depart = array(
        '1'			        =>  '成都',
        '2'			        =>  '网销'
  );

$pope_arr = $menu_arr;

$yunType = array('dhb'=>'','ali'=>'阿里','shuan'=>'曙安','teeny' => '天力精算','wxqy' => '微信企业');
?>