<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="template/red/css/showpage.css" rel="stylesheet" type="text/css">
<link href="template/red/css/base.css" rel="stylesheet" type="text/css">
<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/jqzoom.pack.1.0.1.js" type="text/javascript"></script>
<style>
#windowForm{
display:none;
height:auto;
width:540px;
}
.altimg {
    display: none;
    height: 120px;
    overflow: hidden;
    padding-top: 25px;
    position: absolute;
    text-align: left;
    width: 160px;
    z-index: 998;
}
.ddclear{
clear:both
}
.list_content dl dd span{
display:inline-block;
}
</style>
<script type="text/javascript">
$(function() {
$(".jqzoom").jqzoom();
});
</script>
</head>

<body>
<? include template('header'); ?>
<div class="main-info">
<div class="now">当前位置：<a href="home.php"> 首页 </a> / 
<a href="list.php?m=<?=$in['m']?>&ty=<?=$in['ty']?>"><?=$titlemsg?> </a>
</div>
    <div class="main-info-l1 f-l">

<div class="product-type">
        <div class="main-info-title">
        	<b>所有分类</b>
        </div>
   
<? include template('unicom+leftMenu'); ?>
    </div>
    
    <div class="kuaijie kuaijie1 m-t">
    
<? include template('unicom+kuaijie'); ?>
    </div>
    </div>
    <div class="main-info-r f-r m-l">
    	<form name="changetypeform" id="changetypeform" action="list.php?m=spc" method="get">
    	<div class="main-info-title">
        	<b>商品列表</b>
            <a class="f-r m-r icon5" href="
<? if($goodslist['nextpage']) { ?>
<?=$goodslist['nextpage']?>
<? } else { ?>
javascript:;
<? } ?>
"><span class="icon ">&#xe046;</span></a> 
            <a class="f-r icon5" href="
<? if($goodslist['prepage']) { ?>
<?=$goodslist['prepage']?>
<? } else { ?>
javascript:;
<? } ?>
"><span class="icon ">&#xe045;</span></a>
            <span class="f-r m-r">
            <strong class="color-r m-l">
           
<? if($in['page']) { ?>
           			<?=$in['page']?>
           
<? } else { ?>
           			1
           		
<? } ?>
            </strong >
            /<?=$goodslist['totalpage']?>
            </span>
            <select name="ps" id="ps" onchange="javascript:submit()" class=" f-r xiala m-t-5">
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
            <select id="o" name="o" onchange="javascript:submit()" class="f-r xiala m-r m-t-5">
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
<? if($in['t'] == "textlist") { ?>
<a class="f-r m-r xiala xiansi f-r-active "><span class="icon ">&#xe056;</span> 无图</a> 
            <a class="f-r xiala xiansi" href="javascript:change_show_type('imglist')"><span class="icon ">&#xe08c;</span> 有图</a>
            
<? } else { ?>
            	<a class="f-r m-r xiala xiansi" href="javascript:change_show_type('textlist')"><span class="icon ">&#xe056;</span> 无图</a> 
            <a class="f-r xiala xiansi f-r-active "><span class="icon ">&#xe08c;</span> 有图</a>
            
<? } ?>
<input type="submit" value="GO" style="display:none" />
<input type="hidden" name="t" id="t" value="<?=$in['t']?>" />
<input type="hidden" name="m" id="m" value="<?=$in['m']?>" />
<input type="hidden" name="ty" id="ty" value="<?=$in['ty']?>" />
<input type="hidden" name="s" id="s" value="<?=$in['s']?>" />
<input type="hidden" name="b" id="b" value="<?=$in['b']?>" />					
        </div>
        </form>
    
<? include template('unicom+content_list_inc'); ?>
        <div class="pagelink">
        	<div class="pagelink_info">
 			<?=$goodslist['showpage']?>
 		</div>
        </div>
    </div>     
    <div  style=" clear:both"></div>
</div>

<!-- 尾部 Start -->
<? include template('bottom'); ?>
<!-- 尾部 End -->
</body>
</html>

<script>
//控制二级菜单框的高度

$(function(){
    if(parseInt($(".menuAdd").height())<"474" || parseInt($(".menuAdd").height()) =="474"){
        $(".menuAdd").css("height","474px");
    }
});
</script>