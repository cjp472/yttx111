
<style>
.end-info{
      width:80% !important;
}
.end-info span p{
    text-align: center !important;
 
}
.Customer{
color: #fff !important;
font-size: 12px;
}
.helpCustomer {
opacity: 0;
}

.helpCustomer1{
opacity: 0;
}
.mohu1{
width: 525px;
height: auto;
background-color: #fff;
border-left:2px solid #01A157;
border-bottom:2px solid #01A157;
border-right:2px solid #01A157;
margin-top:2px;
margin-left: -2px;
position: absolute;
z-index: 99;
}
.mohu1>ul>li{
height: 30px;
line-height: 30px;
width: 515px;
text-align: left;
padding-left: 10px;
border-bottom: 1px solid #ededed;
}
.mohu1>ul>li>p:nth-child(2){
float: right;color:#aaa;font-size: 13px;padding-right: 5px;
}
.mohu1>ul>li>p:nth-child(2):hover{
color: #fff;
}

.mohu1>ul>li>p:nth-child(1):hover{
color: #fff;
}
.mohu1>ul>li:hover{
background-color:#33a676;
color: #fff !important;
}

</style>
<div class="footer m-t">
<div class="clear" style="display:none;"></div>
<div class="diy-menu">
<ul style="overflow:hidden;width:600px;margin:auto;padding-left: 50px;">
<li>
        		<a href="home.php">首页</a>
        </li>
        <li>
        	<a href="list.php">商品中心</a>
        </li>
        <li>
        	<a href="list.php?m=spc">优惠促销</a>
        </li>
        <li>
        	<a href="myorder.php">我的订单</a>
        </li>
        <li>
        	<a href="finance.php" style="border:none;">款项信息</a>
        </li>
    </ul>
</div>
    
<div class="message">
<p class="p1">Copyright&nbsp©&nbsp2014 - <?=date('Y')?>&nbsp医统天下（北京）网络科技有限公司</p>
<p class="p2">互联网药品交易服务资格证书国A20150005号，京ICP备14037820号，京公网安备11010102001371号 电话 ：400-855-9111 邮箱：info@yitong111.com</p>
<ul class="po">
<li><a href="http://qyxy.baic.gov.cn/" target="_blank"><img src="<?=CONF_PATH_IMG?>/images/1.png" /></a></li>
<li><a href="http://www.szfw.org/" target="_blank"><img src="<?=CONF_PATH_IMG?>/images/2.png" /></a></li>
<li><a href="http://www.miitbeian.gov.cn/state/outPortal/loginPortal.action;jsessionid=cDGTW7JfLhXMLn8QWW130Kyhf1TFTpSWHHNCgv2QkvscR2nBLfYH!1794355333" target="_blank"><img src="<?=CONF_PATH_IMG?>/images/3.png" /></a></li>
<li><a href="http://app1.sfda.gov.cn/datasearch/face3/base.jsp?tableId=28&tableName=TABLE28&title=%BB%A5%C1%AA%CD%F8%D2%A9%C6%B7%D0%C5%CF%A2%B7%FE%CE%F1&bcId=118715637133379308522963029631" target="_blank"><img src="<?=CONF_PATH_IMG?>/images/4.png" /></a></li>
<li><a href="http://app1.sfda.gov.cn/datasearch/face3/base.jsp?tableId=28&tableName=TABLE28&title=%BB%A5%C1%AA%CD%F8%D2%A9%C6%B7%D0%C5%CF%A2%B7%FE%CE%F1&bcId=118715637133379308522963029631" target="_blank"><img src="<?=CONF_PATH_IMG?>/images/5.png" /></a></li>
</ul>
<div class="clear"></div>
</div>
</div>
</div>

<div class="shopingcar" style="position: fixed;right:6px;bottom:350px;cursor: pointer;z-index: 2000">
<a href="cart.php"><span class="iconfont icon-circularorder"  style="font-size: 40px;color: #01A157"></span></a>
<? if(!empty($_SESSION['cartitems'])) { ?>
<span onclick="javascript:window.location.href='cart.php'" style="width: 20px;height: 20px;line-height:20px;text-align:center;position:absolute;left:20px;top:-12px;border-radius:10px;background-color: #FF8E32;display:block;" id="cartnumber">
<? if(count($_SESSION['cartitems']) > 99) { echo count($_SESSION['cartitems']);; ?>+
<? } else { echo count($_SESSION['cartitems']);; } ?>
</span>
<? } ?>
</div>

<div class="Services" style="position: fixed;width: 120px;right:6px;bottom: 278px;cursor: pointer;">
<div class="help1" style="float: right;width: 40px;height: 43px;"><span class="iconfont icon-kefu" style="font-size: 40px;color: #01A157" alt=""/></div>
<div class="helpCustomer" style="width: 120px;background-color: #fff;position:absolute;box-shadow: 2px 2px 2px #ddd,-2px -2px 2px #ddd;bottom:-165px;right: 0px;color:#fff">
<div class="help2" style="width:94px;height:18px;line-height:18px;margin:5px auto;border-radius:50px;text-align:center;font-size:12px;background-color: #34b479;padding-bottom: 5px;"><a class="Customer" style="color: #fff !important;font-size: 12px;" href="./templatel/unicom/customerFirst.html">联系我们</a></div>
<div class="help3" style="width:90px;height:45px;margin:0 auto;font-size:12px;color: #666"><?=$customer_service['ContactName']?><?=$Symbol?><br>
<p style="font-size: 12px;margin-top: -6px;"><?=$customer_service['ContactValue']?></p>
</div>
<div class="help4" style="width:90px;height: 40px;margin:0 auto;font-size:12px;color: #666">医统客服:<br>
<p style="font-size: 12px;margin-top: -6px;">0371-60942268</p></div>
<div class="help_er" style="height: 100px;">
<div class="help_bar" style="width: 90px;height: 90px;margin: 0 auto">
<img src="./template/red/images/help_bar.jpg" style="border: 2px solid #34b479;" alt=""/>
</div></div>
</div>
</div>


<div class="helpAll" style="position: fixed;width: 50px;right:6px;bottom: 245px;cursor: pointer;z-index:2000">
<div class="help2" style="float: right;"><span class="iconfont icon-bangzhuzhongxin1" style="font-size: 40px;color: #01A157" alt=""/></div>
<div class="helpCustomer1" style="width: 110px;height: 108px;background-color: #fff;padding-right:8px;box-shadow: 2px 2px 2px #ddd,-2px -2px 2px #ddd;position:absolute;bottom:-65px;right: 0px;padding-left:10px;color: #fff !important;">
<div class="help2" style="width: 94px;height: 18px;margin:7px auto;line-height:18px;background-color: #34b479;border-radius:50px;text-align:center;margin-bottom:5px;font-size:12px;"><a target="_blank"  class="Customer" href="template/tpl/unicom/customerFirst.html">首次下单</a></div>
<div class="help2" style="width: 94px;height: 18px;margin:7px auto;line-height:18px;background-color: #34b479;border-radius:50px;text-align:center;margin-bottom:5px;font-size:12px;"><a target="_blank"  class="Customer" href="template/tpl/unicom/readymoney.html">充值指南</a></div>
<div class="help3" style="width: 94px;height: 18px;margin:7px auto;line-height:18px;background-color: #34b479;border-radius:50px;text-align:center;font-size:12px;"><a target="_blank"  class="Customer" href="template/tpl/unicom/returnGoods.html">退货详情</a></div>
<div class="help3" style="width: 94px;height: 18px;margin:7px auto;line-height:18px;background-color: #34b479;border-radius:50px;text-align:center;font-size:12px;"><a target="_blank"  class="Customer" href="template/tpl/unicom/bankcard.html">解绑银行卡</a></div>
</div>
</div>



<img id="comeback2" src="./template/red/images/xiangs.png" style="display:none;width: 40px;height: 42px;position: fixed;right:8px;bottom: 100px;cursor: pointer" alt=""/>

<div class="clear"></div>





<!-- 随屏搜索 -->
<style type="text/css">
.fixed-head{height:83px;width:100%;position:fixed;background:#fff none repeat scroll 0 0 !important;z-index:1000;border-bottom:1px solid #eee;box-shadow:0 2px 20px rgba(0,0,0,0.1);display:block;top:-89px}
.w{margin:0 auto;width:1000px}
.fixed-head > .w > img{float:left;height:55px;margin:12px 0 0 70px;width:180px}
img{border:0 none;vertical-align:middle}
fieldset,img,input,button{border:medium none;margin:0;outline-style:none;padding:0}
.search-input{color:#615f5f;font-size:14px;line-height:46px;height:46px;width:506px;padding-left:15px;margin-top:1px;}
.search-head > form > button{background-color:#01a157;height:47px;margin-top:0px;width:75px;cursor:pointer;float:right;}
.search-head > form > button:hover{opacity:0.9}
.search-head{height:47px;margin-left:40px;margin-top:15px;width:600px;border:2px solid #01a157;}
</style>


<div class="fixed-head" id="fixed-head">
    <div class="w">
    	<img src="template/red/images/logo.png" style="cursor:pointer;" onclick="javascript:window.location='home.php'" />
        <div class="search-head fl" style="float: left">
        	<form name="searchform" id="searchform" action="search.php" method="get" style="display:inline;">
        	<input type="text" name="kw" class="search-input" id="search1" autocomplete="off" placeholder="输入商品名称|商品编号|药企名称" />
            <button><img src="template/red/images/ss.png" /></button>
<div class="mohu1" style="display:none;">
<ul id="vague_data1"  style="margin-left: -40px;">
</ul>
</div>
            </form>
        </div>
        <div class="clear"></div>
</div>
</div>

<div class="b_show" id="branser-notice" style="display:none;height: 59px;line-height:59px;width: 100%;position:fixed;top:0;background-color:#fff;box-shadow:0 2px 20px rgba(0,0,0,0.2);border-bottom: 1px solid #dbdbdb;z-index:9998;">
<div class="b_show_g" style="height: 10px;width: 100%;background-color: #66BEA8;">
</div>
<div class="w">
<img src="template/red/images/0.png"style="width: 30px;float: left;margin-top: 5px" alt=""/>
<p style="font-size: 18px;height: 50px;line-height: 50px;text-align:center;float: left;margin-left: 20px">
尊敬的客户您好：您当前使用的浏览器版本过低，为了您能获得更好的浏览体验，建议更换&nbsp;&nbsp;&nbsp;&nbsp;
<a href="http://se.360.cn/" style="color:#66BEA8" target="_blank">360浏览器</a>、
<a href="http://www.firefox.com.cn/" style="color:#66BEA8" target="_blank">火狐浏览器</a>
</p>

</div>
<span onclick="javascript:$('#branser-notice').fadeOut('slow');" class="ali-small-16 iconfont icon-guanbi" style="cursor:pointer;display: block;float: right;margin-right: 20px;margin-top: -5px;color: #615f5f;font-size: 22px;"></span>
</div>
<script>

$(document).ready(function(){
$(".Services").mouseenter(function(){
$(".helpCustomer").show();
$(".helpCustomer").css("right","0");
$(".helpCustomer").animate({
right:'50px',
opacity:'1',

},500)
});$(".Services").mouseleave(function(){
$(".helpCustomer").hide(50);

});

});
</script>
<script>
$(document).ready(function(){
$(".helpAll").mouseenter(function(){
$(".helpCustomer1").show();
$(".helpCustomer1").css("right","0");
$(".helpCustomer1").animate({
right:'50px',
opacity:'1',
})
});
$(".helpAll").mouseleave(function(){
$(".helpCustomer1").hide();
});
});
</script>

<script type="text/javascript">
$(document).ready(function(){

//平滑回到顶部
$('#comeback2').bind('click', function(){
$('body,html').animate({ scrollTop: 0 }, 1000);
});

//淡出浏览器版本提示
if(!$.support.leadingWhitespace){
var _b = $('#branser-notice');
_b.show();
setTimeout(function(){
_b.fadeOut('slow')
}, 15000);
}
});
var animateFlag=1;$(document).scroll(function()
{var scrollTop=$(document).scrollTop();if(scrollTop>400) {$('#comeback2').fadeIn('slow');$("#fixed-head").css('display','block');
if(animateFlag) {animateFlag=0;$("#fixed-head").stop();
$("#fixed-head").animate({'top':'0px'},500)}}
else{$(".searchRe").css('display','none');
$('#comeback2').fadeOut('slow');
if(animateFlag==0)
{animateFlag=1;$("#fixed-head").stop();
$("#fixed-head").animate({'display':'none','top':'-89px'},500)
$(".mohu1").css("display","none");

}}});

</script>

<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?0179b860630b3ea445237b9629f7d87e";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();

//去空隔函数 
function Jtrim(str){ 
return str.replace(/^\s*|\s*$/g,"");  
} 	

$(document).ready(function(){
$('#search1').bind('input propertychange', function() {

var val=$(this).val();

$.ajax({
type: 'GET',
url: "vague_search.php" ,
data: {"kw":val} ,
success: function(datas){

if(datas!='' && datas.list.length >0){
$(".mohu1").css("display","block");
var html="";
$.each(datas.list, function(i, item){
html+='<li class="select_li"><p style="float: left;">'+item.Name+'</p><p>约有<span>'+item.c_num+'</span>个结果</p></li>';
$("#vague_data1").html(html);
});
}

$(document).click(function(){
$(".mohu1").css("display","none");
});

$(".select_li").click(function(){
var select_vals=$(this).find("p:first-child").html();
select_vals=Jtrim(select_vals);
$(".mohu1").css("display","none");
//$('#search').val(select_vals);
window.location.href="search.php?kw="+select_vals+"&action=vague";
});


} ,
dataType: "json"
});
});
});


</script>

