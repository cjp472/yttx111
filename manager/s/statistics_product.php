<?php
$menu_flag = "statistics";
$pope	       = "pope_view";
include_once ("header.php");

if(empty($in['sid']))
{
    $sortinfo = null;
    $in['sid'] = 0;
}else{
    $sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteName,Content FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}

//药店
$clientdata = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");

$clientIds = array();
foreach($clientdata as $val){
    $clientIds[] = $val['ClientID'];
}

$where = "";
$giftWhere = "";
$returnWhere = "";
if(!empty($in['sid'])){
    $where .= " AND SiteID=".$in['sid'];
}

if(!empty($in['cid'])){
    $where .= " AND OrderUserID=".$in['cid'];
    $giftWhere .= " AND OrderUserID=".$in['cid'];
    $returnWhere .= "AND ReturnClient=".$in['cid'];
}else{
    $where .= " AND OrderUserID IN(".implode(',',$clientIds).")";
    $giftWhere .= " AND OrderUserID IN(".implode(',',$clientIds).")";
    $returnWhere .= " AND ReturnClient IN(".implode(',',$clientIds).")";
}

if(empty($in['beginDate'])){
    $in['beginDate'] = date('Y-m-d',strtotime('-1 months'));
}
if(empty($in['endDate'])){
    $in['endDate'] = date('Y-m-d');
}

$where .= " AND FROM_UNIXTIME(o.OrderDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59' ";

if ($_SESSION['uinfo']['userflag']==2) {
    $agent=$db->get_row("SELECT UpperID FROM ".DATABASEU.DATATABLE."_order_user WHERE UserID=".$_SESSION['uinfo']['userid']."");
    $where .= " AND i.AgentID=".$agent['UpperID']."";
}
//购买商品
$data_sql = "SELECT c.ContentID,COUNT(1) AS cnt,i.Coding,i.Name,SUM(c.ContentNumber) AS ContentNumber,SUM(c.ContentSend) AS ContentSend,SUM(c.ContentPrice * c.ContentNumber * c.ContentPercent * 0.1) AS Total
              FROM ".DATATABLE."_order_cart AS c
              LEFT JOIN ".DATATABLE."_order_orderinfo AS o
                ON o.OrderID = c.OrderID
               LEFT JOIN rsung_order_content_index AS i
                ON c.ContentID = i.ID
              WHERE c.CompanyID = ".$_SESSION['uinfo']['ucompany']." AND o.OrderStatus <> 8 AND o.OrderStatus <> 9 ".$where."
              GROUP BY ContentID,i.Name";
$statdata = $db->get_results($data_sql);

//赠送商品
$gift_sql = "SELECT SUM(ContentNumber) as cnum,SUM(ContentSend) as snum,c.ContentID,c.ContentName
             FROM ".DATATABLE."_order_cart_gifts c
             LEFT JOIN ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID
             WHERE c.CompanyID=".$_SESSION['uinfo']['ucompany']."
             AND FROM_UNIXTIME(o.OrderDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59'
             AND o.OrderStatus!=8 AND o.OrderStatus!=9 ".$giftWhere."
             GROUP BY c.ContentID ORDER BY cnum DESC";
$gift_source = $db->get_results($gift_sql);
$gift_arr = array();
foreach($gift_source as $key=>$val){
    $gift_arr[$val['ContentID']] = $val;
}

//退货商品
$return_sql = "SELECT SUM(ContentNumber) as cnum,c.ContentID,c.ContentName
               FROM ".DATATABLE."_order_cart_return c
               LEFT JOIN ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID
               WHERE c.CompanyID=".$_SESSION['uinfo']['ucompany']."
               AND FROM_UNIXTIME(o.ReturnDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59'
               AND (o.ReturnStatus=2 OR o.ReturnStatus=3 OR o.ReturnStatus=5) ".$returnWhere."
               GROUP BY c.ContentID ORDER BY cnum DESC";
$return_source = $db->get_results($return_sql);
$return_arr = array();
foreach($return_source as $key=>$val){
    $return_arr[$val['ContentID']] = $val;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name='robots' content='noindex,nofollow' />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="css/jquery.treeview.css" />
        <link rel="stylesheet" href="css/showpage.css" />
        <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
        <script src="../scripts/jquery.js" type="text/javascript"></script>
        <script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
        <script src="../scripts/jquery.treeview.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
        <script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
        <script type="text/javascript">
            /******tree****/
            $(function() {
                $("#beginDate,#endDate").datepicker({changeMonth: true,	changeYear: true});
                $("#tree").treeview({
                    collapsed: true,
                    animated: "medium",
                    control:"#sidetreecontrol",
                    persist: "location"
                });

                $("#newbutton").click(function(){
                    $("#MainForm").attr('method','get').attr('target','_self').attr('action','statistics_product.php').get(0).submit();
                });

                $("#exceltable").click(function(){
                    //导出报表
                    $("#MainForm").attr('method','post').attr('action','statistics_excel.php?action=product').attr('target','exe_iframe').get(0).submit();
                });
            })
        </script>
    </head>

    <body>
    <? include_once ("top.php");?>

    <div class="bodyline" style="height:25px;"></div>

    <div id="bodycontent">
        <div class="lineblank"></div>
        <div id="searchline">

            <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
                <form id="FormSearch" name="FormSearch" method="post" action="inventory.php">
                    <tr>
                       <!-- <td width="120" align="center"><strong>名称/型号/拼音码：</strong></td>
                        <td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
                        <td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>-->
                        <td aling="right"><div class="location"><strong>当前位置：</strong><a href="statistics.php">销售统计 &#8250;&#8250; 商品统计</a></div></td>
                    </tr>
                </form>
            </table>
        </div>

        <div class="line2"></div>
        <div class="bline">
            <div id="sortleft">
                <div class="leftlist">
                    <div><strong><a href="statistics_product.php?begindate=<?php echo $in['beginDate']; ?>&enddate=<?php echo $in['endDate']; ?>">按药店统计</a></strong></div>
                    <ul>
                        <form target="_self" name="changetypeform" id="changetypeform"  action="statistics_product.php?beginDate=<?php echo $in['beginDate']; ?>&endDate=<?php echo $in['endDate']; ?>" method="get">
                            <select name="cid" id="cid" class="select2" style="width:166px;">
                                <option value="">⊙ 所有药店</option>
                                <?php
                                    foreach($clientdata as $k=>$v) {
                                        echo "<option value='" . $v['ClientID'] . "' " . ($v['ClientID'] == $in['cid'] ? "selected='selected'" : "") . ">" . substr($v['ClientCompanyPinyi'], 0, 1) . '-' . $v['ClientCompanyName'] . "</option>";
                                    }
                                ?>

                            </select>
                            <script type="text/javascript">
                                $(function(){
                                    $("#cid").change(function(){
                                        window.location.href = "statistics_product.php?beginDate=<?php echo $in['beginDate']; ?>&endDate=<?php echo $in['endDate']; ?>&cid="+$(this).val();
                                    });
                                });
                            </script>
                        </form>
                    </ul>
                </div>
                <hr style="clear: both;">
                <!-- tree -->

                <div id="sidetree">
                    <div class="treeheader">&nbsp;<strong>商品分类</strong></div>
                    <div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
                    <ul id="tree">
                        <?php
                        $sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
                        echo ShowTreeMenu($sortarr,0);
                        ?>
                    </ul>
                </div>
                <!-- tree -->
            </div>

            <div id="sortright">

                <form id="MainForm" name="MainForm" method="post" action="statistics_product.php" target="exe_iframe" >
                    <input type="hidden" name="cid" value="<?php echo $in['cid']; ?>"/>
                    <div class="line">
                        <fieldset class="fieldsetstyle">
                            <legend>商品订购统计</legend>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td height="35">&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;
                                        <input name="beginDate" id="beginDate" onfocus="this.select();" type="text" maxlength="12" value="<?php echo $in['beginDate']; ?>">&nbsp;到&nbsp;
                                        <input name="endDate" id="endDate"  type="text" onfocus="this.select();" maxlength="12" value="<?php echo $in['endDate']; ?>">&nbsp;
                                        <input name="newbutton" class="mainbtn" id="newbutton" type="button" value=" 查看 ">
                                        &nbsp;&nbsp;
                                        <input name="exceltable" class="mainbtn" id="exceltable" type="button" value=" 导出报表 ">
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <thead>
                                            <tr>
                                                <td width="5%" class="bottomlinebold">行号</td>
                                                <td width="12%" class="bottomlinebold">编号</td>
                                                <td class="bottomlinebold">商品名称</td>
                                                <td width="8%" align="right" class="bottomlinebold">订购数</td>
                                                <td width="12%" align="right" class="bottomlinebold">订购金额</td>
                                                <td width="8%" align="right" class="bottomlinebold">赠送数</td>
                                                <td width="8%" align="right" class="bottomlinebold">退货数</td>
                                                <td width="8%" align="right" class="bottomlinebold">实际数</td>
                                                <td width="8%" align="right" class="bottomlinebold">发货数</td>
                                                <!--<td width="8%" align="right" class="bottomlinebold">属性统计</td>-->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $n=0;
                                                $tnum = 0;//订购量(所有商品
                                                $tamount = 0;//订购金额(所有商品
                                                $tgift = 0;//总赠送数
                                                $treturn = 0;//总退货数
                                                $ttnum = 0;//总实际数
                                                $tsnum = 0;//总发货数量
                                                foreach($statdata as $key=>$val){
                                                    $n++;
                                                    $tnum += $val['ContentNumber'];
                                                    $tamount += $val['Total'];
                                                    $val['gift'] = !empty($gift_arr[$val['ContentID']]) ? $gift_arr[$val['ContentID']]['cnum'] : 0; //赠品数量
                                                    $val['gsnum'] = !empty($gift_arr[$val['ContentID']]) ? $gift_arr[$val['ContentID']]['snum'] : 0; //赠品发货数
                                                    $val['return'] = !empty($return_arr[$val['ContentID']]) ? $return_arr[$val['ContentID']]['cnum'] : 0; //退货数量

                                                    $val['tnum'] = $val['ContentNumber'] + $val['gift'] - $val['return'];
                                                    $val['snum'] = $val['ContentSend'] + $val['gsnum'];
                                                    $treturn += $val['return'];
                                                    $tgift += $val['gift'];
                                                    $ttnum += $val['tnum'];
                                                    $tsnum += $val['snum'];
                                            ?>
                                                    <tr class="bottomline" onmouseover="inStyle(this)" onmouseout="outStyle(this)">
                                                        <td><?php echo $n; ?></td>
                                                        <td>
                                                           <?php
                                                            if(empty($in['cid'])){
                                                                echo "<a href='statistics_product_client.php?beginDate=".$in['beginDate']."&endDate=".$in['endDate']."&ID=".$val['ContentID']."' target='_blank'>".$val['Coding']."</a>";
                                                            }else{
                                                                echo $val['Coding'];
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><a title="商品详细" href="product_content.php?ID=<?php echo $val['ContentID']; ?>" target="_blank"><?php echo $val['Name']; ?></a></td>
                                                        <td align="right"><?php echo $val['ContentNumber']; ?></td>
                                                        <td align="right">¥ <?php echo sprintf('%.2f',$val['Total']); ?></td>
                                                        <td align="right"><?php echo $val['gift']; ?></td>
                                                        <td align="right"><?php echo $val['return']; ?></td>
                                                        <td align="right"><?php echo $val['tnum']; ?></td>
                                                        <td align="right"><?php echo $val['snum']; ?></td>
                                                       <!-- <td align="right"><a href="product_stat_color.php?ID=2130&amp;begindate=2014-10-26&amp;enddate=2014-11-26" target="_blank">颜色</a>&nbsp;|&nbsp;<a href="product_stat_spec.php?ID=2130&amp;begindate=2014-10-26&amp;enddate=2014-11-26" target="_blank">规格</a></td>-->
                                                    </tr>
                                                    <?php
                                                } ?>
                                            <tr class="bottomline" onmouseover="inStyle(this)" onmouseout="outStyle(this)">
                                                <td colspan="2"><strong>合计：</strong></td>
                                                <td><strong>&nbsp;<?php echo $n; ?> 种</strong></td>
                                                <td align="right"><strong> <?php echo $tnum; ?> </strong></td>
                                                <td align="right"><strong>¥  <?php echo sprintf('%.2f',$tamount); ?> </strong></td>
                                                <td align="right"><strong> <?php echo $tgift; ?> </strong></td>
                                                <td align="right"><strong> <?php echo $treturn; ?> </strong></td>
                                                <td align="right"><strong> <?php echo $ttnum; ?> </strong></td>
                                                <td align="right"><strong> <?php echo $tsnum; ?> </strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
        <br style="clear:both;" />
    </div>

    <? include_once ("bottom.php");?>
    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <div id="windowForm6">
        <div class="windowHeader">
            <h3 id="windowtitle">商品统计详细</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent">
            正在载入数据...
        </div>
    </div>
    </body>
    </html>
<?
function ShowTreeMenu($resultdata,$p_id)
{
    $frontMsg  = "";
    foreach($resultdata as $key => $var)
    {
        if($var['ParentID'] == $p_id)
        {
            if($var['ParentID']=="0")
            {
                $frontMsg  .= '<li><a href="statistics_product.php?sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
            }
            else
            {
                $frontMsg  .= '<li><a href="statistics_product.php?sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
            }

            $frontMsg2 = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID']);
            if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
            $frontMsg .= '</li>';
        }
    }
    return $frontMsg;
}
?>