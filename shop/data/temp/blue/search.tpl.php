<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="<?=CONF_PATH_IMG?>css/showpage.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="template/js/wm-zoom/jquery.wm-zoom-1.0.min.css">

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<script type="text/javascript" src="template/js/wm-zoom/jquery.wm-zoom-1.0.min.js"></script>
<style  type="text/css">
.ordertd tbody tr td{
vertical-align:middle;
}
.ddclear{
clear:both
}
</style>
<script type="text/javascript">
$(document).ready(function(){
$('.my-zoom-1').WMZoom();
});
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div class="main_left">
 
<? include template('left'); ?>
</div>

<div class="main_right">
<div id="location" style="margin-left: -250px"><strong>您的当前位置： </strong><a href="home.php">首页</a> / <a href="list.php?t=<?=$in['t']?>">商品</a> / <a href="#">搜索</a>

</div>
<div class="right_product_tit">
<form name="changetypeform" id="changetypeform" action="search.php" method="get">
<div class="xs_0">搜索：</div>
<div class="xs_1">
显示方式：
<? if($in['t'] == "textlist") { ?>
<a href="javascript:change_show_type('imglist')" ><img src="<?=CONF_PATH_IMG?>images/list_type_0.jpg" class="img" title="图文形式" id="imglist_imgid" onmouseover="change_img_over('1')" onmouseout="change_img_out('1')" /></a>&nbsp;&nbsp;&nbsp;<img src="<?=CONF_PATH_IMG?>images/list_type_3.jpg" class="img" title="列表形式" />
<? } else { ?>
<img src="<?=CONF_PATH_IMG?>images/list_type_1.jpg" class="img" title="图文形式" />&nbsp;&nbsp;&nbsp;<a href="javascript:change_show_type('textlist')" ><img src="<?=CONF_PATH_IMG?>images/list_type_2.jpg" id="textlist_imgid" onmouseover="change_img_over('2')" onmouseout="change_img_out('2')" class="img" title="列表形式" /></a>
<? } ?>
&nbsp;&nbsp;
<select id="o" name="o" onchange="javascript:submit()">
                <optgroup label="- 排序条件 -">
<option value="0" 
<? if($in['o'] == "0") { ?>
selected="selected"
<? } ?>
 >默认排序</option>
                    <option value="1" 
<? if($in['o'] == "1") { ?>
selected="selected"
<? } ?>
 >价格降序</option>
                    <option value="2" 
<? if($in['o'] == "2") { ?>
selected="selected"
<? } ?>
 >价格升序</option>
                    <option value="3" 
<? if($in['o'] == "3") { ?>
selected="selected"
<? } ?>
 >上架时间</option>
                </optgroup>
</select>
&nbsp;&nbsp;
<select name="ps" id="ps" onchange="javascript:submit()">
                <optgroup label="- 每页显示 -">
                    <option value="18" 
<? if($in['ps'] == "18") { ?>
selected="selected"
<? } ?>
 >18条</option>
                    <option value="30" 
<? if($in['ps'] == "30") { ?>
selected="selected"
<? } ?>
 >30条</option>
                    <option value="50" 
<? if($in['ps'] == "50") { ?>
selected="selected"
<? } ?>
 >50条</option>
                </optgroup>
</select>

<input type="submit" value="GO" class="hide" />
<input type="hidden" name="t" id="t" value="<?=$in['t']?>" />
<input type="hidden" name="kw" id="kw2" value="<?=$enkw?>" />
<? if($in['ism']=="yes") { ?>
<input type="hidden" name="ism" id="ism2" value="yes" />
<? } ?>
</div>
</form>
</div>


<div class="right_product_main">

         	<div class="list_line">
<? if(empty($goodslist['total'])) { if(!empty($goodslist['error'])) { ?>
<div class="border_line"><strong>错误：</strong><?=$goodslist['error']?></div>
<? } else { if(!empty($goodslist['corrected'])) { ?>
<div class="border_line"><strong>您是不是要找：</strong>
<? if(is_array($goodslist['corrected'])) { foreach($goodslist['corrected'] as $ckey => $cvar) { ?>
<a href="search.php?kw=<? echo urlencode($cvar);; ?>"><?=$cvar?></a>&nbsp;&nbsp;
<? } } ?>
</div>
<? } else { ?>
<div class="border_line">
<p>&nbsp;&nbsp;找不到和 <strong><?=$enkw?></strong> 相符的内容或信息。建议您：</p>
<ol>
<li>请检查输入字词有无错误。</li>
<li>请换用另外的查询字词。</li>
<li>请改用较短、较为常见的字词。</li>
</ul>
</div>
<? } } } else { ?>
<div class="line font14" style="border-bottom:#e7e3dd solid 1px;">&nbsp;&nbsp;&nbsp;&nbsp;搜索：“<span class="font14h"><?=$enkw?></span>”&nbsp;&nbsp;共有 <span class="font14h"><?=$goodslist['total']?></span> 项符合查询结果</div>
<? include template('unicom+content_list_inc'); if(!empty($goodslist['related'])) { ?>
<div class="border_line"><strong>相关搜索：</strong>
<? if(is_array($goodslist['related'])) { foreach($goodslist['related'] as $ckey => $cvar) { ?>
<a href="search.php?kw=<? echo urlencode($cvar); ?>"><?=$cvar?></a>&nbsp;&nbsp;
<? } } ?>
</div>
<? } ?>
<div class="list_showpage"><?=$goodslist['showpage']?></div>
<? } ?>
<br />&nbsp;
            </div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
