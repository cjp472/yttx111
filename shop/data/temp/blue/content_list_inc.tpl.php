
<? if($in['t'] == "imglist") { ?>
            	<ul>
<? if(is_array($goodslist['list'])) { foreach($goodslist['list'] as $gkey => $gvar) { ?>
            		<li id="linegoods_<?=$gvar['ID']?>">
                    	<div class="list_img">
<? if(!empty($gvar['Picture'])) { ?>
<a href="<?=RESOURCE_PATH?><? echo str_replace('thumb_','img_',$gvar['Picture']); ?>" class="jqzoom"  >
<img src="<?=RESOURCE_PATH?><?=$gvar['Picture']?>" title="<?=$gvar['Name']?>" border="0" />
</a>
<? } else { ?>
<img src="<?=CONF_PATH_IMG?>images/default.jpg" title="<?=$gvar['Name']?>" border="0" />
<? } ?>
</div>
                        <div class="list_content">
                        	<dt><a href="content.php?id=<?=$gvar['ID']?>" title="<?=$gvar['Name']?>" target="_blank" ><?=$gvar['Name']?></a>&nbsp;&nbsp;<span class="test_1"><?=$producttypearr[$gvar['CommendID']]?></span></dt>
                            
<? if(!empty($gvar['Coding'])) { ?>
<dd><strong>编号：</strong><?=$gvar['Coding']?></dd>
<? } if(!empty($gvar['Casing'])) { ?>
<dd><strong>包装：</strong><?=$gvar['Casing']?></dd>
<? } if(!empty($gvar['ShowField'])) { if(is_array($gvar['ShowField'])) { foreach($gvar['ShowField'] as $skey => $svar) { if(!empty($svar['value'])) { ?>
<dd><strong><?=$svar['name']?>：</strong><?=$svar['value']?></dd>
<? } } } } else { if(!empty($gvar['Barcode'])) { ?>
<dd><strong>条码：</strong><?=$gvar['Barcode']?></dd>
<? } ?>
                            
<? if(!empty($gvar['Model'])) { ?>
<dd><strong>型号：</strong><?=$gvar['Model']?></dd>
<? } } ?>
                        </div>
                        
                        <div class="list_button">                        	
<dd><span class="test_2">¥ <?=$gvar['Price']?></span><span class="gray">&nbsp;&nbsp;元/<?=$gvar['Units']?>&nbsp;&nbsp;</span></dd>
                            
<? if($setarr['product_price']['price1_show'] == 'on') { ?>
                            <dd><string><?=$setarr['product_price']['price1_name']?> : </string><span class="test_1">¥ <?=$gvar['Price1']?></span><span class="gray">&nbsp;&nbsp;元/<?=$gvar['Units']?>&nbsp;&nbsp;</span></dd>
                            
<? } ?>
                            
<? if($setarr['product_price']['price2_show'] == 'on') { ?>
                            <dd><string><?=$setarr['product_price']['price2_name']?> : </string><span class="test_1">¥ <?=$gvar['Price2']?></span><span class="gray">&nbsp;&nbsp;元/<?=$gvar['Units']?>&nbsp;&nbsp;</span></dd>
                            
<? } if($pns=="on") { if(empty($goodslist['number'][$gvar['ID']])) { ?>
<dd >&nbsp;&nbsp;库存：&nbsp;&nbsp;0&nbsp;<?=$gvar['Units']?></dd>
<? } else { ?>
<dd>&nbsp;&nbsp;库存：&nbsp;&nbsp;<?=$goodslist['number'][$gvar['ID']]?>&nbsp;<?=$gvar['Units']?></dd>
<? } } ?>
                        <dd>
<? if($gvar['CommendID']=="9") { ?>
<a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');"><img src="<?=CONF_PATH_IMG?>images/notic_a.jpg" border="0" class="img" /></a>
<? } else { if($pn=="on" && $png=="off" && $goodslist['number'][$gvar['ID']] <= 0) { ?>
<a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');"><img src="<?=CONF_PATH_IMG?>images/notic_a.jpg" border="0" class="img" /></a>
<? } else { ?>
<a href="javascript:void(0);" onclick="addtocart('<?=$gvar['ID']?>','<?=$gvar['cs']?>');" id="shareit_<?=$gvar['ID']?>"><img src="<?=CONF_PATH_IMG?>images/mypay.jpg" border="0" class="img" /></a>
<? } } ?>
                                
<? if($iswish) { ?>
                                <a onclick="javascript:removewishlist('<?=$gvar['ID']?>');" href="javascript:void(0);" title="从我的收藏中移除掉。">&#8250; 移除</a>
                                
<? } else { ?>
                                <a onclick="javascript:addtowishlist('<?=$gvar['ID']?>');" href="javascript:void(0);" title="将常订的商品加入我的收藏夹，方便日后订购。">&#8250; 收藏</a>
                                
<? } ?>
</dd>
<dd style="display:none;">
<? if($iswish) { ?>
<a onclick="javascript:removewishlist('<?=$gvar['ID']?>');" href="javascript:void(0);" title="从我的收藏中移除掉。">&#8250; 移除</a>
<? } else { ?>
<a onclick="javascript:addtowishlist('<?=$gvar['ID']?>');" href="javascript:void(0);" title="将常订的商品加入我的收藏夹，方便日后订购。">&#8250; 添加到收藏夹</a>
<? } ?>
</dd>	
                        </div>
                    </li>
                 
<? } } ?>
                </ul>
                
<? } else { ?>
                	<br class="clear" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
                    <thead>
  <tr>
    <td width="22%" height="28">&nbsp;编号/货号</td>
    <td>&nbsp;名称</td>
    <td width="16%" align="right">&nbsp;价格&nbsp;(元)</td>
      
<? if($setarr['product_price']['price1_show'] == 'on') { ?>
      <td width="14%" align="right">&nbsp;<?=$setarr['product_price']['price1_name']?>&nbsp;(元)</td>
      
<? } ?>
      
<? if($setarr['product_price']['price2_show'] == 'on') { ?>
      <td width="14%" align="right">&nbsp;<?=$setarr['product_price']['price2_name']?>&nbsp;(元)</td>
      
<? } ?>
    <td width="14%" align="center">&nbsp;
<? if($iswish) { ?>
移除
<? } else { ?>
包装
<? } ?>
</td>
    <td width="10%">&nbsp;订购</td>
  </tr>
   </thead>
   <tbody>
<? if(is_array($goodslist['list'])) { foreach($goodslist['list'] as $gkey => $gvar) { ?>
  <tr onmouseover="inStyle(this);control('altimg_<?=$gvar['ID']?>', 'show');"  onmouseout="outStyle(this);control('altimg_<?=$gvar['ID']?>', 'hide');" id="linegoods_<?=$gvar['ID']?>" >
    <td height="42" ><div id="altimg_<?=$gvar['ID']?>" class="altimg">
<? if(!empty($gvar['Picture'])) { ?>
<img src="<?=RESOURCE_PATH?><?=$gvar['Picture']?>" title="<?=$gvar['Name']?>" border="0" />
<? } ?>
</div>&nbsp;<?=$gvar['Coding']?></td>
    <td  ><a href="content.php?id=<?=$gvar['ID']?>" target="_blank" ><?=$gvar['Name']?></a></td>
    <td align="right"><span class="test_2">¥ <?=$gvar['Price']?></span><span class="gray">&nbsp;/<?=$gvar['Units']?></span></td>
      
<? if($setarr['product_price']['price1_show'] == 'on') { ?>
      <td align="right"><span class="test_1">¥ <?=$gvar['Price1']?></span><span class="gray">&nbsp;/<?=$gvar['Units']?></span></td>
      
<? } ?>
      
<? if($setarr['product_price']['price2_show'] == 'on') { ?>
      <td align="right"><span class="test_1">¥ <?=$gvar['Price2']?></span><span class="gray">&nbsp;/<?=$gvar['Units']?></span></td>
      
<? } ?>
    <td align="center">&nbsp;<label>
<? if($iswish) { ?>
<a onclick="javascript:removewishlist('<?=$gvar['ID']?>');" href="javascript:void(0);" title="从我的收藏中移除掉。">&#8250; 移除</a>
<? } else { ?>
<?=$gvar['Casing']?>
<? } ?>
</label></td>
    <td 
<? if($pns=="on") { if(empty($goodslist['number'][$gvar['ID']])) { ?>
title="库存:&nbsp;0 <?=$gvar['Units']?>"
<? } else { ?>
title="库存:&nbsp;<?=$goodslist['number'][$gvar['ID']]?> <?=$gvar['Units']?>"
<? } } ?>
 >
<? if($gvar['CommendID']=="9") { ?>
<a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');"><img src="<?=CONF_PATH_IMG?>images/notic.jpg" border="0" class="img" /></a>
<? } else { if($pn=="on" && $png=="off" && $goodslist['number'][$gvar['ID']] <= 0) { ?>
<a href="javascript:void(0);" onclick="noticegoods('<?=$gvar['ID']?>');"><img src="<?=CONF_PATH_IMG?>images/notic.jpg" border="0" class="img" title="缺货" /></a>
<? } else { ?>
<a href="javascript:void(0);" onclick="addtocart('<?=$gvar['ID']?>','<?=$gvar['cs']?>');" id="shareit_<?=$gvar['ID']?>"><img src="<?=CONF_PATH_IMG?>images/buy.jpg" border="0" class="img" /></a>
<? } } ?>
</td>
  </tr>
   
<? } } ?>
 
   </tbody>
</table>
<? } ?>
