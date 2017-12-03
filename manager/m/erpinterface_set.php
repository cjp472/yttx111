<?php
$menu_flag = "system";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<div id="bodycontent">
    <div class="lineblank"></div>

    <div id="searchline">
        <div class="rightdiv">
            <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="producttype_set.php">模式设置</a></div>
        </div>
    </div>


    <div class="line2"></div>
    <div class="bline" >

        <div id="sortleft">
            <!-- tree -->
            <div class="leftlist">
                <div >
                    <strong>系统设置</strong></div>
                <!-- 系统设置菜单开始 -->
                <?php include_once("inc/system_set_left_bar.php")  ;?>
                <!-- 系统设置菜单结束 -->

                <br style="clear:both;" />
            </div>
            <!-- tree -->
        </div>

        <div id="sortright">
            <div id="oldinfo" class="line">
                <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
                    <fieldset  class="fieldsetstyle">
                        <legend>ERP接口配置</legend>
                        <tr><td height="5"></td><td></td></tr>
                        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
                            <?
                            $valuearr = null;
                            $setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='erp' limit 0,1");
                            if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);

                            ?>
                            <tr>
                                <td ><div align="right"></div></td>
                                <td></td>
                            </tr>

                            <tr><td height="5"></td><td></td></tr>
                            <tr>
                                <td height="50"  bgcolor="#F0F0F0" align="right"><strong>启用接口：</strong></td>
                                <td >
                                    <div style="height:28px; clear:both;"><input type="radio" name="erp_interface" id="erp_interface1" value="N" <? if(empty($valuearr['erp_interface']) || $valuearr['erp_interface'] == "N") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;不启用</strong>&nbsp;</div>
                                    <div style="height:28px; clear:both;"><input type="radio" name="erp_interface" id="erp_interface2" value="Y" <? if($valuearr['erp_interface'] == "Y") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;启用</strong>&nbsp;&nbsp;&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td height="5"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td height="50" bgcolor="#F0F0F0" align="right"><strong>订单传递：</strong></td>
                                <td>
                                    <div style="height:28px; clear:both;"><input type="radio" name="erp_order_check" id="erp_order_check1" value="N" <? if(empty($valuearr['erp_order_check']) || $valuearr['erp_order_check'] == "N") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;无需审核</strong>&nbsp;</div>
                                    <div style="height:28px; clear:both;"><input type="radio" name="erp_order_check" id="erp_order_check2" value="Y" <? if($valuearr['erp_order_check'] == "Y") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;需要审核</strong>&nbsp;&nbsp;&nbsp;</div>
                                </td>
                            </tr>

                            <tr>
                                <td ><div align="right"></div></td>
                                <td><input type="button" name="sendbuttoninfo" id="sendbuttoninfo" value="保存设置" class="button_2" onclick="savesettype('erp');" /></td>
                            </tr>
                        </table>
                    </fieldset>

                </form>
                <br style="clear:both;" />
            </div>
        </div>
        <br style="clear:both;" />
    </div>
</div>


<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>