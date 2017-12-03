

    <link rel="stylesheet" href="css/index.css">

    <style type="text/css">
    html{width:100%;height:100%}
    body {width:100%;height:100%;position:relative;margin:0; padding:0; font-size:12px; background-repeat: repeat-x;background-position: left 48px;font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; color:#666;}
    #topmenu{top:0px;}

    #extend-ul{margin-top:expression( ($(window).height() * 0.15) + "px");}

  @font-face {font-family: "iconfont";
    src: url('css/fonts/iconfont.eot?t=1489028863591'); /* IE9*/
    src: url('css/fonts/iconfont.eot?t=1489028863591#iefix') format('embedded-opentype'), /* IE6-IE8 */
    url('css/fonts/iconfont.woff?t=1489028863591') format('woff'), /* chrome, firefox */
    url('css/fonts/iconfont.ttf?t=1489028863591') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/
    url('css/fonts/iconfont.svg?t=1489028863591#iconfont') format('svg'); /* iOS 4.1- */
  }

  .iconfont {
    font-family:"iconfont" !important;
    font-size:16px;
    font-style:normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

.icon-ic_local_pharmacy_px:before { content: "\e717"; }
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
.icon-business:before { content: "\e61e"; }
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
                background-color: #2C2F32;}
            #ProMenu>li{float:left;width:150px;
                height: 50px;}
            #ProMenu>li>a{display:block;position:relative;width:145px;height:50px;line-height:50px;text-align:left; border-left: solid 4px transparent;color:#fff;font-size:18px;margin-left:1px;display:inline;float:left;padding-left:8px;}
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
    #ProMenu .drop>a{display:block;width:146px;height:50px;line-height:50px;text-align:center;  border-left: solid 4px transparent;color:#000;font-size:16px;}
    #ProMenu .drop>a:hover{border-left: solid 4px #33a676;background-color:#fff;}
    #comeback2:hover{color:#33a676}

    .page_home{position:relative;}
    .home_list{display:none;position:absolute;top:60px;right:0;width:120px;height:50px;background-color:#fff;box-shadow:1px 1px 8px rgba(226,226,266,.6),-1px -1px 8px rgba(226,226,266,.6)}
    .page_home:hover{background-color:#fff;color:#fff}
    .page_home:hover .home_list{display:block;}
    .home_list>a{display:block;width:120px;height:50px;line-height:50px;text-align:center;font-size:16px;font-weight:normal;}
    .oversize{display:block;white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
    .leftright>li:hover{background-color:#1E9161;}
    .leftright>li:nth-child(1){background-color:#33a676;}

    .leftright>li>a:hover{background-color:#1E9161 !important;}

    </style>

    <div id="topmenu" style="width:100%;height:60px;background-color:#33a676">
        <div id="header2">
    	<div id="logo"><a href="home.php" class="ho"><img src="img/77.png?=2014121001" style="width:135px;height:39px;margin-top:12px;" alt="" title=""  border="0" /></a></div>
                          <div class="header7" style="margin-left:230px;">
                            <div class="header7_fl" style="float:left;"><p style="margin-top:20px;font-size:16px;color:#fff;font-weight:bold"><?php echo $_SESSION['uinfo']['usertruename'];?>@<?php echo $_SESSION['uc']['CompanyName'];?></p></div>
                            <div class="header7_fr" style="float:right;margin-right:2%">
                            <ul class="leftright">
                            <li class="oversize" style="color:#fff;width:200px;text-align:right;line-height:55px;">您好，<span style="color:#fff;font-size:14px;"><?=$_SESSION['uinfo']['usertruename']?></span></li>
                            <li>
                             <a href="./home.php"><span class="iconfont icon-shouye_shouye" style="color:#fff;font-size:23px;"></a>

                             </li>
                            <li style="" class="page_home">
                            <a href="./system.php"><span class="iconfont icon-icon01" style="color:#fff;font-size:18px;"></a>
                             <span class="home_list">
                             <a href="./change_pass.php">修改密码</a>
                             </span>

                            </li>
                            <li><a href="./do_login.php?m=logout"><span class="iconfont icon-guanji" style="color:#fff;font-size:18px;"></a></li>
                            </ul>
                            </div>
                            </div>
            </div>
    </div>
    
    <?php 
    
    $menu_arr = array(
				"order"				=> array(
					"order"			=> "订单管理",
					"order_add"		=> "新增订单"
				),
				"consignment"		=> array(
					"consignment"	=> "发货信息",
				),
				"client"			=> array(
					"client"		=> "药店",
					"client_recycle"	=> "回收站",
					"client_log"	=> "登录日志"
				),
				"inventory"				=> array(
					"inventory"			=> "库存状况",
					"inventory_list"	=> "库存明细",
				),
				"deduct"		=> array(
					"deduct"	=> "业务提成",
					"statistics_deduct"		=> "提成统计",
				),
				"statistics"				=> array(
					"statistics"			=> "订单统计",
					"statistics_product"	=> "商品统计",
				),
				"change_pass"			=> array(
					"home"				=> "系统首页",
				)
			);
    
    ?>


    <div id="app" style="height:0%;height:0px;">
      <div id="ProMenu" style="position:fixed">
           <li>
            <a href="order.php" class="<?php if($menu_flag == 'order') echo 'highlight'?>">&nbsp;<i class="iconfont icon-dingdan" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;订单管理</a>
            <span class="drop">
            <?php foreach($menu_arr['order'] as $link => $name){?>
             <a href="<?=$link?>.php"><?=$name?></a>
            <?php }?>
            </span>
           </li>
           <li>
            <a href="consignment.php" class="<?php if($menu_flag == 'consignment') echo 'highlight'?>">&nbsp;<i class="iconfont icon-huoche" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;发货管理</a>
            <span class="drop">
                <?php foreach($menu_arr['consignment'] as $link => $name){?>
    	         <a href="<?=$link?>.php"><?=$name?></a>
    	        <?php }?>
            </span>
           </li>
           <li>
            <a href="client.php" class="<?php if($menu_flag == 'client') echo 'highlight'?>">&nbsp;<i class="iconfont icon-ic_local_pharmacy_px" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;药店管理</a>
            <span class="drop">
                <?php foreach($menu_arr['client'] as $link => $name){?>
    	         <a href="<?=$link?>.php"><?=$name?></a>
    	        <?php }?>
            </span>
           </li>
           <li>
            <a href="inventory.php" class="<?php if($menu_flag == 'inventory') echo 'highlight'?>">&nbsp;<i class="iconfont icon-kucun" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;库存状况</a>
            <span  class="drop">
             	<?php foreach($menu_arr['inventory'] as $link => $name){?>
    	         <a href="<?=$link?>.php"><?=$name?></a>
    	        <?php }?>
            </span>
           </li>
           <li>
            <a href="deduct.php" class="<?php if($menu_flag == 'deduct') echo 'highlight'?>">&nbsp;<i class="iconfont icon-business" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;业务提成</a>
            <span  class="drop">
             	<?php foreach($menu_arr['deduct'] as $link => $name){?>
    	         <a href="<?=$link?>.php"><?=$name?></a>
    	        <?php }?>
            </span>
           </li>
           <li>
            <a href="statistics.php" class="<?php if($menu_flag == 'statistics') echo 'highlight'?>">&nbsp;<i class="iconfont icon-tongji" style="font-size:16px;"></i>&nbsp;&nbsp;&nbsp;销售统计</a>
            <span class="drop">
                <?php foreach($menu_arr['statistics'] as $link => $name){?>
    	         <a href="<?=$link?>.php"><?=$name?></a>
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
    		$('body,html').animate({ scrollTop: 0 }, 1000);
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






