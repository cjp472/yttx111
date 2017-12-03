<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/orderdata.php");
include_once (SITE_ROOT_PATH."/class/sms.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();

if($in['m']=="confirm"){
	
	$status = orderdata::confirmincept($in['cid']);

	if($status)
	{
		exit('ok');
	}else{
		exit('操作不成功！');
	}

}elseif($in['m']=="showcontent"){
	
	$urlmsg = "";
	if(!empty($in['id']))
	{
		$in['id'] = intval($in['id']);
		$in['sn'] = '';
		$urlmsg  .= "&id=".$in['id'];
	}elseif(!empty($in['sn'])){
		$in['id'] = 0;
	}
	$content = orderdata::showconsignment($in['id'],$in['sn']);
	
	$sendtypearr = orderdata::statusarr("sendtype");
	
	include template("consignment_content");

}elseif($in['m']=="address"){

	$addresslist = orderdata::listaddress();	
	
	include template("consignment_address");

}elseif($in['m']=="deladdress"){

	$status = orderdata::deladdress($in['kid']);
	if($status)
	{
		exit("ok");
	}else{
		exit('操作失败，请与供应商联系！');
	}

}elseif($in['m']=="setaddress"){

	$status = orderdata::setaddress($in['kid']);
	if($status)
	{
		exit("ok");
	}else{
		exit('操作失败，请与供应商联系！');
	}

}elseif($in['m']=="saveaddress"){

	$status = orderdata::saveaddress($in);
		
	echo $status;
	exit();

}elseif($in['m']=="showkuaidi"){
	
	$arr_html_kuaidi = array (
		  '0' => 'ems',
		  '1' => 'shentong',
		  '2' => 'shunfeng',
		  '3' => 'youzhengguonei',
		  '4' => 'youzhengguoji',
		  '5' => 'yunda',
		  '6' => 'yuantong',
		  '7' => 'zhongtong'
	);

	if(!empty($in['Com']) && !empty($in['Nu']))
	{
		$typeCom  = $in['Com'];//快递公司
		$typeNu    = $in['Nu'];  //快递单号
		$AppKey    = KUDAIDIAPPKEY;

		if (in_array($in['Com'], $arr_html_kuaidi))
		{
			$url  = 'http://www.kuaidi100.com/applyurl?key='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'';
		}else{
			$url = 'http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';
		}
		//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
		$powered = '<div style="display:none;">查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 </div>';

		//优先使用curl模式发送数据
		if (function_exists('curl_init') == 1){
		  $curl = curl_init();
		  curl_setopt ($curl, CURLOPT_URL, $url);
		  curl_setopt ($curl, CURLOPT_HEADER,0);
		  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		  curl_setopt ($curl, CURLOPT_TIMEOUT,5);
		  $get_content = curl_exec($curl);
		  curl_close ($curl);
		}else{
		  include("./class/snoopy.php");
		  $snoopy = new Snoopy();
		  $snoopy->referer = 'http://www.google.com/';//伪装来源
		  $snoopy->fetch($url);
		  $get_content = $snoopy->results;
		}
		if (in_array($in['Com'], $arr_html_kuaidi))
		{
			echo '<iframe name="kudiaurl"  frameborder="0" scrolling="no" width="98%" height="350" scrolling="auto" src="'.$get_content.'" ></iframe> ';
		}else{
			print_r($get_content . '<br/>' . $powered);
		}
	}else{
		echo '<p ><font color=red>无法获取物流状态! 请确认物流公司编号和运单号正确! </font></p><p ><a href="kuaidi_search.php" target="_blank"  title="快递查询"><img src="template/img/c1.jpg" alt="快递查询" /></a>&nbsp; &nbsp; &nbsp; &nbsp; <a href="wuliu_search.php" target="_blank"  title="物流查询"><img src="template/img/c2.jpg" alt="物流查询" /></a></p>';
	}
	exit;

}else{

	$conlist     = orderdata::listconsignment($in['status'],12,'consignment.php');
	$sendtypearr = orderdata::statusarr("sendtype");

	include template("consignment");
	
//END
}
?> 
