<?php
$menu_flag = "system";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 网银在线支付开通说明</title>
    <link rel="stylesheet" href="css/main.css" type="text/css"/>
    <link href="img/explanations/css/main.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .location a,.bodyline a{display:inline-block;}
        div{font-size:14px;color:#606060;}
    </style>
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function(){
            $(document).click(function(){
                $("#image_cache img").hide();
            });
            $(".pay_chose>a").click(function(){
                var tg = $(".zf_info2[data-content='Pay_"+(parseInt($(this).index())+1)+"']");
                tg.show().siblings(".zf_info2").hide();
                $("html,body").animate({scrollTop:tg.offset().top-50},100);
                //$(".sxcl_info").hide();
            });


            $(".sxcl").click(function(){
                $("html,body").animate({scrollTop:$(".sxcl_info").offset().top-50},100);
            });

            $(".sxcl_title>a").click(function(){
                $("#image_cache").find("img").hide();
                if($(this).hasClass('sxcl_hover1')){
                    $(this).addClass('sxcl_hover').removeClass('sxcl_hover1');
                    $(".sxcl_info1").hide();
                }else{
                    $(this).addClass('sxcl_hover1').siblings("a").removeClass("sxcl_hover1").addClass('sxcl_hover');
                    $(".sxcl_info1:eq("+$(this).index()+")").show().siblings(".sxcl_info1").hide();
                }
            });

            $("a[data-act='alipay_docking_btn']").click(function(){
                $("div[data-content='alipay_docking']").toggle();
            });

            $("a[data-pop]").mouseover(function(){
                var _this = $(this);
                var idx = $(this).index("a");
                var id = "pop_"+idx;
                if($("#image_cache").find("#pop_"+idx).length>0){

                }else{
                    $("#image_cache").append("<img src='"+$(this).data('pop')+"' id='pop_"+idx+"' style='display:none;' title='点击关闭图片' alt='点击关闭图片' />");
                }
                $("#"+id).get(0).onload = function(){
                    var h = $("#"+id).height();
                    var w = $("#"+id).width();

                    $("#"+id).css({
                        'position':'absolute',
                        'right':($(window).width()-1200)/2 + 50,
                        'top':($(window).height()-h)/2 + $(window).scrollTop()+60
                    });

                    $("#"+id).show().siblings("img").hide();

                };
                $("#"+id).css({
                    'top':($(window).height()-$("#"+id).height())/2 + $(window).scrollTop()+60
                });
                $("#"+id).show().siblings("img").hide();
            });

            $("#image_cache img").live('click',function(){
                $(this).hide();
            });
        });
    </script>
</head>

<body>
<?php include_once ("top.php");?>
<div class="bodyline" style="height:25px;"></div>
<div class="bodyline" style="height:32px;">
    <div class="leftdiv" style="margin-top:8px; padding-left:12px;"><span><h4><?php echo $_SESSION['uc']['CompanyName'];?></h4></span><span valign="bottom">&nbsp;&nbsp;<? echo $_SESSION['uinfo']['usertruename']."(".$_SESSION['uinfo']['username'].")";?> 欢迎您！</span>&nbsp;&nbsp;<span>[<a href="change_pass.php">修改密码</a>]</span>&nbsp;&nbsp;<span>[<a href="do_login.php?m=logout">退出系统</a>]</span></div>
    <div class="rightdiv">
        <span><img src="img/menu2_left.jpg" /></span>
            <span id="menu2">
            	<ul>
                    <li ><a href="home.php">系统首页</a></li>
                    <li ><a href="changelog.php" target="_blank">系统公告</a></li>
                    <li ><a href="help.php" target="_blank">操作指南</a></li>
                </ul>
            </span>
        <span><img src="img/menu2_right.jpg" /></span>
    </div>
</div>

<div class="bodyline" style="height:70px; background-image:url(img/bodyline_bg.jpg);">
    <div class="leftdiv"><img src="img/blue_left.jpg" /></div>
    <div class="rightdiv" style="color:#ffffff; padding-right:20px; padding-top:40px;">欢迎您使用 订货宝 订货管理系统</div>
</div>

<div id="bodycontent">
    <div class="lineblank"></div>
<h1 class="zftltle">在线支付开通说明</h1>
<h1>选择您要了解的第三方支付平台：</h1>
<div class="pay_chose">
    <a class="pay_chose_1"><div class="zf_0 zf_1"></div></a>
    <a class="pay_chose_2"><div class="zf_0 zf_2"></div></a>
    <a class="pay_chose_3"><div class="zf_0 zf_3"></div></a>
    <div style=" clear:both"></div>
</div>

<div class="zxcell">
    <p style="margin:0;line-height:30px;font-size:14px;">咨询电话：<span style="font-size:14px;">400-6311-682</span></p>
    <a href="tencent://message/?uin=1730407198&Site=www.dhb.hk&Menu=yes">
    	<img src="img/explanations/images/QQ.jpg" width="77" height="22" />
    </a>
</div>

<div class="zf_info">

    <!--通联支付开始-->
    <div class="zf_info2" style="display:none;" data-content="Pay_1">
        <h1>申请条件</h1>
        1. 已正式使用订货宝产品在线版或独立版的所有企业用户；<br />

        2. 您需确保提交的材料真实合法，申请提交后通联支付将会进行商户资料的资质审核
        <br /><br />
        <h1>办理方式</h1>
        1. 您提交资料给订货宝，订货宝帮助用户与通联建立合作；<br />

        2. 订货宝帮助您将通联在线支付功能集成到订货宝系统。
        <br /><br />
        <h1>收费标准</h1>
        1. 银行卡支付：收款金额的0.5%<br />

        2. 信用卡支付：收款金额的0.5%<br />
        3. 企业网银支付：20元/笔<br />
        备注：<br />
        <p style=" text-indent:2em; height:14px;"> A、交易费用由第三方支付公司直接收取，订货宝不收任何费用。</p>

        <p style=" text-indent:2em; height:14px;"> B、手续费可选择由收款方（您）支付或者付款方（您的客户）支付。</p>
        <br/>
        <h1>结算周期</h1>
        1. 结算周期为T+1模式，系指：交易日（即T日）后的第1个工作日，通联支付将该交易日所发生的交易资金，结算至用户指
        定的银行账户中。<br/>
        2. 如果用户在一个结算周期中所发生的交易资金少于人民币1000元的，则将该交易资金并入下一结算周期一并结算。<br/>
        3. 小于人民币1000元要求该周期立即结算，划款费用为人民币1元/笔。<br/>
        <br/>
        <h1>通联简介</h1>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;通联支付是国内规模最大的第三方支付服务公司，是第一批通过央行在公司资质、系统、风控、业务等多方面审核的支付机构之一，获取全业务牌照（银行卡、网络支付、预付卡、货币汇兑、电话支付）。全国拥有35家分子公司，并设立300余家地市级分支机构，全国员工4000余人。可提供持续的全国性服务。是中国支付清算协会非金融支付服务机构中的唯一一家副会长单位。其它副会长单位分别为央行、大型商业银行与央行特许清算机构。
        <a href="http://www.allinpay.com/" target="_blank">详细了解通联支付</a><br/><br/>
        <a data-pop="img/explanations/images/业务许可证.png" style="color:#f00;font-size:14px;margin-right:100px;">通联支付牌照</a>
        <a data-pop="img/explanations/images/副会长证书.png" style="color:#f00;font-size:14px;">支付协会副会长证书</a>
        <div class="btn_info">
            <a class="btn ljkt" data-act="立即开通" data-down>立即开通</a>
            <div class="telphone"></div>
            <a class="QQ1" href="tencent://message/?uin=2261915847&Site=www.dhb.hk&Menu=yes" style=" margin-left:50px;"></a>
            <!--<a class="btn sqlc">申请流程</a>
            <a class="btn sxcl">所需材料</a>-->
        </div>
        <hr style="border:1px solid #ccc;border-top:none;"/>
        <h1>开通所需材料:</h1>
        <div class="lc_img" style="display:none;"><img src="img/explanations/images/lc.jpg" width="473" height="802"/></div>
        <div class="sxcl_info">
            <div class="sxcl_title">
                <a class="sxcl_hover">您用公司账号开通</a>
                <a class="sxcl_hover">您用私人账号开通</a>
                <a class="sxcl_hover">开通流程</a>
            </div>
            <div class="sxcl_info1" style="display:none;">
                <h1>签署合同协议</h1>
                <div class="sxcl_line"><p>1.《网上支付接入及结算服务协议》（需签署四份）</p> <a data-pop="img/explanations/images/商户信息（收款方支付）.jpg">收款方支付示例</a> <a data-pop="img/explanations/images/商户信息（付款方支付）.jpg">付款方支付示例</a></div>
                <div class="sxcl_line"><p>2.《通联网上支付商户申请表》</p>  <a data-pop="img/explanations/images/申请表（收款方支付）.jpg">收款方支付示例</a> <a data-pop="img/explanations/images/申请表（付款方支付）.jpg">付款方支付示例</a></div>
                <div class="sxcl_line"> <a href="download.php?m=accounts&s=company" style="color:#277DB7;" data-href="http://resource.dhb.hk/file/dhb_allinpay.rar">下载《企业信息表》 >></a></div>
                <div class="sxcl_line"> <span style="color:red;font-size:14px;">(下载并填写《企业信息表》,填写回传后,我们的工作人员会帮您填写合同协议的签署项)</span></div>
                <br /><br />
                <h1>企业资质文件</h1>
                <div class="sxcl_line"><p>1.营业执照副本</p> <a data-pop="img/explanations/images/营业执照（副本）.jpg">示例</a><br /></div>
                <div class="sxcl_line"><p>2.企业税务登记证副本复印件</p>  <a data-pop="img/explanations/images/税务登记证（国税）副本.jpg">国税示例</a> <a data-pop="img/explanations/images/税务登记证（地税）副本.jpg">地税示例</a></div>
                <div class="sxcl_line"><p>3.企业组织机构代码证复印件（若商户类型为个体户，则无需提供）</p> <a data-pop="img/explanations/images/组织机构代码证.jpg">示例</a></div>
                <div class="sxcl_line"><p>4.机构信用代码证（若商户类型为个体户，则无需提供）</p> <a data-pop="img/explanations/images/机构信用代码证.jpg">示例</a></div>
                <div class="sxcl_line"><p>5.基本户开户许可证（若商户类型为个体户，则无需提供）</p> <a data-pop="img/explanations/images/基本开户许可证.jpg">示例</a></div>
                <div class="sxcl_line"><p>6.法人代表身份证复印件（正反两面）</p> <a data-pop="img/explanations/images/身份证复印件.jpg">示例</a></div>
                <div class="sxcl_line"> <span style="font-size:14px;color:red;">（以上复印件均需加盖公章）</span></div>
                <div class="btn_info">
                    <a class="btn ljkt" data-act="立即开通" data-down style=" font-size:18px; line-height:55px; color:#fff">立即开通</a>
                    <div class="telphone"></div>
                    <a class="QQ1" href="tencent://message/?uin=2261915847&Site=www.dhb.hk&Menu=yes" style=" margin-left:50px;"></a>
                </div>
                <h1>特别提示：</h1>
                <div class="sxcl_line"><p>1.提交的所有企业资质文件</p> <span style="font-size:14px;color:red;line-height:20px;">&nbsp;都需要加盖公司公章。</span><br /></div>
                <div class="sxcl_line"><p>2.《网上支付接入及结算服务协议》（即合同）需要签署四份。</p>  <br /></div>
                <div class="sxcl_line"><p>3.合同请盖骑缝章。</p> <a data-pop="img/explanations/images/骑缝章.jpg">示例</a><br /></div>
            </div>
            <div class="sxcl_info1" style="display:none;">
                <h1>签署合同协议</h1>
                <div class="sxcl_line"><p>1.《网上支付接入及结算服务协议》（需签署四份）</p> <a data-pop="img/explanations/images/商户信息（收款方支付）.jpg">收款方支付示例</a> <a data-pop="img/explanations/images/商户信息（付款方支付）.jpg">付款方支付示例</a></div>
                <div class="sxcl_line"><p>2.《通联网上支付商户申请表》</p>  <a data-pop="img/explanations/images/申请表（收款方支付）.jpg">收款方支付示例</a> <a data-pop="img/explanations/images/申请表（付款方支付）.jpg">付款方支付示例</a></div>
                <div class="sxcl_line"><p>3.《结算账户委托授权书》</p> <a data-pop="img/explanations/images/授权书.jpg">示例</a></div>
                <div class="sxcl_line"> <a href="download.php?m=accounts&s=single" style="color:#277DB7;" data-href="http://resource.dhb.hk/file/dhb_allinpay_p.rar">下载合同协议 >></a><br /></div>
                <div class="sxcl_line"> <span style="color:red;font-size:14px;">(下载并填写《企业信息表》,填写回传后,我们的工作人员会帮您填写合同协议的签署项)</span></div>
                <br /><br />
                <h1>企业资质文件</h1>
                <div class="sxcl_line"><p>1.营业执照副本</p> <a data-pop="img/explanations/images/营业执照（副本）.jpg">示例</a><br /></div>
                <div class="sxcl_line"><p>2.企业税务登记证副本复印件</p>  <a data-pop="img/explanations/images/税务登记证（国税）副本.jpg">国税示例</a> <a data-pop="img/explanations/images/税务登记证（地税）副本.jpg">地税示例</a></div>
                <div class="sxcl_line"><p>3.企业组织机构代码证复印件（若商户类型为个体户，则无需提供）</p> <a data-pop="img/explanations/images/组织机构代码证.jpg">示例</a></div>
                <div class="sxcl_line"><p>4.机构信用代码证（若商户类型为个体户，则无需提供）</p> <a data-pop="img/explanations/images/机构信用代码证.jpg">示例</a></div>
                <div class="sxcl_line"><p>5.基本户开户许可证（若商户类型为个体户，则无需提供）</p> <a data-pop="img/explanations/images/基本开户许可证.jpg">示例</a></div>
                <div class="sxcl_line"><p>6.法人代表身份证复印件（正反两面）</p> <a data-pop="img/explanations/images/身份证复印件.jpg">示例</a></div>
                <div class="sxcl_line"><p>7.对私银行卡复印件(正反面)</p> <a data-pop="img/explanations/images/银行卡正反面.jpg">示例</a></div>
                <div class="sxcl_line"> <span style="color:red;font-size:14px;">(以上复印件均需加盖公章)</span><br /></div>
                <div class="btn_info">
                    <a class="btn ljkt" data-act="立即开通" data-down style=" font-size:18px; line-height:55px; color:#fff">立即开通</a>
                    <div class="telphone"></div>
                    <a class="QQ1" href="tencent://message/?uin=2261915847&Site=www.dhb.hk&Menu=yes" style=" margin-left:50px;"></a>
                </div>
                <h1>特别提示：</h1>
                <div class="sxcl_line"><p>1.提交的所有企业资质文件</p> <span style="font-size:14px;color:red;line-height:20px;">&nbsp;都需要加盖公司公章。</span><br /></div>
                <div class="sxcl_line"><p>2.《网上支付接入及结算服务协议》（即合同）需要签署四份。</p>  <br /></div>
                <div class="sxcl_line"><p>3.合同请盖骑缝章。</p> <a data-pop="img/explanations/images/骑缝章.jpg">示例</a><br /></div>
                <div class="sxcl_line" style="height:60px; line-height:30px;"><p>4.结算的私人账户必须为法人账户，如非法人账户，需提交该账户银行卡、所有人身份证及能证明私人账户所有者与企业

                        关系的材料。<br />如股东提供股权证明、法人配偶提供结婚证、员工高管提供聘用合同等。</p></div>
            </div>
            <div class="sxcl_info1" style="display:none;">
                <div class="lc_img"><img src="img/explanations/images/lc.jpg" width="473" height="802"/></div>
            </div>

        </div>
    </div>
    <!--支付宝开始-->
    <div class="zf_info2" style="display:none;" data-content="Pay_2">
        <h1>申请条件</h1>
        1. 您必须有已建设完成的网站（非淘宝网店），且经营的商品或服务内容明确、完整；<br />

        2. 您申请前必须拥有支付宝账号，且通过支付宝实名认证审核；<br />
        3. 您需确保提交的材料真实合法，申请提交后支付宝将会进行商户资料的资质审核。
        <br /><br />
        <h1>办理方式</h1>
        1. 您与支付宝进行对接，开通在线支付帐号；<br />

        2. 订货宝帮助您将支付宝在线支付功能集成到订货宝系统。
        <br /><br />
        <h1>收费标准</h1>
        1.单笔阶梯费率<br /><br />
        <img src="img/explanations/images/1iBXcjky5R.png" width="781" height="200" /><br />
        阶梯费率算法示例：<b>如您的交易量为12万，则您的费用为6万*1.2%+（12 万-6万）*1.0%=0.132万元。</b><br /> <br />
        2.包量套餐费率<br /><br />
        <img src="img/explanations/images/1iBXY8bTJZ.png" width="781" height="135" /><br /><br />
        <h1>结算周期</h1>
        1. 即时到帐：款项直接到目标用户的支付宝账户；<br />

        2. 担保交易：款项先存入支付宝，付款方确认后，再划入目标用户支付宝账户；<br />
        3. 支付宝转到银行卡提现需次日到账或者2小时到账。<a href="https://cshall.alipay.com/lab/help_detail.htm?help_id=248394" target="_blank">了解详情>></a><br />
        <div class="btn_info">
            <div class="ktzfub">
                <a class="kt_zfb kt_zfb1" href="https://b.alipay.com/order/productIndex.htm" target="_blank"></a>
            </div>
            <div class="ktzfub">
                <a class="kt_zfb kt_zfb2" data-act="alipay_docking_btn"></a>
            </div>

        </div>
        <div class="sxcl_info" style="display:none;" data-content="alipay_docking">
            <br />
            1.登录订货宝“管理端”，进入“系统选项”，选择“收款账号设置”栏目，点击右上角的“新增收款账号”按钮进行新增账号。
            备注：只有主账号能设置该功能，子账号不能设置该栏目。<br /><br />
            <img src="img/explanations/images/help1.jpg" width="1000"  height="528" /><br /><br /><br />
            2.填写您与支付宝签订在线支付协议的支付宝账号信息，并保存退出。<br /><br />
            <img src="img/explanations/images/help2.jpg" width="1000"  height="475" /><br /><br /><br />
            3.此时回到系统列表页面，选择列表右侧“管理”项目下的按钮，对账号进行编辑。<br /><br />
            <img src="img/explanations/images/help3.jpg" width="1000"  height="539" /><br /><br /><br />
            4.进入账号编辑页面后，在“支付类型”选项卡中，选择“支付宝在线支付”，在弹出的栏目中，选择签约类型并填写您与支付宝的签约信息，然后保存。<br /><br />
            <img src="img/explanations/images/help4.jpg" width="1000"  height="604" /><br /><br /><br />
            5.如果您的信息填写正确，立即就能开始使用支付宝在线支付功能。建议您在实际使用之前，先进行测试（用其他账号通过订货宝平台支付一小笔款项，看收款是否正常），以免信息填写错误发生风险。
            <br /><br />
        </div>
    </div>
    
    <!-- 易极付开始 -->
    <div class="zf_info2" style="display:none;" data-content="Pay_3">
        <h1>易极付在线支付的优势</h1>
        
        1. 费用低：药店付款手续费更低，省时省力更省钱<br />
        
		2. 更安全：药店每一笔付款都是定向支付给您，安全有保障<br />
		
		3. 可融资：基于支付交易流水，可以为您及您的药店提供纯信用的贷款<br />
		
		4. 一体化：客户在订货宝平台下单后可立即通过电脑或手机支付订货款，方便快捷<br />

		5. 收款快：药店通过电脑或手机可以跨行、跨省、7*24小时给您付款，款项实
        时到帐，您也可以随时随地查询资金到帐情况 

        <br /><br />
        <h1>收费标准</h1>
        1. 支付:&nbsp;&nbsp;&nbsp;&nbsp;2元／笔<br /><br />
        
        2. 提现<br />
        &nbsp;&nbsp;&nbsp;&nbsp;到帐时间：<u>Ｔ＋1</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;免费<br />
        &nbsp;&nbsp;&nbsp;&nbsp;到帐时间：<u>Ｔ＋0</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;10元／笔

        <br /><br />
        <h1>易极付公司介绍</h1>
        重庆易极付科技有限公司(下称易极付)是重庆博恩集团的下属子公司，是重庆地区第一家具有互联网支付牌照的互联网金融企业, 同时也是西南地区唯一一家跨境外汇 支付牌照 (全国共 1 7 家 )、 跨境人民币支付牌照(全国共计 4 家)，重庆唯一一张基金支付牌照(全国共计 24 家)。注册资本 1 亿元人民币，2014年全年支付流量超过 10000 亿元，是中国中西部第一、全国前七的第三方支付公司。<br><br>

		易极付母公司重庆博恩集团是中国中西部最大的网络金融服务集团，旗下拥有全球最大的威客网—猪八戒网，中国中西部最大的第三方支付公司--易极付，中国国内投资成功率最高的天使投资机构—易一天使投资公司。博恩集团同时是重庆第一家民营银行的主要发起股东，重庆第一家征信公司的主要发起股东，重庆第一家网络金融公司（易九金融）的主要发起股东，董事长熊新翔先生是2013、2014年连续两年重庆市十大经济人物，是2014年重庆市首届十大金融创新人物的榜首。<br/><br/>
		
		重庆博恩集团官网：<a href="http://www.borncn.com/mainIndex.htm" target="_blank" title="点击前往">http://www.borncn.com/mainIndex.htm</a><br/><br/>
		中国人民银行支付牌照网址：<a href="http://www.borncn.com/mainIndex.htm" target="_blank" title="点击前往">http://www.pbc.gov.cn/publish/zhengwugongkai/3580/2011/20111231150938684742652/20111231150938684742652_.html</a>
<br>


    	<div><br><br>
<!--    		<a class="btn ljkt center" href="YJFopenApi.php?type=setAccount" target="_blank" style="text-decoration:none;float:none;">易极付服务电话：023-65937137</a>-->
<a style="color:#f00;font-size:14px;margin-right:100px;" data-pop="img/explanations/images/yijifuCert.jpg">易极付支付牌照</a>
    		<div class="yijifutelphone"></div>
    		<div class="clear"></div>
    	</div>
     
    
    
    </div>

    <div class="zf_info1" style="padding-left:20px;">
        <h1>开通说明：</h1>
        1.      用户在订货宝平台使用在线支付，需要先开通易极付、通联支付或支付宝第三方支付帐号；<br />

        2.      在线支付开通订货宝不收取任何费用，使用过程中的费率由第三方支付公司收取；<br />

        3.     订货宝与易极付、通联支付是战略合作伙伴，可以帮助用户申请开通通联在线支付功能，并免费帮助用户与订货宝对接；<br />

        &nbsp;&nbsp;&nbsp;&nbsp;订货宝在线支付需用户自行开通账户，订货宝方可以帮助用户免费与订货宝对接；<br />

        4.     如有任何问题，请与我们的客服取得联系，我们将竭诚为您服务，联系电话 400-6311-682<br />


    </div>

</div>
</div>

<div id="image_cache">
</div>

<div id="mask" class="xftx" style="height:100%;width:100%;position:fixed;background:#000;opacity:.8;top:0px;z-index:9999999;display:none;">
</div>
<div class="xftx" id="xftx_content" style="width:490px;height:250px;background:#fff;margin:0px auto;border-radius:3px;z-index:999999990;position:absolute;top:0px;display:none;">
    <div style="border-bottom:1px solid #ccc;background:rgb(106,158,218);height:45px;line-height:45px;margin-bottom:0px;">
        <h2 style="color:#fff;text-align:center;font-weight:150;margin:0px;font-family:微软雅黑;">开通在线支付</h2>
    </div>
    <div style="height:130px;margin:0px;">
        <p style="padding-top:10px;text-align:center;font-family:微软雅黑;font-weight:none;font-size:16px;">
            请下载并填写《商户信息表》,发送至dhb@rsung.com进行开通申请。或者请与我们的客服取得联系：<font style="color:#f60;font-size:18px;font-weight:bold;">400-6311-682</font>
           </p>
    </div>
    <div style="text-align:center;">
        <button onclick="window.location.href='download.php?m=accounts&s=company';" style="font-family:微软雅黑;cursor:pointer;width:140px;height:40px;background:rgb(106,158,218);border:none;border-radius: 3px;color:#fff;font-size:14px;margin-right:10px;">立即下载</button>
        <button onclick="$('.xftx').hide();" style="font-family:微软雅黑;cursor:pointer;width:140px;height:40px;background:#f5f5f5;border:1px solid #ccc;border-radius: 3px;color:#000;font-size:14px;box-shadow:inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);">确定</button>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $("*[data-down]").click(function(){
            $("#xftx_content").css({
                'top':($(window).height() - $("#xftx_content").height())/2 + $(window).scrollTop(),
                'left':($(window).width()-$("#xftx_content").width())/2
            });
            $(".xftx").show();
        });
    });
</script>
</body>
</html>
