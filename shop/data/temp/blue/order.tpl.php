<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link type="text/css" href="plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/cart.js?v=<?=VERID?>" type="text/javascript"></script>

<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript" src="plugin/layer/layer.js"></script>


<link rel="stylesheet" type="text/css" href="template/css/icon.css?v=<?=VERID?>"/>
<link rel="stylesheet" type="text/css" href="template/css/pop.css?v=<?=VERID?>"/>
<style type="text/css">
.fix-type-area ul li{
float: left;
height:31px;
margin-bottom:10px;
}
.fix-type-area label{
cursor:pointer;
}
.fix-type-area div{
border: 2px solid #dbdbdb;
    margin-right: 10px;
    padding: 3px 10px;
    width: auto;
}
.fix-type-area div.current{
border: 2px solid #239A56;
background: transparent url("./template/img/greenlitter.jpg") no-repeat scroll right bottom;
}
.qmark-icon {
 	background: transparent url("./template/img/qmark02.png") no-repeat scroll 0 0;
    height: 13px;
    padding: 0 7px;
    width: 13px;
    visibility:;
    margin-top: 5px;
    display:none;
    -webkit-float:right;
  
}
.position-msg-box{
display:none;
line-height:30px;
padding: 0 15px;
border:1px solid #92c3dd;
background-color:#d7effc;
width:auto;
position:absolute;
top:0;
left:0;
font-size:12px;
box-shadow: 0 0 2px 2px #eee;
}
.unfloat{
line-height:31px !important;
clear:both;
float:none !important;
}
.unfloat-hover{
background-color:#fff3f3;
}
.show-edit{
cursor:pointer;
display:none;
color:#136db3;
}
#edit-mycompany-address{
display:none;
}
.hide-send-more{
overflow:hidden;
}
.my-more-address{
padding:2px 5px;
color:#136db3;
margin:0;
cursor:pointer;
width:auto;
display:none;
margin-left: 10px;
}
.my-more-address b{
display: inline-block;
    height: 10px;
    line-height: 10px;
    margin-left: 5px;
    vertical-align: middle;
    width: 9px;
    background: transparent url("./template/img/addr-i.png") no-repeat scroll right top;
}
.my-more-address b.top{

}
.my-more-address b.bottom{
background: transparent url("./template/img/addr-i.png") no-repeat scroll right bottom;
}

.line96>ul>li>input{
height: 25px;
padding-left: 10px;
}
</style>
</head>

<div>
<div class="position-msg-box" id="position-msg-box"></div>
<? include template('header'); include template('popApply'); ?>
<div id="main">
<div id="location"><strong>您的当前位置： </strong><a href="home.php">首页</a> &#8250;&#8250; <a href="cart.php">我的购物车 </a>&#8250;&#8250; <a href="#">提交订单</a></div>

<div class="car_tit" style="width: 980px;"><span class="iconfont icon-gouwuche" style="margin-left: 10px;color: #ffb236"></span>   填写核对订单信息</div>

<div class="main_car">
<? if(empty($cartproduct)) { ?>
<div class="line font12" ><p align="center">您的还没有预订任何商品！<a href="list.php">返回订购</a></p></div>
<? } else { ?>
<form id="formorder" name="formorder" method="post" action="">
<div class="line96">
<br class="clear" />
<div class="font14">收货人信息</div>			
</div>

<div class="line96 fix-type-area">
<? if(!empty($addressdata)) { ?>
<ul id="my-address">
<? if(is_array($addressdata)) { foreach($addressdata as $akey => $avar) { ?>
<li class="unfloat">
<div class="
<? if($avar['AddressFlag']=="1") { ?>
current
<? } ?>
" style="float:left;">
<label data-tip="">
<input name="sendaddress" type="radio" id="sendaddress_<?=$avar['AddressID']?>" value="<?=$avar['AddressID']?>" onclick="setinputval('<?=$avar['AddressCompany']?>','<?=$avar['AddressContact']?>','<?=$avar['AddressPhone']?>','<?=$avar['AddressAddress']?>');" 
<? if($avar['AddressFlag']=="1") { ?>
 checked="checked" 
<? } ?>
 />
      					<span class="bold"><?=$avar['AddressContact']?></span><span>&nbsp;&nbsp;<?=$avar['AddressCompany']?></span>
      				</label>
      			</div>
      			<span style="line-height:180%;">
      			<?=$avar['AddressContact']?>&nbsp;&nbsp;
      			<?=$avar['AddressCompany']?>&nbsp;&nbsp;
      			<?=$avar['AddressAddress']?>&nbsp;&nbsp;
      			<?=$avar['AddressPhone']?>&nbsp;&nbsp;
      			<span class="show-edit" onclick="setinputval('<?=$avar['AddressCompany']?>','<?=$avar['AddressContact']?>','<?=$avar['AddressPhone']?>','<?=$avar['AddressAddress']?>');" >编辑</span>
      			&nbsp;&nbsp;<span class="show-edit add-new">新增</span>
      			</span>
      		</li>
<? } } ?>
</ul>
<p id="my-more-address" class="my-more-address"><span>更多收货地址</span><b></b></p>
<? } ?>
</div>

<div class="line96" id="edit-mycompany-address" 
<? if(empty($addressdata)) { ?>
style="display:block;"
<? } ?>
>				
<ul class="labelwidth">
 <li><label>收货人/公司：</label><input name="AddressCompany" style="border: 1px solid #ABADB3" id="AddressCompany" type="text" class="input1"  value="<?=$defaultaddress['AddressCompany']?>" /></li>
 <li><label>联 系 人：</label><input name="AddressContact" style="border: 1px solid #ABADB3" id="AddressContact" type="text" class="input1"  value="<?=$defaultaddress['AddressContact']?>" /></li>
 <li><label>联系电话：</label><input name="AddressPhone" style="border: 1px solid #ABADB3" id="AddressPhone" type="text" class="input1" value="<?=$defaultaddress['AddressPhone']?>" /></li>
 <li><label>收货地址：</label><input name="AddressAddress" style="border: 1px solid #ABADB3" id="AddressAddress" type="text" class="input1"  value="<?=$defaultaddress['AddressAddress']?>" /></li>
 <li><label>&nbsp;</label><input name="saveaddinfoadd" id="saveaddinfoadd" value="保存到常用地址" type="button" class="button_4" onclick="addressadd();" />&nbsp;&nbsp;<input name="managerlink" id="managerlink" value="常用收货地址管理" type="button" class="button_5" onclick="javascript:window.location.href='consignment.php?m=address';" /></li>
</ul>
<br class="clear" />				
</div>


<br class="clear" />
<div class="line96">
<div class="font14">请选择配送方式</div>			
</div>
<div class="line96 fix-type-area">
<ul>
<? if(is_array($sendtypedata)) { foreach($sendtypedata as $skey => $svar) { ?>
<li>
<div class="
<? if($svar['TypeFlag']=="1") { ?>
current
<? } ?>
">
<label data-tip="<?=$svar['TypeAbout']?>">
<input name="sendtype" type="radio" id="sendtype_<?=$svar['TypeID']?>" value="<?=$svar['TypeID']?>" 
<? if($svar['TypeFlag']=="1") { ?>
 checked="checked"
<? } ?>
  />&nbsp;<span class="bold"><?=$svar['TypeName']?></span>
&nbsp;<span class="qmark-icon"></span>&nbsp;
</label>
</div>
</li>
<? } } ?>
</ul>
</div>


<br class="clear" />
<br class="clear" />
<div class="line96">
<div class="font14">请选择支付方式</div>			
</div>
<div class="line96 fix-type-area">
<ul>
                    <li>在线支付：&nbsp;&nbsp;</li>
                    <li>
                        <div class="current">
                            <label data-tip="快捷支付">
                                <input name="paytype" type="radio"  id="paytype_9" value="9" checked="checked" />&nbsp;<span class="bold">快捷支付</span>
                                &nbsp;<span class="qmark-icon"></span>&nbsp;
                            </label>
                        </div>
                    </li>
                    
<? if(!empty($alipayInfo)) { ?>
<li>
<div class="">
<label data-tip="支付宝支付">
<input name="paytype" type="radio"  id="paytype_11" value="11" />&nbsp;<span class="bold">支付宝支付</span>
&nbsp;<span class="qmark-icon"></span>&nbsp;
</label>
</div>
</li>
                                        
<? if($CompanyCredit==1) { ?>
                    			<li>
<div class="">
<label data-tip="医统平台的账期金融工具，给予一月免息期，让您资金更灵活">
<input name="paytype" type="radio"  id="paytype_12" value="12" onclick='QuOnck("<?=$svar['TypeName']?>")'/>&nbsp;<span class="bold">医统账期</span>
&nbsp;<span class="qmark-icon"></span>&nbsp;
</label>
</div>
</li>
                                        
<? } } ?>
                   <li class="clear">线下支付：&nbsp;&nbsp;</li>
                      
<? if(is_array($paytypedata)) { foreach($paytypedata as $skey => $svar) { ?>
                   <li>
                       <div class="">
                           <label data-tip="<?=$svar['TypeAbout']?>">
                               <input name="paytype" type="radio"  id="paytype_<?=$svar['TypeID']?>" value="<?=$svar['TypeID']?>" />&nbsp;<span class="bold"><?=$svar['TypeName']?></span>
                               &nbsp;<span class="qmark-icon"></span>&nbsp;
                           </label>
                       </div>
                   </li>
                   
<? } } ?>
                </ul>
</div>
<br class="clear" />
<? if($setarr['invoice_p']=="Y" || $setarr['invoice_z']=="Y") { ?>
<br class="clear" />
<div class="line96">
<div class="font14">请选择开票类型</div>			
</div>
<div class="line96 fix-type-area">
<ul>
<li>
<div class="current">
<label data-tip="">
<input name="invoicetype" type="radio" id="invoicetype_1" value="N" checked="checked" onclick="setinvoicetype('N',0)" />&nbsp;
<span class="bold">不开发票</span><span>&nbsp;&nbsp;</span>
</label>
</div>
</li>
<? if($setarr['invoice_p']=="Y") { ?>
<li>
<div>
<label data-tip="">
<input name="invoicetype" type="radio" id="invoicetype_2" value="P" onclick="setinvoicetype('P',<?=$setarr['invoice_p_tax']?>)" />&nbsp;
<span class="bold">开普通发票</span>
<span>&nbsp;&nbsp;(<font color="red"> 税点：<?=$setarr['invoice_p_tax']?>% </font>)</span>
</label>
</div>
</li>
<? } if($setarr['invoice_z']=="Y") { ?>
<li>
<div>
<label data-tip="">
<input name="invoicetype" type="radio" id="invoicetype_3" value="Z" onclick="setinvoicetype('Z',<?=$setarr['invoice_z_tax']?>)" />&nbsp;
<span class="bold">开增值税发票</span>
<span>&nbsp;&nbsp;(<font color="red"> 税点：<?=$setarr['invoice_z_tax']?>% </font>)</span>
</label>
</div>
</li>
<? } ?>
</ul>
<br class="clear" />
<div id="invoice_content_div" style="display:none;border:none;">
<ul class="labelwidth">
 <li><label>开票抬头：</label><input name="InvoiceHeader" id="InvoiceHeader" type="text" class="input1"  value="<?=$clientdata['InvoiceHeader']?>" /></li>
 <li><label>开票内容：</label><input name="InvocieContent" id="InvocieContent" type="text" class="input1"  value="商品明细" /></li>
 <span id="invoice_content_z_div" style="display:none;">
 <li><label>纳税人识别号：</label><input name="TaxpayerNumber" id="TaxpayerNumber" type="text" class="input1" value="<?=$clientdata['TaxpayerNumber']?>" /></li>
 <li style="margin-top:8px;"><label>开户名称：</label><input name="AccountName" id="AccountName" type="text" class="input1"  value="<?=$clientdata['AccountName']?>" /></li>
 <li><label>开户银行：</label><input name="BankName" id="BankName" type="text" class="input1"  value="<?=$clientdata['BankName']?>" /></li>
 <li><label>银行账号：</label><input name="BankAccount" id="BankAccount" type="text" class="input1"  value="<?=$clientdata['BankAccount']?>" /></li>
 </span>
</ul>
<br class="clear" />
</div>
</div>
<? } ?>
 
 <br class="clear" />
 <input name="delivery_time" id="delivery_time" value="<?=$setarr['delivery_time']?>" type="hidden" />
 
<? if($setarr['delivery_time']=="Y" || $setarr['delivery_time']=="B") { ?>
 <div class="line96" >
 	<div class="leftdiv"><img src="template/img/icon_arrow_down.gif" border="0" class="img" /></div>
 	<div class="leftdiv font14"><label><strong>交货日期：</strong></label><input name="DeliveryDate" id="DeliveryDate" style="width:120px; background:url(/template/img/calendar.gif) no-repeat right 50%; border:#999 solid 1px;" /> 
<? if($setarr['delivery_time']=="B") { ?>
<font color="red">*</font>
<? } ?>
 
</div>
 </div>
 <br class="clear" />
 
<? } ?>
<br class="clear" />
<div class="line96">
<div class="font14">订单结算信息：</div>
</div>

<div class="line96">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <thead>
  <tr>
    <td width="6%" height="28">&nbsp;序号</td>
    <td>&nbsp;商品名称</td>
<!--    <td width="18%">&nbsp;生产厂家</td> -->
<!--     <td width="12%">&nbsp;规格</td> -->
    <td width="18%">&nbsp;品牌</td>
    <td width="12%">&nbsp;规格</td>
    <td width="12%" style="display:none;">&nbsp;颜色 / 规格</td>
    
<? if($pns=="on") { ?>
    <td width="6%" align="right">库存</td>
    
<? } ?>
    <td width="10%" align="right">数量/单位</td>
    <td width="8%" align="right">单价</td>  
<!--     <td width="5%" align="right">折扣</td>
    <td width="10%" align="right">折后价</td>   -->  
    <td width="12%" align="right">小计(元)</td> 
  </tr>
   </thead>
   <tbody>
    
<? if(is_array($carttempproduct)) { foreach($carttempproduct as $cartkey => $cartvar) { ?>
    
<? if($cartvar['No'] <= $pagesize) { ?>
    
<? if($cartvar['library']=="empty") { ?>
        <tr id="linegoods_<?=$cartvar['kid']?>" style="background-color:#FFFF99;"  >
    
<? } else { ?>
        <tr id="linegoods_<?=$cartvar['kid']?>" 
<? if(fmod($cartkey,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    
<? } ?>
    <td height="35">&nbsp;<input name="kid[]" type="hidden" value="<?=$cartvar['kid']?>" /><?=$cartvar['No']?></td>
    <td><a href="content.php?id=<?=$cartvar['id']?>" target="_blank"><?=$cartvar['Name']?></a></td>
    <td ><?=$cartvar['BrandName']?>&nbsp;</td>
    <td >&nbsp;<?=$cartvar['Model']?></td>
    <td style="display:none">
        &nbsp;
<? if(strlen($cartvar['color']) > 0) { ?>
<?=$cartvar['color']?>
<? } ?>
        /
        
<? if(strlen($cartvar['spec']) > 0) { ?>
<?=$cartvar['spec']?>
<? } ?>
    </td>
    
<? if($pns=="on") { ?>
    <td align="right" class="font12h">
<? if($cartvar['onumber']==9999999999) { ?>
充足
<? } else { ?>
<?=$cartvar['onumber']?>&nbsp;
<? } ?>
</td>
    
<? } ?>
    <td align="right"><?=$cartvar['number']?>&nbsp;/&nbsp;<?=$cartvar['Units']?>&nbsp;</td>
<!--     <td align="right">¥ <?=$cartvar['price']?> </td> 
    <td align="right"><?=$cartvar['pencent']?></td> -->
    <td align="right">¥ <?=$cartvar['price_end']?></td>
    <td class="font12" align="right">¥ <?=$cartvar['notetotal']?>&nbsp;</td>
  </tr>
  
<? } ?>
   
<? } } ?>
    
  
<? if(count($carttempproduct) > $pagesize) { ?>
  <tr>
    <td colspan="2" align="left" class="font14">......</td>
    
<? if($pns=="on") { ?>
    <td>&nbsp;</td>
    
<? } ?>
    <td class="font14" align="right" colspan="7" align="right"><a href="cart.php">返回购物车，查看全部明细 &#8250;&#8250;</a>&nbsp;</td>
  </tr>
    
<? } ?>
    
<? if($stair_count > 0) { ?>
    <tr>
        <td style="text-align:right;padding-right:10px;" colspan="
<? if($pns == 'on') { ?>
10
<? } else { ?>
9
<? } ?>
">
            订单满 <span style="color:red;font-size:16px;font-weight:bold;">¥ <?=$stair_amount?></span> 省 <span style="color:red;font-size:16px;font-weight:bold;">¥ <?=$stair_count?></span>
        </td>
    </tr>
    
<? } ?>
  <tr>
    <td height="28" class="font14" colspan="2">&nbsp;合计：</td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    
<? if($pns=="on") { ?>
    <td>&nbsp;</td>
    
<? } ?>
    <td class="font14" align="right"><?=$productnum?>&nbsp;&nbsp;&nbsp;&nbsp; </td>
<!--     <td >&nbsp;</td>
    <td >&nbsp;</td> -->
    <td class="font14" align="right" colspan="2">
        ¥ <? echo $producttotal - $stair_count; ?>        <input name="product_total" id="product_total" value="<? echo $producttotal - $stair_count; ?>" type="hidden" />
    </td>
  </tr>
  <tr id="total_tax_line" style="display:none;">
    <td height="28" class="font14" colspan="2">&nbsp;税点：</td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    
<? if($pns=="on") { ?>
    <td>&nbsp;</td>
    
<? } ?>
    <td class="font12" align="right" colspan="4" id="show_tax_program">&nbsp;&nbsp;&nbsp;&nbsp; </td>
    <td class="font14" align="right"  id="show_tax_total">¥ 0&nbsp;</td>
  </tr>


  <tr id="total_all_line" style="display:none;">
    <td height="28" class="font14" colspan="2">&nbsp;总计：</td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    
<? if($pns=="on") { ?>
    <td>&nbsp;</td>
    
<? } ?>
    <td class="font14" align="right">&nbsp;&nbsp;&nbsp;&nbsp; </td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td class="font14h" align="right" colspan="2" id="show_all_total">
        
<? if($stair_count > 0) { ?>
        ¥ <?=$producttotal?> 减 ¥ <?=$stair_count?> 等于
        ¥ <? echo $producttotal - $stair_count; ?>&nbsp;
        
<? } else { ?>
        ¥ <?=$producttotal?>&nbsp;
        
<? } ?>
        <?=$stair_count?>
    </td>
  </tr>
   </tbody>
</table>
 </div>

 <div class="line96" >

 <div class="line96" style="margin:8px;"><label><strong>如果你有特殊要求请在这说明：</strong></label><br /><textarea name="OrderRemark" id="OrderRemark" cols="45" rows="3" style="width:100%;border: 1px solid #dbdbdb;"></textarea> </div>

  <div class="line96" style="width:65%;" >
<? if($isempty) { ?>
<span class="notic_e"  ><font color="red">注：黄色部份为：订货量超出可用库存数，请调整该商品的订购数量，再提交订单!</font></span>
<span class="notic_e"><input name="addorder" id="addorder" value="返回购物车，调整商品数量?" type="button" class="button_4" onclick="javascript:window.location.href='cart.php'" /></span>
<? } else { ?>
          
<? if($_SESSION['cc']['cflag']==8) { ?>
<span class="notic_e"><input type="button" name="addorder" class="button_4" id="addorder" value="下一步，提交订单" onclick="alert('您的账号为待审核状态,不能提交订单,请联系供货商!');"  /></span>
          
<? } else { ?>
          <span class="notic_e"><input type="button" name="addorder" class="button_4" id="addorder" value="下一步，提交订单" onclick="guestorderadd();"  /></span>
          
<? } ?>
<span class="notic_e" style="margin-top: 5px;"><a href="cart.php">返回购物车，继续选购其它商品? </a></span>
<? } ?>
</div>
 </div>
</form>
<? } ?>
</div>

</div>
</div>
<? include template('bottom'); ?>
</body>
<script type="text/javascript">
$(function(){
    var myDate = new Date();
    $("#DeliveryDate").datepicker({
        minDate: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate()+1)
    });
    
    //类型选择
    var msgBox = $("#position-msg-box"),
    	label = $("label", $("div.fix-type-area > ul > li"));
    label.bind({
    	"click" : function(){
    		//重置选中项并设置当前选中
   			$(this).parent().addClass('current').parent().siblings().find('div').removeClass('current');
    	},
    	"mouseover" : function(e){
//    		$("span.qmark-icon", $(this)).css('visibility', 'visible');
    	},
    	"mouseout" : function(e){
//    		$("span.qmark-icon", $(this)).css('visibility', 'hidden');
    	}
    });
   //显示提示信息
//   $("span.qmark-icon", label).bind({
   	label.bind({
   		"mouseover" : function(){
   			var tips	= $(this) ;
var offset	= tips.offset();
var left	= offset.left,
top		= offset.top;

tips.attr('data-tip').length && msgBox.css({"left": (left - 12) + "px","top" : (top + 28) + "px"}).html(tips.attr('data-tip')).show();
   		},
   		"mouseout" : function(){
   			msgBox.hide();
   		}
   });
   
   //收货地址
$("li", $("#my-address")).bind({
"mouseover" : function(){
$(this).addClass('unfloat-hover').find("span.show-edit").show();
},
"mouseout" : function(){
$(this).removeClass('unfloat-hover').find("span.show-edit").hide();
}
});

//显示收货地址编辑区域
   $("span.show-edit").bind("click", function(){
if($(this).hasClass('add-new')){
$("#AddressCompany").val('');
$("#AddressContact").val('');
$("#AddressPhone").val('');
$("#AddressAddress").val('');
}   	
$("#edit-mycompany-address").show();
   });
   
   //当收货地址多余1行时，只显示一行收货地址
   var  sendLine	= $("#my-address > li"),
   		moreAddress = $("#my-more-address");
   var lineHeight	= sendLine.eq(0).height();
   	   lineHeight += 10;
   
   		//隐藏
   		if(sendLine.length > 1){
   			sendLine.parent().addClass('hide-send-more').css("height", lineHeight + "px");
   			moreAddress.show();
   		}
   		
   		moreAddress.toggle(
   						function(){
   							sendLine.parent().css("height", (lineHeight * sendLine.length) + "px");
   							$(this).html('<span>收起地址</span><b class="bottom"></b>');
   						},
   						function(){
   							sendLine.parent().css("height", (lineHeight) + "px");
   							$(this).html('<span>更多收货地址</span><b class=""></b>');
//   							$("#edit-mycompany-address").hide();
   						}
   					);
});

function setinputval(companymsg,contactmsg,phonemsg,addressmsg)
{
$("#AddressCompany").val(companymsg);
$("#AddressContact").val(contactmsg);
$("#AddressPhone").val(phonemsg);
$("#AddressAddress").val(addressmsg);
}

function addressadd()
{	
//$("#tip").animate({opacity: 'show'}, 'slow');
if($("#AddressContact").val()=="" || $("#AddressPhone").val()=="" || $("#AddressAddress").val()=="")
{
$.growlUI('联系人 / 联系电话 / 详细地址 不能为空!');
}else{
$.post("consignment.php",
{m:"saveaddress", data_AddressCompany:$("#AddressCompany").val(), data_AddressContact: $("#AddressContact").val(), data_AddressPhone: $("#AddressPhone").val(), data_AddressAddress: $("#AddressAddress").val()},
function(data){		
if(data.status == "ok"){	
$.growlUI('提交成功...');
$('#saveaddinfoadd').attr("disabled","disabled");
}else{
$.growlUI(data.msg);
$('#saveaddinfoadd').attr("disabled","");
}
}, 'json'		
);
}
//window.setTimeout("hideshow('tip')",20000);
}

function setinvoicetype(ty,tax)
{

if(ty == "P"){
$('#invoice_content_z_div').hide();
$('#invoice_content_div').show();
var taxtotal = $('#product_total').val() * tax / 100;
var alltotal = parseFloat(taxtotal) + parseFloat($('#product_total').val());
taxtotal = taxtotal.toFixed(2);
alltotal = alltotal.toFixed(2);	
$('#show_tax_program').html($('#product_total').val() + ' * ' + tax + ' % = ');	
$('#show_tax_total').html('¥ '+taxtotal);
$('#show_all_total').html('¥ '+alltotal);
$('#total_tax_line').show();
$('#total_all_line').show();		
}else if(ty == "Z"){
$('#invoice_content_z_div').show();
$('#invoice_content_div').show();
var taxtotal = $('#product_total').val() * tax / 100;
var alltotal = parseFloat(taxtotal) + parseFloat($('#product_total').val());
taxtotal = taxtotal.toFixed(2);
alltotal = alltotal.toFixed(2);	
$('#show_tax_program').html($('#product_total').val() + ' * ' + tax + ' % = ');	
$('#show_tax_total').html('¥ '+taxtotal);
$('#show_all_total').html('¥ '+alltotal);
$('#total_tax_line').show();
$('#total_all_line').show();	
}else{
        $('#total_tax_line').hide();
        $('#total_all_line').hide();
        $('#invoice_content_div').hide();
}
}
    function QuOnck(a){
        //  判断是否上传企业资质
        
<? if($BottomZizhi != 'T'){  ?>
          
                var validHtml = '<div class="layui-layer-content" style="height: 143px;line-height: 28px;font-size: 16px;text-align: center;margin: 0 15px;">';
                validHtml +='<img src="../manager/images/wenjian.jpg" style="width: 50px;display: block;margin: 0 auto;margin-top: 10px;">';
                
<? if($BottomZizhi == 'W' || $BottomZizhi ==""){  ?>
                validHtml += '为便于您更好的使用系统<br />请现在前往 ›› <a href="my.php?m=qualification" style="color:#33a676;font-size:16px">上传资质文件</a>';
                
<? }else if($BottomZizhi == 'F'){  ?>
                validHtml = validHtml.replace('wenjian.jpg', 'cha.jpg');
                validHtml += '您的企业资料审核未通过<br />请现在前往 ›› <a href="my.php?m=qualification" style="color:#33a676;font-size:16px">更新企业资质文件</a>';
                
<? }else{  ?>
                validHtml += '您所提交的企业资质正在审核中，请耐心等待';
                
<? }  ?>
                validHtml += '</div>';

                layer.open({
                        type : 1,
                        title: '提示信息',
                        area: ['390px', '170px'],
                        content: validHtml,
                        cancel: function(){

                                
<? echo (strpos($_SERVER['SCRIPT_NAME'], 'cart.php') === false) ? '' : "window.location = 'home.php?isin';";  ?>
                        },
                        end:function(){
                            $("#paytype_9").attr("checked","checked");
                            $("#paytype_12").parent().parent().removeClass('current');
                            $("#paytype_9").parent().parent().addClass('current');
                        }
                });
                return false;
       
<? }  ?>
    <!--// end-->
        $.post("my.php?m=SelQu",{a:a},
        function(result){
                if(result != 'open'){
                layer.open({
                        type : 1,
                        title: '',
                        shadeClose:false,
                        closeBtn:false,
                        area: ['310px', '150px'],
                        content: $('#applyPop'),
                        resize: false,
                        scrollbar: false,
                        end:function(){
                            $("#paytype_9").attr("checked","checked");
                            $("#paytype_12").parent().parent().removeClass('current');
                            $("#paytype_9").parent().parent().addClass('current');
                        }
                       
                });  
                };
            });
    }
</script>

</html>
