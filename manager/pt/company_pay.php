<?php
$menu_flag = "manager";
include_once ("header.php");
$erp_version = include_once("inc/erp_version.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);

if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');
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
        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
        <script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
        <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

        <script type="text/javascript">
            $(function() {

                $("#tree").treeview({
                    collapsed: true,
                    animated: "medium",
                    control:"#sidetreecontrol",
                    persist: "location"
                });
                $("#bdate").datepicker({changeMonth: true,	changeYear: true});
                $("#edate").datepicker({changeMonth: true,	changeYear: true});
            })

            function show_time_log(cid,cname)
            {
                $("#windowtitle").html(cname+' - 时间线');
                $('#windowContent').html('<iframe src="show_time_log.php?ID='+cid+'" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
                $.blockUI({
                    message: $('#windowForm6'),
                    css:{
                        width: '620px',height:'580px',top:'8%'
                    }
                });
                $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
            }

            function add_pay() {
                $("#windowtitle").html('新增 - 在线支付');
                $('#windowContent').html('<iframe src="show_pay_add.php" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
                $.blockUI({
                    message: $('#windowForm6'),
                    css:{
                        width: '620px',height:'580px',top:'8%'
                    }
                });
                $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
            }

            function show_pay(cid,pid,cname)
            {
                $("#windowtitle").html(cname+' - 在线支付');
                $('#windowContent').html('<iframe src="show_pay.php?ID='+cid+'&PID='+pid+'" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
                $.blockUI({
                    message: $('#windowForm6'),
                    css:{
                        width: '620px',height:'580px',top:'8%'
                    }
                });
                $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
            }
        </script>
    </head>



    <body>
    <?php include_once ("top.php");?>
    <?php include_once ("inc/son_menu_bar.php");?>

    <div id="bodycontent">
    <div class="lineblank"></div>

    <div id="searchline">
        <div class="leftdiv">

            <form id="FormSearch" name="FormSearch" method="get" action="company_pay.php">
                <input name="d" value="<?php echo $in['d']; ?>" type="hidden"/>
                <label>
                    &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" class="inputline" />
                </label>
                <label>
                    <select id="iid" name="iid"  style="width:165px;" class="select2">
                        <option value="" >⊙ 所有行业</option>
                        <?php
                        $n = 0;
                        $accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");

                        foreach($accarr as $accvar)
                        {
                            $n++;
                            $industryarr[$accvar['IndustryID']] = $accvar['IndustryName'];

                            if($in['iid'] == $accvar['IndustryID']) $smsg = 'selected="selected"'; else $smsg ="";

                            echo '<option value="'.$accvar['IndustryID'].'" '.$smsg.' title="'.$accvar['IndustryName'].'"  >'.$accvar['IndustryName'].'</option>';
                        }
                        ?>
                    </select>
                </label>
                <label>
                    <select id="aid" name="aid"  style="width:135px;" class="select2">
                        <option value="" >⊙ 所有地区</option>
                        <?php
                        $n = 0;
                        $sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city  ORDER BY AreaParent asc,AreaID ASC ");
                        foreach($sortarr as $areavar)
                        {
                            $n++;
                            if($areavar['AreaID']==$in['aid']) $areainfoselected = $areavar;
                            $areaarr[$areavar['AreaID']] = $areavar['AreaName'];
                        }
                        echo ShowTreeMenu($sortarr,0,$in['aid'],1);
                        ?>
                    </select>
                </label>
                <label>
                    <select id="status" name="status"  style="width:135px;" class="select2">
                        <option value="" >⊙ 所有状态</option>
                        <option value="allinpay" <?php echo $in['status'] == 'allinpay' ? "selected='selected'" : ""; ?> >通联支付</option>
                        <option value="yijifu" <?php echo $in['status'] == 'yijifu' ? "selected='selected'" : ""; ?> >易极付</option>
                    </select>
                </label>

                <label>
                    <select id="date_field" name="date_field"  style="width:105px;" class="select2">
                        <option value="end_date" <?php if($in['date_field'] == 'end_date') echo 'selected="selected"';?> >到期时间</option>
                        <option value="begin_date" <?php if($in['date_field'] == 'begin_date') echo 'selected="selected"';?> >开通时间</option>
                    </select>
                </label>
                <label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
                <label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

                </label>
                <label>

                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

                </label>
            </form>
        </div>
    </div>

    <div class="line2"></div>

    <div class="bline">
        <?php

        $sqlmsg = '';
        $having = '';
        if(empty($in['num'])){
            if(!empty($in['iid']))  $sqlmsg .= " and c.CompanyIndustry=".$in['iid']." "; else $in['iid'] = '';
            if(!empty($in['aid']))
            {
                if(empty($areainfoselected['AreaParent']))
                {
                    $sqlmsg .= " and ( c.CompanyArea=".$in['aid']." or c.CompanyArea in (SELECT AreaID FROM ".DATABASEU.DATATABLE."_order_city where  AreaParent=".$in['aid']." ORDER BY AreaID ASC) ) ";
                }else{
                    $sqlmsg .= " and c.CompanyArea=".$in['aid']." ";
                }
            }else{
                $in['aid'] = '';
            }
            if(!empty($in['gid']))  $sqlmsg .= " and c.CompanyAgent=".$in['gid']." "; else $in['gid'] = '';
            if($in['date_field'] == 'begin_date'){
                $datefield = 's.CS_BeginDate';
            }else{
                $datefield = 's.CS_EndDate';
            }
            if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
            if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";

            //if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail) like '%".$in['kw']."%' ";
            if(!empty($in['kw'])) {
                $sqlmsg .= " AND (";
                $likeArr = array();
                foreach(explode(',', 'c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail') as $lk) {
                    $likeArr[] = " {$lk} like '%".$in['kw']."%'";
                }
                $sqlmsg .= implode(" OR " , $likeArr);
                $sqlmsg .= ")";
            }

            if(!empty($in['status'])) {
                $sqlmsg .= " AND g.GetWay='{$in['status']}'";
            }

        }

        $data_cnt = $db->get_var("SELECT COUNT(*) AS Total FROM ".DATABASEU.DATATABLE."_order_getway AS g LEFT JOIN ".DATABASEU.DATATABLE."_order_company AS c ON c.CompanyID = g.CompanyID LEFT JOIN ".DATABASEU.DATATABLE."_order_cs AS s ON s.CS_Company = c.CompanyID WHERE 1=1 {$sqlmsg}");

        $page = new ShowPage;
        $page->PageSize = 100;
        $page->Total = (int)$data_cnt;
        $page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid'],'gid' => $in['gid'],'date_field' => $in['date_field'],'bdate' => $in['bdate'],'edate'=>$in['edate'],"status"=>$in['status'],"version"=>$in['verison'],'develop' => $in['develop']);
        $datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_order_getway AS g LEFT JOIN ".DATABASEU.DATATABLE."_order_company AS c ON c.CompanyID = g.CompanyID LEFT JOIN ".DATABASEU.DATATABLE."_order_cs AS s ON s.CS_Company = c.CompanyID WHERE 1=1 {$sqlmsg} ORDER BY g.GetWayID DESC ";
        $list_data = $db->get_results($datasql . ' ' . $page->OffSet());
        ?>

        <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="bottomlinebold">
                        <input type="button" name="newbutton" id="newbutton" value=" 新增支付 " onclick="add_pay();" class="button_2" />
                    </td>
                    <td align="right"  height="30" class="bottomlinebold">
                        <? echo $page->ShowLink('company_pay.php');?>
                    </td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">

                <thead>
                <tr>
                    <td width="6%" class="bottomlinebold">编号</td>
                    <td class="bottomlinebold">公司名称/系统名称</td>
                    <td width="7%" class="bottomlinebold">支付类型</td>
                    <td width="20%" class="bottomlinebold">商户号</td>
                    <td class="bottomlinebold">商户信息</td>
                    <td width="15%" class="bottomlinebold">联系人/联系方式</td>
                    <td width="8%" class="bottomlinebold" align="center">管理</td>
                </tr>
                </thead>

                <tbody>

                <?php
                if(!empty($list_data))
                {
                    foreach($list_data as $lsv)
                    {
                        /**
                        if($_GET['m'] == 'add'){
                        if(!(file_exists (RESOURCE_PATH.$lsv['CompanyID'])))
                        {
                        _mkdir(RESOURCE_PATH,$lsv['CompanyID']);
                        echo RESOURCE_PATH.$lsv['CompanyID'];
                        }
                        }
                         **/
                        ?>

                        <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
                            <td >10<? echo $lsv['CompanyID'];?></td>
                            <td ><a href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank"><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?></a></td>
                            <td>
                                <?php
                                    switch($lsv['GetWay']) {
                                        case 'allinpay':
                                            echo "通联支付";
                                            break;
                                        case 'yijifu':
                                            echo "易极付";
                                            break;
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if($lsv['GetWay'] == 'yijifu') {
                                    if($lsv['IsDefault'] == 'Y') {
                                        echo "【默认】";
                                    }
                                    echo $lsv['SignNO'] . '<br/>' . $lsv['SignAccount'];
                                } else {
                                    echo $lsv['MerchantNO'] . '<br/>' . $lsv['SignMsgKey'];
                                } ?>
                            </td>


                            <td>
                                <?php if($lsv['GetWay'] == 'yijifu') {
                                    echo $lsv['AccountType'] == 'company' ? '【公司账号】' : '【个人账号】' . $lsv['MerchantName'];
                                    echo "<br/>";
                                    echo '【对接码】 dhb0m'.sprintf("%07.0f",($lsv['CompanyID'] + 818));
                                } else {
                                    echo $lsv['B2B'] == 'T' ? "公对公" : "--";
                                    echo "<br/>";
                                    echo $lsv['Fee'] == 'Collection' ? "收款方付手续费" : "付款方付手续费";
                                } ?>
                            </td>

                            <td ><? echo $lsv['CompanyContact'].'<br />'.$lsv['CompanyMobile'];?></td>

                            <td align="center" title="<?php echo $lsv['CompanyRemark'];?>">
                                <a href="javascript:void(0)" onclick="show_pay('<? echo $lsv['CompanyID'];?>','<?php echo $lsv['GetWayID']; ?>','<? echo $lsv['CompanyName'];?>');" >修改</a>
                            </td>
                        </tr>
                    <? } }else{?>

                    <tr>
                        <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
                    </tr>
                <? }?>
                </tbody>
            </table>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100" align="center"><?php echo count($list_data);?></td>
                    <td align="right"  height="30" >
                        <? echo $page->ShowLink('company_pay.php');?>
                    </td>
                </tr>
            </table>

            <INPUT TYPE="hidden" name="referer" value ="" >

        </form>

    </div>

    <br style="clear:both;" />
    </div>

    <?php include_once ("bottom.php");?>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <div id="windowForm6">
        <div class="windowHeader" >
            <h3 id="windowtitle" style="width:540px">时间线</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent">
            正在载入数据...
        </div>
    </div>

    </body>
    </html>
<?php
function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1)
{
    $frontMsg  = "";
    $frontTitleMsg = "┠-";
    $selectmsg = "";

    if($var['AreaParent']=="0") $layer = 1; else $layer++;

    foreach($resultdata as $key => $var)
    {
        if($var['AreaParent'] == $p_id)
        {
            $repeatMsg = str_repeat(" -+- ", $layer-2);
            if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";

            $frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";

            $frontMsg2  = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
            $frontMsg  .= $frontMsg2;
        }
    }
    return $frontMsg;
}
?>