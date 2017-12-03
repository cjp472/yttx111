<?php

class SmsTpl{
	
	private $_smsTpl = array(
			"YJFOpenApiReapet"		=> "开户回推信息异常：已开户，但再次收到开户回推。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
			"YJFOpenApiLessZero"	=> "开户回推信息异常：修正供应商ID后，小于0。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
			"YJFOpenApiSuccess"		=> "写入开户数据成功。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
			"YJFOpenApiFailed"		=> "写入开户数据失败。当前供应商简称：{COMPANYSIGNED}，ID：{MERCHANTID}，开户类型：{USERTYPE}。",
			"YTZQForRepayment"		=> "尊敬的客户，您的医统账期上个账单日总计消费{REPAYMENTTOTAL}元，请于{REPAYMENTDATE}日前还清，以免产生利息和影响您的信用",
			"YTZQSecurityCode"		=> "验证码：{SECURITYCODE}，有效期5分钟。手机尾号({LASTNUM})正在修改医统账期支付密码，若非您本人操作，请联系医统天下",
			"YTZQSETSUCCESS"		=> "尊敬的客户，您手机尾号({LASTNUM})设置的医统账期独立支付密码已生效，请牢记并妥善保管。医统天下不会以任何形式要求您提供该密码！",
			"YTZQPAYPWDRETSET"		=> "尊敬的客户，您手机尾号({LASTNUM})绑定的医统账期独立支付密码已修改成功，请牢记并妥善保管。医统天下不会以任何形式要求您提供该密码！",
			"YTZQPAYSUCCESS"		=> "尊敬的客户，您在{PAYTIME}使用医统账期为订单{ORDERSN}支付的交易已完成，金额{ORDERTOTAL}元",
			"YTZQREFUNDERROR"		=> "医统账期结算异常：结算金额小于本期应还总额，当前结算金额{TOTAL}元",
			"YTZQRAPPLY"			=> "收到来自 {CLIENTNAME} 医统账期开户申请，请尽快登录运营后台3个工作日内处理",
			"YTZQSUCCESSNOTICE"		=> "尊敬的客户，您手机尾号({LASTNUM})申请的医统账期已通过审核，额度：{ORDERTOTAL}",
			"YTZQUNAPPROVE"			=> "尊敬的客户，您手机尾号({LASTNUM})申请的医统账期未通过审核，具体原因请登陆平台查询",
			"YTZQTUIKUAN"			=> "尊敬的{CLIENTNAME}，您的订单{ORDERSN}已取消，金额{ORDERTOTAL}已退款至医统账期账户",
		);
	
	public function __construct(){}
	
	/**
	 * 获取短信模板
	 * @param string $tplName
	 */
	public  function getSmsTpl($tplName = ''){
		
		return  COMPANY_SIGNED_NAME . $this->_smsTpl[$tplName];
		
// 		if(isset($smsTpl[$tplName])){
// 			return COMPANY_SIGNED_NAME . $smsTpl[$tplName];
// 		}else{
// 			//抛出异常
// 		}
		
	}
	
}