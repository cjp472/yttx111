<?php

	$burl = 'https://api.dhb168.com/api.php?controller=WeiXin&action=getOpenId&noSkey=noSkey&code='.trim($_GET['code']).'&state='.trim($_GET['state']).'';
	header("location: ".$burl);
?>