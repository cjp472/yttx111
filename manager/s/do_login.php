<?php
include_once ("../common.php");

$inv = new Input();
$in  = $inv->parse_incoming();
$db	 = dbconnect::dataconnect()->getdb();

$fromurl = strtolower($_SERVER["HTTP_REFERER"]);
if(empty($in['m']))
{
 	echo "error!";
 	exit();
}
$in = $inv->_htmlentities($in);

if($in['m']=="logout")
{	
	session_unset(); 
	session_destroy();

		echo '
		<script language="javascript">
		window.location.href="../";
		</script>
		';
}
elseif($in['m']=="change_pass")
{
	$oldpass = strtolower($in['OldPass']);
	$newpass = strtolower($in['NewPass']);

	$opsmsg = ChangeMsg($_SESSION['uinfo']['username'],$oldpass);
	$npsmsg = ChangeMsg($_SESSION['uinfo']['username'],$newpass);

	if(!is_filename($newpass)) exit('请输入合法的密码！(3-18位数字、字母和下划线)');
	if(strlen($newpass) < 3 || strlen($newpass) > 18 ) exit('请输入合法的密码！(3-18位数字、字母和下划线)');

	$upsql = "update ".DATABASEU.DATATABLE."_order_user set UserPass='".$npsmsg."' where UserID=".$_SESSION['uinfo']['userid']." and UserCompany=".$_SESSION['uinfo']['ucompany']." and UserPass='".$opsmsg."'";

	if($db->query($upsql))
	{
		exit("ok");
	}else{
		exit("olderror");
	}
}



/********** Function ************/

	function ChangeMsg($msgu,$msgp)
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

exit('非法操作!');
?>