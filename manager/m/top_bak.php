
 <link rel="stylesheet" href="css/index.css">
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<script src="https://unpkg.com/element-ui/lib/index.js"></script>

<style type="text/css">
body {position:relative;margin:0; padding:0; font-size:12px; background-repeat: repeat-x;background-position: left 48px;font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; color:#333;}
#topmenu{top:0px;}
#extend-ul{margin-top:expression( ($(window).height() * 0.15) + "px");}
@font-face {font-family: "iconfont";
  src: url('css/fonts/iconfont.eot?t=1487056219212'); /* IE9*/
  src: url('css/fonts/iconfont.eot?t=1487056219212#iefix') format('embedded-opentype'), /* IE6-IE8 */
  url('css/fonts/iconfont.woff?t=1487056219212') format('woff'), /* chrome, firefox */
  url('css/fonts/iconfont.ttf?t=1487056219212') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/
  url('css/fonts/iconfont.svg?t=1487056219212#iconfont') format('svg'); /* iOS 4.1- */
}

.iconfont {
  font-family:"iconfont" !important;
  font-size:14px;
  font-style:normal;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.icon-kefu:before { content: "\e600"; }

.icon-icon01:before { content: "\e601"; }

.icon-xinxi:before { content: "\e604"; }

.icon-jingxuan:before { content: "\e602"; }

.icon-dingdan:before { content: "\e61a"; }

.icon-xiaoxi:before { content: "\e672"; }

.icon-guanji:before { content: "\e654"; }

.icon-huoche:before { content: "\e703"; }

.icon-kucun:before { content: "\e642"; }

.icon-tuihuozhong:before { content: "\e87c"; }

.icon-kehu-copy:before { content: "\e629"; }

.icon-dayuhao:before { content: "\e634"; }

.icon-zhifukuanxiang:before { content: "\e62e"; }

.icon-tongji:before { content: "\e63f"; }

.icon-youpinwangtubiao-:before { content: "\e6b3"; }


</style>



<script type="text/javascript">

function do_save_experience_isset_un(){
	$('#un_isset_contact').css('opacity','0.5').removeAttr('onclick').removeAttr('style');
	$('#un_isset_contact div').text('等待中...');

	$.post("do_feedback.php",
		{m:"contact_add_save_isset_un"},
		function(data){
		data = $.trim(data);
			if(data == "ok"){
				window.location.reload();
			}else{
				alert(data);
				$('#un_isset_contact').css('opacity','1').attr('onclick','do_save_experience_isset_un();').attr('style','cursor:pointer;');
				$('#un_isset_contact div').text('医统天下引导');
			}
		}
	);
}

$(document).ready(function(){
	$('.service_tools_menu li').each(function(){
		$(this).bind('mouseover',function(){
			$('.tools_menu_item').css('display','none');
			$('#'+$(this).attr('class')).css('display','block');
		});
	});

	//显示右侧底部下面三个工具栏
	$("#move-bar-in-li").find('li').bind({
		'mouseover': function(){
			$('div', $(this)).show();
		},
		'mouseout': function(){
			$('div', $(this)).hide();
		}
	});

	//设置IE11右边栏关闭按钮样式
	if($.browser.msie){
		$("#action-close").css("margin-top", "3px");
	}
//	if($.browser.msie || $.browser.mozilla && parseInt($.browser.version) == 11){
//		$("#feedback-close").css("margin-top", "15px");
//	}
//	if((navigator.userAgent.toLowerCase()).indexOf('qqbrowser')+1){//需要验证是否要加上上一个的判断条件
//		$("#feedback-close").css("margin-top", "3px");
//	}

	$("#elevator").click(function(){
		$('body,html').animate({scrollTop:0},1000);
		return false;
	});
	$(".slide-feedback").click(function(){
		$('#ele_feedback').animate({'right':'0px'},300);
		$('div.right-slide').animate({
            right: '90px'
        },300)
	});

	$('#ele_feedback .feedback_item label').each(function(){
		var _ = $(this);
		$(this).click(function(){
			_.siblings().removeClass('current');
			$(this).addClass('current');
		});
	});

	$('.feedback-close').click(function(){
		$('#ele_feedback').animate({'right':'-250px'},300);
		$('div.right-slide').animate({
			 right: '-13.5em'
		 },300);
	});

	$('#new_message').blur(function(){
		if($(this).val()){
			$(this).css('border-color','#eef2f2');
		}
		if($('#ele_feedback .feedback_tips span').text()=='请输入反馈内容！'){
			$('#ele_feedback .feedback_tips').css('display','none').html('');
		}
	});
	$('#new_title').blur(function(){
		if($(this).val()){
			$(this).css('border-color','#eef2f2');
		}
		if($('#ele_feedback .feedback_tips span').text()=='请输入手机号！'){
			$('#ele_feedback .feedback_tips').css('display','none').html('');
		}
	});

	$('#ele_feedback #submit_button').click(function(){
		if($('#new_title').val()==""){
			$('#new_title').css('border-color','red');
		}

		if($('#new_message').val()==""){
			$('#new_message').css('border-color','red');
		}

		if($('#new_title').val()==""){
			$('#ele_feedback .feedback_tips').css('display','block').html('<span style="color:red;">请输入手机号！</span>');
		}else if($('#new_message').val()==""){
			$('#ele_feedback .feedback_tips').css('display','block').html('<span style="color:red;">请输入反馈内容！</span>');
		}else{
			$('#ele_feedback .feedback_tips').css('display','none');
			$(this).attr('disabled','disabled').val('正在提交...');

			$.post("do_feedback.php",
				{m:"feedback_add_save", type: $('input[name="new_type"]:checked').val(),title: $('#new_title').val(),message: $('#new_message').val()},
				function(data){
				data = $.trim(data);
					$('#ele_feedback #submit_button').removeAttr('disabled').attr('value','提交');
					if(data == "ok"){
						$('#new_title,#new_message').val('');
						$("input[name='new_type']:checked").removeAttr("checked");
						$("input[name='new_type']").eq(0).attr("checked","checked");
						$('#ele_feedback .feedback_item label').removeClass('current').eq(0).addClass('current');
						$('#ele_feedback .feedback_tips').css('display','block').html('<span style="color:green;">您的建议对我们非常重要！感谢您的参与！我们会努力做到更好！</span>');

						setTimeout(function(){
							$('#ele_feedback').animate({'right':'-250px'});
							$('#ele_feedback .feedback_tips').css('display','none').html('');
							$('div.right-slide').animate({
								 right: '-13.5em'
							 },300);
						},3000);
					}else{
						$('#ele_feedback .feedback_tips').css('display','block').html('<span style="color:red;">'+data+'</span>');
					}
				}
			);
		}
	});

});

		function setIe7(){
			$('div.slide-left,div.slide-right').css('height',($(window).height() - $('.page-top').height()) + 'px');
			$('#extend-ul').css('margin-left', '0');
		}

    	$(function(){
    	    // IE7 兼容处理
			setIe7();
			$(window).resize(function(){
				setIe7();
			});

    	    // 鼠标移入移出事件
    	    $('ul.slide-ul li').hover(function(){
    	        var left = '-100px';
    	        if($(this).hasClass('slide-qq') || $(this).hasClass('slide-scan')){
    	            left = '-157px'
    	        }
    	        $(this).children('.choice').show();
                $(this).children('div').stop(true).animate({
                    left: left
                },300);
            },function(){
                $(this).children('.choice').hide();
                $(this).children('div').stop(true).animate({
                    left: '0'
                },300);
            })

            // 展开扩展功能选项
            $('li.slide-ext').click(function(e){
                // 阻止事件冒泡
                e.stopPropagation();
                $('div.right-slide').animate({
                    right: 0
                },300);
				$('#ele_feedback').animate({'right':'-250px'},300);
            });
            // 收起功能扩展选项
            $('div.slide-right span.close').click(function(e){
                // 阻止事件冒泡
                e.stopPropagation();
                $('div.right-slide').animate({
                    right: '-13.5em'
                },300)
            });
            $(document).click(function(){
               /* $('div.right-slide').animate({
                    right: '-13.5em'
                },300)
				*/
            });
    	});
</script>

<?php if(DHB_RUNTIME_MODE === 'experience'):?>

<?php

$sOldIndustry_experience = isset($_SESSION['industry']) ? trim($_SESSION['industry']) : '';
$sIndustry_experience = getSafeIndustry($sOldIndustry_experience);
$bError_experience = false;
if(empty($sIndustry_experience) || !in_array($sIndustry_experience,$EXPERIENCE_INDUSTRY)){
	$bError_experience = true;
}
$sCp_experience = isset($_SESSION['ucc']['CompanyID']) ? encodeData(trim($_SESSION['ucc']['CompanyID'])) : '';

?>

<div style="left: 0;position: fixed !important;top: 0;width: 100%;z-index: 1000;height:80px;">
	<div class="page-top">
		<div class="clearfix">
		    <div class="float-left">
    			<span style="*vertical-align: super;color:#FFF;">您正在体验演示账号，账号内容为模拟内容，操作仅在当天保存。若想正式使用您可以</span>&nbsp;&nbsp;
    			<button class="go-free" onclick="window.open('http://m.dhb.hk/pro/openAccount.php');">开通20用户免费版</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    			<button class="go-pay" onclick="window.open('http://m.dhb.hk/pro/openAccount.php?action=buy');">购买无限版</button>
			</div>
			<div class="float-right">
				<?php if($bError_experience ===true):?>
				<span style="color:#FFF;">【行业未指定，无法切换】</span>
				<?php else:?>
				<span class="seek" style="color:#FFF;" onclick="window.open('http://wpa.b.qq.com/cgi/wpa.php?ln=2&uin=4006311682')"><em></em> 售前咨询</span>
				<button class="go-bsmm" style="color:#FFF;" onclick="window.location.href='<?php echo makeFront($sOldIndustry_experience,$sCp_experience);?>';">切换到订货端</button>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
<?php endif;?>

<div id="topmenu">
    <div id="header2">
      <div id="logo"><a href="home.php"><img src="img/logo2.jpg?=2014121001" alt="医统天下 订货管理系统 (DHB.HK)" title="医统天下 订货管理系统 (DHB.HK)"  border="0" /></a></div>
            	<ul>
        			<?php
        			foreach($menu_arr as $km=>$kv)
        			{
        				if($km == "system" && $_SESSION['uinfo']['userflag']!="9") continue;
        				if($menu_flag == $km)
        				{
        					echo '<li class="current" onclick="javascript:window.location.href=\''.$km.'.php\'">'.$kv.'</li>';
        				}else{
        					echo '<li ><a href="'.$km.'.php">'.$kv.'</a></li>';
        				}
        			}
        			?>
                </ul>

        </div>

</div>
<div class="bodyline" style="height:<?php if(DHB_RUNTIME_MODE === 'experience'):?>115<?php else:?>55<?php endif;?>px;">&nbsp;</div>

<div id="app" style="margin-top:15px;height:0px;float: left;">
    <el-row class="tac">
        <el-col :span="8">
            <el-menu default-active="2" class="el-menu-vertical-demo" @open="handleOpen" @close="handleClose" theme="dark">
                <el-submenu index="1">
                    <template slot="title"><span class="iconfont icon-dingdan" style="">&nbsp;&nbsp;&nbsp;&nbsp;订单</template>
                        <el-menu-item index="1-1">订单管理</el-menu-item>
                        <el-menu-item index="1-2">商品明细</el-menu-item>
                        <el-menu-item index="1-3">赠品明细</el-menu-item>
                        <el-menu-item index="1-4">开票信息</el-menu-item>
                        <el-menu-item index="1-5">订单留言</el-menu-item>
                        <el-menu-item index="1-6">订单统计</el-menu-item>
                </el-submenu>
                <el-submenu index="2">
                    <template slot="title"><span class="iconfont icon-huoche" style="">&nbsp;&nbsp;&nbsp;&nbsp;发货</template>
                    <el-menu-item index="2-1">发货管理</el-menu-item>
                    <el-menu-item index="2-2">发货明细</el-menu-item>
                    <el-menu-item index="2-3">待发货订单</el-menu-item>
                    <el-menu-item index="2-4">待发货明细</el-menu-item>
                    <el-menu-item index="2-5">新增发货单</el-menu-item>
                    <el-menu-item index="2-6">物流公司</el-menu-item>
                    <el-menu-item index="2-7">新增公司</el-menu-item>
                </el-submenu>
                <el-submenu index="3">
                    <template slot="title"><span class="iconfont icon-zhifukuanxiang" style="">&nbsp;&nbsp;&nbsp;&nbsp;货款</template>
                    <el-menu-item index="3-1">收款单</el-menu-item>
                    <el-menu-item index="3-2">其他款项</el-menu-item>
                    <el-menu-item index="3-3">应收款</el-menu-item>
                    <el-menu-item index="3-4">往来对账</el-menu-item>
                    <el-menu-item index="3-5">收款账户</el-menu-item>
                    <el-menu-item index="3-6">款项统计</el-menu-item>
                    <el-menu-item index="3-7">支付管理</el-menu-item>
                </el-submenu>
                <el-submenu index="4">
                    <template slot="title"><span class="iconfont icon-tuihuozhong" style="">&nbsp;&nbsp;&nbsp;&nbsp;退单</template>
                    <el-menu-item index="4-1">退单管理</el-menu-item>
                    <el-menu-item index="4-2">退货明细</el-menu-item>
                    <el-menu-item index="4-3">退款统计</el-menu-item>
                </el-submenu>
                <el-submenu index="5">
                    <template slot="title"><span class="iconfont icon-jingxuan" style="">&nbsp;&nbsp;&nbsp;&nbsp;商品</template>
                    <el-menu-item index="5-1">商品管理</el-menu-item>
                    <el-menu-item index="5-2">批量传图</el-menu-item>
                    <el-menu-item index="5-3">批量传图</el-menu-item>
                    <el-menu-item index="5-4">批量导入</el-menu-item>
                    <el-menu-item index="5-5">商品分类</el-menu-item>
                    <el-menu-item index="5-6">商品品牌</el-menu-item>
                    <el-menu-item index="5-7">下架商品</el-menu-item>
                    <el-menu-item index="5-8">到货通知</el-menu-item>
                </el-submenu>
                <el-submenu index="6">
                    <template slot="title"><span class="iconfont icon-kucun" style="">&nbsp;&nbsp;&nbsp;&nbsp;库存</template>
                    <el-menu-item index="6-1">库存状况</el-menu-item>
                    <el-menu-item index="6-2">库存预警</el-menu-item>
                    <el-menu-item index="6-3">入库单</el-menu-item>
                    <el-menu-item index="6-4">入库明细</el-menu-item>
                    <el-menu-item index="6-5">商品入库</el-menu-item>
                    <el-menu-item index="6-6">库存调整</el-menu-item>
                </el-submenu>
                <el-submenu index="7">
                    <template slot="title"><span class="iconfont icon-youpinwangtubiao-" style="">&nbsp;&nbsp;&nbsp;&nbsp;药店</template>
                    <el-menu-item index="7-1">药店</el-menu-item>
                    <el-menu-item index="7-2">药店积分</el-menu-item>
                    <el-menu-item index="7-3">回收站</el-menu-item>
                    <el-menu-item index="7-4">登录日志</el-menu-item>
                    <el-menu-item index="7-5">地区管理</el-menu-item>
                </el-submenu>
                <el-submenu index="8">
                    <template slot="title"><span class="iconfont icon-kehu-copy" style="">&nbsp;&nbsp;&nbsp;&nbsp;客情官</template>
                    <el-menu-item index="8-1">客情官</el-menu-item>
                    <el-menu-item index="8-2">回收站</el-menu-item>
                    <el-menu-item index="8-3">业务提成</el-menu-item>
                    <el-menu-item index="8-4">其他提成</el-menu-item>
                    <el-menu-item index="8-5">提成统计</el-menu-item>
                </el-submenu>
                <el-submenu index="9">
                    <template slot="title"><span class="iconfont icon-xinxi" style="">&nbsp;&nbsp;&nbsp;&nbsp;信息</template>
                    <el-menu-item index="9-1">信息管理</el-menu-item>
                    <el-menu-item index="9-2">新增信息</el-menu-item>
                    <el-menu-item index="9-3">回收站</el-menu-item>
                    <el-menu-item index="9-4">广告管理</el-menu-item>
                    <el-menu-item index="9-5">栏目管理</el-menu-item>
                </el-submenu>
                <el-submenu index="10">
                    <template slot="title"><span class="iconfont icon-kefu" style="">&nbsp;&nbsp;&nbsp;&nbsp;客服</template>
                    <el-menu-item index="10-1">留言管理</el-menu-item>
                    <el-menu-item index="10-2">交流工具</el-menu-item>
                    <el-menu-item index="10-3">联系方式</el-menu-item>
                </el-submenu>
                <el-submenu index="11">
                    <template slot="title"><span class="iconfont icon-tongji" style="">&nbsp;&nbsp;&nbsp;&nbsp;统计</template>
                    <el-menu-item index="11-1">订单统计</el-menu-item>
                    <el-menu-item index="11-2">发货统计</el-menu-item>
                    <el-menu-item index="11-3">客情官统计</el-menu-item>
                    <el-menu-item index="11-4">退单统计</el-menu-item>
                    <el-menu-item index="11-5">商品统计</el-menu-item>
                    <el-menu-item index="11-6">地区统计</el-menu-item>
                    <el-menu-item index="11-7">款项统计</el-menu-item>
                    <el-menu-item index="11-8">往来对账</el-menu-item>
                    <el-menu-item index="11-9">提成统计</el-menu-item>
                </el-submenu>
            </el-menu>
        </el-col>
    </el-row>
</div>



<script>
    var Main = {
        methods: {
            handleOpen(key, keyPath) {
        console.log(key, keyPath);
    },
    handleClose(key, keyPath) {
        console.log(key, keyPath);
    }
    }
    }
    var Ctor = Vue.extend(Main);
    new Ctor().$mount('#app')
</script>

<?php if(DHB_RUNTIME_MODE === 'experience'):?>
<script type="text/javascript">
function do_save_experience_contact_un(){
	$('#un_set_contact').css('opacity','0.5').removeAttr('onclick').removeAttr('style');
	$('#un_set_contact div').text('等待中...');

	$.post("do_feedback.php",
		{m:"contact_add_save_un"},
		function(data){
		data = $.trim(data);
			if(data == "ok"){
				window.location.reload();
			}else{
				alert(data);
				$('#un_set_contact').css('opacity','1').attr('onclick','do_save_experience_contact_un();').attr('style','cursor:pointer;');
				$('#un_set_contact div').text('联系服务专员');
			}
		}
	);
}
</script>

<?php

$sCookieContact = isset($_COOKIE['experience_contact']) ? getSafeIndustry($_COOKIE['experience_contact']) : '';
$sCookieContactUn = isset($_COOKIE['experience_contact_un']) ? $_COOKIE['experience_contact_un'] : '';
$bContact_excerience = $sCookieContact ? true : false;
$bShowContact = $bContact_excerience===false && $_SESSION['uinfo']['ucompany'] > 109;

//设置成不显示
$bShowContact = false;
?>

<?php if($bShowContact):?>
<div class="experience_mask experience_opacity"></div>
<div class="experience_content">
	<h1>温馨提示</h1>
	为了更好的帮助您体验系统，我们为您安排了一位服务专员，<br />请留下您的联系信息以便她与您联系：
	<div class="experience_item">手机号码<input type="text" placeholder="请输入您的正确的手机号码以便联络" id="experience-Phone" name="experience-Phone"><br /><span id="experience_phone_error" class="experience_error"></span></div>
	<div class="experience_item" style=" padding-left:13px;">联系人<input type="text" placeholder="请填写联系人" id="experience-Name" name="experience-Name"><br /><span id="experience_name_error" class="experience_error"></span></div>
	<div class="experience_button">
		<a onclick="do_save_experience_contact();" value="确定" id="experience-submitbutton" name="" class="btn">确定</a>
		<?php if(!$sCookieContactUn):?><!--<?php endif;?><a onclick="do_save_experience_contact2();" value="跳过" id="experience-submitbutton2" name="" class="btn1" >跳过</a><?php if(!$sCookieContactUn):?>--><?php endif;?>
	</div>
</div>

<style type="text/css">
html,body{ height:100%; margin:0; padding:0; font-size:14px;}
.experience_mask{height:100%; width:100%;  _position:absolute;position:fixed; top:0; z-index:9999999;left:0; }
.experience_opacity{ opacity:0.5; filter: alpha(opacity=30); background-color:#000; }
.experience_content{-webkit-box-shadow: 8px 3px 8px;  -moz-box-shadow: 8px 8px 8px;  box-shadow: 3px 3px 3px; -webkit-border-radius: 2px; border-radius: 2px; height:300px; width:500px; overflow:auto; border:2px solid #ccc;border:none; background-color:#fff; position:fixed; top:50%; left:50%; margin:-160px auto auto -300px; z-index:10000000; word-wrap: break-word; padding:25px;font-size:15px;color:#565655;padding-bottom:25px; text-align:center }
.experience_content h1{ color:#f57256; font-size:24px; font-weight: normal; line-height:36px; margin-top:0px;}
.experience_item{display:block;margin:15px 0;font-size:14px;font-weight:bold;}
.experience_item input{width:410px;height:50px;width:300px;height:30px;margin-left:10px;margin-right:8px;font-size:14px;border:1px solid #ccc;padding:0px 3px;color:#999;}
.experience_error{font-weight:normal;font-size:14px;color:red;}
.experience_button{text-align:center; width:324px; margin:0 auto;}


.experience_item span{ height:15px; text-align:left; width:250px; display:block; margin:0 auto;}
.btn{width:<?php if(!$sCookieContactUn):?>360<?php else:?>140<?php endif;?>px; height:35px; text-align:center; display:block;  color:#fff; border-radius:90px; font-size:18px; background-color:#FC5A38; float:left; margin:5px;cursor:pointer; overflow:hidden; line-height:35px;}
.btn1{width:150px; height:33px; text-align:center; display:block;border-radius:90px; font-size:18px; background-color:#fff; float:left; margin:5px;cursor:pointer; overflow:hidden; border:1px solid #ff5c1a; color:#ff5c1a;  line-height:33px;}
.btn:hover{ background-color:#fd4900; color:#fff;text-decoration:none;}
.btn1:hover{ background-color:#fd4900; color:#fff; text-decoration:none;}
</style>

<script type="text/javascript">
/* 手机号码验证 */
function isMobile(nMobile){
	//var mobile = /^((1[3|5|7|8][0-9]{1})+\d{8})$/;
    var mobile = /^1\d{10}$/;
	var length = nMobile.length;
	return length == 11 && mobile.test(nMobile);
}

$(document).ready(function(){
	$('#experience-Phone').blur(function(){
		if($(this).val() && isMobile($(this).val())){
			$(this).css('border-color','#ccc');
			$('#experience_phone_error').html('');
		}else if($(this).val() && !isMobile($(this).val())){
			$('#experience_phone_error').html('手机号码格式不正确');
		}else{
			$('#experience_phone_error').html('手机号码格式不正确');
		}
	});

	$('#experience-Name').blur(function(){
		if($(this).val() && /^[\u4E00-\u9FA5]+$/.test($(this).val()) && $(this).val().length>=2 && $(this).val().length<=6){
			$(this).css('border-color','#ccc');
			$('#experience_name_error').html('');
		}else{
			$('#experience_name_error').html('联系名字只能是2-6个中文');
		}
	});

});

function do_save_experience_contact(){
	if(!$('#experience-Phone').val()){
		$('#experience_phone_error').html('请输入您的手机号码');
		$('#experience-Phone').css('border-color','red');
	}else if(!isMobile($('#experience-Phone').val())){
		$('#experience_phone_error').html('手机号码格式不正确');
		$('#experience-Phone').css('border-color','red');
	}

	if(!$('#experience-Name').val()){
		$('#experience_name_error').html('请输入您的联系名字');
		$('#experience-Name').css('border-color','red');
	}

	if(!/^[\u4E00-\u9FA5]+$/.test($('#experience-Name').val())){
		$('#experience_name_error').html('联系名字只能是中文');
		$('#experience-Name').css('border-color','red');
	}

	if($('#experience-Name').val().length<2 || $('#experience-Name').val().length>6){
		$('#experience_name_error').html('联系名字只能是2-6个中文');
		$('#experience-Name').css('border-color','red');
	}

	if(!$('#experience-Phone').val()){
	}else if(!isMobile($('#experience-Phone').val())){
	}else if(!$('#experience-Name').val()){
	}else if(!/^[\u4E00-\u9FA5]+$/.test($('#experience-Name').val())){
	}else if($('#experience-Name').val().length<2 || $('#experience-Name').val().length>6){
	}else{
		$('#experience-Phone,#experience-Name').css('border-color','#ccc');
		$('#experience_phone_error,#experience_name_error').html('');

		$('#experience-submitbutton').css('opacity','0.5').removeAttr('onclick').text('正在提交...');

		$.post("do_feedback.php",
			{m:"contact_add_save", Name: $('#experience-Name').val(),Phone: $('#experience-Phone').val()},
			function(data){
			data = $.trim(data);
				if(data == "ok"){
					$('#experience-submitbutton').text('提交成功').css({'color':'#fff','font-weight':'bold'});
					setTimeout(function(){
						window.location.reload();
					},1000);
				}else{
					alert(data);
					$('#experience-submitbutton').css('opacity','1').attr('onclick','do_save_experience_contact();').text('确定');
				}
			}
		);
	}
}

function do_save_experience_contact2(){
	$('#experience-submitbutton2').css('opacity','0.5').removeAttr('onclick').text('关闭中...');

	$.post("do_feedback.php",
		{m:"contact_add_save2"},
		function(data){
		data = $.trim(data);
			if(data == "ok"){
				$('#experience-submitbutton2').text('关闭中...').css({'color':'#000','font-weight':'bold'});
				setTimeout(function(){
					window.location.reload();
				},1000);
			}else{
				alert(data);
				$('#experience-submitbutton2').css('opacity','1').attr('onclick','do_save_experience_contact2();').text('跳过');
			}
		}
	);
}
</script>
<?php endif;?>
<?php endif;?>

<?php

$sCookieIsset = isset($_COOKIE['experience_isset']) ? ( DHB_RUNTIME_MODE === 'experience' ? getSafeIndustry($_COOKIE['experience_isset']) : $_COOKIE['experience_isset'] ) : '';
$bIsset_excerience = $sCookieIsset ? false : true;

if($bIsset_excerience === true){
	$db = dbconnect::dataconnect()->getdb();
	$arrTryIsset = $db->get_row("select CompanyID FROM ".DATABASEU.DATATABLE."_experience_isset where CompanyID  = '{$_SESSION['uinfo']['ucompany']}' limit 0,1");
	if(!empty($arrTryIsset['CompanyID'])){
		$bIsset_excerience = false;
	}
}

if(DHB_RUNTIME_MODE === 'experience'){
	$bShowIsset = $bIsset_excerience && !$bShowContact && $_SESSION['uinfo']['ucompany'] > 109;
}else{
	$bShowIsset = $bIsset_excerience;
}

if($bShowIsset):
?>

<script type="text/javascript">

/*
$(document).ready(function(){
	$('.quick-modal .other li').each(function(i){
		$(this).mouseover(function(){
			if(i>0){
				$(this).siblings().removeClass('active');
				$(this).addClass('active');
				$('.quick-modal .quick-link').css('display','none');
				$('.quick-modal #quick-link-'+i).css('display','block');
			}
		});
	});
});

function do_save_logincount(strUrl){
	$('.quick-modal .quick-title').text('执行中，请稍后...');

	$.post("do_feedback.php",
		{m:"logincount_add_save"},
		function(data){
			if(data == "ok"){
				window.location.href=strUrl;
			}else{
				alert(data);
			}
		}
	);
}

function do_save_logincount2(){
	$('.quick-modal .quick-title').text('执行中，请稍后...');

	$.post("do_feedback.php",
		{m:"logincount_add_save2"},
		function(data){
			if(data == "ok"){
				window.location.reload();
			}else{
				alert(data);
			}
		}
	);
}

*/
</script>

<style type="text/css" >
body{margin: 0;font-size: 12px;font-family: Microsoft YaHei,SimSun,Arial;}

.clearfix:after{content:'\0020';display:block;height:0;clear:both;visibility:hidden;}
.clearfix{*zoom:1;}

.quick-modal{
	position: fixed;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	bottom: 0;
	font-size: 14px;
	z-index: 999;
}
.quick-modal > div:first-child{
	background-color: #000000;
	opacity: .5;
	filter: alpha(opacity=50);
	height: 100%;
}
.quick-modal > div:first-child + div{
	width: 700px;
	height: 500px;
	position: absolute;
	top: 15%;
	left: 50%;
	background-color: #FFFFFF;
	margin-left: -350px;
	color: rgb(139,139,139);
	-ms-box-shadow: 0px 0px 1em  0 #000000;
	-webkit-box-shadow: 0px 0px 1em  0 #000000;
	-moz-box-shadow: 0px 0px 1em  0 #000000;
	box-shadow: 0px 0px 1em  0 #000000;
}
.quick-modal > div:first-child + div > div{
	padding: 2em;
	text-align: center;
}
.quick-modal h3.quick-title{
	font-size: 22px;
	font-weight: normal;
	margin:0;
	color: #ff4a00;
}
.quick-modal h3.quick-title + p{
	margin: 6px;
	color: rgb(80,80,80);
	font-size: 16px;
}
.quick-modal ul,.quick-modal li{list-style: none;margin:10px 0 0;padding: 0;}
.quick-modal ul li{
	float: left;
	text-align: center;
}
.quick-modal ul li:first-child{
	width: 12%;
	text-align: right;
}
.quick-modal ul li + li{
	width: 29%;
	cursor: pointer;
}
.quick-modal ul.you-want li > p.help-img{
	background: url(img/quick.png) no-repeat;
	height: 60px;
	background-position: 70px 10px;
}
.quick-modal ul.you-want li > p.help-img2{
	background-position: -110px 10px;
}
.quick-modal ul.you-want li > p.help-img3{
	background-position: -272px 10px;
}
.quick-modal ul.other li + li > p{
	margin: 0 auto;
	width: 80%;
	height: 40px;
	line-height: 40px;
	border-radius: 20px;
	border: 1px solid rgb(245, 114, 86);
	cursor: pointer;
}
.quick-modal ul.other li + li > p:hover,.quick-modal ul.other li + li.active > p{
	background-color: rgb(245, 114, 86);
	color: #fff;
}
.quick-modal .quick-link{
	margin-top: 30px;
	height: 70px;
	padding: 10px 40px 40px;
	line-height: 28px;
	text-align: justify;
	background-color: rgb(255,244,242);
	position: relative;
}
.quick-modal div.quick-link > div{
	position: absolute;
	width: 0;
	height: 0;
	font-size: 0;
	top: -40px;
	left: 140px;
	border: 20px solid rgb(255,244,242);
	border-color: transparent transparent rgb(255,244,242) transparent;
}
.quick-modal div.quick-link.quick-link2 > div{
	left: 330px;
}
.quick-modal div.quick-link.quick-link3 > div{
	left: 520px;
}
.quick-modal div.quick-link a{
	color: rgb(63,187,241);
	margin-left: 10px;
}

.quick-modal .quick-modal-delete{
	background: #ccc;
	width: 20px;
	height: 20px;
	display: block;
	color: #fff;
	-moz-border-radius: 15px;
    -webkit-border-radius: 15px;
    border-radius:15px;
	position: relative;
	left: 630px;
	cursor: pointer;
	*left: 320px;
	_left: -320px;
}

.quick-modal .quick-modal-delete:hover{
	background: rgb(245, 114, 86);
}

.quick-modal a.not-callme-again {
	margin: 0 auto;
	padding: 5px 25px;
	line-height: 40px;
	border-radius: 7px;
	border: 1px solid gray;
	cursor: pointer;
	color: gray;
}

.quick-modal a.not-callme-again:hover {
	text-decoration: none;
	border-color: #000;
}
</style>


		</div>
	</div>

<?php endif;?>
