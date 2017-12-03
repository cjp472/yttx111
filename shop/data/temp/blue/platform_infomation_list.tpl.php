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


</head>

<body>
<? include template('header'); ?>
<div id="main">
<div class="main_left" style="margin-top: 33px;">
<div class="fenlei_bottom" style="width: 225px;height: 40px;color:white;background-color: #01A157;">
<span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px;margin-top: -8px"></span>   信息栏目
<i style="display:block;width: 225px;margin-top: -4px;margin-left: 45px">Platform information</i>
</div>
  <div class="news_info">
  <ul>
<li><a href="infomation.php?sid=0" ><span class="ali-small-circle iconfont icon-next-s"></span>公告信息</a></li>
<li><a href="infomation.php?ty=platfrom_list" ><span class="ali-small-circle iconfont icon-next-s"></span>平台通知</a></li>
<? if(is_array($sortdata_arr_left)) { foreach($sortdata_arr_left as $skey => $svar) { ?>
                <li><a href="infomation.php?sid=<?=$svar['SortID']?>" ><span class="ali-small-circle iconfont icon-next-s"></span><?=$svar['SortName']?></a></li>
<? } } ?>
  </ul>

  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">
<div id="location" style=" margin-left: -254px;">当前位置：<a href="home.php">首页</a> / <a href="infomation.php">信息</a> / <a href="infomation.php?ty=platfrom_list">平台通知</a></div>
<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>平台通知</div>

</div>

<div class="right_product_main">
<div class="list_line">

<div class="line">
<ol>
        
<? if(is_array($infomation['list'])) { foreach($infomation['list'] as $ikey => $tvar) { ?>
             <li style="font-size:14px; line-height:200%; list-style:none; margin:2px 0 2px 0; border-bottom:1px dotted #dbdbdb;"> <img src="./template/red/images/BLUE.png" class="img" />&nbsp; <a href="infomation.php?ty=plat&m=content&sid=0&ID=<?=$tvar['ArticleID']?>"><?=$tvar['ArticleTitle']?></a>
<? if(empty($sortinfo['SortID'])) { ?>
<span style="font-size:12px; padding-left:5px;">[<? echo date("Y-m-d",$tvar['ArticleDate']); ?>]</span>
<? } ?>
</li>
            
<? } } ?>
</ol>
</div>

<div class="list_showpage"><?=$infomation['showpage']?></div><br />&nbsp;

</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
</body>
</html>
