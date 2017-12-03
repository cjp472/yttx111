<?php 
header("Content-type: text/html; charset=utf-8");

//引入客户开发地区分布图
$ipmapdata = require('./ipmap.php');

//获取计算机的外网ip
function get_onlineip() {
    $onlineip = '';
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }
//     return '61.54.106.187'; //新乡
//      return '218.12.41.186'; //石家庄

//     return '61.52.31.38'; //郑州
    return $onlineip;
}

//获取当前外网的IP地区信息
$ip = get_onlineip();
if(empty($_COOKIE['ip_area']) || $_COOKIE['ip']!=$ip){
    //获取当前地区开户信息
    $str1 = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$ip);
    $localIpData = json_decode($str1,true);
    $ip_area = substr($localIpData['data']['city'], 0, -3);
    
    setcookie('ip',$ip);
    setcookie('ip_area',$ip_area);
}else{
    $ip_area = $_COOKIE['ip_area'];
}

$ipmaparr = array();
foreach ($ipmapdata as $key=> $val){
    $ipmaparr = array_merge($ipmaparr, $val['sonMap']);
}
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		


<meta content="width=device-width,initial-scale=1,user-scalable=no" name="viewport" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<meta http-equiv="expires" content="-1">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
        <title>医统天下手机网站</title>
		<link href="./style.css" rel="stylesheet" type="text/css">
		<style>@media screen and ( min-width: 319px){
		.footer-p{font-size:12px !important}
		}
              </style>
	</head>
	<body style="overflow:-Scroll;overflow-y:hidden;overflow-x:hidden;width:100%;">
		<header>
			<div class="logo"><img src="./logoa.png"></div>                                                                                                                                                                            
			<div class="hengxian"></div>
            <div class="tel"></div>
		</header>
	
		<div class="viewport">
			<div class="list-box">
				<div class="fast_city">
					<b>当前站点：</b>
					
					<?php  if(!empty($ipmaparr[$ip_area.'库'])){?>
					<span class="current_site">进入&nbsp;<strong><a class="bj" href="http://<?php echo $ipmaparr[$ip_area.'库']?>.yitong111.com/shop/"><?php echo $ip_area;?></a></strong>&nbsp;库</span>
                    <?php  }else{?>
                    <span class="current_site"><?php echo $ip_area;?>，此地区尚未开通</span>
                    <?php  }?>
					<p style="line-height:15px;">&nbsp;</p>
					
					 <?php  foreach ($ipmapdata as $key=>$val){?>
                        <p class="list_site"><?php echo $key?></p>
                        <span class="current_site">
                         <?php  foreach ($val['sonMap'] as $sk=>$sv){?>
                            <a href="http://<?php echo $sv?>.yitong111.com/shop/"><?php echo $sk;?></a>&nbsp;
                            <?php }?>
                        </span>
                    <?php }?>
                    
                </div>
			<br /><br /><br />
			</div>


		<footer style="position:fixed;bottom:0px;width:100%">

		<div style="background-color:#1AA967">
			<center>
			    <p>
					<span class="footer-p1">互联网药品交易服务资格证书国A20150005号</span><br />
					<span class="footer-p">Copyright©2014-<?=date('Y')?>医统天下(北京)网络科技有限公司</span>
				</p>
			</center>
			<div class="im" style="width:100%;height:33px">
			<ul class="donghuijun" style=";width:328px;height:33px;text-align:center;margin:0 auto;">
				<li style="margin-left:2px;"><a><img src="./1.png" /></a></li>
				<li><a><img src="./2.png" /></a></li>
				<li><a><img src="./3.png" /></a></li>
				<li><a><img src="./4.png" /></a></li>
				<li><a><img src="./5.png" /></a></li>
				<div style="clear:both;"></div>
			</ul>
			</div>
		</div>
		</footer>
		
		</div>

</body></html>