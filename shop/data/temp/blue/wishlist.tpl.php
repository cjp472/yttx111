<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="<?=CONF_PATH_IMG?>css/showpage.css" rel="stylesheet" type="text/css">

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/jqzoom.pack.1.0.1.js" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
$(".jqzoom").jqzoom();
});
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="list.php?t=<?=$in['t']?>">我收藏的商品</a></div>
<div class="main_left" style="margin-top: -32px">
 
<? include template('left'); ?>
</div>
<div class="main_right">

<div class="right_product_tit">
<form name="changetypeform" id="changetypeform" action="wishlist.php" method="get">
<div class="xs_0">共有 <?=$goodslist['total']?> 件藏品：</div>
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
                    <option value="4" 
<? if($in['o'] == "4") { ?>
selected="selected"
<? } ?>
 >商品人气</option>
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
<input type="hidden" name="s" id="s" value="<?=$in['s']?>" />
</div>
</form>
</div>


<div class="right_product_main">
         	<div class="list_line">
<? if($goodslist['showpage']) { ?>
<div class="list_showpage"><?=$goodslist['showpage']?></div>
<? } include template('content_list_inc'); ?>
<div class="list_showpage"><?=$goodslist['showpage']?>
<? if(empty($goodslist['list'])) { ?>
<p align="center"><img src="<?=CONF_PATH_IMG?>images/null.jpg" style="width: 340px;height: 340px;" alt=""/></p>
<? } ?>
</div><br />&nbsp;
            </div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
