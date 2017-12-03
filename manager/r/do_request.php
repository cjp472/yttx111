<?php
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
if(empty($in['m']) || $in['request_method']!="post")
{
 	echo "error!";
 	exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=="content_add_request_save")
{
	if(empty($in['data_Mobile']) || !is_phone($in['data_Mobile']))  $in['data_Mobile'] = '';

	$upsql = "insert into ".DATABASEU.DATATABLE."_order_request(CompanyID,EndDate,Password,CompanyName,Contact,QQ,Mobile,Phone,Email,Remark,RequestDate,AddUser) values(".$in['data_CompanyID'].",'".$in['data_EndDate']."','".$in['data_Password']."','".$in['data_CompanyName']."','".$in['data_Contact']."','".$in['data_QQ']."','".$in['data_Mobile']."','".$in['data_Phone']."','".$in['data_Email']."','".$in['data_Remark']."',".time().",'".$_SESSION['uinfo']['username']."')";
	$update  = $db->query($upsql);	
	if($update)
	{
		$db->query("update ".DATABASEU.DATATABLE."_order_company set CompanyLogo='',CompanyLogin='' where CompanyID=".$in['data_CompanyID']);
		
		$insql = "update ".DATABASEU.DATATABLE."_order_cs set CS_EndDate='".$in['data_EndDate']."', CS_UpDate='".date("Y-m-d")."', CS_UpdateTime=".time().",CS_SmsNumber=10 where CS_Company=".$in['data_CompanyID'];
		$db->query($insql);

		//帐号
		$infou = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".intval($in['data_CompanyID'])." and UserFlag='9' order by UserID asc limit 0,1");

		$db->query("delete from ".DATABASEU.DATATABLE."_order_weixin where UserID = ".$infou['UserID']."");
		$db->query("delete from ".DATABASEU.DATATABLE."_order_qq where UserID = ".$infou['UserID']."");

		$in['data_Password']    = strtolower($in['data_Password']);
		$passmsg = ChangeMsg($infou['UserName'],$in['data_Password']);
		$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserPass='".$passmsg."', UserTrueName='订货宝' where UserID=".$infou['UserID']." and UserCompany=".$in['data_CompanyID']."";
		$db->query($upsql);

		//设置
		$proarr['producttype']      = "imglist";
		$proarr['product_number']   = "on";
		$proarr['product_negative'] = "off";
		$proarr['product_number_show'] = "on";
		$proarr['return_type'] = "product";
		$proarr['deduct_type'] = "on";
		$proarr['audit_type']  = "off";
		$valuemsg = serialize($proarr);

		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$in['data_CompanyID']." and SetName='product' limit 0,1");
		if(empty($valuemsg)) $valuemsg = '';
		if(!empty($setinfo['SetID']))
		{
			$isq = $db->query("update ".DATABASEU.DATATABLE."_order_companyset set SetValue = '".$valuemsg."' where SetCompany = ".$in['data_CompanyID']." and SetID='".$setinfo['SetID']."'");
		}else{
			$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$in['data_CompanyID'].",'product','".$valuemsg."')");
		}
		$isd = $db->query("delete from ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$in['data_CompanyID']." and SetName='template' ");

		//短信
		$smsarr =  Array(
			'0' => '1',
			'1' => '2',
			'2'	=> '3',
			'3' => '4',
			'4' => '5',
			'5' => '6'
		);
		 $smsarr['Mobile']['MainPhone']      = $in['data_Mobile'];
		 $smsarr['Mobile']['FinancePhone']   = $in['data_Mobile'];
		 $smsarr['Mobile']['LibaryPhone']    = $in['data_Mobile'];
		$valuemsg = serialize($smsarr);
		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$in['data_CompanyID']." and SetName='sms' limit 0,1");
		if(empty($valuemsg)) $valuemsg = '';
		if(!empty($setinfo['SetID']))
		{
			$isq = $db->query("update ".DATABASEU.DATATABLE."_order_companyset set SetValue = '".$valuemsg."' where SetCompany = ".$in['data_CompanyID']." and SetID='".$setinfo['SetID']."'");
		}else{
			$isq = $db->query("insert into ".DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$in['data_CompanyID'].",'sms','".$valuemsg."')");
		}

		//经销商
		$upsqlmsg = " ,ClientMobile='".$in['data_Mobile']."' ";
		$dinfo= $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany=".intval($in['data_CompanyID'])." and ClientFlag=0 order by ClientID asc limit 0,1");
		$db->query("delete from ".DATABASEU.DATATABLE."_order_weixin where UserID = ".$dinfo['clientID']."");
		$db->query("delete from ".DATABASEU.DATATABLE."_order_qq where UserID = ".$dinfo['clientID']."");
		if(!empty($in['data_Mobile']))
		{			
			$upsql = "update ".DATABASEU.DATATABLE."_order_dealers set ClientMobile=''  where ClientMobile='".$in['data_Mobile']."' ";
			$db->query($upsql);			
		}
		$db->query("update ".DATATABLE."_order_client set ClientMobile='".$in['data_Mobile']."'  where ClientID=".$dinfo['ClientID']." and ClientCompany=".$in['data_CompanyID']."");
		$upsql = "update ".DATABASEU.DATATABLE."_order_dealers set ClientPassword='".$in['data_Password']."' ".$upsqlmsg."  where ClientID=".$dinfo['ClientID']." and ClientCompany=".$in['data_CompanyID']."";
		$db->query($upsql);			

		exit("ok");
	}else{
		exit("保存不成功!");
	}
}

if($in['m']=="sendto_email")
{
	if(empty($in['ID'])) exit('参数错误！');

	$rinfo = $db->get_row("SELECT r.*,c.CompanySigned,c.CompanyPrefix FROM ".DATABASEU.DATATABLE."_order_request r left join ".DATABASEU.DATATABLE."_order_company c on r.CompanyID=c.CompanyID where r.ID=".$in['ID']."  limit 0,1");
	if(empty($rinfo)) exit('您发送的数据不存在!');

	$uinfo = $db->get_row("select * from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$rinfo['CompanyID']." and UserFlag='9' order by UserID asc limit 0,1");

	$cinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$rinfo['CompanyID']." and ClientFlag=0 order by ClientID asc limit 0,1");
	if(!empty($cinfo['ClientMobile']))  $us = $cinfo['ClientMobile']; else $us = $cinfo['ClientName'];

		$content = '<style type="text/css" id="myStyle">
a,dt, dl, span, ul, li, div, h1, h2, h3{font-size:12px; font-family:"微软雅黑",Arial, Helvetica, sans-serif; color:#5b5b5b;font-size:12px; margin:0px; text-decoration:none;}
a{ cursor:pointer;}
a:link,a:visited{
 text-decoration:none;
}
a:hover{
 text-decoration:none; color:#000;
}
/*------顶部样式------*/
.header{ width:650px; height:80px; background-color:#fff; margin:0 auto; margin-top:25px;}
.logo{ float:left; margin-top:20px; margin-left:20px;}
.menu{ width:230px; float:right; height:14px; margin-top:40px;}
.menu dt{ float:left; color:#0098aa; margin-left:20px; line-height:14px;}
.menu dt a{text-decoration:none; color:#0098aa;}
.banner{ width:650px; height:137px;margin:0 auto;}

/*------试用账号样式------*/
.dear{ width:610px; margin:0 auto; background-color:#FFF; height:40px; padding:20px; line-height:20px;}
.dear span{ color:#0098aa; font-size:14px; font-weight:bold}

.user_info{ width:650px; height:165px; margin:0 auto; background:#fff;}
.user_info_th1{border-top:#ececec 1px solid; border-bottom:#6fcad5 2px solid; color:#0098aa}
.user_info_th2{ color:#0098aa; text-align:left; padding-left:42px;}
.user_info_td1{ border-bottom:#ececec 1px solid;}
.user_info table tr{ height:40px;}
.user_info table td{ text-align:center;}
.user_info_td1 a{text-decoration:none;}
/*------微信客户端样式------*/
.wechat{ width:650px; background-color:#fff; margin:0 auto; margin-top:0px; height:162px;}
.wechat h1{ line-height:40px; font-size:14px; color:#0098aa; padding-left:40px;}
.wechat_info{ width:600px; height:120px; border:1px solid #ececec; margin:0 auto}
.wechat_info_left{ float:left; overflow:hidden; width:180px; height:120px;}
.wechat_info_left dt{ width:157px; border-bottom:#ececec 1px solid; height:40px; display:block; border-right:#ececec 1px solid; line-height:40px; padding-left:20px;}
.ewm_text{ width:157px; height:40px; line-height:40px; padding-left:20px;}
.wechat_info_right{ width:112px; height:112px; float:right; padding:4px;}
.wechat_info_l{ width:112px; height:112px; float:left; padding:4px;}

/*------底部样式------*/
.footer{ width:650px; background-color:#fff; margin:0 auto;}
.company{ border-bottom:1px solid #ececec; line-height:24px; font-size:12px; color:#afafaf; padding-left:20px; width:630px; padding-top:20px;padding-bottom:20px;}
.company span{ font-size:14px; font-weight:bold; color:#22bbce; margin-left:5px;}
.company b{ font-size:14px;}
.footer_info_left{ width:400px; float:left; overflow:hidden; padding:20px;}
.footer_info_right{ width:81px; height:89px; float:right; overflow:hidden; padding:20px; margin-top:10px; }
</style>
<div style="background-color:#f5f5f5; width:100%; height:auto;">
<!--顶部开始-->
<div class="header">
	<div class="logo"><a href="http://www.dhb.hk" target="_blank"><img src="http://www.dhb.hk/email/images/logo.jpg" width="215" height="38" /></a></div>
    <div class="menu">
    	<dt><a href="http://www.dhb.hk/site/TypicalFeatures/" target="_blank" style="text-decoration:none;">产品介绍</a></dt>
        <dt><a href="http://www.dhb.hk/site/PriceSolutions/" target="_blank" style="text-decoration:none;">价格方案</a></dt>
        <dt><a href="http://help.dhb.net.cn/manager.php" target="_blank" style="text-decoration:none;">操作指南</a></dt>
    </div>
</div>
<!--顶部结束-->
<!--banner开始-->
<div class="banner" >
	<img src="http://www.dhb.hk/email/images/banner.jpg" width="650" height="137" />
</div>
<!--banner结束-->
<!--试用账号开始-->
<div class="dear">
   <b>亲爱的用户：</b><span>'.$rinfo['Contact'].'</span><br/> 
您好，《订货宝》网上订货系统试用帐号是： 
</div>
<div class="user_info">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="24%" class="user_info_th1 user_info_th2" scope="col">端口</th>
    <th width="23%" class="user_info_th1" scope="col">登陆地址</th>
    <th width="28%" class="user_info_th1" scope="col">账号</th>
    <th width="25%" class="user_info_th1" scope="col">密码</th>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">客户订货端</th>
    <td class="user_info_td1"><a href="http://tc.dhb.net.cn?c='.$rinfo['CompanyPrefix'].'&u='.$us.'&p='.$cinfo['ClientPassword'].'" target="_blank" style="text-decoration:none;" >http://tc.dhb.net.cn/</a></td>
    <td class="user_info_td1">'.$us.' </td>
    <td class="user_info_td1">'.$cinfo['ClientPassword'].' </td>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">手机订货端</th>
    <td class="user_info_td1"><a href="http://sj.dhb.hk" target="_blank" style="text-decoration:none;">http://sj.dhb.hk</a></td>
    <td class="user_info_td1">'.$us.' </td>
    <td class="user_info_td1">'.$cinfo['ClientPassword'].' </td>
  </tr>
  <tr>
    <th scope="row" class="user_info_td1 user_info_th2">管理端</th>
    <td class="user_info_td1"><a href="http://tm.dhb.net.cn?u='.$uinfo['UserName'].'&p='.$rinfo['Password'].'" target="_blank" style="text-decoration:none;">http://tm.dhb.net.cn</a></td>
    <td class="user_info_td1">'.$uinfo['UserName'].' </td>
    <td class="user_info_td1">'.$rinfo['Password'].' </td>
  </tr>
</table>
</div>
<!--试用账号结束-->
<!--微信客户端开始-->
<div class="wechat">
<h1>订货宝手机移动端</h1>
<div class="wechat_info">

	<div class="wechat_info_left">
    	<dt>手机APP下载 </dt>
		<dt>Android, IOS </dt>
      <div class="ewm_text">扫描右侧二维码下载</div>
    </div>
	<div class="wechat_info_l">
    <img src="http://www.dhb.hk/email/images/app.jpg" width="111" height="111" /> </div>

	<div class="wechat_info_left">
    	<dt>微信公众号：订货宝手机客户端 </dt>
        <dt>微信号：vdhb_hk</dt>
      <div class="ewm_text">扫描右侧二维码关注</div>
    </div>
	<div class="wechat_info_right">
    <img src="http://www.dhb.hk/email/images/ewm2.jpg" width="111" height="111" /> </div>
</div>
</div>
<!--微信客户端结束-->
<!--底部开始-->
<div class="footer">
	<div class="company" ><b>订货宝客服中心会在24小时内电话回访您，为您提供专业细致的服务支持</b><br  />
若有需要，请随时致电我们的客服中心 <span>400-6311-682</span><br />
在线QQ客服：<span>2261915847</span> , <span>1730407198</span>
</div>
    <div class="footer_info">
   	  <div class="footer_info_left">
        	服务热线：400-6311-682   028-84191728 
             <br/>官网：<a href="http://www.dhb.hk" target="_blank" style="text-decoration:none;">www.dhb.hk</a>
            <br/> <br/>软件企业编号：川R-2010-0006 | 软件著作权登记号：2011SR027284 
		    <br/>阿商信息技术有限公司 版权所有 © 2005-2015 
      </div>
        <div class="footer_info_right">
   	    <img src="http://www.dhb.hk/email/images/ewm2_03.jpg" width="81" height="89" /> </div>
        <div style="clear:both"></div>
    </div>
</div>
<!--底部结束-->
</div>
<script>
window.onload = function(){
	var styleText = document.getElementById("myStyle").innerHTML;
	styleText = styleText.replace(/.qmbox/g, "");
	document.getElementById("myStyle").innerHTML = styleText;
};
</script>
		';

		if(empty($rinfo['Email'])) $tomail = $rinfo['QQ']."@qq.com"; else $tomail = $rinfo['Email'];
		$rmsg = send_mail($tomail,$rinfo['CompanyName'],"订货宝 网上订货系统 试用帐号 ",$content);
		
		//短信通知
		$tomobile = '';
		if(!empty($cinfo['ClientMobile']) && is_phone($cinfo['ClientMobile'])){
			$message = "【订货宝】感谢您申请体验,您的体验帐号信息如下,订货端网址:tc.dhb.net.cn,帐号:".$us." 密码：".$cinfo['ClientPassword']." 管理端网址:tm.dhb.net.cn 帐号:".$uinfo['UserName']." 密码:".$rinfo['Password']." 微信订货端帐号：订货宝手机客户端。 订货宝服务中心会在24小时内电话回访您,为您提供专业细致的服务支持。若有需要请致电客服中心4006311682.";
			$mobilearr[]    = $cinfo['ClientMobile'];
			$statusCode2    = $client->login();
			$statusCode     = $client->sendSMS($mobilearr,$message);
			$tomobile = "、手机：".$cinfo['ClientMobile'];
		}
		if($rmsg=="ok")
		{
			$db->query("update ".DATABASEU.DATATABLE."_order_request set SendFlag='T' where ID=".$rinfo['ID']." limit 1");
			echo '开通邮件已发送至：'.$tomail.$tomobile; 
		}else{
			echo $rmsg;
		}
		exit;
}

//格式化邮件内容
if($in['m']=="format")
{
	$omsg = "application/json;charset=UTF-8";
	if(!empty($in['content']))
	{
		$mobile = $phone = $qq = $product = $email = $name = $companyname = '';
		$contentarr = explode("\n",$in['content']);

		foreach($contentarr as $v)
		{
			$v = trim($v);
			if(substr($v,0,11) == 'CompanyName') $companyname = str_replace('CompanyName：','',$v);
			if(substr($v,0,4) == 'Name') $name = str_replace('Name：','',$v);
			if(substr($v,0,7) == 'Contact') $contact = str_replace('Contact：','',$v);
			if(substr($v,0,5) == 'Email') $email = str_replace('Email：','',$v);
			if(substr($v,0,2) == 'QQ') $qq = str_replace('QQ：','',$v);
			if(substr($v,0,7) == 'Product') $product = str_replace('Product：','',$v);
			if(!empty($contact)) if(is_phone($contact)) $mobile = $contact; else $phone = $contact;
			if(!empty($email) && !is_email($email)) $email = '';
		}
		if(!empty($companyname) && !empty($name))
		{
			$omsg = '{"backtype":"ok", "CompanyName":"'.$companyname.'", "Name":"'.$name.'", "Mobile":"'.$mobile.'", "Phone":"'.$phone.'","Email":"'.$email.'", "QQ":"'.$qq.'", "Product":"'.$product.'"}';
		}else{
			$omsg = '{"backtype":"资料格式不正确，只能复制邮箱中的内容!"}';
		}
	}else{
		$omsg = '{"backtype":"请复制邮件中的客户资料"}';
	}
	echo $omsg;
	exit();
}

/********** Function ************/
	function ChangeMsg($msgu,$msgp)
    {
       	if(!empty($msgu) && !empty($msgp))
       	{
     		$delmsg = md5($msgu);
       		$rname  = substr($delmsg,5,1).",".substr($delmsg,7,1).",".substr($delmsg,15,1).",".substr($delmsg,17,1);
     		$rnamearray = explode(',',$rname);
       		$rpass  = md5($msgp);
       		$r_msg = str_replace($rnamearray, "", $rpass);
       	}else{
       		$r_msg = $msgp;
       	}
     	return $r_msg;
   } 


function send_mail($to_address, $to_name ,$subject, $body, $attach = "")
{
	//使用phpmailer发送邮件
	require_once("../class/phpmailer/class.phpmailer.php");
	$mail = new PHPMailer();
	$mail->IsSMTP(); // set mailer to use SMTP
	//$mail->SMTPDebug = true;
	$mail->CharSet = 'utf-8';
	$mail->Encoding = 'base64';
	$mail->FromName = '订货宝试用帐号';
	$mail->Port = 25; //default is 25, gmail is 465 or 587
	$mail->SMTPAuth = true;

	$randnum = rand();
	$ynum = $randnum % 3;
	if($ynum == 1){
		$mail->From = 'kf05@rsung.com';
		$mail->Host = 'smtp.qq.com';
		$mail->Username = "2261915847@qq.com";
		$mail->Password = "rsungdhb123456";
	}elseif($ynum == 2){
		$mail->From = 'dhb_kf@126.com';
		$mail->Host = 'smtp.126.com';
		$mail->Username = "dhb_kf@126.com";
		$mail->Password = "rsung123456";
	}else{
		$mail->From = 'kf06@rsung.com';
		$mail->Host = 'smtp.qq.com';
		$mail->Username = "1871822876@qq.com";
		$mail->Password = "rsungdhb123456";
	}
	
	$mail->From = 'dhb_kf@126.com';
	$mail->Host = 'smtp.126.com';
	$mail->Username = "dhb_kf@126.com";
	$mail->Password = "rsung123456";

	$mail->AddAddress($to_address, $to_name);

	$mail->WordWrap = 50;
	if (!empty($attach)) $mail->AddAttachment($attach);
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $body;
	//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

	if(!$mail->Send())
	{
		$backmsg = "发送失败: " . $mail->ErrorInfo . "(".$mail->From.")";
		return $backmsg;
	}
	else
	{
		return "ok";
	}

}


exit('非法操作!');
?>