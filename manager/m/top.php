<?php include_once ("inc/son_menu_bar.php");?>
<link rel="stylesheet" href="css/index.css">

<style type="text/css">
html{width:100%;height:100%}
body {width:100%;height:100%;position:relative;margin:0; padding:0; font-size:12px; background-repeat: repeat-x;background-position: left 48px;font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; color:#666;}
#topmenu{top:0px;}

#extend-ul{margin-top:expression( ($(window).height() * 0.15) + "px");}


@font-face {font-family: "iconfont";
  src: url('css/fonts/iconfont.eot?t=1488766294281'); /* IE9*/
  src: url('css/fonts/iconfont.eot?t=1488766294281#iefix') format('embedded-opentype'), /* IE6-IE8 */
  url('css/fonts/iconfont.woff?t=1488766294281') format('woff'), /* chrome, firefox */
  url('css/fonts/iconfont.ttf?t=1488766294281') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/
  url('css/fonts/iconfont.svg?t=1488766294281#iconfont') format('svg'); /* iOS 4.1- */
}



.iconfont {
  font-family:"iconfont" !important;
  font-size:16px;
  font-style:normal;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.icon-xiaohuojian:before { content: "\e6da"; }
.icon-kefu:before { content: "\e600"; }
.icon-iconfontlanmu:before { content: "\e60a"; }
.icon-icon01:before { content: "\e601"; }
.icon-huangguan:before { content: "\e603"; }
.icon-xinxi:before { content: "\e604"; }
.icon-dacha01:before { content: "\e653"; }
.icon-jingxuan:before { content: "\e602"; }
.icon-xiugaixinxi:before { content: "\e614"; }
.icon-dingdan:before { content: "\e61a"; }

.icon-xiaoxi:before { content: "\e672"; }

.icon-guanji:before { content: "\e654"; }

.icon-huoche:before { content: "\e703"; }

.icon-kucun:before { content: "\e642"; }

.icon-tuihuozhong:before { content: "\e87c"; }

.icon-kehu-copy:before { content: "\e629"; }
.icon-shouye_shouye:before { content: "\e607"; }
.icon-dayuhao:before { content: "\e634"; }

.icon-zhifukuanxiang:before { content: "\e62e"; }

.icon-tongji:before { content: "\e63f"; }

.icon-youpinwangtubiao-:before { content: "\e6b3"; }
.icon-suoyouleibie:before { content: "\e61c"; }

.ho:hover{background:none;}

        li {list-style-type:none;}
        a{text-decoration:none;}
        #ProMenu{width:150px;height:860px;clear:both;float:left;position: relative;z-index:999;
            background-color: #2C2F32;margin-top:3px;}
        #ProMenu>li{float:left;width:150px;
            height: 50px;}
        #ProMenu>li>a{display:block;position:relative;width:135px;height:50px;line-height:50px;text-align:left; border-left: solid 4px transparent;color:#fff;font-size:18px;margin-left:1px;display:inline;float:left;padding-left:10px;}
        #ProMenu>li>a>span{}
        #ProMenu span{display:none;float:left;}

        a:hover{
          text-decoration: none;
            }
            a:hover a>span{display:block;}
        #ProMenu>li:hover span{
            display: block;
            position:absolute;left:150px;top:0;width:150px;height:860px;background-color: #EEEEEE;
            z-index:9999999;
        }


        .highlight{background-color: #fff;color: #000 !important;text-decoration: none;border-left: solid 4px ##33a676 !important;}


#ProMenu>li>a:hover{background-color: #fff;color: #000 !important;text-decoration: none;border-left: solid 4px #33a676;}
#ProMenu .drop>a{display:block;width:106px;height:50px;line-height:50px;text-align:left;  padding-left:40px;border-left: solid 4px transparent;color:#000;font-size:16px;}
#ProMenu .drop>a:hover{border-left: solid 4px #33a676;background-color:#fff;}
#comeback2:hover{color:#33a676}

.page_home{position:relative;}
.home_list{display:none;position:absolute;top:60px;right:0;width:120px;height:250px;background-color:#fff;box-shadow:1px 1px 8px rgba(226,226,266,.6),-1px -1px 8px rgba(226,226,266,.6)}
.page_home:hover{background-color:#fff;color:#fff}
.page_home:hover .home_list{display:block;}
.home_list>a{display:block;width:120px;height:50px;line-height:50px;text-align:center;font-size:16px;font-weight:normal;}
.oversize{display:block;white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
.leftright>li:hover{background-color:#1E9161;}
.leftright>li:nth-child(1){background-color:#33a676;}

.leftright>li>a:hover{background-color:#1E9161 !important;}

</style>

<div id="topmenu">
    <div id="header2">
	<div id="logo"><a href="home.php" class="ho"><img src="img/77.png?=2014121001" style="width:135px;height:39px;margin-top:12px;" alt="" title=""  border="0" /></a></div>
                      <div class="header7" style="margin-left:230px;">
                        <div class="header7_fl" style="float:left;"><p style="margin-top:20px;font-size:16px;color:#fff;font-weight:bold"><?php echo $_SESSION['uc']['CompanyName'];?>@医统天下</p></div>
                        <div class="header7_fr" style="float:right;margin-right:2%">
                        <ul class="leftright">
                        <li class="oversize" style="color:#fff;width:200px;text-align:right;line-height:55px;">您好，<span style="color:#fff;font-size:14px;"><?=$_SESSION['uinfo']['usertruename']?></span></li>
                        <li>
                         <a href="./home.php"><span class="iconfont icon-shouye_shouye" style="color:#fff;font-size:23px;"></a>

                         </li>
                        <li><a href="./order_guestbook.php"><span class="iconfont icon-xiaoxi" style="color:#fff;font-size:18px;"></a></li>
                        <?php  if($_SESSION['uinfo']['userflag'] == 9){?>
                        <li style="" class="page_home">
                        <a href="./system.php"><span class="iconfont icon-icon01" style="color:#fff;font-size:18px;"></a>
                         <span class="home_list"> 
                         <a href="./system.php">系统设置</a>
                         <a href="./user.php">账号管理</a>
                         <a href="./user_recycle.php">冻结账号</a>
                         <a href="./user_log.php">登录日志</a>
                         <a href="./execution_log.php">操作日志</a>
                         </span>
                        </li>
                        <?php }?>
                        <li><a href="./do_login.php?m=logout"><span class="iconfont icon-guanji" style="color:#fff;font-size:18px;"></a></li>
                        <?php if(in_array($_SESSION['uinfo']['userid'],array(1))) {?>
                        <li><a href="../pt/feedback.php"><span class="iconfont icon-huangguan" style="color:#fff;font-size:18px;"></a></li>
                        <?php }?>
                        </ul>
                        </div>
                        </div>
        </div>
</div>


<div id="app" style="height:0%;height:0px;float: left;">
  <div id="ProMenu" style="position:fixed">
       <li>
        <a href="order.php" class="<?php if($menu_flag == 'order') echo 'highlight'?>">&nbsp;<i class="iconfont icon-dingdan" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;订单</a>
        <span class="drop">
        <?php foreach($son_menu['order'] as $link => $name){?>
         <a href="<?=$link?>.php"><?=$name?></a>
        <?php }?>
        </span>
       </li>
       <?php  if($_SESSION['uinfo']['userflag'] !=2){?>
       <li>
        <a href="consignment.php" class="<?php if($menu_flag == 'consignment') echo 'highlight'?>">&nbsp;<i class="iconfont icon-huoche" style="font-size:18px;"></i>&nbsp;&nbsp;&nbsp;发货</a>
        <span class="drop">
            <?php foreach($son_menu['consignment'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <li>
        <a href="finance.php" class="<?php if($menu_flag == 'finance') echo 'highlight'?>">&nbsp;<i class="iconfont icon-zhifukuanxiang" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;款项</a>
        <span class="drop">
            <?php foreach($son_menu['finance'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <li>
        <a href="return.php" class="<?php if($menu_flag == 'return') echo 'highlight'?>">&nbsp;<i class="iconfont icon-tuihuozhong" style="font-size:18px;"></i>&nbsp;&nbsp;&nbsp;退单</a>
        <span  class="drop">
         	<?php foreach($son_menu['return'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <?php }?>
       <li>
        <a href="product.php" class="<?php if($menu_flag == 'product') echo 'highlight'?>">&nbsp;<i class="iconfont icon-jingxuan" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;商品</a>
        <span  class="drop">
         	<?php foreach($son_menu['product'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <li>
        <a href="inventory.php" class="<?php if($menu_flag == 'inventory') echo 'highlight'?>">&nbsp;<i class="iconfont icon-kucun" style="font-size:20px;"></i>&nbsp;&nbsp;&nbsp;库存</a>
        <span class="drop">
            <?php foreach($son_menu['inventory'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <li>
        <a href="client.php" class="<?php if($menu_flag == 'client') echo 'highlight'?>">&nbsp;<i class="iconfont icon-youpinwangtubiao-" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;药店</a>
        <span class="drop">
            <?php foreach($son_menu['client'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <li>
        <a href="saler.php" class="<?php if($menu_flag == 'saler') echo 'highlight'?>">&nbsp;<i class="iconfont icon-kehu-copy" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;客情官</a>
        <span class="drop">
            <?php foreach($son_menu['saler'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <li>
        <a href="infomation.php" class="<?php if($menu_flag == 'infomation') echo 'highlight'?>">&nbsp;<i class="iconfont icon-xinxi" style="font-size:18px;"></i>&nbsp;&nbsp;&nbsp;信息</a>
        <span class="drop" >
            <?php foreach($son_menu['infomation'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
       </li>
       <li>
        <a href="forum.php" class="<?php if($menu_flag == 'forum') echo 'highlight'?>">&nbsp;<i class="iconfont icon-kefu" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;客服</a>
        <span  class="drop">
            <?php foreach($son_menu['forum'] as $link => $name){?>
	         <a href="<?=$link?>.php"><?=$name?></a>
	        <?php }?>
        </span>
          </li>
       <li>
          <a href="statistics.php"  style="" class="<?php if($menu_flag == 'statistics') echo 'highlight'?>">&nbsp;<i class="iconfont icon-tongji" style="font-size:18px;"></i>&nbsp;&nbsp;&nbsp;统计</a>
        <span  class="drop">
         <?php foreach($son_menu['statistics'] as $link => $name){?>
	         <a href="<?=$link?>.php" class=""><?=$name?></a>
	        <?php }?>
        </span>
      </li>
  </div>
</div>


<span id="comeback2"  class="iconfont icon-xiaohuojian" style="display:none;width: 40px;height: 42px;font-size:40px;position: fixed;right:28px;bottom: 100px;cursor: pointer"></span>

<script type="text/javascript">
$(document).ready(function(){

	//平滑回到顶部
	$('#comeback2').bind('click', function(){
		$('body,html').animate({ scrollTop: 0 }, 500);
	});

	//淡出浏览器版本提示
	if(!$.support.leadingWhitespace){
		var _b = $('#branser-notice');
			_b.show();
			setTimeout(function(){
				_b.fadeOut('slow');
			}, 15000);
	}
});
var animateFlag=1;$(document).scroll(function(){var scrollTop=$(document).scrollTop();if(scrollTop>400){$('#comeback2').fadeIn('slow');$("#fixed-head").css('display','block');if(animateFlag){animateFlag=0;$("#fixed-head").stop();$("#fixed-head").animate({'top':'0px'},500)}}else{$(".searchRe").css('display','none');$('#comeback2').fadeOut('slow');if(animateFlag==0){animateFlag=1;$("#fixed-head").stop();$("#fixed-head").animate({'display':'none','top':'-89px'},500)}}});

</script>


<script>
    var Main = {
        methods: {
            handleOpen(key, keyPath) {
    },
    handleClose(key, keyPath) {
    }
    }
    }
    var Ctor = Vue.extend(Main);
    new Ctor().$mount('#app')
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