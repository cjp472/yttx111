<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<style type="text/css">
<!--
.leftimg{width:70%; float:left; height:auto; min-height:400px; margin:8px;}
.rightimg{width:25%; float:right; height:auto; margin:12px 8px; min-height: 400px;  border:#CCCCCC solid 1px; }
.title{width:100%; margin:4px;font-weight:bold; border:#CCCCCC solid 1px; background-color:#efefef; padding:4px; font-size:14px;}

.title2{width:94%; margin:4px;  padding:2px; font-size:14px;}
#bigimg{width:98%; margin:4px; padding:4px; text-align:center;}
.thumbimg{margin:2px;}
.thumbimg li{list-style:none; list-style-type:none; margin:6px; padding:4px; border:#eeeeee solid 1px; width:95px; height:95px; float:left; text-align:center; overflow:hidden; }
.thumbimg li img{ height:85px; }
.search-title{ border:none;margin-top:0 !important; }
-->
</style>
<script type="text/javascript">
function set_bigimg(bigimg)
{
document.getElementById('bigimg').innerHTML = '<IMG src="'+bigimg+'"   />';
}
</script>
</head>

<body>
<? include template('header'); ?>
<div class="main-info">
  <div class="now">当前位置： <a href="home.php"> 首页 </a> / <a href="list.php"> 商品中心 </a> / <a>商品图片</a> </div>
  <div class="car_tit" style="height:5px; overflow:hidden;"></div>
  <div class="border_line1" style="min-height:600px; height;auto;">
    <div class="leftimg">
      <div class="title"><?=$goods['index']['Name']?></div>
      <div id="bigimg"><IMG style="max-width:685px;" src="<?=RESOURCE_PATH?><?=$goods['index']['PictureBig']?>" alt="<?=$goods['index']['Name']?>"  /></div>
    </div>
    <div class="rightimg">
      <div class="title2"> 商品图片 （
        <? echo count($goods['content']['PicArray']); ?>        张）</div>
      <div class="thumbimg">
        
<? if(is_array($goods['content']['PicArray'])) { foreach($goods['content']['PicArray'] as $pkey => $pvar) { ?>
        <li><a href="javascript:void(0);" onClick="set_bigimg('<?=RESOURCE_PATH?><?=$pvar['Path']?>img_<?=$pvar['Name']?>');"><img src="<?=RESOURCE_PATH?><?=$pvar['Path']?>thumb_<?=$pvar['Name']?>" border="0"  /></a></li>
        
<? } } ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
