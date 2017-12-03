<?php 


/**
 * 网站路径设置
 */
define('OFFICE_SITE','http://s1.w');
// define('M_SITE','http://online_m.rs.com');
// define('C_SITE','http://online.rs.com');

define('M_SITE','http://ty_m.rs.com');
define('C_SITE','http://ty.rs.com');

/**
 * 安全设置
 */
define('MAX_WAIT_EXPERIENCE',3);
define('EXPERIENCE_EXPIRY_TIME',86400);
define('EXPERIENCE_SAFE_CODE','cd-028-rsung-key-yes');

/**
 * 行业设置
 */
$EXPERIENCE_INDUSTRY = array(
	1, // 个护、化妆
	5, // 汽车用品配件
	2, // 婴童用品
	6, // 食品饮料酒水
	13, // 日用百货
	9, // 餐饮连锁
	7, // 数码电器
	8, // 服装服饰
	4, // 鞋靴箱包配饰
	10, // 家居家纺
	14, // 医药保健
	12 // 其他行业
);

$EXPERIENCE_INDUSTRY_NAME = array(
	1 => '个护、化妆', // 个护、化妆
	5 => '汽车用品配件', // 汽车用品配件
	2 => '婴童用品', // 婴童用品
	6 => '食品饮料酒水', // 食品饮料酒水
	13 => '日用百货', // 日用百货
	9 => '餐饮连锁', // 餐饮连锁
	7 => '数码电器', // 数码电器
	8 => '服装服饰', // 服装服饰
	4 => '鞋靴箱包配饰', // 鞋靴箱包配饰
	10 => '家居家纺', // 家居家纺
	14 => '医药保健', // 医药保健
	12 => '其他行业' // 其他行业
);

function authcode($string,$operation=TRUE,$key=null,$expiry=0){
	$ckey_length=4;

	$key=md5($key?$key:'rsung');
	$keya=md5(substr($key,0,16));
	$keyb=md5(substr($key,16,16));
	$keyc=$ckey_length?($operation===TRUE?substr($string,0,$ckey_length):substr(md5(microtime()),-$ckey_length)):'';

	$cryptkey=$keya.md5($keya.$keyc);
	$key_length=strlen($cryptkey);
	$string=$operation===TRUE?base64_decode(substr($string, $ckey_length)):sprintf('%010d',$expiry?$expiry+time():0).substr(md5($string.$keyb),0,16).$string;
	$string_length=strlen($string);

	$result='';
	$box=range(0,255);
	$rndkey=array();
	for($i=0;$i<=255;$i++){
		$rndkey[$i]=ord($cryptkey[$i%$key_length]);
	}

	for($j=$i=0;$i<256;$i++){
		$j=($j+$box[$i]+$rndkey[$i])%256;
		$tmp=$box[$i];
		$box[$i]=$box[$j];
		$box[$j]=$tmp;
	}

	for($a=$j=$i=0;$i<$string_length;$i++){
		$a=($a+1)%256;
		$j=($j+$box[$a])%256;
		$tmp=$box[$a];
		$box[$a]=$box[$j];
		$box[$j]=$tmp;
		$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
	}

	if($operation===TRUE){
		if((substr($result,0,10)==0 || substr($result,0,10)-time()>0) && substr($result,10,16)==substr(md5(substr($result,26).$keyb),0,16)){
			return substr($result,26);
		}else{
			return '';
		}
	}else{
		return $keyc.str_replace('=','',base64_encode($result));
	}
}


function getSafeIndustry($sIndustry){
	$sIndustry = authcode($sIndustry,true,EXPERIENCE_SAFE_CODE,EXPERIENCE_EXPIRY_TIME);
	$sIndustry = intval($sIndustry);
	return $sIndustry;
}

function encodeData($data){
	return $data ? authcode($data,false,EXPERIENCE_SAFE_CODE,EXPERIENCE_EXPIRY_TIME) : '';
}

function makeAdmin($sIndustry,$sCompany=''){
	return M_SITE.'/experience.php?industry='.urlencode($sIndustry).($sCompany ? '&cp='.$sCompany : '');
}

function makeFront($sIndustry,$sCompany=''){
	return C_SITE.'/experience.php?industry='.urlencode($sIndustry).($sCompany ? '&cp='.$sCompany : '');
}

function makeUrl($nIndustry){
	return 'industry.php?industry='.urlencode(encodeData($nIndustry));
}

function makeAuthUrl($system_type, $industry){
	if($system_type == 'manager'){
		return '/experience/authentic.php?url='.urlencode(makeAdmin($industry)).'&industry='.urlencode($industry);
	}else{
		return '/experience/authentic.php?url='.urlencode(makeFront($industry)).'&industry='.urlencode($industry);
	}
}

?>