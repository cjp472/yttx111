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
<script src="template/js/function.js" type="text/javascript"></script>
<script src="template/js/my.js?v=<?=VERID?>" type="text/javascript"></script>

 
<link rel="stylesheet" type="text/css" href="template/css/credit.css?v=<?=VERID?>"/>
<link rel="stylesheet" type="text/css" href="template/css/icon.css?v=<?=VERID?>"/>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="my.php?m=profile">我的医药账户</a> / <a href="my.php?m=credit">医统账期</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span>我的医药账户</span></div>
  <div class="news_info">
    <!-- 载入菜单 -->
  
<? include template('my_profile_menu'); ?>
  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right" style="height: 570px !important;">

<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>医统账期/申请账期</div>
</div>

<div class="right_product_main">
<div class="list_line">
<ul class="credit_apply">
<li>
<span>采购频率：</span>
<div class="credit_rate">
<div>
                <p class="times"><span value='1'><i class='active'></i></span><em>2周3次以内</em></p>
<p class="times"><span value='2'><i></i></span><em>2周3次以上</em></p>
<p class="times"><span value='3'><i></i></span><em>1月6次以内</em></p>
<p class="times"><span value='4'><i></i></span><em>1月6次以上</em></p>
</div>					
                    <div>其他：&nbsp;<input type="text" class="other" id="LevelOther" value="<?=$creditApply['LevelOther']?>" placeholder="例：1周4次"/></div>
</div>
</li>
<li class="same_li">
<span>月平均采购额：</span>
<input type="text" placeholder="例：5000" id='PurchaseAmount' value="<?=$creditApply['PurchaseAmount']?>"  />
</li>
<li class="same_li">
<span>期望授信额度：</span>
<input type="text" id='Amount' value="0-5000" readonly= 'true' class="showChoice"/>
<i class="iconfont icon-xiala showChoice"></i>
<i class="iconfont icon-xiala-copy hide showChoice"></i>
</li>

</ul>
<div class="amountChoose hide clear">
<input type="text" value="0-5000" class="amountMoney" readonly="true"/><br />
<input type="text" value="5001-10000" class="amountMoney" readonly="true"/><br />
<input type="text" value="10001-15000" class="amountMoney" readonly="true"/><br />
<input type="text" value="15001-20000" class="amountMoney" readonly="true"/><br />
</div>
<input type='button' value='申请' class="credit_sure" id="subQue"/>

<br class="clear" />
<br class="clear" />
</div>
<br />&nbsp;
       </div>
</div>
</div>
</div>
<? include template('bottom'); ?>
</body>

<script type="text/javascript">
$(function(){
/*选择期望授信额*/
$('.amountChoose input').hover(function(){
$('.amountChoose input').css({
"background": '#fff'
});
$(this).css({
"background": '#f6f6f6'
});
});
if(window.sessionStorage.getItem('Content')){
console.log('缓存')
var aa = window.sessionStorage.getItem('Content');
$('#Amount').val(aa);
}
function check(){
var one1        = $('.icon-xiala');
var one2        = $('.icon-xiala-copy');
var amountChoose= $('.amountChoose');

if(one1.hasClass('hide')){
$('#Amount').css({"border-radius":'4px'});
one1.removeClass('hide');
one2.addClass('hide');
amountChoose.addClass('hide');
}else{
$('#Amount').css({"border-radius":'4px 4px 0 0'});
one1.addClass('hide');
one2.removeClass('hide');
amountChoose.removeClass('hide');
};
};
$('.showChoice').click(function(){
check();
});

$('.amountMoney').click(function(){
$('#Amount').val($(this).val());

$('.amountChoose').addClass('hide');
$('.icon-xiala-copy').addClass('hide');
$('.icon-xiala').removeClass('hide');

if($('#Amount').val() != ''){
console.log($('#Amount').val());
console.log(1);
window.sessionStorage.setItem('Content',escape($(this).val()));
}
});
$('body').click(function(){
$('#Amount').css({"border-radius":'4px'});
$('.amountChoose').addClass('hide');
$('.icon-xiala-copy').addClass('hide');
$('.icon-xiala').removeClass('hide');
})
$('.showChoice').click(function(e){
e.stopPropagation();
})
/*选中采购频率按钮*/
$('.times').click(function(){
$('.credit_rate i').removeClass('active');
$(this).find("i").addClass('active');
});
$('.credit_rate i').click(function(){
$('#LevelOther').val('');
$(this).parent().attr("value");
}).each(function(i){//初始换数据
$(this).removeClass('active').parent().attr("value") == '<?=$creditApply['Level']?>' && 			$(this).addClass('active');
});

$('.other').focus(function(){
$('.credit_rate i').removeClass('active');
});
                    $('.credit_sure').click(function(){
                        if( $('.credit_rate i').hasClass("active")){
                            var Level = $('i.active').parent().attr("value") 
                        }else{
                            var LevelOther = $('#LevelOther').val();
                            if(LevelOther==''){
                                $.growlUI('请选择采购频率');
                                exit;
                            }                           
                        }                      
                        var PurchaseAmount = $('#PurchaseAmount').val();
                        var Amount = $('#Amount').val();
                        if(Amount==''||PurchaseAmount==''){
                            $.growlUI('月采购额/期望授信额 不能为空');
                            exit;
                        }
                        
                        $("#subQue").attr('disabled','true');
                        $("#subQue").val("正在提交，请稍等");
                        $.post("my.php?m=AddcreditApply",{Level:Level,LevelOther:LevelOther,PurchaseAmount:PurchaseAmount,Amount:Amount},
                        function(result){
                            if($.trim(result)=='ok'){
                                $.growlUI('操作成功');
                                setTimeout(function(){
                                window.location.href = "my.php?m=credit";
                                },2000) 
                             
                            }else{
                                $.growlUI(result);
                            }
                        });
                })
});
</script>
</html>




