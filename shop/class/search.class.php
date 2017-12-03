<?php
/**
 * search.class.php 
 * 搜索项目入口文件
 * 

 * 默认编码：UTF-8
 */
// 加载 XS 入口文件
require_once '/usr/local/xunsearch/sdk/php/lib/XS.php';

//
// 支持的 GET 参数列表
// q: 查询语句
// m: 开启模糊搜索，其值为 yes/no
// f: 只搜索某个字段，其值为字段名称，要求该字段的索引方式为 self/both
// s: 排序字段名称及方式，其值形式为：xxx_ASC 或 xxx_DESC
// p: 显示第几页，每页数量为 XSSearch::PAGE_SIZE 即 10 条
// ie: 查询语句编码，默认为 UTF-8
// oe: 输出编码，默认为 UTF-8
// xml: 是否将搜索结果以 XML 格式输出，其值为 yes/no
//
// variables
function ContentSearch($ins)
{
	$eu = '';
	$__ = array('q', 'm', 'f', 's', 'p', 'ie', 'oe', 'syn', 'xml','psize');
	foreach ($__ as $_)
		$$_ = isset($ins[$_]) ? $ins[$_] : '';

	$ie  = 'UTF-8';
	$oe = 'UTF-8';

	// recheck request parameters
	$q = get_magic_quotes_gpc() ? stripslashes($q) : $q;
	$q = str_replace("-"," ",$q);
	$f = empty($f) ? '_all' : $f;
	$psize = empty($psize)?'18':$psize;

	// base url
	$bu = $_SERVER['SCRIPT_NAME'] . '?q=' . urlencode($q) . '&m=' . $m . '&f=' . $f . '&s=' . $s . $eu;

	// other variable maybe used in tpl
	$count = $total = $search_cost = 0;
	$docs = $related = $corrected = $hot = array();
	$error = $pager = '';
	//$total_begin = microtime(true);

	// perform the search
	try
	{
		$xs = new XS('content_index');
		$search = $xs->search;
		$search->setCharset('UTF-8');

		if (empty($q))
		{
			// just show hot query
			$hot = $search->getHotQuery();
		}
		else
		{
			// fuzzy search
			$search->setFuzzy($m === 'yes');

			// synonym search
			$search->setAutoSynonyms($syn === 'yes');
			
			// set query
			if (!empty($f) && $f != '_all')
			{
				$search->setQuery($f . ':(' . $q . ')');
			}
			else
			{
				$search->setQuery($q);
			}

			$search->addRange('CompanyID',$_SESSION['cc']['ccompany'],$_SESSION['cc']['ccompany']);

			// set sort
			if(empty($s))
			{
				//$sorts = array('OrderID' => true, 'ID' => true);
				//$search->setMultiSort($sorts);
			}elseif($s=="1"){
				$search->setSort('Price', true);
			}elseif($s=="2"){
				$search->setSort('Price', false);
			}elseif($s=="3"){
				$search->setSort('ID', false);
			}elseif($s=="4"){
				$search->setSort('ID', true);
			}

			// set offset, limit
			$p = max(1, intval($p));
			//$n = XSSearch::PAGE_SIZE;
			$n = $psize;
			$search->setLimit($n, ($p - 1) * $n);

			// get the result
			//$search_begin = microtime(true);
			$docs = $search->search();
			//$search_cost = microtime(true) - $search_begin;
			$sdata['docs']	  = $docs;

			// get other result
			$count = $search->getLastCount();
			$total = $search->getDbTotal();
			$sdata['count']	  = $count;
			if ($xml !== 'yes')
			{
				// try to corrected, if resul too few
				if ($count < 1)	$corrected = $search->getCorrectedQuery();			
				// get related query
				$related = $search->getRelatedQuery();			

				$sdata['corrected'] = $corrected;
				$sdata['related']	  = $related;
			}
		}
	}
	catch (XSException $e)
	{
		$error = strval($e);
		$sdata['error'] = $error;
	}

	// calculate total time cost
	//$total_cost = microtime(true) - $total_begin;
	return $sdata;
	exit;
}
?>