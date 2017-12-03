<?php
$menu_flag = "product";
$pope	   = "pope_view";
include_once ("header.php");

//setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name='robots' content='noindex,nofollow' />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/showpage.css" />

    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

    <script src="js/product.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body style="background-image:none;background:#fff;">
<div>
    <div class="bline">
        <div style=" margin:2px auto;height:470px;overflow-y:auto;padding-right:20px;">
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                <input name="referer" id="referer" type="hidden"/>
                <div id="data_specification">
                    <input type="hidden" name="data_SpecType" value="<?php echo $in['SpecType']; ?>" />
                    <table width="98%" border="0" cellspacing="0" cellpadding="0">
                        <tr style="height:40px;">
                            <td class="bold" width="10%" style="padding-right:2px;text-align:right;">编号:</td>
                            <td width="24%" ><input type="text" name="data_SpecNO" id="data_SpecNO" value="" /></td>
                            <td class="bold" width="10%" style="padding-right:2px;text-align:right;" >名称:</td>
                            <td width="24%"><input type="text" name="data_SpecName" id="data_SpecName" value="" /></td>
                            <td ><input type="button" name="savebutton" style="margin-top:0px;" id="savebutton" value="添加" class="button_2" onclick="do_add_specification();" /> </td>
                        </tr>
                    </table>
                </div>
                <a name="editbill"></a>
                <div id="edit_specification"  style="display:none;">
                    <INPUT TYPE="hidden" name="update_id" id="update_id" value ="" >
                    <input type="hidden" name="edit_SpecType" id="edit_SpecType" value="<?php echo $in['SpecType']; ?>" />
                    <table width="98%" border="0" cellspacing="0" cellpadding="0">
                        <tr style="height:40px;">
                            <td class="bold" width="10%" style="padding-right:2px;text-align:right;" >编号:</td>
                            <td width="24%" ><input type="text" name="edit_SpecNO" id="edit_SpecNO" value="" /></td>
                            <td class="bold" width="10%" style="padding-right:2px;text-align:right;" >名称:</td>
                            <td width="24%"><input type="text" name="edit_SpecName" id="edit_SpecName" value="" /></td>
                            <td ><input type="button" style="margin-top:0px;" name="editbutton" id="editbutton" value="修改" class="button_2" onclick="do_edit_specification();" /> </td>
                        </tr>
                    </table>
                </div>
                <hr/>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <td width="12%" class="bottomlinebold" align="center">行号</td>
                        <td width="24%" class="bottomlinebold">编号</td>
                        <td  class="bottomlinebold">
                            名称
                            <form action="product_specification.php?SpecType=<?php echo $in['SpecType']; ?>"></form>
                            <input type="text" name="kw" id="kw" value="<?php echo $in['kw']; ?>" style="width:80px; height:21px;line-height:auto;"/>
                            <input type="submit" value="搜" class="button_9 mainbtn" style="margin:0px;" onclick="do_search_specification();"/>
							<input type="button" value="全部" class="button_10 mainbtn" style="margin:0px;" onclick="javascript:window.location.href='product_specification.php?SpecType=<?php echo $in['SpecType']; ?>'"/>
                        </td>
                        <td width="10%" class="bottomlinebold" align="center">管理</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $kw = $in['kw'];
                    $wstr = $kw ? " AND (SpecName like '%".$kw."%')" : '';
                    $data_sql  = "SELECT * FROM rsung_order_specification WHERE SpecType = '".$in['SpecType']."' {$wstr} AND CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SpecID DESC";
                    $list_data = $db->get_results($data_sql);
                    if(!empty($list_data))
                    {
                        $n=1;
                        foreach($list_data as $lsv)
                        {
                            $lsv['SpecName'] = trim($lsv['SpecName']);
                            ?>
                            <tr id="line_<? echo $lsv['SpecID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                                <td align="center"><? echo $n++;?></td>
                                <td ><? echo $lsv['SpecNO'];?></td>
                                <td ><strong><? echo $lsv['SpecName'];?></strong></td>
                                <td align="center">
                                    <a href="#editbill" onclick="set_edit_specification('<?php echo $lsv['SpecID']; ?>','<?php echo $lsv['SpecNO']; ?>','<?php echo $lsv['SpecName']; ?>','<?php echo $lsv['SpecType']; ?>')" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_specification('<? echo $lsv['SpecID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>
                                </td>
                            </tr>
                        <? } }else{?>
                        <tr>
                            <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
                        </tr>
                    <? }?>
                    </tbody>
                </table>

                <INPUT TYPE="hidden" name="referer" value ="" >
            </form>
        </div>
    </div>
    <br style="clear:both;" />
</div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">
var spec = "<?php echo $in['SpecType']; ?>";
function set_edit_specification(ID,NO,Name,Type){
    $("#update_id").val(ID);
    $("#edit_SpecNO").val(NO);
    $("#edit_SpecName").val(Name);
    $("#edit_SpecType").val(Type);
    $("#edit_specification").show();
    $("#data_specification").hide();
}

/*搜索*/
function do_search_specification() {
    var kw = $("#kw").val();
    window.location.href = "product_specification.php?SpecType=" + spec + '&kw=' + kw;
}

function do_add_specification()
{
    document.MainForm.referer.value = document.location;

    if($('#data_SpecName').val()==""){
        $.blockUI({ message: "<p>请先输入名称！</p>" });
    }else if($("#data_SpecName").val().length>25){
        $.blockUI({ message: "<p>"+(spec=='Color' ? '颜色' : '规格')+",请保持在25个汉字以内!</p>" });
    }else{
        $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
        $.post("do_specification.php?m=do_add", $("#MainForm").serialize(),
            function(data){
                data = Jtrim(data);
                if(data == "ok"){
                    if(spec=='Specification'){
                        parent.set_specification_spec($("#data_SpecName").val());
                    }else if(spec=='Color'){
                        parent.set_specification_color($("#data_SpecName").val());
                    }
                    $.blockUI({ message: "<p>保存成功!</p>" });
                    window.location.reload();
                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                    $('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
                }
            }
        );
    }
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}

function do_edit_specification()
{
    document.MainForm.referer.value = document.location;

    if($('#edit_SpecName').val()==""){
        $.blockUI({ message: "<p>请先输入名称！</p>" });
    }else if($("#edit_SpecName").val().length>25){
        $.blockUI({ message: "<p>"+(spec=='Color' ? '颜色' : '规格')+",请保持在25个汉字以内!</p>" });
    }else{
        $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
        $.post("do_specification.php?m=do_save", $("#MainForm").serialize(),
            function(data){
                data = Jtrim(data);
                if(data == "ok"){
                    $.blockUI({ message: "<p>保存成功!</p>" });
                    window.location.reload();
                    $('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                    $('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
                }
            }
        );
    }
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}


function do_delete_specification(pid)
{
    if(confirm('确认删除吗?'))
    {
        $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
        $.post("do_specification.php",
            {m:"do_delete", ID: pid,SpecType:spec},
            function(data){
                data = Jtrim(data);
                if(data == "ok"){
                    window.location.reload();
                    $.blockUI({ message: "<p>删除成功!</p>" });
                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                }
            }
        );
    }else{
        return false;
    }
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 2000);
}

</script>
</body>
</html>