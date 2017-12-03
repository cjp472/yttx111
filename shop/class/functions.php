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

	function is_safe($str)
	{
		return  (preg_match("/^([\x81-\xfea-z0-9])+$/i", $str));
	}

	function is_email($str){
		return preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $str);
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

		if(!(file_exists (RESOURCE_NAME.$companyidmsg."/".$basepath)))
		{
			_mkdir(RESOURCE_NAME.$companyidmsg."/".$basepath);
		}
		return $basepath;
	}
	
	/**************/
	function checkpost(){
		$action = $_SERVER['PHP_SELF'];
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			$ref = parse_url($_SERVER['HTTP_REFERER']);
			$srv = "http://{$_SERVER['SERVER_NAME']}";
			if (strcmp($srv,$ref['scheme']."://".$ref['host']) == 0){
				echo "ok";
				exit;
			}else{
				echo "error";
				exit;
			}
		}
	}

	function passport_encrypt($txt, $key) {
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i = 0;$i < strlen($txt); $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
		}
		return base64_encode(passport_key($tmp, $key));
	}

	function passport_decrypt($txt, $key) {
		$txt = passport_key(base64_decode($txt), $key);
		$tmp = '';
		for($i = 0;$i < strlen($txt); $i++) {
			$md5 = $txt[$i];
			$tmp .= $txt[++$i] ^ $md5;
		}
		return $tmp;
	}

	function passport_key($txt, $encrypt_key) {
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
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
 * 获取供应商配置信息
 * @param string $ty 配置代码
 * @return array
 */
function get_set_arr($ty='product')
{
    $db  = dbconnect::dataconnect()->getdb();
    $valuearr = null;
    $setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['ucc']['CompanyID']." and SetName='".$ty."' limit 0,1");
    if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);

    return $valuearr;
}

/**
 * 获取经销商资料
 *
 * 注意：该方法与client/class/client.php一致[现系统中存在两个相同的class名称:client]
 * @author wanjun
 * @return array 资料信息
 */
function clientinfo(){
	$db	     = dbconnect::dataconnect()->getdb();
	
	$sql_2   = "select ClientFlag from ".DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." limit 0,1";
	$result2 = $db->get_row($sql_2);

	return $result2;
}

/**
 * 检查能否在当前时间下提交订单
 * @return array
 */
function check_ordertime(){
    $productset = get_set_arr('product');

    //设置当前订货时间
    $currWeek = array(
        1 => "周一",
        2 => "周二",
        3 => "周三",
        4 => "周四",
        5 => "周五",
        6 => "周六",
        7 => "周日",
    );
    	
    $currWeekMsg = "每".$currWeek[$productset['ordertime']['date_start']]." ".$productset['ordertime']['time_start'];
    $currWeekMsg .= " 到 每".$currWeek[$productset['ordertime']['date_end']]." ".$productset['ordertime']['time_end'];


    $nextWeekMsg = "每".$currWeek[$productset['ordertime']['date_start']]." ".$productset['ordertime']['time_start'];
    $nextWeekMsg .= " 到 次".$currWeek[$productset['ordertime']['date_end']]." ".$productset['ordertime']['time_end'];

    $rmsg = $productset['ordertime']['date_start'] > $productset['ordertime']['date_end'] ?  $nextWeekMsg : $currWeekMsg;

    $rmsg = "请在以下时间段提交订单：".$rmsg."。若急需订购，请联系供应商！";

    if(!empty($productset) && isset($productset)){
        $ordertime = $productset['ordertime'];
        if(empty($ordertime) || $ordertime['time_show'] != 'on')
            return array('status' => true, 'rmsg' => $rmsg);
        else
        {
            $dateS = $ordertime['date_start'];
            $dateE = $ordertime['date_end'];
            $timeS = $ordertime['time_start'];
            $timeE = $ordertime['time_end'];

            $weekarray = array("7","1","2","3","4","5","6");
            $nowweekday = $weekarray[date("w")];
            $nowtime = date('H:i',time());

            if($dateE >= $dateS){
                if($dateE == $dateS && $nowtime >=$timeS && $nowtime <=$timeE){
                    $return = true;}
                else if($nowweekday == $dateS && $dateE > $dateS && $nowtime >=$timeS )
                    $return = true;
                else if($nowweekday == $dateE && $dateE > $dateS && $nowtime <=$timeE)
                    $return = true;
                else if($nowweekday > $dateS && $nowweekday < $dateE)
                    $return = true;
                else
                    $return = false;

                //本组返回
                return array('status' => $return, 'rmsg' => $rmsg);
            }else //跨周
            {
                if($nowweekday == $dateS && $nowtime >=$timeS )
                    $return = true;
                else if($nowweekday == $dateE && $nowtime <=$timeE)
                    $return = true;
                else if($nowweekday<=7 && $nowweekday > $dateS)
                    $return = true;
                else if($nowweekday < $dateE)
                    $return = true;
                else
                    $return = false;

                //本组返回
                return array('status' => $return, 'rmsg' => $rmsg);
            }
        }
    }else
        return array('status' => true, 'rmsg' => $rmsg);
}



/**
 * 获取满省的金额
 * @param int $total 当前订单金额
 * @return int 省的金额
 */
/*function get_stair($total) {
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
            break;
        } else if($total >= $val['amount']){
            $amount = $val['count'];
            break;
        }
    }
    return $amount;
}*/

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

?>