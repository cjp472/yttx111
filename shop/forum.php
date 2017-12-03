<?
/**
 * Forum
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/forum.php");
include_once (SITE_ROOT_PATH."/class/sms.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();
$urlmsg     =   "";
$location   =   null;

if(empty($in['m']))
{	
	$infomation  	  = forum::listforum($in['ty']);

	include template("forum");
}
elseif($in['m']=="submitforum")
{
	if(empty($in['forumname']) || empty($in['forumtitle']) || empty($in['froumcontent'])) exit('呢称 / 标题 / 内容 不能为空!');
	$in = $input->_htmlentities($in);
	$inf  	  = forum::insertforum($in);
	if($inf)
	{
		exit('ok');
	}else{
		exit('发表不成功...');
	}
}
elseif($in['m']=="submitreply")
{
	if(empty($in['pid'])) exit('Error');
	if(empty($in['replycontent']) || empty($in['replyname'])) exit('Error');
	$in = $input->_htmlentities($in);
	$inf  = forum::replyforum($in);
	if($inf)
	{
		echo '<div class="line"><img src="template/default/img/icon_arrow_down.gif" border="0" />&nbsp;<span class="bold">'.$in['replyname'].'</span>&nbsp;|&nbsp;<span>'. date("Y-m-d H:i").'</span></div>
                <div class="line" style="margin-left:10px; line-height:180%; padding:8px; font-size:14px;">'.str_replace("\n","<br />",$in['replycontent']).'</div>';
		exit();
	}else{
		exit('Error');
	}

}
?>