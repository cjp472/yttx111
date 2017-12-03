<?
/**
 * Infomation
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/module/listinfo.php");

$input		=	new Input;
$in			=	$input->parse_incoming();
$urlmsg     =   "";
$location   =   null;
$db2 = new ezSQL_mysql(DB_USER,DB_PASSWORD,DB_DATABASE,DB_HOST);
$db2->query("set names 'utf8'");

if(empty($in['ID'])) $in['ID'] = '';
$in['sid'] = 8;


	$infomationtitle	= listinfo::showhelptitle($in['sid'],50,$db2);
	$infomation			= listinfo::showhelpinfo($in['sid'],$in['ID'],$db2);
	$infomation['ArticleContent'] = html_entity_decode($infomation['ArticleContent'], ENT_QUOTES,'UTF-8');

	include template("help");

$db2->close();
?>