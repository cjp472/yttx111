<?
/**
 * List
 *
 * @author seekfor seekfor@gmail.com
 * @version dhb 1.0 Tue Oct 10 17:42:48 CST 2010 
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/listcatalog.php");

$input		=	new Input;
$in			=	$input->parse_incoming();


$cataloglist  = listcatalog::getallsite();
$k = 0;

for($i=0;$i<count($cataloglist);$i++)
{

		$k++;
		$catalog[$cataloglist[$i]['ParentID']][$cataloglist[$i]['SiteID']] = $cataloglist[$i];
		$catalog[$cataloglist[$i]['ParentID']][$cataloglist[$i]['SiteID']]['ID'] = $k;

}

include template("catalog");
?>