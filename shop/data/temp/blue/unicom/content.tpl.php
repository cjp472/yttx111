<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />

<link href="template/css/img.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="template/red/css/base.css?v=<?=VERID?>" rel="stylesheet" type="text/css">
<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<style>
#windowForm{
display:none;
height:auto;
width:540px;
}
</style>
<script type="text/javascript">
function opentlink(lurl){
window.open(lurl);
}
</script>
</head>
<body>
<? include template('header'); ?>
<div class="main-info">
  <div class="now">当前位置：<a href="home.php"> 首页 </a> / <a href="list.php"> 商品中心 </a> / <a> 商品详情 </a></div>
  <div class="border_line border_line1" style="padding-left:0;padding-right:0;">
 
    <div class="product-img f-l">
      <div id="spec-n1" class="jqzoom product-img-info" onclick="opentlink('content.php?tpl=img&id=<?=$goods['index']['ID']?>')"> 
      
<? if(!empty($goods['index']['PictureBig'])) { ?>
<img src="<?=RESOURCE_PATH?><?=$goods['index']['PictureBig']?>" jqimg="<?=RESOURCE_PATH?><?=$goods['index']['PictureBig']?>"   width="430" height="430" />
<? } else { ?>
<img src="<?=CONF_PATH_IMG?>images/default.jpg" title="<?=$gvar['Name']?>" width="430" height="430"/>
<? } ?>
      	 
      </div>
      
      
<? if(!empty($goods['index']['Picture'])) { ?>
	
      <div id="spec-lists" class="product-img-info"> <a class="spec-control f-l" id="spec-left"><span class=" icon icon6 ">&#x34;</span> </a>
      <div id="spec-list">
        <ul>
<? if(is_array($goods['content']['PicArray'])) { foreach($goods['content']['PicArray'] as $pkey => $pvar) { ?>
<li ><a><img src="<?=RESOURCE_PATH?><?=$pvar['Path']?>img_<?=$pvar['Name']?>" width="70px" height="60px" /></a></li>
<? } } ?>
        </ul>
        </div>
        <a class="spec-control f-r" id="spec-right"><b class=" icon icon6 ">&#x35;</b> </a>
        <div style="clear:both"></div>
      </div>
      
  <SCRIPT type="text/javascript">
$(function(){
$(".jqzoom").jqueryzoom({
xzoom:400,
yzoom:400,
offset:10,
position:"right",
preload:1,
lens:1
});
$("#spec-list").jdMarquee({
deriction:"left",
width:400,
height:72,
step:2,
speed:4,
delay:10,
control:true,
_front:"#spec-right",
_back:"#spec-left"
});
$("#spec-lists img").bind("mouseover",function(){
var src=$(this).attr("src");
$("#spec-n1 img").eq(0).attr({
src:src.replace("\/n5\/","\/n1\/"),
jqimg:src.replace("\/n5\/","\/n0\/")
});
});				
});
</SCRIPT>
<script src="template/js/img.js?v=<?=VERID?>" type="text/javascript"></script>
    
<? } ?>
      
    </div>
    	 
    <div class="product-data f-r">
      <h1 class="color-b font16"><?=$goods['index']['Name']?></h1>
      <div class="price-info m-t"> <span class="price-info-1 f-b m-t-10">¥<?=$goods['index']['Price']?><br />
        <p class="font14">订购价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       
<? if($setarr['product_price']['price1_show'] == 'on') { ?>
        	<?=$setarr['product_price']['price1_name']?>： ¥ <?=$goods['index']['Price1']?>
        
<? } ?>
        
<? if($setarr['product_price']['price2_show'] == 'on') { ?>
        	<?=$setarr['product_price']['price2_name']?>： ¥ <?=$goods['index']['Price2']?>
        
<? } ?>
        </p>
        </span> </div>
      <div class="product-data-1">
      
<? if(!empty($goods['index']['BrandName'])) { ?>
<dt><strong>品牌：</strong><a href="list.php?b=<?=$goods['index']['BrandID']?>&t=imglist" target="_blank"><?=$goods['index']['BrandName']?></a></dt>
<? } ?>
      
<? if(!empty($goods['index']['Coding'])) { ?>
  <!--<dt><strong>编号：</strong><?=$goods['index']['Coding']?></dt>-->
  
<? } if(!empty($goods['index']['Barcode'])) { ?>
<dt><strong>条码：</strong><?=$goods['index']['Barcode']?></dt>
<? } ?>
        
<? if(!empty($goods['index']['Model'])) { ?>
<dt><strong>规格：</strong><?=$goods['index']['Model']?></dt>
<? } if(!empty($goods['index']['Conversion']) && !empty($goods['index']['Casing'])) { ?>
<dt style="height:auto; width:96%;"><strong>包装：</strong><?=$goods['index']['Casing']?> (1<?=$goods['index']['Casing']?> = <?=$goods['index']['Conversion']?><?=$goods['index']['Units']?>)</dt>
<? } if(!empty($goods['content']['ContentPoint'])) { ?>
<dt><strong>积分：</strong><?=$goods['content']['ContentPoint']?></dt>
<? } if(!empty($goods['index']['Nearvalid'])) { ?>
<dt class="ddclear" style="line-height:22px;heihgt:22px;"><span class="" style="display:inline;"><strong>近效期：</strong></span><?=$goods['index']['Nearvalid']?></dt>
<? } if(!empty($goods['index']['Farvalid'])) { ?>
<dt class="ddclear" style="line-height:22px;heihgt:22px;"><span class="" style="display:inline;"><strong>远效期：</strong></span><?=$goods['index']['Farvalid']?></dt>
<? } if($goods['index']['Price2'] > 0 ) { ?>
<dt class="ddclear" style="line-height:22px;heihgt:22px;"><span class="" style="display:inline;"><strong>毛利率：</strong></span>
<?=$goods['index']['maoli']?>%</dt>
<? } if(!empty($goods['index']['Appnumber'])) { ?>
<dt class="ddclear" style="line-height:22px;heihgt:22px;"><span class="" style="display:inline;"><strong>批准文号：</strong></span><?=$goods['index']['Appnumber']?></dt>
<? } if(!empty($goods['library']['pns']) && $goods['library']['pns']=="on") { ?>
	
<dt><strong>库存：</strong>
<? if(!empty($goods['number']['OrderNumber'])) { ?>
<?=$goods['number']['OrderNumber']?>
<? } else { ?>
0
<? } ?>
&nbsp;&nbsp;<?=$goods['index']['Units']?></dt>
<? } else { if($goods['library']['pn']=="on" && $goods['library']['png']=="off" && $goods['number']['OrderNumber'] <=0) { ?>
<dt><strong>库存：【缺货】</strong></dt>
<? } } if(!empty($goods['field'])) { if(is_array($goods['field'])) { foreach($goods['field'] as $fkey => $fvar) { ?>
    
<? if(!empty($fvar['value'])) { ?>
               <dt style="height:auto; width:96%;"><strong><?=$fvar['name']?>：</strong><?=$fvar['value']?></dt>
           
<? } } } } ?>
      </div>
      
<? if($goods['index']['CommendID']=="9") { ?>
  <a href="javascript:void(0);" onclick="noticegoods('<?=$goods['index']['ID']?>');" class="m-l  w-sm m-b f-l btn-4"><img src="<?=CONF_PATH_IMG?>images/daoh.png" border="0" class="img" /></a>
  
<? } else { ?>
  
<? if($goods['library']['pn']=="on" && $goods['library']['png']=="off" && $goods['number']['OrderNumber'] <=0) { ?>
<a href="javascript:void(0);" onclick="noticegoods('<?=$goods['index']['ID']?>');" class=" m-l  w-sm m-b f-l btn-4"><img src="<?=CONF_PATH_IMG?>images/daoh.png" border="0" class="img" /></a>
  
<? } else { ?>
<a href="javascript:void(0);" onclick="addtocart('<?=$goods['index']['ID']?>','<?=$goods['index']['cs']?>');" id="shareit_<?=$goods['index']['ID']?>" class="btn-1 m-l  w-sm m-b f-l btn-1 btn-4"> 
      	<span class="icon " style="font-size:16px;">&#xe07a;</span> 我要订购
    </a> 
  
<? } ?>
  
<? } ?>
      <a onclick="javascript:addtowishlist('<?=$goods['index']['ID']?>');" href="javascript:void(0);" title="将关注的商品加入我的收藏夹，方便日后订购。" class="btn-2 m-l  w-sm m-b f-l btn-2 btn-4"> 
      	<span class="icon" id="to-fav" style="font-size:16px;
<? if($goods['fav']) { ?>
color:red;
<? } ?>
">&#xe031;</span> 收藏商品
      </a> 
    </div>
    <div style="clear:both"></div>
  </div>
  <div class="main-info-l1 f-l m-t">
    <div class="kuaijie kuaijie1">
      
<? include template('unicom+kuaijie'); ?>
    </div>
    <div class="product-xiangguan  m-t">
      <div class="main-info-title"> <b>相关商品</b> </div>
      <ul>
        
<? if(!empty($goodslink)) { if(is_array($goodslink)) { foreach($goodslink as $lkey => $lvar) { ?>
     <li>
<a href="content.php?id=<?=$lvar['ID']?>" class="f-l m-a-5">
<? if(!empty($lvar['Picture'])) { ?>
<img src="<?=RESOURCE_PATH?><?=$lvar['Picture']?>" title="<?=$lvar['Name']?>" width="77" height="66" />
<? } else { ?>
<img src="<?=CONF_PATH_IMG?>images/default.jpg" title="<?=$lvar['Name']?>" width="77" height="66" />
<? } ?>
</a>
<a class=" w-md f-l" title="<?=$lvar['Name']?>" href="content.php?id=<?=$lvar['ID']?>" style="max-height:72px;overflow:hidden;"><?=$lvar['Name']?></a>
<span class="price-0 m-l f-l">￥<b class="price-1"><?=$lvar['Price']?></b></span>
     </li>
<? } } } ?>
      </ul>
      <div  style=" clear:both"></div>
    </div>
  </div>
  <div class="main-info-r f-r m-l m-t">
    <div class="main-info-title"> <b>商品描述</b> </div>
    <div class="main-info-son m-a" id="content-box"> 
    
<? if(!empty($goods['content']['Content'])) { ?>
    		<?=$goods['content']['Content']?>
    		
    		<!-- 如果有table超过了宽度则自动调整 -->
    		<script type="text/javascript">
    		$(function(){
    			$('#content-box > table').each(function(){
    				var _this = $(this);
    				if( _this.width() > $('#content-box').width() ) _this.css('width', '100%');
    			});
    		});
    		</script>
    
<? } else { ?>
    		该商品暂无描述
    	
<? } ?>
</div>
  </div>
  <div  style=" clear:both"></div>
</div>
<div id="shareit-box">
<div id="shareit-header"></div>
<div id="shareit-body">
<div id="shareit-blank"></div>
<div id="shareit-url">数量：<input type="text" value="1" onfocus="this.select();"  name="shareit-field" id="shareit-field" class="field" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" style="width:35px;"  />
<input type="hidden" value="" name="togoodsid" id="togoodsid"  /><span id="show_units_cart"></span></div>
<div id="shareit-icon">
<a href="javascript:void(0);" title="关闭" onclick="hideshow('shareit-box');">X</a> <input type="button" name="addtocart" id="addtocart" value="订 购" class="button_7" onclick="saveonetocart();"  /> 
</div>
</div>
</div>
<!-- 尾部 Start -->
<? include template('bottom'); ?>
<!-- 尾部 End -->
</body>
</html>
