<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate']     = date("Y-m-d");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

$urlmsg = "product_statistics.php?begindate=".$in['begindate']."&enddate=".$in['enddate']."";

$saler_sql = "SELECT UserID,UserName,UserTrueName,UserPhone,UserLogin,UserLoginIP,UserLoginDate,UserFlag FROM ".DATABASEU.DATATABLE."_order_user where UserCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and UserFlag!='1' and UserType='S' AND UserID=".$in['uid']." ORDER BY UserID DESC";
$saler = $db->get_row($saler_sql);
print_r($saler);
if(!empty($in['cid'])) $urlmsg .= "&cid=".$in['cid'];
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="css/showpage.css" />

        <script src="js/statistics.js?v=<? echo VERID;?>" type="text/javascript"></script>
        <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
        <link rel="stylesheet" href="css/jquery.treeview.css" />

        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
        <script src="../scripts/jquery.treeview.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
        <style type="text/css">
            .page_bar input{
                height:22px;
            }
        </style>
        <script type="text/javascript">
            $(function() {
                $("#begindate").datepicker();
                $("#enddate").datepicker();
            });
        </script>
    </head>

    <body>
    <?php include_once ("top.php");?>
    <div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <div class="leftdiv">

        </div>

        <div class="location"><strong>当前位置：</strong> <a href="statistics_saler.php">客情官订单统计</a></div>
    </div>

    <div class="line2"></div>
    <div class="bline">
    <div id="sortright" style="width:100%;">
        <form id="MainForm" name="MainForm" method="post" action="statistics_product_excel.php?action=product"  >
            <input name="clientid" id="clientid" type="hidden" value='<? if(!empty($in['cid'])) echo $in['cid'];?>' />
            <input name="siteid" id="siteid" type="hidden" value='<? if(!empty($in['sid'])) echo $in['sid'];?>' />
            <div class="line" >
                <fieldset class="fieldsetstyle">
                    <legend>客情官<?php echo $saler['UserName'].'('.$saler['UserTrueName'].')['.$in['begindate'].'到'.$in['enddate'].']'; ?>订单统计</legend>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                       <!-- <tr>
                            <td height="35" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;<input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<?/* echo $in['begindate'];*/?>"   />&nbsp;到&nbsp;<input name="enddate" type="text" id="enddate" maxlength="12" onfocus="this.select();" value="<?/* echo $in['enddate'];*/?>" />&nbsp;<input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" onclick="show_product_stat_data('between')"/>&nbsp;&nbsp;<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_product_excel('product');" /></td>
                        </tr>-->
                        <?php
                        if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
                        {
                            echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
                        }else{
                            $begin = strtotime(date('Y-m-d 00:00:00',strtotime($in['begindate'])));
                            $end = strtotime(date('Y-m-d 23:59:59',strtotime($in['enddate'])));
                            $sql = "SELECT OrderTotal,SalerID,OrderID,OrderSN,OrderDate,OrderCompany,ClientCompanyName
                                    FROM rsung_order_orderinfo AS o
                                    LEFT JOIN rsung_order_client AS c
                                    ON c.ClientID = o.OrderUserID
                                    LEFT JOIN rsung_order_salerclient AS s
                                    ON s.ClientID = c.ClientID
                                    WHERE o.OrderStatus < 8 AND s.SalerID=".$in['uid']."
                                    AND OrderCompany=".$_SESSION['uc']['CompanyID']."
                                    AND OrderDate BETWEEN ".$begin." AND ".$end;
                            $sql_num = "SELECT count(*) as cnt
                                        FROM rsung_order_orderinfo AS o
                                        LEFT JOIN rsung_order_client AS c
                                        ON c.ClientID = o.OrderUserID
                                        LEFT JOIN rsung_order_salerclient AS s
                                        ON s.ClientID = c.ClientID
                                        WHERE o.OrderStatus < 8 AND s.SalerID=".$in['uid']."
                                        AND OrderCompany=".$_SESSION['uc']['CompanyID']."
                                        AND OrderDate BETWEEN ".$begin." AND ".$end;
                            $num_data = $db->get_row($sql_num);
                            $page = new ShowPage;
                            $page->PageSize = 30;
                            $page->Total   = $num_data['cnt'];
                            $page->LinkAry = array('begindate'=>$in['begindate'],'enddate'=>$in['enddate'],'uid'=>$in['uid']);
                            $list = $db->get_results($sql.' '.$page->OffSet());
                            unset($num_data,$sql_num);
                            if(!empty($list))
                            {
                                ?>
                                <tr>
                                    <td >

                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <thead>
                                            <tr>
                                                <td width="5%" class="bottomlinebold">行号</td>
                                                <td width="30%" class="bottomlinebold">订单编号</td>
                                                <td width="20%" class="bottomlinebold">药店</td>
                                                <td width="12%" class="bottomlinebold" align="center">订单金额</td>
                                                <td width="20%" class="bottomlinebold" align="center">下单时间</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $n = 1;
                                                foreach($list as $val){
                                            ?>
                                                <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                                    <td><?php echo $n; ?></td>
                                                    <td><a href="order_manager.php?ID=<?php echo $val['OrderID']; ?>" target="_blank"><?php echo $val['OrderSN'] ?></a></td>
                                                    <td><?php echo $val['ClientCompanyName']; ?></td>
                                                    <td align="center">¥ <?php echo $val['OrderTotal']; ?></td>
                                                    <td align="center"><?php echo date('Y-m-d H:i',$val['OrderDate']); ?></td>
                                                </tr>
                                            <?php
                                                    $n++;
                                                }
                                            ?>
                                            <tr>
                                                <td colspan="5"  align="right"><? echo $page->ShowLink('statistics_saler_detail.php');?></td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                </tr>
                            <? }else{?>
                                <tr>
                                    <td height="130" bgcolor="#ffffff" align="center">&nbsp; 暂无符合条件的数据!</td>
                                </tr>
                            <? }}?>
                    </table>
                </fieldset>
            </div>
            <br style="clear:both;" />
            <INPUT TYPE="hidden" name="referer" value ="" >
        </form>
    </div>
    </div>
    <br style="clear:both;" />
    </div>


    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <div id="windowForm">
        <div class="windowHeader">
            <h3 id="windowtitle">订购明细：</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContentList" >
            数据载入中...
        </div>
    </div>
    </body>
    </html>
<?
function ShowTreeMenu($resultdata,$p_id,$umsg)
{
    $frontMsg  = "";
    foreach($resultdata as $key => $var)
    {
        if($var['ParentID'] == $p_id)
        {
            if($var['ParentID']=="0")
            {
                $frontMsg  .= '<li><a href="'.$umsg.'&sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
            }else{
                $frontMsg  .= '<li><a href="'.$umsg.'&sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
            }
            $frontMsg2  = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$umsg);
            if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
            $frontMsg .= '</li>';
        }
    }
    return $frontMsg;
}
?>