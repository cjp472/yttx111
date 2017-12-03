<?php
include_once ("header.php");
$menu_flag  = "home";
include_once ("arr_data.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>

    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/index.css"/>
    <script src="../scripts/vue.js"></script>
    <script src="../scripts/index.js"></script>
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/echarts.js" type="text/javascript"></script>
    <script src="../scripts/theme/macarons.js" type="text/javascript"></script>


    <script type="text/javascript">
        function opentlink(){
            window.open('changelog.php');
        }
    </script>

</head>
<style>

    @font-face {font-family: "iconfont";
        src: url('css/fonts/iconfont.eot?t=1489481378830'); /* IE9*/
        src: url('css/fonts/iconfont.eot?t=1489481378830#iefix') format('embedded-opentype'), /* IE6-IE8 */
        url('css/fonts/iconfont.woff?t=1489481378830') format('woff'), /* chrome, firefox */
        url('css/fonts/iconfont.ttf?t=1489481378830') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/
        url('css/fonts/iconfont.svg?t=1489481378830#iconfont') format('svg'); /* iOS 4.1- */
    }

    .iconfont {
        font-family:"iconfont" !important;
        font-size:16px;
        font-style:normal;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }



    .icon-diqu:before { content: "\e6b0"; }
    .icon-zhanghao:before { content: "\e613"; }
    .icon-hezuo-copy:before { content: "\e61b"; }
    .icon-dingdan:before { content: "\e61a"; }
    .icon-jingxuan:before { content: "\e602"; }
    .icon-tongji:before { content: "\e63f"; }
    .icon-Performance:before { content: "\e620"; }
    .icon-xiugaixinxi:before { content: "\e614"; }
    .icon-caigou:before { content: "\e608"; }
    .contain>ul>li{
        float:left;width:48%;height:17%;background-color:#fff;margin:2% 2% 0 0;box-shadow:1.5px 1.5px 1.5px 1.5px rgba(234,234,234,0.3),-1.5px -1.5px 1.5px 1.5px rgba(234,234,234,0.3);}
    .infos{
        height:45px;

        border-bottom: 1px solid #f4f4f4;
    }
    .infoz{
        height: 55px;
        font-size: 12px;
        border-bottom: 1px solid #f4f4f4;
    }
    .infoz>p{
        float: left;
        margin-left: 40px;
    }
    .infoz2{
        height: 45px;

        font-size: 12px;
        border-bottom: 1px solid #f4f4f4;
    }
    .infoz2>p{
        float: left;
        margin-left: 50px;;
    }



    .infox{
        font-size: 12px;;
    }
    .infox2{
        font-size: 12px;;
    }
    .infox2>p{
        float: left;
        margin-left: 50px;
    }
    p>a{font-weight:bold}
    .mo{display:block;width:100px;height:25px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;}



.over{white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
</style>
<?php 
//判断商业公司是否开通账期
$CompanyCreditSql = "select CompanyCredit from ".DATABASEU.DATATABLE."_order_company where CompanyID= ".$_SESSION['uc']['CompanyID']."";
$CompanyCreditSel = $db->get_row($CompanyCreditSql);
$_SESSION['uc']['CompanyCredit'] =  $CompanyCreditSel['CompanyCredit'];
?>
<?php include_once ("top.php");?>


<div class="contain" style="width: 100%;margin-top:-16px;height:1220px;background-color: #fafafa;">
    <ul style="width: 83.5%; margin:0.7% 0 0 140px;height:860px;">
        <li style="float:left;width:48%;height:17%;background-color:#fff;margin:2% 2% 0 0;">
            <div class="info">
                <div class="infos">
                    <span class="iconfont icon-zhanghao" style="font-size:24px;float:left;margin:5px 10px 0 12px;color:#F0C50F"></span><p style="font-size: 16px;float:left;margin-top:9px;">账户信息</p>
                </div>
                <div class="infoz">
                    <p style="margin-left:-60px;">姓名：<span><?=$_SESSION['uinfo']['usertruename']?></span></p>
                    <p>账号：<span><?=$_SESSION['uinfo']['username']?></span></p>
                    <p>职务：<span><?php if($_SESSION['uinfo']['UserPhone']) echo $_SESSION['uinfo']['UserPhone']; else echo '-';?></span></p>
                </div>
                <div class="infox">
                    <a href="./change_pass.php" style="color:#666;margin-left:20px;"><span style="float:left;margin:10px 10px 0 50px;" class="iconfont icon-xiugaixinxi"></span><p style="float:left;">修改密码</p></a>
                </div>
            </div>
        </li>
        <li style=" float:left;width:48%;height:17%;background-color:#fff;margin:2% 2% 0 0;">
            <div class="info">
                <div class="infos">
                    <span class="iconfont icon-hezuo-copy" style="font-size:24px;float:left;margin:5px 10px 0 12px;color:#f26a59"></span><p style="font-size: 16px;float:left;margin-top:9px;">合作</p>
                </div>
                <div class="infoz2">
                    <?php
//                 	$sql = "SELECT ClientFlag,COUNT(*) AS total FROM ".DATATABLE."_order_client WHERE ClientCompany=".$_SESSION['uinfo']['ucompany']." GROUP BY ClientFlag";
                	$sql = "SELECT COUNT(*) AS total FROM ".DATATABLE."_order_client WHERE ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0";
                	$clientInfo = $db->get_row($sql);

                    //冻结
                    $stoSql = "SELECT COUNT(*) AS total FROM ".DATATABLE."_order_client WHERE ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=1";
                    $stoInfo = $db->get_row($stoSql);
                    ?>
                    <p style="margin-left:-28px;">药店</p>
                    <p style="margin-left:50px;">正常：<a href="./client.php"><?=$clientInfo['total']?></a> 个</p>
                    <p >冻结：<a href="./client_recycle.php"><?=$stoInfo['total']?></a> 个</p>
                </div>


                <div class="infox2">
                    <p>客情</p>
                    <?php
					$datasql   = "SELECT COUNT(*) AS total FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserFlag!='1' and UserType='S'";
                	$saleInfo = $db->get_row($datasql);

                    //冻结
                    $stoSalesql   = "SELECT COUNT(*) AS total FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserFlag='1' and UserType='S'";
                    $stoSaleInfo = $db->get_row($stoSalesql);
                    ?>

                    <p style="margin-left:54px;">正常：<a href=""><?=$saleInfo['total']?></a> 个</p>
                    <p style="margin-left:71px;">冻结：<a href=""><?=$stoSaleInfo['total']?></a> 个</p>
                </div>
            </div>
        </li>
        <li style=" float:left;width:48%;height:17%;background-color:#fff;margin:2% 2% 0 0;">
            <div class="info">
                <div class="infos">
                    <span class="iconfont icon-dingdan" style="font-size:22px;float:left;margin:5px 10px 0 12px;color:#40C9D6"></span><p style="font-size: 16px;float:left;margin-top:9px;">订单</p>
                </div>

                <?php
					$osql = "select OrderStatus,count(*) as numstatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." group by OrderStatus";
					$oinfo = $db->get_results($osql);
                $totalorder = 0;
                foreach($oinfo as $ovar)
                {
                $totalorder = $totalorder + $ovar['numstatus'];
                $ordernumber[$ovar['OrderStatus']] = $ovar['numstatus'];
                }
                foreach($oinfo as $ovar)
                {
                $orderpencent[$ovar['OrderStatus']] = round($ordernumber[$ovar['OrderStatus']]/$totalorder*100,2);
                }

                $ssql = "select OrderSendStatus,count(*) as numsendstatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." group by OrderSendStatus";
                $sinfo = $db->get_results($ssql);
                foreach($sinfo as $svar)
                {
                $ordersendnumber[$svar['OrderSendStatus']] = $svar['numsendstatus'];
                }

                $psql = "select OrderPayStatus,count(*) as numpaystatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." group by OrderPayStatus";
                $pinfo = $db->get_results($psql);
                foreach($pinfo as $pvar)
                {
                $orderpaynumber[$pvar['OrderPayStatus']] = $pvar['numpaystatus'];
                }
                ?>

                <div class="infoz2">
                    <p style="margin-left:-28px;">订单数：<a href="./order.php"><?=$totalorder;?></a> 个</p>
                    <p style="margin-left: 200px;">待确认订单：<a href="./order.php?sid=0"><?=intval($ordernumber[0]);?></a> 个</p>
                </div>
                <div class="infox2">
                    <p>待付款订单：<a href="./order.php?pid=0"><?=intval($orderpaynumber[0]);?></a> 个</p>
                    <p style="margin-left: 176px;">待发货订单：<a href="./order.php?fid=1"><?=intval($ordersendnumber[1])?></a> 个</p>
                </div>
            </div>

        </li>
        <li style=" float:left;width:48%;height:17%;background-color:#fff;margin:2% 2% 0 0;">
            <div class="info">
                <div class="infos">
                    <span class="iconfont icon-jingxuan" style="font-size:22px;float:left;margin:5px 10px 0 12px;color:#FFA05F"></span><p style="font-size: 16px;float:left;margin-top:9px;">商品</p>
                </div>
                <?php
				   $psql = "SELECT COUNT(*) AS total FROM ".DATATABLE."_order_content_index WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." AND FlagID=0";
				   $pinfo = $db->get_row($psql);
                ?>

                <div class="infox2">

                    <?php
				   //下架
				   $stoPsql = "SELECT COUNT(*) AS total FROM ".DATATABLE."_order_content_index WHERE CompanyID=".$_SESSION['uinfo']['ucompany']." AND FlagID=1";
				   $stoPpinfo = $db->get_row($stoPsql);

                    ?>
                    <div class="infoz2">
                        <p style="margin-left:-28px;">在售：<a href="./product.php"><?=$pinfo['total'];?></a> 个</p>
                        <p style="margin-left: 200px;">下架：<a href="./product_recycle.php"><?=$stoPpinfo['total'];?></a> 个</p>
                    </div>
                    <div class="infox2">
                        <?php
							$bsql = "select count(*) as total from ".DATATABLE."_order_brand where CompanyID=".$_SESSION['uinfo']['ucompany'];
							$binfo = $db->get_row($bsql);
                        ?>
                        <p style="margin-left:50px;">药厂：<a href="./product_brand.php"><?php echo intval($binfo['total']);?></a> 个</p>

                    </div>
                </div>
        </li>

        <!-- 统计饼图开始 -->
        <!-- 商品销售排行版 -->
        <?php include_once ("home_product_top.php");?>

        <!-- 业绩图 -->
        <?php include_once ("home_sell_top.php");?>

        <!-- 客户采购分布 -->
        <?php include_once ("home_client_top.php");?>

        <!-- 地区采购分布 -->
        <?php include_once ("home_area_top.php");?>

        <!-- 统计饼图结速 -->




</ul>
</div>




<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<div id="mask" class="xftx" style="height:100%;width:100%;position:fixed;background:#000;top:0px;z-index:9999999;display:none;">
</div>
<div class="xftx" id="xftx_content" style="width:490px;height:250px;background:#fff;margin:80px auto;border-radius:3px;z-index:999999990;position:absolute;top:0px;display:none;">
    <div style="border-bottom:1px solid #ccc;background:rgb(106,158,218);height:45px;line-height:45px;margin-bottom:0px;">
        <h2 style="color:#fff;text-align:center;font-weight:150;margin:0px;">续费提醒</h2>
    </div>
    <div style="height:130px;margin:0px;">
        <?php
        if($tdate < 1)
        {
            echo '<h2 style="padding-top:50px;text-align:center;">您的账号已到期，请及时续费！</h2>';
        }
        else if($tdate < 15)
        {
        echo '<h2 style="padding-top:50px;text-align:center;">当前离账号到期仅有：&nbsp;
        <span style="background:url(img/tx_bg.jpg) no-repeat;display:inline-block;width:29px;height:39px;vertical-align:middle;font-size:22px;color:#fff;">'.$tdate.'</span>
        &nbsp;天,请及时续费!</h2>';
        }
        ?>
    </div>
    <div style="text-align:center;">
        <button onclick="window.location.href='/pro/buy_product.php';" style="font-family:微软雅黑;cursor:pointer;width:140px;height:40px;background:rgb(106,158,218);border:none;border-radius: 3px;color:#fff;font-size:14px;margin-right:10px;">立即缴费</button>
        <button onclick="$('.xftx').remove();" style="font-family:微软雅黑;cursor:pointer;width:140px;height:40px;background:#f5f5f5;border:1px solid #ccc;border-radius: 3px;color:#000;font-size:14px;box-shadow:inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);">关闭</button>
    </div>
</div>

<?php
    $company_id = $_SESSION['uinfo']['ucompany'];
    $cs_flag = $db->get_var("SELECT CS_Flag FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");
$cs_last_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_check_log WHERE CompanyID={$company_id} ORDER BY Time DESC LIMIT 1");

if($cs_flag == 'F') {
?>
<div id="Company_Check_Data_Faild" style="display:none;width:350px;">
    <div class="windowHeader">
        <h3 id="windowtitle">实名认证未通过提示</h3>
        <div class="windowClose"><div class="close-form" onclick="$.unblockUI()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <div style="font-size:14px;text-align:left;text-indent:2em;padding-top:15px;">
            <?php echo $cs_last_info['Remark']; ?>
        </div>
        <div style="margin-top:15px;border-top:2px solid #CCC;padding-top:15px;">
            <input type="button" value="重新上传" class="button_1" onclick="window.location.href='company_upload.php';"/>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $.blockUI({
            message : $("#Company_Check_Data_Faild")
        });
    });
</script>
<?php } ?>


<script type="text/javascript" src="../scripts/jquery.cookie.js"></script>
<?php 
	include_once 'bottom_common.php';
?>
</body>
</html>