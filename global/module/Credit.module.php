<?php 

/**
 * 医统账期处理程序
 * 
 * @author wnajun
 * @version 1.0 @ 2015/06/25
 */

!defined('SYSTEM_ACCESS') && exit('Access deny!');

class Credit extends Common{

	/**
	 * 查询应还款账期清单
	 * @param number $companyID
	 * @param number $clientID
	 */
	public function getCreditUnRefundList($companyID = 0, $clientID = 0){
		
		$companyID	= intval($companyID);
		$clientID	= intval($clientID);
		
		$sql = "select ID, CompanyID, ClientID,OrderID,OrderTotal,RecordDate from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$companyID." and ClientID=".$clientID." and CreditStatus='unrefund'";
		return $this->db->get_results($sql);
		
	}
	
	/**
	 * 计算当日应还款账期时间
	 * @param number $companyID
	 * @param number $clientID
	 * ++5.31 ~  +6.1+++++6.2+++++6.3--------------7.1++++++7.2+++++++++7.3
	 * ++还款日 +++支付+++++支付+++++支付                           提醒+++++6.1+6.2+++++6.1+6.2+6.3
	 */
	public function getCreditUnRefundTime($companyID = 0, $clientID = 0){
	
		$companyID	= intval($companyID);
		$clientID	= intval($clientID);
	
		//首先查询截止到今日的以前是否有需要还款的账期，返回记账日期
		$today	= date('Y-m-d 23:59:59');
		$date	=date_create($today);
		date_add($date, date_interval_create_from_date_string("-1 month"));
		$monthAgo = date_format($date, "Y-m-d 23:59:59");
		$agoSql = "select RecordDate,now() as Today from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$companyID." and ClientID=".$clientID." and CreditStatus='unrefund' and RecordDate<='".$monthAgo."' ";
		$agoResult = $this->db->get_row($agoSql);
		
		//如果有账期则需还款
		$needRefund = true;
		
		//如果今天以前没有应还账款，则计算今天以前截止上次还款以后是否有交易产生，返回自还款后新的记账日期
		if(empty($agoResult)){
			$agoSql = "select RecordDate,now() as Today from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$companyID." and ClientID=".$clientID." and CreditStatus='normal' and RecordDate<='".$today."' ";
			$monthDate = $this->db->get_row($agoSql);
			$needRefund = false;
		}

		//截止上次还款后的第一次使用账期时间
// 		$sql_2 = "select RecordDate as RefundDate from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$companyID." and ClientID=".$clientID." and (CreditStatus='normal' or CreditStatus='unrefund') order by ID asc";
// 		$lastRefundDate = $this->db->get_row($sql_2);

		return array (
				'needRefund' => $needRefund,
				'RefundDate' => $needRefund ? $agoResult ['RecordDate'] : $monthDate ['RecordDate'],
				'Today' => $needRefund ? $agoResult ['Today'] : $monthDate ['Today'] 
		);
	}
	
	/**
	 * 更新指定医统账单为已还款
	 * @param number $companyID
	 * @param number $clientID
	 * @param array $refunId
	 */
	public function updateUnRefund($companyID = 0, $clientID = 0, $refunId = array()){
		
		if(empty($companyID) || empty($clientID) || empty($refunId)) return false;
		
		$companyID	= intval($companyID);
		$clientID	= intval($clientID);
		
		$sql = "update ".DATABASEU.DATATABLE."_credit_detail set CreditStatus='refunded' where CompanyID=".$companyID." and ClientID=".$clientID." and CreditStatus='unrefund' and ID in(".implode(',', $refunId).")";
		return $this->db->get_results($sql);
	}
	
	/**
	 * 更新指定医统账单为已还款
	 * @param number $companyID
	 * @param number $clientID
	 * @param array $refunId
	 */
	public function updateReturnStatus($companyID = 0, $clientID = 0, $orderID = 0){
	
		$sql = "update ".DATABASEU.DATATABLE."_credit_detail set CreditStatus='return' where CompanyID=".$companyID." and ClientID=".$clientID." and `Type`='out' and OrderID=".$orderID;
		return $this->db->get_results($sql);
	}
	
	/**
	 * 记录账期还款详情
	 * @param number $companyID
	 * @param number $clientID
	 * @param number $money
	 */
	public function reFundLog($companyID = 0, $clientID = 0, $money = 0, $orderid = 0, $isReturn = false){
		
		if(empty($companyID) || empty($clientID) || empty($money)) return false;
		
		$message = $isReturn ? "医统账期退款，金额：".$money."元" : "医统账期还款，金额：".$money."元";

		$companyID	= intval($companyID);
		$clientID	= intval($clientID);
		$orderid	= intval($orderid);
		$moneyToFen	= MoneyFormat::MoneyOfYuanToFen($money);
		
		$sql = "insert into ".DATABASEU.DATATABLE."_credit_detail (CompanyID,ClientID,PayDate,RecordDate,OrderID,OrderTotal,Type,DescribeContent) values(".$companyID.",".$clientID.", now(), now(),".$orderid.",".$moneyToFen.",'in','".$message."')";
		$this->db->query($sql);
		
		//修改当前账期状态为已退款
		$this->updateReturnStatus($companyID, $clientID, $orderid);
	}
	
	/**
	 * 获取第一次使用账期时间
	 * @param number $companyID
	 * @param number $clientID
	 */
	public function getFirstCredit($companyID = 0, $clientID = 0){
		if(empty($companyID) || empty($clientID)) return false;
	
		$sql = "select ID,PayDate,RecordDate from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$companyID." and ClientID=".$clientID." order by ID asc";
		return $this->db->get_row($sql);
	}
	
	/**
	 * 获取本次还款的订单开始时间与结束时间
	 * @param number $companyID
	 * @param number $clientID
	 * @return array 首次账单日期，本次账单开始时间、结束时间
	 */
	public function getCreditDate($companyID = 0, $clientID = 0){
		if(empty($companyID) || empty($clientID)) return false;
		
		//首次账单日期
		$zhangqi = $this->getFirstCredit($companyID, $clientID);
		$billDate['first']		= $zhangqi['RecordDate'];
		$billDate['firstofzh']	= date('Y年m月d日', strtotime($zhangqi['RecordDate']));
		
		//本期账期开始与结束时间
		$currentDate = $this->getCreditUnRefundTime($companyID, $clientID);
		
		//是否提前还款
		$billDate['needRefund'] = $currentDate['needRefund'];
		
		//本期账单开始日期
		$billDate['start']		= date('Y-m-d', strtotime($currentDate['RefundDate']));
		$billDate['startofzh']	= date('Y年m月d日', strtotime($currentDate['RefundDate']));
		
		//账单结束日期
		$billDate['end']		= date('Y-m-d', strtotime($currentDate['Today']));
		$billDate['endofzh']	= date('Y年m月d日', strtotime($currentDate['Today']));
		
		//是否账期订单
		$billDate['needRefund'] = $currentDate['needRefund'];
		
		return $billDate;
	}
	
	/**
	 * 获取本期账单金额
	 * 这里获取了截止当天为止应该还款的总额度
	 * @param number $companyID
	 * @param number $clientID
	 * @return array 金额元，金额分
	 */
	public function getCreditMoney($companyID = 0, $clientID = 0){
		if(empty($companyID) || empty($clientID)) return false;
		
		$billInfo = $this->getCreditDate($companyID, $clientID);
		
		//定位开始时间
		$start = $billInfo['start'] . ' 00:00:00';
		
		//定位结束时间
		$date = date_create($billInfo['end']);
		$end  = date_format($date, "Y-m-d 23:59:59");
		
		//大于等于当天的开始时间，而小于第二天的开始时间[本金+利息+滞纳金/本金]
		//账期应还还是提前还款
		$billType = $billInfo['needRefund'] ? 'unrefund' : 'normal';
		$sql = "select sum(if(`Type`='out', OrderTotal, -OrderTotal)) as total, sum(Interest) as Interest, sum(OverdueFine) as OverdueFine from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$companyID." and ClientID=".$clientID." and (CreditStatus='".$billType."') and RecordDate >= '".$start."' and RecordDate < '".$end."'";
		
		$result = $this->db->get_row($sql);
		return array (
				'yuan' => MoneyFormat::MoneyOfFenToYuan ( $result ['total'] + $result ['Interest'] + $result ['OverdueFine'] ),
				'yuanFormat' => MoneyFormat::MoneyOfFenToYuan ( ($result ['total'] + $result ['Interest'] + $result ['OverdueFine']), true ),
				'fen' => ($result ['total'] + $result ['Interest'] + $result ['OverdueFine']),
				'InterestOfFen' => $result ['Interest'],
				'InterestFormat' => MoneyFormat::MoneyOfFenToYuan ( $result ['Interest'], true ),
				'OverdueFineOfFen' => $result ['OverdueFine'],
				'OverdueFineformat' => MoneyFormat::MoneyOfFenToYuan ( $result ['OverdueFine'], true ),
				'isAdvanced' => $billInfo['needRefund'] //是否提前还款 
		);
	}
	public function BottomCommon(){
            $client_id = $_SESSION['cc']['cid'];
            $company_id = $_SESSION['cc']['ccompany'];
            $cs_flag = $this->db->get_var("SELECT C_Flag FROM ".DATATABLE."_order_client WHERE ClientCompany={$company_id} and ClientID={$client_id} LIMIT 1");
            return $cs_flag;
        }
	
	
}//EOC ClientInfo

?>