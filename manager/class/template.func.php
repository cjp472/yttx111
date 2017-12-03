<?
function parse_template($file,$tpldir="default/")
{
	global $language;
	$nest = 5;
	$tpldirmsg = str_replace("/", "_", $tpldir);
	$tplfile = CONF_PATH_TPL."/".$tpldir."".$file.".html";
	$objfile = CONF_PATH_COMPILE."/".$tpldirmsg."".$file.".tpl.php";

	if(!@$fp = fopen($tplfile, 'r'))
	{
		exit("'$file.html' Not Fund!");
	}

	$template = fread($fp, filesize($tplfile));
	fclose($fp);

	$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
	$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

	$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
	$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
	$template = preg_replace("/\{lang\s+(.+?)\}/ies", "languagevar('\\1')", $template);
	$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);

	$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
	$template = preg_replace("/$var_regexp/es", "addquote('<?=\\1?>')", $template);
	$template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "addquote('<?=\\1?>')", $template);

	$template = preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_]+)\}[\n\r\t]*/is", "\n<? include template('\\1'); ?>\n", $template);
	$template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", "\n<? include template(\\1); ?>\n", $template);
	$template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('\n<? \\1 ?>\n','')", $template);
	$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<? echo \\1; ?>','')", $template);
	$template = preg_replace("/[\n\r\t]*\{elseif\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('\n<? } elseif(\\1) { ?>\n','')", $template);
	$template = preg_replace("/[\n\r\t]*\{else\}[\n\r\t]*/is", "\n<? } else { ?>\n", $template);

	for($i = 0; $i < $nest; $i++){
		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('\n<? if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\n\\3\n<? } } ?>\n')", $template);
		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('\n<? if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\n\\4\n<? } } ?>\n')", $template);
		$template = preg_replace("/[\n\r\t]*\{if\s+(.+?)\}[\n\r]*(.+?)[\n\r]*\{\/if\}[\n\r\t]*/ies", "stripvtags('\n<? if(\\1) { ?>','\n\\2\n<? } ?>\n')", $template);
	}

	$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
	$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

	if(!@$fp  = fopen($objfile, 'w'))
	{
		exit("Template compile the catalog was not found or no competence!");
	}

	flock($fp, 2);
	fwrite($fp, $template);
	fclose($fp);
	return $template;
}

function addquote($var)
{
	return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}

function languagevar($var)
{
	if(isset($GLOBALS['language'][$var]))
	{
		return $GLOBALS['language'][$var];
	} else {
		return "!$var!";
	}
}

function stripvtags($expr, $statement)
{
	//$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\"\'\[\]\$\x7f-\xff]*)\?\>/s", "\\1", $expr));
	$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
	$statement = str_replace("\\\"", "\"", $statement);
	return $expr.$statement;
}

function template($file,$tpldir ='')
{
	$tpldirmsg = str_replace("/", "_", $tpldir);
	$tplfile = CONF_PATH_TPL."/".$tpldir."".$file.".html";
	$objfile = CONF_PATH_COMPILE."/".$tpldirmsg."".$file.".tpl.php";

	if(@filemtime($tplfile) > @filemtime($objfile))
	{
		parse_template($file,$tpldir);
	}

	return $objfile;
}


function use_cache($key = '',$dir = '')
{
	
		global $cache_filename;

		$cache_filename = CONF_PATH_CACHE. '/'.$dir.$key. '_' . md5($_SERVER['REQUEST_URI']) . '.txt';
		$cachelifetime  = CACHE_LIFETIME*60*60;

		if (@is_file($cache_filename) && filesize($cache_filename) && ((time() - @filemtime($cache_filename)) < $cachelifetime))
		{
			readfile($cache_filename);
			exit;
		}else{
			ob_start("cache_callback");
		}

}

function cache_callback ($output)
{
	global $cache_filename;
	chdir(dirname($_SERVER['SCRIPT_FILENAME']));

	if ($hd = fopen($cache_filename, 'w'))
	{
		flock($hd,LOCK_EX);
		fwrite($hd, $output);
		flock($hd,LOCK_UN);
		fclose($hd);
	}
	return $output;
}
?>