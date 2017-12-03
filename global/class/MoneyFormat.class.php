<?php

/**
 * 金额统一采用分计算，避开浮点数精度
 * @author wanjun
 *
 */
class MoneyFormat{
	
	public static $_fee = 100;	//金额元转分进率
	
	public function __construct(){}
	
	/**
	 * 单位元转为分
	 * @param number $money
	 * @return 整数，分
	 */
	public static function MoneyOfYuanToFen($money = 0){
		
		return $money * self::$_fee;
	}
	
	/**
	 * 单位分转为元
	 * @param number $money
	 * @return 浮点数，元
	 */
	public static function MoneyOfFenToYuan($money = 0, $isFormat = false){
	
		return $isFormat ? (number_format(($money / self::$_fee), 2)) : ($money / self::$_fee);
	}
}