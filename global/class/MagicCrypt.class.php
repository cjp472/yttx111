<?php
/**
 * 加密、解密应用程序
 */

class MagicCrypt{
	private $iv = "yitong111.com^%*";	//密钥偏移量IV，可自定义
	private $encryptKey = YM_SMS_AESPWD;
	
	//加密
	public function encrypt($encryptStr) {
		$localIV = $this->iv;
		$encryptKey = $this->encryptKey;
	
		if (true == EN_GZIP)   $encryptStr = gzencode($encryptStr);
	
		//Open module
		$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, $localIV);
	
		mcrypt_generic_init($module, $encryptKey, $localIV);
	
		//Padding
		$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$pad = $block - (strlen($encryptStr) % $block); //Compute how many characters need to pad
		$encryptStr .= str_repeat(chr($pad), $pad); // After pad, the str length must be equal to block or its integer multiples
	
		//encrypt
		$encrypted = mcrypt_generic($module, $encryptStr);
	
		//Close
		mcrypt_generic_deinit($module);
		mcrypt_module_close($module);
	
		return $encrypted;
	}
	
	//解密
	public function decrypt($encryptStr) {
		$localIV = $this->iv;
		$encryptKey = $this->encryptKey;
	
		//Open module
		$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, $localIV);
	
		mcrypt_generic_init($module, $encryptKey, $localIV);
	
		$encryptedData = mdecrypt_generic($module, $encryptStr);
	
		if (true == EN_GZIP)   $encryptedData = gzdecode($encryptedData);
	
		return $encryptedData;
	}
	
}