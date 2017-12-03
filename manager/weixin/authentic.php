<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>体验订货宝，请用微信扫描二维码</title>
    <link rel="stylesheet" href="css/pc.css"/>
    <link rel="stylesheet" href="css/style.css?_v=<?php echo rand(4000,5000);?>" type="text/css">
</head>
<?php

require('../common.php');

if(isset($_SESSION['is_auth'])){
    $is_auth = $_SESSION['is_auth'];

    if($is_auth == 1){
        header('location:' . $_GET['url']);
        exit;
    }
}

include_once('../../global/mode.php');
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
<body>
    <div class="container">
        <div class="header">
            <div class="left float-left">
                <p>
                    <img src="img/logo.jpg" alt="">
                    <span class="current-hy"><?php echo $EXPERIENCE_INDUSTRY_NAME[$sIndustry_experience];?></span>
                    <span class="current-hy-change"><a href="http://www.dhb.hk/experience/" title="切换行业">[切换行业]</a></span>
                </p>
                <ul class="clearfix">
                    <li onclick="window.open('http://www.dhb.hk/intro/base/m-login.html');" title="操作演示"><span class="icon"></span> 操作演示</li>
                    <li onclick="window.open('http://help.dhb.net.cn/manager.php?s=/Client/Subquestion/index/uid/272.html');" title="视频教学"><span class="icon"></span> 视频教学</li>
                    <li onclick="window.open('http://help.dhb.net.cn/manager.php');" title="帮助中心"><span class="icon">t</span> 帮助中心</li>
                    <li title="售前咨询" onclick="window.open('http://wpa.b.qq.com/cgi/wpa.php?ln=2&amp;uin=4006311682');"><span class="icon">v</span> 售前咨询</li>
                </ul>
            </div>
            <div class="right float-right">
                <ul class="clearfix">
                    <li>
                        <img src="img/scann.png" alt="">
                    </li>
                    <li>
                        <img style="vertical-align: middle;" src="img/hot_line.png" alt="">
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content">
        <p class="user-title">体验订货宝，请用微信扫描二维码</p>
        <img src="img/weixn.png" alt="" class="weixin"/>
        <p class="user-info">扫码关注<span>“订货宝”</span>公众号后</p>

        <p class="user-info">点击<span>【功能体验】</span>→<span>【获取体验码】</span>进行获取</p>
        <div class="input">
            <input type="text" placeholder="请输入体验码" class="input-box" id="auth_code"/>
            <button type="button" class="input-btn" id="submit-btn">立即体验</button>
        </div>
        <div style="width:375px; margin:auto;">
            <p class="err_msg"></p>
        </div>
    </div>
    <script type="text/javascript" src="../scripts/jquery.min.js"></script>
    <script>

        $(function(){
            function getUrlParam(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
                var r = window.location.search.substr(1).match(reg); //匹配目标参数
                if (r != null) return unescape(r[2]); return null; //返回参数值
            }

            var url = getUrlParam('url');
            //获取url参数上面的值，type=m：表示管理端，type=c表示订货端， industry参数用于表示试用哪个行业的；
            function post_code(){
                var code = $.trim($('#auth_code').val());
                //TODO: 需要验证规则
                if(!check(code)){
                    $('.err_msg').html('验证码格式错误')
                    return;
                }

                $.post('/experience/auth.php', {code: code}, function(data){
                    data = JSON.parse(data);
                    if(data.is_ok == 1) {
                        //成功
                        location.href = url;
                    }
                    else{
                        //失败
                        $('.err_msg').html(data.err_msg)
                    }
                });
            }
            
            function check(value){ 
                var pattern = /^\d{6}$/; 
                return pattern.test(value);
            } 

            $('#submit-btn').on('click', post_code);
        });
    </script>
</body>
</html>