<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta content=always name=referrer>
    <!--compatible兼容  设置浏览器兼容模式-->
    <!--edge win10自带浏览器  如果不是win10 就使用最新的浏览器渲染页面-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<link rel="shortcut icon" href="/favicon.ico" />
    <link href="template/red/css/base_index.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
    <link href="template/red/css/index.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
    <link href="template/red/css/normalize.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
    <link href="template/red/css/base.css" rel="stylesheet" type="text/css">
    
    <script src="template/js/jquery.js" type="text/javascript"></script>
    <script src="template/js/jquery.adScroll.js" type="text/javascript"></script>
    <script src="template/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="template/js/lrtk.js" type="text/javascript" charset="UTF-8"></script>
<script>
    $(document).ready(function(){
        $("#dao_ad").adScroll({
        		line:1,
        		speed:1000,
        		timer:1000,
        		maxLine:1
        	});
    });
</script>
    <style type="text/css">
        *{margin:0;padding:0;list-style-type:none;}
        a,img{border:0;}
        /* flexslider */
        .flexslider{height:446px;position: relative;
            width: 100%;overflow:hidden;}
        .slides{z-index:1;}
        .slides li{height:446px;}
        .flex-control-nav{position:absolute;bottom:10px;z-index:999;width:100%;text-align:center;}
        .flex-control-nav li{display:inline-block;width:14px;height:13px;margin:0 5px;*display:inline;zoom:1;}
        .flex-control-nav a{display:inline-block;width:14px;height:13px;line-height:40px;overflow:hidden;background:url(./template/red/images/dot.png) right 0 no-repeat;cursor:pointer;}
        .flex-control-nav .flex-active{background-position:0 0;}

        .flex-direction-nav{position:absolute;z-index:3;width:100%;top:45%;}
        .flex-direction-nav li a{display:block;width:50px;height:50px;overflow:hidden;cursor:pointer;position:absolute;}

        .mask {
            background: rgba(0, 0, 0, .5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display:block;
            z-index:999999;
            display:none;

        }
        .logan {
            width: 464px;
            height: 493px;
            margin: 0px auto;
            position: relative;
            top:-493px;
            /*animation:mymove 1s infinite;*/
            /*/!*Safari 和 Chrome:*!/*/
            /*-webkit-animation:mymove 1s infinite;*/
            /*animation-iteration-count:1;*/
        }

        .Customer{
            color: #fff !important;
        }
        .helpCustomer {
            opacity: 0;
        }

        .helpCustomer1{
            opacity: 0;
        }
        .dao_ad{
            width: 500px;
            height: 24px;
            overflow: hidden;
            float: left;
            margin-top: 0px;
            margin-left: 110px;
            text-indent: 15px;
         }


        /*@keyframes mymove*/
        /*{*/
        /*from {top:-493px;}*/
        /*to {top:1px}*/
        /*}*/

        /*/!*Safari 和 Chrome:*!/*/
        /*@-webkit-keyframes mymove*/
        /*{*/
        /*from {top:-493px;}*/
        /*to {top:1px}*/
        /*}*/

    </style>


<script type="text/javascript">





$(document).ready(function(){

//平滑回到顶部
$('#comeback').bind('click', function(){
$('body,html').animate({ scrollTop: 0 }, 1000);
});

//banner大图覆盖层，临时解决方案
$('ul.menu-box > li').bind({
'mouseover' : function(){
$('.top2').css('width', '1000px');
},
'mouseout' : function(){
$('.top2').css('width', 'auto');
}
});
});

//收藏
function AddToFavorite()   
{   
  title = document.title; 
  url = document.location; 
  try { 
    // Internet Explorer 
    window.external.AddFavorite( url, title ); 
  } 
  catch (e) { 
    try { 
      // Mozilla 
      window.sidebar.addPanel( title, url, "" ); 
    } 
    catch (e) { 
      // Opera 
      if( typeof( opera ) == "object" ) { 
        a.rel = "sidebar"; 
        a.title = title; 
        a.url = url; 
        return true; 
      } 
      else { 
        // Unknown 
        alert( '您的浏览器不支持,请按 Ctrl+D 手动收藏!' ); 
      } 
    } 
  } 
  return false;  
} 
</script>
</head>
<body>
<div class="top">
    <div class="top_info">
        <div class="w">
            <div class="fl">
                <div class="green fl">Hi, <b><?=$_SESSION['cc']['ctruename']?><?=$c_level?></b> 欢迎登陆医统BMB平台&nbsp;&nbsp;
                	<a href="login.php?m=logout" class="exit">[退出]</a>

               		<a href="javascript:;" onclick="AddToFavorite();" class="xx" >
               			<span class="ali-small-16 iconfont icon-wujiaoxing"></span>&nbsp;收藏医统
                    </a>

                 	<a href="" class="xx" id="xx">
                 		<span class="ali-small-16 iconfont icon-hert" style=""></span>&nbsp;关注我们
                     	<img src="./images/ylkj.png" class="erma"  alt="" style="width: 230px;height: 130px;"/>
                    </a>

             	</div>
             </div>
        <div class="fr" style="margin-right:0;">
            <div class="ccc fl">
                <img src="./template/red/images/che.png" onclick="javascript:window.location.href='cart.php'" />

                <span onclick="javascript:window.location.href='cart.php'" id="cartnumber_2">
<? if(empty($_SESSION['cartitems'])) { ?>
0
<? } else { echo count($_SESSION['cartitems']);; } ?>
</span>
                <div class="clear"></div>
            </div>
            <a href="my.php?m=profile"><img src="./template/red/images/geren.png" class="person" alt=""/>用户中心</a>
            <img src="./template/red/images/m.png" class="money" />
            当前余额：<b class="a-btn" style="font-size:18px;"><? echo commondata::get_amount(); ?></b>&nbsp;元<a class="a-btn" href="finance.php?m=new&t=y"> [充值] </a></div>
    </div>
    </div>
    <div class="fix">

    <div class="w" style="border: 1px solid transparent;">

    <a href="home.php">
    
<? if(empty($_SESSION['ucc']['CompanyLogo'])) { ?>
    	<img src="<?=CONF_PATH_IMG?>images/logo.png" class="logo" style="width: 210px;height: 64px;" alt="<?=$_SESSION['ucc']['CompanyName']?> - 医统天下BMB系统" />
    
<? } else { ?>
    	<img src="<?=RESOURCE_PATH?><?=$_SESSION['ucc']['CompanyLogo']?>" class="logo" style="width: 210px;height: 64px;" alt="<?=$_SESSION['ucc']['CompanyName']?> - 医统天下BMB系统" border="0" />
    
<? } ?>
    </a>

    	<div class="zheng fr" style="margin-right:0;">
            <img src="./template/red/images/zhenga.png" class="" alt=""/>
        </div>
    	<div style="height:38px;width:480px;float:right;margin-right:70px;margin-top:30px;border:2px solid #04A057;">
    	<form name="searchform" id="searchform" action="search.php" method="get" style="display:inline;">
        <input type="text" size="50" maxlength="30" class="span4 search" id="search" name="kw" autocomplete="off" style="color:#A9A9A9;font-size:14px" placeholder="输入商品名称|商品编号|药企名称" />
                <button name="searchbutton" class="search-btn f-l index-search-botton" type="submit" ><img src="./template/red/images/sousuo.png" class="fdj" style="width: 16px;height: 16px;" alt=""/>  搜索</button>
                <div class="mohu" style="display:none;">
                    <ul id="vague_data">
     
                    </ul>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>

<!-- 
<? if(!empty($_SESSION['Initial_pass'] )) { ?>
<div class="tip">
    <p style="font-size: 14px;line-height:28px;margin-top:5px">尊敬的客户：您现在使用的初始密码，为了保障账户安全，建议您&nbsp;&nbsp;<a href="my.php?m=password" style="color:#fff;background-color: #ff8d32;">前往修改</a> </p>
</div>
<? } ?>
-->
 
<div class="nav">
    <div class="nav-info w">
    	<ul style="width: 218px;float:left;">
    		<li>
            	<p>
            		<img src="./template/red/images/flan.png" class="san" alt=""/>
            		所有分类
            	</p>
            </li>
    	</ul>
    	<div class="nav-box">
        <ul>
            <li class="cur"><a href="home.php" class="">首页</a></li>
            <li ><a class="aa" href="list.php">商品中心</a></li>
            <li ><a class="aa" href="brand.php">品牌街</a></li>
            <li ><a class="aa" href="myorder.php">我的订单</a></li>
            <li ><a class="aa" href="consignment.php">发货单</a></li>
            <li ><a class="aa" href="return.php">退货单</a></li>
            <li ><a class="aa" href="finance.php">付款单</a></li>
            <li ><a class="aa" href="statistics.php">数据统计</a></li>
            <li ><a class="aa" href="forum.php">在线客服</a></li>
            <li ><a class="aa" href="infomation.php">信息公告</a></li>
         </ul>
         <div class="clear"></div>
         <div class="nav-trigon"></div>
         <div class="clear"></div>
     </div>
        <div class="top2" style="width:auto;">
                     <div class="top2-left">
                         <ul class="menu-box">					 
 
<? $loopnum = 0; if(is_array($listallsite)) { foreach($listallsite as $skey => $svar) { if($loopnum < 11) { ?>
                <li data-id="<?=$svar['SiteID']?>">
                    <div class="li-l">
                    	<a href="list.php?s=<?=$svar['SiteID']?>&t=<?=$in['t']?>">&nbsp;&nbsp;&nbsp;&nbsp;<?=$svar['SiteName']?></a>
                    </div>

                    <div class="li-r" style="z-index: 9999">
<? if(empty($svar['son'])) { ?>
<span class="menu-stock"></span>
<span style="color:#000;padding-left:20px"><a href="list.php?s=<?=$svar['SiteID']?>&t=<?=$in['t']?>"><?=$svar['SiteName']?></a></span>
<div class="menu-border"></div>
<ul>
<li><a href="javascript:;">&nbsp;</a></li><br>
</ul>
<? } if(is_array($svar['son'])) { foreach($svar['son'] as $key => $vr) { ?>
                        <span class="menu-stock"></span>
                        <span style="color:#000;padding-left:20px"><a href="list.php?s=<?=$vr['SiteID']?>&t=<?=$in['t']?>"><?=$vr['SiteName']?></a></span>
                        <div class="menu-border"></div>
                        <ul style="">
<? if(empty($vr['son'])) { ?>
                        	<li><a href="javascript:;">&nbsp;</a></li>
<? } else { if(is_array($vr['son'])) { foreach($vr['son'] as $k => $v) { ?>
                            <li><a href="list.php?s=<?=$v['SiteID']?>&t=<?=$in['t']?>"><?=$v['SiteName']?></a></li>
<? } } } ?>
                        </ul>
<? } } ?>
                    </div>

                </li>

                
<? } ?>
            
<? $loopnum++; } } ?>
                         </ul>
 	<div class="clear"></div>
                     </div>

                 </div>
    </div>   
    <div class="clear"></div>
    </div>
<div class="lin"></div>

<div class="htmleaf-container">
    <div class="flexslider">
        <ul class="slides" style="z-index: 99">
        
        
<? if(!empty($xd_info)) { ?>
           
<? if(is_array($xd_info)) { foreach($xd_info as $key => $info) { ?>
           	 	<li style="background:url('<?=RESOURCE_PATH?><?=$info['ArticlePicture']?>') 50% 0 no-repeat;">
           	 	<a href="<?=$info['ArticleLink']?>" target="_blank" style="display:block;width:100%;height:445px;">&nbsp;</a>
           	 	</li>
           
<? } } ?>
        
<? } else { ?>
        <li style="background:url('<?=CONF_PATH_IMG?>images/index1.jpg') 50% 0 no-repeat;background-size:cover;">
           <a href="<?=$info['ArticleLink']?>" target="_blank" style="display:block;width:100%;height:445px;">&nbsp;</a>
        </li>
        <li style="background:url('<?=CONF_PATH_IMG?>images/index2.jpg') 50% 0 no-repeat;background-size:cover;">
           <a href="<?=$info['ArticleLink']?>" target="_blank" style="display:block;width:100%;height:445px;">&nbsp;</a>
        </li>
        <li style="background:url('<?=CONF_PATH_IMG?>images/index3.jpg') 50% 0 no-repeat;background-size:cover;">
           <a href="<?=$info['ArticleLink']?>" target="_blank" style="display:block;width:100%;height:445px;">&nbsp;</a>
        </li>
        <li style="background:url('<?=CONF_PATH_IMG?>images/index4.jpg') 50% 0 no-repeat;background-size:cover;">
           <a href="<?=$info['ArticleLink']?>" target="_blank" style="display:block;width:100%;height:445px;">&nbsp;</a>
        </li>
        
<? } ?>
        </ul>
    </div>
</div>

<div class="public">



</div>
<div class="icon w">
    <ul>
        <li class="fast"><a href="myorder.php"><img src="./template/red/images/DG.png" class="xwcms" alt=""/><p>一键复购</p></a></li>
        <li><a href="myorder.php?m=product"><img src="./template/red/images/DD.png" class="xwcms" alt=""/><p>我订过的</p></a></li>
        <li><a href="list.php?m=spc"><img src="./template/red/images/cx.png" class="xwcms" alt=""/><p>优惠促销</p></a></li>
        <li><a href="wishlist.php"><img src="./template/red/images/SC.png" class="xwcms" alt=""/><p>我的收藏</p></a></li>
        <li><a href="list.php?m=spc&ty=4"><img src="./template/red/images/rx.png" class="xwcms" alt=""/><p>热销商品</p></a></li>
        <li><a href="consignment.php"><img src="./template/red/images/fh.png" class="xwcms" alt=""/><p>&nbsp&nbsp发货单</p> </a></li>
        <li><a href="infomation.php"><img src="./template/red/images/xx.png" class="xwcms" alt=""/><p>信息公告</p></a></li>
    </ul>
</div>

<div class="line w">
    <div class="green-border"></div>
</div>
<!--喇叭-->
<!--<div class="w" style="margin-top: 5px;">-->
<div class="pu w">
    <img class="la" src="./template/red/images/gonggao.png" />
    <div id="dao_ad" class="dao_ad">
        <ul>
        
<? if(is_array($gg_info)) { foreach($gg_info as $ggkey => $ggvar) { ?>
            
<? if(isset($ggvar['type']) && $ggvar['type'] ==2 ) { ?>
            <li><a href="infomation.php?ty=plat&m=content&sid=0&ID=<?=$ggvar['id']?>" target="_blank"><font color="red">[平台]</font> <?=$ggvar['title']?></a></li>
<? } else { ?>
<li><a href="infomation.php?m=content&sid=0&ID=<?=$ggvar['ArticleID']?>" target="_blank"><font color="<?=$ggvar['ArticleColor']?>">· <?=$ggvar['ArticleTitle']?></font></a></li>
<? } ?>
        
<? } } ?>
        </ul>
    </div>
</div>

<div class="heart w">
    <img src="./template/red/images/banner.jpg" style="margin-top: 3px;"/>
</div>

<div class="hot w" style="margin-top:20px;">
    <p class="" style="line-height:40px;"><span class="speedgou too-hot"></span>&nbsp;&nbsp;热门推荐</p>
    <div class="green-border"></div>
    <div class="sp">
        <ul>
        
<? if(is_array($goodslist4['list'])) { foreach($goodslist4['list'] as $gkey => $gvar) { ?>
            	<li 
<? if(($gkey+1) % 4 == 0) { ?>
style="border-right:none;"
<? } ?>
>
            		<a href="content.php?id=<?=$gvar['ID']?>" target="_blank">
            
<? if(!empty($gvar['Picture'])) { ?>
<img src="<?=RESOURCE_PATH?><? echo str_replace('thumb_', 'img_', $gvar['Picture']); ?>" width="150" height="150"/>
<? } else { ?>
<img src="<?=CONF_PATH_IMG?>images/default.jpg" width="150" height="150"/>
<? } ?>
            		</a>
            		<a href="content.php?id=<?=$gvar['ID']?>" style="height:auto;" target="_blank"><?=$gvar['Name']?></a>
            		<span>¥ <?=$gvar['Price']?> 元/<?=$gvar['Units']?></span>
            	</li>
            
<? } } ?>
        </ul>
        <div class="clear"></div>
    </div>
</div>
<div class="med w hot" style="margin-top:40px;">
       <p class="" style="line-height:40px;"><span class="speedgou to-hot"></span>&nbsp;&nbsp;品牌推荐</p>
        <div class="green-border"></div>
        <div class="y">
            <ul>
 
<? if(is_array($brand_info)) { foreach($brand_info as $sonkey => $sonvar) { ?>
                <li>
                	<a href="list.php?b=<?=$sonvar['BrandID']?>&t=<?=$in['t']?>">
                
<? if($sonvar['Logo']) { ?>
                		<img src="<?=RESOURCE_PATH?><? echo str_replace('thumb_', 'img_', $sonvar['Logo']); ?>" class="on-logo" />
                
<? } else { ?>
                		<img src="<?=CONF_PATH_IMG?>images/defaultbrand.gif" class="on-logo" />
                		
<? } ?>
                	</a>
                	<a href="list.php?b=<?=$sonvar['BrandID']?>&t=<?=$in['t']?>" style=" overflow:hidden;text-overflow:ellipsis; -o-text-overflow:ellipsis;white-space:nowrap;width:100%;"><?=$sonvar['BrandName']?></a>
                	<img style="margin:0px;overflow:hidden;width:248px;" src="./template/red/images/online.png" class="on" />
                	<a class="ftu" href="list.php?b=<?=$sonvar['BrandID']?>&t=<?=$in['t']?>"><?=$sonvar['total']?>个</a>
                </li>
                
<? } } ?>
                <div class="clear"></div>
            </ul>
        </div>
        <div class="clear"></div>
</div>

<div class="footer w">
<div class="fen w">
    <ul class="fll">
        <li><a href="home.php">首页</a></li>
        <li><a href="list.php">商品中心</a></li>
        <li><a href="list.php?m=spc">优惠促销</a></li>
        <li><a href="myorder.php">我的订单</a></li>
        <li style="border:none;"><a href="statistics.php?m=finance">款项信息</a></li>
    </ul>
    <p class="p1">Copyright&nbsp©&nbsp2014 - <?=date('Y')?>&nbsp医统天下（北京）网络科技有限公司</p>
    <p class="p2">互联网药品交易服务资格证书国A20150005号，京ICP备14037820号，京公网安备11010102001371号 电话 ：400-855-9111 邮箱：info@yitong111.com</p>
    <ul class="po">
        <li style="border:none;"><a href="http://qyxy.baic.gov.cn/" target="_blank"><img src="./template/red/images/1.png" alt=""/></a></li>
        <li style="border:none;"><a href="http://www.szfw.org/" target="_blank"><img src="./template/red/images/2.png" alt=""/></a></li>
        <li style="border:none;"><a href="http://www.miitbeian.gov.cn/state/outPortal/loginPortal.action;jsessionid=cDGTW7JfLhXMLn8QWW130Kyhf1TFTpSWHHNCgv2QkvscR2nBLfYH!1794355333" target="_blank"><img src="./template/red/images/3.png" alt=""/></a></li>
        <li style="border:none;"><a href="http://app1.sfda.gov.cn/datasearch/face3/base.jsp?tableId=28&tableName=TABLE28&title=%BB%A5%C1%AA%CD%F8%D2%A9%C6%B7%D0%C5%CF%A2%B7%FE%CE%F1&bcId=118715637133379308522963029631" target="_blank"><img src="./template/red/images/4.png" alt=""/></a></li>
        <li style="border:none;"><a href="http://app1.sfda.gov.cn/datasearch/face3/base.jsp?tableId=28&tableName=TABLE28&title=%BB%A5%C1%AA%CD%F8%D2%A9%C6%B7%D0%C5%CF%A2%B7%FE%CE%F1&bcId=118715637133379308522963029631" target="_blank"><img src="./template/red/images/5.png" alt=""/></a></li>
    </ul>
</div>
</div>

<div class="shopingcar" style="position: fixed;right:6px;bottom:350px;cursor: pointer;z-index: 2000">
    <a href="cart.php"><span class="iconfont icon-circularorder"  style="font-size: 40px;color: #01A157"></span></a>

    
<? if(!empty($_SESSION['cartitems'])) { ?>
    <span onclick="javascript:window.location.href='cart.php'" style="width: 20px;height: 20px;line-height:20px;text-align:center;position:absolute;left:20px;top:0px;border-radius:10px;background-color: #FF8E32;">
<? if(count($_SESSION['cartitems']) > 99) { echo count($_SESSION['cartitems']);; ?>+
<? } else { echo count($_SESSION['cartitems']);; } ?>
</span>
    
<? } ?>
</div>

<div class="Services" style="position: fixed;width: 120px;right:6px;bottom: 300px;cursor: pointer;z-index:2000">
    <div class="help1" style="float: right;width: 40px;height: 43px;"><span class="iconfont icon-kefu" style="font-size: 40px;color: #01A157" alt=""/></div>
    <div class="helpCustomer" style="width: 120px;background-color: #fff;position:absolute;box-shadow: 2px 2px 2px #ddd,-2px -2px 2px #ddd;bottom:-175px;right: 0px;color:#fff">
        <div class="help2" style="width:94px;height:18px;line-height:18px;margin:10px auto;border-radius:50px;text-align:center;font-size:12px;background-color: #34b479;"><a class="Customer" style="color: #fff !important;" href="templatel/unicom/customerFirst.html">联系我们</a></div>
        <div class="help3" style="width:90px;height:40px;margin:0 auto;font-size:12px;color: #666"><?=$customer_service['ContactName']?><?=$Symbol?><br>
            <?=$customer_service['ContactValue']?>
        </div>
        <div class="help4" style="width:90px;height: 40px;margin:0 auto;font-size:12px;color: #666">医统客服:<br>
            0371-60942268</div>
        <div class="help_er" style="height: 100px;">
            <div class="help_bar" style="width: 90px;height: 90px;margin: 0 auto">
            <img src="./template/red/images/help_bar.jpg" style="border: 2px solid #34b479;" alt="" />
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





<img id="comeback" src="./template/red/images/xiangs.png" style="width: 40px;height: 42px;position: fixed;right:8px;bottom: 100px;cursor: pointer;display:none;" alt=""/>

<!-- 随屏搜索 -->
<style type="text/css">
.fixed-head{height:83px;width:100%;position:fixed;background:#fff none repeat scroll 0 0 !important;z-index:9999;border-bottom:1px solid #eee;box-shadow:0 2px 20px rgba(0,0,0,0.1);display:block;top:-89px}
.fixed-head > .w > img{float:left;height:55px;margin:12px 0 0 70px;width:180px}
img{border:0 none;vertical-align:middle}
fieldset,img,input,button{border:medium none;margin:0;outline-style:none;padding:0}
.search-input{color:#615f5f;font-size:14px;line-height:47px;height:47px;width:510px;padding-left:15px}
.search-head > form > button{background-color:#01a157;height:47px;margin-top: 0px;width:75px;cursor:pointer;float:right;}
.search-head > button:hover{opacity:0.9}
.search-head{height:47px;margin-left:40px;margin-top:15px;width:600px;border: 2px solid #01a157;}
</style>

<div class="fixed-head" id="fixed-head">
    <div class="w">
    	<img src="template/red/images/logo.png" style="cursor:pointer;" onclick="javascript:window.location='home.php'" />
        <div class="search-head fl">
        	<form name="searchform" id="searchform" action="search.php" method="get" style="display:inline;">
        	<input type="text" class="search-input" name="kw"  autocomplete="off" id="search1" placeholder="输入商品名称|商品编号|药企名称" />
            <button><img src="template/red/images/ss.png" /></button>
                <div class="mohu1" style="display:none;">
                    <ul id="vague_data1">
                        
                    </ul>
                </div>
            </form>
        </div>
        <div class="clear"></div>
</div>
</div>

<!-- 浏览器  -->
<div class="b_show" id="branser-notice" style="z-index:9998;display:none;height: 59px;line-height:59px;width: 100%;position:fixed;top:0;background-color:#fff;box-shadow:0 2px 20px rgba(0,0,0,0.2);border-bottom: 1px solid #dbdbdb;">
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


<div class="mask" >
    <div class="logan" style="">

        <img src="template/tpl/unicom/motai.png" alt="" style="width: 464px;height: 493px;"/>
        <span class="iconfont icon-guanbi1" style="color:#4bc7a8;font-size:28px;position: absolute;top:41%;right:2%;cursor: pointer"></span>

        <div id="important_notice" style="position: absolute;top: 260px;margin: 0 6px 0 10px;">
            
        </div>
    </div>

    </div>
    </div>
</div>


<script>

    $(document).ready(function(){
        $(".logan").animate({
            top:'0px'
        },1000);
    });


</script>

<script>
    $(document).ready(function(){
        $(".Services").mouseenter(function(){
            $(".helpCustomer").show();
            $(".helpCustomer").css("right","30");
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
            $(".helpCustomer1").css("right","30");
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
<script>

$().ready(function() { 
var mask  = $(".mask");
var logan = $(".logan");
var close = $("span.icon-guanbi1");
var url="platform_notice.php";
$.post(url,{m:"important_notice"},function(rsp){
if(rsp.status ==1){
//setCookie("the_cookie_num", "STOP", -1); // 存储一个带1天期限的 cookie

    if(getCookie('the_cookie_num')==null || getCookie('the_cookie_num')=="" ) {
$("#important_notice").html(rsp.data);
mask.css("display","block");

close.bind('click', function(){
setCookie("the_cookie_num", "STOP",1); // 存储一个带1天期限的 cookie
mask.hide();
});

    }

}
},"json");

function setCookie(name,value,Days)
{
if(Days == '')  Days = 1;
var exp = new Date();
exp.setTime(exp.getTime() + Days*24*60*60*1000);
document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}

function getCookie(name)
{
var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
if(arr=document.cookie.match(reg))
return unescape(arr[2]);
else
return null;
}




}); 

</script>

<script type="text/javascript">

var animateFlag=1;$(document).scroll(function()
{var scrollTop=$(document).scrollTop();if(scrollTop>400){$('#comeback').fadeIn('slow');$("#fixed-head").css('display','block');
    if(animateFlag){animateFlag=0;$("#fixed-head").stop();
    $("#fixed-head").animate({'top':'0px'},500);
    }}
       else{$(".searchRe").css('display','none');
    $('#comeback').fadeOut('slow');
    if(animateFlag==0)
    {animateFlag=1;$("#fixed-head").stop();
    $("#fixed-head").animate({'display':'none','top':'-89px'},500)
        $(".mohu1").css("display","none");
    }}});
</script>

<script src="template/js/jquery.flexslider-min.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('.flexslider').flexslider({
            directionNav: true,
            pauseOnAction:true
        });
    });
    
    //淡出浏览器版本提示
if(!$.support.leadingWhitespace){
var _b = $('#branser-notice');
_b.show();
setTimeout(function(){
_b.fadeOut('slow');
}, 5000);
}
    
    //公告


//右侧导航自适应高度

    $(".li-l").mouseenter(function(){
var lir_height=$(this).next().height();
//alert(lir_height);
        if(lir_height<435){
            $(this).next().css("height","435px");
        }else{
            //$(this).next().css("height","auto");
        }
    })

//去空隔函数 
function Jtrim(str){ 
return str.replace(/^\s*|\s*$/g,"");  
} 	
//模糊搜索
$(document).ready(function(){
$('#search').bind('input propertychange', function() {

var val=$(this).val();

$.ajax({
 type: 'GET',
 url: "vague_search.php" ,
data: {"kw":val} ,
success: function(datas){

if(datas!='' && datas.list.length >0){
$(".mohu").css("display","block");
var html="";
　$.each(datas.list, function(i, item){ 
html+='<li class="select_li"><p style="float: left;">'+item.Name+'</p><p>约有<span>'+item.c_num+'</span>个结果</p></li>';
$("#vague_data").html(html);
　　	});
}

$(document).click(function(){
$(".mohu").css("display","none");
});

$(".select_li").click(function(){
var select_vals=$(this).find("p:first-child").html();
select_vals = Jtrim(select_vals);
$(".mohu").css("display","none");
//$('#search').val(select_vals);
window.location.href="search.php?kw="+select_vals+"&action=vague";
});


} ,
dataType: "json"
});
}); 
 });




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
select_vals = Jtrim(select_vals);
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
<!--消息推送-->
<? include template('bottom_common'); ?>
</body>
</html>