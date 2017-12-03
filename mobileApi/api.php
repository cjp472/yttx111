<?php
include_once ("common.php");
include_once ("global.config.php");
set_time_limit(120);
$db		= dbconnect::dataconnect()->getdb();
$db->cache_dir  = CONF_PATH_CACHE;
$log   = KLogger::instance(LOG_PATH, KLogger::INFO);

$input		=	new Input;
$in			=	$input->parse_incoming();
//$in		=   $input->_htmlentities($in);

$param		=   json_decode($in['v'],true);

if(empty($param['step']) || (int)$param['step']>100){
    $param['step'] = 100;
}
if(empty($param['begin'])){
    $param['begin'] = 0;
}

if(!empty($in['f']))
{
	$func   = trim($in['f']);

	if(substr($func,0,7) == 'manager'){
		include_once ("managerController.php");
		$module = new managerController();
	}else{
		include_once ("controller.php");
		$module = new controller();
	}

	if (method_exists($module, $func))
	{
		$rdata = $module->{$func}($param);
		$rdatamsg = json_encode($rdata);
		$rdatamsg = str_replace("\n","",$rdatamsg);
		$rdatamsg = str_replace("\t","",$rdatamsg);
		$rdatamsg = str_replace('"rData":null','"rData":[]',$rdatamsg);
		$rdatamsg = str_replace('null','""',$rdatamsg);
		echo $rdatamsg = str_replace("\r","",$rdatamsg);
	}else {
		$rdata['rStatus'] = '101';
		$rdata['error'] = '请求方法不存在';
		$rdata['rData'] = '';
		echo json_encode($rdata);
	}  
}


function JSON($array) {
    arrayRecursive($array, 'urlencode', true);
    $json = json_encode($array);
    return urldecode($json);
}

function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }

        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
}
?>