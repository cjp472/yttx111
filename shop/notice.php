<?
/**
 * my
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
	 
$input		=	new Input;
$in			=	$input->parse_incoming();

if(empty($in['m']))
{
	if(empty($in['gid']))
	{
		$in['gid']  = 0;
	}

	include template("notice");

}elseif($in['m'] == "addnotice"){

	$db	    = dbconnect::dataconnect()->getdb();

	if(empty($in['ProductID'])) exit('参数错误！');

	if(empty($in['Email']) && empty($in['Mobile'])) exit('手机号不能都为空!');
	if(!is_phone($in['Mobile'])) exit('请输入正确的手机号!');

	$countnow = $db->get_row("select count(*) as arow from ".DATATABLE."_order_notice where CompanyID=".$_SESSION['cc']['ccompany']." and ClientID=".$_SESSION['cc']['cid']." and ProductID=".$in['ProductID']." and Flag='0'");

	if(empty($countnow['arow']))
	{	
		$isin = $db->query("insert into ".DATATABLE."_order_notice(CompanyID,ClientID,ProductID,Email,Mobile) values(".$_SESSION['cc']['ccompany'].", ".$_SESSION['cc']['cid'].", ".$in['ProductID'].", '".$in['Email']."', '".$in['Mobile']."')");
		exit('ok');
	}else{
		exit('您已经提交过了,到货我们会及时通知您!');
	}

}
?>