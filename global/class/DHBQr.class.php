<?php
    
    
/**
 * DHB 二维码生成类
 *
 * PHP version 5
 *
 * @category  PHP
 * @author    WanJun <316174705@qq.com>
 * @copyright 2015 Rsung
 * @package global/plugin/phpqrcode/qrencode.php L286
 * @version   1.0
 * @date      2015/12/28
 *
 */

!defined('SYSTEM_ACCESS') && exit('Access deny!');    //权限验证
//include_once GLOBAL_PLUGIN_PATH . '/phpqrcode/qrlib.php';
include_once GLOBAL_PLUGIN_PATH . '/phpqrcode/phpqrcode.php';

class DHBQr{
	
	private $qrText = 'My Name Is DHB',      //二维码存储数据
			//$outfile = false,                //是否输出 
			$level = 'L',                    //容错率，params:L、7%；M、15%；Q,25%；H、30%
			$size = 7,                       //图片尺寸，数字越大图片越大
			$margin = 1;                     //边距补白

			     
			
			         
	public function __construct(){

	}
	           
	
	public function createQR($contentText = ''){	 
		$path = RESOURCE_PATH.$_SESSION['uinfo']['ucompany'].'/qrcode';
		$this -> mkFolder($path);  
	    QRcode::png($contentText, $path.'/'.$contentText.'.png', $level, 3,2);
	    $QR = RESOURCE_PATH.$_SESSION['uinfo']['ucompany'].'/qrcode.png';//已经生成的原始二维码图 
	    $QR = imagecreatefromstring(file_get_contents($QR)); 
	    return $QR;
		//Code here...这里需要完善，以适应以后的扩展，比如加logo
	}
	
	public function addLogo(){
	    
	}
	
	public function mkFolder($path){  
	    if(!is_readable($path))  
	    {  
	        is_file($path) or mkdir($path,0777);  
	    }  
	} 
	
	
	
	
	
	       
}