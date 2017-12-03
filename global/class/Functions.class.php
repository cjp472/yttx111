<?php
/**
* class for global functions
*
* PHP version 5
*
* @category  PHP
* @author    WanJun <316174705@qq.com>
* @copyright 2015 Rsung
* @version   1.0
* @date	  2015/03/11
*
* 全局通用Function
*/

class Functions{
	
	/**
	 *@name 密码MD5
	 *@author wanjun
	 *@param 
	 *	$pwd string 待加密字符串
	 *@return string 加密后的字符串 
	 */
	static function pwdMd5($pwd = ''){
		return md5(PASSWORD_MIX_UP . $pwd);
	}//pwdMd5
	
	/**
	 * @name 以数据的ID为索引键名重组数组
	 * @author hugh
	 * @param array $data:待重组的数据
	 * @param string $key_name:ID键名
	 * @return array 重组后的数组
	 * */
	static function rebulidInfo($data = array(), $key_name = ""){
	
		if(empty($data) || empty($key_name)) return array();
	
		$length = count($data);
		$tmp = array();
		for($i = 0; $i < $length; $i++){
			$tmp[$data[$i][$key_name]] = $data[$i];
		}
		return $tmp;
	
	}//rebulidInfo
	
	//写入数据
	static function putMsg($filePath = '', $fileName = '', $message = ''){
		
		if(empty($filePath) || empty($fileName)) return false;
		
		if(!file_exists($filePath)){
			mkdir($filePath, 0777);
			chmod($filePath, 0777);
		}

		$rm = rtrim($filePath, '/')."/".$fileName;
		$handle = fopen($rm, "w+b") or die('Cannot open file: '.$rm);
		
		fwrite($handle, $message);
		fclose($handle);
	}

	/**
	 * 下面为单个表单过滤的一些方法
	 * 
	 * @author 小牛New
	 * @date 2015/08/04
	 */
	static public function cleanJs($sText){
		$sText=trim($sText);
		$sText=stripslashes($sText);
		$sText=preg_replace('/<!--?.*-->/','',$sText);// 完全过滤注释
		$sText=preg_replace('/<\?|\?>/','',$sText);// 完全过滤动态代码
		$sText=preg_replace('/<script?.*\/script>/','',$sText);// 完全过滤js
		$sText=preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i','',$sText);// 过滤多余html
		while(preg_match('/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i',$sText,$arrMat)){//过滤on事件lang js
			$sText=str_replace($arrMat[0],$arrMat[1],$sText);
		}
		while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$sText,$arrMat)){
			$sText=str_replace($arrMat[0],$arrMat[1].$arrMat[3],$sText);
		}
		return $sText;
	}

	static function text($sText,$bDeep=true){
		$sText=self::cleanJs($sText);
		//$sText=preg_replace('/\s(?=\s)/','',$sText);// 彻底过滤空格
		$sText=preg_replace('/[\n\r\t]/',' ',$sText);
		/*$sText=str_replace('  ',' ',$sText);
		$sText=str_replace(' ','',$sText);*/
		if($bDeep===true){
			$sText=str_replace('&nbsp;','',$sText);
			$sText=str_replace('&','',$sText);
			$sText=str_replace('=','',$sText);
			$sText=str_replace('-','',$sText);
			$sText=str_replace('#','',$sText);
			$sText=str_replace('%','',$sText);
			$sText=str_replace('!','',$sText);
			$sText=str_replace('@','',$sText);
			$sText=str_replace('^','',$sText);
			$sText=str_replace('*','',$sText);
			$sText=str_replace('amp;','',$sText);
		}
		$sText=strip_tags($sText);
		$sText=htmlspecialchars($sText);
		$sText=str_replace("'","",$sText);
		return $sText;
	}

	static public function strip($sText){
		$sText=trim($sText);
		$sText=self::cleanJs($sText);
		$sText=strip_tags($sText);
		$sText=htmlspecialchars($sText);
		return $sText;
	}

	static public function html($sText){
		$sText=trim($sText);
		$sText=htmlspecialchars($sText);
		return $sText;
	}

	static public function htmlView($sText){
		$sText=stripslashes($sText);
		$sText=nl2br($sText);
		return $sText;
	}
	
	/**
	 * 根据系统根目录检查一个完整路劲，若目录不存在则创建
	 * @param string $rootPath 系统根目录
	 * @param string $filePath 待保存的完整文件路径
	 * @return void
	 * @author wanjun
	 * @since 2016-02-29
	 * @example
	 *     Functions::chekcAndMakeDir(RESOURCE_PATH, 'd:/resource/56/57/58/cnf.ini');
	 */
	static public function chekcAndMakeDir($rootPath = '', $filePath = ''){
	    if(empty($rootPath) || empty($filePath)) 
	        exit('Params is error at file : ' . __FILE__ . __LINE__);
	    
        //统一使用反斜线'/'
        $rootPath = str_replace('\\', '/', $rootPath);
        $filePath = str_replace('\\', '/', $filePath);
        
        //获取根路径外的目录
        $chkPath = str_replace($rootPath, '', substr($filePath, 0, strrpos($filePath, '/')));
        $chkPath = trim($chkPath, '/');
        
        $paths = explode('/', $chkPath);
        $depath = trim($rootPath, '/') . '/';
        
        for($i = 0; $i < count($paths); $i++){
            $depath .= $paths[$i] . '/';
            mkdir($depath, 0777);
        }
        
        unset($rootPath, $filePath, $paths, $depath, $chkPath);
	}

}//END Functions


?>