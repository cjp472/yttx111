<div class="fenlei_tit2" style="margin-top: 32px"><img src="<?=CONF_PATH_IMG?>images/spfl2x.png" border="0" style="width:226px;" /></div>
<div class="fenlei">
<ul>
<span class="fen2"><a href="wishlist.php" > &#8250;&#8250; 我的收藏</a></span>
<span class="fen"><a href="myorder.php?m=product" > &#8250;&#8250; 我订过的</a></span><br />
<span class="fen2"><a href="list.php?m=spc&ty=1" > &#8250;&#8250; 推荐商品</a></span>
<span class="fen"><a href="list.php?m=spc&ty=2" > &#8250;&#8250; 特价促销</a></span><br />
<span class="fen2"><a href="list.php?m=spc&ty=3" > &#8250;&#8250; 新款上架</a></span>
<span class="fen"><a href="list.php?m=spc&ty=4" > &#8250;&#8250; 热销商品</a></span><br />
<span class="fen2"><a href="list.php?m=spc&ty=9" > &#8250;&#8250; 缺货商品</a></span>
</ul>
</div>
<div class="fenlei_bottom"></div>
<!--sort-->
<div class="fenlei_tit">
    <a href="list.php"><img src="<?=CONF_PATH_IMG?>images/spfl2x.png" style="width: 226" border="0" /></a>
    <a href="javascript:;" class="expend-act" style="font-size:14px;margin-top:-30px;z-index:10;width:50px;height:37px;position:absolute;color:#fff;margin-left:170px;display:block;text-align:center;" title="展开/折叠">
        收起 ∧
    </a>
    <script>
        $(function(){
            if($.cookie("category_expend") == 0) {
                $(".syd-category li").hide();
                $(".syd-category").addClass("expend-close");
                $(".expend-act").html("展开 ∨");
            }
            if($.cookie("category_sub_expend") != "") {
                $(".syd-category ul[data-id='"+ $.cookie("category_sub_expend")+"'] a[data-target]").html("-");
                $(".syd-category ul[data-id='"+ $.cookie("category_sub_expend")+"']").find("li").show().end().siblings("ul[data-id]").find("li").hide();
            }
            $(".expend-act").click(function(){
                if($(".syd-category").hasClass("expend-close")) {
                    $(".syd-category li").show();
                    $.cookie("category_expend",1);
                    $(this).html("收起 ∧");
                } else {
                    $(".syd-category li").hide();
                    $.cookie("category_expend",0);
                    $(this).html("展开 ∨");
                }
                $(".syd-category").toggleClass('expend-close');
            });
            $(".syd-category a[data-target]").click(function(){
                var tg = $(this).attr('data-target');
                var lis = $(".syd-category ul[data-id='"+tg+"'] li");
                $(".syd-category ul[data-id='"+tg+"']").siblings("ul").find("a[data-target]").html("+").end().find("li").hide();
                lis.toggle();
                if(lis.is(":visible")) {
                    //显示
                    $.cookie("category_sub_expend" , tg);
                    $(this).html('-');
                } else {
                    //隐藏
                    $.cookie("category_sub_expend" , "");
                    $(this).html('+');
                }
            });
        });
    </script>
</div>
<div class="fenlei syd-category">
<? if(is_array($listallsite)) { foreach($listallsite as $skey => $svar) { ?>
<ul data-id="<?=$svar['SiteID']?>">
<span class="fen">
            
<? if(count($svar['son']) > 0) { ?>
            <a href="javascript:;" data-target="<?=$svar['SiteID']?>">+</a>
            
<? } ?>
            <a href="list.php?s=<?=$svar['SiteID']?>&t=<?=$in['t']?>" onclick="$.cookie('category_sub_expend','<?=$svar['SiteID']?>');" 
<? if(!empty($in['s']) && $in['s']==$svar['SiteID']) { ?>
class="current-category"
<? } ?>
> <?=$svar['SiteName']?></a>
        </span>
<br/>
<? if(is_array($svar['son'])) { foreach($svar['son'] as $sonkey => $sonvar) { ?>
<li><a href="list.php?s=<?=$sonvar['SiteID']?>&t=<?=$in['t']?>" 
<? if(!empty($in['s']) && $in['s']==$sonvar['SiteID']) { ?>
class="current-category"
<? } ?>
> <?=$sonvar['SiteName']?></a></li>
<? } } ?>
</ul>
<? } } ?>
<ul>
<span class="fen"><a href="wishlist.php" > &#8250;&#8250; 我的收藏</a></span>
</ul>
</div>
<div class="fenlei_bottom"></div>
<!--brand-->
<? if($brand_info) { ?>
<div class="fenlei_tit2" ><img src="<?=CONF_PATH_IMG?>images/fenlei.png" border="0" /></div> 
<div class="fenlei syd-brand" style="overflow:hidden;">
<ul>
<span class="fen"><a href="list.php" > &#8250;&#8250; 商品品牌</a></span><br />
<? if(is_array($brand_info)) { foreach($brand_info as $sonkey => $sonvar) { ?>
<li><a href="list.php?b=<?=$sonvar['BrandID']?>&t=<?=$in['t']?>" > <?=$sonvar['BrandName']?></a></li>
<? } } ?>
</ul>
</div>
<? if(count($brand_info) > 20) { ?>
<div style="position: absolute; z-index:99; ">
    <div id="brand-allbutton" style="position: absolute; z-index:99; background:url(./template/img/20130415i.png) -125px 0 no-repeat; text-align:center; left:175px; width:55px; height:26px; line-height:25px; cursor: pointer;" >
        <span onclick="show_brand_all('ALL');">展开 ∨ </span>
    </div>
    <script>
        function show_brand_all(ty) {
            if(ty=='ALL')
            {
                $(".syd-brand").css('height','auto');
                $("#brand-allbutton").html('<span onclick="show_brand_all(\'NO\');">收起 ∧ </span>');
            }else{
                $(".syd-brand").css('height','280px');
                $("#brand-allbutton").html('<span onclick="show_brand_all(\'ALL\');">展开 ∨ </span>');
            }
        }
        show_brand_all('NO');
    </script>
</div>
<? } ?>
<div class="fenlei_bottom"><img src="<?=CONF_PATH_IMG?>images/fenlei_bottom.jpg" /></div>
<? } ?>
<div id="windowForm">
<div id="windowContent">  </div>
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