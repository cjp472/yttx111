<?php
/**
 * Class functions
 * 常用函数
 * 
 * @author 
 */
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
	return preg_match("/^([\x81-\xfea-z0-9])+$/i", $str);
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

//文本格式化
function _striptext($document)
{

	$document = strip_tags($document,'<p><br><img><table><tr><td>');	

	$search = array("'<script[^>]*?>.*?</script>'si",	// strip out javascript
			"'style=\"\"'si",
			"'([\r\n])[\s]+'",					// strip out white space
			"'&(quot|#34|#034|#x22);'i",		// replace html entities
			"'&(amp|#38|#038|#x26);'i",			// added hexadecimal values
			"'&(lt|#60|#060|#x3c);'i",
			"'&(gt|#62|#062|#x3e);'i",
			"'&(nbsp|#160|#xa0);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(copy|#169);'i",
			"'&(reg|#174);'i",
			"'&(deg|#176);'i",
			"'&(#39|#039|#x27);'",
			"'&(euro|#8364);'i",				// europe
			"'&a(uml|UML);'",					// german
			"'&o(uml|UML);'",
			"'&u(uml|UML);'",
			"'&A(uml|UML);'",
			"'&O(uml|UML);'",
			"'&U(uml|UML);'",
			"'&szlig;'i",
	);
	$replace = array("",
			"",
			"\\1",
			"\"",
			"&",
			"<",
			">",
			" ",
			chr(161),
			chr(162),
			chr(163),
			chr(169),
			chr(174),
			chr(176),
			chr(39),
			chr(128),
			"?",
			"?",
			"?",
			"?",
			"?",
			"?",
			"?",
	);
	$document = preg_replace("/style=\"(.*)\"/isU","",$document);

	return $document;
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


//获取执行价格
function setprice3($p3,$clientId,$clientLevl)
{
	$rp3 = '';
	$lkey = '';
	if(!empty($p3))
	{
		$pricearr = unserialize(urldecode($p3));
		//单个指定
		if(!empty($pricearr['clientprice'][$clientId]))
		{
			$rp3 = $pricearr['clientprice'][$clientId];
		}else{
			if(empty($pricearr['typeid'])) $pricearr['typeid'] = 'A';
			if(!empty($clientLevl))
			{
				$clientlevelarr = explode(",", $clientLevl);
				if(substr($clientlevelarr[0],0,1)==="l")
				{
					if($pricearr['typeid']=="A") $lkey = $clientlevelarr[0];
				}else{
					foreach($clientlevelarr as $cvar)
					{
						if($pricearr['typeid']==substr($cvar,0,1))
						{
							$lkey = substr($cvar,2);
							break;
						}
					}
				}
			}
			if(!empty($pricearr[$lkey])) $rp3 = $pricearr[$lkey];
		}
	}
	return $rp3;
}


//获取快递状态
function getExpressDelivery($code,$invoice){
	/* 原代码 tubo 2016-02-29修改
	$result = array('status'=>0,'info'=>'未知错误');
	$url = "http://m.kuaidi100.com/query?type={$code}&postid={$invoice}&id=1&valicode=&temp=".rand(1,710);
	//$body = file_get_contents($url); //FIXME
	$curl = curl_init();
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt ($curl, CURLOPT_HEADER,0);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	$body = curl_exec($curl);
	curl_close ($curl);

	$body = json_decode($body,true);
	$result['status'] = $body['status'] == 200 ? 1 : 0;
	$result['info'] = $body['message'];
	isset($body['data']) && ($result['state']=$body['state']) && ($result['data'] = $body['data']) ;
	return $result;
	*/
	$result = array('status'=>0,'info'=>'未知错误');
	$AppKey = KUDAIDIAPPKEY;
	$url  = 'http://www.kuaidi100.com/applyurl?key='.$AppKey.'&com='.$code.'&nu='.$invoice.'';
	$curl = curl_init();
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt ($curl, CURLOPT_HEADER,0);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	$get_content = curl_exec($curl);
	curl_close ($curl);
	
	$url = "http://www.kuaidi100.com/query?type={$code}&postid={$invoice}&id=1&valicode=&temp=".rand(1,710);
	$curl = curl_init();
	$header = array (
		"Referer:".$get_content
	);
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt ($curl, CURLOPT_HEADER,0);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_HTTPHEADER,$header);
	curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	$body = curl_exec($curl);
	curl_close ($curl);
	$body = json_decode($body,true);
	$result['status'] = $body['status'] == 200 ? 1 : 0;
	$result['info'] = $body['message'];
	if(isset($body['data'])){
		$result['state']=$body['state'];
		$result['data'] = $body['data'];
	}
	return $result;
}

//禁止重复提交
function denyRepeatSubmit($inData,$aciotn='submitOrder'){
	
	$cache_file = SITE_ROOT_PATH.'/'.LOG_PATH.$aciotn.'_submitkey.txt';
	$key = md5(json_encode($inData));

	if (file_exists($cache_file) ){
		if ( (time() - filemtime($cache_file)) < (1*60) )
		{
			$result_key = file_get_contents($cache_file);			
			if($result_key == $key){
				return true;
			}
		}
		@unlink($cache_file);
	}	
	error_log ( $key, 3, $cache_file);
	return false;
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
 * @author seekfor
 * @desc 写文件
 * @param $wcontent:写入内容,$Mode:写放方式
 * @return
 * @version 2013-12-24
 */
function write_token($wcontent)
{
	$cache_file	= WEIXIN_TOKEN_FILE;

	@unlink($cache_file);
	error_log ( $wcontent, 3, $cache_file);
	return true;
}

/**
 * @author seekfor
 * @desc 获取access_token
 * @param $url:获取地址
 * @return string access_token
 * @version 2013-12-24
 */
function get_token()
{
	$cache_file	= WEIXIN_TOKEN_FILE;
	$get_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET.'';
	$cache_lifetime = 7000;
	if (file_exists($cache_file)){
		if((time() - filemtime($cache_file)) < $cache_lifetime){
			$result_key = file_get_contents($cache_file);
			return $result_key;
		}
	}
	$rdata = curl_get_data($get_url);
	write_token($rdata['access_token']);
	return $rdata['access_token'];
}

/**
 * @author seekfor
 * @desc 生成KID cart
 * @param $product_id:商品ID , $product_color：商品颜色, $product_spec：商品规格
 * @return string access_token
 * @version 2013-12-24
 */
function make_kid($product_id, $product_color='', $product_spec=''){
	$kid = $product_id;
	$fp = array('+','/','=','_');
	$rp = array('-','|','DHB',' ');

	if(!empty($product_color))
	{
		$kid .= "_p_".str_replace($fp,$rp,base64_encode($product_color));
	}
	if(!empty($product_spec))
	{
		$kid .= "_s_".str_replace($fp,$rp,base64_encode($product_spec));
	}

	return $kid;
}

//读文件
function read_cart_cache($cidarr,$Mode = "r")
{
	$cartcontent = '';
	$filename	= md5($cidarr['ClientID']).".txt";
	$path		= CART_PATH.$cidarr['CompanyID']."/";
	$cartfile = $path.$filename;
	
	if(@file_exists($cartfile)){
		$cartcontent = file_get_contents($cartfile);
	}else{
		$cartcontent = '';
	}

	return $cartcontent;
}

//写文件
function write_cart_cache($cidarr,$wcontent,$Mode = "w")
{
	$isw = false;
	$filename	= md5($cidarr['ClientID']).".txt";
	$path		= CART_PATH.$cidarr['CompanyID']."/";

	if(!file_exists($path)) mkdir($path, 0777);
	$cartfile = $path.$filename;

	if ($Fp = fopen($cartfile, $Mode))
	{
		$isw = fwrite($Fp, $wcontent);
		@fclose($Fp);
	}
	return $isw;
}


/**********************************************************************
 *  store_cache
 */

function store_cache($key,$content){
	// disk caching of queries
	if ($key){
		$cache_file = buid_dir($key,true);//子目录缓存
		if (file_exists($cache_file) ){
			unlink($cache_file);
		}
		error_log ( json_encode($content), 3, $cache_file);
	}

}

/**********************************************************************
 *  get_cache
 */

function get_cache($key){

	$cache_file = buid_dir($key,false);

	if (file_exists($cache_file) ){
		// Only use this cache file if less than 'cache_timeout' (hours)
		if ( (time() - filemtime($cache_file)) > (CACHE_LIFETIME * 3600) ){
			unlink($cache_file);
		}else{
			$result_cache = json_decode(file_get_contents($cache_file), true);
			return $result_cache;
		}
	}

}

function buid_dir ($key, $isbuild, $mode=0777){
	$md5msg  = $key;
	$fheader = substr($md5msg,0,1);
	$cache_dir = SITE_ROOT_PATH.'/'.CONF_PATH_CACHE;
		
	$b_path  = $cache_dir.'/'.$fheader;
	$b_path  = str_replace('//', '/', $b_path);
	$cache_file = $b_path.'/'.$md5msg;
	if(!$isbuild) return $cache_file;

	if(@file_exists($b_path)){
		return $cache_file;
	}else{
		if (@mkdir ($b_path, $mode))
		{
			@chmod ($b_path, $mode);
			return $cache_file;
		}else{
			return $cache_dir.'/'.$md5msg;
		}
	}

}


function change_msg($msgu,$msgp)
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
 * 验证该手机号是否已使用 (buy_account已有或company已有)
 * @param $db
 * @param $mobile
 * @return bool 是否可用
 */
function valid_mobile($db,$mobile) {
	$buy_acc_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_buy_account WHERE mobile='{$mobile}' LIMIT 1");
	if($buy_acc_cnt) {
		return false;
	}
	$company_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_order_company WHERE CompanyMobile='{$mobile}' LIMIT 1");
	if($company_cnt) {
		return false;
	}
	return true;
}

/**
 * 验证登录账户是否可用
 * @param $db
 * @param $user_name
 * @return bool
 */
function valid_name($db,$user_name) {
	$buy_acc_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_buy_account WHERE user_name='{$user_name}' AND status='T' LIMIT 1");
	if($buy_acc_cnt) {
		return false;
	}
	$user_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_order_user WHERE UserName='{$user_name}' LIMIT 1");
	if($user_cnt) {
		return false;
	}
	return true;
}

/**
 * 验证独立二级域名
 * @param $db
 * @param $prefix
 * @return bool
 */
function valid_prefix($db,$prefix) {
	//保留独立二级域名
    $prefixarr = array('soap','admin','manager','client','mobile','shouji','weixin','www','app','ipad','shop','store','think','rsung','jxc','erp','crm','com');
    if(in_array($prefix,$prefixarr)) return false;
    
	$buy_acc_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_buy_account WHERE prefix='{$prefix}' AND status='T' LIMIT 1");
	if($buy_acc_cnt) {
		return false;
	}
	$com_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_order_company WHERE CompanyPrefix='{$prefix}' LIMIT 1");
	if($com_cnt) {
		return false;
	}
	return true;
}

/**
 * 获取满省的金额
 * @param ez_sql $db
 * @param int $total 当前订单金额
 * @param int $company_id
 * @param string $type default stair
 * @return int 省的金额
 */
function get_stair($db,$total,$company_id,$type = 'stair') {
    if(!function_exists('stair_sort')) {
        function stair_sort($a,$b) {
            if($a['count'] == $b['count']) {
                return 0;
            }
            return $a['count'] > $b['count'] ? -1 : 1;
        }
    }

    $amount = 0;

    $setting = array();
    $setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$company_id." and SetName='product' limit 0,1");
    if(!empty($setinfo['SetValue'])) {
        $setting = unserialize($setinfo['SetValue']);
    }

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

//tubo 增加判断erp是否开通方法 2015-11-5
/**
 * ERP接口是否開通且正在運行
 * @param $db
 * @param $company_id
 * @return bool
 */
function erp_is_run($db,$company_id) {
    $serial = $db->get_row("SELECT * FROM ".DB_DATABASEU.DATATABLE."_api_serial WHERE CompanyID=" . $company_id);
    $is_erp = false;
    if(!empty($serial) && $serial['Status'] == 'T' && $serial['RunStatus'] == 'T') {
        $is_erp = true;
    }
    return $is_erp;
}

?>