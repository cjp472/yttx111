<?php
/**
 * 模拟post进行url请求
 * @param string $url
 * @param string $param
 */
function request_post($url = '', $param = '') {
    if (empty($url) || empty($param)) {
        return false;
    }

    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}


/**
 * @author seekfor
 * @desc 获取数据
 * @param $url:获取地址
 * @return 数组格式数据
 * @version 2013-12-24
 */
function curl_get_data($url)
{
	$ch = curl_init();
	$timeout = 10;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	$file_contents = curl_exec($ch);
	curl_close($ch);

	$rdata = json_decode($file_contents);
	$rdata = (array)$rdata;

	return $rdata;
}

/**
 * @author seekfor
 * @desc 提交数据
 * @param $url:提交地址,$data:要提交的数据（json格式）
 * @return 数组格式数据
 * @version 2013-12-24
 */
function curl_post_data($url,$data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$tmpInfo = curl_exec($ch);

	if (curl_errno($ch)){
		$tmpInfo = 'Errno'.curl_error($ch);
	}
	curl_close($ch);

	$rdata = json_decode($tmpInfo);
	$rdata = (array)$rdata;

	return $rdata;
}



/**
 * 产生随机字符串
 *$result=random(10);//生成10位随机数
 *$result=random(10, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');//生成10位字母数字混合字符串
 *echo "<input type='text' size='20' value='{$result}'>";
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789'){
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}
/**
 * Class functions
 * 常用函数
 * 2014
 * @author 
 */
/**
 * @param $char 需要加密的字符串
 * @return string 加密之后的字符串
 */
function encodeParam($char){
    $char = "http://www.dhb.hk/action.php?a=".$char;
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"http://dwz.cn/create.php");
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $data=array('url'=>$char);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    $strRes=curl_exec($ch);
    curl_close($ch);
    $arrResponse=json_decode($strRes,true);
    if($arrResponse['status']==0)
    {
        /**错误处理*/
        echo iconv('UTF-8','GBK',$arrResponse['err_msg'])."\n";
    }
    $key = strrpos($arrResponse['tinyurl'],"/");
    $result = substr($arrResponse['tinyurl'],$key+1);
    return $result;
}

/**
 * @param $char 需要解密的字符串 6位
 * @return mixed 解密之后的字符串
 */
function decodeParam($char)
{
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"dwz.cn/query.php");
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $data=array('tinyurl'=>$char);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    $strRes=curl_exec($ch);
    curl_close($ch);
    $arrResponse=json_decode($strRes,true);
    $arr = explode('?a=',$arrResponse['longurl']);
    return $arr[1];
}

function is_filename($str)
{
      return preg_match("/^[A-Za-z0-9_.@\-]+$/", $str);
}

//验证前缀
function is_prefix($str)
{
    if(strlen($str) < 3 || strlen($str) > 10 ) return false;
    return preg_match("/^[A-Za-z0-9]+$/", $str);
}

function is_safe($str)
{
	return  (preg_match("/^([\x81-\xfea-z0-9])+$/i", $str));
}

function is_email($str){
	return preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $str);
}

function is_mobile($str){
    return preg_match("/^((\(\d{3}\))|(\d{3}\-))?13\d{9}$/", $str);
}

function is_phone($var)
{
		$var = trim($var);
		if(preg_match ("/^[-]?[0-9]+([\.][0-9]+)?$/", $var))
		{
			if(strlen($var) == 11) return true; else return false;
		}else{
			return false;
		}
}

/*
 * 匹配固定电话，纯数字
 * */
function is_telephone($var)
{
    $var = trim($var);
    if(preg_match("/^[0-9]*$/",$var))
    {
        return true;
    }else{
        return false;
    }
}

function checkDateForm($date, $formats = array("Y-m-d")) {
    $unixTime = strtotime($date);
    if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
        return false;
    }
    
    //校验日期的有效性，只要满足其中一个格式就OK
     foreach ($formats as $format) {
         if (date($format, $unixTime) == $date) {
            return true;
         }
     }
     
     return false;
}

function is_chinese($str){
    return preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str);
}

function is_english($str){
    return preg_match("/^[A-Za-z]+$/", $str);
}

function is_zip($str){
	return preg_match("/^[1-9]\d{5}$/", $str);
}

function is_url($str){
	return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $str);
}

function RealIp()
{
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif(isset($_SERVER["HTTP_CLIENT_IP"]))
	{
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}else{
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	return $ip;
}

/*
* @todo 中文截取，支持gb2312,gbk,utf-8,big5
*
* @param string $str 要截取的字串
* @param int $start 截取起始位置
* @param int $length 截取长度
* @param int $param  是否去除HTML代码
* @param string $charset utf-8|gb2312|gbk|big5 编码
* @param $suffix 是否加尾缀
*/
function cutmsg($str, $length, $suffix="", $param=0, $start=0, $charset="utf-8")
{
	if($param==1)
	{
		$str = preg_replace("#<.+?>#is", "", $str);
	}

	if(function_exists("mb_substr"))
	return mb_substr($str, $start, $length, $charset);

	$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));

	return $slice.$suffix;
}

function countStrLength($string)
{
   $string = preg_replace ( '/[\x80-\xff]{3}/', 'x', $string );
   return strlen ($string);
}


//删除HTML
function filterHtml($str){
	$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
	$alltext = "";
	$start = 1;
	for($i=0;$i<strlen($str);$i++)
	{
		if($start==0 && $str[$i]==">")
		{
			$start = 1;
		}
		else if($start==1)
		{
			if($str[$i]=="<")
			{
				$start = 0;
				$alltext .= " ";
			}
			else if(ord($str[$i])>31)
			{
				$alltext .= $str[$i];
			}
		}
	}
	$alltext = str_replace("　"," ",$alltext);
	$alltext = str_replace("&nbsp;", '', $alltext);
	$alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
	$alltext = preg_replace("/[ ]+/s"," ",$alltext);
	return $alltext;
}

/**
	 * @todo GetExt 取得文件的扩展名
	 * 
	 * @param string $filename 文件名
	 * @return string $ext 扩展名
	 */
function GetExt($filename)
{
	$ext = "";
	if($filename == "") return $ext;
	$ext_a = explode(".", $filename);
	$ext = array_pop($ext_a);
	$ext = strtolower($ext);
	return $ext;
}

function ShowImg($filename)
{
	$ext = "";
	if(empty($filename)) $ext = "/template/img/default.jpg"; else $ext = "/".RESOURCE_PATH.$filename;
	return $ext;
}

function GoodsType($ftype)
{
	if(empty($ftype)) return "";
	$ext = "";
	 switch($ftype)
	 {
		case 0:
		{
			$ext = "";
			break;
		}
		case 1:
		{
			$ext = "<span class=regbg>[荐]</span>";
			break;
		}
		case 2:
		{
			$ext = "<span class=greenbg>[特]</span>";
			break;
		}
		case 3:
		{
			$ext = "<span class=yellowbg>[新]</span>";
			break;
		}
		case 4:
		{
			$ext = "<span class=bluebg>[热]</span>";
			break;
		}
		case 9:
		{
			$ext = "<span class=darkbg>[缺]</span>";
			break;
		}
		default: 
			$ext = "";
			break;
	}


	return $ext;
}

/**
     * @todo 取得当前页的完整地址
     * 
     * @param  
     * @return string $url 地址
     */     
function get_url()
{
	if (isset($_SERVER['REQUEST_URI']))
	{
		$url = $_SERVER['REQUEST_URI'];
	}else{
		$url = $_SERVER['SCRIPT_NAME'];
		$url .= (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';
	}
	return $url;
}


/**
     * @todo 取得时间戳
     * 
     * @param  
     * @return float 时间
     */  
function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function currentTimeMillis()
{
	list($usec, $sec) = explode(" ",microtime()); 
	return substr($usec, 2, 3); 
}

function _mkdir ($r_path, $dirname, $mode=0777)
{
	$b_path = str_replace('//', '/', $r_path . '' . $dirname);
	if (@mkdir ($b_path, $mode))
	{
		@chmod ($b_path, $mode);
      	return true;
	}
	return false;
}

function setuppath($companyidmsg = "")
{
	$basepath = date("Ym");

	if(!(file_exists (RESOURCE_PATH."".$companyidmsg."/".$basepath)))
	{
		//_mkdir(RESOURCE_PATH."".$companyidmsg."/".$basepath);
		_mkdir(RESOURCE_PATH,$companyidmsg."/".$basepath);
	}
	return $basepath;
}

function get_set_arr($ty='product')
{
	$db  = dbconnect::dataconnect()->getdb();
	$valuearr = null;
	$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='".$ty."' limit 0,1");
	if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);
	
	return $valuearr;
}

 function toCNcap($data)
 {
   $capnum=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
   $capdigit=array("","拾","佰","仟");
   $subdata=explode(".",$data);
   $yuan=$subdata[0];
   $j=0; $nonzero=0;
   for($i=0;$i<strlen($subdata[0]);$i++){
      if(0==$i){ //确定个位 
         if($subdata[1]){ 
            $cncap=(substr($subdata[0],-1,1)!=0)?"圆":"圆零";
         }else{
            $cncap="圆";
         }
      }   
      if(4==$i){ $j=0;  $nonzero=0; $cncap="万".$cncap; } //确定万位
      if(8==$i){ $j=0;  $nonzero=0; $cncap="亿".$cncap; } //确定亿位
      $numb=substr($yuan,-1,1); //截取尾数
      $cncap=($numb)?$capnum[$numb].$capdigit[$j].$cncap:(($nonzero)?"零".$cncap:$cncap);
      $nonzero=($numb)?1:$nonzero;
      $yuan=substr($yuan,0,strlen($yuan)-1); //截去尾数	  
      $j++;
   }

   if($subdata[1]){
     $chiao=(substr($subdata[1],0,1))?$capnum[substr($subdata[1],0,1)]."角":"零";
     $cent=(substr($subdata[1],1,1))?$capnum[substr($subdata[1],1,1)]."分":"零分";
   }
   $cncap .= $chiao.$cent."整";
   $cncap=preg_replace("/(零)+/","\\1",$cncap); //合并连续“零”
   return $cncap;
 }

/**
 * 字符串加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? ENCODE_KEY : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace('=', '',base64_encode($str));
}

/**
 * 字符串解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? ENCODE_KEY : $key);
    $x      = 0;
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }

    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

function send_email($to_address, $to_name ,$subject, $body, $attach = "")
{
    //使用phpmailer发送邮件
    require_once(SITE_ROOT_PATH."/class/phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP(); // set mailer to use SMTP
    //$mail->SMTPDebug = true;
    $mail->CharSet = 'utf-8';
    $mail->Encoding = 'base64';
    $mail->From = 'dhb@rsung.com';
    $mail->FromName = '订货宝试用帐号';

    $mail->Host = 'smtp.qq.com';
    $mail->Port = 25; //default is 25, gmail is 465 or 587
    $mail->SMTPAuth = true;
    $mail->Username = "1730407198@qq.com";
    $mail->Password = "rsungdhb123456";

    $mail->AddAddress($to_address, $to_name);

    $mail->WordWrap = 50;
    if (!empty($attach)) $mail->AddAttachment($attach);
    $mail->IsHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    //$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

    if(!$mail->Send())
    {
        $backmsg = "发送失败: " . $mail->ErrorInfo . "";
        return $backmsg;
    }
    else
    {
        return "ok";
    }

}

/**
 * 兼容 array_column
 * @since 2015-02-26
 */
if(!function_exists('array_column')){
    function array_column(array $array, $column_key, $index_key = null){
        $result = array();
        foreach($array as $arr){
            if(!is_array($arr)) continue;

            if(is_null($column_key)){
                $value = $arr;
            }else{
                $value = $arr[$column_key];
            }

            if(!is_null($index_key)){
                $key = $arr[$index_key];
                $result[$key] = $value;
            }else{
                $result[] = $value;
            }

        }
        return $result;
    }
}

/**
 * ERP接口是否開通且正在運行
 * @param $db
 * @param $company_id
 * @return bool
 */
function erp_is_run($db,$company_id) {
    $serial = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_api_serial WHERE CompanyID=" . $company_id);
    $is_erp = false;
    if(!empty($serial) && $serial['Status'] == 'T' && $serial['RunStatus'] == 'T') {
        $is_erp = true;
    }
    return $is_erp;
}

/**
 * 获取满省的金额
 * @param int $total 当前订单金额
 * @param string $type default 'stair'
 * @return int 省的金额
 */
function get_stair($total,$type = 'stair') {
    if(!function_exists('stair_sort')) {
        function stair_sort($a,$b) {
            if($a['count'] == $b['count']) {
                return 0;
            }
            return $a['count'] > $b['count'] ? -1 : 1;
        }
    }

    $amount = 0;
    $setting = get_set_arr('product');
    if($setting['stair_status'] == 'N' || empty($setting['stair_status'])) {
        return $amount;
    }
    $stair = $setting['stair'];
    usort($stair,'stair_sort');

    foreach($stair as $key => $val) {
        if($key == 0 && $total >= $val['amount']) {
            $amount = $val['count'];
            if($type != 'stair') {
                $amount = $val['amount'];
            }
            break;
        } else if($total >= $val['amount']){
            $amount = $val['count'];
            if($type != 'stair') {
                $amount = $val['amount'];
            }
            break;
        }
    }
    return $amount;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function hcget_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 加密/解密字符串
 * @param string $string 待加密/待解密字符串
 * @param boolean $operation TRUE：解密，False：加密
 * @param string $key 加密/解密码
 * @param int $expiry 过期时间（单位为秒）
 * @return string
 */
function cookieAuthCode($string,$operation=TRUE,$key=null,$expiry=0){
    $ckey_length=4;

    $key=md5($key?$key:'fasr254tgsety64567vzsdft45zdf');
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

function checkLoginStatus()
{
    $codedStr = $_COOKIE['userinfo'];
    $rest = false;
    
    if(!empty($codedStr))
    {
        $codeStr = cookieAuthCode($codedStr,true);
        if(!empty($codedStr))
        {
            $userArr = explode("\t", $codeStr);
            $ip = RealIp();
            $db  = dbconnect::dataconnect()->getdb();
            
            $userLog = $db->get_row("SELECT MAX(LoginDate) FROM ".DATABASEU.DATATABLE."_order_login_user_log WHERE LoginName='".$userArr[0]."' limit 0,1");
            
            //6分钟内有登录日志则判定为另地登录。6分钟界限是因为每5分钟Ajax会刷新一次，故在此基础上加1分钟以确保刷新期间内捕捉到掉线
            if((time()-intval($userLog['LoginDate'])) <= 360)
            {
                //另外登录挤下线，删除本地Cookie信息
                $db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginIP,LoginDate,LoginUrl,LoginContent) values('m','".$userArr[0]."','". $ip."',".time().",'Compute','另外登录致Session丢失')");
                setcookie('userinfo','',time()-3600);
                $rest = true;
            }
            else 
            {
                $psmsg = EnCodePWD($userArr[0],$userArr[1]);
                
                $uinfo = $db->get_row("select UserID,UserName,UserPass,UserCompany,UserTrueName,UserFlag,UserSessionID,UserType from ".DATABASEU.DATATABLE."_order_user where UserName = '".$userArr[0]."' and UserFlag!='1' limit 0,1");
                if(!empty($uinfo) && ($uinfo['UserPass'] == $psmsg) && ($uinfo['UserID'] == $userArr[2]) && ($uinfo['UserCompany'] == $userArr[3]))
                {
                    $db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginIP,LoginDate,LoginUrl,LoginContent) values('m','".$userArr[0]."','". $ip."',".time().",'Compute','Session丢失信息回写开始:'.$codedStr)");
                    header("Location:do_login.php?m=login&userinfo=".urlencode($codedStr));
                }
                else 
                {
                    //Cookie被篡改，删除本地Cookie信息
                    $db->query("insert into ".DATABASEU.DATATABLE."_order_login_log(LoginType,LoginName,LoginIP,LoginDate,LoginUrl,LoginContent) values('m','".$userArr[0]."','". $ip."',".time().",'Compute','Cookie被篡改，Session回写终止')");
                    setcookie('userinfo','',time()-3600);
                    $rest = true;
                }
            }
        }
        else 
        {
            //Cookie超过解密期限，删除Cookie信息
            setcookie('userinfo','',time()-3600);
            $rest = true;
        }
    }
    else
        $rest = true;
    
    return $rest;
}

/********** 加密Password ************/
function EnCodePWD($msgu,$msgp)
{
    if(!empty($msgu) && !empty($msgp))
    {
        $delmsg = md5($msgu);
        $rname  = substr($delmsg,5,1).",".substr($delmsg,7,1).",".substr($delmsg,15,1).",".substr($delmsg,17,1);
        $rnamearray = explode(',',$rname);
        $rpass = md5($msgp);
        $r_msg = str_replace($rnamearray, "", $rpass);
    }else{
        $r_msg = $msgp;
    }
    return $r_msg;
}

/**
 * DHB跟踪记录
 * @param $companyid 日志跟踪ID
 * @param $content 记录内容
 */
function ESaveCompanyLog($companyid,$content)
{
    $db  = dbconnect::dataconnect()->getdb();

    $upsql = "insert into ".DATABASEU.DATATABLE."_order_company_log(CompanyID,CreateDate,CreateUser,Content) values('".intval($companyid)."',".time().",'".$_SESSION['uinfo']['username']."','".$content."')";
    return $db->query($upsql);
}

  /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
function wx_json_encode($arr) {
        $parts = array ();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode ( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                $str .= 'false'; //The booleans
                elseif ($value === true)
                $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }
?>