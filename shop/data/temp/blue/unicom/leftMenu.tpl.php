

<ul class="menu1">
<? $loopnum = 0; if(is_array($listallsite)) { foreach($listallsite as $skey => $svar) { if($loopnum < 11) { ?>
<li data-id="<?=$svar['SiteID']?>"><a href="list.php?s=<?=$svar['SiteID']?>&t=<?=$in['t']?>" onclick="$.cookie('category_sub_expend','<?=$svar['SiteID']?>');" 
<? if(!empty($in['s']) && $in['s']==$svar['SiteID']) { ?>
class="current-category"
<? } ?>
><span class="f-l"><?=$svar['SiteName']?></span> </a></li>
<? } $loopnum++; } } ?>
    </ul>
    
    <div class="addMenu1" -style="display: none">
        <ul class="menu1">
       
<? $loopnum = 0; ?>
        
<? if(is_array($listallsite)) { foreach($listallsite as $skey => $svar) { if($loopnum > 10) { ?>
<li data-id="<?=$svar['SiteID']?>"><a href="list.php?s=<?=$svar['SiteID']?>&t=<?=$in['t']?>" onclick="$.cookie('category_sub_expend','<?=$svar['SiteID']?>');" 
<? if(!empty($in['s']) && $in['s']==$svar['SiteID']) { ?>
class="current-category"
<? } ?>
><span class="f-l"><?=$svar['SiteName']?></span> </a></li>
<? } $loopnum++; } } ?>
        </ul>
        <a class="pickUp"> <span class=" icon icon3">&#x36;</span> </a>
    </div>
    
    
<? if(is_array($listallsite)) { foreach($listallsite as $skey => $svar) { ?>
<div style="clear: both"></div>

    <div class="menuAdd" id="ShowSubMenu_<?=$svar['SiteID']?>" style="display: none">
<? if(empty($svar['son'])) { ?>
<div>
<span class="menu-stock"></span>
<span style="color:#000;padding-left:20px"><a href="list.php?s=<?=$svar['SiteID']?>&t=<?=$in['t']?>"><?=$svar['SiteName']?></a></span>
<div class="menu-border"></div>
<ul style="margin-left: 0px;padding-left: 0px;">
<li><a href="javascript:;">&nbsp;</a></li>
</ul>
</div>
<? } if(is_array($svar['son'])) { foreach($svar['son'] as $key => $vr) { ?>
<div>
<div style="clear: both;"></div>
<div>
                        <span class="menu-stock"></span>
                        <span style="color:#000;padding-left:20px"><a href="list.php?s=<?=$vr['SiteID']?>&t=<?=$in['t']?>"><?=$vr['SiteName']?></a></span></div>
                        <div class="menu-border"></div>

                        <ul style="">

</ul>
</div>
<? if(empty($vr['son'])) { ?>
<div>
<ul style="margin-left: 0px;padding-left: 0px;">
                        	<li><a href="javascript:;">&nbsp;</a></li></ul></div>
<? } else { if(is_array($vr['son'])) { foreach($vr['son'] as $k => $v) { ?>
<div>
<ul style="margin-left: 0px;padding-left: 0px;">
                     <li><a href="list.php?s=<?=$v['SiteID']?>&t=<?=$in['t']?>" style="float: left;"><?=$v['SiteName']?></a></li></ul>
</div>
<? } } } ?>
                        </ul>
<? } } ?>
    </div>
    
<? } } ?>
  
    <a class="type-more"> <span class=" icon icon3"><img src="template/red/images/xia111.png" style="margin-top: 6px" alt=""/></span> </a>

<script>

$(".menu1>li").hover(function(){
var sid = $(this).attr('data-id');

//$('.menu2').each(function(){$(this).css("display","none");});
$("#ShowSubMenu_" + sid).css("display","block");
},
function () {
var sid = $(this).attr('data-id');
//$("#ShowSubMenu_" + sid).css("display","none");
   $(".menuAdd").css("display","none");
});

$(".menuAdd").hover(function(){
        $(this).css("display","block");
    },
    function () {
        $(this).css("display","none");
    });
$(".type-more").click(function(){
    $(".addMenu1").css("display","block")
});
$(".pickUp").click(function(){
    $(".addMenu1").css("display","none")
});
</script>
    