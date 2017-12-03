<?php
$menu_flag = "client";
$pope	   = "pope_form";
include_once ("header.php");

if(empty($in['sid']))
{
    $sortinfo = null;
    $in['sid'] = 0;
}
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
        <script src="js/client.js?v=43<? echo VERID;?>" type="text/javascript"></script>
        <script type="text/javascript">
            function remove_content_line(pid) {
                var delline = "line_" + pid;
                $("#"+delline).remove();
            }
        </script>
    </head>

    <body>
    <?php include_once ("top.php");?>
    <div id="bodycontent">
        <div class="lineblank"></div>

        <div id="searchline">
            <div class="rightdiv">
                <div class="locationl"><a name="editname" id="editname"></a><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250; <a href="client_import.php">批量导入</a> </div>
            </div>
        </div>

        <div class="line2"></div>
        <div class="bline" >
            <fieldset title="“*”为必填项！" class="fieldsetstyle">
                <legend>批量导入药店</legend>
                <?php if(!erp_is_run($db,$_SESSION['uinfo']['ucompany'])) { ?>
                <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" action="do_upload.php?m=uploadclientexcel" target="exe_iframe" onsubmit="alert_uploading();" >
                    <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" align="center" >
                        <tr>
                            <td bgcolor="#F0F0F0"><div align="right">导入EXCEL 表格：</div></td>
                            <td  bgcolor="#FFFFFF"><label>
                                    <input type="file" name="import_file" id="import_file" />&nbsp;
                                    <span class="red">*</span></label></td>
                            <td><a href="<?php echo RESOURCE_URL.'example/client.xls'?>" target="_blank">下载 药店导入模板 Excel 文件格式</a> </td>
                        </tr>
                    </table>
                    <div align="center"><input name="saveproductsort" type="submit" class="button_1" id="saveproductsort" value=" 上传验证 "  /></div>
                </form>
                <?php }else { ?>
             	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Erp用户请通过接口同步新增药店资料。
             	<?php } ?>
            </fieldset>

            <br style="clear:both;" />
            <fieldset  id="yz_div" style="display:none;">
                <legend>再次验证导入的药店数据</legend>
                <form id="MainFormImport" name="MainFormImport" method="post" action="" target="exe_iframe" >
                    <div style="width:1148px;overflow-x:auto;">
                        <div style="width:2000px;">
                            <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
                                <thead>
                                <tr height="28">
                                    <td width="60" bgcolor="#efefef"  >序号</td>
                                    <td width="100" bgcolor="#efefef" align="center">删除</td>
                                    <td width="80" bgcolor="#efefef" align="center">状态</td>
                                    <td width="130" bgcolor="#efefef" >编号</td>
                                    <td width="200" bgcolor="#efefef" >名称</td>
                                    <td width="120" bgcolor="#efefef" >登录账号</td>
                                    <td width="110" bgcolor="#efefef" >登录密码</td>
                                    <td width="120" bgcolor="#efefef" >手机号</td>
                                    <td width="120" bgcolor="#efefef" >地区</td>
                                    <td width="120" bgcolor="#efefef" >联系人</td>
                                    <td width="120" bgcolor="#efefef" >联系电话</td>
                                    <td width="120" bgcolor="#efefef" >传真</td>
                                    <td width="120" bgcolor="#efefef">邮箱</td>
                                    <td width="220" bgcolor="#efefef">地址</td>
                                    <td width="120" bgcolor="#efefef" >备注</td>
                                    <td width="120" bgcolor="#efefef" align="center">级别</td>
                                    <td width="120" bgcolor="#efefef" align="center">执行价格</td>
                                    <td width="120" bgcolor="#efefef" align="center">折扣</td>
                                    <td width="120" bgcolor="#efefef" align="center">开户名称</td>
                                    <td width="120" bgcolor="#efefef" align="center">开户银行</td>
                                    <td width="120" bgcolor="#efefef" align="center">银行账号</td>
                                    <td width="120" bgcolor="#efefef" align="center">开票抬头</td>
                                    <td width="120" bgcolor="#efefef" align="center">纳税人识别号</td>
                                </tr>
                                </thead>
                                <tbody id="showimportdata">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input name="log_id" id="log_id" type="hidden" value=""/>
                    <INPUT TYPE="hidden" name="referer" value ="" >
                </form>

                <table width="98%" border="0" cellspacing="0" cellpadding="0" >
                    <tr>
                        <td width="50%"><font color=red>只能导入验证通过的药店数据</font></td>
                        <td  align="right"><input type="button" name="importtocart" class="button_2" id="importtocart" value=" 提交数据 " onclick="subinportcontent();" /></td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <br style="clear:both;" />
    </div>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    </body>
    </html>
<?php
function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1)
{
    $frontMsg  = "";
    $frontTitleMsg = "┠-";
    $selectmsg = "";

    if($var['ParentID']=="0") $layer = 1; else $layer++;

    foreach($resultdata as $key => $var)
    {
        if($var['ParentID'] == $p_id)
        {
            $repeatMsg = str_repeat("-+-", $layer-2);
            if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";

            $frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";

            $frontMsg2  = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$s_id,$layer);
            $frontMsg  .= $frontMsg2;
        }
    }
    return $frontMsg;
}

function charblank($char)
{
    if(strlen($char) > 5)
    {
        $rchar = substr($char,0,4);
    }else{
        $rchar = $char.str_repeat(" -", (4-strlen($char)));
    }
    return $rchar;
}


?>

