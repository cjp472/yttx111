<?php
$ep = $locationurl = basename($_SERVER['SCRIPT_FILENAME']);
$rarr = array(".php",".php","_add","_edit");
$locationurl = str_replace($rarr,"",$locationurl);
//fixed 商品入库
$locationurl = ($ep == 'storage_add.php') ? 'storage_add' : $locationurl;

$son_description = array(
				"product"		=>	"此栏目针对商品等基础资料进行管理，主要包括：商品资料、图片、价格、品牌等资料进行维护。",
				"order"			=>	"此栏目针对订单管理，主要包括订单处理、查看、打印、导出以及统计分析。",
				"consignment"	=>	"此栏目针对物流，货运信息管理，主要包括物流公司，发货单，物流跟踪，发货信息，收货信息。",
				"finance"		=>	"此栏目针对款项管理，主要包括收款单、费用、银行账号信息、客户转账及确认。",
				"return"		=>	"此栏目针对退单管理，主要包括退单处理、查看、打印、导出以及统计分析。",
				"inventory"		=>	"此栏目针对库存管理，主要包括库存状况、入库单、出库单,库存调整等操作。",
				"client"		=>	"此栏目针对药店管理，主要包括分区管理、邀请药店关注企业号、帐号管理、审核帐号、查看登录日志等操作。",
				"saler"			=>	"此栏目针对客情官，帐号管理、权限分配、业务提成管理。",
				"infomation"	=>	"此栏目针对网站信息进行管理，栏目管理，信息管理（如：公告，公司动态，促销活动）、广告管理等",
				"forum"			=>	"此栏目针对客户留言,在线交流工具,联系方式等进行管理。",
				"sms"			=>	"此栏目提供短信群发功能，可以针对药店或其他客户发送手机短信。",
				"statistics"	=>	"此栏目针对统计分析，主要包括订单、退单、款项、库存、商品、往来对帐进行统计分析。",
				"system"		=>	"此栏目针对系统进行设置、帐号管理、登录日志、操作日志等操作。",

);

$son_menu	 = array(
				"product"			=> array(
					"product"			=> "商品管理",
					"product_picture"	=> "批量传图",
					"product_price"		=> "批量调价",
					"product_import"	=> "批量导入",
					"product_sort"		=> "商品分类",					
					"product_brand"		=> "商品品牌",
					"product_recycle"	=> "下架商品",
// 					"product_notice"	=> "到货通知"
				),
				"order"				=> array(
					"order"				=> "订单管理",
					"order_product"		=> "商品明细",
// 					"order_product_gifts"	=> "赠品明细",
// 					"order_invoice"	=>	"开票信息",
					"order_guestbook"	=>	"订单留言",
					"statistics"		=> "订单统计"
				),
				"consignment"		=> array(
					"consignment"		=> "发货管理",
					"consignment_send"	=> "发货明细",
					"consignment_order"	=> "待发货订单",
					"consignment_product"=> "待发货明细",					
					"consignment_add"	=> "新增发货单",
					"logistics"			=> "物流公司",
					"logistics_add"		=> "新增公司",
				),
				"finance"			=> array(
					"finance"			=> "收款单",
					"expense"			=> "其他款项",
					"finance_client"	=> "应收款",					
					"reconciliation"	=> "往来对账",
					"accounts"			=> "收款账户",
					"finance_stat"		=> "款项统计",
					"paytype"			=> "支付管理",
				),
				"return"			=> array(
					"return"			=> "退单管理",
					"return_product"	=> "退货明细",
					"return_stat"		=> "退单统计",
				),
				"inventory"			=> array(
					"inventory"			=> "库存状况",
					"warning"			=> "库存预警",
					"storage"			=> "入 库 单",
					"storage_list"		=> "入库明细",
					"storage_add"		=> "商品入库",
					"library"			=> "库存调整",
				),
				"client"			=> array(
					"client"			=> "药店",
					"client_list_point"	=> "药店积分",
                    //"client_wxqy"	=> "微信企业号",
                    //"client_import"     => "药店导入",
					"client_recycle"	=> "回 收 站",
					"client_log"		=> "登录日志",
					"client_area"		=> "地区管理",
				),
				"saler"				=> array(
					"saler"				=> "客情官",
					"saler_recycle"		=> "回 收 站",
					"deduct"			=> "业务提成",
					"deduct_other"		=> "其他提成",
					"statistics_deduct"	=> "提成统计",
				),
				"infomation"		=> array(
					"infomation"		=> "信息管理",
					"infomation_add"	=> "新增信息",
					"infomation_recycle"=> "回 收 站",
					"infomation_xd"		=> "广告管理",
					"sort"				=> "栏目管理",
				),
				"forum"					=> array(
					"forum"				=> "留言管理",
					"forum_tool"		=> "交流工具",
					"forum_contact"		=> "联系方式",
				),
				"sms"				=> array(
					"sms"				=> "发 短 信",
					"sms_send"			=> "发 信 箱",
					"sms_phonebook"		=> "通 讯 录",
					"sms_template"		=> "短信模板",
				),
				"statistics"		=> array(
					"statistics"		=> "订单统计",
                    "statistics_deliver"=> "发货统计",
                    "statistics_saler"  => "客情官统计",
					"return_stat"		=> "退单统计",
					"product_statistics"=> "商品统计",
                    "area_statistics"   => "地区统计",
					"finance_stat"		=> "款项统计",
					"reconciliation"	=> "往来对帐",
					"statistics_deduct"	=> "提成统计",
//					"statistics_credit"	=> "账期对账",
				),
				"system"		=> array(
					"system"			=> "系统设置",
					"user"				=> "帐号管理",
					"user_made"			=> "供应商账号",
					"user_recycle"		=> "回收站",
					"user_log"			=> "登陆日志",
					"execution_log"		=> "操作日志",
//					"pay_log"			=> "财务管理",
				)
			);
 if($_SESSION['uinfo']['userflag'] == 2){
    
        $son_menu	 = array(
				"product"			=> array(
					"product"			=> "商品管理",
					"product_picture"	=> "批量传图",
					"product_price"		=> "批量调价",
					"product_import"	=> "批量导入",
					"product_sort"		=> "商品分类",					
					"product_brand"		=> "商品品牌",
					"product_recycle"	=> "下架商品",
// 					"product_notice"	=> "到货通知"
				),
				"order"				=> array(
					"order"				=> "订单管理",
					"order_product"		=> "商品明细",
// 					"order_product_gifts"	=> "赠品明细",
// 					"order_invoice"	=>	"开票信息",
					"order_guestbook"	=>	"订单留言",
					"statistics"		=> "订单统计"
				),
				"inventory"			=> array(
					"inventory"			=> "库存状况",
					"warning"			=> "库存预警",
					"storage"			=> "入 库 单",
					"storage_list"		=> "入库明细",
					"storage_add"		=> "商品入库",
					"library"			=> "库存调整",
				),
				"client"			=> array(
					"client"			=> "药店",
					"client_list_point"	=> "药店积分",
                    //"client_wxqy"	=> "微信企业号",
                    //"client_import"     => "药店导入",
					"client_recycle"	=> "回 收 站",
					"client_log"		=> "登录日志",
					"client_area"		=> "地区管理",
				),
				"saler"				=> array(
					"saler"				=> "客情官",
					"saler_recycle"		=> "回 收 站",
					"deduct"			=> "业务提成",
					"deduct_other"		=> "其他提成",
					"statistics_deduct"	=> "提成统计",
				),
				"infomation"		=> array(
					"infomation"		=> "信息管理",
					"infomation_add"	=> "新增信息",
					"infomation_recycle"=> "回 收 站",
					"infomation_xd"		=> "广告管理",
					"sort"				=> "栏目管理",
				),
				"forum"					=> array(
					"forum"				=> "留言管理",
					"forum_tool"		=> "交流工具",
					"forum_contact"		=> "联系方式",
				),
				"sms"				=> array(
					"sms"				=> "发 短 信",
					"sms_send"			=> "发 信 箱",
					"sms_phonebook"		=> "通 讯 录",
					"sms_template"		=> "短信模板",
				),
				"statistics"		=> array(
					"statistics"		=> "订单统计",

                    "statistics_saler"  => "客情官统计",

					"product_statistics"=> "商品统计",


					"statistics_deduct"	=> "提成统计",
					"statistics_credit"	=> "账期对账",
				),
				"system"		=> array(
					"system"			=> "系统设置",
					"user"				=> "帐号管理",
					"user_made"			=> "供应商账号",
					"user_recycle"		=> "回收站",
					"user_log"			=> "登陆日志",
					"execution_log"		=> "操作日志",
//					"pay_log"			=> "财务管理",
				)
			);
    
}
			
if($_SESSION['uc']['CompanyID'] == 577){
	$son_menu	 = array(
				"product"			=> array(
					"product"			=> "商品管理",
					"product_picture"	=> "批量传图",
					"product_price"		=> "批量调价",
					"product_import"	=> "批量导入",
					"product_sort"		=> "商品分类",					
					"product_brand"		=> "商品品牌",
					"product_recycle"	=> "下架商品",
					"product_notice"	=> "到货通知"
				),
				"order"				=> array(
					"order"				=> "订单管理",
					"order_product"		=> "商品明细",
					"order_product_gifts"	=> "赠品明细",
					"order_invoice"	=>	"开票信息",
					"order_guestbook"	=>	"订单留言",
					"statistics"		=> "订单统计"
				),
				"consignment"		=> array(
					"consignment"		=> "发货管理",
					"consignment_send"	=> "发货明细",
					"consignment_order"	=> "待发货订单",
					"consignment_product"=> "待发货明细",					
					"consignment_add"	=> "新增发货单",
					"logistics"			=> "物流公司",
					"logistics_add"		=> "新增公司",
				),
				"finance"			=> array(
					"finance"			=> "收款单",
					"expense"			=> "其他款项",
					"finance_client"	=> "应收款",					
					"reconciliation"	=> "往来对账",
					"accounts"			=> "收款账户",
					"finance_stat"		=> "款项统计",
					"paytype"			=> "支付管理",
				),
				"return"			=> array(
					"return"			=> "退单管理",
					"return_product"	=> "退货明细",
					"return_stat"		=> "退单统计",
				),
				"inventory"			=> array(
					"inventory"			=> "库存状况",
					"warning"			=> "库存预警",
					"storage"			=> "入 库 单",
					"storage_list"		=> "入库明细",
					"storage_add"		=> "商品入库",
					"library"			=> "库存调整",
				),
				"client"			=> array(
					"client"			=> "药店",
					"client_list_point"	=> "药店积分",
					 "client_wxqy"	=> "微信企业号",
                    "client_import"     => "药店导入",
					"client_recycle"	=> "回 收 站",
					"client_log"		=> "登录日志",
					"client_area"		=> "地区管理",
				),
				"saler"				=> array(
					"saler"				=> "业 务 员",
					"saler_recycle"		=> "回 收 站",
					"deduct"			=> "业务提成",
					"deduct_other"		=> "其他提成",
					"statistics_deduct"	=> "提成统计",
				),
				"infomation"		=> array(
					"infomation"		=> "信息管理",
					"infomation_add"	=> "新增信息",
					"infomation_recycle"=> "回 收 站",
					"infomation_xd"		=> "广告管理",
					"sort"				=> "栏目管理",
				),
				"forum"					=> array(
					"forum"				=> "留言管理",
					"forum_tool"		=> "交流工具",
					"forum_contact"		=> "联系方式",
				),
				"sms"				=> array(
					"sms"				=> "发 短 信",
					"sms_send"			=> "发 信 箱",
					"sms_phonebook"		=> "通 讯 录",
					"sms_template"		=> "短信模板",
				),
				"statistics"		=> array(
					"statistics"		=> "订单统计",
                    "statistics_deliver"=> "发货统计",
                    "statistics_saler"  => "客情官统计",
					"return_stat"		=> "退单统计",
					"product_statistics"=> "商品统计",
                    "area_statistics"   => "地区统计",
					"finance_stat"		=> "款项统计",
					"reconciliation"	=> "往来对帐",
					"statistics_deduct"	=> "提成统计",
				),
				"system"		=> array(
					"system"			=> "系统设置",
					"user"				=> "帐号管理",
					"user_made"			=> "供应商账号",
					"user_recycle"		=> "回收站",
					"user_log"			=> "登陆日志",
					"execution_log"		=> "操作日志",
				)
			);
}else if($_SESSION['uc']['CompanyCredit'] == 1){

    $son_menu	 = array(
				"product"			=> array(
					"product"			=> "商品管理",
					"product_picture"	=> "批量传图",
					"product_price"		=> "批量调价",
					"product_import"	=> "批量导入",
					"product_sort"		=> "商品分类",					
					"product_brand"		=> "商品品牌",
					"product_recycle"	=> "下架商品",
// 					"product_notice"	=> "到货通知"
				),
				"order"				=> array(
					"order"				=> "订单管理",
					"order_product"		=> "商品明细",
// 					"order_product_gifts"	=> "赠品明细",
// 					"order_invoice"	=>	"开票信息",
					"order_guestbook"	=>	"订单留言",
					"statistics"		=> "订单统计"
				),
				"consignment"		=> array(
					"consignment"		=> "发货管理",
					"consignment_send"	=> "发货明细",
					"consignment_order"	=> "待发货订单",
					"consignment_product"=> "待发货明细",					
					"consignment_add"	=> "新增发货单",
					"logistics"			=> "物流公司",
					"logistics_add"		=> "新增公司",
				),
				"finance"			=> array(
					"finance"			=> "收款单",
					"expense"			=> "其他款项",
					"finance_client"	=> "应收款",					
					"reconciliation"	=> "往来对账",
					"accounts"			=> "收款账户",
					"finance_stat"		=> "款项统计",
					"paytype"			=> "支付管理",
				),
				"return"			=> array(
					"return"			=> "退单管理",
					"return_product"	=> "退货明细",
					"return_stat"		=> "退单统计",
				),
				"inventory"			=> array(
					"inventory"			=> "库存状况",
					"warning"			=> "库存预警",
					"storage"			=> "入 库 单",
					"storage_list"		=> "入库明细",
					"storage_add"		=> "商品入库",
					"library"			=> "库存调整",
				),
				"client"			=> array(
					"client"			=> "药店",
					"client_list_point"	=> "药店积分",
                    //"client_wxqy"	=> "微信企业号",
                    //"client_import"     => "药店导入",
					"client_recycle"	=> "回 收 站",
					"client_log"		=> "登录日志",
					"client_area"		=> "地区管理",
				),
				"saler"				=> array(
					"saler"				=> "客情官",
					"saler_recycle"		=> "回 收 站",
					"deduct"			=> "业务提成",
					"deduct_other"		=> "其他提成",
					"statistics_deduct"	=> "提成统计",
				),
				"infomation"		=> array(
					"infomation"		=> "信息管理",
					"infomation_add"	=> "新增信息",
					"infomation_recycle"=> "回 收 站",
					"infomation_xd"		=> "广告管理",
					"sort"				=> "栏目管理",
				),
				"forum"					=> array(
					"forum"				=> "留言管理",
					"forum_tool"		=> "交流工具",
					"forum_contact"		=> "联系方式",
				),
				"sms"				=> array(
					"sms"				=> "发 短 信",
					"sms_send"			=> "发 信 箱",
					"sms_phonebook"		=> "通 讯 录",
					"sms_template"		=> "短信模板",
				),
				"statistics"		=> array(
					"statistics"		=> "订单统计",
                    "statistics_deliver"=> "发货统计",
                    "statistics_saler"  => "客情官统计",
					"return_stat"		=> "退单统计",
					"product_statistics"=> "商品统计",
                    "area_statistics"   => "地区统计",
					"finance_stat"		=> "款项统计",
					"reconciliation"	=> "往来对帐",
					"statistics_deduct"	=> "提成统计",
					"statistics_credit"	=> "账期对账",
				),
				"system"		=> array(
					"system"			=> "系统设置",
					"user"				=> "帐号管理",
					"user_made"			=> "供应商账号",
					"user_recycle"		=> "回收站",
					"user_log"			=> "登陆日志",
					"execution_log"		=> "操作日志",
//					"pay_log"			=> "财务管理",
				)
			);
}
			
?>
<div class="bodyline" style="height:25px;"></div>

<div class="bodyline" style="height:32px;">

</div>
    
