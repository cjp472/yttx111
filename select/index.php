<?php 
header("Content-type: text/html; charset=utf-8");
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
{
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/select/mobile_index.php');
	exit;
}

//引入客户开发地区分布图
$ipmapdata = require('./ipmap.php');

//获取计算机的外网ip
function get_onlineip() {
    $onlineip = '';
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }
    
//     return '116.11.159.27';
//     return '61.54.106.187'; //新乡
//     return '218.12.41.186'; //石家庄
	// return '219.137.150.255';

//     return '61.52.31.38'; //郑州
    return $onlineip;
}

//获取当前外网的IP地区信息
$ip = get_onlineip();
if(empty($_COOKIE['ip_area']) || $_COOKIE['ip']!=$ip){
    //获取当前地区开户信息
    $str1 = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$ip);
    $localIpData = json_decode($str1,true);
    $ip_area = substr($localIpData['data']['city'], 0, -3);
	
	//特殊处理
	if($ip_area == '广州' || $ip_area == '佛山') $ip_area = '广佛';
    
    setcookie('ip',$ip);
    setcookie('ip_area',$ip_area);
}else{
    $ip_area = $_COOKIE['ip_area'];
}

$ipmaparr = array();
foreach ($ipmapdata as $key=> $val){
    $ipmaparr = array_merge($ipmaparr, $val['sonMap']);
}

?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no, email=no" />
    <link rel="icon" href="http://img.bj.wezhan.cn/2019570_favicon.ico" />
    <link rel="shortcut icon" href="http://img.bj.wezhan.cn/2019570_favicon.ico" />
    <link rel="bookmark" href="http://img.bj.wezhan.cn/2019570_favicon.ico"/>

    <title>医统天下（北京）网络科技有限公司</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />

    <link rel="stylesheet" href="./base.pc.css" type="text/css" />
    <link rel="stylesheet" href="./select.css" type="text/css" />
    

    <script type="text/javascript" src="http://img.bj.wezhan.cn/Plugins/Designer/Script/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="http://img.bj.wezhan.cn/Plugins/Designer/Script/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="http://img.bj.wezhan.cn/Plugins/Designer/Script/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://img.bj.wezhan.cn/Scripts/public.common.min.js"></script>
    <script type="text/javascript" src="http://img.bj.wezhan.cn/Plugins/Designer/Script/jquery.lazyload.min.js"></script>

    <script type="text/javascript" src="http://img.bj.wezhan.cn/Plugins/Designer/Script/jquery.slider.js"></script>


    <style type="text/css">
    /* Style */
    body{
        background-color:;
    }
    a {
        cursor: pointer;
        outline: medium none;
        text-decoration: none;
    }
    .page-wrap { clear:both; margin:0 auto; padding:0; width:auto; height:100%; }
    .page-wrap .header,.page-wrap .main-wrap .main,.page-wrap .footer { position:relative; clear:both; margin:0 auto; padding:0;}
    .page-wrap .main-wrap { clear:both; margin:0; padding:0; width:auto; }
    .page-wrap .main-wrap .main .sidebar-left,.page-wrap .main-wrap .main .content { float:left; display:inline;}
    .page-wrap .main-wrap .main .content { float:right;}
    .clearfix:before,.clearfix:after { content:""; display:table;}
    .clearfix:after { clear:both; overflow:hidden;}
    .clearfix { zoom:1;}
    .fast_city a {
        color: #005db1;
        margin-right: 8px;
        text-decoration: underline;
    }
    .content_1{
	   font-size: 18px;
    }
    .citysear {
        height: 22px;
        line-height: 22px;
        padding: 10px 0 18px;
        white-space: nowrap;
    }
    .index_bo {
        border-top: 1px solid #cccccc;
    }
    #clist {
        clear: both;
        font-size: 16px;
        line-height: 30px;
        overflow: hidden;
        padding: 10px 0 10px 20px;
    }
    #clist dt {
        clear: both;
        display: inline;
        float: left;
        font-weight: bold;
        width: 60px;
    }
    #clist dd {
        display: inline;
        float: left;
        width: 850px;
    }
    #clist dd a {
        color: #005db1;
        display: inline-block;
        margin-right: 14px;
        white-space: nowrap;
    	text-decoration: none;
    }
    #clist dd a:hover {
        color: #f00;
    }
    </style>
</head>
<body>
<div id="view_layout_1_277344184" class="mainSamrtView yibuSmartViewMargin">
    <div class='yibuFrameContent layout_Block3_Item0' style='border-style:none;'>
        <div class="page-wrap">
            <div style="*z-index:11;*position:relative;width:100%;height:100px;margin-left:auto;margin-right:auto;background-color:">
              <div class="header page_header yibuLayout_tempPanel" style="width:1100px;height:100px;;">
                    <div class='runTimeflowsmartView'><div  id="view_photoalbum_34_277344184" class="yibuSmartViewMargin absPos"  style="top:65px" >
                        <div class='yibuFrameContent overflow_hidden photoalbum_Style2_Item0 view_photoalbum_34_277344184_Style2_Item0' style='height:4px;width:1100px;'>
                            <div class="w_slider_2 renderfullScreen w_slider_2_34">
                                <div class="w_slider_img">
                                    <ul>
                                            <li style="background: url(http://img.bj.wezhan.cn/content/sitefiles/2019570/images/7171267_2.png) center 0 no-repeat;">
                                             
                                            </li>
                                    </ul>
                                </div>
                                <!-- 下面是前/后按钮代码，如果不需要删除即可 -->
                                <a   style="display:none;"     class="prev" href="javascript:void(0)"></a>
                                <a   style="display:none;"     class="next" href="javascript:void(0)"></a>
                            
                                <div class="w_slider_num"   style="display:none;"      ><ul></ul></div>
                            </div>
                        
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    if ("fade"=="fold") {
                                        setRenderFullScreen($("#view_photoalbum_34_277344184"), "window");
                                    } else {
                                        setRenderFullScreen($("#view_photoalbum_34_277344184"));
                                    }
                                    $(".w_slider_2_34").slide({
                                        titCell: $(".w_slider_2_34 .w_slider_num ul"),
                                        mainCell: $(".w_slider_2_34 .w_slider_img ul"),
                                        effect: "fade",
                                        autoPlay: "true",
                                        autoPage: true,
                                        trigger: "mouseover",
                                        interTime: "1500"
                                    });
                                });
                            </script>
                            </div>
                        </div>
                        <div  id="view_image_36_277344184" class="yibuSmartViewMargin absPos" style="top:0;" >
                            <div class='yibuFrameContent overflow_hidden image_Style1_Item0' style='height:50px;width:283px;'>    <div class="megwh" style="height:100%; width:100%;">
                                    <a id="autosize_view_image_36_277344184" href="javascript:void(0)" target="_self" >
                                        <img class="lazyload" src="./logoa.png" data-original="http://img1.bj.wezhan.cn/content/sitefiles/2019570/images/7171311_5_9032f0eb-cc27-48f4-8481-507187071e79_resize_picture.png" alt="" style="border:none; position:relative;" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
              </div>
            </div>
            <div class="main-wrap clearfix" style="*z-index:10;*position:relative;width:1100px;margin-left:auto;margin-right:auto;background-color:">
                <div class="content_1">
                    <div class="fast_city">
                        <b>当前站点：</b>
                        <?php  if(!empty($ipmaparr[$ip_area.'库'])){?>
                        	<a class="bj" href="http://<?php echo $ipmaparr[$ip_area.'库']?>.yitong111.com/shop/">进入<?php echo $ip_area;?>库</a>
                        <?php  }else{?>
                            <?php echo $ip_area;?>，此地区尚未开通
                        <?php  }?>
                    </div>
                </div>
                <div class="citysear"> </div>
                <div class="index_bo">
                    <dl id="clist">
                    <?php  foreach ($ipmapdata as $key=>$val){?>
                        <dt><?php echo $key?>.</dt>
                        <dd>
                         <?php  foreach ($val['sonMap'] as $sk=>$sv){?>
                            <a href="http://<?php echo $sv?>.yitong111.com/shop/"><?php echo $sk;?></a>
                         <?php }?>
                        </dd>
                    <?php }?>
                    </dl>
                </div>
            </div>
            <div style="width:100%;height:175px;background-color:;position:fixed;bottom:0;">
                <div class="footer page_footer yibuLayout_tempPanel" style="width:1100px;height:175px;;">
                    <div class='runTimeflowsmartView'>
                        <div  id="view_photoalbum_61_277344184" class="yibuSmartViewMargin absPos"   >
                            <div class='yibuFrameContent overflow_hidden photoalbum_Style2_Item0 view_photoalbum_61_277344184_Style2_Item0' style='height:174px;width:1100px;'>
                                <div class="w_slider_2 renderfullScreen w_slider_2_61">
                                    <div class="w_slider_img">
                                        <ul>
                                                <li style="background: url(http://img.bj.wezhan.cn/content/sitefiles/2019570/images/7174428_3.png) center 0 no-repeat;">
                                                    <div class="siteWidth">
                                
                                                        <p class="txt"   style="display:none;"       >
                                                            3
                                                            <span class="btn"></span>
                                                        </p>
                                                        <p class="txtDesc"   style="display: none;"         >
                                                            图片0
                                                        </p>
                                
                                                        <a   target="_self"                                title="3" href="javascript:void(0)"></a>
                                
                                                    </div>
                                                </li>
                                        </ul>
                                    </div>
                                </div>
                            
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        if ("fade"=="fold") {
                                            setRenderFullScreen($("#view_photoalbum_61_277344184"), "window");
                                        } else {
                                            setRenderFullScreen($("#view_photoalbum_61_277344184"));
                                        }
                                        
                                    });
                                </script>
                                </div>
                        </div>
                        <div  id="view_text_62_277344184" class="yibuSmartViewMargin absPos"   >
                            <div class='yibuFrameContent overflow_hidden text_Style1_Item0' style='height:55px;width:1100px;'>
                                <style type="text/css">
                                    #view_text_62_277344184_txt ul{ padding-left:20px;}
                                </style>
                                <div id='view_text_62_277344184_txt'   style="cursor:default; height:100%; width:100%;"  >
                                    <div class="editableContent" style="line-height:1.5;">
                                        <p style="white-space: normal; text-align: center; line-height: 2.3em;"><span style="font-family: 微软雅黑, &#39;Microsoft YaHei&#39;; color: rgb(255, 255, 255);">Copyright ? 2014-2016 医统天下（北京）网络科技有限公司</span></p><p style="white-space: normal; text-align: center; line-height: 2.3em;"><span style="font-family: 微软雅黑, &#39;Microsoft YaHei&#39;; color: rgb(255, 255, 255);">&nbsp; 互联网药品交易服务资格证书国A20150005号，京ICP备14037820号，京公安网备11010102001371号&nbsp;</span></p><p style="white-space: normal;"><span style="font-family: 微软雅黑, &#39;Microsoft YaHei&#39;; color: rgb(255, 255, 255);"><br/></span></p><p><br/></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  id="view_image_63_277344184" class="yibuSmartViewMargin absPos"   >
                            <div class='yibuFrameContent overflow_hidden image_Style1_Item0' style='height:41px;width:561px;'>
                                <div class="megwh" style="height:100%; width:100%;">
                                    <a id="autosize_view_image_63_277344184" href="javascript:void(0)" target="_self" >
                                        <img src="http://img1.bj.wezhan.cn/content/sitefiles/2019570/images/7174454_5_061334bb-8063-4543-b324-390c851cfcaa_resize_picture.png" data-original="http://img1.bj.wezhan.cn/content/sitefiles/2019570/images/7174454_5_061334bb-8063-4543-b324-390c851cfcaa_resize_picture.png" alt="" style="border:none; position:relative;" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
