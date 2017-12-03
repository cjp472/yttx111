<?php
echo 'document.write (\'<input type="hidden" name="LoginIP" id="LoginIP" value="'.RealIp().'" />\'); ';

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
?>