<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>医统天下 网上订货系统 - 管理平台</title>
    <link href="css/main.css?v=20150831" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/showpage.css" />

    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script src="js/system.js?v=20150831" type="text/javascript"></script>
        <style>
        .allArea{
            width: 150px;
            height: 35px;
        }
        .nowState{
            float: right;
            margin-right: 15px;
            line-height: 40px;
            font-weight: bold;
        }
        .clientInfo tr td{
            width: auto;
            text-align: center;
            height: 40px;
            line-height: 40px;
            border-bottom: 1px  dashed darkgray;
        }
        .clientInfo thead tr td{
            border-bottom:2px  solid #CCCCCC;
        }
        .lookOver{
            cursor: pointer;
        }
        .moreDetail{
            overflow: hidden;
        }
        .DetailBox{
            width: 80%;
            margin: auto;
            border: 1px solid blue;
            padding-bottom: 30px;
        }
        .detailTitle{
            list-style: none;
            overflow: hidden;
            width: 100%;
        }
        .detailTitle li{
            float: left;
            width: 14%;
            text-align: center;
            font-size: 13px;
            color: #333333;
        }
        .detailInfo{
            border-top:2px solid #cccccc ;
            border-bottom:2px solid #cccccc ;
        }
        .detailInfo td {
            height: 40px;
            line-height: 40px;
            border-bottom: 1px dashed #cccccc;
        }
        .packUp{
            float: right;
            cursor: pointer;
        }
            .use{
                display: block;
                width: 50px;
                height: auto;
                background: blue;
                color: #ffffff;
                margin: auto;
            }
            .notUse{
                display: block;
                width: 50px;
                height: auto;
                background: #FF9933;
                color: #ffffff;
                margin: auto;
            }
        .LossOnline{
            display: block;
            width: 50px;
            height: auto;
            background: red;
            color: #ffffff;
            margin: auto;
        }
    </style>
</head>
<body>
<script type="text/javascript">

//点击查看按钮
$(function(){
    $('body').on('click','.lookOver',function(){
        $(this).parent().next().find('.DetailBox').css('display','block')
    });

    $('body').on('click','.packUp',function(){
        $(this).parent().css('display','none');
    });
});


    //正式环境使用
    $(function(){

        //定位右侧栏意见反馈
        $('#ele_feedback').css('top', 'auto');

    });

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


<div id="topmenu">
    <div id="header2">
        <div id="logo"><a href="home.php"><img src="img/logo2.jpg?=2014121001" alt="医统天下 订货管理系统 (DHB.HK)" title="医统天下 订货管理系统 (DHB.HK)"  border="0" /></a></div>
        <ul>
            <li ><a href="">反馈</a></li><li ><a href="">使用</a></li><li class="current"><a href="">体验</a></li>
        </ul>
    </div>
</div>
<div class="bodyline" style="height:55px;">&nbsp;</div>

<div class="right-slide clearfix">
    <div class="slide-left">
        <ul class="slide-ul clearfix" style="padding:0;margin-left:0;" id="extend-ul">
            <li class="slide-notice" onclick="window.open('http://www.dhb.hk/site/notice/');" style="cursor:pointer;">
                <div>系统通告</div>
            </li>
            <li class="slide-ext" style="cursor:pointer;">
                <div>扩展功能</div>
            </li>
            <li class="slide-qq">
                <div style="height:368px;">
                    <div>
                        <h4>技术支持</h4>
                        <span style="clear:both;display:block;">售前咨询：</span>
                        <span class="tel" style="display:block;">400-6311-682</span>
                        <span style="clear:both;display:block;margin-top:-10px;">售后服务：</span>
                        <span class="tel" style="display:block;">400-6311-887</span>
                        <span style="display:block;margin-top:-6px;">工作日咨询：</span>
                        <a class="first" href="http://wpa.b.qq.com/cgi/wpa.php?ln=2&uin=4006311682" target="_blank">QQ交谈</a>
                        <span style="display:block;margin-top:5px;">周末值班：</span>
                        <a href="tencent://message/?uin=2261915847&Site=www.dhb.hk&Menu=yes" target="_blank">QQ交谈</a>
                        <a href="tencent://message/?uin=1730407198&Site=www.dhb.hk&Menu=yes" target="_blank">QQ交谈</a>
                    </div>
                </div>
                <em class="choice"></em>
            </li>
            <li class="slide-scan">
                <div style="color: black;height:280px;*height:290px;">
                    <h4>二维码扫描</h4>
                    <em class="choice"></em>
						<span style="display:block;">
							<img width="86" height="86" src="http://www.dhb.hk/qr-code/wxd.png">
						</span>
                    <span style="display:block;">医统天下微信端</span>
						<span style="display:block;margin-top:15px;">
							<img width="86" height="86" src="http://www.dhb.hk/qr-code/app.png">
						</span>
                    <span style="display:block;">手机APP下载</span>
                </div>
                <em class="choice"></em>
            </li>
            <li class="slide-yindao" id="un_isset_contact" onclick="do_save_experience_isset_un();" style="cursor:pointer;">
                <div>医统天下引导</div>
            </li>
        </ul>
        <ul class="slide-bottom slide-ul clearfix" style="margin-left:0px;margin-top:0px;position:absolute;bottom:0;padding-left:0px;" id="move-bar-in-li">
            <li class="slide-feedback" style="cursor: pointer;">
                <div style="display:none">意见反馈</div>
            </li>
            <li class="slide-help" onclick="window.open('http://help.dhb.net.cn/manager.php','_blank');" style="cursor: pointer;">
                <div style="display:none">帮助中心</div>
            </li>
            <li class="slide-gotop" onclick="window.location.href='#top';" style="cursor: pointer;">
                <div style="display:none">回到顶部</div>
            </li>


        </ul>
    </div>
    <div class="slide-right" id="fix-experience-margintop">
        <div style="width: 132px; margin-left:15px;">

            <h4><span style="float:left;color: #f03b15;font-size: 18px;line-height: 30px;">功能扩展</span>
                <span class="close" id="action-close">×</span>
            </h4>
            <ul style="padding:0;clear:both;margin-left:0;">
                <li><a href="http://m.dhb.hk/pro/buy_product.php" target="_blank">系统续费/升级</a></li>
                <li><a href="http://m.dhb.hk/pro/buy_sms.php" target="_blank">购买短信</a></li>
                <li><a href="http://m.dhb.hk/pro/erp_info.php" target="_blank">对接ERP系统</a></li>
                <li><a href="http://www.dhb.hk/dhbpay/" target="_blank">开通在线支付</a></li>
                <li><a href="http://m.dhb.hk/pro/weixin_info.php" target="_blank">部署独立微信</a></li>
            </ul>
        </div>
        <div style="height:140px;width:100%;bottom:0;background-color:#ffffff;position:absolute;"></div>
    </div>
</div>

<div class="slide-right" id="ele_feedback">
    <div style="width: 217px; margin-left:15px;">
        <h4><span style="float:left;color: #f03b15;font-size: 18px;line-height: 30px;">需求反馈</span>
            <span id="feedback-close" class="feedback-close">×</span>
        </h4>
        <div class="feedback_item " >
            <p>类型：</p>
            <label class="current"><input type="radio" name="new_type" id="new_type_1" value="功能建议" style="width:20px;border:none;" checked> <span class="feedback-gray">功能建议</span></label>
            <label class=""><input type="radio" name="new_type" id="new_type_2" value="用户体验" style="width:20px;border:none;"> <span class="feedback-gray">用户体验</span></label>
            <label class=""><input type="radio" name="new_type" id="new_type_3" value="设觉设计" style="width:20px;border:none;"> <span class="feedback-gray">设觉设计</span></label>
            <label class=""><input type="radio" name="new_type" id="new_type_4" value="系统BUG" style="width:20px;border:none;"> <span class="feedback-gray">系统BUG</span></label>
            <label class=""><input type="radio" name="new_type" id="new_type_5" value="其他意见" style="width:20px;border:none;"> <span class="feedback-gray">其他意见</span></label>
        </div>
        <div class="feedback_item">
            <textarea id="new_message" name="new_message" placeholder="请输入您的意见或者建议" style="width:207px"></textarea>
        </div>
        <div class="feedback_item">
            <input type="text" id="new_title" name="new_title" placeholder="请输入手机号" style="width:207px" />
        </div>
        <div class="feedback_item feedback_tips"></div>
        <div class="feedback_button">
            <input type="button" value="提交" name="submit_button" id="submit_button" />
        </div>
    </div>

</div>
<div class="bodyline" style="height:25px;"></div>
<div class="bodyline" style="height:32px;">
    <div class="leftdiv" style=" margin-top:8px; padding-left:12px;">
        <span><h4>阿商信息技术有限公司001</h4></span>
        <span valign="bottom">&nbsp;&nbsp;黄瑜(huangyu) 欢迎您！</span>
        &nbsp;&nbsp;<span>[<a href="http://help.dhb.net.cn/manager.php?skey=MDAwMDAwMDAwMJWL3NmEdnqXsnibk7yPZaSWvYyi" target="_blank"><font color=red>帮助？</font></a>]</span>
        &nbsp;&nbsp;<span>[<a href="do_login.php?m=logout">退出</a>]</span>
        &nbsp;&nbsp;<span><a href="http://online_m.rs.com/pro/buy_product.php" target="_blank" style="background:rgb(255,77,0);color:#fff;padding:0px 3px;">立即升级</a></span>
    </div>
    <div class="rightdiv">
        <span class="leftdiv"><img src="img/menu2_left.jpg" /></span>
            <span id="menu2">
			<ul>
               <li  class ="current2" ><a href="">体验</a></li> <li ><a href="">分析</a></li></ul>
		</span>
        <span><img src="img/menu2_right.jpg" /></span>
    </div>
</div>

<div class="bodyline" style="height:70px; background-image:url(img/bodyline_bg.jpg);">
    <div class="leftdiv"><img src="img/blue_left.jpg" /></div>
    <div class="leftdiv"><h1>体验</h1></div>
</div>
<div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <div class="leftdiv">
            <form id="FormSearch" name="FormSearch" method="post" action="user_recycle.php">
                <label>
                    &nbsp;&nbsp;姓名，电话： <input type="text" name="kw" id="kw" class="inputline" />
                </label>
                <!--地区-->
                <label>
                    <select class="allArea">
                        <option value="">全部地区</option>
                    </select>
                </label>
                <!--时间段-->
                <label>
                    &nbsp;&nbsp;时间段 <input type="text"  class="timeFrom" /> -  <input type="text"  class="timeTo" />
                </label>
                <label>
                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
                </label>
            </form>
        </div>
        <div class="location"><strong>当前位置：</strong><a href="user.php">体验信息</a> </div>
    </div>


    <div class="line2"></div>
    <div class="bline">

        <div >
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <td  class="bottomlinebold">编号</td>
                        <td  class="bottomlinebold">联系人</td>
                        <td  class="bottomlinebold">联系方式</td>
                        <td  class="bottomlinebold">添加时间</td>
                        <td  class="bottomlinebold" >备注 IP地址</td>
                        <td  class="bottomlinebold" >是否注册</td>
                        <td  class="bottomlinebold">详细情况</td>
                        <td class="bottomlinebold">操作</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="detailInfo">
                        <td ><span class="Num">162</span></td>
                        <td><span class="clientName">张三</span></td>
                        <td><span class="clientPhone">13659989359</span></td>
                        <td><span class="time">15-09-10 19:00</span></td>
                        <td ><span class="address">新疆乌鲁木齐</span>（<span class="ip">192.168.0.1</span>）</td>
                        <td >未注册</td>
                        <td class="lookOver">查看</td>
                        <td>[<a href="">回访记录</a>]</td>
                    </tr>
                    <tr class='moreDetail'>
                        <td colspan="8">
                            <div class="DetailBox"  style="display: none">
                                <ul class="detailTitle">
                                    <li>记录时间</li>
                                    <li>联系人</li>
                                    <li>联系人职务</li>
                                    <li>回访记录简情</li>
                                    <li>使用状态</li>
                                    <li>详情查看</li>
                                </ul>
                                <!--详情介绍-->
                                <div class="detailInfo">
                                    <ul class="detailTitle">
                                        <li>2015-01-01</li>
                                        <li>联系人</li>
                                        <li>联系人职务</li>
                                        <li>打到日本</li>
                                        <li><span  class="use">使用</span></li>
                                        <li><a href="">详情</a></li>
                                    </ul>
                                    <ul class="detailTitle">
                                        <li>2015-01-01</li>
                                        <li>联系人</li>
                                        <li>联系人职务</li>
                                        <li>打到日本</li>
                                        <li><span  class="LossOnline">失联</span></li>
                                        <li><a href="">详情</a></li>
                                    </ul>
                                    <ul class="detailTitle">
                                        <li>2015-01-01</li>
                                        <li>联系人</li>
                                        <li>联系人职务</li>
                                        <li>打到日本</li>
                                        <li><span  class="notUse">没用</span></li>
                                        <li><a href="">详情</a></li>
                                    </ul>
                                </div>
                                <div class="packUp">↑【收起】</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>

                </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="4%"  height="30" ></td>

                        <td  class="sublink"></td>
                        <td width="50%" align="right"></td>
                    </tr>
                </table>
                <INPUT TYPE="hidden" name="referer" value ="" >

            </form>
        </div>
    </div>
    <br style="clear:both;" />
</div>

<div class="bodyline" style="background-image:url(img/bottom_bg.jpg); height:12px;">
    <div class="leftdiv"><img src="img/bottom_left.jpg" /></div>
    <div class="rightdiv"><img src="img/bottom_right.jpg" /></div>
</div>

<div id="copyright"><span class="leftdiv">[<a href="http://help.dhb.net.cn/manager.php?skey=MDAwMDAwMDAwMJWL3NmEdnqXsnibk7yPZaSWvYyi" target="_blank" title="操作指南">帮助?</a>]&nbsp;&nbsp;&nbsp;&nbsp;
			</span><span class="rightdiv">Powered By Rsung DingHuoBao (<a href="http://www.dhb.hk" target="_blank">WWW.DHB.HK</a>) System © 2006 - 2015 <a href="http://www.rsung.com" target="_blank">Rsung</a> Ltd.</span></div>

<script language="JavaScript" type="text/javascript">
    <!--
    if(typeof(jQuery) == "undefined") document.write('<script src="../scripts/jquery.min.js" type="text/javascript"></script>');
document.write('<script src="../scripts/jquery.messager.js" type="text/javascript"></script>');
function refresh_message()
{
$.post("do_message.php?rid=1807",
{m:"refresh"},
function(data){
if(data=="isouttime")
{
alert('登陆超时或您的帐号在别的地方登陆了，请重新登陆！');
top.window.location.href='/index.html';
}else if(data != "" && data !=0 && data != "undefined" && data != undefined){

$.messager.anim('fade', 2000);
$.messager.show('','<font color=red>您有新订单！</font><br />  共有 <strong>'+data+'</strong> 个订单待审核!<br /><a href=order.php?sid=0 >点击查看 &nbsp;&nbsp;</a><br /><bgsound src="./img/message.mp3" loop="1" delay="2" />',0);
}
}
);
}
window.setInterval("refresh_message()", 300000);
//$.messager.show('','<font color=red>系统又升级了</font><br />  <br /><a href=changelog.php >点击查看详细 &nbsp;&nbsp;</a>',0);
-->
</script>
<link rel="stylesheet" href="../scripts/select2/select2.min.css" type="text/css" />
<script src="../scripts/select2/select2.min.js" type="text/javascript"></script>
<script src="../scripts/select2/zh-CN.js" type="text/javascript"></script>
<script>
    $(function(){
        if($(".select2").length >0){
            $(".select2").select2();
        }
    });
</script>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>

</body>
</html>