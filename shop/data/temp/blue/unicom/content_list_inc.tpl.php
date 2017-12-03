
<? if($in['t'] == "imglist") { ?>
    <ul>
<? if(is_array($goodslist['list'])) { foreach($goodslist['list'] as $gkey => $gvar) { ?>
  		<li id="linegoods_<?=$gvar['ID']?>">
  		
  
<? if(!empty($gvar['Picture'])) { ?>
<div class="wm-zoom-container my-zoom-1 goods-img f-l">
  				<div class="wm-zoom-box">
<img src="<?=RESOURCE_PATH?><?=$gvar['Picture']?>" class="wm-zoom-default-img" alt="<?=$gvar['Name']?>" data-hight-src="<?=RESOURCE_PATH?><? echo str_replace('thumb_','img_',$gvar['Picture']); ?>" data-loader-src="template/img/loader.gif">
  				</div>
  			</div>
  
<? } else { ?>
  			<div class="goods-img f-l">
<img src="<?=CONF_PATH_IMG?>images/default.jpg" title="<?=$gvar['Name']?>" width="140" />
</div>
<? } ?>
            <div class="list_content f-l">
              <dl>
               	<dd class="ddclear">
                	<a href="content.php?id=<?=$gvar['ID']?>" title="<?=$gvar['Name']?>" target="_blank" class=" f-l">
                	<p class="color-b"><b><?=$gvar['Name']?></b>
                
<? if(!empty($producttypearr[$gvar['CommendID']])) { ?>
                		<lable style="color:#F39911;display:inline-block">&nbsp;&nbsp;<?=$producttypearr[$gvar['CommendID']]?></lable>
                		
<? } ?>
                	</p>
                	</a>
               	</dd>
<dd class="ddclear" style="line-height:22px;heihgt:22px;">
<span class="font17" style="font-weight:normal;">品牌：<a href="list.php?b=<?=$gvar['BrandID']?>&t=imglist" target="_blank" style="font-size:12px;"><?=$gvar['BrandName']?></a></span></dd>
<? if(!empty($gvar['Coding'])) { ?>
<!--<dd class="ddclear" style="line-height:22px;heihgt:22px;"><span class="font12">编号：<?=$gvar['Coding']?></span></dd>-->
<? } if(!empty($gvar['ShowField'])) { if(is_array($gvar['ShowField'])) { foreach($gvar['ShowField'] as $skey => $svar) { if(!empty($svar['value'])) { ?>
<dd class="ddclear" style="line-height:22px;heihgt:22px;">
<span class="font12"><?=$svar['name']?>：<?=$svar['value']?></span>
</dd>
<? } } } } else { if(!empty($gvar['Barcode'])) { ?>
<dd class="ddclear font17" style="line-height:22px;heihgt:22px;font-weight:normal;">
<span class="" style="display:inline;font-weight:normal;">条码：<?=$gvar['Barcode']?></span>
</dd>
<? } } ?>
   <dd class="ddclear font12" style="line-height:22px;heihgt:22px;font-weight:normal;">
   	<span class="" style="display:inline;font-weight:normal;">规格：<?=$gvar['Model']?></span>
   </dd>
<? if(!empty($gvar['Nearvalid'])) { ?>
<dd class="ddclear font12" style="line-height:22px;heihgt:22px;font-weight:normal;">
<span class="font12" style="display:inline;font-weight:normal;">效期：</span>
<? $gvar['Nearvalid']=str_replace("-",".",$gvar['Nearvalid']); ?>
<?=$gvar['Nearvalid']?> -
<? $gvar['Farvalid']=str_replace("-",".",$gvar['Farvalid']); ?>
<?=$gvar['Farvalid']?>
</dd>
<? } if(!empty($gvar['Conversion']) && !empty($gvar['Casing']) ) { ?>
<dd class="ddclear font12" style="line-height:22px;heihgt:22px;font-weight:normal;">
<span class="font12" style="display:inline;font-weight:normal;">包装：
<?=$gvar['Casing']?> <?=$gvar['Units']?>&nbsp;<!-- (1<?=$gvar['Casing']?>=<?=$gvar['Conversion']?> <?=$gvar['Units']?>) -->
</span>
</dd>
<? } ?>
</dl>
            </div>
            
            <div class="list_content1 f-l">
            <dd class="font12" style="font-weight:normal;">&nbsp;</dd>
            <dd class="font12" style="font-weight:normal;">订货价：
<span class="color-r f-b">¥ <?=$gvar['Price']?></span>
<span class="gray">&nbsp;元/<?=$gvar['Units']?></span>
</dd>
              
<? if($setarr['product_price']['price1_show'] == 'on') { ?>
                <dd class="font12" style="font-weight:normal;"> 
                	<span class="m-b"><?=$setarr['product_price']['price1_name']?>：¥ <?=$gvar['Price1']?>
                		<span class="gray">&nbsp;元/<?=$gvar['Units']?></span>
                	</span>
                </dd>
              
<? } ?>
          
<? if($setarr['product_price']['price2_show'] == 'on') { ?>
           <dd class="font12" style="font-weight:normal;">
           	<span class="m-b"><?=$setarr['product_price']['price2_name']?>：¥ <?=$gvar['Price2']?>
           		<span class="gray">&nbsp;元/<?=$gvar['Units']?></span>
           	</span>
           </dd>
          
<? } if($gvar['Price2'] > 0 ) { ?>
<dd style="line-height:22px;heihgt:22px;font-weight:normal;" class="font12" >
<span style="display:inline;font-weight:normal;">毛利率：</span>
<?=$gvar['maoli']?>%
<? } ?>
</dl>
            </div>
            
            
            <div class="list_button f-r m-r">
            
            	<dd class="font12" style="font-weight:normal;">&nbsp;</dd>  
              <dl>
<? if($pns=="on") { if(empty($goodslist['number'][$gvar['ID']])) { ?>
<dd ><span class="font12" style="font-weight:normal;">库存：&nbsp;0&nbsp;<?=$gvar['Units']?> </span></dd>
<? } else { ?>
<dd><span class="font12" style="font-weight:normal;">库存：&nbsp;<?=$goodslist['number'][$gvar['ID']]?>&nbsp;<?=$gvar['Units']?> </span></dd>
<? } } ?>
<dd>
<div class="item-amount m-t-5">
<a href="javascript:void(0);" onclick="changenum('<?=$gvar['ID']?>','sub');" id="subnum_<?=$gvar['ID']?>" class=" xiala f-l amount-j J_Minus">-</a>
<input name="shareit-field-<?=$gvar['ID']?>" id="shareit-field-<?=$gvar['ID']?>" onfocus="this.select();" type="text" value="1" class="xiala f-l text-amount" data-max="700" data-now="1" autocomplete="off" />
<a href="javascript:void(0);" onclick="changenum('<?=$gvar['ID']?>','add');" id="addnum_<?=$gvar['ID']?>" class=" xiala f-l amount-j J_Plus">+</a>
</div>
</dd>
              	
<dd>
<? if($gvar['CommendID']=="9") { ?>
<a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');">
<span class="btn-1" style="background-color:#dbdbdb;padding-left:0px;padding-right:0px;margin-top:10px;width:60px;">到货通知</span>
</a>
<? } else { if($pn=="on" && $png=="off" && $goodslist['number'][$gvar['ID']] <= 0) { ?>
<a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');">
<span class="btn-1" style="background-color:#dbdbdb;padding-left:0px;padding-right:0px;margin-top:10px;width:60px;">到货通知</span>
</a>
<? } else { ?>
<a href="javascript:void(0);" onclick="savegoodtocart(<?=$gvar['ID']?>);" id="shareit_<?=$gvar['ID']?>" class="btn-1 f-l m-t"> <span class="icon ">&#xe07a;</span> 订购</a>
<? if($gvar['cs'] == 'Y') { ?>
<a href="javascript:void(0);" onclick="addtocart('<?=$gvar['ID']?>','<?=$gvar['cs']?>');" id="shareit_<?=$gvar['ID']?>" class="btn-2 f-l m-t  m-l-5"> <span class="icon ">&#xe07a;</span> 批量订购</a> 
<? } } } ?>
<br style=" display:block" />
</dd>

                  	<dd style="clear:both;" id="wish_<?=$gvar['ID']?>">
                  		<a style="margin-top:0px;" onclick="
<? if($gvar['fav']) { ?>
javascript:removewishlist('<?=$gvar['ID']?>',1);
<? } else { ?>
javascript:addtowishlist('<?=$gvar['ID']?>');
<? } ?>
" href="javascript:void(0);" class=" f-l font12">
                  		<span class="icon" id="wish_icon_<?=$gvar['ID']?>" style="
<? if($gvar['fav']) { ?>
color:red;
<? } ?>
">&#xe031;</span> 收藏
                  		</a>
                  	</dd>
</dl>
             </div>
          </li>
     
<? } } ?>
     </ul>               
<? } else { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="ordertd" style="margin:0 auto">
  <tr>
    <th width="35%">&nbsp;&nbsp;名称</th>
    <th>价格</th>
    <th width="17%">&nbsp;</th>
    <th width="17%">数量</th>
    <th width="80px;">订购</th>
  </tr>
  
<? if(is_array($goodslist['list'])) { foreach($goodslist['list'] as $gkey => $gvar) { ?>
  <tr onmouseover="inStyle(this);control('altimg_<?=$gvar['ID']?>', 'show');"  onmouseout="outStyle(this);control('altimg_<?=$gvar['ID']?>', 'hide');" id="linegoods_<?=$gvar['ID']?>">
    <td><dl>
        <dd>
          <a href="content.php?id=<?=$gvar['ID']?>" title="<?=$gvar['Name']?>" target="_blank" class=" f-l">
        	 <div id="altimg_<?=$gvar['ID']?>" class="altimg">
<? if(!empty($gvar['Picture'])) { ?>
<img src="<?=RESOURCE_PATH?><?=$gvar['Picture']?>" title="<?=$gvar['Name']?>" border="0" />
<? } ?>
</div>
         	 <p class="color-b" style="padding-left:7px"><?=$gvar['Name']?></p>
          </a>
          
<? if(!empty($producttypearr[$gvar['CommendID']]) ) { ?>
          	&nbsp;&nbsp;<span class="test_1 color-r" style="color:#F39911"><?=$producttypearr[$gvar['CommendID']]?></span>
          	  
<? } ?>
         </dd>
         <dd style="clear:both;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></dd>
        <dd style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:240px;">&nbsp;&nbsp;<span class="font12">品牌：<?=$gvar['BrandName']?></span></dd>
        <!--<dd>&nbsp;&nbsp;<span class="font12">编号：<?=$gvar['Coding']?></span></dd>-->
        <dd>&nbsp;&nbsp;<span class="font12">规格：<?=$gvar['Model']?></span></dd>
<? if(!empty($gvar['Nearvalid'])) { ?>
<dd class="ddclear" style="line-height:22px;heihgt:22px;"><span class="font12" style="display:inline;">效期：</span>
<? $gvar['Nearvalid']=implode(".",explode("-",$gvar['Nearvalid'])); ?>
<?=$gvar['Nearvalid']?> -
<? $gvar['Farvalid']=implode(".",explode("-",$gvar['Farvalid'])); ?>
<?=$gvar['Farvalid']?></dd>
<? } if(!empty($gvar['Farvalid'])) { ?>
<!--<dd class="ddclear" style="line-height:22px;heihgt:22px;"><span class="font12" style="display:inline;">远效期：</span></dd>-->
<? } if(!empty($gvar['Conversion']) && !empty($gvar['Casing']) ) { ?>
<dd class="ddclear" style="line-height:22px;heihgt:22px;"><span class="font12" style="display:inline;">包装：</span>
1<?=$gvar['Casing']?>=<?=$gvar['Conversion']?> <?=$gvar['Units']?></dd>
<? } if(!empty($gvar['Appnumber'])) { ?>
<!--<dd class="ddclear" style="line-height:22px;heihgt:22px;"><span class="font12" style="display:inline;">批准文号：</span><?=$gvar['Appnumber']?></dd>-->
<? } ?>
      </dl></td>
    <td><dl>
        <dd>
        
<? if($setarr['product_price']['price1_show'] == 'on') { ?>
    	<span class="m-b font12"><?=$setarr['product_price']['price1_name']?>：<span>¥ <?=$gvar['Price1']?></span>&nbsp;&nbsp;/<?=$gvar['Units']?></span>
    
<? } ?>
    
<? if($setarr['product_price']['price2_show'] == 'on') { ?>
    	<span class="m-b font12"><?=$setarr['product_price']['price2_name']?>：<span>¥ <?=$gvar['Price2']?></span>&nbsp;&nbsp;/<?=$gvar['Units']?></span>
    
<? } ?>
        </dd>
        <dd><span class="m-b font12">订货价：<span class="color-r f-b ">¥ <?=$gvar['Price']?></span>&nbsp;&nbsp;/<?=$gvar['Units']?></span></dd>
        
<? if($gvar['Price2'] > 0 ) { ?>
<dd style="line-height:22px;heihgt:22px;"><span class="font12" style="display:inline;">毛利率：</span>
<? $maoli=round(($gvar['Price2']-$gvar['Price1'])/$gvar['Price1'],2)*100; ?>
<?=$maoli?>%
<? } ?>
<dd>
<? if($pns=="on") { if(empty($goodslist['number'][$gvar['ID']])) { ?>
<dd ><span class="font12">库存：&nbsp;&nbsp;0&nbsp;<?=$gvar['Units']?> </span></dd>
<? } else { ?>
<dd><span class="font12">库存：&nbsp;&nbsp;<?=$goodslist['number'][$gvar['ID']]?>&nbsp;<?=$gvar['Units']?> </span></dd>
<? } } ?>
  
  </dl></td>
    <td><dl class="hide">
        <dd style="margin-top:10px;">
          <select id="spec-<?=$gvar['ID']?>" name="spec-<?=$gvar['ID']?>" class="xiala w-sm">
            <optgroup label="- 规格 -">
            
<? if(!empty($gvar['Specification']) ) { ?>
                
<? $gvar['cur_spec'] = explode(',',$gvar['Specification']); ?>
                
<? if(is_array($gvar['cur_spec'])) { foreach($gvar['cur_spec'] as $skey => $svar) { ?>
                			<option value="<?=$svar?>"><?=$svar?></option>
                
<? } } ?>
                
<? } else { ?>
                		<option value="">默认规格</option>
                	
<? } ?>
            </optgroup>
          </select>
        </dd>

        <dd style="visibility:hidden;">
          <select id="color-<?=$gvar['ID']?>" name="color-<?=$gvar['ID']?>" class="xiala w-sm m-t-5">
            <optgroup label="- 颜色 -">
            
<? if(!empty($gvar['Color']) ) { ?>
                
<? $gvar['cur_color'] = explode(',',$gvar['Color']); ?>
                
<? if(is_array($gvar['cur_color'])) { foreach($gvar['cur_color'] as $skey => $svar) { ?>
                			<option value="<?=$svar?>"><?=$svar?></option>
                
<? } } ?>
                
<? } else { ?>
                		<option value="">默认颜色</option>
                	
<? } ?>
            </optgroup>
          </select>
        </dd>
      </dl></td>
    <td valign="top"><div class="item-amount" style="margin-top: 10px;"> <a href="javascript:void(0);" onclick="changenum('<?=$gvar['ID']?>','sub');" id="subnum_<?=$gvar['ID']?>" class=" xiala f-l amount-j J_Minus">-</a>
        <input name="shareit-field-<?=$gvar['ID']?>" id="shareit-field-<?=$gvar['ID']?>" onfocus="this.select();" type="text" value="1" class="xiala f-l text-amount" data-max="700" data-now="1" autocomplete="off">
        <a href="javascript:void(0);" onclick="changenum('<?=$gvar['ID']?>','add');" id="addnum_<?=$gvar['ID']?>" class=" xiala f-l amount-j J_Plus">+</a>
        </div>
</td>
    


<td valign="top" style="padding-top:5px;">
    
<? if($gvar['CommendID']=="9") { ?>
<dd><a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');">
<span class="btn-1" style="background-color:#dbdbdb;padding-left:0px;padding-right:0px;margin-top:5px;width:60px;">到货通知</span>
</a></dd>
<? } else { if($pn=="on" && $png=="off" && $goodslist['number'][$gvar['ID']] <= 0) { ?>
<dd><a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');">
<span class="btn-1" style="background-color:#dbdbdb;padding-left:0px;padding-right:0px;margin-top:5px;width:60px;">到货通知</span>
</a></dd>
<? } else { ?>
<dd><a href="javascript:void(0);" onclick="savegoodtocart(<?=$gvar['ID']?>);" id="shareit_<?=$gvar['ID']?>" class="btn-1 f-l m-t-5 "> <span class="icon ">&#xe07a;</span> 订购</a> <br style=" clear:both">
        </dd>
        
<? if($gvar['cs'] == 'Y') { ?>
        	<dd><a class="btn-2 m-t-5 f-l" href="javascript:void(0);" onclick="addtocart('<?=$gvar['ID']?>','<?=$gvar['cs']?>');" id="shareit_<?=$gvar['ID']?>"> <span class="icon ">&#xe07a;</span> 批量订购</a></dd>
      	
<? } } } ?>
   
      </dl></td>
  </tr>
  
<? } } ?>
</table>        	
<? } ?>
<script>
$(function(){
$(".text-amount").numeral(false);
});
</script>