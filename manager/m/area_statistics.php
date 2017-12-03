<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
if(empty($in['enddate'])) $in['enddate']     = date("Y-m-d");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

$urlmsg = "area_statistics.php?begindate=".$in['begindate']."&enddate=".$in['enddate']."";
if(!empty($in['aid'])) $urlmsg .= "&aid=".$in['cid'];


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
        <script type="text/javascript">
            $(function() {
                $("#begindate").datepicker();
                $("#enddate").datepicker();

                $("#newbutton").click(function(){
                    document.MainForm.action = 'area_statistics.php';
                    document.MainForm.target = '_self';
                    document.MainForm.submit();
                });
            });
        </script>

        <script type="text/javascript">
            /******tree****/
            $(function() {
                $("#tree").treeview({
                    collapsed: true,
                    animated: "medium",
                    control:"#sidetreecontrol",
                    persist: "location"
                });
            })
        </script>
    </head>

    <body>
    <?php include_once ("top.php");?>
    <div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <div class="leftdiv">

        </div>

        <div class="location"><strong>当前位置：</strong> <a href="area_statistics.php">商品地区统计</a></div>
    </div>
    <div class="line2"></div>
    <div class="bline">
    <div id="sortleft">
        <!-- tree -->
        <hr style="clear:both;" />
        <div id="sidetree">
            <div class="treeheader">&nbsp;<strong><a href="<? echo $urlmsg;?>">商品分类</a></strong></div>
            <div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
            <ul id="tree">
                <?php
                $sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
                echo ShowTreeMenu($sortarr,0,$urlmsg);
                ?>
            </ul>
        </div>
        <!-- tree -->
    </div>

    <div id="sortright">
    <form id="MainForm" name="MainForm" method="post" action="statistics_product_excel.php?action=product"  >
    <input name="clientid" id="clientid" type="hidden" value='<? if(!empty($in['cid'])) echo $in['cid'];?>' />
    <input name="siteid" id="siteid" type="hidden" value='<? if(!empty($in['sid'])) echo $in['sid'];?>' />
    <div class="line" >
        <fieldset class="fieldsetstyle">
            <legend>商品订购统计</legend>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td height="35" >&nbsp;<strong>日期:</strong>&nbsp;&nbsp;从&nbsp;
                        <input name="begindate" type="text" id="begindate"   maxlength="12" onfocus="this.select();" value="<? echo $in['begindate'];?>"   />&nbsp;到&nbsp;
                        <input name="enddate" type="text" id="enddate" maxlength="12" onfocus="this.select();" value="<? echo $in['enddate'];?>" />&nbsp;
                        <input type="button" name="newbutton" id="newbutton" value=" 查看 " class="mainbtn" />&nbsp;&nbsp;
                        <!--<input type="button" name="exceltable" id="exceltable" value=" 导出报表 " class="mainbtn" onclick="output_product_excel('product');" />-->
                    </td>
                </tr>
                <?php
                if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
                {
                    echo ('<tr><td height="60" align="center">注意：时间跨度不能超过一年!</td></tr>');
                }else{
                    $companyID = $_SESSION['uinfo']['ucompany'];
                    $site = $db->get_row("SELECT * FROM rsung_order_site WHERE CompanyID={$companyID} AND SiteID=".$in['sid']);
                    $aid = $in['aid'] ? (int)$in['aid'] : 0;
                    $alist = $db->get_results("SELECT * FROM rsung_order_area WHERE AreaParentID={$aid} AND AreaCompany=".$companyID);
                    $wt = "";
                    foreach($alist as $k=>$v){
                        $wt .= " WHEN AreaID=".$v['AreaID']." THEN '".implode(',',getSon($v['AreaID']))."'";
                    }
                    $sql = "SELECT cli.AreaID,cli.AreaName,cli.IDS,SUM(cart.ContentNumber) AS ContentNumber,SUM(cart.ContentNumber * cart.ContentPrice * cart.ContentPercent * 0.1) AS Total
                            FROM ".DATATABLE."_order_orderinfo AS o
                              INNER JOIN
                                (SELECT ClientID,ClientName,ClientArea,A.IDS,A.AreaID,A.AreaName
                                FROM
                                  ".DATATABLE."_order_client AS c
                                  LEFT JOIN
                                    (SELECT
                                      AreaID,
                                      AreaName,
                                      CASE
                                        {$wt}
                                        ELSE ''
                                      END AS IDS
                                    FROM
                                      ".DATATABLE."_order_area
                                    WHERE AreaCompany = {$companyID}
                                      AND AreaID IN
                                      (SELECT AreaID FROM ".DATATABLE."_order_area
                                      WHERE AreaParentID = {$aid} AND AreaCompany = {$companyID})) AS A
                                    ON A.IDS REGEXP CONCAT('^',c.ClientArea,'$|^',c.ClientArea,',|,',c.ClientArea,',|,',c.ClientArea,'$')
                                WHERE c.ClientCompany = {$companyID}
                                  AND A.IDS <> '') AS cli
                                ON o.OrderUserID = cli.ClientID
                                INNER JOIN ".DATATABLE."_order_cart AS cart
                                ON cart.OrderID = o.OrderID
                                INNER JOIN ".DATATABLE."_order_content_index AS i
                                ON i.ID = cart.ContentID
                                INNER JOIN ".DATATABLE."_order_site AS s
                                ON s.SiteID = i.SiteID AND s.SiteNO LIKE '{$site['SiteNO']}%'
                            WHERE o.OrderCompany = {$companyID} AND o.OrderStatus <> 8 AND o.OrderStatus <> 9
                              AND o.OrderDate BETWEEN ".strtotime($in['begindate'].' 00:00:00')." AND ".strtotime($in['enddate'].' 23:59:59')."
                             GROUP BY cli.AreaName
                             ORDER BY Total DESC ";

                    $areaList = $db->get_results("SELECT AreaID,AreaName,'0' as Total,'0' as ContentNumber, CASE  {$wt} ELSE '' END AS IDS FROM ".DATATABLE."_order_area WHERE AreaCompany={$companyID} AND AreaID IN( SELECT AreaID FROM ".DATATABLE."_order_area WHERE AreaParentID = {$aid} AND AreaCompany={$companyID} )
");
                    $areaList = $areaList ? $areaList : array();

                    $list = $db->get_results($sql);
                    $list = $list ? $list : array();
                    $listAsoc = array();
                    foreach($list as $key=>$val){
                        $listAsoc[$val['AreaID']] = $val;
                    }
                    foreach($areaList as $key=>$val){
                        if(empty($listAsoc[$val['AreaID']])){
                            $listAsoc[$val['AreaID']] = $val;
                        }
                    }
                    $list = $listAsoc;
                    if(!empty($list))
                    {
                        ?>
                        <tr>
                            <td height="28" bgcolor="#efefef" >
                                <?php
                                    if($site){
                                        echo "商品分类:<strong>".$site['SiteName']."</strong>";
                                    }
                                    if($aid!=0){
                                        echo "-地区:<strong>".$db->get_var("SELECT AreaName FROM ".DATATABLE."_order_area WHERE AreaID={$aid}")."</strong>";
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td >

                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <thead>
                                    <tr>
                                        <td width="10%" class="bottomlinebold">行号</td>
                                        <td width="30%" class="bottomlinebold">地区</td>
                                        <td class="bottomlinebold">数量</td>
                                        <td width="30%" class="bottomlinebold">金额</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $n=0;
                                    $totalNum = 0;
                                    $totalAmount = 0;
                                    foreach($list as $k=>$v){
                                        $n++;
                                        $totalNum += $v['ContentNumber'];
                                        $totalAmount += $v['Total'];
                                        ?>
                                        <tr class="bottomline" onmouseover="inStyle(this);" onmouseout="outStyle(this);">
                                            <td><?php echo $n; ?></td>
                                            <td>
                                                <?php
                                                if($v['IDS']==$v['AreaID']){
                                                    echo $v['AreaName'];
                                                }else{
                                                    ?>
                                                    <a href="area_statistics.php?begindate=<?php echo $in['begindate']; ?>&enddate=<?php echo $in['enddate']; ?>&aid=<?php echo $v['AreaID']; ?>&sid=<?php echo $in['sid']; ?>"><?php echo $v['AreaName']; ?></a>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $v['ContentNumber']; ?></td>
                                            <td>¥ <?php echo sprintf("%01.2f",$v['Total']); ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                        <td><strong>合计：</strong></td>
                                        <td >&nbsp;</td>
                                        <td><strong> <? echo $totalNum;?> </strong></td>
                                        <td><strong>¥  <? echo sprintf("%01.2f", round($totalAmount,2));?> </strong></td>
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

function getSon($pid){
    global $db;
    $son = array();
    $son[] = $pid;
    $list = $db->get_results("SELECT * FROM rsung_order_area WHERE AreaParentID = {$pid} AND AreaCompany=".$_SESSION['uinfo']['ucompany']);
    foreach($list as $val){
        $son = array_merge($son,getSon($val['AreaID']));
    }
    return $son;
}

?>