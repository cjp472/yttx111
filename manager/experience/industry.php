<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>立刻开启你的免费体验之旅！</title>
	<link rel="stylesheet" href="css/style.css?_v=<?php echo rand(4000,5000);?>" type="text/css">
	<script src="../scripts/jquery-1.7.2.min.js" type="text/javascript"></script>
</head>
<body>

<?php

require('common.php');
require('../../global/mode.php');

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

/**
 * 测试行业
 */
$sOldIndustry_experience = isset($_GET['industry']) ? trim($_GET['industry']) : '';
$sIndustry_experience = getSafeIndustry($sOldIndustry_experience);
if(empty($sIndustry_experience) || !in_array($sIndustry_experience,$EXPERIENCE_INDUSTRY)){
	echo '<script language="javascript">window.location.href="'.OFFICE_SITE.'";</script>';
}

?>

	<!--[if IE 6]>
	<script type="text/javascript" src="<?php echo OFFICE_SITE;?>/tools/ie6.php?v_=201508031108" />
	<script type="text/javascript"></script>
	<![endif]-->

	<div class="container">
		<div class="header">
			<div class="left float-left">
				<p>
					<img src="images/logo.png" alt="" />
					<span class="current-hy"><?php echo $EXPERIENCE_INDUSTRY_NAME[$sIndustry_experience];?></span>
					<span class="current-hy-change"><a href="http://www.dhb.hk/experience/" title="切换行业">[切换行业]</a></span>
				</p>
				<ul class="clearfix">
					<li onclick="window.open('http://www.dhb.hk/intro/base/m-login.html');" title="操作演示"><span class="icon">&#xe00e;</span> 操作演示</li>
					<li onclick="window.open('http://help.dhb.net.cn/manager.php?s=/Client/Subquestion/index/uid/272.html');" title="视频教学"><span class="icon">&#xe024;</span> 视频教学</li>
					<li onclick="window.open('http://help.dhb.net.cn/manager.php');" title="帮助中心"><span class="icon">&#x74;</span> 帮助中心</li>
					<li title="售前咨询" onclick="window.open('http://wpa.b.qq.com/cgi/wpa.php?ln=2&uin=4006311682');"><span class="icon">&#x76;</span> 售前咨询</li>
				</ul>
			</div>
			<div class="right float-right">
				<ul class="clearfix">
					<li>
						<img src="images/scann.png" alt="" />
					</li>
					<li>
						<img style="vertical-align: middle;" src="images/hot_line.png" alt="" />
					</li>
				</ul>
			</div>
		</div>
		<div class="content clearfix">
			<div class="content-left float-left">
				<a href="<?php echo makeAuthUrl('manager', $sOldIndustry_experience);?>" title="管理端登录">
					<div class="login-enter">
						<p class="float-left"><img src="images/bm.png" alt="" /></p>
						<div class="float-right last">
							<h4 class="login-title">管理端登录</h4>
							<span>供应商通过管理端管理商品、订单、财务与客户</span>
						</div>
					</div>
				</a>
				<div class="feature"><strong>管理端能做什么 ?</strong></div>
				<div class="do-what clearfix">
					<div class="float-left"><img src="images/one.png" alt="" /></div>
					<div class="float-right last">
						<h3>发布我的商品</h3>
						<p>多图、多规格、价格根据客户不同可使用等级价或指定，针对某些经销商屏蔽某些商品，支持批量导入，还能进行自定义扩展，让商品直观准确呈现在客户面前</p>
					</div>
				</div>

				<div class="do-what clearfix">
					<div class="float-left"><img src="images/two.png" alt="" /></div>
					<div class="float-right last">
						<h3>管理我的订单流程</h3>
						<p>从接单、审核、付款、发货、物流到完结乃至退货，全流程在线高效管理订单，处理过程可追溯，效率更高更准确。</p>
					</div>
				</div>

				<div class="do-what clearfix">
					<div class="float-left"><img src="images/three.png" alt="" /></div>
					<div class="float-right last">
						<h3>管理我的客户与业务员</h3>
						<p>您可以将自己的客户进行分类，并为每个分类设置折扣等级，将客户指定给特定的业务员进行精准管理。同时还可以管理业务员的提成。</p>
					</div>
				</div>

				<div class="do-what clearfix">
					<div class="float-left"><img src="images/four.png" alt="" /></div>
					<div class="float-right last">
						<h3>做帐、对账，了解货款信息</h3>
						<p>系统有效地做到了订单与货款的一一对应，支持款项。</p>
					</div>
				</div>
			</div>
			<div class="middle-line"></div>
			<div class="content-right float-right">
				<a href="<?php echo makeAuthUrl('client', $sOldIndustry_experience);?>" title="订货端登录">
					<div class="login-enter">
						<p class="float-left"><img src="images/dhd.png" alt="" /></p>
						<div class="float-right last">
							<h4 class="login-title">订货端登录</h4>
							<span>订货方通过订货端向上游订货并跟踪管理自己的订单</span>
						</div>
					</div>
				</a>
				<div class="feature"><strong>订货端能做什么 ?</strong></div>
				<div class="do-what clearfix">
					<div class="float-left"><img src="images/one-r.png" alt="" /></div>
					<div class="float-right last">
						<h3>企业专属订货商城</h3>
						<p>供应商可以为自己的订货方建立自己专属的登录界面及订货商城，可以在登录界面及商城中体现自己的企业或品牌信息，可以对接自己的在线客服。</p>
					</div>
				</div>

				<div class="do-what clearfix">
					<div class="float-left"><img src="images/two-r.png" alt="" /></div>
					<div class="float-right last">
						<h3>像网上购物一样订货</h3>
						<p>订货端订货前台采用商城模式，订货犹如网上购物一样直观方便，大大提升了订货效率，避免了误差。<br /><br /></p>
					</div>
				</div>

				<div class="do-what clearfix">
					<div class="float-left"><img src="images/three-r.png" alt="" /></div>
					<div class="float-right last">
						<h3>在线付款及更多支付方式</h3>
						<p>除了转账、汇款、POS刷卡等线下支付方式，订货宝也支持通过银行卡、网银、支付宝等在线支付方式。<br /><br /></p>
					</div>
				</div>

				<div class="do-what clearfix">
					<div class="float-left"><img src="images/four-r.png" alt="" /></div>
					<div class="float-right last">
						<h3>跟踪订单，查看货物运到哪儿了</h3>
						<p>订单的审核流程全程双方透明可查看，供应商发货后即可在系统内查看到货物的物流信息，随时随地做到心中有数。</p>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="more" style="cursor:pointer;" title="查看更多功能亮点" onclick="window.open('http://www.dhb.hk/site/TypicalFeatures/');">更多功能 >></div>
		</div>
		<div class="footer">
			<p>也许你还需要：</p>
			<ul class="clearfix">
				<li onclick="window.open('http://www.dhb.hk/dhbpay/');" title="开通在线支付">
					<p><img src="images/pay.png" alt="" /></p>
					<p>开通在线支付</p>
				</li>
				<li onclick="window.open('http://m.dhb.hk/pro/erp_info.php');" title="对接ERP系统">
					<p><img src="images/erp.png" alt="" /></p>
					<p>对接ERP系统</p>
				</li>
				<li onclick="window.open('http://m.dhb.hk/pro/weixin_info.php');" title="部署专属微信 订货系统">
					<p><img src="images/chat.png" alt="" /></p>
					<p>部署专属微信 订货系统 </p>
				</li>
				<li class="last" style="cursor:default;" title="获信用贷款">
					<p style="line-height: 4em;text-align:bottom;">或想了解</p>
					<p style="width:500px;">如何凭订货宝系统交易记录 <em>获信用贷款？</em> 请咨询 400-6311-682。</p>
				</li>
			</ul>
		</div>
	</div>

	<script type="text/javascript">
		// IE8+ support
//		var obj = document.querySelectorAll('.login-enter');
		var obj = $('.login-enter');
		
		

		if(obj){
			for (var i = 0,len = obj.length; i < len; i += 1) {
				obj[i].onmouseover = function(){
					this.getElementsByTagName('h4')[0].style.color = '#FFFFFF';
				};
				obj[i].onmouseout = function(){
					//this.getElementsByTagName('h4')[0].style.color = '';
					this.getElementsByTagName('h4')[0].removeAttribute('style');
				};
			}
		}
	
	</script>
<div style="display:none;">
<script language="javascript" src="http://count36.51yes.com/click.aspx?id=361587363&logo=12" charset="gb2312"></script>
</div>
</body>
</html>