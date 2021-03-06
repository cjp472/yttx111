<?php
	$order_from = array(
		'Compute'						=>  '电脑',
		'WeiXin'						=>  '微信',
		'Ios'							=>  '苹果',
		'Andirod'						=>  '安卓',
		'Api'							=>  '接口'
	 );


	$pay_status_arr = array(
		'0'			=>  '未付款',
		'1'			=>  '付款中',
		'2'			=>  '已付款',
		'3'			=>  '预付款'
 	 );
	
	$send_status_arr = array(
		'0'			=>  '待审核',
		'1'			=>  '备货中',
		'2'			=>  '已发货',
		'3'			=>  '未发完',
		'4'			=>  '已收货'
 	 );

	$order_status_arr = array(
		'0'			=>  '待审核',
		'1'			=>  '备货中',
		'2'			=>  '已出库',
		'3'			=>  '已收货',
		'5'         =>  '已收款',
		'7'         =>  '已完成',
		'8'         =>  '客户端取消',
		'9'         =>  '管理端取消'
	 );

	$pay_send_arr = array(
		'1'			=>  '已付',
		'2'			=>  '到付'
 	 );

	$incept_arr = array(
		'0'			=>  '在途',
		'1'			=>  '确认收货',
		'2'			=>  '管理员确认'
 	 );

	$finance_arr = array(
		'0'			=>  '在途',
		'2'			=>  '确认到帐'
 	 );

	$return_status_arr = array(
		'0'			=>  '待审核',
		'1'			=>  '未通过',
		'2'			=>  '已审核',
		'3'			=>  '已收货',
		'5'         =>  '已完成',
		'8'         =>  '客户取消',
		'9'         =>  '管理员取消',
 	 );

	//wangd 2017-11-30 与PC端同步支付类型
	$paytypearr = array (
	  '1' => '现金(先付)',
	  '2' => '转帐(先付)',
	  //'3' => '在线支付(先付)',
	  '4' => '转帐(后付)',
	  '5' => '代收款',
	  '6' => '月结',
	  '7' => '预存款(先付)',
	  '8' => '货到付款',
	  '9' => '快捷支付',
	  '10' => '网银支付',
	  '11' => '支付宝支付'
	);

	$senttypearr = array (
	  '1' => '送货上门',
	  '2' => '快递',
	  '3' => '货运',
	  '4' => '上门自取'
	);

	$invoicetypearr = array (
	  'N' => '不开票',
	  'P' => '普票',
	  'Z' => '增票'
	);

	$paytypeidarr = array('1','2','3','7');

	$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '[推荐]',
		'2'			=>  '[特价]',
		'3'			=>  '[新款]',
		'4'			=>  '[热销]',
		'9'			=>  '[缺货]'
 	 );

	$setfieldarr['order'] = array(
		'NO'							=>  '行号',
		'Coding'						=>  '编号',
		'ContentName'					=>  '商品名称',
		'Barcode'						=>  '条码',
		'ContentColor'					=>  '颜色',
		'ContentSpecification'			=>  '规格',
		'ContentNumber'					=>  '数量',
		'Units'							=>  '单位',
		'Casing'						=>  '包装',
		'Price1'						=>  '价格1',
		'Price2'						=>  '价格2',	
		'ContentPrice'					=>  '单价',
		'ContentPercent'				=>  '折扣',
		'PercentPrice'					=>  '折后价',
		'LineTotal'						=>  '金额'
	 );
	//$disarr['order'] =  array('ContentName','ContentNumber','PercentPrice','LineTotal');
	$disarr['order'] =  array('ContentName','ContentNumber');

	$setfieldarr['return'] = array(
		'NO'							=>  '行号',
		'Coding'						=>  '编号',
		'ContentName'					=>  '商品名称',
		'Barcode'						=>  '条码',
		'ContentColor'					=>  '颜色',
		'ContentSpecification'			=>  '规格',
		'ContentNumber'					=>  '数量',
		'Units'							=>  '单位',
		'Casing'						=>  '包装',
		'ContentPrice'					=>  '单价',
		'LineTotal'						=>  '金额'
	 );
	$disarr['return'] =  array('ContentName','ContentNumber');

	$setfieldarr['send'] = array(
		'NO'							=>  '行号',
		'Coding'						=>  '编号',
		'ContentName'					=>  '商品名称',
		'Barcode'						=>  '条码',
		'ContentColor'					=>  '颜色',
		'ContentSpecification'			=>  '规格',
		'ContentNumber'					=>  '数量',
		'Units'							=>  '单位',
		'Casing'						=>  '包装',
		'Price1'						=>  '价格1',
		'Price2'						=>  '价格2',	
		'ContentPrice'					=>  '单价',
		'ContentPercent'				=>  '折扣',
		'PercentPrice'					=>  '折后价',
		'LineTotal'						=>  '金额'
	 );
	$disarr['send'] =  array('ContentName','ContentNumber');

	$setfieldarr['library'] = array(
		'NO'							=>  '行号',
		'Coding'						=>  '编号',
		'Name'							=>  '商品名称',
		'Barcode'						=>  '条码',
		'ContentColor'					=>  '颜色',
		'ContentSpec'					=>  '规格',
		'Casing'						=>  '包装',
		'ContentNumber'					=>  '数量',
		'Units'							=>  '单位'

	 );
	$disarr['library'] =  array('Name','ContentNumber');

	$prefixarr = array('soap','admin','manager','client','mobile','shouji','weixin','www','app','ipad','shop','store','think','rsung','jxc','erp','crm','com');
?>