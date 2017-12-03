<?php
// header('location:http://www.dhb.hk/experience/');
// exit();
require('common.php');
require('../../global/mode.php');

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

$nIframe = isset($_GET['iframe']) ? trim($_GET['iframe']) : '';

function makeHyMenu($nID){
	global $nIframe,$EXPERIENCE_INDUSTRY;
	return ($nIframe ==1 ? 'parent.' : '' )."window.location.href='".makeUrl($EXPERIENCE_INDUSTRY[$nID])."';";
}

?>

<!doctype html>
<html lang="en" style="height:100%">
<head>
	<meta charset="UTF-8">
	<title>请选择行业</title>
	<style type="text/css">
		
		body{margin: 0;font-size: 12px;font-family: Microsoft YaHei,SimSun,Arial; background-color:#64596f; background-image:url(images/hy-bg.jpg); background-position:center center; background-repeat:no-repeat;}
		
		.clearfix:after{content:'\0020';display:block;height:0;clear:both;visibility:hidden;}
		.clearfix{*zoom:1;}

		.choice-modal{
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			bottom: 0;	
			font-size: 14px;
		}
		.choice-modal > div:first-child{
			opacity: .5;
			filter: alpha(opacity=50); 
			height: 100%;
		}
		.choice-modal > div:first-child + div{
			width: 801px;
			height: 440px;
			position: absolute;
			top: 50%;
			left: 50%;
			background-color: #FFFFFF;
			margin-left: -28.57143em;
			margin-top: -14.64285em;
			
		}
		.choice-modal > div:first-child + div > div{
			padding: 2em;
		}
		.choice-modal h3.choice-title{
			font-size: 1.5em;
			font-weight: normal;
			margin:0;
			color: #ff4a00;
			text-align: center;
		}
		.choice-modal ul{
			list-style: none;
			padding: 0;
			margin-left:-0px;
			text-align: center;
			border: 1px solid #ededed;
			border-bottom: none;
			border-right: none;
		}
		.choice-modal ul > li{
			float: left;
			width: 123px;
			height:150px;
			border-right: 1px solid #ededed;
			border-bottom: 1px solid #ededed;
			padding-top: 1.2em;
			display:block;
			cursor: pointer;
		}
		.choice-modal ul > li{}
		.choice-modal ul > li:hover{background:#fff;color:#ff4a00; border:1px solid #ff5c1a; width:122px; height:149px}
		.choice-modal ul > li img{height: 3.64286em;}

		.choice-modal ul > li img{height: 3.64286em;}
		
		.choice-modal ul > li  p > span{
		    display: inline-block;
		    height: 4em;
		    width: 4em;
		    background: url(images/choice.png) no-repeat;
		}
		.choice-modal ul > li.trade2  p > span{
		    background-position: 0 -8.4em;
		}
		.choice-modal ul > li.trade3  p > span{
		    background-position: -.4em -17em;
		}
		.choice-modal ul > li.trade4  p > span{
		    background-position: 0 -25.4em;
		}
		.choice-modal ul > li.trade5  p > span{
		    background-position: 0 -34.2em;
		}
		.choice-modal ul > li.trade6  p > span{
		    background-position: 0 -43em;
		}
		.choice-modal ul > li.trade7  p > span{
		    background-position: -7.4em 0em;
		}
		.choice-modal ul > li.trade8  p > span{
		    background-position: -7.4em -8.8em;
		}
		.choice-modal ul > li.trade9  p > span{
		    background-position: -7.4em -17.4em;
		}
		.choice-modal ul > li.trade10  p > span{
		    background-position: -7.4em -25.6em;
		}
		.choice-modal ul > li.trade11  p > span{
		    background-position: -7.4em -34.6em;
		}
		.choice-modal ul > li.trade12  p > span{
		    background-position: -7.4em -43.3em;
		}
	</style>
</head>
<body  style="height:100%; overflow:hidden;">
	<!--[if IE 6]>
	<script type="text/javascript" src="<?php echo OFFICE_SITE;?>/tools/ie6.php?v_=201508031108" />
	<script type="text/javascript"></script>
	<![endif]-->

	<div class="choice-modal">
		<div></div>
		<div>
			<div>
				<h3 class="choice-title">选择你的行业</h3>
				<ul class="clearfix">
					<li onclick="<?php echo makeHyMenu(6);?>" title="数码电器" style="cursor:pointer;">
						<p><span></span></p>
						<p>数码电器</p>
					</li>
					<li class="trade2" onclick="<?php echo makeHyMenu(7);?>" title="服装服饰" style="cursor:pointer;">
						<p><span></span></p>
						<p>服装服饰</p>
					</li>
					<li class="trade3" onclick="<?php echo makeHyMenu(1);?>" title="汽车用品、配件" style="cursor:pointer;">
						<p><span></span></p>
						<p>汽车用品、配件</p>
					</li>
					<li class="trade4" onclick="<?php echo makeHyMenu(3);?>" title="食品饮料酒水" style="cursor:pointer;">
						<p><span></span></p>
						<p>食品饮料酒水</p>
					</li>
					<li class="trade5" onclick="<?php echo makeHyMenu(5);?>" title="餐饮连锁" style="cursor:pointer;">
						<p><span></span></p>
						<p>餐饮连锁</p>
					</li>
					<li class="trade6" onclick="<?php echo makeHyMenu(10);?>" title="医药保健" style="cursor:pointer;">
						<p><span></span></p>
						<p>医药保健</p>
					</li>
					<li class="trade7" onclick="<?php echo makeHyMenu(8);?>" title="鞋靴箱包配饰" style="cursor:pointer;">
						<p><span></span></p>
						<p>鞋靴箱包配饰</p>
					</li>
					<li class="trade8" onclick="<?php echo makeHyMenu(0);?>" title="个护化妆" style="cursor:pointer;">
						<p><span></span></p>
						<p>个护化妆</p>
					</li>
					<li class="trade9" onclick="<?php echo makeHyMenu(2);?>" title="婴童用品" style="cursor:pointer;">
						<p><span></span></p>
						<p>婴童用品</p>
					</li>
					<li class="trade10" onclick="<?php echo makeHyMenu(4);?>" title="日用百货" style="cursor:pointer;">
						<p><span></span></p>
						<p>日用百货</p>
					</li>
					<li class="trade11" onclick="<?php echo makeHyMenu(9);?>" title="家居家纺" style="cursor:pointer;">
						<p><span></span></p>
						<p>家居家纺</p>
					</li>
					<li class=" trade12" onclick="<?php echo makeHyMenu(11);?>" title="其他行业" style="cursor:pointer;">
						<p><span></span></p>
						<p>其他行业</p>
					</li>
				</ul>
			</div>
		</div>
	</div>
<div style="display:none;">
<script language="javascript" src="http://count36.51yes.com/click.aspx?id=361587363&logo=12" charset="gb2312"></script>
</div>
</body>
</html>